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
            $calidad_tabla = 'calidad';
            $certificadoEncabezado = 'certificadoloteterminado_encabezado';
            break;
        case '2':
            $calidad_tabla = 'calidad_organico';
            $certificadoEncabezado = 'certificadoloteterminado_encabezado_organico';
            break;
        case '5':
            $calidad_tabla = 'calidad_mantequilla';
            $certificadoEncabezado = 'certificadoloteterminado_encabezado_mantequilla';
            break;    
        case '6':
            $calidad_tabla = 'calidad_altiplano';
            $certificadoEncabezado = 'certificadoloteterminado_encabezado_altiplano';
            break;      
        case '7':
            $calidad_tabla = 'calidad_naranjo';
            $certificadoEncabezado = 'certificadoloteterminado_encabezado_naranjo';
            break;    
        case '8':
            $calidad_tabla = 'calidad_aguacate';
            $certificadoEncabezado = 'certificadoloteterminado_encabezado_aguacate';
            break;      
        case '9':
            $calidad_tabla = 'calidad_mezquite';
            $certificadoEncabezado = 'certificadoloteterminado_encabezado_mezquite';
            break; 
        default:
            throw new Exception('Argumento inválido');
            break;
    }

    $sql = "SELECT cc.idEncabezado, cc.fechaCalidad, cc.cliente, cc.tipoDeCliente, cc.loteCalidad, c.idLoteInterno 
     FROM $certificadoEncabezado cc
     LEFT JOIN $calidad_tabla c ON c.idLoteInterno = cc.idLote";

    if (isset($_GET['mes'])) {
        $mes = $_GET['mes'];
        $sql .= " WHERE SUBSTR(cc.fechaCalidad FROM 6 FOR 2) =" . $mes;
    }

    $sql .= " ORDER BY c.idLoteInterno DESC ";

    $datos = $con->prepare($sql);
    $datos->execute();
    if ($datos == FALSE) {
        throw new Exception($con->errorInfo());
    }

    // $resultado = $datos->fetchAll(PDO::FETCH_ASSOC);

    foreach ($datos->fetchAll(PDO::FETCH_ASSOC) as $dato) {
        if ($dato['tipoDeCliente'] == '10') {
            $query = "SELECT *
         FROM clientesexportadores
         WHERE idClienteExportador = :idClienteExportador";
            $datosResp = $con->prepare($query);
            $datosResp->bindParam(':idClienteExportador', $dato['cliente']);
            $datosResp->execute();
            $dato['cliente'] = $datosResp->fetch(PDO::FETCH_ASSOC);
        } else if ($dato['tipoDeCliente'] == '6') {
            $seleccionarClientes = $con->prepare("SELECT nombre FROM clientes WHERE idCliente = :idCliente");
            $seleccionarClientes->bindParam(':idCliente', $dato['cliente']);
            $seleccionarClientes->execute();
            $dato['cliente'] = $seleccionarClientes->fetch(PDO::FETCH_ASSOC);
        }
        array_push($resultado, $dato);
    }


    echo json_encode(['error' => false, 'message' => '', 'data' => $resultado]);
} catch (Exception $e) {
    echo json_encode(['error' => true, 'message' => $e->getMessage() . '. Line ' . $e->getLine(), 'data' => $resultado]);
}
