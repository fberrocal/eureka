<?php
require_once('database.class.php');
$conn = new Database();
$consulta = "SELECT IDSEDE, DESCRIPCION FROM SED ORDER BY 1";
$sth = $conn->prepare($consulta);
$sth->execute();
$result = $sth->fetchall(PDO::FETCH_ASSOC);

    /* Query para la creación del ítem de menú en la base de datos
        SET IDENTITY_INSERT menu ON;
        go
        insert into menu (menuid,secuencia,nombre,idpadre,link,activo)
        values (490,3070,'Atención integral adolescencia,Juventud,adultez y vejez',20,'?r=integral',1);
        go
        SET IDENTITY_INSERT menu OFF;
        go

        insert into menud (MENUID,USUARIO,ACCESO) values (490,'AGALVIS',1);
        insert into menud (MENUID,USUARIO,ACCESO) values (490,'VBRUN',1);
        insert into menud (MENUID,USUARIO,ACCESO) values (490,'SHERRERA',1);
    */
?>

<div class="starter-template">
    <h3>Atenci&oacute;n Integral en adolescencia, Juventud, adultez y vejez</h3>
</div>
<form name="form1" action="integral_rpt.php" method="POST">
    <div class="row">
        <div class="col">
            <div class="form-group">
                <label for="sede">Sede:</label>
                <select name="idsede" id="sede" class="form-control">
                    <option value="" selected>Todas</option>
                    <?php
                    foreach ($result as $row) {
                        echo "<option value='" . $row['IDSEDE'] . "'>" . $row['DESCRIPCION'] . "</option>";
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
        <div class="row">
            <div class="col">
                <br>
                <button type="submit" class="btn btn-primary">Consultar</button>
            </div>
        </div>

</form>
<hr>