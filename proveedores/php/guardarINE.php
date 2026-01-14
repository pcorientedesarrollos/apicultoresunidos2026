<?php

//include_once '../../DAOConeccion/coneccion.php';
//$cn = new Coneccion();
//$cn->Conectarse();

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

$sql = "INSERT INTO archivosine (idProveedor, archivoINE, tipo) VALUES (:id, :archivoR, :tipo)";
$infor = $conexion->prepare($sql);
$infor->bindParam(':id', $id);
$infor->bindParam(':archivoR', $archivoR);
$infor->bindParam(':tipo', $tipo);
$infor->execute();

if ($infor === TRUE) {
    echo "New record created successfully";
} else {
    echo "Error: ";
}
