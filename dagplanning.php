<?php
try {
    $db = new PDO("mysql:host=localhost;dbname=technolab-dashboard", "root", "");
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Fout!: " . $e->getMessage());
}

// Dag kiezen via GET (?dag=ma, ?dag=di, ...) of standaard vandaag
$daysMap = [1=>'ma',2=>'di',3=>'wo',4=>'do',5=>'vr'];
$todayNum = (int)date('N');
$todayCol = $daysMap[$todayNum];

$dag = isset($_GET['dag']) && in_array($_GET['dag'], $daysMap) ? $_GET['dag'] : $todayCol;

// Bepaal datum van de gekozen dag
$dagDatum = new DateTime();
$dagOffset = array_search($dag, $daysMap) - $todayNum;
$dagDatum->modify($dagOffset.' days');

$week = $dagDatum->format('W');
$jaar = $dagDatum->format('o');

// Werknemers ophalen
$stmt = $db->query("SELECT * FROM werknemers ORDER BY achternaam ASC, voornaam ASC");
$werknemers = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Automatisch invullen indien nog geen status in week_planning
foreach($werknemers as $w) {
    $id = $w['id'];

    // Check of er al status is
    $stmtCheck = $db->prepare("SELECT status FROM week_planning WHERE werknemer_id=:id AND weeknummer=:week AND jaar=:jaar AND dag=:dag");
    $stmtCheck->execute([':id'=>$id, ':week'=>$week, ':jaar'=>$jaar, ':dag'=>$dag]);
    $row = $stmtCheck->fetch(PDO::FETCH_ASSOC);

    if(!$row) {
        $werkdagKolom = 'werkdag_'.$dag;
        $status = ($w[$werkdagKolom]==1) ? 'Aanwezig' : 'Afwezig';

        $stmtInsert = $db->prepare("INSERT INTO week_planning (werknemer_id, weeknummer, jaar, dag, status) VALUES (:id,:week,:jaar,:dag,:status)");
        $stmtInsert->execute([
            ':id'=>$id, ':week'=>$week, ':jaar'=>$jaar, ':dag'=>$dag, ':status'=>$status
        ]);
    }
}

// POST update van status (handmatig)
if($_SERVER['REQUEST_METHOD']==='POST' && isset($_POST['id'], $_POST['status'])){
    $id = (int)$_POST['id'];
    $status = $_POST['status'];

    $stmt = $db->prepare("INSERT INTO week_planning (werknemer_id, weeknummer, jaar, dag, status)
        VALUES (:id,:week,:jaar,:dag,:status)
        ON DUPLICATE KEY UPDATE status=VALUES(status)");
    $stmt->execute([':id'=>$id, ':week'=>$week, ':jaar'=>$jaar, ':dag'=>$dag, ':status'=>$status]);

    header("Location: ".$_SERVER['PHP_SELF']."?dag=".$dag);
    exit;
}

// Werknemers + status ophalen voor tabel
$stmt = $db->prepare("SELECT w.id, w.voornaam, w.tussenvoegsel, w.achternaam, wp.status
                      FROM werknemers w
                      LEFT JOIN week_planning wp 
                      ON w.id=wp.werknemer_id AND wp.weeknummer=:week AND wp.jaar=:jaar AND wp.dag=:dag
                      ORDER BY FIELD(wp.status,'Aanwezig','Afwezig','Ziek','Eefetjes Afwezig'), w.achternaam, w.voornaam");
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
<title>Absence Tracker</title>

<style>
body { 
  font-family: "Inter", sans-serif;
  background: #f9fafb;
  color: #1f2937;
  margin: 0;
}

/* Status dots */
.dot.status-aanwezig { background: #4ade80; }
.dot.status-afwezig { background: #f87171; }
.dot.status-ziek { background: #facc15; }
.dot.status-eefetjes { background: #fb923c; }

/* Table container */
.table-container {
  background: #fff;
  border: 1px solid #e5e7eb;
  border-radius: 0.75rem;
  overflow-x: auto;
  box-shadow: 0 1px 3px rgba(0,0,0,0.05);
}

/* Table basics */
table {
  width: 100%;
  border-collapse: collapse;
  min-width: 600px;
}
thead { background: #f3f4f6; }
th, td {
  padding: 0.9rem 1.5rem;
  text-align: left;
  font-size: 0.875rem;
}

/* Subtiele divider tussen statusgroepen */
.group-header td {
  border-top: 2px solid #e5e7eb;
  padding: 0;
  height: 0.5rem;
  background: #f9fafb;
}

/* Kolom Dividers (diagonaal) */
table th.divider, 
table td.divider {
  border-right: 1px solid #e5e7eb;
  position: relative;
  z-index: 1;
  padding-right: 1.25rem;
}
table th.divider:last-child, 
table td.divider:last-child { border-right: none; }
table th.divider::after,
table td.divider::after {
  content: "";
  position: absolute;
  top: 0;
  bottom: 0;
  right: 0;
  width: 18px;
  background: repeating-linear-gradient(
    45deg,
    rgba(0,0,0,0.06) 0px,
    rgba(0,0,0,0.06) 2px,
    transparent 2px,
    transparent 6px
  );
  opacity: 0.18;
  pointer-events: none;
  z-index: 0;
}
table th.divider:last-child::after,
table td.divider:last-child::after { display: none; }

/* Zwarte onderranden tussen personen */
table tbody tr:not(.group-header) td {
  border-bottom: 1px solid #000;
}
table tbody tr:not(.group-header):last-child td {
  border-bottom: none;
}

/* BHV-icoon inline zodat rijen gelijk blijven */
td .logo-icon {
    display: inline-block;
    vertical-align: middle;
    width: 20px;
    height: 20px;
    margin-left: 4px;
}

/* Kleine paddingfix */
table td { padding-top: 0.9rem; padding-bottom: 0.9rem; }

/* Modal CSS */
#detail-modal {
  display: none;
  position: fixed;
  z-index: 9999;
  left: 0;
  top: 0;
  width: 100%;
  height: 100%;
  overflow: auto;
  background-color: rgba(0,0,0,0.5);
}
#modal-content {
  background-color: #fff;
  margin: 50px auto;
  padding: 20px;
  border-radius: 8px;
  max-width: 700px;
  position: relative;
}
#close-modal {
  position: absolute;
  top: 10px;
  right: 10px;
  border: none;
  background: none;
  font-size: 1.5rem;
  cursor: pointer;
}
</style>
</head>
<body>
<header class="header">
    <section class="logo-container">
        <a href="#" class="logo-link">
            <img src="image/technolab.png" alt="Technolab Logo" class="logo-icone">
        </a>
    </section>
    <nav class="nav" aria-label="Main Navigation"></nav>
</header>

<main class="main">
    <div class="page-header"></div>

    <div class="legend" style="display: flex; gap: 15px; margin: 15px 0;">
      <div><span class="dot status-aanwezig"></span> Aanwezig</div>
      <div><span class="dot status-afwezig"></span> Afwezig</div>
      <div><span class="dot status-ziek"></span> Ziek</div>
      <div><span class="dot status-eefetjes"></span> Tijdelijk Afwezig</div>
    </div>

    <div class="toolbar">
        <div class="toolbar-left">
            <div class="search-input">
                <input type="text" id="search" placeholder="Zoek op naam...">
            </div>
            <div class="toggle-group">
                <button class="toggle active">Vandaag</button>
                <a href="week.php"><button class="toggle">Week</button></a>
            </div>
        </div>
    </div>

    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th class="divider">Naam</th>
                    <th class="divider">Status</th>
                    <th class="divider">Acties</th>
                </tr>
            </thead>
            <tbody>
            <?php
            $currentStatus = '';
            foreach($werknemers as $w):
                $statusClass = match($w['status']){
                    'Aanwezig' => 'status-aanwezig',
                    'Afwezig' => 'status-afwezig',
                    'Ziek' => 'status-ziek',
                    'Eefetjes Afwezig' => 'status-eefetjes',
                    default => ''
                };

                if ($w['status'] !== $currentStatus) {
                    $currentStatus = $w['status'];
                    echo "<tr class='group-header'><td colspan='4'></td></tr>";
                }
                ?>
                <tr class="<?= $statusClass ?>">
                    <td class="divider"><?= ($w['voornaam'].' '.($w['tussenvoegsel']?$w['tussenvoegsel'].' ':'').$w['achternaam']) ?> 
                        <?= $w['BHV'] ? '<img src="image/BHV.png" alt="BHV" class="logo-icon">' : '' ?>
                    </td>
                    <td class="divider tijdelijk-afwezig">
                        <form method="post" action="">
                            <input type="hidden" name="id" value="<?= $w['id'] ?>">
                            <select class="filter-elements filter-lists" name="status" onchange="this.form.submit()">
                                <option value="Aanwezig" <?= $w['status']=='Aanwezig'?'selected':'' ?>>Aanwezig</option>
                                <option value="Afwezig" <?= $w['status']=='Afwezig'?'selected':'' ?>>Afwezig</option>
                                <option value="Ziek" <?= $w['status']=='Ziek'?'selected':'' ?>>Ziek</option>
                                <option value="Eefetjes Afwezig" <?= $w['status']=='Eefetjes Afwezig'?'selected':'' ?>>Tijdelijk Afwezig</option>
                            </select>
                            <?php if($w['status']=='Eefetjes Afwezig'): ?>
                                <label>Tot tijd:</label>
                                <input type="time" name="tijdelijk_tot"
                                       value="<?= $w['tijdelijk_tot'] ? date('H:i', strtotime($w['tijdelijk_tot'])) : '' ?>"
                                       onchange="this.form.submit()">
                            <?php endif; ?>
                        </form>
                    </td>
                    <td class="divider action-icons">
                        <a href="#" class="btn-action btn-details" data-id="<?= $w['id'] ?>">
                            <i class="bi bi-pc-display-horizontal"></i>
                        </a>
                        <a href="?delete=<?= $w['id'] ?>" class="btn-action btn-delete" onclick="return confirm('Weet je zeker?');"><i class="bi bi-trash3"></i></a>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <br>
    <a href="add.php"><button>➕ Voeg een medewerker toe</button></a>
</main>

<script src="js/details.js"></script>
<script src="js/dagplanning.js"></script>

<!-- ✅ Modal container -->
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
</script>
</body>
</html>
