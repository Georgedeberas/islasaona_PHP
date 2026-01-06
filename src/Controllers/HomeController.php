<?php
require_once __DIR__ . '/../Models/Tour.php';

class HomeController
{
    public function index()
    {
        $tourModel = new Tour();
        $tours = $tourModel->getAll(true); // Solo activos

        // Vista Home
        require __DIR__ . '/../Views/front/home.php';
    }
}
