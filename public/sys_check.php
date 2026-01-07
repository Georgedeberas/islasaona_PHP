<?php
// public/sys_check.php
// Herramienta de Diagnóstico Temporal para Depuración de Producción
// NO DEJAR EN PRODUCCIÓN PERMANENTEMENTE

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header("Content-Type: text/plain");

echo "========================================\n";
echo "       DIAGNÓSTICO MOCHILEROS RD        \n";
echo "          " . date('Y-m-d H:i:s') . "\n";
echo "========================================\n\n";

// 1. INFORMACIÓN DEL ENTORNO
echo "[1] ENTORNO:\n";
echo "Document Root: " . $_SERVER['DOCUMENT_ROOT'] . "\n";
echo "Current Dir (__DIR__): " . __DIR__ . "\n";
echo "Server Name: " . $_SERVER['SERVER_NAME'] . "\n";
echo "PHP Version: " . phpversion() . "\n\n";

// 2. VERIFICACIÓN DE CARPETAS DE IMÁGENES
echo "[2] SISTEMA DE ARCHIVOS (IMÁGENES):\n";

$rutas_a_probar = [
    'assets/uploads',
    '../public/assets/uploads',
    'public/assets/uploads', // relativo si script corre desde root, pero estamos en public
];

foreach ($rutas_a_probar as $ruta) {
    if (is_dir($ruta)) {
        echo "✅ Carpeta encontrada: ($ruta)\n";
        echo "   Permisos: " . substr(sprintf('%o', fileperms($ruta)), -4) . "\n";
        echo "   Contenido (primeros 5):\n";
        $files = scandir($ruta);
        $count = 0;
        foreach ($files as $file) {
            if ($file == '.' || $file == '..')
                continue;
            echo "     - $file\n";
            $count++;
            if ($count >= 5)
                break;
        }
        if ($count == 0)
            echo "     (Carpeta VACÍA)\n";
    } else {
        echo "❌ Carpeta NO encontrada: ($ruta)\n";
    }
}
echo "\n";

// 3. BASE DE DATOS
echo "[3] BASE DE DATOS:\n";

// Autoload manual simple para cargar Database.php sin composer si falla
require_once __DIR__ . '/../src/Config/Database.php';

use App\Config\Database;

try {
    $db = Database::getConnection();
    echo "✅ Conexión Exitosa.\n";

    // Consultar una imagen de tour
    $stmt = $db->query("SELECT id, title, (SELECT image_path FROM tour_images WHERE tour_id = tours.id LIMIT 1) as img_path FROM tours LIMIT 3");
    $tours = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo "   Muestra de rutas guardadas en DB:\n";
    foreach ($tours as $t) {
        echo "   [ID: {$t['id']}] {$t['title']} \n";
        echo "      -> Ruta DB: '{$t['img_path']}'\n";

        // Simulación de URL
        $cleanPath = ltrim($t['img_path'], '/');
        echo "      -> URL Simulada: http://" . $_SERVER['SERVER_NAME'] . "/$cleanPath \n\n";
    }

} catch (Exception $e) {
    echo "❌ Error BD: " . $e->getMessage() . "\n";
}

// 4. LOGS DE ERRORES (Intento de lectura)
echo "[4] LOGS RECIENTES (Últimas líneas):\n";
$logFiles = ['error_log', '../error_log', 'php_error.log'];
$foundLog = false;
foreach ($logFiles as $logFile) {
    if (file_exists($logFile)) {
        echo "✅ Log encontrado: $logFile\n";
        $lines = file($logFile);
        $last = array_slice($lines, -15);
        foreach ($last as $l) {
            echo "   " . $l;
        }
        $foundLog = true;
        break;
    }
}
if (!$foundLog)
    echo "⚠️ No se encontró archivo de log estándar accesible.\n";

echo "\n========================================\n";
echo "           FIN DEL REPORTE              \n";
echo "========================================\n";
