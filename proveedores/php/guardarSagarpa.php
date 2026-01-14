<?php

include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$conexion = $pdo->conectar();

$id = $_GET["id"];

$file = $_FILES["file"]["name"];


if (!is_dir("../archivos/"))
    mkdir("../archivos/", 0777);

if ($file && move_uploaded_file($_FILES["file"]["tmp_name"], "../archivos/" . $file)) {
    echo $file;
}


$archivoC = "archivos/" . $file;

$sql = "INSERT INTO archivossagarpa (idProveedor, archivoSagarpa) VALUES (:id, :archivoC)";
$informa = $conexion->prepare($sql);
$informa->bindParam(':id', $id);
$informa->bindParam(':archivoC', $archivoC);
$informa->execute();

if ($informa === TRUE) {
    echo "New record created successfully";
} else {
    echo "Error: ";
}

