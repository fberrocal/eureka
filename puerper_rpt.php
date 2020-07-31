<?php
// Require composer autoload
require_once __DIR__ . '/vendor/autoload.php';
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
$plantilla='PUERPER';
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
    "FNACIMIENTO","EDAD","DIRECCION","SEXO","TELEFONORES","GRUPOETNICO","MOTCONSU","ENFACTU","CABEZA","ORGESENT",
    "CRESPI","TORAXMA","Abdomen","IQUIRUR","GURINARIO","OSTEOMUS","NEUROLO","VASCULARP","PIELFANERA","Insomnio ",
    "IRRITA","LLANTO","FINTERES","FC","FR","TARTERIAL","TEMP","PESO","TALLA ","IMC","ALERTA3","ESTAGENER",
    "CABEZA2","ORGSENTID","CUELLO","CARDIOPUL","TORAXMA2","ALERTA4","ABDOMEN2","valoración","CLAALTUR",
    "GENITOURI2","SANGRAD","TIPO","ASPECTO","CANTIDA","OLOR","OSTEOMUSC","NEURO2","VASCUPER2","PIELFANE2",
    "TACVAG","OTROS2","SINTORES","OBEPROTE","VICTMAL","VIOLENE","ENFEMEN","CACERVIX","CASENO","LEXCLUSI",
    "OTLECHES","TIPOLE","LECHE","SMENTON","ABOCA","LNINO","AREOLA","SLENTA","EAGARRE","PMETODO","FECHAM",
    "TMETODO","PMEDICA","INTERCO","RECOMEN","REMITIDO","MOTIVOREM","ESPECIALIDAD");

$cabecera=array("SEDE","RAZONSOCIAL","FECHA","TIPO_DOC","IDAFILIADO","PNOMBRE","SNOMBRE","PAPELLIDO","SAPELLIDO",
    "FNACIMIENTO","EDAD","DIRECCION","SEXO","TELEFONO","GRUPOETNICO","MOTIVO CONSULTA ","ENFERMEDAD ACTUAL","Cabeza",
    "Organos de los sentidos ","Cardiopulmonar","Torax / Mamas","Abdomen","Insición quirúrgica ","Genitourinario",
    "Osteomuscular","Neurologico ","Vascular periferico ","Piela y faneras","Insomnio ","Irritabilidad",
    "Llanto facil ","Falta de Interés","FRECUENCIA CARDIACA /MIN","FEECUENCIA RESPIRATORIA /MIN",
    "TENSION ARTERIAL mmHg","TEMPERATURA °C","PESO Kg","TALLA M","IMC ","ALERTA","Estado general","Cabeza ",
    "Organos de los sentidos ","Cuello","Cardiopulmonar ","Torax / Mamas","ALERTA","Abdomen","valoración",
    "Clasificacion altura uterina","Genitourinario","Presencia sangrado o exudados?","TIPO","ASPECTO","CANTIDAD",
    "OLOR","Osteomuscular","Neurologico ","Vascular periferico","Piel y faneras","Tacto vaginal","Otros hallazgos ",
    "Sintomatico respiratorio","Obesidad o Desnutrición Proteico Calórica ","Victima de maltrato",
    "Victima de violencia sexual ","Enfermedad mental","Cancer de cervix ","Cancer de seno ",
    "Brinda lactancia materna exclusiva","El niño recibe otras leches","Tipo de leche que recibe",
    "Motivo consumo de otras leches y/o alimentos","El niño toca el seno con el mentón",
    "El niño abre bien la boca mientras amamanta","el labio inferior está volteado hacia afuera",
    "areola se ve mas por encima de la boca del bb","Succiona en forma lenta y profunda","Evaluación del agarre",
    "Prescripcion metodo anticonceptivo ","Fecha inicio metodo","Tipo de Método ","Prescripcion de otros medicamentos",
    "Interconsultas","Recomendaciones ","Remitido","Motivo de remision ","Especialidad ");
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


$consulta="exec spV_HCA_Pivot '$fechaini','$fechafin','$plantilla','$idsede','[MOTCONSU],[ENFACTU],[CABEZA],[ORGESENT],
[CRESPI],[TORAXMA],[Abdomen],[IQUIRUR],[GURINARIO],[OSTEOMUS],[NEUROLO],[VASCULARP],[PIELFANERA],[Insomnio ],
[IRRITA],[LLANTO],[FINTERES],[FC],[FR],[TARTERIAL],[TEMP],[PESO],[TALLA ],[IMC],[ALERTA3],[ESTAGENER],[CABEZA2],
[ORGSENTID],[CUELLO],[CARDIOPUL],[TORAXMA2],[ALERTA4],[ABDOMEN2],[valoración],[CLAALTUR],[GENITOURI2],[SANGRAD],
[TIPO],[ASPECTO],[CANTIDA],[OLOR],[OSTEOMUSC],[NEURO2],[VASCUPER2],[PIELFANE2],[TACVAG],[OTROS2],[SINTORES],
[OBEPROTE],[VICTMAL],[VIOLENE],[ENFEMEN],[CACERVIX],[CASENO],[LEXCLUSI],[OTLECHES],[TIPOLE],[LECHE],[SMENTON],
[ABOCA],[LNINO],[AREOLA],[SLENTA],[EAGARRE],[PMETODO],[FECHAM],[TMETODO],[PMEDICA],[INTERCO],[RECOMEN],[REMITIDO],
[MOTIVOREM],[ESPECIALIDAD]' ";

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
$filename='historiaclinica_postparto';
//$activeSheet->setCellValue('A1' , 'New file content')->getStyle('A1')->getFont()->setBold(true);
header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment;filename="'. $filename .'.xlsx"'); /*-- $filename is  xsl filename ---*/
header('Cache-Control: max-age=0');
$Excel_writer->save('php://output');
exit;