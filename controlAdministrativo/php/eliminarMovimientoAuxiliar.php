<?php

include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$con = $pdo->conectar();
$postdata = file_get_contents('php://input');

try {

    $con->beginTransaction();

    if (!$postdata) {
        throw new Exception('No se recibieron los datos');
    }
    $datos = json_decode($postdata);

    if ($datos->tipoDePersona != '0'
        && $datos->nombreDe != '0'
        && $datos->tipoMovimiento != '0'
        && $datos->idSubcuenta
        && $datos->idSubcuenta != '0') {
            // Este es un auxiliar

        $query = $con->prepare("DELETE FROM auxiliardebancos WHERE idAuxiliar = :idAuxiliar");
        $query->bindParam(':idAuxiliar', $datos->idAuxiliar);
        $query->execute();

        if($datos->idPolizaCheque
        && $datos->idPolizaCheque != '0'){
            $query2 = $con->prepare("DELETE FROM polizacheque WHERE idPolizaCheque = :idPolizaCheque");
            $query2->bindParam(':idPolizaCheque', $datos->idPolizaCheque);
            $query2->execute();
            $query3 = $con->prepare("SELECT idCajaChica FROM cajachica WHERE idPolizaCheque = :idPolizaCheque");
            $query3->bindParam(':idPolizaCheque', $datos->idPolizaCheque);
            $query3->bindColumn('idCajaChica', $idCaja);
            $query3->execute();
            $query3->fetch(PDO::FETCH_BOUND);
            $query4 = $con->prepare("DELETE FROM cajachica WHERE idPolizaCheque = :idPolizaCheque");
            $query4->bindParam(':idPolizaCheque', $datos->idPolizaCheque);
            $query4->execute();
            $query5 = $con->prepare("DELETE FROM cajachicadetalle WHERE idCajaChica = :idCajaChica");
            $query5->bindParam(':idCajaChica', $idCaja);
            $query5->execute();
            $query6 = $con->prepare("DELETE FROM relaciondemovimientos WHERE idMovimiento = :idMovimiento");
            $query6->bindParam(':idMovimiento', $datos->idAuxiliar);
            $query6->execute();
        }

    }

    if ($datos->tipoDePersona != '0' && $datos->nombreDe != '0'
        && $datos->idTransferencia
        && $datos->idTransferencia == '1') {
        // Este es un movimiento de transferencia

        $consultaId = $con->prepare("SELECT ingreso, egreso FROM relaciondemovimientos WHERE tipoMovimiento = '4' AND egreso = :egreso");
        $consultaId->bindParam(':egreso', $datos->idAuxiliar);
        $consultaId->bindColumn('ingreso', $ingreso);
        $consultaId->bindColumn('egreso', $egreso);
        $consultaId->execute();

        if(!$consultaId) {
            throw new Exception($con->errorInfo());
        } else {
            $consultaId->fetch(PDO::FETCH_BOUND);

            $idIngreso = $ingreso;
            $idEgreso = $egreso;

        }

        $sqlEliminar = $con->prepare("DELETE FROM auxiliardebancos WHERE idAuxiliar = :ingreso;");
        $sqlEliminar->bindParam(':ingreso', $idIngreso);
        $sqlEliminar->execute();

        if(!$sqlEliminar) {
            throw new Exception($con->errorInfo());
        }

        $sqlEliminar2 = $con->prepare("DELETE FROM auxiliardebancos WHERE idAuxiliar = :egreso;");
        $sqlEliminar2->bindParam(':egreso', $idEgreso);
        $sqlEliminar2->execute();

        if(!$sqlEliminar2) {
            throw new Exception($con->errorInfo());
        }

    }

    $con->commit();
    echo json_encode(['error' => false, 'message' => 'Se ha eliminado el registro.']);
} catch (Exception $e) {
    $con->rollBack();
    echo json_encode(['error' => true, 'message' => $e->getMessage()]);
}
