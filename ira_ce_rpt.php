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

$columnas=array("n","DXINGRESO","DESCRIPCION","<1","1","2-4","5-19","20-39","40-59",">=60","Total");

$cabecera=array("n","DXINGRESO","DESCRIPCION","<1","1","2-4","5-19","20-39","40-59",">=60","Total");
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
mem1 as (
	select idafiliado, IDDX, CLASEPLANTILLA,  n=row_number() over (partition by CONSECUTIVO order by fecha) 
	from hca where  PROCEDENCIA='CE' AND 
	IDDX IN ( 'J00','J010','J011','J012','J013','J014','J018' ,'J020','J028','J029','J030','J038','J039',
	'J040','J041','J042','J050','J051' ,'J060' ,'J068' ,'J069' ,'J09','J18' ,'J100' ,'J101','J108' ,'J110',
	 'J111','J118' ,'J120','J121','J122','J128','J13','J14','J150','J151','J152','J153', 'J154','J155','J156','J157','J158','J159','J160'
	 ,'J168','J170','J171','J172','J173','J178','J180','J181','J182','J188','J189','J20','J22','J200','J201','J202','J203'
	 ,'J204','J205','J206','J207','J208','J209','J210','J218','J219','J22', 'U071','U072'
)
),
mem2 as (
	select  c.SEXO, 
	geta = 
		    case when dbo.fna_EdadenMeses(c.FNACIMIENTO,a.FECHA) < 12 then '<1' else
			case when dbo.fna_EdadenMeses(c.FNACIMIENTO,a.FECHA) < 24 then '1' else
			case when dbo.fna_EdadenMeses(c.FNACIMIENTO,a.FECHA) <= 48 then '2-4' else
			case when dbo.fna_EdadenMeses(c.FNACIMIENTO,a.FECHA) <= 228 then '5-19' else
				case when dbo.fna_EdadenMeses(c.FNACIMIENTO,a.FECHA) <= 468 then '20-39' else
					case when dbo.fna_EdadenMeses(c.FNACIMIENTO,a.FECHA) <= 708 then '40-59' else '>=60' end
			             end
			          end
				end	
		end
    end,
	DXINGRESO=b.IDDX, b.CLASEPLANTILLA
	from cit a 
		join mem1 b on a.idafiliado=b.idafiliado and b.n=1
		left join afi c on a.IDAFILIADO=c.IDAFILIADO
	where  c.SEXO = '$sexo' and
		dbo.FNK_ANTIGUEDAD(c.FNACIMIENTO,a.FECHA,'A') between @EdadIni and @EdadFin and a.FECHA between @FechaIni and @FechaFin 
		and a.IDSEDE=CASE WHEN COALESCE(@IDSEDE,'')='' THEN a.IDSEDE ELSE @IDSEDE END
),
mem3 as (
	select n=ROW_NUMBER() over (order by [<1]+[1]+[2-4]+[5-19]+[20-39]+[40-59]+[>=60] desc), *, Total= [<1]+[1]+[2-4]+[5-19]+[20-39]+[40-59]+[>=60] 
	from (
		select a.DXINGRESO, b.DESCRIPCION,  a.geta
		from mem2 a 
			left join mdx b on a.DXINGRESO=b.IDDX
		where a.DXINGRESO=case when coalesce(@IDDX,'')='' then a.DXINGRESO else @IDDX end 
			and  not a.DXINGRESO is null) x pivot (count(geta) for geta in ([<1],[1],[2-4],[5-19],[20-39],[40-59],[>=60])
	) b
)
select n,DXINGRESO,DESCRIPCION,[<1],[1],[2-4],[5-19],[20-39],[40-59],[>=60],Total from mem3 ";

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

$filename='ira_ce';
//$activeSheet->setCellValue('A1' , 'New file content')->getStyle('A1')->getFont()->setBold(true);
header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment;filename="'. $filename .'.xlsx"'); /*-- $filename is  xsl filename ---*/
header('Cache-Control: max-age=0');
$Excel_writer->save('php://output');
exit;