<?php

// Verbinding maken
try {
    $db = new PDO("mysql:host=localhost;dbname=technolab-dashboard", "root", "");
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Fout!: " . $e->getMessage());
}

// Status bijwerken
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'], $_POST['status'])) {
    $stmt = $db->prepare("UPDATE werknemers SET status = :status WHERE id = :id");
    $stmt->execute([
        ':status' => $_POST['status'],
        ':id' => intval($_POST['id'])
    ]);
    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}

// Verwijderen
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $stmt = $db->prepare("DELETE FROM werknemers WHERE id = :id");
    $stmt->execute([':id' => $id]);
    echo "<p style='color:red;'>Werknemer is verwijderd.</p>";
}

// Ophalen
$stmt = $db->query("SELECT id, voornaam, tussenvoegsel, achternaam, status FROM werknemers");
$werknemers = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Absence Tracker</title>

  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet"/>
  <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet"/>
  <link rel="stylesheet" href="css/dagplanning.css"/>
</head>
<style>


.status-aanwezig { background-color: #c8e6c9; }
.status-afwezig  { background-color: #ffcdd2; }
.status-ziek     { background-color: #fff9c4; }
.status-opdeschool { background-color: #bbdefb; }
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
        <a href="#">Dashboard</a>
        <a href="#">Employees</a>
        <a href="#" class="active">Absences</a>
        <a href="#">Reports</a>
      </nav>

      <div class="user-actions">
        <button class="icon-btn">
          <span class="material-symbols-outlined">notifications</span>
        </button>
        <img class="avatar" src="https://lh3.googleusercontent.com/aida-public/AB6AXuCmQf2vygWEjWviZ9ns2f4aSSvopGZE_WGG6LFfdymZs3GNRL9bGxgSTl4D0SgC1mfhCkYEnaSbaEoznHIUlfvzCcPhwjdPCqO5DEcYVICNYLP6daKQecjcpnR7yAqkLR-QSsAWdvJ51xF37rhgul5qEpD3fk2EqfjGj4aSd6RZGr63JJv4T3iaZw2oI0NfONTWxexvOns6EHeM8FR7hew_h3axBllD6hKj38EOugX_OlXH54YeJhfbmFiOInJ_5HWIb2id8Ho0SNA" alt="User avatar">
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
            <button class="toggle">Week</button>
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
                                <select name="status" onchange="this.form.submit()">
                                    <option value="Aanwezig"   <?= $w['status']=='Aanwezig' ? 'selected' : '' ?>>Aanwezig</option>
                                    <option value="Afwezig"    <?= $w['status']=='Afwezig' ? 'selected' : '' ?>>Afwezig</option>
                                    <option value="Ziek"       <?= $w['status']=='Ziek' ? 'selected' : '' ?>>Ziek</option>
                                    <option value="Op de school" <?= $w['status']=='Op de school' ? 'selected' : '' ?>>Op de school</option>
                                </select>
                                <input type="hidden" name="id" value="<?= $w['id'] ?>">
                            </form>
                        </td>
                        <td>
                            <a href="?delete=<?= $w['id'] ?>" onclick="return confirm('Weet je zeker dat je deze werknemer wilt verwijderen?');">Verwijder</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr><td colspan="4">Geen werknemers gevonden</td></tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </main>
  </div>
</body>
</html>
