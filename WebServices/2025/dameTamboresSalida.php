<?php

header('Access-Control-Allow-Origin: *');
include_once '../DAOConeccion/conexionWebServices.php';
$pdo = new conePDO();
// $con = $pdo->conectar('apicultores2019');
// $con = $pdo->conectar('apicultores2020');
// $con = $pdo->conectar('mielorganica2020');
// $con = $pdo->conectar('apicultores2021');
// $con = $pdo->conectar('mielorganica2021');
// $con = $pdo->conectar('apicultores2022');
// $con = $pdo->conectar('apicultores2023');
$con = $pdo->conectar('apicultores2024');
// $con = $pdo->conectar('mielorganica2022');
// $con = $pdo->conectar('mielorganica2023');

$con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
try {
    $resultado = array();
    $sqlImpresion = "SELECT * FROM impresion WHERE estado = 3";
    $dato = $con->prepare($sqlImpresion);
    $dato->execute();
    if ($dato == FALSE) {
        throw new Exception($con->errorInfo());
    } else {
        foreach ($dato->fetchAll(PDO::FETCH_ASSOC) as $impresion) {
            switch ($impresion['idTipoDeMiel']) {
                case '1':
                    $entradaysalida = 'entradaysalida';

                    break;
                case '2':
                    $entradaysalida = 'entradaysalida_organico';
                    break;
                case '5':
                    $entradaysalida = 'entradaysalida_mantequilla';
                    break;
                case '6':
                    $entradaysalida = 'entradaysalida_altiplano';
                    break;
                case '7':
                    $entradaysalida = 'entradaysalida_naranjo';
                    break;
                case '8':
                    $entradaysalida = 'entradaysalida_aguacate';
                    break;
                case '9':
                    $entradaysalida = 'entradaysalida_mezquite';
                    break;
            }

            $sql = "SELECT ldp.lote, eys.fechaImpresion
            FROM listadepesos ldp
            LEFT JOIN $entradaysalida eys ON ldp.lote = eys.lote
            WHERE idTamborPeso = :idTamborPeso";
            $datos = $con->prepare($sql);
            $datos->bindParam(':idTamborPeso', $impresion['idAlmacen']);
            $datos->execute();

            if ($datos == FALSE) {
                throw new Exception($con->errorInfo());
            }
            $datosEncabezado = $datos->fetch(PDO::FETCH_ASSOC);

            $sqlTambor = $con->prepare("SELECT tldp.bruto, tldp.tara, tldp.neto, f.floracion, tldp.color, tldp.humedad
            FROM tamboreslistapesos tldp
            LEFT JOIN floraciones f ON tldp.idFloracion = f.idFloracion
            WHERE tldp.idTamborPeso = :idTamborPeso");
            $sqlTambor->bindParam(':idTamborPeso', $impresion['idAlmacen']);
            $sqlTambor->execute();
            if ($sqlTambor == FALSE) {
                throw new Exception($con->errorInfo());
            }
            foreach ($sqlTambor->fetchAll(PDO::FETCH_ASSOC) as $tambo) {
                $tambo['idImpresion'] = $impresion['idImpresion'];
                $tambo['lote'] = $datosEncabezado['lote'];
                $tambo['fechaImpresion'] = $datosEncabezado['fechaImpresion'];
                array_push($resultado, $tambo);
            }
        }

        $sqlTamboresSalida = $con->prepare("SELECT * FROM impresion WHERE estado = 4");
        $sqlTamboresSalida->execute();
        if ($sqlTamboresSalida == FALSE) {
            throw new Exception($con->errorInfo());
        } else {
            $tambores = $sqlTamboresSalida->fetchAll(PDO::FETCH_ASSOC);
        }

        foreach ($tambores as $tambor) {
            switch ($tambor['idTipoDeMiel']) {
                case '1':
                    $entradaysalida = 'entradaysalida';

                    break;
                case '2':
                    $entradaysalida = 'entradaysalida_organico';
                    break;
                case '5':
                    $entradaysalida = 'entradaysalida_mantequilla';
                    break;
            }

            $sqlTambor = $con->prepare("SELECT tldp.bruto, tldp.tara, tldp.neto, f.floracion, tldp.color, tldp.humedad, ldp.lote, eys.fechaImpresion, ldp.filtro
            FROM tamboreslistapesos tldp
            LEFT JOIN floraciones f ON tldp.idFloracion = f.idFloracion
            LEFT JOIN listadepesos ldp ON tldp.idTamborPeso = ldp.idTamborPeso
            LEFT JOIN $entradaysalida eys ON ldp.lote = eys.lote
            WHERE tldp.idPesoTambo = :idPesoTambo");
            $sqlTambor->bindParam(':idPesoTambo', $tambor['idAlmacen']);
            $sqlTambor->execute();
            if ($sqlTambor == FALSE) {
                throw new Exception($con->errorInfo());
            }

            $datosTambor = $sqlTambor->fetch(PDO::FETCH_ASSOC);
            if ($datosTambor) { // Solo mandar los que si trajo registros la consulta
                $datosTambor['idImpresion'] = $tambor['idImpresion'];
                array_push($resultado, $datosTambor);
            }
        }
    }
    echo json_encode($resultado);
} catch (Exception $e) {
    echo json_encode([]);
}
