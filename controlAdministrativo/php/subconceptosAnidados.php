<?php

include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO(); $con = $pdo->conectar();

$idConceptoCC = $_GET['idConceptoCC'];

// Trae los precios, unidad y peso de los conceptos para caja chica
// Este archivo deberá ser modificado para tomar los datos de la nueva tabla de subsubcuentas

$query = "SELECT idSubconceptoCC, subconceptoCC, precioUnitario, unidad, peso, udm.nombre as nombreUnidad
    FROM subconceptoscajachica
    LEFT JOIN unidadesdemedida udm ON unidad = udm.idUnidad
    WHERE idConceptoCC = :idConceptoCC";
$datos = $con->prepare($query);
$datos->bindParam(':idConceptoCC', $idConceptoCC);
$datos->execute();

$arrayCuentas = $datos->fetchAll(PDO::FETCH_ASSOC);
echo json_encode($arrayCuentas);
?>