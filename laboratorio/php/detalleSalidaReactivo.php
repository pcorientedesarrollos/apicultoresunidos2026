<?php

include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$con = $pdo->conectar();
$con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$resultado = new stdClass();
try {

    if (!isset($_GET['idSalida'])) {
        throw new Exception('No se recibieron datos');
    } else {
        $idSalida = $_GET['idSalida'];
        $miel = $_GET['miel'];

        switch ($miel) {
            case '1':
                $conformacion = 'conformacionhomogeneo_encabezado';
                $folios = 'conformacionhomogeneo_folios';
                $almacen = 'almacen';
                $almacenEncabezado = 'almacenencabezado';
                $reactivos = 'conformacionhomogeneo_reactivos';
                break;
            case '2':
                $conformacion = 'conformacionhomogeneo_encabezado_organico';
                $folios = 'conformacionhomogeneo_folios_organico';
                $almacen = 'almacen_organico';
                $almacenEncabezado = 'almacenencabezado_organico';
                $reactivos = 'conformacionhomogeneo_reactivos_organico';
                break;
        };
    }

    $sql = "SELECT ce.*, $miel AS tipoMiel 
    FROM $conformacion ce WHERE ce.idEncabezado = :idSalida";
    $datos = $con->prepare($sql);
    $datos->bindParam(':idSalida', $idSalida);
    $datos->execute();
    if ($datos == FALSE) {
        throw new Exception($con->errorInfo());
    }
    $resultado = $datos->fetch(PDO::FETCH_ASSOC);
    $foliosLst = [];

    if ($resultado['busqueda'] == '3') {
        $sqlDivisor1 = $con->prepare("SELECT folio, nombre, localidad, tipoMiel FROM conformacionhomogeneo_sinfolios WHERE idEncabezado = :idSalida AND tipoMiel = :tipoMiel");
        $sqlDivisor1->bindParam(':idSalida', $idSalida);
        $sqlDivisor1->bindParam(':tipoMiel', $miel);
        $sqlDivisor1->execute();
        $resultado['listaFolios'] =  $sqlDivisor1->fetchAll(PDO::FETCH_ASSOC);
    } else {
        $sqlDivisor = $con->prepare("SELECT folio, almacen, sobrante FROM $folios WHERE idEncabezado = :idSalida");
        $sqlDivisor->bindParam(':idSalida', $idSalida);
        $sqlDivisor->execute();
        foreach ($sqlDivisor->fetchAll(PDO::FETCH_ASSOC) as $id) {
            if ($id['almacen'] == '1') {
                $sqlDetalle = $con->prepare("SELECT f.folio, p.nombre, l.localidad
            FROM $folios f 
            LEFT JOIN $almacen a ON a.idAlmacen = f.folio
            LEFT JOIN $almacenEncabezado e ON e.idAlmacen = a.idAlmacenEncabezado
            LEFT JOIN proveedor p ON p.idProveedor = e.idProveedor
            LEFT JOIN direccion d ON d.idDireccion = p.idDireccion
            LEFT JOIN localidades l ON l.idlocalidad = d.idlocalidad
            WHERE f.folio = :folio AND sobrante = 0");
                $sqlDetalle->bindParam(':folio', $id['folio']);
                $sqlDetalle->execute();
                if ($sqlDetalle == FALSE) {
                    throw new Exception($con->errorInfo());
                }
                $resp1 = $sqlDetalle->fetch(PDO::FETCH_ASSOC);
                $foliosId['folio'] = $resp1["folio"];
                $foliosId['nombre'] = $resp1["nombre"];
                $foliosId['localidad'] = $resp1["localidad"];
            } else {
                $sql2 = "SELECT CONCAT(s.codigo, '-', a.consecutivo) AS folio, s.nombre, '--' AS localidad
            FROM $folios f 
            INNER JOIN almacensobrantes a ON a.consecutivo = f.folio AND a.sobrante = f.sobrante
            INNER JOIN sobrantes s ON s.idSobrante = f.sobrante
            WHERE a.consecutivo = :folio AND a.sobrante = :sobrante AND a.tipoDeMiel = $miel";
                $sqlDetalle2 = $con->prepare($sql2);
                $sqlDetalle2->bindParam(':folio', $id['folio']);
                $sqlDetalle2->bindParam(':sobrante', $id['sobrante']);
                $sqlDetalle2->execute();
                if ($sqlDetalle2 == FALSE) {
                    throw new Exception($con->errorInfo());
                }
                $resp1 = $sqlDetalle2->fetch(PDO::FETCH_ASSOC);
                $foliosId['folio'] = $resp1["folio"];
                $foliosId['nombre'] = $resp1["nombre"];
                $foliosId['localidad'] = $resp1["localidad"];
            }
            array_push($foliosLst, $foliosId);
        }
        $resultado['listaFolios'] = $foliosLst;
    }

    $sqlR = "SELECT r.cantidad, r.resultado AS resultadoReactivo, cr.reactivo AS nombre
    FROM $reactivos r 
    LEFT JOIN catalogoreactivos cr ON cr.idReactivo = r.reactivo
    WHERE r.idEncabezado = :idSalida";
    $sqlDetalleRe = $con->prepare($sqlR);
    $sqlDetalleRe->bindParam(':idSalida', $idSalida);
    $sqlDetalleRe->execute();

    if ($sqlDetalleRe == FALSE) {
        throw new Exception($con->errorInfo());
    } else {
        $resultado['reactivos'] = $sqlDetalleRe->fetchAll(PDO::FETCH_ASSOC);
    }

    echo json_encode(['error' => false, 'message' => 'Consulta realizada', 'data' => $resultado]);
} catch (Exception $e) {
    echo json_encode(['error' => true, 'message' => $e->getMessage(), 'data' => $resultado]);
    exit();
}
