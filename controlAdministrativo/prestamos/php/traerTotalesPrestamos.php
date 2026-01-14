<?php

date_default_timezone_set("America/Merida");

function getData($con)
{
    $seleccinaPrestamos = $con->prepare(" SELECT SUM(cantidad) as pendientes,
    (SELECT SUM(pd.cantidad) as abonos
        FROM prestamodetalle pd
        LEFT JOIN prestamos p
        ON pd.idPrestamo = p.idPrestamo
        WHERE p.dacc = 0) as abonos
    FROM prestamos
    WHERE dacc = 0");
    $seleccinaPrestamos->execute();
    $seleccinaPrestamos->bindColumn('pendientes', $Pendiente);
    $seleccinaPrestamos->bindColumn('abonos', $Abonos);
    $seleccinaPrestamos->fetch(PDO::FETCH_BOUND);

    $resultado = array(
    'pendientes'=>$Pendiente,
    'abonos'=>$Abonos
    );
    return $resultado;
}

if (isset($_GET['totalesPrestamos'])) {
    include_once '../../../DAOConeccion/conePDO.php';
    $pdo = new conePDO();
    $con = $pdo->conectar();
    try {
        $resultado = getData($con);
        echo json_encode(['error'=>false, 'message'=>'Query executed successfully!' , 'content'=>$resultado]);
    } catch (Exception $e) {
        echo json_encode(['error'=>true, 'message'=>$e->getMessage(), 'content'=>[]]);
    }
}
