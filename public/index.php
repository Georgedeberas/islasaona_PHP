<?php
// public/index.php - Updated with Create/Store routes

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/../src/autoload.php';

use App\Controllers\HomeController;
use App\Controllers\TourController;
use App\Controllers\AdminController;
use App\Controllers\AuthController;
use App\Controllers\SettingController;
use App\Controllers\PageController;

try {
    $requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

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
            (new AdminController())->createTour();
        } elseif ($requestUri === '/admin/tours/edit') {
            (new AdminController())->editTour($_GET['id'] ?? null);
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
        // Rutas dinámicas (slugs de páginas)
        // Intentar cargar como página estática si no matchea nada más
        $slug = trim($requestUri, '/');
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
