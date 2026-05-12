<?php
class AuthController {

    public function loginForm(): void {
        require __DIR__ . '/../views/auth/login.php';
    }

    public function login(): void {
        $email    = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
        $password = $_POST['password'] ?? '';

        // Protección CSRF básica
        if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
            die('Token CSRF inválido');
        }

        $db   = getDB();
        $stmt = $db->prepare("SELECT * FROM usuarios WHERE email = ? LIMIT 1");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password_hash'])) {
            session_regenerate_id(true); // previene session fixation
            $_SESSION['user_id']  = $user['id'];
            $_SESSION['user_rol'] = $user['rol'];
            $_SESSION['user_nombre'] = $user['nombre'];
            header('Location: /galeria');
        } else {
            header('Location: /login?error=1');
        }
        exit;
    }

    public function logout(): void {
        session_destroy();
        header('Location: /login');
        exit;
    }
}