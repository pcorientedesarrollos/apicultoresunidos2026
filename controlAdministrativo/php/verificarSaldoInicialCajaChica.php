<?php

include_once '../../DAOConeccion/conePDO.php';
date_default_timezone_set('America/Merida');
$pdo = new conePDO();
$con = $pdo->conectar();
$con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

function main($mes, $fecha)
{
    global $con;

    try {
        $dato = $con->prepare("SELECT idCajaChica FROM cajachica WHERE idMes = :idMes ORDER BY idCajaChica");
        $dato->bindParam(':idMes', $mes);
        $dato->execute();

        if ($dato->rowCount() >= 1) {
            $respuesta = 1;
        } else {
            $dato = $con->prepare("SELECT idMes FROM cajachica WHERE idMes < :idMes ORDER BY idCajaChica");
            $dato->bindParam(':idMes', $mes);
            $dato->execute();
            if ($dato->rowCount() == 0) {
                $respuesta = 0;
            } else {
                $respuesta = 2;
            }
        }
        echo json_encode($respuesta);
    } catch (PDOException $e) {
        error_log('verificarSaldoInicialCajaChica.php - mes=' . $mes . ' database=' . ($_SESSION['database'] ?? '(sin sesion)') . ' - ' . $e->getMessage());
        echo json_encode('error');
    }
}

if (isset($_GET['mes'])) {
    $fecha = json_decode(file_get_contents('php://input'));
    main($_GET['mes'], $fecha);
} else {
    exit();
}
