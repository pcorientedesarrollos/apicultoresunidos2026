<?php

include_once '../../DAOConeccion/conePDO.php';
include_once './calcularNotaCajaChica.php';
include_once './nombreDePersona.php';
$pdo = new conePDO();
$con = $pdo->conectar();

$postdata = file_get_contents('php://input');

if ($postdata) {
    switch($_GET['opcion']):
        case 'encabezado':
            $encabezado = json_decode($postdata);
            try{
                $con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
                $con->beginTransaction();
                $fecha = $encabezado->fecha;
                $idMes = explode('-', $fecha)[1];
                $hora = $encabezado->hora;
                $tipoDeCliente = $encabezado->tipoDeCliente;
                $nombre = $encabezado->idNombre;
                $idCajaChica = $encabezado->idCajaChica;
                $sql = $con->prepare(
                    "UPDATE cajachica SET fecha = :fecha, idMes = :idMes, hora = :hora, tipoDeCliente = :tipoDeCliente, nombre = :nombre WHERE idCajaChica = :idCajaChica"
                );
            
                $sql->bindParam(':fecha', $fecha);
                $sql->bindParam(':idMes', $idMes);
                $sql->bindParam(':hora', $hora);
                $sql->bindParam(':tipoDeCliente', $tipoDeCliente);
                $sql->bindParam(':nombre', $nombre);
                $sql->bindParam(':idCajaChica', $idCajaChica);
                $sql->execute();
        
                if($sql->rowCount() == 1){
                    $resultado = array();
                    $consulta = $con->prepare("SELECT * FROM cajachica WHERE idCajaChica = :idCajaChica");
                    $consulta->bindParam(':idCajaChica', $idCajaChica);
                    $consulta->execute();
                    $resultado['encabezado'] = $consulta->fetch(PDO::FETCH_ASSOC);
                    $resultado['encabezado']['tipoLg'] = $resultado['encabezado']['tipo'] == 0 ? 'INGRESO' : 'EGRESO';
                    $resultado['encabezado']['idNombre'] = $resultado['encabezado']['nombre'];
                    $resultado['encabezado']['nombre'] = retornarNombre($con, $resultado['encabezado']['tipoDeCliente'], $resultado['encabezado']['nombre']);
        
                } else {
                    throw new Exception('No se ha podido actualizar.');
                }
        
                $con->commit();
                echo json_encode(['error'=>false, 'message'=>'Se ha editado el encabezado.', 'cajaChicaEncabezado'=>$resultado]);
            } catch( Exception $e) {
                $con->rollBack();
                echo json_encode(['error'=>true, 'message'=>$e->getMessage()]);
            }
        break;
        case 'borrarDetalle':
            $datos = json_decode($postdata);
            try{
                $con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                $con->beginTransaction();
                if($datos->idMovimiento == 8) {
                    throw new Exception('Este movimiento no puede ser eliminado aún');
                } else {
                    $sql = $con->prepare("DELETE FROM cajachicadetalle WHERE idDetalle = :idDetalle");
                    $sql->bindParam(':idDetalle', $datos->idDetalle);
                    $sql->execute();
                    if( $sql->rowCount() != 1 ) {
                        throw new Exception('El registro no ha podido ser eliminado');
                    }

                    $calcularCajaChica = calcularNotaCajaChica($con, $datos->idCajaChica);
                    if($calcularCajaChica['error']){
                        throw new Exception($calcularCajaChica['message']);
                    }
                    $con->commit();
                    echo json_encode(['error'=>false, 'message'=>'El registro ha sido eliminado', 'total'=>$calcularCajaChica['total']]);
                }
            }catch(Exception $e){
                $con->rollBack();
                echo json_encode(['error'=>true, 'message'=>$e->getMessage()]);
            }
        
        break;
        case 'editarDetalle':
            $datos = json_decode($postdata);
            try{
                $con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                // $concepto = $datos->detalleCC->concepto;
                $idConcepto = $datos->detalleCC->idConcepto;
                $idSubconcepto = $datos->detalleCC->idSubConcepto;
                $descripcion = $datos->detalleCC->descripcion;
                // $movimiento = $datos->detalleCC->movimiento;
                $idMovimiento = $datos->detalleCC->idMovimiento;
                $idDetalle = $datos->detalleCC->idDetalle;

                $sqlCuenta = $con->prepare("SELECT cuenta FROM cuentas WHERE idCuentaConcepto = :cuenta");
                $sqlCuenta->bindParam(':cuenta', $idMovimiento);
                $sqlCuenta->execute();
                if (!$sqlCuenta) {
                    throw new Exception($con->errorInfo());
                }
                $resultadoCuenta = $sqlCuenta->fetch(PDO::FETCH_ASSOC);
        
                $sqlSubcuenta = $con->prepare("SELECT subcuenta FROM subcuentas WHERE idSubcuenta = :subcuenta");
                $sqlSubcuenta->bindParam(':subcuenta', $idConcepto);
                $sqlSubcuenta->execute();
                if (!$sqlSubcuenta) {
                    throw new Exception($con->errorInfo());
                }
                $resultadoSubcuenta = $sqlSubcuenta->fetch(PDO::FETCH_ASSOC);
        
                $sqlSubsubcuenta = $con->prepare("SELECT subSubcuenta FROM subsubcuentas WHERE idSubSubcuenta = :subsubcuenta");
                $sqlSubsubcuenta->bindParam(':subsubcuenta', $idSubconcepto);
                $sqlSubsubcuenta->execute();
                if (!$sqlSubsubcuenta) {
                    throw new Exception($con->errorInfo());
                }
                $resultadoSubsubcuenta = $sqlSubsubcuenta->fetch(PDO::FETCH_ASSOC);
        

                if($datos->tipo == 0) {
                    $kg = $datos->detalleCC->kg;
                    $precio = $datos->detalleCC->precio;
                    $importe = $datos->detalleCC->importe;
                    $sql = $con->prepare("UPDATE cajachicadetalle SET concepto = :concepto, idConcepto = :idConcepto, idSubConcepto = :idSubConcepto,
                    descripcion = :descripcion, movimiento = :movimiento, idMovimiento = :idMovimiento, kg = :kg, precio = :precio,
                    importe = :importe, subconcepto = :subconcepto WHERE idDetalle = :idDetalle");
                    $sql->bindParam(':kg', $kg);
                    $sql->bindParam(':precio', $precio);
                    $sql->bindParam(':importe', $importe);
                } else {
                    $cantidad = $datos->detalleCC->cantidad;
                    $sql = $con->prepare("UPDATE cajachicadetalle SET concepto = :concepto, idConcepto = :idConcepto, idSubConcepto = :idSubConcepto,
                    descripcion = :descripcion, movimiento = :movimiento, idMovimiento = :idMovimiento, cantidad = :cantidad, subconcepto = :subconcepto
                    WHERE idDetalle = :idDetalle");
                    $sql->bindParam(':cantidad', $cantidad);
                }
                $sql->bindParam(':concepto', $resultadoSubcuenta['subcuenta']);
                $sql->bindParam(':idConcepto', $idConcepto);
                $sql->bindParam(':idSubConcepto', $idSubconcepto);
                $sql->bindParam(':descripcion', $descripcion);
                $sql->bindParam(':movimiento', $resultadoCuenta['cuenta']);
                $sql->bindParam(':idMovimiento', $idMovimiento);
                $sql->bindParam(':subconcepto', $resultadoSubsubcuenta['subSubcuenta']);
                $sql->bindParam(':idDetalle', $idDetalle);

                $sql->execute();
                if($sql->rowCount() != 1) {
                    throw new Exception('No se pudo actualiza el detalle');
                }

                $calcularCajaChica = calcularNotaCajaChica($con, $datos->idCajaChica);
                if($calcularCajaChica['error']){
                    throw new Exception($calcularCajaChica['message']);
                }
                echo json_encode(['error'=>false, 'message'=>'Se recibe parametro', 'total'=>$calcularCajaChica['total']]);
            } catch(Exception $e) {
                echo json_encode(['error'=>true, 'message'=>$e->getMessage()]);
            }
        break;
        default:
            echo json_encode(['error'=>true, 'message'=>'Edición desconocida']);
        break;
    endswitch;
} else {
    exit();
}
