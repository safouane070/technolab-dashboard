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
    $db->exec("UPDATE werknemers SET status = 'Afwezig' WHERE status NOT IN ('Ziek','Eefetjes Afwezig','Op de school')");
    $stmt = $db->prepare("UPDATE werknemers SET status = 'Aanwezig' WHERE $column = 1 AND status NOT IN ('Ziek','Eefetjes Afwezig','Op de school')");
    $stmt->execute();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'], $_POST['status'])) {
    $stmt = $db->prepare("UPDATE werknemers SET status = :status WHERE id = :id");
    $stmt->execute([':status' => $_POST['status'], ':id' => intval($_POST['id'])]);
    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}

if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $stmt = $db->prepare("DELETE FROM werknemers WHERE id = :id");
    $stmt->execute([':id' => $id]);
}

$stmt = $db->query("SELECT id, voornaam, tussenvoegsel, achternaam, status FROM werknemers ORDER BY achternaam ASC, voornaam ASC");
$werknemers = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8"/>
<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
<link href="css/dagplanning.css" rel="stylesheet"/>
<title>Absence Tracker</title>
</head>
<body>
<div class="app">
    <header class="header">
        <div class="logo-container">
            <a href="#" class="logo-link">
                <svg class="logo-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"
                    stroke-linecap="round" stroke-linejoin="round" stroke-width="2"></path>
                </svg>
                <span class="logo-text">Acme HR</span>
            </a>
        </div>
        <nav class="nav">
            <a href="dagplanning.php" class="active">Dashboard</a>
            <a href="#">Employees</a>
            <a href="absent.php">Absences</a>
            <a href="#">Reports</a>
        </nav>
        <div class="user-actions">
            <button class="icon-btn"></button>
        </div>
    </header>

    <main class="main">
        <div class="page-header">
            
        </div>

        <div class="toolbar">
            <div class="toolbar-left">
                <div class="date-input">
                    <span class="material-symbols-outlined icon"></span>
                    <input type="text" placeholder="Select Dates">
                </div>
                <div class="toggle-group">
                    <button class="toggle active">Today</button>
                    <a href="week.php"><button class="toggle">Week</button></a>
                </div>
                <button class="btn-secondary"><span class="material-symbols-outlined"></span> Reset</button>
            </div>
        </div>

        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>Naam</th>
                        <th>Locatie</th>
                        <th>Status</th>
                        <th>Acties</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach($werknemers as $w): 
                    $statusClass = match($w['status']){
                        'Aanwezig' => 'status-aanwezig',
                        'Afwezig' => 'status-afwezig',
                        'Ziek' => 'status-ziek',
                        'Op de school' => 'status-opdeschool',
                        'Eefetjes Afwezig' => 'status-eefetjes',
                        default => ''
                    };
                ?>
                    <tr class="<?= $statusClass ?>">
                        <td><?= htmlspecialchars($w['voornaam'].' '.($w['tussenvoegsel']?$w['tussenvoegsel'].' ':'').$w['achternaam']) ?></td>
                        <td><?= $w['status']=='Aanwezig'?'Technolab':'Unknown' ?></td>
                        <td>
                            <form method="post" action="">
                                <select class="filter-elements filter-lists" name="status" onchange="this.form.submit()">
                                    <option value="Aanwezig" <?= $w['status']=='Aanwezig'?'selected':'' ?>>Aanwezig</option>
                                    <option value="Afwezig" <?= $w['status']=='Afwezig'?'selected':'' ?>>Afwezig</option>
                                    <option value="Ziek" <?= $w['status']=='Ziek'?'selected':'' ?>>Ziek</option>
                                    <option value="Op de school" <?= $w['status']=='Op de school'?'selected':'' ?>>Op school</option>
                                    <option value="Eefetjes Afwezig" <?= $w['status']=='Eefetjes Afwezig'?'selected':'' ?>>tijdelijk Afwezig</option>
                                </select>
                                <input type="hidden" name="id" value="<?= $w['id'] ?>">
                            </form>
                        </td>
                        <td class="action-icons">
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
        <section id="modal-content"></section>
    </article>
</section>

<script src="js/details.js"></script>
</body>
</html>
