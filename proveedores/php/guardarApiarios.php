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


$archivoC = "archivos/" . $file;

$sql = "INSERT INTO archivosapiarios (idProveedor, archivoApiario, tipo) VALUES (:id, :archivos, :tipo)";
$datos = $conexion->prepare($sql);
$datos->bindParam(':id', $id);
$datos->bindParam(':archivos', $archivoC);
$datos->bindParam(':tipo', $tipo);
$datos->execute();

if ($datos === TRUE) {
    echo "New record created successfully";
} else {
    echo "Error: ";
}
