<?php include 'header(1).php'; ?>
<link rel="stylesheet" href="style.css">

<section class="auth-container">
  <h2>Connexion</h2>
  <form id="loginForm" method="POST">
    <label for="email">Email</label>
    <input type="email" id="email" name="email" required />

    <label for="password">Mot de passe</label>
    <input type="password" id="password" name="password" required />

    <button type="submit" class="btn">Se connecter</button>

    <p>Pas encore de compte ? <a href="register.php">Inscrivez-vous</a></p>

    <div id="resultMessage"></div>
  </form>
</section>

<script>
document.getElementById('loginForm').addEventListener('submit', async function(e) {
  e.preventDefault();

  const email = this.email.value.trim();
  const password = this.password.value;
  const result = document.getElementById('resultMessage');

  if (!email || !password) {
    result.textContent = "Tous les champs sont requis.";
    result.style.color = "red";
    return;
  }

  try {
    const response = await fetch('login_process.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ email, password })
    });

    const res = await response.json();
    result.textContent = res.message;
    result.style.color = res.status === "success" ? "green" : "red";

    if (res.status === "success") {
      setTimeout(() => {
        window.location.href = res.redirect;
      }, 800);
    }
  } catch {
    result.textContent = "Erreur, réessayez.";
    result.style.color = "red";
  }
});
</script>

<?php include 'footer.php'; ?>


