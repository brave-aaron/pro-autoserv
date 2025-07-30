<?php
// update_statut.php
header('Content-Type: text/plain');

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $user_id = intval($_POST['user_id'] ?? 0);
    $statut = $_POST['statut'] ?? '';

    if ($user_id > 0 && in_array($statut, ['actif', 'inactif', 'banni', 'en_attente'])) {
        $conn = new mysqli("localhost", "root", "", "auto_services");
        if ($conn->connect_error) {
            http_response_code(500);
            echo "Erreur de connexion BDD.";
            exit;
        }

        $stmt = $conn->prepare("UPDATE users SET statut = ? WHERE id = ?");
        $stmt->bind_param("si", $statut, $user_id);
        if ($stmt->execute()) {
            echo "Statut mis à jour avec succès.";
        } else {
            http_response_code(500);
            echo "Erreur lors de la mise à jour.";
        }

        $stmt->close();
        $conn->close();
    } else {
        http_response_code(400);
        echo "Données invalides.";
    }
} else {
    http_response_code(405);
    echo "Méthode non autorisée.";
}
?>


