<?php

include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO(); $con = $pdo->conectar();
$con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

function dameMielInicialAnual() {
    global $con;

    $sqlSelect = $con->prepare('SELECT existenciaPasada, importeAcumuladoPasado FROM saldoinicialinventario WHERE nombre = "miel" LIMIT 1;');
    $sqlSelect->execute();
    if ($sqlSelect == FALSE) {
        throw new Exception($con->errorInfo());
    } else {
        $data = $sqlSelect->fetch(PDO::FETCH_ASSOC);
        echo json_encode(['error'=>false, 'data'=>$data]);
    }

};

function guardarMielInicial(){
    global $con;
    $data = file_get_contents('php://input');
    if (!$data) {
        throw new Exception('No se recibieron los datos');
    } else {
        $mielInicialAnual = json_decode($data);
        $_replaceSigns = ['$', ',', 'M', 'X','N', 'm', 'x', 'n', ' '];
        $existenciaPasada = str_replace($_replaceSigns, '', $mielInicialAnual->existenciaPasada);
        $importeAcumuladoPasado = str_replace($_replaceSigns, '', $mielInicialAnual->importeAcumuladoPasado);
    }

    $sqlDelete = $con->prepare('DELETE FROM saldoinicialinventario WHERE nombre = "miel"');
    $sqlDelete->execute();
    if ($sqlDelete == FALSE) {
        throw new Exception($con->errorInfo());
    }

    $sqlInsert = $con->prepare("INSERT INTO saldoinicialinventario (existenciaPasada, importeAcumuladoPasado, nombre) VALUES (:existencia, :importe, 'miel')");
    $sqlInsert->bindParam(':existencia', $existenciaPasada);
    $sqlInsert->bindParam(':importe', $importeAcumuladoPasado);
    $sqlInsert->execute();
    if ($sqlInsert == FALSE) {
        throw new Exception($con->errorInfo());
    }


    echo json_encode(['error'=>false, 'message'=>'Se ha guardado los datos']);
}

try {
    if (!isset($_GET['opt'])) {
        throw new Exception('No se recibieron parámetros');
    } else {
        switch($_GET['opt']) {
            case 'get':
                dameMielInicialAnual();
                break;
            case 'set':
                guardarMielInicial();
                break;
            default:
                throw new Exception('Parámetros inválidos');
                break;
        }
    }
} catch (Exception $e) {
    echo json_encode(['error'=>true, 'message'=>$e->getMessage()]);
    exit();
}