<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="stylesheet" href="lib/bootstrap/css/bootstrap.min.css">
    <script src="lib/bootstrap/js/bootstrap.min.js"></script>
    <script src="lib/jquery-3.2.1.min.js"></script>
    <script src="lib/popper.js"></script>


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
<nav class="navbar navbar-expand-md navbar-dark bg-dark mb-4">
    <a class="navbar-brand" href="#">E.S.E. VIDASINU</a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarCollapse" aria-controls="navbarCollapse" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarCollapse">
        <ul class="navbar-nav mr-auto">
            <li class="nav-item active">
                <a class="nav-link" href="#">Home <span class="sr-only">(current)</span></a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#">Asistencial</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#">Administrativos</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#">Calidad</a>
            </li>
            <!--<li class="nav-item">
                <a class="nav-link disabled" href="#">Disabled</a>
            </li>-->
        </ul>
        <form class="form-inline mt-2 mt-md-0">
            <input class="form-control mr-sm-2" type="text" placeholder="Search" aria-label="Search">
            <button class="btn btn-outline-success my-2 my-sm-0" type="submit">Search</button>
        </form>
    </div>
</nav>
<div class="container-fluid">
<main role="main" class="container-fluid">

    <div class="starter-template">
        <h3>Reporte de Morbilidad</h3>
    </div>
    <form name="form1" action="test.php" target="myIframe" method="POST">
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
            <div class="col">
                <div class="form-group">
                    <label for="edadini">Edad Inicial:</label>
                    <input type="number" class="form-control" name="edadini" id="edadini" min="0" value="0" required>
                </div>
            </div>
            <div class="col">
                <div class="form-group">
                    <label for="edadfin">Edad Final:</label>
                    <input type="number" class="form-control" name="edadfin" id="edadfin" min="1" max="120" value="120" required>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col col-3">
                <label for="sexo">Sexo:</label>
                <select class="form-control" name="sexo" id="sexo" required>
                    <option value=""></option>
                    <option value="Masculino">Masculino</option>
                    <option value="Femenino">Femenino</option>
                </select>
            </div>
        </div>
        <div class="row">
            <div class="col">
                <br><button type="submit" class="btn btn-primary">Consultar</button>
            </div>
        </div>

    </form>
    <hr>
    <!--
    <form name="form1" action="test.php" target="myIframe" method="POST">
        <label for="id">Documento:</label>
        <input type="text" name="id" id="id" placeholder="Documento Afiliado">
        <input type="submit" value="Consultar">
    </form>-->
    <div class="responsive-video">
        <iframe class="embed-responsive-item" src="" name="myIframe"></iframe>
    </div>


</main>

</div>


</body>
</html>