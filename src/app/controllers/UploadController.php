<?php
class UploadController {

    public function index(): void {
        requireLogin();
        require ROOT_PATH . '/app/views/upload/index.php';
    }

    public function store(): void {
        requireLogin();
        $this->checkCsrf();

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
                header('Location: /u/' . $_SESSION['username']);
                exit;
            }
            $errors[] = 'No se pudo guardar la imagen.';
        }

        $_SESSION['errors'] = $errors;
        header('Location: /upload');
        exit;
    }

    public function destroy(): void {
        requireLogin();
        $this->checkCsrf();

        $photo_id = (int)($_POST['photo_id'] ?? 0);
        $db       = getDB();

        $stmt = $db->prepare("SELECT * FROM photos WHERE id = ? AND user_id = ? LIMIT 1");
        $stmt->execute([$photo_id, $_SESSION['user_id']]);
        $photo = $stmt->fetch();

        if ($photo) {
            $file = ROOT_PATH . '/public/uploads/' . $photo['image_path'];
            if (file_exists($file)) unlink($file);

            $stmt = $db->prepare("DELETE FROM photos WHERE id = ?");
            $stmt->execute([$photo_id]);
        }

        header('Location: /upload');
        exit;
    }

    public function editCaption(): void {
        requireLogin();
        $this->checkCsrf();

        $photo_id = (int)($_POST['photo_id'] ?? 0);
        $caption  = trim($_POST['caption']   ?? '');

        $db   = getDB();
        $stmt = $db->prepare(
            "UPDATE photos SET caption = ? WHERE id = ? AND user_id = ?"
        );
        $stmt->execute([$caption, $photo_id, $_SESSION['user_id']]);

        header('Location: /upload');
        exit;
    }

    private function checkCsrf(): void {
        if (empty($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
            http_response_code(403);
            die('Token CSRF inválido.');
        }
    }
}

