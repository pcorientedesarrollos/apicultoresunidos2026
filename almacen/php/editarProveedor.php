<?php

include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$con = $pdo->conectar();

$idAlmacen = $_GET["idAlmacen"];
$idProveedor = $_GET["idProveedor"];
$miel = $_GET['miel'];
if (isset($_GET['cubeta'])) {
    if ($miel == '1') {
        $almacenencabezado = 'cubetasencabezado';
    } else if ($miel == '2') {
        $almacenencabezado = 'cubetasencabezado_organico';
    }else if ($miel == '5') {
        $almacenencabezado = 'cubetasencabezado_mantequilla';
    }else if ($miel == '6') {
        $almacenencabezado = 'cubetasencabezado_altiplano';
    }else if ($miel == '7') {
        $almacenencabezado = 'cubetasencabezado_naranjo';
    }else if ($miel == '8') {
        $almacenencabezado = 'cubetasencabezado_aguacate';
    }else if ($miel == '9') {
        $almacenencabezado = 'cubetasencabezado_mezquite';
    }
} else {
    if ($miel == '1') {
        $almacenencabezado = 'almacenencabezado';
    } else if ($miel == '2') {
        $almacenencabezado = 'almacenencabezado_organico';
    } else if ($miel == '5') {
        $almacenencabezado = 'almacenencabezado_mantequilla';
    } else if ($miel == '6') {
        $almacenencabezado = 'almacenencabezado_altiplano';
    } else if ($miel == '7') {
        $almacenencabezado = 'almacenencabezado_naranjo';
    } else if ($miel == '8') {
        $almacenencabezado = 'almacenencabezado_aguacate';
    } else if ($miel == '9') {
        $almacenencabezado = 'almacenencabezado_mezquite';
    }
}

$sqlUp = "UPDATE $almacenencabezado SET idProveedor = :idProveedor WHERE idAlmacen = :idAlmacen";
$dats = $con->prepare($sqlUp);
$dats->bindParam(':idAlmacen', $idAlmacen);
$dats->bindParam(':idProveedor', $idProveedor);
$dats->execute();
