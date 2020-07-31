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


$columnas=array("IDSEDE","DESCRIPCION","IDAFILIADO","SEXO","FNACIMIENTO","IDDX");

$cabecera=array("IDSEDE","DESCRIPCION","IDAFILIADO","SEXO","FNACIMIENTO","IDDX");
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
HCA.IDAFILIADO,
AFI.SEXO,
CONVERT(VARCHAR(10),AFI.FNACIMIENTO,103) AS FNACIMIENTO,
HCA.IDDX
FROM HCA  
INNER JOIN MED ON HCA.IDMEDICO=MED.IDMEDICO
INNER JOIN AFI ON HCA.IDAFILIADO=AFI.IDAFILIADO
INNER JOIN TER ON AFI.IDADMINISTRADORA=TER.IDTERCERO
INNER JOIN MPL ON HCA.CLASEPLANTILLA=MPL.CLASEPLANTILLA
INNER JOIN SED ON HCA.IDSEDE=SED.IDSEDE

WHERE
 HCA.FECHA  between @FechaIni and @FechaFin AND HCA.ESTADO='Activa' AND MED.IDEMEDICA='900'

UNION ALL
 SELECT

CIT.IDSEDE,
SED.DESCRIPCION,
CIT.IDAFILIADO,
AFI.SEXO,
CONVERT(VARCHAR(10),AFI.FNACIMIENTO,103) AS FNACIMIENTO,
CIT.IDDX

FROM CIT
	INNER JOIN MED ON CIT.IDMEDICO = MED.IDMEDICO
	INNER JOIN SED ON CIT.IDSEDE = SED.IDSEDE
	INNER JOIN SER ON CIT.IDSERVICIO = SER.IDSERVICIO
	INNER JOIN AFI ON CIT.IDAFILIADO = AFI.IDAFILIADO 
	INNER JOIN USUSU ON CIT.USUARIO = USUSU.USUARIO

	 WHERE  
	CIT.FECHA between @FechaIni and @FechaFin and CIT.IDEMEDICA IN ('900') AND CIT.CITASIMULTANEA='1'

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

$filename='totaldeconsultas';
//$activeSheet->setCellValue('A1' , 'New file content')->getStyle('A1')->getFont()->setBold(true);
header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment;filename="'. $filename .'.xlsx"'); /*-- $filename is  xsl filename ---*/
header('Cache-Control: max-age=0');
$Excel_writer->save('php://output');
exit;