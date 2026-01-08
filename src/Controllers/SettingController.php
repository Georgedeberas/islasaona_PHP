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
            if (isset($_POST['delete_hero_bg']) && $_POST['delete_hero_bg'] === '1') {
                $data['home_hero_bg'] = ''; // Limpiar referencia en BD
            } elseif (isset($_FILES['home_hero_bg']) && !empty($_FILES['home_hero_bg']['name'])) {
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

            // Cleanup: remove auxiliary fields
            unset($data['delete_hero_bg']);

            // Manejo de Arrays (Select Multiple)
            if (isset($data['home_featured_tours']) && is_array($data['home_featured_tours'])) {
                $data['home_featured_tours'] = json_encode($data['home_featured_tours']);
            }

            // Manejo de Switches (Booleanos)
            // Para checkboxes no marcados, el navegador no envía nada, así que forzamos '0'
            $switches = ['home_show_why', 'home_show_welcome', 'maintenance_mode'];
            foreach ($switches as $s) {
                if (!isset($data[$s])) {
                    $data[$s] = '0';
                }
            }

            // DEBUG LOGGING
            $logMsg = "POST Update: " . date('Y-m-d H:i:s') . "\n";
            $logMsg .= print_r($data, true);
            file_put_contents(__DIR__ . '/../../debug_settings_log.txt', $logMsg, FILE_APPEND);

            $settingModel->updateBatch($data);
            header('Location: /admin/settings?saved=1');
            exit;
        }

        $settings = $settingModel->getAllFull();
        require __DIR__ . '/../Views/admin/settings/index.php';
    }
}
