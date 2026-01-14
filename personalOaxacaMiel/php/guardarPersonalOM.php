<?php

include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$con = $pdo->conectar();
$miInfo = file_get_contents("php://input");

try {

    if (isset($_GET['alta_rapida'])) {
        if (!$miInfo) {
            throw new Exception('No se recibieron los datos');
        }
        $datos = json_decode($miInfo);

        $nombre_separado = explode(' ', $datos->nombre);

        if (isset($nombre_separado[0]) && isset($nombre_separado[1])) {
            $nombres = $nombre_separado[0] . ' ' . $nombre_separado[1];
        } elseif (isset($nombre_separado[0])) {
            $nombres = $nombre_separado[0];
        } else {
            $nombres = '';
        }

        if (isset($nombre_separado[2])) {
            $apellido_paterno = $nombre_separado[2];
        } else {
            $apellido_paterno = '';
        }

        if (isset($nombre_separado[3])) {
            $apellido_materno = $nombre_separado[3];
        } else {
            $apellido_materno = '';
        }

        $sql = "INSERT INTO personaloaxaca (idArea, clave, nombre, nombres, apellido_paterno, apellido_materno,
        idPuesto, estado) VALUES (:idArea, :clave, :nombre, :nombres, :paterno, :materno, :idPuesto, '0')";
        $data = $con->prepare($sql);
        $data->bindParam(':idArea', $datos->idArea);
        $data->bindParam(':clave', $datos->clave);
        $data->bindParam(':nombre', $datos->nombre);
        $data->bindParam(':nombres', $nombres);
        $data->bindParam(':paterno', $apellido_paterno);
        $data->bindParam(':materno', $apellido_materno);
        $data->bindParam(':idPuesto', $datos->idPuesto);
        $data->execute();
        if ($data == false) {
            throw new Exception($con->errorInfo());
        }

        echo json_encode(['error' => false, 'message' => 'Nuevo personal disponible']);
    } else {
        if (!$miInfo) {
            throw new Exception('No se recibieron los datos');
        }
        $datos = json_decode($miInfo);
        $nombre_completo = join(' ', [$datos->nombres, $datos->apellido_paterno, $datos->apellido_materno]);

        $sql = "INSERT INTO personaloaxaca (idArea, clave, nombre, nombres, apellido_paterno, apellido_materno,
        idPuesto, correo, estado) VALUES (:idArea, :clave, :nombre, :nombres, :paterno, :materno, :idPuesto, :correo, '0')";
        $data = $con->prepare($sql);
        $data->bindParam(':idArea', $datos->idArea);
        $data->bindParam(':clave', $datos->clave);
        $data->bindParam(':nombre', $nombre_completo);
        $data->bindParam(':nombres', $datos->nombres);
        $data->bindParam(':paterno', $datos->apellido_paterno);
        $data->bindParam(':materno', $datos->apellido_materno);
        $data->bindParam(':idPuesto', $datos->idPuesto);
        $data->bindParam(':correo', $datos->correo);
        $data->execute();
        if ($data == false) {
            throw new Exception($con->errorInfo());
        }

        echo json_encode(['error' => false, 'message' => 'Nuevo personal disponible']);
    }

} catch (Exception $e) {
    echo json_encode(['error' => true, 'message' => $e->getMessage()]);
}
