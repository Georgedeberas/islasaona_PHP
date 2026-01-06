<?php
// Script de emergencia para resetear password de admin
require_once __DIR__ . '/../config/database.php';

try {
    $db = Database::getConnection();

    // 1. Definir password nuevo y generar hash fresco
    $newPassword = 'admin123';
    $newHash = password_hash($newPassword, PASSWORD_DEFAULT);
    $email = 'admin@mochilerosrd.com';

    // 2. Verificar si el usuario existe primero
    $stmtCheck = $db->prepare("SELECT id FROM users WHERE email = :email");
    $stmtCheck->bindParam(':email', $email);
    $stmtCheck->execute();

    if ($stmtCheck->rowCount() > 0) {
        // 3. Update
        $sql = "UPDATE users SET password_hash = :hash WHERE email = :email";
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':hash', $newHash);
        $stmt->bindParam(':email', $email);
        $stmt->execute();

        echo "<h1>Éxito: Contraseña Reseteada</h1>";
        echo "<p>La contraseña para <strong>$email</strong> ha sido actualizada a: <strong>$newPassword</strong></p>";
        echo "<p><a href='/admin/login'>Ir al Login</a></p>";
    } else {
        // Crear usuario si no existe (por si acaso el install.php falló silenciosamente en insert)
        $sql = "INSERT INTO users (email, password_hash, role) VALUES (:email, :hash, 'admin')";
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':hash', $newHash);
        $stmt->execute();

        echo "<h1>Éxito: Usuario Admin Creado</h1>";
        echo "<p>Se creó el usuario <strong>$email</strong> con clave: <strong>$newPassword</strong></p>";
        echo "<p><a href='/admin/login'>Ir al Login</a></p>";
    }

    echo "<br><p style='color:red'>POR FAVOR BORRA ESTE ARCHIVO (reset_admin.php) DEL SERVIDOR UNA VEZ LOGUEADO.</p>";

} catch (PDOException $e) {
    die("Error de Base de Datos: " . $e->getMessage());
}
