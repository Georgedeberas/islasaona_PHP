<?php

namespace App\Controllers;

use App\Models\Tour;

class TourController
{
    public function detail($slug)
    {
        $tourModel = new Tour();
        $tour = $tourModel->getBySlug($slug);

        if (!$tour) {
            http_response_code(404);
            echo "Tour no encontrado";
            return;
        }

        $images = $tourModel->getImages($tour['id']);

        // Vista Detalle
        require __DIR__ . '/../Views/front/tour_detail.php';
    }
}
