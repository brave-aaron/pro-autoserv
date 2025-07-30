<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

include 'header(1).php';

$conn = new mysqli("localhost", "root", "", "auto_services");
if ($conn->connect_error) {
    die("Erreur de connexion : " . $conn->connect_error);
}

$user_id = intval($_SESSION['user_id']);
$vehicule_id = intval($_GET['id'] ?? 0);

if ($vehicule_id <= 0) {
    echo "<p>ID véhicule invalide.</p>";
    exit;
}

// Récupérer les infos du véhicule
$stmt = $conn->prepare("SELECT * FROM vehicules WHERE id = ? AND user_id = ?");
$stmt->bind_param("ii", $vehicule_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows !== 1) {
    echo "<p>Véhicule non trouvé ou accès refusé.</p>";
    exit;
}

$vehicule = $result->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Traitement du formulaire soumis
    $marque = $_POST['marque'] ?? '';
    $modele = $_POST['modele'] ?? '';
    $annee = intval($_POST['annee'] ?? 0);
    $km = intval($_POST['km'] ?? 0);
    $energie = $_POST['energie'] ?? '';
    $transmission = $_POST['transmission'] ?? '';
    $description = $_POST['description'] ?? '';
    $prix = floatval($_POST['prix'] ?? 0);
    $type = $_POST['type'] ?? '';

    // TODO: Ajouter validation simple ici (ex: champs obligatoires)

    // Gérer l’upload d’image si nouveau fichier soumis
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = "uploads/";
        $tmpName = $_FILES['image']['tmp_name'];
        $fileName = basename($_FILES['image']['name']);
        $targetFile = $uploadDir . $fileName;

        if (move_uploaded_file($tmpName, $targetFile)) {
            $image = $fileName;

            // Optionnel : supprimer ancienne image si besoin
        } else {
            $image = $vehicule['image'];
            $error = "Erreur lors de l’upload de l’image.";
        }
    } else {
        $image = $vehicule['image']; // garder l’ancienne image si pas de nouveau fichier
    }

    // Mettre à jour la base de données
    $stmtUpdate = $conn->prepare("UPDATE vehicules SET marque = ?, modele = ?, annee = ?, km = ?, energie = ?, transmission = ?, description = ?, prix = ?, image = ?, type = ? WHERE id = ? AND user_id = ?");
    $stmtUpdate->bind_param("ssiiissdssii", $marque, $modele, $annee, $km, $energie, $transmission, $description, $prix, $image, $type, $vehicule_id, $user_id);


    if ($stmtUpdate->execute()) {
        $_SESSION['message'] = "Véhicule modifié avec succès.";
        header("Location: mon_compte.php");
        exit();
    } else {
        $error = "Erreur lors de la mise à jour.";
    }
}
?>

<section class="publication-form">
  <h2>Modifier véhicule</h2>

  <?php if (!empty($error)): ?>
    <p style="color:red;"><?= htmlspecialchars($error) ?></p>
  <?php endif; ?>

  <form action="" method="post" enctype="multipart/form-data">
    <label for="marque">Marque :</label>
    <input type="text" id="marque" name="marque" value="<?= htmlspecialchars($vehicule['marque']) ?>" required>

    <label for="modele">Modèle :</label>
    <input type="text" id="modele" name="modele" value="<?= htmlspecialchars($vehicule['modele']) ?>" required>

    <label for="annee">Année :</label>
    <input type="number" id="annee" name="annee" min="1900" max="2099" step="1" value="<?= htmlspecialchars($vehicule['annee']) ?>" required>

    <label for="km">Kilométrage :</label>
    <input type="number" id="km" name="km" min="0" value="<?= htmlspecialchars($vehicule['km']) ?>" required>

    <label for="energie">Énergie :</label>
    <select id="energie" name="energie" required>
      <?php
      $energies = ['Essence', 'Diesel', 'Électrique', 'Hybride'];
      foreach ($energies as $e) {
          $selected = ($vehicule['energie'] === $e) ? "selected" : "";
          echo "<option value=\"$e\" $selected>$e</option>";
      }
      ?>
    </select>

    <label for="transmission">Boîte :</label>
    <select id="transmission" name="transmission" required>
      <?php
      $transmissions = ['Manuelle', 'Automatique'];
      foreach ($transmissions as $t) {
          $selected = ($vehicule['transmission'] === $t) ? "selected" : "";
          echo "<option value=\"$t\" $selected>$t</option>";
      }
      ?>
    </select>

    <label for="description">Description :</label>
    <textarea id="description" name="description" required><?= htmlspecialchars($vehicule['description']) ?></textarea>

    <label for="prix">Prix (€) :</label>
    <input type="number" id="prix" name="prix" step="0.01" min="0" value="<?= htmlspecialchars($vehicule['prix']) ?>" required>

    <label for="type">Type :</label>
    <select id="type" name="type" required>
      <?php
      $types = ['vente', 'location'];
      foreach ($types as $typeOption) {
          $selected = ($vehicule['type'] === $typeOption) ? "selected" : "";
          echo "<option value=\"$typeOption\" $selected>$typeOption</option>";
      }
      ?>
    </select>

    <label for="image">Image (laisser vide pour garder l'actuelle) :</label>
    <input type="file" id="image" name="image" accept="image/*">

    <button type="submit" class="btn">Modifier</button>
  </form>
</section>

<?php
$conn->close();
include 'footer.php';
?>
