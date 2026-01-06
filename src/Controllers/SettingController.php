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

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Recibir todos los inputs y actualizar
            // Filtramos solo los que sabemos que existen o enviamos todo $_POST excepto botones
            $data = $_POST;
            unset($data['submit']);

            $settingModel->updateBatch($data);

            // Recargar con mensaje de éxito (query param simple)
            header('Location: /admin/settings?saved=1');
            exit;
        }

        $settings = $settingModel->getAllFull();
        require __DIR__ . '/../Views/admin/settings/index.php';
    }
}
