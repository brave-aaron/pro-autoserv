<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

include 'header(1).php';

$conn = new mysqli("localhost", "root", "", "auto_services");
if ($conn->connect_error) {
    die("Erreur de connexion : " . $conn->connect_error);
}

$sql = "SELECT * FROM vehicules WHERE type = 'vente' ORDER BY id DESC";
$result = $conn->query($sql);
?>

<h2 style="text-align:center;">Véhicules en vente</h2>

<div class="vehicule-list">
<?php
while ($row = $result->fetch_assoc()) {
    $imagePath = "uploads/" . htmlspecialchars($row['image']);
    echo '<div class="vehicule-card">';
    echo '<img src="' . $imagePath . '" alt="' . htmlspecialchars($row['marque']) . ' ' . htmlspecialchars($row['modele']) . '" class="car-img">';
    echo '<h3>' . htmlspecialchars($row['marque']) . ' ' . htmlspecialchars($row['modele']) . ' – ' . htmlspecialchars($row['annee']) . '</h3>';
    echo '<p><strong>Prix :</strong> ' . htmlspecialchars($row['prix']) . ' €</p>';
    echo '<a href="details.php?id=' . intval($row['id']) . '" class="btn">Voir détail</a>';
    echo '</div>';
}
$conn->close();
?>
</div>

<?php include 'footer.php'; ?>



