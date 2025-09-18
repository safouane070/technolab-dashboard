<?php

try {
    $db = new PDO("mysql:host=localhost;dbname=technolab-dashboard", "root", "");
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Fout!: " . $e->getMessage());
}

$today = date('N'); // 1 = maandag, 2 = dinsdag, ... 7 = zondag

$daysMap = [
    1 => 'werkdag_ma',
    2 => 'werkdag_di',
    3 => 'werkdag_wo',
    4 => 'werkdag_do',
    5 => 'werkdag_vr'
];

if ($today >= 1 && $today <= 5) {
    $column = $daysMap[$today];

    $db->exec("UPDATE werknemers SET status = 'Afwezig' WHERE status NOT IN ('Ziek','Eefetjes Afwezig','Op de school')");

    $stmt = $db->prepare("UPDATE werknemers SET status = 'Aanwezig' WHERE $column = 1 AND status NOT IN ('Ziek','Eefetjes Afwezig','Op de school')");
    $stmt->execute();
}


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'], $_POST['status'])) {
    $stmt = $db->prepare("UPDATE werknemers SET status = :status WHERE id = :id");
    $stmt->execute([
        ':status' => $_POST['status'],
        ':id' => intval($_POST['id'])
    ]);
    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}


if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $stmt = $db->prepare("DELETE FROM werknemers WHERE id = :id");
    $stmt->execute([':id' => $id]);
}


$stmt = $db->query("SELECT id, voornaam, tussenvoegsel, achternaam, status FROM werknemers");
$werknemers = $stmt->fetchAll(PDO::FETCH_ASSOC);
$stmt = $db->query("
    SELECT id, voornaam, tussenvoegsel, achternaam, status 
    FROM werknemers
    ORDER BY achternaam ASC, voornaam ASC
");
$werknemers = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">

    <title>Absence Tracker</title>

  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet"/>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
  <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet"/>
  <link rel="stylesheet" href="css/dagplanning.css"/>
</head>
<style>


.status-aanwezig { background-color: #55cc32; }
.status-afwezig  { background-color: #df2a2a; }
.status-ziek     { background-color: #f7e379; }
.status-opdeschool { background-color: #bbdb44; }
.status-eefetjes { background-color: #f2a134; }

</style>
<body>
  <div class="app">
    <header class="header">
      <div class="logo-container">
        <a href="#" class="logo-link">
          <svg class="logo-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"
            xmlns="http://www.w3.org/2000/svg">
            <path d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"
              stroke-linecap="round" stroke-linejoin="round" stroke-width="2"></path>
          </svg>
          <span class="logo-text">Acme HR</span>
        </a>
      </div>

      <nav class="nav">
        <a href="dagplanning.php"class="active">Dashboard</a>
        <a href="#">Employees</a>
        <a href="absent.php" >Absences</a>
        <a href="#">Reports</a>
      </nav>

      <div class="user-actions">
        <button class="icon-btn">
           </div>
    </header>

    <main class="main">
      <div class="page-header">
        <h1>Absence List</h1>
        <div class="status-legend">
          <div class="status"><span class="dot present"></span>Present</div>
          <div class="status"><span class="dot absent"></span>Absent</div>
          <div class="status"><span class="dot planned"></span>Planned</div>
          <button class="btn-primary">
               <span class="material-symbols-outlined">add</span> Add Absence


          </button>
        </div>
      </div>

      <div class="toolbar">
        <div class="toolbar-left">
          <div class="date-input">
            <span class="material-symbols-outlined icon">calendar_today</span>
            <input type="text" placeholder="Select Dates">
          </div>
          <div class="toggle-group">
            <button class="toggle active">Today</button>
              <a href="week.php"><button class="toggle">Week</button></a>

            <button class="toggle">Month</button>
          </div>
          <button class="btn-secondary">
            <span class="material-symbols-outlined">refresh</span> Reset
          </button>
        </div>
        <div class="toolbar-right">
          <button class="icon-btn"><span class="material-symbols-outlined">chevron_left</span></button>
          <span class="current-date">October 21, 2024</span>
          <button class="icon-btn"><span class="material-symbols-outlined">chevron_right</span></button>
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
          <tbody>
            <?php if (count($werknemers) > 0): ?>
                <?php foreach ($werknemers as $w): ?>
                    <?php
                    $statusClass = '';
                    if ($w['status'] == 'Aanwezig') {
                        $statusClass = 'status-aanwezig';
                    } elseif ($w['status'] == 'Afwezig') {
                        $statusClass = 'status-afwezig';
                    } elseif ($w['status'] == 'Ziek') {
                        $statusClass = 'status-ziek';
                    } elseif ($w['status'] == 'Op de school') {
                        $statusClass = 'status-opdeschool';
                    } elseif ($w['status'] == 'Eefetjes Afwezig') {
                        $statusClass = 'status-eefetjes';
                    }
                    ?>
                    <tr class="<?= $statusClass ?>">
                        <td>
                            <?= ($w['voornaam'] . ' ' . ($w['tussenvoegsel'] ? $w['tussenvoegsel'].' ' : '') . $w['achternaam']) ?>
                        </td>
                        <td>
                            <?= $w['status'] == 'Aanwezig' ? 'Technolab' : 'Unknown' ?>
                        </td>
                        <td>
                            <form method="post" action="">
                                <select class="filter-elements filter-lists" id="filter-status" name="status" onchange="this.form.submit()">
                                    <option value="Aanwezig" <?= $w['status']=='Aanwezig' ? 'selected' : '' ?>>Aanwezig</option>
                                    <option value="Afwezig" <?= $w['status']=='Afwezig' ? 'selected' : '' ?>>Afwezig</option>
                                    <option value="Ziek" <?= $w['status']=='Ziek' ? 'selected' : '' ?>>Ziek</option>
                                    <option value="Op de school" <?= $w['status']=='Op de school' ? 'selected' : '' ?>>Op de school</option>
                                    <option value="Eefetjes Afwezig" <?= $w['status']=='Eefetjes Afwezig' ? 'selected' : '' ?>>Eefetjes Afwezig</option>
                                </select>
                                <input type="hidden" name="id" value="<?= $w['id'] ?>">
                            </form>
                        </td>
                        <td>
                            <a href="details.php?id=<?= $w['id'] ?>"><i class="bi bi-pc-display-horizontal"></i>|</a>
                            <a href="?delete=<?= $w['id'] ?>" onclick="return confirm('Weet je zeker dat je deze werknemer wilt verwijderen?');"><i class=" bi bi-trash3"></i></a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr><td colspan="4">Geen werknemers gevonden</td></tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
        <br>
        <a href="add.php">
            <button>➕ Voeg een medewerker toe</button>
        </a>
    </main>
  </div>
</body>
</html>
