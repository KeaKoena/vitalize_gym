<?php
include 'db.php'; include 'functions.php'; include 'navbar.php';
$id = (int)($_GET['id'] ?? 0);
$stmt=$conn->prepare("SELECT * FROM programs WHERE id=?"); $stmt->bind_param("i",$id); $stmt->execute();
$program=$stmt->get_result()->fetch_assoc();
if(!$program){ flash('error','Program not found.'); header("Location: view_programs.php"); exit; }
if($_SERVER['REQUEST_METHOD']==='POST'){
  $ok = update_program($conn,$id,
    sanitize($_POST['name']??''),
    sanitize($_POST['description']??''),
    sanitize($_POST['coach']??''),
    sanitize($_POST['contact']??''),
    (int)($_POST['duration']??0),
    sanitize($_POST['skill']??'')
  );
  if($ok){ header("Location: view_programs.php"); exit; }
}
?>
<div class="container">
  <h2>Edit Program</h2>
  <?php render_flashes(); ?>
  <form method="post">
    <input name="name" value="<?= htmlspecialchars($program['name']) ?>" required>
    <textarea name="description"><?= htmlspecialchars($program['description']) ?></textarea>
    <input name="coach" value="<?= htmlspecialchars($program['coach']) ?>" required>
    <input name="contact" value="<?= htmlspecialchars($program['contact']) ?>" required>
    <input type="number" name="duration" value="<?= (int)$program['duration_weeks'] ?>" min="1" required>
    <select name="skill" required>
      <option <?= $program['skill']==='Beginner'?'selected':'' ?>>Beginner</option>
      <option <?= $program['skill']==='Intermediate'?'selected':'' ?>>Intermediate</option>
      <option <?= $program['skill']==='Advanced'?'selected':'' ?>>Advanced</option>
    </select>
    <button type="submit">Update</button>
  </form>
</div>
