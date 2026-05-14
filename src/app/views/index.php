<?php
require_once dirname(__DIR__, 2) . '/config/database.php';

try {
    $db = getDB();
    $status = '✅ Conexión con la base de datos establecida';
    $color  = 'green';
} catch (Exception $e) {
    $status = '❌ Error de conexión: ' . $e->getMessage();
    $color  = 'red';
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Galería</title>
</head>
<body>
    <h1>Bienvenido a la Galería</h1>
    <p style="color: <?= $color ?>">
        <?= htmlspecialchars($status) ?>
    </p>
</body>
</html>