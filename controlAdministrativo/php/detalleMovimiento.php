<?php

include_once '../../DAOConeccion/conePDO.php';
include_once './nombreDePersona.php';
$pdo = new conePDO();
$dbh = $pdo->conectar();

$post = json_decode(file_get_contents('php://input'));
if ($post) {
    $resultado = array();
    try {
        $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $consulta = $dbh->prepare("SELECT * FROM cajachica WHERE idCajaChica = :idCajaChica");
        $consulta->bindParam(':idCajaChica', $post->idCajaChica);
        $consulta->execute();
        $resultado['encabezado'] = $consulta->fetch(PDO::FETCH_ASSOC);
        $resultado['encabezado']['tipoLg'] = $resultado['encabezado']['tipo'] == 0 ? 'INGRESO' : 'EGRESO';
        $resultado['encabezado']['idNombre'] = $resultado['encabezado']['nombre'];
        $resultado['encabezado']['nombre'] = retornarNombre($dbh, $resultado['encabezado']['tipoDeCliente'], $resultado['encabezado']['nombre']);
        $detalle = $dbh->prepare("SELECT ccd.*, b.banco, cb.numDeCuenta FROM cajachicadetalle ccd
                                    LEFT JOIN bancos b ON ccd.idBanco = b.idBanco
                                    LEFT JOIN cuentasbancarias cb ON ccd.idCuenta = cb.idCuenta
                                    WHERE idCajaChica = :idCajaChica ORDER BY ccd.idDetalle");
        $detalle->bindParam(':idCajaChica', $post->idCajaChica);
        $detalle->execute();

        $resultado['detalle'] = $detalle->fetchAll(PDO::FETCH_ASSOC);
        
        echo json_encode($resultado);
        exit();
    } catch (Exception $e) {
        echo json_encode(['error'=>true, 'message'=>$e->getMessage()]);
        exit();
    }
} else {
    exit();
}
