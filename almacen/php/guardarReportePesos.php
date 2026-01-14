<?php


include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$con = $pdo->conectar();
$con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$json = file_get_contents("php://input");

$idTamborPeso = 0;
try {
    $con->beginTransaction();
    if (!$json) {
        throw new Exception('No se recibieron parámetros');
    } else {
        $datos = json_decode($json);
        $info = $datos->valor;
    }

    $sqlLista = $con->prepare("INSERT INTO listadepesos (lote, fecha, totalBruto, totalTara, totalNeto, destino,
    tipoMiel, mielHomogeneizada, tipoDeCliente, idCliente, filtro) VALUES (:lote, '0000-00-00', :totalBruto, :totalTara, :totalNeto, :destino,
    :tipoMiel, :mielHomogeneizada, :tipoDeCliente, :idCliente, :filtro)");
    $sqlLista->bindParam(':lote', $info[0]->lote);
    $sqlLista->bindParam(':totalBruto', $info[0]->totalBruto);
    $sqlLista->bindParam(':totalTara', $info[0]->totalTara);
    $sqlLista->bindParam(':totalNeto', $info[0]->totalNeto);
    $sqlLista->bindParam(':destino', $info[0]->destino);
    $sqlLista->bindParam(':tipoMiel', $info[0]->tipoMiel);
    $sqlLista->bindParam(':mielHomogeneizada', $info[0]->mielHomogeneizada);
    $sqlLista->bindParam(':tipoDeCliente', $info[0]->tipoDeCliente);
    $sqlLista->bindParam(':idCliente', $info[0]->idCliente);
    $sqlLista->bindParam(':filtro', $info[0]->filtro);
    $sqlLista->execute();

    if ($sqlLista == false) {
        throw new Exception($con->errorInfo());
    } else {
        $idTamborPeso = $con->lastInsertId();
    }

    if ($info[0]->mielHomogeneizada == '1') {
        foreach ($info[1] as $pesosTambos) {

            $sqlTambosEnvasados = $con->prepare("INSERT INTO tamboreslistapesos (bruto, tara, neto, humedad, color,
            idFloracion, idTamborPeso) VALUES (:bruto, :tara, :neto, :humedad, :color, :idFloracion, :idTamborPeso)");
            $sqlTambosEnvasados->bindParam(':bruto', $pesosTambos->bruto);
            $sqlTambosEnvasados->bindParam(':tara', $pesosTambos->tara);
            $sqlTambosEnvasados->bindParam(':neto', $pesosTambos->neto);
            $sqlTambosEnvasados->bindParam(':humedad', $pesosTambos->porcentaje);
            $sqlTambosEnvasados->bindParam(':color', $pesosTambos->color);
            $sqlTambosEnvasados->bindParam(':idFloracion', $pesosTambos->idFloracion);
            $sqlTambosEnvasados->bindParam(':idTamborPeso', $idTamborPeso);
            $sqlTambosEnvasados->execute();

            if ($sqlTambosEnvasados == false) {
                throw new Exception($con->errorInfo());
            }
        }
    } else {
        switch ($info[0]->tipoMiel) {
            case '1':
                $almacen_tabla = 'almacen';
                break;
            case '2':
                $almacen_tabla = 'almacen_organico';
                break;
            case '5':
                $almacen_tabla = 'almacen_mantequilla';
                break;
            case '6':
                $almacen_tabla = 'almacen_altiplano';
                break;
            case '7':
                $almacen_tabla = 'almacen_naranjo';
                break;
            case '8':
                $almacen_tabla = 'almacen_aguacate';
                break;
            case '9':
                $almacen_tabla = 'almacen_mezquite';
                break;
        }

        foreach ($info[1] as $pesosTambos) {

            if ($pesosTambos->tipo == '1' && $pesosTambos->clasificacion != '0') {

                // Verificar que el folio del tambor tenga un estado 2 o 4, si no mandar una Exception

                $sqlSelect = $con->prepare("SELECT estado FROM almacensobrantes WHERE consecutivo = :consecutivo AND tipoDeMiel = :tipoDeMiel AND sobrante = :clasificacion");
                $sqlSelect->bindParam(':consecutivo', $pesosTambos->folio);
                $sqlSelect->bindParam(':tipoDeMiel', $info[0]->tipoMiel);
                $sqlSelect->bindParam(':clasificacion', $pesosTambos->clasificacion);
                $sqlSelect->execute();
                if ($sqlSelect == false) {
                    throw new Exception($con->errorInfo());
                } else {
                    $tambor = $sqlSelect->fetch(PDO::FETCH_ASSOC);
                }

                if ($tambor['estado'] == '2' || $tambor['estado'] == '4') {

                    $pesosTambos->porcentaje = isset($pesosTambos->porcentaje) ? $pesosTambos->porcentaje : '';
                    $pesosTambos->color = isset($pesosTambos->color) ? $pesosTambos->color : '';
                    $pesosTambos->idFloracion = isset($pesosTambos->idFloracion) ? $pesosTambos->idFloracion : '';

                    $sqlTambosEnvasados = $con->prepare("INSERT INTO tamboreslistapesos (folio, bruto, tara, neto, humedad,
                                                color, idFloracion, idTamborPeso, tipo, clasificacion) VALUES (:folio, :bruto, :tara, :neto, :humedad, :color, :idFloracion,
                                                :idTamborPeso, '1', :clasificacion)");
                    $sqlTambosEnvasados->bindParam(':folio', $pesosTambos->folio);
                    $sqlTambosEnvasados->bindParam(':bruto', $pesosTambos->bruto);
                    $sqlTambosEnvasados->bindParam(':tara', $pesosTambos->tara);
                    $sqlTambosEnvasados->bindParam(':neto', $pesosTambos->neto);
                    $sqlTambosEnvasados->bindParam(':humedad', $pesosTambos->porcentaje);
                    $sqlTambosEnvasados->bindParam(':color', $pesosTambos->color);
                    $sqlTambosEnvasados->bindParam(':idFloracion', $pesosTambos->idFloracion);
                    $sqlTambosEnvasados->bindParam(':idTamborPeso', $idTamborPeso);
                    $sqlTambosEnvasados->bindParam(':clasificacion', $pesosTambos->clasificacion);
                    $sqlTambosEnvasados->execute();

                    if ($sqlTambosEnvasados == false) {
                        throw new Exception($con->errorInfo());
                    }
                } else {
                    throw new Exception('El tambor de miel sobrante con folio "' . $pesosTambos->folio . '" no puede ser enlistado como producto terminado.');
                }

            } else {
                
                // Verificar que el folio del tambor tenga un estado 2 o 4, si no mandar una Exception

                $sqlSelect = $con->prepare("SELECT estado FROM $almacen_tabla WHERE idAlmacen = :idAlmacen");
                $sqlSelect->bindParam(':idAlmacen', $pesosTambos->folio);
                $sqlSelect->execute();
                if ($sqlSelect == false) {
                    throw new Exception($con->errorInfo());
                } else {
                    $tambor = $sqlSelect->fetch(PDO::FETCH_ASSOC);
                }

                if ($tambor['estado'] == '2' || $tambor['estado'] == '4') {
                    $sqlTambosEnvasados = $con->prepare("INSERT INTO tamboreslistapesos (folio, bruto, tara, neto, humedad,
                                color, idFloracion, idTamborPeso) VALUES (:folio, :bruto, :tara, :neto, :humedad, :color, :idFloracion,
                                :idTamborPeso)");
                    $sqlTambosEnvasados->bindParam(':folio', $pesosTambos->folio);
                    $sqlTambosEnvasados->bindParam(':bruto', $pesosTambos->bruto);
                    $sqlTambosEnvasados->bindParam(':tara', $pesosTambos->tara);
                    $sqlTambosEnvasados->bindParam(':neto', $pesosTambos->neto);
                    $sqlTambosEnvasados->bindParam(':humedad', $pesosTambos->porcentaje);
                    $sqlTambosEnvasados->bindParam(':color', $pesosTambos->color);
                    $sqlTambosEnvasados->bindParam(':idFloracion', $pesosTambos->idFloracion);
                    $sqlTambosEnvasados->bindParam(':idTamborPeso', $idTamborPeso);
                    $sqlTambosEnvasados->execute();

                    if ($sqlTambosEnvasados == false) {
                        throw new Exception($con->errorInfo());
                    }
                } else {
                    throw new Exception('El tambor con folio "' . $pesosTambos->folio . '" no puede ser enlistado como producto terminado.');
                }

            }
        }
    }


    $con->commit();
    echo json_encode(['error' => false, 'message' => 'Nuevo reporte disponible']);

} catch (Exception $e) {
    $con->rollBack();
    echo json_encode(['error' => true, 'message' => $e->getMessage() . '. Line ' . $e->getLine()]);
    exit();
}
