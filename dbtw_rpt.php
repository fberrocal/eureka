<?php
// Require composer autoload
require_once __DIR__ . '/vendor/autoload.php';
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
$plantilla='HCDBTW';
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
SELECT 'CLASEPLANTILLA' AS DESCCAMPO, 'CLASEPLANTILLA' AS DESCCAMPO
UNION ALL
select CAMPO, DESCCAMPO from mpld where CLASEPLANTILLA='$plantilla' and campo in('TCONTROL', 'TCONSUL', 'PRESION1', 'PRESION2', 'TALLA',
'PESO', 'IMC', 'UMENS', 'FECHACITO', 'RESULTCIT', 'RIESGOT', 'TIPOMETO', 'PRESCRIP1', 'FECHAMET', 'FECHACIT', 'FECHACIT2')";

$columnas=array("SEDE","RAZONSOCIAL","FECHA_HC","TIPO_DOC","IDAFILIADO","PNOMBRE","SNOMBRE","PAPELLIDO","SAPELLIDO",
    "FNACIMIENTO","EDAD","DIRECCION","SEXO","TELEFONORES","GRUPOETNICO","TIPOCONSUL","PROF","FECHADIAG","FECHAINGR",
    "FC","FR","PESO","TALLA","IMC","TEMP","PERI","SISTO1","DIASTO","MEDIA","ESTRARIES ",
    "RESULT","FECHAHEMOGRA","RESULGLIBA","FECHAGLICEMB","RESULCREATI","FECHCREATI","RESULTCOLE","FECHACOLET",
    "RESULHDL","FECHAHDL","FECHALDL","RESULLDL","RESULTRIGL","FECHATRIGLI","RESULPOTA","FECHAPOTAS","RESULORINA",
    "FECHACITOQUI","RESULTPROTE","FECHAPROTE","RESULTSH","FECHATSH","RESULEKG","FECHAEKG","RESURX","FECHARX","OTROSLAB",
    "TASA","SEDENTAR","TRFARMA","TRNOFARMA","CRITERIO","REMITIDO","MOTIVOREM","ESPECIALI","PROXIMOCON","FECHAPRO","OBS");

$cabecera=array("SEDE","RAZONSOCIAL","FECHA","TIPO_DOC","IDAFILIADO","PNOMBRE","SNOMBRE","PAPELLIDO","SAPELLIDO",
    "FNACIMIENTO","EDAD","DIRECCION","SEXO","TELEFONO","GRUPOETNICO","TIPO DE CONSULTA","Profesional a Cargo",
    "Fecha diagnostico","Fecha ingreso programa ","Frecuencia Cardiaca /MIN",
    "Frecuencia Respiratoria /MIN","Peso","Talla","IMC","Temperatura","Perimetro abdominal ",
    "Sistolica mmHg","Distolica mmHg","Media mmHg","ESTRATIFICACIÓN DEL RIES ","Resultado Citoquimico de orina ",
    "Fecha citoquimico de orina ","Resultado Glicemia basal mg/dL","Fecha Glicemia basal",
    "Resultado creatinina serica mg/dL","Fecha creatinina serica","Resultado colesterol total mg/dL","Fecha colesterol total",
    "Resultado HDL mg/dL","Fecha HDL","Fecha LDL","Resultado LDL mg/dL","Resultado trigliceridos mg/dL",
    "Fecha Trigliceridos ","Resultado Hemoglobina glicosilada gr/dL","Fecha Hemoglobina Glicosilada",
    "Resultado microalbuminuria mcg/min","Fecha Microalbuminuria","Resultado proteinuria de 24 horas mg/24 horas",
    "Fecha proteinuria de 24 horas ","Resucltado TSH mlU/L","Fecha TSH","Resultado EKG","Fecha EKG",
    "Resultado radiografía de torax","Fecha radiografía de torax","OTROS LABORATORIOS","Tasa de filtracion glomerular",
    "Clasificacion de la dbt","Tratamiento farmacologico","Tratamiento no farmacologico","Criterio de control","Remitido",
    "Motivo de remision ","Especialidad ","Proximo control ","Fecha Proximo control","Observaciones");
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


$consulta="exec spV_HCA_Pivot '$fechaini','$fechafin','$plantilla','$idsede','[TIPOCONSUL],[PROF],[FECHADIAG],
[FECHAINGR],[FC],[FR],[PESO],[TALLA],[IMC],[TEMP],[PERI],[SISTO1],[DIASTO],[MEDIA],
[ESTRARIES ],[RESULT],[FECHAHEMOGRA],[RESULGLIBA],[FECHAGLICEMB],[RESULCREATI],[FECHCREATI],[RESULTCOLE],
[FECHACOLET],[RESULHDL],[FECHAHDL],[FECHALDL],[RESULLDL],[RESULTRIGL],[FECHATRIGLI],[RESULPOTA],[FECHAPOTAS],
[RESULORINA],[FECHACITOQUI],[RESULTPROTE],[FECHAPROTE],[RESULTSH],[FECHATSH],[RESULEKG],[FECHAEKG],[RESURX],
[FECHARX],[OTROSLAB],[TASA],[SEDENTAR],[TRFARMA],[TRNOFARMA],[CRITERIO],[REMITIDO],[MOTIVOREM],[ESPECIALI],
[PROXIMOCON],[FECHAPRO],[OBS]' ";

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

$filename='diabetes';
//$activeSheet->setCellValue('A1' , 'New file content')->getStyle('A1')->getFont()->setBold(true);
header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment;filename="'. $filename .'.xlsx"'); /*-- $filename is  xsl filename ---*/
header('Cache-Control: max-age=0');
$Excel_writer->save('php://output');
exit;