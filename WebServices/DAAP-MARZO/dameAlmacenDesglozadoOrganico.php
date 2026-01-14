<?php

header('Access-Control-Allow-Origin: *');
include_once '../DAOConeccion/conexionWebServices.php';
$pdo = new conePDO();
// $con = $pdo->conectar('mielorganica2020');
$con = $pdo->conectar('mielorganica2022');

$con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$resultado = array();

$sqlImpresion = "SELECT idImpresion, idTipoDeMiel FROM impresion WHERE estado = 0 ORDER BY idAlmacen";
$dato = $con->prepare($sqlImpresion);
$dato->execute();
if ($dato == false) {
    $resultado = [];
} else {
    foreach ($dato->fetchAll(PDO::FETCH_ASSOC) as $impresion) {
        switch ($impresion['idTipoDeMiel']) {
            case '1':
                $laboratorio = 'laboratorio';
                $almacen = 'almacen';
                $almacenEncabezado = 'almacenencabezado';
                $pref = 'C-';
                break;
            case '2':
                $laboratorio = 'laboratorio_organico';
                $almacen = 'almacen_organico';
                $almacenEncabezado = 'almacenencabezado_organico';
                $pref = 'O-';
                break;
        }


        $sql = "SELECT pr.nombre AS proveedor, l.localidad, al.tara, al.bruto, al.idAlmacen, zt.nombre AS zona,
                ale.fecha, al.neto, imp.idImpresion, tdm.tipoDeMiel, CONCAT('" . $pref . "', al.idAlmacen) AS barcode
                FROM $almacen al
                LEFT JOIN $laboratorio lab ON lab.idAlmacen = al.idAlmacen
                INNER JOIN $almacenEncabezado ale ON ale.idAlmacen = al.idAlmacenEncabezado
                INNER JOIN proveedor pr ON pr.idProveedor = ale.idProveedor
                INNER JOIN direccion dr ON pr.idDireccion = dr.idDireccion
                LEFT JOIN localidades l ON l.idlocalidad = dr.idlocalidad
                LEFT JOIN resultadofinal rs ON rs.idresultadoFinal = lab.resultadoFinal
                INNER JOIN impresion imp ON al.idAlmacen = imp.idAlmacen 
                INNER JOIN tiposdemiel tdm ON imp.idTipoDeMiel = tdm.idTipoDeMiel
                INNER JOIN zonastambores zt ON zt.idZonaTambor = al.zona
                WHERE imp.estado = 0 AND imp.idImpresion = :idImpresion";
        $datos = $con->prepare($sql);
        $datos->bindParam(':idImpresion', $impresion['idImpresion']);
        $datos->execute();

        // if ($datos != false) {
            array_push($resultado, $datos->fetch(PDO::FETCH_ASSOC));
        // }
    }
}
echo json_encode($resultado);