<?php

namespace App\Services;

use App\Config\Database;
use PDO;

class Analytics
{
    /**
     * Registra una visita en la base de datos.
     * Debe llamarse al inicio de la carga de la página (layout principal).
     */
    public static function track()
    {
        // Evitar rastrear archivos estáticos o llamadas AJAX si se desea
        // o bots conocidos si queremos filtrar tráfico basura.

        // 1. Obtener Datos
        $ip = self::getIpAddress();
        $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown';
        $pageUrl = $_SERVER['REQUEST_URI'] ?? '/';
        $referrer = $_SERVER['HTTP_REFERER'] ?? null;

        // Excluir rutas de admin o assets para no ensuciar la data
        if (strpos($pageUrl, '/admin') === 0 || strpos($pageUrl, '/assets') === 0) {
            return;
        }

        // 2. Identificación de Visitante Único (Cookie simple de 24h)
        $visitorCookie = 'is_tracker_vid';
        if (!isset($_COOKIE[$visitorCookie])) {
            $visitorId = bin2hex(random_bytes(16));
            setcookie($visitorCookie, $visitorId, time() + 86400, "/"); // 1 día
        } else {
            $visitorId = $_COOKIE[$visitorCookie];
        }

        // 3. Geolocalización "Offline" (Marcadores de posición)
        // Para implementar esto REALMENTE offline, se requiere la BD MaxMind GeoLite2 (.mmdb)
        // y la librería 'geoip2/geoip2'.
        // Por ahora, guardaremos NULL y permitiremos que un proceso posterior lo resuelva,
        // o implementaremos una lógica básica si se instala la librería.
        $countryCode = 'XX'; // Desconocido por defecto
        $city = 'Unknown';

        // Intentar headers de Cloudflare si existen (a veces el hosting los provee gratis)
        if (isset($_SERVER["HTTP_CF_IPCOUNTRY"])) {
            $countryCode = $_SERVER["HTTP_CF_IPCOUNTRY"];
        }

        try {
            $db = Database::getConnection();
            $stmt = $db->prepare("
                INSERT INTO analytics_visits 
                (ip_address, visitor_id, page_url, referrer, user_agent, country_code, city) 
                VALUES (?, ?, ?, ?, ?, ?, ?)
            ");
            $stmt->execute([$ip, $visitorId, $pageUrl, $referrer, $userAgent, $countryCode, $city]);
        } catch (\Exception $e) {
            // Silencioso: Si falla el tracking, no romper la página del usuario
            // error_log("Analytics Error: " . $e->getMessage());
        }
    }

    /**
     * Obtener estadísticas generales para el Dashboard
     */
    public static function getStats($days = 30)
    {
        $db = Database::getConnection();
        $stats = [];

        // Total Visitas (Últimos X días)
        $stmt = $db->prepare("SELECT COUNT(*) as total FROM analytics_visits WHERE created_at >= DATE_SUB(NOW(), INTERVAL ? DAY)");
        $stmt->execute([$days]);
        $stats['total_visits'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

        // Visitantes Únicos
        $stmt = $db->prepare("SELECT COUNT(DISTINCT visitor_id) as unique_visitors FROM analytics_visits WHERE created_at >= DATE_SUB(NOW(), INTERVAL ? DAY)");
        $stmt->execute([$days]);
        $stats['unique_visitors'] = $stmt->fetch(PDO::FETCH_ASSOC)['unique_visitors'];

        // Top Páginas
        $stmt = $db->prepare("
            SELECT page_url, COUNT(*) as visits 
            FROM analytics_visits 
            WHERE created_at >= DATE_SUB(NOW(), INTERVAL ? DAY)
            GROUP BY page_url 
            ORDER BY visits DESC 
            LIMIT 5
        ");
        $stmt->execute([$days]);
        $stats['top_pages'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Top Países
        $stmt = $db->prepare("
            SELECT country_code, COUNT(*) as visits 
            FROM analytics_visits 
            WHERE created_at >= DATE_SUB(NOW(), INTERVAL ? DAY)
            GROUP BY country_code 
            ORDER BY visits DESC 
            LIMIT 5
        ");
        $stmt->execute([$days]);
        $stats['top_countries'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return $stats;
    }

    private static function getIpAddress()
    {
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            return $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            return $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            return $_SERVER['REMOTE_ADDR'];
        }
    }
}
