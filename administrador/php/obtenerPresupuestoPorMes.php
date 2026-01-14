<?php

include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$con = $pdo->conectar();
$con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$resultado = array();
try {

    if (!isset($_GET['idMes'])) {
        throw new Exception('No se recibieron datos');
    } else {
        $idMes = $_GET['idMes'];
    }

    $sqlEncabezado = $con->prepare("SELECT p.idCuenta, c.cuenta
    FROM presupuestos_cuentas p
    LEFT JOIN cuentas c ON c.idCuentaConcepto = p.idCuenta
    WHERE p.idMes = :idMes
    GROUP BY p.idCuenta");
    $sqlEncabezado->bindParam(':idMes', $idMes);
    $sqlEncabezado->execute();
    $listaCuentas = $sqlEncabezado->fetchAll(PDO::FETCH_ASSOC);

    foreach ($listaCuentas as $dato) {
        $nueva_cuenta = new stdClass();
        $nueva_cuenta->idCuenta = $dato['idCuenta'];
        $nueva_cuenta->cuenta = $dato['cuenta'];
        $dato['subcuentas'] = [];

        $sqlDetalle = $con->prepare("SELECT p.idPresupuesto, s.subcuenta, p.cantidad
        FROM presupuestos_cuentas p 
        LEFT JOIN subcuentas s ON s.idSubcuenta = p.idSubcuenta
        WHERE p.idCuenta = :idCuenta AND p.idMes = :idMes");
        $sqlDetalle->bindParam(':idCuenta', $nueva_cuenta->idCuenta);
        $sqlDetalle->bindParam(':idMes', $idMes);
        $sqlDetalle->execute();
        $dato['subcuentas'] = $sqlDetalle->fetchAll(PDO::FETCH_ASSOC);
        array_push($resultado, $dato);
    }

    echo json_encode(['error' => false, 'message' => 'Consulta realizada', 'data' => $resultado]);
} catch (Exception $e) {
    echo json_encode(['error' => true, 'message' => $e->getMessage(), 'data' => $resultado]);
    exit();
}
