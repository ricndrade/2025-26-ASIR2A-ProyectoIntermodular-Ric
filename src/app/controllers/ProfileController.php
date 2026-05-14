<?php
class ProfileController {

    public function show(string $username): void {
        $db   = getDB();
        $stmt = $db->prepare("SELECT * FROM users WHERE username = ? LIMIT 1");
        $stmt->execute([$username]);
        $user = $stmt->fetch();

        if (!$user) {
            http_response_code(404);
            echo "Usuario no encontrado.";
            return;
        }

        $stmt = $db->prepare("SELECT * FROM photos WHERE user_id = ? ORDER BY created_at DESC");
        $stmt->execute([$user['id']]);
        $fotos = $stmt->fetchAll();

        $isLoggedIn = isLoggedIn();
        $isOwner    = $isLoggedIn && $_SESSION['username'] === $user['username'];
        $pageTitle  = $user['display_name'] ?: $user['username'];
        $galleryHref = $isLoggedIn ? '/u/' . $_SESSION['username'] : '/login';
        $headerActions = $isLoggedIn
            ? [
                ['href' => '/upload', 'label' => 'Subir foto', 'icon' => 'upload', 'visible' => true],
                ['href' => '/settings', 'label' => 'Editar perfil', 'icon' => 'settings', 'visible' => true],
                ['href' => '/search', 'label' => 'Buscar', 'icon' => 'search', 'visible' => true],
                ['href' => '/logout', 'label' => 'Cerrar sesion', 'icon' => 'logout', 'visible' => true],
            ]
            : [
                ['href' => '/search', 'label' => 'Buscar', 'icon' => 'search', 'visible' => true],
                ['href' => '/login', 'label' => 'Perfil o iniciar sesion', 'icon' => 'profile', 'visible' => true],
            ];

        require ROOT_PATH . '/app/views/profile/show.php';
    }
}
