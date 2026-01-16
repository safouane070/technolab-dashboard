<?php
try {
    $db = new PDO("mysql:host=localhost;dbname=technolab-dashboard", "root", "");
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Fout!: " . $e->getMessage());
}

// Ophalen werknemer via GET id
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("Geen geldig ID opgegeven.");
}
$id = intval($_GET['id']);

// Ophalen van werknemer uit de employee tabel
$stmt = $db->prepare("SELECT * FROM employee WHERE id = :id");
$stmt->execute([':id' => $id]);
$werknemer = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$werknemer) {
    die("Werknemer niet gevonden.");
}

// Haal bestaande sectoren op uit JSON-bestand
$sectorenJsonFile = __DIR__ . '/sectoren.json';
$sectoren = [];
if (file_exists($sectorenJsonFile)) {
    $sectoren = json_decode(file_get_contents($sectorenJsonFile), true);
    if (!is_array($sectoren)) $sectoren = [];
}

// ✅ Update uitvoeren bij POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $voornaam = $_POST['voornaam'];
    $tussenvoegsel = $_POST['tussenvoegsel'];
    $achternaam = $_POST['achternaam'];
    $email = $_POST['email'];

    // Nieuwe sector invoer afhandelen
    if (isset($_POST['sector_nieuw']) && trim($_POST['sector_nieuw']) !== '') {
        $sector = trim($_POST['sector_nieuw']);
        if (!in_array($sector, $sectoren)) {
            $sectoren[] = $sector;
            file_put_contents($sectorenJsonFile, json_encode($sectoren, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        }
    } else {
        $sector = $_POST['sector'];
    }

    $BHV = isset($_POST['BHV']) ? 1 : 0;

    // Werkdagen
    $workday_mon = isset($_POST['workday_mon']) ? 1 : 0;
    $workday_tue = isset($_POST['workday_tue']) ? 1 : 0;
    $workday_wed = isset($_POST['workday_wed']) ? 1 : 0;
    $workday_thu = isset($_POST['workday_thu']) ? 1 : 0;
    $workday_fri = isset($_POST['workday_fri']) ? 1 : 0;

    // Update werknemer in database
    $stmt = $db->prepare("UPDATE employee SET 
        name = :name,
        middle_name = :middle_name,
        last_name = :last_name,
        email = :email,
        sector = :sector,
        bhv = :BHV,
        workday_mon = :mon,
        workday_tue = :tue,
        workday_wed = :wed,
        workday_thu = :thu,
        workday_fri = :fri
        WHERE id = :id");

    $stmt->execute([
        ':name' => $voornaam,
        ':middle_name' => $tussenvoegsel,
        ':last_name' => $achternaam,
        ':email' => $email,
        ':sector' => $sector,
        ':BHV' => $BHV,
        ':mon' => $workday_mon,
        ':tue' => $workday_tue,
        ':wed' => $workday_wed,
        ':thu' => $workday_thu,
        ':fri' => $workday_fri,
        ':id' => $id
    ]);

    // Redirect naar detailpagina van werknemer
    header("Location: details.php?id=$id");
    exit;
}
?>

<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <title>Werknemer bewerken - <?= htmlspecialchars($werknemer['name'] . ' ' . $werknemer['last_name']) ?></title>
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<div class="update-card shadow-lg p-4 rounded-4 bg-white">
    <h2>Bewerk <?= htmlspecialchars($werknemer['name'] . ' ' . $werknemer['last_name']) ?></h2>

    <form method="post" class="update-form">
        <div class="row g-3 mb-3">
            <div class="col-md-4">
                <label class="form-label">Voornaam</label>
                <input type="text" name="voornaam" class="form-control" value="<?= htmlspecialchars($werknemer['name']) ?>" required>
            </div>
            <div class="col-md-4">
                <label class="form-label">Tussenvoegsel</label>
                <input type="text" name="tussenvoegsel" class="form-control" value="<?= htmlspecialchars($werknemer['middle_name']) ?>">
            </div>
            <div class="col-md-4">
                <label class="form-label">Achternaam</label>
                <input type="text" name="achternaam" class="form-control" value="<?= htmlspecialchars($werknemer['last_name']) ?>" required>
            </div>
            <div class="col-12 col-md-6">
                <label class="form-label">Email</label>
                <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($werknemer['email']) ?>" required>
            </div>
            <div class="col-12 col-md-6">
                <label class="form-label">Sector</label>
                <select name="sector" id="sectorSelect" class="form-select">
                    <?php
                    $huidige = $werknemer['sector'] ?? '';
                    $huidige_in_lijst = in_array($huidige, $sectoren);
                    if ($huidige !== '' && !$huidige_in_lijst): ?>
                        <option value="<?= htmlspecialchars($huidige) ?>" selected><?= htmlspecialchars($huidige) ?> (huidig)</option>
                    <?php endif; ?>
                    <?php foreach ($sectoren as $s): ?>
                        <option value="<?= htmlspecialchars($s) ?>" <?= $s === $huidige ? 'selected' : '' ?>><?= htmlspecialchars($s) ?></option>
                    <?php endforeach; ?>
                    <option value="__andere__">Andere...</option>
                </select>
                <input type="text" name="sector_nieuw" id="sectorNieuw" class="form-control mt-2" placeholder="Nieuwe sector" style="display:none;">
            </div>
        </div>

        <div class="mb-3 form-check form-switch">
            <input type="checkbox" name="BHV" class="form-check-input" id="BHV" <?= $werknemer['bhv'] ? 'checked' : '' ?>>
            <label class="form-check-label" for="BHV">BHV</label>
        </div>

        <h5>Werkdagen</h5>
        <div class="row mb-3">
            <?php
            $days = [
                'mon' => 'Maandag',
                'tue' => 'Dinsdag',
                'wed' => 'Woensdag',
                'thu' => 'Donderdag',
                'fri' => 'Vrijdag'
            ];
            foreach ($days as $key => $label):
                $checked = !empty($werknemer["workday_$key"]) ? 'checked' : '';
                ?>
                <div class="col-6 col-md-2 form-check">
                    <input type="checkbox" name="workday_<?= $key ?>" class="form-check-input" id="workday_<?= $key ?>" <?= $checked ?>>
                    <label class="form-check-label" for="workday_<?= $key ?>"><?= $label ?></label>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="d-flex justify-content-between">
            <a href="dagplanning.php" class="btn btn-outline-secondary">Terug</a>
            <button type="submit" class="btn btn-success">Opslaan</button>
        </div>
    </form>
</div>

<script>
    const select = document.getElementById('sectorSelect');
    const nieuwInput = document.getElementById('sectorNieuw');

    function toggleNieuw() {
        if (select.value === '__andere__') {
            nieuwInput.style.display = 'block';
            nieuwInput.focus();
        } else {
            nieuwInput.style.display = 'none';
            nieuwInput.value = '';
        }
    }

    toggleNieuw();
    select.addEventListener('change', toggleNieuw);
</script>

</body>
</html>
