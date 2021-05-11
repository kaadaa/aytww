<?php
 require './dbcon.php';
 switch ($_POST['action']){
  
  case 'login':
   $result = mysqli_query($connect,"SELECT * FROM operators WHERE idoperator={$_POST['idoperator']} LIMIT 1");
   $datacnt = mysqli_num_rows($result);
   if ($datacnt == 1) echo json_encode(mysqli_fetch_assoc($result));
  break;
 
  case 'getprodlist':
   $dateto = $_POST['dateto']." 23:59:59";                  
   $sql = "SELECT SQL_CALC_FOUND_ROWS scans.idscan, products.product AS product, scans.pserial, scans.state, DATE_FORMAT(scans.sdate,'%e.%c.%Y %T') AS sdate, products.description AS description FROM scans JOIN products ON scans.product=products.idproducts WHERE (scans.sdate BETWEEN '{$_POST['datefrom']}' AND '{$dateto}')";
   if (isset($_POST['product']) && $_POST['product'] !== ''){
    if (strpos($_POST['product'], '#') !== false){
     $tmp = explode('#',$_POST['product']);
     $sql .= " AND (products.product LIKE '%{$tmp[0]}%' AND scans.pserial LIKE '%{$tmp[1]}%')";
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
   $res = mysqli_query($connect,$sql);
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
   $res = mysqli_query($connect,"SELECT idscan, DATE_FORMAT(sdate,'%e.%c.%Y %T') AS sdate, gate, operators.name as operator, state FROM scans JOIN operators ON operator = idoperator JOIN products ON scans.product = products.idproducts WHERE products.product = '{$_POST['product']}' AND pserial = '{$_POST['pserial']}' ORDER BY scans.sdate ASC");
   $rows = array();
   while ($r = mysqli_fetch_assoc($res))
    $rows[] = $r;
   echo json_encode($rows);
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
  
  case 'getloglist':
   $dateto = $_POST['dateto']." 23:59:59";                  
   $sql = "SELECT operatorlog.operator AS idoperator, operators.name AS name, COUNT( IF( operatorlog.action = 'S', 1, NULL ) ) AS scans FROM operatorlog JOIN operators ON operatorlog.operator=operators.idoperator WHERE operators.idoperator={$_POST['idoperator']} AND (operatorlog.time BETWEEN '{$_POST['datefrom']}' AND '{$dateto}') GROUP BY operatorlog.operator ORDER BY operators.name ASC";
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
    
  default:
   printf("400: Bad Request");
 } 
?>