<?php
include_once "../../../DAOConeccion/conePDO.php";
$pdo = new conePDO();
$con = $pdo->conectar();
$con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$json = file_get_contents("php://input");

$resultado = new stdClass();
try {
   
    if (!$json) {
        throw new Exception('No se recibieron los parámetros');
    } else {
        $datos = json_decode($json);
        $opcionCatalogo = $datos->opcionCatalogo;
        $id = $datos->id;
    }

    switch ($opcionCatalogo) {
        case '1': /* COMPRADORES */
            $consulta = "SELECT idcomprador AS id, nombre, telefono FROM compradores WHERE idcomprador = :id";
            $consulta = $con->prepare($consulta);
            $consulta->bindParam(':id', $id);
            $consulta->execute();
            if ($consulta == false) {
                throw new Exception($con->errorInfo());
            } else {
                $resultado = $consulta->fetch(PDO::FETCH_ASSOC);
                echo json_encode(['error' => false, 'message' => 'Consulta realizada', 'data' => $resultado]);
            }
            break;
        case '2': /* ZONAS */
            $consulta = "SELECT idzona AS id, zona as nombre, referencia FROM zonas WHERE idzona = :id";
            $consulta = $con->prepare($consulta);
            $consulta->bindParam(':id', $datos->id);
            $consulta->execute();
            if ($consulta == false) {
                throw new Exception($con->errorInfo());
            } else {
                $resultado = $consulta->fetch(PDO::FETCH_ASSOC);
                echo json_encode(['error' => false, 'message' => 'Consulta realizada', 'data' => $resultado]);
            }
            break;
        case '3': /* LOCALIDADES */
            $consulta = "SELECT idlocalidad AS id, localidad as nombre, idEstado FROM localidades WHERE idlocalidad =:id";
            $consulta = $con->prepare($consulta);
            $consulta->bindParam(':id', $datos->id);
            $consulta->execute();
            if ($consulta == false) {
                throw new Exception($con->errorInfo());
            } else {
                $resultado = $consulta->fetch(PDO::FETCH_ASSOC);
                echo json_encode(['error' => false, 'message' => 'Consulta realizada', 'data' => $resultado]);
            }
            break;
        case '4': /* TRANSPORTES */
            $consulta = "SELECT idTransporte AS id, transporte as nombre, marca, modelo, placas FROM transportes WHERE idTransporte =:id";
            $consulta = $con->prepare($consulta);
            $consulta->bindParam(':id', $datos->id);
            $consulta->execute();
            if ($consulta == false) {
                throw new Exception($con->errorInfo());
            } else {
                $resultado = $consulta->fetch(PDO::FETCH_ASSOC);
                echo json_encode(['error' => false, 'message' => 'Consulta realizada', 'data' => $resultado]);
            }
            break;
    }

} catch (Exception $e) {
    echo json_encode(['error' => true, 'message' => $e->getMessage()]);
}
