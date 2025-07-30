<?php include 'header(1).php'; ?>
<link rel="stylesheet" href="style.css"> <!-- lien vers le CSS -->

<section>
    <h2>Détails du véhicule</h2>

    <?php
    // Connexion à la base
    $conn = new mysqli("localhost", "root", "", "auto_services");
    if ($conn->connect_error) {
        die("<p class='error'>Erreur de connexion : " . $conn->connect_error . "</p>");
    }

    // Récupération sécurisée de l'ID
    $id = isset($_GET['id']) ? intval($_GET['id']) : 0;

    if ($id > 0) {
        $stmt = $conn->prepare("SELECT * FROM vehicules WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($car = $result->fetch_assoc()) {
            $imagePath = !empty($car['image']) ? "uploads/" . htmlspecialchars($car['image']) : "uploads/default.jpg";

            echo '<div class="card details">';
            echo '<img src="' . $imagePath . '" alt="' . htmlspecialchars($car['marque']) . '" class="car-img"/>';
            echo '<h3>' . htmlspecialchars($car['marque']) . ' ' . htmlspecialchars($car['modele']) . ' – ' . htmlspecialchars($car['annee']) . '</h3>';
            echo '<p><strong>Kilométrage :</strong> ' . htmlspecialchars($car['km']) . ' km</p>';
            echo '<p><strong>Énergie :</strong> ' . htmlspecialchars($car['energie']) . '</p>';
            echo '<p><strong>Boîte :</strong> ' . htmlspecialchars($car['transmission']) . '</p>';
            echo '<p><strong>Description :</strong> ' . htmlspecialchars($car['description']) . '</p>';
            echo '<p><strong>Prix :</strong> ' . number_format($car['prix'], 0, '', ' ') . ' €</p>';
            echo '<a href="vente.php" class="btn">Retour</a>';
            echo '</div>';
        } else {
            echo '<p class="error">Véhicule introuvable.</p>';
        }

        $stmt->close();
    } else {
        echo '<p class="error">Identifiant manquant ou invalide.</p>';
    }

    $conn->close();
    ?>
</section>

<?php include 'footer.php'; ?>


