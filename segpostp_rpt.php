<?php
// Require composer autoload
require_once __DIR__ . '/vendor/autoload.php';
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
$plantilla='SEGPOSTP';
$spreadsheet = new Spreadsheet();  /*----Spreadsheet object-----*/
$Excel_writer = new Xlsx($spreadsheet);  /*----- Excel (Xls) Object*/
$spreadsheet->setActiveSheetIndex(0);
$activeSheet = $spreadsheet->getActiveSheet();


require_once('database.class.php');
$conn = new Database();
// Create an instance of the class:
//$mpdf = new \Mpdf\Mpdf();
//$mpdf->setFooter('{PAGENO}');
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

$columnas=array("SEDE","RAZONSOCIAL","FECHA_HC","TIPO_DOC","IDAFILIADO","PNOMBRE","SNOMBRE","PAPELLIDO","SAPELLIDO",
    "FNACIMIENTO","EDAD","DIRECCION","SEXO","TELEFONORES","GRUPOETNICO","HALUMBRA","PESO","SENSORIO","PERFUSION",
    "PULSO","TA","CONDUCTA","TORO","LLANTO","FC","COLOR","TEMP","COND");

$cabecera=array("SEDE","RAZONSOCIAL","FECHA","TIPO_DOC","IDAFILIADO","PNOMBRE","SNOMBRE","PAPELLIDO","SAPELLIDO",
    "FNACIMIENTO","EDAD","DIRECCION","SEXO","TELEFONO","GRUPOETNICO","HORA ALUMBRAMIENTO","PESO","SENSORIO",
    "PERFUSIÓN","PULSO","TA SISTÓLICA","CONDUCTA","TORO UTERINO","RESPIRACIONES O LLANTO","FRECUENCIA CARDÍACA",
    "COLOR","TEMPERATURA","CONDUCTA");
//$sth = $conn->prepare($cabecera);
//$sth->execute();
//$result = $sth->fetchall(PDO::FETCH_ASSOC);
$x=1;
$y=1;
/*while ($row = $sth->fetch())
{
    for($i=0;$i<count($columnas);$i++){
        //echo "<br>".$columnas[$i];
        //echo "<br>".$row[$columnas[$i]];
        $activeSheet->setCellValueByColumnAndRow($x,$y,$row[$columnas[$i]]);
        $x++;
    }
}*/
   for($i=0;$i<count($cabecera);$i++){
        //echo "<br>".$columnas[$i];
        //echo "<br>".$row[$columnas[$i]];
        $activeSheet->setCellValueByColumnAndRow($x,$y,$cabecera[$i]);
        $x++;
    }


$consulta="exec spV_HCA_Pivot '$fechaini','$fechafin','$plantilla','$idsede','[HALUMBRA],[PESO],[SENSORIO],[PERFUSION],
[PULSO],[TA],[CONDUCTA],[TORO],[LLANTO],[FC],[COLOR],[TEMP],[COND]' ";

$sth = $conn->prepare($consulta);
$sth->execute();
//$result = $sth->fetchall(PDO::FETCH_ASSOC);
$x=1;
$y=2;
while ($row = $sth->fetch())
{
    for($i=0;$i<count($columnas);$i++){
        //echo "<br>".$columnas[$i];
        //echo "<br>".$row[$columnas[$i]];
        $activeSheet->setCellValueByColumnAndRow($x,$y,$row[$columnas[$i]]);
        $x++;
    }
    $x=1;
    $y++;
    //echo $row['name'] . "\n";
}
/*foreach($result as $key=>$row) {
    foreach($row as $key2=>$row2){

        $activeSheet->setCellValueByColumnAndRow($x,$y,$row2);
        $x++;
    }
    $y++;
    $x=1;
}*/

$filename='seguimiento_postparto';
//$activeSheet->setCellValue('A1' , 'New file content')->getStyle('A1')->getFont()->setBold(true);
header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment;filename="'. $filename .'.xlsx"'); /*-- $filename is  xsl filename ---*/
header('Cache-Control: max-age=0');
$Excel_writer->save('php://output');
exit;