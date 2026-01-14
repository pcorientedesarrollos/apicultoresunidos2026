<?php
// ob_start();
// error_reporting(E_ALL & ~E_NOTICE);
// ini_set('display_errors', 0);
// ini_set('log_errors', 1);

if (!isset($_GET['consulta'])) {
    include_once '../../../DAOConeccion/conePDO.php';
    $pdo = new conePDO();
    $con = $pdo->conectar();
    $con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
}
date_default_timezone_set('America/Merida');

function obtenerInventarioMielAdministracion($idMes = false, $acumulado = false, $fechaInicial = false, $fechaFinal = false)
{

    global $con;
    $resultado = array();
    $resultado['totalProductos'] = 0;
    $resultado['totalEntradas'] = 0;
    $resultado['totalSalidas'] = 0;
    $resultado['totalSaldo'] = 0;
    $resultado['totalImporteSalida'] = 0;
    $resultado['caja'] = 0;
    $resultado['bancos'] = 0;
    $resultado['totalImporteVenta'] = 0;
    $resultado['productos'] = array();

    $selectSubconceptos = $con->prepare("SELECT s.subSubcuenta AS concepto, idSubconceptoCC AS id
    FROM otrassalidasdetalle o 
		LEFT JOIN subsubcuentas s ON s.idSubSubcuenta = o.idSubconceptoCC
    WHERE o.cajachica = 1 AND o.idSubconceptoCC IS NOT NULL GROUP BY o.idSubconceptoCC
    UNION
    SELECT s.subSubcuenta AS concepto, c.idSubConcepto AS id
    FROM cajachicadetalle c
		LEFT JOIN subsubcuentas s ON s.idSubSubcuenta = c.idSubConcepto
    WHERE c.idConcepto = '131' AND c.idSubConcepto IS NOT NULL GROUP BY c.idSubConcepto
		    UNION
    SELECT s.subSubcuenta AS concepto, a.idSubsubcuenta AS id
    FROM auxiliardebancos a
		LEFT JOIN subsubcuentas s ON s.idSubSubcuenta = a.idSubsubcuenta
    WHERE a.idSubcuenta	= '131' AND a.idSubsubcuenta IS NOT NULL GROUP BY a.idSubsubcuenta
		     UNION
    SELECT nombre as concepto, '' AS id
     FROM saldoinicial_miel
    GROUP BY concepto");
    $selectSubconceptos->execute();
    if ($selectSubconceptos == false) {
        throw new Exception($con->errorInfo());
    }

    foreach ($selectSubconceptos->fetchAll(PDO::FETCH_ASSOC) as $subconcepto) {

        $subconcepto['existenciaPasada'] = 0;
        $subconcepto['importePasado'] = 0;
        // * ANA 2025
        $subconcepto['precioVenta'] = 0;
        // *
        $sqlSaldoInicial = $con->prepare("SELECT existenciaPasada, importePasado FROM saldoinicial_miel WHERE nombre = :nombre");
        $sqlSaldoInicial->bindParam(':nombre', $subconcepto['concepto']);
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

        $obtenerCosto = $con->prepare("SELECT s.idSubSubcuenta, s.precioUnitario, c.ingresoEgreso 
        FROM subsubcuentas s 
        LEFT JOIN subcuentas sb ON sb.idSubcuenta = s.idSubcuenta
        LEFT JOIN cuentas c ON c.idCuentaConcepto = sb.idCuentaConcepto
        WHERE s.subSubcuenta = :nombre");
        $obtenerCosto->bindParam(':nombre', $subconcepto['concepto']);
        $obtenerCosto->execute();
        if ($obtenerCosto == false) {
            throw new Exception($con->errorInfo());
        }
        foreach ($obtenerCosto->fetchAll(PDO::FETCH_ASSOC) as $costos) {
            if ($costos['ingresoEgreso'] === '1') { //entrada
                $subconcepto['idSubcuenta'] = $costos['idSubSubcuenta'];
                $subconcepto['precioVenta'] = 0;
            } else if ($costos['ingresoEgreso'] === '0') { //salida
                $subconcepto['precioVenta'] = $costos['precioUnitario'];
                $subconcepto['idSubcuenta'] = $costos['idSubSubcuenta'];
            }
        }

        $subconcepto['entradas'] = 0;
        $subconcepto['salidas'] = 0;
        $subconcepto['importeEntrada'] = 0;
        $subconcepto['importeSalida'] = 0;
        $subconcepto['precioPromedio'] = 0;
        $subconcepto['registros'] = array();
        $subconcepto['importeEntrada'] += $subconcepto['importePasado'];
        $resultado['totalProductos'] += $subconcepto['acumulado'];

        $anterior_entradas = 0;
        $anterior_salidas = 0;
        $anterior_salidasBancos = 0;

        $sqlSaldoAnterior_entradas = "SELECT SUM(edd.kg) AS cantidad 
        FROM otrassalidasdetalle edd 
        LEFT JOIN otrassalidas ede ON ede.idOtraSalida = edd.idOtrasSalidas
        WHERE edd.cajaChica = 1 AND edd.idSubconceptoCC = :subconceptoCC";
        if (!$acumulado) {
            if ($fechaInicial) {
                $sqlSaldoAnterior_entradas .= " AND ede.fecha < '" . $fechaInicial . "'";
            } else {
                $sqlSaldoAnterior_entradas .= " AND SUBSTR(ede.fecha FROM 6 FOR 2 ) < " . $idMes;
            }
        }

        $resp_entradas = $con->prepare($sqlSaldoAnterior_entradas);
        $resp_entradas->bindParam(':subconceptoCC', $subconcepto['idSubcuenta']);
        $resp_entradas->bindColumn('cantidad', $anterior_entradas);
        $resp_entradas->execute();
        if ($resp_entradas == false) {
            throw new Exception($con->errorInfo());
        } else {
            $resp_entradas->fetch(PDO::FETCH_BOUND);
        }

        $sqlSaldoAnterior_salidas = "SELECT SUM(cd.kg) AS cantidad 
        FROM cajachicadetalle cd 
        LEFT JOIN cajachica ce ON ce.idCajaChica = cd.idCajaChica
        WHERE cd.subconcepto = :subconceptoCC AND ce.tipo = 0";
        if (!$acumulado) {
            if ($fechaInicial) {
                $sqlSaldoAnterior_salidas .= " AND ce.fecha < '" . $fechaInicial . "'";
            } else {
                $sqlSaldoAnterior_salidas .= " AND SUBSTR(ce.fecha FROM 6 FOR 2 ) < " . $idMes;
            }
        }
        $resp_salidas = $con->prepare($sqlSaldoAnterior_salidas);
        $resp_salidas->bindParam(':subconceptoCC', $subconcepto['concepto']);
        $resp_salidas->bindColumn('cantidad', $anterior_salidas);
        $resp_salidas->execute();
        if ($resp_salidas == false) {
            throw new Exception($con->errorInfo());
        } else {
            $resp_entradas->fetch(PDO::FETCH_BOUND);
        }

        $sqlSaldoAnteriorBancos_salidas = "SELECT SUM(a.kg) AS cantidad 
        FROM auxiliardebancos a 
        WHERE a.subsubcuenta = :subconceptoCC AND a.ingresoEgreso = 0";
        if (!$acumulado) {
            if ($fechaInicial) {
                $sqlSaldoAnteriorBancos_salidas .= " AND a.fecha < '" . $fechaInicial . "'";
            } else {
                $sqlSaldoAnteriorBancos_salidas .= " AND SUBSTR(a.fecha FROM 6 FOR 2 ) < " . $idMes;
            }
        }
        $resp_salidasBancos = $con->prepare($sqlSaldoAnteriorBancos_salidas);
        $resp_salidasBancos->bindParam(':subconceptoCC', $subconcepto['concepto']);
        $resp_salidasBancos->bindColumn('cantidad', $anterior_salidasBancos);
        $resp_salidasBancos->execute();
        if ($resp_salidasBancos == false) {
            throw new Exception($con->errorInfo());
        } else {
            $resp_entradas->fetch(PDO::FETCH_BOUND);
        }

        if ($acumulado) {
            $subconcepto['existencia'] = $subconcepto['acumulado'];
        } else {
            $subconcepto['existencia'] = $subconcepto['acumulado'] + $anterior_entradas - $anterior_salidas - $anterior_salidasBancos;
        }

        $sqlSelect = "SELECT osd.cantidad, '1' AS tipo, '0' AS importe, 'entradas' AS origen
        FROM otrassalidasdetalle osd
        LEFT JOIN otrassalidas ede ON ede.idOtraSalida = osd.idOtrasSalidas
        LEFT JOIN subsubcuentas s ON s.idSubSubcuenta = osd.idSubconceptoCC
        WHERE osd.cajaChica = 1 AND s.subSubcuenta = :subconceptoCC";
        if (!$acumulado) {
            if ($fechaInicial && $fechaFinal) {
                $sqlSelect .= " AND ede.fecha BETWEEN '" . $fechaInicial . "' AND '" . $fechaFinal . "'
                UNION 
                SELECT cd.kg AS cantidad, '2' AS tipo, cd.importe, 'caja' AS origen 
        FROM cajachicadetalle cd 
        LEFT JOIN cajachica ce ON ce.idCajaChica = cd.idCajaChica
        WHERE cd.subconcepto = :subconceptoCC AND ce.tipo = 0
                AND ce.fecha BETWEEN '" . $fechaInicial . "' AND '" . $fechaFinal . "'
                UNION
                SELECT a.kg AS cantidad, '2' AS tipo, a.cantidad AS importe, 'bancos' AS origen
                FROM auxiliardebancos a
                WHERE a.ingresoEgreso = 0 AND a.subsubcuenta = :subconceptoCC AND a.fecha BETWEEN '" . $fechaInicial . "' AND '" . $fechaFinal . "'";
            } else {
                $sqlSelect .= " AND SUBSTR(ede.fecha FROM 6 FOR 2 ) = " . $idMes;
                $sqlSelect .= "  UNION 
                SELECT cd.kg AS cantidad, '2' AS tipo, cd.importe, 'caja' AS origen 
        FROM cajachicadetalle cd 
        LEFT JOIN cajachica ce ON ce.idCajaChica = cd.idCajaChica
        WHERE cd.subconcepto = :subconceptoCC AND ce.tipo = 0
                AND SUBSTR(ce.fecha FROM 6 FOR 2 ) =  $idMes
                UNION
                SELECT a.kg AS cantidad, '2' AS tipo, a.cantidad AS importe, 'bancos' AS origen
                FROM auxiliardebancos a
                WHERE a.ingresoEgreso = 0 AND a.subsubcuenta = :subconceptoCC AND SUBSTR(a.fecha FROM 6 FOR 2 ) =  $idMes";
            }
        } else if ($acumulado) {
            $sqlSelect .= " UNION 
            SELECT cd.kg AS cantidad, '2' AS tipo, cd.importe, 'caja' AS origen 
    FROM cajachicadetalle cd 
    LEFT JOIN cajachica ce ON ce.idCajaChica = cd.idCajaChica
    WHERE cd.subconcepto = :subconceptoCC AND ce.tipo = 0
            UNION
            SELECT a.kg AS cantidad, '2' AS tipo, a.cantidad AS importe, 'bancos' AS origen
            FROM auxiliardebancos a
            WHERE a.ingresoEgreso = 0 AND a.subsubcuenta = :subconceptoCC";
        }
        $query = $con->prepare($sqlSelect);
        $query->bindParam(':subconceptoCC', $subconcepto['concepto']);
        // $query->bindParam(':idSubconcepto', $subconcepto['idSubcuenta']);
        $query->execute();

        if ($query == false) {
            throw new Exception($con->errorInfo());
        }

        foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $registro) {

            if ($registro['tipo'] === '1') {
                $registro['entrada'] = floatval($registro['cantidad']);
                $registro['salida'] = 0;
                $subconcepto['acumulado'] += $registro['entrada'];
                $subconcepto['entradas'] += $registro['entrada'];
                $subconcepto['saldo'] = $subconcepto['existencia'] + $registro['entrada'];
                $resultado['totalEntradas'] += $registro['entrada'];
                $registro['importeVenta'] = 0;
            } else if ($registro['tipo'] === '2') {
                $registro['entrada'] = 0;
                $registro['salida'] = floatval($registro['cantidad']);
                $subconcepto['acumulado'] -= $registro['salida'];
                $subconcepto['salidas'] += $registro['salida'];
                $subconcepto['saldo'] = $subconcepto['existencia'] - $registro['salida'];
                $resultado['totalSalidas'] += $registro['salida'];
                if ($registro['origen'] == 'caja') {
                    $resultado['caja'] += $registro['importe'];
                } else if ($registro['origen'] == 'bancos') {
                    $resultado['bancos'] += $registro['importe'];
                }
            }
            $registro['existencia'] = $subconcepto['acumulado'];
            $subconcepto['saldo'] = $subconcepto['existencia'] + $subconcepto['entradas'] - $subconcepto['salidas'];
            $subconcepto['importeVenta'] = $subconcepto['precioVenta'] * $subconcepto['salidas'];
            array_push($subconcepto['registros'], $registro);
        }
        $resultado['totalImporteVenta'] += $subconcepto['importeVenta'];
        // $resultado['caja'] += $subconcepto['caja'];
        // $resultado['bancos'] += $subconcepto['bancos'];
        array_push($resultado['productos'], $subconcepto);
    }

    $resultado['totalSaldo'] = $resultado['totalProductos'] + $resultado['totalEntradas'] - $resultado['totalSalidas'];
    return $resultado;
}

// ob_end_clean();
