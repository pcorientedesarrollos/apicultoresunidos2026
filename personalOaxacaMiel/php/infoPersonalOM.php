<?php

include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$con = $pdo->conectar();
$con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$error = "";

if (!isset($_GET['idPersonalOM'])) {
    echo $error = "No se recibieron parámetro";
    die;
}

$idPersonalOM = $_GET['idPersonalOM'];

$query = "SELECT om.idPersonalOM, om.idArea, om.clave, om.nombres, om.idPuesto, a.area, p.puesto, om.apellido_materno, om.apellido_paterno, om.correo
    FROM personaloaxaca om 
    LEFT JOIN areas a ON a.idArea = om.idArea
    LEFT JOIN puestos p ON p.idPuesto = om.idPuesto
    WHERE om.idPersonalOM = :idPersonalOM";
$datos = $con->prepare($query);
$datos->bindParam(':idPersonalOM', $idPersonalOM);
$datos->execute();
while ($resp = $datos->fetch()) {
    $personal = new stdClass();
    $personal->idPersonalOM = $resp["idPersonalOM"];
    $personal->idArea = $resp["idArea"];
    $personal->clave = $resp["clave"];
    $personal->nombres = $resp["nombres"];
    $personal->idPuesto = $resp["idPuesto"];
    $personal->area = $resp["area"];
    $personal->puesto = $resp["puesto"];
    $personal->apellido_materno = $resp["apellido_materno"];
    $personal->apellido_paterno = $resp["apellido_paterno"];
    $personal->correo = $resp["correo"];
    $personal->periodos = array();

    $sqlPeriodos = "SELECT * FROM periodos_personal WHERE idPersonal = :idPersonal ";
    $datosPeriodos = $con->prepare($sqlPeriodos);
    $datosPeriodos->bindParam(':idPersonal', $idPersonalOM);
    $datosPeriodos->execute();
    while ($rsCont = $datosPeriodos->fetch()) {
        $periodo = new stdClass();
        $periodo->idPeriodo = $rsCont["idPeriodo"];
        $periodo->fechaUno = $rsCont["fechaUno"];
        $periodo->fechaDos = $rsCont["fechaDos"];
        $periodo->idPersonal = $rsCont["idPersonal"];
        $personal->periodos[] = $periodo;
    }
}
echo $json_response = json_encode($personal);
