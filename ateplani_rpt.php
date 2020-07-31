<?php
// Require composer autoload
require_once __DIR__ . '/vendor/autoload.php';
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
$plantilla='ATEPLANI';
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
    "FNACIMIENTO","EDAD","DIRECCION","SEXO","TELEFONORES","GRUPOETNICO","ANAMNESI","METODO","SATISFACCION","OBSV",
    "ULTIMA APLIC","FULTIMOCONTR","FUM","PRESION1","PRESION2","PESO","TALLA","IMC","ESTADONUTRI","CITOLOGIA",
    "RESULTACITO","ODONTOLOGIA","INGRESO AJ","RANGOEDAD","SINTORESP","SINTOPIEL","VICMALTRAT","VICTABUSO",
    "OBESIDAD","ITS","CANCERVIX","CANSENO","METODFORMU","INTERV","PROXCITATIPO","FCHPROXCITA");

$cabecera=array("SEDE","RAZONSOCIAL","FECHA","TIPO_DOC","IDAFILIADO","PNOMBRE","SNOMBRE","PAPELLIDO","SAPELLIDO",
    "FNACIMIENTO","EDAD","DIRECCION","SEXO","TELEFONO","GRUPOETNICO","Anamnesis","Método utilizado",
    "Satisfacción con el método","Observación","Fecha última aplicación/Toma","Fecha último control Planificación Familiar",
    "FUM","Presión arterial  Sistólica mmHg","Presión arterial Diatólica mmHg","Peso","Talla","IMC",
    "Estado nutricional","Fecha última citología","Resultado citología","Fecha última cita odontología",
    "Inscripción al programa DTA Adulto joven","Rango de edad al ingreso","Sintomático respiratorio",
    "Sintomático de piel","Víctima de maltrato","Víctima de abuso sexual","Obesidad o Desnutrición Proteico Calórica",
    "Infecciones de Trasmisión Sexual","Cáncer de Cérvix","Cáncer de Seno","Método prescrito","Intervenciones",
    "Próxima cita","Fecha próxima cita");
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


$consulta="exec spV_HCA_Pivot '$fechaini','$fechafin','$plantilla','$idsede','[ANAMNESI],[METODO],[SATISFACCION],
[OBSV],[ULTIMA APLIC],[FULTIMOCONTR],[FUM],[PRESION1],[PRESION2],[PESO],[TALLA],[IMC],[ESTADONUTRI],[CITOLOGIA],
[RESULTACITO],[ODONTOLOGIA],[INGRESO AJ],[RANGOEDAD],[SINTORESP],[SINTOPIEL],[VICMALTRAT],[VICTABUSO],[OBESIDAD],
[ITS],[CANCERVIX],[CANSENO],[METODFORMU],[INTERV],[PROXCITATIPO],[FCHPROXCITA]' ";

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

$filename='at_enfermeria_suministros';
//$activeSheet->setCellValue('A1' , 'New file content')->getStyle('A1')->getFont()->setBold(true);
header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment;filename="'. $filename .'.xlsx"'); /*-- $filename is  xsl filename ---*/
header('Cache-Control: max-age=0');
$Excel_writer->save('php://output');
exit;