<?php

include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$con = $pdo->conectar();
try {
    if (!isset($_GET['id']) || !isset($_GET['idTipoDeMiel'])) {
        throw new Exception('No se especificaron parámetros');
    } else {
        $id = $_GET['id'];
        $idTipoDeMiel = $_GET['idTipoDeMiel'];
        $sobrante = $_GET['sobrante'];
        if ($idTipoDeMiel == '1') {
            $tamboresexperimentales_tabla = 'tamboresexperimentales';
            $tamboreslotes_tabla = 'tamboreslotes';
        } else if($idTipoDeMiel == '2') {
            $tamboresexperimentales_tabla = 'tamboresexperimentales_organico';
            $tamboreslotes_tabla = 'tamboreslotes_organico';
        } else if($idTipoDeMiel == '5') {
            $tamboresexperimentales_tabla = 'tamboresexperimentales_mantequilla';
            $tamboreslotes_tabla = 'tamboreslotes_mantequilla';
        } else if($idTipoDeMiel == '6') {
            $tamboresexperimentales_tabla = 'tamboresexperimentales_altiplano';
            $tamboreslotes_tabla = 'tamboreslotes_altiplano';
        } else if($idTipoDeMiel == '7') {
            $tamboresexperimentales_tabla = 'tamboresexperimentales_naranjo';
            $tamboreslotes_tabla = 'tamboreslotes_naranjo';
        } else if($idTipoDeMiel == '8') {
            $tamboresexperimentales_tabla = 'tamboresexperimentales_aguacate';
            $tamboreslotes_tabla = 'tamboreslotes_aguacate';
        } else if($idTipoDeMiel == '9') {
            $tamboresexperimentales_tabla = 'tamboresexperimentales_mezquite';
            $tamboreslotes_tabla = 'tamboreslotes_mezquite';
        }
    }


    //     $sql = $con->prepare("SELECT al.consecutivo AS idAlmacen, 'S' AS identificador, CONCAT(s.codigo, '-', al.consecutivo) AS Folio, al.idEntradaSobrante Entrada, al.fecha Fecha, '' AS Proveedor, '' AS idSagarpa,
    //     '' AS Localidad, z.nombre AS zona, '' AS Lista, al.bruto Bruto, al.tara Tara, al.neto Neto,
    //     '' AS Precio, te.idLoteExperimental AS LExperimental, tl.idLoteInterno AS LInterno, al.tipoDeMiel AS idTipoDeMiel
    //     FROM almacensobrantes al
    //     LEFT JOIN sobrantes s ON s.idSobrante = al.sobrante
    //     LEFT JOIN zonastambores z ON z.idZonaTambor = al.zona 
    //     LEFT JOIN $tamboresexperimentales_tabla te ON te.tipo = '1' AND al.tipoDeMiel = :idTipoDeMiel AND te.clasificacion = :sobrante AND te.folioTambor = al.consecutivo
    // LEFT JOIN $tamboreslotes_tabla tl ON tl.tipo = '1' AND al.tipoDeMiel = :idTipoDeMiel AND tl.clasificacion = :sobrante AND tl.folioTambor = al.consecutivo
    //     WHERE al.tipoDeMiel = :idTipoDeMiel AND al.consecutivo = :idAlmacen AND al.sobrante = :sobrante");
    $sql = $con->prepare("SELECT al.consecutivo AS idAlmacen, 'S' AS identificador, CONCAT(s.codigo, '-', al.consecutivo) AS Folio, al.idEntradaSobrante, al.consecutivoEntrada AS Entrada, al.fecha Fecha, '' AS Proveedor, '' AS idSagarpa,
'' AS Localidad, z.nombre AS zona, '' AS Lista, al.bruto Bruto, al.tara Tara, al.neto Neto,
'' AS Precio, te.idLoteExperimental AS LExperimental, tl.idLoteInterno AS LInterno, al.tipoDeMiel AS idTipoDeMiel
FROM almacensobrantes al
LEFT JOIN sobrantes s ON s.idSobrante = al.sobrante
LEFT JOIN zonastambores z ON z.idZonaTambor = al.zona 
LEFT JOIN $tamboresexperimentales_tabla te ON te.tipo = '1' AND al.tipoDeMiel = :idTipoDeMiel AND te.clasificacion = :sobrante AND te.folioTambor = al.consecutivo
LEFT JOIN $tamboreslotes_tabla tl ON tl.tipo = '1' AND al.tipoDeMiel = :idTipoDeMiel AND tl.clasificacion = :sobrante AND tl.folioTambor = al.consecutivo
WHERE al.tipoDeMiel = :idTipoDeMiel AND al.consecutivo = :idAlmacen AND al.sobrante = :sobrante");
    $sql->bindParam(':idTipoDeMiel', $idTipoDeMiel);
    $sql->bindParam(':idAlmacen', $id);
    $sql->bindParam(':sobrante', $sobrante);
    $sql->execute();
    if ($sql == false) {
        throw new Exception($con->errorInfo());
    } else {
        $tambor = $sql->fetch(PDO::FETCH_ASSOC);
    }

    if ($tambor == false) {
        echo json_encode(['error' => false, 'message' => 'Folio no encontrado', 'tambor' => $tambor]);
    } else {
        echo json_encode(['error' => false, 'tambor' => $tambor]);
    }
} catch (Exception $e) {
    echo json_encode(['error' => true, 'message' => $e->getMessage()]);
}
