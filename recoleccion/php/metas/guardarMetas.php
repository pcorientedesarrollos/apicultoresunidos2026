<?php
include_once '../../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$con = $pdo->conectar();

$postdata = file_get_contents('php://input');

try {

    if (!$postdata) {
        throw new Exception('No se recibieron datos');
    } else {
        $infoMetas = json_decode($postdata);
        $miel = $_GET['miel'];
        switch ($miel) {
            case '1':
                $tabla_meta = 'metascompra';
                break;
            case '2':
                $tabla_meta = 'metascompra_organico';
                break;
        }
    }

    // iniciamos una transaccion

    $con->beginTransaction();

    foreach ($infoMetas as $zona) {

        $sqlVerifica = "SELECT idZona FROM $tabla_meta WHERE idZona = :idZona";
        $sqlVerifica = $con->prepare($sqlVerifica);
        $sqlVerifica->bindParam(':idZona', $zona->idzona);
        $sqlVerifica->execute();
        if ($sqlVerifica == false) {
            throw new Exception($con->errorInfo());
        }

        $respuesta = $sqlVerifica->fetch(PDO::FETCH_ASSOC);

        if ($respuesta == false) {
            $sqlInsert = "INSERT INTO $tabla_meta (idZona, tambores, kilogramos)
            VALUES (:idZona, :tambores, :kilogramos)";
            $queryInsert = $con->prepare($sqlInsert);
            $queryInsert->bindParam(':idZona', $zona->idzona);
            $queryInsert->bindParam(':tambores', $zona->tambores);
            $queryInsert->bindParam(':kilogramos', $zona->kilogramos);
            $queryInsert->execute();

            if (!$queryInsert) {
                throw new Exception($con->errorInfo());
            }
        } else {
            $sqlUpdate = "UPDATE $tabla_meta SET tambores = :tambores, kilogramos = :kilogramos
            WHERE idZona = :idZona";
            $queryUpdate = $con->prepare($sqlUpdate);
            $queryUpdate->bindParam(':tambores', $zona->tambores);
            $queryUpdate->bindParam(':kilogramos', $zona->kilogramos);
            $queryUpdate->bindParam(':idZona', $zona->idzona);
            $queryUpdate->execute();

            if (!$queryUpdate) {
                throw new Exception($con->errorInfo());
            }
        }
    }

    // Si llega a este punto, es que no hubo algun error

    $con->commit();
    echo json_encode(['error' => false, 'message' => 'Registros guardados']);
} catch (Exception $e) {
    $con->rollBack();
    echo json_encode(['error' => true, 'message' => $e->getMessage()]);
}
