<div id='loglist'><h2>aktivita operátorů</h2>
<table id='loglfiltertab'>
 <tr>
  <td class='logtex'>datum od: </td>
  <td class='loginp'><input id='logdatefrom' type='date' required='required' value='<?php echo date("Y-m-d", strtotime('-7 days'));?>'></td>
  <td class='logtex'>datum do: </td>
  <td class='loginp'><input id='logdateto' type='date' required='required' value='<?php echo date("Y-m-d");?>'></td>
 </tr>
</table>
<div id='logltab'><table id='logltable'>
 <thead><tr>
  <th>operátor</th>
  <th>počet skenů</th>
 </tr></thead>
 <tbody id='loglrows'>
 </tbody>
</table></div>
</div>

<div id='logdetail'><h2>přístupy operátora</h2><div id='logdetailprint'>
<div id='logoperator'></div>
<div id='logdtab'><table id='logdtable'>
 <thead><tr>
  <th>datum a čas</th>
  <th>akce</th>
  <th>brána</th>
 </tr></thead>
 <tbody id='logdrows'>
 </tbody>
</table></div>
</div></div>
 
<script type='text/javascript'>

$('#logdatefrom').change(function(){
 getloglist();
}); 

$('#logdateto').change(function(){
 getloglist();
});                       

function getloglist(){
 $('#loading').fadeIn();
 $('#logdetail').hide(); 
 $.post('./db.php', {action: 'getloglist', idoperator: sessionStorage.getItem('idoperator'), datefrom: $('#logdatefrom').val(), dateto: $('#logdateto').val()}, function(data){
  var logs = $.parseJSON(data);
  var loghtml = '';
  for (i = 0; i < logs.length; i++){
   loghtml += "<tr id='l"+logs[i]['idoperator']+"' onClick=javascript:selectlog('"+logs[i]['idoperator']+"')><td>"+logs[i]['name']+"</td><td>"+logs[i]['scans']+"</td></tr>";
  }
  $('#loglrows').html(loghtml);
  $('#loading').fadeOut();                                                  
 }); 
}; 

function selectlog(idoperator){
 $('#logltable tr').removeAttr('style');
 $('#l'+idoperator).css('border', '2px solid #AAAAAA');
 $('#l'+idoperator).css('background-color', '#555555');
 $('#l'+idoperator).css('background-image', 'linear-gradient(to bottom, #FF7766, #FF4433)');
 $.post('./db.php', {action: 'getlog', operator: idoperator, datefrom: $('#logdatefrom').val(), dateto: $('#logdateto').val()}, function(data){
  var logs = $.parseJSON(data);
  var loghtml = '';
  $('#logoperator').html('operátor: '+logs[0]['name']); 
  for (i = 1; i < logs.length; i++){
   loghtml += "<tr><td>"+logs[i]['time']+"</td><td>";
   if (logs[i]['action'] == 'I') loghtml += "přihlášení";
   if (logs[i]['action'] == 'O') loghtml += "odhlášení";
   loghtml += "</td><td>"+logs[i]['gate']+"</td></tr>";
  }  
  $('#logdrows').html(loghtml);
 }); 
 $('#logdetail').show();
};     
 
</script>