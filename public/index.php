<?php
// public/index.php - Punto de entrada principal

// ACTIVAR DEBUGGING TEMPORALMENTE
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Cargar Autoloader
require_once __DIR__ . '/../src/autoload.php';

use App\Controllers\HomeController;
use App\Controllers\TourController;
use App\Controllers\AdminController;
use App\Controllers\AuthController;

try {
    // Router Básico
    $requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

    // Rutas Admin
    if (strpos($requestUri, '/admin') === 0) {
        // Auth Check manual si es necesario, o dentro de los constructores

        $auth = new AuthController();

        if ($requestUri === '/admin/login') {
            $auth->login();
        } elseif ($requestUri === '/admin/logout') {
            $auth->logout();
        } elseif ($requestUri === '/admin/dashboard') {
            $admin = new AdminController();
            $admin->dashboard();
        } elseif ($requestUri === '/admin/tours/create') {
            $admin = new AdminController();
            $admin->createTour();
        } elseif ($requestUri === '/admin/tours/edit') {
            $id = $_GET['id'] ?? null;
            if (!$id) {
                header('Location: /admin/dashboard');
                exit;
            }
            $admin = new AdminController();
            $admin->editTour($id);
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
        $slug = $matches[1];
        $tourController = new TourController();
        $tourController->detail($slug);
    } else {
        http_response_code(404);
        echo "<h1>404 Not Found</h1><p>Página no encontrada.</p><a href='/'>Ir al Inicio</a>";
    }

} catch (Throwable $e) {
    http_response_code(500);
    echo "<h1>Error Crítico (500)</h1>";
    echo "<p>" . $e->getMessage() . "</p>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
}
