<?php
include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$con = $pdo->conectar();

$postdata = file_get_contents('php://input');

try {

    if (!$postdata) {
        throw new Exception('No se recibieron datos');
    } else {
        $datosGuardar = json_decode($postdata);
        switch ($datosGuardar->tipoDeMiel) {
            case '1':
                $tabla = 'lotescontratados';
                break;
            case '2':
                $tabla = 'lotescontratados_organico';
                break;
        }
    }

    $con->beginTransaction();

    if (isset($datosGuardar->idLoteContratado)) {
        $queryInserta = $con->prepare("UPDATE $tabla SET tipoDeCliente = :tipoDeCliente, idCliente = :idCliente, contrato = :contrato, marcaCliente = :marcaCliente, loteInterno = :loteInterno, kilos = :kilos, humedad = :humedad, color = :color, hmf = :hmf, fg = :fg, glucosa = :glucosa, diastasa = :diastasa, invertasa = :invertasa, sulfas = :sulfas, estrepto = :estrepto, tetras = :tetras, cloranphenicol = :cloranphenicol, macrolidas = :macrolidas, nitrofuranos = :nitrofuranos, fluroquinolanas = :fluroquinolanas, pesticidas = :pesticidas, glifosato = :glifosato, ogm = :ogm, pas = :pas WHERE idLoteContratado = :idLoteContratado");
        $queryInserta->bindParam(':idLoteContratado', $datosGuardar->idLoteContratado);

        $queryInserta->bindParam(':tipoDeCliente', $datosGuardar->tipoDeCliente);
        $queryInserta->bindParam(':idCliente', $datosGuardar->idCliente);
        $queryInserta->bindParam(':contrato', $datosGuardar->contrato);
        $queryInserta->bindParam(':marcaCliente', $datosGuardar->marcaCliente);
        $queryInserta->bindParam(':loteInterno', $datosGuardar->loteInterno);
        $queryInserta->bindParam(':kilos', $datosGuardar->kilos);
        $queryInserta->bindParam(':humedad', $datosGuardar->humedad);
        $queryInserta->bindParam(':color', $datosGuardar->color);
        $queryInserta->bindParam(':hmf', $datosGuardar->hmf);
        $queryInserta->bindParam(':fg', $datosGuardar->fg);
        $queryInserta->bindParam(':glucosa', $datosGuardar->glucosa);
        $queryInserta->bindParam(':diastasa', $datosGuardar->diastasa);
        $queryInserta->bindParam(':invertasa', $datosGuardar->invertasa);
        $queryInserta->bindParam(':sulfas', $datosGuardar->sulfas);
        $queryInserta->bindParam(':estrepto', $datosGuardar->estrepto);
        $queryInserta->bindParam(':tetras', $datosGuardar->tetras);
        $queryInserta->bindParam(':cloranphenicol', $datosGuardar->cloranphenicol);
        $queryInserta->bindParam(':macrolidas', $datosGuardar->macrolidas);
        $queryInserta->bindParam(':nitrofuranos', $datosGuardar->nitrofuranos);
        $queryInserta->bindParam(':fluroquinolanas', $datosGuardar->fluroquinolanas);
        $queryInserta->bindParam(':pesticidas', $datosGuardar->pesticidas);
        $queryInserta->bindParam(':glifosato', $datosGuardar->glifosato);
        $queryInserta->bindParam(':ogm', $datosGuardar->ogm);
        $queryInserta->bindParam(':pas', $datosGuardar->pas);
        $queryInserta->execute();
        $con->commit();
        echo json_encode(['error' => false, 'message' => 'Se ha editado el registro']);
    } else {

        for ($i = 0; $i < $datosGuardar->numeroLotes; $i++) {
            $queryInserta = $con->prepare("INSERT INTO $tabla (tipoDeCliente, idCliente, contrato, marcaCliente, loteInterno, kilos, humedad, color, hmf, fg, glucosa, diastasa, invertasa, sulfas, estrepto, tetras, cloranphenicol, macrolidas, nitrofuranos, fluroquinolanas, pesticidas, glifosato, ogm, pas) VALUES (:tipoDeCliente, :idCliente, :contrato, :marcaCliente, :loteInterno, :kilos, :humedad, :color, :hmf, :fg, :glucosa, :diastasa, :invertasa, :sulfas, :estrepto, :tetras, :cloranphenicol, :macrolidas, :nitrofuranos, :fluroquinolanas, :pesticidas, :glifosato, :ogm, :pas)");
            $queryInserta->bindParam(':tipoDeCliente', $datosGuardar->tipoDeCliente);
            $queryInserta->bindParam(':idCliente', $datosGuardar->idCliente);
            $queryInserta->bindParam(':contrato', $datosGuardar->contrato);
            $queryInserta->bindParam(':marcaCliente', $datosGuardar->marcaCliente);
            $queryInserta->bindParam(':loteInterno', $datosGuardar->loteInterno);
            $queryInserta->bindParam(':kilos', $datosGuardar->kilos);
            $queryInserta->bindParam(':humedad', $datosGuardar->humedad);
            $queryInserta->bindParam(':color', $datosGuardar->color);
            $queryInserta->bindParam(':hmf', $datosGuardar->hmf);
            $queryInserta->bindParam(':fg', $datosGuardar->fg);
            $queryInserta->bindParam(':glucosa', $datosGuardar->glucosa);
            $queryInserta->bindParam(':diastasa', $datosGuardar->diastasa);
            $queryInserta->bindParam(':invertasa', $datosGuardar->invertasa);
            $queryInserta->bindParam(':sulfas', $datosGuardar->sulfas);
            $queryInserta->bindParam(':estrepto', $datosGuardar->estrepto);
            $queryInserta->bindParam(':tetras', $datosGuardar->tetras);
            $queryInserta->bindParam(':cloranphenicol', $datosGuardar->cloranphenicol);
            $queryInserta->bindParam(':macrolidas', $datosGuardar->macrolidas);
            $queryInserta->bindParam(':nitrofuranos', $datosGuardar->nitrofuranos);
            $queryInserta->bindParam(':fluroquinolanas', $datosGuardar->fluroquinolanas);
            $queryInserta->bindParam(':pesticidas', $datosGuardar->pesticidas);
            $queryInserta->bindParam(':glifosato', $datosGuardar->glifosato);
            $queryInserta->bindParam(':ogm', $datosGuardar->ogm);
            $queryInserta->bindParam(':pas', $datosGuardar->pas);
            $queryInserta->execute();
            if ($queryInserta->rowCount() != 1) {
                throw new Exception('No se ha podido crear uno o más campos en la tabla');
            }
        }
        $con->commit();
        echo json_encode(['error' => false, 'message' => 'Se han creado los registros']);

        // $queryInserta = $con->prepare("INSERT INTO $tabla (tipoDeCliente, idCliente, contrato, marcaCliente, loteInterno, kilos, humedad, color, hmf, fg, glucosa, diastasa, invertasa, sulfas, estrepto, tetras, cloranphenicol, macrolidas, nitrofuranos, fluroquinolanas, pesticidas, glifosato, ogm, pas) VALUES (:tipoDeCliente, :idCliente, :contrato, :marcaCliente, :loteInterno, :kilos, :humedad, :color, :hmf, :fg, :glucosa, :diastasa, :invertasa, :sulfas, :estrepto, :tetras, :cloranphenicol, :macrolidas, :nitrofuranos, :fluroquinolanas, :pesticidas, :glifosato, :ogm, :pas)");
    }
    // $queryInserta->bindParam(':tipoDeCliente', $datosGuardar->tipoDeCliente);
    // $queryInserta->bindParam(':idCliente', $datosGuardar->idCliente);
    // $queryInserta->bindParam(':contrato', $datosGuardar->contrato);
    // $queryInserta->bindParam(':marcaCliente', $datosGuardar->marcaCliente);
    // $queryInserta->bindParam(':loteInterno', $datosGuardar->loteInterno);
    // $queryInserta->bindParam(':kilos', $datosGuardar->kilos);
    // $queryInserta->bindParam(':humedad', $datosGuardar->humedad);
    // $queryInserta->bindParam(':color', $datosGuardar->color);
    // $queryInserta->bindParam(':hmf', $datosGuardar->hmf);
    // $queryInserta->bindParam(':fg', $datosGuardar->fg);
    // $queryInserta->bindParam(':glucosa', $datosGuardar->glucosa);
    // $queryInserta->bindParam(':diastasa', $datosGuardar->diastasa);
    // $queryInserta->bindParam(':invertasa', $datosGuardar->invertasa);
    // $queryInserta->bindParam(':sulfas', $datosGuardar->sulfas);
    // $queryInserta->bindParam(':estrepto', $datosGuardar->estrepto);
    // $queryInserta->bindParam(':tetras', $datosGuardar->tetras);
    // $queryInserta->bindParam(':cloranphenicol', $datosGuardar->cloranphenicol);
    // $queryInserta->bindParam(':macrolidas', $datosGuardar->macrolidas);
    // $queryInserta->bindParam(':nitrofuranos', $datosGuardar->nitrofuranos);
    // $queryInserta->bindParam(':fluroquinolanas', $datosGuardar->fluroquinolanas);
    // $queryInserta->bindParam(':pesticidas', $datosGuardar->pesticidas);
    // $queryInserta->bindParam(':glifosato', $datosGuardar->glifosato);
    // $queryInserta->bindParam(':ogm', $datosGuardar->ogm);
    // $queryInserta->bindParam(':pas', $datosGuardar->pas);
    // $queryInserta->execute();
    // if (!$queryInserta) {
    //     throw new Exception($con->errorInfo());
    // }

    // $con->commit();
    // echo json_encode(['error' => false, 'message' => 'Se ha guardado un nuevo registro']);
} catch (Exception $e) {
    $con->rollBack();
    echo json_encode(['error' => true, 'message' => $e->getMessage()]);
}
