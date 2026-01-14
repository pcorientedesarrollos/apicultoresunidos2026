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
    $informacion = json_decode($postdata);

    $valor = $_GET["valor"];

    if ($valor == 1) {

        $verificaSubcuentas = $con->prepare("SELECT COUNT(idSubcuenta) AS registros FROM subcuentas WHERE idCuentaConcepto = :idCuentaConcepto");
        $verificaSubcuentas->bindParam(':idCuentaConcepto', $informacion->idCuentaConcepto);
        $verificaSubcuentas->bindColumn('registros', $registros);
        $verificaSubcuentas->execute();

        if(!$verificaSubcuentas) {
            throw new Exception($con->errorInfo());
        } else {
            $verificaSubcuentas->fetch(PDO::FETCH_BOUND);
            $registrosCuentas = $registros;
            if($registrosCuentas > 0){
                $selectSub = $con->prepare("SELECT idSubcuenta FROM subcuentas WHERE idCuentaConcepto = :idCuentaConcepto");
                $selectSub->bindParam(':idCuentaConcepto', $informacion->idCuentaConcepto);
                $selectSub->execute();
                if(!$selectSub) {
                    throw new Exception($con->errorInfo());
                }
        
                foreach($selectSub->fetchAll(PDO::FETCH_ASSOC) as $subcuentas){
                    $verificaSubSub = $con->prepare("SELECT COUNT(idSubSubcuenta) AS registrosSub FROM subsubcuentas WHERE idSubcuenta = :idSubcuenta");
                    $verificaSubSub->bindParam(':idSubcuenta', $subcuentas['idSubcuenta']);
                    $verificaSubSub->bindColumn('registrosSub', $registrosSub);
                    $verificaSubSub->execute();
                    if(!$verificaSubSub){
                        throw new Exception($con->errorInfo());
                    }else{
                        $verificaSubSub->fetch(PDO::FETCH_BOUND);
                        $registrosSubSub = $registrosSub;
                        if($registrosSubSub > 0){                             
                            $eliminarSubSubcuentas = "DELETE FROM subsubcuentas WHERE idSubcuenta = :idSubcuenta";
                            $eliminarExc = $con->prepare($eliminarSubSubcuentas);
                            $eliminarExc->bindParam(':idSubcuenta', $subcuentas['idSubcuenta']);
                            $eliminarExc->execute();
                            if(!$eliminarExc) {
                                throw new Exception($con->errorInfo());
                            }
                        }
                    }
                }  
                $eliminarSubcuentas = "DELETE FROM subcuentas WHERE idCuentaConcepto = :idCuentaConcepto";
                $eliminarSubCuenta = $con->prepare($eliminarSubcuentas);
                $eliminarSubCuenta->bindParam(':idCuentaConcepto', $informacion->idCuentaConcepto);
                $eliminarSubCuenta->execute();
                if(!$eliminarSubCuenta) {
                    throw new Exception($con->errorInfo());
                }
            }
        }    
        
        $sqlEliminar = "DELETE FROM cuentas WHERE idCuentaConcepto = :idCuentaConcepto";
        $datos = $con->prepare($sqlEliminar);
        $datos->bindParam(':idCuentaConcepto', $informacion->idCuentaConcepto);
        $datos->execute();
        if(!$datos) {
            throw new Exception($con->errorInfo());
        }
        
    }else if($valor == 2){

        $verificaSubSub = $con->prepare("SELECT COUNT(idSubSubcuenta) AS registrosSub FROM subsubcuentas WHERE idSubcuenta = :idSubcuenta");
        $verificaSubSub->bindParam(':idSubcuenta', $informacion->idSubcuenta);
        $verificaSubSub->bindColumn('registrosSub', $registrosSub);
        $verificaSubSub->execute();
        if(!$verificaSubSub){
            throw new Exception($con->errorInfo());
        }else{
            $verificaSubSub->fetch(PDO::FETCH_BOUND);
            $registrosSubSub = $registrosSub;
            if($registrosSubSub > 0){                             
                $eliminarSubSubcuentas = "DELETE FROM subsubcuentas WHERE idSubcuenta = :idSubcuenta";
                $eliminarExc = $con->prepare($eliminarSubSubcuentas);
                $eliminarExc->bindParam(':idSubcuenta', $informacion->idSubcuenta);
                $eliminarExc->execute();
                if(!$eliminarExc) {
                    throw new Exception($con->errorInfo());
                }
            }
        }

        $sql = "DELETE FROM subcuentas WHERE idSubcuenta = :idSubcuenta";
        $datos = $con->prepare($sql);
        $datos->bindParam(':idSubcuenta', $informacion->idSubcuenta);
        $datos->execute();
    }else if($valor == 3){
        $sql = "DELETE FROM subsubcuentas WHERE idSubSubcuenta = :idSubSubcuenta";
        $datos = $con->prepare($sql);
        $datos->bindParam(':idSubSubcuenta', $informacion->idSubSubcuenta);
        $datos->execute();
    }

    // if ($datos->tipoDePersona != '0' && $datos->nombreDe != '0'
    //     && $datos->idTransferencia
    //     && $datos->idTransferencia == '1') {
    //     // Este es un movimiento de transferencia

    //     $consultaId = $con->prepare("SELECT ingreso, egreso FROM relaciondemovimientos WHERE tipoMovimiento = '4' AND egreso = :egreso");
    //     $consultaId->bindParam(':egreso', $datos->idAuxiliar);
    //     $consultaId->bindColumn('ingreso', $ingreso);
    //     $consultaId->bindColumn('egreso', $egreso);
    //     $consultaId->execute();

    //     if(!$consultaId) {
    //         throw new Exception($con->errorInfo());
    //     } else {
    //         $consultaId->fetch(PDO::FETCH_BOUND);

    //         $idIngreso = $ingreso;
    //         $idEgreso = $egreso;

    //     }

    //     $sqlEliminar = $con->prepare("DELETE FROM auxiliardebancos WHERE idAuxiliar = :ingreso;");
    //     $sqlEliminar->bindParam(':ingreso', $idIngreso);
    //     $sqlEliminar->execute();

    //     if(!$sqlEliminar) {
    //         throw new Exception($con->errorInfo());
    //     }

    //     $sqlEliminar2 = $con->prepare("DELETE FROM auxiliardebancos WHERE idAuxiliar = :egreso;");
    //     $sqlEliminar2->bindParam(':egreso', $idEgreso);
    //     $sqlEliminar2->execute();

    //     if(!$sqlEliminar2) {
    //         throw new Exception($con->errorInfo());
    //     }

    // }

    // if ($datos->tipoDePersona != '0' && $datos->nombreDe != '0'
    //     && $datos->idPolizaCheque
    //     && $datos->idPolizaCheque != '0') {
    //     // Este es una póliza de cheque

    //     $query = $con->prepare("SELECT poliza FROM relaciondemovimientos WHERE idMovimiento = :idMovimiento");
    //     $query->bindParam(':idMovimiento', $datos->idAuxiliar);
    //     $query->bindColumn('poliza', $poliza);   
    //     $query->execute();
    //     $query->fetch(PDO::FETCH_BOUND);
    //     $query1 = $con->prepare("DELETE FROM auxiliardebancos WHERE idAuxiliar = :idAuxiliar");
    //     $query1->bindParam(':idAuxiliar', $datos->idAuxiliar);
    //     $query1->execute();
    //     $query2 = $con->prepare("DELETE FROM polizacheque WHERE idPolizaCheque = :idPolizaCheque");
    //     $query2->bindParam(':idPolizaCheque', $poliza);
    //     $query2->execute();
    //     $query3 = $con->prepare("SELECT idCajaChica FROM cajachica WHERE idPolizaCheque = :idPolizaCheque");
    //     $query3->bindParam(':idPolizaCheque', $poliza);
    //     $query3->bindColumn('idCajaChica', $idCaja);
    //     $query3->execute();
    //     $query3->fetch(PDO::FETCH_BOUND);
    //     $query4 = $con->prepare("DELETE FROM cajachica WHERE idPolizaCheque = :idPolizaCheque");
    //     $query4->bindParam(':idPolizaCheque', $poliza);
    //     $query4->execute();
    //     $query5 = $con->prepare("DELETE FROM cajachicadetalle WHERE idCajaChica = :idCajaChica");
    //     $query5->bindParam(':idCajaChica', $idCaja);
    //     $query5->execute();
    // }

    $con->commit();
    echo json_encode(['error' => false, 'message' => 'Se ha editado el registro.']);
} catch (Exception $e) {
    $con->rollBack();
    echo json_encode(['error' => true, 'message' => $e->getMessage()]);
}
