<?php
include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$con = $pdo->conectar();
$post = file_get_contents('php://input');

try {
    if (!$post) {
        throw new Exception('No se recibieron datos');
    } else {
        $nuevaOpcion = json_decode($post);
    }

    if (isset($nuevaOpcion->idConcepto)) {
        $success_message = 'Se ha editado el registro';
        $sqlInsert = $con->prepare("UPDATE otrosconceptossalida SET concepto = :concepto WHERE idConcepto = :idConcepto");
        $sqlInsert->bindParam(':idConcepto', $nuevaOpcion->idConcepto);

    } else {
        $success_message = 'Se ha agregado una nueva opción';
        $sqlInsert = $con->prepare("INSERT INTO otrosconceptossalida (concepto) VALUES (:concepto)");
    }

    $sqlInsert->bindParam(':concepto', $nuevaOpcion->concepto);
    $sqlInsert->execute();

    if ($sqlInsert == false) {
        throw new Exception($con->errorInfo());
    }
    echo json_encode(['error' => false, 'message' => $success_message]);

} catch (Exception $e) {
    echo json_encode(['error' => true, 'message' => $e->getMessage()]);
}

