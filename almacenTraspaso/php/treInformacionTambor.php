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
                $almacen_tabla = 'almacentraspaso';
                $tamboreslotes_tabla = 'tamboreslotes';
                break;
            case '2':
                $almacen_tabla = 'almacentraspaso_organico';
                $tamboreslotes_tabla = 'tamboreslotes_organico';
                break;
            default:
                throw new Exception('El tipo de miel seleccionado no es válido');
                break;
        }
    }

    $consulta = "SELECT alm.bruto, alm.tara, alm.neto, alm.humedad, lb.color, lb.idFloracion
        FROM $almacen_tabla alm
        LEFT JOIN laboratorio_traspaso lb ON lb.idAlmacen = alm.idAlmacen AND lb.tipoDeMiel = :miel
        WHERE alm.idAlmacen = :folioTambor";
    if (isset($_GET['sinLote'])) {
        $consulta .= " AND alm.estado = 0;";
    } else {
        if (isset($_GET['idLoteInterno'])) {
            $idLoteInterno = $_GET['idLoteInterno'];
            $consulta .= " AND alm.estado IN (2,4) AND alm.idAlmacen IN (SELECT folioTambor FROM $tamboreslotes_tabla WHERE idLoteInterno = $idLoteInterno AND tipo = 2);";
        } else {
            throw new Exception($con->errorInfo());
            // O dejar pasar sin la condición del lote
        }
    }
    $consulta = $con->prepare($consulta);
    $consulta->bindParam(':folioTambor', $folioTambor);
    $consulta->bindParam(':miel', $_GET['tipoDeMiel']);
    $consulta->execute();
    if ($consulta == false) {
        throw new Exception($con->errorInfo());
    } else {
        $resultado = $consulta->fetch(PDO::FETCH_ASSOC);
    }


    echo json_encode(['error' => false, 'message' => 'Consulta realizada', 'data' => $resultado]);
} catch (Exception $e) {
    echo json_encode(['error' => true, 'message' => $e->getMessage()]);
}
