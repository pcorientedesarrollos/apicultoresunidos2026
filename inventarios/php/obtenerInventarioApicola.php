<?php
if (!isset($_GET['informeFinanciero'])) {
    include_once '../../DAOConeccion/conePDO.php';
    include_once '../../controlAdministrativo/php/nombreDePersona.php';
    $pdo = new conePDO();
    $con = $pdo->conectar();
    $con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
}

function obtenerInventarioApicola($idMes, $acumulado, $fechaFinal = false)
{
    global $con;
    $resultado = array();
    $resultado['totalImporteSalida'] = 0;
    $resultado['productos'] = array();

    // Seleccionar los conceptos de productos apícolas

    // Seleccionar solo subcuentascuentas
    // Y seleccinar solo conceptos

// Lista primero los conceptos y despues las cuentas

// CONCEPTOS
    // Agrupar por nombres, porque los conceptos de ingresos y egresos tienen diferente id

    $selectSubconceptos = $con->prepare("SELECT concepto as subconceptoCC FROM almacenapicola WHERE idConcepto IS NOT NULL GROUP BY concepto
    UNION
    SELECT nombre as subconceptoCC FROM saldoinicial_apicola GROUP BY nombre");
    $selectSubconceptos->execute();
    if ($selectSubconceptos == false) {
        throw new Exception($con->errorInfo());
    }

    foreach ($selectSubconceptos->fetchAll(PDO::FETCH_ASSOC) as $subconcepto) {

        $subconcepto['existenciaPasada'] = 0;
        $subconcepto['importePasado'] = 0;

        // Saldo inicial con el nuevo modulo
        $sqlSaldoInicial = $con->prepare("SELECT existenciaPasada, importePasado FROM saldoinicial_apicola WHERE nombre = :nombre");
        $sqlSaldoInicial->bindParam(':nombre', $subconcepto['subconceptoCC']);
        $sqlSaldoInicial->bindColumn('existenciaPasada', $subconcepto['existenciaPasada']);
        $sqlSaldoInicial->bindColumn('importePasado', $subconcepto['importePasado']);
        $sqlSaldoInicial->execute();
        if ($sqlSaldoInicial == false) {
            throw new Exception($con->errorInfo());
        } else {
            $sqlSaldoInicial->fetch(PDO::FETCH_BOUND);
        }

        if (!$subconcepto['existenciaPasada']) {
            $subconcepto['existenciaPasada'] = 0;
        }

        if (!$subconcepto['importePasado']) {
            $subconcepto['importePasado'] = 0;
        }

        $subconcepto['importeAcumulado'] = $subconcepto['importePasado'];
        $subconcepto['acumulado'] = $subconcepto['existenciaPasada'];
        $subconcepto['entradas'] = $subconcepto['existenciaPasada'];
        $subconcepto['salidas'] = 0;
        $subconcepto['importeEntrada'] = 0;
        $subconcepto['importeSalida'] = 0;
        $subconcepto['precioPromedio'] = 0;
        $subconcepto['registros'] = array();

        $subconcepto['importeEntrada'] += $subconcepto['importePasado'];

        $sqlSelect = "SELECT aa.cantidad, aa.descripcion, aa.importe, aa.costoUnitario, aea.tipo, aea.tipoPersona, aea.idProveedor, aea.fecha FROM `almacenapicola` aa
            LEFT JOIN almacenencabezadoapicola aea ON aa.idAlmacenEncabezado = aea.idAlmacen
            WHERE aa.concepto = :subconceptoCC";
        if (!$acumulado) {
            if ($fechaFinal) {
                $sqlSelect .= " AND aea.fecha <= '" . $fechaFinal . "'";
            } else {
                $sqlSelect .= " AND SUBSTR(aea.fecha FROM 6 FOR 2 ) <= " . $idMes;
            }
        }
        $query = $con->prepare($sqlSelect);
        $query->bindParam(':subconceptoCC', $subconcepto['subconceptoCC']);
        $query->execute();

        if ($query == false) {
            throw new Exception($con->errorInfo());
        }

        foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $registro) {

            $registro['nombre'] = retornarNombre($con, $registro['tipoPersona'], $registro['idProveedor']);
            if ($registro['tipo'] == '1') {
                // Entrada
                $registro['entrada'] = floatval($registro['cantidad']);
                $registro['salida'] = 0;
                $registro['importeEntrada'] = $registro['importe'];
                $registro['importeSalida'] = 0;
                $subconcepto['acumulado'] += $registro['entrada'];
                $subconcepto['entradas'] += $registro['entrada'];
                $subconcepto['importeEntrada'] += $registro['importe'];

                // Acumular el importe
                $registro['acumulado'] = $subconcepto['importeAcumulado'] + $registro['importeEntrada'];
                $subconcepto['importeAcumulado'] += $registro['importeEntrada'];

            } else if ($registro['tipo'] == '2') {
                // Salida
                $registro['entrada'] = 0;
                $registro['salida'] = floatval($registro['cantidad']);
                $registro['importeSalida'] = $registro['importe'];
                $registro['importeEntrada'] = 0;
                $subconcepto['acumulado'] -= $registro['salida'];
                $subconcepto['salidas'] += $registro['salida'];
                $subconcepto['importeSalida'] += $registro['importe'];
                $resultado['totalImporteSalida'] += $registro['importe'];

                // Acumular el importe
                $registro['acumulado'] = $subconcepto['importeAcumulado'] - $registro['importeSalida'];
                $subconcepto['importeAcumulado'] -= $registro['importeSalida'];

            }

            $registro['existencia'] = $subconcepto['acumulado'];

            array_push($subconcepto['registros'], $registro);
        }

        // Si tiene acumulado, calcula el precio promedio, si no pone cero
        $subconcepto['precioPromedio'] = $subconcepto['entradas'] > 0 ? ($subconcepto['importeEntrada']) / $subconcepto['entradas'] : 0;
        array_push($resultado['productos'], $subconcepto);

    }


    // Subcuentas (que no tienen conceptos)
    // Agrupar por nombres, porque los conceptos de ingresos y egresos tienen diferente id
    $selectSubconceptos = $con->prepare("SELECT subcuenta as subconceptoCC FROM almacenapicola WHERE idSubcuenta IS NOT NULL GROUP BY subcuenta");
    $selectSubconceptos->execute();
    if ($selectSubconceptos == false) {
        throw new Exception($con->errorInfo());
    }

    foreach ($selectSubconceptos->fetchAll(PDO::FETCH_ASSOC) as $subconcepto) {

        $subconcepto['existenciaPasada'] = 0;
        $subconcepto['importePasado'] = 0;

        // Saldo inicial con el nuevo modulo
        $sqlSaldoInicial = $con->prepare("SELECT existenciaPasada, importePasado FROM saldoinicial_apicola WHERE nombre = :nombre");
        $sqlSaldoInicial->bindParam(':nombre', $subconcepto['subconceptoCC']);
        $sqlSaldoInicial->bindColumn('existenciaPasada', $subconcepto['existenciaPasada']);
        $sqlSaldoInicial->bindColumn('importePasado', $subconcepto['importePasado']);
        $sqlSaldoInicial->execute();
        if ($sqlSaldoInicial == false) {
            throw new Exception($con->errorInfo());
        } else {
            $sqlSaldoInicial->fetch(PDO::FETCH_BOUND);
        }

        if (!$subconcepto['existenciaPasada']) {
            $subconcepto['existenciaPasada'] = 0;
        }

        if (!$subconcepto['importePasado']) {
            $subconcepto['importePasado'] = 0;
        }

        $subconcepto['importeAcumulado'] = $subconcepto['importePasado'];
        $subconcepto['acumulado'] = $subconcepto['existenciaPasada'];
        $subconcepto['entradas'] = $subconcepto['existenciaPasada'];
        $subconcepto['salidas'] = 0;
        $subconcepto['importeSalida'] = 0;
        $subconcepto['importeEntrada'] = 0;
        $subconcepto['precioPromedio'] = 0;
        $subconcepto['registros'] = array();

        $subconcepto['importeEntrada'] += $subconcepto['importePasado'];

        $sqlSelect = "SELECT aa.cantidad, aa.descripcion, aa.importe, aa.costoUnitario, aea.tipo, aea.tipoPersona, aea.idProveedor, aea.fecha FROM `almacenapicola` aa
            LEFT JOIN almacenencabezadoapicola aea ON aa.idAlmacenEncabezado = aea.idAlmacen
            WHERE aa.subcuenta = :subconceptoCC AND aa.idConcepto IS NULL";
        if (!$acumulado) {
            if ($fechaFinal) {
                $sqlSelect .= " AND aea.fecha <= '" . $fechaFinal . "'";
            } else {
                $sqlSelect .= " AND SUBSTR(aea.fecha FROM 6 FOR 2 ) <= " . $idMes;
            }
        }
        $query = $con->prepare($sqlSelect);
        $query->bindParam(':subconceptoCC', $subconcepto['subconceptoCC']);
        $query->execute();

        if ($query == false) {
            throw new Exception($con->errorInfo());
        }

        foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $registro) {
            $registro['nombre'] = retornarNombre($con, $registro['tipoPersona'], $registro['idProveedor']);
            if ($registro['tipo'] == '1') {
                // Entrada
                $registro['entrada'] = floatval($registro['cantidad']);
                $registro['salida'] = 0;
                $registro['importeEntrada'] = $registro['importe'];
                $registro['importeSalida'] = 0;
                $subconcepto['acumulado'] += $registro['entrada'];
                $subconcepto['entradas'] += $registro['entrada'];
                $subconcepto['importeEntrada'] += $registro['importe'];

                // Acumular el importe
                $registro['acumulado'] = $subconcepto['importeAcumulado'] + $registro['importeEntrada'];
                $subconcepto['importeAcumulado'] += $registro['importeEntrada'];

            } else if ($registro['tipo'] == '2') {
                // Salida
                $registro['entrada'] = 0;
                $registro['salida'] = floatval($registro['cantidad']);
                $registro['importeSalida'] = $registro['importe'];
                $registro['importeEntrada'] = 0;
                $subconcepto['acumulado'] -= $registro['salida'];
                $subconcepto['salidas'] += $registro['salida'];
                $subconcepto['importeSalida'] += $registro['importe'];
                $resultado['totalImporteSalida'] += $registro['importe'];

                // Acumular el importe
                $registro['acumulado'] = $subconcepto['importeAcumulado'] - $registro['importeSalida'];
                $subconcepto['importeAcumulado'] -= $registro['importeSalida'];

            }

            $registro['existencia'] = $subconcepto['acumulado'];

            array_push($subconcepto['registros'], $registro);
        }

        
        // Si tiene acumulado, calcula el precio promedio, si no pone cero
        $subconcepto['precioPromedio'] = $subconcepto['entradas'] > 0 ? ($subconcepto['importeEntrada']) / $subconcepto['entradas'] : 0;
        array_push($resultado['productos'], $subconcepto);

    }


    return $resultado;
}