<?php

include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$con = $pdo->conectar();

$json = file_get_contents("php://input");

try {

    if (!$json) {
        throw new Exception('No se recibieron parámetros');
    } else {
        $datos = json_decode($json);
        $info = $datos->valor;
        $miel = $_GET['miel'];
        $idLoteInterno = $_GET['idLoteInterno'];
        $idLoteExperimental = $_GET['idLoteExperimental'];
        switch ($miel) {
            case '1':
                $calidad = 'calidad';
                $tamboresLote = 'tamboreslotes';
                $almacen = 'almacen';
                $experimental = 'experimental';
                $tamboresExperimentales = 'tamboresexperimentales';
                $traspaso = 'almacentraspaso';
                break;
            case '2':
                $calidad = 'calidad_organico';
                $tamboresLote = 'tamboreslotes_organico';
                $almacen = 'almacen_organico';
                $experimental = 'experimental_organico';
                $tamboresExperimentales = 'tamboresexperimentales_organico';
                $traspaso = 'almacentraspaso_organico';
                break;
            case '5':
                $calidad = 'calidad_mantequilla';
                $tamboresLote = 'tamboreslotes_mantequilla';
                $almacen = 'almacen_mantequilla';
                $experimental = 'experimental_mantequilla';
                $tamboresExperimentales = 'tamboresexperimentales_mantequilla';
                $traspaso = 'almacentraspaso_mantequilla';
                break;
            case '6':
                $calidad = 'calidad_altiplano';
                $tamboresLote = 'tamboreslotes_altiplano';
                $almacen = 'almacen_altiplano';
                $experimental = 'experimental_altiplano';
                $tamboresExperimentales = 'tamboresexperimentales_altiplano';
                $traspaso = 'almacentraspaso_altiplano';
                break;
            case '7':
                $calidad = 'calidad_naranjo';
                $tamboresLote = 'tamboreslotes_naranjo';
                $almacen = 'almacen_naranjo';
                $experimental = 'experimental_naranjo';
                $tamboresExperimentales = 'tamboresexperimentales_naranjo';
                $traspaso = 'almacentraspaso_naranjo';
                break;
                
            case '8':
                $calidad = 'calidad_aguacate';
                $tamboresLote = 'tamboreslotes_aguacate';
                $almacen = 'almacen_aguacate';
                $experimental = 'experimental_aguacate';
                $tamboresExperimentales = 'tamboresexperimentales_aguacate';
                $traspaso = 'almacentraspaso_aguacate';
                break;
            case '9':
                $calidad = 'calidad_mezquite';
                $tamboresLote = 'tamboreslotes_mezquite';
                $almacen = 'almacen_mezquite';
                $experimental = 'experimental_mezquite';
                $tamboresExperimentales = 'tamboresexperimentales_mezquite';
                $traspaso = 'almacentraspaso_mezquite';
                break;
        }
    }
    $con->beginTransaction();

    $sqlEliminarLote = "UPDATE $calidad SET idLoteExperimental = '', fechaProceso = '', fechaEnvasado = '', loteCliente = '', marcaFinalCliente = '', observaciones = '', muestraInterna = '', numeroDeTambores = '0', kilosTotales = '0' WHERE idLoteInterno = :idLoteInterno";
    $respEliminarLote = $con->prepare($sqlEliminarLote);
    $respEliminarLote->bindParam(':idLoteInterno', $idLoteInterno);
    $respEliminarLote->execute();
    if ($respEliminarLote == false) {
        throw new Exception($con->errorInfo());
    } else {
        $sqlEliminarTambores = "DELETE FROM $tamboresLote WHERE idLoteInterno = :idLoteInterno";
        $respEliminarTambores = $con->prepare($sqlEliminarTambores);
        $respEliminarTambores->bindParam(':idLoteInterno', $idLoteInterno);
        $respEliminarTambores->execute();
        if ($respEliminarTambores == false) {
            throw new Exception($con->errorInfo());
        } else {
            $sqlEliminarExperimental = "DELETE FROM $experimental WHERE idLoteExperimental = :idLoteExperimental";
            $respEliminarExperimental = $con->prepare($sqlEliminarExperimental);
            $respEliminarExperimental->bindParam(':idLoteExperimental', $idLoteExperimental);
            $respEliminarExperimental->execute();
            if ($respEliminarExperimental == false) {
                throw new Exception($con->errorInfo());
            } else {
                $sqlEliminarTambores = "DELETE FROM $tamboresExperimentales WHERE idLoteExperimental = :idLoteExperimental";
                $respEliminarTambores = $con->prepare($sqlEliminarTambores);
                $respEliminarTambores->bindParam(':idLoteExperimental', $idLoteExperimental);
                $respEliminarTambores->execute();
                if ($respEliminarTambores == false) {
                    throw new Exception($con->errorInfo());
                } else {
                    foreach ($info as $data) {
                        if ($data->tipo == "0") {
                            $sqlActualizaAlmacen = "UPDATE $almacen set estado = '0' WHERE idAlmacen = :idAlmacen";
                            $respAlmacen = $con->prepare($sqlActualizaAlmacen);
                            $respAlmacen->bindParam(':idAlmacen', $data->folioTambor);
                            $respAlmacen->execute();
                            if ($respAlmacen == false) {
                                throw new Exception($con->errorInfo());
                            }
                        } else if ($data->tipo == "1") {
                            $sqlSobrante = "UPDATE almacensobrantes SET estado = '0' WHERE consecutivo = :folioTambor AND sobrante = :clasificacion AND tipoDeMiel = :miel";
                            $respSobrante = $con->prepare($sqlSobrante);
                            $respSobrante->bindParam(':folioTambor', $data->folioTambor);
                            $respSobrante->bindParam(':clasificacion', $data->clasificacion);
                            $respSobrante->bindParam(':miel', $miel);
                            $respSobrante->execute();
                            if ($respSobrante == false) {
                                throw new Exception($con->errorInfo());
                            }
                        } else if ($data->tipo == "2") {
                            $sqlActualizaAlmacenT = "UPDATE $traspaso set estado = '0' WHERE idAlmacen = :idAlmacen";
                            $respAlmacen1 = $con->prepare($sqlActualizaAlmacenT);
                            $respAlmacen1->bindParam(':idAlmacen', $data->folioTambor);
                            $respAlmacen1->execute();
                            if ($respAlmacen1 == false) {
                                throw new Exception($con->errorInfo());
                            }
                        }
                    }
                }
            }
        }
    }
    $con->commit();
    echo json_encode(['error' => false, 'info' => $info]);
} catch (Exception $e) {
    $con->rollBack();
    echo json_encode(['error' => true, 'message' => $e->getMessage() . ' Linea: ' . $e->getLine()]);
}

exit();
