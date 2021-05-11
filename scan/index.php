<?php require './dbcon.php'; ?>
<!doctype html>
<html>
 <head>
  <title>QAudit scan v2.00</title>
  <meta http-equiv='content-type' content='text/html; charset='utf-8'>
  <link rel='shortcut icon' href='./img/favicon.png'> 
  <link rel='stylesheet' type='text/css' href='./css/style.css'> 
  <script src='./js/jquery-2.2.3.min.js'></script>                                
 </head>
 
 <body>
   <div id='login'>
    <input type='password' name='operator' id='operator' maxlength=5 placeholder='id'>
    <img src='./img/login_bw.png' id='loginbtn' alt='přihlásit' title='přihlásit'>
    <div id='logintext'></div>
  </div>             
  <div id='scanmenu'>
   <div id='head'>
    <img class='datetime' src='./img/date.png'><div id='dat'></div>
    <img class='datetime' src='./img/time.png'><div id='clock'></div>
    <img src='./img/view.png' id='viewbtn' alt='prohlížení záznamů' title='prohlížení záznamů'>
    <img src='./img/logout.png' id='logoutbtn' alt='odhlásit' title='odhlásit'>
    <span id='logedin'></span>
   </div>
   <div id='counters_l'>FB: 0<br>FC: 0</div>
   <div id='counters_r'>RB: 0<br>RC: 0</div>
   <div id='states'>
    <img src='./img/ok_bw.png' id='ok' class='state' alt='ok' title='ok'>
    <img src='./img/repair_bw.png' id='repair' class='state' alt='zapravení' title='zapravení'>
    <img src='./img/decide_bw.png' id='decide' class='state' alt='posouzení' title='posouzení'>
    <img src='./img/nok_bw.png' id='nok' class='state' alt='nok' title='nok'>
    <img src='./img/scrap_bw.png' id='scrap' class='state' alt='scrap' title='scrap'>
   </div>
   <input type='text' name='code' id='code' maxlength=23 placeholder='kód produktu'>
   <input type='number' name='idnok' id='idnok' placeholder='číslo vady'>
   <input type='text' name='abcode' id='abcode' maxlength=22 placeholder='BAR code label'>
   <div id='status'></div>
  </div> 
  <div id='logo'><img src='./img/favicon.png'>QAudit scan v2.00</div> 
  <div id='err'></div>                  
 </body>
</html>

<script type='text/javascript'>

window.state = '';
var stateshort;
var idoperator;
var hidestatus;
var lastpress = new Date(); 
var lastpress = new Date();
var lastkey;
var code;
var lastcode;
const scannerdelay = 40;
var snok = new Audio('./snd/nok.mp3');

function clocks(){
 var date = new Date(); 
 var day = date.getDate();
 var mth = date.getMonth() + 1;
 var yr = date.getFullYear();
 var hr = date.getHours();
 var min = date.getMinutes();
 var sec = date.getSeconds();
  
  if (hr < 10) hr = '0'+hr;
  if (min < 10) min = '0'+min;
  if (sec < 10) sec = '0'+sec; 
   
  time = hr+':'+min+':'+sec;
  dat = day+'.'+mth+'.'+yr;
  $('#dat').html(dat);
  $('#clock').html(time);
}

setInterval(function() {
 clocks();
  }, 1000);

$(document).ready(function(){
 if (sessionStorage.getItem('idoperator') != null){
  idoperator = sessionStorage.getItem('idoperator');
  $('#logedin').html(sessionStorage.getItem('opname'));
  $('#login').hide();
  $('#scanmenu').show();
  $('#code').val('');
  $('#code').focus();
  sessionStorage.removeItem('idoperator');
  sessionStorage.removeItem('opname');
 }
 else{
  $('#scanmenu').hide();
  $('#operator').focus();
 }
});

function rgb2hex(rgb){
 rgb = rgb.match(/^rgba?\((\d+),\s*(\d+),\s*(\d+)(?:,\s*(\d+))?\)$/);
 function hex(x){
  return ("0" + parseInt(x).toString(16)).slice(-2);
 };
 return "#" + hex(rgb[1]) + hex(rgb[2]) + hex(rgb[3]);
};  

$('#operator').on('input',function(){
 $.post('db.php', {action: 'check_login', login: $(this).val()}, function(data){
  var operator = $.parseJSON(data);  
  if (data != 'null' && data != 'false'){
   $('#operator').css({'background-color' : '#00ff00'});
   $('#logintext').html(operator['name']);
   $('#logedin').html(operator['name']);
   idoperator = operator['idoperator'];
   $('#loginbtn').attr('src', './img/login.png');
   $('#loginbtn').css('cursor', 'pointer');
   $('#logintext').show();
  }
  else{
   $('#operator').css({'background-color' : '#ffffff'});
   $('#loginbtn').attr('src', './img/login_bw.png');
   $('#loginbtn').css('cursor', 'default');
   $('#logintext').hide();
  };
 });
});

function login(){
 if (rgb2hex($('#operator').css('background-color')) == '#00ff00'){
  $('#login').hide();
  $('#scanmenu').show();
  $('#code').val('');
  $('#code').focus();
  $.post('./db.php', {action: 'log', operator: idoperator, logaction: 'I'}, function(data){ 
   if (data != 'ok') alert('Chyba při zápisu dat - prosím kontaktujte správce. Popis chyby: ' + data);
  });
 };
};

function logout(){
 $('#ok').attr('src', './img/ok_bw.png');
 $('#repair').attr('src', './img/repair_bw.png');
 $('#decide').attr('src', './img/decide_bw.png');
 $('#nok').attr('src', './img/nok_bw.png');
 $('#scrap').attr('src', './img/scrap_bw.png');
 state = '';
 if(typeof hidestatus !== 'undefined') clearTimeout(hidestatus);
 $('#status').hide();
 $('#operator').val('');
 $('#operator').css({'background-color' : '#ffffff'});
 $('#loginbtn').attr('src', './img/login_bw.png');
 $('#loginbtn').css('cursor', 'default');
 $('#logintext').hide();
 $('#scanmenu').hide();
 $('#login').show();
 $('#operator').focus();
 $.post('./db.php', {action: 'log', operator: idoperator, logaction: 'O'}, function(data){
  if (data != 'ok') alert('Chyba při zápisu dat - prosím kontaktujte správce. Popis chyby: ' + data);
 });
}
  
$('#operator').keyup(function(e){
 if (e.keyCode == 13){
  login();
 };                  
});  

$('#loginbtn').click(function(){
 login();
});   

$('#logoutbtn').click(function(){
 logout();
});  

$('#viewbtn').click(function(){
 sessionStorage.setItem('idoperator', idoperator);
 sessionStorage.setItem('opname', $('#logedin').html());
 window.location.replace('./../scview/');
}); 

function ok(){if (!$('#idnok').is(':visible') && !$('#abcode').is(':visible')){
 $('#repair').attr('src', './img/repair_bw.png');
 $('#decide').attr('src', './img/decide_bw.png');
 $('#nok').attr('src', './img/nok_bw.png');
 $('#scrap').attr('src', './img/scrap_bw.png');
 if ($('#ok').attr('src') == './img/ok_bw.png'){
  $('#ok').attr('src', './img/ok.png');
  state = 'O';
 }
 else{
  $('#ok').attr('src', './img/ok_bw.png');
  state = '';
 }
 $('#code').focus();
}};

function repair(){if (!$('#idnok').is(':visible') && !$('#abcode').is(':visible')){
 $('#ok').attr('src', './img/ok_bw.png');
 $('#decide').attr('src', './img/decide_bw.png');
 $('#nok').attr('src', './img/nok_bw.png');
 $('#scrap').attr('src', './img/scrap_bw.png');
 if ($('#repair').attr('src') == './img/repair_bw.png'){
  $('#repair').attr('src', './img/repair.png');
  state = 'R';
 }
 else{
  $('#repair').attr('src', './img/repair_bw.png');
  state = '';
 }
 $('#code').focus();
}};

function decide(){if (!$('#idnok').is(':visible') && !$('#abcode').is(':visible')){
 $('#ok').attr('src', './img/ok_bw.png');
 $('#repair').attr('src', './img/repair_bw.png');
 $('#nok').attr('src', './img/nok_bw.png');
 $('#scrap').attr('src', './img/scrap_bw.png');
 if ($('#decide').attr('src') == './img/decide_bw.png'){
  $('#decide').attr('src', './img/decide.png');
  state = 'D';
 }
 else{
  $('#decide').attr('src', './img/decide_bw.png');
  state = '';
 }
 $('#code').focus();
}};

function nok(){if (!$('#idnok').is(':visible') && !$('#abcode').is(':visible')){
 $('#ok').attr('src', './img/ok_bw.png');
 $('#repair').attr('src', './img/repair_bw.png');
 $('#decide').attr('src', './img/decide_bw.png');
 $('#scrap').attr('src', './img/scrap_bw.png');
 if ($('#nok').attr('src') == './img/nok_bw.png'){
  $('#nok').attr('src', './img/nok.png');
  state = 'N';
 }
 else{
  $('#nok').attr('src', './img/nok_bw.png');
  state = '';
 }
 $('#code').focus();
}};

function scrap(){if (!$('#idnok').is(':visible') && !$('#abcode').is(':visible')){
 $('#ok').attr('src', './img/ok_bw.png');
 $('#repair').attr('src', './img/repair_bw.png');
 $('#decide').attr('src', './img/decide_bw.png');
 $('#nok').attr('src', './img/nok_bw.png');
 if ($('#scrap').attr('src') == './img/scrap_bw.png'){
  $('#scrap').attr('src', './img/scrap.png');
  state = 'S';
 }
 else{
  $('#scrap').attr('src', './img/scrap_bw.png');
  state = '';
 }
 $('#code').focus();
}};

$('#ok').click(function(){
 ok();
});  
 
$('#repair').click(function(){
 repair();
});

$('#decide').click(function(){
 decide();
});
  
$('#nok').click(function(){
 nok();
}); 

$('#scrap').click(function(){
 scrap();
});  
  
$(document).keypress(function(e){
 if ($('#scanmenu').is(':visible')){
  stateshort = 0; 
  delay = new Date().getTime() - lastpress;
  lastpress = new Date().getTime();
  if (e.which == 47) {ok(); stateshort = 1; e.preventDefault();}
  if (e.which == 42) {repair(); stateshort = 1; e.preventDefault();}
  if (e.which == 45 && delay > scannerdelay){decide(); stateshort = 1; e.preventDefault();}
  if (e.which == 45 && lastkey == 45 && delay < scannerdelay){                  //prodleva ctecky kvuli pomlcka
   $('#code').val($('#code').val().slice(0,-1));
   decide();
   stateshort = 1;
   e.preventDefault();
  }
  if (e.which == 43) {nok(); stateshort = 1; e.preventDefault();}
  if (e.which == 44) {scrap(); stateshort = 1; e.preventDefault();}
 lastkey = e.which;
 }
});

$('#code, #idnok, #abcode').keydown(function(e){
 $('#err').html('');
 var type = '';
 if (e.keyCode == 13){
  if (stateshort == 1 && delay < scannerdelay) {stateshort = 0; return false;}
  if(typeof hidestatus !== 'undefined') clearTimeout(hidestatus);
  $.ajax({
   url: './db.php',
   type: 'POST',
   data : {action: 'gettype', code: $('#code').val()},
   success: function(data){
    if (typeof(data) == 'undefined' || data == '') $('#err').html('Výrobek nebyl nalezen v databázi,<br>kontaktujte vedoucí kvality');
    else type = data;
   },
   async: false
  });
  
  if (state == '') $('#err').html('Nejprve zvolte výsledek kontroly');
  
  else if ($('#code').val().length != 23 || $('#code').val().charAt(12) != '#') $('#err').html('Nesprávně zadaný kód');
  
  else if (state == 'O' && type == 'FB' && !$('#abcode').is(':visible')){
   $('#code').prop('disabled', true);
   $('#abcode').show();
   $('#abcode').focus();                                 
   return false;
  } 
  
  else if (state == 'N' && !$('#idnok').is(':visible')){
   $('#code').prop('disabled', true);
   $('#idnok').show();
   $('#idnok').focus();
   return false;
  } 
  
  else if (state == 'N'){
   $.ajax({
    url: './db.php',
    type: 'POST',
    data : {action: 'validnok', idnok: $('#idnok').val()},
    success: function(data){
     if (data != 'ok') $('#err').html('Nesprávný kód chyby');
    },
    async: false
   });
  }
   
  else if ($('#abcode').is(':visible')){
   $.ajax({
    url: './db.php',
    type: 'POST',
    data : {action: 'checkab', code: $('#code').val(), abcode: $('#abcode').val()},
    success: function(data){
     if (data != 'ok')
      $('#idnok').val('24');
    },
    async: false
   }); 
  }
  
  if ($('#err').html() == ''){ 
   var wr = true; 
   code = $('#code').val(); 
   $('#status').css({'background-color' : '#00ff00'});
   if (code.substring(0,3) == '521') $('#status').css({'background-color' : '#2fbdd6'});      //nova vyroba
   if (state == 'O'){
    $.ajaxSetup({async:false});
    $.post('./db.php', {action: 'getnok', scan: code}, function(data){
     if (data == '') $('#status').html('OK<br>Scan: '+code);
     else if (data == 'R'){
      $('#status').html('OK<br>Scan: '+code+'<br>Zkontrolujte kus po zapravení');
      $('#status').css({'background-color' : '#ffff00'});     
     }
     else if (data == 'O'){
      $('#status').html('OK<br>Scan: '+code+'<br>Kus je již evidován jako OK');
      $('#status').css({'background-color' : '#ffff00'}); 
      wr = false;     
     }
     else{
      var nok = $.parseJSON(data);
      $('#status').html('OK<br>Scan: '+code+'<br>Zkontrolujte chybu č. '+nok['idnok']+' - '+nok['name']);
      $('#status').css({'background-color' : '#ffff00'}); 
     }
    });
    $.ajaxSetup({async:true}); 
   } 
   if (state == 'R') $('#status').html('Zapravení<br>Scan: '+code);
   if (state == 'D') $('#status').html('Posouzení<br>Scan: '+code);
   if (state == 'N') $('#status').html('NOK<br>Scan: '+code+'<br>Chyba: '+$('#idnok').val());
   if (state == 'S') $('#status').html('Scrap<br>Scan: '+code);
   
   if (!$('#idnok').is(':visible') && $('#idnok').val() == '24'){
    state = 'N';          
    $('#status').css({'background-color' : '#ffff00'});
    $('#status').html('NOK<br>Scan: '+code+'<br>Chyba: '+$('#idnok').val());
    snok.play(); 
   }
   
   if (lastcode == null) lastcode = code;
   $.ajaxSetup({async:false});
   $.post('./db.php', {action: 'checkpair', scan: code, last: lastcode}, function(data){
    if (data != 'ok'){
     $('#status').css({'background-color' : '#ffff00'});
     $('#status').html($('#status').html()+'<br>Zkontrolujte záměnu kusů');
     snok.play(); 
    }
   });
   $.ajaxSetup({async:true});  
  
   if (wr){
    $.post('./db.php', {action: 'scan', scan: code, state: state, idoperator: idoperator, idnok: $('#idnok').val()}, function(data){
     if (data != 'ok'){
      alert('Chyba při zápisu dat - prosím kontaktujte správce. Popis chyby: ' + data);
      $('#status').html('CHYBA<br>'+data);
      $('#status').css({'background-color' : '#ff0000'});
     }
    });
   }
   
   lastcode = code;
  }
  else{
   $('#status').html('CHYBA<br>'+$('#err').html());
   $('#status').css({'background-color' : '#ff0000'});
  };
  
  $('#ok').attr('src', './img/ok_bw.png');
  $('#repair').attr('src', './img/repair_bw.png');
  $('#decide').attr('src', './img/decide_bw.png');
  $('#nok').attr('src', './img/nok_bw.png');
  $('#scrap').attr('src', './img/scrap_bw.png');
  state = '';
  $('#code').prop('disabled', false);
  $('#code').val('');
  $('#idnok').val('');
  $('#idnok').hide();
  $('#abcode').val('');
  $('#abcode').hide();
  $('#status').show();
  $('#code').focus();
  hidestatus = setTimeout("$('#status').fadeOut('slow');",20000); 
 
  $.post('./db.php', {action: 'daycounts'}, function(data){
   var FB = FC = RB = RC = 0;
   if (data != ''){
    var cnts = $.parseJSON(data);   
    for (i = 0; i < cnts.length; i++){
     switch(cnts[i]['typ']){
      case 'FB':
       FB = cnts[i]['cnt'];
      break;
      case 'FC':
       FC = cnts[i]['cnt'];
      break;
      case 'RB':
       RB = cnts[i]['cnt'];
      break;
      case 'RC':
       RC = cnts[i]['cnt'];
      break;
      default: FB = FC = RB = RC = 0;
     }
    }
   }
  $('#counters_l').html("FB: "+FB+"<br>FC: "+FC);
  $('#counters_r').html("RB: "+RB+"<br>RC: "+RC);
  });  
  
  $.post('./db.php', {action: 'log', operator: idoperator, logaction: 'S'}, function(data){
   if (data != 'ok') alert('Chyba při zápisu dat - prosím kontaktujte správce. Popis chyby: ' + data); 
  });
  
 };                  
});  


</script>