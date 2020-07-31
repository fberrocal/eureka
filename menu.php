<?php
require_once('database.class.php');
function crear_menu()
{
    $conn = new Database();
    $menu = ""; // Vaciamos la variable menú
    //$consulta = " SELECT MENUID,SECUENCIA,NOMBRE,LINK , IDPADRE FROM MENU WHERE ACTIVO='1' ORDER BY SECUENCIA";
    $consulta = " SELECT m.MENUID AS MENUID,m.SECUENCIA AS SECUENCIA,m.NOMBRE as NOMBRE,
m.LINK AS LINK , m.IDPADRE AS IDPADRE 
FROM MENU m
INNER JOIN MENUD n on m.MENUID=n.MENUID
WHERE m.ACTIVO='1' 
AND n.USUARIO='" . $_SESSION['usuario'] . "' 
AND n.ACCESO=1 AND m.MENUID>0 
ORDER BY m.SECUENCIA";
    $sth = $conn->prepare($consulta);
    $sth->execute();
    $resultado = $sth->fetchall(PDO::FETCH_ASSOC);
    $aux = $resultado;
    foreach ($resultado as $row) {
        if ($row['IDPADRE'] == 0) {
            $menu .= '<li class="nav-item dropdown">';
            $menu .= ' <a class="nav-link dropdown-toggle" href="' . $row['LINK'] . '" id="navbarDropdown" role="button" data-toggle="dropdown" 
 aria-haspopup="true" aria-expanded="false"> ' . $row['NOMBRE'] . '</a>';
            $menu .= '<div class="dropdown-menu" aria-labelledby="navbarDropdown">';
            foreach ($aux as $r) {
                if ($r['IDPADRE'] != 0 && $r['IDPADRE'] == $row['MENUID']) {
                    $menu .= ' <a class="dropdown-item" href="' . $r['LINK'] . '">' . $r['NOMBRE'] . '</a>';
                }
            }
            $menu .= '</div></li>';
        }
    }
    return $menu;
}