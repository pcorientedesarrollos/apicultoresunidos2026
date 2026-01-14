<?php

include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$cn = $pdo->conectar();

$json = file_get_contents("php://input");

try {

    if (!$json) {
        throw new Exception('No se recibieron parámetros');
    } else {
        $json = file_get_contents("php://input");
        $datos = json_decode($json);
        $info = $datos->valor;
        $fecha = date("Y-m-d");
        if ($info[0]->humedad == 'undefined') {
            $info[0]->humedad = '0';
        }
    }

    $cn->beginTransaction();

    switch($_GET['miel']){
        case '1':
            $experimental = 'experimental';
            $tambores = 'tamboresexperimentales';
            $almacen = 'almacen';
        break;
        case '2':
            $experimental = 'experimental_organico';
            $tambores = 'tamboresexperimentales_organico';
            $almacen = 'almacen_organico';
        break;
    }

    $sql = "INSERT INTO $experimental (fecha, humedad, numeroDeTambores, kilosTotales, resultadoLaboratorio, loteInterno) 
    VALUES ('$fecha', :humedad, :numeroDeTambores, :kilosTotales, '0', '0')";
    $data = $cn->prepare($sql);
    $data->bindParam(':humedad', $info[0]->humedad);
    $data->bindParam(':numeroDeTambores', $info[0]->numeroDeTambores);
    $data->bindParam(':kilosTotales', $info[0]->kilosTotales);
    $data->execute();
    if ($data == false) {
        throw new Exception($cn->errorInfo());
    }
    $idLoteExperimental = $cn->lastInsertId();
    
    foreach ($info[1] as $arregloTambores) {

        if($arregloTambores->tipo == '0'){

            $sqlTambor= "INSERT INTO $tambores (folioTambor, idLoteExperimental, tipo, clasificacion) 
            VALUES ( :idAlmacen, '$idLoteExperimental', :tipo, 0)";
            $dato = $cn->prepare($sqlTambor);
            $dato->bindParam(':idAlmacen', $arregloTambores->idAlmacen);
            $dato->bindParam(':tipo', $arregloTambores->tipo);            
            $dato->execute();
            if ($dato == false) {
                throw new Exception($cn->errorInfo());
            }
    
            $sqlActualizaEstado = $cn->prepare("UPDATE $almacen set estado = '1' WHERE idAlmacen = :idAlmacen");
            $sqlActualizaEstado->bindParam(':idAlmacen', $arregloTambores->idAlmacen);
            $sqlActualizaEstado->execute();
    
            if ($sqlActualizaEstado == false) {
                throw new Exception($cn->errorInfo());
            }

        }else if($arregloTambores->tipo == '1'){
            
            $sqlTambor= "INSERT INTO $tambores (folioTambor, idLoteExperimental, tipo, clasificacion) 
            VALUES ( :idAlmacen, '$idLoteExperimental', :tipo, :clasificacion )";
            $dato = $cn->prepare($sqlTambor);
            $dato->bindParam(':idAlmacen', $arregloTambores->idAlmacen);
            $dato->bindParam(':tipo', $arregloTambores->tipo);
            $dato->bindParam(':clasificacion', $arregloTambores->clasificacion);            
            $dato->execute();
            if ($dato == false) {
                throw new Exception($cn->errorInfo());
            }
    
            $sqlActualizaEstado = $cn->prepare("UPDATE almacensobrantes set estado = '1' WHERE consecutivo = :idAlmacen AND sobrante = :clasificacion AND tipoDeMiel = :tipoDeMiel");
            $sqlActualizaEstado->bindParam(':idAlmacen', $arregloTambores->idAlmacen);
            $sqlActualizaEstado->bindParam(':clasificacion', $arregloTambores->clasificacion);
            $sqlActualizaEstado->bindParam(':tipoDeMiel', $_GET['miel']);            
            $sqlActualizaEstado->execute();
    
            if ($sqlActualizaEstado == false) {
                throw new Exception($cn->errorInfo());
            }

        }
    }
    $cn->commit();
    echo json_encode(['error' => false, 'message' => 'Consulta realizada']);

} catch (Exception $e) {
    $cn->rollBack();
    echo json_encode(['error' => true, 'message' => $e->getMessage() . ' Linea: ' . $e->getLine()]);
}