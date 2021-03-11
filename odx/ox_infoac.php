<?php
    require_once('./database.class.php');
    $conn = new Database();
    
    /*
    $consulta = "SELECT IDSEDE, DESCRIPCION FROM SED ORDER BY 1";
    $sth = $conn->prepare($consulta);
    $sth->execute();
    $result = $sth->fetchall(PDO::FETCH_ASSOC);
    */

    /* Query para la creación del ítem de menú en la base de datos
            SET IDENTITY_INSERT menu ON;
            go
            insert into menu (MENUID,SECUENCIA,NOMBRE,IDPADRE,LINK,ACTIVO) 
            values (750,3600,'Información Archivo AC de Odontología',30,'?r=./odx/ox_infoac',1);
            go
            SET IDENTITY_INSERT menu OFF;
            go

            insert into menud (MENUID,USUARIO,ACCESO) values (750,'AGALVIS',1);
            insert into menud (MENUID,USUARIO,ACCESO) values (750,'VBRUN',1);
    */

    /*
    $consulta2 = "SELECT IDSERVICIO, DESCSERVICIO FROM SER WHERE PREFIJO='550' ORDER BY 2";
    $sth2 = $conn->prepare($consulta2);
    $sth2->execute();
    $result2 = $sth2->fetchall(PDO::FETCH_ASSOC);
    */
?>

<div class="starter-template">
    <h3>Informaci&oacute;n archvio AC Odontología</h3>
</div>

<form name="form1" action="./odx/ox_infoac_rpt.php" method="POST">
    
    <!-- Fechas de inicio y fin del reporte -->

    <div class="row">

        <!-- Fecha inicial del reporte -->
        <div class="col">
            <div class="form-group">
                <label for="fechaini">Fecha Inicial:</label>
                <input type="date" class="form-control" name="fechaini" id="fechaini" placeholder="dd/mm/yyyy" required>
            </div>
        </div>
        
        <!-- Fecha final del reporte -->
        <div class="col">
            <div class="form-group">
                <label for="fechafin">Fecha Final:</label>
                <input type="date" class="form-control" name="fechafin" id="fechafin" placeholder="dd/mm/yyyy" required>
            </div>
        </div>

    </div>
    
    <!-- Botón de comando para lanzar la consulta -->

    <div class="row">
        <div class="col">
            <br>
            <button type="submit" class="btn btn-primary">Consultar</button>
        </div>
    </div>

</form>
<hr>
