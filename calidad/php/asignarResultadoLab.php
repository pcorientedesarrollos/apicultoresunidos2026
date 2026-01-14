<?php

include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$con = $pdo->conectar();

$valor = $_GET["valor"];
$idLoteExperimental = $_GET["idLoteExperimental"];

$miInfo = file_get_contents("php://input");
$info = json_decode($miInfo);

if(isset($_GET["organica"])){
    $sql = "UPDATE experimental_organico SET resultadoLaboratorio = :valor WHERE idLoteExperimental = :idLoteExperimental";
    $dats = $con->prepare($sql);
    $dats->bindParam(':valor', $valor);
    $dats->bindParam(':idLoteExperimental', $idLoteExperimental);
    $dats->execute();
    if ($dats == false) {
        echo "Error al ingresar";
    } else {
        echo "Estado cambiado satisfactoriamente";
    }
    
    if ($valor == 4) {
        foreach ($info as $i) {
            $sqlUpA = "UPDATE almacen_organico set estado = '0' WHERE almacen_organico.idAlmacen = :idAlmacen";
            $dats = $con->prepare($sqlUpA);
            $dats->bindParam(':idAlmacen', $i->folioTambor);
            $dats->execute();
        }
    } else {
        foreach ($info as $i) {
            $sqlUp = "UPDATE almacen_organico set estado = '1' WHERE almacen_organico.idAlmacen = :idAlmacen";
            $dats = $con->prepare($sqlUp);
            $dats->bindParam(':idAlmacen', $i->folioTambor);
            $dats->execute();
        }
    }
} else if(isset($_GET["mantequilla"])){

    $sql = "UPDATE experimental_mantequilla SET resultadoLaboratorio = :valor WHERE idLoteExperimental = :idLoteExperimental";
    $dats = $con->prepare($sql);
    $dats->bindParam(':valor', $valor);
    $dats->bindParam(':idLoteExperimental', $idLoteExperimental);
    $dats->execute();
    if ($dats == false) {
        echo "Error al ingresar";
    } else {
        echo "Estado cambiado satisfactoriamente";
    }
    
    if ($valor == 4) {
        foreach ($info as $i) {
            $sqlUpA = "UPDATE almacen_mantequilla set estado = '0' WHERE almacen_mantequilla.idAlmacen = :idAlmacen";
            $dats = $con->prepare($sqlUpA);
            $dats->bindParam(':idAlmacen', $i->folioTambor);
            $dats->execute();
        }
    } else {
        foreach ($info as $i) {
            $sqlUp = "UPDATE almacen_mantequilla set estado = '1' WHERE almacen_mantequilla.idAlmacen = :idAlmacen";
            $dats = $con->prepare($sqlUp);
            $dats->bindParam(':idAlmacen', $i->folioTambor);
            $dats->execute();
        }
    }

}  else if(isset($_GET["altiplano"])){

    $sql = "UPDATE experimental_altiplano SET resultadoLaboratorio = :valor WHERE idLoteExperimental = :idLoteExperimental";
    $dats = $con->prepare($sql);
    $dats->bindParam(':valor', $valor);
    $dats->bindParam(':idLoteExperimental', $idLoteExperimental);
    $dats->execute();
    if ($dats == false) {
        echo "Error al ingresar";
    } else {
        echo "Estado cambiado satisfactoriamente";
    }
    
    if ($valor == 4) {
        foreach ($info as $i) {
            $sqlUpA = "UPDATE almacen_altiplano set estado = '0' WHERE almacen_altiplano.idAlmacen = :idAlmacen";
            $dats = $con->prepare($sqlUpA);
            $dats->bindParam(':idAlmacen', $i->folioTambor);
            $dats->execute();
        }
    } else {
        foreach ($info as $i) {
            $sqlUp = "UPDATE almacen_altiplano set estado = '1' WHERE almacen_altiplano.idAlmacen = :idAlmacen";
            $dats = $con->prepare($sqlUp);
            $dats->bindParam(':idAlmacen', $i->folioTambor);
            $dats->execute();
        }
    }

} else if(isset($_GET["naranjo"])){

    $sql = "UPDATE experimental_naranjo SET resultadoLaboratorio = :valor WHERE idLoteExperimental = :idLoteExperimental";
    $dats = $con->prepare($sql);
    $dats->bindParam(':valor', $valor);
    $dats->bindParam(':idLoteExperimental', $idLoteExperimental);
    $dats->execute();
    if ($dats == false) {
        echo "Error al ingresar";
    } else {
        echo "Estado cambiado satisfactoriamente";
    }
    
    if ($valor == 4) {
        foreach ($info as $i) {
            $sqlUpA = "UPDATE almacen_naranjo set estado = '0' WHERE almacen_naranjo.idAlmacen = :idAlmacen";
            $dats = $con->prepare($sqlUpA);
            $dats->bindParam(':idAlmacen', $i->folioTambor);
            $dats->execute();
        }
    } else {
        foreach ($info as $i) {
            $sqlUp = "UPDATE almacen_naranjo set estado = '1' WHERE almacen_naranjo.idAlmacen = :idAlmacen";
            $dats = $con->prepare($sqlUp);
            $dats->bindParam(':idAlmacen', $i->folioTambor);
            $dats->execute();
        }
    }

} 
else if(isset($_GET["aguacate"])){

    $sql = "UPDATE experimental_aguacate SET resultadoLaboratorio = :valor WHERE idLoteExperimental = :idLoteExperimental";
    $dats = $con->prepare($sql);
    $dats->bindParam(':valor', $valor);
    $dats->bindParam(':idLoteExperimental', $idLoteExperimental);
    $dats->execute();
    if ($dats == false) {
        echo "Error al ingresar";
    } else {
        echo "Estado cambiado satisfactoriamente";
    }
    
    if ($valor == 4) {
        foreach ($info as $i) {
            $sqlUpA = "UPDATE almacen_aguacate set estado = '0' WHERE almacen_aguacate.idAlmacen = :idAlmacen";
            $dats = $con->prepare($sqlUpA);
            $dats->bindParam(':idAlmacen', $i->folioTambor);
            $dats->execute();
        }
    } else {
        foreach ($info as $i) {
            $sqlUp = "UPDATE almacen_aguacate set estado = '1' WHERE almacen_aguacate.idAlmacen = :idAlmacen";
            $dats = $con->prepare($sqlUp);
            $dats->bindParam(':idAlmacen', $i->folioTambor);
            $dats->execute();
        }
    }

} 
else if(isset($_GET["mezquite"])){

    $sql = "UPDATE experimental_mezquite SET resultadoLaboratorio = :valor WHERE idLoteExperimental = :idLoteExperimental";
    $dats = $con->prepare($sql);
    $dats->bindParam(':valor', $valor);
    $dats->bindParam(':idLoteExperimental', $idLoteExperimental);
    $dats->execute();
    if ($dats == false) {
        echo "Error al ingresar";
    } else {
        echo "Estado cambiado satisfactoriamente";
    }
    
    if ($valor == 4) {
        foreach ($info as $i) {
            $sqlUpA = "UPDATE almacen_mezquite set estado = '0' WHERE almacen_mezquite.idAlmacen = :idAlmacen";
            $dats = $con->prepare($sqlUpA);
            $dats->bindParam(':idAlmacen', $i->folioTambor);
            $dats->execute();
        }
    } else {
        foreach ($info as $i) {
            $sqlUp = "UPDATE almacen_mezquite set estado = '1' WHERE almacen_mezquite.idAlmacen = :idAlmacen";
            $dats = $con->prepare($sqlUp);
            $dats->bindParam(':idAlmacen', $i->folioTambor);
            $dats->execute();
        }
    }

} 
else{
    $sql = "UPDATE experimental SET resultadoLaboratorio = :valor WHERE idLoteExperimental = :idLoteExperimental";
    $dats = $con->prepare($sql);
    $dats->bindParam(':valor', $valor);
    $dats->bindParam(':idLoteExperimental', $idLoteExperimental);
    $dats->execute();
    if ($dats == false) {
        echo "Error al ingresar";
    } else {
        echo "Estado cambiado satisfactoriamente";
    }
    
    if ($valor == 4) {
        foreach ($info as $i) {
            $sqlUpA = "UPDATE almacen set estado = '0' WHERE almacen.idAlmacen = :idAlmacen";
            $dats = $con->prepare($sqlUpA);
            $dats->bindParam(':idAlmacen', $i->folioTambor);
            $dats->execute();
        }
    } else {
        foreach ($info as $i) {
            $sqlUp = "UPDATE almacen set estado = '1' WHERE almacen.idAlmacen = :idAlmacen";
            $dats = $con->prepare($sqlUp);
            $dats->bindParam(':idAlmacen', $i->folioTambor);
            $dats->execute();
        }
    }
}




