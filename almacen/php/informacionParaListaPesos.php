<?php

include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$con = $pdo->conectar();

try {

    if (!isset($_GET['lote']) || !isset($_GET['tipoDeMiel'])) {
        throw new Exception($con->errorInfo());
    } else {
        $lote = $_GET['lote'];
        $tipoDeMiel = $_GET['tipoDeMiel'];
    }

    // $entradaysalida_tabla = $tipoDeMiel == '1'
    //     ? 'entradaysalida'
    //     : 'entradaysalida_organico';


    switch($tipoDeMiel){
        case '1':
            $entradaysalida_tabla = 'entradaysalida';
            break;
        case '2':
            $entradaysalida_tabla = 'entradaysalida_organico';
            break;
        case '5':
            $entradaysalida_tabla = 'entradaysalida_mantequilla';
            break;
        case '6':
            $entradaysalida_tabla = 'entradaysalida_altiplano';
            break;
        case '7':
            $entradaysalida_tabla = 'entradaysalida_naranjo';
            break;
        case '8':
            $entradaysalida_tabla = 'entradaysalida_aguacate';
            break;
        case '9':
            $entradaysalida_tabla = 'entradaysalida_mezquite';
            break;
    }


    $query = "SELECT rc.fechaImpresion, rc.marca, rc.modelo, rc.contenedor, rc.sello, rc.idLoteInterno,
        o.operador, o.compania, p.placa
        FROM $entradaysalida_tabla rc
        LEFT JOIN choferes o ON o.idOperador = rc.idOperador 
        LEFT JOIN placaschoferes p ON p.idPlaca = rc.idPlaca
        WHERE rc.lote = :lote";
    $datos = $con->prepare($query);
    $datos->bindParam(':lote', $lote);
    $datos->execute();

    if ($datos == false) {
        throw new Exception($con->errorInfo());
    } else {
        $informacion = $datos->fetch(PDO::FETCH_ASSOC);
    }

    echo json_encode(['error' => false, 'info' => $informacion]);
} catch (Exception $e) {
    echo json_encode(['error' => true, 'message' => $e->getMessage()]);
}
