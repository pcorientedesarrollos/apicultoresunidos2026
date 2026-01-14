<?php

include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$con = $pdo->conectar();

$query = "SELECT idConceptoCC, concepto FROM conceptoscajachica";
$datos = $con->prepare($query);
$datos->execute();

$arraySub = array();
while ($row = $datos->fetch()) {
    $subcuenta = new stdClass();
    $subcuenta->idConceptoCC = $row["idConceptoCC"];
    $subcuenta->concepto = $row["concepto"];
    $arraySub[] = $subcuenta;
}

# JSON-encode the response
echo $json_response = json_encode($arraySub);
?>