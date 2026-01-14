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
        $idLoteExperimental = $_GET['loteExp'];

        switch ($miel) {
            case '1':
                $tambores = 'tamboresexperimentales';
                $almacen = 'almacen';
                $experimental = 'experimental';
                $traspaso = 'almacentraspaso';
                break;
            case '2':
                $tambores = 'tamboresexperimentales_organico';
                $almacen = 'almacen_organico';
                $experimental = 'experimental_organico';
                $traspaso = 'almacentraspaso_organico';
                break;
            case '5':
                $tambores = 'tamboresexperimentales_mantequilla';
                $almacen = 'almacen_mantequilla';
                $experimental = 'experimental_mantequilla';
                $traspaso = 'almacentraspaso_mantequilla';
                break;
            case '6':
                $tambores = 'tamboresexperimentales_altiplano';
                $almacen = 'almacen_altiplano';
                $experimental = 'experimental_altiplano';
                $traspaso = 'almacentraspaso_altiplano';
                break;
            case '7':
                $tambores = 'tamboresexperimentales_naranjo';
                $almacen = 'almacen_naranjo';
                $experimental = 'experimental_naranjo';
                $traspaso = 'almacentraspaso_naranjo';
                break;
            case '8':
                $tambores = 'tamboresexperimentales_aguacate';
                $almacen = 'almacen_aguacate';
                $experimental = 'experimental_aguacate';
                $traspaso = 'almacentraspaso_aguacate';
                break;
            case '9':
                $tambores = 'tamboresexperimentales_mezquite';
                $almacen = 'almacen_mezquite';
                $experimental = 'experimental_mezquite';
                $traspaso = 'almacentraspaso_mezquite';
                break;
        }
    }
    $con->beginTransaction();

    $sqlTblExp = "DELETE FROM $experimental WHERE idLoteExperimental = :idLoteExperimental";
    $datEx = $con->prepare($sqlTblExp);
    $datEx->bindParam(':idLoteExperimental', $idLoteExperimental);
    $datEx->execute();
    if ($datEx == false) {
        throw new Exception($con->errorInfo());
    } else {
        $sql = "DELETE FROM $tambores WHERE idLoteExperimental = :idLoteExperimental";
        $datTX = $con->prepare($sql);
        $datTX->bindParam(':idLoteExperimental', $idLoteExperimental);
        $datTX->execute();
        if ($datTX == false) {
            throw new Exception($con->errorInfo());
        } else {
            foreach ($info as $data) {
                if ($data->tipo == "0") {
                    $sqlAl = "UPDATE $almacen set estado = '0' WHERE idAlmacen = :idAlmacen";
                    $datos = $con->prepare($sqlAl);
                    $datos->bindParam(':idAlmacen', $data->folioTambor);
                    $datos->execute();
                    if ($datos == false) {
                        throw new Exception($con->errorInfo());
                    }
                } else if ($data->tipo == "1") {
                    $sqlAl = "UPDATE almacensobrantes SET estado = '0' WHERE consecutivo = :folioTambor AND sobrante = :clasificacion AND tipoDeMiel = :miel";
                    $datos = $con->prepare($sqlAl);
                    $datos->bindParam(':folioTambor', $data->folioTambor);
                    $datos->bindParam(':clasificacion', $data->clasificacion);
                    $datos->bindParam(':miel', $_GET['miel']);
                    $datos->execute();
                    if ($datos == false) {
                        throw new Exception($con->errorInfo());
                    }
                } else if ($data->tipo == "2") {
                    $sqlAl = "UPDATE $traspaso set estado = '0' WHERE idAlmacen = :idAlmacen";
                    $datos = $con->prepare($sqlAl);
                    $datos->bindParam(':idAlmacen', $data->folioTambor);
                    $datos->execute();
                    if ($datos == false) {
                        throw new Exception($con->errorInfo());
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
