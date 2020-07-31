<?php
// Require composer autoload
require_once __DIR__ . '/vendor/autoload.php';
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
$plantilla='VACUNRC';
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
    "FNACIMIENTO","EDAD","DIRECCION","SEXO","TELEFONORES","GRUPOETNICO","NUMGESTA","TIPOPARTO","DOSISHEPAT",
    "SITIOAPLI","VIAAPLICA","DOSISADMON","LOTEHEPATITI","LABHEPATITI","FECHAVENHEPA","JERING","LOTEJERIN",
    "LABJERINGA","FECHAVENC","DOSISBCG","SITIOBCG","VIA","DOSBCG","LOTEBCG","LABBCG","FECHAV","JERINGABCG",
    "LOTEJERBCG","LABJERINBCG","FEVENJERBCG","CAUSA","OBSRV","EDUCA");

$cabecera=array("SEDE","RAZONSOCIAL","FECHA","TIPO_DOC","IDAFILIADO","PNOMBRE","SNOMBRE","PAPELLIDO","SAPELLIDO",
    "FNACIMIENTO","EDAD","DIRECCION","SEXO","TELEFONO","GRUPOETNICO","Numero de gestación","Tipo de parto",
    "Dosis vacuna Hepatitis B","Sitio de aplicación","Vía aplicación","Dosis Administración","Lote","Laboratorio",
    "Fecha de vencimiento","Jereinga con aguja numero","Lote jeringa","Laboratorio jeringa",
    "Fecha de vencimiento jeringa","Dosis de BCG","Sitio de aplicación BCG","Via aplicación de BCG",
    "Dosis(de acuerdo a recomdaciones del inserto)","Lote BCG","Laboratorio de BCG","Fecha de vencimiento de BCG",
    "Jeringa con aguja numero","Lote jeringa","Laboratorio jeringa","Fecha vencimiento Jeringa",
    "Causa de NO VACUNACION","Observavciones","Educación");
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

$consulta="exec spV_HCA_Pivot '$fechaini','$fechafin','$plantilla','$idsede','[NUMGESTA],[TIPOPARTO],[DOSISHEPAT],
[SITIOAPLI],[VIAAPLICA],[DOSISADMON],[LOTEHEPATITI],[LABHEPATITI],[FECHAVENHEPA],[JERING],[LOTEJERIN],
[LABJERINGA],[FECHAVENC],[DOSISBCG],[SITIOBCG],[VIA],[DOSBCG],[LOTEBCG],[LABBCG],[FECHAV],[JERINGABCG],
[LOTEJERBCG],[LABJERINBCG],[FEVENJERBCG],[CAUSA],[OBSRV],[EDUCA]' ";
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

$filename='vacunacion_recien_nacido';
//$activeSheet->setCellValue('A1' , 'New file content')->getStyle('A1')->getFont()->setBold(true);
header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment;filename="'. $filename .'.xlsx"'); /*-- $filename is  xsl filename ---*/
header('Cache-Control: max-age=0');
$Excel_writer->save('php://output');
exit;