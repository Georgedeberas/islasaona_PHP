<?php

namespace App\Controllers;

use App\Models\Tour;

class HomeController
{
    public function index()
    {
        $tourModel = new Tour();
        $tours = $tourModel->getAll(true); // Solo activos

        // Vista Home
        // Ajustamos la ruta al estar en src/Controllers
        // Vista Home
        // Ajustamos la ruta al estar en src/Controllers
        require __DIR__ . '/../Views/front/home.php';
    }
}
