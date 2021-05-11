<?php
 require './dbcon.php';
 switch ($_POST['action']){
  case 'check_login':
   $data = mysqli_fetch_assoc(mysqli_query($connect,"SELECT * FROM operators WHERE login = '{$_POST['login']}' LIMIT 1"));
   echo json_encode($data);
  break;
  
  case 'scan':
   $code = explode('#', $_POST['scan']);
   $product = $code[0];
   $product = mysqli_fetch_assoc(mysqli_query($connect,"SELECT idproducts FROM products WHERE product = '$product'"));
   $pserial = $code[1];
   if (mysqli_query($connect,"INSERT INTO scans VALUES (NULL, NULL, '{$product['idproducts']}', '$pserial', '$gate', '{$_POST['state']}', {$_POST['idoperator']})"))
    echo 'ok';
   else echo mysqli_error($connect);
   if ($_POST['state'] == 'N'){
    $data = mysqli_fetch_assoc(mysqli_query($connect,"SELECT idscan FROM scans ORDER BY sdate DESC LIMIT 1"));
    if (!mysqli_query($connect,"INSERT INTO defects VALUES (NULL, {$_POST['idnok']}, {$data['idscan']})"))
     echo mysqli_error($connect);
   }
  break;
 
  case 'checkab':
   $data = mysqli_query($connect,"SELECT * FROM ab WHERE my16 = '".substr($_POST['abcode'],0,6)."' AND fa3d = '".substr($_POST['code'],0,12)."' LIMIT 1");
    if (mysqli_num_rows($data) == 1) echo 'ok';
    else echo mysqli_error($connect);
  break;
  
  case 'checkpair':
   $code = explode('#', $_POST['scan']);
   $product = $code[0];
   $product1 = mysqli_fetch_assoc(mysqli_query($connect,"SELECT idproducts FROM products WHERE product = '$product'"));
   $code = explode('#', $_POST['last']);
   $product = $code[0];
   $product2 = mysqli_fetch_assoc(mysqli_query($connect,"SELECT idproducts FROM products WHERE product = '$product'"));   
   $data = mysqli_fetch_array(mysqli_query($connect,"SELECT COUNT(idpairs) AS cnt FROM pairs WHERE (product1 = {$product1['idproducts']} AND product2 = {$product2['idproducts']}) OR (product1 = {$product2['idproducts']} AND product2 = {$product1['idproducts']}) LIMIT 1"));
   if ($data['cnt'] == 0) echo 'ok';
   echo mysqli_error($connect);
  break;
  
  case 'gettype':
   $code = explode('#', $_POST['code']);
   $product = $code[0];
   $product = mysqli_fetch_assoc(mysqli_query($connect,"SELECT type FROM products WHERE product = '$product'"));
   if (isset($product)) echo substr($product['type'], 0, 2);
  break;
  
  case 'getnok':
   $code = explode('#', $_POST['scan']);
   $product = $code[0];
   $product = mysqli_fetch_assoc(mysqli_query($connect,"SELECT idproducts FROM products WHERE product = '$product'"));
   $pserial = $code[1];  
   $nok = mysqli_fetch_assoc(mysqli_query($connect,"SELECT idscan,state FROM scans WHERE product = {$product['idproducts']} AND pserial = '$pserial' ORDER BY idscan DESC LIMIT 1"));
   if ($nok['state'] == 'R'){
    echo 'R';
   } 
   elseif ($nok['state'] == 'O'){
    echo 'O';
   } 
   elseif ($nok['state'] == 'N'){
    $idnok = mysqli_fetch_assoc(mysqli_query($connect,"SELECT idnok FROM defects WHERE idscan = {$nok['idscan']} LIMIT 1"));
    $data = mysqli_fetch_assoc(mysqli_query($connect,"SELECT * FROM noklist WHERE idnok = {$idnok['idnok']} LIMIT 1"));
    echo json_encode($data);
   }   
  break;

  case 'validnok':
   $data = mysqli_fetch_assoc(mysqli_query($connect,"SELECT * FROM noklist WHERE idnok = {$_POST['idnok']} LIMIT 1"));
   if (isset($data['idnok']) && $data['active'] == 1) echo 'ok';
  break;
  
  case 'daycounts':
   $today = date("Y-m-d");  
   $data = mysqli_query($connect,"SELECT LEFT(products.type,2) AS typ, COUNT(scans.idscan) AS cnt FROM scans JOIN products ON scans.product=products.idproducts WHERE DATE(scans.sdate)='{$today}' AND scans.idscan IN (SELECT MAX(scans.idscan) FROM scans WHERE DATE(scans.sdate)='{$today}' GROUP BY scans.product, scans.pserial) AND scans.state='O' GROUP BY LEFT(products.type,2)");
   if (mysqli_num_rows($data) > 0){
    $rows = array();
    while ($r = mysqli_fetch_assoc($data))
     $rows[] = $r; 
   echo json_encode($rows);
   }
  break;
  
  case 'log':
   if (mysqli_query($connect,"INSERT INTO operatorlog VALUES (NULL,NULL,'{$gate}','{$_POST['operator']}','{$_POST['logaction']}')")) echo 'ok';
   else echo mysqli_error($connect);
  break;
   
  default:
   printf("400: Bad Request");
 } 
?>