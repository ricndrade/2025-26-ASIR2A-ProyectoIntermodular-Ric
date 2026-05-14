<?php
$errors = $_SESSION['errors'] ?? [];
unset($_SESSION['errors']);
?>
<!DOCTYPE html>
<html lang="es">
<head><meta charset="UTF-8"><title>Subir foto</title></head>
<body>
    <h1>Subir foto</h1>
    <a href="/u/<?= $_SESSION['username'] ?>">← Volver al perfil</a>

    <?php foreach ($errors as $e): ?>
        <p style="color:red"><?= htmlspecialchars($e) ?></p>
    <?php endforeach ?>

    <form method="POST" action="/upload" enctype="multipart/form-data">
        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">

        <label>Imagen<br>
            <input type="file" name="photo" accept="image/*" required>
        </label><br><br>

        <label>Pie de foto<br>
            <input type="text" name="caption" maxlength="220">
        </label><br><br>

        <button type="submit">Subir</button>
    </form>
</body>
</html>