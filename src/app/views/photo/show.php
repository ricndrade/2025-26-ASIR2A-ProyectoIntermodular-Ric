<?php
require dirname(__DIR__) . '/partials/header.php';
?>

<main class="photo-main">
    <?php
    /**
     * @var array<string, mixed> $photo
     * @var array<string, mixed> $user
     */
    
    $displayName = trim((string) ($user['display_name'] ?? '')) ?: (string) ($user['username'] ?? '');
    $username    = (string) ($user['username'] ?? '');
    $avatarUrl   = !empty($user['profile_image']) ? '/uploads/' . rawurlencode((string) $user['profile_image']) : null;
    $avatarText  = strtoupper(substr($username, 0, 1) ?: '?');
    
    $photoPath   = '/uploads/' . rawurlencode((string) $photo['image_path']);
    $caption     = (string) ($photo['caption'] ?? '');
    $createdAt   = new DateTime((string) ($photo['created_at'] ?? 'now'));
    
    $months = ['enero', 'febrero', 'marzo', 'abril', 'mayo', 'junio', 'julio', 'agosto', 'septiembre', 'octubre', 'noviembre', 'diciembre'];
    $day = $createdAt->format('j');
    $month = $months[(int) $createdAt->format('n') - 1];
    $year = $createdAt->format('Y');
    $formattedDate = "$day de $month de $year";
    ?>
    
    <button class="photo-back-button" onclick="history.back()" aria-label="Volver">
        <svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
            <polyline points="15 18 9 12 15 6"></polyline>
        </svg>
    </button>

    <div class="photo-container">
        <div class="photo-header">
            <?php if ($avatarUrl): ?>
                <img
                    class="photo-avatar"
                    src="<?= htmlspecialchars($avatarUrl) ?>"
                    alt="Foto de perfil de <?= htmlspecialchars($displayName) ?>"
                >
            <?php else: ?>
                <div class="photo-avatar photo-avatar-fallback" aria-hidden="true">
                    <?= htmlspecialchars($avatarText) ?>
                </div>
            <?php endif ?>
            
            <div class="photo-user-info">
                <p class="photo-user-name"><?= htmlspecialchars($displayName) ?></p>
                <p class="photo-user-username">@<?= htmlspecialchars($username) ?></p>
            </div>
        </div>

        <figure class="photo-figure">
            <img
                class="photo-image"
                src="<?= htmlspecialchars($photoPath) ?>"
                alt="<?= htmlspecialchars($caption ?: 'Foto de ' . $displayName) ?>"
            >
        </figure>

        <?php if (!empty($caption)): ?>
            <div class="photo-caption-section">
                <p class="photo-caption"><?= htmlspecialchars($caption) ?></p>
            </div>
        <?php endif ?>

        <div class="photo-metadata">
            <time class="photo-date" datetime="<?= htmlspecialchars((string) $photo['created_at']) ?>">
                Fecha de subida: <?= htmlspecialchars($formattedDate) ?>
            </time>
        </div>
    </div>
</main>

<?php
require dirname(__DIR__) . '/partials/footer.php';
?>
