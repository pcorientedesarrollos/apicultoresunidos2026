<?php

function obtenerSaldosDeudores($mes = false, $meses = false)
{

    global $con;
    $lista_proveedores = array();
    $resultado = array(
        'totalSaldoDeudor' => 0,
        'totalSaldoProveedor' => 0,
        'conciliacion' => 0
    );
    
    // switch ($_GET['parametro']) {
    //     case 'activos':
    //         $estado = 'p.idEstado = 1';
    //     break;
    //     case 'morosos':
    //         $estado = 'p.idEstado = 2';
    //     break;
    //     case 'todos':
    //         $estado = '(p.idEstado = 1 OR p.idEstado = 2)';
    //     break;
    //     default:
    //         throw new Exception('El parámetro no es válido');
    //         break;
    // }

    // Vamos a poner el estado por defecto en todos
    $estado = '(p.idEstado = 1 OR p.idEstado = 2)';

    $queryProveedores = $con->prepare("SELECT idProveedor, cantidad
    FROM(SELECT ab.nombreDe AS idProveedor, p.cantidad
    FROM auxiliardebancos ab
    INNER JOIN proveedor p ON p.idProveedor = ab.nombreDe
    WHERE ab.tipoDePersona = 1 AND p.empresa = 0
    AND $estado GROUP BY ab.nombreDe ORDER BY p.nombre ASC) auxiliar
    UNION
    SELECT idProveedor, cantidad
    FROM (SELECT c.nombre AS idProveedor, p.cantidad
    FROM cajachica c
    INNER JOIN proveedor p ON p.idProveedor = c.nombre
    WHERE c.tipoDeCliente = 1 AND p.empresa = 0
    AND $estado GROUP BY c.nombre ORDER BY p.nombre ASC) caja
    UNION
    SELECT idProveedor, cantidad
    FROM (SELECT g.idProveedor, p.cantidad
    FROM gastosrealizados g
    INNER JOIN proveedor p ON p.idProveedor = g.idProveedor
    WHERE p.empresa = 0
    AND $estado GROUP BY g.idProveedor ORDER BY p.nombre ASC) gastos
    UNION
    SELECT idProveedor, cantidad
    FROM (SELECT aec.idProveedor, p.cantidad
    FROM almacenencabezadocera aec
    INNER JOIN proveedor p ON p.idProveedor = aec.idProveedor
    WHERE aec.tipoPersona = 1 AND p.empresa = 0
    AND $estado GROUP BY aec.idProveedor ORDER BY p.nombre ASC) almacencera
    UNION
    SELECT idProveedor, cantidad
    FROM (SELECT aea.idProveedor, p.cantidad
    FROM almacenencabezadoapicola aea
    INNER JOIN proveedor p ON p.idProveedor = aea.idProveedor
    WHERE aea.tipoPersona = 1 AND p.empresa = 0
    AND $estado GROUP BY aea.idProveedor ORDER BY p.nombre ASC) almacenapicola
    UNION
    SELECT idProveedor, cantidad
    FROM (SELECT ae.idProveedor, p.cantidad
    FROM almacenencabezado ae
    INNER JOIN proveedor p ON p.idProveedor = ae.idProveedor
    WHERE p.empresa = 0
    AND $estado GROUP BY ae.idProveedor ORDER BY p.nombre ASC) almacen
    UNION
    SELECT idProveedor, cantidad
    FROM (SELECT ce.idProveedor, p.cantidad
    FROM cubetasencabezado ce
    INNER JOIN proveedor p ON p.idProveedor = ce.idProveedor
    WHERE p.empresa = 0
    AND $estado GROUP BY ce.idProveedor ORDER BY p.nombre ASC) almacenCubeta
    UNION
    SELECT idProveedor, cantidad
    FROM (SELECT p.idProveedor, p.cantidad
    FROM proveedor p
    WHERE p.cantidad > 0 AND p.empresa = 0
    AND $estado GROUP BY p.idProveedor ORDER BY p.nombre ASC) proveedores
    GROUP BY idProveedor");
    $queryProveedores->execute();

    if ($queryProveedores == false) {
        throw new Exception($con->errorInfo());
    }

        // Obtener las subcuentas que van a deudores y proveedores

    $sqlSubcuentas = $con->prepare("SELECT idSubcuenta FROM subcuentas WHERE deudores = 1");
    $sqlSubcuentas->execute();
    if ($sqlSubcuentas == false) {
        throw new Exception($con->errorInfo());
    }

    $arregloIdSubcuentas = $sqlSubcuentas->fetchAll(PDO::FETCH_ASSOC);

    $idSubcuentas = '(';
    foreach ($arregloIdSubcuentas as $index => $idSubcuenta) {
        $idSubcuentas .= $idSubcuenta['idSubcuenta'];
        if (isset($arregloIdSubcuentas[($index + 1)])) {
            $idSubcuentas .= ', ';
        }
    }
    $idSubcuentas .= ')';

    foreach ($queryProveedores->fetchAll(PDO::FETCH_ASSOC) as $proveedor) {
        $idProveedor = $proveedor['idProveedor'];
        $saldoInicial = $proveedor['cantidad'];
        // $estado = $proveedor['idEstado'];

        $sqlMovimientosProveedor = $con->prepare("SELECT idAuxiliar, fecha, descripcion, referencia, nombreBanco, nombreCuenta, cantidad,'' as neto, '' as precio, '' as totalCompra, tipo, origen, '' AS idConcepto
                                        FROM (SELECT a.idAuxiliar, a.fecha, a.descripcion, a.referencia, a.idBanco AS nombreBanco, a.idCuenta AS nombreCuenta, a.cantidad, a.ingresoEgreso AS tipo, 'Banco' AS origen 
                                        FROM auxiliardebancos a 
                                        WHERE a.ingresoEgreso = 1 AND a.tipoDePersona = 1 AND a.nombreDe = :idProveedor AND a.idSubcuenta IN $idSubcuentas
                                        ) AS tablaAuxiliar
                                        UNION
                                        SELECT idAuxiliar, fecha, descripcion, referencia,'' AS nombreBanco, '' AS nombreCuenta, cantidad, '' as neto, '' as precio, '' as totalCompra, tipo, origen, idConcepto
                                        FROM (SELECT d.idCajaChica AS idAuxiliar, c.fecha, d.descripcion, '' AS referencia,
                                        CASE WHEN d.cantidad IS NOT NULL THEN d.cantidad ELSE importe END AS cantidad,      
                                        c.tipo, 'Caja' AS origen, d.idConcepto
                                        FROM cajachica c 
                                        LEFT JOIN cajachicadetalle d On d.idCajaChica = c.idCajaChica
                                        WHERE c.tipoDeCliente = 1 AND c.nombre = :idProveedor AND d.idConcepto IN $idSubcuentas) AS tablaCaja
                                        UNION
                                        SELECT '' as idAuxiliar, fecha, descripcion, '' AS referencia,'' AS nombreBanco, '' AS nombreCuenta, cantidad, '' as neto, '' as precio, '' as totalCompra, tipo, origen, '' AS idConcepto
                                        FROM (SELECT g.fecha, g.concepto AS descripcion, g.cantidad, '1' AS tipo, 'Gastos' AS origen
                                        FROM gastosrealizados g 
                                        WHERE g.idProveedor = :idProveedor) AS gastosRealizados
                                        UNION
                                        SELECT '' as idAuxiliar, fecha, descripcion, '' AS referencia, '' AS nombreBanco, '' AS nombreCuenta, cantidad, '' as neto, '' as precio, '' as totalCompra, 
                                        tipo, origen, idConcepto
                                        FROM (SELECT aec.fecha,adc.descripcion, adc.importe AS cantidad, 'AlmacenCeraApicola' AS origen, tipo, adc.clasificacion AS idConcepto
                                        FROM almacenencabezadocera aec
                                        INNER JOIN almacencera adc ON aec.idAlmacen = adc.idAlmacenEncabezado
                                        WHERE aec.tipoPersona = 1 AND aec.idProveedor = :idProveedor AND (CASE WHEN aec.tipo = 1 THEN (adc.clasificacion = 2 OR adc.clasificacion = 3) ELSE adc.clasificacion >= 1 END )) tablaCera
                                        UNION
                                        SELECT '' as idAuxiliar, fecha, descripcion, '' AS referencia, '' AS nombreBanco, '' AS nombreCuenta, cantidad, '' as neto, '' as precio, '' as totalCompra, 
                                        tipo, origen, idConcepto
                                        FROM (SELECT aea.fecha,aa.descripcion, aa.importe AS cantidad, 'AlmacenCeraApicola' AS origen, tipo, aa.clasificacion AS idConcepto
                                        FROM almacenencabezadoapicola aea
                                        INNER JOIN almacenapicola aa ON aea.idAlmacen = aa.idAlmacenEncabezado
                                        WHERE aea.tipoPersona = 1 AND aea.idProveedor = :idProveedor  AND (CASE WHEN aea.tipo = 1 THEN (aa.clasificacion = 2 OR aa.clasificacion = 3) ELSE aa.clasificacion >= 1 END)) tablaProApicolas
                                        UNION
                                        SELECT '' as idAuxiliar, fecha, descripcion, referencia,'' AS nombreBanco, '' AS nombreCuenta, cantidad, neto, precio, totalCompra, tipo, origen, '' AS idConcepto
                                        FROM(SELECT am.idAlmacen AS referencia, am.fecha, 'Ingreso directo de miel 100% pura' AS descripcion, '' as cantidad,
                                        SUM(al.neto) AS neto, al.precio, am.totalCompra, '0' AS tipo, 'Entrada' AS origen
                                        FROM almacen al
                                        LEFT JOIN almacenencabezado am ON am.idAlmacen = al.idAlmacenEncabezado
                                        WHERE am.idProveedor = :idProveedor
                                        GROUP BY am.idAlmacen) AS almacen
                                        UNION
                                        SELECT '' as idAuxiliar, fecha, descripcion, referencia,'' AS nombreBanco, '' AS nombreCuenta, cantidad, neto, precio, totalCompra, tipo, origen, '' AS idConcepto
                                        FROM(SELECT ce.idAlmacen AS referencia, ce.fecha, 'Ingreso directo de miel 100% pura' AS descripcion, '' as cantidad,
                                        SUM(cd.neto) AS neto, cd.precio, ce.totalCompra, '0' AS tipo, 'EntradaCubeta' AS origen
                                        FROM cubetasdetalle cd
                                        LEFT JOIN cubetasencabezado ce ON ce.idAlmacen = cd.idAlmacenEncabezado
                                        WHERE ce.idProveedor = :idProveedor AND ce.folioEntradaTambor = 99999
                                        GROUP BY ce.idAlmacen) AS almacenCubetas
                                        UNION
                                        SELECT '' as idAuxiliar, fecha, descripcion, referencia,'' AS nombreBanco, '' AS nombreCuenta, cantidad, neto, precio, totalCompra, tipo, origen, '' AS idConcepto
                                        FROM(SELECT am.idAlmacen AS referencia, am.fecha, 'Ingreso directo de miel 100% orgánica' AS descripcion, '' as cantidad,
                                        SUM(al.neto) AS neto, al.precio, am.totalCompra, '0' AS tipo, 'Entrada Organico' AS origen
                                        FROM almacen_organico al
                                        LEFT JOIN almacenencabezado_organico am ON am.idAlmacen = al.idAlmacenEncabezado
                                        WHERE am.idProveedor = :idProveedor
                                        GROUP BY am.idAlmacen) AS almacen
                                        UNION
                                        SELECT '' as idAuxiliar, fecha, descripcion, referencia,'' AS nombreBanco, '' AS nombreCuenta, cantidad, neto, precio, totalCompra, tipo, origen, '' AS idConcepto
                                        FROM(SELECT ce.idAlmacen AS referencia, ce.fecha, 'Ingreso directo de miel 100% orgánica' AS descripcion, '' as cantidad,
                                        SUM(cd.neto) AS neto, cd.precio, ce.totalCompra, '0' AS tipo, 'Entrada Cubeta Organico' AS origen
                                        FROM cubetasdetalle_organico cd
                                        LEFT JOIN cubetasencabezado_organico ce ON ce.idAlmacen = cd.idAlmacenEncabezado
                                        WHERE ce.idProveedor = :idProveedor AND ce.folioEntradaTambor = 99999
                                        GROUP BY ce.idAlmacen) AS almacenCubetas
                                        UNION
                                        SELECT idAuxiliar, fecha, descripcion, referencia, nombreBanco, nombreCuenta, cantidad,'' as neto, '' as precio, '' as totalCompra, tipo, origen, '' AS idConcepto
                                        FROM (SELECT a.idAuxiliar, a.fecha, a.descripcion, a.referencia, a.idBanco AS nombreBanco, a.idCuenta AS nombreCuenta, a.cantidad, a.ingresoEgreso AS tipo, 'devolucionBanco' AS origen
                                        FROM auxiliardebancos a
                                        WHERE a.ingresoEgreso = 0 AND a.idSubcuenta IN $idSubcuentas AND a.tipoDePersona = 1 AND a.nombreDe = :idProveedor
                                        ) AS devolucionBancos
                                        ORDER BY fecha ASC");
        $sqlMovimientosProveedor->bindParam(':idProveedor', $idProveedor);
        $sqlMovimientosProveedor->execute();
        // $proveedor['arrayDeudores'] = [];
        $entradas = 0;
        $salidas = 0;
        $totalBancos = 0; // Si
        $totalCaja = 0;
        $totalKilos = 0;
        $totalNetoCubetas = 0;
        $totalImporte = 0;
        $totalPrecio = 0;
        $totalDevolucionesMiel = 0;
        $totalGastosRealizados = 0;
        $totalIngresosCeraApicola = 0;
        $totalEgresosCeraApicola = 0;
        $totalDevolucionesCeraApicola = 0;
        $totalVentasCeraApicola = 0;
        $proveedor['saldoInicial'] = $saldoInicial;

        foreach ($sqlMovimientosProveedor->fetchAll(PDO::FETCH_ASSOC) as $movimiento) {
            $movimiento['importe'] = is_numeric($movimiento['totalCompra']) ? $movimiento['totalCompra'] : 0;
            $totalKilos += is_numeric($movimiento['neto']) ? $movimiento['neto'] : 0;
            $totalImporte += is_numeric($movimiento['totalCompra']) ? $movimiento['totalCompra'] : 0;
            if ($movimiento['origen'] == 'Banco') {
                $movimiento['banco'] = $movimiento['cantidad'];
                $entradas += $movimiento['cantidad'];
                $totalBancos += $movimiento['cantidad']; //Si
                $movimiento['cheque'] = $movimiento['referencia'];
                $sqlBancoCuenta = $con->prepare("SELECT b.banco AS deBanco, c.numDeCuenta AS deCuenta
                                                FROM bancos b 
                                                INNER JOIN cuentasbancarias c ON c.idBanco = b.idBanco
                                                WHERE b.idBanco = :nombreBanco AND c.idCuenta = :nombreCuenta");
                $sqlBancoCuenta->bindParam(':nombreBanco', $movimiento['nombreBanco']);
                $sqlBancoCuenta->bindParam(':nombreCuenta', $movimiento['nombreCuenta']);
                $sqlBancoCuenta->execute();
                $sqlBancoCuenta->bindColumn('deBanco', $movimiento['deBanco']);
                $sqlBancoCuenta->bindColumn('deCuenta', $movimiento['deCuenta']);
                $sqlBancoCuenta->fetch(PDO::FETCH_BOUND);
            } else if ($movimiento['origen'] == 'Caja') {
                if ($movimiento['tipo'] == '1') {
                    $movimiento['caja'] = $movimiento['cantidad'];
                    $totalCaja += $movimiento['cantidad'];
                    $entradas += $movimiento['cantidad'];
                    // switch ($movimiento['idConcepto']) {
                    //     case 1:
                    //         // $movimiento['caja'] = $movimiento['cantidad'];
                    //         // $totalCaja += $movimiento['cantidad'];
                    //         // $entradas += $movimiento['cantidad'];
                    //         break;
                    //     case 2:
                    //         // $movimiento['caja'] = $movimiento['cantidad'];
                    //         // $totalCaja += $movimiento['cantidad'];
                    //         // $entradas += $movimiento['cantidad'];
                    //         break;
                    //     case 3:
                    //         // $movimiento['caja'] = $movimiento['cantidad'];
                    //         // $totalCaja += $movimiento['cantidad'];
                    //         // $entradas += $movimiento['cantidad'];
                    //         break;
                    // }
                } else if ($movimiento['tipo'] == '0') {
                    $salidas += $movimiento['cantidad'];
                    $movimiento['ventasCeraApicola'] = $movimiento['cantidad'];
                    $totalVentasCeraApicola += $movimiento['cantidad'];
                    // switch ($movimiento['idConcepto']) {
                    //     case 2:
                    //         // $movimiento['ventasCeraApicola'] = $movimiento['cantidad'];
                    //         // $totalVentasCeraApicola += $movimiento['cantidad'];
                    //         // $salidas += $movimiento['cantidad'];
                    //         break;
                    //     case 3:
                    //         // $movimiento['ventasCeraApicola'] = $movimiento['cantidad'];
                    //         // $totalVentasCeraApicola += $movimiento['cantidad'];
                    //         // $salidas += $movimiento['cantidad'];
                    //         break;
                    //     case 7:
                    //         // $movimiento['devolucionesMiel'] = $movimiento['cantidad'];
                    //         // $totalDevolucionesMiel += $movimiento['cantidad'];
                    //         // $salidas += $movimiento['cantidad'];
                    //         break;
                    // }
                }
                $movimiento['cheque'] = $movimiento['referencia'];
                $movimiento['deBanco'] = '';
                $movimiento['deCuenta'] = '';
            } else if ($movimiento['origen'] == 'devolucionBanco') {
                // $movimiento['devolucionesMiel'] = $movimiento['cantidad'];
                // $totalDevolucionesMiel += $movimiento['cantidad'];
                $salidas += $movimiento['cantidad'];
                $movimiento['cheque'] = $movimiento['referencia'];
                $movimiento['deBanco'] = '';
                $movimiento['deCuenta'] = '';
            } else if ($movimiento['origen'] == 'Entrada') {
                $movimiento['entrada'] = $movimiento['referencia'];
                $movimiento['deBanco'] = '';
                $movimiento['deCuenta'] = '';
                $sqlConCubeta = $con->prepare("SELECT SUM(cd.neto) AS netoCubeta, ce.totalCompra
                                                            FROM cubetasdetalle cd 
                                                            LEFT JOIN cubetasencabezado ce ON ce.idAlmacen = cd.idAlmacenEncabezado
                                                            WHERE ce.folioEntradaTambor = :id");
                $sqlConCubeta->bindParam(':id', $movimiento['referencia']);
                $sqlConCubeta->execute();
                $sqlConCubeta->bindColumn('netoCubeta', $movimiento['netoCubeta']);
                $sqlConCubeta->bindColumn('totalCompra', $movimiento['totalCompraCC']);
                $sqlConCubeta->fetch(PDO::FETCH_BOUND);
                if ($movimiento['netoCubeta'] != 0) {
                    $movimiento['kilosNeto'] = $movimiento['neto'] + $movimiento['netoCubeta'];
                    $totalNetoCubetas += $movimiento['netoCubeta'];
                } else {
                    $movimiento['kilosNeto'] = $movimiento['neto'];
                }
                $movimiento['precioPromedio'] = $movimiento['importe'] / $movimiento['kilosNeto'];
                $salidas += $movimiento['importe'];
            } else if ($movimiento['origen'] == 'EntradaCubeta') {
                $movimiento['entrada'] = $movimiento['referencia'];
                $movimiento['deBanco'] = '';
                $movimiento['deCuenta'] = '';
                $movimiento['kilosNeto'] = $movimiento['neto'];
                $movimiento['precioPromedio'] = $movimiento['importe'] / $movimiento['kilosNeto'];
                $salidas += $movimiento['importe'];
            } else if ($movimiento['origen'] == 'Entrada Organico') {
                $movimiento['entrada'] = $movimiento['referencia'];
                $movimiento['deBanco'] = '';
                $movimiento['deCuenta'] = '';
                $sqlConCubeta = $con->prepare("SELECT SUM(cd.neto) AS netoCubeta, ce.totalCompra
                                                            FROM cubetasdetalle_organico cd 
                                                            LEFT JOIN cubetasencabezado_organico ce ON ce.idAlmacen = cd.idAlmacenEncabezado
                                                            WHERE ce.folioEntradaTambor = :id");
                $sqlConCubeta->bindParam(':id', $movimiento['referencia']);
                $sqlConCubeta->execute();
                $sqlConCubeta->bindColumn('netoCubeta', $movimiento['netoCubeta']);
                $sqlConCubeta->bindColumn('totalCompra', $movimiento['totalCompraCC']);
                $sqlConCubeta->fetch(PDO::FETCH_BOUND);
                if ($movimiento['netoCubeta'] != 0) {
                    $movimiento['kilosNeto'] = $movimiento['neto'] + $movimiento['netoCubeta'];
                    $totalNetoCubetas += $movimiento['netoCubeta'];
                } else {
                    $movimiento['kilosNeto'] = $movimiento['neto'];
                }
                $movimiento['precioPromedio'] = $movimiento['importe'] / $movimiento['kilosNeto'];
                $salidas += $movimiento['importe'];
            } else if ($movimiento['origen'] == 'Entrada Cubeta Organico') {
                $movimiento['entrada'] = $movimiento['referencia'];
                $movimiento['deBanco'] = '';
                $movimiento['deCuenta'] = '';
                $movimiento['kilosNeto'] = $movimiento['neto'];
                $movimiento['precioPromedio'] = $movimiento['importe'] / $movimiento['kilosNeto'];
                $salidas += $movimiento['importe'];
            } else if ($movimiento['origen'] == 'Gastos') {
                $totalGastosRealizados += $movimiento['cantidad'];
                $movimiento['gastos'] = $movimiento['cantidad'];
                $salidas += $movimiento['cantidad'];
            } else if ($movimiento['origen'] == 'AlmacenCeraApicola') {
                if ($movimiento['tipo'] == '1') {   //Entradas (Egresos)
                    $salidas += $movimiento['cantidad'];
                    $totalEgresosCeraApicola += $movimiento['cantidad'];
                    $movimiento['egresosCeraApicola'] = $movimiento['cantidad'];
                    // switch ($movimiento['idConcepto']) {
                    //     case 2: //devolución
                    //         // $totalDevolucionesCeraApicola += $movimiento['cantidad'];
                    //         // $movimiento['devolucionesCeraApicola'] = $movimiento['cantidad'];
                    //         // $salidas += $movimiento['cantidad'];
                    //         break;
                    //     case 3: //compra
                    //         // $totalEgresosCeraApicola += $movimiento['cantidad'];
                    //         // $movimiento['egresosCeraApicola'] = $movimiento['cantidad'];
                    //         // $salidas += $movimiento['cantidad'];
                    //         break;
                    // }
                } else if ($movimiento['tipo'] == '2') {  //Salidas (Ingresos)
                    $totalIngresosCeraApicola += $movimiento['cantidad'];
                    $movimiento['ingresosCeraApicola'] = $movimiento['cantidad'];
                    $entradas += $movimiento['cantidad'];
                }
            } else {
                $salidas += $movimiento['importe'];
                if ($totalKilos != 0) {
                    $totalPrecio = $totalImporte / $totalKilos;
                }
            }
            if ($totalKilos != 0) {
                $totalPrecio = $totalImporte / $totalKilos;
            }

            $movimiento['saldo'] = $saldoInicial + $entradas - $salidas;
            
            // array_push($proveedor['arrayDeudores'], $movimiento);

        }

        $proveedor['saldoInicial'] = $saldoInicial;
        $proveedor['totalBancos'] = $totalBancos;
        $proveedor['totalCaja'] = $totalCaja;
        $proveedor['totalKilos'] = $totalKilos + $totalNetoCubetas;
        $proveedor['totalImporte'] = $totalImporte;
        $proveedor['totalPrecio'] = $totalPrecio;
        $proveedor['totalDevolucionesMiel'] = $totalDevolucionesMiel;
        $proveedor['totalIngresosCeraApicola'] = $totalIngresosCeraApicola;
        $proveedor['totalEgresosCeraApicola'] = $totalEgresosCeraApicola;
        $proveedor['totalDevolucionesCeraApicola'] = $totalDevolucionesCeraApicola;
        $proveedor['totalGastos'] = $totalGastosRealizados;
        $proveedor['totalVentasCeraApicola'] = $totalVentasCeraApicola;
        $proveedor['totalSaldo'] = $saldoInicial + $totalBancos + $totalCaja - $totalImporte - $totalDevolucionesMiel + $totalIngresosCeraApicola - $totalEgresosCeraApicola - $totalDevolucionesCeraApicola - $totalGastosRealizados - $totalVentasCeraApicola;
        array_push($lista_proveedores, $proveedor);

    } // Foreach proveedores


    foreach ($lista_proveedores as $proveedor) {
// hacer las operaciones para sacar los resultados
        if ($proveedor['totalSaldo'] === 0) {
            $proveedor['saldoDeudor'] = 0;
            $proveedor['saldoProveedor'] = 0;
        } else if ($proveedor['cantidad'] === 0) {
            $proveedor['saldoDeudor'] = 0;
            $proveedor['saldoProveedor'] = 0;
        } else if ($proveedor['totalSaldo'] > 0) {
            $proveedor['saldoDeudor'] = floatval($proveedor['totalSaldo']);
            $proveedor['saldoProveedor'] = 0;
            $resultado['totalSaldoDeudor'] += floatval($proveedor['totalSaldo']);
        } else if ($proveedor['totalSaldo'] < 0) {
            $proveedor['saldoProveedor'] = floatval($proveedor['totalSaldo']);
            $proveedor['saldoDeudor'] = 0;
            $resultado['totalSaldoProveedor'] += floatval($proveedor['totalSaldo']);
        } else if ($proveedor['cantidad'] > 0) {
            $proveedor['saldoProveedor'] = 0;
            $proveedor['saldoDeudor'] = floatval($proveedor['cantidad']);
            $resultado['totalSaldoDeudor'] += floatval($proveedor['saldoDeudor']);
        } else if ($proveedor['cantidad'] < 0) {
            $proveedor['saldoProveedor'] = floatval($proveedor['cantidad']);
            $proveedor['saldoDeudor'] = 0;
            $resultado['totalSaldoProveedor'] += floatval($proveedor['cantidad']);
        }
    }

    $resultado['conciliacion'] = $resultado['totalSaldoDeudor'] + $resultado['totalSaldoProveedor'];
    return $resultado;

}
