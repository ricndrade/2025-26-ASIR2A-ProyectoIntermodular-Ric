<!DOCTYPE html>
<html lang="es">
<?php if (!isset($user)) {
    $user = ['username' => '', 'id' => null, 'foto_perfil' => null];
} ?>

<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($user['username']) ?></title>
</head>

<body>
    <?php
    // Avatar: foto propia o inicial del username como fallback
    $avatar = $user['profile_image']
        ? '/uploads/' . htmlspecialchars($user['profile_image'])
        : null;
    ?>

    <div>
        <?php if ($avatar): ?>
            <img src="<?= $avatar ?>"
                style="width:80px;height:80px;object-fit:cover;border-radius:50%;">
        <?php else: ?>
            <div style="width:80px;height:80px;border-radius:50%;background:#ccc;
                    display:flex;align-items:center;justify-content:center;
                    font-size:2rem;font-weight:bold;">
                <?= strtoupper($user['username'][0]) ?>
            </div>
        <?php endif ?>

        <h1><?= htmlspecialchars($user['display_name']) ?></h1>
        <p style="color:gray">@<?= htmlspecialchars($user['username']) ?></p>

        <?php if (!empty($user['bio'])): ?>
            <p><?= nl2br(htmlspecialchars($user['bio'])) ?></p>
        <?php endif ?>

        <?php if (isLoggedIn() && $_SESSION['username'] === $user['username']): ?>
            <a href="/upload">Subir foto</a> |
            <a href="/settings">Editar perfil</a> |
            <a href="/logout">Cerrar sesión</a>
        <?php endif ?>
    </div>

    <hr>

    <?php if (empty($fotos)): ?>
        <p>No hay fotos todavía.</p>
    <?php else: ?>
        <div>
            <?php foreach ($fotos as $foto): ?>
                <div>
                    <img src="/uploads/<?= htmlspecialchars($foto['image_path']) ?>" width="200">
                    <p><?= htmlspecialchars($foto['caption'] ?? '') ?></p>
                    <?php if (isLoggedIn() && $_SESSION['user_id'] === $user['id']): ?>
                        <form method="POST" action="/settings">
                            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                            <input type="hidden" name="action" value="delete_photo">
                            <input type="hidden" name="photo_id" value="<?= $foto['id'] ?>">
                            <button type="submit">Eliminar</button>
                        </form>
                    <?php endif ?>
                </div>
            <?php endforeach ?>
        </div>
    <?php endif ?>
</body>

</html>