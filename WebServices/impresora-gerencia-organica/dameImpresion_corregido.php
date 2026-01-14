<?php

header('Access-Control-Allow-Origin: *');
include_once '../DAOConeccion/conexionWebServices.php';
$pdo = new conePDO();
$con = $pdo->conectar('mielorganica2025');

$con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

try {
    // Verificar access key si está configurado
    if (isset($_POST['accessKey']) && $_POST['accessKey'] !== '8ca4c7322b882552296176913fde7efa') {
        echo json_encode(['error' => 'Access denied']);
        exit;
    }
    
    $resultado = array();
    
    // Obtener todos los registros de impresión pendientes (estado 0)
    $sql = "SELECT 
                imp.idImpresion, 
                imp.idAlmacen, 
                imp.estado, 
                imp.idTipoDeMiel,
                tdm.tipoDeMiel,
                CASE 
                    WHEN imp.estado = 0 THEN 'Tambores de almacen'
                    WHEN imp.estado = 1 THEN 'Tambores de almacen'
                    WHEN imp.estado = 2 THEN 'Equipos'
                    WHEN imp.estado = 3 THEN 'Tambores de salida'
                    WHEN imp.estado = 4 THEN 'Tambores de salida'
                    WHEN imp.estado = 5 THEN 'Sobrantes'
                    ELSE 'Desconocido'
                END as tipoEtiqueta
            FROM impresion imp
            LEFT JOIN tiposdemiel tdm ON imp.idTipoDeMiel = tdm.idTipoDeMiel
            WHERE imp.estado IN (0, 1, 2, 3, 4, 5)
            ORDER BY imp.idImpresion DESC";
            
    $stmt = $con->prepare($sql);
    $stmt->execute();
    
    if ($stmt == false) {
        throw new Exception($con->errorInfo());
    }
    
    $datos = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Si no hay datos, retornar array vacío
    if (!$datos) {
        $datos = [];
    }
    
    echo json_encode($datos);
    
} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
?>