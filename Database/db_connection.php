<?php

// Centrale databaseconfiguratie voor alle pagina's
$DB_HOST = 'localhost';
$DB_NAME = 'technolab-dashboard';
$DB_USER = 'root';
$DB_PASS = '';
$DB_CHARSET = 'utf8mb4';

try {
    $db = new PDO(
        "mysql:host={$DB_HOST};dbname={$DB_NAME};charset={$DB_CHARSET}",
        $DB_USER,
        $DB_PASS,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]
    );
} catch (PDOException $e) {
    die("Databasefout: " . $e->getMessage());
}
?>



