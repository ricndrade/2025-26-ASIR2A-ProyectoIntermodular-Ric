<?php
$errors = $_SESSION['errors'] ?? [];
$old    = $_SESSION['old']    ?? [];
unset($_SESSION['errors'], $_SESSION['old']);
?>
<!DOCTYPE html>
<html lang="es">
<head><meta charset="UTF-8"><title>Registro</title></head>
<body>
    <h1>Crear cuenta</h1>

    <?php foreach ($errors as $e): ?>
        <p style="color:red"><?= htmlspecialchars($e) ?></p>
    <?php endforeach ?>

    <form method="POST" action="/register">
        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">

        <label>Usuario<br>
            <input type="text" name="username" value="<?= htmlspecialchars($old['username'] ?? '') ?>" required>
        </label><br><br>

        <label>Email<br>
            <input type="email" name="email" value="<?= htmlspecialchars($old['email'] ?? '') ?>" required>
        </label><br><br>

        <label>Contraseña (mín. 8 caracteres)<br>
            <input type="password" name="password" required>
        </label><br><br>

        <button type="submit">Registrarse</button>
    </form>

    <p>¿Ya tienes cuenta? <a href="/login">Inicia sesión</a></p>
</body>
</html>