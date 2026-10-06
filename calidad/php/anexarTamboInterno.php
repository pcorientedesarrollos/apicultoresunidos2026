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
            $traspaso_tabla = 'almacentraspaso';
            break;
        case '2':
            $almacen_tabla = 'almacen_organico';
            $calidad_tabla = 'calidad_organico';
            $tambos_tabla = 'tamboreslotes_organico';
            $traspaso_tabla = 'almacentraspaso_organico';
            break;
        case '5':
            $almacen_tabla = 'almacen_mantequilla';
            $calidad_tabla = 'calidad_mantequilla';
            $tambos_tabla = 'tamboreslotes_mantequilla';
            $traspaso_tabla = 'almacentraspaso_mantequilla';
            break;
        case '6':
            $almacen_tabla = 'almacen_altiplano';
            $calidad_tabla = 'calidad_altiplano';
            $tambos_tabla = 'tamboreslotes_altiplano';
            $traspaso_tabla = 'almacentraspaso_altiplano';
            break;
        case '7':
            $almacen_tabla = 'almacen_naranjo';
            $calidad_tabla = 'calidad_naranjo';
            $tambos_tabla = 'tamboreslotes_naranjo';
            $traspaso_tabla = 'almacentraspaso_naranjo';
            break;
        case '8':
            $almacen_tabla = 'almacen_aguacate';
            $calidad_tabla = 'calidad_aguacate';
            $tambos_tabla = 'tamboreslotes_aguacate';
            $traspaso_tabla = 'almacentraspaso_aguacate';
            break;
        case '9':
            $almacen_tabla = 'almacen_mezquite';
            $calidad_tabla = 'calidad_mezquite';
            $tambos_tabla = 'tamboreslotes_mezquite';
            $traspaso_tabla = 'almacentraspaso_mezquite';
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

        $sqlUpTotales = "UPDATE $calidad_tabla c
            LEFT JOIN (
                SELECT t.idLoteInterno, COUNT(*) AS numeroDeTambores, IFNULL(SUM(CASE t.tipo
                    WHEN '0' THEN (SELECT a.neto FROM $almacen_tabla a WHERE a.idAlmacen = t.folioTambor LIMIT 1)
                    WHEN '1' THEN (SELECT s.neto FROM almacensobrantes s WHERE s.consecutivo = t.folioTambor AND s.sobrante = t.clasificacion AND s.tipoDeMiel = :miel LIMIT 1)
                    WHEN '2' THEN (SELECT tr.neto FROM $traspaso_tabla tr WHERE tr.idAlmacen = t.folioTambor LIMIT 1)
                END), 0) AS kilosTotales
                FROM $tambos_tabla t
                WHERE t.idLoteInterno = :idLoteInternoTambos
                GROUP BY t.idLoteInterno
            ) tot ON tot.idLoteInterno = c.idLoteInterno
            SET c.numeroDeTambores = IFNULL(tot.numeroDeTambores, 0), c.kilosTotales = IFNULL(tot.kilosTotales, 0)
            WHERE c.idLoteInterno = :idLoteInterno";
        $datosTotales = $con->prepare($sqlUpTotales);
        $datosTotales->bindParam(':miel', $_GET['tipoDeMiel']);
        $datosTotales->bindParam(':idLoteInternoTambos', $info->idLoteInterno);
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