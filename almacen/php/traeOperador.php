<?php

include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$con = $pdo->conectar();

$error = "";

if (!isset($_GET['idOperador'])) {
    echo $error = "Falta el codigo";
    die;
}

$proveAlmacen = $_GET['idOperador'];

$query = "SELECT * FROM choferes WHERE idOperador = :idOperador ";
$datos = $con->prepare($query);
$datos->bindParam(':idOperador', $proveAlmacen);
$datos->execute();

while ($row = $datos->fetch()) {
    $operador = new stdClass();
    $operador->idOperador = $row["idOperador"];
    $operador->operador = $row["operador"];
    $operador->licencia = $row["licencia"];
    $operador->vigencia = $row["vigencia"];
    $operador->compania = $row["compania"];
    // $operador->remolque = $row["remolque"];
    // $operador->marca = $row["marca"];
    // $operador->modelo = $row["modelo"];
    // $operador->placas = $row["placas"];
    // $operador->caja = $row["caja"];
    // $operador->unas = $row["unas"];
    // $operador->cabello = $row["cabello"];
    // $operador->ropa = $row["ropa"];

    $operador->arregloPlacas = array();
    $sqlPlacas = "SELECT * FROM placaschoferes WHERE idOperador = :idOperador ";
    $datosPlacas = $con->prepare($sqlPlacas);
    $datosPlacas->bindParam(':idOperador', $proveAlmacen);
    $datosPlacas->execute();
    while ($rsCont = $datosPlacas->fetch()) {
        $placa = new stdClass();
        $placa->idPlaca = $rsCont["idPlaca"];
        $placa->placa = $rsCont["placa"];
        $placa->tipo = $rsCont["tipo"];
        $placa->marca = $rsCont["marca"];
        $placa->modelo = $rsCont["modelo"];
        $placa->remolque = $rsCont["remolque"];
        $placa->marcaRemolque = $rsCont["marcaRemolque"];
        $placa->modeloRemolque = $rsCont["modeloRemolque"];
        $placa->placaRemolque = $rsCont["placaRemolque"];
        $operador->arregloPlacas[] = $placa;
    }
}
echo $json_response = json_encode($operador);
?>