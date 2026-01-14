<?php

include_once '../../DAOConeccion/conePDO.php';
include_once '../../controller/DameCaracteristica.php';
$pdo = new conePDO();
$con = $pdo->conectar();
$con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$json = file_get_contents("php://input");

try {
    $con->beginTransaction();
    if (!$json) {
        throw new Exception('No se recibieron los parámetros');
    } else {
        $datos = json_decode($json);
    }

    switch ($datos[0]->tipoMiel) {
        case '1':
            $configuracionlaboratorio_tabla = 'configuracionlaboratorio';
            break;
        case '2':
            $configuracionlaboratorio_tabla = 'configuracionlaboratorio_organico';
            break;
        default:
            throw new Exception('Parámetro de tipo de miel inválido');
            break;
    }

    $caracteristicas = new DameCaracteristica($con, $configuracionlaboratorio_tabla);
    $listaSf = $caracteristicas->obtenerValorSf();
    $listaSt = $caracteristicas->obtenerValorSt();
    $listaTetra = $caracteristicas->obtenerValorTt();
    $resultadoSf = "";
    $resultadoSt = "";
    $resultadoTt = "";

    $numeroTambos = sizeof($datos[1]);

    $sql = "INSERT INTO analisisexternosencabezado (fecha, idExterno, tipoMiel, idFloracion, empaque, numeroTambos, porcentaje, sf, st, c13, hmf, color, tt, interpretacion, tipoAnalisis)
                VALUES (:fecha, :idExterno, :tipoMiel, :idFloracion, :empaque, :numeroTambos, :porcentaje, :sf, :st, :c13, :hmf, :color, :tt, :interpretacion, :tipoAnalisis)";
    $insertarEncabezado = $con->prepare($sql);
    $insertarEncabezado->bindParam(':fecha', $datos[0]->fecha);
    $insertarEncabezado->bindParam(':idExterno', $datos[0]->idExterno);
    $insertarEncabezado->bindParam(':tipoMiel', $datos[0]->tipoMiel);
    $insertarEncabezado->bindParam(':idFloracion', $datos[0]->idFloracion);
    $insertarEncabezado->bindParam(':empaque', $datos[0]->empaque);
    $insertarEncabezado->bindParam(':numeroTambos', $numeroTambos);
    $insertarEncabezado->bindParam(':porcentaje', $datos[0]->porcentaje);
    $insertarEncabezado->bindParam(':sf', $datos[0]->sf);
    $insertarEncabezado->bindParam(':st', $datos[0]->st);
    $insertarEncabezado->bindParam(':c13', $datos[0]->c13);
    $insertarEncabezado->bindParam(':hmf', $datos[0]->hmf);
    $insertarEncabezado->bindParam(':color', $datos[0]->color);
    $insertarEncabezado->bindParam(':tt', $datos[0]->tt);
    $insertarEncabezado->bindParam(':interpretacion', $datos[0]->interpretacion);
    $insertarEncabezado->bindParam(':tipoAnalisis', $datos[0]->tipoAnalisis);

    $insertarEncabezado->execute();

    if ($insertarEncabezado == false) {
        throw new Exception($con->errorInfo());
    } else {
        $idSalida = $con->lastInsertid();

        foreach ($datos[1] as $muestras) {

            if (isset($muestras->sf)) {
                foreach ($listaSf as $value) {
                    switch ($value['signo']) {
                        case 1:
                            if ($muestras->sf < $value['rango1']) {
                                $resultadoSf = $value['descripcion'];
                            }
                            break;
                        case 2:
                            if ($muestras->sf > $value['rango1']) {
                                $resultadoSf = $value['descripcion'];
                            }
                            break;
                        case 3:
                            if ($muestras->sf <= $value['rango1']) {
                                $resultadoSf = $value['descripcion'];
                            }
                            break;
                        case 4:
                            if ($muestras->sf >= $value['rango1']) {
                                $resultadoSf = $value['descripcion'];
                            }
                            break;
                        case 5:
                            if ($muestras->sf >= $value['rango1'] && $muestras->sf <= $value['rango2']) {
                                $resultadoSf = $value['descripcion'];
                            }
                            break;
                    }
                }
            }

            if (isset($muestras->st)) {
                foreach ($listaSt as $value) {
                    switch ($value['signo']) {
                        case 1:
                            if ($muestras->st < $value['rango1']) {
                                $resultadoSt = $value['descripcion'];
                            }
                            break;
                        case 2:
                            if ($muestras->st > $value['rango1']) {
                                $resultadoSt = $value['descripcion'];
                            }
                            break;
                        case 3:
                            if ($muestras->st <= $value['rango1']) {
                                $resultadoSt = $value['descripcion'];
                            }
                            break;
                        case 4:
                            if ($muestras->st >= $value['rango1']) {
                                $resultadoSt = $value['descripcion'];
                            }
                            break;
                        case 5:
                            if ($muestras->st >= $value['rango1'] && $muestras->st <= $value['rango2']) {
                                $resultadoSt = $value['descripcion'];
                            }
                            break;
                    }
                }
            }

            if (isset($muestras->tt)) {
                foreach ($listaTetra as $value) {
                    switch ($value['signo']) {
                        case 1:
                            if ($muestras->tt < $value['rango1']) {
                                $resultadoTt = $value['descripcion'];
                            }
                            break;
                        case 2:
                            if ($muestras->tt > $value['rango1']) {
                                $resultadoTt = $value['descripcion'];
                            }
                            break;
                        case 3:
                            if ($muestras->tt <= $value['rango1']) {
                                $resultadoTt = $value['descripcion'];
                            }
                            break;
                        case 4:
                            if ($muestras->tt >= $value['rango1']) {
                                $resultadoTt = $value['descripcion'];
                            }
                            break;
                        case 5:
                            if ($muestras->tt >= $value['rango1'] && $muestras->tt <= $value['rango2']) {
                                $resultadoTt = $value['descripcion'];
                            }
                            break;
                    }
                }
            }

            $sqlMuestras = "INSERT INTO analisisexternosdetalle (idAnalisisEncabezado, marcaEmpresa, marcaAup, porcentaje, sf, st, c13, hmf, color, tt, resultado, resultadoSf, resultadoSt, resultadoTt)
            VALUES (:idAnalisisEncabezado, :marcaEmpresa, '0', :porcentaje, :sf, :st, :c13, :hmf, :color, :tt, :resultado, :resultadoSf, :resultadoSt, :resultadoTt)";
            $insertarFolios = $con->prepare($sqlMuestras);
            $insertarFolios->bindParam(':idAnalisisEncabezado', $idSalida);
            $insertarFolios->bindParam(':marcaEmpresa', $muestras->marcaEmpresa);
            $insertarFolios->bindParam(':porcentaje', $muestras->porcentaje);
            $insertarFolios->bindParam(':sf', $muestras->sf);
            $insertarFolios->bindParam(':st', $muestras->st);
            $insertarFolios->bindParam(':c13', $muestras->c13);
            $insertarFolios->bindParam(':hmf', $muestras->hmf);
            $insertarFolios->bindParam(':color', $muestras->color);
            $insertarFolios->bindParam(':tt', $muestras->tt);
            $insertarFolios->bindParam(':resultado', $muestras->resultado);
            $insertarFolios->bindParam(':resultadoSf', $resultadoSf);
            $insertarFolios->bindParam(':resultadoSt', $resultadoSt);
            $insertarFolios->bindParam(':resultadoTt', $resultadoTt);
            $insertarFolios->execute();
            if ($insertarFolios == false) {
                throw new Exception($con->errorInfo());
            } else {
                $idRegistro = $con->lastInsertid();
                if ($datos[0]->tipoAnalisis == '1') {
                    $marcaAup = "HMG-" . $idRegistro;
                } else if ($datos[0]->tipoAnalisis == '2') {
                    $marcaAup = "RCL-" . $idRegistro;
                } else if ($datos[0]->tipoAnalisis == '3') {
                    $marcaAup = "PTR-" . $idRegistro;
                } else if ($datos[0]->tipoAnalisis == '4') {
                    $marcaAup = "EXP-" . $idRegistro;
                }

                $sqlMarca = "UPDATE analisisexternosdetalle SET marcaAup = :marca WHERE idAnalisisDetalle = :idRegistro";
                $insertarMarca = $con->prepare($sqlMarca);
                $insertarMarca->bindParam(':marca', $marcaAup);
                $insertarMarca->bindParam(':idRegistro', $idRegistro);
                $insertarMarca->execute();
                if ($insertarMarca == false) {
                    throw new Exception($con->errorInfo());
                }
            }
        }
    }

    $con->commit();
    echo json_encode(['error' => false, 'message' => 'Se han guardado los datos', 'swal' => 'success']);
} catch (Exception $e) {
    $con->rollBack();
    echo json_encode(['error' => true, 'message' => $e->getMessage() . '. Line ' . $e->getLine(), 'swal' => 'error']);
    exit();
}
