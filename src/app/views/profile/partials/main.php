<?php
/**
 * @var array<string, mixed> $user
 * @var array<int, array<string, mixed>> $fotos
 */

$displayName = trim((string) ($user['display_name'] ?? '')) ?: (string) ($user['username'] ?? '');
$username    = (string) ($user['username'] ?? '');
$avatarUrl   = !empty($user['profile_image']) ? '/uploads/' . rawurlencode((string) $user['profile_image']) : null;
$avatarText  = strtoupper(substr($username, 0, 1) ?: '?');
?>
<main class="profile-main">
    <section class="profile-summary" aria-label="Resumen del perfil">
        <?php if ($avatarUrl): ?>
            <img
                class="profile-avatar"
                src="<?= htmlspecialchars($avatarUrl) ?>"
                alt="Foto de perfil de <?= htmlspecialchars($displayName) ?>"
            >
        <?php else: ?>
            <div class="profile-avatar profile-avatar-fallback" aria-hidden="true">
                <?= htmlspecialchars($avatarText) ?>
            </div>
        <?php endif ?>

        <div class="profile-identity">
            <h1 class="profile-name"><?= htmlspecialchars($displayName) ?></h1>
            <p class="profile-username">@<?= htmlspecialchars($username) ?></p>
        </div>
    </section>

    <?php if (!empty($user['bio'])): ?>
        <section class="profile-bio-section" aria-label="Biografia">
            <p class="profile-bio"><?= nl2br(htmlspecialchars((string) $user['bio'])) ?></p>
        </section>
    <?php endif ?>

    <section class="profile-gallery-section" aria-label="Galeria de fotos">
        <?php if (empty($fotos)): ?>
            <p class="profile-empty-state">No hay fotos todavia.</p>
        <?php else: ?>
            <div class="profile-photo-grid">
                <?php foreach ($fotos as $foto): ?>
                    <a href="/photo/<?= (int) $foto['id'] ?>" class="profile-photo-card">
                        <img
                            class="profile-photo"
                            src="/uploads/<?= rawurlencode((string) $foto['image_path']) ?>"
                            alt="Foto publicada por <?= htmlspecialchars($displayName) ?>"
                            loading="lazy"
                        >
                    </a>
                <?php endforeach ?>
            </div>
        <?php endif ?>
    </section>
</main>
