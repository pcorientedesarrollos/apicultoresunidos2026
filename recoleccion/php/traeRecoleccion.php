<?php
include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$con = $pdo->conectar();

try {

    if (!isset($_GET['idRecoleccion'])) {
        throw new Exception('No se recibió parámetro.');
    } else {
        $idRecoleccion = $_GET['idRecoleccion'];
    }

    $sqlRecoleccion = "SELECT idRecoleccion, fecha, observaciones, totalTambores, totalImporteCompra, totalPrecioPromedio FROM recoleccionencabezado
    WHERE idRecoleccion = :idRecoleccion";

    $queryRecoleccion = $con->prepare($sqlRecoleccion);
    $queryRecoleccion->bindParam(':idRecoleccion', $idRecoleccion);
    $queryRecoleccion->execute();
    if (!$queryRecoleccion) {
        throw new Exception($con->errorInfo());
    }

    $resultadoRecoleccion = $queryRecoleccion->fetch(PDO::FETCH_ASSOC);

    // Obtener personal y transporte de la recoleccion y operadores

    $sqlPersonalRecoleccion = "SELECT rp.nombre
    FROM recoleccionpersonal rp
    WHERE idRecoleccion = :idRecoleccion";

    $sqlTransporteRecoleccion = "SELECT rt.idTransporte
    FROM recolecciontransporte rt
    WHERE idRecoleccion = :idRecoleccion";

    $sqlOperadoresRecoleccion = "SELECT rp.idPersonalOM
    FROM recoleccionoperador rp
    WHERE idRecoleccion = :idRecoleccion";

    // Personal:
    $queryPersonalRecoleccion = $con->prepare($sqlPersonalRecoleccion);
    $queryPersonalRecoleccion->bindParam(':idRecoleccion', $idRecoleccion);
    $queryPersonalRecoleccion->execute();
    if (!$queryPersonalRecoleccion) {
        throw new Exception($con->errorInfo());
    }
    $personalRecoleccion = $queryPersonalRecoleccion->fetchAll(PDO::FETCH_ASSOC);
    $resultadoRecoleccion['personal']= $personalRecoleccion;
    // Transportes:
    $queryTransporteRecoleccion = $con->prepare($sqlTransporteRecoleccion);
    $queryTransporteRecoleccion->bindParam(':idRecoleccion', $idRecoleccion);
    $queryTransporteRecoleccion->execute();
    if (!$queryTransporteRecoleccion) {
        throw new Exception($con->errorInfo());
    }
    $transportesRecoleccion = $queryTransporteRecoleccion->fetchAll(PDO::FETCH_ASSOC);

    // Operadores
    $queryOperadoresRecoleccion = $con->prepare($sqlOperadoresRecoleccion);
    $queryOperadoresRecoleccion->bindParam(':idRecoleccion', $idRecoleccion);
    $queryOperadoresRecoleccion->execute();
    if (!$queryOperadoresRecoleccion) {
        throw new Exception($con->errorInfo());
    }
    $operadoresRecoleccion = $queryOperadoresRecoleccion->fetchAll(PDO::FETCH_ASSOC);

    // Crear dos propiedades más, una para transportes y una para personal que contenga en
    // la lista de los ids, para el select chosen multiple

    // $solo_ids_personal = array_map(function ($personalObj) {
    //     return $personalObj['idPersonalOM'];
    // }, $personalRecoleccion);
    // $resultadoRecoleccion['personal'] = $solo_ids_personal;

    $solo_ids_transporte = array_map(function ($transporteObj) {
        return $transporteObj['idTransporte'];
    }, $transportesRecoleccion);
    $resultadoRecoleccion['transporte'] = $solo_ids_transporte;

    $solo_ids_operadores = array_map(function ($operadorObj) {
        return $operadorObj['idPersonalOM'];
    }, $operadoresRecoleccion);
    $resultadoRecoleccion['operadores'] = $solo_ids_operadores;

    // YA NO SE MUESTRA EN EL FORM
    // /** CONVERTIR LAS PROPIEDADES QUE SON NUMEROS, EN EL HTML NECESITAN SER NUMEROS PARA EL FORM DE EDICIÓN*/
    // $resultadoRecoleccion['anterior'] = floatval($resultadoRecoleccion['anterior']);
    // $resultadoRecoleccion['nuevo'] = floatval($resultadoRecoleccion['nuevo']);
    // $resultadoRecoleccion['recoleccion'] = floatval($resultadoRecoleccion['recoleccion']);
    // $resultadoRecoleccion['total'] = floatval($resultadoRecoleccion['total']);
    // $resultadoRecoleccion['precio'] = floatval($resultadoRecoleccion['precio']);
    // $resultadoRecoleccion['humedad'] = floatval($resultadoRecoleccion['recoleccion']);

    // Obtener las localidades visitadas en la recoleccion

    $sqlLocalidadesRecoleccion = "SELECT r.idRecoleccionDetalle, r.anterior,
    r.nuevo, r.recoleccion, r.total, r.precio, r.humedad, r.importe, l.localidad, l.idlocalidad as idLocalidad
    FROM recoleccion r
    LEFT JOIN localidades l ON r.idLocalidad = l.idlocalidad
    WHERE idRecoleccion = :idRecoleccion
    ORDER BY idRecoleccionDetalle";
    $queryLocalidadesRecoleccion = $con->prepare($sqlLocalidadesRecoleccion);
    $queryLocalidadesRecoleccion->bindParam(':idRecoleccion', $idRecoleccion);
    $queryLocalidadesRecoleccion->execute();
    if (!$queryLocalidadesRecoleccion) {
        throw new Exception($con->errorInfo());
    }
    $resultadoRecoleccion['localidades'] = $queryLocalidadesRecoleccion->fetchAll(PDO::FETCH_ASSOC);

    // contar cuantos tambores son
    $totalTambores = 0;
    foreach ($resultadoRecoleccion['localidades'] as $localidad) {
        $totalTambores += $localidad['recoleccion'];
    }

    echo json_encode(['error' => false, 'resultado' => $resultadoRecoleccion]);
} catch (Exception $e) {
    echo json_encode(['error' => true, 'message' => $e->getMessage()]);
}
