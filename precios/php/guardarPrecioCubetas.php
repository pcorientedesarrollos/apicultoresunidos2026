<?php
include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$con = $pdo->conectar();
$con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$postdata = file_get_contents('php://input');

try {
    $con->beginTransaction();

    if (!$postdata) {
        throw new Exception('No se recibieron parámetros');
    } else {
        $datos = json_decode($postdata);
        $info = $datos->valor;
    }

    // $folio = $_GET["folio"];
    $totalCompra = $_GET["totalCompra"];
    $id = $_GET["id"];

    if (isset($_GET['miel'])) {
        switch ($_GET['miel']) {
            case '1':
                $encabezado = 'cubetasencabezado';
                $detalle = 'almacen';
                $detalle2 = 'cubetasdetalle';
                break;
            case '2':
                $encabezado = 'cubetasencabezado_organico';
                $detalle = 'almacen_organico';
                $detalle2 = 'cubetasdetalle_organico';
                break;
            case '5':
                $encabezado = 'cubetasencabezado_mantequilla';
                $detalle = 'almacen_mantequilla';
                $detalle2 = 'cubetasdetalle_mantequilla';
                break;
            case '6':
                $encabezado = 'cubetasencabezado_altiplano';
                $detalle = 'almacen_altiplano';
                $detalle2 = 'cubetasdetalle_altiplano';
                break;
            case '7':
                $encabezado = 'cubetasencabezado_naranjo';
                $detalle = 'almacen_naranjo';
                $detalle2 = 'cubetasdetalle_naranjo';
                break;
            case '8':
                $encabezado = 'cubetasencabezado_aguacate';
                $detalle = 'almacen_aguacate';
                $detalle2 = 'cubetasdetalle_aguacate';
                break;
            case '9':
                $encabezado = 'cubetasencabezado_mezquite';
                $detalle = 'almacen_mezquite';
                $detalle2 = 'cubetasdetalle_mezquite';
                break;
            default:
                throw new Exception('Tipo de miel inválido');
                break;
        }
    }


    $sqlInsert = $con->prepare("UPDATE $encabezado set totalCompra= :totalCompra WHERE idAlmacen = :id");
    // $sqlInsert->bindParam(':folio', $folio);
    $sqlInsert->bindParam(':totalCompra', $totalCompra);
    $sqlInsert->bindParam(':id', $id);
    $sqlInsert->execute();
    if ($sqlInsert == false) {
        throw new Exception($con->errorInfo());
    }

    foreach ($info as $i) {
        $sql1 = "UPDATE $detalle2 set precio = :precio, costoTotal = :costoTotal WHERE idAlmacen = :idAlmacen";
        $datosUp = $con->prepare($sql1);
        $datosUp->bindParam(':precio', $i->precio);
        $datosUp->bindParam(':costoTotal', $i->costoTotal);
        $datosUp->bindParam(':idAlmacen', $i->idAlmacen);
        $datosUp->execute();
        if ($datosUp == false){
            throw new Exception($con->errorInfo());
        }
    
    }

    $mensaje = 'Se ha guardado el registro';

    $con->commit();
    echo json_encode(['error' => false, 'message' => $mensaje]);
} catch (Exception $e) {
    $con->rollBack();
    echo json_encode(['error' => true, 'message' => $e->getMessage() . '. Line ' . $e->getLine()]);
}
