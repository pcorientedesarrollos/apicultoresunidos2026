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
        $idLoteExperimental = $_GET["idLoteExperimental"];
        $numeroDeTambores = $info[0]->numeroDeTambores + $info[2]->numeroDeTambores;
        $kilosTotales = $info[0]->kilosTotales + $info[2]->kilosTotales;
    }

    $cn->beginTransaction();

    switch ($_GET['miel']) {
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

    $sql = "UPDATE $experimental SET numeroDeTambores = :numeroDeTambores, kilosTotales = :kilosTotales WHERE idLoteExperimental = :idLoteExperimental";
    $data = $cn->prepare($sql);
    $data->bindParam(':numeroDeTambores', $numeroDeTambores);
    $data->bindParam(':kilosTotales', $kilosTotales);
    $data->bindParam(':idLoteExperimental', $idLoteExperimental);
    $data->execute();
    if ($data == false) {
        throw new Exception('error');
    }

    foreach ($info[1] as $arregloTambores) {

        if ($arregloTambores->tipo === "0") {
            $sqlTambor = "INSERT INTO $tambores (folioTambor, idLoteExperimental, tipo, clasificacion) 
            VALUES ( :idAlmacen, '$idLoteExperimental', :tipo, 0)";
            $dato = $cn->prepare($sqlTambor);
            $dato->bindParam(':idAlmacen', $arregloTambores->idAlmacen);
            $dato->bindParam(':tipo', $arregloTambores->tipo);
            $dato->execute();
            if ($dato == false) {
                throw new Exception('error');
            }

            $sqlActualizaEstado = $cn->prepare("UPDATE $almacen set estado = '1' WHERE idAlmacen = :idAlmacen");
            $sqlActualizaEstado->bindParam(':idAlmacen', $arregloTambores->idAlmacen);
            $sqlActualizaEstado->execute();

            if ($sqlActualizaEstado == false) {
                throw new Exception('error');
            }
        } else if ($arregloTambores->tipo === "1") {

            $sqlTambor = "INSERT INTO $tambores (folioTambor, idLoteExperimental, tipo, clasificacion) 
            VALUES ( :idAlmacen, '$idLoteExperimental', :tipo, :clasificacion )";
            $dato = $cn->prepare($sqlTambor);
            $dato->bindParam(':idAlmacen', $arregloTambores->idAlmacen);
            $dato->bindParam(':tipo', $arregloTambores->tipo);
            $dato->bindParam(':clasificacion', $arregloTambores->clasificacion);
            $dato->execute();
            if ($dato == false) {
                throw new Exception('error');
            }

            $sqlActualizaEstado = $cn->prepare("UPDATE almacensobrantes set estado = '1' WHERE consecutivo = :idAlmacen AND sobrante = :clasificacion AND tipoDeMiel = :tipoDeMiel");
            $sqlActualizaEstado->bindParam(':idAlmacen', $arregloTambores->idAlmacen);
            $sqlActualizaEstado->bindParam(':clasificacion', $arregloTambores->clasificacion);
            $sqlActualizaEstado->bindParam(':tipoDeMiel', $_GET['miel']);
            $sqlActualizaEstado->execute();

            if ($sqlActualizaEstado == false) {
                throw new Exception('error');
            }
        }
    }
    $cn->commit();
    echo json_encode(['error' => false, 'message' => 'Consulta realizada']);
} catch (Exception $e) {
    $cn->rollBack();
    echo json_encode(['error' => true, 'message' => $e->getMessage() . ' Linea: ' . $e->getLine()]);
}
