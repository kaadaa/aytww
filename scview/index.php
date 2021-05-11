<!DOCTYPE html> 
<?php require './dbcon.php'; ?>
<html>
 <head>
  <title>QAudit operator view v2.00</title>
  <meta http-equiv='content-type' content='text/html; charset=utf-8'>
  <link rel='shortcut icon' href='./img/favicon.png'>  
  <link rel='stylesheet' type='text/css' href='./css/jquery-ui.min.css'> 
  <link rel='stylesheet' type='text/css' href='./css/index.css'> 
  <link rel='stylesheet' type='text/css' href='./css/view.css'> 
  <link rel='stylesheet' type='text/css' href='./css/scraps.css'>  
  <link rel='stylesheet' type='text/css' href='./css/log.css'> 
  <script src="./js/jquery-2.2.3.min.js"></script> 
  <script src='./js/jquery-ui.min.js'></script>
  <script src='./js/chart.js'></script>                            
 </head>
 <body>
  <div id='loading'></div>
  <div id='head'>
   <img id='headviewbtn' class='headimg' src='./img/view.png' title='procházení záznamů'>
   <img id='headscrapsbtn' class='headimg' src='./img/scraps.png' title='statistiky vad'>
   <img id='headlogbtn' class='headimg' src='./img/log.png' title='aktivita operátorů'>
   <img id='scanbtn' src='./img/scan.png' alt='kontrola kvality' title='kontrola kvality'>
   <img id='logoutbtn' src='./img/logout.png' alt='odhlásit' title='odhlásit'>
   <span id='logedin'></span>
  </div>
  <div id='menu' class='frame'> 
   <table align='center'>  
    <tr>
     <td><div id='viewbtn'><img id='viewimg' src='./img/view.png' title='procházení záznamů'>procházení<br>záznamů</div></td>
     <td><div id='logbtn'><img id='logimg' src='./img/log.png' title='aktivita operátorů'>aktivita<br>operátorů</div></td>
    </tr><tr>
     <td><div id='scrapsbtn'><img id='scrapsimg' src='./img/scraps.png' title='statistiky vad'>statistiky<br>vad</div></td>
     <td></td>
    </tr>
   </table>
  </div>
  <div id='view' class='frame'><?php require './view.php'; ?></div>   
  <div id='scraps' class='frame'><?php require './scraps.php'; ?></div>  
  <div id='log' class='frame'><?php require './log.php'; ?></div>           
 </body>
</html>

<script type='text/javascript'>
                             
$(document).keyup(function(e){ 
});

$(document).ready(function(){
 login();
});

$('#viewbtn').click(function(){
 $('.frame').hide();
 $('#view').show();
 $('#headxlsbtn').hide();
 getprodlist();
});

$('#headviewbtn').click(function(){
 $('.frame').hide();
 $('#view').show();
 $('#headxlsbtn').hide();
 getprodlist();
});

$('#scrapsbtn').click(function(){
 $('.frame').hide();
 $('#scraps').show();
 $('#headxlsbtn').show();
 getscraps();
});

$('#headscrapsbtn').click(function(){
 $('.frame').hide();
 $('#scraps').show();
 $('#headxlsbtn').show();
 getscraps();
});

$('#logbtn').click(function(){
 $('.frame').hide();
 $('#log').show();
 getloglist();
});

$('#headlogbtn').click(function(){
 $('.frame').hide();
 $('#log').show();
 getloglist();
});

$('#scanbtn').click(function(){
 window.location.replace('./../scan/');
});

function login(){
 $.post('db.php', {action: 'login', idoperator: sessionStorage.getItem('idoperator')}, function(data){
  if ($.isNumeric(data[15])){ 
   var loged = $.parseJSON(data);
   $('#logedin').html(loged['name']);
   $('#logoutbtn').show();
   $('#headlogbtn').show();
   $('#headviewbtn').show();
   $('#headscrapsbtn').show();
   $('#logbtn').show();
   $('#viewbtn').show();
   $('#scrapsbtn').show();
  }
 });
};

$('#logoutbtn').click(function(){
 sessionStorage.removeItem('idoperator');
 sessionStorage.removeItem('opname');
 window.location.replace('./../scan/');
});  

</script>