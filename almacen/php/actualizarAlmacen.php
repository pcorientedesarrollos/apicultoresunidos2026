<?php

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

    if(isset($_GET['tipoMiel'])){
        switch ($_GET['tipoMiel']) {
                    case '1':
                        $detalle = 'almacen';
                        break;
                    case '2':
                        $detalle = 'almacen_organico';
                        break;
                    case '5':
                        $detalle = 'almacen_mantequilla';
                        break;
                    case '6':
                        $detalle = 'almacen_altiplano';
                        break;
                    case '7':
                        $detalle = 'almacen_naranjo';
                        break;
                    case '8':
                        $detalle = 'almacen_aguacate';
                        break;
                    case '9':
                        $detalle = 'almacen_mezquite';
                        break;
                    default:
                        throw new Exception('Tipo de miel inválido');
                        break;
        }
    }

    $con->beginTransaction();
    
    foreach ($info as $i) {
        $sql = "UPDATE $detalle set zona = :zona, pesoLista = :pesoLista, bruto = :bruto, tara = :tara, neto = :neto, diferencia = :diferencia, humedad = :humedad, referencia = :referencia WHERE idAlmacen = :idAlmacen";
        $datos = $con->prepare($sql);
        $datos->bindParam(':zona', $i->zona);
        $datos->bindParam(':pesoLista', $i->pesoLista);
        $datos->bindParam(':bruto', $i->bruto);
        $datos->bindParam(':tara', $i->tara);
        $datos->bindParam(':neto', $i->neto);
        $datos->bindParam(':diferencia', $i->diferencia);
        $datos->bindParam(':humedad', $i->humedad);
        $datos->bindParam(':referencia', $i->referencia);
        $datos->bindParam(':idAlmacen', $i->idAlmacen);
        $datos->execute();
    
        if ($sql == false){
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
