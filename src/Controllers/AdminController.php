<?php

namespace App\Controllers;

use App\Models\Tour;
use App\Models\User;
use App\Models\Article;
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

            $articleModel = new Article();
            $totalArticles = count($articleModel->getAll());

            // WA Stats (Phase 7)
            $waClicks = 0;
            try {
                $db = \App\Config\Database::getConnection();
                $stmtWa = $db->query("SELECT COUNT(*) FROM click_tracks WHERE track_type = 'whatsapp'");
                if ($stmtWa)
                    $waClicks = $stmtWa->fetchColumn();
            } catch (\Exception $ignore) {
            }

            $stats = [
                'total_tours' => count($allTours),
                'active_tours' => count(array_filter($allTours, function ($t) {
                    return $t['is_active'] == 1;
                })),
                'inactive_tours' => count(array_filter($allTours, function ($t) {
                    return $t['is_active'] == 0;
                })),
                'total_articles' => $totalArticles,
                'wa_clicks' => $waClicks
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
