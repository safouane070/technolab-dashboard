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

$volledigeNaam = $werknemer['voornaam'];
if (!empty($werknemer['tussenvoegsel'])) {
    $volledigeNaam .= ' ' . $werknemer['tussenvoegsel'];
}
$volledigeNaam .= ' ' . $werknemer['achternaam'];

// Werkdagen lijst
$werkdagen = [];
if ($werknemer['werkdag_ma']) $werkdagen[] = "Maandag";
if ($werknemer['werkdag_di']) $werkdagen[] = "Dinsdag";
if ($werknemer['werkdag_wo']) $werkdagen[] = "Woensdag";
if ($werknemer['werkdag_do']) $werkdagen[] = "Donderdag";
if ($werknemer['werkdag_vr']) $werkdagen[] = "Vrijdag";
$werkdagenText = !empty($werkdagen) ? implode(", ", $werkdagen) : "Geen";
?>
<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <title>Details van <?= ($volledigeNaam) ?></title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 30px;
            background: #f0f2f5;
        }
        .card {
            border: none;
            padding: 20px 30px;
            border-radius: 10px;
            width: 480px;
            background: #fff;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        }
        .card h2 {
            margin-top: 0;
            color: #333;
        }
        .card p {
            margin: 10px 0;
            font-size: 15px;
        }
        .status {
            font-weight: bold;
            padding: 4px 8px;
            border-radius: 6px;
            display: inline-block;
        }
        .status-aanwezig { background: #c8f7c5; color: #256029; }
        .status-afwezig  { background: #f8c8c8; color: #7d2020; }
        .status-ziek     { background: #fff3cd; color: #856404; }
        .status-opdeschool { background: #d6eaff; color: #004085; }
        .status-eefetjes { background: #ffe0b3; color: #804000; }
        .bhv { font-weight: bold; }
        .bhv-ja { color: green; }
        .bhv-nee { color: red; }
        a {
            display: inline-block;
            margin-top: 15px;
            text-decoration: none;
            color: #007bff;
        }
    </style>
</head>
<body>
<div class="card">
    <h2><?= ($volledigeNaam) ?></h2>
    <p><strong>Email:</strong> <?= ($werknemer['email']) ?></p>
    <p><strong>Status:</strong>
        <span class="status
            <?= $werknemer['status']=='Aanwezig' ? 'status-aanwezig' : '' ?>
            <?= $werknemer['status']=='Afwezig' ? 'status-afwezig' : '' ?>
            <?= $werknemer['status']=='Ziek' ? 'status-ziek' : '' ?>
            <?= $werknemer['status']=='Op de school' ? 'status-opdeschool' : '' ?>
            <?= $werknemer['status']=='Eefetjes Afwezig' ? 'status-eefetjes' : '' ?>
        ">
            <?= ($werknemer['status']) ?>
        </span>
    </p>
    <p><strong>Sector:</strong> <?= ($werknemer['sector']) ?></p>
    <p><strong>BHV:</strong>
        <span class="bhv <?= $werknemer['BHV'] ? 'bhv-ja' : 'bhv-nee' ?>">
            <?= $werknemer['BHV'] ? 'Ja' : 'Nee' ?>
        </span>
    </p>
    <p><strong>Werkdagen:</strong> <?= ($werkdagenText) ?></p>
    <br>
    <a href="dagplanning.php">← Terug naar lijst</a>
</div>
</body>
</html>
