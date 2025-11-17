<?php
session_start();
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

// Bepaal huidige dag
$dagenMap = [
    1 => 'ma',
    2 => 'di',
    3 => 'wo',
    4 => 'do',
    5 => 'vr'
];

$dagNummer = (int) date('N'); // 1 (maandag) - 7 (zondag)
$huidigeDag = $dagenMap[$dagNummer] ?? null;

//  Alleen updaten als het een werkdag is (ma-vr)
if ($huidigeDag) {
    $col = 'werkdag_' . $huidigeDag;

    // Status bepalen op basis van werkdag, behalve bij Ziek of Eefetjes Afwezig
    if (!in_array($werknemer['status'], ['Ziek', 'Eefetjes Afwezig'])) {
        $status = $werknemer[$col] ? 'Aanwezig' : 'Afwezig';
        $werknemer['status'] = $status;
    }
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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        .werkdag.today {
            font-weight: bold;
            text-decoration: underline;
            color: #0b5ed7;
        }
    </style>
</head>
<body>
<div class="card">
    <h2><?= ($volledigeNaam) ?></h2>

    <p><i class="bi bi-envelope"></i> <strong>Email:</strong> <?= ($werknemer['email']) ?></p>

    <p>
        <i class="bi bi-activity"></i> <strong>Status:</strong>
        <span class="status
                <?= $werknemer['status'] === 'Aanwezig' ? 'status-aanwezig' : '' ?>
                <?= $werknemer['status'] === 'Afwezig' ? 'status-afwezig' : '' ?>
                <?= $werknemer['status'] === 'Ziek' ? 'status-ziek' : '' ?>
                <?= $werknemer['status'] === 'Eefetjes Afwezig' ? 'status-eefetjes' : '' ?>
            "><?= ($werknemer['status']) ?></span>
    </p>

    <p><i class="bi bi-diagram-3"></i> <strong>Sector:</strong> <?= ($werknemer['sector']) ?></p>

    <p>
        <i class="bi bi-shield-check"></i> <strong>BHV:</strong>
        <span class="bhv <?= $werknemer['BHV'] ? 'bhv-ja' : 'bhv-nee' ?>">
                <?= $werknemer['BHV'] ? 'Ja' : 'Nee' ?>
            </span>
    </p>

    <p><i class="bi bi-calendar-week"></i> <strong>Werkdagen:</strong></p>
    <div class="werkdagen">
        <?php foreach ($dagen as $afkorting => $naam):
            $isWerkdag = $werknemer['werkdag_' . $afkorting];
            $todayClass = ($huidigeDag === $afkorting) ? 'today' : '';
            ?>
            <span class="werkdag <?= $isWerkdag ? 'active' : 'inactive' ?> <?= $todayClass ?>">
                    <?= ($naam) ?>
                </span>
        <?php endforeach; ?>
    </div>

    <div class="button-group">
        <a href="dagplanning.php" class="btn btn-back"><i class="bi bi-arrow-left"></i> Terug</a>
        <a href="update.php?id=<?= $werknemer['id'] ?>" class="btn btn-edit"><i class="bi bi-pencil-square"></i> Bewerken</a>
    </div>
</div>
</body>
</html>
