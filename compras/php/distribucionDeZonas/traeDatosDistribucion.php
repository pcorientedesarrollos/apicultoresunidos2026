<?php
include_once "../../../DAOConeccion/conePDO.php";
$pdo = new conePDO();
$con = $pdo->conectar();
$con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$resultado = new stdClass();
try {

    $consulta = "SELECT z.idzona, z.zona, c.nombre as comprador, z.referencia FROM zonas z INNER JOIN compradores c ON c.idcomprador = z.idcomprador";
    $consulta = $con->prepare($consulta);
    $consulta->execute();
    if ($consulta == false) {
        throw new Exception($con->errorInfo());
    } else {
        $resultado = $consulta->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode(['error' => false, 'message' => 'Consulta realizada', 'data' => $resultado]);
    }

} catch (Exception $e) {
    echo json_encode(['error' => true, 'message' => $e->getMessage()]);
}
