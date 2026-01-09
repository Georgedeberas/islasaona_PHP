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

        // 1. Prioridad: Vista específica por slug (ej: contact.php)
        $overrideView = __DIR__ . '/../Views/front/' . $slug . '.php';
        if (file_exists($overrideView)) {
            require $overrideView;
            return;
        }

        // 2. Sistema de Plantillas
        $template = $page['template'] ?? 'classic';

        // Sanitizar template para evitar directory traversal
        $template = preg_replace('/[^a-z0-9_]/', '', $template);
        if (empty($template))
            $template = 'classic';

        $templatePath = __DIR__ . '/../Views/front/pages/' . $template . '.php';

        if (file_exists($templatePath)) {
            require $templatePath;
            return;
        }

        // 3. Fallback: Classic Template
        require __DIR__ . '/../Views/front/pages/classic.php';
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
            $order = intval($_POST['order_index'] ?? 0);
            $template = $_POST['template'] ?? 'classic';

            // Recolectar Meta Data según plantilla
            $metaData = [
                'meta_title' => $_POST['meta_title'] ?? '',
                'meta_description' => $_POST['meta_description'] ?? '',
                'keywords' => $_POST['keywords'] ?? ''
            ];

            // 1. Landing Page Fields
            if ($template === 'landing') {
                $metaData['hero_title'] = $_POST['hero_title'] ?? '';
                $metaData['hero_subtitle'] = $_POST['hero_subtitle'] ?? '';
                $metaData['cta_text'] = $_POST['cta_text'] ?? '';
                $metaData['cta_link'] = $_POST['cta_link'] ?? '';

                // Features (simulated array from inputs)
                $metaData['features'] = [];
                if (isset($_POST['feature_title'])) {
                    foreach ($_POST['feature_title'] as $k => $ft) {
                        if (!empty($ft)) {
                            $metaData['features'][] = [
                                'title' => $ft,
                                'desc' => $_POST['feature_desc'][$k] ?? '',
                                'icon' => $_POST['feature_icon'][$k] ?? 'star'
                            ];
                        }
                    }
                }

                // Hero Image Upload
                if (isset($_FILES['hero_image']) && $_FILES['hero_image']['error'] === 0) {
                    $upload = $this->handleFileUpload($_FILES['hero_image']);
                    if ($upload)
                        $metaData['hero_image'] = $upload;
                }
            }

            // 2. Gallery Fields
            if ($template === 'gallery') {
                $metaData['gallery_description'] = $_POST['gallery_description'] ?? '';
                // Multiple Uploads handled separately or via generic handler? 
                // For MVP let's assume a simplified single-file flow or loop for gallery
                if (isset($_FILES['gallery_photos'])) {
                    $galleryPaths = [];
                    foreach ($_FILES['gallery_photos']['tmp_name'] as $key => $tmp) {
                        if ($_FILES['gallery_photos']['error'][$key] === 0) {
                            // Manual construction for the helper
                            $file = [
                                'name' => $_FILES['gallery_photos']['name'][$key],
                                'type' => $_FILES['gallery_photos']['type'][$key],
                                'tmp_name' => $tmp,
                                'error' => 0,
                                'size' => $_FILES['gallery_photos']['size'][$key]
                            ];
                            $path = $this->handleFileUpload($file);
                            if ($path)
                                $galleryPaths[] = $path;
                        }
                    }
                    $metaData['images'] = $galleryPaths;
                }
            }

            // Simple slugify si viene vacio o limpiar
            if (empty($slug)) {
                $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $title)));
            } else {
                $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $slug)));
            }

            $pageModel = new Page();
            try {
                // Encode Meta
                $metaJson = !empty($metaData) ? json_encode($metaData) : null;

                if ($pageModel->create($title, $slug, $content, $template, $metaJson, $order)) {
                    header('Location: /admin/pages?saved=1');
                    exit;
                }
            } catch (\Exception $e) {
                $error = "Error al crear: Probablemente el slug ya existe.";
            }
        }

        require __DIR__ . '/../Views/admin/pages/create.php';
    }

    private function handleFileUpload($file)
    {
        $uploadDirRelative = '/assets/uploads/pages/';
        $uploadDirAbsolute = __DIR__ . '/../../public' . $uploadDirRelative;
        if (!is_dir($uploadDirAbsolute))
            mkdir($uploadDirAbsolute, 0755, true);

        $finfo = new \finfo(FILEINFO_MIME_TYPE);
        $mime = $finfo->file($file['tmp_name']);
        if (!in_array($mime, ['image/jpeg', 'image/png', 'image/webp']))
            return null;

        $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
        $newName = 'page_' . uniqid() . '.' . $ext;

        if (move_uploaded_file($file['tmp_name'], $uploadDirAbsolute . $newName)) {
            return $uploadDirRelative . $newName;
        }
        return null;
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
            $order = intval($_POST['order_index'] ?? 0);
            $template = $_POST['template'] ?? 'classic';

            // Recolectar Meta Data
            $currentMeta = json_decode($page['meta_data'] ?? '[]', true) ?? [];
            $newMeta = [
                'meta_title' => $_POST['meta_title'] ?? '',
                'meta_description' => $_POST['meta_description'] ?? '',
                'keywords' => $_POST['keywords'] ?? ''
            ];

            // 1. Landing Logic
            if ($template === 'landing') {
                $newMeta['hero_title'] = $_POST['hero_title'] ?? '';
                $newMeta['hero_subtitle'] = $_POST['hero_subtitle'] ?? '';
                $newMeta['cta_text'] = $_POST['cta_text'] ?? '';
                $newMeta['cta_link'] = $_POST['cta_link'] ?? '';

                // Features
                $newMeta['features'] = [];
                if (isset($_POST['feature_title'])) {
                    foreach ($_POST['feature_title'] as $k => $ft) {
                        if (!empty($ft)) {
                            $newMeta['features'][] = [
                                'title' => $ft,
                                'desc' => $_POST['feature_desc'][$k] ?? '',
                                'icon' => $_POST['feature_icon'][$k] ?? 'star'
                            ];
                        }
                    }
                }

                // Image Handling
                if (isset($_FILES['hero_image']) && $_FILES['hero_image']['error'] === 0) {
                    $upload = $this->handleFileUpload($_FILES['hero_image']);
                    if ($upload)
                        $newMeta['hero_image'] = $upload;
                } else {
                    // Keep existing if not uploaded
                    $newMeta['hero_image'] = $currentMeta['hero_image'] ?? '';
                }
            }

            // 2. Gallery Logic
            if ($template === 'gallery') {
                $newMeta['gallery_description'] = $_POST['gallery_description'] ?? '';

                // Keep existing images
                $newMeta['images'] = $currentMeta['images'] ?? [];

                // Add new images
                if (isset($_FILES['gallery_photos'])) {
                    foreach ($_FILES['gallery_photos']['tmp_name'] as $key => $tmp) {
                        if ($_FILES['gallery_photos']['error'][$key] === 0) {
                            $file = [
                                'name' => $_FILES['gallery_photos']['name'][$key],
                                'type' => $_FILES['gallery_photos']['type'][$key],
                                'tmp_name' => $tmp,
                                'error' => 0,
                                'size' => $_FILES['gallery_photos']['size'][$key]
                            ];
                            $path = $this->handleFileUpload($file);
                            if ($path)
                                $newMeta['images'][] = $path;
                        }
                    }
                }

                // Handle deletion of specific images (todo: add UI for this later)
            }

            // Clean slug
            $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $slug)));

            $metaJson = !empty($newMeta) ? json_encode($newMeta) : null;

            $pageModel->update($id, $title, $slug, $content, $template, $metaJson, $order);
            header('Location: /admin/pages?saved=1');
            exit;
        }

        // Decode meta for view
        $page['meta_data'] = json_decode($page['meta_data'] ?? '[]', true);

        require __DIR__ . '/../Views/admin/pages/edit.php';
    }
    // ADMIN: DUPLICAR
    public function duplicate()
    {
        AuthController::requireLogin();
        $id = $_GET['id'] ?? null;
        if (!$id) {
            header('Location: /admin/pages?error=Falta ID');
            exit;
        }

        $pageModel = new Page();
        $page = $pageModel->getById($id);

        if (!$page) {
            header('Location: /admin/pages?error=Pagina no encontrada');
            exit;
        }

        // Crear copia
        $newTitle = "[Copia] " . $page['title'];
        $newSlug = $page['slug'] . "-copia-" . time();
        $content = $page['content'];
        $template = $page['template'];
        $metaData = $page['meta_data']; // JSON
        $order = intval($page['order_index']) + 1;

        $pageModel->create($newTitle, $newSlug, $content, $template, $metaData, $order);

        header('Location: /admin/pages?saved=1&msg=Página duplicada correctamente');
        exit;
    }

    // ADMIN: ELIMINAR
    public function delete()
    {
        AuthController::requireLogin();
        $id = $_GET['id'] ?? null;
        if (!$id) {
            header('Location: /admin/pages?error=Falta ID');
            exit;
        }

        $pageModel = new Page();
        $page = $pageModel->getById($id);

        // Protección de páginas sistema
        if (in_array($page['slug'], ['home', 'contact', 'about', 'tours', 'gallery'])) {
            header('Location: /admin/pages?error=No puedes eliminar páginas base del sistema');
            exit;
        }

        $pageModel->delete($id);
        header('Location: /admin/pages?saved=1&msg=Página eliminada');
        exit;
    }
}
