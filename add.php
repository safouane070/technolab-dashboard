<?php
// Verbinding maken
try {
    $db = new PDO("mysql:host=localhost;dbname=technolab-dashboard", "root", "");
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Fout!: " . $e->getMessage());
}

// Haal bestaande sectoren op voor de dropdown
$sectorenStmt = $db->query("SELECT DISTINCT sector FROM werknemers WHERE sector IS NOT NULL AND sector <> '' ORDER BY sector ASC");
$sectoren = $sectorenStmt->fetchAll(PDO::FETCH_COLUMN);

$melding = null;

// Verwerking formulier
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $voornaam = $_POST['voornaam'];
    $tussenvoegsel = $_POST['tussenvoegsel'] ?? null;
    $achternaam = $_POST['achternaam'];
    $email = $_POST['email'];

    // Check of email al bestaat
    $checkStmt = $db->prepare("SELECT COUNT(*) FROM werknemers WHERE email = :email");
    $checkStmt->execute([':email' => $email]);
    $bestaat = $checkStmt->fetchColumn();

    if ($bestaat > 0) {
        $melding = '<div class="alert alert-danger text-center">Dit e-mailadres bestaat al. Gebruik een ander adres.</div>';
    } else {
        // sector kan komen uit dropdown of nieuw veld
        if (isset($_POST['sector_nieuw']) && trim($_POST['sector_nieuw']) !== '') {
            $sector = trim($_POST['sector_nieuw']);
        } else {
            $sector = $_POST['sector'];
        }

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
}
?>
<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <title>Nieuwe medewerker toevoegen</title>
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
        .add-card {
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
        }
    </style>
</head>
<body>

<div class="add-card shadow-lg p-4 rounded-4 bg-white">
    <?php if ($melding) echo $melding; ?>

    <div class="d-flex align-items-center mb-3">
        <div class="me-3">
            <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" fill="#0b5ed7" class="bi bi-person-plus" viewBox="0 0 16 16">
                <path d="M6 8a3 3 0 1 0 0-6 3 3 0 0 0 0 6z"/>
                <path fill-rule="evenodd" d="M6 9a6 6 0 1 0 0 12A6 6 0 0 0 6 9zm9.5 3a.5.5 0 0 1 .5.5v2h2a.5.5 0 0 1 0 1h-2v2a.5.5 0 0 1-1 0v-2h-2a.5.5 0 0 1 0-1h2v-2a.5.5 0 0 1 .5-.5z"/>
            </svg>
        </div>
        <div>
            <h2 class="mb-0">Nieuwe medewerker toevoegen</h2>
            <div class="small-muted">Vul de gegevens in en klik op opslaan</div>
        </div>
    </div>

    <form method="post">
        <div class="row g-3 form-section">
            <div class="col-md-4">
                <label class="form-label">Voornaam</label>
                <input type="text" name="voornaam" class="form-control" required>
            </div>
            <div class="col-md-4">
                <label class="form-label">Tussenvoegsel</label>
                <input type="text" name="tussenvoegsel" class="form-control">
            </div>
            <div class="col-md-4">
                <label class="form-label">Achternaam</label>
                <input type="text" name="achternaam" class="form-control" required>
            </div>

            <div class="col-md-6">
                <label class="form-label">Email</label>
                <input type="email" name="email" class="form-control" required>
            </div>

            <div class="col-md-6">
                <label class="form-label">Cirkels</label>
                <select name="sector" id="sectorSelect" class="form-select" required>
                    <option value="">-- Kies sector --</option>
                    <?php foreach ($sectoren as $s): ?>
                        <option value="<?= ($s) ?>"><?= ($s) ?></option>
                    <?php endforeach; ?>
                    <option value="__andere__">Andere...</option>
                </select>
                <input type="text" name="sector_nieuw" id="sectorNieuw" class="form-control mt-2" placeholder="Voer nieuwe sector in" style="display:none;">
                <div class="small-muted mt-1">Kies een bestaande sector of voeg een nieuwe toe.</div>
            </div>
        </div>

        <div class="form-section">
            <div class="form-check form-switch mb-3">
                <input type="checkbox" name="bhv" class="form-check-input" id="bhv">
                <label class="form-check-label" for="bhv">Heeft BHV</label>
            </div>

            <h5 class="mb-2">Werkdagen</h5>
            <div class="days-grid mb-3">
                <?php
                $days = ['ma' => 'Maandag', 'di' => 'Dinsdag', 'wo' => 'Woensdag', 'do' => 'Donderdag', 'vr' => 'Vrijdag'];
                foreach ($days as $key => $label): ?>
                    <div class="form-check">
                        <input type="checkbox" name="werkdag_<?= $key ?>" class="form-check-input" id="werkdag_<?= $key ?>">
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

        select.addEventListener('change', toggleNieuw);
    })();
</script>

</body>
</html>
