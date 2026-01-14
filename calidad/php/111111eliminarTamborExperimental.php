<?php

include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$con = $pdo->conectar();

if (isset($_GET['idExperimental'])) {   //TAMBORES EN EXPERIMENTALES 
    $json = file_get_contents("php://input");
    try {
        if (!$json) {
            throw new Exception('No se recibieron parámetros');
        } else {
            $datos = json_decode($json);
            $info = $datos->valor;
            $folioTambor = $info->folioTambor;
            $miel = $info->miel;
            $tipo = $info->tipo;
            $clasificacion = $info->clasificacion;

            $peso = 0;
            $numeroDeTambores = 0;
            $kilosTotales = 0;

            switch ($miel) {
                case '1':
                    $experimental = 'experimental';
                    $tambores = 'tamboresexperimentales';
                    $almacen = 'almacen';
                    $traspaso = 'almacentraspaso';
                    break;
                case '2':
                    $experimental = 'experimental_organico';
                    $tambores = 'tamboresexperimentales_organico';
                    $almacen = 'almacen_organico';
                    $traspaso = 'almacentraspaso_organico';
                    break;
            }

            // if ($clasificacion == "0") {
            //     $tipo = '0';
            // } else {
            //     $tipo = '1';
            // }
        }
        $con->beginTransaction();

        if ($tipo == '0') { //tambo en almacén
            $sqlNetoAlmacen = "SELECT neto FROM $almacen WHERE idAlmacen = :folioTambor";
            $respAlmacen = $con->prepare($sqlNetoAlmacen);
            $respAlmacen->bindParam(':folioTambor', $folioTambor);
            $respAlmacen->execute();
            $respAlmacen->bindColumn('neto', $peso);
            if ($respAlmacen == false) {
                throw new Exception($con->errorInfo());
            } else {
                $respAlmacen->fetch(PDO::FETCH_BOUND);
            }
        } else if ($tipo == '2') { //tambo en traspaso
            $sqlNetoAlmacen = "SELECT neto FROM $traspaso WHERE idAlmacen = :folioTambor";
            $respAlmacen = $con->prepare($sqlNetoAlmacen);
            $respAlmacen->bindParam(':folioTambor', $folioTambor);
            $respAlmacen->execute();
            $respAlmacen->bindColumn('neto', $peso);
            if ($respAlmacen == false) {
                throw new Exception($con->errorInfo());
            } else {
                $respAlmacen->fetch(PDO::FETCH_BOUND);
            }
        } else if ($tipo == '1') { //tambo en sobrantes
            $sqlNetoSobrante = "SELECT neto FROM almacensobrantes WHERE idEntradaSobrante = :folioTambor AND sobrante = :clasificacion AND tipoDeMiel = :miel";
            $respSobrante = $con->prepare($sqlNetoSobrante);
            $respSobrante->bindParam(':folioTambor', $folioTambor);
            $respSobrante->bindParam(':clasificacion', $clasificacion);
            $respSobrante->bindParam(':miel', $miel);
            $respSobrante->execute();
            $respSobrante->bindColumn('neto', $peso);
            if ($respSobrante == false) {
                throw new Exception($con->errorInfo());
            } else {
                $respSobrante->fetch(PDO::FETCH_BOUND);
            }
        }

        $sqlGetTotales = "SELECT numeroDeTambores, kilosTotales FROM $experimental WHERE idLoteExperimental = :idLoteExperimental";
        $respTotal = $con->prepare($sqlGetTotales);
        $respTotal->bindParam(':idLoteExperimental', $_GET['idExperimental']);
        $respTotal->execute();
        $respTotal->bindColumn('numeroDeTambores', $numeroDeTambores);
        $respTotal->bindColumn('kilosTotales', $kilosTotales);
        if ($respTotal == false) {
            throw new Exception($con->errorInfo());
        } else {
            $respTotal->fetch(PDO::FETCH_BOUND);
        }
        $numeroDeTambores = floatval($numeroDeTambores);
        $kilosTotales = floatval($kilosTotales);
        $neto = floatval($peso);
        $tambos = 0;
        $kilos = 0;
        if ($numeroDeTambores > 0 && $kilosTotales > 0) {
            $tambos = $numeroDeTambores - 1;
            $kilos = $kilosTotales - $neto;
        } else {
            $tambos = 0;
            $kilos = 0;
        }

        $sqlEliminarTambo = "DELETE FROM $tambores WHERE folioTambor = :folioTambor AND tipo = :tipo AND clasificacion = :clasificacion";
        $respEliminar = $con->prepare($sqlEliminarTambo);
        $respEliminar->bindParam(':folioTambor', $folioTambor);
        $respEliminar->bindParam(':tipo', $tipo);
        $respEliminar->bindParam(':clasificacion', $clasificacion);
        $respEliminar->execute();
        if ($respEliminar == false) {
            throw new Exception($con->errorInfo());
        } else {
            if ($tipo == '0') {
                $sqlUpAlmacen = "UPDATE $almacen SET estado = '0' WHERE idAlmacen = :folioTambor";
                $respUpAlmacen = $con->prepare($sqlUpAlmacen);
                $respUpAlmacen->bindParam(':folioTambor', $folioTambor);
                $respUpAlmacen->execute();
                if ($respUpAlmacen == false) {
                    throw new Exception($con->errorInfo());
                } else {
                    $sqlEncabezado = "UPDATE $experimental SET numeroDeTambores = :numeroDeTambores, kilosTotales = :kilosTotales WHERE idLoteExperimental = :idLoteExperimental";
                    $respEncabezado = $con->prepare($sqlEncabezado);
                    $respEncabezado->bindParam(':numeroDeTambores', $tambos);
                    $respEncabezado->bindParam(':kilosTotales', $kilos);
                    $respEncabezado->bindParam(':idLoteExperimental', $_GET['idExperimental']);
                    $respEncabezado->execute();
                    if ($respEncabezado == false) {
                        throw new Exception($con->errorInfo());
                    }
                }
            } else if ($tipo == '1') {
                $sqlUpSobrante = "UPDATE almacensobrantes SET estado = '0' WHERE idEntradaSobrante = :folioTambor AND sobrante = :clasificacion AND tipoDeMiel = :miel";
                $respUpSobrante = $con->prepare($sqlUpSobrante);
                $respUpSobrante->bindParam(':folioTambor', $folioTambor);
                $respUpSobrante->bindParam(':clasificacion', $clasificacion);
                $respUpSobrante->bindParam(':miel', $miel);
                $respUpSobrante->execute();
                if ($respUpSobrante == false) {
                    throw new Exception($con->errorInfo());
                } else {
                    $sqlEncabezado1 = "UPDATE $experimental SET numeroDeTambores = :numeroDeTambores, kilosTotales = :kilosTotales WHERE idLoteExperimental = :idLoteExperimental";
                    $respEncabezado1 = $con->prepare($sqlEncabezado1);
                    $respEncabezado1->bindParam(':numeroDeTambores', $tambos);
                    $respEncabezado1->bindParam(':kilosTotales', $kilos);
                    $respEncabezado1->bindParam(':idLoteExperimental', $_GET['idExperimental']);
                    $respEncabezado1->execute();
                    if ($respEncabezado1 == false) {
                        throw new Exception($con->errorInfo());
                    }
                }
            } else if ($tipo == '2') {
                $sqlUpAlmacen = "UPDATE $traspaso SET estado = '0' WHERE idAlmacen = :folioTambor";
                $respUpAlmacen = $con->prepare($sqlUpAlmacen);
                $respUpAlmacen->bindParam(':folioTambor', $folioTambor);
                $respUpAlmacen->execute();
                if ($respUpAlmacen == false) {
                    throw new Exception($con->errorInfo());
                } else {
                    $sqlEncabezado = "UPDATE $experimental SET numeroDeTambores = :numeroDeTambores, kilosTotales = :kilosTotales WHERE idLoteExperimental = :idLoteExperimental";
                    $respEncabezado = $con->prepare($sqlEncabezado);
                    $respEncabezado->bindParam(':numeroDeTambores', $tambos);
                    $respEncabezado->bindParam(':kilosTotales', $kilos);
                    $respEncabezado->bindParam(':idLoteExperimental', $_GET['idExperimental']);
                    $respEncabezado->execute();
                    if ($respEncabezado == false) {
                        throw new Exception($con->errorInfo());
                    }
                }
            }
        }
        $con->commit();
        echo json_encode(['error' => false, 'info' => $info]);
    } catch (Exception $e) {
        $con->rollBack();
        echo json_encode(['error' => true, 'message' => $e->getMessage() . ' Linea: ' . $e->getLine()]);
    }
} else if (isset($_GET['idLoteInterno'])) {  // TAMBORES EN LOTES INTERNOS //
    $json = file_get_contents("php://input");
    try {
        if (!$json) {
            throw new Exception('No se recibieron parámetros');
        } else {
            $datos = json_decode($json);
            $info = $datos->valor;
            $folioTambor = $info->folioTambor;
            $miel = $info->miel;
            $clasificacion = $info->clasificacion;
            $tipo = $info->tipo;
            $peso = 0;
            $respTambosCalidad = 0;
            $respKilosCalidad = 0;
            $neto = 0;
            $numeroDeTambores = 0;
            $kilosTotales = 0;
            $respTambosExperimental = 0;
            $respKilosExperimental = 0;
            $numeroDeTamboresExp = 0;
            $kilosTotalesExp = 0;

            switch ($miel) {
                case '1':
                    $tambores = 'tamboreslotes';
                    $almacen = 'almacen';
                    $calidad = 'calidad';
                    $tamboresExperimental = 'tamboresexperimentales';
                    $experimental = 'experimental';
                    $traspaso = 'almacentraspaso';
                    break;
                case '2':
                    $tambores = 'tamboreslotes_organico';
                    $almacen = 'almacen_organico';
                    $calidad = 'calidad_organico';
                    $tamboresExperimental = 'tamboresexperimentales_organico';
                    $experimental = 'experimental_organico';
                    $traspaso = 'almacentraspaso_organico';
                    break;
            };

            // if ($clasificacion == "0") {
            //     $tipo = '0';
            // } else {
            //     $tipo = '1';
            // }
        }

        $con->beginTransaction();

        if ($tipo === '0') { //tambores almacén
            $sqlAlmacen = "SELECT neto FROM $almacen WHERE idAlmacen = :folioTambor";
            $respAlmacen = $con->prepare($sqlAlmacen);
            $respAlmacen->bindParam(':folioTambor', $folioTambor);
            $respAlmacen->execute();
            $respAlmacen->bindColumn('neto', $peso);
            if ($respAlmacen == false) {
                throw new Exception($con->errorInfo());
            } else {
                $respAlmacen->fetch(PDO::FETCH_BOUND);
            }
        } else if ($tipo === '2') { //tambores traspaso
            $sqlAlmacen = "SELECT neto FROM $traspaso WHERE idAlmacen = :folioTambor";
            $respAlmacen = $con->prepare($sqlAlmacen);
            $respAlmacen->bindParam(':folioTambor', $folioTambor);
            $respAlmacen->execute();
            $respAlmacen->bindColumn('neto', $peso);
            if ($respAlmacen == false) {
                throw new Exception($con->errorInfo());
            } else {
                $respAlmacen->fetch(PDO::FETCH_BOUND);
            }
        } else if ($tipo === '1') { // tambores miel sobrante
            $sqlSobrante = "SELECT neto FROM almacensobrantes WHERE idEntradaSobrante = :folioTambor AND sobrante = :clasificacion AND tipoDeMiel = :miel";
            $respSobrante = $con->prepare($sqlSobrante);
            $respSobrante->bindParam(':folioTambor', $folioTambor);
            $respSobrante->bindParam(':clasificacion', $clasificacion);
            $respSobrante->bindParam(':miel', $miel);
            $respSobrante->execute();
            $respSobrante->bindColumn('neto', $peso);
            if ($respSobrante == false) {
                throw new Exception($con->errorInfo());
            } else {
                $respSobrante->fetch(PDO::FETCH_BOUND);
            }
        }

        //Traer número de tambores y kilos totales del lote interno (respaldo)
        $encabezadoCalidad = "SELECT numeroDeTambores, kilosTotales FROM $calidad WHERE idLoteInterno = :idLoteInterno";
        $respCalidad = $con->prepare($encabezadoCalidad);
        $respCalidad->bindParam(':idLoteInterno', $_GET['idLoteInterno']);
        $respCalidad->execute();
        $respCalidad->bindColumn('numeroDeTambores', $respTambosCalidad);
        $respCalidad->bindColumn('kilosTotales', $respKilosCalidad);
        if ($respCalidad == false) {
            throw new Exception($con->errorInfo());
        } else {
            $respCalidad->fetch(PDO::FETCH_BOUND);
        }
        $respTambosCalidad = floatval($respTambosCalidad);
        $respKilosCalidad = floatval($respKilosCalidad);
        $neto = floatval($peso);
        if ($respTambosCalidad > 0 && $respKilosCalidad > 0) {
            $numeroDeTambores = $respTambosCalidad - 1;
            $kilosTotales = $respKilosCalidad - $neto;
        } else {
            $numeroDeTambores = '0';
            $kilosTotales = '0';
        }

        $sqlLote = "DELETE FROM $tambores WHERE folioTambor = :folioTambor AND tipo = :tipo AND clasificacion = :clasificacion";
        $data = $con->prepare($sqlLote);
        $data->bindParam(':folioTambor', $folioTambor);
        $data->bindParam(':tipo', $tipo);
        $data->bindParam(':clasificacion', $clasificacion);
        $data->execute();
        if ($data == false) {
            throw new Exception($con->errorInfo());
        } else {
            $sqlUpTotales = "UPDATE $calidad SET numeroDeTambores = :numeroDeTambores, kilosTotales = :kilosTotales WHERE idLoteInterno = :idLoteInterno";
            $datosTotales = $con->prepare($sqlUpTotales);
            $datosTotales->bindParam(':numeroDeTambores', $numeroDeTambores);
            $datosTotales->bindParam(':kilosTotales', $kilosTotales);
            $datosTotales->bindParam(':idLoteInterno', $_GET['idLoteInterno']);
            $datosTotales->execute();
            if ($datosTotales == false) {
                throw new Exception($con->errorInfo());
            } else {
                //verificar primero si el folio se encuentra en un experimental
                $sqlVerificarFolio = "SELECT folioTambor FROM $tamboresExperimental WHERE folioTambor = :folioTambor";
                $respVerificacion = $con->prepare($sqlVerificarFolio);
                $respVerificacion->bindParam(':folioTambor', $folioTambor);
                $respVerificacion->execute();
                $respVerificacion->bindColumn('folioTambor', $existe);
                if ($respVerificacion == false) {
                    throw new Exception($con->errorInfo());
                } else {
                    $respVerificacion->fetch(PDO::FETCH_BOUND);
                    if ($existe == null) {
                        if ($tipo === '0') {
                            $sqlUp = "UPDATE $almacen SET estado = '0' WHERE idAlmacen = :folioTambor";
                            $datos = $con->prepare($sqlUp);
                            $datos->bindParam(':folioTambor', $folioTambor);
                            $datos->execute();
                            if ($datos == false) {
                                throw new Exception($con->errorInfo());
                            }
                        } else if ($tipo === '2') {
                            $sqlUp = "UPDATE $traspaso SET estado = '0' WHERE idAlmacen = :folioTambor";
                            $datos = $con->prepare($sqlUp);
                            $datos->bindParam(':folioTambor', $folioTambor);
                            $datos->execute();
                            if ($datos == false) {
                                throw new Exception($con->errorInfo());
                            }
                        } else if ($tipo === '1') {
                            $sqlUp = "UPDATE almacensobrantes SET estado = '0' WHERE idEntradaSobrante = :folioTambor AND sobrante = :clasificacion AND tipoDeMiel = :miel";
                            $datos = $con->prepare($sqlUp);
                            $datos->bindParam(':folioTambor', $folioTambor);
                            $datos->bindParam(':clasificacion', $clasificacion);
                            $datos->bindParam(':miel', $miel);
                            $datos->execute();
                            if ($datos == false) {
                                throw new Exception($con->errorInfo());
                            }
                        }
                    } else {
                        $sqlExperimental = "SELECT idLoteExperimental FROM $tamboresExperimental WHERE folioTambor = :folioTambor AND clasificacion = :clasificacion";
                        $informacion = $con->prepare($sqlExperimental);
                        $informacion->bindParam(':folioTambor', $folioTambor);
                        $informacion->bindParam(':clasificacion', $clasificacion);
                        $informacion->execute();
                        $informacion->bindColumn('idLoteExperimental', $idLoteExperimental);
                        if ($informacion == false) {
                            throw new Exception($con->errorInfo());
                        } else {
                            $informacion->fetch(PDO::FETCH_BOUND);
                        }
                        $experimentalTotales = "SELECT numeroDeTambores, kilosTotales FROM $experimental WHERE idLoteExperimental = :idLoteExperimental";
                        $dato = $con->prepare($experimentalTotales);
                        $dato->bindParam(':idLoteExperimental', $idLoteExperimental);
                        $dato->execute();
                        $dato->bindColumn('numeroDeTambores', $respTambosExperimental);
                        $dato->bindColumn('kilosTotales', $respKilosExperimental);
                        if ($dato == false) {
                            throw new Exception($con->errorInfo());
                        } else {
                            $dato->fetch(PDO::FETCH_BOUND);
                        }
                        $respTambosExperimental = floatval($respTambosExperimental);
                        $respKilosExperimental = floatval($respKilosExperimental);
                        $neto = floatval($peso);
                        if ($respTambosExperimental > 0 && $respKilosExperimental > 0) {
                            $numeroDeTamboresExp = $respTambosExperimental - 1;
                            $kilosTotalesExp = $respKilosExperimental - $neto;
                        } else {
                            $numeroDeTamboresExp = '0';
                            $kilosTotalesExp = '0';
                        }

                        $sql = "DELETE FROM $tamboresExperimental WHERE folioTambor = :folioTambor AND clasificacion = :clasificacion";
                        $dats = $con->prepare($sql);
                        $dats->bindParam(':folioTambor', $folioTambor);
                        $dats->bindParam(':clasificacion', $clasificacion);
                        $dats->execute();
                        if ($dats == false) {
                            throw new Exception($con->errorInfo());
                        } else {
                            $sqlUpTotalesExp = "UPDATE $experimental SET numeroDeTambores = :numeroDeTambores, kilosTotales = :kilosTotales WHERE idLoteExperimental = :idLoteExperimental";
                            $datosTotalesExp = $con->prepare($sqlUpTotalesExp);
                            $datosTotalesExp->bindParam(':numeroDeTambores', $numeroDeTamboresExp);
                            $datosTotalesExp->bindParam(':kilosTotales', $kilosTotalesExp);
                            $datosTotalesExp->bindParam(':idLoteExperimental', $idLoteExperimental);
                            $datosTotalesExp->execute();
                            if ($datosTotalesExp == false) {
                                throw new Exception($con->errorInfo());
                            } else {
                                if ($tipo === '0') {
                                    $sqlUp = "UPDATE $almacen SET estado = '0' WHERE idAlmacen = :folioTambor";
                                    $datos = $con->prepare($sqlUp);
                                    $datos->bindParam(':folioTambor', $folioTambor);
                                    $datos->execute();
                                    if ($datos == false) {
                                        throw new Exception($con->errorInfo());
                                    }
                                } else if ($tipo === '2') {
                                    $sqlUp = "UPDATE $traspaso SET estado = '0' WHERE idAlmacen = :folioTambor";
                                    $datos = $con->prepare($sqlUp);
                                    $datos->bindParam(':folioTambor', $folioTambor);
                                    $datos->execute();
                                    if ($datos == false) {
                                        throw new Exception($con->errorInfo());
                                    }
                                } else if ($tipo === '1') {
                                    $sqlUp = "UPDATE almacensobrantes SET estado = '0' WHERE idEntradaSobrante = :folioTambor AND sobrante = :clasificacion AND tipoDeMiel = :miel";
                                    $datos = $con->prepare($sqlUp);
                                    $datos->bindParam(':folioTambor', $folioTambor);
                                    $datos->bindParam(':clasificacion', $clasificacion);
                                    $datos->bindParam(':miel', $miel);
                                    $datos->execute();
                                    if ($datos == false) {
                                        throw new Exception($con->errorInfo());
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }

        $con->commit();
        echo json_encode(['error' => false, 'message' => 'Consulta realizada']);
    } catch (Exception $e) {
        $con->rollBack();
        echo json_encode(['error' => true, 'message' => $e->getMessage() . ' Linea: ' . $e->getLine()]);
    }
}
