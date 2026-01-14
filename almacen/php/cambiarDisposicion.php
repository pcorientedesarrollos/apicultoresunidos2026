<?php

include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$con = $pdo->conectar();

$valor = $_GET["valor"];
$id = $_GET["id"];
$semana = $_GET["semana"];

switch ($semana) {
    case 1:
        $sql = "UPDATE disposiciondelpersonal set semana1 = :valor WHERE idDisposicion = :id";
        break;
    case 2:
        $sql = "UPDATE disposiciondelpersonal set semana2 = :valor WHERE idDisposicion = :id";
        break;
    case 3:
        $sql = "UPDATE disposiciondelpersonal set semana3 = :valor WHERE idDisposicion = :id";
        break;
    case 4:
        $sql = "UPDATE disposiciondelpersonal set semana4 = :valor WHERE idDisposicion = :id";
        break;
    case 5:
        $sql = "UPDATE disposiciondelpersonal set semana5 = :valor WHERE idDisposicion = :id";
        break;
    case 6:
        $sql = "UPDATE disposiciondelpersonal set semana1A = :valor WHERE idDisposicion = :id";
        break;
    case 7:
        $sql = "UPDATE disposiciondelpersonal set semana2A = :valor WHERE idDisposicion = :id";
        break;
    case 8:
        $sql = "UPDATE disposiciondelpersonal set semana3A = :valor WHERE idDisposicion = :id";
        break;
    case 9:
        $sql = "UPDATE disposiciondelpersonal set semana4A = :valor WHERE idDisposicion = :id";
        break;
    case 10:
        $sql = "UPDATE disposiciondelpersonal set semana5A = :valor WHERE idDisposicion = :id";
        break;
    case 11:
        $sql = "UPDATE disposiciondelpersonal set semana1B = :valor WHERE idDisposicion = :id";
        break;
    case 12:
        $sql = "UPDATE disposiciondelpersonal set semana2B = :valor WHERE idDisposicion = :id";
        break;
    case 13:
        $sql = "UPDATE disposiciondelpersonal set semana3B = :valor WHERE idDisposicion = :id";
        break;
    case 14:
        $sql = "UPDATE disposiciondelpersonal set semana4B = :valor WHERE idDisposicion = :id";
        break;
    case 15:
        $sql = "UPDATE disposiciondelpersonal set semana5B = :valor WHERE idDisposicion = :id";
        break;
   case 16:
        $sql = "UPDATE disposiciondelpersonal set semana1C = :valor WHERE idDisposicion = :id";
        break;
    case 17:
        $sql = "UPDATE disposiciondelpersonal set semana2C = :valor WHERE idDisposicion = :id";
        break;
    case 18:
        $sql = "UPDATE disposiciondelpersonal set semana3C = :valor WHERE idDisposicion = :id";
        break;
    case 19:
        $sql = "UPDATE disposiciondelpersonal set semana4C = :valor WHERE idDisposicion = :id";
        break;
    case 20:
        $sql = "UPDATE disposiciondelpersonal set semana5C = :valor WHERE idDisposicion = :id";
        break;
    case 21:
        $sql = "UPDATE disposiciondelpersonal set semana1D = :valor WHERE idDisposicion = :id";
        break;
    case 22:
        $sql = "UPDATE disposiciondelpersonal set semana2D = :valor WHERE idDisposicion = :id";
        break;
    case 23:
        $sql = "UPDATE disposiciondelpersonal set semana3D = :valor WHERE idDisposicion = :id";
        break;
    case 24:
        $sql = "UPDATE disposiciondelpersonal set semana4D = :valor WHERE idDisposicion = :id";
        break;
    case 25:
        $sql = "UPDATE disposiciondelpersonal set semana5D = :valor WHERE idDisposicion = :id";
        break;
    case 26:
        $sql = "UPDATE disposiciondelpersonal set semana1E = :valor WHERE idDisposicion = :id";
        break;
    case 27:
        $sql = "UPDATE disposiciondelpersonal set semana2E = :valor WHERE idDisposicion = :id";
        break;
    case 28:
        $sql = "UPDATE disposiciondelpersonal set semana3E = :valor WHERE idDisposicion = :id";
        break;
    case 29:
        $sql = "UPDATE disposiciondelpersonal set semana4E = :valor WHERE idDisposicion = :id";
        break;
    case 30:
        $sql = "UPDATE disposiciondelpersonal set semana5E = :valor WHERE idDisposicion = :id";
        break;
    case 31:
        $sql = "UPDATE disposiciondelpersonal set semana1F = :valor WHERE idDisposicion = :id";
        break;
    case 32:
        $sql = "UPDATE disposiciondelpersonal set semana2F = :valor WHERE idDisposicion = :id";
        break;
    case 33:
        $sql = "UPDATE disposiciondelpersonal set semana3F = :valor WHERE idDisposicion = :id";
        break;
    case 34:
        $sql = "UPDATE disposiciondelpersonal set semana4F = :valor WHERE idDisposicion = :id";
        break;
    case 35:
        $sql = "UPDATE disposiciondelpersonal set semana5F = :valor WHERE idDisposicion = :id";
        break;
}
$datos = $con->prepare($sql);
$datos->bindParam(':valor', $valor);
$datos->bindParam(':id', $id);
$datos->execute();
if ($datos == false) {
    echo mysql_error();
} else {
    echo "Estado cambiado satisfactoriamente";
}
?>