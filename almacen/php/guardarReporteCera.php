<?php

include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$con = $pdo->conectar();
$con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$json = file_get_contents("php://input");

try {
    $con->beginTransaction();
    if (!$json || !isset($_GET['tipo'])) {
        throw new Exception('No se recibieron los parámetros');
    } else {
        $datos = json_decode($json);
        $tipoCera = $datos->tipoCera;
        $proveedor = $datos->proveedor;
        $conceptos = $datos->conceptos;
        $fechaEntrada = $datos->fecha;
        $tipoPersona = $datos->tipoPersona;
        $totalCompra = $datos->total;
        $kg = $datos->kg;

        // $clasificacion = $_GET['tipo'] == '1' ? $datos->clasificacion->idClasificacionCera : $datos->clasificacion->idCondicionSalida;
        $tipo = $_GET['tipo'];
    }
    if (isset($datos->idAlmacen)) {
        $sql = "UPDATE almacenencabezadocera SET fecha = :fecha, tipoPersona = :tipoPersona, idProveedor = :idProveedor, total = :total, kg = :kg, tipoCera = :tipoCera  WHERE idAlmacen = :idAlmacen";
        $insertarEncabezado = $con->prepare($sql);
        $insertarEncabezado->bindParam(':fecha', $fechaEntrada);
        $insertarEncabezado->bindParam(':tipoPersona', $tipoPersona);
        $insertarEncabezado->bindParam(':idProveedor', $proveedor->id);
        $insertarEncabezado->bindParam(':total', $totalCompra);
        $insertarEncabezado->bindParam(':kg', $kg);
        $insertarEncabezado->bindParam(':idAlmacen', $datos->idAlmacen);
        $insertarEncabezado->bindParam(':tipoCera', $tipoCera);
    } else {
        $sql = "INSERT INTO almacenencabezadocera (fecha, tipoPersona, idProveedor, folio, total, kg, tipo, tipoCera)
                VALUES (:fecha, :tipoPersona, :idProveedor, '0', :total, :kg, :tipo, :tipoCera)";
        $insertarEncabezado = $con->prepare($sql);
        $insertarEncabezado->bindParam(':fecha', $fechaEntrada);
        $insertarEncabezado->bindParam(':tipoPersona', $tipoPersona);
        $insertarEncabezado->bindParam(':idProveedor', $proveedor->id);
        $insertarEncabezado->bindParam(':total', $totalCompra);
        $insertarEncabezado->bindParam(':kg', $kg);
        $insertarEncabezado->bindParam(':tipo', $tipo);
        $insertarEncabezado->bindParam(':tipoCera', $tipoCera);
    }
    $insertarEncabezado->execute();

    if ($insertarEncabezado == false) {
        throw new Exception($con->errorInfo());
    }
    if (isset($datos->idAlmacen)) {
        $idAlmacen = $datos->idAlmacen;
        $sql = "DELETE FROM almacencera WHERE idAlmacenEncabezado = :idAlmacenEncabezado";
        $deleteConcepts = $con->prepare($sql);
        $deleteConcepts->bindParam(':idAlmacenEncabezado', $idAlmacen);
        $deleteConcepts->execute();
        if ($deleteConcepts == false) {
            throw new Exception($con->errorInfo());
        }
    } else {
        $idAlmacen = $con->lastInsertid();
    }

    foreach ($conceptos as $concepto) {

        // La clasificación depende si es entrada o salida, son diferentes las tablas
        $clasificacion = $tipo == '1' ? $concepto->clasificacion->idClasificacionCera : $concepto->clasificacion->idCondicionSalida;
        if ($tipo == '2') { 
            $concepto->zona = 0;
        }
        // Ya no vamos a insertar unidad
        // movimiento y concepto son opcionales en la vista, subcuenta si viene siempre
        // hay que sacar el nombre de todos 

        // A) MOVIMIENTO
        $idMovimiento = $concepto->idMovimiento;
        $nombre_movimiento = $concepto->movimiento;

        // B) SUBCUENTA

        $idSubcuenta = $concepto->idSubcuenta;
        $nombre_subcuenta = $concepto->subcuenta;

        // C) CONCEPTO
        $idConcepto = $concepto->idConcepto;
        $nombre_concepto = $concepto->concepto;

        //ZONA
        // $zona = $concepto->zona->idZonaCera;


        $sql = "INSERT INTO almacencera (idAlmacenEncabezado, zona, cantidad, descripcion, costoUnitario, importe, kgTotal, clasificacion, idMovimiento, movimiento, idSubcuenta, subcuenta, idConcepto, concepto)
                VALUES (:idAlmacenEncabezado, :zona, :cantidad, :descripcion, :costoUnitario, :importe, :kgTotal, :clasificacion, :idMovimiento, :movimiento, :idSubcuenta, :subcuenta, :idConcepto, :concepto)";
        $insertarDetalle = $con->prepare($sql);
        $insertarDetalle->bindParam(':idAlmacenEncabezado', $idAlmacen);
        $insertarDetalle->bindParam(':zona', $concepto->zona);
        $insertarDetalle->bindParam(':cantidad', $concepto->cantidad);
        // $insertarDetalle->bindParam(':unidad', $concepto->unidad->idSubconceptoCC);
        $insertarDetalle->bindParam(':descripcion', $concepto->descripcion);
        $insertarDetalle->bindParam(':costoUnitario', $concepto->costoUnitario);
        $insertarDetalle->bindParam(':importe', $concepto->importe);
        $insertarDetalle->bindParam(':kgTotal', $concepto->kgTotal);
        $insertarDetalle->bindParam(':clasificacion', $clasificacion);

        // Nuevos campos
        $insertarDetalle->bindParam(':idMovimiento', $idMovimiento);
        $insertarDetalle->bindParam(':movimiento', $nombre_movimiento);
        $insertarDetalle->bindParam(':idSubcuenta', $idSubcuenta);
        $insertarDetalle->bindParam(':subcuenta', $nombre_subcuenta);
        $insertarDetalle->bindParam(':idConcepto', $idConcepto);
        $insertarDetalle->bindParam(':concepto', $nombre_concepto);
        $insertarDetalle->execute();

        if ($insertarDetalle == false) {
            throw new Exception($con->errorInfo());
        }
    }

    $con->commit();
    echo json_encode(['error' => false, 'message' => 'Se han guardado los datos', 'swal' => 'success']);
} catch (Exception $e) {
    $con->rollBack();
    echo json_encode(['error' => true, 'message' => $e->getMessage() . '. Line ' . $e->getLine(), 'swal' => 'error']);
    exit();
}