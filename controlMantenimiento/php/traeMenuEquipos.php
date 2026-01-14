<?php

include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$con = $pdo->conectar();

$idArea = $_GET['idArea'];
$str = '&#10004;';
$sql = "SELECT area FROM areas WHERE idArea = :idArea";
$dato = $con->prepare($sql);
$dato->bindParam(':idArea', $idArea);
$dato->execute();

while ($row = $dato->fetch()) {
    $equipo = new stdClass();
    $equipo->area = $row["area"];
    $equipo->equipos = array();

    $query = "SELECT e.idEquipo, e.nombre,
          a.area, e.programacion, e.mantto FROM equipos e
          INNER JOIN areas a ON a.idArea = e.idArea
          WHERE a.idArea = :idArea
          ORDER BY e.idEquipo ASC";
    $datos = $con->prepare($query);
    $datos->bindParam(':idArea', $idArea);
    $datos->execute();

    while ($row = $datos->fetch()) {
        $menuEquipos = new stdClass();
        $menuEquipos->idEquipo = $row["idEquipo"];
        $menuEquipos->programacion = $row["programacion"];
        $menuEquipos->nombre = $row["nombre"];
        $menuEquipos->mantto = $row["mantto"];
//        $menuEquipos->codigo = $row["codigo"];

        if ($menuEquipos->programacion == "1") {
            $menuEquipos->programacion = $str;
            $menuEquipos->ruta = "verProgramacion";
        } else {
            $menuEquipos->programacion = " ";
            $menuEquipos->ruta = "nvaProgramacion";
        }

               
        $equipo->equipos[] = $menuEquipos;
    }
}




//$arrayR = array();


echo json_encode($equipo);
?>