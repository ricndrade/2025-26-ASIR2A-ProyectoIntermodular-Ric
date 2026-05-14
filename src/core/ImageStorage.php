<?php
class ImageStorage
{
    private const PHOTO_ALLOWED_MIME_TYPES = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];
    private const AVATAR_ALLOWED_MIME_TYPES = ['image/jpeg', 'image/png', 'image/webp'];
    private const PHOTO_MAX_BYTES = 5242880;
    private const AVATAR_MAX_BYTES = 2097152;

    private string $uploadDir;

    public function __construct(?string $uploadDir = null)
    {
        $this->uploadDir = $uploadDir ?? ROOT_PATH . '/public/uploads';
    }

    public function storePhotoUpload(?array $file): array
    {
        return $this->storeUpload(
            $file,
            'photo_',
            self::PHOTO_ALLOWED_MIME_TYPES,
            self::PHOTO_MAX_BYTES,
            'Error al subir el archivo.',
            'Solo se permiten imagenes JPG, PNG, WEBP o GIF.',
            'La imagen no puede superar 5MB.',
            'No se pudo guardar la imagen.'
        );
    }

    public function storeAvatarUpload(?array $file, int $userId): array
    {
        return $this->storeUpload(
            $file,
            'avatar_' . $userId . '_',
            self::AVATAR_ALLOWED_MIME_TYPES,
            self::AVATAR_MAX_BYTES,
            'Error al subir la imagen.',
            'Solo se permiten imagenes JPG, PNG o WEBP.',
            'La foto de perfil no puede superar 2MB.',
            'No se pudo guardar la imagen.'
        );
    }

    public function delete(?string $filename): void
    {
        if (!$filename) {
            return;
        }

        $path = $this->uploadDir . '/' . basename($filename);
        if (is_file($path)) {
            unlink($path);
        }
    }

    private function storeUpload(
        ?array $file,
        string $prefix,
        array $allowedMimeTypes,
        int $maxBytes,
        string $uploadError,
        string $mimeError,
        string $sizeError,
        string $saveError
    ): array {
        if (!$file || ($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
            return ['filename' => null, 'errors' => [$uploadError]];
        }

        $errors = [];
        $tmpName = (string) ($file['tmp_name'] ?? '');
        $mimeType = is_file($tmpName) ? mime_content_type($tmpName) : false;

        if ($mimeType === false || !in_array($mimeType, $allowedMimeTypes, true)) {
            $errors[] = $mimeError;
        }
        if ((int) ($file['size'] ?? 0) > $maxBytes) {
            $errors[] = $sizeError;
        }
        if (!empty($errors)) {
            return ['filename' => null, 'errors' => $errors];
        }

        $extension = $this->resolveExtension((string) ($file['name'] ?? ''), (string) $mimeType);
        $filename = uniqid($prefix, true) . '.' . $extension;
        $destination = $this->uploadDir . '/' . $filename;

        if (!move_uploaded_file($tmpName, $destination)) {
            return ['filename' => null, 'errors' => [$saveError]];
        }

        return ['filename' => $filename, 'errors' => []];
    }

    private function resolveExtension(string $originalName, string $mimeType): string
    {
        $extension = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
        if ($extension !== '') {
            return preg_replace('/[^a-z0-9]+/', '', $extension) ?: 'bin';
        }

        return match ($mimeType) {
            'image/jpeg' => 'jpg',
            'image/png' => 'png',
            'image/webp' => 'webp',
            'image/gif' => 'gif',
            default => 'bin',
        };
    }
}
