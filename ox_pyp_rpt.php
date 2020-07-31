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

$idmedico=$_POST['idmedico'];
$idservicio=$_POST['idservicio'];


$columnas=array("IdUnidFuncional",
    "FechaAtencion",
    "tipoDocumento",
    "numDocumento",
    "PrimerApellido",
    "SegundoApellido",
    "PrimerNombre",
    "SegundoNombre",
    "FechaNac",
    "Edad",
    "Sexo",
    "PertEtnica",
    "IdOcupacion",
    "IdNivelEducativo",
    "Direccion",
    "TelCelular",
    "TelFijo",
    "IdServicio",
    "Servicio",
    "FechaAplicacion",
    "NroPrestacion",
    "Facturada",
    "FechaProxControl",
    "IdMedico",
    "NombreProf",
    "Observaciones");
$x=1;
$y=1;
for($i=0;$i<count($columnas);$i++){
    $activeSheet->setCellValueByColumnAndRow($x,$y,$columnas[$i]);
    $x++;
}
$consulta="select
	y.IDSEDE			as IdUnidFuncional,
	x.PraFchRealiz		as FechaAtencion,
	z.TIPO_DOC			as tipoDocumento,
	z.DOCIDAFILIADO		as numDocumento,
	z.papellido			as PrimerApellido,	
	z.sapellido			as SegundoApellido,
	z.pnombre			as PrimerNombre,
	z.snombre			as SegundoNombre,
	CONVERT(VARCHAR(10),z.fnacimiento,103) AS FechaNac,
	dbo.F_EDAD_TEXTO(z.FNACIMIENTO,getdate()) as Edad,
	z.sexo as Sexo,
	z.grupoetnico		as PertEtnica,
	z.idocupacion		as IdOcupacion,
	z.idescolaridad		as IdNivelEducativo,
	z.direccion			as Direccion,
	z.CELULAR			as TelCelular,
	z.telefonores		as TelFijo,
	x.idservicio		as IdServicio,
	x.PracDescripcion	as Servicio,
	x.PraFchRealiz		as FechaAplicacion,
    p.NOPRESTACION      as NroPrestacion,
    p.FACTURADA         as Facturada,
	''					as FechaProxControl,
	x.idmedico			as IdMedico,
	m.nombre			as NombreProf,
	x.PraObservaciones	as Observaciones
from
	oxPra x WITH(INDEX(IDX_OXPRA_TRATIDPRAID)) 
        inner join hpred p with(nolock) on x.HPREDID=p.HPREDID 
		left join oxTrat y with(nolock) on x.Tratid=y.Tratid
		left join afi z with(nolock) on z.IDAFILIADO=y.idafiliado
		left join med m with(nolock) on x.idmedico=m.idmedico
where
	x.PraFchRealiz between '$fechaini' and '$fechafin' and p.FACTURABLE=1 
	and y.IDSEDE=CASE WHEN COALESCE('$idsede','')='' THEN y.idsede ELSE '$idsede' END 
	and x.idservicio=CASE WHEN COALESCE('$idservicio','')='' THEN x.idservicio ELSE '$idservicio' END 
	and X.idmedico=CASE WHEN COALESCE('$idmedico','')='' THEN x.idmedico ELSE '$idmedico' END";

$sth = $conn->prepare($consulta);
$sth->execute();
$x=1;
$y=2;
while ($row = $sth->fetch())
{
    for($i=0;$i<count($columnas);$i++){
        $activeSheet->setCellValueByColumnAndRow($x,$y,$row[$columnas[$i]]);
        $x++;
    }
    $x=1;
    $y++;
}

$filename='ox_pyp';
header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment;filename="'. $filename .'.xlsx"'); /*-- $filename is  xsl filename ---*/
header('Cache-Control: max-age=0');
$Excel_writer->save('php://output');
exit;