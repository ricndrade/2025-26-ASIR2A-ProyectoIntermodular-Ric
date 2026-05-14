<?php
class UploadController extends Controller
{
    private PhotoModel $photos;
    private ImageStorage $storage;

    public function __construct()
    {
        $this->photos = new PhotoModel();
        $this->storage = new ImageStorage();
    }

    public function index(): void
    {
        $userId = $this->requireAuthUserId();

        $this->render('upload/index', $this->buildMemberShell('Subir foto') + [
            'errors' => $this->pullFlash('errors', []),
            'fotos' => $this->photos->findByUserId($userId),
        ]);
    }

    public function store(): void
    {
        $userId = $this->requireAuthUserId();
        $this->requireCsrf();

        $caption = trim((string) ($_POST['caption'] ?? ''));
        $upload = $this->storage->storePhotoUpload($_FILES['photo'] ?? null);

        if (empty($upload['errors'])) {
            $this->photos->create($userId, (string) $upload['filename'], $caption);
            $this->redirect('/u/' . $this->currentUsername());
        }

        $this->pushFlash('errors', $upload['errors']);
        $this->redirect('/upload');
    }

    public function destroy(): void
    {
        $userId = $this->requireAuthUserId();
        $this->requireCsrf();

        $photoId = (int) ($_POST['photo_id'] ?? 0);
        $photo = $this->photos->findOwnedById($photoId, $userId);

        if ($photo) {
            $this->storage->delete((string) $photo['image_path']);
            $this->photos->deleteById($photoId);
        }

        $this->redirect('/upload');
    }

    public function editCaption(): void
    {
        $userId = $this->requireAuthUserId();
        $this->requireCsrf();

        $photoId = (int) ($_POST['photo_id'] ?? 0);
        $caption = trim((string) ($_POST['caption'] ?? ''));

        $this->photos->updateCaption($photoId, $userId, $caption);
        $this->redirect('/upload');
    }
}
