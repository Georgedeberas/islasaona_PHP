<?php

namespace App\Controllers;

use App\Models\Page;

class PageController
{

    // FRONTEND
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

        $viewPath = __DIR__ . '/../Views/front/' . $slug . '.php';
        if (file_exists($viewPath)) {
            require $viewPath;
            return;
        }

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

    // ADMIN: LISTAR
    public function index()
    {
        AuthController::requireLogin();
        $pageModel = new Page();
        $pages = $pageModel->getAll();
        require __DIR__ . '/../Views/admin/pages/index.php';
    }

    // ADMIN: CREAR
    public function create()
    {
        AuthController::requireLogin();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $title = $_POST['title'] ?? '';
            $slug = $_POST['slug'] ?? '';
            $content = $_POST['content'] ?? '';

            // Simple slugify si viene vacio o limpiar
            if (empty($slug)) {
                $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $title)));
            } else {
                $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $slug)));
            }

            $pageModel = new Page();
            // Validar unico no implementado en detalle aqui, asumimos DB error si dup key
            try {
                if ($pageModel->create($title, $slug, $content)) {
                    header('Location: /admin/pages?saved=1');
                    exit;
                }
            } catch (\Exception $e) {
                $error = "Error al crear: Probablemente el slug ya existe.";
            }
        }

        require __DIR__ . '/../Views/admin/pages/create.php';
    }

    // ADMIN: EDITAR
    public function edit()
    {
        AuthController::requireLogin();
        $id = $_GET['id'] ?? null;
        if (!$id) {
            header('Location: /admin/pages');
            exit;
        }

        $pageModel = new Page();
        // Usar DB crudo o agregar getById al Modelo (vamos a usar getById ad-hoc o query directa desde controller para rapido, o mejor GetAll y filtrar como antes, para no romper compatibilidad si no edito modelo... pero ya edité modelo arriba para tener update con slug)
        // Oops, no agregué getById en el último paso al modelo Page.php, pero sí getAll, getBySlug. 
        // Vamos a usar una query directa aqui rápido o updatear Page.php en la mente.
        // Espera, el modelo Page.php lo acabo de sobrescribir y NO tiene getById. Usaré Database directo para leer por ID.

        $db = \App\Config\Database::getConnection();
        $stmt = $db->prepare("SELECT * FROM pages WHERE id = :id");
        $stmt->execute([':id' => $id]);
        $page = $stmt->fetch();

        if (!$page)
            die("Página no encontrada");

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $title = $_POST['title'];
            $slug = $_POST['slug'];
            $content = $_POST['content'];

            // Clean slug
            $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $slug)));

            $pageModel->update($id, $title, $slug, $content);
            header('Location: /admin/pages?saved=1');
            exit;
        }

        require __DIR__ . '/../Views/admin/pages/edit.php';
    }
}
