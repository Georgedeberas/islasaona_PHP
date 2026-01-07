<?php

namespace App\Controllers;

use App\Models\Tour;
use App\Models\User;
use Exception;

class AdminController
{

    public function __construct()
    {
        AuthController::requireLogin();
    }

    public function dashboard()
    {
        try {
            // Handle Reset Action
            if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['reset_analytics'])) {
                if ($_POST['reset_analytics'] === 'confirm') {
                    \App\Services\Analytics::resetStats();
                    header("Location: /admin/dashboard?reset=1");
                    exit;
                }
            }

            $tourModel = new Tour();
            $allTours = $tourModel->getAll(false);

            // Tour Stats
            $stats = [
                'total_tours' => count($allTours),
                'active_tours' => count(array_filter($allTours, fn($t) => $t['is_active'] == 1)),
                'inactive_tours' => count(array_filter($allTours, fn($t) => $t['is_active'] == 0)),
            ];

            // Analytics Filter
            $filter = $_GET['month'] ?? 30; // Default 30 days
            $trafficStats = \App\Services\Analytics::getStats($filter);

            // Available Months for Dropdown
            $availableMonths = \App\Services\Analytics::getAvailableMonths();

            require __DIR__ . '/../Views/admin/dashboard.php';
        } catch (Exception $e) {
            die("Error cargando dashboard: " . $e->getMessage());
        }
    }

    public function tours()
    {
        try {
            $tourModel = new Tour();
            $tours = $tourModel->getAll(false);
            require __DIR__ . '/../Views/admin/tours/index.php';
        } catch (Exception $e) {
            die("Error cargando tours: " . $e->getMessage());
        }
    }

    public function createTour()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->handleSaveTour();
        } else {
            require __DIR__ . '/../Views/admin/tour_form.php';
        }
    }

    public function editTour($id)
    {
        try {
            $tourModel = new Tour();
            // Optimización: Crear método getById en modelo, pero por ahora usamos getAll y filtramos
            // TODO: Crear getById en TourModel para eficiencia
            $allTours = $tourModel->getAll(false);
            $tour = null;
            foreach ($allTours as $t) {
                if ($t['id'] == $id) {
                    $tour = $t;
                    break;
                }
            }

            if (!$tour) {
                die("Tour no encontrado ID: $id");
            }

            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $this->handleSaveTour($id);
            } else {
                require __DIR__ . '/../Views/admin/tour_form.php';
            }
        } catch (Exception $e) {
            die("Error editando tour: " . $e->getMessage());
        }
    }

    private function handleSaveTour($id = null)
    {
        try {
            $tourModel = new Tour();

            if (!isset($_POST['title']) || empty($_POST['title'])) {
                throw new Exception("El título es obligatorio.");
            }

            // Sanitización básica para arrays de líneas
            $sanitizeLines = function ($input) {
                return array_filter(array_map('trim', explode("\n", $input ?? '')));
            };

            $data = [
                'title' => $_POST['title'],
                'slug' => $_POST['slug'] ?? $this->slugify($_POST['title']), // Permitir editar slug o generar
                'description_short' => $_POST['description_short'] ?? '',
                'description_long' => $_POST['description_long'] ?? '',
                'price_adult' => !empty($_POST['price_adult']) ? $_POST['price_adult'] : 0,
                'price_child' => !empty($_POST['price_child']) ? $_POST['price_child'] : 0,
                'duration' => $_POST['duration'] ?? '',
                'is_active' => isset($_POST['is_active']) ? 1 : 0,
                // Listas JSON
                'includes' => $sanitizeLines($_POST['includes']),
                'not_included' => $sanitizeLines($_POST['not_included']),
                // SEO AEO Fields
                'seo_title' => $_POST['seo_title'] ?? '',
                'seo_description' => $_POST['seo_description'] ?? '',
                'keywords' => $_POST['keywords'] ?? '',
                'schema_type' => $_POST['schema_type'] ?? 'TouristTrip',
                'rating_score' => $_POST['rating_score'] ?? 4.8,
                'review_count' => $_POST['review_count'] ?? 0,
                'tour_highlights' => $sanitizeLines($_POST['tour_highlights']),
                // New Extended Fields (2026)
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
                'specific_dates' => !empty($_POST['specific_dates']) ? explode(',', $_POST['specific_dates']) : []
            ];

            if ($id) {
                // Actualizar
                if (!$tourModel->update($id, $data)) {
                    throw new Exception("Error al actualizar en Base de Datos.");
                }
                $tourId = $id;

                // 2. Gestionar Borrado de Imágenes
                if (isset($_POST['delete_images']) && is_array($_POST['delete_images'])) {
                    // Nota: Aquí deberíamos borrar físicamente too, pero por seguridad MVP solo borramos registro o marcamos
                    // Para V1.5 borramos registro DB, el archivo queda "huérfano" por seguridad hasta cleanup script
                    foreach ($_POST['delete_images'] as $imgIdToDelete) {
                        // $tourModel->deleteImage($imgIdToDelete); (Implementar método en Modelo si no existe)
                        // Hack temporal: Query directa si el modelo no tiene deleteImage, o agregarlo.
                        // Asumiremos que agregamos deleteImage al modelo o usamos raw query aquí por brevedad del ejemplo
                        $db = \App\Config\Database::getConnection();
                        $stmt = $db->prepare("DELETE FROM tour_images WHERE id = :id");
                        $stmt->execute([':id' => $imgIdToDelete]);
                    }
                }

                // 3. Gestionar Portada (Cover)
                if (isset($_POST['cover_image'])) {
                    $coverId = $_POST['cover_image'];
                    $db = \App\Config\Database::getConnection();
                    // Reset all
                    $db->prepare("UPDATE tour_images SET is_cover = 0 WHERE tour_id = ?")->execute([$tourId]);
                    // Set new
                    $db->prepare("UPDATE tour_images SET is_cover = 1 WHERE id = ?")->execute([$coverId]);
                }

            } else {
                // Crear
                $existing = $tourModel->getBySlug($data['slug']);
                if ($existing) {
                    $data['slug'] .= '-' . time();
                }
                $tourId = $tourModel->create($data);
                if (!$tourId) {
                    throw new Exception("Error al crear en Base de Datos.");
                }
            }

            // 4. Manejo de Nuevas Imágenes
            if (isset($_FILES['images']) && !empty($_FILES['images']['name'][0])) {
                $uploadDirRelative = '/assets/uploads/';
                $uploadDirAbsolute = __DIR__ . '/../../public' . $uploadDirRelative;

                if (!is_dir($uploadDirAbsolute)) {
                    mkdir($uploadDirAbsolute, 0755, true);
                }

                foreach ($_FILES['images']['tmp_name'] as $key => $tmp_name) {
                    if ($_FILES['images']['error'][$key] === UPLOAD_ERR_OK) {
                        // Validación MIME
                        $finfo = new \finfo(FILEINFO_MIME_TYPE);
                        $mime = $finfo->file($tmp_name);
                        $allowedMimes = ['image/jpeg', 'image/png', 'image/webp'];

                        if (!in_array($mime, $allowedMimes))
                            continue;

                        $ext = ($mime === 'image/png') ? 'png' : (($mime === 'image/webp') ? 'webp' : 'jpg');
                        $newName = 'tour_' . $tourId . '_' . uniqid() . '.' . $ext;

                        if (move_uploaded_file($tmp_name, $uploadDirAbsolute . $newName)) {
                            $dbPath = 'assets/uploads/' . $newName;
                            // Si es nuevo tour y es la primera foto, es cover automáticamente
                            $isCover = (!$id && $key === 0) ? 1 : 0;
                            $tourModel->addImage($tourId, $dbPath, $isCover);
                        }
                    }
                }
            }

            // Redirección POST-Redirect-GET a la misma página de edición
            header("Location: /admin/tours/edit/$tourId?success=1");
            exit;

        } catch (Exception $e) {
            die("<h1>Error Guardando Tour</h1><p>" . $e->getMessage() . "</p><a href='javascript:history.back()'>Volver</a>");
        }
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
