<?php

if ($conn==null)
{
   header('Content-Type: application/javascript; charset=utf-8');
   $conn = require('db_conn.php');
   $auto_manage = true;
}

date_default_timezone_set ('Asia/Jerusalem');
$days_db = array('שני', 'שלישי', 'רביעי', 'חמישי', 'שישי', 'שבת', 'ראשון');
$month_db = array('ינואר', 'פברואר', 'מרץ', 'אפריל', 'מאי', 'יוני', 'יולי', 'אוגוסט', 'סמפטמבר', 'אוקטובר', 'נובמבר', 'דצמבר');
$date_str = date('יום '.$days_db[intval(Date('N'))-1].', j ב'.$month_db[intval(Date('n'))-1].' Y');
$day = Date('Y-m-d');

$id=-1;
$status = -1;
$lock = 0;

if (isset($_GET['delete'])) {
  setcookie('id', null, -1, '/');
} else {
  if (isset($_GET['id'])) { $id = intval($_GET['id']); }
  elseif (isset($_COOKIE['id'])) { $id = intval($_COOKIE['id']); }
}

$result = $conn->query('select permission_request from users where id='.$id)->fetch_row();
if ($result!=null)
{
    $settings = array(permission_request=>$result[0]==1);
    setcookie('id', $id, time() + (86400 * 30), '/'); // 86400 = 1 day

    $result = $conn->query('select status from reports where u_id='.$id.' and day="'.$day.'" and active=1')->fetch_row();
    if ($result!=null) $status=intval($result[0]);
    
    if ($conn->query('select day from locked where day="'.$day.'"')->num_rows == 1) $lock = 1;
    
} else {
    $id=-1;
}

$result = array(id=>$id, date_str=>$date_str, day=>$day, status=>$status, lock=>$lock, settings=>$settings, ver=>1.0);

if ($auto_manage)
{
  $conn->close();
  echo $_GET['callback'].'('.json_encode($result).');';
}

return $result;
?>