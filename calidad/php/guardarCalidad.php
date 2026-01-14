<?php

include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$cn = $pdo->conectar();

$json = file_get_contents("php://input");


try {

    if (!$json) {
        throw new Exception('No se recibieron parámetros');
    } else {
        $datos = json_decode($json);
        $info = $datos->valor;
        $idLoteExperimental = $_GET["idLoteExperimental"];
        $miel = $_GET['miel'];
        $fechaProceso = date("Y-m-d", strtotime($info[0]->fechaProceso));
        $fechaEnvasado = date("Y-m-d", strtotime($info[0]->fechaEnvasado));
    }

    switch ($miel) {
        case '1':
            $calidad = 'calidad';
            $tambores = 'tamboreslotes';
            $almacen = 'almacen';
            $experimental = 'experimental';
            $traspaso = 'almacentraspaso';
            break;
        case '2':
            $calidad = 'calidad_organico';
            $tambores = 'tamboreslotes_organico';
            $almacen = 'almacen_organico';
            $experimental = 'experimental_organico';
            $traspaso = 'almacentraspaso_organico';
            break;
        case '5':
            $calidad = 'calidad_mantequilla';
            $tambores = 'tamboreslotes_mantequilla';
            $almacen = 'almacen_mantequilla';
            $experimental = 'experimental_mantequilla';
            $traspaso = 'almacentraspaso_mantequilla';
            break;
        case '6':
            $calidad = 'calidad_altiplano';
            $tambores = 'tamboreslotes_altiplano';
            $almacen = 'almacen_altiplano';
            $experimental = 'experimental_altiplano';
            $traspaso = 'almacentraspaso_altiplano';
            break;
        case '7':
            $calidad = 'calidad_naranjo';
            $tambores = 'tamboreslotes_naranjo';
            $almacen = 'almacen_naranjo';
            $experimental = 'experimental_naranjo';
            $traspaso = 'almacentraspaso_naranjo';
            break;
        case '8':
            $calidad = 'calidad_aguacate';
            $tambores = 'tamboreslotes_aguacate';
            $almacen = 'almacen_aguacate';
            $experimental = 'experimental_aguacate';
            $traspaso = 'almacentraspaso_aguacate';
            break;
        case '9':
            $calidad = 'calidad_mezquite';
            $tambores = 'tamboreslotes_mezquite';
            $almacen = 'almacen_mezquite';
            $experimental = 'experimental_mezquite';
            $traspaso = 'almacentraspaso_mezquite';
            break;
    }

    $cn->beginTransaction();

    $sqlExpe = $cn->prepare("INSERT INTO $calidad (idLoteExperimental, fechaProceso, fechaEnvasado, loteCliente, marcaFinalCliente, observaciones, muestraInterna,  kilosTotales, numeroDeTambores)
        VALUES (:idLoteExperimental, :fechaProceso, :fechaEnvasado, :loteCliente, :marcaFinalCliente, :observaciones, 0, :kilosTotales, :numeroDeTambores)");
    $sqlExpe->bindParam(':idLoteExperimental', $idLoteExperimental);
    $sqlExpe->bindParam(':fechaProceso', $fechaProceso);
    $sqlExpe->bindParam(':fechaEnvasado', $fechaEnvasado);
    $sqlExpe->bindParam(':loteCliente', $info[0]->loteCliente);
    $sqlExpe->bindParam(':marcaFinalCliente', $info[0]->marcaFinalCliente);
    $sqlExpe->bindParam(':observaciones', $info[0]->observaciones);
    $sqlExpe->bindParam(':kilosTotales', $info[1]->kilosTotales);
    $sqlExpe->bindParam(':numeroDeTambores', $info[1]->numeroDeTambores);
    $sqlExpe->execute();

    if ($sqlExpe == false) {
        throw new Exception($cn->errorInfo());
    }
    $idLoteInterno = $cn->lastInsertId();

    foreach ($info[2] as $arregloTambores) {

        if ($arregloTambores->tipo == '0') {
            $sqlTam = $cn->prepare("INSERT INTO $tambores (folioTambor, idLoteInterno, tipo, clasificacion) VALUES (:folioTambor, :idLoteInterno, :tipo, '0')");
            $sqlTam->bindParam(':folioTambor', $arregloTambores->folioTambor);
            $sqlTam->bindParam(':tipo', $arregloTambores->tipo);
            $sqlTam->bindParam(':idLoteInterno', $idLoteInterno);
            $sqlTam->execute();
            if ($sqlTam == false) {
                throw new Exception($cn->errorInfo());
            }

            $sqlActualizaEstado = $cn->prepare("UPDATE $almacen SET estado = '2' WHERE idAlmacen = :idAlmacen");
            $sqlActualizaEstado->bindParam(':idAlmacen', $arregloTambores->folioTambor);
            $sqlActualizaEstado->execute();

            if ($sqlActualizaEstado == false) {
                throw new Exception($cn->errorInfo());
            }
        } else if ($arregloTambores->tipo == '1') {
            $sqlTam = $cn->prepare("INSERT INTO $tambores (folioTambor, idLoteInterno, tipo, clasificacion) VALUES (:folioTambor, :idLoteInterno, :tipo, :clasificacion)");
            $sqlTam->bindParam(':folioTambor', $arregloTambores->folioTambor);
            $sqlTam->bindParam(':tipo', $arregloTambores->tipo);
            $sqlTam->bindParam(':clasificacion', $arregloTambores->clasificacion);
            $sqlTam->bindParam(':idLoteInterno', $idLoteInterno);
            $sqlTam->execute();
            if ($sqlTam == false) {
                throw new Exception($cn->errorInfo());
            }

            $sqlActualizaEstado1 = $cn->prepare("UPDATE almacensobrantes set estado = '2' WHERE consecutivo = :folioTambor AND sobrante = :clasificacion AND tipoDeMiel = :tipoDeMiel");
            $sqlActualizaEstado1->bindParam(':folioTambor', $arregloTambores->folioTambor);
            $sqlActualizaEstado1->bindParam(':clasificacion', $arregloTambores->clasificacion);
            $sqlActualizaEstado1->bindParam(':tipoDeMiel', $miel);
            $sqlActualizaEstado1->execute();

            if ($sqlActualizaEstado1 == false) {
                throw new Exception($cn->errorInfo());
            }
        } else if ($arregloTambores->tipo == '2') {
            $sqlTam = $cn->prepare("INSERT INTO $tambores (folioTambor, idLoteInterno, tipo, clasificacion) VALUES (:folioTambor, :idLoteInterno, :tipo, '0')");
            $sqlTam->bindParam(':folioTambor', $arregloTambores->folioTambor);
            $sqlTam->bindParam(':tipo', $arregloTambores->tipo);
            $sqlTam->bindParam(':idLoteInterno', $idLoteInterno);
            $sqlTam->execute();
            if ($sqlTam == false) {
                throw new Exception($cn->errorInfo());
            }

            $sqlActualizaEstado = $cn->prepare("UPDATE $traspaso SET estado = '2' WHERE idAlmacen = :idAlmacen");
            $sqlActualizaEstado->bindParam(':idAlmacen', $arregloTambores->folioTambor);
            $sqlActualizaEstado->execute();

            if ($sqlActualizaEstado == false) {
                throw new Exception($cn->errorInfo());
            }
        }
    }

    $sqlUp = $cn->prepare("UPDATE $experimental SET loteInterno = '1' WHERE idLoteExperimental = '$idLoteExperimental'");
    $sqlUp->execute();

    if ($sqlUp == false) {
        throw new Exception($cn->errorInfo());
    }

    $cn->commit();
    echo json_encode(['error' => false, 'message' => 'Consulta realizada']);
} catch (Exception $e) {
    $cn->rollBack();
    echo json_encode(['error' => true, 'message' => $e->getMessage() . ' Linea: ' . $e->getLine()]);
}
