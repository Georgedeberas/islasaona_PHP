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
            // Seasonal Pricing (JSON string from UI)
            'price_rules' => $_POST['price_rules'] ?? null,
            // Phase 4
            'private_notes' => $_POST['private_notes'] ?? '',
            'sort_order' => $_POST['sort_order'] ?? 0,

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

            // Log
            \App\Services\ActivityLogger::log('create', 'tour', $id, "Created tour '{$data['title']}'");

            // Imágenes
            $this->handleImages($id, true);

            header("Location: /admin/tours/edit?id=$id&success=1");
            exit;

        } catch (\Exception $e) {
            die($e->getMessage());
        }
    }

    public function duplicate($id)
    {
        AuthController::requireLogin();
        try {
            $tourModel = new Tour();
            // Get original
            // TODO: Optimize getById helper later
            $allTours = $tourModel->getAll(false);
            $original = null;
            foreach ($allTours as $t) {
                if ($t['id'] == $id) {
                    $original = $t;
                    break;
                }
            }

            if (!$original)
                die("Original tour not found");

            // Prepare Cloned Data - Remove ID and non-clonable meta
            $newData = $original;
            unset($newData['id'], $newData['created_at'], $newData['updated_at']);

            $newData['title'] = $original['title'] . ' (Copia)';
            $newData['slug'] = $this->slugify($newData['title']);
            $newData['is_active'] = 0; // Draft by default

            // JSON fields likely come as strings from DB, create needs array or specific handling?
            // create() expects arrays for JSON fields if we follow store() Logic...
            // BUT store() Logic gets raw strings from POST? No, store calls getPostData which creates arrays/strings...
            // Let's check create() in Model: It expects arrays or encoded strings?
            // Model::create() does json_encode() if it IS array. If it's already string (from DB select), it might double encode if input is string?
            // "is_array($data['includes']) ? json_encode... : $data['includes'] ?? '[]'"
            // So if DB returns string "['foo']", it passes as string. Safe.

            // However, we must ensure keys match create() expectations.
            // create() params: :seo_title, etc. which are keys in $data.
            // $original keys match DB columns. Model::create() uses $data keys to bind :params.
            // Model::create() Params match: :title, :slug, etc.
            // So passing $original array (filtered) should work if column names match params.

            $newId = $tourModel->create($newData);

            if ($newId) {
                // Clone Images? Maybe later. For now just data.
                // If user desires image cloning, we need to copy files and DB rows.
                // Let's do simple image cloning for cover at least?
                // "Clonador de Ofertas" usually implies full clone.
                // Let's try to clone images DB rows pointing to SAME files (efficient).
                $images = $tourModel->getImages($id);
                foreach ($images as $img) {
                    $tourModel->addImage($newId, $img['image_path'], $img['is_cover']);
                }

                header("Location: /admin/tours/edit?id=$newId&success=cloned");
                exit;
            } else {
                throw new \Exception("Error duplicating tour");
            }

        } catch (\Exception $e) {
            die($e->getMessage());
        }
    }

    public function update($id)
    {
        AuthController::requireLogin();
        try {
            $tourModel = new Tour();

            // Phase 5: Get Old Data for Redirects & Comparison
            $oldTour = null;
            $allTours = $tourModel->getAll(false); // Hack, better getById
            foreach ($allTours as $t) {
                if ($t['id'] == $id) {
                    $oldTour = $t;
                    break;
                }
            }

            $data = $this->getPostData();
            // Recalculate slug if title changed (user logic may vary, assuming auto-slug on update?)
            // Usually we don't change slug automatically unless explicitly requested to avoid breaking SEO.
            // But here let's assume if title changes significantly or we want to force slug update.
            // Let's NOT force slug change on update to be safe, unless we have a specific slug field.
            // Wait, previous code didn't update slug.
            // So redirects only happen if we explicitly added logic to update slug.
            // If I look at `getPostData`, it doesn't return `slug`.

            // Let's ADD slug update logic if we want redirects to happen.
            // Ideally we should have a 'slug' field in the form.
            // For now, let's just log the update.

            $tourModel->update($id, $data);

            // Log Activity
            \App\Services\ActivityLogger::log('update', 'tour', $id, "Updated tour '{$data['title']}'");

            // Borrar imágenes
            if (isset($_POST['delete_images']) && is_array($_POST['delete_images'])) {
                $db = \App\Config\Database::getConnection();
                foreach ($_POST['delete_images'] as $imgId) {
                    $db->prepare("DELETE FROM tour_images WHERE id = ?")->execute([$imgId]);
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

            header("Location: /admin/tours/edit?id=$id&success=1");
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

    // --- PHASE 4: ADMIN POWER TOOLS ---

    public function trash()
    {
        AuthController::requireLogin();
        $tourModel = new Tour();
        // GetAll deleted only? We need to update getAll logic or filter manually
        // Our getAll supports $includeDeleted. But we want ONLY deleted?
        // Let's just fetch all including deleted, and filter in PHP for the trash view.
        // Efficient enough for small catalogue.
        $allTours = $tourModel->getAll(false, true);
        $deletedTours = array_filter($allTours, function ($t) {
            return !empty($t['deleted_at']);
        });

        require __DIR__ . '/../Views/admin/tours/trash.php';
    }

    public function moveToTrash($id)
    {
        AuthController::requireLogin();
        $tourModel = new Tour();
        if ($tourModel->softDelete($id)) {
            \App\Services\ActivityLogger::log('soft_delete', 'tour', $id, "Moved tour to trash");
            header('Location: /admin/tours?trashed=1');
        } else {
            die("Error moving to trash");
        }
    }

    public function restore($id)
    {
        AuthController::requireLogin();
        $tourModel = new Tour();
        if ($tourModel->restore($id)) {
            \App\Services\ActivityLogger::log('restore', 'tour', $id, "Restored tour from trash");
            header('Location: /admin/tours/trash?restored=1');
        } else {
            die("Error restoring");
        }
    }

    public function destroy($id)
    {
        AuthController::requireLogin();
        $tourModel = new Tour();
        if ($tourModel->forceDelete($id)) {
            \App\Services\ActivityLogger::log('force_delete', 'tour', $id, "Permanently deleted tour");
            header('Location: /admin/tours/trash?deleted=1');
        } else {
            die("Error deleting permanently");
        }
    }

    public function quickUpdate()
    {
        AuthController::requireLogin();
        header('Content-Type: application/json');

        $input = json_decode(file_get_contents('php://input'), true);

        if (!$input || empty($input['id']) || empty($input['field'])) {
            echo json_encode(['success' => false, 'message' => 'Invalid data']);
            return;
        }

        $tourModel = new Tour();
        $result = $tourModel->updateField($input['id'], $input['field'], $input['value']);

        echo json_encode(['success' => $result]);
    }

    public function reorder()
    {
        AuthController::requireLogin();
        header('Content-Type: application/json');

        $input = json_decode(file_get_contents('php://input'), true);
        if (!$input || !is_array($input['order'])) {
            echo json_encode(['success' => false]);
            return;
        }

        $tourModel = new Tour();
        foreach ($input['order'] as $position => $id) {
            $tourModel->updateField($id, 'sort_order', $position);
        }

        echo json_encode(['success' => true]);
    }
}
