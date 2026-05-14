<!DOCTYPE html>
<html lang="es">
<?php if (!isset($user)) { $user = ['username' => '', 'id' => null, 'foto_perfil' => null]; } ?>
<head><meta charset="UTF-8"><title><?= htmlspecialchars($user['username']) ?></title></head>
<body>
    <?php if ($user['profile_image']): ?>
        <img src="/uploads/<?= htmlspecialchars($user['profile_image']) ?>" width="100">
    <?php endif ?>

    <h1><?= htmlspecialchars($user['display_name']) ?></h1>
    <p style="color:gray">@<?= htmlspecialchars($user['username']) ?></p>

    <?php if (!empty($user['bio'])): ?>
        <p><?= htmlspecialchars($user['bio']) ?></p>
    <?php endif ?>

    <?php if (isLoggedIn() && $_SESSION['username'] === $user['username']): ?>
        <a href="/settings">Editar perfil</a> |
        <a href="/logout">Cerrar sesión</a>
    <?php endif ?>

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