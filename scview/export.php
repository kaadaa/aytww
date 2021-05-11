<?php
 require './dbcon.php';
 require './PHPExcel.php';
 require 'PHPExcel/Writer/Excel2007.php';
 
 $objPHPExcel = new PHPExcel();

 $objPHPExcel->getProperties()->setCreator('Stival automotive s.r.o.');
 $objPHPExcel->getProperties()->setLastModifiedBy('Stival automotive s.r.o.');
 $objPHPExcel->getProperties()->setTitle('QAudit statistiky vad');
 $objPHPExcel->getProperties()->setSubject('QAudit statistiky vad');
 $objPHPExcel->getProperties()->setDescription('Statistiky vad exportované z programu QAudit.');


 $objPHPExcel->setActiveSheetIndex(0);
 
 $objPHPExcel->getActiveSheet()->SetCellValue('A1', 'Datum a čas');
 $objPHPExcel->getActiveSheet()->SetCellValue('B1', 'Typ');
 $objPHPExcel->getActiveSheet()->SetCellValue('C1', 'Kontroloval');
 $objPHPExcel->getActiveSheet()->SetCellValue('D1', 'Číslo vady');
 $objPHPExcel->getActiveSheet()->SetCellValue('F1', 'Vyrobeno kusů');
 $objPHPExcel->getActiveSheet()->SetCellValue('G1', 'Zjištěno vad');
 $objPHPExcel->getActiveSheet()->SetCellValue('H1', 'PPM');
 $objPHPExcel->getActiveSheet()->SetCellValue('F2', $_POST['ppmmade']);
 $objPHPExcel->getActiveSheet()->SetCellValue('G2', $_POST['ppmnok']);
 $objPHPExcel->getActiveSheet()->SetCellValue('H2', $_POST['ppmppm']);
 
 
 $dateto = $_POST['dateto']." 23:59:59";
 $sql = "SELECT DATE_FORMAT(scans.sdate,'%e.%c.%Y %T') AS sdate, LEFT(products.type, 2) AS type, operators.name AS operator, idnok FROM defects JOIN scans ON defects.idscan = scans.idscan JOIN products ON scans.product = products.idproducts JOIN operators ON scans.operator = operators.idoperator WHERE (sdate BETWEEN '{$_POST['datefrom']}' AND '{$dateto}') ORDER BY scans.sdate ASC";
 $res = mysql_query($sql);
 $i = 2;
 while ($r = mysql_fetch_assoc($res)) {   
  $objPHPExcel->getActiveSheet()->SetCellValue('A'.$i, $r['sdate']);
  $objPHPExcel->getActiveSheet()->SetCellValue('B'.$i, $r['type']);
  $objPHPExcel->getActiveSheet()->SetCellValue('C'.$i, $r['operator']);
  $objPHPExcel->getActiveSheet()->SetCellValue('D'.$i, $r['idnok']);
  $i++;
 }
 $objPHPExcel->getActiveSheet()->setTitle('vady');

 $objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel);
 $file = './exp/export'.date("d.m.y_H.i.s", time()).'.xlsx';
 $objWriter->save($file);
 echo $file;

?>