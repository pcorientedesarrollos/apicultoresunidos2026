<?php

include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$con = $pdo->conectar();

$id = $_GET['id'];

if(isset($_GET['organica'])){
    $sql = "SELECT * FROM cubetasencabezado_organico al 
    LEFT JOIN proveedor pr 
    ON pr.idProveedor = al.idProveedor 
    LEFT JOIN direccion dr 
    ON dr.idDireccion = pr.idDireccion
    LEFT JOIN localidades lo ON lo.idlocalidad = dr.idlocalidad
    WHERE idAlmacen = :id";
    $datos = $con->prepare($sql);
    $datos->bindParam(':id', $id);
    $datos->execute();
    if ($datos == false) {
        echo mysql_error();
    } else {
        $precioCub = new stdClass();
        while ($rs = $datos->fetch()) {
            $precioCub->idAlmacen = $rs["idAlmacen"];
            $precioCub->idProveedor = $rs["idProveedor"];
            $precioCub->proveedor = $rs["nombre"];
            $precioCub->idlocalidad = $rs["idlocalidad"];
            $precioCub->localidad = $rs["localidad"];
            $precioCub->idSagarpa = $rs["idSagarpa"];
            $precioCub->fecha = $rs["fecha"];
            $precioCub->folioEntradaTambor = $rs["folioEntradaTambor"];
            $precioCub->totalCompra = $rs["totalCompra"];
    
    
            $sqlTotales = "SELECT SUM(costoTotal) as costosTotales
                  FROM cubetasdetalle WHERE idAlmacenEncabezado = :id";
            $datosTotales = $con->prepare($sqlTotales);
            $datosTotales->bindParam(':id', $id);
            $datosTotales->execute();
            if ($datosTotales == false) {
                echo mysql_error();
            } else {
                while ($resTotal = $datosTotales->fetch()) {
                    $precioCub->costosTotales = $resTotal["costosTotales"];
                }
            }
        }
        echo json_encode($precioCub);
    }
}else if(isset($_GET['organicam'])){

    $sql = "SELECT * FROM cubetasencabezado_mantequilla al 
    LEFT JOIN proveedor pr 
    ON pr.idProveedor = al.idProveedor 
    LEFT JOIN direccion dr 
    ON dr.idDireccion = pr.idDireccion
    LEFT JOIN localidades lo ON lo.idlocalidad = dr.idlocalidad
    WHERE idAlmacen = :id";
    $datos = $con->prepare($sql);
    $datos->bindParam(':id', $id);
    $datos->execute();
    if ($datos == false) {
        echo mysql_error();
    } else {
        $precioCub = new stdClass();
        while ($rs = $datos->fetch()) {
            $precioCub->idAlmacen = $rs["idAlmacen"];
            $precioCub->idProveedor = $rs["idProveedor"];
            $precioCub->proveedor = $rs["nombre"];
            $precioCub->idlocalidad = $rs["idlocalidad"];
            $precioCub->localidad = $rs["localidad"];
            $precioCub->idSagarpa = $rs["idSagarpa"];
            $precioCub->fecha = $rs["fecha"];
            $precioCub->folioEntradaTambor = $rs["folioEntradaTambor"];
            $precioCub->totalCompra = $rs["totalCompra"];
    
    
            $sqlTotales = "SELECT SUM(costoTotal) as costosTotales
                  FROM cubetasdetalle WHERE idAlmacenEncabezado = :id";
            $datosTotales = $con->prepare($sqlTotales);
            $datosTotales->bindParam(':id', $id);
            $datosTotales->execute();
            if ($datosTotales == false) {
                echo mysql_error();
            } else {
                while ($resTotal = $datosTotales->fetch()) {
                    $precioCub->costosTotales = $resTotal["costosTotales"];
                }
            }
        }
        echo json_encode($precioCub);
    }

} else if(isset($_GET['organicaa'])){

    $sql = "SELECT * FROM cubetasencabezado_altiplano al 
    LEFT JOIN proveedor pr 
    ON pr.idProveedor = al.idProveedor 
    LEFT JOIN direccion dr 
    ON dr.idDireccion = pr.idDireccion
    LEFT JOIN localidades lo ON lo.idlocalidad = dr.idlocalidad
    WHERE idAlmacen = :id";
    $datos = $con->prepare($sql);
    $datos->bindParam(':id', $id);
    $datos->execute();
    if ($datos == false) {
        echo mysql_error();
    } else {
        $precioCub = new stdClass();
        while ($rs = $datos->fetch()) {
            $precioCub->idAlmacen = $rs["idAlmacen"];
            $precioCub->idProveedor = $rs["idProveedor"];
            $precioCub->proveedor = $rs["nombre"];
            $precioCub->idlocalidad = $rs["idlocalidad"];
            $precioCub->localidad = $rs["localidad"];
            $precioCub->idSagarpa = $rs["idSagarpa"];
            $precioCub->fecha = $rs["fecha"];
            $precioCub->folioEntradaTambor = $rs["folioEntradaTambor"];
            $precioCub->totalCompra = $rs["totalCompra"];
    
    
            $sqlTotales = "SELECT SUM(costoTotal) as costosTotales
                  FROM cubetasdetalle WHERE idAlmacenEncabezado = :id";
            $datosTotales = $con->prepare($sqlTotales);
            $datosTotales->bindParam(':id', $id);
            $datosTotales->execute();
            if ($datosTotales == false) {
                echo mysql_error();
            } else {
                while ($resTotal = $datosTotales->fetch()) {
                    $precioCub->costosTotales = $resTotal["costosTotales"];
                }
            }
        }
        echo json_encode($precioCub);
    }

}
else if(isset($_GET['organican'])){

    $sql = "SELECT * FROM cubetasencabezado_naranjo al 
    LEFT JOIN proveedor pr 
    ON pr.idProveedor = al.idProveedor 
    LEFT JOIN direccion dr 
    ON dr.idDireccion = pr.idDireccion
    LEFT JOIN localidades lo ON lo.idlocalidad = dr.idlocalidad
    WHERE idAlmacen = :id";
    $datos = $con->prepare($sql);
    $datos->bindParam(':id', $id);
    $datos->execute();
    if ($datos == false) {
        echo mysql_error();
    } else {
        $precioCub = new stdClass();
        while ($rs = $datos->fetch()) {
            $precioCub->idAlmacen = $rs["idAlmacen"];
            $precioCub->idProveedor = $rs["idProveedor"];
            $precioCub->proveedor = $rs["nombre"];
            $precioCub->idlocalidad = $rs["idlocalidad"];
            $precioCub->localidad = $rs["localidad"];
            $precioCub->idSagarpa = $rs["idSagarpa"];
            $precioCub->fecha = $rs["fecha"];
            $precioCub->folioEntradaTambor = $rs["folioEntradaTambor"];
            $precioCub->totalCompra = $rs["totalCompra"];
    
    
            $sqlTotales = "SELECT SUM(costoTotal) as costosTotales
                  FROM cubetasdetalle WHERE idAlmacenEncabezado = :id";
            $datosTotales = $con->prepare($sqlTotales);
            $datosTotales->bindParam(':id', $id);
            $datosTotales->execute();
            if ($datosTotales == false) {
                echo mysql_error();
            } else {
                while ($resTotal = $datosTotales->fetch()) {
                    $precioCub->costosTotales = $resTotal["costosTotales"];
                }
            }
        }
        echo json_encode($precioCub);
    }

}
else if(isset($_GET['organicame'])){

    $sql = "SELECT * FROM cubetasencabezado_mezquite al 
    LEFT JOIN proveedor pr 
    ON pr.idProveedor = al.idProveedor 
    LEFT JOIN direccion dr 
    ON dr.idDireccion = pr.idDireccion
    LEFT JOIN localidades lo ON lo.idlocalidad = dr.idlocalidad
    WHERE idAlmacen = :id";
    $datos = $con->prepare($sql);
    $datos->bindParam(':id', $id);
    $datos->execute();
    if ($datos == false) {
        echo mysql_error();
    } else {
        $precioCub = new stdClass();
        while ($rs = $datos->fetch()) {
            $precioCub->idAlmacen = $rs["idAlmacen"];
            $precioCub->idProveedor = $rs["idProveedor"];
            $precioCub->proveedor = $rs["nombre"];
            $precioCub->idlocalidad = $rs["idlocalidad"];
            $precioCub->localidad = $rs["localidad"];
            $precioCub->idSagarpa = $rs["idSagarpa"];
            $precioCub->fecha = $rs["fecha"];
            $precioCub->folioEntradaTambor = $rs["folioEntradaTambor"];
            $precioCub->totalCompra = $rs["totalCompra"];
    
    
            $sqlTotales = "SELECT SUM(costoTotal) as costosTotales
                  FROM cubetasdetalle WHERE idAlmacenEncabezado = :id";
            $datosTotales = $con->prepare($sqlTotales);
            $datosTotales->bindParam(':id', $id);
            $datosTotales->execute();
            if ($datosTotales == false) {
                echo mysql_error();
            } else {
                while ($resTotal = $datosTotales->fetch()) {
                    $precioCub->costosTotales = $resTotal["costosTotales"];
                }
            }
        }
        echo json_encode($precioCub);
    }

}
else if(isset($_GET['organicaagua'])){

    $sql = "SELECT * FROM cubetasencabezado_aguacate al 
    LEFT JOIN proveedor pr 
    ON pr.idProveedor = al.idProveedor 
    LEFT JOIN direccion dr 
    ON dr.idDireccion = pr.idDireccion
    LEFT JOIN localidades lo ON lo.idlocalidad = dr.idlocalidad
    WHERE idAlmacen = :id";
    $datos = $con->prepare($sql);
    $datos->bindParam(':id', $id);
    $datos->execute();
    if ($datos == false) {
        echo mysql_error();
    } else {
        $precioCub = new stdClass();
        while ($rs = $datos->fetch()) {
            $precioCub->idAlmacen = $rs["idAlmacen"];
            $precioCub->idProveedor = $rs["idProveedor"];
            $precioCub->proveedor = $rs["nombre"];
            $precioCub->idlocalidad = $rs["idlocalidad"];
            $precioCub->localidad = $rs["localidad"];
            $precioCub->idSagarpa = $rs["idSagarpa"];
            $precioCub->fecha = $rs["fecha"];
            $precioCub->folioEntradaTambor = $rs["folioEntradaTambor"];
            $precioCub->totalCompra = $rs["totalCompra"];
    
    
            $sqlTotales = "SELECT SUM(costoTotal) as costosTotales
                  FROM cubetasdetalle WHERE idAlmacenEncabezado = :id";
            $datosTotales = $con->prepare($sqlTotales);
            $datosTotales->bindParam(':id', $id);
            $datosTotales->execute();
            if ($datosTotales == false) {
                echo mysql_error();
            } else {
                while ($resTotal = $datosTotales->fetch()) {
                    $precioCub->costosTotales = $resTotal["costosTotales"];
                }
            }
        }
        echo json_encode($precioCub);
    }

}
else{
    $sql = "SELECT * FROM cubetasencabezado al 
    LEFT JOIN proveedor pr 
    ON pr.idProveedor = al.idProveedor 
    LEFT JOIN direccion dr 
    ON dr.idDireccion = pr.idDireccion
    LEFT JOIN localidades lo ON lo.idlocalidad = dr.idlocalidad
    WHERE idAlmacen = :id";
    $datos = $con->prepare($sql);
    $datos->bindParam(':id', $id);
    $datos->execute();
    if ($datos == false) {
        echo mysql_error();
    } else {
        $precioCub = new stdClass();
        while ($rs = $datos->fetch()) {
            $precioCub->idAlmacen = $rs["idAlmacen"];
            $precioCub->idProveedor = $rs["idProveedor"];
            $precioCub->proveedor = $rs["nombre"];
            $precioCub->idlocalidad = $rs["idlocalidad"];
            $precioCub->localidad = $rs["localidad"];
            $precioCub->idSagarpa = $rs["idSagarpa"];
            $precioCub->fecha = $rs["fecha"];
            $precioCub->folioEntradaTambor = $rs["folioEntradaTambor"];
            $precioCub->totalCompra = $rs["totalCompra"];
    
    
            $sqlTotales = "SELECT SUM(costoTotal) as costosTotales
                  FROM cubetasdetalle WHERE idAlmacenEncabezado = :id";
            $datosTotales = $con->prepare($sqlTotales);
            $datosTotales->bindParam(':id', $id);
            $datosTotales->execute();
            if ($datosTotales == false) {
                echo mysql_error();
            } else {
                while ($resTotal = $datosTotales->fetch()) {
                    $precioCub->costosTotales = $resTotal["costosTotales"];
                }
            }
        }
        echo json_encode($precioCub);
    }
}