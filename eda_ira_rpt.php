<?php
// Require composer autoload
require_once __DIR__ . '/vendor/autoload.php';
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
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


$columnas=array("IDSEDE","DESCRIPCION","SEXO","FNACIMIENTO","IDDX","IDAFILIADO","FECHA");

$cabecera=array("IDSEDE","DESCRIPCION","SEXO","FNACIMIENTO","IDDX","IDAFILIADO","FECHA");
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

$consulta="
DECLARE @Fechaini DATETIME='$fechaini';
DECLARE @FechaFin DATETIME='$fechafin';
SELECT 
HCA.IDSEDE,
SED.DESCRIPCION,
AFI.SEXO,
CONVERT(VARCHAR(10),AFI.FNACIMIENTO,103) AS FNACIMIENTO,
HCA.IDDX,
HCA.IDAFILIADO,
HCA.FECHA


FROM HCA  
INNER JOIN MED ON HCA.IDMEDICO=MED.IDMEDICO
INNER JOIN AFI ON HCA.IDAFILIADO=AFI.IDAFILIADO
INNER JOIN TER ON AFI.IDADMINISTRADORA=TER.IDTERCERO
INNER JOIN MPL ON HCA.CLASEPLANTILLA=MPL.CLASEPLANTILLA
INNER JOIN SED ON HCA.IDSEDE=SED.IDSEDE

WHERE
 HCA.FECHA  between @FechaIni and @FechaFin AND HCA.ESTADO='Activa' AND HCA.PROCEDENCIA='CE' AND MED.IDEMEDICA='900' AND HCA.IDDX IN ('A048','A049','A053','A058','A059','A084','A085','A09X','A000','A001','A009',
	'K580','K591','J00X','J010','J011','J012','J013','J014','J018' ,'J020','J028','J029','J030','J038','J039',
	'J040','J041','J042','J050','J051' ,'J060' ,'J068' ,'J069' ,'J09','J18' ,'J100' ,'J101','J108' ,'J110',
	 'J111','J118' ,'J120','J121','J122','J128','J13','J14','J150','J151','J152','J153', 'J154','J155','J156','J157','J158','J159','J160'
	 ,'J168','J170','J171','J172','J173','J178','J180','J181','J182','J188','J189','J20','J22','J200','J201','J202','J203'
	 ,'J204','J205','J206','J207','J208','J209','J210','J218','J219','J22X','U071','U072' )
UNION ALL
SELECT
CIT.IDSEDE,
SED.DESCRIPCION,
AFI.SEXO,
CONVERT(VARCHAR(10),AFI.FNACIMIENTO,103) AS FNACIMIENTO,
CIT.IDDX,
CIT.IDAFILIADO,
CIT.FECHA

FROM CIT
	INNER JOIN MED ON CIT.IDMEDICO = MED.IDMEDICO
	INNER JOIN SED ON CIT.IDSEDE = SED.IDSEDE
	INNER JOIN SER ON CIT.IDSERVICIO = SER.IDSERVICIO
	INNER JOIN AFI ON CIT.IDAFILIADO = AFI.IDAFILIADO 
	INNER JOIN USUSU ON CIT.USUARIO = USUSU.USUARIO

	 WHERE  
	CIT.FECHA between @FechaIni and @FechaFin and CIT.IDEMEDICA IN ('900') AND CIT.CITASIMULTANEA='1' AND CIT.IDDX IN ('A048','A049','A053','A058','A059','A084','A085','A09X','A000','A001','A009',
	'K580','K591','J00X','J010','J011','J012','J013','J014','J018' ,'J020','J028','J029','J030','J038','J039',
	'J040','J041','J042','J050','J051' ,'J060' ,'J068' ,'J069' ,'J09','J18' ,'J100' ,'J101','J108' ,'J110',
	 'J111','J118' ,'J120','J121','J122','J128','J13','J14','J150','J151','J152','J153', 'J154','J155','J156','J157','J158','J159','J160'
	 ,'J168','J170','J171','J172','J173','J178','J180','J181','J182','J188','J189','J20','J22','J200','J201','J202','J203'
	 ,'J204','J205','J206','J207','J208','J209','J210','J218','J219','J22X','U071','U072' )

";

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

$filename='eda_ira';
//$activeSheet->setCellValue('A1' , 'New file content')->getStyle('A1')->getFont()->setBold(true);
header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment;filename="'. $filename .'.xlsx"'); /*-- $filename is  xsl filename ---*/
header('Cache-Control: max-age=0');
$Excel_writer->save('php://output');
exit;