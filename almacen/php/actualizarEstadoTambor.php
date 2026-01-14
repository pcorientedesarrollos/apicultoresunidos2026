<?php
include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$con = $pdo->conectar();
$postData = file_get_contents('php://input');

// Este archivo es el que actualiza los estados de los tambores a 3 después de guardar el reporte de lista de pesos

try {
    $con->beginTransaction();
    if (!$postData) {
        throw new Exception('No se recibieron datos');
    } else {
        $reporte = json_decode($postData);
        $encabezado = $reporte[0];
        $tambores = $reporte[1];
        $lista_tambores = array();
    }

    switch ($encabezado->tipoMiel) {
        // Para saber que tabla de tamboreslotes usar
        case '1':
            $tamboreslotes_tabla = 'tamboreslotes';
            $almacen_tabla = 'almacen';
            break;
        case '2':
            $tamboreslotes_tabla = 'tamboreslotes_organico';
            $almacen_tabla = 'almacen_organico';
            break;
        case '5':
            $tamboreslotes_tabla = 'tamboreslotes_mantequilla';
            $almacen_tabla = 'almacen_mantequilla';
            break;
        case '6':
            $tamboreslotes_tabla = 'tamboreslotes_altiplano';
            $almacen_tabla = 'almacen_altiplano';
            break;
        case '7':
            $tamboreslotes_tabla = 'tamboreslotes_naranjo';
            $almacen_tabla = 'almacen_naranjo';
            break;
        case '8':
            $tamboreslotes_tabla = 'tamboreslotes_aguacate';
            $almacen_tabla = 'almacen_aguacate';
            break;
        case '9':
            $tamboreslotes_tabla = 'tamboreslotes_mezquite';
            $almacen_tabla = 'almacen_mezquite';
            break;
    }

    if ($encabezado->mielHomogeneizada == '2') {
        foreach ($tambores as $tambor) {
            if (isset($tambor->folio)) {
                $nvoTambor = array(
                    'idAlmacen' => $tambor->folio,
                    'tipo' => $tambor->tipo,
                    'clasificacion' => $tambor->clasificacion
                );
                array_push($lista_tambores, $nvoTambor);
            }
        }
    } else {
        $sqlSeleccionaFolios = $con->prepare("SELECT folioTambor, tipo, clasificacion FROM $tamboreslotes_tabla WHERE idLoteInterno = :idLoteInterno");
        $sqlSeleccionaFolios->bindParam(':idLoteInterno', $encabezado->idLoteInterno);
        $sqlSeleccionaFolios->execute();
        if ($sqlSeleccionaFolios == false) {
            throw new Exception($con->errorInfo());
        }
        foreach ($sqlSeleccionaFolios->fetchAll(PDO::FETCH_ASSOC) as $tambor) {
            $nvoTambor = array(
                'idAlmacen' => $tambor['folioTambor'],
                'tipo' => $tambor['tipo'],
                'clasificacion' => $tambor['clasificacion']
            );
            array_push($lista_tambores, $nvoTambor);
        }
    }

    $cantidad_tambores = count($lista_tambores);

    foreach ($lista_tambores as $tambor_a_modificar) {

        if ($tambor_a_modificar['tipo'] == '1' && $tambor_a_modificar['clasificacion'] != '0') {
            $sqlUpdate = $con->prepare("UPDATE almacensobrantes SET estado = 3 WHERE consecutivo = :idAlmacen AND tipoDeMiel = :tipoDeMiel AND sobrante = :clasificacion");
            $sqlUpdate->bindParam(':idAlmacen', $tambor_a_modificar['idAlmacen']);
            $sqlUpdate->bindParam(':tipoDeMiel', $encabezado->tipoMiel);
            $sqlUpdate->bindParam(':clasificacion', $tambor_a_modificar['clasificacion']);
            $sqlUpdate->execute();
            if ($sqlUpdate == false) {
                throw new Exception($con->errorInfo());
            }
        } else {
            $sqlUpdate = $con->prepare("UPDATE $almacen_tabla SET estado = 3 WHERE idAlmacen = :idAlmacen");
            $sqlUpdate->bindParam(':idAlmacen', $tambor_a_modificar['idAlmacen']);
            $sqlUpdate->execute();
            if ($sqlUpdate == false) {
                throw new Exception($con->errorInfo());
            }
        }
    }
    $con->commit();

    echo json_encode(['error' => false, 'message' => 'Se ha actualizado el estado de ' . $cantidad_tambores . ' tambores']);
} catch (Exception $e) {
    $con->rollBack();
    echo json_encode(['error' => true, 'message' => $e->getMessage()]);
}
