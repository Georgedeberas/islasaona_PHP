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

        // 3. Geolocalización
        $countryCode = 'XX';
        $city = 'Unknown';

        // A. Intentar headers de Cloudflare (Priority 1)
        if (isset($_SERVER["HTTP_CF_IPCOUNTRY"])) {
            $countryCode = $_SERVER["HTTP_CF_IPCOUNTRY"];
        }
        // B. Intentar API Fallback (Priority 2)
        else {
            // Solo consultar API si no tenemos dato y la IP es pública
            // Para evitar latencia excesiva en cada request, idealmente esto se cachearía en sesión
            // Pero por simplicidad en este MVP, lo haremos on-fly con un timeout corto
            $countryCode = self::resolveCountry($ip);
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
            // Silencioso
        }
    }

    /**
     * Obtener estadísticas generales para el Dashboard
     */
    /**
     * Obtener meses disponibles para filtro (YYYY-MM)
     */
    public static function getAvailableMonths()
    {
        $db = Database::getConnection();
        $stmt = $db->query("
            SELECT DISTINCT DATE_FORMAT(created_at, '%Y-%m') as month_str 
            FROM analytics_visits 
            ORDER BY month_str DESC
        ");
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    /**
     * Obtener estadísticas filtradas (Últimos X días o Mes Específico)
     * @param string|int $filter puede ser un entero (días) o string 'YYYY-MM'
     */
    public static function getStats($filter = 30)
    {
        $db = Database::getConnection();
        $stats = [];
        $params = [];

        // Construcción del WHERE dinámico
        if ($filter === '1d') {
            $where = "created_at >= DATE_SUB(NOW(), INTERVAL 24 HOUR)";
        } elseif ($filter === '3d') {
            $where = "created_at >= DATE_SUB(NOW(), INTERVAL 3 DAY)";
        } elseif ($filter === '7d') {
            $where = "created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)";
        } elseif (is_numeric($filter)) {
            $where = "created_at >= DATE_SUB(NOW(), INTERVAL ? DAY)";
            $params[] = $filter;
        } elseif (preg_match('/^\d{4}-\d{2}$/', $filter)) {
            $where = "DATE_FORMAT(created_at, '%Y-%m') = ?";
            $params[] = $filter;
        } else {
            // Default 30 days
            $where = "created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)";
        }

        // Total Visitas
        $stmt = $db->prepare("SELECT COUNT(*) as total FROM analytics_visits WHERE $where");
        $stmt->execute($params);
        $stats['total_visits'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

        // Visitantes Únicos
        $stmt = $db->prepare("SELECT COUNT(DISTINCT visitor_id) as unique_visitors FROM analytics_visits WHERE $where");
        $stmt->execute($params);
        $stats['unique_visitors'] = $stmt->fetch(PDO::FETCH_ASSOC)['unique_visitors'];

        // Top Páginas
        $stmt = $db->prepare("
            SELECT page_url, COUNT(*) as visits 
            FROM analytics_visits 
            WHERE $where
            GROUP BY page_url 
            ORDER BY visits DESC 
            LIMIT 5
        ");
        $stmt->execute($params);
        $stats['top_pages'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Top Países
        $stmt = $db->prepare("
            SELECT country_code, COUNT(*) as visits 
            FROM analytics_visits 
            WHERE $where
            GROUP BY country_code 
            ORDER BY visits DESC 
            LIMIT 10
        ");
        $stmt->execute($params);
        $rawCountries = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Enriquecer con nombres completos
        $stats['top_countries'] = array_map(function ($c) {
            $c['country_name'] = self::getCountryName($c['country_code']);
            return $c;
        }, $rawCountries);

        return $stats;
    }

    /**
     * Resetear estadísticas (Truncate)
     */
    public static function resetStats()
    {
        $db = Database::getConnection();
        return $db->exec("TRUNCATE TABLE analytics_visits");
    }

    /**
     * Mapeo simple de códigos ISO a Nombres Español
     */
    public static function getCountryName($code)
    {
        if ($code == 'XX' || empty($code))
            return 'Desconocido';

        $map = [
            'DO' => 'República Dominicana',
            'US' => 'Estados Unidos',
            'ES' => 'España',
            'FR' => 'Francia',
            'IT' => 'Italia',
            'DE' => 'Alemania',
            'CA' => 'Canadá',
            'GB' => 'Reino Unido',
            'CO' => 'Colombia',
            'MX' => 'México',
            'AR' => 'Argentina',
            'CL' => 'Chile',
            'PE' => 'Perú',
            'BR' => 'Brasil',
            'PR' => 'Puerto Rico',
            'VE' => 'Venezuela',
            'RU' => 'Rusia',
            'NL' => 'Países Bajos',
            'CH' => 'Suiza'
        ];

        return $map[$code] ?? $code;
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
    private static function resolveCountry($ip)
    {
        // Evitar llamar API para localhost
        if ($ip == '127.0.0.1' || $ip == '::1')
            return 'XX';

        // Intentar geolocalización básica vía API pública (con timeout estricto para no frenar la carga)
        try {
            $ctx = stream_context_create(['http' => ['timeout' => 2]]); // 2 segundos max
            $json = @file_get_contents("http://ip-api.com/json/{$ip}?fields=countryCode", false, $ctx);
            if ($json) {
                $data = json_decode($json, true);
                if (isset($data['countryCode'])) {
                    return $data['countryCode'];
                }
            }
        } catch (\Exception $e) {
            // Fallo silencioso
        }
        return 'XX';
    }
}
