<?php

include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$con = $pdo->conectar();
$postdata = file_get_contents('php://input');

function returnResponse($n, $text = '')
{
    switch ($n) :
        case 1:
        echo json_encode(['error' => true, 'message' => 'No se recibieron datos', 'swal' => 'error']);
        break;
    case 2:
        echo json_encode(['error' => true, 'message' => 'El registro no existe o ya ha sido eliminado, actualice la página.', 'swal' => 'warning']);
        break;
    case 3:
        echo json_encode(['error' => true, 'message' => $text, 'swal' => 'error']);
        break;
    case 4:
        echo json_encode(['error' => false, 'message' => 'El registro ha sido eliminado', 'swal' => 'success']);
        break;
    endswitch;
    exit();
}

if ($postdata) {
    $sql_selectItem = $con->prepare("SELECT idCajaChica FROM cajachica WHERE idCajaChica = $postdata");
    $sql_selectItem->execute();
    if ($sql_selectItem->rowCount() > 1) {
        returnResponse(2);
    }
    try {
        $con->beginTransaction();
        // Eliminar el encabezado y detalle
        $sql_deleteEncabezado = $con->prepare("DELETE FROM cajachica WHERE idCajaChica = $postdata");
        $sql_deleteEncabezado->execute();
        if ($sql_deleteEncabezado->rowCount() > 1) {
            throw new Exception('No se ha podido eliminar el encabezado del movimiento');
        }

        $sql_deleteDetalle = $con->prepare("DELETE FROM cajachicadetalle WHERE idCajaChica = $postdata");
        $sql_deleteDetalle->execute();
        
        // Eliminarlo de la tabla de relaciones, si es que existe algún registro

        $sql_selectFromRelaciones = $con->prepare("SELECT idMovimiento FROM relaciondemovimientos WHERE cajaChica = $postdata");
        $sql_selectFromRelaciones->execute();
        if ($sql_selectFromRelaciones->rowCount() >= 1) {
            foreach ($sql_selectFromRelaciones->fetchAll(PDO::FETCH_ASSOC) as $mov) {
                $sql_deleteAuxiliar = $con->prepare("DELETE FROM auxiliardebancos WHERE idAuxiliar = :idAuxiliar");
                $sql_deleteAuxiliar->bindParam(':idAuxiliar', $mov['idMovimiento']);
                $sql_deleteAuxiliar->execute();
                if ($sql_deleteAuxiliar->rowCount() > 1) {
                    throw new Exception('No se ha podido eliminar la relación con Auxiliar de bancos');
                }
            }
        }
        $con->commit();
        returnResponse(4);
    } catch (Exception $e) {
        $con->rollBack();
        returnResponse(3, $e->getMessage() . '. Line ' . $e->getLine());
    }
} else {
    returnResponse(1);
}
