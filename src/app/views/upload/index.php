<?php
requireLogin();
$errors = $_SESSION['errors'] ?? [];
unset($_SESSION['errors']);

$db   = getDB();
$stmt = $db->prepare(
    "SELECT * FROM photos WHERE user_id = ? ORDER BY created_at DESC"
);
$stmt->execute([$_SESSION['user_id']]);
$fotos = $stmt->fetchAll();

$pageTitle = 'Subir foto';
$galleryHref = '/u/' . $_SESSION['username'];
$headerActions = [
    ['href' => '/upload', 'label' => 'Subir foto', 'icon' => 'upload', 'visible' => true],
    ['href' => '/settings', 'label' => 'Editar perfil', 'icon' => 'settings', 'visible' => true],
    ['href' => '/search', 'label' => 'Buscar', 'icon' => 'search', 'visible' => true],
    ['href' => '/logout', 'label' => 'Cerrar sesion', 'icon' => 'logout', 'visible' => true],
];

require dirname(__DIR__) . '/partials/header.php';
?>
<main class="page-main">
    <section class="surface-card">
        <div class="surface-head">
            <h1 class="surface-title">Subir foto</h1>
            <p class="surface-copy">Publica una imagen y ajusta su pie de foto despues.</p>
        </div>

        <?php if (!empty($errors)): ?>
            <div class="alert-stack">
                <?php foreach ($errors as $error): ?>
                    <p class="alert-message alert-message-error"><?= htmlspecialchars($error) ?></p>
                <?php endforeach ?>
            </div>
        <?php endif ?>

        <form class="form-stack" method="POST" action="/upload" enctype="multipart/form-data">
            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">

            <label class="field-group">
                <span class="field-label">Imagen</span>
                <input class="text-input text-input-file" type="file" name="photo" accept="image/*" required>
            </label>

            <label class="field-group">
                <span class="field-label">Pie de foto</span>
                <input class="text-input" type="text" name="caption" maxlength="220" placeholder="Escribe un pie corto">
            </label>

            <button class="button button-primary" type="submit">Subir</button>
        </form>
    </section>

    <section class="surface-card">
        <div class="surface-head">
            <h2 class="surface-title surface-title-small">Mis fotos</h2>
        </div>

        <?php if (empty($fotos)): ?>
            <p class="empty-state">No has subido fotos todavia.</p>
        <?php else: ?>
            <div class="upload-list">
                <?php foreach ($fotos as $foto): ?>
                    <article class="upload-item">
                        <img
                            class="upload-thumb"
                            src="/uploads/<?= rawurlencode((string) $foto['image_path']) ?>"
                            alt="Foto subida el <?= htmlspecialchars(date('d/m/Y', strtotime((string) $foto['created_at']))) ?>"
                        >

                        <div class="upload-copy">
                            <form class="form-inline" method="POST" action="/foto/editar">
                                <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                                <input type="hidden" name="photo_id" value="<?= (int) $foto['id'] ?>">
                                <input
                                    class="text-input"
                                    type="text"
                                    name="caption"
                                    value="<?= htmlspecialchars((string) ($foto['caption'] ?? '')) ?>"
                                    maxlength="220"
                                    placeholder="Pie de foto"
                                >
                                <button class="button button-secondary" type="submit">Guardar</button>
                            </form>

                            <p class="meta-note"><?= htmlspecialchars(date('d/m/Y', strtotime((string) $foto['created_at']))) ?></p>
                        </div>

                        <form method="POST" action="/foto/borrar" onsubmit="return confirm('Eliminar esta foto?')">
                            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                            <input type="hidden" name="photo_id" value="<?= (int) $foto['id'] ?>">
                            <button class="button button-danger" type="submit">Eliminar</button>
                        </form>
                    </article>
                <?php endforeach ?>
            </div>
        <?php endif ?>
    </section>
</main>
<?php require dirname(__DIR__) . '/partials/footer.php'; ?>
