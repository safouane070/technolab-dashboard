<?php
try {
    $db = new PDO("mysql:host=localhost;dbname=technolab-dashboard", "root", "");
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Fout!: " . $e->getMessage());
}



if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'], $_POST['dag'], $_POST['status'])) {
    $dag = $_POST['dag'];
    $toegestaneVelden = ['status_ma','status_di','status_wo','status_do','status_vr'];
    if (in_array($dag, $toegestaneVelden)) {
        $stmt = $db->prepare("UPDATE werknemers SET $dag = :status WHERE id = :id");
        $stmt->execute([
            ':status' => $_POST['status'],
            ':id' => intval($_POST['id'])
        ]);
    }
    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}



$stmt = $db->query("SELECT * FROM werknemers ORDER BY achternaam ASC, voornaam ASC");
$werknemers = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Dagen van de week
$weekDays = [
    'Ma' => 'status_ma',
    'Di' => 'status_di',
    'Wo' => 'status_wo',
    'Do' => 'status_do',
    'Vr' => 'status_vr',
];

// Huidige week berekenen (maandag t/m vrijdag)
$startOfWeek = new DateTime();
$startOfWeek->modify(('Monday' == $startOfWeek->format('l')) ? 'this monday' : 'last monday');


$weekDays = [
    'Ma' => 'status_ma',
    'Di' => 'status_di',
    'Wo' => 'status_wo',
    'Do' => 'status_do',
    'Vr' => 'status_vr',
];



$startOfWeek = new DateTime();
$startOfWeek->modify(( 'Monday' == $startOfWeek->format('l')) ? 'this monday' : 'last monday');
$weekDays = [
    'Ma' => ['col' => 'status_ma', 'date' => clone $startOfWeek],
    'Di' => ['col' => 'status_di', 'date' => (clone $startOfWeek)->modify('+1 day')],
    'Wo' => ['col' => 'status_wo', 'date' => (clone $startOfWeek)->modify('+2 day')],
    'Do' => ['col' => 'status_do', 'date' => (clone $startOfWeek)->modify('+3 day')],
    'Vr' => ['col' => 'status_vr', 'date' => (clone $startOfWeek)->modify('+4 day')],
];

// Huidige weekdagen
$dagen = ['ma', 'di', 'wo', 'do', 'vr'];
$dagenVolledig = ['Maandag', 'Dinsdag', 'Woensdag', 'Donderdag', 'Vrijdag'];
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
</head>
<body>
<div class="app">

    <!-- Header -->
    <header class="header">
        <div class="header-left">
            <a class="brand" href="#">
                <svg class="brand-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"
                          stroke-linecap="round" stroke-linejoin="round" stroke-width="2"></path>
                </svg>
                <span class="brand-name">Acme HR</span>
            </a>
        </div>
        <nav class="nav">
            <a href="#">Dashboard</a>
            <a href="#">Employees</a>
            <a class="active" href="#">Absences</a>
            <a href="#">Reports</a>
        </nav>
        <div class="header-right"></div>
    </header>

    <!-- Main -->
    <main class="main">
        <div class="main-header">
            <h1>Absence List</h1>
            <div class="legend">
                <div><span class="dot status-aanwezig"></span> Aanwezig</div>
                <div><span class="dot status-afwezig"></span> Afwezig</div>
                <div><span class="dot status-ziek"></span> Ziek</div>
                <div><span class="dot status-opdeschool"></span> Op de school</div>
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

                <!-- Today / Week knoppen naast zoekveld -->
                <div class="range-buttons" style="display: flex; gap: 5px;">
                    <a href="dagplanning.php"><button class="toggle">Today</button></a>
                    <button class="active">Week</button>
                </div>

                <!-- Reset knop -->
                <button class="reset-btn" style="margin-left: 10px;">
                    <span class="material-symbols-outlined">refresh</span> Reset
                </button>
            </div>

            <div class="filter-right">
                <button class="icon-button"><span class="material-symbols-outlined">chevron_left</span></button>
                <span class="date-label"><?= $startOfWeek->format('F d') ?> - <?= (clone $startOfWeek)->modify('+4 day')->format('F d, Y') ?></span>
                <button class="icon-button"><span class="material-symbols-outlined">chevron_right</span></button>
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
                    <tr>
                        <td class="werknemer-naam"><?= ($w['voornaam'].' '.($w['tussenvoegsel'] ? $w['tussenvoegsel'].' ' : '').$w['achternaam']) ?></td>
                        <?php foreach ($weekDays as $dayName => $info): ?>
                            <?php $col = $info['col']; $status = $w[$col]; $class = 'status-'.strtolower(str_replace(' ', '', $status)); ?>
                            <td>
                                <div class="status-dot <?= $class ?>" onclick="openStatusModal(<?= $w['id'] ?>, '<?= $col ?>')"></div>
                                <div id="modal-<?= $w['id'] ?>-<?= $col ?>" class="status-modal">
                                    <div class="status-modal-content">
                                        <span class="close" onclick="closeStatusModal(<?= $w['id'] ?>, '<?= $col ?>')">&times;</span>
                                        <form method="post" action="">
                                            <input type="hidden" name="id" value="<?= $w['id'] ?>">
                                            <input type="hidden" name="dag" value="<?= $col ?>">
                                            <label><input type="radio" name="status" value="Aanwezig" <?= $status=='Aanwezig' ? 'checked' : '' ?>> Aanwezig</label><br>
                                            <label><input type="radio" name="status" value="Afwezig" <?= $status=='Afwezig' ? 'checked' : '' ?>> Afwezig</label><br>
                                            <label><input type="radio" name="status" value="Ziek" <?= $status=='Ziek' ? 'checked' : '' ?>> Ziek</label><br>
                                            <label><input type="radio" name="status" value="Op de school" <?= $status=='Op de school' ? 'checked' : '' ?>> Op de school</label><br>
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

<script>
    function openStatusModal(id, dag) {
        document.getElementById(`modal-${id}-${dag}`).style.display = "block";
    }
    function closeStatusModal(id, dag) {
        document.getElementById(`modal-${id}-${dag}`).style.display = "none";
    }
    window.onclick = function(event) {
        const modals = document.querySelectorAll('.status-modal');
        modals.forEach(modal => { if (event.target == modal) modal.style.display = "none"; });
    }

    // Live zoeken op naam
    const searchInput = document.getElementById('searchInput');
    searchInput.addEventListener('input', () => {
        const filter = searchInput.value.toLowerCase();
        const rows = document.querySelectorAll('#werknemerTable tbody tr');
        rows.forEach(row => {
            const naam = row.querySelector('.werknemer-naam').textContent.toLowerCase();
            row.style.display = naam.includes(filter) ? '' : 'none';
        });
    });
</script>
</body>
</html>