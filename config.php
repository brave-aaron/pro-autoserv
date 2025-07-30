<?php
$host = 'localhost';
$user = 'root';
$pass = '';
$dbname = 'auto_services';

$conn = new mysqli($host, $user, $pass, $dbname);

if ($conn->connect_error) {
    die("Connexion échouée: " . $conn->connect_error);
}
?>
