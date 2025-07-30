<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= $title ?? 'AutoService - Votre partenaire automobile' ?></title>
  <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body>
  <header>
    <nav>
      <div class="logo">
        <h1><a href="/index.php">AutoService</a></h1>
      </div>
      <ul class="nav-links">
        <li><a href="/index.php">Accueil</a></li>
        <li><a href="/vente.php">Vente</a></li>
        <li><a href="/location.php">Location</a></li>
        <li><a href="/contact.php">Contact</a></li>
        
        <?php session_start(); ?>
        <?php if (isset($_SESSION['user_id'])): ?>
          <li><a href="/mon_compte.php">Mon Compte</a></li>
          <?php if ($_SESSION['role'] === 'admin'): ?>
            <li><a href="/dashboard_admin.php">Admin</a></li>
          <?php else: ?>
            <li><a href="/dashboard_user.php">Dashboard</a></li>
          <?php endif; ?>
          <li><a href="/logout.php">Déconnexion</a></li>
        <?php else: ?>
          <li><a href="/login.php">Connexion</a></li>
          <li><a href="/register.php">Inscription</a></li>
        <?php endif; ?>
      </ul>
    </nav>
  </header>
  
  <main>