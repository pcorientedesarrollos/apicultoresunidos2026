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
                $tabla = 'enviomuestras';
                break;
            case '2':
                $tabla = 'enviomuestras_organico';
                break;
        }
    }

    $con->beginTransaction();

    if (isset($datosGuardar->idEnvio)) {
        $queryInserta = $con->prepare("UPDATE $tabla SET marcaLaboratorio = :marcaLaboratorio, loteInterno = :loteInterno, marcaCliente = :marcaCliente, guia = :guia, mensajeria = :mensajeria, laboratorioExterno = :laboratorioExterno, cliente = :cliente, fechaEnvio = :fechaEnvio, lugarEnvio = :lugarEnvio, observaciones = :observaciones, sulfa = :sulfa, estrepto = :estrepto, tetra = :tetra, cloran = :cloran, nitro = :nitro, coumap = :coumap, enrofla = :enrofla, c13 = :c13, nmr = :nmr, hmf = :hmf, glucosa = :glucosa,fg = :fg, humedad = :humedad, glifosato = :glifosato, remol = :remol, ogms = :ogms, pas = :pas, resultados = :resultados, numContrato = :numContrato, contrato = :contrato WHERE idEnvio = :idEnvio");
        $queryInserta->bindParam(':idEnvio', $datosGuardar->idEnvio);
    } else {
        $queryInserta = $con->prepare("INSERT INTO $tabla (marcaLaboratorio, loteInterno, marcaCliente, guia, mensajeria, laboratorioExterno, cliente, fechaEnvio, lugarEnvio, observaciones, sulfa, estrepto, tetra, cloran, nitro, coumap, enrofla, c13, nmr, hmf, glucosa, fg, humedad, glifosato, remol, ogms, pas, resultados, numContrato, contrato) VALUES ( :marcaLaboratorio, :loteInterno, :marcaCliente, :guia, :mensajeria, :laboratorioExterno, :cliente, :fechaEnvio, :lugarEnvio, :observaciones, :sulfa, :estrepto, :tetra, :cloran, :nitro, :coumap, :enrofla, :c13, :nmr, :hmf, :glucosa, :fg, :humedad, :glifosato, :remol, :ogms, :pas, :resultados, :numContrato, :contrato)");
    }
    $queryInserta->bindParam(':marcaLaboratorio', $datosGuardar->marcaLaboratorio);
    $queryInserta->bindParam(':loteInterno', $datosGuardar->loteInterno);
    $queryInserta->bindParam(':marcaCliente', $datosGuardar->marcaCliente);
    $queryInserta->bindParam(':guia', $datosGuardar->guia);
    $queryInserta->bindParam(':mensajeria', $datosGuardar->mensajeria);
    $queryInserta->bindParam(':laboratorioExterno', $datosGuardar->laboratorioExterno);
    $queryInserta->bindParam(':cliente', $datosGuardar->cliente);
    $queryInserta->bindParam(':fechaEnvio', $datosGuardar->fechaEnvio);
    $queryInserta->bindParam(':lugarEnvio', $datosGuardar->lugarEnvio);
    $queryInserta->bindParam(':observaciones', $datosGuardar->observaciones);
    $queryInserta->bindParam(':sulfa', $datosGuardar->sulfa);
    $queryInserta->bindParam(':estrepto', $datosGuardar->estrepto);
    $queryInserta->bindParam(':tetra', $datosGuardar->tetra);
    $queryInserta->bindParam(':cloran', $datosGuardar->cloran);
    $queryInserta->bindParam(':nitro', $datosGuardar->nitro);
    $queryInserta->bindParam(':coumap', $datosGuardar->coumap);
    $queryInserta->bindParam(':enrofla', $datosGuardar->enrofla);
    $queryInserta->bindParam(':c13', $datosGuardar->c13);
    $queryInserta->bindParam(':nmr', $datosGuardar->nmr);
    $queryInserta->bindParam(':hmf', $datosGuardar->hmf);
    $queryInserta->bindParam(':glucosa', $datosGuardar->glucosa);
    $queryInserta->bindParam(':fg', $datosGuardar->fg);
    $queryInserta->bindParam(':humedad', $datosGuardar->humedad);
    $queryInserta->bindParam(':glifosato', $datosGuardar->glifosato);
    $queryInserta->bindParam(':remol', $datosGuardar->remol);
    $queryInserta->bindParam(':ogms', $datosGuardar->ogms);
    $queryInserta->bindParam(':pas', $datosGuardar->pas);
    $queryInserta->bindParam(':resultados', $datosGuardar->resultados);
    $queryInserta->bindParam(':numContrato', $datosGuardar->numContrato);
    $queryInserta->bindParam(':contrato', $datosGuardar->contrato);
    $queryInserta->execute();
    if (!$queryInserta) {
        throw new Exception($con->errorInfo());
    }

    $con->commit();
    echo json_encode(['error' => false, 'message' => 'Se ha guardado un nuevo registro']);
} catch (Exception $e) {
    $con->rollBack();
    echo json_encode(['error' => true, 'message' => $e->getMessage()]);
}
