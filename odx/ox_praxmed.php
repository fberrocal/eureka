<?php
require_once('./database.class.php');
$conn = new Database();
$consulta = "SELECT IDSEDE, DESCRIPCION FROM SED ORDER BY 1";
$sth = $conn->prepare($consulta);
$sth->execute();
$result = $sth->fetchall(PDO::FETCH_ASSOC);

/* Query para la creación del ítem de menú en la base de datos
        SET IDENTITY_INSERT menu ON;
        go
        insert into menu (MENUID,SECUENCIA,NOMBRE,IDPADRE,LINK,ACTIVO) 
        values (450,1860,'Historia Clínica de Hipertensión Arterial',10,'?r=hchtaw',1);
        go
        SET IDENTITY_INSERT menu OFF;
        go

        insert into menud (MENUID,USUARIO,ACCESO) values (450,'AGALVIS',1);
        insert into menud (MENUID,USUARIO,ACCESO) values (450,'VBRUN',1);
    */

// $consulta2 = "SELECT IDSERVICIO, DESCSERVICIO FROM SER WHERE PREFIJO='550' AND PYP=1 ORDER BY 2";
$consulta2 = "SELECT IDSERVICIO, DESCSERVICIO FROM SER WHERE PREFIJO='550' ORDER BY 2";
$sth2 = $conn->prepare($consulta2);
$sth2->execute();
$result2 = $sth2->fetchall(PDO::FETCH_ASSOC);
?>

<div class="starter-template">
    <h3>Procedimientos Odontológicos x Profesional</h3>
</div>
<form name="form1" action="./odx/ox_praxmed_rpt.php" method="POST">
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
    </div>
    <div class="row">
        <div class="col">
            <div class="form-group">
                <label for="idservicio">Servicio:</label>
                <select name="idservicio" id="idservicio" class="form-control">
                    <option value="" selected>Todos</option>
                    <?php

                    foreach ($result2 as $row) {
                        echo "<option value='" . $row['IDSERVICIO'] . "'>" . $row['DESCSERVICIO'] . "</option>";
                    }

                    ?>
                </select>
            </div>
        </div>
        <div class="col">
            <div class="form-group">
                <label for="idmedico">Médico:</label>
                <select name="idmedico" id="idmedico" class="form-control">
                    <option value="" selected>Todos</option>
                    <?php
                    $consulta = "SELECT IDMEDICO, NOMBRE FROM MED WHERE IDEMEDICA IN('360','361') ORDER BY 2";
                    $sth = $conn->prepare($consulta);
                    $sth->execute();
                    $result = $sth->fetchall(PDO::FETCH_ASSOC);
                    foreach ($result as $row) {
                        echo "<option value='" . $row['IDMEDICO'] . "'>" . $row['NOMBRE'] . "</option>";
                    }
                    ?>
                </select>
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