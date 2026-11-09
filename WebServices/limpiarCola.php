<?php

header('Access-Control-Allow-Origin: *');
header('Content-Type: text/plain');

function response($tipo)
{

    include_once __DIR__ . '/bdImpresion.php';
    date_default_timezone_set('America/Merida');
    $pdo = new conePDO();

    switch ($tipo) {
        default:
            throw new Exception('Tipo no válido: ' . $tipo);
        case 'ALMACEN':
            $consultaEquipos = "DELETE FROM impresion WHERE estado IN(0, 1)";
            break;
        case 'SALIDA':
            $consultaEquipos = "DELETE FROM impresion WHERE estado IN(3, 4)";
            break;
        case 'EQUIPOS':
            $consultaEquipos = "DELETE FROM impresion WHERE estado = 2";
            break;
        case 'SOBRANTE':
            $consultaEquipos = "DELETE FROM impresion WHERE estado = 5";
            break;
    }

    $borrados = 0;
    foreach (dameBdsImpresion() as $bd) {
        $dbh = $pdo->conectar($bd);
        $query = $dbh->prepare($consultaEquipos);
        $query->execute();
        $borrados += $query->rowCount();
    }
    echo json_encode(['error' => false, 'message' => $borrados]);
}

try {
    if (isset($_POST['tipo'])) {
        response($_POST['tipo']);
    } else {
        throw new Exception('No se ha recibido el parámetro esperado');
    }
} catch (Exception $e) {
    echo json_encode(['error' => true, 'message' => $e->getMessage()]);
}
