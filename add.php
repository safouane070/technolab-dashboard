<?php
// Verbinding maken
try {
    $db = new PDO("mysql:host=localhost;dbname=technolab-dashboard", "root", "");
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Fout!: " . $e->getMessage());
}

// Verwerking formulier
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $voornaam = $_POST['voornaam'];
    $tussenvoegsel = $_POST['tussenvoegsel'] ?? null;
    $achternaam = $_POST['achternaam'];
    $email = $_POST['email'];
    $sector = $_POST['sector'];
    $bhv = isset($_POST['bhv']) ? 1 : 0;

    $werkdag_ma = isset($_POST['werkdag_ma']) ? 1 : 0;
    $werkdag_di = isset($_POST['werkdag_di']) ? 1 : 0;
    $werkdag_wo = isset($_POST['werkdag_wo']) ? 1 : 0;
    $werkdag_do = isset($_POST['werkdag_do']) ? 1 : 0;
    $werkdag_vr = isset($_POST['werkdag_vr']) ? 1 : 0;

    $status = ($werkdag_ma || $werkdag_di || $werkdag_wo || $werkdag_do || $werkdag_vr) ? "Aanwezig" : "Afwezig";

    $stmt = $db->prepare("INSERT INTO werknemers 
        (voornaam, tussenvoegsel, achternaam, email, werkdag_ma, werkdag_di, werkdag_wo, werkdag_do, werkdag_vr, sector, BHV, status) 
        VALUES (:voornaam, :tussenvoegsel, :achternaam, :email, :werkdag_ma, :werkdag_di, :werkdag_wo, :werkdag_do, :werkdag_vr, :sector, :bhv, :status)");

    $stmt->execute([
        ':voornaam' => $voornaam,
        ':tussenvoegsel' => $tussenvoegsel,
        ':achternaam' => $achternaam,
        ':email' => $email,
        ':werkdag_ma' => $werkdag_ma,
        ':werkdag_di' => $werkdag_di,
        ':werkdag_wo' => $werkdag_wo,
        ':werkdag_do' => $werkdag_do,
        ':werkdag_vr' => $werkdag_vr,
        ':sector' => $sector,
        ':bhv' => $bhv,
        ':status' => $status
    ]);

    header("Location: dagplanning.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nieuwe medewerker toevoegen</title>
    <link rel="stylesheet" href="css/add.css">
</head>
<body>
<div class="container">
    <h2>Nieuwe medewerker toevoegen</h2>
    <form method="post">
        <label>Voornaam:</label>
        <input type="text" name="voornaam" required>

        <label>Tussenvoegsel:</label>
        <input type="text" name="tussenvoegsel">

        <label>Achternaam:</label>
        <input type="text" name="achternaam" required>

        <label>Email:</label>
        <input type="email" name="email" required>

        <label>Werkdagen:</label>
        <div class="checkbox-group">
            <label><input type="checkbox" name="werkdag_ma"> Maandag</label>
            <label><input type="checkbox" name="werkdag_di"> Dinsdag</label>
            <label><input type="checkbox" name="werkdag_wo"> Woensdag</label>
            <label><input type="checkbox" name="werkdag_do"> Donderdag</label>
            <label><input type="checkbox" name="werkdag_vr"> Vrijdag</label>
        </div>

        <label>Sector:</label>
        <select name="sector" required>
            <option value="Techniek">Techniek</option>
            <option value="ICT">ICT</option>
            <option value="Onderwijs">Onderwijs</option>
            <option value="Administratie">Administratie</option>
        </select>

        <label><input type="checkbox" name="bhv"> Heeft BHV</label>

        <button type="submit">Opslaan</button>
    </form>
    <a href="dagplanning.php" class="back-link">⬅ Terug naar lijst</a>
</div>
</body>
</html>
