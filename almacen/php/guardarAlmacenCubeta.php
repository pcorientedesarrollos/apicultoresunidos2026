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
    $fecha = date("Y-m-d");
    $idProveedor = $_GET["idProveedor"];
    $valorFolio = $_GET["valorFolio"];
    if(isset($_GET['miel'])){
        switch ($_GET['miel']) {
                    case '1':
                        $encabezado = 'cubetasencabezado';
                        $detalle = 'cubetasdetalle';
                        break;
                    case '2':
                        $encabezado = 'cubetasencabezado_organico';
                        $detalle = 'cubetasdetalle_organico';
                        break;
                    case '5':
                        $encabezado = 'cubetasencabezado_mantequilla';
                        $detalle = 'cubetasdetalle_mantequilla';
                        break;
                    case '6':
                        $encabezado = 'cubetasencabezado_altiplano';
                        $detalle = 'cubetasdetalle_altiplano';
                        break;
                    case '7':
                        $encabezado = 'cubetasencabezado_naranjo';
                        $detalle = 'cubetasdetalle_naranjo';
                        break;
                    case '8':
                        $encabezado = 'cubetasencabezado_aguacate';
                        $detalle = 'cubetasdetalle_aguacate';
                        break;
                    case '9':
                        $encabezado = 'cubetasencabezado_mezquite';
                        $detalle = 'cubetasdetalle_mezquite';
                        break;
                    default:
                        throw new Exception('Tipo de miel inválido');
                        break;
        }
    }

    $con->beginTransaction();

    if ($valorFolio == '1') {
        $folioEntradaTambor = $_GET["folioEntradaTambor"];
    }else if($valorFolio == '2'){
        $folioEntradaTambor = '99999';
    }

        $sqlInsert = $con->prepare("INSERT INTO $encabezado (fecha, idProveedor, totalCompra, folioEntradaTambor) VALUES (:fecha, :idProveedor, '0', :folioEntradaTambor)");
        $sqlInsert->bindParam(':fecha', $fecha);
        $sqlInsert->bindParam(':idProveedor', $idProveedor);
        $sqlInsert->bindParam(':folioEntradaTambor', $folioEntradaTambor);        
        $sqlInsert->execute();
        if ($sqlInsert == false) {
            throw new Exception($con->errorInfo());
        }

        $idAlmacenEncabezado = $con->lastInsertId();            

        foreach ($info as $almacen) {
            $sql = $con->prepare("INSERT INTO $detalle (idAlmacenEncabezado, zona, trazabilidad, pesoLista, bruto, tara, neto, diferencia, humedad, autorizado, precio, costoTotal, tamborAsignado, referencia) VALUES(:idAlmacenEncabezado, :zona, '', :pesoLista, :bruto, :tara, :neto, :diferencia, :humedad, '0', '0', '0', '0', :referencia)");
            $sql->bindParam(':idAlmacenEncabezado', $idAlmacenEncabezado);
            $sql->bindParam(':zona', $almacen->zona);        
            // $sql->bindParam(':trazabilidad', $almacen->trazabilidad);        
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
