<?php
try {
    $db = new PDO("mysql:host=localhost;dbname=technolab-dashboard", "root", "");
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Fout!: " . $e->getMessage());
}

if (!isset($_GET['id'])) {
    die("Geen werknemer geselecteerd.");
}

$id = intval($_GET['id']);

$stmt = $db->prepare("SELECT * FROM werknemers WHERE id = :id");
$stmt->execute([':id' => $id]);
$werknemer = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$werknemer) {
    die("Werknemer niet gevonden.");
}
?>
<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <title>Details van <?= ($werknemer['voornaam']) ?></title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .card {
            border: 1px solid #ddd;
            padding: 20px;
            border-radius: 8px;
            width: 400px;
            background: #f9f9f9;
        }
        .card h2 { margin-top: 0; }
        .card p { margin: 8px 0; }
        a { text-decoration: none; color: blue; }
    </style>
</head>
<body>
<div class="card">
    <h2><?= ($werknemer['voornaam'] . ' ' . ($werknemer['tussenvoegsel'] ? $werknemer['tussenvoegsel'].' ' : '') . $werknemer['achternaam']) ?></h2>
    <p><strong>Status:</strong> <?= ($werknemer['status']) ?></p>
    <p><strong>Sector:</strong> <?= ($werknemer['sector']) ?></p>
    <p><strong>BHV:</strong> <?= $werknemer['BHV'] ? 'Ja' : 'Nee' ?></p>
    <p><strong>Werkdagen:</strong>
        <?= $werknemer['werkdag_ma'] ? 'Maandag ' : '' ?>
        <?= $werknemer['werkdag_di'] ? 'Dinsdag' : '' ?>
    </p>
    <br>
    <a href="dagplanning.php"> Terug naar lijst</a>
</div>
</body>
</html>
