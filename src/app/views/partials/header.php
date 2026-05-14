<?php
/**
 * @var string $pageTitle
 * @var string $galleryHref
 * @var array<int, array{href:string,label:string,icon:string,visible:bool}> $headerActions
 */

$renderAppIcon = static function (string $icon): string {
    return match ($icon) {
        'upload' => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 16V8M8.5 11.5 12 8l3.5 3.5M5 4.75h14A1.25 1.25 0 0 1 20.25 6v12A1.25 1.25 0 0 1 19 19.25H5A1.25 1.25 0 0 1 3.75 18V6A1.25 1.25 0 0 1 5 4.75Z"/></svg>',
        'settings' => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 12a3.75 3.75 0 1 0 0-7.5 3.75 3.75 0 0 0 0 7.5ZM5.75 19.25a6.25 6.25 0 0 1 12.5 0"/></svg>',
        'search' => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M10.5 17.25a6.75 6.75 0 1 1 0-13.5 6.75 6.75 0 0 1 0 13.5ZM15.25 15.25 20.25 20.25"/></svg>',
        'logout' => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M10.25 5.75H6A1.25 1.25 0 0 0 4.75 7v10A1.25 1.25 0 0 0 6 18.25h4.25M13 8.25l4 3.75-4 3.75M8.75 12h8.25"/></svg>',
        'profile' => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 12a3.75 3.75 0 1 0 0-7.5 3.75 3.75 0 0 0 0 7.5ZM5.75 19.25a6.25 6.25 0 0 1 12.5 0"/></svg>',
        'login' => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M10.25 5.75H6A1.25 1.25 0 0 0 4.75 7v10A1.25 1.25 0 0 0 6 18.25h4.25M13 8.25l4 3.75-4 3.75M8.75 12h8.25"/></svg>',
        default => '',
    };
};
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle) ?></title>
    <link rel="stylesheet" href="/assets/profile.css">
</head>
<body>
    <div class="profile-shell">
        <header class="profile-header">
            <a class="profile-brand" href="<?= htmlspecialchars($galleryHref) ?>">Galeria</a>

            <?php if (!empty($headerActions)): ?>
                <nav class="profile-actions" aria-label="Acciones principales">
                    <?php foreach ($headerActions as $action): ?>
                        <?php if (!$action['visible']) continue; ?>
                        <a
                            class="profile-action-button"
                            href="<?= htmlspecialchars($action['href']) ?>"
                            aria-label="<?= htmlspecialchars($action['label']) ?>"
                            title="<?= htmlspecialchars($action['label']) ?>"
                        >
                            <?= $renderAppIcon($action['icon']) ?>
                            <span class="sr-only"><?= htmlspecialchars($action['label']) ?></span>
                        </a>
                    <?php endforeach ?>
                </nav>
            <?php endif ?>
        </header>
