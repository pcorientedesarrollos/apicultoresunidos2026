<?php
include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$con = $pdo->conectar();

function obtenerMes($idMes)
{
    global $con;
    $mes = $con->prepare("SELECT mes FROM meses WHERE idMes = :idMes");
    $mes->bindParam(':idMes', $idMes);
    $mes->execute();
    if ($mes == false) {
        throw new Exception($con->errorInfo());
    }

    if ($mes->rowCount() == 0) {
        throw new Exception('Error al encontrar el mes solicitado.');
    } else {
        $mes_encontrado = $mes->fetch(PDO::FETCH_ASSOC);
        return $mes_encontrado;
    }
}

try {
    if (isset($_GET['idMes']) && !isset($_GET['function']) && !isset($_GET['parametro'])) {
        $mes = obtenerMes($_GET['idMes']);
        echo json_encode(['error' => false, 'mes' => $mes]);
    }

} catch (Exception $e) {
    echo json_encode(['error' => true, 'message' => $e->getMessage()]);
}