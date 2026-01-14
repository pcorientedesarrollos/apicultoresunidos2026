<?php

include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$con = $pdo->conectar();

try {
    if (!isset($_GET['idTamborPeso'])) {
        throw new Exception('No se han recibido parámetros');
    } else {
        $idTamborPeso = $_GET['idTamborPeso'];
    }

    $sqlTipoDeMiel = $con->prepare("SELECT tipoMiel FROM listadepesos WHERE idTamborPeso = :idTamborPeso;");
    $sqlTipoDeMiel->bindParam(':idTamborPeso', $idTamborPeso);
    $sqlTipoDeMiel->bindColumn('tipoMiel', $tipoDeMielReporte);
    $sqlTipoDeMiel->execute();
    if($sqlTipoDeMiel == FALSE){
        throw new Exception($con->errorInfo());
    } else {
        $sqlTipoDeMiel->fetch(PDO::FETCH_BOUND);
    }

    switch($tipoDeMielReporte){
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
        default:
            throw new Exception('El tipo de miel del reporte no es válido');
            break;
    }

    $sql = "SELECT lp.*, rc.fechaImpresion, rc.contenedor, rc.sello, o.operador, o.compania, p.marca, p.modelo, p.placa, rc.idLoteInterno, cm.clasificacion
        FROM listadepesos lp
        LEFT JOIN $entradaysalida_tabla rc ON rc.lote = lp.lote
        LEFT JOIN clasificacionesmiel cm ON cm.idClasificacionMiel = rc.idClasificacion
        LEFT JOIN choferes o ON o.idOperador = rc.idOperador 
        LEFT JOIN placaschoferes p ON p.idPlaca = rc.idPlaca
        WHERE lp.idTamborPeso = :idTamborPeso";
    $datos = $con->prepare($sql);
    $datos->bindParam(':idTamborPeso', $idTamborPeso);
    $datos->execute();

    if ($datos == false) {
        throw new Exception($con->errorInfo());
    }

    $reportePesos = $datos->fetch(PDO::FETCH_ASSOC);
    $reportePesos['pesosTambos'] = array();


    $sqlTambo = "SELECT idPesoTambo, folio, bruto, tara, neto, humedad AS porcentaje, color, idFloracion, 
                idTamborPeso, tipo, clasificacion
                FROM tamboreslistapesos
                WHERE idTamborPeso = :idTamborPeso";
    $datosPE = $con->prepare($sqlTambo);
    $datosPE->bindParam(':idTamborPeso', $idTamborPeso);
    $datosPE->execute();

    if ($datosPE == false) {
        throw new Exception($con->errorInfo());
    }

    $reportePesos['pesosTambos'] = $datosPE->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($reportePesos);

} catch (Exception $e) {
    echo json_encode(['error' => true, 'message' => $e->getMessage()]);
    exit();
}
?>