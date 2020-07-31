<?php
require_once('database.class.php');
$conn = new Database();
$consulta = "SELECT CODIGO,DESCRIPCION FROM TGEN WHERE TABLA='REPORTES' AND CAMPO='SEDERURAL'";
$sth = $conn->prepare($consulta);
$sth->execute();
$result = $sth->fetchall(PDO::FETCH_ASSOC);
?>
<div class="starter-template">
    <h3>Eventos Adversos</h3>
</div>
<form name="form1" action="med_rural_rpt.php" method="POST">
    <div class="row">
        <div class="col">
            <div class="form-group">
                <label for="sede">Sede:</label>
                <select name="idsede" id="sede" class="form-control">
                    <option value="" selected>Todas</option>
                    <?php
	                    foreach ($result as $row) {
	                       echo "<option value='" . $row['CODIGO'] . "'>" . $row['DESCRIPCION'] . "</option>";
	                    }
                    ?>
                </select>
            </div>
        </div>
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