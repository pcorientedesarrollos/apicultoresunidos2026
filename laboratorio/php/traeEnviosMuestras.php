<?php

include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO;
$con = $pdo->conectar();

$tipoDeMiel = file_get_contents('php://input');
$resultado = [];
try {
    if (!$tipoDeMiel) {
        throw new Exception('Se esperaban argumentos');
    }
    switch ($tipoDeMiel) {
        case '1':
            $tabla = 'enviomuestras';
            break;
        case '2':
            $tabla = 'enviomuestras_organico';
            break;
        default:
            throw new Exception('Argumento inválido');
            break;
    }

    $sql = "SELECT idEnvio, fechaEnvio, lugarEnvio, cliente, guia, marcaLaboratorio FROM $tabla ORDER BY idEnvio DESC";
    $datos = $con->prepare($sql);
    $datos->execute();
    if ($datos == FALSE) {
        throw new Exception($con->errorInfo());
    }

    $resultado = $datos->fetchAll(PDO::FETCH_ASSOC);

    // foreach ($datos->fetchAll(PDO::FETCH_ASSOC) as $dato) {
    //     if ($dato['tipoDeCliente'] == '10') {
    //         $query = "SELECT *
    //      FROM clientesexportadores
    //      WHERE idClienteExportador = :idClienteExportador";
    //         $datosResp = $con->prepare($query);
    //         $datosResp->bindParam(':idClienteExportador', $dato['idCliente']);
    //         $datosResp->execute();
    //         $dato['cliente'] = $datosResp->fetch(PDO::FETCH_ASSOC);
    //     } else if ($dato['tipoDeCliente'] == '6') {
    //         $seleccionarClientes = $con->prepare("SELECT nombre FROM clientes WHERE idCliente = :idCliente");
    //         $seleccionarClientes->bindParam(':idCliente', $dato['idCliente']);
    //         $seleccionarClientes->execute();
    //         $dato['cliente'] = $seleccionarClientes->fetch(PDO::FETCH_ASSOC);
    //     }
    //     array_push($resultado, $dato);
    // }


    echo json_encode(['error' => false, 'message' => '', 'data' => $resultado]);
} catch (Exception $e) {
    echo json_encode(['error' => true, 'message' => $e->getMessage() . '. Line ' . $e->getLine(), 'data' => $resultado]);
}
