<?php

include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$con = $pdo->conectar();

$idLoteInterno = $_GET["idLoteInterno"];

$tipo = $_GET['tipoMiel'];

switch($tipo){
    case '1':
        $tamboresLotes = 'tamboreslotes';
        $almacen = 'almacen';
        $almacenEncabezado = 'almacenencabezado';
        break;
    case '2':
        $tamboresLotes = 'tamboreslotes_organico';
        $almacen = 'almacen_organico';
        $almacenEncabezado = 'almacenencabezado_organico';
        break;
    case '5':
        $tamboresLotes = 'tamboreslotes_mantequilla';
        $almacen = 'almacen_mantequilla';
        $almacenEncabezado = 'almacenencabezado_mantequilla';
        break;
    case '6':
        $tamboresLotes = 'tamboreslotes_altiplano';
        $almacen = 'almacen_altiplano';
        $almacenEncabezado = 'almacenencabezado_altiplano';
        break;
    case '7':
        $tamboresLotes = 'tamboreslotes_naranjo';
        $almacen = 'almacen_naranjo';
        $almacenEncabezado = 'almacenencabezado_naranjo';
        break;
        
    case '8':
        $tamboresLotes = 'tamboreslotes_aguacate';
        $almacen = 'almacen_aguacate';
        $almacenEncabezado = 'almacenencabezado_aguacate';
        break;
    case '9':
        $tamboresLotes = 'tamboreslotes_mezquite';
        $almacen = 'almacen_mezquite';
        $almacenEncabezado = 'almacenencabezado_mezquite';
        break;
}

$sql = "SELECT  tex.idLoteInterno, pr.idSagarpa
            FROM $tamboresLotes tex
            INNER JOIN $almacen al ON al.idAlmacen = tex.folioTambor
            INNER JOIN $almacenEncabezado ale ON ale.idAlmacen = al.idAlmacenEncabezado
            INNER JOIN proveedor pr ON pr.idProveedor = ale.idProveedor
            WHERE tex.idLoteInterno = :idLoteInterno AND tex.tipo = '0'
            GROUP BY idSagarpa
        UNION
            SELECT  tex.idLoteInterno, pr.idSagarpa
            FROM $tamboresLotes tex
            INNER JOIN almacensobrantes al ON al.consecutivo = tex.folioTambor
            INNER JOIN proveedor pr
            WHERE tex.idLoteInterno = :idLoteInterno AND tex.tipo = '1' AND al.tipoDeMiel = '" . $tipo . "' AND pr.idProveedor = '83'
            GROUP BY idSagarpa";
$datos = $con->prepare($sql);
$datos->bindParam(':idLoteInterno', $idLoteInterno);
$datos->execute();

$array = array();

if ($datos == false) {
    throw new Exception($con->errorInfo());
} else {
    while ($rs = $datos->fetch()) {
        $detalleAnalisis = new stdClass();
        $detalleAnalisis->idSagarpa = $rs["idSagarpa"];
        $array[] = $detalleAnalisis;
    }
    echo json_encode($array);
}
?>