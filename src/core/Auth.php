<?php
function requireLogin(): void {
    if (empty($_SESSION['user_id'])) {
        header('Location: /login');
        exit;
    }
}

function isLoggedIn(): bool {
    return !empty($_SESSION['user_id']);
}