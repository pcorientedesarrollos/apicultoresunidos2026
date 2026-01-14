<?php
include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$con = $pdo->conectar();
function obtenerEstructuraTabla()
{
    global $con;

    $datos = $con->prepare("SELECT * FROM divisionescertificado");
    $datos->execute();
    $resultado = array();

    // Obtener los análisis por división

    // Aquí es donde entra si se abre el reporte de concentrado
    if ($datos->rowCount() >= 1) {
        foreach ($datos->fetchAll(PDO::FETCH_ASSOC) as $division) {
            $idDivision = $division['idDivision'];

            $sqlDetalle = $con->prepare("SELECT * FROM analisiscertificado WHERE divisor = :idDivision");
            $sqlDetalle->bindParam(':idDivision', $idDivision);
            $sqlDetalle->execute();
            $division['arregloDeAnalisis'] = [];

            foreach ($sqlDetalle->fetchAll(PDO::FETCH_ASSOC) as $analisis) {
                array_push($division['arregloDeAnalisis'], $analisis);
            }
            array_push($resultado, $division);
        }
    }
    return $resultado;
}

if (!isset($_GET['function'])) {
    $resultado = obtenerEstructuraTabla();
    echo json_encode($resultado);
}
