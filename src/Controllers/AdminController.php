<?php

namespace App\Controllers;

use App\Models\Tour;
use App\Models\User;
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
            // Reset Logic Removed as per user request (security/data integrity)


            $tourModel = new Tour();
            $allTours = $tourModel->getAll(false);

            // Tour Stats
            // Tour Stats
            $stats = [
                'total_tours' => count($allTours),
                'active_tours' => count(array_filter($allTours, function ($t) {
                    return $t['is_active'] == 1; })),
                'inactive_tours' => count(array_filter($allTours, function ($t) {
                    return $t['is_active'] == 0; })),
            ];

            // Analytics Filter
            $filter = $_GET['month'] ?? 30; // Default 30 days
            $trafficStats = \App\Services\Analytics::getStats($filter);

            // Available Months for Dropdown
            $availableMonths = \App\Services\Analytics::getAvailableMonths();

            require __DIR__ . '/../Views/admin/dashboard.php';
        } catch (Exception $e) {
            die("Error cargando dashboard: " . $e->getMessage());
        }
    }
}
