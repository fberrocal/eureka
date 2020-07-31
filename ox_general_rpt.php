<?php
// Require composer autoload
require_once __DIR__ . '/vendor/autoload.php';
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

$spreadsheet  = new Spreadsheet();  				/*----Spreadsheet object-----*/
$Excel_writer = new Xlsx($spreadsheet);  			/*----- Excel (Xls) Object*/
$spreadsheet->setActiveSheetIndex(0);
$activeSheet  = $spreadsheet->getActiveSheet();

require_once('database.class.php');
$conn = new Database();

if(!isset($_POST['idsede'])){
    $idsede='';
}else{
    $idsede=$_POST['idsede'];
}

$parts      = explode('-', $_POST['fechaini']);
$fechaini   = $parts[2].'/'.$parts[1].'/'.$parts[0];

$idmedico   = $_POST['idmedico'];
$idservicio = $_POST['idservicio'];

$parts      = explode('-', $_POST['fechafin']);
$fechafin   = $parts[2].'/'.$parts[1].'/'.$parts[0];

//$columnas=array("FechaAtencion","NroPrestacion","Facturada","IdUnidFuncional","UniFuncional","IdMedico","NombreProf","tipoDocumento","DescTipoDoc","numDocumento","PrimerApellido","SegundoApellido","PrimerNombre","SegundoNombre","FechaNac","Sexo","IdServicio","Servicio","FechaAplicacion");

$columnas=array("Id_Administradora","Razon_Social","Id_Practica","Id_Medico","Nombre_prof","Cantidad","Id_Servicio","Desc_Servicio","Prac_Descripcion","Id_Odontograma","Id_Sede","Id_Afiliado","Primer_Apellido","Segundo_Apellido","Primer_Nombre","Fecha_Atencion","Fecha_practica","Id_Prestacion","No_Prestacion","Fecha_procedimiento");

$x=1;
$y=1;

for($i=0;$i<count($columnas);$i++){
    $activeSheet->setCellValueByColumnAndRow($x,$y,$columnas[$i]);
    $x++;
}

// $consulta="select
// 	CONVERT(VARCHAR(10),x.PraFchRealiz,103) as FechaAtencion,
// 	p.NOPRESTACION      as NroPrestacion,
//     p.FACTURADA         as Facturada,
// 	y.IDSEDE			as IdUnidFuncional,
// 	k1.descripcion		as UniFuncional,
// 	x.idmedico			as IdMedico,
// 	m.nombre			as NombreProf,
// 	z.TIPO_DOC			as tipoDocumento,
// 	k3.descripcion      as DescTipoDoc,
// 	z.DOCIDAFILIADO		as numDocumento,
// 	z.papellido			as PrimerApellido,	
// 	z.sapellido			as SegundoApellido,
// 	z.pnombre			as PrimerNombre,
// 	z.snombre			as SegundoNombre,
// 	CONVERT(VARCHAR(10),z.fnacimiento,103) AS FechaNac,
// 	z.sexo,
// 	x.idservicio		as IdServicio,
// 	x.PracDescripcion	as Servicio,
// 	CONVERT(VARCHAR(10),x.PraFchRealiz,103) as FechaAplicacion
// from
// 	oxPra x 
// 		inner join hpred p on x.HPREDID=p.HPREDID 
// 		left join oxTrat y on x.Tratid=y.Tratid
// 		left join afi z on z.IDAFILIADO=y.idafiliado
// 		left join med m on x.idmedico=m.idmedico
// 		left join sed k1 on k1.idsede=y.idsede
// 		left join tgen k3 on k3.codigo=z.TIPO_DOC and k3.tabla='AFI' and k3.campo='TIPO_DOC'
// where
// 	x.PraFchRealiz between '$fechaini' and '$fechafin' and p.FACTURABLE=1 
// 	and x.idservicio=CASE WHEN COALESCE('$idservicio','')='' THEN x.idservicio ELSE '$idservicio' END
// 	and m.idmedico=CASE WHEN COALESCE('$idmedico','')='' THEN m.idmedico ELSE '$idmedico' END
// 	and y.idsede=CASE WHEN COALESCE('$idsede','')='' THEN y.idsede ELSE '$idsede' END";


$consulta="select
	VWA_RIPS.IDCONTRATANTE as Id_Administradora,
	TER.RAZONSOCIAL        as Razon_Social,
	OXPRA.PRAID            as Id_Practica,
	OXPRA.IDMEDICO         as Id_Medico,
	MED.NOMBRE             as Nombre_prof,
	VWA_RIPS.CANTIDAD      as Cantidad,
	VWA_RIPS.IDSERVICIO    as Id_Servicio,
	SER.DESCSERVICIO       as Desc_Servicio,
	OXPRA.PracDescripcion  as Prac_Descripcion,
	OXPRA.TRATID           as Id_Odontograma,
	VWA_RIPS.IDSEDE        as Id_Sede,
	VWA_RIPS.IDAFILIADO    as Id_Afiliado,
	AFI.PAPELLIDO          as Primer_Apellido,
	AFI.SAPELLIDO          as Segundo_Apellido,
	AFI.PNOMBRE            as Primer_Nombre,
	CONVERT(VARCHAR(10),OXPRA.PraFchRealiz,103) as Fecha_Atencion,
	CONVERT(VARCHAR(10),OXPRA.PraFch,103)       as Fecha_practica,
	HPRED.HPREDID          						as Id_Prestacion,
	HPRED.NOPRESTACION     						as No_Prestacion,
	CONVERT(VARCHAR(10),VWA_RIPS.FECHA,103)     as Fecha_procedimiento
from VWA_RIPS
		LEFT JOIN TER   ON TER.IDTERCERO  = VWA_RIPS.IDCONTRATANTE
		LEFT JOIN HPRED ON HPRED.HPREDID  = VWA_RIPS.PRESTACIONID
		LEFT JOIN OXPRA ON OXPRA.HPREDID  = HPRED.HPREDID
		LEFT JOIN MED   ON OXPRA.IDMEDICO = MED.IDMEDICO
		LEFT JOIN SER   ON SER.IDSERVICIO = VWA_RIPS.IDSERVICIO
		LEFT JOIN AFI   ON AFI.IDAFILIADO = VWA_RIPS.IDAFILIADO
where		
	VWA_RIPS.FECHA BETWEEN '$fechaini' AND '$fechafin'
	AND OXPRA.IDSERVICIO=CASE WHEN COALESCE('$idservicio','')='' THEN OXPRA.IDSERVICIO ELSE '$idservicio' END
	AND MED.IDMEDICO=CASE WHEN COALESCE('$idmedico','')='' THEN MED.IDMEDICO ELSE '$idmedico' END
	AND VWA_RIPS.IDSEDE=CASE WHEN COALESCE('$idsede','')='' THEN VWA_RIPS.IDSEDE ELSE '$idsede' END
	AND SER.PREFIJO='550'
	AND VWA_RIPS.ARCHIVORIPS='AP'";

$sth = $conn->prepare($consulta);
$sth->execute();
$result = $sth->fetchall(PDO::FETCH_ASSOC);

$x=1;
$y=2;

foreach($result as $key=>$row) {
    foreach($row as $key2=>$row2){
        $activeSheet->setCellValueByColumnAndRow($x,$y,$row2);
        $x++;
    }
    $y++;
    $x=1;
}

$filename='ox_general';

header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment;filename="'. $filename .'.xlsx"'); 					/*-- $filename is  xsl filename ---*/
header('Cache-Control: max-age=0');
$Excel_writer->save('php://output');
exit;