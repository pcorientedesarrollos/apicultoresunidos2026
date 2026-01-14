<?php

include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$conexion = $pdo->conectar();


$id = $_GET["id"];
$tipo = $_GET["tipo"];
$file = $_FILES["file"]["name"];

if (!is_dir("../archivos/"))
    mkdir("../archivos/", 0777);

if ($file && move_uploaded_file($_FILES["file"]["tmp_name"], "../archivos/" . $file)) {
    echo $file;
}


$archivoR = "archivos/" . $file;

$sql = "INSERT INTO archivosinstalaciones (idProveedor, archivoInstalacion, tipo) VALUES (:id, :archivosC, :tipo)";
$inf = $conexion->prepare($sql);
$inf->bindParam(':id', $id);
$inf->bindParam(':archivosC', $archivoR);
$inf->bindParam(':tipo', $tipo);
$inf->execute();


if ($inf === TRUE) {
    echo "New record created successfully";
} else {
    echo "Error: ";
}
