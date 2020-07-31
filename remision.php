<?php
require_once('database.class.php');
$conn = new Database();
$consulta = "SELECT IDSEDE, DESCRIPCION FROM SED ORDER BY 1";
$sth = $conn->prepare($consulta);
$sth->execute();
$result = $sth->fetchall(PDO::FETCH_ASSOC);

$consulta2 = "SELECT IDSERVICIO, DESCSERVICIO FROM SER WHERE PREFIJO='550' ORDER BY 2";
$sth2 = $conn->prepare($consulta2);
$sth2->execute();
$result2 = $sth2->fetchall(PDO::FETCH_ASSOC);
?>

<div class="starter-template">
    <h3>Remisiones</h3>
</div>
<form name="form1" action="remision_rpt.php" method="POST">
    <div class="row">
       <div class="col">
            <div class="form-group">
                <label for="fechaini">Fecha Inicial:</label>
                <input type="date" class="form-control" name="fechaini" id="fechaini" placeholder="dd/mm/yyyy" required>
            </div>
        </div>
        <div class="col">
            <div class="form-group">
                <label for="fechafin">Fecha Final:</label>
                <input type="date" class="form-control" name="fechafin" id="fechafin" placeholder="dd/mm/yyyy" required>
            </div>
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