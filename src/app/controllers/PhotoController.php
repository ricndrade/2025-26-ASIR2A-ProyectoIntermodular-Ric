<?php
class PhotoController extends Controller
{
    private PhotoModel $photos;
    private UserModel $users;

    public function __construct()
    {
        $this->photos = new PhotoModel();
        $this->users = new UserModel();
    }

    public function show(int $photoId): void
    {
        $photo = $this->photos->findById($photoId);
        if (!$photo) {
            http_response_code(404);
            echo 'Foto no encontrada.';
            return;
        }

        $user = $this->users->findById((int) $photo['user_id']);
        if (!$user) {
            http_response_code(404);
            echo 'Usuario no encontrado.';
            return;
        }

        $pageTitle = 'Foto';
        $shell = $this->isAuthenticated()
            ? $this->buildMemberShell($pageTitle)
            : $this->buildGuestShell($pageTitle);

        $this->render('photo/show', $shell + [
            'photo' => $photo,
            'user' => $user,
        ]);
    }
}
