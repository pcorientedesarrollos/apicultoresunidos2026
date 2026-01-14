<?php

include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$conexion = $pdo->conectar();
$idConceptoCC = $_GET["idConceptoCC"];

$sql = "SELECT idConceptoCC, concepto FROM conceptoscajachica 
WHERE idConceptoCC = :idConceptoCC";

$datos = $conexion->prepare($sql);
$datos->bindParam(':idConceptoCC', $idConceptoCC);
$datos->execute();

if ($datos == false) {
    echo mysql_error();
} else {
    while ($rs = $datos->fetch()) {
        $subcuenta = new stdClass();
        $subcuenta->idConceptoCC = $rs["idConceptoCC"];
        $subcuenta->concepto = $rs["concepto"];
    }

    echo json_encode($subcuenta);
}