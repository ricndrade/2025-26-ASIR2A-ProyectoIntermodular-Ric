<?php
class ImageStorage
{
    private const PHOTO_ALLOWED_MIME_TYPES = [
        'image/jpeg', 'image/png', 'image/webp', 'image/gif',
        'image/heic', 'image/heif',  // <- añadido para iPhone
    ];
    private const AVATAR_ALLOWED_MIME_TYPES = [
        'image/jpeg', 'image/png', 'image/webp',
        'image/heic', 'image/heif',
    ];
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
            'Solo se permiten imágenes JPG, PNG, WEBP, GIF o HEIC.',
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
            'Solo se permiten imágenes JPG, PNG, WEBP o HEIC.',
            'La foto de perfil no puede superar 2MB.',
            'No se pudo guardar la imagen.'
        );
    }

    public function delete(?string $filename): void
    {
        if (!$filename) return;
        $path = $this->uploadDir . '/' . basename($filename);
        if (is_file($path)) unlink($path);
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

        $errors  = [];
        $tmpName = (string)($file['tmp_name'] ?? '');
        $mime    = is_file($tmpName) ? mime_content_type($tmpName) : false;

        if ($mime === false || !in_array($mime, $allowedMimeTypes, true)) {
            $errors[] = $mimeError;
        }
        if ((int)($file['size'] ?? 0) > $maxBytes) {
            $errors[] = $sizeError;
        }
        if (!empty($errors)) {
            return ['filename' => null, 'errors' => $errors];
        }

        // Nombre final siempre en .webp
        $filename    = uniqid($prefix, true) . '.webp';
        $destination = $this->uploadDir . '/' . $filename;

        $converted = $this->convertToWebp($tmpName, (string)$mime, $destination);

        if (!$converted) {
            return ['filename' => null, 'errors' => [$saveError]];
        }

        return ['filename' => $filename, 'errors' => []];
    }

    private function convertToWebp(string $tmpPath, string $mime, string $dest): bool
    {
        // HEIC/HEIF requiere ImageMagick
        if (in_array($mime, ['image/heic', 'image/heif'], true)) {
            return $this->convertHeicWithImagick($tmpPath, $dest);
        }

        // El resto con GD
        $image = match($mime) {
            'image/jpeg' => imagecreatefromjpeg($tmpPath),
            'image/png'  => $this->pngWithAlpha($tmpPath),
            'image/gif'  => imagecreatefromgif($tmpPath),
            'image/webp' => imagecreatefromwebp($tmpPath),
            default      => false,
        };

        if (!$image) return false;

        $result = imagewebp($image, $dest, 85);
        imagedestroy($image);
        return $result;
    }

    // PNG puede tener transparencia — hay que preservarla
    private function pngWithAlpha(string $tmpPath): \GdImage|false
    {
        $image = imagecreatefrompng($tmpPath);
        if (!$image) return false;

        imagealphablending($image, true);
        imagesavealpha($image, true);
        return $image;
    }

    private function convertHeicWithImagick(string $tmpPath, string $dest): bool
    {
        if (!extension_loaded('imagick')) return false;

        try {
            $imagick = new Imagick($tmpPath);   // Imagick no es un error, PHP no sabe que la extensión no está en el entorno del IDE.
            $imagick->setImageFormat('webp');
            $imagick->setImageCompressionQuality(85);
            $imagick->writeImage($dest);
            $imagick->destroy();
            return true;
        } catch (\Exception) {
            return false;
        }
    }

    // resolveExtension ya no se usa (siempre .webp), la dejamos por si acaso
    private function resolveExtension(string $originalName, string $mimeType): string
    {
        $ext = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
        if ($ext !== '') {
            return preg_replace('/[^a-z0-9]+/', '', $ext) ?: 'bin';
        }
        return match($mimeType) {
            'image/jpeg' => 'jpg',
            'image/png'  => 'png',
            'image/webp' => 'webp',
            'image/gif'  => 'gif',
            default      => 'bin',
        };
    }
}