<?php
// Require composer autoload
require_once __DIR__ . '/vendor/autoload.php';
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
$plantilla='AIEP25CE';
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
    "FNACIMIENTO","EDAD","DIRECCION","SEXO","TELEFONORES","GRUPOETNICO","TIPOCONSUL","TEMPE","FC","FR","TALLA",
    "PESO","PC","IMC","PERIBRA","SAT","BCG","POLIO1","POLIO2","POLIO3","POLIOR1","POLIOR2","HB0","HB1","HB2",
    "HB3","HIB1","HIB2","HIB3","DPT1","DPT2","DPT3","DPTR1","DPTR2","TRIPEV1","TRIPEV2","FIEBREAMA","ROTAV1",
    "ROTAV2","NEUMO1","NEUMO2","NEUMO3","INFLU1","INFLU2","INFLU3","INFLU4","HEPATI","VARICELA","OTRASV","DIAG1",
    "CODDIAG1","REMITIDO","ESPEC","MOTIVOREM");

$cabecera=array("SEDE","RAZONSOCIAL","FECHA","TIPO_DOC","IDAFILIADO","PNOMBRE","SNOMBRE","PAPELLIDO","SAPELLIDO",
    "FNACIMIENTO","EDAD","DIRECCION","SEXO","TELEFONO","GRUPOETNICO","TIPO DE CONSULTA","TEMPERATURA","FC","FR",
    "TALLA","PESO","PC","IMC","Perímetro del Brazo","SATURACIÓN DE OXÍGENO","BCG","POLIO (1RA DOSIS)",
    "POLIO (2DA DOSIS)","POLIO (3RA DOSIS)","POLIO (1ER REFUERZO)","POLIO (2DO REFUERZO)","HB (RECIEN NACIDO)",
    "HB (1RA DOSIS)","HB (2DA DOSIS)","HB (3RA DOSIS)","HIB (1RA DOSIS)","HIB (2DA DOSIS)","HIB (3RA DOSIS)",
    "DPT (1RA DOSIS)","DPT (2DA DOSIS)","DPT (3RA DOSIS)","DPT (1ER REFUERZO)","DPT (2DO REFUERZO)",
    "TRIPLE VIRAL","TRIPLE VIRAL (2)","FIEBRE AMARILLA","ANTIROTAVIRUS (1RA DOSIS)","ANTIROTAVIRUS (2DA DOSIS)",
    "ANTINEUMOCOCO (1RA DOSIS)","ANTINEUMOCOCO (2DA DOSIS)","ANTINEUMOCOCO (3RA DOSIS)",
    "INFLUENZA ESTACIONAL (DOSIS 1)","INFLUENZA  ESTACIONAL (DOSIS 2)","INFLUENZA  ESTACIONAL (DOSIS 3)",
    "INFLUENZA  ESTACIONAL (DOSIS 4)","HEPATITIS A","VARICELA","OTRAS VACUNAS","DIAGNOSTICO1","CODIGO DIAG 1",
    "REMITIDO","ESPECIALIDAD","MOTIVO REMISIÓN");
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


$consulta="exec spV_HCA_Pivot '$fechaini','$fechafin','$plantilla','$idsede','[TIPOCONSUL],[TEMPE],[FC],[FR],[TALLA],
[PESO],[PC],[IMC],[PERIBRA],[SAT],[BCG],[POLIO1],[POLIO2],[POLIO3],[POLIOR1],[POLIOR2],[HB0],[HB1],[HB2],[HB3],[HIB1],
[HIB2],[HIB3],[DPT1],[DPT2],[DPT3],[DPTR1],[DPTR2],[TRIPEV1],[TRIPEV2],[FIEBREAMA],[ROTAV1],[ROTAV2],[NEUMO1],[NEUMO2],
[NEUMO3],[INFLU1],[INFLU2],[INFLU3],[INFLU4],[HEPATI],[VARICELA],[OTRASV],[DIAG1],[CODDIAG1],[REMITIDO],[ESPEC],[MOTIVOREM]' ";

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

$filename='aiepi25ce';
//$activeSheet->setCellValue('A1' , 'New file content')->getStyle('A1')->getFont()->setBold(true);
header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment;filename="'. $filename .'.xlsx"'); /*-- $filename is  xsl filename ---*/
header('Cache-Control: max-age=0');
$Excel_writer->save('php://output');
exit;