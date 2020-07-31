<?php
// Require composer autoload
require_once __DIR__ . '/vendor/autoload.php';
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
$plantilla='CASENO';
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
    "FNACIMIENTO","EDAD","DIRECCION","SEXO","TELEFONORES","GRUPOETNICO","CLASIRIES","PESO","TALLA ","IMC","FC",
    "FR","TEMP","TAD","TAD2","ALERTA3","EXASENODER","ENDURECI","ASIMETR","VENACRE","ERUPCIO","FLUIDO","PROTUB",
    "HENDIDU","ARDOREN","PEZON","PIELNARA","BULTOINT","HUECOS","MASASAXILA","LUNARES","OTROS ","DESCHA","ALERTA1",
    "EXAMESENO2","ENDURE2","ASIMET2","VENACRE2","ERUPCIO2","FLUIDODES2","PROTUBE2","HENDIDU2","ENROJE2","HUNDIPE2",
    "PIELNARA2","BULTO2","HUECOS2","MASASAX2","CALUNA2","OTROS2","DESCRIP2","CASENO2","REMITIDO","ESPECIALID",
    "MOTIVO","PLANINTER","FECHAPROX","ORDMAM");

$cabecera=array("SEDE","RAZONSOCIAL","FECHA","TIPO_DOC","IDAFILIADO","PNOMBRE","SNOMBRE","PAPELLIDO","SAPELLIDO",
    "FNACIMIENTO","EDAD","DIRECCION","SEXO","TELEFONO","GRUPOETNICO","Clasificacion del riesgo ","Peso","Talla ",
    "IMC","Frecuencia cardiaca ","Frecuencia respiratoria ","Temperatura","Tension arterial sistolica",
    "Tension arterial diastolica ","ALERTA","EXAMEN DE SENO Y ANEXOS (MAMA IZQUIERDA)","Endurecimiento (m. izquierda)",
    "Asimetrica (m. izquierda)","Vena creciente (m. izquierda)","Erupcion (m. izquierda)",
    "Fluido desconocido (m. izquierda)","Protuberancia (m. izquierda)","Hendidura (m. izquierda)",
    "Enrojecimiento o ardor (m. izquierda)","Hundimiento del pezon (m. izquierda)","Piel de naranja (m. izquierda)",
    "Bulto interno (m. izquierda)","Huecos  (m. izquierda)","Masas en las axilas (m. izquierda)",
    "Cambios en lunares o cicatrices en mama (m. izquierda)","Otros (m. izquierda)","Descripcion hallazgos (m. izquierda)",
    "ALERTA (m. izquierda)","EXAMEN DE SENO Y ANEXOS (MAMA DERECHA)","Endurecimiento (m. derecha)",
    "Asimetrica (m. derecha)","Vena creciente  (m. derecha)","Erupcion (m. derecha)","Fluido desconocido (m. derecha)",
    "Protuberancia (m. derecha)","Hendidura (m. derecha)","Enrojecimiento o ardor (m. derecha)",
    "Hundimiento del pezon (m. derecha)","Piel de naranja (m. derecha)","Bulto interno (m. derecha)",
    "Huecos (m. derecha)","Masas en las axilas (m. derecha)","Cambios en lunares o cicatrices en la mama (m. derecha)",
    "Otros (m. derecha)","Descripcion hallazgos (m. derecha)","Cáncer de Seno","Remitido ","Especialidad ",
    "Motivo de remision ","Plan de intervencion","Fecha proxima cita ","Se ordena Mamografía");
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


$consulta="exec spV_HCA_Pivot '$fechaini','$fechafin','$plantilla','$idsede','[CLASIRIES],[PESO],[TALLA ],[IMC],[FC],
[FR],[TEMP],[TAD],[TAD2],[ALERTA3],[EXASENODER],[ENDURECI],[ASIMETR],[VENACRE],[ERUPCIO],[FLUIDO],[PROTUB],[HENDIDU],
[ARDOREN],[PEZON],[PIELNARA],[BULTOINT],[HUECOS],[MASASAXILA],[LUNARES],[OTROS ],[DESCHA],[ALERTA1],[EXAMESENO2],
[ENDURE2],[ASIMET2],[VENACRE2],[ERUPCIO2],[FLUIDODES2],[PROTUBE2],[HENDIDU2],[ENROJE2],[HUNDIPE2],[PIELNARA2],[BULTO2],
[HUECOS2],[MASASAX2],[CALUNA2],[OTROS2],[DESCRIP2],[CASENO2],[REMITIDO],[ESPECIALID],[MOTIVO],[PLANINTER],[FECHAPROX],[ORDMAM]' ";

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

$filename='camama';
//$activeSheet->setCellValue('A1' , 'New file content')->getStyle('A1')->getFont()->setBold(true);
header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment;filename="'. $filename .'.xlsx"'); /*-- $filename is  xsl filename ---*/
header('Cache-Control: max-age=0');
$Excel_writer->save('php://output');
exit;