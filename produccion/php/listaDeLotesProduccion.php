<?php

include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$con = $pdo->conectar();

if(isset($_GET["organica"])){
    switch ($_GET['organica']) {
        case 0:
            $calidad = 'calidad_organico';
            $reporte = 'reportesdeprocesos_organico';

            break;
        case 1:
            $calidad = 'calidad_mantequilla';
            $reporte = 'reportesdeprocesos_mantequilla';
 
            break;
        case 2:
            $calidad = 'calidad_altiplano';
            $reporte = 'reportesdeprocesos_altiplano';

            break;
        case 3:
            $calidad = 'calidad_naranjo';
            $reporte = 'reportesdeprocesos_naranjo';

            break;
        case 4:
            $calidad = 'calidad_aguacate';
            $reporte = 'reportesdeprocesos_aguacate';

            break;
        case 5:
            $calidad = 'calidad_mezquite';
            $reporte = 'reportesdeprocesos_mezquite';
            break;
    }
    $sql = "SELECT c.idLoteInterno
    FROM $calidad c
    LEFT OUTER JOIN $reporte t
    ON c.idLoteInterno = t.idLoteInterno
    WHERE t.idLoteInterno IS NULL";
    $data = $con->prepare($sql);
    $data->execute();
    
    $arrayLotes = array();
    
    while ($row = $data->fetch()) {
        $listaLotes = new stdClass();
        $listaLotes->idLoteInterno = $row["idLoteInterno"];
        $arrayLotes[] = $listaLotes;
    }
    
    echo $json_response = json_encode($arrayLotes);
}else{
    $sql = 'SELECT c.idLoteInterno
    FROM calidad c
    LEFT OUTER JOIN reportesdeprocesos t
    ON c.idLoteInterno = t.idLoteInterno
    WHERE t.idLoteInterno IS NULL';
    $data = $con->prepare($sql);
    $data->execute();
    
    $arrayLotes = array();
    
    while ($row = $data->fetch()) {
        $listaLotes = new stdClass();
        $listaLotes->idLoteInterno = $row["idLoteInterno"];
        $arrayLotes[] = $listaLotes;
    }
    
    echo $json_response = json_encode($arrayLotes);
}
?>