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

    if (isset($_GET['tipo'])) {
        $tipo = $_GET['tipo'];
        switch ($tipo) {
            case '1':
                $encabezado = 'cubetasencabezado';
                $tabla = 'cubetasdetalle';
                break;
            case '2':
                $encabezado = 'cubetasencabezado_organico';
                $tabla = 'cubetasdetalle_organico';
                break;
            case '5':
                $encabezado = 'cubetasencabezado_mantequilla';
                $tabla = 'cubetasdetalle_mantequilla';
                break;
            case '6':
                $encabezado = 'cubetasencabezado_altiplano';
                $tabla = 'cubetasdetalle_altiplano';
                break;
            case '7':
                $encabezado = 'cubetasencabezado_naranjo';
                $tabla = 'cubetasdetalle_naranjo';
                break;
            case '8':
                $encabezado = 'cubetasencabezado_aguacate';
                $tabla = 'cubetasdetalle_aguacate';
                break;
            case '9':
                $encabezado = 'cubetasencabezado_mezquite';
                $tabla = 'cubetasdetalle_mezquite';
                break;
        }
    }

    $con->beginTransaction();

    $sqlEnc = "UPDATE $encabezado set folioEntradaTambor = :folioEntradaTambor WHERE idAlmacen = :idAlmacen";
    $dato = $con->prepare($sqlEnc);
    $dato->bindParam(':folioEntradaTambor', $_GET['folio']);
    $dato->bindParam(':idAlmacen', $_GET['idAlmacen']);
    $dato->execute();

    foreach ($info as $i) {
        $sql = "UPDATE $tabla set zona = :zona, pesoLista = :pesoLista, bruto = :bruto, tara = :tara, neto = :neto, diferencia = :diferencia, humedad = :humedad, tamborAsignado = :tamborAsignado, referencia = :referencia WHERE idAlmacen = :idAlmacen";
        $datos = $con->prepare($sql);
        $datos->bindParam(':zona', $i->zona);
        $datos->bindParam(':pesoLista', $i->pesoLista);
        $datos->bindParam(':bruto', $i->bruto);
        $datos->bindParam(':tara', $i->tara);
        $datos->bindParam(':neto', $i->neto);
        $datos->bindParam(':diferencia', $i->diferencia);
        $datos->bindParam(':humedad', $i->humedad);
        $datos->bindParam(':tamborAsignado', $i->tamborAsignado);
        $datos->bindParam(':referencia', $i->referencia);
        $datos->bindParam(':idAlmacen', $i->idAlmacen);
        $datos->execute();
    }

    $mensaje = 'Se ha guardado el registro';

    $con->commit();
    echo json_encode(['error' => false, 'message' => $mensaje]);
} catch (Exception $e) {
    $con->rollBack();
    echo json_encode(['error' => true, 'message' => $e->getMessage() . '. Line ' . $e->getLine()]);
}
