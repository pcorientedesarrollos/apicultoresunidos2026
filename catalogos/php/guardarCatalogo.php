<?php
include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO(); $con = $pdo->conectar();
$con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$post = file_get_contents('php://input');

try {
    if(!$post) { throw new Exception('No se recibieron datos'); }
    $catalogo = json_decode($post);
    if(isset($catalogo->concepto) && $catalogo->concepto != ''){
        $sqlInsert = $con->prepare("INSERT INTO conceptoscajachica
            (concepto, catalogo) VALUES (:concepto, 1)");
        $sqlInsert->bindParam(':concepto', $catalogo->concepto);
        $sqlInsert->execute();
        if($sqlInsert == FALSE) { throw new Exception($con->errorInfo()); }
        echo json_encode(['error'=>false, 'message'=>'Catálogo registrado']);
    } else {
        throw new Exception('Formato inválido');
    }
} catch (Exception $e) {
    echo json_encode(['error'=>true, 'message'=>$e->getMessage()]);
}
