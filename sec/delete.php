<?php

require_once('../database.class.php');
include("function.php");
$connection = new Database();
if (isset($_POST["id"])) {
    /*$statement = $connection->prepare(
        "DELETE FROM MENUD WHERE MENUDID = :id"
    );*/
    $statement = $connection->prepare(
        "DECLARE @id int=:id; 
                    UPDATE MENUD SET ACCESO=(SELECT ~(convert(bit,acceso)) FROM MENUD WHERE MENUDID=@id) WHERE MENUDID=@id "
    );
        $result = $statement->execute(
            array(
                ':id' => $_POST["id"]
            )
        );
    if (!empty($result)) {
        echo 'Permiso actualizado';
    }
}
?>