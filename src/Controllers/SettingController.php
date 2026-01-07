<?php

namespace App\Controllers;

use App\Models\Setting;

class SettingController
{

    public function __construct()
    {
        AuthController::requireLogin();
    }

    public function index()
    {
        $settingModel = new Setting();

        // Cargar tours para el selector de 'Destacados'
        $tourModel = new \App\Models\Tour();
        $allTours = $tourModel->getAll();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = $_POST;
            unset($data['submit']);

            // Manejo de Imagen Hero
            if (isset($_FILES['home_hero_bg']) && !empty($_FILES['home_hero_bg']['name'])) {
                $file = [
                    'tmp_name' => $_FILES['home_hero_bg']['tmp_name'],
                    'name' => $_FILES['home_hero_bg']['name'],
                    'type' => $_FILES['home_hero_bg']['type'],
                    'error' => 0,
                    'size' => $_FILES['home_hero_bg']['size']
                ];
                // Guardar en assets/img
                $newBg = \App\Services\ImageService::optimizeAndSave($file, __DIR__ . '/../../public/assets/img/');
                if ($newBg) {
                    $data['home_hero_bg'] = 'assets/img/' . $newBg;
                }
            }

            // Manejo de Arrays (Select Multiple)
            if (isset($data['home_featured_tours']) && is_array($data['home_featured_tours'])) {
                $data['home_featured_tours'] = json_encode($data['home_featured_tours']);
            } else {
                // Si no se envía nada (checkboxes desactivados o select vacío), hay que ver
                // Para booleanos (switches), si no vienen en POST, seteamos '0' SOLO si estaban en groups
                // Pero updateBatch itera sobre lo que recibe. 
                // Hack: Forzar envío de '0' para switches en el view usando hidden inputs o manejandolo aqui.
                // Por simplicidad, asumiremos que el form envía todo o manejaremos los keys específicos.
                $switches = ['home_show_why', 'home_show_welcome'];
                foreach ($switches as $s) {
                    if (!isset($data[$s]))
                        $data[$s] = '0';
                }
            }

            $settingModel->updateBatch($data);
            header('Location: /admin/settings?saved=1');
            exit;
        }

        $settings = $settingModel->getAllFull();
        require __DIR__ . '/../Views/admin/settings/index.php';
    }
}
