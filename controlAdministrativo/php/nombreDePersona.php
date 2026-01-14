<?php
function retornarNombre($con, $tipo = 0, $id = 0)
{
    
    switch ($tipo) {
        case 1:
            $sqlNombre = "SELECT nombre as nombre FROM proveedor WHERE idProveedor = :id";
            $dato = $con->prepare($sqlNombre);
            $dato->bindParam(':id', $id);
            $dato->execute();
            $dato->bindColumn('nombre', $responseName);
            $dato->fetch(PDO::FETCH_BOUND);
            break;
        case 2:
            $responseName = "VENTA AL PÚBLICO";
            break;
        case 3:
            $sqlNombre = "SELECT nombreProveedor as nombre FROM proveedoresmantto WHERE idProveedorMantto = :id";
            $dato = $con->prepare($sqlNombre);
            $dato->bindParam(':id', $id);
            $dato->execute();
            $dato->bindColumn('nombre', $responseName);
            $dato->fetch(PDO::FETCH_BOUND);
            break;
        case 4:
            $sqlNombre = "SELECT nombre as nombre FROM personaloaxaca WHERE idPersonalOM = :id";
            $dato = $con->prepare($sqlNombre);
            $dato->bindParam(':id', $id);
            $dato->execute();
            $dato->bindColumn('nombre', $responseName);
            $dato->fetch(PDO::FETCH_BOUND);
            break;
        case 6:
            $sqlNombre = "SELECT nombre as nombre FROM clientes WHERE idCliente = :id";
            $dato = $con->prepare($sqlNombre);
            $dato->bindParam(':id', $id);
            $dato->execute();
            $dato->bindColumn('nombre', $responseName);
            $dato->fetch(PDO::FETCH_BOUND);
            break;           
        case 8:
            $sqlNombre = "SELECT nombre as nombre FROM propios WHERE idPropio = :id";
            $dato = $con->prepare($sqlNombre);
            $dato->bindParam(':id', $id);
            $dato->execute();
            $dato->bindColumn('nombre', $responseName);
            $dato->fetch(PDO::FETCH_BOUND);
            break; 
        case 9:
            $sqlNombre = "SELECT nombre as nombre FROM acreedores WHERE idAcreedor = :id";
            $dato = $con->prepare($sqlNombre);
            $dato->bindParam(':id', $id);
            $dato->execute();
            $dato->bindColumn('nombre', $responseName);
            $dato->fetch(PDO::FETCH_BOUND);
            break;
        default:
            $responseName = "";
            break;
    }

    return $responseName;
}
