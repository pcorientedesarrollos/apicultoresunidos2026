<?php

include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$con = $pdo->conectar();
$con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$postdata = file_get_contents('php://input');

try {
    if (!$postdata) {
        throw new Exception('No se recibieron parámetros');
    } else {
        $datos = json_decode($postdata);
        $info = $datos->valor;
    }
    date_default_timezone_set('America/Merida');
    $fecha = date("Y-m-d");
    $idProveedor = $_GET["idProveedor"];
    if(isset($_GET['miel'])){
        switch ($_GET['miel']) {
                    case '1':
                        $encabezado = 'almacenencabezado';
                        $detalle = 'almacen';
                        break;
                    case '2':
                        $encabezado = 'almacenencabezado_organico';
                        $detalle = 'almacen_organico';
                        break;
                    case '5':
                        $encabezado = 'almacenencabezado_mantequilla';
                        $detalle = 'almacen_mantequilla';
                        break;
                    case '6':
                        $encabezado = 'almacenencabezado_altiplano';
                        $detalle = 'almacen_altiplano';
                        break;
                    case '7':
                        $encabezado = 'almacenencabezado_naranjo';
                        $detalle = 'almacen_naranjo';
                        break;
                    case '8':
                        $encabezado = 'almacenencabezado_aguacate';
                        $detalle = 'almacen_aguacate';
                        break;
                    case '9':
                        $encabezado = 'almacenencabezado_mezquite';
                        $detalle = 'almacen_mezquite';
                        break;
                    default:
                        throw new Exception('Tipo de miel inválido');
                        break;
        }
    }

    $con->beginTransaction();

        $sqlInsert = $con->prepare("INSERT INTO $encabezado (fecha, idProveedor, folio, totalCompra) VALUES (:fecha, :idProveedor, '0', '0');");
        $sqlInsert->bindParam(':fecha', $fecha);
        $sqlInsert->bindParam(':idProveedor', $idProveedor);        
        $sqlInsert->execute();
        if ($sqlInsert == false) {
            throw new Exception($con->errorInfo());
        }
        $idAlmacenEncabezado = $con->lastInsertId();
        
        $sqlUpdate = $con->prepare("UPDATE $encabezado set folio = :folio WHERE idAlmacen = :idAlmacen");
        $sqlUpdate->bindParam(':folio', $idAlmacenEncabezado);
        $sqlUpdate->bindParam(':idAlmacen', $idAlmacenEncabezado);        
        $sqlUpdate->execute();
        if ($sqlUpdate == false){
            throw new Exception($con->errorInfo());
        }
    
        foreach ($info as $almacen) {
            $sql = $con->prepare("INSERT INTO $detalle (idAlmacenEncabezado, zona,  trazabilidad, pesoLista, bruto, tara, neto, diferencia, humedad, autorizado, precio, costoTotal, estado, referencia) VALUES(:idAlmacenEncabezado, :zona, :trazabilidad, :pesoLista, :bruto, :tara, :neto, :diferencia, :humedad, '0', '0', '0', '0', :referencia)");
            $sql->bindParam(':idAlmacenEncabezado', $idAlmacenEncabezado);
            $sql->bindParam(':zona', $almacen->zona);        
            $sql->bindParam(':trazabilidad', $almacen->trazabilidad);        
            $sql->bindParam(':pesoLista', $almacen->pesoLista);        
            $sql->bindParam(':bruto', $almacen->bruto);        
            $sql->bindParam(':tara', $almacen->tara);        
            $sql->bindParam(':neto', $almacen->neto);        
            $sql->bindParam(':diferencia', $almacen->diferencia);        
            $sql->bindParam(':humedad', $almacen->humedad);        
            $sql->bindParam(':referencia', $almacen->referencia);            
            $sql->execute();
            if ($sql == false){
                throw new Exception($con->errorInfo());
            }
        }

        $mensaje = 'Se ha guardado el registro';

    $con->commit();
    echo json_encode(['error' => false, 'message' => $mensaje]);

} catch (Exception $e) {
    $con->rollBack();
    echo json_encode(['error' => true, 'message' => $e->getMessage() . '. Line ' . $e->getLine()]);
}
