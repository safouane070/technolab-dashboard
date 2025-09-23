<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>

  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">

  <!-- Bootstrap Icons (correcte versie) -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">

  <!-- Google Fonts -->
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet"/>
  <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet"/>

  <!-- Custom CSS -->
  <link rel="stylesheet" href="css/nav.css"/>

  <title>Absence Tracker</title>
</head>
<body>
  <main class="app">
    <!-- Header -->
    <header class="header">
      
      <!-- Logo -->
      <section class="logo-container">
        <a href="#" class="logo-link">
          <img src="image/technolab.png" alt="Technolab Logo" class="logo-icon">
        </a>
      </section>

      <!-- Navigatie -->
      <nav class="nav" aria-label="Main Navigation">
        <a href="dagplanning.php" class="active">Dashboard</a>
        <a href="#">Employees</a>
        <a href="absent.php">Absences</a>
        <a href="#">Reports</a>
      </nav>

      <!-- User actions -->
      <section class="user-actions">
        <button class="icon-btn" aria-label="User menu">
          <span class="material-symbols-outlined">account_circle</span>
        </button>
      </section>
    </header>
  </main>


  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
