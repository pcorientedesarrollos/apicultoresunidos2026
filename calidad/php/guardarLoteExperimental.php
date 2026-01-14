<?php

// include_once '../../DAOConeccion/conePDO.php';
// $pdo = new conePDO();
// $cn = $pdo->conectar();

// $json = file_get_contents("php://input");

// try {

//     if (!$json) {
//         throw new Exception('No se recibieron parámetros');
//     } else {
//         $json = file_get_contents("php://input");
//         $datos = json_decode($json);
//         $info = $datos->valor;
//         $fecha = date("Y-m-d");
//         if ($info[0]->humedad == 'undefined') {
//             $info[0]->humedad = '0';
//         }
//     }

//     $cn->beginTransaction();

//     switch ($_GET['miel']) {
//         case '1':
//             $experimental = 'experimental';
//             $tambores = 'tamboresexperimentales';
//             $almacen = 'almacen';
//             $traspaso = 'almacentraspaso';
//             break;
//         case '2':
//             $experimental = 'experimental_organico';
//             $tambores = 'tamboresexperimentales_organico';
//             $almacen = 'almacen_organico';
//             $traspaso = 'almacentraspaso_organico';
//             break;
//     }

//     $sql = "INSERT INTO $experimental (fecha, humedad, numeroDeTambores, kilosTotales, resultadoLaboratorio, loteInterno, contrato, numContrato) 
//     VALUES ('$fecha', :humedad, :numeroDeTambores, :kilosTotales, '0', '0', :contrato, :numContrato)";
//     $data = $cn->prepare($sql);
//     $data->bindParam(':humedad', $info[0]->humedad);
//     $data->bindParam(':numeroDeTambores', $info[0]->numeroDeTambores);
//     $data->bindParam(':kilosTotales', $info[0]->kilosTotales);
//     $data->bindParam(':contrato', $info[0]->contrato);
//     $data->bindParam(':numContrato', $info[0]->numContrato);
//     $data->execute();
//     if ($data == false) {
//         // throw new Exception($cn->errorInfo());
//         throw new Exception('Error');
//     }
//     $idLoteExperimental = $cn->lastInsertId();

//     foreach ($info[1] as $arregloTambores) {
        // if ($arregloTambores->tipo === '2') { //traspaso
        //     $sqlTamborTraspaso = "INSERT INTO $tambores (folioTambor, idLoteExperimental, tipo, clasificacion) 
        //     VALUES ( :idAlmacen, '$idLoteExperimental', :tipo, 0)";
        //     $datoTraspaso = $cn->prepare($sqlTamborTraspaso);
        //     $datoTraspaso->bindParam(':idAlmacen', $arregloTambores->idAlmacen);
        //     $datoTraspaso->bindParam(':tipo', $arregloTambores->tipo);
        //     $datoTraspaso->execute();
        //     if ($datoTraspaso == false) {
        //         throw new Exception($cn->errorInfo());
        //     }

        //     $sqlActualizaTraspaso = $cn->prepare("UPDATE $traspaso set estado = '1' WHERE idAlmacen = :idAlmacen");
        //     $sqlActualizaTraspaso->bindParam(':idAlmacen', $arregloTambores->idAlmacen);
        //     $sqlActualizaTraspaso->execute();

        //     if ($sqlActualizaTraspaso == false) {
        //         throw new Exception($cn->errorInfo());
        //     }
        // } else
        // if ($arregloTambores->tipo === '0') { //almacén
        //     $sqlTambor = "INSERT INTO $tambores (folioTambor, idLoteExperimental, tipo, clasificacion) 
        //     VALUES ( :idAlmacen, '$idLoteExperimental', :tipo, 0)";
        //     $dato = $cn->prepare($sqlTambor);
        //     $dato->bindParam(':idAlmacen', $arregloTambores->idAlmacen);
        //     $dato->bindParam(':tipo', $arregloTambores->tipo);
        //     $dato->execute();
        //     if ($dato == false) {
                // throw new Exception($cn->errorInfo());
            //     throw new Exception('Error');
            // }

            // $sqlActualizaEstado = $cn->prepare("UPDATE $almacen set estado = '1' WHERE idAlmacen = :idAlmacen");
            // $sqlActualizaEstado->bindParam(':idAlmacen', $arregloTambores->idAlmacen);
            // $sqlActualizaEstado->execute();

            // if ($sqlActualizaEstado == false) {
                // throw new Exception($cn->errorInfo());
        //         throw new Exception('Error');
        //     }
        // } else if ($arregloTambores->tipo === '1') { //sobrantes

        //     $sqlTambor1 = "INSERT INTO $tambores (folioTambor, idLoteExperimental, tipo, clasificacion) 
        //     VALUES ( :idAlmacen, '$idLoteExperimental', :tipo, :clasificacion )";
        //     $dato1 = $cn->prepare($sqlTambor1);
        //     $dato1->bindParam(':idAlmacen', $arregloTambores->idAlmacen);
        //     $dato1->bindParam(':tipo', $arregloTambores->tipo);
        //     $dato1->bindParam(':clasificacion', $arregloTambores->clasificacion);
        //     $dato1->execute();
        //     if ($dato1 == false) {
                // throw new Exception($cn->errorInfo());
            //     throw new Exception('Error');
            // }

            // $sqlActualizaEstado1 = $cn->prepare("UPDATE almacensobrantes set estado = '1' WHERE consecutivo = :idAlmacen AND sobrante = :clasificacion AND tipoDeMiel = :tipoDeMiel");
            // $sqlActualizaEstado1->bindParam(':idAlmacen', $arregloTambores->idAlmacen);
            // $sqlActualizaEstado1->bindParam(':clasificacion', $arregloTambores->clasificacion);
            // $sqlActualizaEstado1->bindParam(':tipoDeMiel', $_GET['miel']);
            // $sqlActualizaEstado1->execute();

            // if ($sqlActualizaEstado1 == false) {
                // throw new Exception($cn->errorInfo());
//                 throw new Exception('Error');
//             }
//         }
//     }
//     $cn->commit();
//     echo json_encode(['error' => false, 'message' => 'Consulta realizada']);
// } catch (Exception $e) {
//     $cn->rollBack();
//     echo json_encode(['error' => true, 'message' => $e->getMessage() . ' Linea: ' . $e->getLine()]);
// }

// 4
// 10
// 11
// 12
// 13
// 14
// 15


// 864
// 865
// 1036
// 1040
// 1047
// 1053
// 1062
// 1066
// 1067
// 1068

include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$cn = $pdo->conectar();

$json = file_get_contents("php://input");

try {

    if (!$json) {
        throw new Exception('No se recibieron parámetros');
    } else {
        $json = file_get_contents("php://input");
        $datos = json_decode($json);
        $info = $datos->valor;
        $fecha = date("Y-m-d");
        if ($info[0]->humedad == 'undefined') {
            $info[0]->humedad = '0';
        }
    }

    $cn->beginTransaction();

    switch ($_GET['miel']) {
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
        case '5':
            $experimental = 'experimental_mantequilla';
            $tambores = 'tamboresexperimentales_mantequilla';
            $almacen = 'almacen_mantequilla';
            $traspaso = 'almacentraspaso_mantequilla';
            break;
        case '6':
            $experimental = 'experimental_altiplano';
            $tambores = 'tamboresexperimentales_altiplano';
            $almacen = 'almacen_altiplano';
            $traspaso = 'almacentraspaso_altiplano';
            break;
        case '7':
            $experimental = 'experimental_naranjo';
            $tambores = 'tamboresexperimentales_naranjo';
            $almacen = 'almacen_naranjo';
            $traspaso = 'almacentraspaso_naranjo';
            break;
        case '8':
            $experimental = 'experimental_aguacate';
            $tambores = 'tamboresexperimentales_aguacate';
            $almacen = 'almacen_aguacate';
            $traspaso = 'almacentraspaso_aguacate';
            break;
        case '9':
            $experimental = 'experimental_mezquite';
            $tambores = 'tamboresexperimentales_mezquite';
            $almacen = 'almacen_mezquite';
            $traspaso = 'almacentraspaso_mezquite';
            break;
    }

    $sql = "INSERT INTO $experimental (fecha, humedad, numeroDeTambores, kilosTotales, resultadoLaboratorio, loteInterno, contrato, numContrato) 
    VALUES ('$fecha', :humedad, :numeroDeTambores, :kilosTotales, '0', '0', :contrato, :numContrato)";
    $data = $cn->prepare($sql);
    $data->bindParam(':humedad', $info[0]->humedad);
    $data->bindParam(':numeroDeTambores', $info[0]->numeroDeTambores);
    $data->bindParam(':kilosTotales', $info[0]->kilosTotales);
    $data->bindParam(':contrato', $info[0]->contrato);
    $data->bindParam(':numContrato', $info[0]->numContrato);
    $data->execute();
    if ($data == false) {
        throw new Exception($cn->errorInfo());
    }
    $idLoteExperimental = $cn->lastInsertId();

    foreach ($info[1] as $arregloTambores) {

        if ($arregloTambores->tipo === '2') { //traspaso
            $sqlTamborTraspaso = "INSERT INTO $tambores (folioTambor, idLoteExperimental, tipo, clasificacion) 
            VALUES ( :idAlmacen, '$idLoteExperimental', :tipo, 0)";
            $datoTraspaso = $cn->prepare($sqlTamborTraspaso);
            $datoTraspaso->bindParam(':idAlmacen', $arregloTambores->idAlmacen);
            $datoTraspaso->bindParam(':tipo', $arregloTambores->tipo);
            $datoTraspaso->execute();
            if ($datoTraspaso == false) {
                throw new Exception($cn->errorInfo());
            }

            $sqlActualizaTraspaso = $cn->prepare("UPDATE $traspaso set estado = '1' WHERE idAlmacen = :idAlmacen");
            $sqlActualizaTraspaso->bindParam(':idAlmacen', $arregloTambores->idAlmacen);
            $sqlActualizaTraspaso->execute();

            if ($sqlActualizaTraspaso == false) {
                throw new Exception($cn->errorInfo());
            }
        } else if ($arregloTambores->tipo === '0') { //almacén
            $sqlTambor = "INSERT INTO $tambores (folioTambor, idLoteExperimental, tipo, clasificacion) 
            VALUES ( :idAlmacen, '$idLoteExperimental', :tipo, 0)";
            $dato = $cn->prepare($sqlTambor);
            $dato->bindParam(':idAlmacen', $arregloTambores->idAlmacen);
            $dato->bindParam(':tipo', $arregloTambores->tipo);
            $dato->execute();
            if ($dato == false) {
                throw new Exception($cn->errorInfo());
            }

            $sqlActualizaEstado = $cn->prepare("UPDATE $almacen set estado = '1' WHERE idAlmacen = :idAlmacen");
            $sqlActualizaEstado->bindParam(':idAlmacen', $arregloTambores->idAlmacen);
            $sqlActualizaEstado->execute();

            if ($sqlActualizaEstado == false) {
                throw new Exception($cn->errorInfo());
            }
        } else if ($arregloTambores->tipo === '1') { //sobrantes

            $sqlTambor1 = "INSERT INTO $tambores (folioTambor, idLoteExperimental, tipo, clasificacion) 
            VALUES ( :idAlmacen, '$idLoteExperimental', :tipo, :clasificacion )";
            $dato1 = $cn->prepare($sqlTambor1);
            $dato1->bindParam(':idAlmacen', $arregloTambores->idAlmacen);
            $dato1->bindParam(':tipo', $arregloTambores->tipo);
            $dato1->bindParam(':clasificacion', $arregloTambores->clasificacion);
            $dato1->execute();
            if ($dato1 == false) {
                throw new Exception($cn->errorInfo());
            }

            $sqlActualizaEstado1 = $cn->prepare("UPDATE almacensobrantes set estado = '1' WHERE consecutivo = :idAlmacen AND sobrante = :clasificacion AND tipoDeMiel = :tipoDeMiel");
            $sqlActualizaEstado1->bindParam(':idAlmacen', $arregloTambores->idAlmacen);
            $sqlActualizaEstado1->bindParam(':clasificacion', $arregloTambores->clasificacion);
            $sqlActualizaEstado1->bindParam(':tipoDeMiel', $_GET['miel']);
            $sqlActualizaEstado1->execute();

            if ($sqlActualizaEstado1 == false) {
                throw new Exception($cn->errorInfo());
            }
        }
    }
    $cn->commit();
    echo json_encode(['error' => false, 'message' => 'Consulta realizada']);
} catch (Exception $e) {
    $cn->rollBack();
    echo json_encode(['error' => true, 'message' => $e->getMessage() . ' Linea: ' . $e->getLine()]);
}
