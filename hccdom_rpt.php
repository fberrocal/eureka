<?php
    // Require composer autoload
    require_once __DIR__ . '/vendor/autoload.php';  // <-- Using Composer - En utilisant Composer
    use PhpOffice\PhpSpreadsheet\Spreadsheet;
    use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
    $plantilla='HCCDOM';                          // <-- Clase de Plantilla [Adulto Mayor]
    $spreadsheet  = new Spreadsheet();              /*----Spreadsheet object-----*/
    $Excel_writer = new Xlsx($spreadsheet);         /*----- Excel (Xls) Object*/
    $spreadsheet->setActiveSheetIndex(0);
    $activeSheet  = $spreadsheet->getActiveSheet();

    $campos_mpl = "";           // <-- Variable que contiene los campos de la plantilla, para el llamado al SP
    $qry        = "";           // <-- Consulta de Campos

    require_once('config.php');
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
   
    $cabecera = $cabecerag . "
    UNION ALL
    select CAMPO, DESCCAMPO from mpld where CLASEPLANTILLA='$plantilla' and CAMPO<>'Sexo' and CAMPO<>'EDAD' and ltrim(rtrim(tipo_campo)) not in ('Titulo','Subtitulo')";
   
    // SE ARMA LA CONSULTA PARA CONCATENAR EN EL LLAMADO AL STORED PROCEDURE

    $qry = "SELECT CAMPO,DESCCAMPO FROM mpld WHERE CLASEPLANTILLA='$plantilla' and CAMPO<>'Sexo' and CAMPO<>'EDAD' and ltrim(rtrim(tipo_campo)) not in ('Titulo','Subtitulo')";
    $cmp = $conn->prepare($qry);

    $cmp->execute();

    $res        = $cmp->fetchall(PDO::FETCH_ASSOC);
    $campos_mpl = "";

    foreach($res as $row) {
        $campos_mpl .= "[" . $row['CAMPO'] . "],";
    }

    $campos_mpl = substr($campos_mpl, 0, -1);

    /** Se ejecuta la consulta de los encabezados y se arma el encabezado de Excel */

    $sth = $conn->prepare($cabecera);
    $sth->execute();
    $result = $sth->fetchall(PDO::FETCH_ASSOC);
    $x=1;
    $y=1;
    foreach($result as $row) {
        $activeSheet->setCellValueByColumnAndRow($x,$y,$row['DESCCAMPO']);
        $x++;
    }

    $consulta="exec spV_HCA_Pivot_v2 '$fechaini','$fechafin','$plantilla','$idsede','".$campos_mpl."'";
   
    $sth = $conn->prepare($consulta);
    $sth->execute();
    $result = $sth->fetchall(PDO::FETCH_ASSOC);
    $x=1;
    $y=2;
    foreach($result as $key=>$row) {
        foreach($row as $key2=>$row2){
            $activeSheet->setCellValueByColumnAndRow($x,$y,preg_replace(array('/--/','/\+\+/','/==/'), ' ',$row2));
            $x++;
        }
        $y++;
        $x=1;
    }
    
    $filename='hccdom';
    //$activeSheet->setCellValue('A1' , 'New file content')->getStyle('A1')->getFont()->setBold(true);
    header('Content-Type: application/vnd.ms-excel');
    header('Content-Disposition: attachment;filename="'. $filename .'.xlsx"'); /*-- $filename is  xsl filename ---*/
    header('Cache-Control: max-age=0');
    $Excel_writer->save('php://output');
    exit;
    