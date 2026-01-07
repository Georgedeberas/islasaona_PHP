<?php

namespace App\Controllers;

use App\Models\Tour;

class TourController
{

    // PUBLICS
    public function detail($slug)
    {
        $tourModel = new Tour();
        $tour = $tourModel->getBySlug($slug);

        if (!$tour) {
            http_response_code(404);
            require __DIR__ . '/../Views/front/404.php'; // Asumiendo exista
            return;
        }

        $images = $tourModel->getImages($tour['id']);

        // Vista Detalle
        require __DIR__ . '/../Views/front/tour_detail.php';
    }

    // ADMIN ACTIONS
    // Estas requieren Auth. Deberíamos llamar AuthController::requireLogin() en cada uno.

    public function create()
    {
        AuthController::requireLogin();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->store();
        } else {
            require __DIR__ . '/../Views/admin/tours/edit.php';
        }
    }

    public function edit($id)
    {
        AuthController::requireLogin();

        $tourModel = new Tour();
        // Buscar tour por ID (Usamos getAll filtrado por ahora o getById recomendado)
        // Hack rápido: GetAll
        $allTours = $tourModel->getAll(false);
        $tour = null;
        foreach ($allTours as $t) {
            if ($t['id'] == $id) {
                $tour = $t;
                break;
            }
        }

        if (!$tour)
            die("Tour not found");

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->update($id);
        } else {
            require __DIR__ . '/../Views/admin/tours/edit.php';
        }
    }

    private function getPostData()
    {
        $sanitizeLines = function ($input) {
            return array_filter(array_map('trim', explode("\n", $input ?? '')));
        };

        return [
            'title' => $_POST['title'],
            'description_short' => $_POST['description_short'] ?? '',
            'description_long' => $_POST['description_long'] ?? '',
            'price_adult' => !empty($_POST['price_adult']) ? $_POST['price_adult'] : 0,
            'price_child' => !empty($_POST['price_child']) ? $_POST['price_child'] : 0,
            'duration' => $_POST['duration'] ?? '',
            'is_active' => isset($_POST['is_active']) ? 1 : 0,

            // New Extended Fields
            'info_cost' => $_POST['info_cost'] ?? '',
            'info_dates_text' => $_POST['info_dates_text'] ?? '',
            'info_duration' => $_POST['info_duration'] ?? '',
            'info_includes' => $_POST['info_includes'] ?? '',
            'info_visiting' => $_POST['info_visiting'] ?? '',
            'info_not_included' => $_POST['info_not_included'] ?? '',
            'info_departure' => $_POST['info_departure'] ?? '',
            'info_parking' => $_POST['info_parking'] ?? '',
            'info_important' => $_POST['info_important'] ?? '',
            'info_what_to_bring' => $_POST['info_what_to_bring'] ?? '',
            'frequency_type' => $_POST['frequency_type'] ?? 'daily',
            'specific_dates' => !empty($_POST['specific_dates']) ? explode(',', $_POST['specific_dates']) : [],

            // SEO
            'seo_title' => $_POST['seo_title'] ?? '',
            'seo_description' => $_POST['seo_description'] ?? '',
            'keywords' => $_POST['keywords'] ?? '',
            'tour_highlights' => $sanitizeLines($_POST['tour_highlights']),
        ];
    }

    public function store()
    {
        AuthController::requireLogin();
        try {
            $tourModel = new Tour();
            $data = $this->getPostData();
            $data['slug'] = $this->slugify($data['title']);

            // Crear
            $id = $tourModel->create($data);

            // Imágenes
            $this->handleImages($id, true);

            header("Location: /admin/tours/edit/$id?success=1");
            exit;

        } catch (\Exception $e) {
            die($e->getMessage());
        }
    }

    public function update($id)
    {
        AuthController::requireLogin();
        try {
            $tourModel = new Tour();
            $data = $this->getPostData();

            $tourModel->update($id, $data);

            // Borrar imágenes
            if (isset($_POST['delete_images']) && is_array($_POST['delete_images'])) {
                $db = \App\Config\Database::getConnection();
                foreach ($_POST['delete_images'] as $imgId) {
                    $db->prepare("DELETE FROM tour_images WHERE id = ?")->execute([$imgId]);
                    // TODO: Borrar archivo físico
                }
            }

            // Cover
            if (isset($_POST['cover_image'])) {
                $db = \App\Config\Database::getConnection();
                $db->prepare("UPDATE tour_images SET is_cover = 0 WHERE tour_id = ?")->execute([$id]);
                $db->prepare("UPDATE tour_images SET is_cover = 1 WHERE id = ?")->execute([$_POST['cover_image']]);
            }

            // Subir Nuevas
            $this->handleImages($id);

            header("Location: /admin/tours/edit/$id?success=1");
            exit;

        } catch (\Exception $e) {
            die($e->getMessage());
        }
    }

    private function handleImages($tourId, $firstIsCover = false)
    {
        if (isset($_FILES['images']) && !empty($_FILES['images']['name'][0])) {
            $uploadDir = __DIR__ . '/../../public/assets/uploads/';
            if (!is_dir($uploadDir))
                mkdir($uploadDir, 0755, true);

            $tourModel = new Tour();

            foreach ($_FILES['images']['tmp_name'] as $key => $tmp) {
                if ($_FILES['images']['error'][$key] === 0) {
                    $ext = pathinfo($_FILES['images']['name'][$key], PATHINFO_EXTENSION);
                    $name = 'tour_' . $tourId . '_' . uniqid() . '.' . $ext;
                    if (move_uploaded_file($tmp, $uploadDir . $name)) {
                        $isCover = ($firstIsCover && $key === 0) ? 1 : 0;
                        $tourModel->addImage($tourId, 'assets/uploads/' . $name, $isCover);
                    }
                }
            }
        }
    }

    private function slugify($text)
    {
        // Simple slugify
        $text = preg_replace('~[^\pL\d]+~u', '-', $text);
        $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);
        $text = preg_replace('~[^-\w]+~', '', $text);
        $text = trim($text, '-');
        $text = preg_replace('~-+~', '-', $text);
        $text = strtolower($text);
        return empty($text) ? 'n-a' : $text;
    }
}
