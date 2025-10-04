<?php
include 'db.php'; include 'functions.php'; include 'navbar.php';
$search = $_GET['search'] ?? ''; $skill = $_GET['skill'] ?? '';
$result = get_programs($conn, $search, $skill);
?>
<div class="container">
  <h2>Programs</h2>
  <?php render_flashes(); ?>
  <form class="searchbar" method="get">
    <input type="text" name="search" value="<?= htmlspecialchars($search) ?>" placeholder="Search by name...">
    <select name="skill">
      <option value="">All Skill Levels</option>
      <option <?= $skill==='Beginner'?'selected':'' ?>>Beginner</option>
      <option <?= $skill==='Intermediate'?'selected':'' ?>>Intermediate</option>
      <option <?= $skill==='Advanced'?'selected':'' ?>>Advanced</option>
    </select>
    <button type="submit">Filter</button>
    <a class="btn" href="add_program.php">+ Add Program</a>
  </form>
  <table>
    <tr><th>Name</th><th>Coach</th><th>Duration</th><th>Skill</th><th>Enrolled</th><th>Actions</th></tr>
    <?php while($row=$result->fetch_assoc()): ?>
      <tr>
        <td><?= htmlspecialchars($row['name']) ?></td>
        <td><?= htmlspecialchars($row['coach']) ?></td>
        <td><?= (int)$row['duration_weeks'] ?> weeks</td>
        <td><?= htmlspecialchars($row['skill']) ?></td>
        <td><?= (int)$row['enrolled_count'] ?></td>
        <td>
          <a href="edit_program.php?id=<?= (int)$row['id'] ?>">Edit</a> |
          <a href="delete_program.php?id=<?= (int)$row['id'] ?>" onclick="return confirm('Delete this program?')">Delete</a>
        </td>
      </tr>
    <?php endwhile; ?>
  </table>
</div>
