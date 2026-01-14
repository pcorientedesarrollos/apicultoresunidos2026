<?php

include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$con = $pdo->conectar();

if (isset($_GET["salida"])) {

    try {

        $salida = $_GET["salida"];
        $con->beginTransaction();

        $sqlDetalle = "DELETE FROM reactivossalidadetalle WHERE idSalida = :salida";
        $datos = $con->prepare($sqlDetalle);
        $datos->bindParam(':salida', $salida);
        $datos->execute();
        if ($datos == false) {
            throw new Exception($con->errorInfo());
        }

        $sqlEncabezado = "DELETE FROM reactivossalidaencabezado WHERE idSalida = :salida";
        $data = $con->prepare($sqlEncabezado);
        $data->bindParam(':salida', $salida);
        $data->execute();
        if ($data == false) {
            throw new Exception($con->errorInfo());
        }

        $con->commit();
        echo json_encode(['error' => false, 'message' => 'Consulta realizada']);
    } catch (Exception $e) {
        $con->rollBack();
        echo json_encode(['error' => true, 'message' => $e->getMessage() . ' Linea: ' . $e->getLine()]);
    }

}
?>