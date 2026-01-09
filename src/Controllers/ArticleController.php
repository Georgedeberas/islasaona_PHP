<?php

namespace App\Controllers;

use App\Models\Article;
use App\Services\ImageService;

class ArticleController
{
    // Frontend: List
    public function index()
    {
        $articleModel = new Article();
        $articles = $articleModel->getAll(true); // Published only
        require __DIR__ . '/../Views/front/blog.php';
    }

    // Frontend: Detail
    public function show($slug)
    {
        $articleModel = new Article();
        $article = $articleModel->getBySlug($slug);

        if (!$article || !$article['is_published']) {
            http_response_code(404);
            require __DIR__ . '/../Views/front/404.php';
            return;
        }

        require __DIR__ . '/../Views/front/article.php';
    }

    // Admin: List
    public function adminIndex()
    {
        AuthController::requireLogin();
        $articleModel = new Article();
        $articles = $articleModel->getAll(false); // All
        require __DIR__ . '/../Views/admin/articles/index.php';
    }

    // Admin: Create/Edit Form
    public function edit()
    {
        AuthController::requireLogin();
        $id = $_GET['id'] ?? null;
        $article = null;

        if ($id) {
            $articleModel = new Article();
            $article = $articleModel->getById($id);
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->save($id);
        } else {
            require __DIR__ . '/../Views/admin/articles/edit.php';
        }
    }

    private function save($id = null)
    {
        AuthController::requireLogin();

        $data = [
            'title' => $_POST['title'],
            'slug' => !empty($_POST['slug']) ? $_POST['slug'] : $this->slugify($_POST['title']),
            'content' => $_POST['content'], // Rich Text HTML
            'excerpt' => $_POST['excerpt'],
            'is_published' => isset($_POST['is_published']) ? 1 : 0,
            'seo_title' => $_POST['seo_title'],
            'seo_description' => $_POST['seo_description']
        ];

        // Handle Image
        if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
            $file = [
                'tmp_name' => $_FILES['image']['tmp_name'],
                'name' => $_FILES['image']['name'],
                'type' => $_FILES['image']['type'],
                'error' => 0,
                'size' => $_FILES['image']['size']
            ];

            $dir = __DIR__ . '/../../public/assets/uploads/blog/';
            // Use ImageService (optimize to WebP)
            $savedName = ImageService::optimizeAndSave($file, $dir);

            if ($savedName) {
                $data['image_path'] = 'assets/uploads/blog/' . $savedName;
            }
        }

        $articleModel = new Article();
        if ($id) {
            $articleModel->update($id, $data);
            $msg = "Artículo actualizado.";
        } else {
            $id = $articleModel->create($data);
            $msg = "Artículo creado.";
        }

        header("Location: /admin/articles/edit?id=$id&success=1");
        exit;
    }

    public function delete($id)
    {
        AuthController::requireLogin();
        // Permission Check: Only Admin can delete
        if ($_SESSION['user_role'] !== 'admin') {
            die("Acceso denegado. Solo administradores pueden borrar.");
        }

        $articleModel = new Article();
        $articleModel->delete($id);
        header("Location: /admin/articles?deleted=1");
        exit;
    }



    private function slugify($text)
    {
        $text = preg_replace('~[^\pL\d]+~u', '-', $text);
        $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);
        $text = preg_replace('~[^-\w]+~', '', $text);
        $text = trim($text, '-');
        $text = preg_replace('~-+~', '-', $text);
        $text = strtolower($text);
        return empty($text) ? 'n-a' : $text;
    }
}
