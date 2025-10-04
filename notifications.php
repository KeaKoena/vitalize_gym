<?php
include 'db.php'; include 'functions.php'; include 'navbar.php';
$notes=$conn->query("SELECT * FROM notifications ORDER BY created_at DESC");
?>
<div class="container">
  <h2>Coach Notifications</h2>
  <ul>
    <?php while($n=$notes->fetch_assoc()): ?>
      <li><?= htmlspecialchars($n['message']) ?> — <small><?= htmlspecialchars($n['created_at']) ?></small></li>
    <?php endwhile; ?>
  </ul>
</div>
