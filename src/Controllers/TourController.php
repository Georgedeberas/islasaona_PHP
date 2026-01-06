<?php
require_once __DIR__ . '/../Models/Tour.php';

class TourController
{
    public function detail($slug)
    {
        $tourModel = new Tour();
        $tour = $tourModel->getBySlug($slug);

        if (!$tour) {
            http_response_code(404);
            echo "Tour no encontrado"; // Idealmente una vista 404 bonita
            return;
        }

        $images = $tourModel->getImages($tour['id']);

        // Vista Detalle
        require __DIR__ . '/../Views/front/tour_detail.php';
    }
}
