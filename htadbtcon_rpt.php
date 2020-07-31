<?php
// Require composer autoload
require_once __DIR__ . '/vendor/autoload.php';
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
$plantilla='HTADBTCO';
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
    "FNACIMIENTO","EDAD","DIRECCION","SEXO","TELEFONORES","GRUPOETNICO","FECHADIAG", "FECHAINGRE", "DBTM", "PESO", "TALLA ", "IMC",
    "PERI", "TAS", "TAD", "MEDIA", "TAS1", "TAD2", "MEDIA2", "TAS3",
    "TAD3", "MEDIA3", "CLALHTA", "RESULHEMOGLO", "RESULT", "FECHAHEMOGRA",
    "RESULGLIBA", "FECHAGLICEMB", "RESULTCREAT", "FECHCREAT", "RESULTCOLE",
    "FECHACOLET", "RESULHDL", "FECHAHDL", "FECHLDL", "RESULLDL", "RESULTRIGL",
    "FECHATRIGLI", "RESULPOTA", "FECHAPOTAS", "RESULORINA", "FECHACITOQUI",
    "RESUHEMOGLO",  "FECHHEMOGLO", "RESULMICRO", "FECHAMICROAL", "RESULPROTEI",
    "FECHAPROTEIN",  "RESULTSH", "FECHATSH", "RESULEKG", "FECHAEKG", "RESURX",
    "FECHARX", "TASA", "ESTRAHTA", "CLADBT", "IECA", "ARA II", "CRITECON",
    "REMITIDO", "MOTIREM", "ESPECLI", "PROXIMOCON", "FECHAPRO");

$cabecera=array("SEDE","RAZONSOCIAL","FECHA","TIPO_DOC","IDAFILIADO","PNOMBRE","SNOMBRE","PAPELLIDO","SAPELLIDO",
    "FNACIMIENTO","EDAD","DIRECCION","SEXO","TELEFONO","GRUPOETNICO","Fecha diagnostico","Fecha ingreso programa ",
    "Diabetes mellitus","Peso", "Talla","IMC","Perimetro abdominal ","Sistolica mmHg (sentado)",
    "Diastolica mmHg (sentado)","Media mmHg (sentado)","Sistolica mmHg (acostado)",
    "Diastolica mmHg (acostado)","Media mmHg (acostado)","Sistolica mmHg (de pie)",
    "Diastolica mmHg (de pie)","Media mmHg (de pie)","Clasificacion de la presion arterial",
    "Resultado Hemoglobina gr/dL","Resultado Hemograma ","Fecha Hemograma ",
    "Resultado Glicemia basal mg/dL","Fecha Glicemia basal","Resultado Creatinina serica mg/dL",
    "Fecha Creatinina serica","Resultado colesterol total mg/dL","Fecha colesterol total",
    "Resultado HDL mg/dL","Fecha HDL","Fecha LDL","Resultado LDL mg/dL",
    "Resultado trigliceridos mg/dL","Fecha Trigliceridos ","Resultado potasio mEq/L",
    "Fecha potasio","Resultado citoquimico de orina ","Fecha citoquimico de orina ",
    "Resultado Hemoglobina glicosilada gr/dL","Fecha Hemoglobina glicosilada",
    "Resultado Microalbumina mcg/min","Fecha Microalbumina",
    "Resultado Proteinuria de 24 horas mg/24 horas","Fecha Proteinuria de 24 horas ",
    "Resultado TSH mlU/L","Fecha TSH","Resultado EKG","Fecha EKG","Resultado radiografía de torax",
    "Fecha radiografía de torax","Tasa de filtracion glomerular","Clasificacion del riesgo Cardiovascular",
    "Clasificacion de la diabetes mellitus","Paciente recibe IECA","Paciente recibe ARA II",
    "Criterio de control","Remitido","Motivo de remision ","Especialidad ","Proximo control ",
    "Fecha Proximo control");
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


$consulta="exec spV_HCA_Pivot '$fechaini','$fechafin','$plantilla','$idsede','[FECHADIAG], [FECHAINGRE], [DBTM], [PESO], 
[TALLA ], [IMC], [PERI], [TAS], [TAD], [MEDIA], [TAS1], [TAD2], [MEDIA2], [TAS3],
[TAD3], [MEDIA3], [CLALHTA], [RESULHEMOGLO], [RESULT], [FECHAHEMOGRA],
[RESULGLIBA], [FECHAGLICEMB], [RESULTCREAT], [FECHCREAT], [RESULTCOLE],
[FECHACOLET], [RESULHDL], [FECHAHDL], [FECHLDL], [RESULLDL],
[RESULTRIGL], [FECHATRIGLI], [RESULPOTA], [FECHAPOTAS], [RESULORINA],
[FECHACITOQUI], [RESUHEMOGLO], [FECHHEMOGLO], [RESULMICRO], [FECHAMICROAL],
[RESULPROTEI], [FECHAPROTEIN], [RESULTSH], [FECHATSH], [RESULEKG],
[FECHAEKG], [RESURX], [FECHARX], [TASA], [ESTRAHTA], [CLADBT], [IECA],
[ARA II], [CRITECON], [REMITIDO], [MOTIREM], [ESPECLI],
[PROXIMOCON], [FECHAPRO]' ";

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

$filename='htadbt_control';
//$activeSheet->setCellValue('A1' , 'New file content')->getStyle('A1')->getFont()->setBold(true);
header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment;filename="'. $filename .'.xlsx"'); /*-- $filename is  xsl filename ---*/
header('Cache-Control: max-age=0');
$Excel_writer->save('php://output');
exit;