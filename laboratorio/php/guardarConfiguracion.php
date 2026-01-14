<?php

include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO; $con = $pdo->conectar();
$json = file_get_contents("php://input");

try {
    if(!$json || !isset($_GET["idOpcion"])) {
        throw new Exception('No se recibieron parámetros');
    } else {
        $datos = json_decode($json);
        $info = $datos->valor;
        $tipoDeMiel = $datos->tipoDeMiel;
        $idOpcionConf = $_GET["idOpcion"];
    }

    switch($tipoDeMiel){
        case '1':
            $configuracionlaboratorio_tabla = 'configuracionlaboratorio';
        break;
        case '2':
            $configuracionlaboratorio_tabla = 'configuracionlaboratorio_organico';
        break;
        default:
            throw new Exception('Opción para tipo de miel no es válido');
        break;
    }

    foreach ($info as $conf) {
        $idRango = "";
        $sqlObtenerIdRango = "SELECT * FROM rangos WHERE simbolo = :simbolo";
        $datosIdRango = $con->prepare($sqlObtenerIdRango);
        $datosIdRango->bindParam(':simbolo', $conf->simbolo);
        $datosIdRango->execute();
        if ($datosIdRango == FALSE) {
            throw new Exception($con->errorInfo());
        }
        while ($rsId = $datosIdRango->fetch()) {
            $idRango = $rsId["idRangos"];
        }
        if ($conf->idConfiguracionLaboratorio == 0) {
            $sqlConfiguracion1 = "INSERT INTO $configuracionlaboratorio_tabla (idOpcionLab, rango1, rango2, signo, descripcion) VALUES ('" . $idOpcionConf . "', :rango1, :rango2, '" . $idRango . "', :descripcion)";
            $datosConfLab1 = $con->prepare($sqlConfiguracion1);
            $datosConfLab1->bindParam(':rango1', $conf->rango1);
            $datosConfLab1->bindParam(':rango2', $conf->rango2);
            $datosConfLab1->bindParam(':descripcion', $conf->descripcion);
            $datosConfLab1->execute();
            if ($datosConfLab1 == FALSE) {
                throw new Exception($con->errorInfo());
            }
        } else {
            $sqlConfiguracion = "UPDATE $configuracionlaboratorio_tabla set idOpcionLab ='" . $idOpcionConf . "', rango1 = :rango1, "
                    . "rango2 = :rango2, signo = '" . $idRango . "', descripcion= :descripcion WHERE idconfiguracionLaboratorio = :idConfiguracionLaboratorio";
            $datosConfLab = $con->prepare($sqlConfiguracion);
            $datosConfLab->bindParam(':rango1', $conf->rango1);
            $datosConfLab->bindParam(':rango2', $conf->rango2);
            $datosConfLab->bindParam(':descripcion', $conf->descripcion);
            $datosConfLab->bindParam(':idConfiguracionLaboratorio', $conf->idConfiguracionLaboratorio);
            $datosConfLab->execute();
            if ($datosConfLab == FALSE) {
                throw new Exception($con->errorInfo());
            } 
        }
    }
    echo json_encode(['error'=>false, 'message'=>'Se ha guardado la configuración']);
} catch (Exception $e) {
    echo json_encode(['error'=>true, 'message'=>$e->getMessage() . '. Line: ' . $e->getline()]);
}
