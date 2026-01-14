<?php
include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$con = $pdo->conectar();
function obtenerDeudoresProveedores()
{
    global $con;

    $datos = $con->prepare("SELECT idProveedor, cantidad, idEstado, idComprador 
                            FROM proveedor 
                            WHERE idProveedor = :idProveedor");
    $datos->bindParam(':idProveedor', $_GET['idProveedor']);
    $datos->execute();

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

    // Aquí es donde entra si se abre el reporte de concentrado
    if ($datos->rowCount() >= 1) {
        $proveedor = $datos->fetch(PDO::FETCH_ASSOC);
        $idProveedor = $proveedor['idProveedor'];
        $saldoInicial = $proveedor['cantidad'];

        $fecha = $_GET['fecha'];

        $sqlDetalle = $con->prepare("SELECT idAuxiliar, fecha, descripcion, referencia, nombreBanco, nombreCuenta, cantidad,'' as neto, '' as precio, '' as totalCompra, tipo, origen, '' AS idConcepto
                                    FROM (SELECT a.idAuxiliar, a.fecha, a.descripcion, a.referencia, a.idBanco AS nombreBanco, a.idCuenta AS nombreCuenta, a.cantidad, a.ingresoEgreso AS tipo, 'Banco' AS origen 
                                    FROM auxiliardebancos a 
                                    WHERE a.fecha <= :fecha AND a.ingresoEgreso = 1 AND a.tipoDePersona = 1 AND a.nombreDe = :idProveedor AND a.idSubcuenta IN $idSubcuentas
                                    ) AS tablaAuxiliar
                                    UNION
                                    SELECT idAuxiliar, fecha, descripcion, referencia,'' AS nombreBanco, '' AS nombreCuenta, cantidad, '' as neto, '' as precio, '' as totalCompra, tipo, origen, idConcepto
                                    FROM (SELECT d.idCajaChica AS idAuxiliar, c.fecha, d.descripcion, '' AS referencia,
                                    CASE WHEN d.cantidad IS NOT NULL THEN d.cantidad ELSE importe END AS cantidad,      
                                    c.tipo, 'Caja' AS origen, d.idConcepto
                                    FROM cajachica c 
                                    LEFT JOIN cajachicadetalle d On d.idCajaChica = c.idCajaChica
                                    WHERE c.fecha <= :fecha AND c.tipoDeCliente = 1 AND c.nombre = :idProveedor AND d.idConcepto IN $idSubcuentas) AS tablaCaja
                                    UNION
                                    SELECT '' as idAuxiliar, fecha, descripcion, '' AS referencia,'' AS nombreBanco, '' AS nombreCuenta, cantidad, '' as neto, '' as precio, '' as totalCompra, tipo, origen, '' AS idConcepto
                                    FROM (SELECT g.fecha, g.concepto AS descripcion, g.cantidad, '1' AS tipo, 'Gastos' AS origen
                                    FROM gastosrealizados g 
                                    WHERE g.idProveedor = :idProveedor AND g.fecha <= :fecha) AS gastosRealizados
                                    UNION
                                    SELECT '' as idAuxiliar, fecha, descripcion, '' AS referencia, '' AS nombreBanco, '' AS nombreCuenta, cantidad, '' as neto, '' as precio, '' as totalCompra, 
                                    tipo, origen, idConcepto
                                    FROM (SELECT aec.fecha,adc.descripcion, adc.importe AS cantidad, 'AlmacenCeraApicola' AS origen, tipo, adc.clasificacion AS idConcepto
                                    FROM almacenencabezadocera aec
                                    INNER JOIN almacencera adc ON aec.idAlmacen = adc.idAlmacenEncabezado
                                    WHERE aec.fecha <= :fecha AND aec.tipoPersona = 1 AND aec.idProveedor = :idProveedor AND (CASE WHEN aec.tipo = 1 THEN (adc.clasificacion = 2 OR adc.clasificacion = 3) ELSE adc.clasificacion >= 1 END )) tablaCera
                                    UNION
                                    SELECT idAuxiliar, fecha, descripcion, '' AS referencia, '' AS nombreBanco, '' AS nombreCuenta, cantidad, '' as neto, '' as precio, '' as totalCompra, 
                                    tipo, origen, idConcepto
                                    FROM (SELECT aa.idAlmacen AS idAuxiliar, aea.fecha,aa.descripcion, aa.importe AS cantidad, 'AlmacenCeraApicola' AS origen, tipo, aa.clasificacion AS idConcepto
                                    FROM almacenencabezadoapicola aea
                                    INNER JOIN almacenapicola aa ON aea.idAlmacen = aa.idAlmacenEncabezado
                                    WHERE aea.fecha <= :fecha AND aea.tipoPersona = 1 AND aea.idProveedor = :idProveedor  AND (CASE WHEN aea.tipo = 1 THEN (aa.clasificacion = 2 OR aa.clasificacion = 3) ELSE aa.clasificacion >= 1 END)) tablaProApicolas
                                    UNION
                                    SELECT '' as idAuxiliar, fecha, descripcion, referencia,'' AS nombreBanco, '' AS nombreCuenta, cantidad, neto, precio, totalCompra, tipo, origen, '' AS idConcepto
                                    FROM(SELECT am.idAlmacen AS referencia, am.fecha, 'Ingreso directo de miel 100% pura' AS descripcion, '' as cantidad,
                                    SUM(al.neto) AS neto, al.precio, am.totalCompra, '0' AS tipo, 'Entrada' AS origen
                                    FROM almacen al
                                    LEFT JOIN almacenencabezado am ON am.idAlmacen = al.idAlmacenEncabezado
                                    WHERE am.fecha <= :fecha AND am.idProveedor = :idProveedor
                                    GROUP BY am.idAlmacen) AS almacen
                                    UNION
                                    SELECT '' as idAuxiliar, fecha, descripcion, referencia,'' AS nombreBanco, '' AS nombreCuenta, cantidad, neto, precio, totalCompra, tipo, origen, '' AS idConcepto
                                    FROM(SELECT ce.idAlmacen AS referencia, ce.fecha, 'Ingreso directo de miel 100% pura' AS descripcion, '' as cantidad,
                                    SUM(cd.neto) AS neto, cd.precio, ce.totalCompra, '0' AS tipo, 'EntradaCubeta' AS origen
                                    FROM cubetasdetalle cd
                                    LEFT JOIN cubetasencabezado ce ON ce.idAlmacen = cd.idAlmacenEncabezado
                                    WHERE ce.fecha <= :fecha AND ce.idProveedor = :idProveedor AND ce.folioEntradaTambor = 99999
                                    GROUP BY ce.idAlmacen) AS almacenCubetas
                                    UNION
                                    SELECT '' as idAuxiliar, fecha, descripcion, referencia,'' AS nombreBanco, '' AS nombreCuenta, cantidad, neto, precio, totalCompra, tipo, origen, '' AS idConcepto
                                    FROM(SELECT am.idAlmacen AS referencia, am.fecha, 'Ingreso directo de miel 100% orgánica' AS descripcion, '' as cantidad,
                                    SUM(al.neto) AS neto, al.precio, am.totalCompra, '0' AS tipo, 'Entrada Organico' AS origen
                                    FROM almacen_organico al
                                    LEFT JOIN almacenencabezado_organico am ON am.idAlmacen = al.idAlmacenEncabezado
                                    WHERE am.fecha <= :fecha AND am.idProveedor = :idProveedor
                                    GROUP BY am.idAlmacen) AS almacen
                                    UNION

                                    SELECT '' as idAuxiliar, fecha, descripcion, referencia,'' AS nombreBanco, '' AS nombreCuenta, cantidad, neto, precio, totalCompra, tipo, origen, '' AS idConcepto
                                    FROM(SELECT am.idAlmacen AS referencia, am.fecha, 'Ingreso directo de miel 100% mantequilla' AS descripcion, '' as cantidad,
                                    SUM(al.neto) AS neto, al.precio, am.totalCompra, '0' AS tipo, 'Entrada Mantequilla' AS origen
                                    FROM almacen_mantequilla al
                                    LEFT JOIN almacenencabezado_mantequilla am ON am.idAlmacen = al.idAlmacenEncabezado
                                    WHERE am.fecha <= :fecha AND am.idProveedor = :idProveedor AND al.referencia = 0
                                    GROUP BY am.idAlmacen) AS almacen
                                    UNION
                                    SELECT '' as idAuxiliar, fecha, descripcion, referencia,'' AS nombreBanco, '' AS nombreCuenta, cantidad, neto, precio, totalCompra, tipo, origen, '' AS idConcepto
                                    FROM(SELECT ce.idAlmacen AS referencia, ce.fecha, 'Ingreso directo de miel 100% mantequilla' AS descripcion, '' as cantidad,
                                    SUM(cd.neto) AS neto, cd.precio, ce.totalCompra, '0' AS tipo, 'Entrada Cubeta Mantequilla' AS origen
                                    FROM cubetasdetalle_mantequilla cd
                                    LEFT JOIN cubetasencabezado_mantequilla ce ON ce.idAlmacen = cd.idAlmacenEncabezado
                                    WHERE ce.fecha <= :fecha AND ce.idProveedor = :idProveedor AND ce.folioEntradaTambor = 99999 AND cd.referencia = 0
                                    GROUP BY ce.idAlmacen) AS almacenCubetas
                                    UNION

                                    SELECT '' as idAuxiliar, fecha, descripcion, referencia,'' AS nombreBanco, '' AS nombreCuenta, cantidad, neto, precio, totalCompra, tipo, origen, '' AS idConcepto
                                    FROM(SELECT am.idAlmacen AS referencia, am.fecha, 'Ingreso directo de miel 100% altiplano' AS descripcion, '' as cantidad,
                                    SUM(al.neto) AS neto, al.precio, am.totalCompra, '0' AS tipo, 'Entrada Altiplano' AS origen
                                    FROM almacen_altiplano al
                                    LEFT JOIN almacenencabezado_altiplano am ON am.idAlmacen = al.idAlmacenEncabezado
                                    WHERE am.fecha <= :fecha AND am.idProveedor = :idProveedor AND al.referencia = 0
                                    GROUP BY am.idAlmacen) AS almacen
                                    UNION
                                    SELECT '' as idAuxiliar, fecha, descripcion, referencia,'' AS nombreBanco, '' AS nombreCuenta, cantidad, neto, precio, totalCompra, tipo, origen, '' AS idConcepto
                                    FROM(SELECT ce.idAlmacen AS referencia, ce.fecha, 'Ingreso directo de miel 100% altiplano' AS descripcion, '' as cantidad,
                                    SUM(cd.neto) AS neto, cd.precio, ce.totalCompra, '0' AS tipo, 'Entrada Cubeta Altiplano' AS origen
                                    FROM cubetasdetalle_altiplano cd
                                    LEFT JOIN cubetasencabezado_altiplano ce ON ce.idAlmacen = cd.idAlmacenEncabezado
                                    WHERE ce.fecha <= :fecha AND ce.idProveedor = :idProveedor AND ce.folioEntradaTambor = 99999 AND cd.referencia = 0
                                    GROUP BY ce.idAlmacen) AS almacenCubetas
                                    UNION

                                    SELECT '' as idAuxiliar, fecha, descripcion, referencia,'' AS nombreBanco, '' AS nombreCuenta, cantidad, neto, precio, totalCompra, tipo, origen, '' AS idConcepto
                                    FROM(SELECT am.idAlmacen AS referencia, am.fecha, 'Ingreso directo de miel 100% aguacate' AS descripcion, '' as cantidad,
                                    SUM(al.neto) AS neto, al.precio, am.totalCompra, '0' AS tipo, 'Entrada Aguacate' AS origen
                                    FROM almacen_aguacate al
                                    LEFT JOIN almacenencabezado_aguacate am ON am.idAlmacen = al.idAlmacenEncabezado
                                    WHERE am.fecha <= :fecha AND am.idProveedor = :idProveedor AND al.referencia = 0
                                    GROUP BY am.idAlmacen) AS almacen
                                    UNION
                                    SELECT '' as idAuxiliar, fecha, descripcion, referencia,'' AS nombreBanco, '' AS nombreCuenta, cantidad, neto, precio, totalCompra, tipo, origen, '' AS idConcepto
                                    FROM(SELECT ce.idAlmacen AS referencia, ce.fecha, 'Ingreso directo de miel 100% aguacate' AS descripcion, '' as cantidad,
                                    SUM(cd.neto) AS neto, cd.precio, ce.totalCompra, '0' AS tipo, 'Entrada Cubeta Aguacate' AS origen
                                    FROM cubetasdetalle_aguacate cd
                                    LEFT JOIN cubetasencabezado_aguacate ce ON ce.idAlmacen = cd.idAlmacenEncabezado
                                    WHERE ce.fecha <= :fecha AND ce.idProveedor = :idProveedor AND ce.folioEntradaTambor = 99999 AND cd.referencia = 0
                                    GROUP BY ce.idAlmacen) AS almacenCubetas
                                    UNION

                                    SELECT '' as idAuxiliar, fecha, descripcion, referencia,'' AS nombreBanco, '' AS nombreCuenta, cantidad, neto, precio, totalCompra, tipo, origen, '' AS idConcepto
                                    FROM(SELECT am.idAlmacen AS referencia, am.fecha, 'Ingreso directo de miel 100% mezquite' AS descripcion, '' as cantidad,
                                    SUM(al.neto) AS neto, al.precio, am.totalCompra, '0' AS tipo, 'Entrada Mezquite' AS origen
                                    FROM almacen_mezquite al
                                    LEFT JOIN almacenencabezado_mezquite am ON am.idAlmacen = al.idAlmacenEncabezado
                                    WHERE am.fecha <= :fecha AND am.idProveedor = :idProveedor AND al.referencia = 0
                                    GROUP BY am.idAlmacen) AS almacen
                                    UNION
                                    SELECT '' as idAuxiliar, fecha, descripcion, referencia,'' AS nombreBanco, '' AS nombreCuenta, cantidad, neto, precio, totalCompra, tipo, origen, '' AS idConcepto
                                    FROM(SELECT ce.idAlmacen AS referencia, ce.fecha, 'Ingreso directo de miel 100% mezquite' AS descripcion, '' as cantidad,
                                    SUM(cd.neto) AS neto, cd.precio, ce.totalCompra, '0' AS tipo, 'Entrada Cubeta Mezquite' AS origen
                                    FROM cubetasdetalle_mezquite cd
                                    LEFT JOIN cubetasencabezado_mezquite ce ON ce.idAlmacen = cd.idAlmacenEncabezado
                                    WHERE ce.fecha <= :fecha AND ce.idProveedor = :idProveedor AND ce.folioEntradaTambor = 99999 AND cd.referencia = 0
                                    GROUP BY ce.idAlmacen) AS almacenCubetas
                                    UNION

                                    SELECT '' as idAuxiliar, fecha, descripcion, referencia,'' AS nombreBanco, '' AS nombreCuenta, cantidad, neto, precio, totalCompra, tipo, origen, '' AS idConcepto
                                    FROM(SELECT am.idAlmacen AS referencia, am.fecha, 'Ingreso directo de miel 100% naranjo' AS descripcion, '' as cantidad,
                                    SUM(al.neto) AS neto, al.precio, am.totalCompra, '0' AS tipo, 'Entrada Naranjo' AS origen
                                    FROM almacen_naranjo al
                                    LEFT JOIN almacenencabezado_naranjo am ON am.idAlmacen = al.idAlmacenEncabezado
                                    WHERE am.fecha <= :fecha AND am.idProveedor = :idProveedor AND al.referencia = 0
                                    GROUP BY am.idAlmacen) AS almacen
                                    UNION
                                    SELECT '' as idAuxiliar, fecha, descripcion, referencia,'' AS nombreBanco, '' AS nombreCuenta, cantidad, neto, precio, totalCompra, tipo, origen, '' AS idConcepto
                                    FROM(SELECT ce.idAlmacen AS referencia, ce.fecha, 'Ingreso directo de miel 100% naranjo' AS descripcion, '' as cantidad,
                                    SUM(cd.neto) AS neto, cd.precio, ce.totalCompra, '0' AS tipo, 'Entrada Cubeta Naranjo' AS origen
                                    FROM cubetasdetalle_naranjo cd
                                    LEFT JOIN cubetasencabezado_naranjo ce ON ce.idAlmacen = cd.idAlmacenEncabezado
                                    WHERE ce.fecha <= :fecha AND ce.idProveedor = :idProveedor AND ce.folioEntradaTambor = 99999 AND cd.referencia = 0
                                    GROUP BY ce.idAlmacen) AS almacenCubetas

                                    UNION
                                    SELECT '' as idAuxiliar, fecha, descripcion, referencia,'' AS nombreBanco, '' AS nombreCuenta, cantidad, neto, precio, totalCompra, tipo, origen, '' AS idConcepto
                                    FROM(SELECT ce.idAlmacen AS referencia, ce.fecha, 'Ingreso directo de miel 100% orgánica' AS descripcion, '' as cantidad,
                                    SUM(cd.neto) AS neto, cd.precio, ce.totalCompra, '0' AS tipo, 'Entrada Cubeta Organico' AS origen
                                    FROM cubetasdetalle_organico cd
                                    LEFT JOIN cubetasencabezado_organico ce ON ce.idAlmacen = cd.idAlmacenEncabezado
                                    WHERE ce.fecha <= :fecha AND ce.idProveedor = :idProveedor AND ce.folioEntradaTambor = 99999
                                    GROUP BY ce.idAlmacen) AS almacenCubetas
                                    UNION
                                    SELECT idAuxiliar, fecha, descripcion, referencia, nombreBanco, nombreCuenta, cantidad,'' as neto, '' as precio, '' as totalCompra, tipo, origen, '' AS idConcepto
                                    FROM (SELECT a.idAuxiliar, a.fecha, a.descripcion, a.referencia, a.idBanco AS nombreBanco, a.idCuenta AS nombreCuenta, a.cantidad, a.ingresoEgreso AS tipo, 'devolucionBanco' AS origen
                                    FROM auxiliardebancos a
                                    WHERE a.fecha <= :fecha AND a.ingresoEgreso = 0 AND a.idSubcuenta IN $idSubcuentas AND a.tipoDePersona = 1 AND a.nombreDe = :idProveedor
                                    ) AS devolucionBancos
                                    ORDER BY fecha ASC");
        $sqlDetalle->bindParam(':idProveedor', $idProveedor);
        $sqlDetalle->bindParam(':fecha', $fecha);
        $sqlDetalle->execute();
        $proveedor['arregloDeMovimientos'] = [];
        $entradas = 0;
        $salidas = 0;
        $totalBancos = 0;
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

        foreach ($sqlDetalle->fetchAll(PDO::FETCH_ASSOC) as $movimiento) {
            $movimiento['importe'] = is_numeric($movimiento['totalCompra']) ? $movimiento['totalCompra'] : 0;
            $totalKilos += is_numeric($movimiento['neto']) ? $movimiento['neto'] : 0;
            $totalImporte += is_numeric($movimiento['totalCompra']) ? $movimiento['totalCompra'] : 0;
            if ($movimiento['origen'] == 'Banco') {
                $movimiento['banco'] = $movimiento['cantidad'];
                $entradas += $movimiento['cantidad'];
                $totalBancos += $movimiento['cantidad'];
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
                } else if ($movimiento['tipo'] == '0') {
                    $movimiento['caja'] = -$movimiento['cantidad'];
                    $salidas += $movimiento['cantidad'];

                    $totalCaja -= $movimiento['cantidad'];
                }
                $movimiento['cheque'] = $movimiento['referencia'];
                $movimiento['deBanco'] = '';
                $movimiento['deCuenta'] = '';
            } else if ($movimiento['origen'] == 'devolucionBanco') {

                $movimiento['banco'] = -$movimiento['cantidad'];
                $totalBancos -= $movimiento['cantidad'];
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
                    $movimiento['ceraApicola'] = -$movimiento['cantidad'];
                    $totalEgresosCeraApicola += $movimiento['cantidad'];
                } else if ($movimiento['tipo'] == '2') {  //Salidas (Ingresos)

                    $entradas += $movimiento['cantidad'];
                    $movimiento['ceraApicola'] = $movimiento['cantidad'];
                    $totalIngresosCeraApicola += $movimiento['cantidad'];
                }
            } else {
                $movimiento['desconocido'] = $movimiento['cantidad'];
                $salidas += $movimiento['importe'];
                if ($totalKilos != 0) {
                    $totalPrecio = $totalImporte / $totalKilos;
                }
            }
            if ($totalKilos != 0) {
                $totalPrecio = $totalImporte / $totalKilos;
            }
            $proveedor['saldoInicial'] = $saldoInicial;
            $movimiento['saldo'] = $saldoInicial + $entradas - $salidas;
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
            $proveedor['totalCeraApicola'] = $totalIngresosCeraApicola - $totalEgresosCeraApicola;
            $proveedor['totalSaldo'] = $saldoInicial + $totalBancos + $totalCaja - $totalImporte - $totalDevolucionesMiel + $totalIngresosCeraApicola - $totalEgresosCeraApicola - $totalDevolucionesCeraApicola - $totalGastosRealizados - $totalVentasCeraApicola;
            array_push($proveedor['arregloDeMovimientos'], $movimiento);
        }
        // array_push($resultado, $proveedor);
        // }
        $resultado = $proveedor;
    }
    return $resultado;
}

if (!isset($_GET['function'])) {
    $resultado = obtenerDeudoresProveedores();
    echo json_encode($resultado);
}
