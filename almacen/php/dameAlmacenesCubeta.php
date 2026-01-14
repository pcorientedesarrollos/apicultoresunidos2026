<?php

include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$con = $pdo->conectar();

try {

    $arrayObtenido = file_get_contents('php://input');
    $info = json_decode($arrayObtenido);

    switch ($info[0]) {
        case '1':
            $almacenencabezado_tabla = 'cubetasencabezado';
            $almacen_tabla = 'cubetasdetalle';
            break;
        case '2':
            $almacenencabezado_tabla = 'cubetasencabezado_organico';
            $almacen_tabla = 'cubetasdetalle_organico';
            break;
        case '5':
            $almacenencabezado_tabla = 'cubetasencabezado_mantequilla';
            $almacen_tabla = 'cubetasdetalle_mantequilla';
            break;
        case '6':
            $almacenencabezado_tabla = 'cubetasencabezado_altiplano';
            $almacen_tabla = 'cubetasdetalle_altiplano';
            break;
        case '7':
            $almacenencabezado_tabla = 'cubetasencabezado_naranjo';
            $almacen_tabla = 'cubetasdetalle_naranjo';
            break;
        case '8':
            $almacenencabezado_tabla = 'cubetasencabezado_aguacate';
            $almacen_tabla = 'cubetasdetalle_aguacate';
            break;
        case '9':
            $almacenencabezado_tabla = 'cubetasencabezado_mezquite';
            $almacen_tabla = 'cubetasdetalle_mezquite';
            break;
    }

    if (isset($_GET['mes'])) {
        $mes = $_GET['mes'];
    }

    $sql = "SELECT *, sum(alm.neto) kgs
    FROM $almacenencabezado_tabla al 
    LEFT JOIN proveedor pr 
    ON pr.idProveedor = al.idProveedor
    LEFT JOIN $almacen_tabla alm
    on al.idAlmacen = alm.idAlmacenEncabezado
    LEFT JOIN direccion dr 
    on pr.idDireccion = dr.idDireccion
    LEFT JOIN localidades lo ON lo.idlocalidad = dr.idlocalidad";

    if ($info[2] == '1') {
        if (isset($_GET['mes'])) {
            $sql .= " WHERE al.idAlmacen = alm.idAlmacenEncabezado AND SUBSTR(fecha FROM 6 FOR 2) = " . $mes;
        } else {
            $sql .= " WHERE al.idAlmacen = alm.idAlmacenEncabezado";
        }
    } else if ($info[2] == '2') {
        if (isset($_GET['mes'])) {
            $sql .= " WHERE al.idAlmacen NOT IN (SELECT alm.idAlmacenEncabezado FROM $almacen_tabla alm) AND SUBSTR(fecha FROM 6 FOR 2) = " . $mes;
        } else {
            $sql .= " WHERE al.idAlmacen NOT IN (SELECT alm.idAlmacenEncabezado FROM $almacen_tabla alm)";
        }
    } else if (isset($_GET['mes'])) {
        $sql .= " WHERE SUBSTR(fecha FROM 6 FOR 2) = " . $_GET['mes'];
    }


    $sql .= " group by al.idAlmacen ORDER BY al.idAlmacen DESC";
    $datos = $con->prepare($sql);
    $datos->execute();

    if ($datos == false) {
        throw new Exception($con->errorInfo());
    }

    $array = array();
    while ($rs = $datos->fetch()) {
        $almacenCubeta = new stdClass();
        $almacenCubeta->fecha = $rs["fecha"];
        $almacenCubeta->id = $rs[0];
        $almacenCubeta->proveedor = $rs["nombre"];
        $almacenCubeta->folioEntradaTambor = $rs["folioEntradaTambor"];
        $almacenCubeta->localidad = $rs["localidad"];
        $almacenCubeta->kgs = $rs['kgs'];

        array_push($array, $almacenCubeta);
    }

    echo json_encode(['error' => false, 'resultado' => $array]);
} catch (Exception $e) {
    echo json_encode(['error' => true, 'message' => $e->getMessage()]);
}
