<?php
// 📌 Database connectie
try {
    $db = new PDO("mysql:host=localhost;dbname=technolab-dashboard", "root", "");
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Fout!: " . $e->getMessage());
}

// 📅 Dag bepalen
$daysMap = [1=>'ma', 2=>'di', 3=>'wo', 4=>'do', 5=>'vr'];
$todayNum = (int)date('N');
$todayCol = $daysMap[$todayNum];
$dag = isset($_GET['dag']) && in_array($_GET['dag'], $daysMap) ? $_GET['dag'] : $todayCol;

$dagDatum = new DateTime();
$dagOffset = array_search($dag, $daysMap) - $todayNum;
$dagDatum->modify($dagOffset.' days');
$week = $dagDatum->format('W');
$jaar = $dagDatum->format('o');

// 🧼 Dubbele rijen voorkomen via unieke sleutel (moet al in DB staan)
$insertUpdate = $db->prepare("
    INSERT INTO week_planning (werknemer_id, weeknummer, jaar, dag, status, tijdelijk_tot)
    VALUES (:id, :week, :jaar, :dag, :status, :tijdelijk_tot)
    ON DUPLICATE KEY UPDATE status=VALUES(status), tijdelijk_tot=VALUES(tijdelijk_tot)
");

// 🧑 Werknemers inplannen als ze er nog niet in staan
$stmtWerknemers = $db->query("SELECT * FROM werknemers");
$werknemers = $stmtWerknemers->fetchAll(PDO::FETCH_ASSOC);

foreach ($werknemers as $w) {
    $check = $db->prepare("SELECT id FROM week_planning WHERE werknemer_id=:id AND weeknummer=:week AND jaar=:jaar AND dag=:dag");
    $check->execute([':id'=>$w['id'], ':week'=>$week, ':jaar'=>$jaar, ':dag'=>$dag]);
    if (!$check->fetch()) {
        $werkdagKolom = 'werkdag_'.$dag;
        $status = ($w[$werkdagKolom] == 1) ? 'Aanwezig' : 'Afwezig';
        $insertUpdate->execute([
            ':id'=>$w['id'],
            ':week'=>$week,
            ':jaar'=>$jaar,
            ':dag'=>$dag,
            ':status'=>$status,
            ':tijdelijk_tot'=>null
        ]);
    }
}

// ⏰ Tijdelijk afwezig resetten als tijd voorbij is
$checkTijd = $db->prepare("SELECT id, tijdelijk_tot FROM week_planning WHERE weeknummer=:week AND jaar=:jaar AND dag=:dag AND status='Eefetjes Afwezig'");
$checkTijd->execute([':week'=>$week, ':jaar'=>$jaar, ':dag'=>$dag]);
$now = time();
$reset = $db->prepare("UPDATE week_planning SET status='Aanwezig', tijdelijk_tot=NULL WHERE id=:id");
while ($row = $checkTijd->fetch(PDO::FETCH_ASSOC)) {
    if ($row['tijdelijk_tot'] && strtotime($row['tijdelijk_tot']) <= $now) {
        $reset->execute([':id'=>$row['id']]);
    }
}

// 📨 Status updaten
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'], $_POST['status'])) {
    $id = (int)$_POST['id'];
    $status = $_POST['status'];
    $tijdelijk_tot = null;

    if ($status === 'Eefetjes Afwezig' && !empty($_POST['tijd'])) {
        $tijdelijk_tot = $dagDatum->format('Y-m-d') . ' ' . $_POST['tijd'] . ':00';
    }

    $insertUpdate->execute([
        ':id'=>$id,
        ':week'=>$week,
        ':jaar'=>$jaar,
        ':dag'=>$dag,
        ':status'=>$status,
        ':tijdelijk_tot'=>$tijdelijk_tot
    ]);

    // Ook hoofdstatus bijwerken in werknemers voor overzicht
    $db->prepare("UPDATE werknemers SET status=:status WHERE id=:id")
        ->execute([':status'=>$status, ':id'=>$id]);

    header("Location: ".$_SERVER['PHP_SELF']."?dag=".$dag);
    exit;
}

// 🗑️ Bulk verwijderen
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['bulk_delete']) && !empty($_POST['selected_ids'])) {
    $ids = array_map('intval', $_POST['selected_ids']);
    $placeholders = implode(',', array_fill(0, count($ids), '?'));
    $stmt = $db->prepare("DELETE FROM werknemers WHERE id IN ($placeholders)");
    $stmt->execute($ids);
    header("Location: ".$_SERVER['PHP_SELF']."?dag=".$dag);
    exit;
}

// 👥 Data ophalen voor tabel
$stmt = $db->prepare("
    SELECT w.id, w.voornaam, w.tussenvoegsel, w.achternaam, w.BHV, wp.status, wp.tijdelijk_tot
    FROM werknemers w
    LEFT JOIN week_planning wp 
        ON w.id = wp.werknemer_id
        AND wp.weeknummer = :week
        AND wp.jaar = :jaar
        AND wp.dag = :dag
    ORDER BY FIELD(wp.status,'Aanwezig','Eefetjes Afwezig','Ziek','Afwezig'),
             w.achternaam, w.voornaam
");
$stmt->execute([':week'=>$week, ':jaar'=>$jaar, ':dag'=>$dag]);
$werknemersStatus = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
    <link href="css/dagplanning.css" rel="stylesheet"/>
    <link href="css/nav.css" rel="stylesheet"/>
    <title>Dagplanning</title>
</head>
<body>
<header class="header">
    <section class="logo-container">
        <a href="#" class="logo-link">
            <img src="image/technolab.png" alt="Technolab Logo" class="logo-icone">
        </a>
    </section>
</header>

<main class="main">
    <div class="page-header">
        <h4><?= $dagDatum->format('l d-m-Y') ?> </h4>
        <div class="day-nav" style="margin:10px 0;">
            <a href="?dag=ma" class="btn btn-outline-secondary btn-sm">Ma</a>
            <a href="?dag=di" class="btn btn-outline-secondary btn-sm">Di</a>
            <a href="?dag=wo" class="btn btn-outline-secondary btn-sm">Wo</a>
            <a href="?dag=do" class="btn btn-outline-secondary btn-sm">Do</a>
            <a href="?dag=vr" class="btn btn-outline-secondary btn-sm">Vr</a>
        </div>
    </div>

    <div class="legend" style="display:flex;gap:15px;margin:15px 0;">
        <div><span class="dot status-aanwezig"></span> Aanwezig</div>
        <div><span class="dot status-afwezig"></span> Afwezig</div>
        <div><span class="dot status-ziek"></span> Ziek</div>
        <div><span class="dot status-eefetjes"></span> Tijdelijk Afwezig</div>
    </div>

    <div class="toolbar">
        <div class="toolbar-left">
            <input type="text" id="search" placeholder="Zoek op naam...">
            <div class="toggle-group">
                <button class="toggle active">Vandaag</button>
                <a href="week.php"><button class="toggle">Week</button></a>
            </div>
            <div class="toolbar-right" style="display:flex;gap:10px;">
                <button id="select-mode" class="btn btn-outline-secondary btn-sm">Selecteren</button>
                <form method="post" id="bulk-delete-form" style="display:none;">
                    <input type="hidden" name="bulk_delete" value="1">
                    <button type="submit" class="btn btn-danger btn-sm">Verwijderen</button>
                </form>
            </div>
        </div>
    </div>

    <div class="table-container">
        <table>
            <thead>
            <tr>
                <th><input type="checkbox" id="select-all"></th>
                <th>Naam</th>
                <th>Status</th>
                <th>Actie</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($werknemersStatus as $w): ?>
                <?php
                $status = $w['status'] ?? 'Afwezig';
                $statusClass = match($status) {
                    'Aanwezig' => 'status-aanwezig',
                    'Afwezig' => 'status-afwezig',
                    'Ziek' => 'status-ziek',
                    'Eefetjes Afwezig' => 'status-eefetjes',
                    default => ''
                };
                ?>
                <tr class="<?= $statusClass ?>">
                    <td><input type="checkbox" name="selected_ids[]" value="<?= $w['id'] ?>" class="select-row" form="bulk-delete-form" style="display:none;"></td>
                    <td><?= htmlspecialchars($w['voornaam'].' '.($w['tussenvoegsel']?$w['tussenvoegsel'].' ':'').$w['achternaam']) ?>
                        <?= $w['BHV'] ? '<img src="image/BHV.png" alt="BHV" class="logo-icon">' : '' ?>
                    </td>
                    <td>
                        <form method="post">
                            <input type="hidden" name="id" value="<?= $w['id'] ?>">
                            <select name="status" onchange="toggleTijd(this, <?= $w['id'] ?>); this.form.submit();">
                                <option value="Aanwezig" <?= $status=='Aanwezig'?'selected':'' ?>>Aanwezig</option>
                                <option value="Afwezig" <?= $status=='Afwezig'?'selected':'' ?>>Afwezig</option>
                                <option value="Ziek" <?= $status=='Ziek'?'selected':'' ?>>Ziek</option>
                                <option value="Eefetjes Afwezig" <?= $status=='Eefetjes Afwezig'?'selected':'' ?>>Tijdelijk Afwezig</option>
                            </select>
                            <input type="time" name="tijd" id="tijdveld-<?= $w['id'] ?>" value="<?= $w['tijdelijk_tot'] ? date('H:i', strtotime($w['tijdelijk_tot'])) : '' ?>" style="display:<?= $status=='Eefetjes Afwezig'?'inline-block':'none' ?>;">
                        </form>
                    </td>
                    <td><a href="#" class="btn btn-sm btn-outline-primary btn-details" data-id="<?= $w['id'] ?>"><i class="bi bi-pc-display-horizontal"></i></a></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <br>
    <a href="add.php"><button>➕ Voeg een medewerker toe</button></a>
</main>

<!-- 📌 Modal -->
<div id="detail-modal">
    <div id="modal-content"></div>
    <button id="close-modal">&times;</button>
</div>

<script>
    // Verbeterde details.js code voor popup
    document.querySelectorAll('.btn-details').forEach(btn => {
        btn.addEventListener('click', () => {
            const id = btn.dataset.id;
            fetch('details.php?id=' + id)
                .then(response => response.text())
                .then(data => {
                    const parser = new DOMParser();
                    const doc = parser.parseFromString(data, 'text/html');
                    const card = doc.querySelector('.card');
                    document.getElementById('modal-content').innerHTML = card ? card.outerHTML : '<p>Geen details gevonden.</p>';
                    document.getElementById('detail-modal').style.display = 'flex';
                })
                .catch(() => {
                    document.getElementById('modal-content').innerHTML = '<p class="text-danger">Fout bij laden van details.</p>';
                    document.getElementById('detail-modal').style.display = 'flex';
                });
        });
    });

    document.getElementById('close-modal').addEventListener('click', () => {
        document.getElementById('detail-modal').style.display = 'none';
    });
    window.addEventListener('click', e => {
        if (e.target.id === 'detail-modal') {
            document.getElementById('detail-modal').style.display = 'none';
        }
    });
    const selectModeBtn = document.getElementById('select-mode');
    const bulkDeleteForm = document.getElementById('bulk-delete-form');
    const checkboxes = document.querySelectorAll('.select-row');
    const selectAll = document.getElementById('select-all');

    let selecting = false;

    selectModeBtn.addEventListener('click', () => {
        selecting = !selecting;
        document.querySelectorAll('.select-row').forEach(cb => {
            cb.style.display = selecting ? 'inline-block' : 'none';
            cb.checked = false;
        });
        selectAll.style.display = selecting ? 'inline-block' : 'none';
        bulkDeleteForm.style.display = selecting ? 'inline-block' : 'none';
        selectModeBtn.textContent = selecting ? 'Annuleren' : 'Selecteren';
    });

    selectAll.addEventListener('change', (e) => {
        checkboxes.forEach(cb => cb.checked = e.target.checked);
    });
</script>
</body>
</html>
