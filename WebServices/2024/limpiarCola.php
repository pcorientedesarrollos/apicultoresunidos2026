<?php

header('Access-Control-Allow-Origin: *');
header('Content-Type: text/plain');

function response($tipo)
{

    include_once '../DAOConeccion/conexionWebServices.php';
    date_default_timezone_set('America/Merida');
    $pdo = new conePDO();
    // $dbh = $pdo->conectar('apicultores2019');
    // $dbh = $pdo->conectar('apicultores2020');
    // $dbh = $pdo->conectar('mielorganica2020');
    // $dbh = $pdo->conectar('apicultores2021');
    // $dbh = $pdo->conectar('mielorganica2021');
    // $dbh = $pdo->conectar('apicultores2022');
    // $dbh = $pdo->conectar('apicultores2023');
    $dbh = $pdo->conectar('apicultores2024');
    // $dbh = $pdo->conectar('mielorganica2022');
    // $dbh = $pdo->conectar('mielorganica2023');

    switch ($tipo) {
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

    $query = $dbh->prepare($consultaEquipos);
    $query->execute();
    if ($query == false) {
        throw new Exception($dbh->errorInfo());
    }
    echo json_encode(['error' => false, 'message' => $query->rowCount()]);
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
