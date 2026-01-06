<?php
// ACTIVAR DEBUGGING (Solo para diagnóstico, quitar en producción real luego)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Autoloader simple
spl_autoload_register(function ($class_name) {
    // Mapeo básico de clases a rutas
    $paths = [
        '../src/Controllers/',
        '../src/Models/',
        '../src/Views/', // Por si acaso
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

try {
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
    require_once __DIR__ . '/../src/Controllers/TourController.php';

    if ($requestUri === '/' || $requestUri === '/index.php') {
        $home = new HomeController();
        $home->index();
    } elseif (preg_match('#^/tour/([\w-]+)$#', $requestUri, $matches)) {
        $slug = $matches[1];
        $tourController = new TourController();
        $tourController->detail($slug);
    } else {
        // Fallback 404
        http_response_code(404);
        echo "<h1>404 Not Found</h1><p>La página que buscas no existe.</p>";
    }

} catch (Throwable $e) {
    // Capturar errores fatales globales
    http_response_code(500);
    echo "<h1>Error del Servidor (500)</h1>";
    echo "<pre style='background:#f4f4f4; padding:15px; border:1px solid #ccc; border-radius:5px;'>";
    echo "Mensaje: " . htmlspecialchars($e->getMessage()) . "\n";
    echo "Archivo: " . $e->getFile() . "\n";
    echo "Línea: " . $e->getLine() . "\n";
    echo "</pre>";
}
