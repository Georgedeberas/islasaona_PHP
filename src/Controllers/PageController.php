<?php

namespace App\Controllers;

use App\Models\Page;
use App\Models\Database; // Ensure we have DB access if needed, though Page model handles it
use Exception;

class PageController
{

    public function __construct()
    {
        // Constructor vacío o lógica de init si hace falta
    }

    public function show($slug)
    {
        $pageModel = new Page();
        $page = $pageModel->getBySlug($slug);

        if (!$page) {
            http_response_code(404);
            require __DIR__ . '/../Views/layout/header.php';
            echo "<div class='container py-20 text-center'><h1>404 Página no encontrada</h1></div>";
            require __DIR__ . '/../Views/layout/footer.php';
            return;
        }

        // Determinar vista basada en slug o usar genérica
        $viewPath = __DIR__ . '/../Views/front/' . $slug . '.php';

        if (file_exists($viewPath) && ($slug === 'about' || $slug === 'contact')) {
            // Pasamos $page y $settings (necesario recargarlo o pasarlo global)
            // Para simplicidad en este MVP, las vistas 'page' hacen su propio require de header que carga settings
            require $viewPath;
            return;
        }

        // Layout genérico para páginas de contenido puro
        require __DIR__ . '/../Views/layout/header.php';
        ?>
        <div class="py-12 bg-gray-50">
            <div class="container mx-auto px-4">
                <h1 class="text-4xl font-bold text-center text-secondary mb-8"><?= htmlspecialchars($page['title']) ?></h1>
                <div class="bg-white p-8 rounded-2xl shadow-sm prose max-w-none mx-auto lg:w-3/4">
                    <?= $page['content'] ?>
                </div>
            </div>
        </div>
        <?php
        require __DIR__ . '/../Views/layout/footer.php';
    }

    // LISTADO ADMIN
    public function index()
    {
        AuthController::requireLogin();
        $db = \App\Config\Database::getConnection();
        $stmt = $db->query("SELECT * FROM pages ORDER BY title ASC");
        $pages = $stmt->fetchAll();

        require __DIR__ . '/../Views/admin/pages/index.php';
    }

    // EDICIÓN ADMIN
    public function edit()
    {
        AuthController::requireLogin();

        $id = $_GET['id'] ?? null;
        if (!$id) {
            header('Location: /admin/dashboard');
            exit;
        }

        $db = \App\Config\Database::getConnection();
        $stmt = $db->prepare("SELECT * FROM pages WHERE id = :id");
        $stmt->execute([':id' => $id]);
        $page = $stmt->fetch();

        if (!$page) {
            die("Página no encontrada");
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $content = $_POST['content'] ?? '';
            // Permitir HTML seguro (en un entorno real se usaría HTMLPurifier, 
            // aquí confiamos en el admin pero advertimos riesgo XSS si el admin es malicioso)

            $stmtUpdate = $db->prepare("UPDATE pages SET content = :content WHERE id = :id");
            $stmtUpdate->execute([':content' => $content, ':id' => $id]);

            header('Location: /admin/pages?saved=1');
            exit;
        }

        require __DIR__ . '/../Views/admin/pages/edit.php';
    }
}
