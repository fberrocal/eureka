<?php
// Require composer autoload
require_once '../vendor/autoload.php';
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

$spreadsheet = new Spreadsheet();           /*----Spreadsheet object-----*/
$Excel_writer = new Xlsx($spreadsheet);     /*----- Excel (Xls) Object*/
$spreadsheet->setActiveSheetIndex(0);
$activeSheet = $spreadsheet->getActiveSheet();

$caracteres = array('/\s\s+/','/-/','/\+/','/=/');

require_once('../database.class.php');
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

$parts = explode('-', $_POST['fechafin']);
$fechafin  = $parts[2].'/'.$parts[1].'/'.$parts[0];

$fechaini.=' 00:00:00.000';
$fechafin.=' 23:59:59.997';

$columnas=array("IdUnidFuncional","id_paciente","nombre_pac","id_practica","id_tratamiento","no_historia_odx","id_servicio","servicio","estado","fecha","fecha_realizada","cantidad","id_medico",
                "nombre_medico","id_prestacion","finalidad","ambito");
$x=1;
$y=1;
for($i=0;$i<count($columnas);$i++){
    $activeSheet->setCellValueByColumnAndRow($x,$y,$columnas[$i]);
    $x++;
}
$consulta="select
    t.IDSEDE            as IdUnidFuncional,
	t.idafiliado        as id_paciente,
    concat(b.pnombre,' ',b.snombre,' ',b.papellido,' ',b.sapellido) as nombre_pac, 
    x.PraID             as id_practica, 
    x.TratID            as id_tratamiento,
    t.HCNum             as no_historia_odx,
    x.IDSERVICIO        as id_servicio,
    x.PracDescripcion   as servicio,
    x.PraEstado         as estado,
    x.PraFch            as fecha,
    x.PraFchRealiz      as fecha_realizada,
    x.PraCantidad       as cantidad,
    x.IDMEDICO          as id_medico,
    m.nombre            as nombre_medico,
    x.HPREDID           as id_prestacion,
    x.FINALIDAD         as finalidad,
    x.AMBITO            as ambito
from
	oxpra x left join oxtrat t on t.tratid=x.tratid left join afi b on b.idafiliado=t.idafiliado left join med m on x.idmedico=m.idmedico
where
	x.PraFchRealiz between '$fechaini' and '$fechafin' 
    and x.PraEstado='F' 
    and X.idmedico=CASE WHEN COALESCE('$idmedico','')='' THEN x.idmedico ELSE '$idmedico' END
    and x.idservicio=CASE WHEN COALESCE('$idservicio','')='' THEN x.idservicio ELSE '$idservicio' END 
	and t.IDSEDE=CASE WHEN COALESCE('$idsede','')='' THEN t.idsede ELSE '$idsede' END";

// echo 'Consulta: ' .   $consulta;  

$sth = $conn->prepare($consulta);
$sth->execute();
$x=1;
$y=2;
while ($row = $sth->fetch())
{
    for($i=0;$i<count($columnas);$i++){
        // $activeSheet->setCellValueByColumnAndRow($x,$y,$row[$columnas[$i]]);   // preg_replace($caracteres, ' ', $row[$columnas[$i]])
        $activeSheet->setCellValueByColumnAndRow($x,$y,preg_replace($caracteres,' ',$row[$columnas[$i]]));
        $x++;
    }
    $x=1;
    $y++;
}

$filename='ox_praxmed';
//$activeSheet->setCellValue('A1' , 'New file content')->getStyle('A1')->getFont()->setBold(true);
header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment;filename="'. $filename .'.xlsx"'); /*-- $filename is  xsl filename ---*/
header('Cache-Control: max-age=0');
$Excel_writer->save('php://output');
exit;