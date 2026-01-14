<?php

include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$con = $pdo->conectar();
$con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$resultado = array();
try {

    if (!isset($_GET['mes'])) {
        throw new Exception('No se recibieron datos');
    } else {
        $idMes = $_GET['mes'];
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

        $sqlDetalle = $con->prepare("SELECT p.idPresupuesto, p.idSubcuenta, s.subcuenta, p.cantidad
        FROM presupuestos_cuentas p 
        LEFT JOIN subcuentas s ON s.idSubcuenta = p.idSubcuenta
        WHERE p.idCuenta = :idCuenta AND p.idMes = :idMes");
        $sqlDetalle->bindParam(':idCuenta', $nueva_cuenta->idCuenta);
        $sqlDetalle->bindParam(':idMes', $idMes);
        $sqlDetalle->execute();
        $listaSubcuentas = $sqlDetalle->fetchAll(PDO::FETCH_ASSOC);

        foreach ($listaSubcuentas as $dato1) {
            $gastoR = new stdClass();
            $gastoR->idSubcuenta = $dato1['idSubcuenta'];

            $sqlGastos = $con->prepare("SELECT SUM(total) AS total
            FROM(
		        SELECT SUM(cantidad) AS total, SUBSTR(fecha FROM 6 FOR 2) AS mes
		        FROM auxiliardebancos
                WHERE tipoMovimiento = :idCuenta AND idSubcuenta = :idSubcuenta AND SUBSTR(fecha FROM 6 FOR 2) = CONVERT (:idMes, UNSIGNED INTEGER)
                GROUP BY idSubcuenta
                UNION
                SELECT SUM(d.cantidad) AS total, SUBSTR(e.fecha FROM 6 FOR 2) AS mes
                FROM cajachicadetalle d LEFT JOIN cajachica e ON e.idCajaChica = d.idCajaChica
                WHERE d.idMovimiento = :idCuenta AND d.idConcepto = :idSubcuenta AND SUBSTR(fecha FROM 6 FOR 2) = CONVERT (:idMes, UNSIGNED INTEGER) GROUP BY d.idConcepto
                ) AS caja");
            $sqlGastos->bindParam(':idCuenta', $nueva_cuenta->idCuenta);
            $sqlGastos->bindParam(':idSubcuenta', $gastoR->idSubcuenta);
            $sqlGastos->bindParam(':idMes', $idMes);
            $sqlGastos->execute();
            $dato1['gasto'] = $sqlGastos->fetch(PDO::FETCH_ASSOC);
            array_push($dato['subcuentas'], $dato1);
        }

        array_push($resultado, $dato);
    }

    echo json_encode(['error' => false, 'message' => 'Consulta realizada', 'data' => $resultado]);
} catch (Exception $e) {
    echo json_encode(['error' => true, 'message' => $e->getMessage(), 'data' => $resultado]);
    exit();
}
