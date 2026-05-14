<?php
class AuthController extends Controller
{
    private ?UserModel $users = null;

    public function loginForm(): void
    {
        if ($this->isAuthenticated()) {
            $this->redirect('/u/' . $this->currentUsername());
        }

        $this->render('auth/login', [
            'errors' => $this->pullFlash('errors', []),
            'old' => $this->pullFlash('old', []),
            'pageTitle' => 'Iniciar sesion',
            'galleryHref' => '/login',
            'headerActions' => [
                ['href' => '/register', 'label' => 'Crear cuenta', 'icon' => 'profile', 'visible' => true],
            ],
        ]);
    }

    public function login(): void
    {
        $this->requireCsrf();

        $email = trim((string) ($_POST['email'] ?? ''));
        $password = (string) ($_POST['password'] ?? '');
        $errors = [];

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Email no valido.';
        }
        if ($password === '') {
            $errors[] = 'La contrasena es obligatoria.';
        }

        if (empty($errors)) {
            $user = $this->users()->findByEmail($email);

            if ($user && password_verify($password, (string) $user['password_hash'])) {
                session_regenerate_id(true);
                $_SESSION['user_id'] = (int) $user['id'];
                $_SESSION['username'] = (string) $user['username'];
                $this->redirect('/u/' . $user['username']);
            }

            $errors[] = 'Email o contrasena incorrectos.';
        }

        $this->pushFlash('errors', $errors);
        $this->pushFlash('old', ['email' => $email]);
        $this->redirect('/login');
    }

    public function registerForm(): void
    {
        if ($this->isAuthenticated()) {
            $this->redirect('/u/' . $this->currentUsername());
        }

        $this->render('auth/register', [
            'errors' => $this->pullFlash('errors', []),
            'old' => $this->pullFlash('old', []),
            'pageTitle' => 'Registro',
            'galleryHref' => '/login',
            'headerActions' => [
                ['href' => '/login', 'label' => 'Iniciar sesion', 'icon' => 'login', 'visible' => true],
            ],
        ]);
    }

    public function register(): void
    {
        $this->requireCsrf();

        $username = strtolower(trim((string) ($_POST['username'] ?? '')));
        $displayName = trim((string) ($_POST['display_name'] ?? $username));
        $email = trim((string) ($_POST['email'] ?? ''));
        $password = (string) ($_POST['password'] ?? '');
        $errors = [];

        if (!preg_match('/^[a-z0-9_]{3,30}$/', $username)) {
            $errors[] = 'El usuario solo puede tener letras, numeros y _ (3-30 caracteres).';
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Email no valido.';
        }
        if (strlen($password) < 8) {
            $errors[] = 'La contrasena debe tener al menos 8 caracteres.';
        }

        if (empty($errors) && $this->users()->usernameExists($username)) {
            $errors[] = 'Ese nombre de usuario ya esta en uso.';
        }

        if (empty($errors) && $this->users()->emailExists($email)) {
            $errors[] = 'Ese email ya esta registrado.';
        }

        if (empty($errors)) {
            $id = $this->users()->create(
                $username,
                $displayName,
                $email,
                password_hash($password, PASSWORD_BCRYPT)
            );

            session_regenerate_id(true);
            $_SESSION['user_id'] = $id;
            $_SESSION['username'] = $username;
            $this->redirect('/u/' . $username);
        }

        $this->pushFlash('errors', $errors);
        $this->pushFlash('old', compact('username', 'email'));
        $this->redirect('/register');
    }

    public function logout(): void
    {
        session_destroy();
        $this->redirect('/login');
    }

    private function users(): UserModel
    {
        return $this->users ??= new UserModel();
    }
}
