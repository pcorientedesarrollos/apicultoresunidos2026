<?php

include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$con = $pdo->conectar();
$json = file_get_contents("php://input");

if (isset($_GET['tipoDeMiel'])) {

    switch ($_GET['tipoDeMiel']) {
        case '1':
            $almacen_tabla = 'almacen';
            $calidad_tabla = 'calidad';
            $tambos_tabla = 'tamboreslotes';            
            break;
        case '2':
            $almacen_tabla = 'almacen_organico';
            $calidad_tabla = 'calidad_organico';
            $tambos_tabla = 'tamboreslotes_organico';                        
            break;
        case '5':
            $almacen_tabla = 'almacen_mantequilla';
            $calidad_tabla = 'calidad_mantequilla';
            $tambos_tabla = 'tamboreslotes_mantequilla';                        
            break;
        case '6':
            $almacen_tabla = 'almacen_altiplano';
            $calidad_tabla = 'calidad_altiplano';
            $tambos_tabla = 'tamboreslotes_altiplano';                        
            break;
        case '7':
            $almacen_tabla = 'almacen_naranjo';
            $calidad_tabla = 'calidad_naranjo';
            $tambos_tabla = 'tamboreslotes_naranjo';                        
            break;
        case '8':
            $almacen_tabla = 'almacen_aguacate';
            $calidad_tabla = 'calidad_aguacate';
            $tambos_tabla = 'tamboreslotes_aguacate';                        
            break;
        case '9':
            $almacen_tabla = 'almacen_mezquite';
            $calidad_tabla = 'calidad_mezquite';
            $tambos_tabla = 'tamboreslotes_mezquite';                        
            break;
        default:
            throw new Exception('El tipo de miel seleccionado no es válido');
            break;
    }

    try {

        $con->beginTransaction();
        
        if (!$json) {
                throw new Exception('No se recibieron parámetros');
        } else {
                $datos = json_decode($json);
                $info = $datos->valor;
        }

        $sql = "INSERT INTO $tambos_tabla (folioTambor, idLoteInterno, tipo, clasificacion) VALUES (:folioTambor, :idLoteInterno, :tipo, :clasificacion)";
        $dats = $con->prepare($sql);
        $dats->bindParam(':folioTambor', $info->idAlmacen);
        $dats->bindParam(':idLoteInterno', $info->idLoteInterno);
        $dats->bindParam(':tipo', $info->tipo);        
        $dats->bindParam(':clasificacion', $info->clasificacion);                        
        $dats->execute();
        if ($dats == false) {
            throw new Exception($con->errorInfo());
        }

        $sqlUpTotales = "UPDATE $calidad_tabla SET numeroDeTambores = :numeroDeTambores, kilosTotales = :kilosTotales WHERE idLoteInterno = :idLoteInterno";
        $datosTotales = $con->prepare($sqlUpTotales);
        $datosTotales->bindParam(':numeroDeTambores', $info->numeroDeTambores);
        $datosTotales->bindParam(':kilosTotales', $info->kilosTotales);
        $datosTotales->bindParam(':idLoteInterno', $info->idLoteInterno);
        $datosTotales->execute();
        if ($datosTotales == false) {
            throw new Exception($con->errorInfo());
        }

        if($info->tipo === '0'){
            $sqlUp = "UPDATE $almacen_tabla SET estado = '2' WHERE idAlmacen = :folioTambor";
            $datos = $con->prepare($sqlUp);
            $datos->bindParam(':folioTambor', $info->idAlmacen);
            $datos->execute();
            if ($datos == false) {
                throw new Exception($con->errorInfo());
            }
        }else if($info->tipo === '1'){
            $sqlUp = "UPDATE almacensobrantes SET estado = '2' WHERE consecutivo = :folioTambor AND sobrante = :clasificacion AND tipoDeMiel = :miel";
            $datos = $con->prepare($sqlUp);
            $datos->bindParam(':folioTambor', $info->idAlmacen);
            $datos->bindParam(':clasificacion', $info->clasificacion);
            $datos->bindParam(':miel', $_GET['tipoDeMiel']);            
            $datos->execute();
            if ($datos == false) {
                throw new Exception($con->errorInfo());
            }
        }

        $con->commit();
        echo json_encode(['error' => false, 'message' => 'Consulta realizada']);
    } catch (Exception $e) {
        $con->rollBack();
        echo json_encode(['error' => true, 'message' => $e->getMessage() . ' Linea: ' . $e->getLine()]);
    }

}

?>