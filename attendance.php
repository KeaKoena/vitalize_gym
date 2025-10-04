<?php
include 'db.php'; include 'functions.php'; include 'navbar.php';
if($_SERVER['REQUEST_METHOD']==='POST'){
  mark_attendance($conn,(int)$_POST['gymnast_id'],(int)$_POST['program_id'],(int)$_POST['week'], isset($_POST['present'])?1:0);
}
$sql="SELECT e.program_id, p.name AS program_name, g.id AS gymnast_id, g.name AS gymnast_name
      FROM enrolments e JOIN programs p ON p.id=e.program_id JOIN gymnasts g ON g.id=e.gymnast_id ORDER BY p.name,g.name";
$list=$conn->query($sql);
?>
<div class="container">
  <h2>Attendance</h2>
  <?php render_flashes(); ?>
  <table>
    <tr><th>Program</th><th>Gymnast</th><th>Week</th><th>Present?</th><th>Save</th></tr>
    <?php while($row=$list->fetch_assoc()): ?>
      <tr>
        <form method="post">
          <td><?= htmlspecialchars($row['program_name']) ?></td>
          <td><?= htmlspecialchars($row['gymnast_name']) ?></td>
          <td><input type="number" name="week" min="1" required></td>
          <td><input type="checkbox" name="present"></td>
          <td>
            <input type="hidden" name="program_id" value="<?= (int)$row['program_id'] ?>">
            <input type="hidden" name="gymnast_id" value="<?= (int)$row['gymnast_id'] ?>">
            <button type="submit">Save</button>
          </td>
        </form>
      </tr>
    <?php endwhile; ?>
  </table>
</div>
