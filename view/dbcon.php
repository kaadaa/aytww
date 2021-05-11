<?php
 header('Content-Type: text/html; charset=utf-8');
 session_name('qauditview');
 session_start();
 $server = "localhost";
 $username = "root";
 $password = "";
 $database = "qaudit";
 $connect = mysqli_connect($server,$username,$password);

 if ($connect){
  mysqli_select_db($connect,$database);
  mysqli_query($connect,"SET NAMES utf8");
 }
 else echo ("Nepřipojeno k databázi: " . mysql_error());
?>