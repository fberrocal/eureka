<?php
// Require composer autoload
require_once __DIR__ . '/vendor/autoload.php';
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

$plantilla='COVIDSD';
$spreadsheet  = new Spreadsheet();  /*----Spreadsheet object-----*/
$Excel_writer = new Xlsx($spreadsheet);  /*----- Excel (Xls) Object*/
$spreadsheet->setActiveSheetIndex(0);
$activeSheet  = $spreadsheet->getActiveSheet();

$subconsulta = "";

require_once('config.php');
require_once('database.class.php');
$conn = new Database();

if (!isset($_POST['idsede'])){
    $idsede='';
} else {
    $idsede=$_POST['idsede'];
}

$parts     = explode('-', $_POST['fechaini']);
$fechaini  = $parts[2].'/'.$parts[1].'/'.$parts[0];

$parts     = explode('-', $_POST['fechafin']);
$fechafin  = $parts[2].'/'.$parts[1].'/'.$parts[0];

$fechaini.=' 00:00:00.000';
$fechafin.=' 23:59:59.997';

$columnas = array(
	"IDADMINISTRADORA",
	"RAZONSOCIAL",
	"TIPO_DOC",
	"IDAFILIADO",
	"PAPELLIDO",
	"SAPELLIDO",
	"PNOMBRE",
	"SNOMBRE",
	"FNACIMIENTO",
	"EDAD",
	"SEXO",
	"DIRECCION",
	"TELEFONORES",
	"GRUPOETNICO",
	"TIPODISCAPACIDAD",
	"ESTADOCIVIL",
	"IDESCOLARIDAD",
	"OCUPACIONC",
	"CONSECUTIVO",
	"IDSEDE",
	"FECHA",
	"CLASEPLANTILLA",
	"TIPO_USUARIO",
	"IDMEDICO",
	"MEDICO",
	"ESPECIALIDADM",
	"NOHISTO",
	"HISTORI",
	"RESPSEG",
	"FESEGUI",
	"HOSEGHI",
	"DIASEGUI",
	"ESTSALUD",
	"DESCEST",
	"FIEBM38",
	"DESCFIE",
	"TOS",
	"DESCTOS",
	"DIFRESP",
	"DESCDIF",
	"ODINOFAG",
	"DESCODI",
	"FATIGA",
	"DESCFAT",
	"CUMREC",
	"DESCREC",
	"OBSHC",
	"CLAFECH"
);
	
$cabecera = array(
	"IDADMINISTRADORA",
	"RAZONSOCIAL",
	"TIPO_DOC",
	"IDAFILIADO",
	"PAPELLIDO",
	"SAPELLIDO",
	"PNOMBRE",
	"SNOMBRE",
	"FNACIMIENTO",
	"EDAD",
	"SEXO",
	"DIRECCION",
	"TELEFONORES",
	"GRUPOETNICO",
	"TIPODISCAPACIDAD",
	"ESTADOCIVIL",
	"IDESCOLARIDAD",
	"OCUPACIONC",
	"CONSECUTIVO",
	"IDSEDE",
	"FECHA",
	"CLASEPLANTILLA",
	"TIPO_USUARIO",
	"IDMEDICO",
	"MEDICO",
	"ESPECIALIDADM",
	"NOHISTO",
	"HISTORI",
	"RESPSEG",
	"FESEGUI",
	"HOSEGHI",
	"DIASEGUI",
	"ESTSALUD",
	"DESCEST",
	"FIEBM38",
	"DESCFIE",
	"TOS",
	"DESCTOS",
	"DIFRESP",
	"DESCDIF",
	"ODINOFAG",
	"DESCODI",
	"FATIGA",
	"DESCFAT",
	"CUMREC",
	"DESCREC",
	"OBSHC",
	"CLAFECH"
);

$x=1;
$y=1;

for($i=0;$i<count($cabecera);$i++){
    $activeSheet->setCellValueByColumnAndRow($x,$y,$cabecera[$i]);
    $x++;
}

$subconsulta = "Select 
		AFI.IDADMINISTRADORA,
		TER.RAZONSOCIAL,
		AFI.TIPO_DOC, 
		HCA.IDAFILIADO,
		COALESCE(AFI.PAPELLIDO,'''') AS PAPELLIDO, 
		COALESCE(AFI.SAPELLIDO,'''') AS SAPELLIDO, 
		COALESCE(AFI.PNOMBRE,'''') AS PNOMBRE, 
		COALESCE(AFI.SNOMBRE,'''') AS SNOMBRE,  
		CONVERT(VARCHAR(12),AFI.FNACIMIENTO,103) AS FNACIMIENTO,
		dbo.fna_EdadenAnos(AFI.FNACIMIENTO,HCA.FECHA) AS EDAD, 
		AFI.SEXO,	
		AFI.DIRECCION,
		AFI.TELEFONORES,
		COALESCE(t1.DESCRIPCION,'') AS GRUPOETNICO, 
		COALESCE(t2.DESCRIPCION,'') AS TIPODISCAPACIDAD, 
		COALESCE(t3.DESCRIPCION,'') AS ESTADOCIVIL, 
		COALESCE(t4.DESCRIPCION,'NINGUNO') AS IDESCOLARIDAD, 
		COALESCE(OCU.DESCRIPCION,'') AS OCUPACIONC, 
		HCAD.CONSECUTIVO, 
		SUBSTRING(ltrim(rtrim(HCAD.CONSECUTIVO)), 1, 2) as IDSEDE,
		HCA.FECHA, 
		HCAD.CLASEPLANTILLA, 
		MED.TIPO_USUARIO,
		HCA.IDMEDICO, 
		MED.NOMBRE AS MEDICO,   
		MED.IDEMEDICA AS ESPECIALIDADM,
		HCAD.CAMPO AS [CAMPO], 
	    (case hcad.TIPOCAMPO when 'Alfanumerico' then coalesce(hcad.ALFANUMERICO,'') when 'Lista' then coalesce(hcad.ALFANUMERICO,'') + coalesce(hcad.LISTA,'') when 'Fecha' then CONVERT(VARCHAR(10),hcad.FECHA,103) when 'Memo' then CAST(hcad.MEMO AS varchar(500)) end) AS VARIABLE
	FROM 
		hca with (nolock) inner join HCAD with (nolock) on hcad.CONSECUTIVO=hca.CONSECUTIVO
		INNER JOIN AFI ON AFI.IDAFILIADO=HCA.IDAFILIADO 
		INNER JOIN TER ON TER.IDTERCERO=AFI.IDADMINISTRADORA 
		INNER JOIN MED ON MED.IDMEDICO=HCA.IDMEDICO 
		LEFT JOIN SED ON SED.IDSEDE=HCA.IDSEDE 
		LEFT JOIN TGEN t1 ON t1.tabla='AFI' and t1.campo='GRUPOETNICO' and AFI.GRUPOETNICO=t1.CODIGO	
		LEFT JOIN TGEN t2 ON t2.tabla='AFI' and t2.campo='TIPODISCAPACIDAD' and AFI.TIPODISCAPACIDAD=t2.CODIGO
		LEFT JOIN TGEN t3 ON t3.tabla='AFI' and t3.campo='ESTADO_CIVIL' and AFI.ESTADO_CIVIL=t3.CODIGO
		LEFT JOIN TGEN t4 ON t4.tabla='AFI' and t4.campo='IDESCOLARIDAD' and AFI.IDESCOLARIDAD=t4.CODIGO
		LEFT JOIN OCU ON AFI.IDOCUPACION=OCU.OCUPACION
	WHERE  
		HCA.CLASEPLANTILLA='COVIDSD' and HCA.fecha between '$fechaini' and '$fechafin'";

if( $idsede != '' ) {
	// $subconsulta.= " and HCA.IDSEDE='".$idsede."'";
	$subconsulta.= " and SUBSTRING(ltrim(rtrim(HCAD.CONSECUTIVO)), 1, 2)='".$idsede."'";
}		

$consulta = "SELECT * FROM (" . $subconsulta . "
)PIV PIVOT(MAX(VARIABLE) FOR CAMPO IN ([NOHISTO],[HISTORI],[RESPSEG],[FESEGUI],[HOSEGHI],[DIASEGUI],[ESTSALUD],[DESCEST],[FIEBM38],[DESCFIE],[TOS],[DESCTOS],[DIFRESP],[DESCDIF],[ODINOFAG],[DESCODI],[FATIGA],[DESCFAT],[CUMREC],[DESCREC],[OBSHC],[CLAFECH])) X";

$sth = $conn->prepare($consulta);
$sth->execute();

$x=1;
$y=2;
while ($row = $sth->fetch())
{
    for($i=0;$i<count($columnas);$i++){
        $activeSheet->setCellValueByColumnAndRow($x,$y,preg_replace(array('/--/','/\+\+/','/==/'), ' ',$row[$columnas[$i]]));
        $x++;
    }
    $x=1;
    $y++;
}

$filename='covidsd';
header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment;filename="'. $filename .'.xlsx"'); /*-- $filename is  xsl filename ---*/
header('Cache-Control: max-age=0');
$Excel_writer->save('php://output');
exit;
    