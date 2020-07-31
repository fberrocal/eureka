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

$edadini=$_POST['edadini'];
$edadfin=$_POST['edadfin'];
$sexo=$_POST['sexo'];

$columnas=array("n","DXINGRESO","DESCRIPCION","<1","1-4","5-9","10-14","15-19","20-24","25-29","30-34","35-39",
    "40-44","45-49","50-54","55-59","60-64","65-69","70-74","75-79",">=80","Total");

$cabecera=array("n","DXINGRESO","DESCRIPCION","<1","1-4","5-9","10-14","15-19","20-24","25-29","30-34","35-39",
    "40-44","45-49","50-54","55-59","60-64","65-69","70-74","75-79",">=80","Total");
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
 declare 
	@EdadIni int='$edadini',
	@EdadFin int='$edadfin',
	@FechaIni datetime='$fechaini',
	@FechaFin datetime='$fechafin',
	@IDDX varchar(10)='',
	@IDSEDE varchar(10)='$idsede';
with 
		mem0 as (
		select mi=0,mf=11.99,l='<1' union all
		select 012.00,048,'1-4'   union all
		select 048.01,108,'5-9'   union all
		select 108.01,168,'10-14' union all
		select 168.01,228,'15-19' union all
		select 228.01,288,'20-24' union all
		select 288.01,348,'25-29' union all
		select 348.01,408,'30-34' union all
		select 408.01,468,'35-39' union all
		select 468.01,528,'40-44' union all
		select 528.01,588,'45-49' union all
		select 588.01,648,'50-54' union all
		select 648.01,708,'55-59' union all
		select 708.01,768,'60-64' union all
		select 768.01,828,'65-69' union all
		select 828.01,888,'70-74' union all
		select 888.01,948,'75-79' union all 
		select 948.01,1500,'>=80'
		),
		mem1 as (
		select idafiliado, IDDX, CLASEPLANTILLA,  n=row_number() over (partition by CONSECUTIVO order by fecha) 
		from hca 
		where  PROCEDENCIA='CE' AND 
		IDDX IN ( 'A048','A049','A053','A058','A059','A084','A085','A09X','A000','A001','A009',
		'K580','K591')
		),
		mem2 as (
		select  c.SEXO, 
		geta = (select l from mem0 where dbo.fna_EdadenMeses(c.FNACIMIENTO,a.FECHA) between mi and mf),
		DXINGRESO=b.iddx, b.CLASEPLANTILLA
		from cit a 
		join mem1 b on a.idafiliado=b.idafiliado and b.n=1
		left join afi c on a.IDAFILIADO=c.IDAFILIADO
		where  c.SEXO='$sexo' and
		dbo.FNK_ANTIGUEDAD(c.FNACIMIENTO,a.FECHA,'A') between @EdadIni and @EdadFin and a.FECHA between @FechaIni and @FechaFin 
		and a.IDSEDE=CASE WHEN COALESCE(@IDSEDE,'')='' THEN a.IDSEDE ELSE @IDSEDE END 
		),
		mem3 as (
		select n=ROW_NUMBER() over (order by [<1]+[1-4]+[5-9]+[10-14]+[15-19]+[20-24]+[25-29]+[30-34]+[35-39]+[40-44]+[45-49]+[50-54]+[55-59]+[60-64]+[65-69]+[70-74]+[75-79]+[>=80]
		desc), *, Total= [<1]+[1-4]+[5-9]+[10-14]+[15-19]+[20-24]+[25-29]+[30-34]+[35-39]+[40-44]+[45-49]+[50-54]+[55-59]+[60-64]+[65-69]+[70-74]+[75-79]+[>=80]
		from (
		select a.DXINGRESO, b.DESCRIPCION,  a.geta
		from mem2 a 
		left join mdx b on a.DXINGRESO=b.IDDX
		where a.DXINGRESO=case when coalesce(@IDDX,'')='' then a.DXINGRESO else @IDDX end 
		and  not a.DXINGRESO is null and a.DXINGRESO<>'') x pivot (count(geta) for geta in ([<1],[1-4],[5-9],[10-14],[15-19],[20-24],[25-29],
		[30-34],[35-39],[40-44],[45-49],[50-54],[55-59],[60-64],[65-69],[70-74],[75-79],[>=80])
		) b
		)
		select n,DXINGRESO,DESCRIPCION,[<1],[1-4],[5-9],[10-14],[15-19],[20-24],[25-29],[30-34],[35-39],[40-44],[45-49],[50-54],[55-59],[60-64],[65-69],[70-74],[75-79],[>=80],Total from mem3";

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

$filename='eda_ce';
//$activeSheet->setCellValue('A1' , 'New file content')->getStyle('A1')->getFont()->setBold(true);
header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment;filename="'. $filename .'.xlsx"'); /*-- $filename is  xsl filename ---*/
header('Cache-Control: max-age=0');
$Excel_writer->save('php://output');
exit;