<?php

// Autoloader simple
spl_autoload_register(function ($class_name) {
    // Mapeo básico de clases a rutas
    $paths = [
        '../src/Controllers/',
        '../src/Models/',
        '../config/'
    ];

    foreach ($paths as $path) {
        $file = __DIR__ . '/' . $path . $class_name . '.php';
        if (file_exists($file)) {
            require_once $file;
            return;
        }
    }
});

// Router Básico
$requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

// Rutas Admin
if (strpos($requestUri, '/admin') === 0) {
    require_once __DIR__ . '/../src/Controllers/AuthController.php';
    require_once __DIR__ . '/../src/Controllers/AdminController.php';

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
        // Default admin redirect
        header('Location: /admin/dashboard');
    }
    exit;
}

// Rutas Públicas
require_once __DIR__ . '/../src/Controllers/HomeController.php';
// require_once __DIR__ . '/../src/Controllers/TourController.php'; // Lo crearemos en breve

if ($requestUri === '/' || $requestUri === '/index.php') {
    $home = new HomeController();
    $home->index();
} elseif (preg_match('#^/tour/([\w-]+)$#', $requestUri, $matches)) {
    // $slug = $matches[1];
    // $tourController = new TourController();
    // $tourController->detail($slug);
    echo "Detalle del tour (Pendiente de implementar): " . htmlspecialchars($matches[1]);
} else {
    http_response_code(404);
    echo "404 Not Found";
}
