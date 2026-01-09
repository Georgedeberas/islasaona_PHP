<?php
// MOCHILEROS RD - MCP BRIDGE (Puente de Control para IA)
// Sube este archivo a: /public/mcp_bridge.php
// -------------------------------------------------------------------

header('Content-Type: application/json');
header("Access-Control-Allow-Origin: *"); // Permite acceso remoto
ini_set('display_errors', 0); // Ocultar errores HTML para no romper el JSON

// ===================================================================
// 🔐 CONFIGURACIÓN DE SEGURIDAD (¡EDITA ESTO ANTES DE SUBIR!)
// ===================================================================

// 1. Define aquí tu clave maestra (invéntate una larga):
$SECRET_TOKEN = "Mochileros_Secret_Key_998877";

// 2. Define aquí los datos de tu Base de Datos de iPage:
$DB_HOST = "nexosystem.yourwebhostingmysql.com";        // Generalmente es 'localhost' en iPage
$DB_NAME = "mochilerosrd_islasaona";  // El nombre de tu base de datos
$DB_USER = "islasaona"; // Tu usuario MySQL
$DB_PASS = "Islasaonaervi123456";   // Tu contraseña MySQL

// ===================================================================
// 🛑 NO EDITAR NADA DEBAJO DE ESTA LÍNEA
// ===================================================================

$ALLOWED_ROOT = __DIR__; // Solo permite tocar archivos desde 'public' hacia abajo
$PARENT_ROOT = dirname(__DIR__); // Permite subir un nivel para acceder a 'src'

// --- VERIFICACIÓN DE SEGURIDAD (AUTH) ---
$headers = getallheaders();
$authHeader = $headers['Authorization'] ?? $_SERVER['HTTP_AUTHORIZATION'] ?? '';

// Extraer token Bearer
if (preg_match('/Bearer\s(\S+)/', $authHeader, $matches)) {
    $providedToken = $matches[1];
} else {
    $providedToken = $authHeader;
}

// Si la clave no coincide, bloquear acceso
if ($providedToken !== $SECRET_TOKEN) {
    http_response_code(403);
    echo json_encode(['status' => 'error', 'msg' => '⛔ ACCESO DENEGADO: Token incorrecto.']);
    exit;
}

// --- PROCESAR LA SOLICITUD ---
$input = json_decode(file_get_contents('php://input'), true);
$action = $input['action'] ?? '';
$file = $input['file'] ?? '';
$content = $input['content'] ?? '';
$sql = $input['query'] ?? '';

try {
    switch ($action) {
        // 1. PING (Prueba de vida)
        case 'ping':
            echo json_encode(['status' => 'ok', 'msg' => '✅ Puente MCP Activo y Seguro.']);
            break;

        // 2. GESTIÓN DE ARCHIVOS
        case 'listar':
            // Permite listar archivos en public o src si se especifica ruta relativa
            $targetPath = $PARENT_ROOT . '/' . ltrim($file, '/');
            if (!is_dir($targetPath))
                $targetPath = $ALLOWED_ROOT;

            $files = glob($targetPath . '/*');
            $result = [];
            foreach ($files as $f) {
                $result[] = basename($f) . (is_dir($f) ? '/' : '');
            }
            echo json_encode(['status' => 'ok', 'data' => $result, 'path' => $targetPath]);
            break;

        case 'leer':
            $targetPath = $PARENT_ROOT . '/' . ltrim($file, '/');
            if (!file_exists($targetPath))
                throw new Exception("Archivo no encontrado: $file");
            echo json_encode(['status' => 'ok', 'content' => file_get_contents($targetPath)]);
            break;

        case 'escribir':
            $targetPath = $PARENT_ROOT . '/' . ltrim($file, '/');
            $dir = dirname($targetPath);
            if (!is_dir($dir))
                mkdir($dir, 0755, true); // Crear carpeta si no existe

            if (file_put_contents($targetPath, $content) === false) {
                throw new Exception("No se pudo escribir en el archivo. Verifica permisos.");
            }
            echo json_encode(['status' => 'ok', 'msg' => 'Archivo guardado exitosamente.']);
            break;

        case 'borrar':
            $targetPath = $PARENT_ROOT . '/' . ltrim($file, '/');
            if (is_file($targetPath)) {
                unlink($targetPath);
                echo json_encode(['status' => 'ok', 'msg' => 'Archivo eliminado.']);
            } else {
                throw new Exception("El objetivo no es un archivo o no existe.");
            }
            break;

        // 3. BASE DE DATOS (PODER ABSOLUTO)
        case 'sql_query':
            // Conexión On-Demand
            $dsn = "mysql:host=$DB_HOST;dbname=$DB_NAME;charset=utf8mb4";
            $pdo = new PDO($dsn, $DB_USER, $DB_PASS, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
            ]);

            // Ejecutar Query
            $stmt = $pdo->prepare($sql);
            $params = $input['params'] ?? [];
            $stmt->execute($params);

            // Si es SELECT o SHOW devuelve datos, si no, devuelve éxito
            if (stripos(trim($sql), 'SELECT') === 0 || stripos(trim($sql), 'SHOW') === 0) {
                echo json_encode(['status' => 'ok', 'data' => $stmt->fetchAll()]);
            } else {
                echo json_encode(['status' => 'ok', 'msg' => 'Consulta ejecutada. Filas afectadas: ' . $stmt->rowCount()]);
            }
            break;

        default:
            throw new Exception("Accion no reconocida: " . $action);
    }

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'msg' => $e->getMessage()]);
}
?>