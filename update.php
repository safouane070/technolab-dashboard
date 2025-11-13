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

$stmt = $db->prepare("SELECT * FROM werknemers WHERE id = :id");
$stmt->execute([':id' => $id]);
$werknemer = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$werknemer) {
    die("Werknemer niet gevonden.");
}

// Haal bestaande sectoren op uit JSON-bestand of database (afhankelijk van implementatie)
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

        // Voeg nieuwe sector toe aan JSON als die nog niet bestaat
        if (!in_array($sector, $sectoren)) {
            $sectoren[] = $sector;
            file_put_contents($sectorenJsonFile, json_encode($sectoren, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        }
    } else {
        $sector = $_POST['sector'];
    }

    $BHV = isset($_POST['BHV']) ? 1 : 0;

    // Werkdagen
    $werkdag_ma = isset($_POST['werkdag_ma']) ? 1 : 0;
    $werkdag_di = isset($_POST['werkdag_di']) ? 1 : 0;
    $werkdag_wo = isset($_POST['werkdag_wo']) ? 1 : 0;
    $werkdag_do = isset($_POST['werkdag_do']) ? 1 : 0;
    $werkdag_vr = isset($_POST['werkdag_vr']) ? 1 : 0;

    // Update werknemer in database
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
    <title>Werknemer bewerken - <?= htmlspecialchars($werknemer['voornaam'] . ' ' . $werknemer['achternaam']) ?></title>
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg,#f4f7fb,#eef6f9);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
        }
        .update-card {
            width: 100%;
            max-width: 720px;
        }
        .form-section {
            background: #fbfdff;
            padding: 1rem;
            border-radius: 12px;
            box-shadow: 0 6px 18px rgba(20,40,90,0.06);
            margin-bottom: 1rem;
        }
        .days-grid {
            display: grid;
            grid-template-columns: repeat(3, minmax(120px,1fr));
            gap: .5rem;
        }
        @media (max-width: 576px) {
            .days-grid { grid-template-columns: repeat(2,1fr); }
        }
        .small-muted { font-size: .85rem; color: #6c757d; }
        .brand {
            font-weight: 700;
            color: #0b5ed7;
            letter-spacing: .2px;
        }
    </style>
</head>
<body>

<div class="update-card shadow-lg p-4 rounded-4 bg-white">
    <div class="d-flex align-items-center mb-3">
        <div class="me-3">
            <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" fill="#0b5ed7" class="bi bi-person-circle" viewBox="0 0 16 16">
              <path d="M13 8a5 5 0 1 0-10 0 5 5 0 0 0 10 0z"/>
              <path fill-rule="evenodd" d="M8 15A7 7 0 1 0 8 1a7 7 0 0 0 0 14zm0-9a3 3 0 1 1 0 6 3 3 0 0 1 0-6z"/>
            </svg>
        </div>
        <div>
            <!-- Hier tonen we de volledige naam van de werknemer als titel -->
            <h2 class="mb-0"><?= ($werknemer['voornaam'] . ' ' . $werknemer['achternaam']) ?></h2>
            <div class="small-muted">Bewerk de gegevens</div>
        </div>
    </div>

    <form method="post" class="update-form">
        <div class="row g-3 form-section">
            <div class="col-md-4">
                <label class="form-label">Voornaam</label>
                <input type="text" name="voornaam" class="form-control" value="<?= ($werknemer['voornaam']) ?>" required>
            </div>
            <div class="col-md-4">
                <label class="form-label">Tussenvoegsel</label>
                <input type="text" name="tussenvoegsel" class="form-control" value="<?= ($werknemer['tussenvoegsel']) ?>">
            </div>
            <div class="col-md-4">
                <label class="form-label">Achternaam</label>
                <input type="text" name="achternaam" class="form-control" value="<?= ($werknemer['achternaam']) ?>" required>
            </div>

            <div class="col-12 col-md-6">
                <label class="form-label">Email</label>
                <input type="email" name="email" class="form-control" value="<?= ($werknemer['email']) ?>" required>
            </div>

            <div class="col-12 col-md-6">
                <label class="form-label">Sector</label>

                <!-- Dropdown voor sectoren -->
                <select name="sector" id="sectorSelect" class="form-select" aria-label="Sector select">
                    <!-- Toon huidige sector als eerste, als die niet in lijst zit -->
                    <?php
                    $huidige = $werknemer['sector'] ?? '';
                    $huidige_in_lijst = in_array($huidige, $sectoren);
                    if ($huidige !== '' && !$huidige_in_lijst): ?>
                        <option value="<?= ($huidige) ?>" selected><?= ($huidige) ?> (huidig)</option>
                    <?php endif; ?>
                    <?php foreach ($sectoren as $s): ?>
                        <option value="<?= ($s) ?>" <?= $s === $huidige ? 'selected' : '' ?>><?= ($s) ?></option>
                    <?php endforeach; ?>
                    <option value="__andere__">Andere...</option>
                </select>

                <!-- Input veld dat verschijnt als gebruiker 'Andere...' kiest -->
                <input type="text" name="sector_nieuw" id="sectorNieuw" class="form-control mt-2" placeholder="Voer nieuwe sector in (bijv. Logistiek)" style="display:none;">
                <div class="small-muted mt-1">Kies een bestaande sector of voeg een nieuwe toe.</div>
            </div>
        </div>

        <div class="form-section">
            <div class="form-check form-switch mb-3">
                <input type="checkbox" name="BHV" class="form-check-input" id="BHV" <?= $werknemer['BHV'] ? 'checked' : '' ?>>
                <label class="form-check-label" for="BHV">BHV</label>
            </div>

            <h5 class="mb-2">Werkdagen</h5>
            <div class="days-grid mb-2">
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
            </div>

            <div class="d-flex justify-content-between mt-3">
                <a href="dagplanning.php" class="btn btn-outline-secondary">Terug</a>
                <button type="submit" class="btn btn-success">Opslaan</button>
            </div>
        </div>
    </form>
</div>

<script>
    (function(){
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

        // initial check
        toggleNieuw();

        select.addEventListener('change', toggleNieuw);
    })();
</script>

</body>
</html>
