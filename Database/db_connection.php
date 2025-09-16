<?php

try {
    $db = new PDO("mysql:host=localhost;dbname=technolab",
        "root", "");
} catch (PDOException $e) {
    die("Error!: " . $e->getMessage());
}
?>



