<?php
require 'config.php';

// SUPPRESSION
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];

    $stmt = $pdo->prepare("DELETE FROM taches WHERE id = ?");
    $stmt->execute([$id]);

    header("Location: index.php");
    exit;
}

// RECUPERER TACHE A MODIFIER
$editTache = null;

if (isset($_GET['edit'])) {
    $id = $_GET['edit'];

    $stmt = $pdo->prepare("SELECT * FROM taches WHERE id = ?");
    $stmt->execute([$id]);

    $editTache = $stmt->fetch();
}

// AJOUT / MODIFICATION
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $titre = trim($_POST["titre"]);
    $description = trim($_POST["description"]);

    if (!empty($titre)) {

        if (isset($_GET['edit'])) {
            $id = $_GET['edit'];

            $stmt = $pdo->prepare("UPDATE taches SET titre=?, description=? WHERE id=?");
            $stmt->execute([$titre, $description, $id]);

        } else {
            $stmt = $pdo->prepare("INSERT INTO taches (titre, description) VALUES (?, ?)");
            $stmt->execute([$titre, $description]);
        }

        header("Location: index.php");
        exit;
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Todo App</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>

<h1>Ma liste de tâches</h1>

<h2><?php echo isset($_GET['edit']) ? "Modifier une tâche" : "Ajouter une tâche"; ?></h2>

<form method="POST" onsubmit="return verifierForm()">

    <input type="text" name="titre" placeholder="Titre"
    value="<?php echo htmlspecialchars($editTache['titre'] ?? ''); ?>">

    <br><br>

    <textarea name="description" placeholder="Description"><?php echo htmlspecialchars($editTache['description'] ?? ''); ?></textarea>

    <br><br>

    <button type="submit">
        <?php echo isset($_GET['edit']) ? "Modifier" : "Ajouter"; ?>
    </button>

</form>

<hr>

<h3>Filtrer</h3>
<button onclick="filtrer('all')">Toutes</button>
<button onclick="filtrer('a_faire')">À faire</button>
<button onclick="filtrer('termine')">Terminées</button>

<hr>

<?php
$stmt = $pdo->query("SELECT * FROM taches ORDER BY id DESC");
$taches = $stmt->fetchAll();

foreach ($taches as $t) {
    echo "<div class='tache' data-statut='" . $t['statut'] . "' style='border:1px solid #000; padding:10px; margin:10px;'>";

    echo "<h3>" . htmlspecialchars($t['titre']) . "</h3>";
    echo "<p>" . htmlspecialchars($t['description']) . "</p>";
    echo "<p>Statut : " . htmlspecialchars($t['statut']) . "</p>";
    echo "<p>Priorité : " . htmlspecialchars($t['priorite']) . "</p>";

    echo "<a href='?edit=" . $t['id'] . "'>Modifier</a> | ";
    echo "<a href='?delete=" . $t['id'] . "' onclick=\"return confirm('Supprimer ?')\">Supprimer</a>";

    echo "</div>";
}
?>

<!-- JAVASCRIPT CAPTURE 1 -->
<script>
function verifierForm() {
    let titre = document.querySelector('input[name="titre"]').value;

    if (titre.trim() === "") {
        alert("Le titre est obligatoire !");
        return false;
    }

    return true;
}

function filtrer(type) {
    let taches = document.querySelectorAll('.tache');

    taches.forEach(t => {
        if (type === 'all') {
            t.style.display = "block";
        } else {
            if (t.dataset.statut === type) {
                t.style.display = "block";
            } else {
                t.style.display = "none";
            }
        }
    });
}
</script>

</body>
</html>