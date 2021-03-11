<?php

    require_once __DIR__ . '/vendor/autoload.php';
    use PhpOffice\PhpSpreadsheet\Spreadsheet;
    use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
    $plantilla='CRECIMTO';
    $spreadsheet = new Spreadsheet();  /*----Spreadsheet object-----*/
    $Excel_writer = new Xlsx($spreadsheet);  /*----- Excel (Xls) Object*/
    $spreadsheet->setActiveSheetIndex(0);
    $activeSheet = $spreadsheet->getActiveSheet();

    require_once('config.php');
    require_once('database.class.php');
    $conn = new Database();

    // Captura de la SEDE
    if (!isset($_POST['idsede'])){
        $idsede='';
    } else {
        $idsede=$_POST['idsede'];
    }

    $parts = explode('-', $_POST['fechaini']);
    $fechaini  = $parts[2].'/'.$parts[1].'/'.$parts[0];

    $parts = explode('-', $_POST['fechafin']);
    $fechafin  = $parts[2].'/'.$parts[1].'/'.$parts[0];

$fechaini.=' 00:00:00.000';
$fechafin.=' 23:59:59.997';

$cabecera = $cabecerag . "
UNION ALL
select CAMPO, DESCCAMPO from mpld where CLASEPLANTILLA='$plantilla' and campo in('MOTIVO', 'TIPOCONSUL', 'ACOMPANA', 'EXCLUSIVA',
'LACTANCI', 'EDADTETA', 'EDADLECHE', 'TIPOLECHE', 'EDADALIEM', 'ALIMENADMIN', 'RECIBE', 'LACTAN', 'VECESLACTA', 'BIBERON',
'OTRASLEC', 'VECESOTR', 'ALICOMPL', 'VECESCOMPL', 'MENTON', 'BOCAMAMA', 'LABIOAFUERA', 'AREOLA', 'SUCLENTO', 'AGARRE',
'BCG', 'POLIO1', 'POLIO2', 'POLIO3', 'POLIOR1', 'POLIOR2', 'HB0', 'HB1', 'HB2', 'HB3', 'HIB1', 'HIB2', 'HIB3', 'DPT1', 'DPT2',
'DPT3', 'DPTR1', 'DPTR2', 'TRIPEV1', 'TRIPEV2', 'FIEBREAMA', 'ROTAV1', 'ROTAV2', 'NEUMO1', 'NEUMO2', 'NEUMO3', 'INFLU1', 'INFLU2',
'INFLU3', 'INFLU4', 'HEPATI', 'VARICELA', 'OTRASV', 'FERROSO', 'FECHAFERROSO', 'DOSISFERROSO', 'VITAMINA', 'FECHAVITA', 'DOSISVITA',
'ALBENDAZ', 'FECHAALBEND', 'OTROMEDICA', 'RESULTGRUESA', 'RESULTFINA', 'RESULTAUDI', 'RESULTPER', 'EXAMENFI', 'PESO', 'TALLA',
'IMC', 'PC', 'PT', 'FC', 'FR', 'TA', 'T', 'PERIBRA', 'CLASIFI1', 'CLASIFI2',  'CLASIFI3', 'CLASIFI4', 'CLASIFI5', 
'CITA', 'FECHA1', 'FECHA2', 'FECHA3', 'FECHA4', 'FECHA5', 'FECHA6', 'FECHA7', 'FECHA8',
'FECHA9', 'FECHA10', 'FECHA11', 'FECHA12', 'FECHA13', 'FECHA14', 'FECHA15', 'FECHA16', 'FECHA17', 'FECHA18', 'FECHA19', 'DIAG',
'CODDIAG', 'REMITIDO', 'ESPEC', 'MOTIVOREM','MODOATE', 'OBSFIN')";

$columnas=array();
$i=0;
$sth = $conn->prepare($cabecera);
$sth->execute();
$result = $sth->fetchall(PDO::FETCH_ASSOC);
$x=1;
$y=1;
foreach($result as $row) {
    //echo '<br>'.$row['DESCCAMPO'];
    $activeSheet->setCellValueByColumnAndRow($x,$y,$row['DESCCAMPO']);
    $columnas[$i]=$row['CAMPO'];
    $i++;
    $x++;
}

$consulta="exec spV_HCA_Pivot_v2 '$fechaini','$fechafin','$plantilla','$idsede','[MOTIVO], [TIPOCONSUL], [ACOMPANA], [EXCLUSIVA],
[LACTANCI], [EDADTETA], [EDADLECHE], [TIPOLECHE], [EDADALIEM], [ALIMENADMIN], [RECIBE], [LACTAN], [VECESLACTA], [BIBERON],
[OTRASLEC], [VECESOTR], [ALICOMPL], [VECESCOMPL], [MENTON], [BOCAMAMA], [LABIOAFUERA], [AREOLA], [SUCLENTO], [AGARRE], [BCG],
[POLIO1], [POLIO2], [POLIO3], [POLIOR1], [POLIOR2], [HB0], [HB1], [HB2], [HB3], [HIB1], [HIB2], [HIB3], [DPT1], [DPT2], [DPT3],
[DPTR1], [DPTR2], [TRIPEV1], [TRIPEV2], [FIEBREAMA], [ROTAV1], [ROTAV2], [NEUMO1], [NEUMO2], [NEUMO3], [INFLU1],[INFLU2], [INFLU3],
[INFLU4], [HEPATI], [VARICELA], [OTRASV], [FERROSO], [FECHAFERROSO], [DOSISFERROSO], [VITAMINA], [FECHAVITA], [DOSISVITA],
[ALBENDAZ], [FECHAALBEND], [OTROMEDICA], [RESULTGRUESA], [RESULTFINA], [RESULTAUDI], [RESULTPER], [EXAMENFI], [PESO], [TALLA],
[IMC], [PC], [PT], [FC], [FR], [TA], [T], [PERIBRA], [CLASIFI1], [CLASIFI2], [CLASIFI3],  [CLASIFI4], [CLASIFI5], [CITA],
 [FECHA1], [FECHA2], [FECHA3], [FECHA4], [FECHA5], [FECHA6], [FECHA7],
[FECHA8], [FECHA9], [FECHA10], [FECHA11], [FECHA12], [FECHA13], [FECHA14], [FECHA15], [FECHA16], [FECHA17], [FECHA18], [FECHA19],
[DIAG], [CODDIAG], [REMITIDO], [ESPEC], [MOTIVOREM], [MODOATE],[OBSFIN]' ";


$sth = $conn->prepare($consulta);
$sth->execute();
if(!$sth){
    print_r($sth->errorInfo());
}
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

$filename='cyd';
//$activeSheet->setCellValue('A1' , 'New file content')->getStyle('A1')->getFont()->setBold(true);
header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment;filename="'. $filename .'.xlsx"'); /*-- $filename is  xsl filename ---*/
header('Cache-Control: max-age=0');
$Excel_writer->save('php://output');
exit;