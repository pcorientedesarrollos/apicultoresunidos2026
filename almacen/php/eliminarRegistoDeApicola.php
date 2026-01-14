<?php

include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$con = $pdo->conectar();

if (isset($_GET["entrada"])) {

    try {

        $entrada = $_GET["entrada"];
        $con->beginTransaction();

        $sqlDetalle = "DELETE FROM almacenapicola WHERE idAlmacenEncabezado = :entrada";
        $datos = $con->prepare($sqlDetalle);
        $datos->bindParam(':entrada', $entrada);
        $datos->execute();
        if ($datos == false) {
            throw new Exception($con->errorInfo());
        }

        $sqlEncabezado = "DELETE FROM almacenencabezadoapicola WHERE idAlmacen = :entrada";
        $data = $con->prepare($sqlEncabezado);
        $data->bindParam(':entrada', $entrada);
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