<?php

header('Access-Control-Allow-Origin: *');
include_once '../DAOConeccion/conexionWebServices.php';
$pdo = new conePDO();
// $con = $pdo->conectar('apicultores2019');
// $con = $pdo->conectar('apicultores2020');
// $con = $pdo->conectar('mielorganica2020');
// $con = $pdo->conectar('apicultores2021');
// $con = $pdo->conectar('mielorganica2021');
// $con = $pdo->conectar('apicultores2022');
// $con = $pdo->conectar('apicultores2023');
$con = $pdo->conectar('apicultores2024');
// $con = $pdo->conectar('mielorganica2022');
// $con = $pdo->conectar('mielorganica2023');


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
            case '5':
                $laboratorio = 'laboratorio_mantequilla';
                $almacen = 'almacen_mantequilla';
                $almacenEncabezado = 'almacenencabezado_mantequilla';
                $pref = 'M-';
                break;
            case '6':
                $laboratorio = 'laboratorio_altiplano';
                $almacen = 'almacen_altiplano';
                $almacenEncabezado = 'almacenencabezado_altiplano';
                $pref = 'A-';
                break;
            case '7':
                $laboratorio = 'laboratorio_naranjo';
                $almacen = 'almacen_naranjo';
                $almacenEncabezado = 'almacenencabezado_naranjo';
                $pref = 'N-';
                break;
            case '8':
                $laboratorio = 'laboratorio_aguacate';
                $almacen = 'almacen_aguacate';
                $almacenEncabezado = 'almacenencabezado_aguacate';
                $pref = 'N-';
                break;
            case '9':
                $laboratorio = 'laboratorio_mezquite';
                $almacen = 'almacen_mezquite';
                $almacenEncabezado = 'almacenencabezado_mezquite';
                $pref = 'N-';
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