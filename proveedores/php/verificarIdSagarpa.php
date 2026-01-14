<?php

include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$conexion = $pdo->conectar();

$idSagarpa = $_GET["idSagarpa"];

$existe = "";
//if (isset($_GET["idSagarpa"])) {
//  $idSagarpa = addslashes($_GET["idSagarpa"]);
//
$sql = "SELECT idSagarpa FROM proveedor WHERE idSagarpa = :idSagarpa";
   
$sqlExiste = $conexion->prepare($sql);
$sqlExiste->bindParam(':idSagarpa', $idSagarpa);
$sqlExiste->execute();

    while($row = $sqlExiste->fetch()){
    $existe = $row["idSagarpa"];
};

if ($existe == null) {
    $respuesta = 0;
} else {
    $respuesta = 1;
}
//}
//    while ($respuExiste = mysql_fetch_array($sqlExiste)) {
//        $idExiste = new stdClass();
//        $idExiste->existe = $respuExiste["existe"];
//    }
//    
//    if ($idExiste->existe == $idSagarpa) {
//        $respuesta = 1;
//    } else {
//        $respuesta = 0;
//   }
//}

echo json_encode($respuesta);
?>