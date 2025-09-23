<?php
try {
    $db = new PDO("mysql:host=localhost;dbname=technolab-dashboard", "root", "");
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Fout!: " . $e->getMessage());
}

// Status bijwerken voor een specifieke dag
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

// Werknemers ophalen
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
    'Ma' => ['col' => 'status_ma', 'date' => clone $startOfWeek],
    'Di' => ['col' => 'status_di', 'date' => (clone $startOfWeek)->modify('+1 day')],
    'Wo' => ['col' => 'status_wo', 'date' => (clone $startOfWeek)->modify('+2 day')],
    'Do' => ['col' => 'status_do', 'date' => (clone $startOfWeek)->modify('+3 day')],
    'Vr' => ['col' => 'status_vr', 'date' => (clone $startOfWeek)->modify('+4 day')],
];
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
    .status-dot {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        display: inline-block;
        cursor: pointer;
    }

    .status-aanwezig { background-color: #4caf50; }   /* groen */
    .status-afwezig { background-color: #f44336; }    /* rood */
    .status-ziek { background-color: #ffeb3b; }       /* geel */
    .status-opdeschool { background-color: #2196f3; } /* blauw */
    .status-eefetjesafwezig { background-color: #ff9800; } /* oranje */

    .status-select {
        display: none;
        margin-top: 5px;
    }
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
                      <td><?= htmlspecialchars($w['voornaam'].' '.($w['tussenvoegsel'] ? $w['tussenvoegsel'].' ' : '').$w['achternaam']) ?></td>
                      <?php foreach ($weekDays as $dayName => $info): ?>
                          <?php
                          $col = $info['col'];
                          $status = $w[$col];
                          $class = 'status-'.strtolower(str_replace(' ', '', $status));
                          ?>
                          <td>
                              <!-- Ronde bolletje -->
                              <div class="status-dot <?= $class ?>" onclick="toggleSelect(this)"></div>

                              <!-- Dropdown (verstopt) -->
                              <form method="post" action="">
                                  <select class="status-select" name="status" onchange="this.form.submit()">
                                      <option value="Aanwezig" <?= $status=='Aanwezig' ? 'selected' : '' ?>>Aanwezig</option>
                                      <option value="Afwezig" <?= $status=='Afwezig' ? 'selected' : '' ?>>Afwezig</option>
                                      <option value="Ziek" <?= $status=='Ziek' ? 'selected' : '' ?>>Ziek</option>
                                      <option value="Op de school" <?= $status=='Op de school' ? 'selected' : '' ?>>Op de school</option>
                                      <option value="Eefetjes Afwezig" <?= $status=='Eefetjes Afwezig' ? 'selected' : '' ?>>Eefetjes Afwezig</option>
                                  </select>
                                  <input type="hidden" name="id" value="<?= $w['id'] ?>">
                                  <input type="hidden" name="dag" value="<?= $col ?>">
                              </form>
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
      function toggleSelect(dot) {
          const select = dot.nextElementSibling.querySelector("select");
          select.style.display = (select.style.display === "block") ? "none" : "block";
      }
  </script>
</body>
</html>
