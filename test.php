<?php
// Require composer autoload
require_once __DIR__ . '/vendor/autoload.php';
require_once('database.class.php');
$conn = new Database();
// Create an instance of the class:
$mpdf = new \Mpdf\Mpdf();
$mpdf->setFooter('{PAGENO}');

$parts = explode('-', $_POST['fechaini']);
$fechaini  = $parts[2].'/'.$parts[1].'/'.$parts[0];

$parts = explode('-', $_POST['fechafin']);
$fechafin  = $parts[2].'/'.$parts[1].'/'.$parts[0];


//$fechaini=$_REQUEST['fechaini'];
//$fechafin=$_REQUEST['fechafin'];
$edadini=$_REQUEST['edadini'];
$edadfin=$_REQUEST['edadfin'];
$sexo=$_REQUEST['sexo'];

$consulta="with 
				mem1 as (
				select noadmision, IDDX, CLASEPLANTILLA,  n=row_number() over (partition by noadmision order by fecha) 
				from hca where coalesce(IDDX,'')<>''
				),
				mem2 as (
				select a.NOADMISION, c.SEXO, 
				geta = 
				case when dbo.fna_EdadenMeses(c.FNACIMIENTO,a.FECHA) < 12 then '<1' else
				case when dbo.fna_EdadenMeses(c.FNACIMIENTO,a.FECHA) <= 48 then '1-4' else
				case when dbo.fna_EdadenMeses(c.FNACIMIENTO,a.FECHA) <= 108 then '5-9' else
				case when dbo.fna_EdadenMeses(c.FNACIMIENTO,a.FECHA) <= 168 then '10-14' else
				case when dbo.fna_EdadenMeses(c.FNACIMIENTO,a.FECHA) <= 228 then '15-19' else
				case when dbo.fna_EdadenMeses(c.FNACIMIENTO,a.FECHA) <= 528 then '20-44' else
				case when dbo.fna_EdadenMeses(c.FNACIMIENTO,a.FECHA) <= 708 then '45-59' else '>=60' end
				end
				end
				end
				end
				end
				end,
				DXINGRESO=coalesce(b.IDDX,a.DXINGRESO), b.CLASEPLANTILLA
				from hadm a 
				left join mem1 b on a.NOADMISION=b.NOADMISION and b.n=1
				left join afi c on a.IDAFILIADO=c.IDAFILIADO
				where a.CERRADA=1 and coalesce(a.CLASENOPROC,'')<>'NP'   and
				dbo.FNK_ANTIGUEDAD(c.FNACIMIENTO,a.FECHAALTA,'A') between '$edadini' and '$edadfin' and a.FECHA between '$fechaini' and '$fechafin'
				),
				mem3 as (
				select n=ROW_NUMBER() over (order by [<1]+[1-4]+[5-9]+[10-14]+[15-19]+[20-44]+[45-59]+[>=60] desc), *, 
				Total=[<1]+[1-4]+[5-9]+[10-14]+[15-19]+[20-44]+[45-59]+[>=60] 
				from (
				select a.DXINGRESO, b.DESCRIPCION,  a.geta
				from mem2 a 
				left join mdx b on a.DXINGRESO=b.IDDX
				where  not a.DXINGRESO is null and a.DXINGRESO<>'' ) x pivot (count(geta) for geta in 
				([<1],[1-4],[5-9],[10-14],[15-19],[20-44],[45-59],[>=60])  
				) b
				) 
				select top 20 n,DXINGRESO,DESCRIPCION,[<1],[1-4],[5-9],[10-14],[15-19],[20-44],[45-59],[>=60],Total from mem3  ";
$sth = $conn->prepare($consulta);
//echo $consulta;
$sth->execute();
$result = $sth->fetchall(PDO::FETCH_ASSOC);
$tbl='<h3>Reporte de Morbilidad</h3>';
$tbl.='<h4>Desde: '.$fechaini.' - Hasta: '.$fechafin.'  - EdadIni: '.$edadini.' - EdadFin: '.$edadfin.' - Sexo: '.$sexo.'</h4>';
$mpdf->writeHTML($tbl);
$tbl = '<table border="1" style="border-collapse: collapse;font-size: 8pt;">';
$tbl .= '<tr><td>N</td><td>DxIngreso</td><td>Descripción</td><td>[<1]</td><td>[1-4]</td>
<td>[5-9]</td><td>[10-14]</td><td>[15-19]</td><td>[20-44]</td><td>[45-59]</td><td>[>=60]</td><td>Total</td></tr>';
foreach ($result as $row) {
    $tbl .= '<tr>';
    $tbl .= '<td>' . $row["n"] . '</td>';
    $tbl .= '<td>' . $row['DXINGRESO'] . '</td>';
    $tbl .= '<td>' . $row['DESCRIPCION'] . '</td>';
    $tbl .= '<td>' . $row['<1'] . '</td>';
    $tbl .= '<td>' . $row['1-4'] . '</td>';
    $tbl .= '<td>' . $row["5-9"] . '</td>';
    $tbl .= '<td>' . $row['10-14'] . '</td>';
    $tbl .= '<td>' . $row['15-19'] . '</td>';
    $tbl .= '<td>' . $row['20-44'] . '</td>';
    $tbl .= '<td>' . $row['45-59'] . '</td>';
    $tbl .= '<td>' . $row['>=60'] . '</td>';
    $tbl .= '<td>' . $row['Total'] . '</td>';
    $tbl .= '</tr>';
}
$tbl .= '</table>';
//echo $tbl;
// Write some HTML code:
$mpdf->WriteHTML($tbl);

// Output a PDF file directly to the browser
$mpdf->Output();