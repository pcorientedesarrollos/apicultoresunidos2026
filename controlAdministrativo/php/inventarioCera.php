<?php

include_once '../../inventarios/php/obtenerInventarioCera.php';


function realizarFuncionesInventarioCera($data)
{
    if (!$data) {
        throw new Exception('No se recibieron parámetros');
    } else {
        $opcionMes = $data;
        $acumulado = $opcionMes->opcion == '1' ? true : false;
    }
    if ($acumulado) {
        $InformacionInventarioCera = dameInventarioCera($opcionMes, $acumulado, false, false, false, $opcionMes->tipoCera);
    } else {
        $idMes = $opcionMes->mes;
        if ($idMes > 1) {
            $saldoPasado = array(
                'importeAcumulado' => 0,
                'existenciaAcumulada' => 0
            );
            for ($i = intval($idMes) - 1; $i > 0; $i--) {
                $EncabezadoMesPasado = dameInventarioCera($i, $acumulado, false, true, false, $opcionMes->tipoCera);
                $saldoPasado['importeAcumulado'] += $EncabezadoMesPasado['importeAcumulado'];
                $saldoPasado['existenciaAcumulada'] += $EncabezadoMesPasado['existenciaAcumulada'];
            }
            $InformacionInventarioCera = dameInventarioCera($idMes, $acumulado, $saldoPasado, false, false, $opcionMes->tipoCera);
        } else {
            $InformacionInventarioCera = dameInventarioCera($idMes, $acumulado, false, false, false, $opcionMes->tipoCera);
        }
    }

    return $InformacionInventarioCera;
}

try {
    if (!isset($_GET['informeFinanciero'])) {
        $data = file_get_contents('php://input');
        $data = json_decode($data);
        $result = realizarFuncionesInventarioCera($data);
        echo json_encode(['error' => false, 'resultado' => $result]);
    }
} catch (Exception $e) {
    echo json_encode(['error' => true, 'message' => $e->getMessage()]);
    exit();
}


// try {
//     if (!$data) {
//         throw new Exception('No se recibieron parámetros');
//     } else {
//         $opcionMes = json_decode($data);
//         $acumulado = $opcionMes->opcion == '1' ? true : false;
//     }
//     if ($acumulado) {
//         $InformacionInventarioCera = dameInventarioCera($opcionMes, $acumulado, false, false, false, $opcionMes->tipoCera);
//     } else {
//         $idMes = $opcionMes->mes;
//         if ($idMes > 1) {

//             $saldoPasado = array(
//                 'importeAcumulado' => 0,
//                 'existenciaAcumulada' => 0
//             );
//             for ($i = intval($idMes) - 1; $i > 0; $i--) {
//                 $EncabezadoMesPasado = dameInventarioCera($i, $acumulado, false, true, false, $opcionMes->tipoCera);
//                 $saldoPasado['importeAcumulado'] += $EncabezadoMesPasado['importeAcumulado'];
//                 $saldoPasado['existenciaAcumulada'] += $EncabezadoMesPasado['existenciaAcumulada'];
//             }
//             $InformacionInventarioCera = dameInventarioCera($idMes, $acumulado, $saldoPasado, false, false, $opcionMes->tipoCera);
//         } else {


//             $InformacionInventarioCera = dameInventarioCera($idMes, $acumulado, false, false, false, $opcionMes->tipoCera);
//         }
//     }

//     echo json_encode(['error' => false, 'resultado' => $InformacionInventarioCera]);
// } catch (Exception $e) {
//     echo json_encode(['error' => true, 'message' => $e->getMessage()]);
// }