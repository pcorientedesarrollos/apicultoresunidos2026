<?php

header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');

function response($accessKey)
{
    if (!isset($accessKey) || $accessKey != '8ca4c7322b882552296176913fde7efa') {
        throw new Exception('Token de autorización inválido');
    }
    include_once '../../DAOConeccion/conexionWebServices.php';
    date_default_timezone_set('America/Merida');
    $pdo = new conePDO();
    // $dbh = $pdo->conectar('apicultores2019');
    $dbh = $pdo->conectar('erpasas2020');

    $consultaEquipos = "SELECT eq.idEquipo, eq.nombre, CONCAT('AF-MID-', eq.idEquipo) as codigo, ar.area, imp.idImpresion
                    FROM equipos eq
                    INNER JOIN areas ar
                    ON eq.idArea = ar.idArea
                    INNER JOIN impresion imp
                    ON eq.idEquipo = imp.idAlmacen
                    AND imp.estado = 2";

    $query = $dbh->prepare($consultaEquipos);
    $query->execute();
    if ($query == false) {
        throw new Exception($dbh->errorInfo());
    }
    // Necesitamos pasarlo a un arreglo para que no de error en la iteracion del sistema de python
    $resultado = array();
    $listaEquipos = $query->fetchAll(PDO::FETCH_ASSOC);
    if ($listaEquipos) {
        foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $equipo) {
            array_push($resultado, $equipo);
        }
    }
    echo json_encode($resultado);
}
try {
    if (isset($_POST['accessKey'])) {
        response($_POST['accessKey']);
    } else {
        throw new Exception('No se ha recibido autorización');
    }
} catch (Exception $e) {
    echo json_encode(['error' => true, 'message' => $e->getMessage()]);
}