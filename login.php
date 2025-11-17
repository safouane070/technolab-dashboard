<?php
session_start();

// Verbinding met database
try {
    $db = new PDO("mysql:host=localhost;dbname=technolab-dashboard", "root", "");
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Databasefout: " . $e->getMessage());
}

$error = "";

// Login verwerken
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $password = $_POST['pincode'] ?? '';

    // Case-insensitive vergelijking
    $stmt = $db->prepare("SELECT COUNT(*) FROM admins WHERE LOWER(password) = LOWER(:pw)");
    $stmt->execute([':pw' => $password]);
    $exists = $stmt->fetchColumn();

    if ($exists > 0) {
        $_SESSION['admin_logged_in'] = true;
        header("Location: dagplanning.php");
        exit;
    } else {
        $error = "Ongeldig wachtwoord.";
    }
}
?>

<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Admin Login - Technolab Dashboard</title>

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet"/>

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
                    fontFamily: { display: ["Inter", "sans-serif"] },
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
    <div class="relative z-10 flex flex-col items-center justify-center
              w-[92%] sm:w-[440px] md:w-[500px] lg:w-[540px] xl:w-[560px]
              bg-white/60 backdrop-blur-xl rounded-3xl shadow-2xl
              p-10 sm:p-12 md:p-14 lg:p-16 xl:p-20
              min-h-[480px] sm:min-h-[520px] md:min-h-[560px] lg:min-h-[600px] xl:min-h-[640px]
              transition-all duration-300">

        <!-- Logo -->
        <header class="flex flex-col items-center gap-3 mb-8">
            <img src="image/technolab.png" alt="Technolab Logo"
                 class="h-36 w-36 sm:h-40 sm:w-40 md:h-44 md:w-44 lg:h-48 lg:w-48 object-contain transition-all duration-300"/>
        </header>

        <!-- Formulier -->
        <main class="w-full">
            <form method="POST" class="flex flex-col gap-6">
                <div class="flex flex-col gap-1.5">
                    <label for="pincode" class="text-sm font-medium text-gray-700 dark:text-gray-300">Wachtwoord</label>
                    <div class="relative flex w-full items-center">
                        <input
                                name="pincode"
                                id="pincode"
                                type="password"
                                placeholder="Voer uw wachtwoord in"
                                required
                                class="form-input w-full rounded-2xl border border-gray-300 bg-gray-50 px-4 py-3 md:py-3.5 text-gray-800 placeholder-gray-400 focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/20 dark:border-gray-700 dark:bg-gray-800 dark:text-white dark:placeholder-gray-500 text-base sm:text-lg md:text-xl"
                        />
                        <button type="button" aria-label="Toon wachtwoord" class="absolute right-4 text-gray-500 dark:text-gray-400" onclick="togglePincode()">
                            <span class="material-symbols-outlined text-xl md:text-2xl" id="toggleIcon">visibility_off</span>
                        </button>
                    </div>
                </div>

                <?php if ($error): ?>
                    <p class="text-red-500 text-sm sm:text-base text-center"><?php echo ($error); ?></p>
                <?php endif; ?>

                <button
                        type="submit"
                        class="flex h-12 sm:h-13 md:h-14 w-full items-center justify-center rounded-2xl bg-secondary text-base sm:text-lg md:text-xl font-semibold text-white transition-all hover:bg-secondary/90 focus:outline-none focus:ring-2 focus:ring-secondary focus:ring-offset-2 dark:bg-primary dark:text-secondary dark:hover:bg-primary/90 dark:focus:ring-offset-background-dark">
                    Inloggen
                </button>
            </form>
        </main>

        <!-- Footer -->
        <footer class="text-center text-sm text-gray-500 dark:text-gray-400 mt-10">
            <p> 2025 Technolab. </p>
        </footer>
    </div>
</section>

<!-- JS voor tonen/verbergen wachtwoord -->
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
