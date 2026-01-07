<?php
// public/index.php - Updated with Create/Store routes

// Production Error Handling
ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);
error_reporting(E_ALL);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/../error_log.php'); // Log local protegido

session_start(); // Inicio de sesión global necesario para Auth y Mensajes Flash

require_once __DIR__ . '/../src/autoload.php';

use App\Controllers\HomeController;
use App\Controllers\TourController;
use App\Controllers\AdminController;
use App\Controllers\AuthController;
use App\Controllers\SettingController;
use App\Controllers\TrackingController;
use App\Controllers\PageController;

try {
    $requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

    // Tracking Route (Phase 7: Sales Intelligence)
    if (strpos($requestUri, '/go/wa') === 0) {
        (new TrackingController())->wa();
        exit;
    }

    // --- FASE 5: MODO MANTENIMIENTO ---
    // Verificar si no es admin area y si está activo
    if (strpos($requestUri, '/admin') !== 0 && strpos($requestUri, '/login') === false) {
        $dbMaint = \App\Config\Database::getConnection();
        $stmtMaint = $dbMaint->query("SELECT setting_value FROM settings WHERE setting_key = 'maintenance_mode'");
        $isMaintenance = $stmtMaint->fetchColumn();

        if ($isMaintenance == '1' && empty($_SESSION['user_id'])) {
            http_response_code(503);
            require __DIR__ . '/../src/Views/front/maintenance.php';
            exit;
        }
    }

    // Rutas Admin
    if (strpos($requestUri, '/admin') === 0) {
        $auth = new AuthController();

        if ($requestUri === '/admin/login') {
            $auth->login();
        } elseif ($requestUri === '/admin/logout') {
            $auth->logout();
        } elseif ($requestUri === '/admin/dashboard') {
            (new AdminController())->dashboard();
        } elseif ($requestUri === '/admin/tours') {
            (new AdminController())->tours();
        } elseif ($requestUri === '/admin/tours/create') {
            (new TourController())->create();
        } elseif ($requestUri === '/admin/tours/edit') {
            (new TourController())->edit($_GET['id'] ?? null);
        } elseif ($requestUri === '/admin/settings') {
            (new SettingController())->index();
        } elseif ($requestUri === '/admin/pages') {
            (new PageController())->index();
        } elseif ($requestUri === '/admin/pages/create') {
            (new PageController())->create();
        } // NEW
        elseif ($requestUri === '/admin/pages/edit') {
            (new PageController())->edit();
        } else {
            header('Location: /admin/dashboard');
        }
        exit;
    }

    // Rutas Públicas
    if ($requestUri === '/' || $requestUri === '/index.php') {
        $home = new HomeController();
        $home->index();
    } elseif (preg_match('#^/tour/([\w-]+)$#', $requestUri, $matches)) {
        $tourController = new TourController();
        $tourController->detail($matches[1]);
    } else {
        // --- FASE 5: REDIRECCIONES 301 ---
        $path = trim($requestUri, '/');
        $db = \App\Config\Database::getConnection();
        $stmt = $db->prepare("SELECT new_url FROM redirects WHERE old_slug = ? LIMIT 1");
        $stmt->execute([$path]);
        if ($newUrl = $stmt->fetchColumn()) {
            header("Location: " . $newUrl, true, 301);
            exit;
        }

        // Rutas dinámicas (slugs de páginas)
        // Intentar cargar como página estática si no matchea nada más
        $slug = $path;
        // Sanitizar básico
        $slug = filter_var($slug, FILTER_SANITIZE_URL);

        if (!empty($slug)) {
            $pageController = new PageController();
            $pageController->show($slug);
        } else {
            // Raiz ya manejada, pero por seguridad
            $home = new HomeController();
            $home->index();
        }
    }

} catch (Throwable $e) {
    http_response_code(500);
    echo "<h1>Error Crítico (500)</h1>";
    echo "<p>" . $e->getMessage() . "</p>";
}
