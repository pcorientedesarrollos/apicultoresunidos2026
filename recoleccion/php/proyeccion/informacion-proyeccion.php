<?php
include_once '../../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$con = $pdo->conectar();

try {

    if (!isset($_GET['idProyeccion']) && !isset($_GET['miel'])) {
        throw new Exception('No se recibió parámetro');
    } else {
        $idProyeccionSolicitado = $_GET['idProyeccion'];
        $miel = $_GET['miel'];
        switch ($miel) {
            case '1':
                $proyeccionEncabezado = 'proyeccionencabezado';
                $proyeccionDetalle = 'proyecciondetalle';
                $almacen = 'almacen';
                $almacenEncabezado = 'almacenencabezado';
                $cubetasEncabezado = 'cubetasencabezado';
                $cubetasDetalle = 'cubetasdetalle';
                break;
            case '2':
                $proyeccionEncabezado = 'proyeccionencabezado_organico';
                $proyeccionDetalle = 'proyecciondetalle_organico';
                $almacen = 'almacen_organico';
                $almacenEncabezado = 'almacenencabezado_organico';
                $cubetasEncabezado = 'cubetasencabezado_organico';
                $cubetasDetalle = 'cubetasdetalle_organico';
                break;
        }
    }

    $sql = "SELECT idProyeccion, nombre, inicio, fin, totalProyeccion
    FROM $proyeccionEncabezado WHERE idProyeccion = :idProyeccion;";

    $query = $con->prepare($sql);
    $query->bindParam(':idProyeccion', $idProyeccionSolicitado);
    $query->execute();

    if (!$query) {
        throw new Exception($con->errorInfo());
    }

    $resultadoInfoProyeccion = $query->fetch(PDO::FETCH_ASSOC);
    $resultadoInfoProyeccion['totalReal'] = 0;
    $resultadoInfoProyeccion['diferencia'] = 0;
    if ($miel == "1") {
        $resultadoInfoProyeccion['tipoDeMiel'] = '1';
    } else if ($miel == "2") {
        $resultadoInfoProyeccion['tipoDeMiel'] = '2';
    }
    // calcular el real y la diferencia

    // $sqlConsultaReal = "SELECT SUM(al.neto) as total
    // FROM almacen al 
    // LEFT JOIN almacenencabezado ae ON al.idAlmacenEncabezado = ae.idAlmacen
    // WHERE ae.fecha BETWEEN :iniciosemana AND :finsemana";

    // $queryRealSemana = $con->prepare($sqlConsultaReal);
    // $queryRealSemana->bindParam(':iniciosemana', $resultadoInfoProyeccion['inicio']);
    // $queryRealSemana->bindParam(':finsemana', $resultadoInfoProyeccion['fin']);
    // $queryRealSemana->execute();

    // if (!$queryRealSemana) {
    //     throw new Exception($con->errorInfo());
    // } else {
    //     $resultadoReal = $queryRealSemana->fetch(PDO::FETCH_ASSOC);
    // }

    // if (!$resultadoReal['total']) { // Si no hay,la suma da NULL
    //     $resultadoReal['total'] = 0;
    // }

    // $resultadoInfoProyeccion['totalReal'] = $resultadoReal['total'];
    // $resultadoInfoProyeccion['diferencia'] = $resultadoReal['total'] - $resultadoInfoProyeccion['totalProyeccion'];


    // Ahora, seleccionar las zonas guardadas como detalle de esa proyeccio
    $querySeleccionaZonas = $con->prepare("SELECT pd.idProyeccionDetalle, pd.idProyeccion, pd.idZona as idzona, pd.proyeccion, pd.kilogramos, z.zona, z.referencia
    FROM $proyeccionDetalle pd
    LEFT JOIN zonas z ON pd.idZona = z.idzona
    WHERE idProyeccion = :idProyeccion;");
    $querySeleccionaZonas->bindParam(':idProyeccion', $idProyeccionSolicitado);

    $querySeleccionaZonas->execute();

    if (!$querySeleccionaZonas) {
        throw new Exeption($con->errorInfo());
    }

    $resultadoZonasProyeccion = $querySeleccionaZonas->fetchAll(PDO::FETCH_ASSOC);

    $resultadoInfoProyeccion['listaZonas'] = array();

    foreach ($resultadoZonasProyeccion as $zona) {
        // Convertir propiedad proyeccion a número

        $zona['proyeccion'] = intval($zona['proyeccion']);

        // Calcular el real

        $sqlRealZona = "SELECT SUM(total) AS total FROM(
            SELECT SUM(a.neto) as total 
                                    FROM $almacen a
                        LEFT JOIN $almacenEncabezado ae ON a.idAlmacenEncabezado = ae.idAlmacen
                        LEFT JOIN proveedor p ON ae.idProveedor = p.idProveedor
                        LEFT JOIN direccion d ON p.idDireccion = d.idDireccion
                        LEFT JOIN localidades l ON d.idlocalidad = l.idlocalidad
                        WHERE ae.fecha BETWEEN :inicio AND :fin AND l.idzona = :idZona
            UNION
            SELECT SUM(a.neto) as total 
                                    FROM $cubetasDetalle a
                        LEFT JOIN $cubetasEncabezado ae ON a.idAlmacenEncabezado = ae.idAlmacen
                        LEFT JOIN proveedor p ON ae.idProveedor = p.idProveedor
                        LEFT JOIN direccion d ON p.idDireccion = d.idDireccion
                        LEFT JOIN localidades l ON d.idlocalidad = l.idlocalidad
                        WHERE ae.fecha BETWEEN :inicio AND :fin AND l.idzona = :idZona) AS tabla";
        $queryRealZona = $con->prepare($sqlRealZona);
        $queryRealZona->bindParam(':inicio', $resultadoInfoProyeccion['inicio']);
        $queryRealZona->bindParam(':fin', $resultadoInfoProyeccion['fin']);
        $queryRealZona->bindParam(':idZona', $zona['idzona']);
        $queryRealZona->execute();

        if (!$queryRealZona) {
            throw new Exeption($con->errorInfo());
        }

        $resultadoRealZona = $queryRealZona->fetch(PDO::FETCH_ASSOC);

        if (!$resultadoRealZona['total']) { // Si no hay,la suma da NULL
            $resultadoRealZona['total'] = 0;
        }

        $zona['real'] = floatval($resultadoRealZona['total']);
        $resultadoInfoProyeccion['totalReal'] += $zona['real'];
        // Meter al arreglo de zonas de la proyeccion
        array_push($resultadoInfoProyeccion['listaZonas'], $zona);
    }

    $resultadoInfoProyeccion['diferencia'] = $resultadoInfoProyeccion['totalReal'] - $resultadoInfoProyeccion['totalProyeccion'];


    echo json_encode(['error' => false, 'resultado' => $resultadoInfoProyeccion]);
} catch (Exception $e) {
    echo json_encode(['error' => true, 'message' => $e->getMessage()]);
}
