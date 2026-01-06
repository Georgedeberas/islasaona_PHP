<?php
// public/index.php - Actualizado rutas CMS

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
            $admin = new AdminController();
            $admin->dashboard();
        } elseif ($requestUri === '/admin/tours') {
            $admin = new AdminController();
            $admin->tours();
        } elseif ($requestUri === '/admin/tours/create') {
            $admin = new AdminController();
            $admin->createTour();
        } elseif ($requestUri === '/admin/tours/edit') {
            $id = $_GET['id'] ?? null;
            if (!$id)
                header('Location: /admin/tours'); // Redirect to tours list
            else {
                $admin = new AdminController();
                $admin->editTour($id);
            }
        } elseif ($requestUri === '/admin/settings') {
            $settings = new SettingController();
            $settings->index();
        } elseif ($requestUri === '/admin/pages') {
            $pages = new PageController();
            $pages->index();
        } elseif ($requestUri === '/admin/pages/edit') {
            $pages = new PageController();
            $pages->edit();
        } else {
            // Default admin landing
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
    } elseif ($requestUri === '/about' || $requestUri === '/contact') {
        $slug = trim($requestUri, '/');
        $pageController = new PageController();
        $pageController->show($slug);
    } else {
        http_response_code(404);
        require __DIR__ . '/../src/Views/layout/header.php';
        echo "<div class='container py-20 text-center'>
                <h1 class='text-4xl font-bold text-gray-800 mb-4'>404</h1>
                <p class='text-gray-600 mb-8'>Página no encontrada.</p>
                <a href='/' class='btn bg-primary text-white px-6 py-3 rounded'>Volver al Inicio</a>
              </div>";
        require __DIR__ . '/../src/Views/layout/footer.php';
    }

} catch (Throwable $e) {
    http_response_code(500);
    echo "<h1>Error Crítico (500)</h1>";
    echo "<p>" . $e->getMessage() . "</p>";
}
