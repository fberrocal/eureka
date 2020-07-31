<?php
session_start();
require_once('database.class.php');
$conn = new Database();
// Create an instance of the class:
//$mpdf = new \Mpdf\Mpdf();
//$mpdf->setFooter('{PAGENO}');
$output = array();
if (isset($_POST['submit'])) {
    $usuario = $_POST['usuario'];
    $password = $_POST['password'];
    $connection = new Database();
    $statement = $connection->prepare(
        "SELECT TOP 1 * FROM USUSU WHERE USUARIO = UPPER('" . $usuario . "') 
                      AND UPPER('" . $password . "')= dbo.FNK_ENCRIPTA(CLAVE,0)");
    $statement->execute();
    $result = $statement->fetchAll();
    //echo $statement->rowCount();
    $usu = '';
    $nombre = '';
    if ($statement->rowCount() > 0) {

        foreach ($result as $row) {
            $usu = $row["USUARIO"];
            $nombre = $row["NOMBRE"];
        }
        $_SESSION['login'] = 1;
        $_SESSION['usuario'] = $usu;
        $_SESSION['nombre'] = $nombre;
        header("location:index.php");
        exit;
    } else {
        session_destroy();
        header("location: login.php?r=2");
        exit;
    }
} else {
    session_destroy();
    header("location: login.php?r=1");
    exit;
}