<?php

include_once '../../DAOConeccion/conePDO.php';
$post = json_decode(file_get_contents("php://input"));
$con = new conePDO();
$cn = $con->conectar();

if ($post) {
    try {
        $cn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        $seleccionarClientes = $cn->prepare("SELECT * FROM clientes");
        $seleccionarClientes->execute();

        if ($seleccionarClientes->rowCount() >= 1) {
            echo json_encode(['error'=>false, 'data'=>$seleccionarClientes->fetchAll(PDO::FETCH_ASSOC)]);
        } else {
            throw new Exeption('No hay clientes para mostrar');
        }
    } catch (Exception $e) {
        echo json_encode(['error'=>true, 'data'=>[]]);
    }
} else {
    exit();
}
