<?php
/** @var array $user */
$errors = $_SESSION['errors'] ?? [];
unset($_SESSION['errors']);
?>
<!DOCTYPE html>
<html lang="es">
<head><meta charset="UTF-8"><title>Configuración</title></head>
<body>
    <h1>Configuración</h1>
    <a href="/u/<?= $_SESSION['username'] ?>">← Volver al perfil</a>

    <?php foreach ($errors as $e): ?>
        <p style="color:red"><?= htmlspecialchars($e) ?></p>
    <?php endforeach ?>

    <!-- Editar perfil -->
    <h2>Editar perfil</h2>
    <form method="POST" action="/settings">
        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
        <input type="hidden" name="action" value="profile">

        <label>Usuario<br>
            <input type="text" name="username" value="<?= htmlspecialchars($user['username'] ?? '') ?>" required>
        </label><br><br>

        <label>Nombre público<br>
            <input type="text" name="display_name" value="<?= htmlspecialchars($user['display_name'] ?? '') ?>" maxlength="100" required>
        </label><br><br>

        <label>Bio<br>
            <textarea name="bio" maxlength="160"><?= htmlspecialchars($user['bio'] ?? '') ?></textarea>
        </label><br><br>

        <button type="submit">Guardar cambios</button>
    </form>

    <hr>

    <!-- Foto de perfil -->
    <h2>Foto de perfil</h2>
    <?php if ($user['profile_image']): ?>
        <img src="/uploads/<?= htmlspecialchars($user['profile_image']) ?>"
             style="width:80px;height:80px;object-fit:cover;border-radius:50%;">
        <form method="POST" action="/settings">
            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
            <input type="hidden" name="action" value="delete_profile_image">
            <button type="submit">Eliminar foto de perfil</button>
        </form>
    <?php else: ?>
        <div style="width:80px;height:80px;border-radius:50%;background:#ccc;
                    display:flex;align-items:center;justify-content:center;
                    font-size:2rem;font-weight:bold;">
            <?= strtoupper($user['username'][0]) ?>
        </div>
    <?php endif ?>

    <form method="POST" action="/settings" enctype="multipart/form-data">
        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
        <input type="hidden" name="action" value="upload_profile_image">
        <label>Nueva foto de perfil (máx. 2MB, JPG/PNG/WEBP)<br>
            <input type="file" name="profile_image" accept="image/jpeg,image/png,image/webp" required>
        </label><br><br>
        <button type="submit">Subir foto de perfil</button>
    </form>

    <hr>

    <!-- Eliminar cuenta -->
    <h2>Zona de peligro</h2>
    <form method="POST" action="/settings"
          onsubmit="return confirm('¿Seguro? Esta acción no se puede deshacer.')">
        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
        <input type="hidden" name="action" value="delete_account">
        <button type="submit" style="color:red">Eliminar mi cuenta</button>
    </form>
</body>
</html>