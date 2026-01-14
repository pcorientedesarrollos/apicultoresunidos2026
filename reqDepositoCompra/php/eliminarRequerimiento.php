<?php
include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$con = $pdo->conectar();

try {

    $con->beginTransaction();

    $idDetalle = $_GET["idDetalle"];
    $idRequisicion = $_GET["idRequisicion"];

    $sqlEliminar = $con->prepare("DELETE FROM requisiciondetalle WHERE idDetalle = :idDetalle AND cobrado = '0'");
    $sqlEliminar->bindParam(':idDetalle', $idDetalle);
    $sqlEliminar->execute();
    if (!$sqlEliminar) {
        throw new Exception($con->errorInfo());
    } else {

        $sqlConsulta = $con->prepare("SELECT SUM(noTambores) AS totalTambores, SUM(peso) AS totalKilos, SUM(importe) AS importeTotal FROM requisiciondetalle WHERE idRequisicion = :idRequisicion");
        $sqlConsulta->bindParam(':idRequisicion', $idRequisicion);
        $sqlConsulta->execute();
        $sqlConsulta->bindColumn('totalTambores', $totalTambores);
        $sqlConsulta->bindColumn('totalKilos', $totalKilos);
        $sqlConsulta->bindColumn('importeTotal', $importeTotal);

        if (!$sqlConsulta) {
            throw new Exception($con->errorInfo());
        } else {
            $sqlConsulta->fetch(PDO::FETCH_BOUND);
        }

        $sqlUpdate = $con->prepare("UPDATE requisicionencabezado SET totalTambores = :totalTambores, totalKilos = :totalKilos, importeTotal = :importeTotal WHERE idRequisicion = :idRequisicion");
        $sqlUpdate->bindParam(':idRequisicion', $idRequisicion);
        $sqlUpdate->bindParam(':totalTambores', $totalTambores);
        $sqlUpdate->bindParam(':totalKilos', $totalKilos);
        $sqlUpdate->bindParam(':importeTotal', $importeTotal);
        $sqlUpdate->execute();
    }

    $con->commit();
    echo json_encode(['error' => false, 'message' => 'Se ha editado el registro.']);
} catch (Exception $e) {
    $con->rollBack();
    echo json_encode(['error' => true, 'message' => $e->getMessage()]);
}
