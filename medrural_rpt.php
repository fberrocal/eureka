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

$caracteres = array('/\s\s+/','/-/','/\+/','/=/');

// Captura de la SEDE
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

// $columnas=array("idreporte","fecha_reporte","hora_reporte","idpaciente","nombre_paciente","edad","periodo_edad","idgenero","genero","estado","desc_estado","descripcionevent","lugar","nom_sede_reporta","porque","barreras","persona_reporta","cargo","idservicio","servicio","idsede","sede","ideps","eps","analizado","idproceso","proceso","mejorado","asociado","fecha_evento","hora_evento","oportunidad","req_analisis","evento","tipoevento","causa_internacion","observacion","idclasificacion_ini","descripcion","taxonomia","desc_taxonomia","idtipobusqueda","tipobusqueda","id_tipo_evento_intl","tipo_evento_intl","dias_prolonga","enfermedad_base","descripcion_enfb");
$columnas=array("SEDE","DOCUEMNTO_AFILIADO","NOMBRE_AFILIADO","NOMBRE_MEDICAMENTO","CANTIDAD","RAZONSOCIAL","FECHA_ORDEN","TIPOCONTRATO","CLASEORDEN");
// $cabecera=array("Id Reporte","Fecha Reporte","Hora Reporte","Id Paciente","Nombre Paciente","Edad","Periodo Edad","Id. Sexo","Sexo","Estado","Desc. Estado","Descripción del Evento","Sede que Reporta","Nombre Sede Reporta","Por que sucede el evento","Barreras Para Prevenir Evento","Persona Reporta","Cargo","Servicio de Ocurrencia","Nombre Servicio Oc","Sede Ocurrencia","Nombre Sede Oc","Id. EPS","EPS del Usuario","Analizado","Servicio Reporta","Nombre Servicio Rep.","Mejorado","Asociado A","Fecha Evento","Hora Eevento","Oportunidad","Req. Analisis","Falla en la Atencion","Descripcion Falla","Diagnostico Actual","Observacion","Clasificacion Inicial","Desc Clasificacion Ini","Gravedad","Desc Gravedad","ID Tipo Busqueda","Tipo Busqueda","ID Tipo de Evento","Tipo de Evento","Atencion a Primera Victima","Enfermedad Base","Desc Enfermedad Base");
$cabecera=array("Sede","Doc_Afiliado","Afiliado","Medicamento","Cantidad","Tercero","Fecha_Orden","Tipo_Contrato","Clase_Orden");
$x=1;
$y=1;

for($i=0;$i<count($cabecera);$i++){     //echo "<br>".$columnas[$i];  //echo "<br>".$row[$columnas[$i]];
    $activeSheet->setCellValueByColumnAndRow($x,$y,$cabecera[$i]);
    $x++;
}

/*
$consulta="SELECT r.idreporte, CONVERT(VARCHAR(10), r.fecha_reporte,103) as fecha_reporte, 
CONVERT(varchar(10),r.hora_reporte,108) AS hora_reporte, r.idpaciente, r.nombre_paciente, r.edad, CASE WHEN r.periodo_edad='A' then 'AÑOS' WHEN r.periodo_edad='M' then 'MESES' WHEN r.periodo_edad='D' then 'DIAS' ELSE r.periodo_edad END AS periodo_edad, r.idgenero, g.genero, r.estado, x.desc_estado,
r.descripcionevent, r.lugar, sr.sede as nom_sede_reporta, r.porque, r.barreras, r.persona_reporta, c.cargo, r.idservicio, s.servicio,
r.idsede,sed.sede, r.ideps, ep.razonsocial as eps, r.analizado, r.idproceso, p.proceso, r.mejorado, 
CASE WHEN r.asociacion_ref <> '' THEN r.asociacion_ref ELSE r.asociado END as asociado, 
CONVERT(VARCHAR(10),r.fecha_evento,103) AS fecha_evento, CONVERT(VARCHAR(10), r.hora_evento,108) AS hora_evento, 
r.oportunidad, r.req_analisis, r.evento, e.evento as tipoevento, r.causa_internacion, r.observacion, r.idclasificacion_ini, ci.descripcion,
r.taxonomia, t.taxonomia AS desc_taxonomia, r.idtipobusqueda, tb.tipobusqueda, r.tipo_evento_intl as id_tipo_evento_intl, ei.tipo_evento_intl,
r.dias_prolonga, r.enfermedad_base, eb.descripcion as descripcion_enfb
FROM reporte_evento r
LEFT JOIN proceso p on p.idproceso=r.idproceso 
LEFT JOIN cargo c   on c.idcargo=r.idcargo
LEFT JOIN evento e  on e.idevento=r.evento
LEFT JOIN servicio_evento s on s.idservicio=r.idservicio
LEFT JOIN sede sed  on sed.idsede=r.idsede
LEFT JOIN eps ep    on ep.ideps=r.ideps
LEFT JOIN estado x  on r.estado=x.idestado
LEFT JOIN genero g  on r.idgenero=g.idgenero
LEFT JOIN clasificacion_evento ci on r.idclasificacion_ini=ci.idclasificacion_ini
LEFT JOIN taxonomia t on r.taxonomia=t.idtaxonomia
LEFT JOIN tipobusqueda tb on r.idtipobusqueda=tb.idtipobusqueda
LEFT JOIN tipo_evento_intl ei on r.tipo_evento_intl=ei.idtipo_evento_intl
LEFT JOIN cie10 eb on r.enfermedad_base=eb.id
LEFT JOIN sede sr on r.lugar=sr.idsede
ORDER BY r.idreporte desc ";
*/

$consulta = "SELECT s.DESCRIPCION AS SEDE, x.DOCIDAFILIADO AS DOCUEMNTO_AFILIADO, dbo.fnNombreAfiliado(x.IDAFILIADO,'N') AS NOMBRE_AFILIADO,";
$consulta.= "y.DESCSERVICIO AS NOMBRE_MEDICAMENTO, c.CANTIDAD, t.RAZONSOCIAL, b.FECHA AS FECHA_ORDEN, c.TIPOCONTRATO, b.CLASEORDEN";
//$consulta.= "c.CANTIDAD, t.RAZONSOCIAL, b.FECHA AS FECHA_ORDEN, c.TIPOCONTRATO, b.CLASEORDEN";
$consulta.= " FROM AUTD c with (nolock) INNER JOIN AUT b with (nolock) ON c.IDAUT = b.IDAUT";
$consulta.= " INNER JOIN AFI x with (nolock) ON x.IDAFILIADO = b.IDAFILIADO";
$consulta.= " INNER JOIN SED s with (nolock) ON s.IDSEDE = b.IDSEDE";
$consulta.= " INNER JOIN SER y with (nolock) ON c.IDSERVICIO = y.IDSERVICIO";
$consulta.= " INNER JOIN TER t with (nolock) ON c.IDTERCEROCA = t.IDTERCERO";
$consulta.= " WHERE b.PREFIJO='500' AND b.FECHA between '".$fechaini."' AND '".$fechafin."' AND b.ESTADO='Pendiente'";

if( $idsede != '' ) {
	$consulta.= " AND b.IDSEDE='".$idsede."'";
}

$sth = $conn->prepare($consulta);
$sth->execute();
//$result = $sth->fetchall(PDO::FETCH_ASSOC);
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

$filename='mrural';
//$activeSheet->setCellValue('A1' , 'New file content')->getStyle('A1')->getFont()->setBold(true);
header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment;filename="'. $filename .'.xlsx"'); /*-- $filename is  xsl filename ---*/
header('Cache-Control: max-age=0');
$Excel_writer->save('php://output');
exit;