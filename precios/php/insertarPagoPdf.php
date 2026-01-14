<?php

include_once '../../DAOConeccion/coneccion.php';
$cn = new Coneccion();
$cn->Conectarse();

$id = $_GET["idAlmacenEncabezado"];

$file = $_FILES["file"]["name"];


if (!is_dir("../archivos/"))
    mkdir("../archivos/", 0777);

if ($file && move_uploaded_file($_FILES["file"]["tmp_name"], "../archivos/" . $file)) {
    echo $file;
}



$archivoC = "archivos/" . $file;

$sql = "INSERT INTO archivospdfpagos (idAlmacen, archivoPago) VALUES ('$id', '$archivoC')";

if (mysql_query($sql) === TRUE) {
    echo "New record created successfully";
} else {
    echo "Error: ";
}
$cn->cerrarBd();
