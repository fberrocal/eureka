<?php
require_once('../database.class.php');
include('function.php');
$connection = new Database();
$requestData = $_REQUEST;
$columns = array(
// datatable column index  => database column name
    0 => 'MENUID',
    1 => 'MENU',
    2 => 'PADRE',
    3 => 'USUARIO',
    4 => 'ACCESO'
);
$query = '';
$output = array();
$query .= "SELECT * FROM (SELECT ROW_NUMBER() OVER(ORDER BY SECUENCIA) AS N,b.MENUDID AS MENUDID,a.SECUENCIA,
a.NOMBRE AS MENU, dbo.Fnc_MenuPadre(a.IDPADRE) AS PADRE, b.USUARIO AS USUARIO, b.ACCESO AS ACCESO, a.MENUID 
FROM MENU a INNER JOIN MENUD b ON b.MENUID=a.MENUID) z WHERE 1=1 AND MENUID>0 ";
if (isset($_POST["search"]["value"])) {   //name
    $query = "SELECT * FROM (SELECT ROW_NUMBER() OVER(ORDER BY SECUENCIA) AS N,b.MENUDID AS MENUDID,a.SECUENCIA,
a.NOMBRE AS MENU, dbo.Fnc_MenuPadre(a.IDPADRE) AS PADRE, b.USUARIO AS USUARIO, b.ACCESO AS ACCESO, a.MENUID 
FROM MENU a INNER JOIN MENUD b ON b.MENUID=a.MENUID WHERE b.USUARIO LIKE '%".$_POST["search"]["value"]."%') z WHERE 1=1 AND MENUID>0";
    //$query .= " AND USUARIO like '%" . $_POST["search"]["value"] . "%' ";
    //echo $query;
}
//echo $query;
/*if(isset($_POST["search"]["value"]))
{
    $query="SELECT * FROM (SELECT ROW_NUMBER() OVER(ORDER BY SECUENCIA) AS N,b.MENUDID AS MENUDID,a.SECUENCIA,
a.NOMBRE AS MENU, dbo.Fnc_MenuPadre(a.IDPADRE) AS PADRE, b.USUARIO AS USUARIO, b.ACCESO AS ACCESO, a.MENUID 
FROM MENU a INNER JOIN MENUD b ON b.MENUID=a.MENUID WHERE b.USUARIO LIKE '%".$_POST["search"]["value"]."%') z WHERE 1=1 AND MENUID>0 ";


    //$query .= " AND USUARIO LIKE '%".$_POST["search"]["value"]."%' ";  //criterios de búsqueda
    //echo $query;
}*/
if (!empty($requestData['columns'][3]['search']['value'])) {   //name
    $query = "SELECT * FROM (SELECT ROW_NUMBER() OVER(ORDER BY SECUENCIA) AS N,b.MENUDID AS MENUDID,a.SECUENCIA,
a.NOMBRE AS MENU, dbo.Fnc_MenuPadre(a.IDPADRE) AS PADRE, b.USUARIO AS USUARIO, b.ACCESO AS ACCESO, a.MENUID 
FROM MENU a INNER JOIN MENUD b ON b.MENUID=a.MENUID WHERE b.USUARIO LIKE '%".$requestData['columns'][3]['search']['value']."%') z WHERE 1=1 AND MENUID>0";
    //echo $query;
}
/*
if (!empty($requestData['columns'][0]['search']['value'])) {   //name
    $query .= " AND MENUDID = '" . $requestData['columns'][0]['search']['value'] . "' ";
    //echo $query;
}
if (!empty($requestData['columns'][1]['search']['value'])) {   //name
    $query .= " AND MENU = '" . $requestData['columns'][1]['search']['value'] . "' ";
    //echo $query;
}
if (!empty($requestData['columns'][2]['search']['value'])) {   //name
    $query .= " AND PADRE = '" . $requestData['columns'][2]['search']['value'] . "' ";
    //echo $query;
}
if (!empty($requestData['columns'][3]['search']['value'])) {   //name
    $query .= " AND USUARIO = '" . $requestData['columns'][3]['search']['value'] . "' ";
    //echo $query;
}*/
if($_POST["length"] != -1)
{
    $start=1*$_POST['start'];
    $length=1*$_POST['length'];
    $query .= ' AND N BETWEEN ' . $start . ' AND ' . ($start+$length);
    //echo $query;
}

/*if(isset($_POST["search"]["value"]))
{
	$query .= ' WHERE b.USUARIO LIKE "%'.$_POST["search"]["value"].'%" ';  //criterios de búsqueda
	$query .= ' OR a.NOMBRE LIKE "%'.$_POST["search"]["value"].'%" ';
}
if(isset($_POST["order"]))
{
	$query .= 'ORDER BY '.$_POST['order']['0']['column'].' '.$_POST['order']['0']['dir'].' ';
}
else
{
	$query .= ' ORDER BY a.SECUENCIA ASC ';  //ordenar por llave primaria
}
if($_POST["length"] != -1)
{
	$query .= 'LIMIT ' . $_POST['start'] . ', ' . $_POST['length'];
}*/
$statement = $connection->prepare($query);
$statement->execute();
$result = $statement->fetchAll();

$data = array();
$filtered_rows = $statement->rowCount();
foreach ($result as $row) {
    /*$image = '';
    if($row["image"] != '')
    {
        $image = '<img src="upload/'.$row["image"].'" class="img-thumbnail" width="50" height="35" />';
    }
    else
    {
        $image = '';
    }*/
    $sub_array = array();
    //$sub_array[] = $image;
    $sub_array[] = $row["MENUDID"];
    $sub_array[] = $row["MENU"];
    $sub_array[] = $row["PADRE"];
    $sub_array[] = $row["USUARIO"];
    $sub_array[] = $row["ACCESO"];
    $sub_array[] = '<button type="button" name="update" id="' . $row["MENUDID"] . '" class="btn btn-warning btn-xs update">Editar</button>';
    $sub_array[] = '<button type="button" name="delete" id="' . $row["MENUDID"] . '" class="btn btn-danger btn-xs delete">Cambiar</button>';
    $data[] = $sub_array;
}
$output = array(
    "draw" => intval($requestData['draw']),
    "recordsTotal" => $filtered_rows,
    "recordsFiltered" => get_total_all_records(),
    "data" => $data
);
echo json_encode($output);
?>