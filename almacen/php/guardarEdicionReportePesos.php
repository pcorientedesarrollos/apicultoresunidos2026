<?php

include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$con = $pdo->conectar();
$con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$json = file_get_contents("php://input");

try {

    $con->beginTransaction();

    if (!$json) {
        throw new Exception('No se recibieron parámetros');
    } else {
        $datos = json_decode($json);
        $info = $datos->valor;
    }


    $sqlProceso = "UPDATE listadepesos SET lote = :lote, fecha = :fecha, totalBruto = :totalBruto, totalTara = :totalTara,
    totalNeto = :totalNeto, destino = :destino, tipoMiel = :tipoMiel, mielHomogeneizada = :mielHomogeneizada, tipoDeCliente = :tipoDeCliente, idCliente = :idCliente, filtro = :filtro WHERE idTamborPeso = :idTamborPeso";
    $dato = $con->prepare($sqlProceso);
    $dato->bindParam(':lote', $info[0]->lote);
    $dato->bindParam(':fecha', $info[0]->fecha);
    $dato->bindParam(':totalBruto', $info[0]->totalBruto);
    $dato->bindParam(':totalTara', $info[0]->totalTara);
    $dato->bindParam(':totalNeto', $info[0]->totalNeto);
    $dato->bindParam(':destino', $info[0]->destino);
    $dato->bindParam(':tipoMiel', $info[0]->tipoMiel);
    $dato->bindParam(':mielHomogeneizada', $info[0]->mielHomogeneizada);
    $dato->bindParam(':tipoDeCliente', $info[0]->tipoDeCliente);
    $dato->bindParam(':idCliente', $info[0]->idCliente);
    $dato->bindParam(':idTamborPeso', $info[0]->idTamborPeso);
    $dato->bindParam(':filtro', $info[0]->filtro);

    $dato->execute();

    if ($dato == false) {
        throw new Exception($con->errorInfo());
    }

    if ($info[0]->mielHomogeneizada == '1') {

        // Eliminar los tambores para insertar los nuevos
        $sqlDelete = $con->prepare("DELETE FROM tamboreslistapesos WHERE idTamborPeso = :idTamborPeso");
        $sqlDelete->bindParam(':idTamborPeso', $info[0]->idTamborPeso);
        $sqlDelete->execute();
        if ($sqlDelete == false) {
            throw new Exception($con->errorInfo());
        }

        foreach ($info[1] as $pesosTambos) {

            if (!isset($pesosTambos->clasificacion)) {
                $pesosTambos->tipo = '0';
                $pesosTambos->clasificacion = '0';
            } else {
                $pesosTambos->tipo = '1';
            }

            $sqlTambosEnvasados = "INSERT INTO tamboreslistapesos (folio, bruto, tara, neto, humedad, color,
            idFloracion, idTamborPeso, tipo, clasificacion) VALUES ('', :bruto, :tara, :neto, :humedad, :color,
            :idFloracion, :idTamborPeso, :tipo, :clasificacion)";
            $dat = $con->prepare($sqlTambosEnvasados);
            $dat->bindParam(':bruto', $pesosTambos->bruto);
            $dat->bindParam(':tara', $pesosTambos->tara);
            $dat->bindParam(':neto', $pesosTambos->neto);
            $dat->bindParam(':humedad', $pesosTambos->porcentaje);
            $dat->bindParam(':color', $pesosTambos->color);
            $dat->bindParam(':idFloracion', $pesosTambos->idFloracion);
            $dat->bindParam(':idTamborPeso', $info[0]->idTamborPeso);
            $dat->bindParam(':tipo', $pesosTambos->tipo);
            $dat->bindParam(':clasificacion', $pesosTambos->clasificacion);
            $dat->execute();

            if ($dat == false) {
                throw new Exception($con->errorInfo());
            }
        }
    } else {


        // Si es miel no homogeneizada

        switch ($info[0]->tipoMiel) {
            case '1':
                $almacen_tabla = 'almacen';
                break;
            case '2':
                $almacen_tabla = 'almacen_organico';
                break;
        }


        // Seleccionar todos los tambores del idTamborPeso y comparar

        $foliosUsados = array(); // Para guardar los folios que ya se habían usado en el reporte. Formato: 'f-s-c'
        $cambiarEstado = array(); // Para los folios que ya no se usan, así poder cambiar su estado
        $nuevosFolios = array(); // Los folios que se están ingresando

        foreach ($info[1] as $pesosTambos) {
            if (!isset($pesosTambos->clasificacion)) {
                $pesosTambos->tipo = '0';
                $pesosTambos->clasificacion = '0';
            } else {
                $pesosTambos->tipo = '1';
            }
            if (isset($pesosTambos->folio)) {
                // pasamos todos los nuevos folios al arreglo
                $nuevo_folio = $pesosTambos->folio . '-' . $pesosTambos->tipo . '-' . $pesosTambos->clasificacion;
                array_push($nuevosFolios, $nuevo_folio);
            }
        }

        // vamos a revisar cuáles eran los folios que ya se habían guardado en este reporte
        // Concatenamos el folio, el tipo y la clasificacion para que se seleccionen folios diferentes a la hora de
        // revisar si ya existían
        $sqlSeleccionaFoliosUsados = $con->prepare("SELECT CONCAT(folio, '-', tipo, '-', clasificacion) as folio FROM tamboreslistapesos WHERE idTamborPeso = :idTamborPeso AND folio IS NOT NULL;");
        $sqlSeleccionaFoliosUsados->bindParam(':idTamborPeso', $info[0]->idTamborPeso);
        $sqlSeleccionaFoliosUsados->execute();
        if ($sqlSeleccionaFoliosUsados == false) {
            throw new Exception($con->errorInfo());
        } else {
            foreach ($sqlSeleccionaFoliosUsados->fetchAll(PDO::FETCH_ASSOC) as $tamborUsado) {
                array_push($foliosUsados, $tamborUsado['folio']);
                if (!in_array($tamborUsado['folio'], $nuevosFolios)) {
                    array_push($cambiarEstado, $tamborUsado['folio']);
                }
            }
        }


        //  Eliminar los tambores para insertar los nuevos
        $sqlDelete = $con->prepare("DELETE FROM tamboreslistapesos WHERE idTamborPeso = :idTamborPeso");
        $sqlDelete->bindParam(':idTamborPeso', $info[0]->idTamborPeso);
        $sqlDelete->execute();
        if ($sqlDelete == false) {
            throw new Exception($con->errorInfo());
        }

        // Actualizar los folios de los tambores
        foreach ($cambiarEstado as $folio) {

            $tambor = explode('-', $folio);
            // $tambor[0] es el folio
            // $tambor[1] es el si es o no sobrante
            // $tambor[2] es el la clasificacion (solo si es sobrante será mayor a 0)

            // Depende si es sobrante y clasificacion a que tabla va a actualizar los folios

            if ($tambor[1] == '1' && $tambor[2] != '0') {

                // Si es sobrante
                $sqlUpdate = $con->prepare("UPDATE almacensobrantes SET estado = 0 WHERE consecutivo = :idAlmacen AND tipoDeMiel = :tipoDeMiel AND sobrante = :clasificacion");
                $sqlUpdate->bindParam(':idAlmacen', $tambor[0]);
                $sqlUpdate->bindParam(':tipoDeMiel', $info[0]->tipoMiel);
                $sqlUpdate->bindParam(':clasificacion', $tambor[2]);
                $sqlUpdate->execute();
                if ($sqlUpdate == false) {
                    throw new Exception($con->errorInfo());
                }
            } else {
                // Si no es sobrante
                $sqlUpdate = $con->prepare("UPDATE $almacen_tabla SET estado = 0 WHERE idAlmacen = :idAlmacen");
                $sqlUpdate->bindParam(':idAlmacen', $tambor[0]);
                $sqlUpdate->execute();
                if ($sqlUpdate == false) {
                    throw new Exception($con->errorInfo());
                }
            }
        }


        foreach ($info[1] as $pesosTambos) {

            if (!isset($pesosTambos->clasificacion)) {
                $pesosTambos->tipo = '0';
                $pesosTambos->clasificacion = '0';
            } else {
                $pesosTambos->tipo = '1';
            }

            // $comparar_folio -- Para verificar si el tambor ya pertenecía anteriormente al reporte
            $comparar_folio = $pesosTambos->folio . '-' . $pesosTambos->tipo . '-' . $pesosTambos->clasificacion;

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

                if ($tambor['estado'] == '2' || $tambor['estado'] == '4' || in_array($comparar_folio, $foliosUsados)) {

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
                    $sqlTambosEnvasados->bindParam(':idTamborPeso', $info[0]->idTamborPeso);
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

                if ($tambor['estado'] == '2' || $tambor['estado'] == '4' || in_array($comparar_folio, $foliosUsados)) {

                    $sqlTambosEnvasados = "INSERT INTO tamboreslistapesos (folio, bruto, tara, neto, humedad, color, idFloracion, idTamborPeso) VALUES (:folio, :bruto, :tara, :neto, :humedad, :color, :idFloracion, :idTamborPeso)";
                    $dat = $con->prepare($sqlTambosEnvasados);
                    $dat->bindParam(':folio', $pesosTambos->folio);
                    $dat->bindParam(':bruto', $pesosTambos->bruto);
                    $dat->bindParam(':tara', $pesosTambos->tara);
                    $dat->bindParam(':neto', $pesosTambos->neto);
                    $dat->bindParam(':humedad', $pesosTambos->porcentaje);
                    $dat->bindParam(':color', $pesosTambos->color);
                    $dat->bindParam(':idFloracion', $pesosTambos->idFloracion);
                    $dat->bindParam(':idTamborPeso', $info[0]->idTamborPeso);
                    $dat->execute();

                    if ($dat == false) {
                        throw new Exception($con->errorInfo());
                    }
                } else {
                    throw new Exception('El tambor con folio "' . $pesosTambos->folio . '" no puede ser enlistado como producto terminado.');
                }
            }
        }
    }


    $con->commit();
    echo json_encode(['error' => false, 'message' => 'El registro ha sido editado']);
} catch (Exception $e) {
    $con->rollBack();
    echo json_encode(['error' => true, 'message' => $e->getMessage() . '. Line ' . $e->getLine()]);
    exit();
}
