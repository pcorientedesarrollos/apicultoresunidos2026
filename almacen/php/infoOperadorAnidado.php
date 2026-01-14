<?php

include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$con = $pdo->conectar();

if (isset($_GET['idOperador'])) {
    $idOperador = $_GET['idOperador'];
    // $query = "SELECT c.idOperador, c.operador, c.compania, c.licencia, c.vigencia, 
    // c.unas, c.cabello, c.ropa, 
    // CASE WHEN c.remolque = '1' THEN t.remolque ELSE 'No aplica' END AS remolque,
    // c.marca, c.modelo, c.placas
    // FROM choferes c
    // LEFT JOIN tiposremolque t ON t.idRemolque = c.caja
    // WHERE c.idOperador = :idOperador";
    $query = "SELECT c.idOperador, c.operador, c.compania, c.licencia, c.vigencia
    FROM choferes c
    WHERE c.idOperador = :idOperador";
    $datos = $con->prepare($query);
    $datos->bindParam(':idOperador', $idOperador);
    $datos->execute();
    if ($datos == FALSE) {
        throw new Exception($con->errorInfo());
    }
} else if (isset($_GET['idPlaca'])) {
    $idPlaca = $_GET['idPlaca'];
    // $queryPlacas = "SELECT p.idPlaca, p.placa, p.tipo, p.marca, p.modelo,
    // FROM placaschoferes p 
    // WHERE p.idPlaca = :idPlaca";
    $queryPlacas = "SELECT p.idPlaca, p.placa, p.tipo, p.marca, p.modelo, 
    t.remolque, p.marcaRemolque, p.modeloRemolque, p.placaRemolque
    FROM placaschoferes p 
    LEFT JOIN tiposremolque t ON t.idRemolque = p.remolque
    WHERE p.idPlaca = :idPlaca";
    $datos = $con->prepare($queryPlacas);
    $datos->bindParam(':idPlaca', $idPlaca);
    $datos->execute();
    if ($datos == FALSE) {
        throw new Exception($con->errorInfo());
    }
}

$infoAnidada = $datos->fetch(PDO::FETCH_ASSOC);

echo $json_response = json_encode($infoAnidada);
