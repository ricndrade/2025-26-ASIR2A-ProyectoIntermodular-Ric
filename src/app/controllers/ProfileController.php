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

        require ROOT_PATH . '/app/views/profile/show.php';
    }
}