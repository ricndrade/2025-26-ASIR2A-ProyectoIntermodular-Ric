<?php
class AuthController {

    public function loginForm(): void {
        if (isset($_SESSION['user_id'])) {
            header('Location: /u/' . $_SESSION['username']);
            exit;
        }
        require ROOT_PATH . '/app/views/auth/login.php';
    }

    public function login(): void {
        $this->checkCsrf();

        $email    = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $errors   = [];

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Email no válido.';
        }
        if (empty($password)) {
            $errors[] = 'La contraseña es obligatoria.';
        }

        if (empty($errors)) {
            $db   = getDB();
            $stmt = $db->prepare("SELECT * FROM users WHERE email = ? LIMIT 1");
            $stmt->execute([$email]);
            $user = $stmt->fetch();

            if ($user && password_verify($password, $user['password_hash'])) {
                session_regenerate_id(true);
                $_SESSION['user_id']  = $user['id'];
                $_SESSION['username'] = $user['username'];
                header('Location: /u/' . $user['username']);
                exit;
            }
            $errors[] = 'Email o contraseña incorrectos.';
        }

        $_SESSION['errors'] = $errors;
        $_SESSION['old']    = ['email' => $email];
        header('Location: /login');
        exit;
    }

    public function registerForm(): void {
        if (isset($_SESSION['user_id'])) {
            header('Location: /u/' . $_SESSION['username']);
            exit;
        }
        require ROOT_PATH . '/app/views/auth/register.php';
    }

    public function register(): void {
        $this->checkCsrf();

        $username     = trim($_POST['username']     ?? '');
        $display_name = trim($_POST['display_name'] ?? $username); // si no lo manda, usa el username
        $email        = trim($_POST['email']        ?? '');
        $password     = $_POST['password']          ?? '';
        $errors       = [];

        if (!preg_match('/^[a-zA-Z0-9_]{3,30}$/', $username)) {
            $errors[] = 'El usuario solo puede tener letras, números y _ (3-30 caracteres).';
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Email no válido.';
        }
        if (strlen($password) < 8) {
            $errors[] = 'La contraseña debe tener al menos 8 caracteres.';
        }

        if (empty($errors)) {
            $db = getDB();

            $stmt = $db->prepare("SELECT id FROM users WHERE username = ? LIMIT 1");
            $stmt->execute([$username]);
            if ($stmt->fetch()) {
                $errors[] = 'Ese nombre de usuario ya está en uso.';
            }

            $stmt = $db->prepare("SELECT id FROM users WHERE email = ? LIMIT 1");
            $stmt->execute([$email]);
            if ($stmt->fetch()) {
                $errors[] = 'Ese email ya está registrado.';
            }
        }

        if (empty($errors)) {
            $db   = getDB();
            $hash = password_hash($password, PASSWORD_BCRYPT);
            $stmt = $db->prepare(
                "INSERT INTO users (username, display_name, email, password_hash) VALUES (?, ?, ?, ?)"
            );
            $stmt->execute([$username, $display_name, $email, $hash]);

            $id = $db->lastInsertId();
            session_regenerate_id(true);
            $_SESSION['user_id']  = $id;
            $_SESSION['username'] = $username;
            header('Location: /u/' . $username);
            exit;
        }

        $_SESSION['errors'] = $errors;
        $_SESSION['old']    = compact('username', 'email');
        header('Location: /register');
        exit;
    }

    public function logout(): void {
        session_destroy();
        header('Location: /login');
        exit;
    }

    private function checkCsrf(): void {
        if (
            empty($_POST['csrf_token']) ||
            !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])
        ) {
            http_response_code(403);
            die('Token CSRF inválido.');
        }
    }
}