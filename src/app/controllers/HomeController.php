<?php
class HomeController {
    public function index(): void {
        if (isLoggedIn()) {
            header('Location: /u/' . $_SESSION['username']);
        } else {
            header('Location: /login');
        }
        exit;
    }
}