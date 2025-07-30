<?php
session_start();
header("Content-Type: application/json");
include 'config.php';

$data = json_decode(file_get_contents("php://input"), true);
$email = trim($data['email'] ?? '');
$password = $data['password'] ?? '';

if (empty($email) || empty($password)) {
    echo json_encode(["status" => "error", "message" => "Tous les champs sont requis."]);
    exit;
}

$stmt = $conn->prepare("SELECT id, username, email, password, role, statut FROM users WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    $user = $result->fetch_assoc();

    if (!password_verify($password, $user['password'])) {
        echo json_encode(["status" => "error", "message" => "Mot de passe incorrect."]);
        exit;
    }

    if ($user['statut'] !== 'actif') {
        echo json_encode(["status" => "error", "message" => "Compte " . $user['statut'] . ", accès refusé."]);
        exit;
    }

    $_SESSION['user_id'] = $user['id'];
    $_SESSION['username'] = $user['username'];
    $_SESSION['email'] = $user['email'];
    $_SESSION['role'] = $user['role'];
    $_SESSION['statut'] = $user['statut'];

    $redirect = ($user['role'] === 'admin') ? 'dashboard_admin.php' : 'dashboard_user.php';

    echo json_encode(["status" => "success", "message" => "Connexion réussie !", "redirect" => $redirect]);
} else {
    echo json_encode(["status" => "error", "message" => "Aucun compte avec cet email."]);
}
$stmt->close();
$conn->close();
?>


