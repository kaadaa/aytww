<?php
 require './dbcon.php';
 switch ($_POST['action']){
  
  case 'login':
   $pass = hash('sha256', $_POST['password']);
   $result = mysqli_query($connect,"SELECT * FROM users WHERE username='{$_POST['username']}' AND password='{$pass}' LIMIT 1");
   $datacnt = mysqli_num_rows($result);
   if ($datacnt == 1) echo json_encode(mysqli_fetch_assoc($result));
   else{
    $result = mysqli_query($connect,"SELECT * FROM users WHERE username='{$_POST['username']}' LIMIT 1");
    $datacnt = mysqli_num_rows($result);
    if ($datacnt == 1) echo 'err2';
    else echo 'err1';
   }
  break;
  
  case 'changepass':
   $result = mysqli_query($connect,"SELECT * FROM users WHERE name = '{$_POST['user']}' LIMIT 1");
   $data = mysqli_fetch_assoc($result);
    if ($data['password'] != $_POST['oldpass']) echo 'err1';
    else{
     mysqli_query($connect,"UPDATE users SET password='{$_POST['newpass1']}' WHERE name = '{$_POST['user']}'");
     echo 'ok';
    }
  break;
  
  case 'permalogin':
   $result = mysqli_query($connect,"SELECT * FROM users WHERE iduser={$_POST['iduser']} LIMIT 1");
   echo json_encode(mysqli_fetch_assoc($result));
  break;
 
  case 'getprodlist':
   $scan = false;
   $dateto = $_POST['dateto']." 23:59:59";                  
   $sql = "SELECT SQL_CALC_FOUND_ROWS scans.idscan, products.product AS product, scans.pserial AS pserial, scans.state AS state, DATE_FORMAT(scans.sdate,'%e.%c.%Y %T') AS sdate, products.description AS description FROM scans JOIN products ON scans.product=products.idproducts WHERE (scans.sdate BETWEEN '{$_POST['datefrom']}' AND '{$dateto}')";
   if (isset($_POST['product']) && $_POST['product'] !== ''){
    if (strpos($_POST['product'], '#') !== false){
     $scan = true;
     $tmp = explode('#',$_POST['product']);
     $sqlscan = "SELECT SQL_CALC_FOUND_ROWS scans.idscan, products.product AS product, scans.pserial, scans.state, DATE_FORMAT(scans.sdate,'%e.%c.%Y %T') AS sdate, products.description AS description FROM scans JOIN products ON scans.product=products.idproducts WHERE products.product LIKE '%{$tmp[0]}%' AND scans.pserial LIKE '%{$tmp[1]}%' AND scans.idscan IN (SELECT MAX(scans.idscan) FROM scans GROUP BY scans.pserial, scans.product) ORDER BY scans.sdate ASC LIMIT 1";
    }
    else
     $sql .= " AND (products.product LIKE '%{$_POST['product']}%' OR scans.pserial LIKE '%{$_POST['product']}%')";
   }
   if (isset($_POST['operator']) && $_POST['operator'] != 0)
    $sql .= " AND scans.operator = {$_POST['operator']}"; 
   if (isset($_POST['ok']) && $_POST['ok'] === 'true') 
    $sql .= " AND scans.state='O'";  
   if (isset($_POST['repair']) && $_POST['repair'] === 'true') 
    $sql .= " AND scans.state='R'";
   if (isset($_POST['decide']) && $_POST['decide'] === 'true')  
    $sql .= " AND scans.state='D'";
   if (isset($_POST['nok']) && $_POST['nok'] === 'true')  
    $sql .= " AND scans.state='N'";
   if (isset($_POST['scrap']) && $_POST['scrap'] === 'true')  
    $sql .= " AND scans.state='S'";
   if (isset($_POST['fb']) && $_POST['fb'] === 'true')
    $sql .= " AND LEFT(products.type, 2) = 'FB'";
   if (isset($_POST['fc']) && $_POST['fc'] === 'true')
    $sql .= " AND LEFT(products.type, 2) = 'FC'";
   if (isset($_POST['rb']) && $_POST['rb'] === 'true')
    $sql .= " AND LEFT(products.type, 2) = 'RB'";
   if (isset($_POST['rc']) && $_POST['rc'] === 'true')
    $sql .= " AND LEFT(products.type, 2) = 'RC'"; 
   if (isset($_POST['am']) && $_POST['am'] === 'true')    
    $sql .= " AND (EXTRACT(HOUR_SECOND FROM scans.sdate) <= 135500)"; 
   if (isset($_POST['pm']) && $_POST['pm'] === 'true')    
    $sql .= " AND (EXTRACT(HOUR_SECOND FROM scans.sdate) > 135500)"; 
   $sql .= " AND scans.idscan IN (SELECT MAX(scans.idscan) FROM scans WHERE (scans.sdate BETWEEN '{$_POST['datefrom']}' AND '{$dateto}') GROUP BY scans.pserial, scans.product)";
   $sql .= " ORDER BY scans.sdate ASC LIMIT 500 OFFSET {$_POST['offset']}";
   if ($scan == true) $res = mysqli_query($connect,$sqlscan);
   else $res = mysqli_query($connect,$sql);
   $cnt = mysqli_fetch_assoc(mysqli_query($connect,"SELECT FOUND_ROWS() AS cnt"));       
   $count = $cnt['cnt'];
   $rows = array();
   while ($r = mysqli_fetch_assoc($res))
    $rows[] = $r;
   $rows[] = $count;
   //$time = microtime(true) - $_SERVER["REQUEST_TIME_FLOAT"];
   //$rows[] = $time;
   echo json_encode($rows);
  break;
  
  case 'getscan':  
   $res = mysqli_query($connect,"SELECT scans.idscan, DATE_FORMAT(scans.sdate,'%e.%c.%Y %T') AS sdate, scans.gate AS gate, operators.name as operator, scans.state FROM scans JOIN operators ON operator = idoperator JOIN products ON scans.product = products.idproducts WHERE products.product = '{$_POST['product']}' AND scans.pserial = '{$_POST['pserial']}' ORDER BY scans.sdate ASC");
   $rows = array();
   while ($r = mysqli_fetch_assoc($res))
    $rows[] = $r;
   echo json_encode($rows);
  break;
  
  case 'getusers':
   $res = mysqli_query($connect,"SELECT * FROM operators ORDER BY login ASC");
   $rows = array();
   while ($r = mysqli_fetch_assoc($res))
    $rows[] = $r;
   echo json_encode($rows);
  break;  
  
  case 'getvusers':
   $res = mysqli_query($connect,"SELECT iduser, username, name, rights FROM users ORDER BY username ASC");
   $rows = array();
   while ($r = mysqli_fetch_assoc($res))
    $rows[] = $r;
   echo json_encode($rows);
  break;   
  
  case 'adduser':  
   if (mysqli_query($connect,"INSERT INTO operators VALUES (NULL, '{$_POST['login']}','{$_POST['name']}')")) echo 'ok';
   else echo mysqli_error($connect);
  break; 

  case 'addvuser':  
   $pass = hash('sha256', $_POST['password']);
   if (mysqli_query($connect,"INSERT INTO users VALUES (NULL, '{$_POST['username']}','{$pass}','{$_POST['name']}','{$_POST['rights']}')")) echo 'ok';
   else echo mysqli_error($connect);
  break; 
  
  case 'edituser':  
   if (mysqli_query($connect,"UPDATE operators SET login='{$_POST['login']}',name='{$_POST['name']}' WHERE idoperator={$_POST['idoperator']}")) echo 'ok';
   else echo mysqli_error($connect); 
  break; 
    
  case 'editvuser':
   if ($_POST['password'] == '') $password = '';
   else{
    $pass = hash('sha256', $_POST['password']);
    $password = "password='{$pass}',";
   } 
   if (mysqli_query($connect,"UPDATE users SET username='{$_POST['username']}',name='{$_POST['name']}',$password rights='{$_POST['rights']}' WHERE iduser={$_POST['iduser']}")) echo 'ok';
   else echo mysqli_error($connect);
  break; 
  
  case 'deluser':  
   if (mysqli_query($connect,"DELETE FROM operators WHERE idoperator = {$_POST['idoperator']}")) echo 'ok';
   else echo mysqli_error($connect);
  break;
    
  case 'delvuser':  
   if (mysqli_query($connect,"DELETE FROM users WHERE iduser={$_POST['iduser']}")) echo 'ok';
   else echo mysqli_error($connect);
  break;
  
  case 'getscraps':
   $dateto = $_POST['dateto']." 23:59:59";
   $sql = "SELECT SQL_CALC_FOUND_ROWS defects.iddefect, DATE_FORMAT(scans.sdate,'%e.%c.%Y %T') AS sdate, LEFT(products.type, 2) AS type, operators.name AS operator, noklist.name AS name FROM defects JOIN scans ON defects.idscan = scans.idscan JOIN products ON scans.product = products.idproducts JOIN operators ON scans.operator = operators.idoperator JOIN noklist ON defects.idnok = noklist.idnok WHERE (sdate BETWEEN '{$_POST['datefrom']}' AND '{$dateto}')";
   if (isset($_POST['scfb']) && $_POST['scfb'] === 'true')
    $sql .= " AND LEFT(products.type, 2) = 'FB'";
   if (isset($_POST['scfc']) && $_POST['scfc'] === 'true')
    $sql .= " AND LEFT(products.type, 2) = 'FC'";
   if (isset($_POST['scrb']) && $_POST['scrb'] === 'true')
    $sql .= " AND LEFT(products.type, 2) = 'RB'";
   if (isset($_POST['scrc']) && $_POST['scrc'] === 'true')
    $sql .= " AND LEFT(products.type, 2) = 'RC'";   
   if (isset($_POST['scam']) && $_POST['scam'] === 'true')    
    $sql .= " AND (EXTRACT(HOUR_SECOND FROM sdate) <= 135500)"; 
   if (isset($_POST['scpm']) && $_POST['scpm'] === 'true')    
    $sql .= " AND (EXTRACT(HOUR_SECOND FROM sdate) > 135500)"; 
   $sql .= " ORDER BY scans.sdate ASC LIMIT 1000 OFFSET {$_POST['offset']}";    
   $res = mysqli_query($connect,$sql);
   $cnt = mysqli_fetch_assoc(mysqli_query($connect,"SELECT FOUND_ROWS() AS cnt"));       
   $count = $cnt['cnt'];   
   $rows = array();
   while ($r = mysqli_fetch_assoc($res))
    $rows[] = $r;
   $rows[] = $count;
   echo json_encode($rows);   
  break;
  
  case 'delfile':
   unlink($_POST['file']);
  break;
  
  case 'getnokchart':
   $dateto = $_POST['dateto']." 23:59:59";
   $sql = "SELECT noklist.name AS name, DATE_FORMAT(scans.sdate,'%e.%c.%Y %T') AS sdate, COUNT(defects.idnok) AS cnt FROM defects JOIN noklist ON defects.idnok = noklist.idnok JOIN scans ON defects.idscan = scans.idscan JOIN products ON scans.product = products.idproducts WHERE (scans.sdate BETWEEN '{$_POST['datefrom']}' AND '{$dateto}')";
   if (isset($_POST['scfb']) && $_POST['scfb'] === 'true')
    $sql .= " AND LEFT(products.type, 2) = 'FB'";
   if (isset($_POST['scfc']) && $_POST['scfc'] === 'true')
    $sql .= " AND LEFT(products.type, 2) = 'FC'";
   if (isset($_POST['scrb']) && $_POST['scrb'] === 'true')
    $sql .= " AND LEFT(products.type, 2) = 'RB'";
   if (isset($_POST['scrc']) && $_POST['scrc'] === 'true')
    $sql .= " AND LEFT(products.type, 2) = 'RC'";   
   if (isset($_POST['scam']) && $_POST['scam'] === 'true')    
    $sql .= " AND (EXTRACT(HOUR_SECOND FROM sdate) <= 135500)"; 
   if (isset($_POST['scpm']) && $_POST['scpm'] === 'true')    
    $sql .= " AND (EXTRACT(HOUR_SECOND FROM sdate) > 135500)"; 
   $sql .= " GROUP by defects.idnok";     
   $res = mysqli_query($connect,$sql);
   $rows = array();
   while ($r = mysqli_fetch_assoc($res))
    $rows[] = $r;
   echo json_encode($rows);    
  break;
  
  case 'getoperatorchart':
   $dateto = $_POST['dateto']." 23:59:59";
   $sql = "SELECT operators.name AS name, DATE_FORMAT(scans.sdate,'%e.%c.%Y %T') AS sdate, COUNT(defects.iddefect) AS cnt FROM defects JOIN scans ON defects.idscan = scans.idscan JOIN products ON scans.product = products.idproducts JOIN operators ON scans.operator = operators.idoperator WHERE (scans.sdate BETWEEN '{$_POST['datefrom']}' AND '{$dateto}')";
   if (isset($_POST['scfb']) && $_POST['scfb'] === 'true')
    $sql .= " AND LEFT(products.type, 2) = 'FB'";
   if (isset($_POST['scfc']) && $_POST['scfc'] === 'true')
    $sql .= " AND LEFT(products.type, 2) = 'FC'";
   if (isset($_POST['scrb']) && $_POST['scrb'] === 'true')
    $sql .= " AND LEFT(products.type, 2) = 'RB'";
   if (isset($_POST['scrc']) && $_POST['scrc'] === 'true')
    $sql .= " AND LEFT(products.type, 2) = 'RC'";   
   if (isset($_POST['scam']) && $_POST['scam'] === 'true')    
    $sql .= " AND (EXTRACT(HOUR_SECOND FROM sdate) <= 135500)"; 
   if (isset($_POST['scpm']) && $_POST['scpm'] === 'true')    
    $sql .= " AND (EXTRACT(HOUR_SECOND FROM sdate) > 135500)"; 
   $sql .= " GROUP by scans.operator";  
   $res = mysqli_query($connect,$sql);
   $rows = array();  
   while ($r = mysqli_fetch_assoc($res))
    $rows[] = $r;
   echo json_encode($rows);    
  break;
  
  case 'getppm':
   $dateto = $_POST['dateto']." 23:59:59";
   $sql = "SELECT (SELECT COUNT(scans.idscan) FROM scans JOIN products ON scans.product = products.idproducts WHERE (sdate BETWEEN '{$_POST['datefrom']}' AND '{$dateto}') AND idscan IN (SELECT MAX(idscan) FROM scans WHERE (scans.sdate BETWEEN '{$_POST['datefrom']}' AND '{$dateto}') GROUP BY pserial, product)";
   if (isset($_POST['scfb']) && $_POST['scfb'] === 'true')
    $sql .= " AND LEFT(products.type, 2) = 'FB'";
   if (isset($_POST['scfc']) && $_POST['scfc'] === 'true')
    $sql .= " AND LEFT(products.type, 2) = 'FC'";
   if (isset($_POST['scrb']) && $_POST['scrb'] === 'true')
    $sql .= " AND LEFT(products.type, 2) = 'RB'";
   if (isset($_POST['scrc']) && $_POST['scrc'] === 'true')
    $sql .= " AND LEFT(products.type, 2) = 'RC'";   
   if (isset($_POST['scam']) && $_POST['scam'] === 'true')    
    $sql .= " AND (EXTRACT(HOUR_SECOND FROM sdate) <= 135500)"; 
   if (isset($_POST['scpm']) && $_POST['scpm'] === 'true')    
    $sql .= " AND (EXTRACT(HOUR_SECOND FROM sdate) > 135500)";   
   $sql .= ") AS made, (SELECT COUNT(defects.iddefect) FROM defects JOIN scans on defects.idscan = scans.idscan JOIN products ON scans.product = products.idproducts WHERE (scans.sdate BETWEEN '{$_POST['datefrom']}' AND '{$dateto}')";
   if (isset($_POST['scfb']) && $_POST['scfb'] === 'true')
    $sql .= " AND LEFT(products.type, 2) = 'FB'";
   if (isset($_POST['scfc']) && $_POST['scfc'] === 'true')
    $sql .= " AND LEFT(products.type, 2) = 'FC'";
   if (isset($_POST['scrb']) && $_POST['scrb'] === 'true')
    $sql .= " AND LEFT(products.type, 2) = 'RB'";
   if (isset($_POST['scrc']) && $_POST['scrc'] === 'true')
    $sql .= " AND LEFT(products.type, 2) = 'RC'";   
   if (isset($_POST['scam']) && $_POST['scam'] === 'true')    
    $sql .= " AND (EXTRACT(HOUR_SECOND FROM scans.sdate) <= 135500)"; 
   if (isset($_POST['scpm']) && $_POST['scpm'] === 'true')    
    $sql .= " AND (EXTRACT(HOUR_SECOND FROM scans.sdate) > 135500)";
   $sql .= ") AS nok";     
   $res = mysqli_query($connect,$sql); 
   $rows = array();
   while ($r = mysqli_fetch_assoc($res))
    $rows[] = $r;
   echo json_encode($rows);
  break;  
  
  case 'getscrap_print':
   $dateto = $_POST['dateto']." 23:59:59";
   $sql = "SELECT noklist.idnok AS idnok, noklist.name AS name, COUNT(defects.idnok) AS cnt, SUM(CASE WHEN LEFT(products.type, 2)='FB' then 1 else 0 end) AS cntfb, SUM(CASE WHEN LEFT(products.type, 2)='FC' then 1 else 0 end) AS cntfc, SUM(CASE WHEN LEFT(products.type, 2)='RB' then 1 else 0 end) AS cntrb, SUM(CASE WHEN LEFT(products.type, 2)='RC' then 1 else 0 end) AS cntrc FROM defects JOIN noklist ON defects.idnok = noklist.idnok JOIN scans ON defects.idscan = scans.idscan JOIN products ON scans.product = products.idproducts WHERE (scans.sdate BETWEEN '{$_POST['datefrom']}' AND '{$dateto}')";
   if (isset($_POST['scam']) && $_POST['scam'] === 'true')    
    $sql .= " AND (EXTRACT(HOUR_SECOND FROM scans.sdate) <= 135500)"; 
   if (isset($_POST['scpm']) && $_POST['scpm'] === 'true')    
    $sql .= " AND (EXTRACT(HOUR_SECOND FROM scans.sdate) > 135500)";
   $sql .= " GROUP by defects.idnok ORDER BY cnt DESC";     
   $res = mysqli_query($connect,$sql);
   $rows = array();
   while ($r = mysqli_fetch_assoc($res))
    $rows[] = $r;   
   $sqlc = "SELECT COUNT(scans.idscan) AS cnt FROM scans JOIN products ON scans.product = products.idproducts WHERE (sdate BETWEEN '{$_POST['datefrom']}' AND '{$dateto}') AND idscan IN (SELECT MAX(idscan) FROM scans WHERE (scans.sdate BETWEEN '{$_POST['datefrom']}' AND '{$dateto}') GROUP BY pserial, product)";
   if (isset($_POST['scam']) && $_POST['scam'] === 'true')    
    $sqlc .= " AND (EXTRACT(HOUR_SECOND FROM scans.sdate) <= 135500)"; 
   if (isset($_POST['scpm']) && $_POST['scpm'] === 'true')    
    $sqlc .= " AND (EXTRACT(HOUR_SECOND FROM scans.sdate) > 135500)";
   $resc = mysqli_query($connect,$sqlc);
   $rows[] = mysqli_fetch_assoc($resc);
   echo json_encode($rows);  
  break;  
  
  case 'getloglist':
   $dateto = $_POST['dateto']." 23:59:59";                  
   $sql = "SELECT operatorlog.operator AS idoperator, operators.name AS name, COUNT( IF( operatorlog.action = 'S', 1, NULL ) ) AS scans FROM operatorlog JOIN operators ON operatorlog.operator=operators.idoperator WHERE (operatorlog.time BETWEEN '{$_POST['datefrom']}' AND '{$dateto}') GROUP BY operatorlog.operator ORDER BY operators.name ASC";
   $res = mysqli_query($connect,$sql);      
   $rows = array();
   while ($r = mysqli_fetch_assoc($res))
    $rows[] = $r;
   echo json_encode($rows);
  break;
  
  case 'getlog':
   $name = mysqli_fetch_assoc(mysqli_query($connect,"SELECT name FROM operators WHERE idoperator = {$_POST['operator']} LIMIT 1"));
   $dateto = $_POST['dateto']." 23:59:59";      
   $res = mysqli_query($connect,"SELECT DATE_FORMAT(time,'%e.%c.%Y %T') AS time, gate, action FROM operatorlog WHERE operator = {$_POST['operator']} AND (time BETWEEN '{$_POST['datefrom']}' AND '{$dateto}') AND (action <> 'S') ORDER BY time ASC");
   $rows = array();
   $rows[] = $name;
   while ($r = mysqli_fetch_assoc($res))
    $rows[] = $r;
   echo json_encode($rows);
  break;
  
  case 'getstats':
   $data = mysqli_query($connect,"SELECT products.type AS typ, SUM(IF(scans.state = 'O', 1, 0)) AS O, SUM(IF(scans.state = 'R', 1, 0)) AS R, SUM(IF(scans.state = 'D', 1, 0)) AS D, SUM(IF(scans.state = 'N', 1, 0)) AS N, SUM(IF(scans.state = 'S', 1, 0)) AS S FROM scans JOIN products ON scans.product=products.idproducts WHERE DATE(scans.sdate)='{$_POST['statsdate']}' AND scans.idscan IN (SELECT MAX(scans.idscan) FROM scans WHERE DATE(scans.sdate)='{$_POST['statsdate']}' GROUP BY scans.product, scans.pserial) GROUP BY products.type ORDER BY products.type ASC");
   if (mysqli_num_rows($data) > 0){
    $rows = array();
    while ($r = mysqli_fetch_assoc($data))
     $rows[] = $r; 
    echo json_encode($rows);
   }
  break;
    
  default:
   printf("400: Bad Request");
 } 
?>