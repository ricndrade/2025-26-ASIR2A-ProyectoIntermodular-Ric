<?php
class SettingsController {

    public function index(): void {
        requireLogin();
        $db   = getDB();
        $stmt = $db->prepare("SELECT * FROM users WHERE id = ? LIMIT 1");
        $stmt->execute([$_SESSION['user_id']]);
        $user = $stmt->fetch();

        require ROOT_PATH . '/app/views/settings/index.php';
    }

    public function update(): void {
        requireLogin();
        $this->checkCsrf();

        $action = $_POST['action'] ?? '';

        match($action) {
            'profile'       => $this->updateProfile(),
            'upload_photo'  => $this->uploadPhoto(),
            'delete_photo'  => $this->deletePhoto(),
            'delete_account'=> $this->deleteAccount(),
            default         => header('Location: /settings')
        };
    }

    private function updateProfile(): void {
        $username     = trim($_POST['username']     ?? '');
        $display_name = trim($_POST['display_name'] ?? '');
        $bio          = trim($_POST['bio']          ?? '');
        $errors       = [];

        if (!preg_match('/^[a-zA-Z0-9_]{3,30}$/', $username)) {
            $errors[] = 'Usuario inválido (letras, números y _, 3-30 caracteres).';
        }
        if (empty($display_name)) {
            $errors[] = 'El nombre público no puede estar vacío.';
        }

        if (empty($errors)) {
            $db = getDB();

            // Comprobar que el username no lo use otro
            $stmt = $db->prepare("SELECT id FROM users WHERE username = ? AND id != ? LIMIT 1");
            $stmt->execute([$username, $_SESSION['user_id']]);
            if ($stmt->fetch()) {
                $errors[] = 'Ese nombre de usuario ya está en uso.';
            }
        }

        if (empty($errors)) {
            $db   = getDB();
            $stmt = $db->prepare(
                "UPDATE users SET username = ?, display_name = ?, bio = ? WHERE id = ?"
            );
            $stmt->execute([$username, $display_name, $bio, $_SESSION['user_id']]);
            $_SESSION['username'] = $username;
            header('Location: /u/' . $username);
            exit;
        }

        $_SESSION['errors'] = $errors;
        header('Location: /settings');
        exit;
    }

    private function uploadPhoto(): void {
        $caption = trim($_POST['caption'] ?? '');
        $file    = $_FILES['photo'] ?? null;
        $errors  = [];

        if (!$file || $file['error'] !== UPLOAD_ERR_OK) {
            $errors[] = 'Error al subir el archivo.';
        } else {
            $allowed = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];
            $mime    = mime_content_type($file['tmp_name']);

            if (!in_array($mime, $allowed)) {
                $errors[] = 'Solo se permiten imágenes JPG, PNG, WEBP o GIF.';
            }
            if ($file['size'] > 5 * 1024 * 1024) {
                $errors[] = 'La imagen no puede superar 5MB.';
            }
        }

        if (empty($errors)) {
            $ext      = pathinfo($file['name'], PATHINFO_EXTENSION);
            $filename = uniqid('photo_', true) . '.' . strtolower($ext);
            $dest     = ROOT_PATH . '/public/uploads/' . $filename;

            if (move_uploaded_file($file['tmp_name'], $dest)) {
                $db   = getDB();
                $stmt = $db->prepare(
                    "INSERT INTO photos (user_id, image_path, caption) VALUES (?, ?, ?)"
                );
                $stmt->execute([$_SESSION['user_id'], $filename, $caption]);
            } else {
                $errors[] = 'No se pudo guardar la imagen.';
            }
        }

        if (!empty($errors)) {
            $_SESSION['errors'] = $errors;
        }

        header('Location: /settings');
        exit;
    }

    private function deletePhoto(): void {
        $photo_id = (int)($_POST['photo_id'] ?? 0);
        $db       = getDB();

        // Verificar que la foto pertenece al usuario
        $stmt = $db->prepare("SELECT * FROM photos WHERE id = ? AND user_id = ? LIMIT 1");
        $stmt->execute([$photo_id, $_SESSION['user_id']]);
        $photo = $stmt->fetch();

        if ($photo) {
            $file = ROOT_PATH . '/public/uploads/' . $photo['image_path'];
            if (file_exists($file)) unlink($file);

            $stmt = $db->prepare("DELETE FROM photos WHERE id = ?");
            $stmt->execute([$photo_id]);
        }

        header('Location: /u/' . $_SESSION['username']);
        exit;
    }

    private function deleteAccount(): void {
        $db   = getDB();

        // Borrar fotos del disco
        $stmt = $db->prepare("SELECT image_path FROM photos WHERE user_id = ?");
        $stmt->execute([$_SESSION['user_id']]);
        foreach ($stmt->fetchAll() as $photo) {
            $file = ROOT_PATH . '/public/uploads/' . $photo['image_path'];
            if (file_exists($file)) unlink($file);
        }

        $stmt = $db->prepare("DELETE FROM users WHERE id = ?");
        $stmt->execute([$_SESSION['user_id']]);

        session_destroy();
        header('Location: /register');
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