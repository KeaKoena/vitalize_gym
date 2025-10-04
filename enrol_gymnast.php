<?php
include 'db.php'; include 'functions.php'; include 'navbar.php';
$programs = get_programs($conn);
if($_SERVER['REQUEST_METHOD']==='POST'){
  $ok = enrol_gymnast($conn,(int)($_POST['program_id']??0),sanitize($_POST['name']??''),(int)($_POST['age']??0),sanitize($_POST['skill']??''));
  if($ok){ header("Location: notifications.php"); exit; }
}
?>
<div class="container">
  <h2>Enrol Gymnast</h2>
  <?php render_flashes(); ?>
  <form method="post">
    <label>Program</label>
    <select name="program_id" required>
      <?php while($p=$programs->fetch_assoc()): ?>
        <option value="<?= (int)$p['id'] ?>"><?= htmlspecialchars($p['name']) ?> (<?= htmlspecialchars($p['skill']) ?>)</option>
      <?php endwhile; ?>
    </select>
    <input name="name" placeholder="Gymnast Name" required>
    <input type="number" name="age" placeholder="Age (>=4)" min="4" required>
    <select name="skill" required>
      <option>Beginner</option><option>Intermediate</option><option>Advanced</option>
    </select>
    <button type="submit">Enrol</button>
  </form>
</div>
