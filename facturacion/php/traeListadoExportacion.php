<?php

include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$conexion = $pdo->conectar();
$conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

if (isset($_GET['id'])) {

    try {

        if (!isset($_GET['id'])) {
            throw new Exception('No se han recibido parámetros');
        } else {
            $id = $_GET['id'];
            $tipoDeMiel = $_GET['tipoMiel'];
        }
        switch ($tipoDeMiel) {
            case '1':
                $entradaysalida = 'entradaysalida';
                break;
            case '2':
                $entradaysalida = 'entradaysalida_organico';
                break;
            case '5':
                $entradaysalida = 'entradaysalida_mantequilla';
                break;
            case '6':
                $entradaysalida = 'entradaysalida_altiplano';
                break;
            case '7':
                $entradaysalida = 'entradaysalida_naranjo';
                break;
            case '8':
                $entradaysalida = 'entradaysalida_aguacate';
                break;
            case '9':
                $entradaysalida = 'entradaysalida_mezquite';
                break;
            default:
                throw new Exception('Tipo de miel no válido');
                break;
        }
        $sqlListado = "SELECT rp.idTamborPeso, rp.precio, rp.tipoDeCliente, rp.idCliente, rp.condicionPago, rp.tiempoPago, 
        c.lote, rp.destino, rp.totalNeto, c.fechaImpresion AS fecha, tdm.tipoDeMiel, c.idLoteInterno, rp.fechaFactura,
        (SELECT COUNT(idPesoTambo)FROM tamboreslistapesos WHERE idTamborPeso = rp.idTamborPeso) as cantidadTambores
        FROM listadepesos rp
        LEFT JOIN $entradaysalida c ON c.lote = rp.lote
        LEFT JOIN tiposdemiel tdm ON rp.tipoMiel = tdm.idTipoDeMiel
        LEFT JOIN clientesexportadores cle ON cle.idClienteExportador = rp.idCliente
        WHERE rp.idTamborPeso = :id";
        $queryListado = $conexion->prepare($sqlListado);
        $queryListado->bindParam(':id', $id);
        $queryListado->execute();
        if ($queryListado == FALSE) {
            throw new Exception($conexion->errorInfo());
        } else {
            if ($queryListado->rowCount() == 1) {
                $response = $queryListado->fetch(PDO::FETCH_ASSOC);
                if ($response['tipoDeCliente'] == '10') {
                    $query = "SELECT *
                    FROM clientesexportadores
                    WHERE idClienteExportador = :idClienteExportador";
                    $datosResp = $conexion->prepare($query);
                    $datosResp->bindParam(':idClienteExportador', $response['idCliente']);
                    $datosResp->execute();
                    $response['cliente'] = $datosResp->fetch(PDO::FETCH_ASSOC);
                } else if ($response['tipoDeCliente'] == '6') {
                    $seleccionarClientes = $conexion->prepare("SELECT nombre FROM clientes WHERE idCliente = :idCliente");
                    $seleccionarClientes->bindParam(':idCliente', $response['idCliente']);
                    $seleccionarClientes->execute();
                    $response['cliente'] = $seleccionarClientes->fetch(PDO::FETCH_ASSOC);
                }
            }
        }

        echo json_encode(['error' => false, 'data' => $response]);
    } catch (Exception $e) {
        echo json_encode(['error' => true, 'message' => $e->getMessage(), 'data' => $response]);
        exit();
    }
} else {
    $response = array();
    try {

        if (!isset($_GET['clasificacion']) && !isset($_GET['miel'])) {
            throw new Exception('No se han recibido parámetros');
        } else {
            $clasificacion = $_GET['clasificacion'];
            $tipoDeMiel = $_GET['miel'];
        }
        switch ($tipoDeMiel) {
            case '1':
                $entradaysalida = 'entradaysalida';
                break;
            case '2':
                $entradaysalida = 'entradaysalida_organico';
                break;
            case '5':
                $entradaysalida = 'entradaysalida_mantequilla';
                break;
            case '6':
                $entradaysalida = 'entradaysalida_altiplano';
                break;
            case '7':
                $entradaysalida = 'entradaysalida_naranjo';
                break;
            case '8':
                $entradaysalida = 'entradaysalida_aguacate';
                break;
            case '9':
                $entradaysalida = 'entradaysalida_mezquite';
                break;
            default:
                throw new Exception('Tipo de miel no válido');
                break;
        }
        $sqlListado = "SELECT rp.idTamborPeso, rp.importe, rp.tipoDeCliente, rp.idCliente, rp.tipoMiel,
        c.lote, rp.destino, rp.folioFactura, rp.totalNeto, c.fechaImpresion AS fecha, tdm.tipoDeMiel, c.idLoteInterno, rp.fechaFactura,
        (SELECT COUNT(idPesoTambo)FROM tamboreslistapesos WHERE idTamborPeso = rp.idTamborPeso) as cantidadTambores
        FROM listadepesos rp
        LEFT JOIN $entradaysalida c ON c.lote = rp.lote
        LEFT JOIN tiposdemiel tdm ON rp.tipoMiel = tdm.idTipoDeMiel
        LEFT JOIN clientesexportadores cle ON cle.idClienteExportador = rp.idCliente
        WHERE rp.tipoMiel = :miel AND c.idClasificacion = :clasificacion
        ORDER BY c.fechaImpresion DESC";
        $queryListado = $conexion->prepare($sqlListado);
        $queryListado->bindParam(':miel', $tipoDeMiel);
        $queryListado->bindParam(':clasificacion', $clasificacion);
        $queryListado->execute();
        if ($queryListado == FALSE) {
            throw new Exception($conexion->errorInfo());
        }

        foreach ($queryListado->fetchAll(PDO::FETCH_ASSOC) as $datos) {
            if ($datos['tipoDeCliente'] == '10') {
                $query = "SELECT *
             FROM clientesexportadores
             WHERE idClienteExportador = :idClienteExportador";
                $datosResp = $conexion->prepare($query);
                $datosResp->bindParam(':idClienteExportador', $datos['idCliente']);
                $datosResp->execute();
                $datos['cliente'] = $datosResp->fetch(PDO::FETCH_ASSOC);
            } else if ($datos['tipoDeCliente'] == '6') {
                $seleccionarClientes = $conexion->prepare("SELECT nombre FROM clientes WHERE idCliente = :idCliente");
                $seleccionarClientes->bindParam(':idCliente', $datos['idCliente']);
                $seleccionarClientes->execute();
                $datos['cliente'] = $seleccionarClientes->fetch(PDO::FETCH_ASSOC);
            }
            array_push($response, $datos);
        }

        echo json_encode(['error' => false, 'data' => $response]);
    } catch (Exception $e) {
        echo json_encode(['error' => true, 'message' => $e->getMessage(), 'data' => $response]);
        exit();
    }
}
