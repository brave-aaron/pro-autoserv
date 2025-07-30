<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>AutoService</title>
  <link rel="stylesheet" href="style.css" />
</head>
<body>

<header>
  <nav>
    <a href="index.php">Accueil</a>
    <a href="vente.php">Vente</a>
    <a href="location.php">Location</a>
    <a href="contact.php">Contact</a>

    <?php if (isset($_SESSION['user_id'])): ?>
    
      <a href="mon_compte.php">Mon compte</a>
    <?php else: ?>
      <a href="login.php">Connexion</a>
      <a href="register.php">Inscription</a>
    <?php endif; ?>
  </nav>
</header>

