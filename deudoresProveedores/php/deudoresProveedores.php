<?php
include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$con = $pdo->conectar();
function obtenerDeudoresProveedores()
{
    global $con;
    switch ($_GET['parametro']) {
        case 'activos':
            $estado = 'p.idEstado = 1';
            break;
        case 'morosos':
            $estado = 'p.idEstado = 2';
            break;
        case 'todos':
            $estado = '(p.idEstado = 1 OR p.idEstado = 2)';
            break;
        default:
            throw new Exception('El parámetro no es válido');
            break;
    }

    $datos = $con->prepare("SELECT nombre, idProveedor, idSagarpa, localidad, cantidad, idEstado, idComprador
    FROM(SELECT ab.nombreDe AS idProveedor, p.nombre, p.idSagarpa, l.localidad, p.cantidad, p.idEstado, p.idComprador
    FROM auxiliardebancos ab 
    INNER JOIN proveedor p ON p.idProveedor = ab.nombreDe
    LEFT JOIN direccion d ON d.idDireccion = p.idDireccion
                LEFT JOIN localidades l ON l.idlocalidad = d.idlocalidad
    WHERE ab.tipoDePersona = 1 AND p.empresa = 0 AND $estado GROUP BY ab.nombreDe ORDER BY p.nombre ASC) auxiliar
    UNION
                SELECT nombre, idProveedor, idSagarpa, localidad, cantidad, idEstado, idComprador
                FROM (SELECT c.nombre AS idProveedor, p.nombre, p.idSagarpa, l.localidad, p.cantidad, p.idEstado, p.idComprador
                FROM cajachica c
                INNER JOIN proveedor p ON p.idProveedor = c.nombre
                LEFT JOIN direccion d ON d.idDireccion = p.idDireccion
                LEFT JOIN localidades l ON l.idlocalidad = d.idlocalidad
                WHERE c.tipoDeCliente = 1 AND p.empresa = 0 AND $estado GROUP BY c.nombre ORDER BY p.nombre ASC) caja
    UNION
                SELECT nombre, idProveedor, idSagarpa, localidad, cantidad, idEstado, idComprador
                FROM (SELECT g.idProveedor, p.nombre, p.idSagarpa, l.localidad, p.cantidad, p.idEstado, p.idComprador
                FROM gastosrealizados g
                INNER JOIN proveedor p ON p.idProveedor = g.idProveedor
                LEFT JOIN direccion d ON d.idDireccion = p.idDireccion
                LEFT JOIN localidades l ON l.idlocalidad = d.idlocalidad
                WHERE p.empresa = 0 AND $estado GROUP BY g.idProveedor ORDER BY p.nombre ASC) gastos
    UNION 
                SELECT nombre, idProveedor, idSagarpa, localidad, cantidad, idEstado, idComprador
                FROM (SELECT aec.idProveedor, p.nombre, p.idSagarpa, l.localidad, p.cantidad, p.idEstado, p.idComprador
                FROM almacenencabezadocera aec
                INNER JOIN proveedor p ON p.idProveedor = aec.idProveedor
                LEFT JOIN direccion d ON d.idDireccion = p.idDireccion
                LEFT JOIN localidades l ON l.idlocalidad = d.idlocalidad
                WHERE aec.tipoPersona = 1 AND p.empresa = 0 AND $estado GROUP BY aec.idProveedor ORDER BY p.nombre ASC) almacencera
    UNION
                SELECT nombre, idProveedor, idSagarpa, localidad, cantidad, idEstado, idComprador
                FROM (SELECT aea.idProveedor, p.nombre, p.idSagarpa, l.localidad, p.cantidad, p.idEstado, p.idComprador
                FROM almacenencabezadoapicola aea
                INNER JOIN proveedor p ON p.idProveedor = aea.idProveedor
                LEFT JOIN direccion d ON d.idDireccion = p.idDireccion
                LEFT JOIN localidades l ON l.idlocalidad = d.idlocalidad
                WHERE aea.tipoPersona = 1 AND p.empresa = 0 AND $estado GROUP BY aea.idProveedor ORDER BY p.nombre ASC) almacenapicola
    UNION
                SELECT nombre, idProveedor, idSagarpa, localidad, cantidad, idEstado, idComprador
                FROM (SELECT dae.idProveedor, p.nombre, p.idSagarpa, l.localidad, p.cantidad, p.idEstado, p.idComprador
                FROM derivadosalmacenencabezado dae
                INNER JOIN proveedor p ON p.idProveedor = dae.idProveedor
                LEFT JOIN direccion d ON d.idDireccion = p.idDireccion
                LEFT JOIN localidades l ON l.idlocalidad = d.idlocalidad
                WHERE dae.tipoPersona = 1 AND p.empresa = 0 AND $estado GROUP BY dae.idProveedor ORDER BY p.nombre ASC) derivadosalmacendetalle
    UNION
                SELECT nombre, idProveedor, idSagarpa, localidad, cantidad, idEstado, idComprador
                FROM (SELECT dae.idProveedor, p.nombre, p.idSagarpa, l.localidad, p.cantidad, p.idEstado, p.idComprador
                FROM derivadosalmacenencabezado_salidas dae
                INNER JOIN proveedor p ON p.idProveedor = dae.idProveedor
                LEFT JOIN direccion d ON d.idDireccion = p.idDireccion
                LEFT JOIN localidades l ON l.idlocalidad = d.idlocalidad
                WHERE dae.tipoPersona = 1 AND p.empresa = 0 AND $estado GROUP BY dae.idProveedor ORDER BY p.nombre ASC) derivadosalmacendetalle_salidas
    UNION
                SELECT  nombre, idProveedor, idSagarpa, localidad, cantidad, idEstado, idComprador
                FROM (SELECT ae.idProveedor, p.nombre, p.idSagarpa, l.localidad, p.cantidad, p.idEstado, p.idComprador
                FROM almacenencabezado ae
                INNER JOIN proveedor p ON p.idProveedor = ae.idProveedor
                LEFT JOIN direccion d ON d.idDireccion = p.idDireccion
                LEFT JOIN localidades l ON l.idlocalidad = d.idlocalidad
                WHERE p.empresa = 0 AND $estado GROUP BY ae.idProveedor ORDER BY p.nombre ASC) almacen
    UNION
    --             SELECT  nombre, idProveedor, idSagarpa, localidad, cantidad, idEstado, idComprador
    --             FROM (SELECT ae.idProveedor, p.nombre, p.idSagarpa, l.localidad, p.cantidad, p.idEstado, p.idComprador
    --             FROM encabezado_cotizacion ae
    --             INNER JOIN proveedor p ON p.idProveedor = ae.idCliente
    --             LEFT JOIN direccion d ON d.idDireccion = p.idDireccion
    --             LEFT JOIN localidades l ON l.idlocalidad = d.idlocalidad
    --             WHERE p.empresa = 0 AND $estado AND ae.exportador = 'APICULTOR' AND ae.estado = 'ENTREGADO' GROUP BY ae.idProveedor ORDER BY p.nombre ASC) almacen
    -- UNION
                SELECT  nombre, idProveedor, idSagarpa, localidad, cantidad, idEstado, idComprador
                FROM (SELECT ae.idProveedor, p.nombre, p.idSagarpa, l.localidad, p.cantidad, p.idEstado, p.idComprador
                FROM almacenencabezado_organico ae
                INNER JOIN proveedor p ON p.idProveedor = ae.idProveedor
                LEFT JOIN direccion d ON d.idDireccion = p.idDireccion
                LEFT JOIN localidades l ON l.idlocalidad = d.idlocalidad
                WHERE p.empresa = 0 AND $estado GROUP BY ae.idProveedor ORDER BY p.nombre ASC) almacenO
    UNION
                SELECT  nombre, idProveedor, idSagarpa, localidad, cantidad, idEstado, idComprador
                FROM (SELECT ae.idProveedor, p.nombre, p.idSagarpa, l.localidad, p.cantidad, p.idEstado, p.idComprador
                FROM almacenencabezado_mantequilla ae
                INNER JOIN proveedor p ON p.idProveedor = ae.idProveedor
                LEFT JOIN direccion d ON d.idDireccion = p.idDireccion
                LEFT JOIN localidades l ON l.idlocalidad = d.idlocalidad
                WHERE p.empresa = 0 AND $estado GROUP BY ae.idProveedor ORDER BY p.nombre ASC) almacenO
    UNION
                SELECT  nombre, idProveedor, idSagarpa, localidad, cantidad, idEstado, idComprador
                FROM (SELECT ae.idProveedor, p.nombre, p.idSagarpa, l.localidad, p.cantidad, p.idEstado, p.idComprador
                FROM almacenencabezado_altiplano ae
                INNER JOIN proveedor p ON p.idProveedor = ae.idProveedor
                LEFT JOIN direccion d ON d.idDireccion = p.idDireccion
                LEFT JOIN localidades l ON l.idlocalidad = d.idlocalidad
                WHERE p.empresa = 0 AND $estado GROUP BY ae.idProveedor ORDER BY p.nombre ASC) almacenO
    UNION
                SELECT  nombre, idProveedor, idSagarpa, localidad, cantidad, idEstado, idComprador
                FROM (SELECT ae.idProveedor, p.nombre, p.idSagarpa, l.localidad, p.cantidad, p.idEstado, p.idComprador
                FROM almacenencabezado_naranjo ae
                INNER JOIN proveedor p ON p.idProveedor = ae.idProveedor
                LEFT JOIN direccion d ON d.idDireccion = p.idDireccion
                LEFT JOIN localidades l ON l.idlocalidad = d.idlocalidad
                WHERE p.empresa = 0 AND $estado GROUP BY ae.idProveedor ORDER BY p.nombre ASC) almacenO
    UNION
                SELECT  nombre, idProveedor, idSagarpa, localidad, cantidad, idEstado, idComprador
                FROM (SELECT ae.idProveedor, p.nombre, p.idSagarpa, l.localidad, p.cantidad, p.idEstado, p.idComprador
                FROM almacenencabezado_aguacate ae
                INNER JOIN proveedor p ON p.idProveedor = ae.idProveedor
                LEFT JOIN direccion d ON d.idDireccion = p.idDireccion
                LEFT JOIN localidades l ON l.idlocalidad = d.idlocalidad
                WHERE p.empresa = 0 AND $estado GROUP BY ae.idProveedor ORDER BY p.nombre ASC) almacenO
    UNION
                SELECT  nombre, idProveedor, idSagarpa, localidad, cantidad, idEstado, idComprador
                FROM (SELECT ae.idProveedor, p.nombre, p.idSagarpa, l.localidad, p.cantidad, p.idEstado, p.idComprador
                FROM almacenencabezado_mezquite ae
                INNER JOIN proveedor p ON p.idProveedor = ae.idProveedor
                LEFT JOIN direccion d ON d.idDireccion = p.idDireccion
                LEFT JOIN localidades l ON l.idlocalidad = d.idlocalidad
                WHERE p.empresa = 0 AND $estado GROUP BY ae.idProveedor ORDER BY p.nombre ASC) almacenO
    UNION
                SELECT  nombre, idProveedor, idSagarpa, localidad, cantidad, idEstado, idComprador
                FROM (SELECT ce.idProveedor, p.nombre, p.idSagarpa, l.localidad, p.cantidad, p.idEstado, p.idComprador
                FROM cubetasencabezado ce
                INNER JOIN proveedor p ON p.idProveedor = ce.idProveedor
                LEFT JOIN direccion d ON d.idDireccion = p.idDireccion
                LEFT JOIN localidades l ON l.idlocalidad = d.idlocalidad
                WHERE p.empresa = 0 AND $estado GROUP BY ce.idProveedor ORDER BY p.nombre ASC) almacenCubeta
    UNION
                SELECT  nombre, idProveedor, idSagarpa, localidad, cantidad, idEstado, idComprador
                FROM (SELECT ce.idProveedor, p.nombre, p.idSagarpa, l.localidad, p.cantidad, p.idEstado, p.idComprador
                FROM cubetasencabezado_organico ce
                INNER JOIN proveedor p ON p.idProveedor = ce.idProveedor
                LEFT JOIN direccion d ON d.idDireccion = p.idDireccion
                LEFT JOIN localidades l ON l.idlocalidad = d.idlocalidad
                WHERE p.empresa = 0 AND $estado GROUP BY ce.idProveedor ORDER BY p.nombre ASC) almacenCubetaO            
    UNION
                SELECT  nombre, idProveedor, idSagarpa, localidad, cantidad, idEstado, idComprador
                FROM (SELECT ce.idProveedor, p.nombre, p.idSagarpa, l.localidad, p.cantidad, p.idEstado, p.idComprador
                FROM cubetasencabezado_mantequilla ce
                INNER JOIN proveedor p ON p.idProveedor = ce.idProveedor
                LEFT JOIN direccion d ON d.idDireccion = p.idDireccion
                LEFT JOIN localidades l ON l.idlocalidad = d.idlocalidad
                WHERE p.empresa = 0 AND $estado GROUP BY ce.idProveedor ORDER BY p.nombre ASC) almacenCubetaO            
    UNION
                    SELECT  nombre, idProveedor, idSagarpa, localidad, cantidad, idEstado, idComprador
                FROM (SELECT ce.idProveedor, p.nombre, p.idSagarpa, l.localidad, p.cantidad, p.idEstado, p.idComprador
                FROM cubetasencabezado_altiplano ce
                INNER JOIN proveedor p ON p.idProveedor = ce.idProveedor
                LEFT JOIN direccion d ON d.idDireccion = p.idDireccion
                LEFT JOIN localidades l ON l.idlocalidad = d.idlocalidad
                WHERE p.empresa = 0 AND $estado GROUP BY ce.idProveedor ORDER BY p.nombre ASC) almacenCubetaO            
    UNION
                    SELECT  nombre, idProveedor, idSagarpa, localidad, cantidad, idEstado, idComprador
                FROM (SELECT ce.idProveedor, p.nombre, p.idSagarpa, l.localidad, p.cantidad, p.idEstado, p.idComprador
                FROM cubetasencabezado_naranjo ce
                INNER JOIN proveedor p ON p.idProveedor = ce.idProveedor
                LEFT JOIN direccion d ON d.idDireccion = p.idDireccion
                LEFT JOIN localidades l ON l.idlocalidad = d.idlocalidad
                WHERE p.empresa = 0 AND $estado GROUP BY ce.idProveedor ORDER BY p.nombre ASC) almacenCubetaO            
    UNION
                SELECT  nombre, idProveedor, idSagarpa, localidad, cantidad, idEstado, idComprador
                FROM (SELECT ce.idProveedor, p.nombre, p.idSagarpa, l.localidad, p.cantidad, p.idEstado, p.idComprador
                FROM cubetasencabezado_aguacate ce
                INNER JOIN proveedor p ON p.idProveedor = ce.idProveedor
                LEFT JOIN direccion d ON d.idDireccion = p.idDireccion
                LEFT JOIN localidades l ON l.idlocalidad = d.idlocalidad
                WHERE p.empresa = 0 AND $estado GROUP BY ce.idProveedor ORDER BY p.nombre ASC) almacenCubetaO            
    UNION
                    SELECT  nombre, idProveedor, idSagarpa, localidad, cantidad, idEstado, idComprador
                FROM (SELECT ce.idProveedor, p.nombre, p.idSagarpa, l.localidad, p.cantidad, p.idEstado, p.idComprador
                FROM cubetasencabezado_mezquite ce
                INNER JOIN proveedor p ON p.idProveedor = ce.idProveedor
                LEFT JOIN direccion d ON d.idDireccion = p.idDireccion
                LEFT JOIN localidades l ON l.idlocalidad = d.idlocalidad
                WHERE p.empresa = 0 AND $estado GROUP BY ce.idProveedor ORDER BY p.nombre ASC) almacenCubetaO            
    UNION
                SELECT  nombre, idProveedor, idSagarpa, localidad, cantidad, idEstado, idComprador
                FROM (SELECT p.idProveedor, p.nombre, p.idSagarpa, l.localidad, p.cantidad, p.idEstado, p.idComprador
                FROM  proveedor p 
                LEFT JOIN direccion d ON d.idDireccion = p.idDireccion
                LEFT JOIN localidades l ON l.idlocalidad = d.idlocalidad
                WHERE p.cantidad > 0 AND p.empresa = 0 AND $estado GROUP BY p.idProveedor ORDER BY p.nombre ASC) proveedores
                GROUP BY idProveedor ORDER BY nombre ASC");
    $datos->execute();
    $resultado = array();

    // Obtener las subcuentas que van a deudores y proveedores

    $sqlSubcuentas = $con->prepare("SELECT idSubcuenta FROM subcuentas WHERE deudores = 1");
    $sqlSubcuentas->execute();
    if ($sqlSubcuentas == false) {
        // throw new Exception($con->errorInfo());
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

    if (isset($_GET['acumulado'])) {
        // Aquí es donde entra si se abre el reporte de concentrado
        if ($datos->rowCount() >= 1) {
            foreach ($datos->fetchAll(PDO::FETCH_ASSOC) as $proveedor) {
                $idProveedor = $proveedor['idProveedor'];
                $saldoInicial = $proveedor['cantidad'];
                $estado = $proveedor['idEstado'];
                $idComprador = $proveedor['idComprador'];

                if (isset($_GET['idComprador'])) {
                    if ($idComprador != $_GET['idComprador']) {
                        continue;
                    }
                }

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
                                    WHERE g.idProveedor = :idProveedor) AS retencionesRealizadas
                                    UNION
                                    SELECT '' as idAuxiliar, fecha, descripcion, '' AS referencia, '' AS nombreBanco, '' AS nombreCuenta, cantidad, '' as neto, '' as precio, '' as totalCompra, 
                                    tipo, origen, idConcepto
                                    FROM (SELECT aec.fecha,adc.descripcion, adc.importe AS cantidad, 'AlmacenCeraApicola' AS origen, tipo, adc.clasificacion AS idConcepto
                                    FROM almacenencabezadocera aec
                                    INNER JOIN almacencera adc ON aec.idAlmacen = adc.idAlmacenEncabezado
                                    WHERE aec.tipoPersona = 1 AND aec.idProveedor = :idProveedor AND (CASE WHEN aec.tipo = 1 THEN (adc.clasificacion = 2 OR adc.clasificacion = 3) ELSE adc.clasificacion >= 1 END )) tablaCera
                                    UNION
                                    SELECT idAuxiliar, fecha, descripcion, '' AS referencia, '' AS nombreBanco, '' AS nombreCuenta, cantidad, '' as neto, '' as precio, '' as totalCompra, tipo, origen, idConcepto
                                    FROM (SELECT aa.idVenta AS idAuxiliar, aea.fecha,aa.descripcion, aa.total as cantidad, 'AlmacenCeraApicola' AS origen, 2 as tipo, '' AS idConcepto
                                    FROM encabezado_cotizacion aea
                                    INNER JOIN cotizacion_detalle aa ON aea.idVenta = aa.idVenta
                                    WHERE aea.idCliente = :idProveedor AND aea.exportador = 'APICULTOR' AND aea.estado = 'ENTREGADO' AND aa.idSubSubCuenta = 213) tablaProApicolas
                                    UNION
                                    SELECT idAuxiliar, fecha, descripcion, '' AS referencia, '' AS nombreBanco, '' AS nombreCuenta, cantidad, '' as neto, '' as precio, '' as totalCompra, 
                                    tipo, origen, idConcepto
                                    FROM (SELECT aa.idAlmacen AS idAuxiliar, aea.fecha,aa.descripcion, aa.importe AS cantidad, 'AlmacenCeraApicola' AS origen, tipo, aa.clasificacion AS idConcepto
                                    FROM almacenencabezadoapicola aea
                                    INNER JOIN almacenapicola aa ON aea.idAlmacen = aa.idAlmacenEncabezado
                                    WHERE aea.tipoPersona = 1 AND aea.idProveedor = :idProveedor  AND (CASE WHEN aea.tipo = 1 THEN (aa.clasificacion = 2 OR aa.clasificacion = 3) ELSE aa.clasificacion >= 1 END)) tablaProApicolas
                                    
                                    UNION
                                    SELECT idAuxiliar, fecha, descripcion, '' AS referencia, '' AS nombreBanco, '' AS nombreCuenta, cantidad, '' as neto, '' as precio, '' as totalCompra, 
                                    tipo, origen, idConcepto
                                    FROM (SELECT ad.idProductoDerivado AS idAuxiliar, dae.fecha,ad.descripcion, ad.importe AS cantidad, 'AlmacenCeraApicola' AS origen, tipo, ad.clasificacion AS idConcepto
                                    FROM derivadosalmacenencabezado dae
                                    INNER JOIN derivadosalmacendetalle ad ON dae.idEntrada = ad.idEntrada
                                    WHERE dae.tipoPersona = 1 AND dae.idProveedor = :idProveedor  AND (CASE WHEN dae.tipo = 1 THEN (ad.clasificacion = 2 OR ad.clasificacion = 3) ELSE ad.clasificacion >= 1 END)) tablaProDerivados
                                    UNION

                                    SELECT idAuxiliar, fecha, descripcion, '' AS referencia, '' AS nombreBanco, '' AS nombreCuenta, cantidad, '' as neto, '' as precio, '' as totalCompra, 
                                    tipo, origen, idConcepto
                                    FROM (SELECT ad.idProductoDerivado AS idAuxiliar, dae.fecha,ad.descripcion, ad.importe AS cantidad, 'AlmacenCeraApicola' AS origen, tipo, ad.clasificacion AS idConcepto
                                    FROM derivadosalmacenencabezado_salidas dae
                                    INNER JOIN derivadosalmacendetalle_salidas ad ON dae.idSalida = ad.idSalida
                                    WHERE dae.tipoPersona = 1 AND dae.idProveedor = :idProveedor  AND (CASE WHEN dae.tipo = 1 THEN (ad.clasificacion = 2 OR ad.clasificacion = 3) ELSE ad.clasificacion >= 1 END)) tablaProDerivadosSalida
                                    UNION

                                    SELECT '' as idAuxiliar, fecha, descripcion, referencia,'' AS nombreBanco, '' AS nombreCuenta, cantidad, neto, precio, totalCompra, tipo, origen, '' AS idConcepto
                                    FROM(SELECT am.idAlmacen AS referencia, am.fecha, 'Ingreso directo de miel 100% pura' AS descripcion, '' as cantidad,
                                    SUM(al.neto) AS neto, al.precio, am.totalCompra, '0' AS tipo, 'Entrada' AS origen
                                    FROM almacen al
                                    LEFT JOIN almacenencabezado am ON am.idAlmacen = al.idAlmacenEncabezado
                                    WHERE am.idProveedor = :idProveedor AND al.referencia = 0 GROUP BY am.idAlmacen) AS almacen
                                    UNION
                                    SELECT '' as idAuxiliar, fecha, descripcion, referencia,'' AS nombreBanco, '' AS nombreCuenta, cantidad, neto, precio, totalCompra, tipo, origen, '' AS idConcepto
                                    FROM(SELECT ce.idAlmacen AS referencia, ce.fecha, 'Ingreso directo de miel 100% pura' AS descripcion, '' as cantidad,
                                    SUM(cd.neto) AS neto, cd.precio, ce.totalCompra, '0' AS tipo, 'EntradaCubeta' AS origen
                                    FROM cubetasdetalle cd
                                    LEFT JOIN cubetasencabezado ce ON ce.idAlmacen = cd.idAlmacenEncabezado
                                    WHERE ce.idProveedor = :idProveedor AND ce.folioEntradaTambor = 99999 AND cd.referencia = 0                                     GROUP BY ce.idAlmacen) AS almacenCubetas
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
                $proveedor['arregloDeMovimientos'] = [];
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
                $proveedor['saldoInicial'] = $saldoInicial;

                foreach ($sqlDetalle->fetchAll(PDO::FETCH_ASSOC) as $movimiento) {
                    $movimiento['importe'] = is_numeric($movimiento['totalCompra']) ? $movimiento['totalCompra'] : 0;
                    $movimiento['cantidad'] = is_numeric($movimiento['cantidad']) ? $movimiento['cantidad'] : 0;
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
                        if ($movimiento['netoCubeta'] > 0) {
                            $movimiento['kilosNeto'] = $movimiento['neto'] + $movimiento['netoCubeta'];
                            $totalNetoCubetas += $movimiento['netoCubeta'];
                            $movimiento['importe'] = $movimiento['totalCompra'] + $movimiento['totalCompraCC'];
                            $totalImporte = $totalImporte + $movimiento['totalCompraCC'];
                        } else {
                            $movimiento['kilosNeto'] = $movimiento['neto'];
                            $movimiento['importe'] = $movimiento['totalCompra'];
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
                        if ($movimiento['netoCubeta'] > 0) {
                            $movimiento['kilosNeto'] = $movimiento['neto'] + $movimiento['netoCubeta'];
                            $totalNetoCubetas += $movimiento['netoCubeta'];
                            $movimiento['importe'] = $movimiento['totalCompra'] + $movimiento['totalCompraCC'];
                            $totalImporte = $totalImporte + $movimiento['totalCompraCC'];
                        } else {
                            $movimiento['kilosNeto'] = $movimiento['neto'];
                            $movimiento['importe'] = $movimiento['totalCompra'];
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
                    }  else if ($movimiento['origen'] == 'Entrada Mantequilla') {
                        $movimiento['entrada'] = $movimiento['referencia'];
                        $movimiento['deBanco'] = '';
                        $movimiento['deCuenta'] = '';
                        $sqlConCubeta = $con->prepare("SELECT SUM(cd.neto) AS netoCubeta, ce.totalCompra
                                                            FROM cubetasdetalle_mantequilla cd 
                                                            LEFT JOIN cubetasencabezado_mantequilla ce ON ce.idAlmacen = cd.idAlmacenEncabezado
                                                            WHERE ce.folioEntradaTambor = :id");
                        $sqlConCubeta->bindParam(':id', $movimiento['referencia']);
                        $sqlConCubeta->execute();
                        $sqlConCubeta->bindColumn('netoCubeta', $movimiento['netoCubeta']);
                        $sqlConCubeta->bindColumn('totalCompra', $movimiento['totalCompraCC']);
                        $sqlConCubeta->fetch(PDO::FETCH_BOUND);
                        if ($movimiento['netoCubeta'] > 0) {
                            $movimiento['kilosNeto'] = $movimiento['neto'] + $movimiento['netoCubeta'];
                            $totalNetoCubetas += $movimiento['netoCubeta'];
                            $movimiento['importe'] = $movimiento['totalCompra'] + $movimiento['totalCompraCC'];
                            $totalImporte = $totalImporte + $movimiento['totalCompraCC'];
                        } else {
                            $movimiento['kilosNeto'] = $movimiento['neto'];
                            $movimiento['importe'] = $movimiento['totalCompra'];
                        }
                        $movimiento['precioPromedio'] = $movimiento['importe'] / $movimiento['kilosNeto'];
                        $salidas += $movimiento['importe'];
                    } else if ($movimiento['origen'] == 'Entrada Cubeta Mantequilla') {
                        $movimiento['entrada'] = $movimiento['referencia'];
                        $movimiento['deBanco'] = '';
                        $movimiento['deCuenta'] = '';
                        $movimiento['kilosNeto'] = $movimiento['neto'];
                        $movimiento['precioPromedio'] = $movimiento['importe'] / $movimiento['kilosNeto'];
                        $salidas += $movimiento['importe'];
                    } 
                    
                    else if ($movimiento['origen'] == 'Entrada Altiplano') {
                        $movimiento['entrada'] = $movimiento['referencia'];
                        $movimiento['deBanco'] = '';
                        $movimiento['deCuenta'] = '';
                        $sqlConCubeta = $con->prepare("SELECT SUM(cd.neto) AS netoCubeta, ce.totalCompra
                                                            FROM cubetasdetalle_altiplano cd 
                                                            LEFT JOIN cubetasencabezado_altiplano ce ON ce.idAlmacen = cd.idAlmacenEncabezado
                                                            WHERE ce.folioEntradaTambor = :id");
                        $sqlConCubeta->bindParam(':id', $movimiento['referencia']);
                        $sqlConCubeta->execute();
                        $sqlConCubeta->bindColumn('netoCubeta', $movimiento['netoCubeta']);
                        $sqlConCubeta->bindColumn('totalCompra', $movimiento['totalCompraCC']);
                        $sqlConCubeta->fetch(PDO::FETCH_BOUND);
                        if ($movimiento['netoCubeta'] > 0) {
                            $movimiento['kilosNeto'] = $movimiento['neto'] + $movimiento['netoCubeta'];
                            $totalNetoCubetas += $movimiento['netoCubeta'];
                            $movimiento['importe'] = $movimiento['totalCompra'] + $movimiento['totalCompraCC'];
                            $totalImporte = $totalImporte + $movimiento['totalCompraCC'];
                        } else {
                            $movimiento['kilosNeto'] = $movimiento['neto'];
                            $movimiento['importe'] = $movimiento['totalCompra'];
                        }
                        $movimiento['precioPromedio'] = $movimiento['importe'] / $movimiento['kilosNeto'];
                        $salidas += $movimiento['importe'];
                    } else if ($movimiento['origen'] == 'Entrada Cubeta Altiplano') {
                        $movimiento['entrada'] = $movimiento['referencia'];
                        $movimiento['deBanco'] = '';
                        $movimiento['deCuenta'] = '';
                        $movimiento['kilosNeto'] = $movimiento['neto'];
                        $movimiento['precioPromedio'] = $movimiento['importe'] / $movimiento['kilosNeto'];
                        $salidas += $movimiento['importe'];
                    } 

                    else if ($movimiento['origen'] == 'Entrada Aguacate') {
                        $movimiento['entrada'] = $movimiento['referencia'];
                        $movimiento['deBanco'] = '';
                        $movimiento['deCuenta'] = '';
                        $sqlConCubeta = $con->prepare("SELECT SUM(cd.neto) AS netoCubeta, ce.totalCompra
                                                            FROM cubetasdetalle_aguacate cd 
                                                            LEFT JOIN cubetasencabezado_aguacate ce ON ce.idAlmacen = cd.idAlmacenEncabezado
                                                            WHERE ce.folioEntradaTambor = :id");
                        $sqlConCubeta->bindParam(':id', $movimiento['referencia']);
                        $sqlConCubeta->execute();
                        $sqlConCubeta->bindColumn('netoCubeta', $movimiento['netoCubeta']);
                        $sqlConCubeta->bindColumn('totalCompra', $movimiento['totalCompraCC']);
                        $sqlConCubeta->fetch(PDO::FETCH_BOUND);
                        if ($movimiento['netoCubeta'] > 0) {
                            $movimiento['kilosNeto'] = $movimiento['neto'] + $movimiento['netoCubeta'];
                            $totalNetoCubetas += $movimiento['netoCubeta'];
                            $movimiento['importe'] = $movimiento['totalCompra'] + $movimiento['totalCompraCC'];
                            $totalImporte = $totalImporte + $movimiento['totalCompraCC'];
                        } else {
                            $movimiento['kilosNeto'] = $movimiento['neto'];
                            $movimiento['importe'] = $movimiento['totalCompra'];
                        }
                        $movimiento['precioPromedio'] = $movimiento['importe'] / $movimiento['kilosNeto'];
                        $salidas += $movimiento['importe'];
                    } else if ($movimiento['origen'] == 'Entrada Cubeta Aguacate') {
                        $movimiento['entrada'] = $movimiento['referencia'];
                        $movimiento['deBanco'] = '';
                        $movimiento['deCuenta'] = '';
                        $movimiento['kilosNeto'] = $movimiento['neto'];
                        $movimiento['precioPromedio'] = $movimiento['importe'] / $movimiento['kilosNeto'];
                        $salidas += $movimiento['importe'];
                    } 

                    else if ($movimiento['origen'] == 'Entrada Mezquite') {
                        $movimiento['entrada'] = $movimiento['referencia'];
                        $movimiento['deBanco'] = '';
                        $movimiento['deCuenta'] = '';
                        $sqlConCubeta = $con->prepare("SELECT SUM(cd.neto) AS netoCubeta, ce.totalCompra
                                                            FROM cubetasdetalle_mezquite cd 
                                                            LEFT JOIN cubetasencabezado_mezquite ce ON ce.idAlmacen = cd.idAlmacenEncabezado
                                                            WHERE ce.folioEntradaTambor = :id");
                        $sqlConCubeta->bindParam(':id', $movimiento['referencia']);
                        $sqlConCubeta->execute();
                        $sqlConCubeta->bindColumn('netoCubeta', $movimiento['netoCubeta']);
                        $sqlConCubeta->bindColumn('totalCompra', $movimiento['totalCompraCC']);
                        $sqlConCubeta->fetch(PDO::FETCH_BOUND);
                        if ($movimiento['netoCubeta'] > 0) {
                            $movimiento['kilosNeto'] = $movimiento['neto'] + $movimiento['netoCubeta'];
                            $totalNetoCubetas += $movimiento['netoCubeta'];
                            $movimiento['importe'] = $movimiento['totalCompra'] + $movimiento['totalCompraCC'];
                            $totalImporte = $totalImporte + $movimiento['totalCompraCC'];
                        } else {
                            $movimiento['kilosNeto'] = $movimiento['neto'];
                            $movimiento['importe'] = $movimiento['totalCompra'];
                        }
                        $movimiento['precioPromedio'] = $movimiento['importe'] / $movimiento['kilosNeto'];
                        $salidas += $movimiento['importe'];
                    } else if ($movimiento['origen'] == 'Entrada Cubeta Mezquite') {
                        $movimiento['entrada'] = $movimiento['referencia'];
                        $movimiento['deBanco'] = '';
                        $movimiento['deCuenta'] = '';
                        $movimiento['kilosNeto'] = $movimiento['neto'];
                        $movimiento['precioPromedio'] = $movimiento['importe'] / $movimiento['kilosNeto'];
                        $salidas += $movimiento['importe'];
                    } 

                    else if ($movimiento['origen'] == 'Entrada Naranjo') {
                        $movimiento['entrada'] = $movimiento['referencia'];
                        $movimiento['deBanco'] = '';
                        $movimiento['deCuenta'] = '';
                        $sqlConCubeta = $con->prepare("SELECT SUM(cd.neto) AS netoCubeta, ce.totalCompra
                                                            FROM cubetasdetalle_naranjo cd 
                                                            LEFT JOIN cubetasencabezado_naranjo ce ON ce.idAlmacen = cd.idAlmacenEncabezado
                                                            WHERE ce.folioEntradaTambor = :id");
                        $sqlConCubeta->bindParam(':id', $movimiento['referencia']);
                        $sqlConCubeta->execute();
                        $sqlConCubeta->bindColumn('netoCubeta', $movimiento['netoCubeta']);
                        $sqlConCubeta->bindColumn('totalCompra', $movimiento['totalCompraCC']);
                        $sqlConCubeta->fetch(PDO::FETCH_BOUND);
                        if ($movimiento['netoCubeta'] > 0) {
                            $movimiento['kilosNeto'] = $movimiento['neto'] + $movimiento['netoCubeta'];
                            $totalNetoCubetas += $movimiento['netoCubeta'];
                            $movimiento['importe'] = $movimiento['totalCompra'] + $movimiento['totalCompraCC'];
                            $totalImporte = $totalImporte + $movimiento['totalCompraCC'];
                        } else {
                            $movimiento['kilosNeto'] = $movimiento['neto'];
                            $movimiento['importe'] = $movimiento['totalCompra'];
                        }
                        $movimiento['precioPromedio'] = $movimiento['importe'] / $movimiento['kilosNeto'];
                        $salidas += $movimiento['importe'];
                    } else if ($movimiento['origen'] == 'Entrada Cubeta Naranjo') {
                        $movimiento['entrada'] = $movimiento['referencia'];
                        $movimiento['deBanco'] = '';
                        $movimiento['deCuenta'] = '';
                        $movimiento['kilosNeto'] = $movimiento['neto'];
                        $movimiento['precioPromedio'] = $movimiento['importe'] / $movimiento['kilosNeto'];
                        $salidas += $movimiento['importe'];
                    } 
                    
                    else if ($movimiento['origen'] == 'Gastos') {
                        $totalGastosRealizados += $movimiento['cantidad'];
                        $movimiento['gastos'] = $movimiento['cantidad'];
                        $salidas += $movimiento['cantidad'];
                    } else if ($movimiento['origen'] == 'Retenciones') {
                        $totalRetencionesRealizadas += $movimiento['cantidad'];
                        $movimiento['retenciones'] = $movimiento['cantidad'];
                        // $salidas += $movimiento['cantidad'];
                        // $retenciones += $movimiento['cantidad'];
                        $entradas += $movimiento['cantidad'];
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
                        $salidas += $movimiento['cantidad'];
                        if ($totalKilos != 0) {
                            $totalPrecio = $totalImporte / $totalKilos;
                        }
                    }
                    if ($totalKilos != 0) {
                        $totalPrecio = $totalImporte / $totalKilos;
                    }
                    $proveedor['saldoInicial'] = $saldoInicial;
                    $movimiento['saldo'] = $saldoInicial + $entradas - $salidas;
                    // if ($movimiento['saldo'] > 0) {
                    //     // $movimiento['saldo'] -= $retenciones;
                    //     $movimiento['saldo'] += $retenciones;
                    // } else if ($movimiento['saldo'] < 0) {
                    //     $movimiento['saldo'] += $retenciones;
                    // }
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
                    $proveedor['totalRetenciones'] = $totalRetencionesRealizadas;
                    $proveedor['totalVentasCeraApicola'] = $totalVentasCeraApicola;
                    $proveedor['totalCeraApicola'] = $totalIngresosCeraApicola - $totalEgresosCeraApicola;
                    $proveedor['totalSaldo'] = $saldoInicial + $totalBancos + $totalCaja - $totalImporte - $totalDevolucionesMiel + $totalIngresosCeraApicola - $totalEgresosCeraApicola - $totalDevolucionesCeraApicola - $totalGastosRealizados - $totalVentasCeraApicola + $totalRetencionesRealizadas;
                    // $proveedor['totalSaldo'] = $saldoInicial + $totalBancos + $totalCaja - $totalImporte - $totalDevolucionesMiel + $totalIngresosCeraApicola - $totalEgresosCeraApicola - $totalDevolucionesCeraApicola - $totalGastosRealizados - $totalVentasCeraApicola;
                    // if ($proveedor['totalSaldo'] > 0) {
                    //     // $proveedor['totalSaldo'] -= $totalRetencionesRealizadas;
                    //     $proveedor['totalSaldo'] += $totalRetencionesRealizadas;
                    // } else if ($proveedor['totalSaldo'] < 0) {
                    //     $proveedor['totalSaldo'] += $totalRetencionesRealizadas;
                    // }
                    array_push($proveedor['arregloDeMovimientos'], $movimiento);
                }
                array_push($resultado, $proveedor);
            }
        }
        return $resultado;
    } else if (isset($_GET['idMes'])) {
        $idMes = $_GET['idMes'];

        if ($datos->rowCount() >= 1) {
            foreach ($datos->fetchAll(PDO::FETCH_ASSOC) as $proveedor) {
                $idProveedor = $proveedor['idProveedor'];
                $saldoInicial = $proveedor['cantidad'];
                $estado = $proveedor['idEstado'];
                $idComprador = $proveedor['idComprador'];
                $saldoAnterior = 0;

                if (isset($_GET['idComprador'])) {
                    if ($idComprador != $_GET['idComprador']) {
                        continue;
                    }
                }

                if ($idMes > 1) {
                    $sqlAnterior = $con->prepare("SELECT idAuxiliar, fecha, descripcion, referencia, nombreBanco, nombreCuenta, cantidad,'' as neto, '' as precio, '' as totalCompra, tipo, origen, '' AS idConcepto
                        FROM (SELECT a.idAuxiliar, a.fecha, a.descripcion, a.referencia, a.idBanco AS nombreBanco, a.idCuenta AS nombreCuenta, a.cantidad, a.ingresoEgreso AS tipo, 'Banco' AS origen 
                        FROM auxiliardebancos a 
                        WHERE a.ingresoEgreso = 1 AND a.tipoDePersona = 1 AND a.nombreDe = :idProveedor AND SUBSTR(a.fecha FROM 6 FOR 2) < $idMes AND a.idSubcuenta IN $idSubcuentas
                        ) AS tablaAuxiliar
                        UNION
                        SELECT idAuxiliar, fecha, descripcion, referencia,'' AS nombreBanco, '' AS nombreCuenta, cantidad, '' as neto, '' as precio, '' as totalCompra, tipo, origen, idConcepto
                        FROM (SELECT d.idCajaChica AS idAuxiliar, c.fecha, d.descripcion, '' AS referencia,
                        CASE WHEN d.cantidad IS NOT NULL THEN d.cantidad ELSE importe END AS cantidad,      
                        c.tipo, 'Caja' AS origen, d.idConcepto
                        FROM cajachica c 
                        LEFT JOIN cajachicadetalle d On d.idCajaChica = c.idCajaChica
                        WHERE c.tipoDeCliente = 1 AND c.nombre = :idProveedor AND SUBSTR(c.fecha FROM 6 FOR 2) < $idMes AND d.idConcepto IN $idSubcuentas) AS tablaCaja
                        UNION
                        SELECT '' AS idAuxiliar, fecha, descripcion, '' AS referencia,'' AS nombreBanco, '' AS nombreCuenta, cantidad, '' as neto, '' as precio, '' as totalCompra, tipo, origen, '' AS idConcepto
                        FROM (SELECT g.fecha, g.concepto AS descripcion, g.cantidad, '1' AS tipo, 'Gastos' AS origen
                        FROM gastosrealizados g 
                        WHERE g.idProveedor = :idProveedor AND SUBSTR(g.fecha FROM 6 FOR 2) < $idMes) AS gastosRealizados
                        UNION
                        SELECT '' AS idAuxiliar, fecha, descripcion, '' AS referencia,'' AS nombreBanco, '' AS nombreCuenta, cantidad, '' as neto, '' as precio, '' as totalCompra, tipo, origen, '' AS idConcepto
                        FROM (SELECT g.fecha, g.concepto AS descripcion, g.cantidad, '1' AS tipo, 'Retenciones' AS origen
                        FROM retencionesisr g 
                        WHERE g.idProveedor = :idProveedor AND SUBSTR(g.fecha FROM 6 FOR 2) < $idMes) AS retencionesRealizadas
                        UNION
                        SELECT '' AS idAuxiliar, fecha, descripcion, '' AS referencia, '' AS nombreBanco, '' AS nombreCuenta, cantidad, '' as neto, '' as precio, '' as totalCompra, 
                        tipo, origen, idConcepto
                        FROM (SELECT aec.fecha,adc.descripcion, adc.importe AS cantidad, 'AlmacenCeraApicola' AS origen, tipo, adc.clasificacion AS idConcepto
                        FROM almacenencabezadocera aec
                        INNER JOIN almacencera adc ON aec.idAlmacen = adc.idAlmacenEncabezado
                        WHERE aec.tipoPersona = 1 AND aec.idProveedor = :idProveedor AND SUBSTR(aec.fecha FROM 6 FOR 2) < $idMes AND (CASE WHEN aec.tipo = 1 THEN (adc.clasificacion = 2 OR adc.clasificacion = 3) ELSE adc.clasificacion >= 1 END )) tablaCera
                        UNION
                        SELECT idAuxiliar, fecha, descripcion, '' AS referencia, '' AS nombreBanco, '' AS nombreCuenta, cantidad, '' as neto, '' as precio, '' as totalCompra, tipo, origen, idConcepto
                        FROM (SELECT aa.idVenta AS idAuxiliar, aea.fecha,aa.descripcion, aa.total as cantidad, 'AlmacenCeraApicola' AS origen, 2 as tipo, '' AS idConcepto
                        FROM encabezado_cotizacion aea
                        INNER JOIN cotizacion_detalle aa ON aea.idVenta = aa.idVenta
                        WHERE aea.idCliente = :idProveedor AND aea.exportador = 'APICULTOR' AND aea.estado = 'ENTREGADO' AND aa.idSubSubCuenta = 213 AND SUBSTR(aea.fecha FROM 6 FOR 2) < $idMes) tablaProBelleza
                        UNION
                        SELECT idAuxiliar, fecha, descripcion, '' AS referencia, '' AS nombreBanco, '' AS nombreCuenta, cantidad, '' as neto, '' as precio, '' as totalCompra, 
                        tipo, origen, idConcepto
                        FROM (SELECT aa.idAlmacen AS idAuxiliar, aea.fecha,aa.descripcion, aa.importe AS cantidad, 'AlmacenCeraApicola' AS origen, tipo, aa.clasificacion AS idConcepto
                        FROM almacenencabezadoapicola aea
                        INNER JOIN almacenapicola aa ON aea.idAlmacen = aa.idAlmacenEncabezado
                        WHERE aea.tipoPersona = 1 AND aea.idProveedor = :idProveedor AND SUBSTR(aea.fecha FROM 6 FOR 2) < $idMes  AND (CASE WHEN aea.tipo = 1 THEN (aa.clasificacion = 2 OR aa.clasificacion = 3) ELSE aa.clasificacion >= 1 END)) tablaProApicolas

                        UNION
                                    SELECT idAuxiliar, fecha, descripcion, '' AS referencia, '' AS nombreBanco, '' AS nombreCuenta, cantidad, '' as neto, '' as precio, '' as totalCompra, 
                                    tipo, origen, idConcepto
                                    FROM (SELECT ad.idProductoDerivado AS idAuxiliar, dae.fecha,ad.descripcion, ad.importe AS cantidad, 'AlmacenCeraApicola' AS origen, tipo, ad.clasificacion AS idConcepto
                                    FROM derivadosalmacenencabezado dae
                                    INNER JOIN derivadosalmacendetalle ad ON dae.idEntrada = ad.idEntrada
                                    WHERE dae.tipoPersona = 1 AND dae.idProveedor = :idProveedor  AND SUBSTR(dae.fecha FROM 6 FOR 2) < $idMes  AND (CASE WHEN dae.tipo = 1 THEN (ad.clasificacion = 2 OR ad.clasificacion = 3) ELSE ad.clasificacion >= 1 END)) tablaProDerivados
                        UNION
                                    SELECT idAuxiliar, fecha, descripcion, '' AS referencia, '' AS nombreBanco, '' AS nombreCuenta, cantidad, '' as neto, '' as precio, '' as totalCompra, 
                                    tipo, origen, idConcepto
                                    FROM (SELECT ad.idProductoDerivado AS idAuxiliar, dae.fecha,ad.descripcion, ad.importe AS cantidad, 'AlmacenCeraApicola' AS origen, tipo, ad.clasificacion AS idConcepto
                                    FROM derivadosalmacenencabezado_salidas dae
                                    INNER JOIN derivadosalmacendetalle_salidas ad ON dae.idSalida = ad.idSalida
                                    WHERE dae.tipoPersona = 1 AND dae.idProveedor = :idProveedor  AND SUBSTR(dae.fecha FROM 6 FOR 2) < $idMes  AND (CASE WHEN dae.tipo = 1 THEN (ad.clasificacion = 2 OR ad.clasificacion = 3) ELSE ad.clasificacion >= 1 END)) tablaProDerivadosSalida
                        UNION

                        SELECT '' AS idAuxiliar, fecha, descripcion, referencia,'' AS nombreBanco, '' AS nombreCuenta, cantidad, neto, precio, totalCompra, tipo, origen, '' AS idConcepto
                        FROM(SELECT am.idAlmacen AS referencia, am.fecha, 'Ingreso directo de miel 100% pura' AS descripcion, '' as cantidad,
                        SUM(al.neto) AS neto, al.precio, am.totalCompra, '0' AS tipo, 'Entrada' AS origen
                        FROM almacen al
                        LEFT JOIN almacenencabezado am ON am.idAlmacen = al.idAlmacenEncabezado
                        WHERE am.idProveedor = :idProveedor AND al.referencia = 0 AND SUBSTR(am.fecha FROM 6 FOR 2) < $idMes
                        GROUP BY am.idAlmacen) AS almacen
                        UNION
                        SELECT '' AS idAuxiliar, fecha, descripcion, referencia,'' AS nombreBanco, '' AS nombreCuenta, cantidad, neto, precio, totalCompra, tipo, origen, '' AS idConcepto
                        FROM(SELECT ce.idAlmacen AS referencia, ce.fecha, 'Ingreso directo de miel 100% pura' AS descripcion, '' as cantidad,
                        SUM(cd.neto) AS neto, cd.precio, ce.totalCompra, '0' AS tipo, 'EntradaCubeta' AS origen
                        FROM cubetasdetalle cd
                        LEFT JOIN cubetasencabezado ce ON ce.idAlmacen = cd.idAlmacenEncabezado
                        WHERE ce.idProveedor = :idProveedor AND cd.referencia = 0 AND ce.folioEntradaTambor = 99999 AND SUBSTR(ce.fecha FROM 6 FOR 2) < $idMes
                        GROUP BY ce.idAlmacen) AS almacenCubetas
                        UNION
                        SELECT '' AS idAuxiliar, fecha, descripcion, referencia,'' AS nombreBanco, '' AS nombreCuenta, cantidad, neto, precio, totalCompra, tipo, origen, '' AS idConcepto
                        FROM(SELECT am.idAlmacen AS referencia, am.fecha, 'Ingreso directo de miel 100% orgánica' AS descripcion, '' as cantidad,
                        SUM(al.neto) AS neto, al.precio, am.totalCompra, '0' AS tipo, 'Entrada Organico' AS origen
                        FROM almacen_organico al
                        LEFT JOIN almacenencabezado_organico am ON am.idAlmacen = al.idAlmacenEncabezado
                        WHERE am.idProveedor = :idProveedor AND al.referencia = 0 AND SUBSTR(am.fecha FROM 6 FOR 2) < $idMes
                        GROUP BY am.idAlmacen) AS almacen
                        UNION
                        SELECT '' AS idAuxiliar, fecha, descripcion, referencia,'' AS nombreBanco, '' AS nombreCuenta, cantidad, neto, precio, totalCompra, tipo, origen, '' AS idConcepto
                        FROM(SELECT ce.idAlmacen AS referencia, ce.fecha, 'Ingreso directo de miel 100% orgánica' AS descripcion, '' as cantidad,
                        SUM(cd.neto) AS neto, cd.precio, ce.totalCompra, '0' AS tipo, 'Entrada Cubeta Organico' AS origen
                        FROM cubetasdetalle_organico cd
                        LEFT JOIN cubetasencabezado_organico ce ON ce.idAlmacen = cd.idAlmacenEncabezado
                        WHERE ce.idProveedor = :idProveedor AND ce.folioEntradaTambor = 99999 AND cd.referencia = 0 AND SUBSTR(ce.fecha FROM 6 FOR 2) < $idMes
                        GROUP BY ce.idAlmacen) AS almacenCubetas  
                        UNION
                        SELECT '' AS idAuxiliar, fecha, descripcion, referencia,'' AS nombreBanco, '' AS nombreCuenta, cantidad, neto, precio, totalCompra, tipo, origen, '' AS idConcepto
                        FROM(SELECT am.idAlmacen AS referencia, am.fecha, 'Ingreso directo de miel 100% mantequilla' AS descripcion, '' as cantidad,
                        SUM(al.neto) AS neto, al.precio, am.totalCompra, '0' AS tipo, 'Entrada Mantequilla' AS origen
                        FROM almacen_mantequilla al
                        LEFT JOIN almacenencabezado_mantequilla am ON am.idAlmacen = al.idAlmacenEncabezado
                        WHERE am.idProveedor = :idProveedor AND al.referencia = 0 AND SUBSTR(am.fecha FROM 6 FOR 2) < $idMes
                        GROUP BY am.idAlmacen) AS almacen
                        UNION
                        SELECT '' AS idAuxiliar, fecha, descripcion, referencia,'' AS nombreBanco, '' AS nombreCuenta, cantidad, neto, precio, totalCompra, tipo, origen, '' AS idConcepto
                        FROM(SELECT ce.idAlmacen AS referencia, ce.fecha, 'Ingreso directo de miel 100% mantequilla' AS descripcion, '' as cantidad,
                        SUM(cd.neto) AS neto, cd.precio, ce.totalCompra, '0' AS tipo, 'Entrada Cubeta Mantequilla' AS origen
                        FROM cubetasdetalle_mantequilla cd
                        LEFT JOIN cubetasencabezado_mantequilla ce ON ce.idAlmacen = cd.idAlmacenEncabezado
                        WHERE ce.idProveedor = :idProveedor AND ce.folioEntradaTambor = 99999 AND cd.referencia = 0 AND SUBSTR(ce.fecha FROM 6 FOR 2) < $idMes
                        GROUP BY ce.idAlmacen) AS almacenCubetas  
                        UNION

                        SELECT '' AS idAuxiliar, fecha, descripcion, referencia,'' AS nombreBanco, '' AS nombreCuenta, cantidad, neto, precio, totalCompra, tipo, origen, '' AS idConcepto
                        FROM(SELECT am.idAlmacen AS referencia, am.fecha, 'Ingreso directo de miel 100% altiplano' AS descripcion, '' as cantidad,
                        SUM(al.neto) AS neto, al.precio, am.totalCompra, '0' AS tipo, 'Entrada Altiplano' AS origen
                        FROM almacen_altiplano al
                        LEFT JOIN almacenencabezado_altiplano am ON am.idAlmacen = al.idAlmacenEncabezado
                        WHERE am.idProveedor = :idProveedor AND al.referencia = 0 AND SUBSTR(am.fecha FROM 6 FOR 2) < $idMes
                        GROUP BY am.idAlmacen) AS almacen
                        UNION
                        SELECT '' AS idAuxiliar, fecha, descripcion, referencia,'' AS nombreBanco, '' AS nombreCuenta, cantidad, neto, precio, totalCompra, tipo, origen, '' AS idConcepto
                        FROM(SELECT ce.idAlmacen AS referencia, ce.fecha, 'Ingreso directo de miel 100% altiplano' AS descripcion, '' as cantidad,
                        SUM(cd.neto) AS neto, cd.precio, ce.totalCompra, '0' AS tipo, 'Entrada Cubeta Altiplano' AS origen
                        FROM cubetasdetalle_altiplano cd
                        LEFT JOIN cubetasencabezado_altiplano ce ON ce.idAlmacen = cd.idAlmacenEncabezado
                        WHERE ce.idProveedor = :idProveedor AND ce.folioEntradaTambor = 99999 AND cd.referencia = 0 AND SUBSTR(ce.fecha FROM 6 FOR 2) < $idMes
                        GROUP BY ce.idAlmacen) AS almacenCubetas  
                        UNION

                        SELECT '' AS idAuxiliar, fecha, descripcion, referencia,'' AS nombreBanco, '' AS nombreCuenta, cantidad, neto, precio, totalCompra, tipo, origen, '' AS idConcepto
                        FROM(SELECT am.idAlmacen AS referencia, am.fecha, 'Ingreso directo de miel 100% aguacate' AS descripcion, '' as cantidad,
                        SUM(al.neto) AS neto, al.precio, am.totalCompra, '0' AS tipo, 'Entrada Aguacate' AS origen
                        FROM almacen_aguacate al
                        LEFT JOIN almacenencabezado_aguacate am ON am.idAlmacen = al.idAlmacenEncabezado
                        WHERE am.idProveedor = :idProveedor AND al.referencia = 0 AND SUBSTR(am.fecha FROM 6 FOR 2) < $idMes
                        GROUP BY am.idAlmacen) AS almacen
                        UNION
                        SELECT '' AS idAuxiliar, fecha, descripcion, referencia,'' AS nombreBanco, '' AS nombreCuenta, cantidad, neto, precio, totalCompra, tipo, origen, '' AS idConcepto
                        FROM(SELECT ce.idAlmacen AS referencia, ce.fecha, 'Ingreso directo de miel 100% aguacate' AS descripcion, '' as cantidad,
                        SUM(cd.neto) AS neto, cd.precio, ce.totalCompra, '0' AS tipo, 'Entrada Cubeta Aguacate' AS origen
                        FROM cubetasdetalle_aguacate cd
                        LEFT JOIN cubetasencabezado_aguacate ce ON ce.idAlmacen = cd.idAlmacenEncabezado
                        WHERE ce.idProveedor = :idProveedor AND ce.folioEntradaTambor = 99999 AND cd.referencia = 0 AND SUBSTR(ce.fecha FROM 6 FOR 2) < $idMes
                        GROUP BY ce.idAlmacen) AS almacenCubetas  
                        UNION

                        SELECT '' AS idAuxiliar, fecha, descripcion, referencia,'' AS nombreBanco, '' AS nombreCuenta, cantidad, neto, precio, totalCompra, tipo, origen, '' AS idConcepto
                        FROM(SELECT am.idAlmacen AS referencia, am.fecha, 'Ingreso directo de miel 100% mezquite' AS descripcion, '' as cantidad,
                        SUM(al.neto) AS neto, al.precio, am.totalCompra, '0' AS tipo, 'Entrada Mezquite' AS origen
                        FROM almacen_mezquite al
                        LEFT JOIN almacenencabezado_mezquite am ON am.idAlmacen = al.idAlmacenEncabezado
                        WHERE am.idProveedor = :idProveedor AND al.referencia = 0 AND SUBSTR(am.fecha FROM 6 FOR 2) < $idMes
                        GROUP BY am.idAlmacen) AS almacen
                        UNION
                        SELECT '' AS idAuxiliar, fecha, descripcion, referencia,'' AS nombreBanco, '' AS nombreCuenta, cantidad, neto, precio, totalCompra, tipo, origen, '' AS idConcepto
                        FROM(SELECT ce.idAlmacen AS referencia, ce.fecha, 'Ingreso directo de miel 100% mezquite' AS descripcion, '' as cantidad,
                        SUM(cd.neto) AS neto, cd.precio, ce.totalCompra, '0' AS tipo, 'Entrada Cubeta Mezquite' AS origen
                        FROM cubetasdetalle_mezquite cd
                        LEFT JOIN cubetasencabezado_mezquite ce ON ce.idAlmacen = cd.idAlmacenEncabezado
                        WHERE ce.idProveedor = :idProveedor AND ce.folioEntradaTambor = 99999 AND cd.referencia = 0 AND SUBSTR(ce.fecha FROM 6 FOR 2) < $idMes
                        GROUP BY ce.idAlmacen) AS almacenCubetas  
                        UNION

                        SELECT '' AS idAuxiliar, fecha, descripcion, referencia,'' AS nombreBanco, '' AS nombreCuenta, cantidad, neto, precio, totalCompra, tipo, origen, '' AS idConcepto
                        FROM(SELECT am.idAlmacen AS referencia, am.fecha, 'Ingreso directo de miel 100% naranjo' AS descripcion, '' as cantidad,
                        SUM(al.neto) AS neto, al.precio, am.totalCompra, '0' AS tipo, 'Entrada Naranjo' AS origen
                        FROM almacen_naranjo al
                        LEFT JOIN almacenencabezado_naranjo am ON am.idAlmacen = al.idAlmacenEncabezado
                        WHERE am.idProveedor = :idProveedor AND al.referencia = 0 AND SUBSTR(am.fecha FROM 6 FOR 2) < $idMes
                        GROUP BY am.idAlmacen) AS almacen
                        UNION
                        SELECT '' AS idAuxiliar, fecha, descripcion, referencia,'' AS nombreBanco, '' AS nombreCuenta, cantidad, neto, precio, totalCompra, tipo, origen, '' AS idConcepto
                        FROM(SELECT ce.idAlmacen AS referencia, ce.fecha, 'Ingreso directo de miel 100% naranjo' AS descripcion, '' as cantidad,
                        SUM(cd.neto) AS neto, cd.precio, ce.totalCompra, '0' AS tipo, 'Entrada Cubeta Naranjo' AS origen
                        FROM cubetasdetalle_naranjo cd
                        LEFT JOIN cubetasencabezado_naranjo ce ON ce.idAlmacen = cd.idAlmacenEncabezado
                        WHERE ce.idProveedor = :idProveedor AND ce.folioEntradaTambor = 99999 AND cd.referencia = 0 AND SUBSTR(ce.fecha FROM 6 FOR 2) < $idMes
                        GROUP BY ce.idAlmacen) AS almacenCubetas  
                        UNION
                        SELECT idAuxiliar, fecha, descripcion, referencia, nombreBanco, nombreCuenta, cantidad,'' as neto, '' as precio, '' as totalCompra, tipo, origen, '' AS idConcepto
                        FROM (SELECT a.idAuxiliar, a.fecha, a.descripcion, a.referencia, a.idBanco AS nombreBanco, a.idCuenta AS nombreCuenta, a.cantidad, a.ingresoEgreso AS tipo, 'devolucionBanco' AS origen
                        FROM auxiliardebancos a
                        WHERE a.ingresoEgreso = 0 AND a.idSubcuenta IN $idSubcuentas AND a.tipoDePersona = 1 AND a.nombreDe = :idProveedor
                        ) AS devolucionBancos
                        ORDER BY fecha ASC");
                    $sqlAnterior->bindParam(':idProveedor', $idProveedor);
                    $sqlAnterior->execute();
                    $totalBancosAnterior = 0;
                    $totalCajaAnterior = 0;
                    $totalImporteAnterior = 0;
                    $totalDevolucionesMielAnterior = 0;
                    $totalGastosRealizadosAnterior = 0;
                    $totalRetencionesRealizadasAnterior = 0;
                    $totalIngresosCeraApicolaAnterior = 0;
                    $totalEgresosCeraApicolaAnterior = 0;
                    $totalDevolucionesCeraApicolaAnterior = 0;
                    $totalVentasCeraApicolaAnterior = 0;

                    foreach ($sqlAnterior->fetchAll(PDO::FETCH_ASSOC) as $movimientoMesAnterior) {
                        $totalImporteAnterior += is_numeric($movimientoMesAnterior['totalCompra']) ? $movimientoMesAnterior['totalCompra'] : 0;
                        if ($movimientoMesAnterior['origen'] == 'Banco') {
                            $totalBancosAnterior += $movimientoMesAnterior['cantidad'];
                        } else if ($movimientoMesAnterior['origen'] == 'Caja') {
                            if ($movimientoMesAnterior['tipo'] == '1') {
                                $totalCajaAnterior += $movimientoMesAnterior['cantidad'];
                            } else if ($movimientoMesAnterior['tipo'] == '0') {
                                $totalVentasCeraApicolaAnterior += $movimientoMesAnterior['cantidad'];
                            }
                        } else if ($movimientoMesAnterior['origen'] == 'Gastos') {
                            $totalGastosRealizadosAnterior += $movimientoMesAnterior['cantidad'];
                        } else if ($movimientoMesAnterior['origen'] == 'Retenciones') {
                            $totalRetencionesRealizadasAnterior += $movimientoMesAnterior['cantidad'];
                        } else if ($movimientoMesAnterior['origen'] == 'AlmacenCeraApicola') {
                            if ($movimientoMesAnterior['tipo'] == '1') {   //Entradas (Egresos)
                                $totalEgresosCeraApicolaAnterior += $movimientoMesAnterior['cantidad'];
                            } else if ($movimientoMesAnterior['tipo'] == '2') {   //Salidas (Ingresos)
                                $totalIngresosCeraApicolaAnterior += $movimientoMesAnterior['cantidad'];
                            }
                        }
                        $saldoAnterior = $totalBancosAnterior + $totalCajaAnterior - $totalImporteAnterior - $totalDevolucionesMielAnterior + $totalIngresosCeraApicolaAnterior - $totalEgresosCeraApicolaAnterior - $totalDevolucionesCeraApicolaAnterior - $totalGastosRealizadosAnterior - $totalVentasCeraApicolaAnterior - $totalRetencionesRealizadasAnterior;
                    }
                }

                $sqlDetalle = $con->prepare("SELECT idAuxiliar, fecha, descripcion, referencia, nombreBanco, nombreCuenta, cantidad,'' as neto, '' as precio, '' as totalCompra, tipo, origen, '' AS idConcepto
                    FROM (SELECT a.idAuxiliar, a.fecha, a.descripcion, a.referencia, a.idBanco AS nombreBanco, a.idCuenta AS nombreCuenta, a.cantidad, a.ingresoEgreso AS tipo, 'Banco' AS origen 
                    FROM auxiliardebancos a 
                    WHERE a.ingresoEgreso = 1 AND a.tipoDePersona = 1 AND a.nombreDe = :idProveedor AND SUBSTR(a.fecha FROM 6 FOR 2) = $idMes AND a.idSubcuenta IN $idSubcuentas
                    ) AS tablaAuxiliar
                    UNION
                    SELECT idAuxiliar, fecha, descripcion, referencia,'' AS nombreBanco, '' AS nombreCuenta, cantidad, '' as neto, '' as precio, '' as totalCompra, tipo, origen, idConcepto
                    FROM (SELECT d.idCajaChica AS idAuxiliar, c.fecha, d.descripcion, '' AS referencia,
                    CASE WHEN d.cantidad IS NOT NULL THEN d.cantidad ELSE importe END AS cantidad,      
                    c.tipo, 'Caja' AS origen, d.idConcepto
                    FROM cajachica c 
                    LEFT JOIN cajachicadetalle d On d.idCajaChica = c.idCajaChica
                    WHERE c.tipoDeCliente = 1 AND c.nombre = :idProveedor AND SUBSTR(c.fecha FROM 6 FOR 2) = $idMes AND d.idConcepto IN $idSubcuentas) AS tablaCaja
                    UNION
                    SELECT '' AS idAuxiliar, fecha, descripcion, '' AS referencia,'' AS nombreBanco, '' AS nombreCuenta, cantidad, '' as neto, '' as precio, '' as totalCompra, tipo, origen, '' AS idConcepto
                    FROM (SELECT g.fecha, g.concepto AS descripcion, g.cantidad, '1' AS tipo, 'Gastos' AS origen
                    FROM gastosrealizados g 
                    WHERE g.idProveedor = :idProveedor AND SUBSTR(g.fecha FROM 6 FOR 2) = $idMes) AS gastosRealizados
                    UNION
                    SELECT '' AS idAuxiliar, fecha, descripcion, '' AS referencia,'' AS nombreBanco, '' AS nombreCuenta, cantidad, '' as neto, '' as precio, '' as totalCompra, tipo, origen, '' AS idConcepto
                    FROM (SELECT g.fecha, g.concepto AS descripcion, g.cantidad, '1' AS tipo, 'Retenciones' AS origen
                    FROM retencionesisr g 
                    WHERE g.idProveedor = :idProveedor AND SUBSTR(g.fecha FROM 6 FOR 2) = $idMes) AS retencionesRealizadas
                    UNION
                    SELECT '' AS idAuxiliar, fecha, descripcion, '' AS referencia, '' AS nombreBanco, '' AS nombreCuenta, cantidad, '' as neto, '' as precio, '' as totalCompra, 
                    tipo, origen, idConcepto
                    FROM (SELECT aec.fecha,adc.descripcion, adc.importe AS cantidad, 'AlmacenCeraApicola' AS origen, tipo, adc.clasificacion AS idConcepto
                    FROM almacenencabezadocera aec
                    INNER JOIN almacencera adc ON aec.idAlmacen = adc.idAlmacenEncabezado
                    WHERE aec.tipoPersona = 1 AND aec.idProveedor = :idProveedor AND SUBSTR(aec.fecha FROM 6 FOR 2) = $idMes AND (CASE WHEN aec.tipo = 1 THEN (adc.clasificacion = 2 OR adc.clasificacion = 3) ELSE adc.clasificacion >= 1 END )) tablaCera
                    UNION
                    SELECT idAuxiliar, fecha, descripcion, '' AS referencia, '' AS nombreBanco, '' AS nombreCuenta, cantidad, '' as neto, '' as precio, '' as totalCompra, tipo, origen, idConcepto
                    FROM (SELECT aa.idVenta AS idAuxiliar, aea.fecha,aa.descripcion, aa.total as cantidad, 'AlmacenCeraApicola' AS origen, 2 as tipo, '' AS idConcepto
                    FROM encabezado_cotizacion aea
                    INNER JOIN cotizacion_detalle aa ON aea.idVenta = aa.idVenta
                    WHERE aea.idCliente = :idProveedor AND aea.exportador = 'APICULTOR' AND aea.estado = 'ENTREGADO' AND aa.idSubSubCuenta = 213 AND SUBSTR(aea.fecha FROM 6 FOR 2) = $idMes) tablaProBelleza
                    UNION
                    SELECT idAuxiliar, fecha, descripcion, '' AS referencia, '' AS nombreBanco, '' AS nombreCuenta, cantidad, '' as neto, '' as precio, '' as totalCompra, 
                    tipo, origen, idConcepto
                    FROM (SELECT aa.idAlmacen AS idAuxiliar, aea.fecha, aa.descripcion, aa.importe AS cantidad, 'AlmacenCeraApicola' AS origen, tipo, aa.clasificacion AS idConcepto
                    FROM almacenencabezadoapicola aea
                    INNER JOIN almacenapicola aa ON aea.idAlmacen = aa.idAlmacenEncabezado
                    WHERE aea.tipoPersona = 1 AND aea.idProveedor = :idProveedor AND SUBSTR(aea.fecha FROM 6 FOR 2) = $idMes  AND (CASE WHEN aea.tipo = 1 THEN (aa.clasificacion = 2 OR aa.clasificacion = 3) ELSE aa.clasificacion >= 1 END)) tablaProApicolas
                    UNION

                    SELECT idAuxiliar, fecha, descripcion, '' AS referencia, '' AS nombreBanco, '' AS nombreCuenta, cantidad, '' as neto, '' as precio, '' as totalCompra, 
                    tipo, origen, idConcepto
                    FROM (SELECT ad.idProductoDerivado AS idAuxiliar, dae.fecha, ad.descripcion, ad.importe AS cantidad, 'AlmacenCeraApicola' AS origen, tipo, ad.clasificacion AS idConcepto
                    FROM derivadosalmacenencabezado dae
                    INNER JOIN derivadosalmacendetalle ad ON dae.idEntrada = ad.idEntrada
                    WHERE dae.tipoPersona = 1 AND dae.idProveedor = :idProveedor AND SUBSTR(dae.fecha FROM 6 FOR 2) = $idMes  AND (CASE WHEN dae.tipo = 1 THEN (ad.clasificacion = 2 OR ad.clasificacion = 3) ELSE ad.clasificacion >= 1 END)) tablaProDerivados
                    
                    UNION

                    SELECT idAuxiliar, fecha, descripcion, '' AS referencia, '' AS nombreBanco, '' AS nombreCuenta, cantidad, '' as neto, '' as precio, '' as totalCompra, 
                    tipo, origen, idConcepto
                    FROM (SELECT ad.idProductoDerivado AS idAuxiliar, dae.fecha, ad.descripcion, ad.importe AS cantidad, 'AlmacenCeraApicola' AS origen, tipo, ad.clasificacion AS idConcepto
                    FROM derivadosalmacenencabezado_salidas dae
                    INNER JOIN derivadosalmacendetalle_salidas ad ON dae.idSalida = ad.idSalida
                    WHERE dae.tipoPersona = 1 AND dae.idProveedor = :idProveedor AND SUBSTR(dae.fecha FROM 6 FOR 2) = $idMes  AND (CASE WHEN dae.tipo = 1 THEN (ad.clasificacion = 2 OR ad.clasificacion = 3) ELSE ad.clasificacion >= 1 END)) tablaProDerivadosSalida
                    
                    UNION

                    SELECT '' AS idAuxiliar, fecha, descripcion, referencia,'' AS nombreBanco, '' AS nombreCuenta, cantidad, neto, precio, totalCompra, tipo, origen, '' AS idConcepto
                    FROM(SELECT am.idAlmacen AS referencia, am.fecha, 'Ingreso directo de miel 100% pura' AS descripcion, '' as cantidad,
                    SUM(al.neto) AS neto, al.precio, am.totalCompra, '0' AS tipo, 'Entrada' AS origen
                    FROM almacen al
                    LEFT JOIN almacenencabezado am ON am.idAlmacen = al.idAlmacenEncabezado
                    WHERE am.idProveedor = :idProveedor AND al.referencia = 0 AND SUBSTR(am.fecha FROM 6 FOR 2) = $idMes
                    GROUP BY am.idAlmacen) AS almacen
                    UNION
                    SELECT '' AS idAuxiliar, fecha, descripcion, referencia,'' AS nombreBanco, '' AS nombreCuenta, cantidad, neto, precio, totalCompra, tipo, origen, '' AS idConcepto
                    FROM(SELECT ce.idAlmacen AS referencia, ce.fecha, 'Ingreso directo de miel 100% pura' AS descripcion, '' as cantidad,
                    SUM(cd.neto) AS neto, cd.precio, ce.totalCompra, '0' AS tipo, 'EntradaCubeta' AS origen
                    FROM cubetasdetalle cd
                    LEFT JOIN cubetasencabezado ce ON ce.idAlmacen = cd.idAlmacenEncabezado
                    WHERE ce.idProveedor = :idProveedor AND cd.referencia = 0 AND ce.folioEntradaTambor = 99999 AND SUBSTR(ce.fecha FROM 6 FOR 2) = $idMes
                    GROUP BY ce.idAlmacen) AS almacenCubetas
                    UNION
                    SELECT '' AS idAuxiliar, fecha, descripcion, referencia,'' AS nombreBanco, '' AS nombreCuenta, cantidad, neto, precio, totalCompra, tipo, origen, '' AS idConcepto
                    FROM(SELECT am.idAlmacen AS referencia, am.fecha, 'Ingreso directo de miel 100% orgánica' AS descripcion, '' as cantidad,
                    SUM(al.neto) AS neto, al.precio, am.totalCompra, '0' AS tipo, 'Entrada Organico' AS origen
                    FROM almacen_organico al
                    LEFT JOIN almacenencabezado_organico am ON am.idAlmacen = al.idAlmacenEncabezado
                    WHERE am.idProveedor = :idProveedor AND al.referencia = 0 AND SUBSTR(am.fecha FROM 6 FOR 2) = $idMes
                    GROUP BY am.idAlmacen) AS almacen
                    UNION
                    SELECT '' AS idAuxiliar, fecha, descripcion, referencia,'' AS nombreBanco, '' AS nombreCuenta, cantidad, neto, precio, totalCompra, tipo, origen, '' AS idConcepto
                    FROM(SELECT ce.idAlmacen AS referencia, ce.fecha, 'Ingreso directo de miel 100% orgánica' AS descripcion, '' as cantidad,
                    SUM(cd.neto) AS neto, cd.precio, ce.totalCompra, '0' AS tipo, 'Entrada Cubeta Organico' AS origen
                    FROM cubetasdetalle_organico cd
                    LEFT JOIN cubetasencabezado_organico ce ON ce.idAlmacen = cd.idAlmacenEncabezado
                    WHERE ce.idProveedor = :idProveedor AND ce.folioEntradaTambor = 99999 AND cd.referencia = 0 AND SUBSTR(ce.fecha FROM 6 FOR 2) = $idMes
                    GROUP BY ce.idAlmacen) AS almacenCubetas     
                    UNION
                    SELECT '' AS idAuxiliar, fecha, descripcion, referencia,'' AS nombreBanco, '' AS nombreCuenta, cantidad, neto, precio, totalCompra, tipo, origen, '' AS idConcepto
                    FROM(SELECT am.idAlmacen AS referencia, am.fecha, 'Ingreso directo de miel 100% mantequilla' AS descripcion, '' as cantidad,
                    SUM(al.neto) AS neto, al.precio, am.totalCompra, '0' AS tipo, 'Entrada Mantequilla' AS origen
                    FROM almacen_mantequilla al
                    LEFT JOIN almacenencabezado_mantequilla am ON am.idAlmacen = al.idAlmacenEncabezado
                    WHERE am.idProveedor = :idProveedor AND al.referencia = 0 AND SUBSTR(am.fecha FROM 6 FOR 2) = $idMes
                    GROUP BY am.idAlmacen) AS almacen
                    UNION
                    SELECT '' AS idAuxiliar, fecha, descripcion, referencia,'' AS nombreBanco, '' AS nombreCuenta, cantidad, neto, precio, totalCompra, tipo, origen, '' AS idConcepto
                    FROM(SELECT ce.idAlmacen AS referencia, ce.fecha, 'Ingreso directo de miel 100% mantequilla' AS descripcion, '' as cantidad,
                    SUM(cd.neto) AS neto, cd.precio, ce.totalCompra, '0' AS tipo, 'Entrada Cubeta Mantequilla' AS origen
                    FROM cubetasdetalle_mantequilla cd
                    LEFT JOIN cubetasencabezado_mantequilla ce ON ce.idAlmacen = cd.idAlmacenEncabezado
                    WHERE ce.idProveedor = :idProveedor AND ce.folioEntradaTambor = 99999 AND cd.referencia = 0 AND SUBSTR(ce.fecha FROM 6 FOR 2) = $idMes
                    GROUP BY ce.idAlmacen) AS almacenCubetas     
                    UNION

                    SELECT '' AS idAuxiliar, fecha, descripcion, referencia,'' AS nombreBanco, '' AS nombreCuenta, cantidad, neto, precio, totalCompra, tipo, origen, '' AS idConcepto
                    FROM(SELECT am.idAlmacen AS referencia, am.fecha, 'Ingreso directo de miel 100% altiplano' AS descripcion, '' as cantidad,
                    SUM(al.neto) AS neto, al.precio, am.totalCompra, '0' AS tipo, 'Entrada Altiplano' AS origen
                    FROM almacen_altiplano al
                    LEFT JOIN almacenencabezado_altiplano am ON am.idAlmacen = al.idAlmacenEncabezado
                    WHERE am.idProveedor = :idProveedor AND al.referencia = 0 AND SUBSTR(am.fecha FROM 6 FOR 2) = $idMes
                    GROUP BY am.idAlmacen) AS almacen
                    UNION
                    SELECT '' AS idAuxiliar, fecha, descripcion, referencia,'' AS nombreBanco, '' AS nombreCuenta, cantidad, neto, precio, totalCompra, tipo, origen, '' AS idConcepto
                    FROM(SELECT ce.idAlmacen AS referencia, ce.fecha, 'Ingreso directo de miel 100% altiplano' AS descripcion, '' as cantidad,
                    SUM(cd.neto) AS neto, cd.precio, ce.totalCompra, '0' AS tipo, 'Entrada Cubeta Altiplano' AS origen
                    FROM cubetasdetalle_altiplano cd
                    LEFT JOIN cubetasencabezado_altiplano ce ON ce.idAlmacen = cd.idAlmacenEncabezado
                    WHERE ce.idProveedor = :idProveedor AND ce.folioEntradaTambor = 99999 AND cd.referencia = 0 AND SUBSTR(ce.fecha FROM 6 FOR 2) = $idMes
                    GROUP BY ce.idAlmacen) AS almacenCubetas     
                    UNION

                    SELECT '' AS idAuxiliar, fecha, descripcion, referencia,'' AS nombreBanco, '' AS nombreCuenta, cantidad, neto, precio, totalCompra, tipo, origen, '' AS idConcepto
                    FROM(SELECT am.idAlmacen AS referencia, am.fecha, 'Ingreso directo de miel 100% aguacate' AS descripcion, '' as cantidad,
                    SUM(al.neto) AS neto, al.precio, am.totalCompra, '0' AS tipo, 'Entrada Aguacate' AS origen
                    FROM almacen_aguacate al
                    LEFT JOIN almacenencabezado_aguacate am ON am.idAlmacen = al.idAlmacenEncabezado
                    WHERE am.idProveedor = :idProveedor AND al.referencia = 0 AND SUBSTR(am.fecha FROM 6 FOR 2) = $idMes
                    GROUP BY am.idAlmacen) AS almacen
                    UNION
                    SELECT '' AS idAuxiliar, fecha, descripcion, referencia,'' AS nombreBanco, '' AS nombreCuenta, cantidad, neto, precio, totalCompra, tipo, origen, '' AS idConcepto
                    FROM(SELECT ce.idAlmacen AS referencia, ce.fecha, 'Ingreso directo de miel 100% aguacate' AS descripcion, '' as cantidad,
                    SUM(cd.neto) AS neto, cd.precio, ce.totalCompra, '0' AS tipo, 'Entrada Cubeta Aguacate' AS origen
                    FROM cubetasdetalle_aguacate cd
                    LEFT JOIN cubetasencabezado_aguacate ce ON ce.idAlmacen = cd.idAlmacenEncabezado
                    WHERE ce.idProveedor = :idProveedor AND ce.folioEntradaTambor = 99999 AND cd.referencia = 0 AND SUBSTR(ce.fecha FROM 6 FOR 2) = $idMes
                    GROUP BY ce.idAlmacen) AS almacenCubetas     
                    UNION

                    SELECT '' AS idAuxiliar, fecha, descripcion, referencia,'' AS nombreBanco, '' AS nombreCuenta, cantidad, neto, precio, totalCompra, tipo, origen, '' AS idConcepto
                    FROM(SELECT am.idAlmacen AS referencia, am.fecha, 'Ingreso directo de miel 100% mezquite' AS descripcion, '' as cantidad,
                    SUM(al.neto) AS neto, al.precio, am.totalCompra, '0' AS tipo, 'Entrada Mezquite' AS origen
                    FROM almacen_mezquite al
                    LEFT JOIN almacenencabezado_mezquite am ON am.idAlmacen = al.idAlmacenEncabezado
                    WHERE am.idProveedor = :idProveedor AND al.referencia = 0 AND SUBSTR(am.fecha FROM 6 FOR 2) = $idMes
                    GROUP BY am.idAlmacen) AS almacen
                    UNION
                    SELECT '' AS idAuxiliar, fecha, descripcion, referencia,'' AS nombreBanco, '' AS nombreCuenta, cantidad, neto, precio, totalCompra, tipo, origen, '' AS idConcepto
                    FROM(SELECT ce.idAlmacen AS referencia, ce.fecha, 'Ingreso directo de miel 100% mezquite' AS descripcion, '' as cantidad,
                    SUM(cd.neto) AS neto, cd.precio, ce.totalCompra, '0' AS tipo, 'Entrada Cubeta Mezquite' AS origen
                    FROM cubetasdetalle_mezquite cd
                    LEFT JOIN cubetasencabezado_mezquite ce ON ce.idAlmacen = cd.idAlmacenEncabezado
                    WHERE ce.idProveedor = :idProveedor AND ce.folioEntradaTambor = 99999 AND cd.referencia = 0 AND SUBSTR(ce.fecha FROM 6 FOR 2) = $idMes
                    GROUP BY ce.idAlmacen) AS almacenCubetas     
                    UNION

                    SELECT '' AS idAuxiliar, fecha, descripcion, referencia,'' AS nombreBanco, '' AS nombreCuenta, cantidad, neto, precio, totalCompra, tipo, origen, '' AS idConcepto
                    FROM(SELECT am.idAlmacen AS referencia, am.fecha, 'Ingreso directo de miel 100% naranjo' AS descripcion, '' as cantidad,
                    SUM(al.neto) AS neto, al.precio, am.totalCompra, '0' AS tipo, 'Entrada Naranjo' AS origen
                    FROM almacen_naranjo al
                    LEFT JOIN almacenencabezado_naranjo am ON am.idAlmacen = al.idAlmacenEncabezado
                    WHERE am.idProveedor = :idProveedor AND al.referencia = 0 AND SUBSTR(am.fecha FROM 6 FOR 2) = $idMes
                    GROUP BY am.idAlmacen) AS almacen
                    UNION
                    SELECT '' AS idAuxiliar, fecha, descripcion, referencia,'' AS nombreBanco, '' AS nombreCuenta, cantidad, neto, precio, totalCompra, tipo, origen, '' AS idConcepto
                    FROM(SELECT ce.idAlmacen AS referencia, ce.fecha, 'Ingreso directo de miel 100% naranjo' AS descripcion, '' as cantidad,
                    SUM(cd.neto) AS neto, cd.precio, ce.totalCompra, '0' AS tipo, 'Entrada Cubeta Naranjo' AS origen
                    FROM cubetasdetalle_naranjo cd
                    LEFT JOIN cubetasencabezado_naranjo ce ON ce.idAlmacen = cd.idAlmacenEncabezado
                    WHERE ce.idProveedor = :idProveedor AND ce.folioEntradaTambor = 99999 AND cd.referencia = 0 AND SUBSTR(ce.fecha FROM 6 FOR 2) = $idMes
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
                $proveedor['arregloDeMovimientos'] = [];
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

                if ($idMes == 1) {
                    $saldoAnterior = 0;
                }
                // $proveedor['saldoInicial'] = $saldoInicial;
                $proveedor['saldoInicial'] = $saldoAnterior + $saldoInicial;

                foreach ($sqlDetalle->fetchAll(PDO::FETCH_ASSOC) as $movimiento) {

                    $movimiento['importe'] = $movimiento['totalCompra'];
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
                            $salidas += $movimiento['cantidad'];
                            $movimiento['caja'] = -$movimiento['cantidad'];
                            $totalCaja -= $movimiento['cantidad'];
                        }
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
                        if ($movimiento['netoCubeta'] > 0) {
                            $movimiento['kilosNeto'] = $movimiento['neto'] + $movimiento['netoCubeta'];
                            $totalNetoCubetas += $movimiento['netoCubeta'];
                            $movimiento['importe'] = $movimiento['totalCompra'] + $movimiento['totalCompraCC'];
                            $totalImporte = $totalImporte + $movimiento['totalCompraCC'];
                        } else {
                            $movimiento['kilosNeto'] = $movimiento['neto'];
                            $movimiento['importe'] = $movimiento['totalCompra'];
                        }
                        $movimiento['precioPromedio'] = $movimiento['importe'] / $movimiento['kilosNeto'];
                        $salidas += $movimiento['importe'];
                    } else if ($movimiento['origen'] == 'devolucionBanco') {

                        $movimiento['banco'] = -$movimiento['cantidad'];
                        $totalBancos -= $movimiento['cantidad'];
                        $salidas += $movimiento['cantidad'];
                        $movimiento['cheque'] = $movimiento['referencia'];
                        $movimiento['deBanco'] = '';
                        $movimiento['deCuenta'] = '';
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
                            $movimiento['importe'] = $movimiento['totalCompra'] + $movimiento['totalCompraCC'];
                            $totalImporte = $totalImporte + $movimiento['totalCompraCC'];
                        } else {
                            $movimiento['kilosNeto'] = $movimiento['neto'];
                            $movimiento['importe'] = $movimiento['totalCompra'];
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
                    } else if ($movimiento['origen'] == 'Entrada Mantequilla') {
                        $movimiento['entrada'] = $movimiento['referencia'];
                        $movimiento['deBanco'] = '';
                        $movimiento['deCuenta'] = '';
                        $sqlConCubeta = $con->prepare("SELECT SUM(cd.neto) AS netoCubeta, ce.totalCompra
                                FROM cubetasdetalle_mantequilla cd 
                                LEFT JOIN cubetasencabezado_mantequilla ce ON ce.idAlmacen = cd.idAlmacenEncabezado
                                WHERE ce.folioEntradaTambor = :id");
                        $sqlConCubeta->bindParam(':id', $movimiento['referencia']);
                        $sqlConCubeta->execute();
                        $sqlConCubeta->bindColumn('netoCubeta', $movimiento['netoCubeta']);
                        $sqlConCubeta->bindColumn('totalCompra', $movimiento['totalCompraCC']);
                        $sqlConCubeta->fetch(PDO::FETCH_BOUND);
                        if ($movimiento['netoCubeta'] != 0) {
                            $movimiento['kilosNeto'] = $movimiento['neto'] + $movimiento['netoCubeta'];
                            $totalNetoCubetas += $movimiento['netoCubeta'];
                            $movimiento['importe'] = $movimiento['totalCompra'] + $movimiento['totalCompraCC'];
                            $totalImporte = $totalImporte + $movimiento['totalCompraCC'];
                        } else {
                            $movimiento['kilosNeto'] = $movimiento['neto'];
                            $movimiento['importe'] = $movimiento['totalCompra'];
                        }
                        $movimiento['precioPromedio'] = $movimiento['importe'] / $movimiento['kilosNeto'];
                        $salidas += $movimiento['importe'];
                    } else if ($movimiento['origen'] == 'Entrada Cubeta Mantequilla') {
                        $movimiento['entrada'] = $movimiento['referencia'];
                        $movimiento['deBanco'] = '';
                        $movimiento['deCuenta'] = '';
                        $movimiento['kilosNeto'] = $movimiento['neto'];
                        $movimiento['precioPromedio'] = $movimiento['importe'] / $movimiento['kilosNeto'];
                        $salidas += $movimiento['importe'];
                    } 
                    
                    else if ($movimiento['origen'] == 'Entrada Altiplano') {
                        $movimiento['entrada'] = $movimiento['referencia'];
                        $movimiento['deBanco'] = '';
                        $movimiento['deCuenta'] = '';
                        $sqlConCubeta = $con->prepare("SELECT SUM(cd.neto) AS netoCubeta, ce.totalCompra
                                FROM cubetasdetalle_altiplano cd 
                                LEFT JOIN cubetasencabezado_altiplano ce ON ce.idAlmacen = cd.idAlmacenEncabezado
                                WHERE ce.folioEntradaTambor = :id");
                        $sqlConCubeta->bindParam(':id', $movimiento['referencia']);
                        $sqlConCubeta->execute();
                        $sqlConCubeta->bindColumn('netoCubeta', $movimiento['netoCubeta']);
                        $sqlConCubeta->bindColumn('totalCompra', $movimiento['totalCompraCC']);
                        $sqlConCubeta->fetch(PDO::FETCH_BOUND);
                        if ($movimiento['netoCubeta'] != 0) {
                            $movimiento['kilosNeto'] = $movimiento['neto'] + $movimiento['netoCubeta'];
                            $totalNetoCubetas += $movimiento['netoCubeta'];
                            $movimiento['importe'] = $movimiento['totalCompra'] + $movimiento['totalCompraCC'];
                            $totalImporte = $totalImporte + $movimiento['totalCompraCC'];
                        } else {
                            $movimiento['kilosNeto'] = $movimiento['neto'];
                            $movimiento['importe'] = $movimiento['totalCompra'];
                        }
                        $movimiento['precioPromedio'] = $movimiento['importe'] / $movimiento['kilosNeto'];
                        $salidas += $movimiento['importe'];
                    } else if ($movimiento['origen'] == 'Entrada Cubeta Altiplano') {
                        $movimiento['entrada'] = $movimiento['referencia'];
                        $movimiento['deBanco'] = '';
                        $movimiento['deCuenta'] = '';
                        $movimiento['kilosNeto'] = $movimiento['neto'];
                        $movimiento['precioPromedio'] = $movimiento['importe'] / $movimiento['kilosNeto'];
                        $salidas += $movimiento['importe'];
                    }

                    else if ($movimiento['origen'] == 'Entrada Aguacate') {
                        $movimiento['entrada'] = $movimiento['referencia'];
                        $movimiento['deBanco'] = '';
                        $movimiento['deCuenta'] = '';
                        $sqlConCubeta = $con->prepare("SELECT SUM(cd.neto) AS netoCubeta, ce.totalCompra
                                FROM cubetasdetalle_aguacate cd 
                                LEFT JOIN cubetasencabezado_aguacate ce ON ce.idAlmacen = cd.idAlmacenEncabezado
                                WHERE ce.folioEntradaTambor = :id");
                        $sqlConCubeta->bindParam(':id', $movimiento['referencia']);
                        $sqlConCubeta->execute();
                        $sqlConCubeta->bindColumn('netoCubeta', $movimiento['netoCubeta']);
                        $sqlConCubeta->bindColumn('totalCompra', $movimiento['totalCompraCC']);
                        $sqlConCubeta->fetch(PDO::FETCH_BOUND);
                        if ($movimiento['netoCubeta'] != 0) {
                            $movimiento['kilosNeto'] = $movimiento['neto'] + $movimiento['netoCubeta'];
                            $totalNetoCubetas += $movimiento['netoCubeta'];
                            $movimiento['importe'] = $movimiento['totalCompra'] + $movimiento['totalCompraCC'];
                            $totalImporte = $totalImporte + $movimiento['totalCompraCC'];
                        } else {
                            $movimiento['kilosNeto'] = $movimiento['neto'];
                            $movimiento['importe'] = $movimiento['totalCompra'];
                        }
                        $movimiento['precioPromedio'] = $movimiento['importe'] / $movimiento['kilosNeto'];
                        $salidas += $movimiento['importe'];
                    } else if ($movimiento['origen'] == 'Entrada Cubeta Aguacate') {
                        $movimiento['entrada'] = $movimiento['referencia'];
                        $movimiento['deBanco'] = '';
                        $movimiento['deCuenta'] = '';
                        $movimiento['kilosNeto'] = $movimiento['neto'];
                        $movimiento['precioPromedio'] = $movimiento['importe'] / $movimiento['kilosNeto'];
                        $salidas += $movimiento['importe'];
                    }

                    else if ($movimiento['origen'] == 'Entrada Mezquite') {
                        $movimiento['entrada'] = $movimiento['referencia'];
                        $movimiento['deBanco'] = '';
                        $movimiento['deCuenta'] = '';
                        $sqlConCubeta = $con->prepare("SELECT SUM(cd.neto) AS netoCubeta, ce.totalCompra
                                FROM cubetasdetalle_mezquite cd 
                                LEFT JOIN cubetasencabezado_mezquite ce ON ce.idAlmacen = cd.idAlmacenEncabezado
                                WHERE ce.folioEntradaTambor = :id");
                        $sqlConCubeta->bindParam(':id', $movimiento['referencia']);
                        $sqlConCubeta->execute();
                        $sqlConCubeta->bindColumn('netoCubeta', $movimiento['netoCubeta']);
                        $sqlConCubeta->bindColumn('totalCompra', $movimiento['totalCompraCC']);
                        $sqlConCubeta->fetch(PDO::FETCH_BOUND);
                        if ($movimiento['netoCubeta'] != 0) {
                            $movimiento['kilosNeto'] = $movimiento['neto'] + $movimiento['netoCubeta'];
                            $totalNetoCubetas += $movimiento['netoCubeta'];
                            $movimiento['importe'] = $movimiento['totalCompra'] + $movimiento['totalCompraCC'];
                            $totalImporte = $totalImporte + $movimiento['totalCompraCC'];
                        } else {
                            $movimiento['kilosNeto'] = $movimiento['neto'];
                            $movimiento['importe'] = $movimiento['totalCompra'];
                        }
                        $movimiento['precioPromedio'] = $movimiento['importe'] / $movimiento['kilosNeto'];
                        $salidas += $movimiento['importe'];
                    } else if ($movimiento['origen'] == 'Entrada Cubeta Mezquite') {
                        $movimiento['entrada'] = $movimiento['referencia'];
                        $movimiento['deBanco'] = '';
                        $movimiento['deCuenta'] = '';
                        $movimiento['kilosNeto'] = $movimiento['neto'];
                        $movimiento['precioPromedio'] = $movimiento['importe'] / $movimiento['kilosNeto'];
                        $salidas += $movimiento['importe'];
                    }

                    else if ($movimiento['origen'] == 'Entrada Naranjo') {
                        $movimiento['entrada'] = $movimiento['referencia'];
                        $movimiento['deBanco'] = '';
                        $movimiento['deCuenta'] = '';
                        $sqlConCubeta = $con->prepare("SELECT SUM(cd.neto) AS netoCubeta, ce.totalCompra
                                FROM cubetasdetalle_naranjo cd 
                                LEFT JOIN cubetasencabezado_naranjo ce ON ce.idAlmacen = cd.idAlmacenEncabezado
                                WHERE ce.folioEntradaTambor = :id");
                        $sqlConCubeta->bindParam(':id', $movimiento['referencia']);
                        $sqlConCubeta->execute();
                        $sqlConCubeta->bindColumn('netoCubeta', $movimiento['netoCubeta']);
                        $sqlConCubeta->bindColumn('totalCompra', $movimiento['totalCompraCC']);
                        $sqlConCubeta->fetch(PDO::FETCH_BOUND);
                        if ($movimiento['netoCubeta'] != 0) {
                            $movimiento['kilosNeto'] = $movimiento['neto'] + $movimiento['netoCubeta'];
                            $totalNetoCubetas += $movimiento['netoCubeta'];
                            $movimiento['importe'] = $movimiento['totalCompra'] + $movimiento['totalCompraCC'];
                            $totalImporte = $totalImporte + $movimiento['totalCompraCC'];
                        } else {
                            $movimiento['kilosNeto'] = $movimiento['neto'];
                            $movimiento['importe'] = $movimiento['totalCompra'];
                        }
                        $movimiento['precioPromedio'] = $movimiento['importe'] / $movimiento['kilosNeto'];
                        $salidas += $movimiento['importe'];
                    } else if ($movimiento['origen'] == 'Entrada Cubeta Naranjo') {
                        $movimiento['entrada'] = $movimiento['referencia'];
                        $movimiento['deBanco'] = '';
                        $movimiento['deCuenta'] = '';
                        $movimiento['kilosNeto'] = $movimiento['neto'];
                        $movimiento['precioPromedio'] = $movimiento['importe'] / $movimiento['kilosNeto'];
                        $salidas += $movimiento['importe'];
                    }
                    
                    else if ($movimiento['origen'] == 'Gastos') {
                        $totalGastosRealizados += $movimiento['cantidad'];
                        $movimiento['gastos'] = $movimiento['cantidad'];
                        $salidas += $movimiento['cantidad'];
                    } else if ($movimiento['origen'] == 'Retenciones') {
                        $totalRetencionesRealizadas += $movimiento['cantidad'];
                        $movimiento['retenciones'] = $movimiento['cantidad'];
                        $entradas += $movimiento['cantidad'];
                        // $retenciones += $movimiento['cantidad'];
                    } else if ($movimiento['origen'] == 'AlmacenCeraApicola') {
                        if ($movimiento['tipo'] == '1') {       // (Egresos para el sistema

                            $movimiento['ceraApicola'] = -$movimiento['cantidad'];
                            $salidas += $movimiento['cantidad'];
                            $totalEgresosCeraApicola += $movimiento['cantidad'];
                        } else if ($movimiento['tipo'] == '2') {    // Ingreso para el sistema

                            $movimiento['ceraApicola'] = $movimiento['cantidad'];
                            $entradas += $movimiento['cantidad'];
                            $totalIngresosCeraApicola += $movimiento['cantidad'];
                        }
                    } else {
                        $movimiento['desconocido'] = $movimiento['cantidad'];
                        $salidas += $movimiento['cantidad'];
                        if ($totalKilos != 0) {
                            $totalPrecio = $totalImporte / $totalKilos;
                        }
                    }

                    if ($totalKilos != 0) {
                        $totalPrecio = $totalImporte / $totalKilos;
                    }

                    $movimiento['saldo'] = $saldoAnterior + $saldoInicial + $entradas - $salidas;
                    // if ($movimiento['saldo'] > 0) {
                    //     // $movimiento['saldo'] -= $retenciones;
                    //     $movimiento['saldo'] += $retenciones;
                    // } else if ($movimiento['saldo'] < 0) {
                    //     $movimiento['saldo'] += $retenciones;
                    // }
                    array_push($proveedor['arregloDeMovimientos'], $movimiento);
                } // Fin foreach por cada movimiento

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
                $proveedor['totalRetenciones'] = $totalRetencionesRealizadas;
                $proveedor['totalVentasCeraApicola'] = $totalVentasCeraApicola;
                $proveedor['totalCeraApicola'] = $totalIngresosCeraApicola - $totalEgresosCeraApicola;
                $proveedor['totalSaldo'] = $saldoAnterior + $saldoInicial + $totalBancos + $totalCaja - $totalImporte - $totalDevolucionesMiel + $totalIngresosCeraApicola - $totalEgresosCeraApicola - $totalDevolucionesCeraApicola - $totalGastosRealizados - $totalVentasCeraApicola + $totalRetencionesRealizadas;
                // $proveedor['totalSaldo'] = $saldoAnterior + $saldoInicial + $totalBancos + $totalCaja - $totalImporte - $totalDevolucionesMiel + $totalIngresosCeraApicola - $totalEgresosCeraApicola - $totalDevolucionesCeraApicola - $totalGastosRealizados - $totalVentasCeraApicola;
                // if ($proveedor['totalSaldo'] > 0) {
                //     // $proveedor['totalSaldo'] -= $totalRetencionesRealizadas;
                //     $proveedor['totalSaldo'] += $totalRetencionesRealizadas;
                // } else if ($proveedor['totalSaldo'] < 0) {
                //     $proveedor['totalSaldo'] += $totalRetencionesRealizadas;
                // }
                array_push($resultado, $proveedor);
            }
        }
        return $resultado;
    }
}
if (!isset($_GET['function'])) {
    $resultado = obtenerDeudoresProveedores();
    echo json_encode($resultado);
}
