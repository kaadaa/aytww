<div id='scrapslist'><h2>statistiky vad</h2>
<table id='scfiltertab'>
 <tr>
  <td class='sctex' colspan=2>datum od: </td>
  <td class='scinp' colspan=2><input id='scdatefrom' type='date' required='required' value='<?php echo date("Y-m-d", strtotime('-1 day'));?>'></td>
  <td class='sctex' colspan=2>datum do: </td>
  <td class='scinp' colspan=2><input id='scdateto' type='date' required='required' value='<?php echo date("Y-m-d");?>'></td>
 </tr><tr>
  <td class='sctex' colspan=2>ranní:</td>
  <td class='scinp' colspan=2><input id='scam' type='checkbox'></td>
  <td class='sctex' colspan=2>odpolední:</td>
  <td class='scinp' colspan=2><input id='scpm' type='checkbox'></td>
 </tr><tr>
  <td class='sctex'>fb:</td>
  <td class='scinp'><input id='scfb' type='checkbox'></td>
  <td class='sctex'>fc:</td>
  <td class='scinp'><input id='scfc' type='checkbox'></td>
  <td class='sctex'>rb:</td>
  <td class='scinp'><input id='scrb' type='checkbox'></td>
  <td class='sctex'>rc:</td>
  <td class='scinp'><input id='scrc' type='checkbox'></td>
 </tr>
</table>
<div id='scrowscount'></div>
<div id='scrapstab'><table id='scrapstable'>
 <thead><tr>
  <th>datum a čas</th>
  <th>typ</th>
  <th>kontroloval</th>
  <th>vada</th>
 </tr></thead>
 <tbody id='scraprows'>
 <tbody>
</table></div>
</div>

<div id='nokchartdiv'><canvas id='nokchart'></canvas></div>
<div id='operatorchartdiv'><canvas id='operatorchart'></canvas></div>
<div id='ppm'>
 <table id='ppmtable'>
  <thead><tr><th>vyrobeno kusů</th><th>zjištěno vad</th><th>ppm</th></tr></thead>
  <tbody id='ppmrows'></tbody>
 </table>
</div>

 
<script type='text/javascript'>

var soffset = 0; 
colors = ["#008000", "#a52a2a", "#ffd700", "#808000", "#00008b", "#bdb76b", "#8b008b", "#ff8c00", "#8b0000", "#000000", "#ffff00", "#4b0082", "#0000ff", "#ff00ff", "#9400d3"]; 

$('#scdatefrom').change(function(){
 getscraps();
}); 

$('#scdateto').change(function(){
 getscraps();
});

$('#scam').change(function(){
 if ($('#scam').is(':checked')){
  $('#scpm').prop('checked', false);
 }
 getscraps();
});

$('#scpm').change(function(){
 if ($('#scpm').is(':checked')){
  $('#scam').prop('checked', false);
 }
 getscraps();
}); 

$('#scfb').change(function(){
 if ($('#scfb').is(':checked')){
  $('#scfc').prop('checked', false);
  $('#scrb').prop('checked', false);
  $('#scrc').prop('checked', false);
 }
 getscraps();
});

$('#scfc').change(function(){
 if ($('#scfc').is(':checked')){
  $('#scfb').prop('checked', false);
  $('#scrb').prop('checked', false);
  $('#scrc').prop('checked', false);
 }
 getscraps();
});

$('#scrb').change(function(){
 if ($('#scrb').is(':checked')){
  $('#scfb').prop('checked', false);
  $('#scfc').prop('checked', false);
  $('#scrc').prop('checked', false);
 }
 getscraps();
});

$('#scrc').change(function(){
 if ($('#scrc').is(':checked')){
  $('#scfb').prop('checked', false);
  $('#scfc').prop('checked', false);
  $('#scrb').prop('checked', false);
 }
 getscraps();
}); 

$(document).on('click', '#smore', function(){
 soffset += 1000;
 getscraps_more();
});                        

function getscraps_more(){
 $('#loading').fadeIn();
 $.post('./db.php', {action: 'getscraps', datefrom: $('#scdatefrom').val(), dateto: $('#scdateto').val(), scam: $('#scam').is(':checked'), scpm: $('#scpm').is(':checked'), scfb: $('#scfb').is(':checked'), scfc: $('#scfc').is(':checked'), scrb: $('#scrb').is(':checked'), scrc: $('#scrc').is(':checked'), offset: 0}, function(data){  
  var scraps = $.parseJSON(data);
  var scraphtml = '';
  for (i = 0; i < scraps.length - 1; i++){
   scraphtml += "<tr><td>"+scraps[i]['sdate']+"</td><td>"+scraps[i]['type']+"</td><td>"+scraps[i]['operator']+"</td><td>"+scraps[i]['name']+"</td></tr>";
  }
  if ((scraps[scraps.length - 1] - soffset - 1000) > 1000) cnt = '1000'; else cnt = scraps[scraps.length - 1] - soffset - 1000;
  if (scraps[scraps.length - 1] > (soffset + 1000)) scraphtml += "<tr id='smore'><td colspan=4>načíst dalších "+cnt+" záznamů...</td></tr>";
  $('#scrapstable tr:last').remove();  
  $('#scraprows').html($('#scraprows').html() + scraphtml); 
  $('#loading').fadeOut();  
 }); 
};   

function getscraps(){
 $('#loading').fadeIn();
 $.post('./db.php', {action: 'getscraps', datefrom: $('#scdatefrom').val(), dateto: $('#scdateto').val(), scam: $('#scam').is(':checked'), scpm: $('#scpm').is(':checked'), scfb: $('#scfb').is(':checked'), scfc: $('#scfc').is(':checked'), scrb: $('#scrb').is(':checked'), scrc: $('#scrc').is(':checked'), offset: 0}, function(data){  
  var scraps = $.parseJSON(data);
  var scraphtml = '';
  for (i = 0; i < scraps.length - 1; i++){
   scraphtml += "<tr><td>"+scraps[i]['sdate']+"</td><td>"+scraps[i]['type']+"</td><td>"+scraps[i]['operator']+"</td><td>"+scraps[i]['name']+"</td></tr>";
  }
  if ((scraps[scraps.length - 1] - soffset - 1000) > 1000) cnt = '1000'; else cnt = scraps[scraps.length - 1] - soffset - 1000;
  if (scraphtml != '' && scraps[scraps.length - 1] > 1000) scraphtml += "<tr id='smore'><td colspan=4>načíst dalších "+cnt+" záznamů...</td></tr>";
  $('#scraprows').html(scraphtml);
  $('#scrowscount').html('počet záznamů: '+scraps[scraps.length - 1]);
  //$('#loading').fadeOut();
 });
 
 
 $('#nokchart').remove();
 $('#nokchartdiv').append("<canvas id='nokchart'><canvas>");
 
 var noklabels = [];
 var nokdata = [];
 var nokcolors = [];
 
 $.post('./db.php', {action: 'getnokchart', datefrom: $('#scdatefrom').val(), dateto: $('#scdateto').val(), scam: $('#scam').is(':checked'), scpm: $('#scpm').is(':checked'), scfb: $('#scfb').is(':checked'), scfc: $('#scfc').is(':checked'), scrb: $('#scrb').is(':checked'), scrc: $('#scrc').is(':checked')}, function(data){ 
  var nok = $.parseJSON(data);
  for (i = 0; i < nok.length; i++){
   noklabels.push(nok[i]['name']);
   nokdata.push(nok[i]['cnt']);
   nokcolors.push(colors[i]);
  }
  var nokchartdata = {
   labels: noklabels,
   datasets:[{
    data: nokdata,
    backgroundColor: nokcolors,
    hoverBackgroundColor: nokcolors
   }]
  };
    
  var ctx = $('#nokchart');
  var nokChart = new Chart(ctx,{
   type: 'pie',
   data: nokchartdata,
   options:{
    legend:{
     display: false 
    },
    title:{
     display: true,
     text: 'četnost výskytů vad'
    }
   }
  });     
 });     

 $('#operatorchart').remove();
 $('#operatorchartdiv').append("<canvas id='operatorchart'><canvas>");
 
 var operatorlabels = [];
 var operatordata = [];
 var operatorcolors = [];
 
 $.post('./db.php', {action: 'getoperatorchart', datefrom: $('#scdatefrom').val(), dateto: $('#scdateto').val(), scam: $('#scam').is(':checked'), scpm: $('#scpm').is(':checked'), scfb: $('#scfb').is(':checked'), scfc: $('#scfc').is(':checked'), scrb: $('#scrb').is(':checked'), scrc: $('#scrc').is(':checked')}, function(data){ 
  var nok = $.parseJSON(data);
  for (i = 0; i < nok.length; i++){
   operatorlabels.push(nok[i]['name']);
   operatordata.push(nok[i]['cnt']);
   operatorcolors.push(colors[colors.length-i])
  }
  var operatorchartdata = {
   labels: operatorlabels,
   datasets: [{
    label: 'počet vad',
    data: operatordata,
    backgroundColor: operatorcolors,
    hoverBackgroundColor: operatorcolors
   }]
  };

  var ctx = $('#operatorchart');
  var operatorChart = new Chart(ctx,{
   type: 'bar',
   data: operatorchartdata,
   options:{
    scales:{
     yAxes:[{
      ticks:{
       beginAtZero: true
      }
     }]
    },
    legend:{
     display: false
    },
    title:{
     display: true,
     text: 'počty nalezených vad'
    }
   }
  });
 });
 
 $.post('./db.php', {action: 'getppm', datefrom: $('#scdatefrom').val(), dateto: $('#scdateto').val(), scam: $('#scam').is(':checked'), scpm: $('#scpm').is(':checked'), scfb: $('#scfb').is(':checked'), scfc: $('#scfc').is(':checked'), scrb: $('#scrb').is(':checked'), scrc: $('#scrc').is(':checked')}, function(data){ 
  var ppm = $.parseJSON(data);    
  $('#ppmrows').html("<tr><td id='ppmmade'>"+ppm[0]['made']+"</td><td id='ppmnok'>"+ppm[0]['nok']+"</td><td id='ppmppm'>"+(Math.round(ppm[0]['nok']/ppm[0]['made']*100000000)/100)+"</td></tr>");
  $('#loading').fadeOut();
  });
};

</script>