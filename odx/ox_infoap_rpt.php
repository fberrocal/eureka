<?php
// Require composer autoload
require_once '../vendor/autoload.php';
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

$spreadsheet  = new Spreadsheet();
$Excel_writer = new Xlsx($spreadsheet);
$spreadsheet->setActiveSheetIndex(0);
$activeSheet  = $spreadsheet->getActiveSheet();

$caracteres = array('/\s\s+/','/-/','/\+/','/=/');

require_once('../database.class.php');
$conn = new Database();

if(!isset($_POST['idsede'])){
    $idsede='';
}else{
    $idsede=$_POST['idsede'];
}

$parts    = explode('-', $_POST['fechaini']);
$fechaini = $parts[2].'/'.$parts[1].'/'.$parts[0];
$parts    = explode('-', $_POST['fechafin']);
$fechafin = $parts[2].'/'.$parts[1].'/'.$parts[0];

// $idmedico   = $_POST['idmedico'];
// $idservicio = $_POST['idservicio'];

$fechaini.=' 00:00:00.000';
$fechafin.=' 23:59:59.997';

$columnas=array("Id_Administradora", "RAZONSOCIAL", "IDPRACTICA", "IDMEDICO", "NOMBRE", "CANTIDAD", "IDSERVICIO", "DESCSERVICIO", "PracDescripcion", "IDODONTOGRAMA", "IDSEDE", 
				"IDAFILIADO", "PAPELLIDO", "SAPELLIDO", "PNOMBRE", "PraFchRealiz", "PraFch", "HPREDID", "NOPRESTACION", "fecha_proc");				

$x=1;
$y=1;
for($i=0;$i<count($columnas);$i++){
    $activeSheet->setCellValueByColumnAndRow($x,$y,$columnas[$i]);
    $x++;
}

$consulta="SELECT
	VWA_RIPS.IDCONTRATANTE AS Id_Administradora,
	TER.RAZONSOCIAL,
	OXPRA.PRAID AS IDPRACTICA,
	OXPRA.IDMEDICO,
	MED.NOMBRE,
	VWA_RIPS.CANTIDAD,
	VWA_RIPS.IDSERVICIO,
	SER.DESCSERVICIO,
	OXPRA.PracDescripcion,
	OXPRA.TRATID AS IDODONTOGRAMA,
	VWA_RIPS.IDSEDE,
	VWA_RIPS.IDAFILIADO,
	AFI.PAPELLIDO,
	AFI.SAPELLIDO,
	AFI.PNOMBRE,
	OXPRA.PraFchRealiz,
	OXPRA.PraFch,
	HPRED.HPREDID,
	HPRED.NOPRESTACION,
	VWA_RIPS.FECHA AS fecha_proc
FROM VWA_RIPS
	LEFT JOIN TER ON TER.IDTERCERO=VWA_RIPS.IDCONTRATANTE
	LEFT JOIN HPRED ON HPRED.HPREDID=VWA_RIPS.PRESTACIONID
	LEFT JOIN OXPRA ON OXPRA.HPREDID=HPRED.HPREDID
	LEFT JOIN MED ON OXPRA.IDMEDICO=MED.IDMEDICO
	LEFT JOIN SER ON SER.IDSERVICIO=VWA_RIPS.IDSERVICIO
	LEFT JOIN AFI ON AFI.IDAFILIADO=VWA_RIPS.IDAFILIADO
WHERE
	VWA_RIPS.FECHA BETWEEN '{$fechaini}' and '{$fechafin}'
	AND SER.PREFIJO='550'
	AND VWA_RIPS.ARCHIVORIPS='AP'";

$sth = $conn->prepare($consulta);
$sth->execute();
$x=1;
$y=2;

while ($row = $sth->fetch())
{
    for($i=0;$i<count($columnas);$i++){
        $activeSheet->setCellValueByColumnAndRow($x,$y,preg_replace($caracteres,' ',$row[$columnas[$i]]));
        $x++;
    }
    $x=1;
    $y++;
}

$filename='ox_infoap';
header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment;filename="'. $filename .'.xlsx"'); /*-- $filename is  xsl filename ---*/
header('Cache-Control: max-age=0');
$Excel_writer->save('php://output');
exit;
