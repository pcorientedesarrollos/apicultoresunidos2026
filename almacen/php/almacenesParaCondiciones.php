<?php
include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO(); $con = $pdo->conectar();
$con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
if(isset($_GET['opt'])){

    switch($_GET['opt']){
        case 'Disponibles':
            $resultado = array();
            try{
                $datos = $con->prepare("SELECT CASE 
                                        WHEN s.idSubarea IN (SELECT idSubarea FROM almacenestemporales)
                                            THEN TRUE
                                            ELSE FALSE
                                        END as active, s.idSubarea, s.subarea, nombre, CONCAT(s.subarea, ' - ', s.nombre) as nombreLg
                                        FROM subareas s WHERE idArea = 6");
                $datos->execute();
                if($datos == FALSE){
                    throw new Exception($con->errorInfo());
                }
                $resultado = $datos->fetchAll(PDO::FETCH_ASSOC);
                echo json_encode(['error'=>false, 'message'=>'Consulta satisfactoria', 'data'=>$resultado]);
            } catch(Exception $e){
                echo json_encode(['error'=>true, 'message'=>$e->getMessage(), 'data'=>$resultado]);
            }
        break;
        case 'Temporales':
            $resultado = array();
            try{
                $datos = $con->prepare("SELECT a.*, CONCAT(a.subarea, ' - ', a.nombre) as nombre FROM almacenestemporales a");
                $datos->execute();
                if($datos == FALSE){
                    throw new Exception($con->errorInfo());
                }
                $resultado = $datos->fetchAll(PDO::FETCH_ASSOC);
                echo json_encode(['error'=>false, 'message'=>'Consulta satisfactoria', 'data'=>$resultado]);
            } catch(Exception $e){
                echo json_encode(['error'=>true, 'message'=>$e->getMessage(), 'data'=>$resultado]);
            }        
        break;
        case 'GuardarTemporales':
            $almacenesDisponibles = json_decode(file_get_contents('php://input'));
            try{
                $con->beginTransaction();

                $truncate = $con->prepare("TRUNCATE TABLE almacenestemporales");
                $truncate->execute();
                if($truncate == FALSE){
                    throw new Exception($con->errorInfo());
                }

                foreach($almacenesDisponibles as $almacen){
                    if($almacen->active == true || $almacen->active == '1'){
                        $sql = $con->prepare("INSERT INTO almacenestemporales (idSubarea, subarea, nombre)
                                                SELECT idSubarea, subarea, nombre FROM subareas sa
                                                WHERE sa.idArea = 6 AND sa.idSubarea NOT IN (SELECT idSubarea FROM almacenestemporales)
                                                AND sa.idSubarea = :idSubarea");
                        $sql->bindParam(':idSubarea', $almacen->idSubarea);
                        $sql->execute();
                        if($sql == FALSE){
                            throw new Exception($con->errorInfo());
                        }
                    }
                }

                echo json_encode(['error'=>false, 'message'=>'Consulta satisfactoria']);
                $con->commit();
            } catch(Exception $e){
                $con->rollBack();
                echo json_encode(['error'=>true, 'message'=>$e->getMessage()]);
            }
        break;
        default:
            echo json_encode(['error'=>true, 'message'=>'Parámetro inválido']);
        break;
    }

} else {
    echo json_encode(['error'=>true, 'message'=>'No se recibió parametro']);
}
