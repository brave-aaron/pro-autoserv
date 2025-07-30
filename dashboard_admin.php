<?php
session_start();

// Vérifie que l'utilisateur est admin (ajuste selon ta logique de session)
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

$conn = new mysqli("localhost", "root", "", "auto_services");

if ($conn->connect_error) {
    die("Connexion échouée: " . $conn->connect_error);
}

// Récupérer les utilisateurs avec leur nombre de publications
$sql = "
    SELECT u.id, u.username, u.email, u.statut, COUNT(v.id) AS total_publications
    FROM users u
    LEFT JOIN vehicules v ON u.id = v.user_id
    GROUP BY u.id, u.username, u.email, u.statut
    ORDER BY u.username ASC
";

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Admin</title>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th, td {
            border: 1px solid #ccc;
            padding: 8px;
            text-align: center;
        }

        select, button {
            padding: 5px;
        }

        .actif { color: green; font-weight: bold; }
        .inactif { color: gray; }
        .banni { color: red; }
        .en_attente { color: orange; }

        .logout-container {
            margin-top: 30px;
            text-align: center;
        }

        .logout-button {
            padding: 8px 16px;
            background-color: #333;
            color: white;
            border: none;
            cursor: pointer;
        }

        .delete-button {
            background-color: red;
            color: white;
            padding: 5px 10px;
            border: none;
            cursor: pointer;
        }

        .delete-button:hover {
            background-color: darkred;
        }
    </style>
</head>
<body>
    <h2>Liste des utilisateurs</h2>

    <table>
        <thead>
            <tr>
                <th>Nom</th>
                <th>Email</th>
                <th>Statut</th>
                <th>Changer Statut</th>
                <th>Publications</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($user = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= htmlspecialchars($user['username']) ?></td>
                    <td><?= htmlspecialchars($user['email']) ?></td>
                    <td class="<?= $user['statut'] ?>"><?= ucfirst($user['statut']) ?></td>
                    <td>
                        <form method="POST" action="changer_statut.php">
                            <input type="hidden" name="user_id" value="<?= $user['id'] ?>">
                            <select name="statut">
                                <option value="actif" <?= $user['statut'] === 'actif' ? 'selected' : '' ?>>Actif</option>
                                <option value="inactif" <?= $user['statut'] === 'inactif' ? 'selected' : '' ?>>Inactif</option>
                                <option value="banni" <?= $user['statut'] === 'banni' ? 'selected' : '' ?>>Banni</option>
                                <option value="en_attente" <?= $user['statut'] === 'en_attente' ? 'selected' : '' ?>>En attente</option>
                            </select>
                            <button type="submit">Appliquer</button>
                        </form>
                    </td>
                    <td><?= $user['total_publications'] ?></td>
                    <td>
                        <form method="POST" action="supprimer.php" onsubmit="return confirm('Supprimer cet utilisateur ?');">
                            <input type="hidden" name="user_id" value="<?= $user['id'] ?>">
                            <button type="submit" class="delete-button">Supprimer</button>
                        </form>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>

    <div class="logout-container">
        <form action="logout.php" method="post">
            <button type="submit" class="logout-button">Déconnexion</button>
        </form>
    </div>
</body>
</html>
