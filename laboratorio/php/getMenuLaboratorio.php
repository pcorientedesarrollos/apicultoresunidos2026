<?php

include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO;
$con = $pdo->conectar();

$tipoDeMiel = file_get_contents('php://input');
$resultado = [];
try {
    if (!$tipoDeMiel) {
        throw new Exception('Se esperaban argumentos');
    }
    switch ($tipoDeMiel) {
        case '1':
            $almacen_tabla = 'almacen';
            $almacenencabezado_tabla = 'almacenencabezado';
            $laboratorio_tabla = 'laboratorio';
            break;
        case '2':
            $almacen_tabla = 'almacen_organico';
            $almacenencabezado_tabla = 'almacenencabezado_organico';
            $laboratorio_tabla = 'laboratorio_organico';
            break;
        case '5':
            $almacen_tabla = 'almacen_mantequilla';
            $almacenencabezado_tabla = 'almacenencabezado_mantequilla';
            $laboratorio_tabla = 'laboratorio_mantequilla';
            break;
        case '6':
            $almacen_tabla = 'almacen_altiplano';
            $almacenencabezado_tabla = 'almacenencabezado_altiplano';
            $laboratorio_tabla = 'laboratorio_altiplano';
            break;
        case '7':
            $almacen_tabla = 'almacen_naranjo';
            $almacenencabezado_tabla = 'almacenencabezado_naranjo';
            $laboratorio_tabla = 'laboratorio_naranjo';
            break;
            
        case '8':
            $almacen_tabla = 'almacen_aguacate';
            $almacenencabezado_tabla = 'almacenencabezado_aguacate';
            $laboratorio_tabla = 'laboratorio_aguacate';
            break;
        case '9':
            $almacen_tabla = 'almacen_mezquite';
            $almacenencabezado_tabla = 'almacenencabezado_mezquite';
            $laboratorio_tabla = 'laboratorio_mezquite';
            break;
        default:
            throw new Exception('Argumento inválido');
            break;
    }

    $sql = "SELECT al.idAlmacen as id, al.folio, al.fecha, al.clasificacionMiel, al.idProveedor,
    pr.nombre, lo.localidad, count(alm.idAlmacen)muestras 
    FROM $almacenencabezado_tabla al
    LEFT JOIN proveedor pr ON pr.idProveedor = al.idProveedor
    INNER JOIN $almacen_tabla alm ON al.idAlmacen = alm.idAlmacenEncabezado
    LEFT JOIN direccion dr ON pr.idDireccion = dr.idDireccion
    LEFT JOIN localidades lo ON lo.idlocalidad = dr.idlocalidad";

    if (isset($_GET['mes'])) {
        $mes = $_GET['mes'];
        $sql .= " WHERE SUBSTR(al.fecha FROM 6 FOR 2) =" . $mes;
    }

    $sql .= " GROUP BY al.idAlmacen ORDER BY al.idAlmacen DESC";

    $datos = $con->prepare($sql);
    $datos->execute();

    if ($datos == FALSE) {
        throw new Exception($con->errorInfo());
    }

    foreach ($datos->fetchAll(PDO::FETCH_ASSOC) as $elemento) {
        $sqlRegistros = "SELECT count(al.autorizado) as datosAutorizados,
        (SELECT count(la.idAlmacen) FROM $laboratorio_tabla la WHERE la.entradaNo = :num) as registros
        FROM $almacen_tabla al
        WHERE al.autorizado = 1 and al.idAlmacenEncabezado = :num";
        $stmt = $con->prepare($sqlRegistros);
        $stmt->bindParam(':num', $elemento['id']);
        $stmt->execute();
        if ($stmt == FALSE) {
            throw new Exception($con->errorInfo());
        }
        $cantidad = $stmt->fetch(PDO::FETCH_ASSOC);
        $elemento['datosAutorizados'] = $cantidad["datosAutorizados"];
        $elemento['registros'] = $cantidad["registros"];

        array_push($resultado, $elemento);
    }
    echo json_encode(['error' => false, 'message' => '', 'data' => $resultado]);
} catch (Exception $e) {
    echo json_encode(['error' => true, 'message' => $e->getMessage() . '. Line ' . $e->getLine(), 'data' => $resultado]);
}
