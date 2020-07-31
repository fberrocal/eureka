<?php
// Require composer autoload
require_once __DIR__ . '/vendor/autoload.php';
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
$plantilla='HCRECIEN';
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

$columnas=array("SEDE","RAZONSOCIAL","FECHA_HC","TIPO_DOC","IDAFILIADO","PAPELLIDO","SAPELLIDO","PNOMBRE","SNOMBRE",
    "FNACIMIENTO","EDAD","DIRECCION","SEXO","TELEFONORES","GRUPOETNICO","TIPO_USUARIO","IDEMEDICA","IDMEDICO","MEDICO","DTSCONSULTA","MOTCONSULTA","ENFACTUAL",
    "FENACIM","HONACIM","EDADG","CONDINAC","NUMCERT","PESO","TALLARN","PC","PT","TEMP","1MIN","5MIN","10MIN","20MIN",
    "OBSRN","TERMRN","TAMRN","CLSARNTER","NREANIMAC","CLASIREANIM","RECOMANIM","REANIMAC","OBSREAN","FALLECE",
    "NCERTIF","CONDUCTA","ATIENDEP","NOMPARTO","ATENDERN","NOMBREATRN","RUPTMEMBR","TIEMPO","LIQUIRUP","FIEBREMAT",
    "TIEMPOFM","CORIOFC","INFECIU","HISTINGES","ANTVIOL","OTRALTER","RESP","LLANTO","VITAL","FRECUEN","CORIOAM",
    "ORIENENAT","ASPECTG","EXAFIS","LESIPART","ANOMAL","ENFERMEDS","TAMIZA","VDRL","TRATAVDR","TSH","BILIRRUB",
    "MECONIO","BOCAARRIBA","RIESGONEO","DX","DESDX1","DX1","DESDX2","DX2","DESDX3","DX3","DESDX4","CONDUCT",
    "FECHAGRE","ESTADOSAL","COMPLEJO","FALLECETRAS","EDADEGRE","MEDIDA","LACTANCIA","BCG","HB","PESOEGRE","RNUIP",
    "NUIP");

$cabecera=array("SEDE","RAZONSOCIAL","FECHA","TIPO_DOC","IDAFILIADO","PAPELLIDO","SAPELLIDO","PNOMBRE","SNOMBRE",
    "FNACIMIENTO","EDAD","DIRECCION","SEXO","TELEFONO","GRUPOETNICO","TIPO_USUARIO","IDEMEDICA","IDMEDICO","MEDICO","Tipo de consulta:","Motivo de consulta:",
    "Enfermedad actual:","Fecha nacimiento:","Hora nacimiento:","Edad gestacional(Sem)",
    "Condición al nacimiento del recién nacido","Número de Certificado","Peso al nacer","Talla (cm):",
    "PC (cm):","Perímetro Torácico","Temp (°C):","1 minuto (/10):","5 minutos (/10):","10 minutos (/10):",
    "20 minutos (/10):","Observaciones:","Clasificación RN según edad gestacional",
    "Clasificación RN según Peso/Edad gestacional","Clasificación RN según Peso","Necesidad de Reanimación:",
    "Valoración necesidad de reanimación","Recomendaciones","Actuación según necesidad de reanimación:",
    "Observaciones:","Fallece en sala de parto","Número de Certificado","Conducta","Atendió Parto",
    "Nombre de Personal","Atendió Recién Nacido","Nombre de Personal","Ruptura prematura de membranas:",
    "Tiempo (Horas):","LÍQUIDO","Fiebre materna o Corioamnionitis:","Tiempo:","FC (/min):",
    "Infección intrauterina:","Historia de ingesta de:","Antecedente de violencia o maltrato:","OTRAS ALTERACIONES:",
    "Respiración:","Llanto:","Vitalidad:","Frecuencia Cardíaca","Corioamnionitis","Riesgo Neonatal",
    "Aspecto General","EXAMEN FISICO DEL RECIÉN NACIDO","Lesiones debidas al parto:","Anomalias congénitas",
    "Enfermedades","Tamización Neonatal","VDRL","Tratamiento","TSH","Bilirrubina en sangre","Meconio primer día",
    "Boca Arriba","Clasificación Riesgo Neonatal","Dx Principal:","Diagnóstico","Dx 1:","Diagnóstico","Dx 2",
    "Diagnóstico","Dx 3:","Diagnóstico","Conducta","Fecha de Egreso","Estado","Referencia a mayor complejidad",
    "Fallece después de traslado","Edad al egreso","Tiempo","Lactancia","BCG","Hepatitis B","Peso al egreso",
    "Registro civil de nacimiento","NUIP");
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

$consulta="exec spV_HCA_Pivot '$fechaini','$fechafin','$plantilla','$idsede','[DTSCONSULTA],[MOTCONSULTA],[ENFACTUAL],
[FENACIM],[HONACIM],[EDADG],[CONDINAC],[NUMCERT],[PESO],[TALLARN],[PC],[PT],[TEMP],[1MIN],[5MIN],[10MIN],[20MIN],
[OBSRN],[TERMRN],[TAMRN],[CLSARNTER],[NREANIMAC],[CLASIREANIM],[RECOMANIM],[REANIMAC],[OBSREAN],[FALLECE],[NCERTIF],
[CONDUCTA],[ATIENDEP],[NOMPARTO],[ATENDERN],[NOMBREATRN],[RUPTMEMBR],[TIEMPO],[LIQUIRUP],[FIEBREMAT],[TIEMPOFM],
[CORIOFC],[INFECIU],[HISTINGES],[ANTVIOL],[OTRALTER],[RESP],[LLANTO],[VITAL],[FRECUEN],[CORIOAM],[ORIENENAT],
[ASPECTG],[EXAFIS],[LESIPART],[ANOMAL],[ENFERMEDS],[TAMIZA],[VDRL],[TRATAVDR],[TSH],[BILIRRUB],[MECONIO],[BOCAARRIBA],
[RIESGONEO],[DX],[DESDX1],[DX1],[DESDX2],[DX2],[DESDX3],[DX3],[DESDX4],[CONDUCT],[FECHAGRE],[ESTADOSAL],[COMPLEJO],
[FALLECETRAS],[EDADEGRE],[MEDIDA],[LACTANCIA],[BCG],[HB],[PESOEGRE],[RNUIP],[NUIP]' ";

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

$filename='recien_nacido_Obstetricia';
//$activeSheet->setCellValue('A1' , 'New file content')->getStyle('A1')->getFont()->setBold(true);
header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment;filename="'. $filename .'.xlsx"'); /*-- $filename is  xsl filename ---*/
header('Cache-Control: max-age=0');
$Excel_writer->save('php://output');
exit;