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

$sql = "INSERT INTO archivoscontratos (idProveedor, archivoContrato, tipo) VALUES (:id, :archvos, :tipo)";
$info = $conexion->prepare($sql);
$info->bindParam(':id', $id);
$info->bindParam(':archvos', $archivoC);
$info->bindParam(':tipo', $tipo);
$info->execute();

if ($info === TRUE) {
    echo "New record created successfully";
} else {
    echo "Error: ";
}

