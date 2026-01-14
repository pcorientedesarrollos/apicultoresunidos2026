<?php
include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$con = $pdo->conectar();


if (isset($_GET['activos'])) {
    $query = "             
    SELECT om.idPersonalOM, om.idArea, om.clave, om.nombre, om.idPuesto, a.area, p.puesto, om.estado
    FROM personaloaxaca om 
    LEFT JOIN areas a ON a.idArea = om.idArea
    LEFT JOIN puestos p ON p.idPuesto = om.idPuesto
    WHERE om.idPersonalOM != '26' AND om.estado = '0'
    ORDER BY a.area ASC
    ";
    $datos = $con->prepare($query);
    $datos->execute();

    $personalOM = array();
    while ($row = $datos->fetch()) {
        $persoOM = new stdClass();
        $persoOM->idPersonalOM = $row["idPersonalOM"];
        $persoOM->idArea = $row["idArea"];
        $persoOM->clave = $row["clave"];
        $persoOM->nombre = $row["nombre"];
        $persoOM->idPuesto = $row["idPuesto"];
        $persoOM->estado = $row["estado"];
        $persoOM->area = $row["area"];
        $persoOM->puesto = $row["puesto"];
        $personalOM[] = $persoOM;
    }

    echo json_encode($personalOM);
} else if (isset($_GET['inactivos'])) {
    $query = "             
    SELECT om.idPersonalOM, om.idArea, om.clave, om.nombre, om.idPuesto, a.area, p.puesto, om.estado
    FROM personaloaxaca om 
    LEFT JOIN areas a ON a.idArea = om.idArea
    LEFT JOIN puestos p ON p.idPuesto = om.idPuesto
    WHERE om.idPersonalOM != '26' AND om.estado = '1'
    ORDER BY a.area ASC
    ";
    $datos = $con->prepare($query);
    $datos->execute();

    $personalOM = array();
    while ($row = $datos->fetch()) {
        $persoOM = new stdClass();
        $persoOM->idPersonalOM = $row["idPersonalOM"];
        $persoOM->idArea = $row["idArea"];
        $persoOM->clave = $row["clave"];
        $persoOM->nombre = $row["nombre"];
        $persoOM->idPuesto = $row["idPuesto"];
        $persoOM->estado = $row["estado"];
        $persoOM->area = $row["area"];
        $persoOM->puesto = $row["puesto"];
        $personalOM[] = $persoOM;
    }

    echo json_encode($personalOM);
} else {
    $query = "             
    SELECT om.idPersonalOM, om.idArea, om.clave, om.nombre, om.idPuesto, a.area, p.puesto, om.estado
    FROM personaloaxaca om 
    LEFT JOIN areas a ON a.idArea = om.idArea
    LEFT JOIN puestos p ON p.idPuesto = om.idPuesto
    WHERE om.idPersonalOM != '26'
    ORDER BY a.area ASC
    ";
    $datos = $con->prepare($query);
    $datos->execute();

    $personalOM = array();
    while ($row = $datos->fetch()) {
        $persoOM = new stdClass();
        $persoOM->idPersonalOM = $row["idPersonalOM"];
        $persoOM->idArea = $row["idArea"];
        $persoOM->clave = $row["clave"];
        $persoOM->nombre = $row["nombre"];
        $persoOM->idPuesto = $row["idPuesto"];
        $persoOM->estado = $row["estado"];
        $persoOM->area = $row["area"];
        $persoOM->puesto = $row["puesto"];
        $personalOM[] = $persoOM;
    }

    echo json_encode($personalOM);
}
?>