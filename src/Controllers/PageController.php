<?php

namespace App\Controllers;

use App\Models\Page;

class PageController
{

    public function show($slug)
    {
        $pageModel = new Page();
        $page = $pageModel->getBySlug($slug);

        if (!$page) {
            http_response_code(404);
            echo "Página no encontrada";
            return;
        }

        // Determinar vista basada en slug (o usar una genérica)
        $viewPath = __DIR__ . '/../Views/front/' . $slug . '.php';

        // Si no existe vista específica, usar una genérica (pendiente crear generic.php si se requiere)
        // Por ahora asumimos que crearemos about.php y contact.php
        if (file_exists($viewPath)) {
            require $viewPath;
            return;
        }

        // Fallback layout básico
        require __DIR__ . '/../Views/layout/header.php';
        echo '<div class="container py-10 prose max-w-none">';
        echo '<h1>' . htmlspecialchars($page['title']) . '</h1>';
        echo $page['content']; // HTML permitido
        echo '</div>';
        require __DIR__ . '/../Views/layout/footer.php';
    }

    public function edit($slug)
    {
        // TODO: Implementar vista de edición para admin
    }
}
