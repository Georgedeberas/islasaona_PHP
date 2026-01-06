<?php
require_once __DIR__ . '/AuthController.php';
require_once __DIR__ . '/../Models/Tour.php';

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
            // Búsqueda lineal temporal (Mejorar con getById en el futuro)
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
            // ACTIVAR DEBUG EN STORE TAMBIÉN
            ini_set('display_errors', 1);
            error_reporting(E_ALL);

            $tourModel = new Tour();

            // 1. Preparar Datos
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
                'includes' => json_encode(array_filter(explode("\n", $_POST['includes'] ?? ''))),
                'not_included' => json_encode(array_filter(explode("\n", $_POST['not_included'] ?? '')))
            ];

            // 2. Guardar en BD (Crear o Actualizar)
            if ($id) {
                unset($data['slug']); // No cambiar slug al editar para no romper SEO
                if (!$tourModel->update($id, $data)) {
                    throw new Exception("Error al actualizar en Base de Datos.");
                }
                $tourId = $id;
            } else {
                // Verificar si slug existe (básico)
                $existing = $tourModel->getBySlug($data['slug']);
                if ($existing) {
                    $data['slug'] .= '-' . time(); // Hook simple para unicidad
                }

                $tourId = $tourModel->create($data);
                if (!$tourId) {
                    throw new Exception("Error al crear en Base de Datos.");
                }
            }

            // 3. Manejo de Imágenes ROBUS-TO
            if (isset($_FILES['images']) && !empty($_FILES['images']['name'][0])) {

                // Definir ruta absoluta
                $uploadDirRelative = '/assets/uploads/';
                $uploadDirAbsolute = __DIR__ . '/../../public' . $uploadDirRelative;

                // Verificar si existe carpeta uploads
                if (!is_dir($uploadDirAbsolute)) {
                    if (!mkdir($uploadDirAbsolute, 0755, true)) {
                        throw new Exception("CRÍTICO: No se puede crear la carpeta de uploads en: " . $uploadDirAbsolute);
                    }
                }

                // Verificar permisos de escritura check
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
                                // Guardar path relativo en BD (sin public/)
                                $dbPath = 'assets/uploads/' . $newName;
                                $isCover = ($key === 0 && !$id) ? 1 : 0;
                                $tourModel->addImage($tourId, $dbPath, $isCover);
                            } else {
                                throw new Exception("Error moviendo archivo subido: " . $name);
                            }
                        }
                    } elseif ($error !== UPLOAD_ERR_NO_FILE) {
                        throw new Exception("Error en subida de archivo código: " . $error);
                    }
                }
            }

            header('Location: /admin/dashboard');
            exit;

        } catch (Exception $e) {
            // Mostrar error feo pero útil para debuggear
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
