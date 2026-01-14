<?php
include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$con = $pdo->conectar();
// $elNombre;
// if(isset($_GET['idProveedor'])){
//         $obtener = $dbh->prepare("SELECT nombre AS nombreDe FROM proveedor WHERE idProveedor = :idProveedor");
//     $obtener->bindParam(':idProveedor', $_GET['idProveedor']);
//     $obtener->execute();
//     $obtener->bindColumn('nombreDe', $elNombre);
//     $obtener->fetch(PDO::FETCH_BOUND);  
// } elseif(isset($_GET['idProveedorMantto'])){
//    $obtener = $dbh->prepare("SELECT nombreProveedor AS nombreDe FROM proveedoresmantto WHERE idProveedorMantto = :idProveedorMantto");
//     $obtener->bindParam(':idProveedorMantto', $_GET['idProveedorMantto']);
//     $obtener->execute();
//     $obtener->bindColumn('nombreDe', $elNombre);
//     $obtener->fetch(PDO::FETCH_BOUND);
// } else{
//     $id = $_GET['idPersonalOM'];
//     $obtener = $dbh->prepare("SELECT nombre  AS nombreDe FROM personaloaxaca WHERE idPersonalOM = :id");
//     $obtener->bindParam(':id', $id);
//     $obtener->execute();
//     $obtener->bindColumn('nombreDe', $elNombre);
//     $obtener->fetch(PDO::FETCH_BOUND);
// }
// echo $elNombre;
$tipo = $_GET['tipo'];
$id = $_GET['id'];
switch ($tipo) {
    case '1':
        $sqlNombre = "SELECT nombre as nombre FROM proveedor WHERE idProveedor = :id";
        $dato = $con->prepare($sqlNombre);
        $dato->bindParam(':id', $id);
        $dato->execute();
        $dato->bindColumn('nombre', $responseName);
        $dato->fetch(PDO::FETCH_BOUND);
        break;
    case '2':
        $responseName = "VENTA AL PÚBLICO";
        break;
    case '3':
        $sqlNombre = "SELECT nombreProveedor as nombre FROM proveedoresmantto WHERE idProveedorMantto = :id";
        $dato = $con->prepare($sqlNombre);
        $dato->bindParam(':id', $id);
        $dato->execute();
        $dato->bindColumn('nombre', $responseName);
        $dato->fetch(PDO::FETCH_BOUND);
        break;
    case '4':
        $sqlNombre = "SELECT nombre as nombre FROM personaloaxaca WHERE idPersonalOM = :id";
        $dato = $con->prepare($sqlNombre);
        $dato->bindParam(':id', $id);
        $dato->execute();
        $dato->bindColumn('nombre', $responseName);
        $dato->fetch(PDO::FETCH_BOUND);
        break;
    case '6':
        $sqlNombre = "SELECT nombre as nombre FROM clientes WHERE idCliente = :id";
        $dato = $con->prepare($sqlNombre);
        $dato->bindParam(':id', $id);
        $dato->execute();
        $dato->bindColumn('nombre', $responseName);
        $dato->fetch(PDO::FETCH_BOUND);
        break;           
    case '8':
        $sqlNombre = "SELECT nombre as nombre FROM propios WHERE idPropio = :id";
        $dato = $con->prepare($sqlNombre);
        $dato->bindParam(':id', $id);
        $dato->execute();
        $dato->bindColumn('nombre', $responseName);
        $dato->fetch(PDO::FETCH_BOUND);
        break; 
    case '9':
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

echo $responseName;


?>