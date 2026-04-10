<?php
require 'config.php';

if (isset($_GET['delete'])) {
    $id = $_GET['delete'];

    $stmt = $pdo->prepare("DELETE FROM taches WHERE id = ?");
    $stmt->execute([$id]);

    header("Location: index.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $titre = $_POST["titre"];
    $description = $_POST["description"];

    $stmt = $pdo->prepare("INSERT INTO taches (titre, description) VALUES (?, ?)");
    $stmt->execute([$titre, $description]);

    header("Location: index.php");
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Todo App</title>
</head>

<body>

<h1>Ma liste de tâches</h1>

<h2>Ajouter une tâche</h2>

<form method="POST">
    <input type="text" name="titre" placeholder="Titre">
    <br><br>

    <textarea name="description" placeholder="Description"></textarea>
    <br><br>

    <button type="submit">Ajouter</button>
</form>

<hr>

<?php
$stmt = $pdo->query("SELECT * FROM taches ORDER BY id DESC");
$taches = $stmt->fetchAll();

foreach ($taches as $t) {
    echo "<div>";
    echo "<h3>" . htmlspecialchars($t['titre']) . "</h3>";
    echo "<p>" . htmlspecialchars($t['description']) . "</p>";
    echo "<p>Statut : " . $t['statut'] . "</p>";
    echo "<p>Priorité : " . $t['priorite'] . "</p>";
    echo "</div>";
}
?>

</body>
</html>