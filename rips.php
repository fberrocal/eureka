<?php
require_once('database.class.php');
$conn = new Database();
$consulta = "select IDTERCERO,RAZONSOCIAL from TER WHERE IDTERCERO IN ('891080005','800608394','890102044','804002105','900226715','811004055','900156264')";
$sth = $conn->prepare($consulta);
$sth->execute();
$result = $sth->fetchall(PDO::FETCH_ASSOC);
?>

    <div class="starter-template">
        <h3>Reporte RIPS </h3>
    </div>
    <form name="form1" action="" method="POST">
        <input type="hidden" name="submitted" value="1">
        <div class="row">
            <div class="col">
                <div class="form-group">
                    <label for="archivo">Archivo:</label>
                    <select name="archivo" id="archivo" class="form-control" required>
                        <option value="" selected></option>
                        <option value="AC">AC</option>
                        <option value="AP">AP</option>
                        <option value="AM">AM</option>
                        <option value="AT">AT</option>
                    </select>
                </div>
            </div>
            <div class="col">
                <div class="form-group">
                    <label for="contratante">Contratante:</label>
                    <select name="idcontratante" id="contratante" class="form-control" required>
                        <option value="" selected></option>
                        <?php
                        foreach ($result as $row) {
                            echo "<option value='" . $row['IDTERCERO'] . "'>" . $row['RAZONSOCIAL'] . "</option>";
                        }

                        ?>
                    </select>
                </div>
            </div>
            <div class="col">
                <div class="form-group">
                    <label for="finalidad">Contrato:</label>
                    <select name="contrato" id="contrato" class="form-control" required>
                        <option value="" selected></option>
                        <option value="M">Morbilidad</option>
                        <option value="P">PyP</option>
                    </select>
                </div>
            </div>
            <div class="col">
                <div class="form-group">
                    <label for="fechaini">Fecha Inicial:</label>
                    <input type="date" class="form-control" name="fechaini" id="fechaini" placeholder="dd/mm/yyyy"
                           required>
                </div>
            </div>
            <div class="col">
                <div class="form-group">
                    <label for="fechafin">Fecha Final:</label>
                    <input type="date" class="form-control" name="fechafin" id="fechafin" placeholder="dd/mm/yyyy"
                           required>
                </div>
            </div>
            <div class="row">
                <div class="col">
                    <br>
                    <button type="submit" class="btn btn-primary">Consultar</button>
                </div>
            </div>

    </form>
    <hr>
<?php
if (isset($_POST['submitted'])) {
    $archivo = $_POST['archivo'];
    $idcontratante = $_POST['idcontratante'];
    $contrato = $_POST['contrato'];
    $parts = explode('-', $_POST['fechaini']);
    $fechaini = $parts[2] . '/' . $parts[1] . '/' . $parts[0];
    $fechaini .= ' 00:00:00.000';
    $parts = explode('-', $_POST['fechafin']);
    $fechafin = $parts[2] . '/' . $parts[1] . '/' . $parts[0];
    $fechafin .= ' 23:59:59.997';
    $finalidad = "";

    if ($contrato == 'M') {
        if ($archivo == 'AC') {
            $finalidad = "='10'";
        } else {
            $finalidad = "='1'";
        }
    } elseif ($contrato == 'P') {
        if ($archivo == 'AC') {
            $finalidad = "<>'10'";
        } else {
            $finalidad = "<>'1'";
        }
    }
    $consulta="DECLARE @IDCONTRATANTE VARCHAR(20)='".$idcontratante."';
DECLARE @FECHAINI DATETIME='".$fechaini."';
DECLARE @FECHAFIN DATETIME='".$fechafin."';
DECLARE @ARCHIVORIPS VARCHAR(20)='".$archivo."';
IF @ARCHIVORIPS='AC'
BEGIN
	select
	'FN0000001' AS NUMERO_FACTURA,
	'230010055301' AS CODIGO_PRESTADOR,
	VWA_RIPS.TIPO_DOC,
	VWA_RIPS.IDAFILIADO,
	CONVERT(VARCHAR(10),VWA_RIPS.FECHA,103) AS FECHA_CONSULTA,
	VWA_RIPS.IDALTERNA AS CODIGO_CONSULTA,
	VWA_RIPS.FINALIDAD,
	VWA_RIPS.CAUSAEXT,
	VWA_RIPS.IDDX AS DX_PRINCIPAL,
	VWA_RIPS.DX1,
	VWA_RIPS.DX2,
	VWA_RIPS.DX3,
	VWA_RIPS.TIPODX,
	CONVERT(INT,VWA_RIPS.VALOR) AS VALOR,
	CONVERT(INT,VWA_RIPS.VALORCOPAGO) AS VALORCOPAGO,
	CONVERT(INT,VWA_RIPS.VRNETO) AS VRNETO
	from [dbo].[VWA_RIPS]
	WHERE 
	VWA_RIPS.CAPITADO = '1'
	AND VWA_RIPS.IDCONTRATANTE = @IDCONTRATANTE
	AND VWA_RIPS.FECHA  between  @FECHAINI and  @FECHAFIN
	AND VWA_RIPS.ARCHIVORIPS = @ARCHIVORIPS
	AND VWA_RIPS.FINALIDAD  ".$finalidad." 
END
IF @ARCHIVORIPS='AP'
BEGIN
	select
    'FN0000001' AS NUMERO_FACTURA,
	'230010055301' AS CODIGO_PRESTADOR,
    VWA_RIPS.TIPO_DOC,
    VWA_RIPS.IDAFILIADO,
    CONVERT(VARCHAR(10),VWA_RIPS.FECHA,103) AS FECHA_PROCEDIMIENTO,
    VWA_RIPS.NOAUTORIZACION,
    VWA_RIPS.IDALTERNA AS CODIGO_PROCEDIMIENTO,
    VWA_RIPS.AMBITO,
    REPLACE(VWA_RIPS.FINALIDAD,' ','') AS FINALIDAD,
    VWA_RIPS.PERSONALAT,
    '' DX_PRINCIPAL,
    '' AS DX1,
    '' AS COMPLICACION,
    VWA_RIPS.FORMAREALIZACION,
    CONVERT(INT,VWA_RIPS.VALOR) AS VALOR
	from [dbo].[VWA_RIPS]
	WHERE 
	VWA_RIPS.CAPITADO = '1'
	AND VWA_RIPS.IDCONTRATANTE = @IDCONTRATANTE
	AND VWA_RIPS.FECHA  between  @FECHAINI and  @FECHAFIN
	AND VWA_RIPS.ARCHIVORIPS = @ARCHIVORIPS
	AND VWA_RIPS.FINALIDAD  ".$finalidad." 
END

IF @ARCHIVORIPS='AM'
BEGIN
	select
    'FN0000001' AS NUMERO_FACTURA,
	'230010055301' AS CODIGO_PRESTADOR,
    VWA_RIPS.TIPO_DOC,
    VWA_RIPS.IDAFILIADO,
    VWA_RIPS.NOAUTORIZACION,
    VWA_RIPS.IDALTERNA AS CODIGO_MEDICAMENTO,
    VWA_RIPS.TIPOMED,
    LEFT(REPLACE(VWA_RIPS.DESCSERVICIO,',',' '),60) AS NOMBRE_GENERICO,
    '' AS FORMA,----EN BLANCO CUANDO TIPOMED = 1
    '' AS CONCENTRACION,--EN BLANCO CUANDO TIPOED = 1
    '' UMEDIDA,--EN BLANCO CUANDO TIPOED = 1
    CONVERT(INT,VWA_RIPS.CANTIDAD) AS CANTIDAD,
    CONVERT(INT,VWA_RIPS.VALOR) AS VALOR,
    CONVERT(INT,VWA_RIPS.VRNETO) AS VRNETO
	from [dbo].[VWA_RIPS]
	WHERE 
	VWA_RIPS.CAPITADO = '1'
	AND VWA_RIPS.IDCONTRATANTE = @IDCONTRATANTE
	AND VWA_RIPS.FECHA  between  @FECHAINI and  @FECHAFIN
	AND VWA_RIPS.ARCHIVORIPS = @ARCHIVORIPS
	AND VWA_RIPS.FINALIDAD  ".$finalidad." 
END
IF @ARCHIVORIPS='AT'
BEGIN
	select
     'FN0000001' AS NUMERO_FACTURA,
	'230010055301' AS CODIGO_PRESTADOR,
    VWA_RIPS.TIPO_DOC,
    VWA_RIPS.IDAFILIADO,
    VWA_RIPS.NOAUTORIZACION,
	VWA_RIPS.TIPOSERVICIO,
    VWA_RIPS.IDALTERNA AS CODIGO_SERVICIO,
    LEFT(REPLACE(VWA_RIPS.DESCSERVICIO,',',' '),60) AS NOMBRE_SERVICIO,
    CONVERT(INT,VWA_RIPS.CANTIDAD) AS CANTIDAD,
    CONVERT(INT,VWA_RIPS.VALOR) AS VALOR,
    CONVERT(INT,VWA_RIPS.VRNETO) AS VRNETO 
	from [dbo].[VWA_RIPS]
	WHERE 
	VWA_RIPS.CAPITADO = '1'
	AND VWA_RIPS.IDCONTRATANTE = @IDCONTRATANTE
	AND VWA_RIPS.FECHA  between  @FECHAINI and  @FECHAFIN
	AND VWA_RIPS.ARCHIVORIPS = @ARCHIVORIPS
	AND VWA_RIPS.FINALIDAD  ".$finalidad." 
END";
    $sth = $conn->prepare($consulta);
    $sth->execute();
    $result = $sth->fetchall(PDO::FETCH_ASSOC);
    $x=1;
    $y=1;
    $texto="";
    /*$fp = fopen('file.csv', 'w');
    foreach ($result as $fields) {
        fputcsv($fp, $fields);
    }
    fclose($fp);*/
    //print_r($result);
    //echo "<textarea>".$texto."</textarea>";
//echo "<table border=1 style='border-collapse: collapse'>";
    foreach($result as $key=>$row) {
        //echo "<tr>";
        $x=1;
        foreach($row as $key2=>$row2){
            //echo "<td>" . $row2 . "</td>";
    //        $activeSheet->setCellValueByColumnAndRow($x,$y,$row2);
            $texto.=$row2.',';
        }
        $texto=substr($texto,0,strlen($texto)-1);
        $texto.="\r\n";
        //echo "</tr>";
    }
    $texto=substr($texto,0,strlen($texto)-1);
    echo '<div class="container-fluid"> <br> <form><div class="row"> <div class="col">';
    echo "<br><textarea class='form-control' rows='10' id='textArea'>".$texto."</textarea>";
    echo '</div></div><div class="row"> <button type="button" class="btn btn-success" value="save" id="save">Guardar</button></div></form></div>';
//echo "</table>";
}
?>
<script>
    function saveTextAsFile() {
        var textToWrite = document.getElementById('textArea').innerHTML;
        var textFileAsBlob = new Blob([ textToWrite ], { type: 'text/plain' });
        var fileNameToSaveAs = "rips.txt";

        var downloadLink = document.createElement("a");
        downloadLink.download = fileNameToSaveAs;
        downloadLink.innerHTML = "Download File";
        if (window.webkitURL != null) {
            // Chrome allows the link to be clicked without actually adding it to the DOM.
            downloadLink.href = window.webkitURL.createObjectURL(textFileAsBlob);
        } else {
            // Firefox requires the link to be added to the DOM before it can be clicked.
            downloadLink.href = window.URL.createObjectURL(textFileAsBlob);
            downloadLink.onclick = destroyClickedElement;
            downloadLink.style.display = "none";
            document.body.appendChild(downloadLink);
        }

        downloadLink.click();
    }

    var button = document.getElementById('save');
    button.addEventListener('click', saveTextAsFile);

    function destroyClickedElement(event) {
        // remove the link from the DOM
        document.body.removeChild(event.target);
    }
</script>
