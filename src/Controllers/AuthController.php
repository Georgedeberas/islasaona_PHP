<?php
session_start();

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../Models/User.php';

class AuthController
{
    public function login()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
            $password = $_POST['password'] ?? '';

            $userModel = new User();
            $user = $userModel->findByEmail($email);

            if ($user && password_verify($password, $user['password_hash'])) {
                // Regenerar ID de sesión para prevenir Session Fixation
                session_regenerate_id(true);
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_role'] = $user['role'];
                $_SESSION['user_email'] = $user['email'];

                header('Location: /admin/dashboard');
                exit;
            } else {
                $error = "Credenciales inválidas.";
                require __DIR__ . '/../Views/admin/login.php';
            }
        } else {
            // Si ya está logueado, ir al dashboard
            if (isset($_SESSION['user_id'])) {
                header('Location: /admin/dashboard');
                exit;
            }
            require __DIR__ . '/../Views/admin/login.php';
        }
    }

    public function logout()
    {
        session_destroy();
        header('Location: /admin/login');
        exit;
    }

    public static function isAuthenticated()
    {
        return isset($_SESSION['user_id']);
    }

    public static function requireLogin()
    {
        if (!self::isAuthenticated()) {
            header('Location: /admin/login');
            exit;
        }
    }
}
