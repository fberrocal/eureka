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

$columnas=array("IDCONTRATANTE","EPS","TIPO_DOC","IDAFILIADO","PAPELLIDO","SAPELLIDO","PNOMBRE","SNOMBRE","SEXO","FNACIMIENTO","EDAD","DIRECCION","TELEFONORES","CELULAR","IDAUT","FECHA_ORDEN_MEDICA","IDSERVICIO","DESCSERVICIO","IDMEDICO","NOMBRE_PROFESIONAL","USUARIO","CODIGO_SEDE","SEDE","CODIGODX","DESCRIPCION_DIAG","CLASEORDEN","IDPESPECIAL","CONSECUTIVO_ORDEN","COMENTARIOS","FINALIDAD","TIPO_CONTRATO");

$cabecera=array("IDCONTRATANTE","EPS","TIPO_DOC","IDAFILIADO","PAPELLIDO","SAPELLIDO","PNOMBRE","SNOMBRE","SEXO","FNACIMIENTO","EDAD","DIRECCION","TELEFONORES","CELULAR","IDAUT","FECHA_ORDEN_MEDICA","IDSERVICIO","DESCSERVICIO","IDMEDICO","NOMBRE_PROFESIONAL","USUARIO","CODIGO_SEDE","SEDE","CODIGODX","DESCRIPCION_DIAG","CLASEORDEN","IDPESPECIAL,","CONSECUTIVO_ORDEN","COMENTARIOS","FINALIDAD","TIPO_CONTRATO");

$x=1;
$y=1;

for($i=0;$i<count($cabecera);$i++){
    $activeSheet->setCellValueByColumnAndRow($x,$y,$cabecera[$i]);
    $x++;
}

$consulta = "SELECT 
AUT.IDCONTRATANTE,
TER.RAZONSOCIAL AS EPS,
AFI.TIPO_DOC,
AUT.IDAFILIADO,
AFI.PAPELLIDO,
AFI.SAPELLIDO,
AFI.PNOMBRE,
AFI.SNOMBRE,
afi.SEXO,
CONVERT(VARCHAR(10),AFI.FNACIMIENTO,103) AS FNACIMIENTO,
dbo.fna_EdadenAnos(AFI.FNACIMIENTO,AUT.FECHA) AS EDAD,
AFI.DIRECCION,
AFI.TELEFONORES,
AFI.CELULAR,
AUTD.IDAUT,
AUT.FECHA AS FECHA_ORDEN_MEDICA,
AUTD.IDSERVICIO,
SER.DESCSERVICIO,
USUSU.IDMEDICO, 
USUSU.NOMBRE AS NOMBRE_PROFESIONAL,
AUT.USUARIO,
AUT.IDSEDE AS CODIGO_SEDE,
SED.DESCRIPCION AS SEDE,
AUT.DXPPAL AS CODIGODX,
MDX.DESCRIPCION AS DESCRIPCION_DIAG,
AUT.CLASEORDEN,
AUT.IDPESPECIAL,
AUT.CONSECUTIVOHCA AS CONSECUTIVO_ORDEN,
AUTD.COMENTARIOS,
AUT.FINALIDAD,  
(CASE AUTD.TIPOCONTRATO 
    WHEN 'C' THEN 'CAPITADO' 
    WHEN 'E' THEN 'EVENTO'
    WHEN 'N' THEN 'ORDEN EXTERNA'  
  END) AS TIPO_CONTRATO
FROM AUTD
INNER JOIN AUT ON AUTD.IDAUT = AUT.IDAUT
INNER JOIN SER ON AUTD.IDSERVICIO = SER.IDSERVICIO
INNER JOIN AFI ON AUT.IDAFILIADO = AFI.IDAFILIADO
INNER JOIN SED ON AUT.IDSEDE = SED.IDSEDE
INNER JOIN USUSU ON AUT.USUARIO=USUSU.USUARIO
left JOIN MDX ON  AUT.DXPPAL=MDX.IDDX   
INNER JOIN TER ON AUT.IDCONTRATANTE=TER.IDTERCERO
WHERE AUT.FECHA  BETWEEN '$fechaini' AND '$fechafin' AND AUT.ESTADO='pendiente' AND AUT.PREFIJO IN ('500') AND AUT.IDAREA='4110'";

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

$filename='gestmed';
//$activeSheet->setCellValue('A1' , 'New file content')->getStyle('A1')->getFont()->setBold(true);
header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment;filename="'. $filename .'.xlsx"'); /*-- $filename is  xsl filename ---*/
header('Cache-Control: max-age=0');
$Excel_writer->save('php://output');
exit;
