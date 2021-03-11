<?php
// Require composer autoload
require_once __DIR__ . '/vendor/autoload.php';
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
$plantilla='HAGUDEZA';
$spreadsheet = new Spreadsheet();  /*----Spreadsheet object-----*/
$Excel_writer = new Xlsx($spreadsheet);  /*----- Excel (Xls) Object*/
$spreadsheet->setActiveSheetIndex(0);
$activeSheet = $spreadsheet->getActiveSheet();

require_once('config.php');
require_once('database.class.php');
$conn = new Database();

if(!isset($_POST['idsede'])){
    $idsede='';
}else{
    $idsede=$_POST['idsede'];
}

$parts = explode('-', $_POST['fechaini']);
$fechaini  = $parts[2].'/'.$parts[1].'/'.$parts[0];

$parts = explode('-', $_POST['fechafin']);
$fechafin  = $parts[2].'/'.$parts[1].'/'.$parts[0];

$fechaini.=' 00:00:00.000';
$fechafin.=' 23:59:59.997';

$cabecera = $cabecerag . "
UNION ALL select CAMPO, DESCCAMPO from mpld where CLASEPLANTILLA='$plantilla' and campo in('SINTOAGUD', 'EXAMODER', 'EXAMOIZQ', 'EXAODER2', 'EXAOIZQ2', 
'OBSER', 'REMIOFT', 'REMOPTO', 'ESPECIALIDAD', 'FECHCONTR')";

$columnas=array();
$i=0;
$sth = $conn->prepare($cabecera);
$sth->execute();
$result = $sth->fetchall(PDO::FETCH_ASSOC);
$x=1;
$y=1;

foreach($result as $row) {
    $activeSheet->setCellValueByColumnAndRow($x,$y,$row['DESCCAMPO']);
    $columnas[$i]=$row['CAMPO'];
    $i++;
    $x++;
}

$consulta="exec spV_HCA_Pivot_v2 '$fechaini','$fechafin','$plantilla','$idsede','[SINTOAGUD],[EXAMODER], [EXAMOIZQ],
[EXAODER2], [EXAOIZQ2], [OBSER], [REMIOFT], [REMOPTO], [ESPECIALIDAD], [FECHCONTR]'";

$sth = $conn->prepare($consulta);
$sth->execute();

if(!$sth) {
    print_r($sth->errorInfo());
}

$x=1;
$y=2;
while ($row = $sth->fetch()) {
    for($i=0;$i<count($columnas);$i++){
        $activeSheet->setCellValueByColumnAndRow($x,$y,$row[$columnas[$i]]);
        $x++;
    }
    $x=1;
    $y++;
}

$filename='agudeza_visual';
header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment;filename="'. $filename .'.xlsx"'); /*-- $filename is  xsl filename ---*/
header('Cache-Control: max-age=0');
$Excel_writer->save('php://output');
exit;
