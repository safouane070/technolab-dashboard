<?php
try {
    $db = new PDO("mysql:host=localhost;dbname=technolab-dashboard", "root", "");
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Fout!: " . $e->getMessage());
}

// Ophalen van de werknemer via GET id
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("Geen geldig ID opgegeven.");
}
$id = intval($_GET['id']);

$stmt = $db->prepare("SELECT * FROM werknemers WHERE id = :id");
$stmt->execute([':id' => $id]);
$werknemer = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$werknemer) {
    die("Werknemer niet gevonden.");
}

// ✅ Update uitvoeren bij POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $voornaam = $_POST['voornaam'];
    $tussenvoegsel = $_POST['tussenvoegsel'];
    $achternaam = $_POST['achternaam'];
    $email = $_POST['email'];
    $sector = $_POST['sector'];
    $BHV = isset($_POST['BHV']) ? 1 : 0;

    // Werkdagen
    $werkdag_ma = isset($_POST['werkdag_ma']) ? 1 : 0;
    $werkdag_di = isset($_POST['werkdag_di']) ? 1 : 0;
    $werkdag_wo = isset($_POST['werkdag_wo']) ? 1 : 0;
    $werkdag_do = isset($_POST['werkdag_do']) ? 1 : 0;
    $werkdag_vr = isset($_POST['werkdag_vr']) ? 1 : 0;

    $stmt = $db->prepare("UPDATE werknemers SET 
        voornaam = :voornaam,
        tussenvoegsel = :tussenvoegsel,
        achternaam = :achternaam,
        email = :email,
        sector = :sector,
        BHV = :BHV,
        werkdag_ma = :ma,
        werkdag_di = :di,
        werkdag_wo = :wo,
        werkdag_do = :do,
        werkdag_vr = :vr
        WHERE id = :id");

    $stmt->execute([
        ':voornaam' => $voornaam,
        ':tussenvoegsel' => $tussenvoegsel,
        ':achternaam' => $achternaam,
        ':email' => $email,
        ':sector' => $sector,
        ':BHV' => $BHV,
        ':ma' => $werkdag_ma,
        ':di' => $werkdag_di,
        ':wo' => $werkdag_wo,
        ':do' => $werkdag_do,
        ':vr' => $werkdag_vr,
        ':id' => $id
    ]);

    header("Location: dagplanning.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <title>Update werknemer</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container py-4">

<h2>Update werknemer</h2>
<form method="post">
    <div class="mb-3">
        <label>Voornaam</label>
        <input type="text" name="voornaam" class="form-control" value="<?= ($werknemer['voornaam']) ?>" required>
    </div>
    <div class="mb-3">
        <label>Tussenvoegsel</label>
        <input type="text" name="tussenvoegsel" class="form-control" value="<?= ($werknemer['tussenvoegsel']) ?>">
    </div>
    <div class="mb-3">
        <label>Achternaam</label>
        <input type="text" name="achternaam" class="form-control" value="<?= ($werknemer['achternaam']) ?>" required>
    </div>
    <div class="mb-3">
        <label>Email</label>
        <input type="email" name="email" class="form-control" value="<?= ($werknemer['email']) ?>" required>
    </div>
    <div class="mb-3">
        <label>Sector</label>
        <input type="text" name="sector" class="form-control" value="<?= ($werknemer['sector']) ?>" required>
    </div>
    <div class="form-check mb-3">
        <input type="checkbox" name="BHV" class="form-check-input" id="BHV" <?= $werknemer['BHV'] ? 'checked' : '' ?>>
        <label class="form-check-label" for="BHV">BHV</label>
    </div>

    <h4>Werkdagen</h4>
    <?php
    $days = ['ma' => 'Maandag', 'di' => 'Dinsdag', 'wo' => 'Woensdag', 'do' => 'Donderdag', 'vr' => 'Vrijdag'];
    foreach ($days as $key => $label):
        ?>
        <div class="form-check">
            <input type="checkbox" name="werkdag_<?= $key ?>" class="form-check-input" id="werkdag_<?= $key ?>"
                <?= $werknemer["werkdag_$key"] ? 'checked' : '' ?>>
            <label class="form-check-label" for="werkdag_<?= $key ?>"><?= $label ?></label>
        </div>
    <?php endforeach; ?>

    <br>
    <button type="submit" class="btn btn-success">Opslaan</button>
    <a href="dagplanning.php" class="btn btn-secondary">Terug</a>
</form>

</body>
</html>
