<?php
// Require composer autoload
require_once __DIR__ . '/vendor/autoload.php';
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
$plantilla='PLANIFIC';
$spreadsheet = new Spreadsheet();  /*----Spreadsheet object-----*/
$Excel_writer = new Xlsx($spreadsheet);  /*----- Excel (Xls) Object*/
$spreadsheet->setActiveSheetIndex(0);
$activeSheet = $spreadsheet->getActiveSheet();


require_once('database.class.php');
$conn = new Database();
$celda='';
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

$cabecera="
SELECT 'IDADMINISTRADORA' AS CAMPO, 'IDADMINISTRADORA' AS DESCCAMPO
UNION ALL
SELECT 'RAZONSOCIAL' AS CAMPO,'EPS' AS DESCCAMPO
UNION ALL 
SELECT 'TIPO_DOC' AS CAMPO, 'TIPO_DOC' AS DESCCAMPO
UNION ALL
SELECT 'IDAFILIADO' AS CAMPO, 'IDAFILIADO' AS DESCCAMPO
UNION ALL
SELECT 'PNOMBRE' AS CAMPO, 'PNOMBRE' AS DESCCAMPO
UNION ALL
SELECT 'SNOMBRE' AS CAMPO, 'SNOMBRE' AS DESCCAMPO
UNION ALL
SELECT 'PAPELLIDO' AS CAMPO, 'PAPELLIDO' AS DESCCAMPO
UNION ALL
SELECT 'SAPELLIDO' AS CAMPO, 'SAPELLIDO' AS DESCCAMPO
UNION ALL
SELECT 'FNACIMIENTO' AS CAMPO, 'FNACIMIENTO' AS DESCCAMPO
UNION ALL
SELECT 'EDAD' AS CAMPO, 'EDAD' AS DESCCAMPO
UNION ALL
SELECT 'SEXO' AS CAMPO, 'SEXO' AS DESCCAMPO
UNION ALL
SELECT 'DIRECCION' AS CAMPO, 'DIRECCION' AS DESCCAMPO
UNION ALL
SELECT 'TELEFONORES' AS CAMPO, 'TELEFONO' AS DESCCAMPO
UNION ALL
SELECT 'GRUPOETNICO' AS CAMPO, 'GRUPOETNICO' AS DESCCAMPO
UNION ALL
SELECT 'CONSECUTIVO' AS CAMPO, 'CONSECUTIVO' AS DESCCAMPO
UNION ALL
SELECT 'IDSEDE' AS CAMPO, 'IDSEDE' AS DESCCAMPO
UNION ALL
SELECT 'SEDE' AS CAMPO, 'SEDE' AS DESCCAMPO
UNION ALL
SELECT 'FECHA' AS CAMPO, 'FECHA' AS DESCCAMPO
UNION ALL
SELECT 'IDMEDICO' AS CAMPO, 'IDMEDICO' AS DESCCAMPO
UNION ALL
SELECT 'MEDICO' AS CAMPO, 'MEDICO' AS DESCCAMPO
UNION ALL
SELECT 'CLASEPLANTILLA' AS DESCCAMPO, 'CLASEPLANTILLA' AS DESCCAMPO
UNION ALL
select CAMPO, DESCCAMPO from mpld where CLASEPLANTILLA='$plantilla' and campo in('MOTIV','ENFACTUAL','TCONTROL', 'TCONSUL', 'PRESION1', 'PRESION2', 'TALLA',
'PESO', 'IMC', 'UMENS', 'FECHACITO', 'RESULTCIT', 'RIESGOT', 'TIPOMETO', 'PRESCRIP1', 'FECHAMET', 'FECHACIT', 'FECHACIT2')";
$columnas=array("SEDE","MOTIV","ENFACTUAL","TCONSUL","TCONTROL","IDMEDICO","MEDICO","FECHA_HC","RAZONSOCIAL","PNOMBRE","SNOMBRE","PAPELLIDO","SAPELLIDO","TIPO_DOC","IDAFILIADO",
"FNACIMIENTO","EDAD","SEXO","DIRECCION","TELEFONORES","GRUPOETNICO","PRESION1","PRESION2","TALLA","PESO","IMC","UMENS","FECHACITO","RESULTCIT","RIESGOT",
    "TIPOMETO","PRESCRIP1","FECHAMET","FECHACIT","MODOATE","FECHACIT2");

$cabecera=array("SEDE","MOTIVO CONSULTA","ENFERMEDAD ACTUAL","TIPO CONSULTA","CONTROL","ID MEDICO","PROFESIONAL","FECHA","RAZONSOCIAL","PNOMBRE","SNOMBRE","PAPELLIDO","SAPELLIDO","TIPO_DOC","DOCUMENTO",
    "FNACIMIENTO","EDAD","SEXO","DIRECCION","TELEFONO","GRUPOETNICO","PRESION SISTOLICA","PRESION DIATOLICA","TALLA","PESO","IMC","FECHA ULTUMA MENSTRUACION",
    "FECHA CITOLOGIA","RESULTADO CITOLOGIA","RIESGO REPRODUCTIVO","TIPO DE METODO","PRESCRIPCION DEL MÉTODO","FECHA SUMINISTRO MET",
    "FECHA PROXIMA CITA ENTREGA","MODALIDAD DE ATENCIÓN","FECHA PROXIMA CITA CONTROL");
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
       $celda=$activeSheet->getCellByColumnAndRow($x,$y)->getCoordinate();
       $activeSheet->getStyle($celda)->getFont()->setBold(true);
        $x++;
    }


$consulta="exec spV_HCA_Pivot '$fechaini','$fechafin','$plantilla','$idsede','[MOTIV],[ENFACTUAL],[TCONTROL],[TCONSUL],[PRESION1],[PRESION2],
[TALLA],[PESO],[IMC],[UMENS],[FECHACITO],[RESULTCIT],[RIESGOT],[TIPOMETO],[PRESCRIP1],[FECHAMET],[FECHACIT],[MODOATE],[FECHACIT2]' ";

$sth = $conn->prepare($consulta);
$sth->execute();
$result = $sth->fetchall(PDO::FETCH_ASSOC);
//print_r($result);
//exit;
$x=1;
$y=2;
$celdaini=$activeSheet->getCellByColumnAndRow(1,2)->getCoordinate();
foreach($result as $row)
//while ($row = $sth->fetch())
{
    for($i=0;$i<count($columnas);$i++){
        
        $activeSheet->setCellValueByColumnAndRow($x,$y,trim(preg_replace('/\s\s+/', ' ', $row[$columnas[$i]])));
        $celda=$activeSheet->getCellByColumnAndRow($x,$y)->getCoordinate();
        //$activeSheet->getStyle($celda)->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\);
        $x++;
        //echo "<br>".$columnas[$i]; echo "<br>".$row[$columnas[$i]];
    }
    //$activeSheet->getStyle($celdaini.':'.$celda)->getAlignment()->setWrapText(true);
    $x=1;
    $y++;
    //echo $row['name'] . "\n";
}
//exit;
/*foreach($result as $key=>$row) {
    foreach($row as $key2=>$row2){

        $activeSheet->setCellValueByColumnAndRow($x,$y,$row2);
        $x++;
    }
    $y++;
    $x=1;
}*/

$filename='planificacion';
$activeSheet->setTitle($filename);
//$activeSheet->setCellValue('A1' , 'New file content')->getStyle('A1')->getFont()->setBold(true);
header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment;filename="'. $filename .'.xlsx"'); /*-- $filename is  xsl filename ---*/
header('Cache-Control: max-age=0');
$Excel_writer->save('php://output');
exit;