<?php
// Archivo: /sites/apicultoresunidos/WebServices/dameImpresion.php
header('Content-Type: application/json');

if ($_POST['accessKey'] === '8ca4c7322b882552296176913fde7efa') {
    try {
        $pdo = new PDO('mysql:host=localhost;dbname=mielorganica2025', $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        $stmt = $pdo->query("SELECT * FROM impresion WHERE estado = 0 ORDER BY idImpresion DESC");
        $datos = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo json_encode($datos);
    } catch(PDOException $e) {
        echo json_encode(['error' => $e->getMessage()]);
    }
} else {
    echo json_encode(['error' => 'Access denied']);
}
?>