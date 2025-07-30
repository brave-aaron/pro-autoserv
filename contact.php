<?php include 'header(1).php'; ?>
<section>
  <h2>Nous contacter</h2>
  <form class="contact-form">
    <label for="nom">Nom :</label>
    <input type="text" id="nom" name="nom" required />
    <label for="email">Email :</label>
    <input type="email" id="email" name="email" required />
    <label for="message">Message :</label>
    <textarea id="message" name="message" rows="5" required></textarea>
    <button type="submit" class="btn">Envoyer</button>
  </form>
</section>
<?php include 'footer.php'; ?>
