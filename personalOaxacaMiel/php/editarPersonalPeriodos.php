<?php
include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$con = $pdo->conectar();
$con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$json = file_get_contents("php://input");

try {
    $con->beginTransaction();
    if (!$json) {
        throw new Exception('No se recibieron los parámetros');
    } else {
        $datos = json_decode($json);
        $info = $datos->valor;
        $nombre_completo = join(' ', [$info[0]->nombres, $info[0]->apellido_paterno, $info[0]->apellido_materno]);
        $nombres = $info[0]->nombres;
        $paterno = $info[0]->apellido_paterno;
        $materno = $info[0]->apellido_materno;
        $idArea = $info[0]->idArea;
        $clave = $info[0]->clave;
        $correo = $info[0]->correo;
        $idPuesto = $info[0]->idPuesto;
        $idPersonalOM = $info[0]->idPersonalOM;
    }

    $sql = "UPDATE personaloaxaca SET idArea = :idArea, clave = :clave, nombre = :nombre, nombres = :nombres,
    apellido_paterno = :paterno, apellido_materno = :materno, idPuesto = :idPuesto, correo = :correo
    WHERE idPersonalOM = :idPersonalOM";
    $insertarDetalle = $con->prepare($sql);
    $insertarDetalle->bindParam(':idArea', $idArea);
    $insertarDetalle->bindParam(':clave', $clave);
    $insertarDetalle->bindParam(':nombre', $nombre_completo);
    $insertarDetalle->bindParam(':nombres', $nombres);
    $insertarDetalle->bindParam(':paterno', $paterno);
    $insertarDetalle->bindParam(':materno', $materno);
    $insertarDetalle->bindParam(':idPuesto', $idPuesto);
    $insertarDetalle->bindParam(':correo', $correo);
    $insertarDetalle->bindParam(':idPersonalOM', $idPersonalOM);
    $insertarDetalle->execute();

    if ($insertarDetalle == false) {
        throw new Exception($con->errorInfo());
    } else {
        foreach ($info[1] as $periodos) {
            if ($periodos->idPeriodo > 0) {
                $sqlPeriodos = "UPDATE periodos_personal SET fechaUno = :fechaUno, fechaDos = :fechaDos WHERE idPeriodo = :idPeriodo";
                $datosPlacas = $con->prepare($sqlPeriodos);
                $datosPlacas->bindParam(':fechaUno', $periodos->fechaUno);
                $datosPlacas->bindParam(':fechaDos', $periodos->fechaDos);
                $datosPlacas->bindParam(':idPeriodo', $periodos->idPeriodo);
                $datosPlacas->execute();
                if ($datosPlacas == false) {
                    throw new Exception($con->errorInfo());
                }
            } else {
                $sqlPeriodos1 = "INSERT INTO periodos_personal (fechaUno, fechaDos, idPersonal) VALUES ( :fechaUno, :fechaDos, :idPersonal)";
                $datosPlacas1 = $con->prepare($sqlPeriodos1);
                $datosPlacas1->bindParam(':fechaUno', $periodos->fechaUno);
                $datosPlacas1->bindParam(':fechaDos', $periodos->fechaDos);
                $datosPlacas1->bindParam(':idPersonal', $idPersonalOM);
                $datosPlacas1->execute();
                if ($datosPlacas1 == false) {
                    throw new Exception($con->errorInfo());
                }
            }
        }
    }

    $con->commit();
    echo json_encode(['error' => false, 'message' => 'Se han guardado los datos', 'swal' => 'success']);
} catch (Exception $e) {
    $con->rollBack();
    echo json_encode(['error' => true, 'message' => $e->getMessage() . '. Line ' . $e->getLine(), 'swal' => 'error']);
    exit();
}
