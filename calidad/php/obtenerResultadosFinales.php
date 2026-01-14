<?php

include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$con = $pdo->conectar();

if(isset($_GET["organica"])){
    $sql = "SELECT rs.idresultadoFinal, rs.resultado, lab.resultadoFinal
    FROM laboratorio_organico lab
    INNER JOIN resultadofinal rs 
    ON rs.idresultadoFinal = lab.resultadoFinal
    INNER JOIN almacen_organico al 
    ON al.idAlmacen = lab.idAlmacen
    INNER JOIN almacenencabezado_organico ale
    ON ale.idAlmacen = al.idAlmacenEncabezado
    INNER JOIN proveedor pr 
    ON pr.idProveedor = ale.idProveedor
    GROUP BY lab.resultadoFinal";
    $datos = $con->prepare($sql);
    $datos->execute();
    
    $listaResultadosFinales = Array();
    if ($datos == false) {
        echo "Error al ingresar";
    } else {
        while ($rs = $datos->fetch()) {
            $resultados = new stdClass();
            $resultados->idresultadoFinal = $rs["idresultadoFinal"];
            $resultados->resultado = $rs["resultado"];
            $listaResultadosFinales[] = $resultados;
        }
        echo json_encode($listaResultadosFinales);
    }
}else{
    $sql = "SELECT rs.idresultadoFinal, rs.resultado, lab.resultadoFinal
    FROM laboratorio lab
    INNER JOIN resultadofinal rs 
    ON rs.idresultadoFinal = lab.resultadoFinal
    INNER JOIN almacen al 
    ON al.idAlmacen = lab.idAlmacen
    INNER JOIN almacenencabezado ale
    ON ale.idAlmacen = al.idAlmacenEncabezado
    INNER JOIN proveedor pr 
    ON pr.idProveedor = ale.idProveedor
    GROUP BY lab.resultadoFinal";
    $datos = $con->prepare($sql);
    $datos->execute();
    
    $listaResultadosFinales = Array();
    if ($datos == false) {
        echo "Error al ingresar";
    } else {
        while ($rs = $datos->fetch()) {
            $resultados = new stdClass();
            $resultados->idresultadoFinal = $rs["idresultadoFinal"];
            $resultados->resultado = $rs["resultado"];
            $listaResultadosFinales[] = $resultados;
        }
        echo json_encode($listaResultadosFinales);
    }
}
?>