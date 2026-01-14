<?php

include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$con = $pdo->conectar();
$con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
try {

    $tipo = $_GET['tipoMiel'];
    switch($tipo){
        case '1':
            $entradaysalida = 'entradaysalida';
            break;
        case '2':
            $entradaysalida = 'entradaysalida_organico';
            break;
        case '5':
            $entradaysalida = 'entradaysalida_mantequilla';
            break;
        case '6':
            $entradaysalida = 'entradaysalida_altiplano';
            break;
        case '7':
            $entradaysalida = 'entradaysalida_naranjo';
            break;
        case '8':
            $entradaysalida = 'entradaysalida_aguacate';
            break;
        case '9':
            $entradaysalida = 'entradaysalida_mezquite';
            break;
    }
    $query = "SELECT rp.*, c.fechaImpresion, c.clasificacionMiel, tdm.tipoDeMiel, c.idLoteInterno, cm.clasificacion,
    (SELECT COUNT(idPesoTambo)FROM tamboreslistapesos WHERE idTamborPeso = rp.idTamborPeso) as cantidadTambores
    FROM listadepesos rp
    LEFT JOIN $entradaysalida c ON c.lote = rp.lote
    LEFT JOIN clasificacionesmiel cm ON cm.idClasificacionMiel = c.idClasificacion
    LEFT JOIN tiposdemiel tdm ON rp.tipoMiel = tdm.idTipoDeMiel
    WHERE rp.tipoMiel = '" . $tipo . "'
    ORDER BY c.fechaImpresion DESC";
    $datos = $con->prepare($query);
    $datos->execute();
    if($datos == FALSE) {
        throw new Exception($con->errorInfo());
    }
    $arrayR = array();
    foreach( $datos->fetchAll(PDO::FETCH_ASSOC) as $reporte) {

        if($reporte['mielHomogeneizada'] == '1') {
            $reporte['mielHomogeneizada'] = 'Homogeneizada';
        } else if($reporte['mielHomogeneizada'] == '2') {
            $reporte['mielHomogeneizada'] = 'No homogeneizada';
        } else {
            $reporte['mielHomogeneizada'] = '';
        }

        array_push($arrayR, $reporte);
    }

    echo json_encode(['error'=>false, 'message'=>'Consulta realizada', 'data'=>$arrayR]);
} catch(Exception $e) {
    echo json_encode(['error'=>true, 'message'=>$e->getMessage()]);
    exit();
}
