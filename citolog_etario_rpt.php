<?php
// Require composer autoload
require_once __DIR__ . '/vendor/autoload.php';
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
$plantilla='CITOLOG';
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


$columnas=array("IDSEDE","DESCRIPCION","IDMEDICO","MEDICO","PNOMBRE","SNOMBRE","PAPELLIDO","SAPELLIDO","TIPO_DOC","DOCIDAFILIADO",
    "FNACIMIENTO","EDAD","GRUPOETAREO","DIRECCION","TELEFONO","GRUPOETNICO","RAZONSOCIAL","FECHAMUESTRA",
    "CONSULTA","FECHALECT","CALIDAD","CATGRAL","MICROORG","NONEO","TUMOR","ESCAMOSA","GLANDULAR","OTRASNEO","OBSERVA2");

$cabecera=array("Id Sede","Sede","IdMedico","Medico","P. Nombre","S. Nombre","P. Apellido","S. Apellido","Tipo Documento","Documento",
    "Fecha Nacimiento","Edad","Grupo Etario","Dirección","Teléfono","Grupo Étnico","EPS","Fecha Muestra",
    "Consulta","Fecha Lectura","Calidad de la Muesta","Categorización General","Microorganismos",
    "Otros Hallazgos no Neoplásicos","Células tumorales no epiteliales","Anormalidades en células escamosas",
    "Anormalidades en células glandulares","Otras neoplasias malignas","Observaciones");
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


$consulta="DECLARE @FechaIni datetime = '$fechaini',
        @FechaFin datetime = '$fechafin',
        @IDSEDE varchar(2) = '$idsede';
SELECT *
FROM (SELECT
  HCA.IDSEDE, SED.DESCRIPCION,
  HCA.IDMEDICO, MED.NOMBRE AS MEDICO,
  COALESCE(AFI.PNOMBRE, '') AS PNOMBRE,
  COALESCE(AFI.SNOMBRE, '') AS SNOMBRE,
  COALESCE(AFI.PAPELLIDO, '') AS PAPELLIDO,
  COALESCE(AFI.SAPELLIDO, '') AS SAPELLIDO,
  AFI.TIPO_DOC, AFI.DOCIDAFILIADO, AFI.FNACIMIENTO,
  dbo.fna_EdadenAnos(AFI.FNACIMIENTO, HCA.FECHA) AS EDAD,
  [GRUPOETAREO] = 
	case when dbo.fna_EdadenMeses(AFI.FNACIMIENTO,HCA.FECHA) < 300 then '<25 años' else
	case when dbo.fna_EdadenMeses(AFI.FNACIMIENTO,HCA.FECHA) <= 408 then '25 a 44 años' else
	case when dbo.fna_EdadenMeses(AFI.FNACIMIENTO,HCA.FECHA) <= 648 then '45 a 54 años' else
	case when dbo.fna_EdadenMeses(AFI.FNACIMIENTO,HCA.FECHA) <= 768 then '55 a 64 años' else '>=65 años' end
	end	
	end
	end,
  AFI.DIRECCION,
  COALESCE(AFI.TELEFONORES, '') AS TELEFONO,
  COALESCE(TGEN.DESCRIPCION, '') AS GRUPOETNICO,
  TER.RAZONSOCIAL, HCA.FECHA AS FECHAMUESTRA,
  COALESCE(
  (CASE HCAD.TIPOCAMPO
    WHEN 'Alfanumerico' THEN CONVERT(nvarchar(max), HCAD.ALFANUMERICO)
    WHEN 'Memo' THEN CONVERT(nvarchar(max), HCAD.MEMO)
    WHEN 'Lista' THEN CONVERT(nvarchar(max), HCAD.ALFANUMERICO)
    WHEN 'Fecha' THEN CONVERT(nvarchar(max), CONVERT(varchar(12), HCAD.FECHA, 103))
    WHEN 'TGEN' THEN CONVERT(nvarchar(max), dbo.Fn_DescripcionTGEN('CITOLOG', HCAD.CAMPO, HCAD.ALFANUMERICO))
  END
  ), '') AS VARIABLE,
  HCAD.CAMPO
FROM HCAD
INNER JOIN HCA ON HCAD.CONSECUTIVO = HCA.CONSECUTIVO
INNER JOIN AFI ON AFI.IDAFILIADO = HCA.IDAFILIADO 
INNER JOIN MED ON MED.IDMEDICO=HCA.IDMEDICO 
LEFT JOIN TER ON TER.IDTERCERO = AFI.IDADMINISTRADORA
LEFT JOIN TGEN ON AFI.GRUPOETNICO = TGEN.CODIGO AND TGEN.TABLA = 'AFI' AND TGEN.CAMPO = 'GRUPOETNICO'
LEFT JOIN SED ON HCA.IDSEDE = SED.IDSEDE
WHERE HCAD.CAMPO IN ('CONSULTA', 'FECHALECT', 'CALIDAD', 'CATGRAL', 'MICROORG', 'NONEO', 'TUMOR', 'ESCAMOSA', 'GLANDULAR', 'OTRASNEO', 'OBSERVA2')
AND HCA.CLASEPLANTILLA = 'CITOLOG'
AND HCA.FECHA BETWEEN @FechaIni AND @FechaFin
AND HCA.IDSEDE = CASE WHEN COALESCE(@IDSEDE, '') = '' THEN HCA.IDSEDE ELSE @IDSEDE END) PIV
PIVOT (MIN(VARIABLE) FOR CAMPO IN ([CONSULTA], [FECHALECT], [CALIDAD], [CATGRAL], [MICROORG], [NONEO], [TUMOR], [ESCAMOSA], [GLANDULAR], [OTRASNEO], [OBSERVA2])) X; ";

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

$filename='citologiaxgrupoetario';
//$activeSheet->setCellValue('A1' , 'New file content')->getStyle('A1')->getFont()->setBold(true);
header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment;filename="'. $filename .'.xlsx"'); /*-- $filename is  xsl filename ---*/
header('Cache-Control: max-age=0');
$Excel_writer->save('php://output');
exit;