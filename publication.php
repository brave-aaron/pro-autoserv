<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
include 'header(1).php';
?>

<section>
  <h2>Publier un véhicule</h2>

  <form action="publication_process.php" method="POST" enctype="multipart/form-data" class="publication-form">
    <label>Marque :</label>
    <input type="text" name="marque" required>

    <label>Modèle :</label>
    <input type="text" name="modele" required>

    <label>Année :</label>
    <input type="number" name="annee" required>

    <label>Kilométrage :</label>
    <input type="number" name="km" required>

    <label>Énergie :</label>
    <select name="energie" required>
      <option value="Essence">Essence</option>
      <option value="Diesel">Diesel</option>
      <option value="Électrique">Électrique</option>
      <option value="Hybride">Hybride</option>
    </select>

    <label>Transmission :</label>
    <select name="transmission" required>
      <option value="Manuelle">Manuelle</option>
      <option value="Automatique">Automatique</option>
    </select>

    <label>Description :</label>
    <textarea name="description" rows="4" required></textarea>

    <label>Prix (en € ou €/jour) :</label>
    <input type="text" name="prix" required>

    <label>Type :</label>
    <select name="type" required>
      <option value="vente">Vente</option>
      <option value="location">Location</option>
    </select>

    <label>Image :</label>
    <input type="file" name="image" accept="image/*" required>

    <button type="submit" class="btn">Publier</button>
  </form>
</section>

<?php include 'footer.php'; ?>
