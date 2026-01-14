<?php

// Última modificación del archivo: Alejandro Medina
// Fecha: 22-10-2018
// Motivo: Cambiar donde se guardan los precios del las subsubcuentas, a la nueva tabla subsubcuentas

include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$con = $pdo->conectar();
$post = file_get_contents('php://input');

try {
    if (!$post) {
        throw new Exception('No se recibieron los datos');
    } else {
        $datos = json_decode($post);
    }

    if (isset($datos->idSubSubcuenta)) {
        $message = 'Se ha actualizado la información del producto';
        $sqlSaveConcepto = $con->prepare("UPDATE subsubcuentas SET precioUnitario = :nuevoPrecio,
            unidad = :unidad, peso = :peso WHERE idSubSubcuenta = :idSubSubcuenta");
        $sqlSaveConcepto->bindParam(':idSubSubcuenta', $datos->idSubSubcuenta);
    } else {

        // Se va a dejar este código disponible, aunque ya no debería ser alcanzable

        $message = 'Se ha registrado un nuevo concepto';
        $sqlSaveConcepto = $con->prepare("INSERT INTO subsubcuentas (subSubcuenta, idSubcuenta, precio,
            precioUnitario, unidad, peso) VALUES (:subSubcuenta, :idSubcuenta, '1', :nuevoPrecio, :unidad, :peso)");
        $sqlSaveConcepto->bindParam(':subSubcuenta', $datos->subSubcuenta);
        $sqlSaveConcepto->bindParam(':idSubcuenta', $datos->idSubcuenta);
    }

    $sqlSaveConcepto->bindParam(':nuevoPrecio', $datos->precioUnitario);
    $sqlSaveConcepto->bindParam(':unidad', $datos->unidad);
    $sqlSaveConcepto->bindParam(':peso', $datos->peso);

    $sqlSaveConcepto->execute();
    if ($sqlSaveConcepto == false) {
        throw new Exception($con->errorInfo());
    }

    echo json_encode(['error' => false, 'message' => $message]);
} catch (Exception $e) {
    echo json_encode(['error' => true, 'message' => $e->getMessage()]);
}