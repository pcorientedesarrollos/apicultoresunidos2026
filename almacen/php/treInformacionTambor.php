<?php
include_once "../../DAOConeccion/conePDO.php";
$pdo = new conePDO();
$con = $pdo->conectar();
$con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$resultado = new stdClass();
try {
    if (!isset($_GET['folioTambor']) || !isset($_GET['tipoDeMiel'])) {
        throw new Exception('Folio del tambor o tipo de miel no especificado.');
    } else {
        $folioTambor = $_GET['folioTambor'];
        switch ($_GET['tipoDeMiel']) {
            case '1':
                $almacen_tabla = 'almacen';
                $lab_tabla = 'laboratorio';
                $tamboreslotes_tabla = 'tamboreslotes';
                break;
            case '2':
                $almacen_tabla = 'almacen_organico';
                $lab_tabla = 'laboratorio_organico';
                $tamboreslotes_tabla = 'tamboreslotes_organico';
                break;
            case '5':
                $almacen_tabla = 'almacen_mantequilla';
                $lab_tabla = 'laboratorio_mantequilla';
                $tamboreslotes_tabla = 'tamboreslotes_mantequilla';
                break;
            case '6':
                $almacen_tabla = 'almacen_altiplano';
                $lab_tabla = 'laboratorio_altiplano';
                $tamboreslotes_tabla = 'tamboreslotes_altiplano';
                break;
            case '7':
                $almacen_tabla = 'almacen_naranjo';
                $lab_tabla = 'laboratorio_naranjo';
                $tamboreslotes_tabla = 'tamboreslotes_naranjo';
                break;
            case '8':
                $almacen_tabla = 'almacen_aguacate';
                $lab_tabla = 'laboratorio_aguacate';
                $tamboreslotes_tabla = 'tamboreslotes_aguacate';
                break;
            case '9':
                $almacen_tabla = 'almacen_mezquite';
                $lab_tabla = 'laboratorio_mezquite';
                $tamboreslotes_tabla = 'tamboreslotes_mezquite';
                break;
            default:
                throw new Exception('El tipo de miel seleccionado no es válido');
                break;
        }
    }

    if (isset($_GET['sobrante']) && $_GET['sobrante'] != "0") {
        $sobrante = $_GET['sobrante'];
        $consulta = "SELECT alm.bruto, alm.tara, alm.neto, '--' AS humedad, '--' AS color, 0 AS idFloracion, '1' as tipo, alm.sobrante as clasificacion
        FROM almacensobrantes alm
        WHERE alm.consecutivo = :folioTambor AND sobrante = :sobrante AND tipoDeMiel = :miel";

        if (isset($_GET['sinLote'])) {
            $consulta .= " AND estado = 0";
        } else {
            if (isset($_GET['idLoteInterno'])) {
                $idLoteInterno = $_GET['idLoteInterno'];
                $consulta .= " AND estado IN (2,4) AND alm.consecutivo IN (SELECT folioTambor FROM $tamboreslotes_tabla WHERE idLoteInterno = $idLoteInterno AND tipo = 1 AND clasificacion = $sobrante);";
            } else {
                throw new Exception($con->errorInfo());
        // O dejar pasar sin la condición del lote
            }
        }

        $consulta = $con->prepare($consulta);
        $consulta->bindParam(':folioTambor', $folioTambor);
        $consulta->bindParam(':sobrante', $sobrante);
        $consulta->bindParam(':miel', $_GET['tipoDeMiel']);
        $consulta->execute();
        if ($consulta == false) {
            throw new Exception($con->errorInfo());
        } else {
            $resultado = $consulta->fetch(PDO::FETCH_ASSOC);
        }
    } else {
        $consulta = "SELECT alm.bruto, alm.tara, alm.neto, alm.humedad, lb.color, lb.idFloracion
        FROM $almacen_tabla alm
        LEFT JOIN $lab_tabla lb ON lb.idAlmacen = alm.idAlmacen
        WHERE alm.idAlmacen = :folioTambor";
        if (isset($_GET['sinLote'])) {
            $consulta .= " AND alm.estado = 0;";
        } else {
            if (isset($_GET['idLoteInterno'])) {
                $idLoteInterno = $_GET['idLoteInterno'];
                $consulta .= " AND alm.estado IN (2,4) AND alm.idAlmacen IN (SELECT folioTambor FROM $tamboreslotes_tabla WHERE idLoteInterno = $idLoteInterno AND tipo = 0 );";
            } else {
                throw new Exception($con->errorInfo());
                // O dejar pasar sin la condición del lote
            }
        }
        $consulta = $con->prepare($consulta);
        $consulta->bindParam(':folioTambor', $folioTambor);
        $consulta->execute();
        if ($consulta == false) {
            throw new Exception($con->errorInfo());
        } else {
            $resultado = $consulta->fetch(PDO::FETCH_ASSOC);
        }
    }


    echo json_encode(['error' => false, 'message' => 'Consulta realizada', 'data' => $resultado]);

} catch (Exception $e) {
    echo json_encode(['error' => true, 'message' => $e->getMessage()]);
}
