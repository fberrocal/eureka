<?php
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
$columnas=array('TipoContrato',	'NOADMISION',	'REGIMENES',	'FECHA_ADMISION',	'FECHAALTA',
    'DIA_ADMISION',	'IDSEDE',	'DESCRIPCION',	'TIPO_DOC',	'IDAFILIADO',	'NOMBREAFILIADO',
    'FNACIMIENTO',	'EDAD',	'SEXO',	'CODIGO_CIUDAD',	'NOMBRE_CIUDAD',	'RAZONSOCIAL',	'ESTADO',
    'MODULO',	'VIA_INGRESO',	'CAUSAEXTERNA',	'DXINGRESO',	'DXEGRESO',	'USUARIO_ADM',	'USUARIOALTA',
    'IDAREA_ALTA',	'N_FACTURA',	'CLASENOPROC');
$x=1;
$y=1;
for($i=0;$i<count($columnas);$i++){
    $activeSheet->setCellValueByColumnAndRow($x,$y,$columnas[$i]);
    $x++;
}

$idtercero=$_POST['idtercero'];
$cerrada=$_POST['cerrada'];
$consulta="
DECLARE @FECHAINI DATETIME='$fechaini';
DECLARE @FECHAFIN DATETIME='$fechafin';
DECLARE @CERRADA VARCHAR(1)='$cerrada' 
DECLARE @IDSEDE VARCHAR(5)='$idsede';
DECLARE @IDTERCERO VARCHAR(20)='$idtercero';

SELECT  
TipoContrato=dbo.fnc_TipoContrato_HADM_xHPRED(noadmision,HADM.IDAFILIADO,hadm.IDTERCERO),
HADM.NOADMISION,
( CASE HADM.TIPOTTEC 
	WHEN 'EPS' THEN 'CONTRIBUTIVO' 
	WHEN 'EPSS' THEN 'SUBSIDIADO'
	WHEN 'VINC' THEN 'VINCULADO'  
	WHEN 'ARL' THEN 'CONTRIBUTIVO'
	WHEN 'ASEG' THEN 'Otro'
	WHEN 'ESE' THEN 'Otro'
	WHEN 'ET' THEN 'Otro'
	WHEN 'FOSYGA' THEN 'Otro'
	WHEN 'IPS' THEN 'Otro'
	WHEN 'PARTJ' THEN 'Particular'
	WHEN 'PARTN' THEN 'Particular'
	WHEN 'PREP' THEN 'Otro'
  END
) AS REGIMENES,
HADM.FECHA AS FECHA_ADMISION,
HADM.FECHAALTA,
(CASE WHEN DATEDIFF(day,hadm.fecha,hadm.fechaalta) IS NULL THEN '' ELSE CONVERT(VARCHAR(4),DATEDIFF(day,hadm.fecha,hadm.fechaalta)) END) AS DIA_ADMISION,
HADM.IDSEDE,
SED.DESCRIPCION,
AFI.TIPO_DOC,
HADM.IDAFILIADO,
dbo.fnNombreAfiliado(AFI.IDAFILIADO,'N') as NOMBREAFILIADO,
CONVERT(VARCHAR(10),AFI.FNACIMIENTO,103) AS FNACIMIENTO,
dbo.fna_EdadenAnos(AFI.FNACIMIENTO,HADM.FECHA) AS EDAD,
AFI.SEXO,
AFI.CIUDAD AS CODIGO_CIUDAD,
CIU.NOMBRE AS NOMBRE_CIUDAD,
TER.RAZONSOCIAL,
( CASE HADM.CERRADA
	WHEN '0' THEN 'Admitido' 
	WHEN '1' THEN 'Alta_Adm'
	WHEN '2' THEN 'Alta_Medica'
  END
) AS ESTADO,
( CASE HADM.CLASEING
	WHEN 'A' THEN 'Hospitalizacion' 
	WHEN 'M' THEN 'Ayuda Dx'
  END
) AS MODULO,
( CASE HADM.VIAINGRESO
	WHEN '1' THEN 'URGENCIAS' 
	WHEN '2' THEN 'CONSULTAEXTERNA PG'
	WHEN '3' THEN 'REMITIDO'
	WHEN '4' THEN 'NACIDO EN INSTITUCION'
	WHEN '5' THEN 'HOSPITALARIA'
	END 
	) AS VIA_INGRESO,
	HADM.CAUSAEXTERNA,
	COALESCE(HADM.DXINGRESO,'') AS DXINGRESO,
	COALESCE(HADM.DXEGRESO,'') AS DXEGRESO,
	HADM.USUARIO AS USUARIO_ADM,
	COALESCE(HADM.USUARIOALTA,'') AS USUARIOALTA,
	COALESCE(HADM.IDAREA_ALTA,'') AS IDAREA_ALTA,
	COALESCE(HADM.N_FACTURA,'') AS N_FACTURA,
	COALESCE(HADM.CLASENOPROC,'') AS CLASENOPROC 
from HADM WITH(INDEX(HADMIDTERCERO)) 
INNER JOIN AFI ON AFI.IDAFILIADO = HADM.IDAFILIADO
INNER JOIN SED ON HADM.IDSEDE = SED.IDSEDE
INNER JOIN CIU ON AFI.CIUDAD = CIU.CIUDAD
INNER JOIN TER ON HADM.IDTERCERO = TER.IDTERCERO 
WHERE 
---PARAMETROS 
HADM.FECHA BETWEEN  @FECHAINI AND @FECHAFIN ---PARAMETRO 1
AND HADM.IDTERCERO =  CASE WHEN COALESCE(@IDTERCERO,'')='' THEN HADM.IDTERCERO ELSE @IDTERCERO END 
AND HADM.CERRADA = CASE WHEN COALESCE(@CERRADA,'')='' THEN HADM.CERRADA ELSE CONVERT(INT,@CERRADA) END 
AND HADM.IDSEDE = CASE WHEN COALESCE(@IDSEDE,'')='' THEN HADM.IDSEDE ELSE @IDSEDE END 
AND HADM.CLASEING <> 'O'
";

$sth = $conn->prepare($consulta);
$sth->execute();
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
/*
$result = $sth->fetchall(PDO::FETCH_ASSOC);

foreach($result as $key=>$row) {
    foreach($row as $key2=>$row2){
        $activeSheet->setCellValueByColumnAndRow($x,$y,$row2);
        $x++;
    }
    $y++;
    $x=1;
}*/

$filename='admisiones_detalladas';
//$activeSheet->setCellValue('A1' , 'New file content')->getStyle('A1')->getFont()->setBold(true);
header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment;filename="'. $filename .'.xlsx"'); /*-- $filename is  xsl filename ---*/
header('Cache-Control: max-age=0');
$Excel_writer->save('php://output');
exit;