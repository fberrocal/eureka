<?php
// Require composer autoload
require_once '../vendor/autoload.php';
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

$spreadsheet  = new Spreadsheet();
$Excel_writer = new Xlsx($spreadsheet);
$spreadsheet->setActiveSheetIndex(0);
$activeSheet  = $spreadsheet->getActiveSheet();

$caracteres = array('/\s\s+/','/-/','/\+/','/=/');

require_once('../database.class.php');
$conn = new Database();

if(!isset($_POST['idsede'])){
    $idsede='';
}else{
    $idsede=$_POST['idsede'];
}

$parts    = explode('-', $_POST['fechaini']);
$fechaini = $parts[2].'/'.$parts[1].'/'.$parts[0];
$parts    = explode('-', $_POST['fechafin']);
$fechafin = $parts[2].'/'.$parts[1].'/'.$parts[0];

// $idmedico   = $_POST['idmedico'];
// $idservicio = $_POST['idservicio'];

$fechaini.=' 00:00:00.000';
$fechafin.=' 23:59:59.997';

$columnas=array("TABLA","PRESTACIONID","CONSECUTIVO","ITEM","CODIGORIPS","ARCHIVORIPS","PYP","IDTERCEROFACT","CODSGSSS","IDADMINISTRADORA","RAZONSOCIAL","IDCONTRATANTE","IDPLAN",
                "N_FACTURA","IDAFILIADO","PNOMBRE","SNOMBRE","PAPELLIDO","SAPELLIDO","SEXO","FNACIMIENTO","TIPO_DOC","DOCIDAFILIADO","EDADAFI","UNIDEDEDAD","FECHA","FINGRESO",
				"NOAUTORIZACION","FEGRESO","IDSERVICIO","IDALTERNA","TIPOMED","TIPOSERVICIO","DESCSERVICIO","NOMGENERICO","FORMA","CONCENTRACION","UMEDIDA","CANTIDAD","AMBITO",
				"FINALIDAD","VIAINGRESO","PERSONALAT","IDDX","TIPODX","DXSALIDA","DX1","DX2","DX3","COMPLICACION","FORMAREALIZACION","CAUSAEXT","CAUSAMUERTE","DESTINO",
				"ESTADOSALIDA","VALOR","VALORCOPAGO","VALORITEM","VRNETO","ESTADOFACTURA","CAPITADO","CIRUGIA","IDCIRUGIA","CONSECUTIVOCX","KCNTRID","TIPOSISTEMA","IDSEDE");

$x=1;
$y=1;
for($i=0;$i<count($columnas);$i++){
    $activeSheet->setCellValueByColumnAndRow($x,$y,$columnas[$i]);
    $x++;
}

$consulta="SELECT   
	'CIT' AS TABLA,
	CIT.CITID PRESTACIONID,
	CIT.CONSECUTIVO,
	1 AS ITEM,
	SER.CODIGORIPS,
	RENCP.ARCHIVO AS ARCHIVORIPS,
	CASE CIT.CLASEORDEN WHEN 'PyP' THEN 1 ELSE 0 END AS PYP,
	FTR.IDTERCERO AS IDTERCEROFACT,	
	KCNTR.CODSGSSS,
	AFI.IDADMINISTRADORA,
	TER.RAZONSOCIAL,
	CIT.IDCONTRATANTE,
	CIT.IDPLAN,
	CIT.N_FACTURA,
	AFI.IDAFILIADO,
	AFI.PNOMBRE,
	COALESCE(AFI.SNOMBRE, '') AS SNOMBRE,
	AFI.PAPELLIDO,
	COALESCE(AFI.SAPELLIDO, '') AS SAPELLIDO,
	AFI.SEXO,
	AFI.FNACIMIENTO,
	AFI.TIPO_DOC,
	AFI.DOCIDAFILIADO,
	dbo.FNK_EDAD_ARS(AFI.FNACIMIENTO,GETDATE(),'A') AS EDADAFI,
	'1' AS UNIDEDEDAD,
	CIT.FECHA,
	NULL AS FINGRESO,
	CIT.NOAUTORIZACION,
	NULL AS FEGRESO,
	CIT.IDSERVICIO,
	SER.IDALTERNA,
	SER.TIPOMED,  
	TIPOSERVICIO  = CASE RENCP.IDCONCEPTORIPS    
						WHEN '06' THEN '3'      
	                    WHEN '07' THEN '4'      
						WHEN '08' THEN '3'      
	                    WHEN '09' THEN '1'      
	                    WHEN '10' THEN '1'      
	                    WHEN '11' THEN '1'      
						WHEN '14' THEN '2'     
						END,
	LEFT (SER.DESCSERVICIO,60) AS DESCSERVICIO,
	'' AS NOMGENERICO,
	'' AS FORMA,
	'' AS CONCENTRACION,
	'' AS UMEDIDA,
	CAST(1 AS INT) AS  CANTIDAD, 
	'1' AS AMBITO, 
	COALESCE((SELECT TOP 1 oxTrat.TratFinalidad FROM oxTrat WHERE oxTrat.IDAFILIADO = CIT.IDAFILIADO AND oxTrat.TratFinalidad<>'' order by oxTrat.TratFch desc),'10') AS FINALIDAD,
	'2' AS VIAINGRESO, 
	'5' AS PERSONALAT,
	COALESCE((SELECT TOP 1 oxTrat.TratDXPpal FROM oxTrat WHERE oxTrat.IDAFILIADO = CIT.IDAFILIADO AND oxTrat.TratDXPpal<>'' order by oxTrat.TratFch desc),'K029') AS IDDX,
	COALESCE((SELECT TOP 1 oxTrat.TIPODX FROM oxTrat WHERE oxTrat.IDAFILIADO = CIT.IDAFILIADO AND oxTrat.TIPODX<>'' order by oxTrat.TratFch desc),'2') AS TIPODX,
	'' AS DXSALIDA,
	(SELECT TOP 1 oxTrat.TratDXRel1 FROM oxTrat WHERE oxTrat.IDAFILIADO = CIT.IDAFILIADO order by oxTrat.TratFch desc) DX1,
	(SELECT TOP 1 oxTrat.TratDXRel2 FROM oxTrat WHERE oxTrat.IDAFILIADO = CIT.IDAFILIADO order by oxTrat.TratFch desc) DX2,
	(SELECT TOP 1 oxTrat.TratDXRel3 FROM oxTrat WHERE oxTrat.IDAFILIADO = CIT.IDAFILIADO order by oxTrat.TratFch desc) DX3,
	'' AS COMPLICACION, 
	'' AS FORMAREALIZACION, 
	COALESCE((SELECT TOP 1 oxTrat.CAUSAEXTERNA FROM oxTrat WHERE oxTrat.IDAFILIADO = CIT.IDAFILIADO AND oxTrat.CAUSAEXTERNA<>'' order by oxTrat.TratFch desc),'13') AS CAUSAEXT,
	'' AS CAUSAMUERTE, 
	'' AS DESTINO, 
	'1' AS ESTADOSALIDA, 
	COALESCE(CIT.VALORTOTAL,0) AS VALOR, 
	COALESCE(CIT.VALORCOPAGO,0) AS VALORCOPAGO, 
	COALESCE(CIT.VALORTOTAL,0) AS VALORITEM, 
	round(COALESCE(CIT.VALORTOTAL,0)-COALESCE(CIT.VALORCOPAGO,0) ,0) AS VRNETO,
	FTR.ESTADO AS ESTADOFACTURA,
	CAPITADO=case when cit.TIPOCONTRATO='C' then 1 else 0 end,
	0 CIRUGIA,
	IDCIRUGIA=cast('' as varchar(20)),
	CONSECUTIVOCX=cast('' as varchar(20)),
	cit.KCNTRID,
	ttec.TIPOSISTEMA,
	CIT.IDSEDE
FROM CIT
	INNER JOIN AFI  ON AFI.IDAFILIADO=CIT.IDAFILIADO
	INNER JOIN SER  ON SER.IDSERVICIO=CIT.IDSERVICIO
	LEFT JOIN PRE   ON PRE.PREFIJO=SER.PREFIJO
	LEFT JOIN RENCP ON RENCP.IDCONCEPTORIPS=SER.CODIGORIPS
	LEFT JOIN FTR   ON FTR.N_FACTURA=CIT.N_FACTURA
	LEFT JOIN KCNTR ON KCNTR.KCNTRID=CIT.KCNTRID
	LEFT JOIN TER   ON TER.IDTERCERO=AFI.IDADMINISTRADORA
	LEFT JOIN ttec  ON cit.tipottec=ttec.tipo
WHERE 
	CIT.CITASIMULTANEA = 0 
	AND CIT.IDAFILIADO IS NOT NULL 
	and TIPOCITA='Cita' 
	and (coalesce(CUMPLIDA,0)=1 or (coalesce(CUMPLIDA,0)=0 
	and coalesce(FACTURADA,0)=1)) 
	and ser.tiposervicio='04'
	AND CIT.FECHA BETWEEN '{$fechaini}' and '{$fechafin}'";

$sth = $conn->prepare($consulta);
$sth->execute();
$x=1;
$y=2;

while ($row = $sth->fetch())
{
    for($i=0;$i<count($columnas);$i++){
        $activeSheet->setCellValueByColumnAndRow($x,$y,preg_replace($caracteres,' ',$row[$columnas[$i]]));
        $x++;
    }
    $x=1;
    $y++;
}

$filename='ox_infoac';
header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment;filename="'. $filename .'.xlsx"'); /*-- $filename is  xsl filename ---*/
header('Cache-Control: max-age=0');
$Excel_writer->save('php://output');
exit;
