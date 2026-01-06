<?php

namespace App\Controllers;

use App\Models\Tour;
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
            $tours = $tourModel->getAll(false);
            require __DIR__ . '/../Views/admin/dashboard.php';
        } catch (Exception $e) {
            die("Error cargando dashboard: " . $e->getMessage());
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
            ini_set('display_errors', 1);
            error_reporting(E_ALL);

            $tourModel = new Tour();

            if (!isset($_POST['title']) || empty($_POST['title'])) {
                throw new Exception("El título es obligatorio.");
            }

            $data = [
                'title' => $_POST['title'],
                'slug' => $this->slugify($_POST['title']),
                'description_short' => $_POST['description_short'] ?? '',
                'description_long' => $_POST['description_long'] ?? '',
                'price_adult' => !empty($_POST['price_adult']) ? $_POST['price_adult'] : 0,
                'price_child' => !empty($_POST['price_child']) ? $_POST['price_child'] : 0,
                'duration' => $_POST['duration'] ?? '',
                'is_active' => isset($_POST['is_active']) ? 1 : 0,
                'display_style' => $_POST['display_style'] ?? 'grid',
                'meta_title' => $_POST['meta_title'] ?? '',
                'meta_description' => $_POST['meta_description'] ?? '',
                'includes' => array_filter(explode("\n", $_POST['includes'] ?? '')), // Array puro, el modelo lo convierte a JSON
                'not_included' => array_filter(explode("\n", $_POST['not_included'] ?? '')) // Array puro
            ];

            if ($id) {
                unset($data['slug']);
                if (!$tourModel->update($id, $data)) {
                    throw new Exception("Error al actualizar en Base de Datos.");
                }
                $tourId = $id;
            } else {
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
                    if (!mkdir($uploadDirAbsolute, 0755, true)) {
                        throw new Exception("CRÍTICO: No se puede crear la carpeta de uploads en: " . $uploadDirAbsolute);
                    }
                }

                if (!is_writable($uploadDirAbsolute)) {
                    throw new Exception("CRÍTICO: La carpeta uploads NO tiene permisos de escritura: " . $uploadDirAbsolute);
                }

                foreach ($_FILES['images']['tmp_name'] as $key => $tmp_name) {
                    $error = $_FILES['images']['error'][$key];
                    if ($error === UPLOAD_ERR_OK) {
                        $name = basename($_FILES['images']['name'][$key]);
                        $ext = strtolower(pathinfo($name, PATHINFO_EXTENSION));

                        if (in_array($ext, ['jpg', 'jpeg', 'png', 'webp'])) {
                            $newName = 'tour_' . $tourId . '_' . uniqid() . '.' . $ext;
                            $destination = $uploadDirAbsolute . $newName;

                            if (move_uploaded_file($tmp_name, $destination)) {
                                $dbPath = 'assets/uploads/' . $newName;
                                $isCover = ($key === 0 && !$id) ? 1 : 0;
                                $tourModel->addImage($tourId, $dbPath, $isCover);
                            } else {
                                throw new Exception("Error moviendo archivo subido: " . $name);
                            }
                        }
                    }
                }
            }

            header('Location: /admin/dashboard');
            exit;

        } catch (Exception $e) {
            die("<div style='background:red; color:white; padding:20px;'><h1>Error Guardando Tour</h1><p>" . $e->getMessage() . "</p><a href='javascript:history.back()' style='color:yellow'>Volver</a></div>");
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
