<?php
session_start();
session_destroy();
$message = '';
if (isset($_GET['r'])) {
    $r = $_GET['r'];
    switch ($r) {
        case 1:
            {
                $message = 'Debe ingresar usuario y contrase&ntilde;a';
                break;
            }
        case 2:
            {
                $message = 'Usuario o contraseña incorrectos. Intente de nuevo';
                break;
            }
        case 3:
            {
                $message = 'Debe iniciar sesi&oacute;n';
                break;
            }
    }
}
?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="icon" type="image/png" sizes="32x32" href="images/favicon-32x32.png">
    <script src="lib/jquery-3.2.1.min.js"></script>
    <script src="lib/popper.js"></script>
    <link rel="stylesheet" href="lib/bootstrap/css/bootstrap.min.css">
    <script src="https://cdn.datatables.net/1.10.18/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.18/js/dataTables.bootstrap4.min.js"></script>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.18/css/dataTables.bootstrap4.min.css"/>
    <script src="lib/bootstrap/js/bootstrap.min.js"></script>
    <title>E.S.E. VIDASINU - REPORTES</title>

    <!-- Custom styles for this template -->
    <link href="signin.css" rel="stylesheet">


<body class="text-center">
<form class="form-signin" action="loginauth.php" method="POST">
    <img class="mb-4" src="images/logo.png" alt="">
    <?php
    if ($message != '') {
        echo '<div class="alert alert-danger" role="alert">' . $message . '</div>';
    }
    ?>
    <h1 class="h3 mb-3 font-weight-normal">Iniciar Sesión</h1>
    <label for="usuario" class="sr-only">Usuario</label>
    <input type="text" name="usuario" id="usuario" class="form-control" placeholder="Usuario" required autofocus>
    <label for="inputPassword" class="sr-only">Password</label>
    <input type="password" name="password" maxlength="8" id="inputPassword" class="form-control" placeholder="Password" required>
    <div class="checkbox mb-3">
        <label>
            <input type="checkbox" value="remember-me"> Recordarme
        </label>
    </div>
    <button class="btn btn-lg btn-primary btn-block" name="submit" type="submit">Login</button>

</form>
</body>
</html>