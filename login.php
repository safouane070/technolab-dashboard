<?php
session_start();

// Hardcoded pincode (voorbeeld)
$admin_pincode = "1234"; // LET OP: gebruik gehashte pincode in productie!

$error = "";

// Login verwerken
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $pincode = $_POST['pincode'] ?? '';

    if ($pincode === $admin_pincode) {
        $_SESSION['admin_logged_in'] = true;
        header("Location: dagplanning.php");
        exit;
    } else {
        $error = "Ongeldige pincode.";
    }
}
?>

<!DOCTYPE html>
<html lang="nl">
<head>
  <meta charset="utf-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Pincode Login - Technolab Dashboard</title>

  <!-- Tailwind CSS -->
  <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>

  <!-- Fonts -->
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet"/>
  <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet"/>

  <!-- Custom CSS -->
  <link rel="stylesheet" href="styles.css"/>

  <!-- Tailwind Config -->
  <script id="tailwind-config">
    tailwind.config = {
      darkMode: "class",
      theme: {
        extend: {
          colors: {
            primary: "#94C01F",
            secondary: "#44205F",
            "background-light": "#F7F9F4",
            "background-dark": "#120B18",
          },
          fontFamily: {
            display: ["Inter", "sans-serif"],
          },
          borderRadius: {
            DEFAULT: "0.5rem",
            lg: "1rem",
            xl: "1.5rem",
            full: "9999px",
          },
        },
      },
    };
  </script>
</head>

<body class="font-display bg-background-light dark:bg-background-dark text-gray-900 dark:text-white">

<section class="relative flex min-h-screen w-full flex-col overflow-hidden items-center justify-center">
  <!-- Achtergrond effecten -->
  <div class="absolute inset-0 z-0">
    <div class="absolute -left-1/4 -top-1/4 h-1/2 w-1/2 rounded-full bg-primary/20 blur-3xl dark:bg-primary/10"></div>
    <div class="absolute -bottom-1/4 -right-1/4 h-1/2 w-1/2 rounded-full bg-secondary/20 blur-3xl dark:bg-secondary/10"></div>
  </div>

  <!-- Login Container -->
  <div class="relative z-10 flex flex-col items-center justify-center w-full max-w-lg bg-white/60 backdrop-blur-xl rounded-xl shadow-2xl p-10 sm:p-14">

    <!-- Logo -->
    <header class="flex flex-col items-center gap-3 mb-8">
      <img src="image/technolab.png" alt="Technolab Logo" class="h-32 w-32 object-contain"/>
      <h1 class="text-2xl font-bold text-secondary dark:text-white">Technolab Dashboard</h1>
    </header>

    <!-- Formulier -->
    <main class="w-full">
      <form method="POST" class="flex flex-col gap-5">
        <div class="flex flex-col gap-1.5">
          <label for="pincode" class="text-sm font-medium text-gray-700 dark:text-gray-300">Pincode</label>
          <div class="relative flex w-full items-center">
            <input
              name="pincode"
              id="pincode"
              type="password"
              inputmode="numeric"
              pattern="[0-9]*"
              maxlength="6"
              placeholder="Voer uw pincode in"
              required
              class="form-input w-full rounded-lg border border-gray-300 bg-gray-50 px-4 py-3 text-gray-800 placeholder-gray-400 focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/20 dark:border-gray-700 dark:bg-gray-800 dark:text-white dark:placeholder-gray-500"
            />
            <button type="button" aria-label="Toon pincode" class="absolute right-4 text-gray-500 dark:text-gray-400" onclick="togglePincode()">
              <span class="material-symbols-outlined text-xl" id="toggleIcon">visibility_off</span>
            </button>
          </div>
        </div>

        <?php if ($error): ?>
          <p class="text-red-500 text-sm text-center"><?php echo $error; ?></p>
        <?php endif; ?>

        <button
          type="submit"
          class="flex h-12 w-full items-center justify-center rounded-lg bg-secondary text-base font-semibold text-white transition-all hover:bg-secondary/90 focus:outline-none focus:ring-2 focus:ring-secondary focus:ring-offset-2 dark:bg-primary dark:text-secondary dark:hover:bg-primary/90 dark:focus:ring-offset-background-dark">
          Inloggen
        </button>
      </form>
    </main>

    <!-- Footer -->
    <footer class="text-center text-sm text-gray-500 dark:text-gray-400 mt-8">
      <p>© 2024 Technolab. Alle rechten voorbehouden.</p>
    </footer>

  </div>
</section>

<!-- JavaScript voor tonen/verbergen pincode -->
<script>
function togglePincode() {
  const input = document.getElementById('pincode');
  const icon = document.getElementById('toggleIcon');
  if (input.type === 'password') {
    input.type = 'text';
    icon.textContent = 'visibility';
  } else {
    input.type = 'password';
    icon.textContent = 'visibility_off';
  }
}
</script>

</body>
</html>
