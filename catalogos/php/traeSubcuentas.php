<?php
include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$con = $pdo->conectar();

try {
        $idCuentaConcepto = $_GET['idCuentaConcepto'];
        
        $datos = $con->prepare('SELECT * FROM subcuentas WHERE idCuentaConcepto = :idCuentaConcepto ');
        $datos->bindParam(':idCuentaConcepto', $idCuentaConcepto); 
        $datos->execute();
        $resultado = array();
        if ($datos == false) {
            throw new Exception($con->errorInfo());
        }

            if ($datos->rowCount() >= 1) {
                foreach ($datos->fetchAll(PDO::FETCH_ASSOC) as $dato) {
                    $idSubcuenta = $dato['idSubcuenta'];
                    $sqlSubSub = $con->prepare("SELECT ssc.idSubSubcuenta, ssc.subSubcuenta,ssc.clave,ssc.min, ssc.precioUnitario, ssc.peso,ssc.unidad , ssc.max, ssc.precio, s.idSubcuenta 
                                                            FROM subsubcuentas ssc 
                                                            LEFT JOIN subcuentas s ON s.idSubcuenta = ssc.idSubcuenta 
                                                            WHERE s.idSubcuenta = :idSubcuenta AND ssc.ocultar = 0
                                                            ORDER BY ssc.clave ASC");
                    $sqlSubSub->bindParam(':idSubcuenta', $idSubcuenta);
                    $sqlSubSub->execute();
                    $dato['subSubcuentas'] = [];
                        foreach ($sqlSubSub->fetchAll(PDO::FETCH_ASSOC) as $data) {
                          array_push($dato['subSubcuentas'], $data);
                        }
                    array_push($resultado, $dato);
                }
            }
      

    // echo json_encode(['error' => false, 'data' => $resultado]);

    echo $json_response = json_encode($resultado);
} catch (Exception $e) {
    // echo json_encode(['error' => true, 'message' => $e->getMessage()]);
}