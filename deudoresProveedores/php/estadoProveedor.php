<?php

include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$con = $pdo->conectar();
$con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

function obtenerEstadoDeCuentaProveedor($idProveedor = false)
{
    global $con;
    if (!$idProveedor) {
        throw new Exception('La función requiere como único parámetro el ID del proveedor');
    }
    $resultado = array();
    $datosEncabezadoProveedorSql = $con->prepare("SELECT p.nombre, p.idProveedor, p.cantidad, p.idEstado
    FROM proveedor p
    WHERE p.empresa = 0 AND idProveedor = :idProveedor
    ORDER BY p.nombre ASC");
    $datosEncabezadoProveedorSql->bindParam(':idProveedor', $idProveedor);
    $datosEncabezadoProveedorSql->execute();
    if ($datosEncabezadoProveedorSql == false) {
        throw new Exception($con->errorInfo());
    } else {
        $datosProveedor = $datosEncabezadoProveedorSql->fetch(PDO::FETCH_ASSOC);
        $saldoInicial = $datosProveedor['cantidad'];
        $datosProveedor['saldoInicial'] = $saldoInicial;
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


    $sqlDetalle = $con->prepare("SELECT idAuxiliar, fecha, descripcion, referencia, nombreBanco, nombreCuenta, cantidad,'' as neto, '' as precio, '' as totalCompra, tipo, origen, '' AS idConcepto
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
                        SELECT '' as idAuxiliar, fecha, descripcion, '' AS referencia,'' AS nombreBanco, '' AS nombreCuenta, cantidad, '' as neto, '' as precio, '' as totalCompra, tipo, origen, '' AS idConcepto
                        FROM (SELECT g.fecha, g.concepto AS descripcion, g.cantidad, '1' AS tipo, 'Retenciones' AS origen
                        FROM retencionesisr g 
                        WHERE g.idProveedor = :idProveedor) AS gastosRealizados
                        UNION
                        SELECT '' as idAuxiliar, fecha, descripcion, '' AS referencia, '' AS nombreBanco, '' AS nombreCuenta, cantidad, '' as neto, '' as precio, '' as totalCompra, 
                        tipo, origen, idConcepto
                        FROM (SELECT aec.fecha,adc.descripcion, adc.importe AS cantidad, 'AlmacenCeraApicola' AS origen, tipo, adc.clasificacion AS idConcepto
                        FROM almacenencabezadocera aec
                        INNER JOIN almacencera adc ON aec.idAlmacen = adc.idAlmacenEncabezado
                        WHERE aec.tipoPersona = 1 AND aec.idProveedor = :idProveedor AND (CASE WHEN aec.tipo = 1 THEN (adc.clasificacion = 2 OR adc.clasificacion = 3) ELSE adc.clasificacion >= 1 END )) tablaCera
                        UNION
                        SELECT idAuxiliar, fecha, descripcion, '' AS referencia, '' AS nombreBanco, '' AS nombreCuenta, cantidad, '' as neto, '' as precio, '' as totalCompra, 
                        tipo, origen, idConcepto
                        FROM (SELECT aa.idAlmacen AS idAuxiliar, aea.fecha,aa.descripcion, aa.importe AS cantidad, 'AlmacenCeraApicola' AS origen, tipo, aa.clasificacion AS idConcepto
                        FROM almacenencabezadoapicola aea
                        INNER JOIN almacenapicola aa ON aea.idAlmacen = aa.idAlmacenEncabezado
                        WHERE aea.tipoPersona = 1 AND aea.idProveedor = :idProveedor  AND (CASE WHEN aea.tipo = 1 THEN (aa.clasificacion = 2 OR aa.clasificacion = 3) ELSE aa.clasificacion >= 1 END)) tablaProApicolas
                        UNION
                        SELECT idAuxiliar, fecha, descripcion, '' AS referencia, '' AS nombreBanco, '' AS nombreCuenta, cantidad, '' as neto, '' as precio, '' as totalCompra, tipo, origen, idConcepto
                        FROM (SELECT aa.idVenta AS idAuxiliar, aea.fecha,aa.descripcion, aa.total as cantidad, 'AlmacenCeraApicola' AS origen, 2 as tipo, '' AS idConcepto
                        FROM encabezado_cotizacion aea
                        INNER JOIN cotizacion_detalle aa ON aea.idVenta = aa.idVenta
                        WHERE aea.idCliente = :idProveedor AND aea.exportador = 'APICULTOR' AND aea.estado = 'ENTREGADO' AND aa.idSubSubCuenta = 213) tablaProApicolas
                        UNION
                        SELECT '' as idAuxiliar, fecha, descripcion, referencia,'' AS nombreBanco, '' AS nombreCuenta, cantidad, neto, precio, totalCompra, tipo, origen, '' AS idConcepto
                        FROM(SELECT am.idAlmacen AS referencia, am.fecha, 'Ingreso directo de miel 100% pura' AS descripcion, '' as cantidad,
                        SUM(al.neto) AS neto, al.precio, am.totalCompra, '0' AS tipo, 'Entrada' AS origen
                        FROM almacen al
                        LEFT JOIN almacenencabezado am ON am.idAlmacen = al.idAlmacenEncabezado
                        WHERE am.idProveedor = :idProveedor AND al.referencia = 0
                        GROUP BY am.idAlmacen) AS almacen
                        UNION
                        SELECT '' as idAuxiliar, fecha, descripcion, referencia,'' AS nombreBanco, '' AS nombreCuenta, cantidad, neto, precio, totalCompra, tipo, origen, '' AS idConcepto
                        FROM(SELECT ce.idAlmacen AS referencia, ce.fecha, 'Ingreso directo de miel 100% pura' AS descripcion, '' as cantidad,
                        SUM(cd.neto) AS neto, cd.precio, ce.totalCompra, '0' AS tipo, 'EntradaCubeta' AS origen
                        FROM cubetasdetalle cd
                        LEFT JOIN cubetasencabezado ce ON ce.idAlmacen = cd.idAlmacenEncabezado
                        WHERE ce.idProveedor = :idProveedor AND ce.folioEntradaTambor = 99999 AND cd.referencia = 0
                        GROUP BY ce.idAlmacen) AS almacenCubetas
                        UNION
                        SELECT '' as idAuxiliar, fecha, descripcion, referencia,'' AS nombreBanco, '' AS nombreCuenta, cantidad, neto, precio, totalCompra, tipo, origen, '' AS idConcepto
                        FROM(SELECT am.idAlmacen AS referencia, am.fecha, 'Ingreso directo de miel 100% orgánica' AS descripcion, '' as cantidad,
                        SUM(al.neto) AS neto, al.precio, am.totalCompra, '0' AS tipo, 'Entrada Organico' AS origen
                        FROM almacen_organico al
                        LEFT JOIN almacenencabezado_organico am ON am.idAlmacen = al.idAlmacenEncabezado
                        WHERE am.idProveedor = :idProveedor AND al.referencia = 0
                        GROUP BY am.idAlmacen) AS almacen
                        UNION
                        SELECT '' as idAuxiliar, fecha, descripcion, referencia,'' AS nombreBanco, '' AS nombreCuenta, cantidad, neto, precio, totalCompra, tipo, origen, '' AS idConcepto
                        FROM(SELECT ce.idAlmacen AS referencia, ce.fecha, 'Ingreso directo de miel 100% orgánica' AS descripcion, '' as cantidad,
                        SUM(cd.neto) AS neto, cd.precio, ce.totalCompra, '0' AS tipo, 'Entrada Cubeta Organico' AS origen
                        FROM cubetasdetalle_organico cd
                        LEFT JOIN cubetasencabezado_organico ce ON ce.idAlmacen = cd.idAlmacenEncabezado
                        WHERE ce.idProveedor = :idProveedor AND ce.folioEntradaTambor = 99999 AND cd.referencia = 0
                        GROUP BY ce.idAlmacen) AS almacenCubetas
                        UNION
                        SELECT '' as idAuxiliar, fecha, descripcion, referencia,'' AS nombreBanco, '' AS nombreCuenta, cantidad, neto, precio, totalCompra, tipo, origen, '' AS idConcepto
                        FROM(SELECT am.idAlmacen AS referencia, am.fecha, 'Ingreso directo de miel 100% mantequilla' AS descripcion, '' as cantidad,
                        SUM(al.neto) AS neto, al.precio, am.totalCompra, '0' AS tipo, 'Entrada Mantequilla' AS origen
                        FROM almacen_mantequilla al
                        LEFT JOIN almacenencabezado_mantequilla am ON am.idAlmacen = al.idAlmacenEncabezado
                        WHERE am.idProveedor = :idProveedor AND al.referencia = 0
                        GROUP BY am.idAlmacen) AS almacen
                        UNION
                        SELECT '' as idAuxiliar, fecha, descripcion, referencia,'' AS nombreBanco, '' AS nombreCuenta, cantidad, neto, precio, totalCompra, tipo, origen, '' AS idConcepto
                        FROM(SELECT ce.idAlmacen AS referencia, ce.fecha, 'Ingreso directo de miel 100% mantequilla' AS descripcion, '' as cantidad,
                        SUM(cd.neto) AS neto, cd.precio, ce.totalCompra, '0' AS tipo, 'Entrada Cubeta Mantequilla' AS origen
                        FROM cubetasdetalle_mantequilla cd
                        LEFT JOIN cubetasencabezado_mantequilla ce ON ce.idAlmacen = cd.idAlmacenEncabezado
                        WHERE ce.idProveedor = :idProveedor AND ce.folioEntradaTambor = 99999 AND cd.referencia = 0
                        GROUP BY ce.idAlmacen) AS almacenCubetas
                        UNION

                        SELECT '' as idAuxiliar, fecha, descripcion, referencia,'' AS nombreBanco, '' AS nombreCuenta, cantidad, neto, precio, totalCompra, tipo, origen, '' AS idConcepto
                        FROM(SELECT am.idAlmacen AS referencia, am.fecha, 'Ingreso directo de miel 100% altiplano' AS descripcion, '' as cantidad,
                        SUM(al.neto) AS neto, al.precio, am.totalCompra, '0' AS tipo, 'Entrada Altiplano' AS origen
                        FROM almacen_altiplano al
                        LEFT JOIN almacenencabezado_altiplano am ON am.idAlmacen = al.idAlmacenEncabezado
                        WHERE am.idProveedor = :idProveedor AND al.referencia = 0
                        GROUP BY am.idAlmacen) AS almacen
                        UNION
                        SELECT '' as idAuxiliar, fecha, descripcion, referencia,'' AS nombreBanco, '' AS nombreCuenta, cantidad, neto, precio, totalCompra, tipo, origen, '' AS idConcepto
                        FROM(SELECT ce.idAlmacen AS referencia, ce.fecha, 'Ingreso directo de miel 100% altiplano' AS descripcion, '' as cantidad,
                        SUM(cd.neto) AS neto, cd.precio, ce.totalCompra, '0' AS tipo, 'Entrada Cubeta Altiplano' AS origen
                        FROM cubetasdetalle_altiplano cd
                        LEFT JOIN cubetasencabezado_altiplano ce ON ce.idAlmacen = cd.idAlmacenEncabezado
                        WHERE ce.idProveedor = :idProveedor AND ce.folioEntradaTambor = 99999 AND cd.referencia = 0
                        GROUP BY ce.idAlmacen) AS almacenCubetas
                        UNION

                        SELECT '' as idAuxiliar, fecha, descripcion, referencia,'' AS nombreBanco, '' AS nombreCuenta, cantidad, neto, precio, totalCompra, tipo, origen, '' AS idConcepto
                        FROM(SELECT am.idAlmacen AS referencia, am.fecha, 'Ingreso directo de miel 100% aguacate' AS descripcion, '' as cantidad,
                        SUM(al.neto) AS neto, al.precio, am.totalCompra, '0' AS tipo, 'Entrada Aguacate' AS origen
                        FROM almacen_aguacate al
                        LEFT JOIN almacenencabezado_aguacate am ON am.idAlmacen = al.idAlmacenEncabezado
                        WHERE am.idProveedor = :idProveedor AND al.referencia = 0
                        GROUP BY am.idAlmacen) AS almacen
                        UNION
                        SELECT '' as idAuxiliar, fecha, descripcion, referencia,'' AS nombreBanco, '' AS nombreCuenta, cantidad, neto, precio, totalCompra, tipo, origen, '' AS idConcepto
                        FROM(SELECT ce.idAlmacen AS referencia, ce.fecha, 'Ingreso directo de miel 100% aguacate' AS descripcion, '' as cantidad,
                        SUM(cd.neto) AS neto, cd.precio, ce.totalCompra, '0' AS tipo, 'Entrada Cubeta Aguacate' AS origen
                        FROM cubetasdetalle_aguacate cd
                        LEFT JOIN cubetasencabezado_aguacate ce ON ce.idAlmacen = cd.idAlmacenEncabezado
                        WHERE ce.idProveedor = :idProveedor AND ce.folioEntradaTambor = 99999 AND cd.referencia = 0
                        GROUP BY ce.idAlmacen) AS almacenCubetas
                        UNION

                        SELECT '' as idAuxiliar, fecha, descripcion, referencia,'' AS nombreBanco, '' AS nombreCuenta, cantidad, neto, precio, totalCompra, tipo, origen, '' AS idConcepto
                        FROM(SELECT am.idAlmacen AS referencia, am.fecha, 'Ingreso directo de miel 100% mezquite' AS descripcion, '' as cantidad,
                        SUM(al.neto) AS neto, al.precio, am.totalCompra, '0' AS tipo, 'Entrada Mezquite' AS origen
                        FROM almacen_mezquite al
                        LEFT JOIN almacenencabezado_mezquite am ON am.idAlmacen = al.idAlmacenEncabezado
                        WHERE am.idProveedor = :idProveedor AND al.referencia = 0
                        GROUP BY am.idAlmacen) AS almacen
                        UNION
                        SELECT '' as idAuxiliar, fecha, descripcion, referencia,'' AS nombreBanco, '' AS nombreCuenta, cantidad, neto, precio, totalCompra, tipo, origen, '' AS idConcepto
                        FROM(SELECT ce.idAlmacen AS referencia, ce.fecha, 'Ingreso directo de miel 100% mezquite' AS descripcion, '' as cantidad,
                        SUM(cd.neto) AS neto, cd.precio, ce.totalCompra, '0' AS tipo, 'Entrada Cubeta Mezquite' AS origen
                        FROM cubetasdetalle_mezquite cd
                        LEFT JOIN cubetasencabezado_mezquite ce ON ce.idAlmacen = cd.idAlmacenEncabezado
                        WHERE ce.idProveedor = :idProveedor AND ce.folioEntradaTambor = 99999 AND cd.referencia = 0
                        GROUP BY ce.idAlmacen) AS almacenCubetas
                        UNION

                        SELECT '' as idAuxiliar, fecha, descripcion, referencia,'' AS nombreBanco, '' AS nombreCuenta, cantidad, neto, precio, totalCompra, tipo, origen, '' AS idConcepto
                        FROM(SELECT am.idAlmacen AS referencia, am.fecha, 'Ingreso directo de miel 100% naranjo' AS descripcion, '' as cantidad,
                        SUM(al.neto) AS neto, al.precio, am.totalCompra, '0' AS tipo, 'Entrada Naranjo' AS origen
                        FROM almacen_naranjo al
                        LEFT JOIN almacenencabezado_naranjo am ON am.idAlmacen = al.idAlmacenEncabezado
                        WHERE am.idProveedor = :idProveedor AND al.referencia = 0
                        GROUP BY am.idAlmacen) AS almacen
                        UNION
                        SELECT '' as idAuxiliar, fecha, descripcion, referencia,'' AS nombreBanco, '' AS nombreCuenta, cantidad, neto, precio, totalCompra, tipo, origen, '' AS idConcepto
                        FROM(SELECT ce.idAlmacen AS referencia, ce.fecha, 'Ingreso directo de miel 100% naranjo' AS descripcion, '' as cantidad,
                        SUM(cd.neto) AS neto, cd.precio, ce.totalCompra, '0' AS tipo, 'Entrada Cubeta Naranjo' AS origen
                        FROM cubetasdetalle_naranjo cd
                        LEFT JOIN cubetasencabezado_naranjo ce ON ce.idAlmacen = cd.idAlmacenEncabezado
                        WHERE ce.idProveedor = :idProveedor AND ce.folioEntradaTambor = 99999 AND cd.referencia = 0
                        GROUP BY ce.idAlmacen) AS almacenCubetas
                        UNION
                        SELECT idAuxiliar, fecha, descripcion, referencia, nombreBanco, nombreCuenta, cantidad,'' as neto, '' as precio, '' as totalCompra, tipo, origen, '' AS idConcepto
                        FROM (SELECT a.idAuxiliar, a.fecha, a.descripcion, a.referencia, a.idBanco AS nombreBanco, a.idCuenta AS nombreCuenta, a.cantidad, a.ingresoEgreso AS tipo, 'devolucionBanco' AS origen
                        FROM auxiliardebancos a
                        WHERE a.ingresoEgreso = 0 AND a.idSubcuenta IN $idSubcuentas AND a.tipoDePersona = 1 AND a.nombreDe = :idProveedor
                        ) AS devolucionBancos
                        ORDER BY fecha ASC");
    $sqlDetalle->bindParam(':idProveedor', $idProveedor);
    $sqlDetalle->execute();
    if ($sqlDetalle == false) {
        throw new Exception($con->errorInfo());
    } else {

        $datosProveedor['arrayDeudores'] = [];
        $datosProveedor['totalBancos'] = 0;
        $datosProveedor['totalCaja'] = 0;
        $datosProveedor['totalNetoCubetas'] = 0;
        $datosProveedor['totalKilos'] = 0;
        $datosProveedor['totalImporte'] = 0;
        $datosProveedor['totalPrecio'] = 0;
        $datosProveedor['totalDevolucionesMiel'] = 0;
        $datosProveedor['totalIngresosCeraApicola'] = 0;
        $datosProveedor['totalEgresosCeraApicola'] = 0;
        $datosProveedor['totalGastos'] = 0;
        $datosProveedor['totalRetenciones'] = 0;
        $datosProveedor['totalDevolucionesCeraApicola'] = 0;
        $datosProveedor['totalVentasCeraApicola'] = 0;
        $datosProveedor['totalSaldo'] = $saldoInicial;

        $entradas = 0;
        $salidas = 0;
        $retenciones = 0;
        $totalBancos = 0;
        $totalCaja = 0;
        $totalKilos = 0;
        $totalNetoCubetas = 0;
        $totalImporte = 0;
        $totalPrecio = 0;
        $totalDevolucionesMiel = 0;
        $totalGastosRealizados = 0;
        $totalRetencionesRealizadas = 0;
        $totalIngresosCeraApicola = 0;
        $totalEgresosCeraApicola = 0;
        $totalDevolucionesCeraApicola = 0;
        $totalVentasCeraApicola = 0;
    }

    foreach ($sqlDetalle->fetchAll(PDO::FETCH_ASSOC) as $data) {
        $data['deBanco'] = '';
        $data['deCuenta'] = '';
        $data['banco'] = '';
        $data['entrada'] = '';
        $data['cheque'] = '';
        $data['precioPromedio'] = '';
        $data['ingresosCeraApicola'] = '';
        $data['precioPromedio'] = '';
        $data['caja'] = '';
        $data['entrada'] = '';
        $data['egresosCeraApicola'] = '';
        $data['gastos'] = '';
        $data['retenciones'] = '';
        $data['devolucionesMiel'] = '';
        $data['devolucionesCeraApicola'] = '';
        $data['ventasCeraApicola'] = '';
        $data['kilosNeto'] = '';
        $data['ceraApicola'] = '';
        $data['desconocido'] = '';

        $data['importe'] = $data['totalCompra'];
        $totalKilos += is_numeric($data['neto']) ? $data['neto'] : 0;
        $totalImporte += is_numeric($data['totalCompra']) ? $data['totalCompra'] : 0;
        if ($data['origen'] == 'Banco') {
            $data['banco'] = $data['cantidad'];
            $entradas += $data['cantidad'];
            $totalBancos += $data['cantidad'];
            $data['cheque'] = $data['referencia'];
            $sqlBancoCuenta = $con->prepare("SELECT b.banco AS deBanco, c.numDeCuenta AS deCuenta
                                               FROM bancos b 
                                               INNER JOIN cuentasbancarias c ON c.idBanco = b.idBanco
                                               WHERE b.idBanco = :nombreBanco AND c.idCuenta = :nombreCuenta");
            $sqlBancoCuenta->bindParam(':nombreBanco', $data['nombreBanco']);
            $sqlBancoCuenta->bindParam(':nombreCuenta', $data['nombreCuenta']);
            $sqlBancoCuenta->execute();
            $sqlBancoCuenta->bindColumn('deBanco', $data['deBanco']);
            $sqlBancoCuenta->bindColumn('deCuenta', $data['deCuenta']);
            $sqlBancoCuenta->fetch(PDO::FETCH_BOUND);
        } else if ($data['origen'] == 'Caja') {
            if ($data['tipo'] == '1') {

                $data['caja'] = $data['cantidad'];
                $totalCaja += $data['cantidad'];
                $entradas += $data['cantidad'];
            } else if ($data['tipo'] == '0') {

                $salidas += $data['cantidad'];
                $data['caja'] = -$data['cantidad'];
                $totalCaja -= $data['cantidad'];
            }
            $data['cheque'] = $data['referencia'];
            $data['deBanco'] = '';
            $data['deCuenta'] = '';
        } else if ($data['origen'] == 'devolucionBanco') {

            $data['banco'] = -$data['cantidad'];
            $totalBancos -= $data['cantidad'];
            $salidas += $data['cantidad'];
            $data['cheque'] = $data['referencia'];
            $data['deBanco'] = '';
            $data['deCuenta'] = '';
        } else if ($data['origen'] == 'Entrada') {
            $data['entrada'] = $data['referencia'];
            $data['deBanco'] = '';
            $data['deCuenta'] = '';
            $sqlConCubeta = $con->prepare("SELECT SUM(cd.neto) AS netoCubeta, ce.totalCompra
                            FROM cubetasdetalle cd 
                            LEFT JOIN cubetasencabezado ce ON ce.idAlmacen = cd.idAlmacenEncabezado
                            WHERE ce.folioEntradaTambor = :id");
            $sqlConCubeta->bindParam(':id', $data['referencia']);
            $sqlConCubeta->execute();
            $sqlConCubeta->bindColumn('netoCubeta', $data['netoCubeta']);
            $sqlConCubeta->bindColumn('totalCompra', $data['totalCompraCC']);
            $sqlConCubeta->fetch(PDO::FETCH_BOUND);
            if ($data['netoCubeta'] > 0) {
                $data['kilosNeto'] = $data['neto'] + $data['netoCubeta'];
                $totalNetoCubetas += $data['netoCubeta'];
                $data['importe'] = $data['totalCompra'] + $data['totalCompraCC'];
                $totalImporte = $totalImporte + $data['totalCompraCC'];
            } else {
                $data['kilosNeto'] = $data['neto'];
                $data['importe'] = $data['totalCompra'];
            }
            $data['precioPromedio'] = $data['importe'] / $data['kilosNeto'];
            $salidas += $data['importe'];
        } else if ($data['origen'] == 'EntradaCubeta') {
            $data['entrada'] = $data['referencia'];
            $data['deBanco'] = '';
            $data['deCuenta'] = '';
            $data['kilosNeto'] = $data['neto'];
            $data['precioPromedio'] = $data['importe'] / $data['kilosNeto'];
            $salidas += $data['importe'];
        } else if ($data['origen'] == 'Entrada Organico') {
            $data['entrada'] = $data['referencia'];
            $data['deBanco'] = '';
            $data['deCuenta'] = '';
            $sqlConCubeta = $con->prepare("SELECT SUM(cd.neto) AS netoCubeta, ce.totalCompra
                            FROM cubetasdetalle_organico cd 
                            LEFT JOIN cubetasencabezado_organico ce ON ce.idAlmacen = cd.idAlmacenEncabezado
                            WHERE ce.folioEntradaTambor = :id");
            $sqlConCubeta->bindParam(':id', $data['referencia']);
            $sqlConCubeta->execute();
            $sqlConCubeta->bindColumn('netoCubeta', $data['netoCubeta']);
            $sqlConCubeta->bindColumn('totalCompra', $data['totalCompraCC']);
            $sqlConCubeta->fetch(PDO::FETCH_BOUND);
            if ($data['netoCubeta'] != 0) {
                $data['kilosNeto'] = $data['neto'] + $data['netoCubeta'];
                $totalNetoCubetas += $data['netoCubeta'];
                $data['importe'] = $data['totalCompra'] + $data['totalCompraCC'];
                $totalImporte = $totalImporte + $data['totalCompraCC'];
            } else {
                $data['kilosNeto'] = $data['neto'];
                $data['importe'] = $data['totalCompra'];
            }
            $data['precioPromedio'] = $data['importe'] / $data['kilosNeto'];
            $salidas += $data['importe'];
        } else if ($data['origen'] == 'Entrada Cubeta Organico') {
            $data['entrada'] = $data['referencia'];
            $data['deBanco'] = '';
            $data['deCuenta'] = '';
            $data['kilosNeto'] = $data['neto'];
            $data['precioPromedio'] = $data['importe'] / $data['kilosNeto'];
            $salidas += $data['importe'];
        } else if ($data['origen'] == 'Entrada Mantequilla') {
            $data['entrada'] = $data['referencia'];
            $data['deBanco'] = '';
            $data['deCuenta'] = '';
            $sqlConCubeta = $con->prepare("SELECT SUM(cd.neto) AS netoCubeta, ce.totalCompra
                            FROM cubetasdetalle_mantequilla cd 
                            LEFT JOIN cubetasencabezado_mantequilla ce ON ce.idAlmacen = cd.idAlmacenEncabezado
                            WHERE ce.folioEntradaTambor = :id");
            $sqlConCubeta->bindParam(':id', $data['referencia']);
            $sqlConCubeta->execute();
            $sqlConCubeta->bindColumn('netoCubeta', $data['netoCubeta']);
            $sqlConCubeta->bindColumn('totalCompra', $data['totalCompraCC']);
            $sqlConCubeta->fetch(PDO::FETCH_BOUND);
            if ($data['netoCubeta'] != 0) {
                $data['kilosNeto'] = $data['neto'] + $data['netoCubeta'];
                $totalNetoCubetas += $data['netoCubeta'];
                $data['importe'] = $data['totalCompra'] + $data['totalCompraCC'];
                $totalImporte = $totalImporte + $data['totalCompraCC'];
            } else {
                $data['kilosNeto'] = $data['neto'];
                $data['importe'] = $data['totalCompra'];
            }
            $data['precioPromedio'] = $data['importe'] / $data['kilosNeto'];
            $salidas += $data['importe'];
        } else if ($data['origen'] == 'Entrada Cubeta Mantequilla') {
            $data['entrada'] = $data['referencia'];
            $data['deBanco'] = '';
            $data['deCuenta'] = '';
            $data['kilosNeto'] = $data['neto'];
            $data['precioPromedio'] = $data['importe'] / $data['kilosNeto'];
            $salidas += $data['importe'];
        }
        else if ($data['origen'] == 'Entrada Altiplano') {
            $data['entrada'] = $data['referencia'];
            $data['deBanco'] = '';
            $data['deCuenta'] = '';
            $sqlConCubeta = $con->prepare("SELECT SUM(cd.neto) AS netoCubeta, ce.totalCompra
                            FROM cubetasdetalle_altiplano cd 
                            LEFT JOIN cubetasencabezado_altiplano ce ON ce.idAlmacen = cd.idAlmacenEncabezado
                            WHERE ce.folioEntradaTambor = :id");
            $sqlConCubeta->bindParam(':id', $data['referencia']);
            $sqlConCubeta->execute();
            $sqlConCubeta->bindColumn('netoCubeta', $data['netoCubeta']);
            $sqlConCubeta->bindColumn('totalCompra', $data['totalCompraCC']);
            $sqlConCubeta->fetch(PDO::FETCH_BOUND);
            if ($data['netoCubeta'] != 0) {
                $data['kilosNeto'] = $data['neto'] + $data['netoCubeta'];
                $totalNetoCubetas += $data['netoCubeta'];
                $data['importe'] = $data['totalCompra'] + $data['totalCompraCC'];
                $totalImporte = $totalImporte + $data['totalCompraCC'];
            } else {
                $data['kilosNeto'] = $data['neto'];
                $data['importe'] = $data['totalCompra'];
            }
            $data['precioPromedio'] = $data['importe'] / $data['kilosNeto'];
            $salidas += $data['importe'];
        } else if ($data['origen'] == 'Entrada Cubeta Altiplano') {
            $data['entrada'] = $data['referencia'];
            $data['deBanco'] = '';
            $data['deCuenta'] = '';
            $data['kilosNeto'] = $data['neto'];
            $data['precioPromedio'] = $data['importe'] / $data['kilosNeto'];
            $salidas += $data['importe'];
        }

        else if ($data['origen'] == 'Entrada Aguacate') {
            $data['entrada'] = $data['referencia'];
            $data['deBanco'] = '';
            $data['deCuenta'] = '';
            $sqlConCubeta = $con->prepare("SELECT SUM(cd.neto) AS netoCubeta, ce.totalCompra
                            FROM cubetasdetalle_aguacate cd 
                            LEFT JOIN cubetasencabezado_aguacate ce ON ce.idAlmacen = cd.idAlmacenEncabezado
                            WHERE ce.folioEntradaTambor = :id");
            $sqlConCubeta->bindParam(':id', $data['referencia']);
            $sqlConCubeta->execute();
            $sqlConCubeta->bindColumn('netoCubeta', $data['netoCubeta']);
            $sqlConCubeta->bindColumn('totalCompra', $data['totalCompraCC']);
            $sqlConCubeta->fetch(PDO::FETCH_BOUND);
            if ($data['netoCubeta'] != 0) {
                $data['kilosNeto'] = $data['neto'] + $data['netoCubeta'];
                $totalNetoCubetas += $data['netoCubeta'];
                $data['importe'] = $data['totalCompra'] + $data['totalCompraCC'];
                $totalImporte = $totalImporte + $data['totalCompraCC'];
            } else {
                $data['kilosNeto'] = $data['neto'];
                $data['importe'] = $data['totalCompra'];
            }
            $data['precioPromedio'] = $data['importe'] / $data['kilosNeto'];
            $salidas += $data['importe'];
        } else if ($data['origen'] == 'Entrada Cubeta Aguacate') {
            $data['entrada'] = $data['referencia'];
            $data['deBanco'] = '';
            $data['deCuenta'] = '';
            $data['kilosNeto'] = $data['neto'];
            $data['precioPromedio'] = $data['importe'] / $data['kilosNeto'];
            $salidas += $data['importe'];
        }


        else if ($data['origen'] == 'Entrada Mezquite') {
            $data['entrada'] = $data['referencia'];
            $data['deBanco'] = '';
            $data['deCuenta'] = '';
            $sqlConCubeta = $con->prepare("SELECT SUM(cd.neto) AS netoCubeta, ce.totalCompra
                            FROM cubetasdetalle_mezquite cd 
                            LEFT JOIN cubetasencabezado_mezquite ce ON ce.idAlmacen = cd.idAlmacenEncabezado
                            WHERE ce.folioEntradaTambor = :id");
            $sqlConCubeta->bindParam(':id', $data['referencia']);
            $sqlConCubeta->execute();
            $sqlConCubeta->bindColumn('netoCubeta', $data['netoCubeta']);
            $sqlConCubeta->bindColumn('totalCompra', $data['totalCompraCC']);
            $sqlConCubeta->fetch(PDO::FETCH_BOUND);
            if ($data['netoCubeta'] != 0) {
                $data['kilosNeto'] = $data['neto'] + $data['netoCubeta'];
                $totalNetoCubetas += $data['netoCubeta'];
                $data['importe'] = $data['totalCompra'] + $data['totalCompraCC'];
                $totalImporte = $totalImporte + $data['totalCompraCC'];
            } else {
                $data['kilosNeto'] = $data['neto'];
                $data['importe'] = $data['totalCompra'];
            }
            $data['precioPromedio'] = $data['importe'] / $data['kilosNeto'];
            $salidas += $data['importe'];
        } else if ($data['origen'] == 'Entrada Cubeta Mezquite') {
            $data['entrada'] = $data['referencia'];
            $data['deBanco'] = '';
            $data['deCuenta'] = '';
            $data['kilosNeto'] = $data['neto'];
            $data['precioPromedio'] = $data['importe'] / $data['kilosNeto'];
            $salidas += $data['importe'];
        }

        else if ($data['origen'] == 'Entrada Naranjo') {
            $data['entrada'] = $data['referencia'];
            $data['deBanco'] = '';
            $data['deCuenta'] = '';
            $sqlConCubeta = $con->prepare("SELECT SUM(cd.neto) AS netoCubeta, ce.totalCompra
                            FROM cubetasdetalle_naranjo cd 
                            LEFT JOIN cubetasencabezado_naranjo ce ON ce.idAlmacen = cd.idAlmacenEncabezado
                            WHERE ce.folioEntradaTambor = :id");
            $sqlConCubeta->bindParam(':id', $data['referencia']);
            $sqlConCubeta->execute();
            $sqlConCubeta->bindColumn('netoCubeta', $data['netoCubeta']);
            $sqlConCubeta->bindColumn('totalCompra', $data['totalCompraCC']);
            $sqlConCubeta->fetch(PDO::FETCH_BOUND);
            if ($data['netoCubeta'] != 0) {
                $data['kilosNeto'] = $data['neto'] + $data['netoCubeta'];
                $totalNetoCubetas += $data['netoCubeta'];
                $data['importe'] = $data['totalCompra'] + $data['totalCompraCC'];
                $totalImporte = $totalImporte + $data['totalCompraCC'];
            } else {
                $data['kilosNeto'] = $data['neto'];
                $data['importe'] = $data['totalCompra'];
            }
            $data['precioPromedio'] = $data['importe'] / $data['kilosNeto'];
            $salidas += $data['importe'];
        } else if ($data['origen'] == 'Entrada Cubeta Naranjo') {
            $data['entrada'] = $data['referencia'];
            $data['deBanco'] = '';
            $data['deCuenta'] = '';
            $data['kilosNeto'] = $data['neto'];
            $data['precioPromedio'] = $data['importe'] / $data['kilosNeto'];
            $salidas += $data['importe'];
        }

        else if ($data['origen'] == 'Gastos') {
            $totalGastosRealizados += $data['cantidad'];
            $data['gastos'] = $data['cantidad'];
            $salidas += $data['cantidad'];
        } else if ($data['origen'] == 'Retenciones') {
            $totalRetencionesRealizadas += $data['cantidad'];
            $data['retenciones'] = $data['cantidad'];
            $entradas += $data['cantidad'];
            // $retenciones += $data['cantidad'];
        } else if ($data['origen'] == 'AlmacenCeraApicola') {

            if ($data['tipo'] == '1') {   //Entradas (Egresos)

                $salidas += $data['cantidad'];
                $totalEgresosCeraApicola += $data['cantidad'];
                $data['ceraApicola'] = -$data['cantidad'];
            } else if ($data['tipo'] == '2') {  //Salidas (Ingresos)

                $entradas += $data['cantidad'];
                $totalIngresosCeraApicola += $data['cantidad'];
                $data['ceraApicola'] = $data['cantidad'];
            }
        } else {
            $data['desconocido'] = $data['cantidad'];
            $salidas += $data['importe'];
            if ($totalKilos != 0) {
                $totalPrecio = $totalImporte / $totalKilos;
            }
        }

        if ($totalKilos != 0) {
            $totalPrecio = $totalImporte / $totalKilos;
        }

        $data['saldoInicial'] = $saldoInicial;
        $data['saldo'] = $saldoInicial + $entradas - $salidas;
        // if ($data['saldo'] > 0) {
        //     // $data['saldo'] -= $retenciones;
        //     $data['saldo'] += $retenciones;
        // } else if ($data['saldo'] < 0) {
        //     $data['saldo'] += $retenciones;
        // }
        array_push($datosProveedor['arrayDeudores'], $data);
    }

    $datosProveedor['totalBancos'] = $totalBancos;
    $datosProveedor['totalCaja'] = $totalCaja;
    $datosProveedor['totalKilos'] = $totalKilos + $totalNetoCubetas;
    $datosProveedor['totalImporte'] = $totalImporte;
    $datosProveedor['totalPrecio'] = $totalPrecio;
    $datosProveedor['totalDevolucionesMiel'] = $totalDevolucionesMiel;
    $datosProveedor['totalIngresosCeraApicola'] = $totalIngresosCeraApicola;
    $datosProveedor['totalEgresosCeraApicola'] = $totalEgresosCeraApicola;
    $datosProveedor['totalGastos'] = $totalGastosRealizados;
    $datosProveedor['totalRetenciones'] = $totalRetencionesRealizadas;
    $datosProveedor['totalDevolucionesCeraApicola'] = $totalDevolucionesCeraApicola;
    $datosProveedor['totalVentasCeraApicola'] = $totalVentasCeraApicola;
    $datosProveedor['totalCeraApicola'] = $totalIngresosCeraApicola - $totalEgresosCeraApicola;
    $datosProveedor['totalSaldo'] = $saldoInicial + $totalBancos + $totalCaja - $totalImporte - $totalDevolucionesMiel + $totalIngresosCeraApicola - $totalEgresosCeraApicola - $totalDevolucionesCeraApicola - $totalGastosRealizados - $totalVentasCeraApicola + $totalRetencionesRealizadas;
    // $datosProveedor['totalSaldo'] = $saldoInicial + $totalBancos + $totalCaja - $totalImporte - $totalDevolucionesMiel + $totalIngresosCeraApicola - $totalEgresosCeraApicola - $totalDevolucionesCeraApicola - $totalGastosRealizados - $totalVentasCeraApicola;
    // if ($datosProveedor['totalSaldo'] > 0) {
    //     // $datosProveedor['totalSaldo'] -= $totalRetencionesRealizadas;
    //     $datosProveedor['totalSaldo'] += $totalRetencionesRealizadas;
    // } else if ($datosProveedor['totalSaldo'] < 0) {
    //     $datosProveedor['totalSaldo'] += $totalRetencionesRealizadas;
    // }
    return $datosProveedor;
}
