<?php
// <-- Require composer autoload
require_once __DIR__ . '/vendor/autoload.php';		
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

$spreadsheet = new Spreadsheet();  					/*--- Spreadsheet object -----*/
$Excel_writer = new Xlsx($spreadsheet);  			/*--- Excel (Xls) Object -----*/
$spreadsheet->setActiveSheetIndex(0);
$activeSheet = $spreadsheet->getActiveSheet();

require_once('database.class.php');
$conn = new Database();

// ,'/`/','/#/','/%/','/>/','/</','/!/','/./','/[ -]+/','/]/','/\*/','/\$/','/;/','/:/','/\?/','/\^/','/{/','/}/','/\/'     
// $caracteres = array('/\s\s+/','/-/','/\+/','/=/','/%/','/>/','/</','/\*/','/\//');
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

//$columnas=array("SEDE","DOCUEMNTO_AFILIADO","NOMBRE_AFILIADO","NOMBRE_MEDICAMENTO","CANTIDAD","RAZONSOCIAL","FECHA_ORDEN","TIPOCONTRATO","CLASEORDEN");
// $columnas=array("SEDE","DOCUEMNTO_AFILIADO","NOMBRE_AFILIADO","CANTIDAD","RAZONSOCIAL","FECHA_ORDEN","TIPOCONTRATO","CLASEORDEN");
$columnas=array("SEDE");
//$cabecera=array("Sede","Doc Afiliado","Afiliado","Medicamento","Cantidad","Tercero","Fecha Orden","Tipo Contrato","Clase Orden");
//$cabecera=array("Sede","Doc Afiliado","Afiliado","Cantidad","Tercero","Fecha Orden","Tipo Contrato","Clase Orden");
$cabecera=array("Sede");

$x=1;
$y=1;

for($i=0;$i<count($cabecera);$i++){     
    $activeSheet->setCellValueByColumnAndRow($x,$y,$cabecera[$i]);
    $x++;
}

$consulta = "SELECT s.DESCRIPCION AS SEDE, x.DOCIDAFILIADO AS DOCUEMNTO_AFILIADO, dbo.fnNombreAfiliado(x.IDAFILIADO,'N') AS NOMBRE_AFILIADO,";
//$consulta.= "y.DESCSERVICIO AS NOMBRE_MEDICAMENTO, c.CANTIDAD, t.RAZONSOCIAL, b.FECHA AS FECHA_ORDEN, c.TIPOCONTRATO, b.CLASEORDEN";
$consulta.= "c.CANTIDAD, t.RAZONSOCIAL, b.FECHA AS FECHA_ORDEN, c.TIPOCONTRATO, b.CLASEORDEN";
$consulta.= " FROM AUTD c with (nolock) INNER JOIN AUT b with (nolock) ON c.IDAUT = b.IDAUT";
$consulta.= " INNER JOIN AFI x with (nolock) ON x.IDAFILIADO = b.IDAFILIADO";
$consulta.= " INNER JOIN SED s with (nolock) ON s.IDSEDE = b.IDSEDE";
$consulta.= " INNER JOIN SER y with (nolock) ON c.IDSERVICIO = y.IDSERVICIO";
$consulta.= " INNER JOIN TER t with (nolock) ON c.IDTERCEROCA = t.IDTERCERO";
$consulta.= " WHERE b.PREFIJO='500' AND b.FECHA between '".$fechaini."' AND '".$fechafin."' AND b.ESTADO='Pendiente'";

if( $idsede != '' ) {
	$consulta.= " AND b.IDSEDE='".$idsede."'";
}

echo $consulta;

$sth = $conn->prepare($consulta);
$sth->execute();

$x=1;
$y=2;

while ($row = $sth->fetch()) {
    for($i=0;$i<count($columnas);$i++) {
        $activeSheet->setCellValueByColumnAndRow($x,$y,preg_replace($caracteres, ' ',$row[$columnas[$i]]));
        $x++;
    }
    $x=1;
    $y++;
}

$filename='medic_rural';
header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment;filename="'. $filename .'.xlsx"'); 			/*-- $filename is  xsl filename ---*/
header('Cache-Control: max-age=0');
$Excel_writer->save('php://output');
exit;