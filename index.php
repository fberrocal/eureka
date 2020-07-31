<?php
session_start();
if (!isset($_SESSION['login'])) {
    header("location: login.php?r=3");
    exit;
}
include('menu.php');
$r = '';
if (isset($_GET['r'])) {
    $r = $_GET['r'];
}
?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <link rel="icon" type="image/png" sizes="32x32" href="images/favicon-32x32.png">
    <script src="lib/jquery-3.2.1.min.js"></script>
    <script src="lib/popper.js"></script>
    <link rel="stylesheet" href="lib/bootstrap/css/bootstrap.min.css">
    <script src="https://cdn.datatables.net/1.10.18/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.18/js/dataTables.bootstrap4.min.js"></script>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.18/css/dataTables.bootstrap4.min.css"/>
    <script src="lib/bootstrap/js/bootstrap.min.js"></script>
    <title>E.S.E. VIDASINU - REPORTES</title>

    <style>
        /*iframe {
            border-radius: 2px;
            border-top: thin 1px;
            border-bottom: none;
            border-left: none;
            border-right: none;
            width: 100%;
            /*height: 90%;*/
        /*position: absolute;*/
        .responsive-video {
            position: relative;
            padding-bottom: 40%;
            padding-top: 60px;
            overflow: hidden;
        }

        .responsive-video iframe,
        .responsive-video object,
        .responsive-video embed {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 90%;
        }

        }
    </style>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4">
    <a class="navbar-brand" href="#"><img src="images/logo.png" width="70"></a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent"
            aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarCollapse">
        <ul class="navbar-nav mr-auto">
            <li class="nav-item active">
                <a class="nav-link" href="?r=home">Home <span class="sr-only">(current)</span></a>
            </li>
            <?php echo crear_menu(); ?>
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown"
                   aria-haspopup="true" aria-expanded="false">
                    Otros
                </a>
                <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                    <a class="dropdown-item" href="?r=eva">Eventos adversos</a>
                    <a class="dropdown-item" href="?r=medrural">Medicamentos Ordenados Sede Rural</a>
                </div>
            </li>
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown"
                   aria-haspopup="true" aria-expanded="false">
                    Usuario(<?php echo $_SESSION['nombre']; ?>)
                </a>
                <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                    <a class="dropdown-item" href="?r=sec/index">Configuración</a>
                    <a class="dropdown-item" href="?r=logout">Salir</a>
                </div>
            </li>
        </ul><!--
        <ul class="navbar-nav mr-auto">
            <li class="nav-item active">
                <a class="nav-link" href="#">Home <span class="sr-only">(current)</span></a>
            </li>

            <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        Asistencial
                    </a>
                <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                    <a class="dropdown-item" href="?r=morbilidad">Morbilidad</a>

                    <div class="dropdown-divider"></div>
                    <a class="dropdown-item" href="?r=htaing">Ingreso HTA</a>
                    <a class="dropdown-item" href="?r=htacon">Control HTA</a>
                    <div class="dropdown-divider"></div>
                    <a class="dropdown-item" href="?r=htadbting">Ingreso HTA+DBT</a>
                    <a class="dropdown-item" href="?r=htadbtcon">Control HTA+DBT</a>
                    <div class="dropdown-divider"></div>
                    <a class="dropdown-item" href="?r=dbtw">Diabetes</a>
                    <div class="dropdown-divider"></div>
                    <a class="dropdown-item" href="?r=remision">Remisiones</a>
                    <div class="dropdown-divider"></div>
                    <a class="dropdown-item" href="?r=citolog_etario">Citología por Grupo Etario</a>
                    <div class="dropdown-divider"></div>
                    <a class="dropdown-item" href="?r=vacunrc">Vacunación Recién Nacido</a>
                    <a class="dropdown-item" href="?r=aieprnce">HC Recién Nacido 72 horas</a>
                    <a class="dropdown-item" href="?r=hcrecien">Recién Nacido en Obstetricia</a>
                    <a class="dropdown-item" href="?r=segpostp">Seguimiento Postparto</a>
                </div>
            </li>
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    PyP
                </a>
                <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                    <a class="dropdown-item" href="?r=agudeza">Agudeza Visual</a>
                    <a class="dropdown-item" href="?r=crecimiento">Crecimiento y Desarrollo</a>
                    <a class="dropdown-item" href="?r=planifica">Planificación Familiar</a>
                    <div class="dropdown-divider"></div>
                    <a class="dropdown-item" href="?r=materno">Prenatal</a>
                    <a class="dropdown-item" href="?r=prenatal_evo">Prenatal Evolución</a>
                    <div class="dropdown-divider"></div>
                    <a class="dropdown-item" href="?r=dtamsano">Adulto Mayor</a>
                    <a class="dropdown-item" href="?r=dtajoven">DTA Joven</a>
                    <a class="dropdown-item" href="?r=camama">CA Mama</a>
                    <a class="dropdown-item" href="?r=ateplafa">Atención por Enf. Planificación</a>
                    <a class="dropdown-item" href="?r=ateplani">Atención por Enf. Suministros Antic.</a>
                    <a class="dropdown-item" href="?r=ecografia">Ecografía Obstétrica</a>
                    <div class="dropdown-divider"></div>
                    <a class="dropdown-item" href="?r=aiepi02ce">AIEPI 0 a 2 meses (Consulta Externa)</a>
                    <a class="dropdown-item" href="?r=aiepi25ce">AIEPI 2 meses a 5 años (Consulta Externa)</a>
                    <div class="dropdown-divider"></div>
                    <a class="dropdown-item" href="?r=forllama">Formato Llamadas Inasistentes</a>
                    <a class="dropdown-item" href="?r=vacgestantes">Vacunación Gestantes</a>
                </div>
            </li>
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    Odontología
                </a>
                <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                    <a class="dropdown-item" href="?r=ox_general">Servicios Odontológicos Generales</a>
                    <a class="dropdown-item" href="?r=ox_pyp">Servicios PyP</a>
                </div>
            </li>
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    Epidemiología
                </a>
                <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                    <a class="dropdown-item" href="?r=eda_ira">EDA - IRA</a>
                    <a class="dropdown-item" href="?r=totalcon">Total de Consultas</a>
                </div>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#">Administrativos</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#">Calidad</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="?r=rips">RIPS</a>
            </li>
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    Usuario(<?php // echo $_SESSION['nombre'] ?>)
                </a>
                <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                    <a class="dropdown-item" href="?r=sec/index">Configuración</a>
                    <a class="dropdown-item" href="?r=logout">Salir</a>
                </div>
            </li>
        </ul>-->
    </div>
</nav>
<!--
<nav class="navbar navbar-expand-lg navbar-light bg-light">
    <a class="navbar-brand" href="#">Navbar</a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="navbarSupportedContent">
        <ul class="navbar-nav mr-auto">
            <li class="nav-item active">
                <a class="nav-link" href="#">Home <span class="sr-only">(current)</span></a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#">Link</a>
            </li>
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    Dropdown
                </a>
                <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                    <a class="dropdown-item" href="#">Action</a>
                    <a class="dropdown-item" href="#">Another action</a>
                    <div class="dropdown-divider"></div>
                    <a class="dropdown-item" href="#">Something else here</a>
                </div>
            </li>
            <li class="nav-item">
                <a class="nav-link disabled" href="#">Disabled</a>
            </li>
        </ul>
        <form class="form-inline my-2 my-lg-0">
            <input class="form-control mr-sm-2" type="search" placeholder="Search" aria-label="Search">
            <button class="btn btn-outline-success my-2 my-sm-0" type="submit">Search</button>
        </form>
    </div>
</nav>-->
<div class="container-fluid">
    <main role="main" class="container-fluid">
        <?php
        $file = $r . '.php';
        if ($r != '') {
            if ($r == 'logout') {
                echo "<script>location.href='logout.php'</script>";
                //header("location: logout.php");
                exit;
            }
            if (file_exists($file)) {
                try {
                    include($file);
                } catch (Exception $e) {
                    echo '<div class="alert alert-danger" role="alert">Reporte no encontrado. Intente de nuevo</div>';
                }
            } else {
                echo '<div class="alert alert-danger" role="alert">Reporte no encontrado. Intente de nuevo</div>';
            }
            /*$success = include($r . 'php');
            if (!$success) {
                echo '<div class="alert alert-danger" role="alert">Reporte no encontrado. Intente de nuevo</div>';
            }*/
        }
        ?>
    </main>
</div>

</body>
</html>