<div id='filter'><h2>vyhledávání produktů</h2>
 <table id='filtertab'>
  <tr>
   <td class='tex' colspan=2>datum od: </td>
   <td class='inp' colspan=2><input id='datefrom' type='date' required='required' value='<?php echo date("Y-m-d", strtotime('-1 day'));?>'></td>
   <td class='tex' colspan=2>datum do: </td>
   <td class='inp' colspan=2><input id='dateto' type='date' required='required' value='<?php echo date("Y-m-d");?>'></td>
  </tr><tr>
   <td class='tex' colspan=2> kód / sériové č.: </td> 
   <td class='inp' colspan=2><input id='product' type='search' maxlength=23></td>
   <td class='tex' colspan=2>kontroloval: </td> 
   <td class='inp' colspan=2>
    <select id='operator' name='operator'>
    <option value=0></option>
    <?php
     $data = mysqli_query($connect,"SELECT idoperator, name FROM operators");
     while ($r = mysqli_fetch_assoc($data))
      printf("<option value={$r['idoperator']}>{$r['name']}</option>");
    ?>
    </select>
   </td>
  </tr>
   <td class='tex'>ok: </td>
   <td class='inp'><input id='ok' type='checkbox'></td>
   <td class='tex'>zapravení: </td>
   <td class='inp'><input id='repair' type='checkbox'></td>
   <td class='tex'>posouzení: </td>
   <td class='inp'><input id='decide' type='checkbox'></td>
   <td class='tex'>nok: </td>
   <td class='inp'><input id='nok' type='checkbox'></td>
   <td class='tex'>scrap: </td>
   <td class='inp'><input id='scrap' type='checkbox'></td>
  </tr>
  </tr>
   <td class='inp'>fb:<input id='fb' type='checkbox'></td>
   <td class='inp'>fc:<input id='fc' type='checkbox'></td>
   <td class='inp'>rb:<input id='rb' type='checkbox'></td>
   <td class='inp'>rc:<input id='rc' type='checkbox'></td>
   <td class='tex'>ranní:</td>
   <td class='inp'><input id='am' type='checkbox'></td>
   <td class='tex'>odpolední:</td>
   <td class='inp'><input id='pm' type='checkbox'></td>
  </tr>
 </table>
 <div id='rowscount'></div>
 <div id='prodlisttab'>
  <table id='prodlist'>
   <thead>
    <tr>
     <th>kód produktu</th>
     <th>sériové číslo</th>
     <th>popis produktu</th>
     <th>poslední scan</th>
    </tr>
   </thead>
   <tbody id='prodrows'>
   </tbody>
  </table>
 </div>
</div>
<div id='proddetail'><h2>karta produktu</h2><div id='detailprint'>
 <div id='prodproduct'></div>
 <div id='prodpserial'></div>
 <div id='prodstate'></div>
 <div id='prodscanstab'>
  <table id='prodscans'>
   <thead>
    <tr>
     <th>čas skenování</th>
     <th>kontroloval</th>
     <th>brána</th>
     <th>stav</th>
    </tr>
   </thead>
   <tbody id='scanrows'>
   </tbody>
  </table>
 </div>
</div></div>

<script type="text/javascript">
var voffset = 0;

$('#datefrom').change(function(){
 getprodlist();
}); 

$('#dateto').change(function(){
 getprodlist();
});

$('#product').change(function(){
 getprodlist();
});    

$('#operator').change(function(){
 getprodlist();
});    

$('#ok').change(function(){
 if ($('#ok').is(':checked')){
  $('#repair').prop('checked', false);
  $('#decide').prop('checked', false);
  $('#nok').prop('checked', false);
  $('#scrap').prop('checked', false);
 }
 getprodlist();
}); 

$('#repair').change(function(){
 if ($('#repair').is(':checked')){
  $('#ok').prop('checked', false);
  $('#decide').prop('checked', false);
  $('#nok').prop('checked', false);
  $('#scrap').prop('checked', false);
 }
 getprodlist();
});  

$('#decide').change(function(){
 if ($('#decide').is(':checked')){
  $('#ok').prop('checked', false);
  $('#repair').prop('checked', false);
  $('#nok').prop('checked', false);
  $('#scrap').prop('checked', false);
 }
 getprodlist();
});    

$('#nok').change(function(){
 if ($('#nok').is(':checked')){
  $('#ok').prop('checked', false);
  $('#repair').prop('checked', false);
  $('#decide').prop('checked', false);
  $('#scrap').prop('checked', false);
 }
 getprodlist();
}); 

$('#scrap').change(function(){
 if ($('#scrap').is(':checked')){
  $('#ok').prop('checked', false);
  $('#repair').prop('checked', false);
  $('#decide').prop('checked', false);
  $('#nok').prop('checked', false);
 }
 getprodlist();
});   

$('#fb').change(function(){
 if ($('#fb').is(':checked')){
  $('#fc').prop('checked', false);
  $('#rb').prop('checked', false);
  $('#rc').prop('checked', false);
 }
 getprodlist();
});

$('#fc').change(function(){
 if ($('#fc').is(':checked')){
  $('#fb').prop('checked', false);
  $('#rb').prop('checked', false);
  $('#rc').prop('checked', false);
 }
 getprodlist();
});

$('#rb').change(function(){
 if ($('#rb').is(':checked')){
  $('#fb').prop('checked', false);
  $('#fc').prop('checked', false);
  $('#rc').prop('checked', false);
 }
 getprodlist();
});

$('#rc').change(function(){
 if ($('#rc').is(':checked')){
  $('#fb').prop('checked', false);
  $('#fc').prop('checked', false);
  $('#rb').prop('checked', false);
 }
 getprodlist();
});                                            

$('#am').change(function(){
 if ($('#am').is(':checked')){
  $('#pm').prop('checked', false);
 }
 getprodlist();
});

$('#pm').change(function(){
 if ($('#pm').is(':checked')){
  $('#am').prop('checked', false);
 }
 getprodlist();
});   

function getprodlist(){
 $('#loading').fadeIn();
 $('#proddetail').hide(); 
 $.post('./db.php', {action: 'getprodlist', datefrom: $('#datefrom').val(), dateto: $('#dateto').val(), product: $('#product').val(), operator: $('#operator').val(), ok: $('#ok').is(':checked'), repair: $('#repair').is(':checked'), decide: $('#decide').is(':checked'), nok: $('#nok').is(':checked'), scrap: $('#scrap').is(':checked'), fb: $('#fb').is(':checked'), fc: $('#fc').is(':checked'), rb: $('#rb').is(':checked'), rc: $('#rc').is(':checked'), am: $('#am').is(':checked'), pm: $('#pm').is(':checked'), offset: 0}, function(data){
  var products = $.parseJSON(data);
  var prodhtml = '';
  for (i = 0; i < products.length - 1; i++){
   prodhtml += "<tr id='p"+products[i]['idscan']+"' onClick=javascript:selectprod('"+products[i]['idscan']+"','"+products[i]['product']+"','"+products[i]['pserial']+"','"+products[i]['state']+"')><td>"+products[i]['product']+"</td><td>"+products[i]['pserial']+"</td><td>"+products[i]['description']+"</td><td>"+products[i]['sdate']+"</td></tr>";
  }
  if ((products[products.length - 1] - voffset - 500) > 500) cnt = '500'; else cnt = products[products.length - 1] - voffset - 500;
  if (prodhtml != '' && products[products.length - 1] > 500) prodhtml += "<tr id='vmore'><td colspan=4>načíst dalších "+cnt+" záznamů...</td></tr>";
  $('#prodrows').html(prodhtml);
  $('#rowscount').html('počet záznamů: '+products[products.length - 1]);
  $('#loading').fadeOut();                                                  
 }); 
};

$(document).on('click', '#vmore', function(){
 voffset += 500;
 getprodlist_more();
});

function getprodlist_more(){  
 $('#loading').fadeIn();
 $('#proddetail').hide(); 
 $.post('./db.php', {action: 'getprodlist', datefrom: $('#datefrom').val(), dateto: $('#dateto').val(), product: $('#product').val(), operator: $('#operator').val(), ok: $('#ok').is(':checked'), repair: $('#repair').is(':checked'), decide: $('#decide').is(':checked'), nok: $('#nok').is(':checked'), scrap: $('#scrap').is(':checked'), fb: $('#fb').is(':checked'), fc: $('#fc').is(':checked'), rb: $('#rb').is(':checked'), rc: $('#rc').is(':checked'), am: $('#am').is(':checked'), pm: $('#pm').is(':checked'), offset: voffset}, function(data){
  var products = $.parseJSON(data);
  var prodhtml = '';
  for (i = 0; i < products.length - 1; i++){
   prodhtml += "<tr id='p"+products[i]['idscan']+"' onClick=javascript:selectprod('"+products[i]['idscan']+"','"+products[i]['product']+"','"+products[i]['pserial']+"','"+products[i]['state']+"')><td>"+products[i]['product']+"</td><td>"+products[i]['pserial']+"</td><td>"+products[i]['description']+"</td><td>"+products[i]['sdate']+"</td></tr>";
  }
  if ((products[products.length - 1] - voffset - 500) > 500) cnt = '500'; else cnt = products[products.length - 1] - voffset - 500;
  if (products[products.length - 1] > (voffset + 500)) prodhtml += "<tr id='vmore'><td colspan=4>načíst dalších "+cnt+" záznamů...</td></tr>";
  $('#prodlist tr:last').remove();
  $('#prodrows').html($('#prodrows').html() + prodhtml);
  $('#loading').fadeOut();  
 });
};  

function selectprod(idscan, product, pserial, state){
 $('#prodlist tr').removeAttr('style');
 $('#p'+idscan).css('border', '2px solid #AAAAAA');
 $('#p'+idscan).css('background-color', '#555555');
 $('#p'+idscan).css('background-image', 'linear-gradient(to bottom, #FF7766, #FF4433)'); 
 $('#prodproduct').html('výrobek: '+product);
 $('#prodpserial').html('sériové číslo: '+pserial);
 if (state == 'O') $('#prodstate').html("stav: <img src='./img/ok.png' title='ok'>");
 if (state == 'R') $('#prodstate').html("stav: <img src='./img/repair.png' title='zapravení'>");
 if (state == 'D') $('#prodstate').html("stav: <img src='./img/decide.png' title='posouzení'>");
 if (state == 'N') $('#prodstate').html("stav: <img src='./img/nok.png' title='nok'>");
 if (state == 'S') $('#prodstate').html("stav: <img src='./img/scrap.png' title='scrap'>");
 $.post('./db.php', {action: 'getscan', product: product, pserial: pserial}, function(data){
  var scans = $.parseJSON(data);
  var scanhtml = '';
   for (i = 0; i < scans.length; i++){
    scanhtml += "<tr><td>"+scans[i]['sdate']+"</td><td>"+scans[i]['operator']+"</td><td>"+scans[i]['gate']+"</td><td>";
    if (scans[i]['state'] == 'O') scanhtml += "<img src='./img/ok.png' title='ok'>";
    if (scans[i]['state'] == 'R') scanhtml += "<img src='./img/repair.png' title='oprava'>";
    if (scans[i]['state'] == 'D') scanhtml += "<img src='./img/decide.png' title='posouzení'>";
    if (scans[i]['state'] == 'N') scanhtml += "<img src='./img/nok.png' title='nok'>";
    if (scans[i]['state'] == 'S') scanhtml += "<img src='./img/scrap.png' title='scrap'>";
    scanhtml += "</td></tr>";
   }
   $('#scanrows').html(scanhtml);
 }); 
 $('#proddetail').show();
};       

</script>