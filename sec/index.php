<?php
require_once('database.class.php');
$message='';
$con = new Database();
$consulta = " SELECT * FROM MENUD WHERE USUARIO='" . $_SESSION['usuario'] . "' AND MENUID=-1 AND ACCESO=1";
$sth = $con->prepare($consulta);
$sth->execute();
if ($sth->rowCount() == 0) {
    echo '<div class="alert alert-danger" role="alert">Acceso Restringido</div>';
    exit;
}
if(isset($_POST['bgenusuario'])){
$consulta = "DECLARE @USUARIO VARCHAR(20)='".$_POST['usuarioid']."';
INSERT INTO MENUD(MENUID,USUARIO,ACCESO) 
SELECT MENUID,@USUARIO,1 
FROM MENU 
WHERE MENUID>0 
AND MENUID NOT IN(SELECT MENUID 
FROM MENUD WHERE USUARIO=@USUARIO)";
$sth = $con->prepare($consulta);
$sth->execute();
$message='Usuario '.$_POST['usuarioid'].' agregado. Verifique los permisos';
}
?>
<div class="container box">
    <h1 align="center">Usuarios - Seguridad</h1>
    <br/>
    <div class="table-responsive">
        <br/>
        <form action="" method="post" name="genusuario">
            <div class="row">
                <div class="col-lg-3">
                    <label for="usuarioid"><strong>Generar menú para el usuario</strong></label>
                </div>
                <div class="col">
                    <select id="usuarioid" name="usuarioid" class="form-control" required>
                        <option value=""></option>
                        <option value=""></option>
                        <?php
                        $consulta = "SELECT USUARIO,NOMBRE FROM USUSU ORDER BY 1";
                        $sth = $con->prepare($consulta);
                        $sth->execute();
                        $result = $sth->fetchall(PDO::FETCH_ASSOC);
                        foreach ($result as $row) {
                            echo "<option value='" . $row['USUARIO'] . "'>" . $row['USUARIO'] . ' - ' . $row['NOMBRE'] . "</option>";
                        }
                        ?>
                    </select>
                </div>
                <div class="col">
                    <button name="bgenusuario" type="submit" class="btn btn-primary">Generar</button>
                </div>
            </div>
        </form>
        <br/>
        <div align="right">
            <button type="button" id="add_button" data-toggle="modal" data-target="#userModal"
                    class="btn btn-info btn-lg">Nuevo
            </button>
        </div>
        <br/><br/>
        <table id="user_data" class="table table-bordered table-striped">
            <thead>
            <tr>
                <th width="10%">Id</th>
                <th width="30%">Menú</th>
                <th width="30%">Padre</th>
                <th width="20%">Usuario</th>
                <th width="10%">Acceso</th>
                <th></th>
                <th></th>
            </tr>
            </thead>
            <tfoot>
            <tr>
                <th>Id</th>
                <th>Menu</th>
                <th>Padre</th>
                <th>Usuario</th>
                <th>Acceso</th>
            </tr>
            </tfoot>
        </table>
    </div>
</div>
</body>
</html>

<div id="userModal" class="modal fade">
    <div class="modal-dialog">
        <form method="post" id="user_form" enctype="multipart/form-data">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Nuevo</h4>
                </div>
                <div class="modal-body">
                    <br/>
                    <label>Menú</label>
                    <select name="MENUID" id="menuid" class="form-control" required>
                        <option value=""></option>
                        <?php
                        $consulta = "SELECT MENUID, NOMBRE,SECUENCIA, IDPADRE FROM MENU WHERE MENUID>0 ORDER BY SECUENCIA";
                        $sth = $con->prepare($consulta);
                        $sth->execute();
                        $result = $sth->fetchall(PDO::FETCH_ASSOC);
                        $level = '';
                        foreach ($result as $row) {
                            if ($row['IDPADRE'] != 0) {
                                $level = ' -> ';
                            } else {
                                $level = '';
                            }
                            echo "<option value='" . $row['MENUID'] . "'>" . $level . $row['NOMBRE'] . "</option>";
                        }
                        ?>
                    </select>
                    <br/>
                    <label>Usuario</label>
                    <select name="USUARIO" id="usuario" class="form-control" required>
                        <option value=""></option>
                        <?php
                        $consulta = "SELECT USUARIO,NOMBRE FROM USUSU ORDER BY 1";
                        $sth = $con->prepare($consulta);
                        $sth->execute();
                        $result = $sth->fetchall(PDO::FETCH_ASSOC);
                        foreach ($result as $row) {
                            echo "<option value='" . $row['USUARIO'] . "'>" . $row['USUARIO'] . ' - ' . $row['NOMBRE'] . "</option>";
                        }
                        ?>
                    </select>

                    <br/>
                    </select>
                    <br/>
                    <label>Acceso</label>
                    <select name="ACCESO" id="acceso" class="form-control" required>
                        <option value="0">No</option>
                        <option value="1">Si</option>
                        ?>
                    </select>
                    <!--  <label>Es Producto?</label>
                      <input type="checkbox" name="esproducto" id="esproducto" class="form-control" value="1">
                          <br />-->
                </div>
                <div class="modal-footer">
                    <input type="hidden" name="menudid" id="menudid"/>
                    <input type="hidden" name="operation" id="operation"/>
                    <input type="submit" name="action" id="action" class="btn btn-success" value="Aceptar"/>
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </form>
    </div>
</div>

<script type="text/javascript" language="javascript">
    var pagina = 'sec/';
    $(document).ready(function () {
        $('#add_button').click(function () {
            $('#user_form')[0].reset();
            $('.modal-title').text("Nuevo");
            $('#action').val("Aceptar");
            $('#operation').val("Add");

        });

        var dataTable = $('#user_data').DataTable({
            "language": {
                "url": "Spanish.json"
            },
            "paging": true,
            "processing": true,
            "serverSide": true,
            "order": [],
            "ajax": {
                url: pagina + "fetch.php",
                type: "POST"
            },
            "columnDefs": [
                {
                    "targets": 0,
                    "orderable": false,
                },
            ],
            initComplete: function () {
                this.api().columns().every(function () {
                    var column = this;
                    var select = $('<select><option value=""></option></select>')
                        .appendTo($(column.footer()).empty())
                        .on('change', function () {
                            var val = $.fn.dataTable.util.escapeRegex(
                                $(this).val()
                            );

                            column
                                .search(val ? '' + val + '' : '', true, false)
                                .draw();
                        });

                    column.data().unique().sort().each(function (d, j) {
                        select.append('<option value="' + d + '">' + d + '</option>')
                    });
                });
            },
        });
         $('#buscar').on( 'keyup', function () {
            dataTable.search( this.value ).draw();
        } );

        $(document).on('submit', '#user_form', function (event) {
            event.preventDefault();
            
            // var formElement = document.getElementById("user_form");
            // datos = new FormData(formElement);

            datos = new FormData(this);
            datos.append("menuid",  document.forms['user_form'].elements['menuid'].value);
            datos.append("usuario", document.forms['user_form'].elements['usuario'].value);
            datos.append("acceso",  document.forms['user_form'].elements['acceso'].value);
            datos.append("menudid", document.forms['user_form'].elements['menudid'].value);
            
            // console.log(datos.get("menuid"));
            // console.log(datos.get("usuario"));
            // console.log(datos.get("acceso"));
            // console.log(datos.get("menudid"));

            $.ajax({
                url: pagina + "insert.php",
                method: 'POST',
                // data: new FormData(this),
                data: datos,
                contentType: false,
                processData: false,
                success: function (data) {
                    // console.log(data);
                    alert(data);
                    $('#user_form')[0].reset();
                    $('#userModal').modal('hide');
                    dataTable.ajax.reload();
                }
            });
        });

        $(document).on('click', '.update', function () {
            var menudid = $(this).attr("id");
            $.ajax({
                url: pagina + "fetch_single.php",
                method: "POST",
                data: {menudid: menudid},
                dataType: "json",
                success: function (data) {
                    $('#userModal').modal('show');
                    $('#menuid').val(data.menuid);
                    $('#usuario').val(data.usuario);
                    $('#acceso').val(data.acceso);
                    $('.modal-title').text("Modificar Permiso");
                    $('#menudid').val(menudid);
                    $('#action').val("Actualizar");
                    $('#operation').val("Edit");
                    $('#menuid option:not(:selected)').prop('disabled', true);
                }
            })
        });

        $(document).on('click', '.delete', function () {
            var menudid = $(this).attr("id");
            if (confirm("Cambiar permiso?")) {
                $.ajax({
                    url: pagina + "delete.php",
                    method: "POST",
                    data: {id: menudid},
                    success: function (data) {
                        alert(data);
                        dataTable.ajax.reload();
                    }
                });
            }
            else {
                return false;
            }
        });
    });
</script>

