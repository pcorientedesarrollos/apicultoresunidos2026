<?php

include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$con = $pdo->conectar();

$idArea = $_GET['idArea'];
$str = '&#10004;';

if (isset($_GET['sinMantto'])) {
    $sql = "SELECT area FROM areas WHERE idArea = :idArea";
    $dato = $con->prepare($sql);
    $dato->bindParam(':idArea', $idArea);
    $dato->execute();

    while ($row = $dato->fetch()) {
        $equipo = new stdClass();
        $equipo->area = $row["area"];
        $equipo->equipos = array();

        $query = "SELECT e.idEquipo, e.nombre, e.caracteristicas, e.marca, e.modelo, e.noSerie, e.costo,
          a.area, (CONCAT(s.subarea,' - ',s.nombre)) AS zona
          FROM equipos e
          INNER JOIN areas a ON a.idArea = e.idArea
          LEFT JOIN subareas s ON s.idSubarea = e.idSubarea
          WHERE a.idArea = :idArea AND e.mantto = 1
          ORDER BY e.idEquipo ASC";
        $datos = $con->prepare($query);
        $datos->bindParam(':idArea', $idArea);
        $datos->execute();

        while ($row = $datos->fetch()) {
            $menuEquipos = new stdClass();
            $menuEquipos->idEquipo = $row["idEquipo"];
            $menuEquipos->marca = $row["marca"];
            $menuEquipos->nombre = $row["nombre"];
            $menuEquipos->costo = $row["costo"];
            $menuEquipos->caracteristicas = $row["caracteristicas"];
            $menuEquipos->modelo = $row["modelo"];
            $menuEquipos->serie = $row["noSerie"];
            $menuEquipos->zona = $row["zona"];
//        $menuEquipos->codigo = $row["codigo"];
//            if ($menuEquipos->programacion == "1") {
//                $menuEquipos->programacion = $str;
//                $menuEquipos->ruta = "verProgramacion";
//            } else {
//                $menuEquipos->programacion = " ";
//                $menuEquipos->ruta = "nvaProgramacion";
//            }


            $equipo->equipos[] = $menuEquipos;
        }
    }
} else {
    $sql = "SELECT area FROM areas WHERE idArea = :idArea";
    $dato = $con->prepare($sql);
    $dato->bindParam(':idArea', $idArea);
    $dato->execute();

    while ($row = $dato->fetch()) {
        $equipo = new stdClass();
        $equipo->area = $row["area"];
        $equipo->equipos = array();

        $query = "SELECT e.idEquipo, e.nombre, e.caracteristicas, e.marca, e.modelo, e.noSerie, e.costo,
          a.area, e.programacion, e.mantto,
          (CONCAT(s.subarea,' - ',s.nombre)) AS zona
          FROM equipos e
          INNER JOIN areas a ON a.idArea = e.idArea
          LEFT JOIN subareas s ON s.idSubarea = e.idSubarea
          WHERE a.idArea = :idArea AND e.mantto = 0
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
            $menuEquipos->marca = $row["marca"];
            $menuEquipos->modelo = $row["modelo"];
            $menuEquipos->costo = $row["costo"];
            $menuEquipos->serie = $row["noSerie"];
            $menuEquipos->zona = $row["zona"];
            $menuEquipos->caracteristicas = $row["caracteristicas"];
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
}

if (isset($_GET['todos'])) {
    $sql = "SELECT area FROM areas WHERE idArea = :idArea";
    $dato = $con->prepare($sql);
    $dato->bindParam(':idArea', $idArea);
    $dato->execute();

    while ($row = $dato->fetch()) {
        $equipo = new stdClass();
        $equipo->area = $row["area"];
        $equipo->equipos = array();

        $query = "SELECT e.idEquipo, e.nombre, e.caracteristicas,
          a.area, e.programacion, e.mantto, e.marca, e.modelo, e.noSerie, e.costo,
          (CONCAT(s.subarea,' - ',s.nombre)) AS zona
          FROM equipos e
          INNER JOIN areas a ON a.idArea = e.idArea
          LEFT JOIN subareas s ON s.idSubarea = e.idSubarea
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
            $menuEquipos->costo = $row["costo"];
            $menuEquipos->marca = $row["marca"];
            $menuEquipos->modelo = $row["modelo"];
            $menuEquipos->serie = $row["noSerie"];
            $menuEquipos->zona = $row["zona"];
            $menuEquipos->caracteristicas = $row["caracteristicas"];
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
}




//$arrayR = array();


echo json_encode($equipo);
?>