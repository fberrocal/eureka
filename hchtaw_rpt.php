<?php
    // Require composer autoload
    require_once __DIR__ . '/vendor/autoload.php';  // <-- Using Composer - En utilisant Composer
    use PhpOffice\PhpSpreadsheet\Spreadsheet;
    use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
    $plantilla='HCHTAW';                          // <-- Clase de Plantilla [Adulto Mayor]
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

    // $cabecera="
    // SELECT 'IDADMINISTRADORA' AS CAMPO, 'IDADMINISTRADORA' AS DESCCAMPO
    // UNION ALL
    // SELECT 'RAZONSOCIAL' AS CAMPO,'EPS' AS DESCCAMPO
    // UNION ALL 
    // SELECT 'TIPO_DOC' AS CAMPO, 'TIPO_DOC' AS DESCCAMPO
    // UNION ALL
    // SELECT 'IDAFILIADO' AS CAMPO, 'IDAFILIADO' AS DESCCAMPO
    // UNION ALL
    // SELECT 'PNOMBRE' AS CAMPO, 'PNOMBRE' AS DESCCAMPO
    // UNION ALL
    // SELECT 'SNOMBRE' AS CAMPO, 'SNOMBRE' AS DESCCAMPO
    // UNION ALL
    // SELECT 'PAPELLIDO' AS CAMPO, 'PAPELLIDO' AS DESCCAMPO
    // UNION ALL
    // SELECT 'SAPELLIDO' AS CAMPO, 'SAPELLIDO' AS DESCCAMPO
    // UNION ALL
    // SELECT 'FNACIMIENTO' AS CAMPO, 'FNACIMIENTO' AS DESCCAMPO
    // UNION ALL
    // SELECT 'EDAD' AS CAMPO, 'EDAD' AS DESCCAMPO
    // UNION ALL
    // SELECT 'SEXO' AS CAMPO, 'SEXO' AS DESCCAMPO
    // UNION ALL
    // SELECT 'DIRECCION' AS CAMPO, 'DIRECCION' AS DESCCAMPO
    // UNION ALL
    // SELECT 'TELEFONORES' AS CAMPO, 'TELEFONO' AS DESCCAMPO
    // UNION ALL
    // SELECT 'GRUPOETNICO' AS CAMPO, 'GRUPOETNICO' AS DESCCAMPO
    // UNION ALL
    // SELECT 'CONSECUTIVO' AS CAMPO, 'CONSECUTIVO' AS DESCCAMPO
    // UNION ALL
    // SELECT 'IDSEDE' AS CAMPO, 'IDSEDE' AS DESCCAMPO
    // UNION ALL
    // SELECT 'SEDE' AS CAMPO, 'SEDE' AS DESCCAMPO
    // UNION ALL
    // SELECT 'FECHA' AS CAMPO, 'FECHA' AS DESCCAMPO
    // UNION ALL
    // SELECT 'CLASEPLANTILLA' AS CAMPO, 'CLASEPLANTILLA' AS DESCCAMPO
    // UNION ALL
    // SELECT 'IDMEDICO' AS CAMPO, 'IDMEDICO' AS DESCCAMPO
    // UNION ALL
    // SELECT 'MEDICO' AS CAMPO, 'MEDICO' AS DESCCAMPO
    // UNION ALL
    // select CAMPO, DESCCAMPO from mpld where CLASEPLANTILLA='$plantilla' and CAMPO<>'Sexo' and ltrim(rtrim(tipo_campo)) not in ('Titulo','Subtitulo')";
    
    $cabecera = $cabecerag . "
    UNION ALL
    select CAMPO, DESCCAMPO from mpld where CLASEPLANTILLA='$plantilla' and CAMPO<>'Sexo' and ltrim(rtrim(tipo_campo)) not in ('Titulo','Subtitulo')";

    // SE ARMA LA CONSULTA PARA CONCATENAR EN EL LLAMADO AL STORED PROCEDURE

    $qry = "SELECT CAMPO,DESCCAMPO FROM mpld WHERE CLASEPLANTILLA='$plantilla' and CAMPO<>'Sexo' and ltrim(rtrim(tipo_campo)) not in ('Titulo','Subtitulo')";
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

    $filename='hchtaw';
    //$activeSheet->setCellValue('A1' , 'New file content')->getStyle('A1')->getFont()->setBold(true);
    header('Content-Type: application/vnd.ms-excel');
    header('Content-Disposition: attachment;filename="'. $filename .'.xlsx"'); /*-- $filename is  xsl filename ---*/
    header('Cache-Control: max-age=0');
    $Excel_writer->save('php://output');
    exit;