<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

require_once 'config.php';

$user_id = $_SESSION['user_id'];

// Récupérer les informations de l’utilisateur
$sql_user = "SELECT username, statut FROM users WHERE id = ?";
$stmt_user = $conn->prepare($sql_user);
$stmt_user->bind_param("i", $user_id);
$stmt_user->execute();
$result_user = $stmt_user->get_result();
$user = $result_user->fetch_assoc();

// Récupérer les voitures de l’utilisateur
$sql = "SELECT * FROM vehicules WHERE user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Utilisateur</title>
    <link rel="stylesheet" href="style.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            background:rgb(164, 207, 224);
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .logout-btn {
            background: #c0392b;
            color: white;
            border: none;
            padding: 8px 14px;
            cursor: pointer;
            border-radius: 5px;
        }

        .publish-btn {
            background-color: #27ae60;
            color: white;
            padding: 10px 16px;
            text-decoration: none;
            border-radius: 6px;
            font-size: 15px;
            display: inline-block;
            margin: 25px 0 15px 0;
        }

        .vehicule-container {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            margin-top: 10px;
        }

        .vehicule-card {
            border: 1px solid #ddd;
            border-radius: 10px;
            width: 300px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            overflow: hidden;
        }

        .vehicule-card img {
            width: 100%;
            height: 180px;
            object-fit: cover;
            cursor: zoom-in;
            transition: transform 0.3s ease;
        }

        .vehicule-card img:hover {
            transform: scale(1.05);
        }

        .vehicule-info {
            padding: 10px;
        }

        .vehicule-info h3 {
            margin: 0 0 10px;
        }

        .vehicule-info p {
            margin: 5px 0;
            color: #555;
        }

        .btn {
            padding: 6px 10px;
            border: none;
            margin-right: 5px;
            border-radius: 5px;
            cursor: pointer;
        }

        .btn-edit {
            background-color: #3498db;
            color: white;
        }

        .btn-delete {
            background-color: #e74c3c;
            color: white;
        }
    </style>
</head>
<body>

<div class="header">
    <h2>Bienvenue, <?php echo htmlspecialchars($user['username']); ?> !</h2>
    <a href="logout.php"><button class="logout-btn">Se déconnecter</button></a>
</div>

<!-- Bouton Publier -->
<a href="publication.php" class="publish-btn">+ Publier une voiture</a>

<h3>Vos véhicules publiés :</h3>

<div class="vehicule-container">
    <?php while ($row = $result->fetch_assoc()): ?>
        <div class="vehicule-card">
            <img src="uploads/<?php echo htmlspecialchars($row['image']); ?>" alt="Image voiture" onclick="window.open(this.src, '_blank')">
            <div class="vehicule-info">
                <h3><?php echo htmlspecialchars($row['marque']); ?> - <?php echo htmlspecialchars($row['modele']); ?></h3>
                <p>Prix : <?php echo htmlspecialchars($row['prix']); ?> FCFA</p>
                <p>Type : <?php echo htmlspecialchars($row['type']); ?></p>
                <p>Année : <?php echo htmlspecialchars($row['annee']); ?></p>
                <a href="modifier.php?id=<?php echo $row['id']; ?>"><button class="btn btn-edit">Modifier</button></a>
                <a href="supprimer.php?id=<?php echo $row['id']; ?>" onclick="return confirm('Supprimer ce véhicule ?')"><button class="btn btn-delete">Supprimer</button></a>
            </div>
        </div>
    <?php endwhile; ?>
</div>

</body>
</html>

