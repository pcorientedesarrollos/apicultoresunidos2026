<?php

include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$con = $pdo->conectar();

try {

    // Va a obtener los registros de los que ya existan movimientos en el almacen


    // Por Conceptos y subcuentas
    $seleccionaConcepto = $con->prepare("SELECT concepto 
    FROM derivadosalmacendetalle 
    WHERE idConcepto IS NOT NULL GROUP BY concepto
    UNION
    SELECT concepto 
    FROM derivadosalmacendetalle_salidas 
    WHERE idConcepto IS NOT NULL GROUP BY concepto
    UNION
    SELECT subcuenta as concepto 
    FROM derivadosalmacendetalle 
    WHERE idSubcuenta IS NOT NULL AND idConcepto IS NULL GROUP BY subcuenta");
    $seleccionaConcepto->execute();
    if ($seleccionaConcepto == false) {
        throw new Exception($con->errorInfo());
    }
    $lista_de_conceptos = $seleccionaConcepto->fetchAll(PDO::FETCH_ASSOC);

    // Tenemos que pasar todos los registros a un arreglo y verificar si ya existen en la tabla de saldos iniciales apicola
    $arreglo_registros = array();
    $arreglo_nombres_registros = array(); // Para guardar los nombres y verificar después si tienen solo saldo inicial
    foreach ($lista_de_conceptos as $concepto) {
        $querySaldos = $con->prepare("SELECT id, nombre, existenciaPasada, importePasado FROM saldoinicial_derivados WHERE nombre = :nombre");
        $querySaldos->bindParam(':nombre', $concepto['concepto']);
        $querySaldos->execute();

        if (!$querySaldos) {
            throw new Exception($con->errorInfo());
        }

        $resultadoRegistro = $querySaldos->fetch(PDO::FETCH_ASSOC);
        if ($resultadoRegistro) {
            array_push($arreglo_registros, $resultadoRegistro);
            array_push($arreglo_nombres_registros, $resultadoRegistro['nombre']);
        } else {
            // Quiere decir que es nuevo y que no está en la tabla de saldos iniciales
            $nuevo_registro = new stdClass();

            $nuevo_registro->nombre = $concepto['concepto'];
            $nuevo_registro->existenciaPasada = '0';
            $nuevo_registro->importePasado = '0';
            array_push($arreglo_registros, $nuevo_registro);
            array_push($arreglo_nombres_registros, $nuevo_registro->nombre);
        }
    }

    // Actualización: traer los saldos iniciales aunque no tengan movimientos
    // Consultar a la tabla de saldos iniciales y verificar si existen o no en la tabla de arrelo_registros

    if (count($arreglo_nombres_registros) == 0) {
        $consulta = "SELECT id, nombre, existenciaPasada, importePasado FROM saldoinicial_derivados";
    } else {
        $lista_nombres = "";
        foreach ($arreglo_nombres_registros as $index => $nombre) {
            $lista_nombres .= "'$nombre'";
            if (($index + 1) < count($arreglo_nombres_registros)) {
                $lista_nombres .= ", ";
            }
        }
        $consulta = "SELECT id, nombre, existenciaPasada, importePasado FROM saldoinicial_derivados WHERE nombre NOT IN (" . $lista_nombres . ")";
    }
    $querySaldos = $con->prepare($consulta);
    $querySaldos->execute();


    if (!$querySaldos) {
        throw new Exception($con->errorInfo());
    }

    $resultadoRegistros = $querySaldos->fetchAll(PDO::FETCH_ASSOC);
    if ($resultadoRegistros) {

        foreach ($resultadoRegistros as $solo_saldos) {
            array_push($arreglo_registros, $solo_saldos);
        }
    }



    echo json_encode(['error' => false, 'data' => $arreglo_registros]);
} catch (Exception $e) {
    echo json_encode(['error' => true, 'message' => $e->getMessage()]);
}
