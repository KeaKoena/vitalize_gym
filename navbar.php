<!-- navbar.php -->
<link rel="stylesheet" href="style.css">
<?php $current_page = basename($_SERVER['PHP_SELF']); ?>
<nav class="navbar">
  <ul>
    <li><a href="index.php" class="<?= $current_page=='index.php'?'active':'' ?>">Home</a></li>
    <li><a href="view_programs.php" class="<?= $current_page=='view_programs.php'?'active':'' ?>">Programs</a></li>
    <li><a href="enrol_gymnast.php" class="<?= $current_page=='enrol_gymnast.php'?'active':'' ?>">Enrolment</a></li>
    <li><a href="attendance.php" class="<?= $current_page=='attendance.php'?'active':'' ?>">Attendance</a></li>
    <li><a href="progress.php" class="<?= $current_page=='progress.php'?'active':'' ?>">Progress</a></li>
    <li><a href="notifications.php" class="<?= $current_page=='notifications.php'?'active':'' ?>">Notifications</a></li>
  </ul>
</nav>
