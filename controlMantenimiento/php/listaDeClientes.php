<?php

include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$con = $pdo->conectar();

$sql = 'SELECT idCliente, nombre FROM clientes ORDER BY nombre ASC';
$datos = $con->prepare($sql);
$datos->execute();

$arrayPersonalE = array();
while ($row = $datos->fetch()) {
    $listaClientes = new stdClass();
    $listaClientes->idCliente = $row["idCliente"];
    $listaClientes->nombre = $row["nombre"];
    $arrayPersonalE[] = $listaClientes;
}

echo $json_response = json_encode($arrayPersonalE);
?>