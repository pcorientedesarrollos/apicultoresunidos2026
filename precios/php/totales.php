<?php

include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$con = $pdo->conectar();

$tmp = $_GET["tmp"];

if($tmp == 1){
    $sql = "Select COUNT(idAlmacen) as tamboresTotal, SUM(pesolista) as pesoListaTotal, sum(bruto) as pesoBrutoTotal, sum(tara) as pesoTaraTotal, sum(neto) as pesoNetoTotal, sum(diferencia) as diferenciaTotal, sum(costoTotal)as compraTotal from almacen";
    $datos = $con->prepare($sql);
    $datos->execute();
    
    if ($datos == false) {
        throw new Exception('No se recibieron parámetros');
    } else {
        while ($rs = $datos->fetch()) {
            $totales = new stdClass();
            $totales->tamboresTotal = $rs["tamboresTotal"];
            $totales->pesoListaTotal = $rs["pesoListaTotal"];
            $totales->pesoBrutoTotal = $rs["pesoBrutoTotal"];
            $totales->pesoTaraTotal = $rs["pesoTaraTotal"];
            $totales->pesoNetoTotal = $rs["pesoNetoTotal"];
            $totales->diferenciaTotal = $rs["diferenciaTotal"];
            $totales->compraTotal = $rs["compraTotal"];
        }
        echo json_encode($totales);
    }
} else if($tmp == 5){
    $sql = "Select COUNT(idAlmacen) as tamboresTotal, SUM(pesolista) as pesoListaTotal, sum(bruto) as pesoBrutoTotal, sum(tara) as pesoTaraTotal, sum(neto) as pesoNetoTotal, sum(diferencia) as diferenciaTotal, sum(costoTotal)as compraTotal from almacen_mantequilla";
    $datos = $con->prepare($sql);
    $datos->execute();
    
    if ($datos == false) {
        echo mysql_error();
    } else {
        while ($rs = $datos->fetch()) {
            $totales = new stdClass();
            $totales->tamboresTotal = $rs["tamboresTotal"];
            $totales->pesoListaTotal = $rs["pesoListaTotal"];
            $totales->pesoBrutoTotal = $rs["pesoBrutoTotal"];
            $totales->pesoTaraTotal = $rs["pesoTaraTotal"];
            $totales->pesoNetoTotal = $rs["pesoNetoTotal"];
            $totales->diferenciaTotal = $rs["diferenciaTotal"];
            $totales->compraTotal = $rs["compraTotal"];
        }
        echo json_encode($totales);
    }
} else if($tmp == 6){
    $sql = "Select COUNT(idAlmacen) as tamboresTotal, SUM(pesolista) as pesoListaTotal, sum(bruto) as pesoBrutoTotal, sum(tara) as pesoTaraTotal, sum(neto) as pesoNetoTotal, sum(diferencia) as diferenciaTotal, sum(costoTotal)as compraTotal from almacen_altiplano";
    $datos = $con->prepare($sql);
    $datos->execute();
    
    if ($datos == false) {
        echo mysql_error();
    } else {
        while ($rs = $datos->fetch()) {
            $totales = new stdClass();
            $totales->tamboresTotal = $rs["tamboresTotal"];
            $totales->pesoListaTotal = $rs["pesoListaTotal"];
            $totales->pesoBrutoTotal = $rs["pesoBrutoTotal"];
            $totales->pesoTaraTotal = $rs["pesoTaraTotal"];
            $totales->pesoNetoTotal = $rs["pesoNetoTotal"];
            $totales->diferenciaTotal = $rs["diferenciaTotal"];
            $totales->compraTotal = $rs["compraTotal"];
        }
        echo json_encode($totales);
    }
} else if($tmp == 7){
    $sql = "Select COUNT(idAlmacen) as tamboresTotal, SUM(pesolista) as pesoListaTotal, sum(bruto) as pesoBrutoTotal, sum(tara) as pesoTaraTotal, sum(neto) as pesoNetoTotal, sum(diferencia) as diferenciaTotal, sum(costoTotal)as compraTotal from almacen_naranjo";
    $datos = $con->prepare($sql);
    $datos->execute();
    
    if ($datos == false) {
        echo mysql_error();
    } else {
        while ($rs = $datos->fetch()) {
            $totales = new stdClass();
            $totales->tamboresTotal = $rs["tamboresTotal"];
            $totales->pesoListaTotal = $rs["pesoListaTotal"];
            $totales->pesoBrutoTotal = $rs["pesoBrutoTotal"];
            $totales->pesoTaraTotal = $rs["pesoTaraTotal"];
            $totales->pesoNetoTotal = $rs["pesoNetoTotal"];
            $totales->diferenciaTotal = $rs["diferenciaTotal"];
            $totales->compraTotal = $rs["compraTotal"];
        }
        echo json_encode($totales);
    }
}
else if($tmp == 8){
    $sql = "Select COUNT(idAlmacen) as tamboresTotal, SUM(pesolista) as pesoListaTotal, sum(bruto) as pesoBrutoTotal, sum(tara) as pesoTaraTotal, sum(neto) as pesoNetoTotal, sum(diferencia) as diferenciaTotal, sum(costoTotal)as compraTotal from almacen_aguacate";
    $datos = $con->prepare($sql);
    $datos->execute();
    
    if ($datos == false) {
        echo mysql_error();
    } else {
        while ($rs = $datos->fetch()) {
            $totales = new stdClass();
            $totales->tamboresTotal = $rs["tamboresTotal"];
            $totales->pesoListaTotal = $rs["pesoListaTotal"];
            $totales->pesoBrutoTotal = $rs["pesoBrutoTotal"];
            $totales->pesoTaraTotal = $rs["pesoTaraTotal"];
            $totales->pesoNetoTotal = $rs["pesoNetoTotal"];
            $totales->diferenciaTotal = $rs["diferenciaTotal"];
            $totales->compraTotal = $rs["compraTotal"];
        }
        echo json_encode($totales);
    }
} else if($tmp == 9){
    $sql = "Select COUNT(idAlmacen) as tamboresTotal, SUM(pesolista) as pesoListaTotal, sum(bruto) as pesoBrutoTotal, sum(tara) as pesoTaraTotal, sum(neto) as pesoNetoTotal, sum(diferencia) as diferenciaTotal, sum(costoTotal)as compraTotal from almacen_mezquite";
    $datos = $con->prepare($sql);
    $datos->execute();
    
    if ($datos == false) {
        echo mysql_error();
    } else {
        while ($rs = $datos->fetch()) {
            $totales = new stdClass();
            $totales->tamboresTotal = $rs["tamboresTotal"];
            $totales->pesoListaTotal = $rs["pesoListaTotal"];
            $totales->pesoBrutoTotal = $rs["pesoBrutoTotal"];
            $totales->pesoTaraTotal = $rs["pesoTaraTotal"];
            $totales->pesoNetoTotal = $rs["pesoNetoTotal"];
            $totales->diferenciaTotal = $rs["diferenciaTotal"];
            $totales->compraTotal = $rs["compraTotal"];
        }
        echo json_encode($totales);
    }
}

else{
    $sql = "Select COUNT(idAlmacen) as tamboresTotal, SUM(pesolista) as pesoListaTotal, sum(bruto) as pesoBrutoTotal, sum(tara) as pesoTaraTotal, sum(neto) as pesoNetoTotal, sum(diferencia) as diferenciaTotal, sum(costoTotal)as compraTotal from almacen_organico";
    $datos = $con->prepare($sql);
    $datos->execute();
    
    if ($datos == false) {
        echo mysql_error();
    } else {
        while ($rs = $datos->fetch()) {
            $totales = new stdClass();
            $totales->tamboresTotal = $rs["tamboresTotal"];
            $totales->pesoListaTotal = $rs["pesoListaTotal"];
            $totales->pesoBrutoTotal = $rs["pesoBrutoTotal"];
            $totales->pesoTaraTotal = $rs["pesoTaraTotal"];
            $totales->pesoNetoTotal = $rs["pesoNetoTotal"];
            $totales->diferenciaTotal = $rs["diferenciaTotal"];
            $totales->compraTotal = $rs["compraTotal"];
        }
        echo json_encode($totales);
    }
}
?>