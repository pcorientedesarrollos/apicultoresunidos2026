<?php

include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$con = $pdo->conectar();

// Guarda los datos del usuario del sistema incluyendo la clave de acceso
$postdata = file_get_contents('php://input');

try {
    if (!$postdata) {
        throw new Exception('No se recibieron datos');
    }else{
        $nuevoPerfil = json_decode($postdata);        
    }

    // VERIFICAR QUE NO EXISTE OTRO PERFIL CON EL MISMO NOMBRE
    $sqlVerificar = $con->prepare("SELECT idPerfil FROM perfiles WHERE perfil = :perfil");
    $sqlVerificar->bindParam(':perfil', $nuevoPerfil->nuevoPerfil);
    $sqlVerificar->execute();
    if ($sqlVerificar == false) {
        throw new Exception($con->errorInfo());
    } else {
        $perfil_ya_existe = $sqlVerificar->fetch(PDO::FETCH_ASSOC);
    }
    if ($perfil_ya_existe == true) {
        throw new Exception('El perfil "' . $nuevoPerfil->nuevoPerfil . '" ya existe.');
    }

    // SI NO EXISTE, REGISTRARLO EN LA BASE
    if($nuevoPerfil->copia == '1'){
        $sqlPermisos = $con->prepare("SELECT per.*, secc.seccion
        FROM permisos per
        LEFT JOIN secciones secc ON per.idSeccion = secc.idSeccion
        WHERE idPerfil = :idPerfil");
        $sqlPermisos->bindParam(':idPerfil', $nuevoPerfil->perfilCopia);       
        $sqlPermisos->execute();
        if ($sqlPermisos == false) {
            throw new Exception($con->errorInfo());
        }
        $resultado = $sqlPermisos->fetchAll(PDO::FETCH_ASSOC);
        
        $sqlInsert = $con->prepare("INSERT INTO perfiles (perfil) VALUES (:perfil)");
        $sqlInsert->bindParam(':perfil', $nuevoPerfil->nuevoPerfil);
        $sqlInsert->execute();
        if ($sqlInsert == false) {
            throw new Exception($con->errorInfo());
        }  
        $idPerfil = $con->lastInsertId();

        foreach ($resultado as $seccion) {
                $sqlInsertPermisos = $con->prepare("INSERT INTO permisos (idSeccion, idPerfil) VALUES (:idSeccion, :idPerfil)");
                $sqlInsertPermisos->bindParam(':idSeccion', $seccion['idSeccion']);
                $sqlInsertPermisos->bindParam(':idPerfil', $idPerfil);
                $sqlInsertPermisos->execute();
                if ($sqlInsertPermisos == false) {
                    throw new Exception($con->errorInfo());
                }
        } 

    }else{
        $sqlInsert = $con->prepare("INSERT INTO perfiles (perfil) VALUES (:perfil)");
        $sqlInsert->bindParam(':perfil', $nuevoPerfil->nuevoPerfil);
        $sqlInsert->execute();
        if ($sqlInsert == false) {
            throw new Exception($con->errorInfo());
        }  
        $idPerfil = $con->lastInsertId();
    }  

    echo json_encode(['error' => false, 'message' => 'Se ha registrado un nuevo perfil', 'id' => $idPerfil]);
} catch (Exception $e) {
    echo json_encode(['error' => true, 'message' => $e->getMessage()]);
}
