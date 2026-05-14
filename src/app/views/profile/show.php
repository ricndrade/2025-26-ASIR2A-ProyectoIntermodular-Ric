<?php
if (!isset($user)) {
    $user = ['username' => '', 'display_name' => '', 'profile_image' => null, 'bio' => ''];
}

$pageTitle = $pageTitle ?? ($user['display_name'] ?: $user['username']);
$galleryHref = $galleryHref ?? '/login';
$headerActions = $headerActions ?? [];
$isLoggedIn = $isLoggedIn ?? false;
$isOwner = $isOwner ?? false;
$fotos = $fotos ?? [];

require dirname(__DIR__) . '/partials/header.php';
require __DIR__ . '/partials/main.php';
require dirname(__DIR__) . '/partials/footer.php';
