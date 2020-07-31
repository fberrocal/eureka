<?php
ob_start();
require_once __DIR__ . '/vendor/autoload.php';      // Require composer autoload
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
$plantilla='HCHTACO';
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
    "FNACIMIENTO","EDAD","DIRECCION","SEXO","TELEFONORES","GRUPOETNICO","FECHADIAG", "FECHAINGRE", "DBTM", "PESO",
    "TALLA ", "IMC", "PERI",     "TAD", "TAS", "TAM", "TAS1", "TAD1", "TAM1", "TAS2", "TAD2", "TAM2",
    "CLALHTA", "RESUL12", "RESULT", "FECHAHEMOGRA", "RESULGLIBA", "FECHAGLICEMB",
    "RESULCRESTI", "FECHCREATI", "RESULTCOLE", "FECHACOLET", "RESULHDL", "FECHAHDL",
    "FECHALDL", "RESULLDL", "RESULTRIGL", "FECHATRIGLI", "RESULPOTA", "FECHAPOTAS",
    "RESULORINA", "FECHACITOQUI", "RESULEKG", "FECHAEKG", "RESURX", "FECHARX",
    "TASA", "ESTRAHTA", "IECA", "ARA II", "CRITERI", "REMITIDO", "MOTIVOREM",
    "ESPECIALI", "PROXIMOCON", "FECHAPRO");

$cabecera=array("SEDE","RAZONSOCIAL","FECHA","TIPO_DOC","IDAFILIADO","PNOMBRE","SNOMBRE","PAPELLIDO","SAPELLIDO",
    "FNACIMIENTO","EDAD","DIRECCION","SEXO","TELEFONO","GRUPOETNICO","Fecha diagnostico", "FECHA DE INGRESO AL PROGRAMA",
    "Diabetes mellitus", "Peso", "Talla", "IMC", "Perimetro abdominal ", "Sistolica mmHg (sentado)",
    "Diastolica mmHg (sentado)", "Media mmHg (sentado)", "Sistolica mmHg (acostado)", "Diatolica mmHg (acostado)",
    "Media mmHg (acostado)", "Sistolica mmHg  (de pie)", "Diastolica mmHg (de pie)", "Media mmHg (de pie)",
    "Clasificacion de la presion arterial", "Resultado Hemoglobina gr/dL", "Resultado Hemograma ",
    "Fecha Hemograma ", "Resultado Glicemia basal mg/dL", "Fecha Glicemia basal",
    "Resultado creatinina serica mg/dL", "Fecha creatinina serica", "Resultado colesterol total mg/dL",
    "Fecha colesterol total", "Resultado HDL mg/dL", "Fecha HDL", "Fecha LDL", "Resultado LDL mg/dL",
    "Resultado trigliceridos mg/dL", "Fecha Trigliceridos ", "Resultado potasio mEq/L",
    "Fecha potasio", "Resultado citoquimico de orina ", "Fecha citoquimico de orina ",
    "Resultado EKG", "Fecha EKG", "Resultado radiografia de torax", "Fecha radiografia de torax",
    "Tasa de filtracion glomerular", "Clasificacion del riesgo ", "Paciente recibe IECA",
    "Paciente recibe ARA II", "Criterio de control", "Remitido", "Motivo de remision ",
    "Especialidad ", "Proximo control ", "Fecha Proximo control");

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
[TALLA ], [IMC], [PERI], [TAD], [TAS], [TAM], [TAS1], [TAD1], [TAM1], [TAS2], [TAD2], [TAM2],
[CLALHTA], [RESUL12], [RESULT], [FECHAHEMOGRA], [RESULGLIBA], [FECHAGLICEMB],
[RESULCRESTI], [FECHCREATI], [RESULTCOLE], [FECHACOLET], [RESULHDL], [FECHAHDL],
[FECHALDL], [RESULLDL], [RESULTRIGL], [FECHATRIGLI], [RESULPOTA], [FECHAPOTAS],
[RESULORINA], [FECHACITOQUI], [RESULEKG], [FECHAEKG], [RESURX], [FECHARX],
[TASA], [ESTRAHTA], [IECA], [ARA II], [CRITERI], [REMITIDO], [MOTIVOREM],
[ESPECIALI], [PROXIMOCON], [FECHAPRO]' ";

$sth = $conn->prepare($consulta);
$sth->execute();
$x=1;
$y=2;

while ($row = $sth->fetch())
{
    for($i=0;$i<count($columnas);$i++){
        //echo "<br>'".$columnas[$i]."'";
        //echo "<br>'".htmlspecialchars($row[$columnas[$i]])."'";
        //$activeSheet->setCellValueByColumnAndRow($x,$y,$row[$columnas[$i]]);
         // $activeSheet->setCellValueByColumnAndRow($x,$y,trim(preg_replace('/\s\s+/', ' ', $row[$columnas[$i]])));
    	 // $activeSheet->setCellValueByColumnAndRow($x,$y,$row[$columnas[$i]]);
    	$activeSheet->setCellValueByColumnAndRow($x,$y,preg_replace(array('/--/','/\+\+/','/==/'), ' ', $row[$columnas[$i]]));
        $x++;
    }
   $x=1;
   $y++;
    //echo $row['name'] . "\n";
}
$filename='hta_control';
//$activeSheet->setCellValue('A1' , 'New file content')->getStyle('A1')->getFont()->setBold(true);
header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment;filename="'. $filename .'.xlsx"'); /*-- $filename is  xsl filename ---*/
header('Cache-Control: max-age=0');
$Excel_writer->save('php://output');
exit;