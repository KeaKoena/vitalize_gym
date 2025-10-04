<?php
include 'db.php'; include 'functions.php';
$id=(int)($_GET['id']??0);
if($id>0) delete_program_by_id($conn,$id);
header("Location: view_programs.php"); exit;
