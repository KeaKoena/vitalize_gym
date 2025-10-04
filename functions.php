<?php
if (session_status() === PHP_SESSION_NONE) session_start();

function flash($type, $msg){ $_SESSION['flash'][]=['type'=>$type,'msg'=>$msg]; }
function render_flashes(){
  if (empty($_SESSION['flash'])) return;
  foreach($_SESSION['flash'] as $f){
    $cls = $f['type']==='error'?'alert error':'alert success';
    echo "<div class='$cls'>".htmlspecialchars($f['msg'])."</div>";
  }
  unset($_SESSION['flash']);
}
function sanitize($s){ return trim(filter_var($s, FILTER_SANITIZE_SPECIAL_CHARS)); }
function valid_skill($s){ return in_array($s, ['Beginner','Intermediate','Advanced'], true); }

// PROGRAMS
function get_programs($conn, $search="", $skill=""){
  $sql = "SELECT p.*,
            (SELECT COUNT(*) FROM enrolments e WHERE e.program_id=p.id) AS enrolled_count
          FROM programs p WHERE 1";
  $types = ""; $params = [];
  if($search!==""){ $sql.=" AND p.name LIKE ?"; $types.="s"; $params[]="%$search%"; }
  if($skill!==""){ $sql.=" AND p.skill = ?";   $types.="s"; $params[]=$skill; }
  $sql.=" ORDER BY p.name ASC";
  $stmt=$conn->prepare($sql);
  if($types!=="") $stmt->bind_param($types, ...$params);
  $stmt->execute(); return $stmt->get_result();
}
function add_program($conn,$name,$description,$coach,$contact,$duration,$skill){
  if(!$name||!$coach||!$contact||!is_numeric($duration)||$duration<=0||!valid_skill($skill)){
    flash('error','Please fill all required fields correctly.'); return false;
  }
  $stmt=$conn->prepare("INSERT INTO programs(name,description,coach,contact,duration_weeks,skill) VALUES(?,?,?,?,?,?)");
  $stmt->bind_param("ssssis",$name,$description,$coach,$contact,$duration,$skill);
  $ok=$stmt->execute(); $ok?flash('success','Program added.') : flash('error','Add failed.'); return $ok;
}
function update_program($conn,$id,$name,$description,$coach,$contact,$duration,$skill){
  $stmt=$conn->prepare("UPDATE programs SET name=?,description=?,coach=?,contact=?,duration_weeks=?,skill=? WHERE id=?");
  $stmt->bind_param("ssssisi",$name,$description,$coach,$contact,$duration,$skill,$id);
  $ok=$stmt->execute(); $ok?flash('success','Program updated.') : flash('error','Update failed.'); return $ok;
}
function delete_program_by_id($conn,$id){
  $stmt=$conn->prepare("DELETE FROM programs WHERE id=?"); $stmt->bind_param("i",$id);
  $ok=$stmt->execute(); $ok?flash('success','Program deleted.') : flash('error','Delete failed.'); return $ok;
}

// ENROL
function enrol_gymnast($conn,$program_id,$name,$age,$skill){
  if(!$program_id || !$name || !is_numeric($age) || $age<4 || !valid_skill($skill)){
    flash('error','Invalid enrolment details.'); return false;
  }
  $stmt=$conn->prepare("INSERT INTO gymnasts(name,age,skill) VALUES(?,?,?)");
  $stmt->bind_param("sis",$name,$age,$skill);
  if(!$stmt->execute()){ flash('error','Could not save gymnast.'); return false; }
  $gymnast_id=$conn->insert_id;
  $stmt2=$conn->prepare("INSERT INTO enrolments(gymnast_id,program_id) VALUES(?,?)");
  $stmt2->bind_param("ii",$gymnast_id,$program_id);
  $ok=$stmt2->execute();
  if($ok){
    $msg="New gymnast $name enrolled in program ID $program_id";
    $n=$conn->prepare("INSERT INTO notifications(message) VALUES(?)"); $n->bind_param("s",$msg); $n->execute();
    flash('success','Enrolment successful.');
  } else flash('error','Enrolment failed.');
  return $ok;
}

// ATTENDANCE
function mark_attendance($conn,$gymnast_id,$program_id,$week,$present){
  $stmt=$conn->prepare("REPLACE INTO attendance(gymnast_id,program_id,week,present) VALUES(?,?,?,?)");
  $stmt->bind_param("iiii",$gymnast_id,$program_id,$week,$present);
  $ok=$stmt->execute(); $ok?flash('success','Attendance saved.') : flash('error','Attendance failed.'); return $ok;
}

// PROGRESS
function save_progress($conn,$gymnast_id,$program_id,$score,$notes){
  if(!is_numeric($score) || $score<0 || $score>100){ flash('error','Score must be 0-100.'); return false; }
  $stmt=$conn->prepare("INSERT INTO progress(gymnast_id,program_id,score,notes) VALUES(?,?,?,?)");
  $stmt->bind_param("iiis",$gymnast_id,$program_id,$score,$notes);
  $ok=$stmt->execute(); $ok?flash('success','Progress saved.') : flash('error','Progress failed.'); return $ok;
}
function calc_progress($conn,$gymnast_id,$program_id){
  $a=$conn->prepare("SELECT SUM(present) AS attended, COUNT(*) AS total FROM attendance WHERE gymnast_id=? AND program_id=?");
  $a->bind_param("ii",$gymnast_id,$program_id); $a->execute(); $ar=$a->get_result()->fetch_assoc();
  $att_pct = ($ar['total']??0)>0 ? ($ar['attended']*100/ max(1,$ar['total'])) : 0;
  $s=$conn->prepare("SELECT AVG(score) AS avgscore FROM progress WHERE gymnast_id=? AND program_id=?");
  $s->bind_param("ii",$gymnast_id,$program_id); $s->execute(); $sr=$s->get_result()->fetch_assoc();
  $score_pct = $sr['avgscore']??0;
  return round(($att_pct*0.5)+($score_pct*0.5));
}
?>
