<?php
// <-- Require composer autoload
require_once __DIR__ . '/vendor/autoload.php';		
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

$spreadsheet  = new Spreadsheet();  					/*--- Spreadsheet object -----*/
$Excel_writer = new Xlsx($spreadsheet);  			    /*--- Excel (Xls) Object -----*/
$spreadsheet->setActiveSheetIndex(0);
$activeSheet  = $spreadsheet->getActiveSheet();

require_once('database.class.php');
$conn = new Database();

// $caracteres =array('/`/','/#/','/%/','/>/','/</','/!/','/./','/[ -]+/','/]/','/\*/','/\$/','/;/','/:/','/\?/','/\^/','/{/','/}/','/\/','//');     
$caracteres = array('/\s\s+/','/-/','/\+/','/=/','/%/','/>/','/</','/\*/','/\//');
// $caracteres = array('/\s\s+/','/-/','/\+/','/=/');

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

$columnas=array("NOADMISION","DIA_ADMISION","TIPO_DOC","IDAFILIADO","DOCIDAFILIADO","PAPELLIDO","SAPELLIDO","PNOMBRE","SNOMBRE","NOMBREAFILIADO","SEXO","FNACIMIENTO",
"EDAD","FECHA","DXINGRESO","DESC1","DXEGRESO","DESC2","IDTERCERO","RAZONSOCIAL","FECHAALTA","TIPOTTEC","IDSEDE","DESCRIPCION","IDAREA_ALTA","FACTURADA",
"URG_NOMBRE","URG_TELE","URG_DIR","DIRECCION","TELEFONORES","IDMEDICOING","IDMEDICOTRA","PNOMBRE","SNOMBRE","PAPELLIDO","IDMEDICOALTA","N_FACTURA","obs","DESTINO","CERRADA","CLASENOPROC",
"USUARIO","TipoContrato");

$cabecera=array("NOADMISION","DIA_ADMISION","TIPO_DOC","IDAFILIADO","DOCIDAFILIADO","PAPELLIDO","SAPELLIDO","PNOMBRE","SNOMBRE","NOMBREAFILIADO","SEXO","FNACIMIENTO",
"EDAD","FECHA","DXINGRESO","DESC1","DXEGRESO","DESC2","IDTERCERO","RAZONSOCIAL","FECHAALTA","TIPOTTEC","IDSEDE","DESCRIPCION","IDAREA_ALTA","FACTURADA",
"URG_NOMBRE","URG_TELE","URG_DIR","DIRECCION","TELEFONORES","IDMEDICOING","IDMEDICOTRA","PNOMBRE","SNOMBRE","PAPELLIDO","IDMEDICOALTA","N_FACTURA","obs","DESTINO","CERRADA","CLASENOPROC",
"USUARIO","TipoContrato");

$x=1;
$y=1;

for($i=0;$i<count($cabecera);$i++){     
    $activeSheet->setCellValueByColumnAndRow($x,$y,$cabecera[$i]);
    $x++;
}

$consulta = "SELECT ";
$consulta.= "HADM.NOADMISION,DATEDIFF(day,hadm.fecha,hadm.fechaalta) AS DIA_ADMISION,AFI.TIPO_DOC,HADM.IDAFILIADO,AFI.DOCIDAFILIADO,AFI.PAPELLIDO,AFI.SAPELLIDO,";
$consulta.= "AFI.PNOMBRE,AFI.SNOMBRE,dbo.fnNombreAfiliado(AFI.IDAFILIADO,'N') as NOMBREAFILIADO,AFI.SEXO,CONVERT(VARCHAR(10),AFI.FNACIMIENTO,103) AS FNACIMIENTO,";
$consulta.= "dbo.fna_EdadenAnos(AFI.FNACIMIENTO,HADM.FECHA) AS EDAD,HADM.FECHA,HADM.DXINGRESO,MDX.DESCRIPCION as DESC1,HADM.DXEGRESO,MDX.DESCRIPCION AS DESC2, HADM.IDTERCERO,";
$consulta.= "TER.RAZONSOCIAL,HADM.FECHAALTA,HADM.TIPOTTEC,HADM.IDSEDE,SED.DESCRIPCION,HADM.IDAREA_ALTA,HADM.FACTURADA,HADM.URG_NOMBRE,HADM.URG_TELE,HADM.URG_DIR,"; 
$consulta.= "AFI.DIRECCION,AFI.TELEFONORES,HADM.IDMEDICOING,HADM.IDMEDICOTRA,MED.PNOMBRE,MED.SNOMBRE,MED.PAPELLIDO,HADM.IDMEDICOALTA,hadm.N_FACTURA,hadm.obs,HADM.DESTINO,HADM.CERRADA, HADM.CLASENOPROC,"; 
$consulta.= "HADM.USUARIO,TipoContrato=dbo.fnc_TipoContrato_HADM_xHPRED(noadmision,HADM.IDAFILIADO,hadm.IDTERCERO) ";
$consulta.= "FROM HADM INNER JOIN AFI ON HADM.IDAFILIADO = AFI.IDAFILIADO ";
$consulta.= "INNER JOIN TER ON HADM.IDTERCERO = TER.IDTERCERO ";
$consulta.= "INNER JOIN SED ON HADM.IDSEDE = SED.IDSEDE ";
$consulta.= "INNER JOIN MDX ON HADM.DXEGRESO= MDX.IDDX ";
$consulta.= "LEFT JOIN MED ON HADM.IDMEDICOTRA=MED.IDMEDICO ";
$consulta.= "WHERE HADM.FECHA between '".$fechaini."' AND '".$fechafin."' AND CLASENOPROC IS NULL AND CLASEING = 'A' ";
// $consulta.= "AND HADM.IDSEDE='".$idsede."'";


if( $idsede != '' ) {
	if($idsede != 'Todas') {
		$consulta.= " AND HADM.IDSEDE IN (SELECT CODIGO FROM TGEN WHERE TABLA='REPHADM' AND CAMPO='SEDE')";	
	} else {
		$consulta.= " AND HADM.IDSEDE='".$idsede."'";
	}
} else {
	$consulta.= " AND HADM.IDSEDE IN (SELECT CODIGO FROM TGEN WHERE TABLA='REPHADM' AND CAMPO='SEDE')";
}


$sth = $conn->prepare($consulta);
$sth->execute();
// $result = $sth->fetchall(PDO::FETCH_ASSOC);

$x=1;
$y=2;

while ($row = $sth->fetch())
{
    for($i=0;$i<count($columnas);$i++){
        $activeSheet->setCellValueByColumnAndRow($x,$y,preg_replace(array('/--/','/\+\+/','/==/'), ' ',$row[$columnas[$i]]));
        $x++;
    }
    $x=1;
    $y++;
}

// foreach($result as $key=>$row) {
//     foreach($row as $key2=>$row2){
//         $activeSheet->setCellValueByColumnAndRow($x,$y,preg_replace($caracteres, ' ',$row2));
//         $x++;
//     }
//     $y++;
//     $x=1;
// }

$filename='admisiones';
header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment;filename="'. $filename .'.xlsx"'); 			/*-- $filename is  xsl filename ---*/
header('Cache-Control: max-age=0');
$Excel_writer->save('php://output');
exit;
