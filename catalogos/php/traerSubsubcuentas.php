<?php
include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$con = $pdo->conectar();
$resultado = array();

try {

    if (!isset($_GET['idSubcuenta'])) {
        throw new Exception('No se recibió el parámetro esperado');
    }
    $idSubcuenta = $_GET['idSubcuenta'];

    // Traer precio, unidad y peso para ingresos en caja chica
    $datos = $con->prepare('SELECT ss.idSubSubcuenta, ss.subSubcuenta, ss.precioUnitario, ss.peso, udm.nombre as nombreUnidad
    FROM subsubcuentas ss
    LEFT JOIN unidadesdemedida udm ON udm.idUnidad = ss.unidad
    WHERE idSubcuenta = :idSubcuenta AND ss.ocultar = 0');
    $datos->bindParam(':idSubcuenta', $idSubcuenta);
    $datos->execute();

    if ($datos == false) {
        throw new Exception($con->errorInfo());
    } else {
        $resultado = $datos->fetchAll(PDO::FETCH_ASSOC);
    }

    echo json_encode(['error' => false, 'data' => $resultado]);

} catch (Exception $e) {
    echo json_encode(['error' => true, 'message' => $e->getMessage()]);
}