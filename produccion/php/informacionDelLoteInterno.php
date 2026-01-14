<?php

include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$con = $pdo->conectar();

$idLoteInterno = $_GET['idLoteInterno'];
if(isset($_GET["organica"])){
    switch ($_GET['organica']) {
        case 0:
            $almacen = 'almacen_organico';
            $tamboreslotes = 'tamboreslotes_organico';
            $calidad = 'calidad_organico';
            break;
        case 1:
            $almacen = 'almacen_mantequilla';
            $tamboreslotes = 'tamboreslotes_mantequilla';
            $calidad = 'calidad_mantequilla';
            break;
        case 2:
            $almacen = 'almacen_altiplano';
            $tamboreslotes = 'tamboreslotes_altiplano';
            $calidad = 'calidad_altiplano';
            break;
        case 3:
            $almacen = 'almacen_altiplano';
            $tamboreslotes = 'tamboreslotes_naranjo';
            $calidad = 'calidad_naranjo';
            break;
        case 4:
            $almacen = 'almacen_aguacate';
            $tamboreslotes = 'tamboreslotes_aguacate';
            $calidad = 'calidad_aguacate';
            break;
        case 5:
            $almacen = 'almacen_mezquite';
            $tamboreslotes = 'tamboreslotes_mezquite';
            $calidad = 'calidad_mezquite';
            break;
    }
    $query = "
    SELECT idLoteInterno, fechaProceso, marcaFinalCliente, numeroDeTambores, kilosTotales,
    (SELECT SUM(al.bruto) FROM $almacen al 
    INNER JOIN $tamboreslotes tex ON tex.folioTambor = al.idAlmacen WHERE tex.idLoteInterno = '$idLoteInterno')as brutoTotal,
    (SELECT SUM(al.tara) FROM $almacen al 
    INNER JOIN $tamboreslotes tex ON tex.folioTambor = al.idAlmacen WHERE tex.idLoteInterno = '$idLoteInterno') as taraTotal,
    (SELECT SUM(al.neto) FROM $almacen al INNER JOIN $tamboreslotes tex ON tex.folioTambor = al.idAlmacen 
    WHERE tex.idLoteInterno = '$idLoteInterno') as netoTotal
    FROM $calidad WHERE idLoteInterno = :idLoteInterno
    ";
    $data = $con->prepare($query);
    $data->bindParam(':idLoteInterno', $idLoteInterno);
    $data->execute();
    
    while ($row = $data->fetch()) {
        $infoLote = new stdClass();
        $infoLote->idLoteInterno = $row["idLoteInterno"];
        $infoLote->fechaProceso = $row["fechaProceso"];
        $infoLote->marcaFinalCliente = $row["marcaFinalCliente"];
        $infoLote->numeroDeTambores = $row["numeroDeTambores"];
        $infoLote->kilosTotales = $row["kilosTotales"];
        $infoLote->brutoTotal = $row["brutoTotal"];
        $infoLote->taraTotal = $row["taraTotal"];
        $infoLote->netoTotal = $row["netoTotal"];
    
        if ($infoLote->fechaProceso == "1969-12-31") {
            $infoLote->fechaProceso = "Aún no asignada";
        } else {
            $infoLote->fechaProceso = $row["fechaProceso"];
        }
    }
    
    # JSON-encode the response
    echo $json_response = json_encode($infoLote);
}else{
    $query = "
    SELECT idLoteInterno, fechaProceso, marcaFinalCliente, numeroDeTambores, kilosTotales,
    (SELECT SUM(al.bruto) FROM almacen al INNER JOIN tamboreslotes tex ON tex.folioTambor = al.idAlmacen WHERE tex.idLoteInterno = '$idLoteInterno')as brutoTotal,
    (SELECT SUM(al.tara) FROM almacen al INNER JOIN tamboreslotes tex ON tex.folioTambor = al.idAlmacen WHERE tex.idLoteInterno = '$idLoteInterno') as taraTotal,
    (SELECT SUM(al.neto) FROM almacen al INNER JOIN tamboreslotes tex ON tex.folioTambor = al.idAlmacen WHERE tex.idLoteInterno = '$idLoteInterno') as netoTotal
    FROM calidad WHERE idLoteInterno = :idLoteInterno
    ";
    $data = $con->prepare($query);
    $data->bindParam(':idLoteInterno', $idLoteInterno);
    $data->execute();
    
    while ($row = $data->fetch()) {
        $infoLote = new stdClass();
        $infoLote->idLoteInterno = $row["idLoteInterno"];
        $infoLote->fechaProceso = $row["fechaProceso"];
        $infoLote->marcaFinalCliente = $row["marcaFinalCliente"];
        $infoLote->numeroDeTambores = $row["numeroDeTambores"];
        $infoLote->kilosTotales = $row["kilosTotales"];
        $infoLote->brutoTotal = $row["brutoTotal"];
        $infoLote->taraTotal = $row["taraTotal"];
        $infoLote->netoTotal = $row["netoTotal"];
    
        if ($infoLote->fechaProceso == "1969-12-31") {
            $infoLote->fechaProceso = "Aún no asignada";
        } else {
            $infoLote->fechaProceso = $row["fechaProceso"];
        }
    }
    
    # JSON-encode the response
    echo $json_response = json_encode($infoLote);
}
?>