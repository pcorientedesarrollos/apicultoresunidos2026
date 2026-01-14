<?php

include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO(); $con = $pdo->conectar();

function actualizacion($modo) {
    global $con;
    $con->beginTransaction();
    // Miel convencional
    echo '<h1>Miel convencional</h1>';
    $sqlSeleccionaFolios = $con->prepare("SELECT folioTambor, idLoteInterno FROM tamboreslotes;");
    $sqlSeleccionaFolios->execute();
    if($sqlSeleccionaFolios == FALSE) {
        throw new Exception($con->errorInfo());
    }

    foreach($sqlSeleccionaFolios->fetchAll(PDO::FETCH_ASSOC) as $folioTambor) {
        echo 'El folio ' . $folioTambor['folioTambor'] .  ' será actualizado al estado 2 <br>';
        $sqlActualizacion = $con->prepare("UPDATE almacen SET estado = 2 WHERE idAlmacen = :idAlmacen");
        $sqlActualizacion->bindParam(':idAlmacen', $folioTambor['folioTambor']);
        $sqlActualizacion->execute();
        if($sqlActualizacion == FALSE) {
            echo 'La consulta falló: ' . $con->errorInfo() . '<br><br>';
        } else {
            echo 'La consulta se ejecutó ' . '<br><br>';
        }
    }

    // Miel orgánica
    echo '<h1>Miel orgánica</h1>';
    $sqlSeleccionaFolios = $con->prepare("SELECT folioTambor, idLoteInterno FROM tamboreslotes_organico;");
    $sqlSeleccionaFolios->execute();
    if($sqlSeleccionaFolios == FALSE) {
        throw new Exception($con->errorInfo());
    }

    foreach($sqlSeleccionaFolios->fetchAll(PDO::FETCH_ASSOC) as $folioTambor) {
        echo 'El folio ' . $folioTambor['folioTambor'] .  ' será actualizado al estado 2 <br>';
        $sqlActualizacion = $con->prepare("UPDATE almacen_organico SET estado = 2 WHERE idAlmacen = :idAlmacen");
        $sqlActualizacion->bindParam(':idAlmacen', $folioTambor['folioTambor']);
        $sqlActualizacion->execute();
        if($sqlActualizacion == FALSE) {
            echo 'La consulta falló: ' . $con->errorInfo() . '<br><br>';
        } else {
            echo 'La consulta se ejecutó ' . '<br><br>';
        }
    }
    if($modo) {
        $con->commit();
    } else {
        $con->rollBack();
    }
}

try {
    if (isset($_GET['estado'])) {
        switch($_GET['estado']) {
            case 'true':
                actualizacion(TRUE);
                break;
            case 'false':
                actualizacion(FALSE);
                break;
            default:
                throw new Exception('');
                break;
        }
    } else {
        throw new Exception('');
    }
} catch (Exception $e) {
    echo json_encode(['error'=>true, 'message'=>$e->getMessage()]);
}
