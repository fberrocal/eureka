<?php
require_once('../database.class.php');
include('function.php');
$connection=new Database();
if(isset($_POST["operation"]))
{
    if($_POST["operation"] == "Add")
	{

		$statement = $connection->prepare("
			INSERT INTO MENUD (MENUID,USUARIO,ACCESO) 
			VALUES (:MENUID,:USUARIO,:ACCESO)
		");
		$result = $statement->execute(
			array(
                ':MENUID'	=>	$_POST["MENUID"],
			    ':USUARIO'	=>	$_POST["USUARIO"],
				':ACCESO'	=>	$_POST["ACCESO"]
			)
		);
        if (!$statement) {
            echo "\nPDO::errorInfo():\n";
            print_r($dbh->errorInfo());
        }
		if(!empty($result))
		{
			echo 'Guardado';
		}
	}
	if($_POST["operation"] == "Edit")
	{

		$statement = $connection->prepare(
			"UPDATE MENUD  
			SET MENUID = :MENUID, USUARIO = :USUARIO, ACCESO = :ACCESO WHERE MENUDID=" . $_POST["menudid"]);
		$result = $statement->execute(
			array(
				':MENUID'	=>	$_POST["menuid"],
				':USUARIO'	=>	$_POST["usuario"],
                ':ACCESO'	=>	$_POST["acceso"]
			)
		);
		if(!empty($result))
		{
			echo 'Actualizado';
		}
	}
}

?>