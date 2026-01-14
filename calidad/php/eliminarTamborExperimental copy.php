<?php

include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$con = $pdo->conectar();

if(isset($_GET['experimental'])){   //TAMBORES EN EXPERIMENTALES 
    $json = file_get_contents("php://input");
    try{ 
        if (!$json) {
            throw new Exception('No se recibieron parámetros');
        } else {
            $datos = json_decode($json);
            $info = $datos->valor;
            $folioTambor = $info->folioTambor;
            $miel = $info->miel;
            $clasificacion = $info->clasificacion;            
            switch($miel){
                case '1':
                    $tambores = 'tamboresexperimentales';
                    $almacen = 'almacen';
                break;
                case '2':
                    $tambores = 'tamboresexperimentales_organico';
                    $almacen = 'almacen_organico';
                break;
            }
            if($clasificacion == "0"){
                $consulta = '0 AND clasificacion = :clasificacion';            
            }else{
                $consulta = '1 AND clasificacion = :clasificacion';
            }
        }
        $con->beginTransaction();      

        $sql = "DELETE FROM $tambores WHERE folioTambor = :folioTambor AND tipo = $consulta";
        $data = $con->prepare($sql);
        $data->bindParam(':folioTambor', $folioTambor);
        $data->bindParam(':clasificacion', $clasificacion);        
        $data->execute();
        if ($data == false) {
            throw new Exception($con->errorInfo());
        }

        if($clasificacion == "0"){
            $sqlUp = "UPDATE $almacen SET estado = '0' WHERE idAlmacen = :folioTambor";
            $datos = $con->prepare($sqlUp);
            $datos->bindParam(':folioTambor', $folioTambor);
            $datos->execute();
            if ($datos == false) {
                throw new Exception($con->errorInfo());
            }  
        }else{
            $sqlUp = "UPDATE almacensobrantes SET estado = '0' WHERE consecutivo = :folioTambor AND sobrante = :clasificacion AND tipoDeMiel = :miel";
            $datos = $con->prepare($sqlUp);
            $datos->bindParam(':folioTambor', $folioTambor);
            $datos->bindParam(':clasificacion', $clasificacion);
            $datos->bindParam(':miel', $miel);            
            $datos->execute();
            if ($datos == false) {
                throw new Exception($con->errorInfo());
            }          
        }
        $con->commit();
        echo json_encode(['error' => false, 'info'=>$info]);
    } catch (Exception $e) {
        $con->rollBack();
        echo json_encode(['error' => true, 'message' => $e->getMessage() . ' Linea: ' . $e->getLine()]);
    }
}


if(isset($_GET['idLoteInterno'])){  // TAMBORES EN LOTES INTERNOS //
    $json = file_get_contents("php://input");
    try{ 
        if (!$json) {
            throw new Exception('No se recibieron parámetros');
        } else {
            $datos = json_decode($json);
            $info = $datos->valor;
            $folioTambor = $info->folioTambor;
            $miel = $info->miel;
            $clasificacion = $info->clasificacion; 
            $peso = 0;
            $respNumeroDeTambores = 0;
            $respKilosTotales = 0;
            $numeroDeTambores = 0;
            $kilosTotales = 0;
            $tambosExp = 0;
            $kilosExp = 0;
            $numeroDeTamboresExp = 0;
            $kilosTotalesExp = 0;           
            switch($miel){
                case '1':
                    $tambores = 'tamboreslotes';
                    $almacen = 'almacen';
                    $calidad = 'calidad';
                    $tamboresExperimental = 'tamboresexperimentales';
                    $experimental = 'experimental';
                    break;
                case '2':
                    $tambores = 'tamboreslotes_organico';
                    $almacen = 'almacen_organico';
                    $calidad = 'calidad_organico';
                    $tamboresExperimental = 'tamboresexperimentales_organico';
                    $experimental = 'experimental_organico';
                    break;
            };
            if($clasificacion == "0"){
                $consulta = '0 AND clasificacion = :clasificacion';            
            }else{
                $consulta = '1 AND clasificacion = :clasificacion';
            }
        }

        $con->beginTransaction();

            if($clasificacion == "0"){
                $sqlGet = "SELECT neto FROM $almacen WHERE idAlmacen = :folioTambor";
                $info = $con->prepare($sqlGet);
                $info->bindParam(':folioTambor', $folioTambor);
                $info->execute();
                $info->bindColumn('neto', $peso);
                if ($info == false) {
                    throw new Exception($con->errorInfo());
                } else {
                    $info->fetch(PDO::FETCH_BOUND);
                }
            }else{
                $sqlGet = "SELECT neto FROM almacensobrantes WHERE consecutivo = :folioTambor AND sobrante = :clasificacion AND tipoDeMiel = :miel";
                $info = $con->prepare($sqlGet);
                $info->bindParam(':folioTambor', $folioTambor);
                $info->bindParam(':clasificacion', $clasificacion);
                $info->bindParam(':miel', $miel);                
                $info->execute();
                $info->bindColumn('neto', $peso);
                if ($info == false) {
                    throw new Exception($con->errorInfo());
                } else {
                    $info->fetch(PDO::FETCH_BOUND);
                }
            }

        $sqlLote = "DELETE FROM $tambores WHERE folioTambor = :folioTambor AND tipo = $consulta";
        $data = $con->prepare($sqlLote);
        $data->bindParam(':folioTambor', $folioTambor);
        $data->bindParam(':clasificacion', $clasificacion);   
        $data->execute();
        if ($data == false) {
            throw new Exception($con->errorInfo());
        }

        $sqlGetTotales = "SELECT numeroDeTambores, kilosTotales FROM $calidad WHERE idLoteInterno = :idLoteInterno";
        $informacionTotal = $con->prepare($sqlGetTotales);
        $informacionTotal->bindParam(':idLoteInterno', $_GET['idLoteInterno']);
        $informacionTotal->execute();
        $informacionTotal->bindColumn('numeroDeTambores', $respNumeroDeTambores);
        $informacionTotal->bindColumn('kilosTotales', $respKilosTotales);
        if ($informacionTotal == false) {
            throw new Exception($con->errorInfo());
        } else {
            $informacionTotal->fetch(PDO::FETCH_BOUND);
        }
        $respNumeroDeTambores = floatval($respNumeroDeTambores);
        $respKilosTotales = floatval($respKilosTotales);
        $neto = floatval($peso);
        if ($respNumeroDeTambores > 0 && $respKilosTotales > 0) {
            $numeroDeTambores = $respNumeroDeTambores - 1;
            $kilosTotales = $respKilosTotales - $neto;
        } else {
            $numeroDeTambores = '0';
            $kilosTotales = '0';
        }

        $sqlUpTotales = "UPDATE $calidad SET numeroDeTambores = :numeroDeTambores, kilosTotales = :kilosTotales WHERE idLoteInterno = :idLoteInterno";
        $datosTotales = $con->prepare($sqlUpTotales);
        $datosTotales->bindParam(':numeroDeTambores', $numeroDeTambores);
        $datosTotales->bindParam(':kilosTotales', $kilosTotales);
        $datosTotales->bindParam(':idLoteInterno', $_GET['idLoteInterno']);
        $datosTotales->execute();
        if ($datosTotales == false) {
            throw new Exception($con->errorInfo());
        }

        $sqlExperimental = "SELECT idLoteExperimental FROM $tamboresExperimental WHERE folioTambor = :folioTambor AND clasificacion = :clasificacion";
        $informacion = $con->prepare($sqlExperimental);
        $informacion->bindParam(':folioTambor', $folioTambor);
        $informacion->bindParam(':clasificacion', $clasificacion);        
        $informacion->execute();
        $informacion->bindColumn('idLoteExperimental', $idLoteExperimental);
        if ($informacion == false) {
            throw new Exception($con->errorInfo());
        } else {
            $informacion->fetch(PDO::FETCH_BOUND);
        }

        $experimentalTotales = "SELECT numeroDeTambores, kilosTotales FROM $experimental WHERE idLoteExperimental = :idLoteExperimental";
        $dato = $con->prepare($experimentalTotales);
        $dato->bindParam(':idLoteExperimental', $idLoteExperimental);
        $dato->execute();
        $dato->bindColumn('numeroDeTambores', $tambosExp);
        $dato->bindColumn('kilosTotales', $kilosExp);
        if ($dato == false) {
            throw new Exception($con->errorInfo());
        } else {
            $dato->fetch(PDO::FETCH_BOUND);
        }
        $tambosExp = floatval($tambosExp);
        $kilosExp = floatval($kilosExp);
        $neto = floatval($peso);
        if ($tambosExp > 0 && $kilosExp > 0) {
            $numeroDeTamboresExp = $tambosExp - 1;
            $kilosTotalesExp = $kilosExp - $neto;
        } else {
            $numeroDeTamboresExp = '0';
            $kilosTotalesExp = '0';
        }

        $sql = "DELETE FROM $tamboresExperimental WHERE folioTambor = :folioTambor AND clasificacion = :clasificacion";
        $dats = $con->prepare($sql);
        $dats->bindParam(':folioTambor', $folioTambor);
        $dats->bindParam(':clasificacion', $clasificacion);        
        $dats->execute();
        if ($dats == false) {
            throw new Exception($con->errorInfo());
        }

        $sqlUpTotalesExp = "UPDATE $experimental SET numeroDeTambores = :numeroDeTambores, kilosTotales = :kilosTotales WHERE idLoteExperimental = :idLoteExperimental";
        $datosTotalesExp = $con->prepare($sqlUpTotalesExp);
        $datosTotalesExp->bindParam(':numeroDeTambores', $numeroDeTamboresExp);
        $datosTotalesExp->bindParam(':kilosTotales', $kilosTotalesExp);
        $datosTotalesExp->bindParam(':idLoteExperimental', $idLoteExperimental);
        $datosTotalesExp->execute();
        if ($datosTotalesExp == false) {
            throw new Exception($con->errorInfo());
        }

        if($clasificacion == "0"){
            $sqlUp = "UPDATE $almacen SET estado = '0' WHERE idAlmacen = :folioTambor";
            $datos = $con->prepare($sqlUp);
            $datos->bindParam(':folioTambor', $folioTambor);
            $datos->execute();
            if ($datos == false) {
                throw new Exception($con->errorInfo());
            }
        }else{
            $sqlUp = "UPDATE almacensobrantes SET estado = '0' WHERE consecutivo = :folioTambor AND sobrante = :clasificacion AND tipoDeMiel = :miel";
            $datos = $con->prepare($sqlUp);
            $datos->bindParam(':folioTambor', $folioTambor);
            $datos->bindParam(':clasificacion', $clasificacion);
            $datos->bindParam(':miel', $miel);            
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