<?php
/** @var array $user */
$errors = $_SESSION['errors'] ?? [];
unset($_SESSION['errors']);

$pageTitle = 'Configuracion';
$galleryHref = '/u/' . $_SESSION['username'];
$headerActions = [
    ['href' => '/upload', 'label' => 'Subir foto', 'icon' => 'upload', 'visible' => true],
    ['href' => '/settings', 'label' => 'Editar perfil', 'icon' => 'settings', 'visible' => true],
    ['href' => '/search', 'label' => 'Buscar', 'icon' => 'search', 'visible' => true],
    ['href' => '/logout', 'label' => 'Cerrar sesion', 'icon' => 'logout', 'visible' => true],
];

$avatarUrl = !empty($user['profile_image']) ? '/uploads/' . rawurlencode((string) $user['profile_image']) : null;
$avatarText = htmlspecialchars(strtoupper(substr((string) ($user['username'] ?? ''), 0, 1) ?: '?'));

require dirname(__DIR__) . '/partials/header.php';
?>
<main class="page-main">
    <?php if (!empty($errors)): ?>
        <div class="alert-stack">
            <?php foreach ($errors as $error): ?>
                <p class="alert-message alert-message-error"><?= htmlspecialchars($error) ?></p>
            <?php endforeach ?>
        </div>
    <?php endif ?>

    <section class="surface-card">
        <div class="surface-head">
            <h1 class="surface-title">Configuracion</h1>
            <p class="surface-copy">Edita usuario, nombre publico y bio.</p>
        </div>

        <form class="form-stack" method="POST" action="/settings">
            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
            <input type="hidden" name="action" value="profile">

            <label class="field-group">
                <span class="field-label">Usuario</span>
                <input class="text-input" type="text" name="username" value="<?= htmlspecialchars((string) ($user['username'] ?? '')) ?>" required>
            </label>

            <label class="field-group">
                <span class="field-label">Nombre publico</span>
                <input class="text-input" type="text" name="display_name" value="<?= htmlspecialchars((string) ($user['display_name'] ?? '')) ?>" maxlength="100" required>
            </label>

            <label class="field-group">
                <span class="field-label">Bio</span>
                <textarea class="text-area" name="bio" maxlength="160"><?= htmlspecialchars((string) ($user['bio'] ?? '')) ?></textarea>
            </label>

            <button class="button button-primary" type="submit">Guardar cambios</button>
        </form>
    </section>

    <section class="surface-card">
        <div class="surface-head">
            <h2 class="surface-title surface-title-small">Foto de perfil</h2>
        </div>

        <div class="settings-avatar-row">
            <?php if ($avatarUrl): ?>
                <img class="profile-avatar" src="<?= htmlspecialchars($avatarUrl) ?>" alt="Foto de perfil actual">
            <?php else: ?>
                <div class="profile-avatar profile-avatar-fallback" aria-hidden="true"><?= $avatarText ?></div>
            <?php endif ?>

            <?php if ($avatarUrl): ?>
                <form method="POST" action="/settings">
                    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                    <input type="hidden" name="action" value="delete_profile_image">
                    <button class="button button-secondary" type="submit">Eliminar foto de perfil</button>
                </form>
            <?php endif ?>
        </div>

        <form class="form-stack" method="POST" action="/settings" enctype="multipart/form-data">
            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
            <input type="hidden" name="action" value="upload_profile_image">

            <label class="field-group">
                <span class="field-label">Nueva foto de perfil</span>
                <input class="text-input text-input-file" type="file" name="profile_image" accept="image/jpeg,image/png,image/webp" required>
            </label>

            <button class="button button-primary" type="submit">Subir foto de perfil</button>
        </form>
    </section>

    <section class="surface-card surface-card-danger">
        <div class="surface-head">
            <h2 class="surface-title surface-title-small">Zona de peligro</h2>
            <p class="surface-copy">Esta accion borra cuenta y fotos.</p>
        </div>

        <form method="POST" action="/settings" onsubmit="return confirm('Seguro? Esta accion no se puede deshacer.')">
            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
            <input type="hidden" name="action" value="delete_account">
            <button class="button button-danger" type="submit">Eliminar mi cuenta</button>
        </form>
    </section>
</main>
<?php require dirname(__DIR__) . '/partials/footer.php'; ?>
