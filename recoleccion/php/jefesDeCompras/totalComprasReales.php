<?php
if (!isset($_GET['vista'])) {
    include_once '../../DAOConeccion/conePDO.php';
    $pdo = new conePDO();
    $con = $pdo->conectar();
} else {
    include_once '../../../DAOConeccion/conePDO.php';
    $pdo = new conePDO();
    $con = $pdo->conectar();
    $resultado = obtenerDatosJefesDeCompras();
    echo json_encode($resultado);
}
function obtenerDatosJefesDeCompras()
{
    global $con;
    $resultado = [
        'sumaKgsCompraReal' => 0,
        'sumaKgsMetas' => 0,
        'sumaTotalAvance' => 0,
        'sumaTotalPorCiento' => 0,
        'compradores' => []
    ];

    switch ($_GET['miel']) {
        case '1':
            $metasCompra = 'metascompra';
            $almacenDetalle = 'almacen';
            $almacenEncabezado = 'almacenencabezado';
            $cubetasDetalle = 'cubetasdetalle';
            $cubetasEncabezado = 'cubetasencabezado';
            break;
        case '2':
            $metasCompra = 'metascompra_organico';
            $almacenDetalle = 'almacen_organico';
            $almacenEncabezado = 'almacenencabezado_organico';
            $cubetasDetalle = 'cubetasdetalle_organico';
            $cubetasEncabezado = 'cubetasencabezado_organico';
            break;
    }

    $sql = "SELECT c.idComprador, c.nombre, SUM(mc.kilogramos) AS meta 
    FROM compradores c 
    LEFT JOIN zonas z ON z.idcomprador = c.idcomprador
    LEFT JOIN $metasCompra mc ON mc.idZona = z.idzona
    WHERE c.estado = 1
    GROUP BY c.idcomprador ORDER BY c.nombre"; // Selecciona nombre y id de compradores activos

    $query = $con->prepare($sql);
    // $query->bindParam(':idProyeccion', $idProyeccionSolicitado);
    $query->execute();

    if (!$query) {
        throw new Exception($con->errorInfo());
    }

    $listaCompradores = $query->fetchAll(PDO::FETCH_ASSOC);

    foreach ($listaCompradores as $comprador) {
        // Por cada comprador, traer sus zonas y sumar en una propiedad el total de kilos en almacén
        $comprador['meta'] = (int)$comprador['meta'];
        $comprador['totalReal'] = 0;
        $comprador['avance'] = 0;
        $comprador['zonas'] = array();

        $sqlZonasComprador = "SELECT z.idzona, z.zona, z.referencia
        FROM zonas z
        LEFT JOIN compradores c ON c.idcomprador = z.idcomprador
        WHERE c.idcomprador = :idComprador";
        $queryZonasComprador = $con->prepare($sqlZonasComprador);
        $queryZonasComprador->bindParam(':idComprador', $comprador['idComprador']);
        $queryZonasComprador->execute();
        if (!$queryZonasComprador) {
            throw new Exception($con->errorInfo());
        }
        $resultadoZonasComprador = $queryZonasComprador->fetchAll(PDO::FETCH_ASSOC);

        foreach ($resultadoZonasComprador as $zona) {


            if (isset($_GET['fecha1']) && isset($_GET['fecha2'])) {
                $sqlRealZona = "SELECT SUM(total) AS total FROM(
                    SELECT SUM(a.neto) as total
                                FROM $almacenDetalle a
                                LEFT JOIN $almacenEncabezado ae ON a.idAlmacenEncabezado = ae.idAlmacen
                                LEFT JOIN proveedor p ON ae.idProveedor = p.idProveedor
                                LEFT JOIN direccion d ON p.idDireccion = d.idDireccion
                                LEFT JOIN localidades l ON d.idlocalidad = l.idlocalidad
                                WHERE l.idzona = :idZona AND ae.fecha BETWEEN :fecha1 AND :fecha2
                    UNION 
                    SELECT SUM(a.neto) as total
                                FROM $cubetasDetalle a
                                LEFT JOIN $cubetasEncabezado ae ON a.idAlmacenEncabezado = ae.idAlmacen
                                LEFT JOIN proveedor p ON ae.idProveedor = p.idProveedor
                                LEFT JOIN direccion d ON p.idDireccion = d.idDireccion
                                LEFT JOIN localidades l ON d.idlocalidad = l.idlocalidad
                                WHERE l.idzona = :idZona AND a.tamborAsignado = '0' AND ae.fecha BETWEEN :fecha1 AND :fecha2) AS tabla";
                // $sqlRealZona .= " AND ae.fecha BETWEEN :fecha1 AND :fecha2";
            } else {
                $sqlRealZona = "SELECT SUM(total) AS total FROM(
                    SELECT SUM(a.neto) as total
                                FROM $almacenDetalle a
                                LEFT JOIN $almacenEncabezado ae ON a.idAlmacenEncabezado = ae.idAlmacen
                                LEFT JOIN proveedor p ON ae.idProveedor = p.idProveedor
                                LEFT JOIN direccion d ON p.idDireccion = d.idDireccion
                                LEFT JOIN localidades l ON d.idlocalidad = l.idlocalidad
                                WHERE l.idzona = :idZona
                    UNION 
                    SELECT SUM(a.neto) as total
                                FROM $cubetasDetalle a
                                LEFT JOIN $cubetasEncabezado ae ON a.idAlmacenEncabezado = ae.idAlmacen
                                LEFT JOIN proveedor p ON ae.idProveedor = p.idProveedor
                                LEFT JOIN direccion d ON p.idDireccion = d.idDireccion
                                LEFT JOIN localidades l ON d.idlocalidad = l.idlocalidad
                                WHERE l.idzona = :idZona AND a.tamborAsignado = '0') AS tabla";
            }
            $queryRealZona = $con->prepare($sqlRealZona);
            $queryRealZona->bindParam(':idZona', $zona['idzona']);
            if (isset($_GET['fecha1']) && isset($_GET['fecha2'])) {
                $queryRealZona->bindParam(':fecha1', $_GET['fecha1']);
                $queryRealZona->bindParam(':fecha2', $_GET['fecha2']);
            }
            $queryRealZona->execute();

            if (!$queryRealZona) {
                throw new Exeption($con->errorInfo());
            }

            $resultadoRealZona = $queryRealZona->fetch(PDO::FETCH_ASSOC);

            if (!$resultadoRealZona['total']) { // Si no hay,la suma da NULL
                $resultadoRealZona['total'] = 0;
            }

            $zona['real'] = floatval($resultadoRealZona['total']);

            $sqlMetaZona = "SELECT CASE WHEN kilogramos IS NULL THEN 0 ELSE kilogramos END AS metaZona FROM $metasCompra WHERE idZona = :idZona";
            $queryMetaZona = $con->prepare($sqlMetaZona);
            $queryMetaZona->bindParam(':idZona', $zona['idzona']);
            $queryMetaZona->execute();
            $queryMetaZona->bindColumn('metaZona', $zona['metaZona']);
            if ($queryMetaZona == false) {
                throw new Exception($con->errorInfo());
            } else {
                $queryMetaZona->fetch(PDO::FETCH_BOUND);
            }

            $zona['faltanteZona'] = $zona['metaZona'] - $zona['real'];
            if ($zona['metaZona'] == 0) {
                $zona['avanceZona'] = 0;
            } else {
                $zona['avanceZona'] = $zona['real'] / $zona['metaZona'] * 100;
            }
            $comprador['totalReal'] += $zona['real'];
            // Meter al arreglo de zonas
            array_push($comprador['zonas'], $zona);
        }
        $comprador['avance'] = $comprador['totalReal'] / $comprador['meta'] * 100;
        $comprador['faltante'] = $comprador['meta'] - $comprador['totalReal'];
        array_push($resultado['compradores'], $comprador);
    }

    foreach ($resultado['compradores'] as $res) {
        $resultado['sumaKgsCompraReal'] += $res['totalReal'];
        $resultado['sumaKgsMetas'] += $res['meta'];
    }
    $arrayCompradores = array();

    foreach ($resultado['compradores'] as  $resp) {
        $resp['totalPorCiento'] = $resp['meta'] / $resultado['sumaKgsMetas'] * 100;
        $resp['acumuladoCompra'] = $resp['totalReal'] / $resultado['sumaKgsCompraReal'] * 100;

        array_push($arrayCompradores, $resp);
    }
    $resultado['compradores'] = $arrayCompradores;

    foreach ($resultado['compradores'] as $res) {
        $resultado['sumaTotalPorCiento'] += $res['acumuladoCompra'];
        $resultado['sumaTotalAvance'] = $resultado['sumaKgsCompraReal'] / $resultado['sumaKgsMetas'] * 100;
        $resultado['totalFaltante'] = $resultado['sumaKgsMetas'] - $resultado['sumaKgsCompraReal'];
    }
    return $resultado;
}
