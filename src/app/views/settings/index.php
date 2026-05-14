<?php
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
            <input type="text" name="display_name" value="<?= htmlspecialchars($user['display_name'] ?? '') ?>" required>
        </label><br><br>

        <label>Bio<br>
            <textarea name="bio" maxlength="160"><?= htmlspecialchars($user['bio'] ?? '') ?></textarea>
        </label><br><br>

        <button type="submit">Guardar cambios</button>
    </form>

    <hr>

    <!-- Subir foto -->
    <h2>Subir foto</h2>
    <form method="POST" action="/settings" enctype="multipart/form-data">
        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
        <input type="hidden" name="action" value="upload_photo">

        <label>Imagen<br>
            <input type="file" name="photo" accept="image/*" required>
        </label><br><br>

        <label>Pie de foto<br>
            <input type="text" name="caption" maxlength="220">
        </label><br><br>

        <button type="submit">Subir</button>
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