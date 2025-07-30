<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if (isset($_GET['id'])) {
    $vehicle_id = intval($_GET['id']);
    $user_id = $_SESSION['user_id'];

    include 'config.php';

    // Supprimer seulement si le véhicule appartient à l'utilisateur
    $stmt = $conn->prepare("DELETE FROM vehicules WHERE id = ? AND user_id = ?");
    $stmt->bind_param("ii", $vehicle_id, $user_id);
    $stmt->execute();

    header("Location: dashboard_user.php");
    exit();
}
?>
