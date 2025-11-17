<?php
session_start();

// CSRF token genereren als niet aanwezig
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Admin check
$isAdmin = boolval($_SESSION['admin_logged_in'] ?? false);

// Database connectie
try {
    $db = new PDO("mysql:host=localhost;dbname=technolab-dashboard", "root", "");
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Databasefout: " . $e->getMessage());
}

$sectorenFile = __DIR__ . '/sectoren.json';

// Als bestand niet bestaat, maak een lege lijst
if (!file_exists($sectorenFile)) {
    file_put_contents($sectorenFile, json_encode([]));
}

// Sectoren uit bestand lezen
$extraSectoren = json_decode(file_get_contents($sectorenFile), true) ?: [];

// Flash messages
$melding = $_SESSION['flash_message'] ?? null;
unset($_SESSION['flash_message']);

// Variabelen initialiseren
$warningMessage = null;
$sectorToDelete = null;
$medewerkers = [];

// =====================
// Sector verwijderen (POST)
// =====================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirm_delete']) && $isAdmin) {
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die("Ongeldige actie (CSRF).");
    }

    $sectorToDelete = $_POST['sector_to_delete'] ?? null;
    $medewerkerSector = $_POST['medewerker_sector'] ?? [];

    if ($sectorToDelete) {
        // Update medewerkers
        if (!empty($medewerkerSector)) {
            $stmtSelect = $db->prepare("SELECT id FROM werknemers WHERE sector = :sector");
            $stmtSelect->execute([':sector' => $sectorToDelete]);
            $medewerkersIds = $stmtSelect->fetchAll(PDO::FETCH_COLUMN);

            foreach ($medewerkersIds as $i => $id) {
                $nieuweSector = $medewerkerSector[$i] ?? null;
                if ($nieuweSector && $nieuweSector !== '__leeg__') {
                    $updateStmt = $db->prepare("UPDATE werknemers SET sector = :nieuw WHERE id = :id");
                    $updateStmt->execute([':nieuw' => $nieuweSector, ':id' => $id]);
                } else {
                    $updateStmt = $db->prepare("UPDATE werknemers SET sector = NULL WHERE id = :id");
                    $updateStmt->execute([':id' => $id]);
                }
            }
        }

        // Verwijder uit JSON
        if (($key = array_search($sectorToDelete, $extraSectoren)) !== false) {
            unset($extraSectoren[$key]);
            $extraSectoren = array_values($extraSectoren);
            file_put_contents($sectorenFile, json_encode($extraSectoren, JSON_PRETTY_PRINT));
        }

        $warningMessage = "<div class='alert alert-success'>Sector <strong>" . htmlspecialchars($sectorToDelete) . "</strong> is verwijderd. Medewerkers zijn bijgewerkt.</div>";
        $sectorToDelete = null;
    }
}

// =====================
// Nieuwe sector toevoegen
// =====================
if ($isAdmin && $_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['sector_toevoegen'])) {
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die("Ongeldige actie (CSRF).");
    }

    $nieuweSector = trim($_POST['nieuwe_sector']);
    if ($nieuweSector !== '') {
        $checkDB = $db->prepare("SELECT COUNT(*) FROM werknemers WHERE sector = :sector");
        $checkDB->execute([':sector' => $nieuweSector]);
        $bestaatInDB = $checkDB->fetchColumn() > 0;
        $bestaatInJSON = in_array($nieuweSector, $extraSectoren);

        if (!$bestaatInDB && !$bestaatInJSON) {
            $extraSectoren[] = $nieuweSector;
            file_put_contents($sectorenFile, json_encode($extraSectoren, JSON_PRETTY_PRINT));
            $_SESSION['flash_message'] = "<div class='alert alert-success text-center'>Sector <strong>" . htmlspecialchars($nieuweSector) . "</strong> is toegevoegd.</div>";
        } else {
            $_SESSION['flash_message'] = "<div class='alert alert-warning text-center'>Sector <strong>" . htmlspecialchars($nieuweSector) . "</strong> bestaat al.</div>";
        }

        header("Location: add.php");
        exit;
    }
}

// =====================
// Sector verwijderen (GET)
// =====================
if ($isAdmin && isset($_GET['delete_sector'])) {
    $sectorToDelete = trim($_GET['delete_sector']);

    if ($sectorToDelete !== '') {
        $stmt = $db->prepare("SELECT voornaam, tussenvoegsel, achternaam, email FROM werknemers WHERE sector = :sector");
        $stmt->execute([':sector' => $sectorToDelete]);
        $medewerkers = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (count($medewerkers) === 0) {
            if (($key = array_search($sectorToDelete, $extraSectoren)) !== false) {
                unset($extraSectoren[$key]);
                $extraSectoren = array_values($extraSectoren);
                file_put_contents($sectorenFile, json_encode($extraSectoren, JSON_PRETTY_PRINT));
            }
            $_SESSION['flash_message'] = "<div class='alert alert-success text-center'>Lege sector <strong>" . htmlspecialchars($sectorToDelete) . "</strong> is verwijderd.</div>";
            header("Location: add.php");
            exit;
        }
    }
}

// =====================
// Sectoren ophalen
// =====================
$sectorenStmt = $db->query("SELECT DISTINCT sector FROM werknemers WHERE sector IS NOT NULL AND sector <> '' ORDER BY sector ASC");
$dbSectoren = $sectorenStmt->fetchAll(PDO::FETCH_COLUMN);

$sectoren = array_unique(array_merge($dbSectoren, $extraSectoren));
sort($sectoren);

// =====================
// Nieuwe medewerker toevoegen
// =====================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($_POST['confirm_delete']) && !isset($_POST['sector_toevoegen'])) {

    $voornaam = trim($_POST['voornaam'] ?? '');
    $tussenvoegsel = trim($_POST['tussenvoegsel'] ?? '');
    $achternaam = trim($_POST['achternaam'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $sector = !empty($_POST['sector_nieuw']) ? trim($_POST['sector_nieuw']) : ($_POST['sector'] ?? null);
    $bhv = isset($_POST['BHV']) ? 1 : 0;

    // Werkdagen
    $werkdagen = ['ma','di','wo','do','vr'];
    foreach ($werkdagen as $dag) {
        ${"werkdag_$dag"} = isset($_POST["werkdag_$dag"]) ? 1 : 0;
    }
    $status = ($werkdag_ma || $werkdag_di || $werkdag_wo || $werkdag_do || $werkdag_vr) ? "Aanwezig" : "Afwezig";

    // Email check
    $checkStmt = $db->prepare("SELECT COUNT(*) FROM werknemers WHERE email = :email");
    $checkStmt->execute([':email' => $email]);
    $bestaat = $checkStmt->fetchColumn();

    if ($bestaat > 0) {
        $melding = '<div class="alert alert-danger">Dit e-mailadres bestaat al.</div>';
    } else {
        $stmt = $db->prepare("INSERT INTO werknemers
            (voornaam, tussenvoegsel, achternaam, email, werkdag_ma, werkdag_di, werkdag_wo, werkdag_do, werkdag_vr, sector, BHV, status)
            VALUES (:voornaam, :tussenvoegsel, :achternaam, :email, :ma, :di, :wo, :do, :vr, :sector, :bhv, :status)");
        $stmt->execute([
            ':voornaam' => $voornaam,
            ':tussenvoegsel' => $tussenvoegsel ?: null,
            ':achternaam' => $achternaam,
            ':email' => $email,
            ':ma' => $werkdag_ma,
            ':di' => $werkdag_di,
            ':wo' => $werkdag_wo,
            ':do' => $werkdag_do,
            ':vr' => $werkdag_vr,
            ':sector' => $sector,
            ':bhv' => $bhv,
            ':status' => $status
        ]);

        $_SESSION['flash_message'] = "<div class='alert alert-success'>Medewerker $voornaam $achternaam is succesvol toegevoegd.</div>";
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
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="css/add.css">
    <style>
        /* Voeg hier je CSS voor popup en sectorbeheer toe, bijvoorbeeld overlay en .popup-card */
    </style>
</head>
<body>

<div class="add-card">
    <?= $melding ?? '' ?>
    <?= $warningMessage ?? '' ?>

    <h2>Nieuwe medewerker toevoegen</h2>
    <div class="small-muted mb-3">Vul de gegevens in en klik op opslaan</div>

    <form method="post">
        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">

        <!-- Persoonlijke gegevens -->
        <div class="form-section d-flex gap-2 flex-wrap">
            <div style="flex:1; min-width: 200px;">
                <label class="form-label" for="voornaam">Voornaam</label>
                <input type="text" id="voornaam" name="voornaam" class="form-control" required>
            </div>
            <div style="flex:1; min-width: 150px;">
                <label class="form-label" for="tussenvoegsel">Tussenvoegsel</label>
                <input type="text" id="tussenvoegsel" name="tussenvoegsel" class="form-control">
            </div>
            <div style="flex:1; min-width: 200px;">
                <label class="form-label" for="achternaam">Achternaam</label>
                <input type="text" id="achternaam" name="achternaam" class="form-control" required>
            </div>
        </div>

        <!-- Email -->
        <div class="form-section">
            <label class="form-label" for="email">Email</label>
            <input type="email" id="email" name="email" class="form-control" required>
        </div>

        <!-- Sector -->
        <div class="form-section">
            <label class="form-label" for="sectorSelect">Sector</label>
            <select name="sector" id="sectorSelect" class="form-select" required>
                <option value="">-- Kies sector --</option>
                <?php foreach ($sectoren as $s): ?>
                    <option value="<?= htmlspecialchars($s, ENT_QUOTES|ENT_SUBSTITUTE) ?>"><?= htmlspecialchars($s) ?></option>
                <?php endforeach; ?>
                <option value="__andere__">Andere...</option>
            </select>
            <input type="text" name="sector_nieuw" id="sectorNieuw" class="form-control mt-2" placeholder="Voer nieuwe sector in" style="display:none;">
            <div class="small-muted mt-1">Kies een bestaande sector of voeg een nieuwe toe.</div>
        </div>

        <!-- BHV + Werkdagen -->
        <div class="form-section">
            <div class="form-check form-switch mb-3">
                <input type="checkbox" name="BHV" class="form-check-input" id="BHV">
                <label class="form-check-label" for="BHV">BHV</label>
            </div>

            <h5 class="mb-2">Werkdagen</h5>

            <div class="days-grid mb-2">
                <?php
                $dagen = ['ma'=>'Maandag','di'=>'Dinsdag','wo'=>'Woensdag','do'=>'Donderdag','vr'=>'Vrijdag'];
                foreach($dagen as $key=>$label):
                    ?>
                    <div class="form-check">
                        <input type="checkbox" name="werkdag_<?= $key ?>" class="form-check-input" id="werkdag_<?= $key ?>">
                        <label class="form-check-label" for="werkdag_<?= $key ?>"><?= $label ?></label>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Buttons -->
        <div class="form-section d-flex justify-content-between">
            <a href="dagplanning.php" class="btn btn-outline-secondary">Terug</a>
            <button type="submit" class="btn btn-success">Opslaan</button>
        </div>
    </form>
</div>

<!-- Admin sector beheer -->
<?php if($isAdmin): ?>
    <div class="form-section mt-3 add-card">
        <h5 class="mb-2">Sectoren beheren</h5>

        <form method="post" class="d-flex gap-2 mb-3">
            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
            <input type="text" name="nieuwe_sector" class="form-control" placeholder="Nieuwe sector toevoegen..." required>
            <button type="submit" name="sector_toevoegen" class="btn btn-success">+</button>
        </form>

        <div class="sector-list">
            <?php foreach ($sectoren as $s): ?>
                <div class="sector-item d-flex justify-content-between align-items-center mb-2">
                    <span><?= htmlspecialchars($s, ENT_QUOTES|ENT_SUBSTITUTE) ?></span>
                    <a href="?delete_sector=<?= urlencode($s) ?>" class="btn btn-sm btn-outline-danger">X</a>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
<?php endif; ?>

<!-- Overlay popup voor verwijderen sector -->
<?php if($sectorToDelete && count($medewerkers) > 0): ?>
    <div class="overlay">
        <div class="popup-card">
            <div class="popup-header">
                Sector verwijderen: <?= htmlspecialchars($sectorToDelete) ?>
                <button type="button" class="close-btn" onclick="document.querySelector('.overlay').style.display='none'">&times;</button>
            </div>

            <div class="popup-content">
                <p>Deze sector heeft <strong><?= count($medewerkers) ?></strong> medewerker(s). Kies per medewerker een nieuwe sector:</p>
                <form method="post">
                    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                    <input type="hidden" name="sector_to_delete" value="<?= htmlspecialchars($sectorToDelete) ?>">

                    <div class="medewerker-lijst">
                        <?php foreach($medewerkers as $i=>$m): ?>
                            <div class="form-section medewerker-item">
                                <strong><?= htmlspecialchars($m['voornaam'].' '.$m['tussenvoegsel'].' '.$m['achternaam'].' ('.$m['email'].')') ?></strong>
                                <select name="medewerker_sector[<?= $i ?>]">
                                    <option value="__leeg__">(Geen, laat leeg)</option>
                                    <?php foreach($sectoren as $s): if($s !== $sectorToDelete): ?>
                                        <option value="<?= htmlspecialchars($s, ENT_QUOTES|ENT_SUBSTITUTE) ?>"><?= htmlspecialchars($s) ?></option>
                                    <?php endif; endforeach; ?>
                                </select>
                            </div>
                        <?php endforeach; ?>
                    </div>
            </div>

            <div class="popup-footer">
                <a href="add.php" class="btn btn-outline-secondary">Annuleren</a>
                <button type="submit" name="confirm_delete" class="btn btn-danger">Verwijderen</button>
                </form>
            </div>
        </div>
    </div>
<?php endif; ?>

<script>
    (function(){
        const select = document.getElementById('sectorSelect');
        const nieuwInput = document.getElementById('sectorNieuw');
        if(select){
            function toggleNieuw(){
                if(select.value==='__andere__'){
                    nieuwInput.style.display='block';
                    nieuwInput.focus();
                } else {
                    nieuwInput.style.display='none';
                    nieuwInput.value='';
                }
            }
            toggleNieuw();
            select.addEventListener('change', toggleNieuw);
        }
    })();
</script>

</body>
</html>
