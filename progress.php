<?php
include 'db.php'; include 'functions.php'; include 'navbar.php';
// For adding new progress entry
if($_SERVER['REQUEST_METHOD']==='POST'){
  save_progress($conn,(int)$_POST['gymnast_id'],(int)$_POST['program_id'],(int)$_POST['score'],sanitize($_POST['notes']??''));
}
$sql="SELECT e.program_id, p.name AS program_name, g.id AS gymnast_id, g.name AS gymnast_name
      FROM enrolments e JOIN programs p ON p.id=e.program_id JOIN gymnasts g ON g.id=e.gymnast_id ORDER BY p.name,g.name";
$list=$conn->query($sql);
?>
<div class="container">
  <h2>Progress</h2>
  <?php render_flashes(); ?>
  <table>
    <tr><th>Gymnast</th><th>Program</th><th>Progress</th><th>Add Score</th></tr>
    <?php while($row=$list->fetch_assoc()): 
      $pct = calc_progress($conn,(int)$row['gymnast_id'],(int)$row['program_id']); ?>
      <tr>
        <td><?= htmlspecialchars($row['gymnast_name']) ?></td>
        <td><?= htmlspecialchars($row['program_name']) ?></td>
        <td><div class="progress-bar"><div class="progress-fill" style="width: <?= $pct ?>%;"><?= $pct ?>%</div></div></td>
        <td>
          <form method="post" style="display:flex; gap:6px; align-items:center">
            <input type="hidden" name="gymnast_id" value="<?= (int)$row['gymnast_id'] ?>">
            <input type="hidden" name="program_id" value="<?= (int)$row['program_id'] ?>">
            <input type="number" name="score" min="0" max="100" placeholder="Score" required>
            <input name="notes" placeholder="Notes">
            <button type="submit">Save</button>
          </form>
        </td>
      </tr>
    <?php endwhile; ?>
  </table>
</div>
