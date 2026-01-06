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
        $tourModel = new Tour();
        $tours = $tourModel->getAll(false); // False para traer todos, activos e inactivos
        require __DIR__ . '/../Views/admin/dashboard.php';
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
        $tourModel = new Tour();
        // Buscar tour por ID (necesitaremos agregar getById al modelo, o usar truco con filter)
        // Por ahora haré un método ad-hoc en el controller o asumiremos que el modelo lo tiene. 
        // Agregando getById dinámicamente o asumiendo getBySlug si fuera slug. 
        // Mejor agregar getById al modelo luego.

        // HACK TEMPORAL: Usar getAll y filtrar (ineficiente pero funcional para prototipo)
        // TODO: Agregar getById a Tour.php
        $tours = $tourModel->getAll(false);
        $tour = null;
        foreach ($tours as $t) {
            if ($t['id'] == $id) {
                $tour = $t;
                break;
            }
        }

        if (!$tour) {
            header('Location: /admin/dashboard?error=TourNotFound');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->handleSaveTour($id);
        } else {
            require __DIR__ . '/../Views/admin/tour_form.php';
        }
    }

    private function handleSaveTour($id = null)
    {
        $tourModel = new Tour();

        // Sanitización y Validación básica
        $data = [
            'title' => $_POST['title'],
            'slug' => $this->slugify($_POST['title']), // Auto-generar slug si no viene
            'description_short' => $_POST['description_short'],
            'description_long' => $_POST['description_long'], // HTML permitido (confiamos en admin por ahora, idealmente usar HTMLPurifier)
            'price_adult' => $_POST['price_adult'],
            'price_child' => $_POST['price_child'],
            'duration' => $_POST['duration'],
            'is_active' => isset($_POST['is_active']) ? 1 : 0,
            'display_style' => $_POST['display_style'],
            'meta_title' => $_POST['meta_title'],
            'meta_description' => $_POST['meta_description'],
            // JSON fields need to be encoded
            'includes' => json_encode(array_filter(explode("\n", $_POST['includes']))),
            'not_included' => json_encode(array_filter(explode("\n", $_POST['not_included'])))
        ];

        if ($id) {
            // Update
            // Si el usuario quiso cambiar el slug manual, usar ese, sino mantener o regenerar (aquí regeneramos por simpleza)
            // Cuidado al editar slug, rompe SEO. Mejor no tocar slug en edit salvo explícito.
            // Para simplicidad fase 1: no updateamos slug al editar.
            unset($data['slug']);

            $tourModel->update($id, $data);
            $tourId = $id;
        } else {
            // Create
            $tourId = $tourModel->create($data);
        }

        // Manejo de Imágenes
        if (isset($_FILES['images'])) {
            $uploadDir = __DIR__ . '/../../public/assets/uploads/';

            // Loop through images
            foreach ($_FILES['images']['tmp_name'] as $key => $tmp_name) {
                if ($_FILES['images']['error'][$key] === UPLOAD_ERR_OK) {
                    $tmp_name = $_FILES['images']['tmp_name'][$key];
                    $name = basename($_FILES['images']['name'][$key]);
                    $ext = strtolower(pathinfo($name, PATHINFO_EXTENSION));

                    // Validación básica de extensión
                    if (in_array($ext, ['jpg', 'jpeg', 'png', 'webp'])) {
                        $newName = 'tour_' . $tourId . '_' . uniqid() . '.' . $ext;
                        move_uploaded_file($tmp_name, $uploadDir . $newName);

                        // Guardar en DB
                        $isCover = ($key === 0 && !$id) ? 1 : 0; // Primera imagen es cover al crear
                        $tourModel->addImage($tourId, 'assets/uploads/' . $newName, $isCover);
                    }
                }
            }
        }

        header('Location: /admin/dashboard');
        exit;
    }

    private function slugify($text)
    {
        // Función simple para slug
        $text = preg_replace('~[^\pL\d]+~u', '-', $text);
        $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);
        $text = preg_replace('~[^-\w]+~', '', $text);
        $text = trim($text, '-');
        $text = preg_replace('~-+~', '-', $text);
        $text = strtolower($text);
        if (empty($text)) {
            return 'n-a';
        }
        return $text;
    }
}
