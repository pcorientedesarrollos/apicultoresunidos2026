<?php

include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$con = $pdo->conectar();

if(isset($_GET["organica"])){
    $sql = 'SELECT ca.idLoteInterno FROM calidad_organico ca
    LEFT OUTER JOIN especificaciones_organico es 
    ON ca.idLoteInterno = es.idLoteInterno
    WHERE es.idLoteInterno IS NULL';
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
    $sql = 'SELECT ca.idLoteInterno FROM calidad ca
    LEFT OUTER JOIN especificaciones es 
    ON ca.idLoteInterno = es.idLoteInterno
    WHERE es.idLoteInterno IS NULL';
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