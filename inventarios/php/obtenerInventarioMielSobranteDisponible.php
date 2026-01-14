<?php

include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO(); $con = $pdo->conectar();
$con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);


function obtenerInventarioMielSobranteDisponible($idTipoDeMiel = FALSE, $idSobrante = FALSE){
    global $con;
    $resultado = array(
        'encabezado' => array(
            'bruto' => 0,
            'tara' => 0,
            'neto' => 0
        ),
        'registros' => array(),
    );
    if(!$idTipoDeMiel) {
        throw new Exception('No se ha recibido el tipo de miel');
    }

    $sqlInventario = "SELECT alms.consecutivo, alms.consecutivoEntrada, alms.fecha, alms.bruto, alms.tara, alms.neto, alms.consecutivo, alms.sobrante AS idSobrante,
    s.nombre AS sobrante, z.nombre AS zona, s.codigo 
    FROM almacensobrantes alms 
    LEFT JOIN sobrantes s ON s.idSobrante = alms.sobrante
    LEFT JOIN zonastambores z ON z.idZonaTambor = alms.zona
    WHERE alms.tipoDeMiel = $idTipoDeMiel AND estado = '0'";
    if($idSobrante) {
        $sqlInventario .= " AND alms.sobrante = " . $idSobrante;
    }
    $sqlInventario .= " ORDER BY alms.consecutivo DESC";
    $query = $con->prepare($sqlInventario);
    $query->execute();

    if($query == FALSE) {
        throw new Exception($con->errorInfo());
    }
    $registros = $query->fetchAll(PDO::FETCH_ASSOC);

    foreach($registros as $tambor) {
        $resultado['encabezado']['bruto'] += floatval($tambor['bruto']);
        $resultado['encabezado']['tara'] += floatval($tambor['tara']);
        $resultado['encabezado']['neto'] += floatval($tambor['neto']);
        array_push($resultado['registros'], $tambor);
    }

    return $resultado;
}