<?php

include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$con = $pdo->conectar();

try {

    $resultado = array(
        'seccion' => new stdClass(),
        'modulos' => array()
    );

    if (!isset($_GET['idSeccion'])) {
        throw new Exception('No se especificó la sección');
    } else {
        $idSeccion = $_GET['idSeccion'];
    }

    $sqlSeccion = $con->prepare("SELECT * FROM secciones WHERE idSeccion = :idSeccion");
    $sqlSeccion->bindParam(':idSeccion', $idSeccion);
    $sqlSeccion->execute();
    if ($sqlSeccion == false) {
        throw new Exception($con->errorInfo());
    }

    $resultado['seccion'] = $sqlSeccion->fetch(PDO::FETCH_ASSOC);


    $sqlModulo = $con->prepare("SELECT * FROM modulos WHERE idSeccion = :idSeccion");
    $sqlModulo->bindParam(':idSeccion', $idSeccion);
    $sqlModulo->execute();

    if ($sqlModulo == false) {
        throw new Exception($con->errorInfo());
    }

    $resultado['modulos'] = $sqlModulo->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode(['error' => false, 'resultado' => $resultado]);

} catch (Exception $e) {
    echo json_encode(['error' => true, 'message' => $e->getMessage()]);
}
