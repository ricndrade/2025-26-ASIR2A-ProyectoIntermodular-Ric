<?php
abstract class Controller
{
    protected function render(string $view, array $data = []): void
    {
        extract($data + [
            'csrfToken' => $this->csrfToken(),
        ], EXTR_SKIP);

        require ROOT_PATH . '/app/views/' . $view . '.php';
    }

    protected function redirect(string $path): void
    {
        header('Location: ' . $path);
        exit;
    }

    protected function pushFlash(string $key, mixed $value): void
    {
        $_SESSION[$key] = $value;
    }

    protected function pullFlash(string $key, mixed $default = null): mixed
    {
        $value = $_SESSION[$key] ?? $default;
        unset($_SESSION[$key]);

        return $value;
    }

    protected function requireCsrf(): void
    {
        $token = $_POST['csrf_token'] ?? '';

        if ($token === '' || !hash_equals($this->csrfToken(), $token)) {
            http_response_code(403);
            die('Token CSRF invalido.');
        }
    }

    protected function csrfToken(): string
    {
        return (string) ($_SESSION['csrf_token'] ?? '');
    }

    protected function isAuthenticated(): bool
    {
        return isLoggedIn();
    }

    protected function currentUsername(): ?string
    {
        return isset($_SESSION['username']) ? (string) $_SESSION['username'] : null;
    }

    protected function requireAuthUserId(): int
    {
        requireLogin();

        return (int) $_SESSION['user_id'];
    }

    protected function buildMemberShell(string $pageTitle): array
    {
        return [
            'pageTitle' => $pageTitle,
            'galleryHref' => '/u/' . $this->currentUsername(),
            'headerActions' => [
                ['href' => '/upload', 'label' => 'Subir foto', 'icon' => 'upload', 'visible' => true],
                ['href' => '/settings', 'label' => 'Editar perfil', 'icon' => 'settings', 'visible' => true],
                ['href' => '/search', 'label' => 'Buscar', 'icon' => 'search', 'visible' => true],
                ['href' => '/logout', 'label' => 'Cerrar sesion', 'icon' => 'logout', 'visible' => true],
            ],
        ];
    }

    protected function buildGuestShell(string $pageTitle): array
    {
        return [
            'pageTitle' => $pageTitle,
            'galleryHref' => '/login',
            'headerActions' => [
                ['href' => '/search', 'label' => 'Buscar', 'icon' => 'search', 'visible' => true],
                ['href' => '/login', 'label' => 'Perfil o iniciar sesion', 'icon' => 'profile', 'visible' => true],
            ],
        ];
    }
}
