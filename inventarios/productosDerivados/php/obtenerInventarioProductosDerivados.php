<?php
ob_start();
error_reporting(E_ALL & ~E_NOTICE);
ini_set('display_errors', 0);
ini_set('log_errors', 1);

if (!isset($_GET['informeFinanciero'])) {
    include_once '../../../DAOConeccion/conePDO.php';
    $pdo = new conePDO();
    $con = $pdo->conectar();
    $con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
}

// include_once '../../../controlAdministrativo/php/nombreDePersona.php';
date_default_timezone_set('America/Merida');

function obtenerInventarioDerivado($idMes = false, $acumulado = false, $fechaInicial = false, $fechaFinal = false)
{

    global $con;
    $resultado = array();
    $resultado['totalProductos'] = 0;
    $resultado['totalEntradas'] = 0;
    $resultado['totalSalidas'] = 0;
    $resultado['totalSaldo'] = 0;
    $resultado['totalImporteSalida'] = 0;
    $resultado['productos'] = array();

    // Seleccionar los conceptos de productos derivados
    // Seleccionar solo subcuentascuentas
    // Y seleccionar solo conceptos
    // Lista primero los conceptos y despues las cuentas
    // CONCEPTOS
    // Agrupar por nombres, porque los conceptos de ingresos y egresos tienen diferente id
    // $selectSubconceptos = $con->prepare("SELECT concepto as subconceptoCC FROM derivadosalmacendetalle WHERE idConcepto IS NOT NULL GROUP BY concepto
    // UNION
    // SELECT nombre as subconceptoCC FROM saldoinicial_derivados GROUP BY nombre");
    $selectSubconceptos = $con->prepare("SELECT concepto as subconceptoCC 
    FROM derivadosalmacendetalle 
    WHERE idConcepto IS NOT NULL GROUP BY concepto
    UNION
    SELECT concepto as subconceptoCC
    FROM derivadosalmacendetalle_salidas
    WHERE idConcepto IS NOT NULL GROUP BY concepto
     UNION
    SELECT nombre as subconceptoCC
     FROM saldoinicial_derivados 
    GROUP BY nombre");
    $selectSubconceptos->execute();
    if ($selectSubconceptos == false) {
        throw new Exception($con->errorInfo());
    }

    foreach ($selectSubconceptos->fetchAll(PDO::FETCH_ASSOC) as $subconcepto) {

        $subconcepto['existenciaPasada'] = 0;
        $subconcepto['importePasado'] = 0;

        // Saldo inicial con el nuevo modulo
        $sqlSaldoInicial = $con->prepare("SELECT existenciaPasada, importePasado FROM saldoinicial_derivados WHERE nombre = :nombre");
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
        $subconcepto['entradas'] = 0;
        $subconcepto['salidas'] = 0;
        $subconcepto['importeEntrada'] = 0;
        $subconcepto['importeSalida'] = 0;
        $subconcepto['precioPromedio'] = 0;
        $anterior_entradas = 0;
        $anterior_salidas = 0;
        $subconcepto['registros'] = array();
        $subconcepto['importeEntrada'] += $subconcepto['importePasado'];
        $resultado['totalProductos'] += $subconcepto['acumulado'];

        $obtenerCosto = $con->prepare("SELECT s.precioUnitario, c.ingresoEgreso 
        FROM subsubcuentas s 
        LEFT JOIN subcuentas sb ON sb.idSubcuenta = s.idSubcuenta
        LEFT JOIN cuentas c ON c.idCuentaConcepto = sb.idCuentaConcepto
        WHERE s.subSubcuenta = :nombre");
        $obtenerCosto->bindParam(':nombre', $subconcepto['subconceptoCC']);
        $obtenerCosto->execute();
        if ($obtenerCosto == false) {
            throw new Exception($con->errorInfo());
        }
        foreach ($obtenerCosto->fetchAll(PDO::FETCH_ASSOC) as $costos) {
            if ($costos['ingresoEgreso'] == 1) { //entrada
                $subconcepto['costoVenta'] = $costos['precioUnitario'];
            } else if ($costos['ingresoEgreso'] == 0) { //salida
                $subconcepto['costoCompra'] = $costos['precioUnitario'];
            }
        }

        $sqlSaldoAnterior_entradas = "SELECT SUM(edd.cantidad) AS cantidad 
        FROM derivadosalmacendetalle edd 
        LEFT JOIN derivadosalmacenencabezado ede ON ede.idEntrada = edd.idEntrada
        WHERE edd.concepto = :subconceptoCC";
        if (!$acumulado) {
            if ($fechaInicial) {
                $sqlSaldoAnterior_entradas .= " AND ede.fecha < '" . $fechaInicial . "'";
            } else {
                $sqlSaldoAnterior_entradas .= " AND SUBSTR(ede.fecha FROM 6 FOR 2 ) <= " . $idMes;
            }
        }

        $resp_entradas = $con->prepare($sqlSaldoAnterior_entradas);
        $resp_entradas->bindParam(':subconceptoCC', $subconcepto['subconceptoCC']);
        $resp_entradas->bindColumn('cantidad', $anterior_entradas);
        $resp_entradas->execute();
        if ($resp_entradas == false) {
            throw new Exception($con->errorInfo());
        } else {
            $resp_entradas->fetch(PDO::FETCH_BOUND);
        }

        $sqlSaldoAnterior_salidas = "SELECT SUM(edds.cantidad) AS cantidad 
        FROM derivadosalmacendetalle_salidas edds 
        LEFT JOIN derivadosalmacenencabezado_salidas edes ON edes.idSalida = edds.idSalida
        WHERE edds.concepto = :subconceptoCC";
        if (!$acumulado) {
            if ($fechaInicial) {
                $sqlSaldoAnterior_salidas .= " AND edes.fecha < '" . $fechaInicial . "'";
            } else {
                $sqlSaldoAnterior_salidas .= " AND SUBSTR(edes.fecha FROM 6 FOR 2 ) <= " . $idMes;
            }
        }

        $resp_salidas = $con->prepare($sqlSaldoAnterior_salidas);
        $resp_salidas->bindParam(':subconceptoCC', $subconcepto['subconceptoCC']);
        $resp_salidas->bindColumn('cantidad', $anterior_salidas);
        $resp_salidas->execute();
        if ($resp_salidas == false) {
            throw new Exception($con->errorInfo());
        } else {
            $resp_salidas->fetch(PDO::FETCH_BOUND);
        }


        if ($acumulado) {
            $subconcepto['existencia'] = $subconcepto['acumulado'];
        } else {
            $subconcepto['existencia'] = $subconcepto['acumulado'] + $anterior_entradas - $anterior_salidas;
        }

        $sqlSelect = "SELECT aa.cantidad, aa.descripcion, aa.importe, aa.costoUnitario, aea.tipo, aea.tipoPersona, aea.idProveedor, aea.fecha 
        FROM derivadosalmacendetalle aa
        LEFT JOIN derivadosalmacenencabezado aea ON aa.idEntrada = aea.idEntrada
        WHERE aa.concepto = :subconceptoCC";
        if (!$acumulado) {
            if ($fechaInicial && $fechaFinal) {
                $sqlSelect .= " AND aea.fecha BETWEEN '" . $fechaInicial . "' AND '" . $fechaFinal . "'
                UNION 
                SELECT aas.cantidad, aas.descripcion, aas.importe, aas.costoUnitario, aeas.tipo, aeas.tipoPersona, aeas.idProveedor, aeas.fecha 
                FROM derivadosalmacendetalle_salidas aas
                LEFT JOIN derivadosalmacenencabezado_salidas aeas ON aas.idSalida = aeas.idSalida
                WHERE aas.concepto = :subconceptoCC AND aeas.fecha BETWEEN '" . $fechaInicial . "' AND '" . $fechaFinal . "'";
            } else {
                $sqlSelect .= " AND SUBSTR(aea.fecha FROM 6 FOR 2 ) = " . $idMes;
                $sqlSelect .= " UNION SELECT aas.cantidad, aas.descripcion, aas.importe, aas.costoUnitario, aeas.tipo, aeas.tipoPersona, aeas.idProveedor, aeas.fecha 
                FROM derivadosalmacendetalle_salidas aas
                LEFT JOIN derivadosalmacenencabezado_salidas aeas ON aas.idSalida = aeas.idSalida
                WHERE aas.concepto = :subconceptoCC AND SUBSTR(aeas.fecha FROM 6 FOR 2 ) = " . $idMes;
            }
        } else if ($acumulado) {
            $sqlSelect .= " UNION 
                SELECT aas.cantidad, aas.descripcion, aas.importe, aas.costoUnitario, aeas.tipo, aeas.tipoPersona, aeas.idProveedor, aeas.fecha 
                FROM derivadosalmacendetalle_salidas aas
                LEFT JOIN derivadosalmacenencabezado_salidas aeas ON aas.idSalida = aeas.idSalida
                WHERE aas.concepto = :subconceptoCC";
        }
        $query = $con->prepare($sqlSelect);
        $query->bindParam(':subconceptoCC', $subconcepto['subconceptoCC']);
        $query->execute();

        if ($query == false) {
            throw new Exception($con->errorInfo());
        }

        foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $registro) {

            // $registro['nombre'] = retornarNombre($con, $registro['tipoPersona'], $registro['idProveedor']);
            if ($registro['tipo'] == '1') {
                // Entrada
                $registro['entrada'] = floatval($registro['cantidad']);
                $registro['salida'] = 0;
                $registro['importeEntrada'] = $registro['importe'];
                $registro['importeSalida'] = 0;
                $subconcepto['acumulado'] += $registro['entrada'];
                $subconcepto['entradas'] += $registro['entrada'];
                $subconcepto['importeEntrada'] += $registro['importe'];
                $subconcepto['saldo'] = $subconcepto['existencia'] + $registro['entrada'];
                $resultado['totalEntradas'] += $registro['entrada'];
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
                $resultado['totalSalidas'] += $registro['salida'];
                // Acumular el importe
                $registro['acumulado'] = $subconcepto['importeAcumulado'] - $registro['importeSalida'];
                $subconcepto['importeAcumulado'] -= $registro['importeSalida'];
            }

            $subconcepto['saldo'] = $subconcepto['existencia'] + $subconcepto['entradas'] - $subconcepto['salidas'];

            $registro['existencia'] = $subconcepto['acumulado'];
            array_push($subconcepto['registros'], $registro);
        }

        // Si tiene acumulado, calcula el precio promedio, si no pone cero
        $subconcepto['precioPromedio'] = $subconcepto['entradas'] > 0 ? ($subconcepto['importeEntrada']) / $subconcepto['entradas'] : 0;
        array_push($resultado['productos'], $subconcepto);
    }

    // Subcuentas (que no tienen conceptos)
    // Agrupar por nombres, porque los conceptos de ingresos y egresos tienen diferente id
    $selectSubconceptos = $con->prepare("SELECT subcuenta as subconceptoCC FROM derivadosalmacendetalle WHERE idSubcuenta IS NOT NULL GROUP BY subcuenta
    UNION SELECT subcuenta as subconceptoCC FROM derivadosalmacendetalle_salidas WHERE idSubcuenta IS NOT NULL GROUP BY subcuenta");
    $selectSubconceptos->execute();
    if ($selectSubconceptos == false) {
        throw new Exception($con->errorInfo());
    }

    foreach ($selectSubconceptos->fetchAll(PDO::FETCH_ASSOC) as $subconcepto) {

        $subconcepto['existenciaPasada'] = 0;
        $subconcepto['importePasado'] = 0;

        // Saldo inicial con el nuevo modulo
        $sqlSaldoInicial = $con->prepare("SELECT existenciaPasada, importePasado FROM saldoinicial_derivados WHERE nombre = :nombre");
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
        $resultado['totalProductos'] += $subconcepto['acumulado'];
        $subconcepto['importeEntrada'] += $subconcepto['importePasado'];


        $anterior_entradas = 0;
        $anterior_salidas = 0;

        $sqlSaldoAnterior_entradas = "SELECT SUM(edd.cantidad) AS cantidad 
        FROM derivadosalmacendetalle edd 
        LEFT JOIN derivadosalmacenencabezado ede ON ede.idEntrada = edd.idEntrada
        WHERE edd.concepto = :subconceptoCC";
        if (!$acumulado) {
            if ($fechaInicial) {
                $sqlSaldoAnterior_entradas .= " AND ede.fecha < '" . $fechaInicial . "'";
            } else {
                $sqlSaldoAnterior_entradas .= " AND SUBSTR(ede.fecha FROM 6 FOR 2 ) <= " . $idMes;
            }
        }

        $resp_entradas = $con->prepare($sqlSaldoAnterior_entradas);
        $resp_entradas->bindParam(':subconceptoCC', $subconcepto['subconceptoCC']);
        $resp_entradas->bindColumn('cantidad', $anterior_entradas);
        $resp_entradas->execute();
        if ($resp_entradas == false) {
            throw new Exception($con->errorInfo());
        } else {
            $resp_entradas->fetch(PDO::FETCH_BOUND);
        }

        $sqlSaldoAnterior_salidas = "SELECT SUM(edds.cantidad) AS cantidad 
        FROM derivadosalmacendetalle_salidas edds 
        LEFT JOIN derivadosalmacenencabezado_salidas edes ON edes.idSalida = edds.idSalida
        WHERE edds.concepto = :subconceptoCC";
        if (!$acumulado) {
            if ($fechaInicial) {
                $sqlSaldoAnterior_salidas .= " AND edes.fecha < '" . $fechaInicial . "'";
            } else {
                $sqlSaldoAnterior_salidas .= " AND SUBSTR(edes.fecha FROM 6 FOR 2 ) <= " . $idMes;
            }
        }
        $resp_salidas = $con->prepare($sqlSaldoAnterior_salidas);
        $resp_salidas->bindParam(':subconceptoCC', $subconcepto['subconceptoCC']);
        $resp_entradas->bindColumn('cantidad', $anterior_salidas);
        $resp_salidas->execute();
        if ($resp_salidas == false) {
            throw new Exception($con->errorInfo());
        } else {
            $resp_salidas->fetch(PDO::FETCH_BOUND);
        }

        if ($acumulado) {
            $subconcepto['existencia'] = $subconcepto['acumulado'];
        } else {
            $subconcepto['existencia'] = $subconcepto['acumulado'] + $anterior_entradas - $anterior_salidas;
        }

        $sqlSelect = "SELECT aa.cantidad, aa.descripcion, aa.importe, aa.costoUnitario, aea.tipo, aea.tipoPersona, aea.idProveedor, aea.fecha FROM derivadosalmacendetalle aa
            LEFT JOIN derivadosalmacenencabezado aea ON aa.idEntrada = aea.idEntrada
            WHERE aa.subcuenta = :subconceptoCC AND aa.idConcepto IS NULL";
        if (!$acumulado) {
            if ($fechaInicial && $fechaFinal) {
                $sqlSelect .= " AND aea.fecha BETWEEN '" . $fechaInicial . "' AND '" . $fechaFinal . "'
                UNION SELECT aas.cantidad, aas.descripcion, aas.importe, aas.costoUnitario, aeas.tipo, aeas.tipoPersona, aeas.idProveedor, aeas.fecha FROM derivadosalmacendetalle_salidas aas
                LEFT JOIN derivadosalmacenencabezado_salidas aeas ON aas.idSalida = aeas.idSalida
                WHERE aas.subcuenta = :subconceptoCC AND aas.idConcepto IS NULL AND aeas.fecha BETWEEN '" . $fechaInicial . "' AND '" . $fechaFinal . "'
                ";
            } else {
                $sqlSelect .= " AND SUBSTR(aea.fecha FROM 6 FOR 2 ) = " . $idMes;
                $sqlSelect .= " UNION SELECT aas.cantidad, aas.descripcion, aas.importe, aas.costoUnitario, aeas.tipo, aeas.tipoPersona, aeas.idProveedor, aeas.fecha FROM derivadosalmacendetalle_salidas aas
                LEFT JOIN derivadosalmacenencabezado_salidas aeas ON aas.idSalida = aeas.idSalida
                WHERE aas.subcuenta = :subconceptoCC AND SUBSTR(aeas.fecha FROM 6 FOR 2 ) = " . $idMes;
            }
        } else if ($acumulado) {
            $sqlSelect .= " UNION SELECT aas.cantidad, aas.descripcion, aas.importe, aas.costoUnitario, aeas.tipo, aeas.tipoPersona, aeas.idProveedor, aeas.fecha FROM derivadosalmacendetalle_salidas aas
            LEFT JOIN derivadosalmacenencabezado_salidas aeas ON aas.idSalida = aeas.idSalida
            WHERE aas.subcuenta = :subconceptoCC";
        }
        $query = $con->prepare($sqlSelect);
        $query->bindParam(':subconceptoCC', $subconcepto['subconceptoCC']);
        $query->execute();

        if ($query == false) {
            throw new Exception($con->errorInfo());
        }

        foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $registro) {
            // $registro['nombre'] = retornarNombre($con, $registro['tipoPersona'], $registro['idProveedor']);
            if ($registro['tipo'] == '1') {
                // Entrada
                $registro['entrada'] = floatval($registro['cantidad']);
                $registro['salida'] = 0;
                $registro['importeEntrada'] = $registro['importe'];
                $registro['importeSalida'] = 0;
                $subconcepto['acumulado'] += $registro['entrada'];
                $subconcepto['entradas'] += $registro['entrada'];
                $subconcepto['importeEntrada'] += $registro['importe'];
                $resultado['totalEntradas'] += $registro['entrada'];
                $subconcepto['saldo'] = $subconcepto['existencia'] + $registro['entradas'];
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
                $resultado['totalSalidas'] += $registro['salida'];
                $subconcepto['saldo'] = $subconcepto['existencia'] - $subconcepto['salidas'];
                // Acumular el importe
                $registro['acumulado'] = $subconcepto['importeAcumulado'] - $registro['importeSalida'];
                $subconcepto['importeAcumulado'] -= $registro['importeSalida'];
            }

            $registro['existencia'] = $subconcepto['acumulado'];
            $subconcepto['saldo'] = $subconcepto['existencia'] + $subconcepto['entradas'] - $subconcepto['salidas'];
            array_push($subconcepto['registros'], $registro);
        }


        // Si tiene acumulado, calcula el precio promedio, si no pone cero
        $subconcepto['precioPromedio'] = $subconcepto['entradas'] > 0 ? ($subconcepto['importeEntrada']) / $subconcepto['entradas'] : 0;
        array_push($resultado['productos'], $subconcepto);
    }

    $resultado['totalSaldo'] = $resultado['totalProductos'] + $resultado['totalEntradas'] - $resultado['totalSalidas'];
    return $resultado;
}

ob_end_clean();
