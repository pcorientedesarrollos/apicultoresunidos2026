<?php

include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$con = $pdo->conectar();
$con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

try {

    $miel = $_GET['miel'];

    switch ($miel) {
        case '1':
            $conformacion = 'conformacionhomogeneo_encabezado';
            break;
        case '2':
            $conformacion = 'conformacionhomogeneo_encabezado_organico';
            break;
    };

    $sql = "SELECT ce.*, a.analisis, r.resultado AS resultadoN, p.nombre
    FROM $conformacion ce
    LEFT JOIN analisisconformacion a ON a.idAnalisis = ce.tipoAnalisis
    LEFT JOIN resultadofinal r ON r.idresultadoFinal = ce.resultado
    LEFT JOIN personaloaxaca p ON p.idPersonalOM = ce.personal";

    if (isset($_GET['mes'])) {
        $mes = $_GET['mes'];
        $sql .= " WHERE SUBSTR(ce.fecha FROM 6 FOR 2) = " . $mes;
    }

    $sql .= " ORDER BY ce.fecha DESC";

    $datos = $con->prepare($sql);
    $datos->execute();
    if ($datos == FALSE) {
        throw new Exception($con->errorInfo());
    }

    $resultado = $datos->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode(['error' => false, 'message' => 'Consulta realizada', 'data' => $resultado]);
} catch (Exception $e) {
    echo json_encode(['error' => true, 'message' => $e->getMessage(), 'data' => []]);
    exit();
}
