<?php

$post = json_decode(file_get_contents("php://input"));

if ($post) {
    include_once '../../DAOConeccion/conePDO.php';
    $con = new conePDO();
    $cn = $con->conectar();
    
    try {
        $cn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $seleccionar = $cn->prepare("SELECT idProveedorMantto, nombreProveedor FROM proveedoresmantto");
        $seleccionar->execute();

        if ($seleccionar->rowCount() >= 1) {
            echo json_encode(['error'=>false, 'data'=>$seleccionar->fetchAll(PDO::FETCH_ASSOC)]);
        } else {
            throw new Exeption('No se ha registrado');
        }
    } catch (Exception $e) {
        echo json_encode(['error'=>true, 'data'=>[]]);
    }
} else {
    exit();
}
