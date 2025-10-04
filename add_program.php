<?php
include 'db.php'; include 'functions.php'; include 'navbar.php';
if($_SERVER['REQUEST_METHOD']==='POST'){
  $ok = add_program($conn,
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
  <h2>Add Program</h2>
  <?php render_flashes(); ?>
  <form method="post">
    <input name="name" placeholder="Program Name" required>
    <textarea name="description" placeholder="Description"></textarea>
    <input name="coach" placeholder="Coach Name" required>
    <input name="contact" placeholder="Contact (email/phone)" required>
    <input type="number" name="duration" placeholder="Duration (weeks)" min="1" required>
    <select name="skill" required>
      <option>Beginner</option><option>Intermediate</option><option>Advanced</option>
    </select>
    <button type="submit">Save</button>
  </form>
</div>
