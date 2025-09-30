<?php
try {
    $db = new PDO("mysql:host=localhost;dbname=technolab-dashboard", "root", "");
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Fout!: " . $e->getMessage());
}

$today = date('N');
$daysMap = [1 => 'werkdag_ma', 2 => 'werkdag_di', 3 => 'werkdag_wo', 4 => 'werkdag_do', 5 => 'werkdag_vr'];

if ($today >= 1 && $today <= 5) {
    $column = $daysMap[$today];
    $db->exec("UPDATE werknemers SET status = 'Afwezig' 
               WHERE status NOT IN ('Ziek','Eefetjes Afwezig','Op de school')");
    $stmt = $db->prepare("UPDATE werknemers 
                          SET status = 'Aanwezig' 
                          WHERE $column = 1 
                          AND status NOT IN ('Ziek','Eefetjes Afwezig','Op de school')");
    $stmt->execute();
}
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'], $_POST['status'])) {
    $id = intval($_POST['id']);
    $status = $_POST['status'];

    $tijdelijk_tot = null;

    if ($status === 'Eefetjes Afwezig' && !empty($_POST['tijdelijk_tot'])) {
        // Neem de gekozen tijd en plak er vandaag’s datum bij
        $tijd = $_POST['tijdelijk_tot']; // bv. 14:30
        $vandaag = date('Y-m-d');
        $tijdelijk_tot = $vandaag . ' ' . $tijd . ':00'; // bv. 2025-09-29 14:30:00
    }

    $stmt = $db->prepare("UPDATE werknemers 
                          SET status = :status, tijdelijk_tot = :tijdelijk_tot 
                          WHERE id = :id");
    $stmt->execute([
        ':status' => $status,
        ':tijdelijk_tot' => $tijdelijk_tot,
        ':id' => $id
    ]);

    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $stmt = $db->prepare("DELETE FROM werknemers WHERE id = :id");
    $stmt->execute([':id' => $id]);
}

$stmt = $db->query("SELECT id, voornaam, tussenvoegsel, achternaam, status, BHV, tijdelijk_tot
                    FROM werknemers 
                                            ORDER BY 
                        FIELD(status, 'Aanwezig', 'Eefetjes Afwezig', 'Op de school', 'Ziek', 'Afwezig'),
                        achternaam ASC, 
                        voornaam ASC");
$werknemers = $stmt->fetchAll(PDO::FETCH_ASSOC);
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
/* (grote CSS uit jouw stylesheet — hier ingekort/gekopieerd voor context,
   maar je kunt ook alleen de "Kolom Dividers" sectie toevoegen als je liever externe CSS wilt blijven gebruiken) */

/* --- Begin bestaande styles (kort voorbeeld, jouw originele bestand bevat meer) --- */
body { 
  font-family: "Inter", sans-serif;
  background: #f9fafb;
  color: #1f2937;
  margin: 0;
}

/* Header, layout, buttons, table, etc. (gebruik je bestaande CSS of laat staan zoals hieronder) */
/* ... (ik heb je originele css in je project; hieronder staan alleen de belangrijkste regels en de nieuwe divider-regels) */

/* Row status background */
.dot.status-aanwezig { background: #4ade80; }   /* groen */
.dot.status-afwezig { background: #f87171; }   /* rood */
.dot.status-ziek { background: #facc15; }      /* geel */
.dot.status-opdeschool { background: #60a5fa; }/* blauw */
.dot.status-eefetjes { background: #fb923c; }  /* oranje */


/* Table container basics (houd je huidige instellingen) */
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
  padding: 1rem 1.5rem;
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

/* --- Einde bestaande styles --- */

/* === Kolom Dividers (nieuwe, verbeterde versie) === */

/* Basis: rechter rand (verticale, "horizontale" scheiding tussen kolommen) */
table th.divider, 
table td.divider {
  border-right: 1px solid #e5e7eb; /* dikkere, lichtere rand */
  position: relative;   /* nodig voor ::after positionering */
  z-index: 1;           /* zorgt dat de celcontent boven de pseudo-elementen komt */
  padding-right: 1.25rem; /* ruimte zodat de diagonale strook niet over tekst loopt */
}

/* Geen rechterrand voor de laatste kolom */
table th.divider:last-child, 
table td.divider:last-child {
  border-right: none;
}

/* Diagonale overlay: smalle strook aan de rechterkant van elke cel */
table th.divider::after,
table td.divider::after {
  content: "";
  position: absolute;
  top: 0;
  bottom: 0;
  right: 0;
  width: 18px; /* breedte van de diagonale divider — pas aan naar smaak */
  background: repeating-linear-gradient(
    45deg,
    rgba(0,0,0,0.06) 0px,
    rgba(0,0,0,0.06) 2px,
    transparent 2px,
    transparent 6px
  );
  opacity: 0.18; /* zichtbaarheid, pas aan (0 = geen, 1 = volledig) */
  pointer-events: none;
  z-index: 0; /* achter de tekst (ouders hebben z-index:1) */
}

/* Zorg dat de laatste kolom geen diagonale strook heeft */
table th.divider:last-child::after,
table td.divider:last-child::after {
  display: none;
}

/* Kleine responsive fix: als de tabel heel smal is, verklein de strook */
@media (max-width: 640px) {
  table th.divider::after,
  table td.divider::after {
    width: 12px;
    opacity: 0.12;
  }
}
</style>
</head>
<body>
<header class="header">

    <!-- Logo -->
    <section class="logo-container">
        <a href="#" class="logo-link">
            <img src="image/technolab.png" alt="Technolab Logo" class="logo-icone">
        </a>
    </section>

    <!-- Navigatie -->
    <nav class="nav" aria-label="Main Navigation">
        <a href="dagplanning.php" class="active">Dashboard</a>
        <a href="employees.php">Employees</a>
        <a href="week.php">Week</a>
        <a href="#">Reports</a>
    </nav>

</header>

    <main class="main">
        <div class="page-header"></div>

        <!-- ✅ Toegevoegde Legenda -->
        <div class="legend" style="display: flex; gap: 15px; margin: 15px 0;">
          <div><span class="dot status-aanwezig"></span> Aanwezig</div>
          <div><span class="dot status-afwezig"></span> Afwezig</div>
          <div><span class="dot status-ziek"></span> Ziek</div>
          <div><span class="dot status-opdeschool"></span> Op de school</div>
          <div><span class="dot status-eefetjes"></span> Tijdelijk Afwezig</div>
        </div>
        <!-- ✅ Einde legenda -->

        <div class="toolbar">
            <div class="toolbar-left">
                <!-- 🔎 Zoekveld op naam -->
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
                        <th class="divider">Locatie</th>
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
                        'Op de school' => 'status-opdeschool',
                        'Eefetjes Afwezig' => 'status-eefetjes',
                        default => ''
                    };

                    // Nieuwe regel: titel per statusgroep
                    if ($w['status'] !== $currentStatus) {
                        $currentStatus = $w['status'];
                        echo "<tr class='group-header'><td colspan='4'></td></tr>";
                    }
                    ?>
                    <tr class="<?= $statusClass ?>">
                        <td class="divider"><?= ($w['voornaam'].' '.($w['tussenvoegsel']?$w['tussenvoegsel'].' ':'').$w['achternaam']) ?>   <span class="bhv <?= $w['BHV'] ? 'bhv-BHV' : 'bhv-BHV' ?>">
                        <?= $w['BHV'] ? '  <img src="image/BHV.png" alt="Technolab Logo" class="logo-icon">' : '' ?>
                            </span></td>
                        <td class="divider"><?= $w['status']=='Aanwezig'?'Technolab':'Unknown' ?></td>
                        <td class="divider tijdelijk-afwezig">
                            <form method="post" action="">
                                <input type="hidden" name="id" value="<?= $w['id'] ?>">

                                <select class="filter-elements filter-lists" name="status" onchange="this.form.submit()">
                                    <option value="Aanwezig" <?= $w['status']=='Aanwezig'?'selected':'' ?>>Aanwezig</option>
                                    <option value="Afwezig" <?= $w['status']=='Afwezig'?'selected':'' ?>>Afwezig</option>
                                    <option value="Ziek" <?= $w['status']=='Ziek'?'selected':'' ?>>Ziek</option>
                                    <option value="Op de school" <?= $w['status']=='Op de school'?'selected':'' ?>>Op school</option>
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
                            <button class="btn-action btn-details" data-id="<?= $w['id'] ?>"><i class="bi bi-pc-display-horizontal"></i></button>
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
</div>

<!-- Modal -->
<section id="detail-modal">
    <article>
        <header>
            <h2>Werknemer Details</h2>
            <button id="close-modal">&times;</button>
        </header>

        <section id="modal-content">


        </section>

    </article>

</section>

<script src="js/details.js"></script>
<script src="js/dagplanning.js"></script>

</body>
</html>
