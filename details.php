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

$id = (int) $_GET['id'];

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

$dagen = [
    'ma' => 'Maandag',
    'di' => 'Dinsdag',
    'wo' => 'Woensdag',
    'do' => 'Donderdag',
    'vr' => 'Vrijdag'
];
?>
<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <title>Details van <?= ($volledigeNaam) ?></title>
    <link rel="stylesheet" href="css/details.css">
</head>
<body>
    <div class="card">
        <h2><?= ($volledigeNaam) ?></h2>

        <p><strong>Email:</strong> <?= ($werknemer['email']) ?></p>

        <p><strong>Status:</strong>
            <span class="status
                <?= $werknemer['status'] === 'Aanwezig' ? 'status-aanwezig' : '' ?>
                <?= $werknemer['status'] === 'Afwezig' ? 'status-afwezig' : '' ?>
                <?= $werknemer['status'] === 'Ziek' ? 'status-ziek' : '' ?>
                <?= $werknemer['status'] === 'Op de school' ? 'status-opdeschool' : '' ?>
                <?= $werknemer['status'] === 'Eefetjes Afwezig' ? 'status-eefetjes' : '' ?>
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

        <p><strong>Werkdagen:</strong></p>
        <div class="werkdagen">
            <?php foreach ($dagen as $afkorting => $naam): ?>
                <?php $isWerkdag = $werknemer['werkdag_' . $afkorting]; ?>
                <span class="werkdag <?= $isWerkdag ? 'active' : 'inactive' ?>">
                    <?= ($naam) ?>
                </span>
            <?php endforeach; ?>
        </div>

        <a href="dagplanning.php" class="back-link">← Terug naar lijst</a>
    </div>
</body>
</html>
