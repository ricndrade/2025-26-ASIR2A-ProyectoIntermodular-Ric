<?php
class ProfileController extends Controller
{
    private UserModel $users;
    private PhotoModel $photos;

    public function __construct()
    {
        $this->users = new UserModel();
        $this->photos = new PhotoModel();
    }

    public function show(string $username): void
    {
        $user = $this->users->findByUsername($username);
        if (!$user) {
            http_response_code(404);
            echo 'Usuario no encontrado.';
            return;
        }

        $pageTitle = trim((string) ($user['display_name'] ?? '')) ?: (string) $user['username'];
        $isLoggedIn = $this->isAuthenticated();
        $shell = $isLoggedIn
            ? $this->buildMemberShell($pageTitle)
            : $this->buildGuestShell($pageTitle);

        $this->render('profile/show', $shell + [
            'user' => $user,
            'fotos' => $this->photos->findByUserId((int) $user['id']),
            'isLoggedIn' => $isLoggedIn,
            'isOwner' => $isLoggedIn && $this->currentUsername() === $user['username'],
        ]);
    }
}
