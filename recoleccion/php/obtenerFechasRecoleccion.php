<?php
include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$con = $pdo->conectar();

$sql = "SELECT re.idRecoleccion, re.fecha, re.totalTambores, re.totalImporteCompra
FROM recoleccionencabezado re
ORDER BY re.fecha DESC";

try {

    $query = $con->prepare($sql);
    $query->execute();
    if (!$query) {
        throw new Exception($con->errorInfo());
    }

    $resultadoFechasRecoleccion = $query->fetchAll(PDO::FETCH_ASSOC);
    $resultado = array();

    // Obtener por recoleccion el nombre de las localidades y el nombre de los compradores
    $sqlLocalidadesRecoleccion = "SELECT l.localidad FROM recoleccion r
    LEFT JOIN localidades l ON r.idLocalidad = l.idlocalidad
    WHERE idRecoleccion = :idRecoleccion
    GROUP BY l.idlocalidad";

    $sqlCompradoresRecoleccion = "SELECT c.nombre FROM recoleccion r
    LEFT JOIN localidades l ON r.idLocalidad = l.idlocalidad
    LEFT JOIN zonas z ON l.idzona = z.idzona
    LEFT JOIN compradores c ON z.idcomprador = c.idcomprador
    WHERE idRecoleccion = :idRecoleccion;
    GROUP BY c.idcomprador";

    $totalTambores = 0;
    foreach ($resultadoFechasRecoleccion as $recoleccion) {
        $totalTambores += $recoleccion['totalTambores'];

        // Localidades:
        $querySeleccionaLocalidades = $con->prepare($sqlLocalidadesRecoleccion);
        $querySeleccionaLocalidades->bindParam(':idRecoleccion', $recoleccion['idRecoleccion']);
        $querySeleccionaLocalidades->execute();
        if (!$querySeleccionaLocalidades) {
            throw new Exception($con->errorInfo());
        }
        $localidadesRecoleccion = $querySeleccionaLocalidades->fetchAll(PDO::FETCH_ASSOC);

        // Compradores:
        $queryCompradoresRecoleccion = $con->prepare($sqlCompradoresRecoleccion);
        $queryCompradoresRecoleccion->bindParam(':idRecoleccion', $recoleccion['idRecoleccion']);
        $queryCompradoresRecoleccion->execute();
        if (!$queryCompradoresRecoleccion) {
            throw new Exception($con->errorInfo());
        }
        $compradoresRecoleccion = $queryCompradoresRecoleccion->fetchAll(PDO::FETCH_ASSOC);

        // Crear dos propiedades más, localidades y una para los compradores
        // un string la lista. Esto es para presentarlo en la tabla 

        // ** Join acepta un arreglo de valores, tenemos que obtener los valores de los nombres
        $solo_nombres_localidades = array_map(function ($personalObj) {
            return $personalObj['localidad'];
        }, $localidadesRecoleccion);
        $recoleccion['localidades'] = join(', ', $solo_nombres_localidades);
        $solo_nombres_compradores = array_map(function ($transporteObj) {
            return $transporteObj['nombre'];
        }, $compradoresRecoleccion);
        $recoleccion['compradores'] = join(', ', $solo_nombres_compradores);

        // Lo enviamos al arreglo de resultado
        array_push($resultado, $recoleccion);
    }

    echo json_encode(['error' => false, 'resultado' => $resultado, 'totalTambores' => $totalTambores]);
} catch (Exception $e) {
    echo json_encode(['error' => true, 'message' => $e->getMessage()]);
}
