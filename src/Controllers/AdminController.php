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
            $tourModel = new Tour();
            $allTours = $tourModel->getAll(false); // Traer todos, activos e inactivos

            // Calcular estadísticas de Tours
            $stats = [
                'total_tours' => count($allTours),
                'active_tours' => count(array_filter($allTours, fn($t) => $t['is_active'] == 1)),
                'inactive_tours' => count(array_filter($allTours, fn($t) => $t['is_active'] == 0)),
            ];

            // Estadísticas de Tráfico (Analytics Service)
            // Importante: Asegurarse de importar el namespace arriba o usar FQCN
            $trafficStats = \App\Services\Analytics::getStats(30); // Últimos 30 días

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

            $data = [
                'title' => $_POST['title'],
                'slug' => $this->slugify($_POST['title']), // Slug inicial, si existe se ajusta abajo
                'description_short' => $_POST['description_short'] ?? '',
                'description_long' => $_POST['description_long'] ?? '',
                'price_adult' => !empty($_POST['price_adult']) ? $_POST['price_adult'] : 0,
                'price_child' => !empty($_POST['price_child']) ? $_POST['price_child'] : 0,
                'duration' => $_POST['duration'] ?? '',
                'is_active' => isset($_POST['is_active']) ? 1 : 0,
                'display_style' => $_POST['display_style'] ?? 'grid',
                'meta_title' => $_POST['meta_title'] ?? '',
                'meta_description' => $_POST['meta_description'] ?? '',
                'includes' => array_filter(explode("\n", $_POST['includes'] ?? '')),
                'not_included' => array_filter(explode("\n", $_POST['not_included'] ?? ''))
            ];

            if ($id) {
                unset($data['slug']); // No cambiar slug al editar para no romper SEO links existentes
                if (!$tourModel->update($id, $data)) {
                    throw new Exception("Error al actualizar en Base de Datos.");
                }
                $tourId = $id;
            } else {
                // Verificar slug único al crear
                $existing = $tourModel->getBySlug($data['slug']);
                if ($existing) {
                    $data['slug'] .= '-' . time();
                }

                $tourId = $tourModel->create($data);
                if (!$tourId) {
                    throw new Exception("Error al crear en Base de Datos.");
                }
            }

            // Manejo de Imágenes
            if (isset($_FILES['images']) && !empty($_FILES['images']['name'][0])) {
                $uploadDirRelative = '/assets/uploads/';
                $uploadDirAbsolute = __DIR__ . '/../../public' . $uploadDirRelative;

                if (!is_dir($uploadDirAbsolute)) {
                    mkdir($uploadDirAbsolute, 0755, true);
                }

                foreach ($_FILES['images']['tmp_name'] as $key => $tmp_name) {
                    if ($_FILES['images']['error'][$key] === UPLOAD_ERR_OK) {
                        $name = basename($_FILES['images']['name'][$key]);
                        $ext = strtolower(pathinfo($name, PATHINFO_EXTENSION));
                        if (in_array($ext, ['jpg', 'jpeg', 'png', 'webp'])) {
                            $newName = 'tour_' . $tourId . '_' . uniqid() . '.' . $ext;
                            if (move_uploaded_file($tmp_name, $uploadDirAbsolute . $newName)) {
                                $dbPath = 'assets/uploads/' . $newName;
                                $isCover = ($key === 0 && !$id) ? 1 : 0; // Primera imagen es cover si es nuevo
                                $tourModel->addImage($tourId, $dbPath, $isCover);
                            }
                        }
                    }
                }
            }

            header('Location: /admin/tours'); // Redirigir a listado de tours
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
