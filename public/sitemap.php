<?php
// public/sitemap.php
// Generador de Sitemap XML Dinámico para Mochileros RD

// Configuración
header("Content-type: application/xml");
require_once __DIR__ . '/../src/autoload.php';

use App\Models\Tour;

// Base URL (Asegurar que termine sin slash o manejarlo consistentemente)
// Idealmente tomar de variable de entorno o settings, aqui hardcodeamos por seguridad seo
$baseUrl = 'http://islasaona.mochilerosrd.com';

echo '<?xml version="1.0" encoding="UTF-8"?>';
?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
    <!-- Home -->
    <url>
        <loc>
            <?= $baseUrl ?>/
        </loc>
        <changefreq>daily</changefreq>
        <priority>1.0</priority>
    </url>

    <!-- Static Pages (Hardcoded por ahora o traer de DB Pages) -->
    <url>
        <loc>
            <?= $baseUrl ?>/about
        </loc>
        <priority>0.8</priority>
    </url>
    <url>
        <loc>
            <?= $baseUrl ?>/contact
        </loc>
        <priority>0.8</priority>
    </url>

    <?php
    // Cargar Tours Activos
    try {
        $tourModel = new Tour();
        $tours = $tourModel->getAll(true); // true = solo activos
    
        foreach ($tours as $tour) {
            $slug = htmlspecialchars($tour['slug']);
            // Fecha de modificación: Si tenemos updated_at lo usamos, sino created_at, sino hoy
            // La tabla tours tiene created_at, asumimos updated_at no existe o quizas si (no estaba en schema original)
            // Usaremos una fecha segura.
            $date = date('Y-m-d');
            if (isset($tour['updated_at']))
                $date = date('Y-m-d', strtotime($tour['updated_at']));
            elseif (isset($tour['created_at']))
                $date = date('Y-m-d', strtotime($tour['created_at']));
            ?>
            <url>
                <loc>
                    <?= $baseUrl ?>/tour/
                    <?= $slug ?>
                </loc>
                <lastmod>
                    <?= $date ?>
                </lastmod>
                <changefreq>weekly</changefreq>
                <priority>0.8</priority>
            </url>
            <?php
        }
    } catch (Exception $e) {
        // En caso de error, no romper el XML visualmente, quizas loguear
        // error_log($e->getMessage());
    }
    ?>
</urlset>