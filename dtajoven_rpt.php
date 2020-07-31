<?php
// Require composer autoload
require_once __DIR__ . '/vendor/autoload.php';
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
$plantilla='DTAJOVEN';
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
SELECT 'FECHA_HC' AS CAMPO, 'FECHA_HC' AS DESCCAMPO
UNION ALL
SELECT 'CLASEPLANTILLA' AS DESCCAMPO, 'CLASEPLANTILLA' AS DESCCAMPO
UNION ALL
select CAMPO, DESCCAMPO from mpld where CLASEPLANTILLA='$plantilla' and campo in('CLASIFIADOL2' , 'MOTICON' , 'ENFEACT' , 
'VALRIES' , 'GESTA2' , 'SINRESP' , 'OBESID2' , 'ENFEMEN' , 'CACERVIX' , 'CASENO2' ,
'VIOLENCIA' , 'VICMALT' , 'VICTIMAL' , 'RIESGO' , 'MALTRATO' , 'AYUDA' , 'VICTVIO' , 'ITS' , 'REVISISTEMAS' , 'ORGANOSSEN' ,
'RESPIRA' , 'Digestivo' , 'GENITOURI' , 'OSTEOMUSC' , 'Neurologico' , 'PIELFANERA' , 'TOS' , 'EXAMFISICO' , 'SIGNOV' , 'FC' ,
'FR' , 'TA' , 'TEMPE' , 'PESO' , 'TALLA' , 'IMC' , 'EXPLORA' , 'ESTAGEN' , 'CABEZA' , 'ORGSENT' , 'CUELLO' , 'CARDIOPUL' , 
'TORAXMAMA' , 'ABDOME' , 'GENI' , 'OSTEOM2' , 'NEURO2' , 'VASUPER2' , 'PIELFANE2' , 'OTROS23' , 'TESTICULOS' , 'EXAMTES' , 
'MAMRIOTA' , 'MAMARIO' , 'TANERPUBI' , 'VARONESTAD' , 'PUBICO1' , 'PUBIANO2' , 'LABORA' , 'FECHAHEMO' ,
'Resultado1' , 'Hematocrito' , 'FECHAHEMATO' , 'RESULHEMATO' , 'VIH' , 'FECHAVIH' , 'ResultadoVIH' , 'VDRL' , 'FECHVDR' , 
'RESULVDRL' , 'FECHACITOLO' , 'RESULCITO' , 'FECHAPROX','HDL' , 'FECHAHDL' ,'PLANINTER', 'RESULHDL' ,'RESULTUROANA',
 'FECHAUROANA','REMITI','ESPECILAIDAD','FECHAPROX')";
/*
$columnas=array("SEDE","RAZONSOCIAL","FECHA","TIPO_DOC","IDAFILIADO","PNOMBRE","SNOMBRE","PAPELLIDO","SAPELLIDO",
    "FNACIMIENTO","EDAD","DIRECCION","SEXO","TELEFONORES","GRUPOETNICO","PESO","TALLA","IMC","TA","PERIME","FECHAHEMO","Resultado1",
    "FECHAVIH","ResultadoVIH","FECHVDR","RESULVDRL","FECHACITOLO","RESULCITO","FECHAHDL","RESULHDL","FECHAHEMATO","RESULHEMATO",
    "FECHAUROANA","RESULTUROANA","FECHCITO2","RESULCIT2","FECPREVIH","ASESOPREVIH","REMITI","ESPECILI","FECHAPROX");

$cabecera=array("SEDE","RAZONSOCIAL","FECHA","TIPO_DOC","IDAFILIADO","PNOMBRE","SNOMBRE","PAPELLIDO","SAPELLIDO",
    "FNACIMIENTO","EDAD","DIRECCION","SEXO","TELEFONO","GRUPOETNICO","PESO","TALLA","IMC","TA","PERIMETRO ABDOMINAL",
    "FECHA GLICEMIA","RESULTADO GLICEMIA", "FECHA COLESTEROL TOTAL","RESULTADO COLESTEROL TOTAL","FECHA HDL","RESULTADO HDL",
    "FECHA LDL","RESULTADO LDL","FECHA TRIGLICERIDOS","RESULTADO TRIGLICERIDOS","FECHA CREATININA","RESULTADO CREATININA",
    "FECHA UROANALISIS","RESULTADO UROANALISIS","FECHA CITOLOGIA","RESULTADO CITOLOGIA","FECHA MAMOGRAFIA","RESULTADO MAMOGRAFIA",
    "REMITIDO","ESPECIALIDAD","FECHA PROXIMA CITA");*/
$columnas=array();
$i=0;
$sth = $conn->prepare($cabecera);
$sth->execute();
$result = $sth->fetchall(PDO::FETCH_ASSOC);
$x=1;
$y=1;
foreach($result as $row) {
    //echo '<br>'.$row['DESCCAMPO'];
    $activeSheet->setCellValueByColumnAndRow($x,$y,$row['DESCCAMPO']);
    $columnas[$i]=$row['CAMPO'];
    $i++;
    $x++;
}

$consulta="exec spV_HCA_Pivot '$fechaini','$fechafin','$plantilla','$idsede','[CLASIFIADOL2] , [MOTICON] , [ENFEACT] , 
[VALRIES] , [GESTA2] , [SINRESP] , [OBESID2] , [ENFEMEN] , [CACERVIX] , [CASENO2] ,
[VIOLENCIA] , [VICMALT] , [VICTIMAL] , [RIESGO] , [MALTRATO] , [AYUDA] , [VICTVIO] , [ITS] , [REVISISTEMAS] , [ORGANOSSEN] ,
[RESPIRA] , [Digestivo] , [GENITOURI] , [OSTEOMUSC] , [Neurologico ] , [PIELFANERA] , [TOS] , [EXAMFISICO] , [SIGNOV] , [FC] ,
[FR] , [TA] , [TEMPE] , [PESO] , [TALLA] , [IMC] , [EXPLORA] , [ESTAGEN] , [CABEZA] , [ORGSENT] , [CUELLO] , [CARDIOPUL] , 
[TORAXMAMA] , [ABDOME] , [GENI] , [OSTEOM2] , [NEURO2] , [VASUPER2] , [PIELFANE2] , [OTROS23] , [TESTICULOS] , [EXAMTES] , 
[MAMRIOTA] , [MAMARIO] , [TANERPUBI] , [VARONESTAD] , [PUBICO1] , [PUBIANO2] , [LABORA] , [FECHAHEMO] ,
[Resultado1] , [Hematocrito] , [FECHAHEMATO] , [RESULHEMATO] , [VIH] , [FECHAVIH] , [ResultadoVIH] , [VDRL] , [FECHVDR] , 
[RESULVDRL] , [FECHACITOLO] , [RESULCITO] ,[HDL] , [FECHAHDL] ,[PLANINTER], [RESULHDL] ,
[RESULTUROANA], [FECHAUROANA],[REMITI],[ESPECILAIDAD],[FECHAPROX]' ";


$sth = $conn->prepare($consulta);
$sth->execute();
if(!$sth){
    print_r($sth->errorInfo());
}
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

$filename='dta_joven';
//$activeSheet->setCellValue('A1' , 'New file content')->getStyle('A1')->getFont()->setBold(true);
header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment;filename="'. $filename .'.xlsx"'); /*-- $filename is  xsl filename ---*/
header('Cache-Control: max-age=0');
$Excel_writer->save('php://output');
exit;