<?php
// Require composer autoload
require_once __DIR__ . '/vendor/autoload.php';
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
$plantilla='ATEPLAFA';
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
    "FNACIMIENTO","EDAD","DIRECCION","SEXO","TELEFONORES","GRUPOETNICO","ANAMNESIS","METODUTIZ","OBSERV",
    "FECHAPLICAC","FULTIMOCONTR","FUM","FCITOLOGIA","RCITOLOGIA","FODONTOLOGIA","INSCRIPAJ","RANGOEDAD",
    "TRASTORNO","CAMBIOS","CEFALEA","MAREO","HIPERPGME","MASTALGIA","EDEMAINF","VARICESINF","DIU","LEUCORRE",
    "DOLOR","EXAMENFISI","SIGNOS","PRESION1","PRESION2","FRECUEN","FRECUN","TEMP","TALLA","PESO","IMC",
    "ESTADONUTRI","SINTORESP","SINTOPIEL","VICMALTRAT","VICTABUSO","OBESIDAD","ITS","CANCERVIX","CANSENO",
    "RIESGO","METODFORMU","INTERV","PROXCITATIPO","FCHPROXCITA");

$cabecera=array("SEDE","RAZONSOCIAL","FECHA","TIPO_DOC","IDAFILIADO","PNOMBRE","SNOMBRE","PAPELLIDO","SAPELLIDO",
    "FNACIMIENTO","EDAD","DIRECCION","SEXO","TELEFONO","GRUPOETNICO","Anamnesis","Método utilizado","Observacíon",
    "Fecha última aplicación/Toma","Fecha último control Planificación Familiar","FUM","Fecha última citología",
    "Resultado citología","Fecha última cita odontologica","Inscripción al programa DTA Joven",
    "Rango de edad al ingreso","Trastornos mestruales","Cambios de comportamiento","Cefaleas","Mareos",
    "Hiperpigmetación en la piel","Mastalgia","Edema miembros inferiores","Varices miembros inferiores",
    "Expulsión DIU","Leucorrea","Dolor pelvico","EXAMEN FISICO","SIGNOS VITALES","Presión arterial sistólica mmHg",
    "Presión arterial diastolica mmHg","Frecuencia respiratoria /MIN","Frecuencia cardiaca /MIN",
    "Temperatura corporal °C","Talla","Peso Kg","IMC","Estado Nutricional","Sintomático respiratorio",
    "Sintomático de piel","Víctima de maltrato","Víctima de abuso sexual","Obesidad o Desnutrición Proteico Calórica",
    "Infecciones de Trasmisión Sexual","Cáncer de Cérvix","Cáncer de Seno","Riesgo reproductivo","Método prescrito",
    "Intervenciones","Próxima cita","Fecha próxima cita");
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


$consulta="exec spV_HCA_Pivot '$fechaini','$fechafin','$plantilla','$idsede','[ANAMNESIS],[METODUTIZ],[OBSERV],
[FECHAPLICAC],[FULTIMOCONTR],[FUM],[FCITOLOGIA],[RCITOLOGIA],[FODONTOLOGIA],[INSCRIPAJ],[RANGOEDAD],[TRASTORNO],
[CAMBIOS],[CEFALEA],[MAREO],[HIPERPGME],[MASTALGIA],[EDEMAINF],[VARICESINF],[DIU],[LEUCORRE],[DOLOR],[EXAMENFISI],
[SIGNOS],[PRESION1],[PRESION2],[FRECUEN],[FRECUN],[TEMP],[TALLA],[PESO],[IMC],[ESTADONUTRI],[SINTORESP],[SINTOPIEL],
[VICMALTRAT],[VICTABUSO],[OBESIDAD],[ITS],[CANCERVIX],[CANSENO],[RIESGO],[METODFORMU],[INTERV],[PROXCITATIPO],[FCHPROXCITA]' ";

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

$filename='at_enfermeria_planificacion';
//$activeSheet->setCellValue('A1' , 'New file content')->getStyle('A1')->getFont()->setBold(true);
header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment;filename="'. $filename .'.xlsx"'); /*-- $filename is  xsl filename ---*/
header('Cache-Control: max-age=0');
$Excel_writer->save('php://output');
exit;