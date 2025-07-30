<?php
include 'config.php'; // contient la connexion $conn

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirmPassword = $_POST['confirm_password'];

    // Vérification des champs
    if (empty($username) || empty($email) || empty($password) || empty($confirmPassword)) {
        echo "Veuillez remplir tous les champs.";
        exit;
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo "Adresse e-mail invalide.";
        exit;
    }

    if ($password !== $confirmPassword) {
        echo "Les mots de passe ne correspondent pas.";
        exit;
    }

    // Vérifier si l'email existe déjà
    $check = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $check->bind_param("s", $email);
    $check->execute();
    $check->store_result();

    if ($check->num_rows > 0) {
        echo "Cet e-mail est déjà utilisé.";
        exit;
    }

    // Hachage du mot de passe
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // Insertion de l'utilisateur (sans la colonne 'status')
    $stmt = $conn->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $username, $email, $hashedPassword);

    if ($stmt->execute()) {
        echo "Inscription réussie. <a href='login.php'>Connectez-vous ici</a>";
    } else {
        echo "Erreur lors de l'inscription.";
    }

    $stmt->close();
    $check->close();
    $conn->close();
} else {
    echo "Méthode non autorisée.";
}

