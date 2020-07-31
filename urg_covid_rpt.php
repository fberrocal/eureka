<?php
// Require composer autoload
require_once __DIR__ . '/vendor/autoload.php';
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

$spreadsheet  = new Spreadsheet();  /*----Spreadsheet object-----*/
$Excel_writer = new Xlsx($spreadsheet);  /*----- Excel (Xls) Object*/
$spreadsheet->setActiveSheetIndex(0);
$activeSheet  = $spreadsheet->getActiveSheet();

require_once('database.class.php');
$conn = new Database();

$caracteres = array('/\s\s+/','/-/','/\+/','/=/');

// Captura de la SEDE
if (!isset($_POST['idsede'])){
    $idsede='';
} else {
    $idsede=$_POST['idsede'];
}

$parts     = explode('-', $_POST['fechaini']);
$fechaini  = $parts[2].'/'.$parts[1].'/'.$parts[0];

$parts     = explode('-', $_POST['fechafin']);
$fechafin  = $parts[2].'/'.$parts[1].'/'.$parts[0];

$fechaini.=' 00:00:00.000';
$fechafin.=' 23:59:59.997';

$columnas=array("NOADMISION","IDAFILIADO","PNOMBRE","SNOMBRE","PAPELLIDO","SAPELLIDO","EDAD","FNACIMIENTO","IDSERVICIO","DESCSERVICIO","FECHA_ADMISION","IDSEDE","DESCRIPCION","FECHAALTA","DXINGRESO","DXEGRESO","RAZONSOCIAL");



$cabecera=array("NOADMISION","IDAFILIADO","PNOMBRE","SNOMBRE","PAPELLIDO","SAPELLIDO","EDAD","FNACIMIENTO","IDSERVICIO","DESCSERVICIO","FECHA_ADMISION","IDSEDE","DESCRIPCION","FECHAALTA","DXINGRESO","DXEGRESO","RAZONSOCIAL");

$x=1;
$y=1;

for($i=0;$i<count($cabecera);$i++){
    $activeSheet->setCellValueByColumnAndRow($x,$y,$cabecera[$i]);
    $x++;
}

$consulta = "SELECT  
HADM.NOADMISION,
AFI.IDAFILIADO,
AFI.PNOMBRE,
AFI.SNOMBRE,
AFI.PAPELLIDO,
AFI.SAPELLIDO,
dbo.fna_EdadenAnos(AFI.FNACIMIENTO,HADM.FECHA) AS EDAD,
CONVERT(VARCHAR(10),AFI.FNACIMIENTO,103) AS FNACIMIENTO,
HPRED.IDSERVICIO,
SER.DESCSERVICIO,
HADM.FECHA AS FECHA_ADMISION,
HADM.IDSEDE,
SED.DESCRIPCION,
HADM.FECHAALTA,
HADM.DXINGRESO,
HADM.DXEGRESO,
TER.RAZONSOCIAL

FROM HPRED 
INNER JOIN HPRE ON HPRED.NOPRESTACION = HPRE.NOPRESTACION
INNER JOIN HADM ON HPRE.NOADMISION = HADM.NOADMISION
INNER JOIN SER ON HPRED.IDSERVICIO = SER.IDSERVICIO
INNER JOIN AFI ON HADM.IDAFILIADO=AFI.IDAFILIADO
INNER JOIN SED ON HADM.IDSEDE=SED.IDSEDE
INNER JOIN TER ON HADM.IDTERCERO=TER.IDTERCERO

WHERE HPRED.IDSERVICIO IN ('908856')

AND HADM.FECHA BETWEEN '$fechaini' and '$fechafin' AND HADM.CLASEING = 'A' ";

if( $idsede != '' ) {
	$consulta.= " AND b.IDSEDE='".$idsede."'";
}

$sth = $conn->prepare($consulta);
$sth->execute();
//$result = $sth->fetchall(PDO::FETCH_ASSOC);
$x=1;
$y=2;
while ($row = $sth->fetch())
{
    for($i=0;$i<count($columnas);$i++){
        $activeSheet->setCellValueByColumnAndRow($x,$y,$row[$columnas[$i]]);
        $x++;
    }
    $x=1;
    $y++;
}

$filename='urg_covid';
//$activeSheet->setCellValue('A1' , 'New file content')->getStyle('A1')->getFont()->setBold(true);
header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment;filename="'. $filename .'.xlsx"'); /*-- $filename is  xsl filename ---*/
header('Cache-Control: max-age=0');
$Excel_writer->save('php://output');
exit;
