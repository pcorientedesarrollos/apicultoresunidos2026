<?php

include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$con = $pdo->conectar();

$id = $_GET["id"];

if (isset($_GET['cubeta'])) {
    $sql = "SELECT SUM(diferencia) AS totalDiferencia FROM cubetasdetalle WHERE idAlmacenEncabezado = :id";
    $datos = $con->prepare($sql);
    $datos->bindParam(':id', $id);
    $datos->execute();
    if ($datos == false) {
        echo mysql_error();
    } else {
        $almacen = new stdClass();
        while ($rs = $datos->fetch()) {
            $almacen->totalDiferencia = $rs["totalDiferencia"];
        }
        echo json_encode($almacen);
    }
} else {
     if(isset($_GET["organica"])){
        $sql = "SELECT SUM(diferencia) AS totalKgDiferencia,
        SUM(bruto) AS totalKgBruto, 
        SUM(tara) AS totalKgTara, 
        SUM(neto) AS totalKgNeto 
        FROM almacen_organico WHERE idAlmacenEncabezado = :id";
       $datos = $con->prepare($sql);
       $datos->bindParam(':id', $id);
       $datos->execute();
   
    
           $almacen = new stdClass();
           while ($rs = $datos->fetch()) {
               $almacen->totalKgDiferencia = $rs["totalKgDiferencia"];
               $almacen->totalKgBruto = $rs["totalKgBruto"];
               $almacen->totalKgTara = $rs["totalKgTara"];
               $almacen->totalKgNeto = $rs["totalKgNeto"];           
           }
           echo json_encode($almacen);   
    }else if(isset($_GET["mantequilla"])){
        $sql = "SELECT SUM(diferencia) AS totalKgDiferencia,
        SUM(bruto) AS totalKgBruto, 
        SUM(tara) AS totalKgTara, 
        SUM(neto) AS totalKgNeto 
        FROM almacen_mantequilla WHERE idAlmacenEncabezado = :id";
       $datos = $con->prepare($sql);
       $datos->bindParam(':id', $id);
       $datos->execute();
   
    
           $almacen = new stdClass();
           while ($rs = $datos->fetch()) {
               $almacen->totalKgDiferencia = $rs["totalKgDiferencia"];
               $almacen->totalKgBruto = $rs["totalKgBruto"];
               $almacen->totalKgTara = $rs["totalKgTara"];
               $almacen->totalKgNeto = $rs["totalKgNeto"];           
           }
           echo json_encode($almacen);   
    } else if(isset($_GET["altiplano"])){
        $sql = "SELECT SUM(diferencia) AS totalKgDiferencia,
        SUM(bruto) AS totalKgBruto, 
        SUM(tara) AS totalKgTara, 
        SUM(neto) AS totalKgNeto 
        FROM almacen_altiplano WHERE idAlmacenEncabezado = :id";
       $datos = $con->prepare($sql);
       $datos->bindParam(':id', $id);
       $datos->execute();
   
    
           $almacen = new stdClass();
           while ($rs = $datos->fetch()) {
               $almacen->totalKgDiferencia = $rs["totalKgDiferencia"];
               $almacen->totalKgBruto = $rs["totalKgBruto"];
               $almacen->totalKgTara = $rs["totalKgTara"];
               $almacen->totalKgNeto = $rs["totalKgNeto"];           
           }
           echo json_encode($almacen);   
    } else if(isset($_GET["naranjo"])){
        $sql = "SELECT SUM(diferencia) AS totalKgDiferencia,
        SUM(bruto) AS totalKgBruto, 
        SUM(tara) AS totalKgTara, 
        SUM(neto) AS totalKgNeto 
        FROM almacen_naranjo WHERE idAlmacenEncabezado = :id";
       $datos = $con->prepare($sql);
       $datos->bindParam(':id', $id);
       $datos->execute();
   
    
           $almacen = new stdClass();
           while ($rs = $datos->fetch()) {
               $almacen->totalKgDiferencia = $rs["totalKgDiferencia"];
               $almacen->totalKgBruto = $rs["totalKgBruto"];
               $almacen->totalKgTara = $rs["totalKgTara"];
               $almacen->totalKgNeto = $rs["totalKgNeto"];           
           }
           echo json_encode($almacen);   
    }
    else if(isset($_GET["aguacate"])){
        $sql = "SELECT SUM(diferencia) AS totalKgDiferencia,
        SUM(bruto) AS totalKgBruto, 
        SUM(tara) AS totalKgTara, 
        SUM(neto) AS totalKgNeto 
        FROM almacen_aguacate WHERE idAlmacenEncabezado = :id";
       $datos = $con->prepare($sql);
       $datos->bindParam(':id', $id);
       $datos->execute();
   
    
           $almacen = new stdClass();
           while ($rs = $datos->fetch()) {
               $almacen->totalKgDiferencia = $rs["totalKgDiferencia"];
               $almacen->totalKgBruto = $rs["totalKgBruto"];
               $almacen->totalKgTara = $rs["totalKgTara"];
               $almacen->totalKgNeto = $rs["totalKgNeto"];           
           }
           echo json_encode($almacen);   
    }
    else if(isset($_GET["mezquite"])){
        $sql = "SELECT SUM(diferencia) AS totalKgDiferencia,
        SUM(bruto) AS totalKgBruto, 
        SUM(tara) AS totalKgTara, 
        SUM(neto) AS totalKgNeto 
        FROM almacen_mezquite WHERE idAlmacenEncabezado = :id";
       $datos = $con->prepare($sql);
       $datos->bindParam(':id', $id);
       $datos->execute();
   
    
           $almacen = new stdClass();
           while ($rs = $datos->fetch()) {
               $almacen->totalKgDiferencia = $rs["totalKgDiferencia"];
               $almacen->totalKgBruto = $rs["totalKgBruto"];
               $almacen->totalKgTara = $rs["totalKgTara"];
               $almacen->totalKgNeto = $rs["totalKgNeto"];           
           }
           echo json_encode($almacen);   
    }
    
    else{
        $sql = "SELECT SUM(diferencia) AS totalKgDiferencia,
        SUM(bruto) AS totalKgBruto, 
        SUM(tara) AS totalKgTara, 
        SUM(neto) AS totalKgNeto 
        FROM almacen WHERE idAlmacenEncabezado = :id";
       $datos = $con->prepare($sql);
       $datos->bindParam(':id', $id);
       $datos->execute();
   
    
           $almacen = new stdClass();
           while ($rs = $datos->fetch()) {
               $almacen->totalKgDiferencia = $rs["totalKgDiferencia"];
               $almacen->totalKgBruto = $rs["totalKgBruto"];
               $almacen->totalKgTara = $rs["totalKgTara"];
               $almacen->totalKgNeto = $rs["totalKgNeto"];           
           }
           echo json_encode($almacen);
    }
    
}

