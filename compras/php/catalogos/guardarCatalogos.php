<?php
include_once '../../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$con = $pdo->conectar();
$post = file_get_contents('php://input');

try {
    if (!$post) {
        throw new Exception('No se recibieron datos');
    } else {
        $datos = json_decode($post);
    }

    switch ($datos->opcionCatalogo) {
        case '1': /* COMPRADORES */
            if (isset($datos->id)) {
                $success_message = 'Se ha editado el registro';
                $sqlInsert = $con->prepare("UPDATE compradores SET nombre = :nombre, telefono = :telefono WHERE idcomprador = :id");
                $sqlInsert->bindParam(':id', $datos->id);
            } else {
                $success_message = 'Se ha agregado un nuevo comprador';
                $sqlInsert = $con->prepare("INSERT INTO compradores (nombre, telefono, estado) VALUES (:nombre, :telefono, '1')");
            }

            $sqlInsert->bindParam(':nombre', $datos->nombre);
            $sqlInsert->bindParam(':telefono', $datos->telefono);
            $sqlInsert->execute();

            if ($sqlInsert == false) {
                throw new Exception($con->errorInfo());
            }
            echo json_encode(['error' => false, 'message' => $success_message]);
            break;
        case '2': /* ZONAS */
            if (isset($datos->id)) {
                $success_message = 'Se ha editado el registro';
                $sqlInsert = $con->prepare("UPDATE zonas SET zona = :nombre, referencia = :referencia WHERE idzona = :id");
                $sqlInsert->bindParam(':id', $datos->id);
            } else {
                $success_message = 'Se ha agregado una nueva zona';
                $sqlInsert = $con->prepare("INSERT INTO zonas (zona, idcomprador, estado, referencia) VALUES (:nombre, '', '1', :referencia)");
            }

            $sqlInsert->bindParam(':nombre', $datos->nombre);
            $sqlInsert->bindParam(':referencia', $datos->referencia);
            $sqlInsert->execute();

            if ($sqlInsert == false) {
                throw new Exception($con->errorInfo());
            }
            echo json_encode(['error' => false, 'message' => $success_message]);
            break;
        case '3': /* LOCALIDADES */
            if (isset($datos->id)) {
                $success_message = 'Se ha editado el registro';
                $sqlInsert = $con->prepare("UPDATE localidades SET localidad = :nombre, idEstado = :idEstado WHERE idlocalidad = :id");
                $sqlInsert->bindParam(':id', $datos->id);
            } else {
                $success_message = 'Se ha agregado una nueva localidad';
                $sqlInsert = $con->prepare("INSERT INTO localidades (localidad, idzona, estado, idEstado) VALUES (:nombre, '', '1', :idEstado)");
            }

            $sqlInsert->bindParam(':nombre', $datos->nombre);
            $sqlInsert->bindParam(':idEstado', $datos->idEstado);
            $sqlInsert->execute();

            if ($sqlInsert == false) {
                throw new Exception($con->errorInfo());
            }
            echo json_encode(['error' => false, 'message' => $success_message]);
            break;
        case '4': /* TRANSPORTES */
            if (isset($datos->id)) {
                $success_message = 'Se ha editado el registro';
                $sqlInsert = $con->prepare("UPDATE transportes SET transporte = :nombre, marca = :marca, modelo = :modelo, placas = :placas WHERE idTransporte = :id");
                $sqlInsert->bindParam(':id', $datos->id);
            } else {
                $success_message = 'Se ha agregado un nuevo transporte';
                $sqlInsert = $con->prepare("INSERT INTO transportes (transporte, marca, modelo, placas) VALUES (:nombre, :marca, :modelo, :placas)");
            }

            $sqlInsert->bindParam(':nombre', $datos->nombre);
            $sqlInsert->bindParam(':marca', $datos->marca);
            $sqlInsert->bindParam(':modelo', $datos->modelo);
            $sqlInsert->bindParam(':placas', $datos->placas);
            $sqlInsert->execute();

            if ($sqlInsert == false) {
                throw new Exception($con->errorInfo());
            }
            echo json_encode(['error' => false, 'message' => $success_message]);
            break;
    }
} catch (Exception $e) {
    echo json_encode(['error' => true, 'message' => $e->getMessage()]);
}
