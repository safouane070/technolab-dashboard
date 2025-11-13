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
// 1️⃣ Sector verwijderen
// =====================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirm_delete']) && $isAdmin) {

    // CSRF check
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die("Ongeldige actie (CSRF).");
    }

    $sectorToDelete = $_POST['sector_to_delete'] ?? null;
    $medewerkerSector = $_POST['medewerker_sector'] ?? [];

    if ($sectorToDelete) {

        // ✅ Update medewerkers alleen als er medewerkers zijn
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

        // ✅ Verwijder sector uit JSON-bestand als aanwezig
        if (($key = array_search($sectorToDelete, $extraSectoren)) !== false) {
            unset($extraSectoren[$key]);
            $extraSectoren = array_values($extraSectoren); // herindexeer array
            file_put_contents($sectorenFile, json_encode($extraSectoren, JSON_PRETTY_PRINT));
        }

        // ✅ Feedback aan admin
        $warningMessage = "<div class='alert alert-success'>Sector <strong>" . htmlspecialchars($sectorToDelete) . "</strong> is verwijderd. Medewerkers zijn bijgewerkt.</div>";
        $sectorToDelete = null;
    }

}

// =====================
// 2️⃣ Nieuwe sector toevoegen
// =====================
if ($isAdmin && $_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['sector_toevoegen'])) {
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die("Ongeldige actie (CSRF).");
    }

    $nieuweSector = trim($_POST['nieuwe_sector']);
    if ($nieuweSector !== '') {
        // Check of sector al in DB of JSON-bestand bestaat
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
// 3️⃣ Admin vraagt sector verwijderen (GET)
// =====================
// Admin vraagt om sector verwijderen
if ($isAdmin && isset($_GET['delete_sector'])) {
    $sectorToDelete = trim($_GET['delete_sector']);

    if ($sectorToDelete !== '') {
        // Check of er medewerkers in deze sector zitten
        $stmt = $db->prepare("SELECT voornaam, tussenvoegsel, achternaam, email FROM werknemers WHERE sector = :sector");
        $stmt->execute([':sector' => $sectorToDelete]);
        $medewerkers = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (count($medewerkers) > 0) {
            // Toon popup om medewerkers te verplaatsen
            // (de bestaande popup-code onderaan de HTML doet dit)
        } else {
            // ❌ Geen medewerkers — verwijder direct de sector
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
// 4️⃣ Sectoren ophalen en combineren
// =====================
$sectorenStmt = $db->query("SELECT DISTINCT sector FROM werknemers WHERE sector IS NOT NULL AND sector <> '' ORDER BY sector ASC");
$dbSectoren = $sectorenStmt->fetchAll(PDO::FETCH_COLUMN);

// Combineer DB + JSON
$sectoren = array_unique(array_merge($dbSectoren, $extraSectoren));
sort($sectoren);

// =====================
// 5️⃣ Nieuwe medewerker toevoegen
// =====================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($_POST['confirm_delete'])) {

    $voornaam = trim($_POST['voornaam'] ?? '');
    $tussenvoegsel = trim($_POST['tussenvoegsel'] ?? '');
    $achternaam = trim($_POST['achternaam'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $sector = !empty($_POST['sector_nieuw']) ? trim($_POST['sector_nieuw']) : ($_POST['sector'] ?? null);
    $bhv = isset($_POST['bhv']) ? 1 : 0;

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
/* Professionele modal styling */
.overlay {
    position: fixed;
    top:0;
    left:0;
    width: 100%;
    height: 100%;
    background: rgba(0,0,0,0.5);
    display: flex;
    justify-content: center;
    align-items: center;
    padding: 20px;
    z-index: 1000;
    animation: fadeIn 0.25s ease;
}

@keyframes fadeIn {
    from { background: rgba(0,0,0,0); }
    to { background: rgba(0,0,0,0.5); }
}

@keyframes slideIn {
    from { transform: translateY(-20px); opacity: 0; }
    to { transform: translateY(0); opacity: 1; }
}

.popup-card {
    background-color: #fff;
    border-radius: 12px;
    max-width: 600px;
    width: 100%;
    max-height: 80vh;
    display: flex;
    flex-direction: column;
    box-shadow: 0 15px 40px rgba(0,0,0,0.25);
    position: relative;
    animation: slideIn 0.25s forwards;
    overflow: hidden;
}

/* Sticky header */
.popup-header {
    padding: 20px 25px;
    background-color: #f7f7f7;
    border-bottom: 1px solid #ddd;
    font-size: 1.5rem;
    font-weight: 700;
    color: #2c3e50;
    position: sticky;
    top: 0;
    z-index: 10;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.popup-header .close-btn {
    background: transparent;
    border: none;
    font-size: 1.3rem;
    cursor: pointer;
    color: #888;
    transition: color 0.2s;
}
.popup-header .close-btn:hover {
    color: #333;
}

/* Scrollable content */
.popup-content {
    padding: 15px 25px;
    overflow-y: auto;
    flex: 1;
}

/* Sticky footer (buttons) */
.popup-footer {
    padding: 15px 25px;
    border-top: 1px solid #ddd;
    display: flex;
    justify-content: flex-end;
    gap: 10px;
    background-color: #f7f7f7;
    position: sticky;
    bottom: 0;
    z-index: 10;
}

/* Medewerker item */
.medewerker-item {
    margin-bottom: 15px;
}
.medewerker-item strong {
    display: block;
    margin-bottom: 5px;
    color: #333;
}

/* Select styling */
.popup-content select {
    width: 100%;
    padding: 8px 10px;
    border-radius: 6px;
    border: 1px solid #ccc;
    font-size: 0.95rem;
}
.popup-content select:focus {
    outline: none;
    border-color: #0b5ed7;
    box-shadow: 0 0 5px rgba(11,94,215,0.25);
}

/* Buttons */
.btn-outline-secondary {
    background-color: #f0f0f0;
    border: 1px solid #ccc;
    color: #333;
    padding: 8px 15px;
    border-radius: 6px;
    font-weight: 600;
}
.btn-outline-secondary:hover {
    background-color: #e0e0e0;
}

.btn-danger {
    background-color: #e74c3c;
    color: #fff;
    padding: 8px 15px;
    border-radius: 6px;
    font-weight: 600;
}
.btn-danger:hover {
    background-color: #c0392b;
}

/* Responsive */
@media (max-width: 768px) {
    .popup-card {
        max-width: 95%;
        max-height: 90vh;
    }
}
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
                    <option value="<?= ($s) ?>"><?= ($s) ?></option>
                <?php endforeach; ?>
                <option value="__andere__">Andere...</option>
            </select>
            <input type="text" name="sector_nieuw" id="sectorNieuw" class="form-control mt-2" placeholder="Voer nieuwe sector in" style="display:none;">
            <div class="small-muted mt-1">Kies een bestaande sector of voeg een nieuwe toe.</div>
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

        <!-- Formulier om nieuwe sector toe te voegen -->
        <form method="post" class="d-flex gap-2 mb-3">
            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
            <input type="text" name="nieuwe_sector" class="form-control" placeholder="Nieuwe sector toevoegen..." required>
            <button type="submit" name="sector_toevoegen" class="btn btn-success">+</button>
        </form>

        <!-- Lijst met bestaande sectoren -->
        <div class="sector-list">
            <?php foreach ($sectoren as $s): ?>
                <div class="sector-item d-flex justify-content-between align-items-center mb-2">
                    <span><?= ($s) ?></span>
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
        <!-- Header met close-knop -->
        <div class="popup-header">
            Sector verwijderen: <?= ($sectorToDelete) ?>
            <button type="button" class="close-btn" onclick="document.querySelector('.overlay').style.display='none'">&times;</button>
        </div>

        <!-- Scrollable content -->
        <div class="popup-content">
            <p>Deze sector heeft <strong><?= count($medewerkers) ?></strong> medewerker(s). Kies per medewerker een nieuwe sector:</p>
            <form method="post">
                <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                <input type="hidden" name="sector_to_delete" value="<?= ($sectorToDelete) ?>">

                <div class="medewerker-lijst">
                    <?php foreach($medewerkers as $i=>$m): ?>
                        <div class="form-section medewerker-item">
                            <strong><?= ($m['voornaam'].' '.$m['tussenvoegsel'].' '.$m['achternaam'].' ('.$m['email'].')') ?></strong>
                            <select name="medewerker_sector[<?= $i ?>]">
                                <option value="__leeg__">(Geen, laat leeg)</option>
                                <?php foreach($sectoren as $s): if($s !== $sectorToDelete): ?>
                                    <option value="<?= ($s) ?>"><?= ($s) ?></option>
                                <?php endif; endforeach; ?>
                            </select>
                        </div>
                    <?php endforeach; ?>
                </div>
        </div>

        <!-- Sticky footer -->
        <div class="popup-footer">
            <a href="add.php" class="btn btn-outline-secondary">Annuleren</a>
            <button type="submit" name="confirm_delete" class="btn btn-danger">Verwijderen</button>
            </form>
        </div>
    </div>
</div>
<?php endif; ?>

<script>
    // Toggle nieuwe sector input
    (function(){
        const select = document.getElementById('sectorSelect');
        const nieuwInput = document.getElementById('sectorNieuw');
        if(select){
            select.addEventListener('change',()=> {
                if(select.value==='__andere__'){ 
                    nieuwInput.style.display='block'; 
                    nieuwInput.focus(); 
                } else { 
                    nieuwInput.style.display='none'; 
                    nieuwInput.value=''; 
                }
            });
        }
    })();
</script>

</body>
</html>
