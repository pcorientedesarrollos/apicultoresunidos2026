<?php

include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$conexion = $pdo->conectar();

$id = $_GET["id"];
$nombreImg = "";
$sqlI = "SELECT archivoContrato FROM archivoscontratos WHERE idContrato = :id";
$dato = $conexion->prepare($sqlI);
$dato->bindParam(':id', $id);
$dato->execute();
while ($row = $dato->fetch()) {
    $nombreImg = $row["archivoContrato"];
}
//$directorio = "proveedores/";
$sql = "DELETE FROM archivoscontratos WHERE archivoscontratos.idContrato =:id";
$datos = $conexion->prepare($sql);
$datos->bindParam(":id", $id);
$datos->execute();

unlink("../" . $nombreImg);


//if ($datos == false) {
//    echo mysql_error();
//} else {
//    echo "Registro eliminado";
//}

?>