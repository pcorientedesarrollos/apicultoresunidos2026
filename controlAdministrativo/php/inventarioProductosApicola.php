<?php

include_once '../../inventarios/php/obtenerInventarioApicola.php';


function realizarFuncionesInventarioApicola($data)
{
    if (!$data) {
        throw new Exception('No se recibieron parámetros');
    } else {
        $opcionMes = $data;
        $acumulado = $opcionMes->opcion == '1' ? true : false;
    }

    if ($acumulado) {
        $informacionInventarioApicola = obtenerInventarioApicola(false, true);
    } else {
        $idMes = $opcionMes->mes;
        $informacionInventarioApicola = obtenerInventarioApicola($idMes, false);
    }

    return $informacionInventarioApicola;

}

try {
    if (!isset($_GET['informeFinanciero'])) {
        $data = file_get_contents('php://input');
        $data = json_decode($data);
        $result = realizarFuncionesInventarioApicola($data);
        echo json_encode(['error' => false, 'resultado' => $result]);
    }
} catch (Exception $e) {
    echo json_encode(['error' => true, 'message' => $e->getMessage()]);
    exit();
}


// $data = file_get_contents('php://input');
// try {
//      if(!$data) {
//          throw new Exception('No se recibieron parámetros');
//      } else {
//          $opcionMes = json_decode($data);
//          $acumulado = $opcionMes->opcion == '1' ? true : false;
//      }

     
//      if($acumulado){
//         $informacionInventarioApicola = obtenerInventarioApicola(FALSE, TRUE);
//      } else {
//         $idMes = $opcionMes->mes;
//         $informacionInventarioApicola = obtenerInventarioApicola($idMes, FALSE);
//      }

//      echo json_encode(['error'=>false, 'resultado'=>$informacionInventarioApicola]);
// } catch (Exception $e) {
//     echo json_encode(['error'=>true, 'message'=>$e->getMessage]);
// }