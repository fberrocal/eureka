<?php
// Require composer autoload
require_once __DIR__ . '/vendor/autoload.php';
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
$plantilla='AIEPRNCE';
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
    "FNACIMIENTO","EDAD","DIRECCION","SEXO","TELEFONORES","GRUPOETNICO","TIPO_USUARIO","IDEMEDICA","IDMEDICO","MEDICO","ACOMP","PARENT","DIR","TEL","INSTITU",
    "TER","DTSCONSULTA","OCUPPADRE","OCUPMADRE","PERRESP","MOTCONSULTA","ENFACTUAL","DUREMBARAZO","COMOFUEEMB",
    "DESCEMB","COMOFUEPARTO","DESPARTO","PESOALNACER","MEDIALNACER","PREPROBLEMA","DESPROB","ENFDADES","DESENF",
    "HOSPPREV","DESHOSPRE","HEMOCLASIF","TEMP","FC","FR","TA","TALLA","PESOACTUAL","PC","IMC","CABYCU","DESCABCU",
    "OJOS","DESOJOS","OIDOS","DESOIDOS","NARIZ","DESNARIZ","BOCA","DESBOCA","CARDIORES","DESCARRES","ABDOMEN",
    "DESABD","GEURI","DESGEURI","MIEINF","DESMIEINF","PIELYFAN","DESPIELYF","EXAMNEU","DESEXAMNE","OBSEXAFIS",
    "EGRAVSEL","CBOXTOS","ENFGRAVE","NROPORINAD","CLSENFGRVE","RECENFGRVE","DIARRLIST","NODIAS","SANGREHESES",
    "ESTGENLIST","CLASDIARREA","CLASDIASAN","CLADESHIDRA","RECODIARREA","CREALIMCB","DIFALIMENTAC","CUALDIFICUL",
    "DEJODCOMER","DSDECUANDO","ALIMLECHMAT","VECES24H","OTROSALIM","ALIMYFREC","PREPARACION","ALIMUTILI","CHUPOS",
    "PESOEDADDE","PESOTALLADE","MENORDE7DIAS","TENDPESO","BOCAABIER","SENMENTON","LABINF","AUREOLA","SUCCION",
    "CACUDER","PECNARPEZ","HIJOMAD","MADSOST","CLACREYALIM","RECCREYALIM","PROBDESCB","MENORMES","ANTFAMIL",
    "PADPARIENT","FAMPROMENFI","CUIDANINO","CVEDESA","PARNENAT","ALTFENOT","DESFENOTIP","PCEF","MEN1MESREAL",
    "AUSMEN1","MAY1MESREAL","AUSMAY1","CLAPROBDESA","RECPROBDESA","MADRE","OTRASVAC","FECPROXVACU","NINO",
    "FECPROXVACNI","TTO","TRATAMIENTO","ENFGRVERES","DESHIDRES","DIARPERRES","DIARSANGRES","NUTRICRES",
    "DESSRES","SUGERENCIA","DX1","DESDX1","DX2","DESDX2","DX3","DESDX3","DX4","DESDX4","TRATSUG","RECOM","DXEGRE1",
    "DESDXE1","DXEGRE2","DESDXE2","DXEGRE3","DESDXE3","DXEGRE4","DESDXE4","CONDUCTA","EVOLUC","VOLVERINME",
    "VOLVERCTRL","MEDPREV","BUENTRATO","REFERIRP","ESPECIAL","PROGCITA","FECHACITA","RESUMEN");

$cabecera=array("SEDE","RAZONSOCIAL","FECHA","TIPO_DOC","IDAFILIADO","PNOMBRE","SNOMBRE","PAPELLIDO","SAPELLIDO",
    "FNACIMIENTO","EDAD","DIRECCION","SEXO","TELEFONO","GRUPOETNICO","TIPO_USUARIO","IDEMEDICA","IDMEDICO","MEDICO","Acompañante:","Parentesco:","Dirección:",
    "Teléfono:","Atendido en:","E.P.S.:","Tipo de Consulta:","Nombre Padre","Nombre Madre","Nombre responsable",
    "Motivo de consulta:","Enfermedad Actual:","¿Cuánto duró el embarazo?","¿Cómo fué el embarazo?","Describa:",
    "¿Cómo fué el parto?","Describa:","¿Cuánto pesó al nacer? (gramos)","¿Cuánto midió al nacer (cms)?",
    "¿Problemas neonatales?","Describa:","¿Enfermedades previas?","Describa:","¿Hospitalizaciones previas?",
    "Describa:","Hemoclasificación:","Temperatura (°C):","F. Cardiaca (/min):","F. Respirat. (/min):",
    "T. Arterial (mmHg):","Talla (Cms):","Peso actual (Gms):","P. Cefálico (Cms):","Ind. Masa Corporal:",
    "Cabeza y Cuello:","Describa:","Ojos:","Describa:","Oídos:","Describa:","Nariz:","Describa:","Boca:",
    "Describa:","Cardio respiratorio:","Describa:","Abdomen:","Describa:","Genito Urinario:","Describa:",
    "Extremidades:","Describa:","Piel y Faneras:","Describa:","Ex. Neurológico:","Describa:","Observaciones",
    "¿Alguna Enfermedad Grave o Infección Local?","Tos o dificultad para respirar?",
    "Opciones para detectar Enf. Grave:","No. de pañales orinados en las últimas 24h",
    "Clasificación Enf. Grave o Inf. Local","Recomendaciones:","¿El niño tiene diarrea?","Desde cuándo (días):",
    "Presencia de sangre en heces:","Estado General:","Clasificación Diarrea Prolongada:",
    "Clasificación Diarrea con sangre:","Clasificación deshidratación:","Recomendaciones:",
    "¿Problemas de crecimiento o Alimentación?","Dificultades de alimentación:","¿Cúal?","Pérdida del apetito:",
    "¿Desde cuándo (días)?","Alimentación exclusiva con Leche materna","¿Cuántas veces en 24H?",
    "Otro tipo de alimentación:","¿Cúal y con qué frecuencia?","¿Cómo prepara la otra leche?",
    "¿Qué utiliza para alimentarlo?","¿Utiliza chupos?","Indicadores de Peso/Edad (DE):",
    "Indicadores de Peso/Talla (DE):","Indicadores de Pérdida de peso:","Tendencia Peso:",
    "Tiene la boca bien abierta?","Toca el Seno con el mentón?","Labio inferior volteado hacia afuera?",
    "Se ve más aureola por encima del labio?","Lenta y profunda con pausas?","Cabeza y cuerpo del niño derechos?",
    "Dirección al Pecho/Nariz Frente Pezón?","Hijo frente a Madre: barriga con barriga?",
    "Madre sostiene todo el cuerpo?","Clasific. Crecimiento y P. Alimentación:","Recomendaciones:",
    "¿Presenta problemas de desarrollo?","Niño menor de un mes?","Evaluar problemas de desarrollo:",
    "Son parientes los padres?","Familiares con problemas mentales o físicos?","Quién cuida al niño?",
    "Cómo ve el desarrollo del niño?","Cómo fué el Parto?","Existen más de 3 alteraciones fenotípicas?",
    "Describa:","P. Cefálico (Cms):","El menor de un mes realiza:","Ausencia de Actividades para menor de 1 mes?",
    "El menor de 1 a 2 meses realiza:","Ausencia de Actividades para mayor de 1 mes?",
    "Clasific. Problemas de desarrollo:","Recomendaciones:","Madre:","Otras Vacunas:",
    "Volver para próxima vacuna el:","Niño/Niña:","Volver para próxima vacuna el:","Tratamiento:",
    "Tratamiento a realizar:","Enfermedad Grave o Inf. Local","Deshidratación:",
    "Diarrea Persistente:","Diarrea con Sangre:","Nutrición:","Desarrollo:","Sugerencia:","Dx Principal:",
    "Diagnóstico","Dx 2:","Diagnóstico","Dx 3:","Diagnóstico","Dx 4:","Diagnóstico","Sugerencias y recomendaciones:",
    "Recomendaciones sobre alimentación:","Dx Principal:","Diagnóstico","Dx 2:","Diagnóstico","Dx 3:",
    "Diagnóstico","Dx 4:","Diagnóstico","Conducta:","EVOLUCION:","Cuándo volver de inmediato/Signos de Alarma",
    "Cuándo volver a consulta de control:","Medidas preventivas específicas:","Recomendaciones de buen trato:",
    "Referir:","Especialidad:","Programación de citas:","Fecha Cita:","Resumen de la consulta:");
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

$consulta="exec spV_HCA_Pivot '$fechaini','$fechafin','$plantilla','$idsede','[ACOMP],[PARENT],[DIR],[TEL],[INSTITU],
[TER],[DTSCONSULTA],[OCUPPADRE],[OCUPMADRE],[PERRESP],[MOTCONSULTA],[ENFACTUAL],[DUREMBARAZO],[COMOFUEEMB],[DESCEMB],
[COMOFUEPARTO],[DESPARTO],[PESOALNACER],[MEDIALNACER],[PREPROBLEMA],[DESPROB],[ENFDADES],[DESENF],[HOSPPREV],
[DESHOSPRE],[HEMOCLASIF],[TEMP],[FC],[FR],[TA],[TALLA],[PESOACTUAL],[PC],[IMC],[CABYCU],[DESCABCU],[OJOS],[DESOJOS],
[OIDOS],[DESOIDOS],[NARIZ],[DESNARIZ],[BOCA],[DESBOCA],[CARDIORES],[DESCARRES],[ABDOMEN],[DESABD],[GEURI],[DESGEURI],
[MIEINF],[DESMIEINF],[PIELYFAN],[DESPIELYF],[EXAMNEU],[DESEXAMNE],[OBSEXAFIS],[EGRAVSEL],[CBOXTOS],[ENFGRAVE],
[NROPORINAD],[CLSENFGRVE],[RECENFGRVE],[DIARRLIST],[NODIAS],[SANGREHESES],[ESTGENLIST],[CLASDIARREA],[CLASDIASAN],
[CLADESHIDRA],[RECODIARREA],[CREALIMCB],[DIFALIMENTAC],[CUALDIFICUL],[DEJODCOMER],[DSDECUANDO],[ALIMLECHMAT],
[VECES24H],[OTROSALIM],[ALIMYFREC],[PREPARACION],[ALIMUTILI],[CHUPOS],[PESOEDADDE],[PESOTALLADE],[MENORDE7DIAS],
[TENDPESO],[BOCAABIER],[SENMENTON],[LABINF],[AUREOLA],[SUCCION],[CACUDER],[PECNARPEZ],[HIJOMAD],[MADSOST],
[CLACREYALIM],[RECCREYALIM],[PROBDESCB],[MENORMES],[ANTFAMIL],[PADPARIENT],[FAMPROMENFI],[CUIDANINO],[CVEDESA],
[PARNENAT],[ALTFENOT],[DESFENOTIP],[PCEF],[MEN1MESREAL],[AUSMEN1],[MAY1MESREAL],[AUSMAY1],[CLAPROBDESA],[RECPROBDESA],
[MADRE],[OTRASVAC],[FECPROXVACU],[NINO],[FECPROXVACNI],[TTO],[TRATAMIENTO],[ENFGRVERES],[DESHIDRES],
[DIARPERRES],[DIARSANGRES],[NUTRICRES],[DESSRES],[SUGERENCIA],[DX1],[DESDX1],[DX2],[DESDX2],[DX3],[DESDX3],[DX4],
[DESDX4],[TRATSUG],[RECOM],[DXEGRE1],[DESDXE1],[DXEGRE2],[DESDXE2],[DXEGRE3],[DESDXE3],[DXEGRE4],[DESDXE4],
[CONDUCTA],[EVOLUC],[VOLVERINME],[VOLVERCTRL],[MEDPREV],[BUENTRATO],[REFERIRP],[ESPECIAL],[PROGCITA],[FECHACITA],
[RESUMEN]' ";

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

$filename='recien_nacido 72 HORAS';
//$activeSheet->setCellValue('A1' , 'New file content')->getStyle('A1')->getFont()->setBold(true);
header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment;filename="'. $filename .'.xlsx"'); /*-- $filename is  xsl filename ---*/
header('Cache-Control: max-age=0');
$Excel_writer->save('php://output');
exit;