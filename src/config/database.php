<?php

$host = 'db';
$dbname = 'museo';
$user = 'museo_user';
$pass = 'museo_pass';

try {
    $pdo = new PDO(
        "mysql:host=$host;dbname=$dbname;charset=utf8",
        $user,
        $pass
    );

    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "<br>";
    echo "Conexión correcta";

} catch (PDOException $e) {
    die("Error conexión: " . $e->getMessage());
}