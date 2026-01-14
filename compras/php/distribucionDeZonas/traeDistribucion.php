<?php


include_once '../../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$con = $pdo->conectar();
$con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

try {

    if (!isset($_GET['id'])) {
        throw new Exception('No re recibieron parámetros');
    } else {
        $id = $_GET['id'];
    }

    $sql = "SELECT z.idzona, c.idcomprador
    FROM zonas z 
    LEFT JOIN compradores c ON c.idcomprador = z.idcomprador
    WHERE idzona = :id";
    $datos = $con->prepare($sql);
    $datos->bindParam(':id', $id);
    $datos->execute();
    if ($datos == false) {
        throw new Exception($con->errorInfo());
    } else {
        // $resultado = new stdClass();
        $resultado = $datos->fetch(PDO::FETCH_ASSOC);
        $sql1 = "SELECT idlocalidad FROM localidades WHERE idzona = :id";
        $sql1 = $con->prepare($sql1);
        $sql1->bindParam(':id', $id);
        $sql1->execute();
        $resultadoLocalidades = $sql1->fetchAll(PDO::FETCH_ASSOC);
        $resultado['idlocalidad'] = array_map(function($objLocalidad){
            return $objLocalidad['idlocalidad'];
        }, $resultadoLocalidades);
    
        echo json_encode(['error' => false, 'message' => 'Consulta realizada', 'data' => $resultado]);
    }

} catch (Exception $e) {
    echo json_encode(['error' => true, 'message' => $e->getMessage(), 'data' => []]);
    exit();
}
