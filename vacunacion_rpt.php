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

$columnas=array("TIPO_DOC","IDAFILIADO","PAPELLIDO","SAPELLIDO","PNOMBRE","SNOMBRE","SEXO","FNACIMIENTO","EDAD","FECHA_ORDEN","IDSERVICIO","DESCSERVICIO","USUARIO","IDSOLICITANTE","NOMBRE_PROFESIONAL","SEDE","DIRECCION","TELEFONORES","CLASEORDEN","IDEESPECIAL","EPS");

$cabecera=array("TIPO_DOC","IDAFILIADO","PAPELLIDO","SAPELLIDO","PNOMBRE","SNOMBRE","SEXO","FNACIMIENTO","EDAD","FECHA_ORDEN","IDSERVICIO","DESCSERVICIO","USUARIO","IDSOLICITANTE","NOMBRE_PROFESIONAL","SEDE","DIRECCION","TELEFONORES","CLASEORDEN","IDEESPECIAL","EPS");

$x=1;
$y=1;

for($i=0;$i<count($cabecera);$i++){
    $activeSheet->setCellValueByColumnAndRow($x,$y,$cabecera[$i]);
    $x++;
}

$consulta = "SELECT 
AFI.TIPO_DOC,
AUT.IDAFILIADO,
AFI.PAPELLIDO,
AFI.SAPELLIDO,
AFI.PNOMBRE,
AFI.SNOMBRE,
AFI.SEXO,
CONVERT(VARCHAR(10),AFI.FNACIMIENTO,103) AS FNACIMIENTO,
dbo.fna_EdadenAnos(AFI.FNACIMIENTO,AUT.FECHA) AS EDAD,
AUT.FECHA AS FECHA_ORDEN,
AUTD.IDSERVICIO,
SER.DESCSERVICIO,
AUT.USUARIO,
AUT.IDSOLICITANTE,
USUSU.NOMBRE AS NOMBRE_PROFESIONAL,
SED.DESCRIPCION AS SEDE,
AFI.DIRECCION,
AFI.TELEFONORES,
AUT.CLASEORDEN,
AUT.IDEESPECIAL,
TER.RAZONSOCIAL AS EPS
FROM AUTD
INNER JOIN AUT ON AUTD.IDAUT = AUT.IDAUT
INNER JOIN SER ON AUTD.IDSERVICIO = SER.IDSERVICIO
INNER JOIN AFI ON AUT.IDAFILIADO = AFI.IDAFILIADO
INNER JOIN SED ON AUT.IDSEDE = SED.IDSEDE
INNER JOIN USUSU ON AUT.USUARIO=USUSU.USUARIO
INNER join TER ON AUTD.IDTERCEROCA=TER.IDTERCERO
WHERE AUT.FECHA  BETWEEN '$fechaini' AND '$fechafin' AND AUTD.IDSERVICIO IN ('993102','993106','993120','993122','993130','993501','993502','993503','993504','993505','993509','993510','993512','993513','993522','995201') AND AUT.ESTADO='pendiente'";

if( $idsede != '' ) {
	$consulta.= " AND AUT.IDSEDE='".$idsede."'";
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

$filename='vacunas';
//$activeSheet->setCellValue('A1' , 'New file content')->getStyle('A1')->getFont()->setBold(true);
header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment;filename="'. $filename .'.xlsx"'); /*-- $filename is  xsl filename ---*/
header('Cache-Control: max-age=0');
$Excel_writer->save('php://output');
exit;
