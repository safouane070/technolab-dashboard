<?php
try {
    $db = new PDO("mysql:host=localhost;dbname=technolab-dashboard", "root", "");
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Fout!: " . $e->getMessage());
}

// Week offset (0 = deze week, +1 volgende week, -1 vorige week)
$weekOffset = isset($_GET['week']) ? intval($_GET['week']) : 0;

// Start van de week berekenen
$startOfWeek = new DateTime();
$startOfWeek->modify("monday this week");
if ($weekOffset !== 0) {
    $startOfWeek->modify(($weekOffset > 0 ? '+' : '') . $weekOffset . ' week');
}
$jaar = $startOfWeek->format("o");
$weeknummer = $startOfWeek->format("W");

//  Weekdagen definities
$weekDays = [
    'Ma' => ['col' => 'ma', 'date' => (clone $startOfWeek)],
    'Di' => ['col' => 'di', 'date' => (clone $startOfWeek)->modify('+1 day')],
    'Wo' => ['col' => 'wo', 'date' => (clone $startOfWeek)->modify('+2 day')],
    'Do' => ['col' => 'do', 'date' => (clone $startOfWeek)->modify('+3 day')],
    'Vr' => ['col' => 'vr', 'date' => (clone $startOfWeek)->modify('+4 day')],
];

// ✅ Opslaan van status (zonder week te verliezen!)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'], $_POST['dag'], $_POST['status'])) {
    $id = intval($_POST['id']);
    $dag = $_POST['dag']; // ma, di, wo, do, vr
    $status = $_POST['status'];

    // Sla status op in juiste week + jaar
    $stmt = $db->prepare("
        INSERT INTO week_planning (werknemer_id, weeknummer, jaar, dag, status)
        VALUES (:id, :week, :jaar, :dag, :status)
        ON DUPLICATE KEY UPDATE status = VALUES(status)
    ");
    $stmt->execute([
        ':id' => $id,
        ':week' => $weeknummer,
        ':jaar' => $jaar,
        ':dag' => $dag,
        ':status' => $status
    ]);

    // Als de dag van vandaag wordt aangepast → sync naar werknemers-tabel
    $today = date('N');
    $daysMap = [1=>'ma',2=>'di',3=>'wo',4=>'do',5=>'vr'];
    if (isset($daysMap[$today]) && $daysMap[$today] === $dag && $weeknummer == date('W') && $jaar == date('o')) {
        $stmt = $db->prepare("UPDATE werknemers SET status = :status WHERE id = :id");
        $stmt->execute([':status'=>$status, ':id'=>$id]);
    }

    // Blijf op dezelfde week na opslaan
    header("Location: " . $_SERVER['PHP_SELF'] . "?week=$weekOffset");
    exit;
}

// Haal alleen werknemers op met een ingevulde voor- en achternaam
try {
    $stmtWerknemers = $db->query("
        SELECT *
        FROM werknemers
        WHERE TRIM(COALESCE(voornaam, '')) <> ''
          AND TRIM(COALESCE(achternaam, '')) <> ''
        ORDER BY  voornaam ASC
    ");
    $werknemers = $stmtWerknemers->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die('Fout bij ophalen werknemers: ' . $e->getMessage());
}

?>
<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Absence Tracker</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet" />
    <link rel="stylesheet" href="css/style.css" />
    <link rel="stylesheet" href="css/week.css" />
    <link rel="stylesheet" href="css/nav.css" />
</head>
<body>
<div class="app">

    <!-- Header -->
<header class="header">

    <!-- Logo -->
    <section class="logo-container">
        <a href="#" class="logo-link">
            <img src="image/technolab.png" alt="Technolab Logo" class="logo-icone">
        </a>
    </section>

    <!-- Navigatie -->
    <nav class="nav" aria-label="Main Navigation">
    </nav>

</header>

    <!-- Main -->
    <main class="main">
        <div class="main-header">
            <h1></h1>
            <div class="legend">
                <div><span class="dot status-aanwezig"></span> Aanwezig</div>
                <div><span class="dot status-afwezig"></span> Afwezig</div>
                <div><span class="dot status-ziek"></span> Ziek</div>
                <div><span class="dot status-eefetjesafwezig"></span> Tijdelijk Afwezig</div>
            </div>
        </div>

        <!-- Filters -->
        <div class="filters">
            <div class="filter-left" style="display: flex; align-items: center; gap: 10px;">
                <!-- Zoekveld -->
                <div class="search-input">
                    <input type="text" id="searchInput" placeholder="Zoek op naam..." />
                </div>

                <!-- Today / Week knoppen -->
                <div class="range-buttons" style="display: flex; gap: 5px;">
                    <a href="dagplanning.php"><button class="toggle">Vandaag</button></a>
                    <button class="active">Week</button>
                </div>
            </div>
            <div> <h2>Weekplanning Week <?= $weeknummer ?></h2></div>
            <div class="filter-right">
                <a href="?week=<?= $weekOffset-1 ?>"><button class="icon-button"><span class="material-symbols-outlined">chevron_left</span></button></a>
                <?php
                $isCurrentWeek = ($weeknummer == date('W') && $jaar == date('o')); ?>
                <span class="date-label" style="<?= $isCurrentWeek ? 'color:#007bff; font-weight:600;' : '' ?>">
                    <?= $startOfWeek->format('d M Y') ?> - <?= (clone $startOfWeek)->modify('+4 days')->format('d M Y') ?>
                </span>
                <a href="?week=<?= $weekOffset+1 ?>"><button class="icon-button"><span class="material-symbols-outlined">chevron_right</span></button></a>
            </div>

        </div>

        <!-- Table -->
        <div class="table-wrapper">
            <table id="werknemerTable">

                <thead>
                <tr>
                    <th>Werknemer</th>
                    <?php foreach ($weekDays as $dayName => $info): ?>
                        <th>
                            <?= $dayName ?><br>
                            <?= $info['date']->format('d/m') ?>
                        </th>
                    <?php endforeach; ?>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($werknemers as $w): ?>
                    <?php
                    $stmt = $db->prepare("SELECT dag, status FROM week_planning WHERE werknemer_id = :id AND weeknummer = :week AND jaar = :jaar");
                    $stmt->execute([':id'=>$w['id'], ':week'=>$weeknummer, ':jaar'=>$jaar]);
                    $weekStatussen = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
                    ?>
                    <tr>
                        <td class="werknemer-naam">
                            <?= ($w['voornaam'].' '.($w['tussenvoegsel']?$w['tussenvoegsel'].' ':'').$w['achternaam']) ?>
                            <span class="bhv <?= $w['BHV'] ? 'bhv-BHV' : 'bhv-BHV' ?>">
                                <?= $w['BHV'] ? '  <img src="image/BHV.png" alt="Technolab Logo" class="logo-icon">' : '' ?>
                            </span>
                        </td>
                        <?php foreach ($weekDays as $dayName => $info): ?>

                            <?php

                            $col = $info['col'];
                            $status = $weekStatussen[$col] ?? ($w['werkdag_'.$col] ? 'Aanwezig' : 'Afwezig');
                            $class = 'status-'.strtolower(str_replace(' ', '', $status));
                            ?>
                            <td>
                                <div class="status-dot <?= $class ?>" onclick="openStatusModal(<?= $w['id'] ?>, '<?= $col ?>')"></div>
                                <div id="modal-<?= $w['id'] ?>-<?= $col ?>" class="status-modal">
                                    <div class="status-modal-content">
                                        <span class="close" onclick="closeStatusModal(<?= $w['id'] ?>, '<?= $col ?>')">&times;</span>
                                        <form method="post">
                                            <input type="hidden" name="id" value="<?= $w['id'] ?>">
                                            <input type="hidden" name="dag" value="<?= $col ?>">
                                            <label><input type="radio" name="status" value="Aanwezig" <?= $status=='Aanwezig' ? 'checked' : '' ?>> Aanwezig</label><br>
                                            <label><input type="radio" name="status" value="Afwezig" <?= $status=='Afwezig' ? 'checked' : '' ?>> Afwezig</label><br>
                                            <label><input type="radio" name="status" value="Ziek" <?= $status=='Ziek' ? 'checked' : '' ?>> Ziek</label><br>
                                            <label><input type="radio" name="status" value="Eefetjes Afwezig" <?= $status=='Eefetjes Afwezig' ? 'checked' : '' ?>> Tijdelijk Afwezig</label><br><br>
                                            <button type="submit">Opslaan</button>
                                        </form>
                                    </div>
                                </div>
                            </td>
                        <?php endforeach; ?>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </main>
</div>
<script src="js/week.js"></script>

</body>
</html>

