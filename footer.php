<script>
document.addEventListener('DOMContentLoaded', () => {
  const img = document.querySelector('.details .car-img');
  if (img) {
    img.addEventListener('click', () => {
      const win = window.open();
      win.document.write('<img src="' + img.src + '" style="width:100%">');
    });
  }
});
</script>

<footer>
  <p>&copy; 2025 AutoService – Tous droits réservés</p>
</footer>