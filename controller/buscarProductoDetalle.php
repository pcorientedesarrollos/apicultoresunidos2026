<?php

include_once '../DAOConeccion/coneccion.php';
$cn = new Coneccion();
$cn->Conectarse();
$codigo = $_GET["codigo"];

