<?php

header('Access-Control-Allow-Origin: *');
include_once '../../DAOConeccion/conexionWebServices.php';
$pdo = new conePDO();
// $con = $pdo->conectar('apicultores2019');
// $con = $pdo->conectar('apicultores2020');
// $con = $pdo->conectar('mielorganica2020');
// $con = $pdo->conectar('apicultores2021');
// $con = $pdo->conectar('apicultores2022');
// $con = $pdo->conectar('apicultores2023');
$con = $pdo->conectar('apicultores2024');

$con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$resultado = array();

$sqlImpresion = "SELECT idImpresion FROM impresion WHERE estado = 2 ORDER BY idAlmacen";
$dato = $con->prepare($sqlImpresion);
$dato->execute();
if ($dato == false) {
    $resultado = [];
} else {
    foreach ($dato->fetchAll(PDO::FETCH_ASSOC) as $impresion) {
        $sql = "SELECT eq.idEquipo, eq.nombre, CONCAT('AF-MID-', eq.idEquipo) as codigo, ar.area, imp.idImpresion
        FROM equipos eq
        INNER JOIN areas ar
        ON eq.idArea = ar.idArea
        INNER JOIN impresion imp
        ON eq.idEquipo = imp.idAlmacen AND imp.estado = 2
        WHERE imp.idImpresion = :idImpresion";
        $datos = $con->prepare($sql);
        $datos->bindParam(':idImpresion', $impresion['idImpresion']);
        $datos->execute();

        array_push($resultado, $datos->fetch(PDO::FETCH_ASSOC));
    }
}
echo json_encode($resultado);
