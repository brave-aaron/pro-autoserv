<?php include 'header(1).php'; ?>
<link rel="stylesheet" href="style.css">

<section class="auth-container">
  <h2>Inscription</h2>
  <form id="registerForm" method="POST">
    <div class="form-group">
      <label for="username">Nom d'utilisateur</label>
      <input type="text" id="username" name="username" required />
    </div>

    <div class="form-group">
      <label for="email">Adresse e-mail</label>
      <input type="email" id="email" name="email" required />
    </div>

    <div class="form-group">
      <label for="password">Mot de passe</label>
      <input type="password" id="password" name="password" required />
    </div>

    <div class="form-group">
      <label for="confirm_password">Confirmez le mot de passe</label>
      <input type="password" id="confirm_password" name="confirm_password" required />
    </div>

    <button type="submit" class="btn">S'inscrire</button>
    <div id="message"></div>
  </form>
</section>

<script>
document.getElementById("registerForm").addEventListener("submit", async function(e) {
  e.preventDefault();

  const form = e.target;
  const username = form.username.value.trim();
  const email = form.email.value.trim();
  const password = form.password.value;
  const confirmPassword = form.confirm_password.value;

  const messageDiv = document.getElementById("message");

  // Validation côté client
  if (!username || !email || !password || !confirmPassword) {
    messageDiv.innerHTML = "Veuillez remplir tous les champs.";
    return;
  }

  if (password !== confirmPassword) {
    messageDiv.innerHTML = "Les mots de passe ne correspondent pas.";
    return;
  }

  const formData = new FormData(form);

  try {
    const response = await fetch("register_process.php", {
      method: "POST",
      body: formData
    });

    const result = await response.text();
    messageDiv.innerHTML = result;
  } catch (error) {
    messageDiv.innerHTML = "Erreur lors de l'inscription.";
  }
});
</script>

<?php include 'footer.php'; ?>


