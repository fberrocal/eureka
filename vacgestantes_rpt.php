<?php
// Require composer autoload
require_once __DIR__ . '/vendor/autoload.php';
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
$plantilla='ENFVACGE';
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
    "FNACIMIENTO","EDAD","DIRECCION","SEXO","TELEFONORES","GRUPOETNICO","SEMGESTA","ANTVACU","VACAPLI","LOTEVAC",
    "LABORVAC","FECVENCVAC","JERINGA","LOTEJERING","FECHAJERIN","LABJERIN","FECPROXVAC","PROVACAPL","VAC2",
    "VACDOSIS","LOTEV2","LABVAC2","FECVENVAC2","JERUT2","LOTEJER2","FECVENJER2","LBJER2","FECVAC2","PROXVACAPL2",
    "EDUCA","VACU01","VACU02");

$cabecera=array("SEDE","RAZONSOCIAL","FECHA","TIPO_DOC","IDAFILIADO","PNOMBRE","SNOMBRE","PAPELLIDO","SAPELLIDO",
    "FNACIMIENTO","EDAD","DIRECCION","SEXO","TELEFONO","GRUPOETNICO","Semana Gestacional",
    "Antecedentes de Vacunación","Vacunas/dosis/sitio/a Aplicar","Lote de vacuna","Laboratorio vacuna",
    "Fecha de vencimiento vacuna","Jeringa Utilizada","Lote de jeringa","Fecha vencimiento jeringa",
    "Laboratorio jeringa","Fecha próxima vacuna","Próxima vacuna a aplicar","VACUNA2","Vacunas/dosis/sitio a aplicar",
    "Lote de vacuna","Laboratorio Vacuna","Feha de Vencimiento Vacuna","Jeringa Utilizada","Lote de Jeringa",
    "Fecha de Vencimiento Jeringa","Laboratorio Jeringa","Fecha Próxima Vacuna","Próxima Vacuna a Aplicar","Educación",
	"Vacuna 1","Vacuna 1");
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


$consulta="exec spV_HCA_Pivot '$fechaini','$fechafin','$plantilla','$idsede','[SEMGESTA],[ANTVACU],[VACAPLI],
[LOTEVAC],[LABORVAC],[FECVENCVAC],[JERINGA],[LOTEJERING],[FECHAJERIN],[LABJERIN],[FECPROXVAC],[PROVACAPL],
[VAC2],[VACDOSIS],[LOTEV2],[LABVAC2],[FECVENVAC2],[JERUT2],[LOTEJER2],[FECVENJER2],[LBJER2],[FECVAC2],
[PROXVACAPL2],[EDUCA],[VACU01],[VACU02]' ";

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

$filename='vacunacion_gestantes';
//$activeSheet->setCellValue('A1' , 'New file content')->getStyle('A1')->getFont()->setBold(true);
header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment;filename="'. $filename .'.xlsx"'); /*-- $filename is  xsl filename ---*/
header('Cache-Control: max-age=0');
$Excel_writer->save('php://output');
exit;