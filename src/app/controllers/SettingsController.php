<?php
class SettingsController extends Controller
{
    private UserModel $users;
    private PhotoModel $photos;
    private ImageStorage $storage;

    public function __construct()
    {
        $this->users = new UserModel();
        $this->photos = new PhotoModel();
        $this->storage = new ImageStorage();
    }

    public function index(): void
    {
        $userId = $this->requireAuthUserId();
        $user = $this->users->findById($userId);

        if (!$user) {
            session_destroy();
            $this->redirect('/login');
        }

        $this->render('settings/index', $this->buildMemberShell('Configuracion') + [
            'user' => $user,
            'errors' => $this->pullFlash('errors', []),
        ]);
    }

    public function update(): void
    {
        $this->requireAuthUserId();
        $this->requireCsrf();

        $action = (string) ($_POST['action'] ?? '');
        if ($action === '' && (string) ($_SERVER['REQUEST_URI'] ?? '') === '/settings/borrar') {
            $action = 'delete_photo';
        }

        match ($action) {
            'profile' => $this->updateProfile(),
            'upload_profile_image' => $this->uploadProfileImage(),
            'delete_profile_image' => $this->deleteProfileImage(),
            'upload_photo' => $this->uploadPhoto(),
            'delete_photo' => $this->deletePhoto(),
            'delete_account' => $this->deleteAccount(),
            default => $this->redirect('/settings'),
        };
    }

    private function updateProfile(): void
    {
        $userId = $this->requireAuthUserId();
        $username = strtolower(trim((string) ($_POST['username'] ?? '')));
        $displayName = trim((string) ($_POST['display_name'] ?? ''));
        $bio = trim((string) ($_POST['bio'] ?? ''));
        $errors = [];

        if (!preg_match('/^[a-z0-9_]{3,30}$/', $username)) {
            $errors[] = 'Usuario invalido (letras, numeros y _, 3-30 caracteres).';
        }
        if ($displayName === '') {
            $errors[] = 'El nombre publico no puede estar vacio.';
        } elseif (strlen($displayName) > 100) {
            $errors[] = 'El nombre publico no puede superar 100 caracteres.';
        }
        if (empty($errors) && $this->users->usernameExists($username, $userId)) {
            $errors[] = 'Ese nombre de usuario ya esta en uso.';
        }

        if (empty($errors)) {
            $this->users->updateProfile($userId, $username, $displayName, $bio);
            $_SESSION['username'] = $username;
            $this->redirect('/u/' . $username);
        }

        $this->pushFlash('errors', $errors);
        $this->redirect('/settings');
    }

    private function uploadPhoto(): void
    {
        $userId = $this->requireAuthUserId();
        $caption = trim((string) ($_POST['caption'] ?? ''));
        $upload = $this->storage->storePhotoUpload($_FILES['photo'] ?? null);

        if (empty($upload['errors'])) {
            $this->photos->create($userId, (string) $upload['filename'], $caption);
        } else {
            $this->pushFlash('errors', $upload['errors']);
        }

        $this->redirect('/settings');
    }

    private function deletePhoto(): void
    {
        $userId = $this->requireAuthUserId();
        $photoId = (int) ($_POST['photo_id'] ?? 0);
        $photo = $this->photos->findOwnedById($photoId, $userId);

        if ($photo) {
            $this->storage->delete((string) $photo['image_path']);
            $this->photos->deleteById($photoId);
        }

        $this->redirect('/u/' . $this->currentUsername());
    }

    private function deleteAccount(): void
    {
        $userId = $this->requireAuthUserId();

        foreach ($this->photos->listImagePathsByUserId($userId) as $imagePath) {
            $this->storage->delete($imagePath);
        }

        $this->storage->delete($this->users->getProfileImage($userId));
        $this->users->deleteById($userId);

        session_destroy();
        $this->redirect('/register');
    }

    private function uploadProfileImage(): void
    {
        $userId = $this->requireAuthUserId();
        $oldImage = $this->users->getProfileImage($userId);
        $upload = $this->storage->storeAvatarUpload($_FILES['profile_image'] ?? null, $userId);

        if (empty($upload['errors'])) {
            if ($oldImage && $oldImage !== $upload['filename']) {
                $this->storage->delete($oldImage);
            }

            $this->users->updateProfileImage($userId, (string) $upload['filename']);
        } else {
            $this->pushFlash('errors', $upload['errors']);
        }

        $this->redirect('/settings');
    }

    private function deleteProfileImage(): void
    {
        $userId = $this->requireAuthUserId();
        $image = $this->users->getProfileImage($userId);

        if ($image) {
            $this->storage->delete($image);
            $this->users->updateProfileImage($userId, null);
        }

        $this->redirect('/settings');
    }
}
