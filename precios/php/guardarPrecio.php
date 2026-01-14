<?php

// include_once '../../DAOConeccion/conePDO.php';
// $pdo = new conePDO();
// $con = $pdo->conectar();

// if(isset($_GET["organica"])){
//     $sql2 = "UPDATE almacenencabezado_organico set folio = :folio, totalCompra= :totalCompra WHERE almacenencabezado_organico.idAlmacen = :id";
//     $datosU = $con->prepare($sql2);
//     $datosU->bindParam(':folio', $folio);
//     $datosU->bindParam(':totalCompra', $totalCompra);
//     $datosU->bindParam(':id', $id);
//     $datosU->execute();
    
//     foreach ($info as $i) {
//         if ($i->neto <= 100) {
//             $sql1 = "UPDATE cubetasdetalle_organico set precio = :precio, costoTotal = :costoTotal WHERE cubetasdetalle_organico.idAlmacen = :idAlmacen";
//             $datosUp = $con->prepare($sql1);
//             $datosUp->bindParam(':precio', $i->precio);
//             $datosUp->bindParam(':costoTotal', $i->costoTotal);
//             $datosUp->bindParam(':idAlmacen', $i->idAlmacen);
//             $datosUp->execute();
//         } else {
//             $sql = "UPDATE almacen_organico set precio = :precio, costoTotal = :costoTotal WHERE almacen_organico.idAlmacen = :idAlmacen";
//             $datosUp1 = $con->prepare($sql);
//             $datosUp1->bindParam(':precio', $i->precio);
//             $datosUp1->bindParam(':costoTotal', $i->costoTotal);
//             $datosUp1->bindParam(':idAlmacen', $i->idAlmacen);
//             $datosUp1->execute();
//         }
//     }
// }else{
//     $sql2 = "UPDATE almacenencabezado set folio = :folio, totalCompra= :totalCompra WHERE almacenencabezado.idAlmacen = :id";
//     $datosU = $con->prepare($sql2);
//     $datosU->bindParam(':folio', $folio);
//     $datosU->bindParam(':totalCompra', $totalCompra);
//     $datosU->bindParam(':id', $id);
//     $datosU->execute();
//     if($datosU == false){
//         echo $con->errorInfo();
//     }
//     foreach ($info as $i) {
    
//         if ($i->neto <= 100) {
//             $sql1 = "UPDATE cubetasdetalle set precio = :precio, costoTotal = :costoTotal WHERE cubetasdetalle.idAlmacen = :idAlmacen";
//             $datosUp = $con->prepare($sql1);
//             $datosUp->bindParam(':precio', $i->precio);
//             $datosUp->bindParam(':costoTotal', $i->costoTotal);
//             $datosUp->bindParam(':idAlmacen', $i->idAlmacen);
//             $datosUp->execute();
            
//         } else {
//             $sql = "UPDATE almacen set precio = :precio, costoTotal = :costoTotal WHERE almacen.idAlmacen = :idAlmacen";
//             $datosUp1 = $con->prepare($sql);
//             $datosUp1->bindParam(':precio', $i->precio);
//             $datosUp1->bindParam(':costoTotal', $i->costoTotal);
//             $datosUp1->bindParam(':idAlmacen', $i->idAlmacen);
//             $datosUp1->execute();
//             if($datosUp1 == false){
//                 echo $con->errorInfo();
//             }
//         }
//     }
// }

include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$con = $pdo->conectar();
$con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$postdata = file_get_contents('php://input');

try {
    if (!$postdata) {
        throw new Exception('No se recibieron parámetros');
    } else {
        $datos = json_decode($postdata);
        $info = $datos->valor;
    }

    $folio = $_GET["folio"];
    $totalCompra = $_GET["totalCompra"];
    $id = $_GET["id"];

    if(isset($_GET['miel'])){
        switch ($_GET['miel']) {
                    case '1':
                        $encabezado = 'almacenencabezado';
                        $detalle = 'almacen';
                        $detalle2 = 'cubetasdetalle';
                        break;
                    case '2':
                        $encabezado = 'almacenencabezado_organico';
                        $detalle = 'almacen_organico';
                        $detalle2 = 'cubetasdetalle_organico';
                        break;
                    case '5':
                        $encabezado = 'almacenencabezado_mantequilla';
                        $detalle = 'almacen_mantequilla';
                        $detalle2 = 'cubetasdetalle_mantequilla';
                        break;
                    case '6':
                        $encabezado = 'almacenencabezado_altiplano';
                        $detalle = 'almacen_altiplano';
                        $detalle2 = 'cubetasdetalle_altiplano';
                        break;
                    case '7':
                        $encabezado = 'almacenencabezado_naranjo';
                        $detalle = 'almacen_naranjo';
                        $detalle2 = 'cubetasdetalle_naranjo';
                        break;
                        
                    case '8':
                        $encabezado = 'almacenencabezado_aguacate';
                        $detalle = 'almacen_aguacate';
                        $detalle2 = 'cubetasdetalle_aguacate';
                        break;
                    case '9':
                        $encabezado = 'almacenencabezado_mezquite';
                        $detalle = 'almacen_mezquite';
                        $detalle2 = 'cubetasdetalle_mezquite';
                        break;
                    default:
                        throw new Exception('Tipo de miel inválido');
                        break;
        }
    }

    $con->beginTransaction();

        $sqlInsert = $con->prepare("UPDATE $encabezado set folio = :folio, totalCompra= :totalCompra WHERE idAlmacen = :id");
        $sqlInsert->bindParam(':folio', $folio);
        $sqlInsert->bindParam(':totalCompra', $totalCompra);
        $sqlInsert->bindParam(':id', $id);     
        $sqlInsert->execute();
        if ($sqlInsert == false) {
            throw new Exception($con->errorInfo());
        }

        foreach ($info as $i) {
            if ($i->neto <= 100) {
                $sql1 = "UPDATE $detalle2 set precio = :precio, costoTotal = :costoTotal WHERE idAlmacen = :idAlmacen";
                $datosUp = $con->prepare($sql1);
                $datosUp->bindParam(':precio', $i->precio);
                $datosUp->bindParam(':costoTotal', $i->costoTotal);
                $datosUp->bindParam(':idAlmacen', $i->idAlmacen);
                $datosUp->execute();
            } else {
                $sql = "UPDATE $detalle set precio = :precio, costoTotal = :costoTotal WHERE idAlmacen = :idAlmacen";
                $datosUp1 = $con->prepare($sql);
                $datosUp1->bindParam(':precio', $i->precio);
                $datosUp1->bindParam(':costoTotal', $i->costoTotal);
                $datosUp1->bindParam(':idAlmacen', $i->idAlmacen);
                $datosUp1->execute();
            }
        }

        $mensaje = 'Se ha guardado el registro';

    $con->commit();
    echo json_encode(['error' => false, 'message' => $mensaje]);

} catch (Exception $e) {
    $con->rollBack();
    echo json_encode(['error' => true, 'message' => $e->getMessage() . '. Line ' . $e->getLine()]);
}