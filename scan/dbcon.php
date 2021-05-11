<?php
 header('Content-Type: text/html; charset=utf-8');
 session_name('qauditscan');
 session_start();
 $server = "localhost";
 $username = "root";
 $password = "";
 $database = "qaudit";

 $connect = mysqli_connect($server, $username, $password, $database);
 if (mysqli_connect_errno()) echo "Nepřipojeno k databázi: " . mysqli_connect_error();
 else mysqli_query($connect, "SET NAMES utf8");
 
 switch ($_SERVER['REMOTE_ADDR']){
  case '192.168.1.96': $gate = 'FB'; break;
  case '192.168.1.97': $gate = 'FC'; break;
  case '192.168.1.98': $gate = 'RB'; break;
  case '192.168.1.99': $gate = 'RC'; break;
  //case '192.168.1.40': $gate = 'G2'; break; 
  default: $gate = 'ER';            
 }
   
?>