<?php
try {
    $db = new PDO("mysql:host=localhost;dbname=technolab-dashboard", "root", "");
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Fout!: " . $e->getMessage());
}



if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $stmt = $db->prepare("DELETE FROM werknemers WHERE id = :id");
    $stmt->execute([':id' => $id]);
}

$stmt = $db->query("SELECT id, voornaam, tussenvoegsel, achternaam, BHV, duur_afwezig
                    FROM werknemers 
                    ORDER BY achternaam ASC, voornaam ASC");
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
        <a href="dagplanning.php" >Dashboard</a>
        <a href="employees.php" class="active" >Employees</a>
        <a href="week.php">Week</a>
        <a href="#">Reports</a>
    </nav>

</header>

<main class="main">
    <div class="page-header"></div>



    <div class="table-container">
        <table>
            <thead>
            <tr>
                <th>Naam</th>

                <th>Acties</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach($werknemers as $w):

                ?>
                <tr >
                    <td><?= ($w['voornaam'].' '.($w['tussenvoegsel']?$w['tussenvoegsel'].' ':'').$w['achternaam']) ?>   <span class="bhv <?= $w['BHV'] ? 'bhv-BHV' : 'bhv-BHV' ?>">
                        <?= $w['BHV'] ? '  <img src="image/BHV.png" alt="Technolab Logo" class="logo-icon">' : '' ?>
                            </span></td>


                    <td class="action-icons">
                        <button class="btn-action btn-details" data-id="<?= $w['id'] ?>"><i class="bi bi-pc-display-horizontal"></i></button>
                        <a href="?delete=<?= $w['id'] ?>" class="btn-action btn-delete" onclick="return confirm('Weet je zeker?');"><i class="bi bi-trash3"></i></a>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
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
</main>
</div>


<script src="js/details.js"></script>
<script src="js/dagplanning.js"></script>

</body>
</html>


