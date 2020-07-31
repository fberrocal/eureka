<?php
include('../database.class.php');
include('function.php');
$connection=new Database();
if(isset($_POST["menudid"]))
{
    $output = array();
	$statement = $connection->prepare(
		"SELECT TOP 1 * FROM MENUD WHERE MENUDID = '".$_POST["menudid"]."' "
	);
	$statement->execute();
	$result = $statement->fetchAll();
	foreach($result as $row)
	{
		//echo $row["MENUID"];
	    $output["menuid"] = $row["MENUID"];
        $output["usuario"] = $row["USUARIO"];
        $output["acceso"] = $row["ACCESO"];
	}
	echo json_encode($output);
}
?>