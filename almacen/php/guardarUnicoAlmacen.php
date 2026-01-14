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

    if (isset($_GET['miel'])) {
        switch ($_GET['miel']) {
            case '1':
                $detalle = 'almacen';
                $encabezado = 'almacenencabezado';
                $descarga = 'entradaysalida';
                break;
            case '2':
                $detalle = 'almacen_organico';
                $encabezado = 'almacenencabezado_organico';
                $descarga = 'entradaysalida_organico';
                break;
            case '5':
                $detalle = 'almacen_mantequilla';
                $encabezado = 'almacenencabezado_mantequilla';
                $descarga = 'entradaysalida_mantequilla';
                break;
            case '6':
                $detalle = 'almacen_altiplano';
                $encabezado = 'almacenencabezado_altiplano';
                $descarga = 'entradaysalida_altiplano';
                break;
            case '7':
                $detalle = 'almacen_naranjo';
                $encabezado = 'almacenencabezado_naranjo';
                $descarga = 'entradaysalida_naranjo';
                break;
            case '8':
                $detalle = 'almacen_aguacate';
                $encabezado = 'almacenencabezado_aguacate';
                $descarga = 'entradaysalida_aguacate';
                break;
            case '9':
                $detalle = 'almacen_mezquite';
                $encabezado = 'almacenencabezado_mezquite';
                $descarga = 'entradaysalida_mezquite';
                break;
            default:
                throw new Exception('Tipo de miel inválido');
                break;
        }
    }

    $con->beginTransaction();

    $sql = "INSERT INTO $detalle (idAlmacenEncabezado, zona, trazabilidad, pesoLista, bruto, tara, neto, diferencia, humedad, autorizado, precio, costoTotal, estado, referencia) VALUES(:id, :zona, :trazabilidad, :pesoLista, :bruto, :tara, :neto, :diferencia, :humedad, '0', '0', '0', '0', :referencia)";
    $dato = $con->prepare($sql);
    $dato->bindParam(':id', $id);
    $dato->bindParam(':zona', $info->zona);
    $dato->bindParam(':trazabilidad', $info->trazabilidad);
    $dato->bindParam(':pesoLista', $info->pesoLista);
    $dato->bindParam(':bruto', $info->bruto);
    $dato->bindParam(':tara', $info->tara);
    $dato->bindParam(':neto', $info->neto);
    $dato->bindParam(':diferencia', $info->diferencia);
    $dato->bindParam(':humedad', $info->humedad);
    $dato->bindParam(':referencia', $info->referencia);
    $dato->execute();
    if ($dato == FALSE) {
        throw new Exception($con->errorInfo());
    } else {

        $sqlSelect = "SELECT e.idReporteDescarga
        FROM $encabezado e INNER JOIN $detalle d ON d.idAlmacenEncabezado = e.idAlmacen
        WHERE e.idAlmacen = :id";
        $dat = $con->prepare($sqlSelect);
        $dat->bindParam(':id', $id);
        $dat->execute();
        $dat->bindColumn('idReporteDescarga', $idReporteDescarga);
        $dat->fetch(PDO::FETCH_BOUND);

        $sqlDescarga = "UPDATE $descarga SET lote = :lote WHERE idReporte = :idReporte";
        $datos = $con->prepare($sqlDescarga);
        $datos->bindParam(':lote', $id);
        $datos->bindParam(':idReporte', $idReporteDescarga);
        $datos->execute();
        if ($datos == FALSE) {
            throw new Exception($con->errorInfo());
        }else{
            $mensaje = 'Se ha guardado el registro';
        }

    }

    $con->commit();
    echo json_encode(['error' => false, 'message' => $mensaje]);
} catch (Exception $e) {
    $con->rollBack();
    echo json_encode(['error' => true, 'message' => $e->getMessage() . '. Line ' . $e->getLine()]);
}
