<?php
header('Content-type: application/vnd.ms-excel');
header("Content-Disposition: attachment; filename=Informe Financiero Oaxaca Miel.xls");
header("Pragma: no-cache");
header("Expires:0");

// Leer la información que se va a imprimir en la sesión
if(!isset($_SESSION)) {
    session_start();
}

if(isset($_SESSION)) {
    if(isset($_SESSION['informeFinanciero'])) {
        $reporte = $_SESSION['informeFinanciero'];
        unset($_SESSION['informeFinanciero']);
        echo $reporte;
    } else {
        echo 'No se ha podido descargar la información';
    }
}