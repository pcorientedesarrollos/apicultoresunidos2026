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
        $id = $_GET["id"];
    }

    if(isset($_GET['miel'])){
        switch ($_GET['miel']) {
                    case '1':
                        $detalle = 'cubetasdetalle';
                        break;
                    case '2':
                        $detalle = 'cubetasdetalle_organico';
                        break;
                    case '5':
                        $detalle = 'cubetasdetalle_mantequilla';
                        break;
                    case '6':
                        $detalle = 'cubetasdetalle_altiplano';
                        break;
                    case '7':
                        $detalle = 'cubetasdetalle_naranjo';
                        break;
                    case '8':
                        $detalle = 'cubetasdetalle_aguacate';
                        break;
                    case '9':
                        $detalle = 'cubetasdetalle_mezquite';
                        break;
                    default:
                        throw new Exception('Tipo de miel inválido');
                        break;
        }
    }

    $con->beginTransaction();

        $sql = "INSERT INTO $detalle (idAlmacenEncabezado, zona, trazabilidad, pesoLista, bruto, tara, neto, diferencia, humedad, autorizado, precio, costoTotal, referencia) VALUES(:id, :zona, '', :pesoLista, :bruto, :tara, :neto, :diferencia, :humedad, '0', '0', '0', :referencia)";
        $dato = $con->prepare($sql);
        $dato->bindParam(':id', $id);
        $dato->bindParam(':zona', $info->zona);
        // $dato->bindParam(':trazabilidad', $info->trazabilidad );
        $dato->bindParam(':pesoLista', $info->pesoLista);
        $dato->bindParam(':bruto', $info->bruto );
        $dato->bindParam(':tara', $info->tara);
        $dato->bindParam(':neto', $info->neto );
        $dato->bindParam(':diferencia', $info->diferencia);
        $dato->bindParam(':humedad',$info->humedad );
        $dato->bindParam(':referencia',$info->referencia );
        $dato->execute();

        $mensaje = 'Se ha guardado el registro';

    $con->commit();
    echo json_encode(['error' => false, 'message' => $mensaje]);

} catch (Exception $e) {
    $con->rollBack();
    echo json_encode(['error' => true, 'message' => $e->getMessage() . '. Line ' . $e->getLine()]);
}
