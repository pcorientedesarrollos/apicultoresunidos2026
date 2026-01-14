<?php

include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$con = $pdo->conectar();
$con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
// $resultado = new stdClass();

// $resultado = {
//    $fechaEntrada = '';
// };

$resultado = array(
    'fechaEntrada' => '',
    'procesoEnvasado' => '',
    'fechaPT' => '',
    'fechaSalida' => '',
    'fechaEmbarque' => '',
    'localidades' => array(),
);

try {

    if (!isset($_GET['miel']) || !isset($_GET['idLoteInterno'])) {
        throw new Exception('No se recibieron datos');
    } else {
        $miel = $_GET['miel'];
        $idLoteInterno = $_GET['idLoteInterno'];
    }

    switch ($miel) {
        case '1':
            $calidad_tabla = 'calidad';
            $tamboreslotes_tabla = 'tamboreslotes';
            $almacen_tabla = 'almacen';
            $almacenencabezado_tabla = 'almacenencabezado';
            $proceso = 'reportesdeprocesos';
            $envasado = 'reportesdeenvasados';
            $conformacion = 'conformacionhomogeneo_encabezado';
            $loteTerminado = 'certificadoloteterminado_encabezado';
            $carga = 'entradaysalida';
            break;
        case '2':
            $calidad_tabla = 'calidad_organico';
            $tamboreslotes_tabla = 'tamboreslotes_organico';
            $almacen_tabla = 'almacen_organico';
            $almacenencabezado_tabla = 'almacenencabezado_organico';
            $proceso = 'reportesdeprocesos_organico';
            $envasado = 'reportesdeenvasados_organico';
            $conformacion = 'conformacionhomogeneo_encabezado_organico';
            $loteTerminado = 'certificadoloteterminado_encabezado_organico';
            $carga = 'entradaysalida_organico';
            break;
    }

    $sqlFechaEntrada = "SELECT MIN(fecha) AS fechaEntrada FROM(
    SELECT alen.fecha 
    FROM $almacenencabezado_tabla alen
    LEFT JOIN $almacen_tabla alde ON alde.idAlmacenEncabezado = alen.idAlmacen
    LEFT JOIN $tamboreslotes_tabla tam ON tam.folioTambor = alde.idAlmacen AND tam.tipo = '0' AND tam.clasificacion = '0'
    WHERE tam.idLoteInterno = :idLote
    UNION
    SELECT alsen.fecha
    FROM almacensobrantesencabezado alsen 
    LEFT JOIN almacensobrantes alsde ON alsde.consecutivoEntrada = alsen.idEncabezadoSobrante
    LEFT JOIN $tamboreslotes_tabla tam ON tam.folioTambor = alsde.consecutivo AND tam.tipo = '1' AND tam.clasificacion = alsde.sobrante
    WHERE tam.idLoteInterno = :idLote AND alsen.tipoDeMiel = :miel
    ) AS entradas";
    $queryFechaEntrada = $con->prepare($sqlFechaEntrada);
    $queryFechaEntrada->bindParam(':idLote', $idLoteInterno);
    $queryFechaEntrada->bindParam(':miel', $miel);
    $queryFechaEntrada->execute();
    if ($queryFechaEntrada == FALSE) {
        throw new Exception($con->errorInfo());
    } else {
        $resultado['fechaEntrada'] = $queryFechaEntrada->fetch(PDO::FETCH_ASSOC);
    }

    $sqlFechaProcesoEnvasado = "SELECT c.fechaProceso, rp.procesada AS kilosProcesados, rp.tiempoHomogeneizacion, rp.tiempoReposo,
    c.fechaEnvasado, re.netosEnvasados AS kilosEnvasados
    FROM $calidad_tabla c
    LEFT JOIN $proceso rp ON rp.idLoteInterno = c.idLoteInterno
    LEFT JOIN $envasado re ON re.idLoteInterno = c.idLoteInterno
    WHERE c.idLoteInterno = :idLoteInterno";
    $queryFechaProcesoEnvasado = $con->prepare($sqlFechaProcesoEnvasado);
    $queryFechaProcesoEnvasado->bindParam(':idLoteInterno', $idLoteInterno);
    $queryFechaProcesoEnvasado->execute();
    if ($queryFechaProcesoEnvasado == FALSE) {
        throw new Exception($con->errorInfo());
    } else {
        $resultado['procesoEnvasado'] = $queryFechaProcesoEnvasado->fetch(PDO::FETCH_ASSOC);
    }

    $sqlFechaPT = " SELECT ce.fechaCalidad 
    FROM $loteTerminado ce
    WHERE ce.idLote = :idLote";
    $queryFechaPT = $con->prepare($sqlFechaPT);
    $queryFechaPT->bindParam(':idLote', $idLoteInterno);
    $queryFechaPT->execute();
    if ($queryFechaPT == FALSE) {
        throw new Exception($con->errorInfo());
    } else {
        $resultado['fechaPT'] = $queryFechaPT->fetch(PDO::FETCH_ASSOC);
    }

    $sqlFechaSalida = "SELECT rc.fechaImpresion AS fechaSalida
    FROM $carga rc
    WHERE rc.idLoteInterno = :idLote";
    $queryFechaSalida = $con->prepare($sqlFechaSalida);
    $queryFechaSalida->bindParam(':idLote', $idLoteInterno);
    $queryFechaSalida->execute();
    if ($queryFechaSalida == FALSE) {
        throw new Exception($con->errorInfo());
    } else {
        $resultado['fechaSalida'] = $queryFechaSalida->fetch(PDO::FETCH_ASSOC);
    }

    $sqlFechaEmbarque = "SELECT s.datosTransporte
    FROM solicitudcze s
    WHERE s.idLoteInterno = :idLote";
    $queryFechaEmbarque = $con->prepare($sqlFechaEmbarque);
    $queryFechaEmbarque->bindParam(':idLote', $idLoteInterno);
    $queryFechaEmbarque->execute();
    if ($queryFechaEmbarque->rowCount() == 1) {
        $result = $queryFechaEmbarque->fetch(PDO::FETCH_ASSOC);
        $datosTransporte = json_decode($result['datosTransporte']);
        $resultado['fechaEmbarque'] = $datosTransporte->fechaEmbarque;
    } else {
        $resultado['fechaEmbarque'] = '0000-00-00';
    }

    $sqlLocalidades = "SELECT localidad FROM(
        SELECT l.localidad 
        FROM $almacenencabezado_tabla alen
        LEFT JOIN $almacen_tabla alde ON alde.idAlmacenEncabezado = alen.idAlmacen
        LEFT JOIN $tamboreslotes_tabla tam ON tam.folioTambor = alde.idAlmacen AND tam.tipo = '0' AND tam.clasificacion = '0'
        LEFT JOIN proveedor p ON p.idProveedor = alen.idProveedor
        LEFT JOIN direccion d ON d.idDireccion = p.idDireccion
        LEFT JOIN localidades l ON l.idlocalidad = d.idlocalidad
        WHERE tam.idLoteInterno = :idLote
        UNION
        SELECT 'Kanasín' AS localidad
        FROM almacensobrantesencabezado alsen 
        LEFT JOIN almacensobrantes alsde ON alsde.consecutivoEntrada = alsen.idEncabezadoSobrante
        LEFT JOIN $tamboreslotes_tabla tam ON tam.folioTambor = alsde.consecutivo AND tam.tipo = '1' AND tam.clasificacion = alsde.sobrante
        WHERE tam.idLoteInterno = :idLote AND alsen.tipoDeMiel = :miel
        ) AS entradas GROUP BY localidad ORDER BY localidad ASC";
    $queryLocalidades = $con->prepare($sqlLocalidades);
    $queryLocalidades->bindParam(':idLote', $idLoteInterno);
    $queryLocalidades->bindParam(':miel', $miel);
    $queryLocalidades->execute();
    if ($queryLocalidades == FALSE) {
        throw new Exception($con->errorInfo());
    } else {
        $resultado['localidades'] = $queryLocalidades->fetchAll(PDO::FETCH_ASSOC);
    }


    echo json_encode(['error' => false, 'message' => 'Consulta realizada', 'data' => $resultado]);
} catch (Exception $e) {
    echo json_encode(['error' => true, 'message' => $e->getMessage(), 'data' => $resultado]);
    exit();
}
