<?php
include_once "../../../DAOConeccion/conePDO.php";
$pdo = new conePDO();
$con = $pdo->conectar();
$con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$resultado = new stdClass();
try {
    if (!isset($_GET['parametro'])) {
        throw new Exception('Parámetro no especificado.');
    } else {
        $parametro = $_GET['parametro'];
    }

    switch ($parametro) {
        case '1':
            $consulta = "SELECT idcomprador AS id, nombre, telefono FROM compradores ORDER BY nombre ASC";
            $consulta = $con->prepare($consulta);
            $consulta->execute();
            if ($consulta == false) {
                throw new Exception($con->errorInfo());
            } else {
                $resultado = $consulta->fetchAll(PDO::FETCH_ASSOC);
                echo json_encode(['error' => false, 'message' => 'Consulta realizada', 'data' => $resultado]);
            }
            break;
        case '2':
            $consulta = "SELECT idzona AS id, zona as nombre, referencia FROM zonas
                        ORDER BY CONVERT(SUBSTR(zona FROM 6 FOR 2), UNSIGNED INTEGER) ASC";
            $consulta = $con->prepare($consulta);
            $consulta->execute();
            if ($consulta == false) {
                throw new Exception($con->errorInfo());
            } else {
                $resultado = $consulta->fetchAll(PDO::FETCH_ASSOC);
                echo json_encode(['error' => false, 'message' => 'Consulta realizada', 'data' => $resultado]);
            }
            break;
        case '3':
            $consulta = "SELECT l.idlocalidad AS id, l.localidad as nombre, e.estado 
            FROM localidades l LEFT JOIN estados e ON l.idEstado = e.idEstado
            ORDER BY nombre ASC";
            $consulta = $con->prepare($consulta);
            $consulta->execute();
            if ($consulta == false) {
                throw new Exception($con->errorInfo());
            } else {
                $resultado = $consulta->fetchAll(PDO::FETCH_ASSOC);
                echo json_encode(['error' => false, 'message' => 'Consulta realizada', 'data' => $resultado]);
            }
            break;
        case '4':
            $consulta = "SELECT idTransporte AS id, transporte as nombre FROM transportes ORDER BY nombre ASC";
            $consulta = $con->prepare($consulta);
            $consulta->execute();
            if ($consulta == false) {
                throw new Exception($con->errorInfo());
            } else {
                $resultado = $consulta->fetchAll(PDO::FETCH_ASSOC);
                echo json_encode(['error' => false, 'message' => 'Consulta realizada', 'data' => $resultado]);
            }
            break;
    }
} catch (Exception $e) {
    echo json_encode(['error' => true, 'message' => $e->getMessage()]);
}
