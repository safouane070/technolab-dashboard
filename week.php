<?php
try {
    $db = new PDO("mysql:host=localhost;dbname=technolab-dashboard", "root", "");
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Fout!: " . $e->getMessage());
}


$stmt = $db->query("SELECT * FROM werknemers ORDER BY achternaam ASC, voornaam ASC");
$werknemers = $stmt->fetchAll(PDO::FETCH_ASSOC);


$today = new DateTime();
$weekStart = (clone $today)->modify('monday this week');
$weekDays = [];
for ($i = 0; $i < 7; $i++) {
    $day = (clone $weekStart)->modify("+$i day");
    $weekDays[] = $day;
}

function statusClass($status) {
    switch ($status) {
        case 'Aanwezig': return 'green';
        case 'Afwezig': return 'red';
        case 'Ziek': return 'yellow';
        case 'Op de school': return 'blue';
        case 'Eefetjes Afwezig': return 'orange';
        default: return '';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Absence Tracker</title>


  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet" />
  <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet" />


  <link rel="stylesheet" href="css/style.css" />
</head>
<style>
    .cell { width: 50px; height: 50px; border-radius: 50%; margin: auto; }
    .green { background: #00e604; }
    .red { background: #f8001f; }
    .yellow { background: #ffcf11; }
    .blue { background: #bbdefb; }
    .orange { background: #e68a00; }
    .weekend { background: #f5f5f5; }
</style>
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
      <div class="header-right">
              </div>
    </header>

    <!-- Main -->
    <main class="main">
      <div class="main-header">
        <h1>Absence List</h1>
        <div class="legend">
          <div><span class="dot green"></span> Present</div>
          <div><span class="dot red"></span> Absent</div>
          <div><span class="dot yellow"></span> Planned</div>
          <button class="primary-btn">
            <span class="material-symbols-outlined">add</span> Add Absence
          </button>
        </div>
      </div>

      <!-- Filters -->
      <div class="filters">
        <div class="filter-left">
          <div class="date-picker">
            <span class="material-symbols-outlined">calendar_today</span>
            <input type="text" placeholder="Select Dates" />
          </div>
          <div class="range-buttons">
              <a href="dagplanning.php"><button class="toggle">Today</button></a>
            <button class="active">Week</button>
            <button>Month</button>
          </div>
          <button class="reset-btn">
            <span class="material-symbols-outlined">refresh</span> Reset
          </button>
        </div>
        <div class="filter-right">
          <button class="icon-button"><span class="material-symbols-outlined">chevron_left</span></button>
          <span class="date-label">October 21-27, 2024</span>
          <button class="icon-button"><span class="material-symbols-outlined">chevron_right</span></button>
        </div>
      </div>

      <!-- Table -->
      <div class="table-wrapper">
        <table>
            <thead>
            <tr>
                <th>Werknemer</th>
                <?php foreach ($weekDays as $day): ?>
                    <th <?= in_array($day->format('N'), [6,7]) ? 'class="weekend"' : '' ?>>
                        <?= $day->format('D') ?><br><?= $day->format('d M') ?>
                    </th>
                <?php endforeach; ?>
            </tr>
            </thead>
          <tbody>
          <?php foreach ($werknemers as $w): ?>
              <tr>
                  <td><?= ($w['voornaam'] . ' ' . ($w['tussenvoegsel'] ? $w['tussenvoegsel'].' ' : '') . $w['achternaam']) ?></td>
                  <?php foreach ($weekDays as $day): ?>
                      <?php
                      $dayName = strtolower($day->format('D'));
                      $status = 'Afwezig';


                      if (
                          ($dayName == 'mon' && $w['werkdag_ma']) ||
                          ($dayName == 'tue' && $w['werkdag_di']) ||
                          ($dayName == 'wed' && $w['werkdag_wo']) ||
                          ($dayName == 'thu' && $w['werkdag_do']) ||
                          ($dayName == 'fri' && $w['werkdag_vr'])
                      ) {
                          $status = $w['status'];
                      }
                      ?>
                      <td>
                          <div class="cell <?= statusClass($status) ?>"></div>
                      </td>
                  <?php endforeach; ?>
              </tr>
          <?php endforeach; ?>
          </tbody>
        </table>
      </div>

    </main>
  </div>
</body>
</html>
