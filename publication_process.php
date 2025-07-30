<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$conn = new mysqli("localhost", "root", "", "auto_services");
if ($conn->connect_error) {
    die("Erreur de connexion : " . $conn->connect_error);
}

// Traitement image
$targetDir = "uploads/";
$imageName = basename($_FILES["image"]["name"]);
$targetFile = $targetDir . $imageName;
move_uploaded_file($_FILES["image"]["tmp_name"], $targetFile);

// Données du formulaire
$marque = $_POST['marque'];
$modele = $_POST['modele'];
$annee = $_POST['annee'];
$km = $_POST['km'];
$energie = $_POST['energie'];
$transmission = $_POST['transmission'];
$description = $_POST['description'];
$prix = $_POST['prix'];
$type = $_POST['type'];
$user_id = $_SESSION['user_id'];

$stmt = $conn->prepare("INSERT INTO vehicules (marque, modele, annee, km, energie, transmission, description, prix, image, type, user_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
$stmt->bind_param("ssisssssssi", $marque, $modele, $annee, $km, $energie, $transmission, $description, $prix, $imageName, $type, $user_id);

if ($stmt->execute()) {
    header("Location: mon_compte.php");
    exit();
} else {
    echo "Erreur lors de la publication.";
}

$conn->close();
?>
