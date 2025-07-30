<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

$sql = "SELECT * FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if ($user['role'] === 'admin') {
    header("Location: dashboard_admin.php");
    exit();
} elseif ($user['statut'] === 'actif') {
    header("Location: dashboard_user.php");
    exit();
} else {
    echo "Votre compte est en attente de validation par l'administrateur.";
}
?>










