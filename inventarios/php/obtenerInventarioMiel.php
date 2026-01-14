<?php
ob_start();
error_reporting(E_ALL & ~E_NOTICE);
ini_set('display_errors', 0);
ini_set('log_errors', 1);

if (!isset($_GET['cone'])) {
    include_once '../../DAOConeccion/conePDO.php';
    $pdo = new conePDO();
    $con = $pdo->conectar();
    $con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
}

include_once '../../controlAdministrativo/php/nombreDePersona.php';
date_default_timezone_set('America/Merida');


function calcularInventarioMensual($idMes, $filtro, $soloEncabezado = false, $datos_del_mes_pasado = false, $acumulado = false, $fechaInicial = false, $fechaFinal = false, $tipoMiel = '1')
{

    // por defecto el tipo de miel es 1, si quiere cambiar tiene que mandarlo por el 6to parámetro
    //= = = = = = = = = = = = = = = = = = = = = = = = P A R A M S = = = = = = = = =  = = = = = = = = = = = = = = 
    // $idMes: El id del mes que se quiere obtener, en caso de no requerir mes, mandar FALSE
    // $soloEncabezado: TRUE o FALSE, si se quiere solo el encabezado o todo el arreglo, ideal para el mes anterior
    // $datos_del_mes_pasado: En caso de requerir todos los datos, debemos mandar el encabezado del mes anterior
    // $acumulado: Si solo requerimos el acumulado, mandamos TRUE, y los demás parámetros en FALSE
    //= = = = = = = = = = = = = = = = = = = = = = = = P A R A M S = = = = = = = = =  = = = = = = = = = = = = = = 

    global $con;

    if ($tipoMiel == '1') {
        // En la tabla, la miel convencional es 1
        $nombre = 'miel';
        $almacen_tabla = 'almacen';
        $almacenencabezado_tabla = 'almacenencabezado';
        $entradaysalida_tabla = 'entradaysalida';
        $cubetasdetalle_tabla = 'cubetasdetalle';
        $cubetasencabezado_tabla = 'cubetasencabezado';
        $traspaso = 'almacentraspaso';
        $traspasoEncabezado = 'almacenencabezadotraspaso';
        $codigo = 'LC26-';
    } else if ($tipoMiel == '5') {
        $nombre = 'miel_mantequilla';
        $almacen_tabla = 'almacen_mantequilla';
        $almacenencabezado_tabla = 'almacenencabezado_mantequilla';
        $entradaysalida_tabla = 'entradaysalida_mantequilla';
        $cubetasdetalle_tabla = 'cubetasdetalle_mantequilla';
        $cubetasencabezado_tabla = 'cubetasencabezado_mantequilla';
        $traspaso = 'almacentraspaso_mantequilla';
        $traspasoEncabezado = 'almacenencabezadotraspaso_mantequilla';
        $codigo = 'LM26-';
    } else if ($tipoMiel == '6') {
        $nombre = 'miel_altiplano';
        $almacen_tabla = 'almacen_altiplano';
        $almacenencabezado_tabla = 'almacenencabezado_altiplano';
        $entradaysalida_tabla = 'entradaysalida_altiplano';
        $cubetasdetalle_tabla = 'cubetasdetalle_altiplano';
        $cubetasencabezado_tabla = 'cubetasencabezado_altiplano';
        $traspaso = 'almacentraspaso_altiplano';
        $traspasoEncabezado = 'almacenencabezadotraspaso_altiplano';
        $codigo = 'LA26-';
    } else if ($tipoMiel == '7') {
        $nombre = 'miel_naranjo';
        $almacen_tabla = 'almacen_naranjo';
        $almacenencabezado_tabla = 'almacenencabezado_naranjo';
        $entradaysalida_tabla = 'entradaysalida_naranjo';
        $cubetasdetalle_tabla = 'cubetasdetalle_naranjo';
        $cubetasencabezado_tabla = 'cubetasencabezado_naranjo';
        $traspaso = 'almacentraspaso_naranjo';
        $traspasoEncabezado = 'almacenencabezadotraspaso_naranjo';
        $codigo = 'LN26-';
    } else if ($tipoMiel == '8') {
        $nombre = 'miel_aguacate';
        $almacen_tabla = 'almacen_aguacate';
        $almacenencabezado_tabla = 'almacenencabezado_aguacate';
        $entradaysalida_tabla = 'entradaysalida_aguacate';
        $cubetasdetalle_tabla = 'cubetasdetalle_aguacate';
        $cubetasencabezado_tabla = 'cubetasencabezado_aguacate';
        $traspaso = 'almacentraspaso_aguacate';
        $traspasoEncabezado = 'almacenencabezadotraspaso_aguacate';
        $codigo = 'LG25-';
    } else if ($tipoMiel == '9') {
        $nombre = 'miel_mezquite';
        $almacen_tabla = 'almacen_mezquite';
        $almacenencabezado_tabla = 'almacenencabezado_mezquite';
        $entradaysalida_tabla = 'entradaysalida_mezquite';
        $cubetasdetalle_tabla = 'cubetasdetalle_mezquite';
        $cubetasencabezado_tabla = 'cubetasencabezado_mezquite';
        $traspaso = 'almacentraspaso_mezquite';
        $traspasoEncabezado = 'almacenencabezadotraspaso_mezquite';
        $codigo = 'LZ26-';
    }else {
        // Y la miel orgánica es el inventario 4
        $nombre = 'miel_organica';
        $almacen_tabla = 'almacen_organico';
        $almacenencabezado_tabla = 'almacenencabezado_organico';
        $entradaysalida_tabla = 'entradaysalida_organico';
        $cubetasdetalle_tabla = 'cubetasdetalle_organico';
        $cubetasencabezado_tabla = 'cubetasencabezado_organico';
        $traspaso = 'almacentraspaso_organico';
        $traspasoEncabezado = 'almacenencabezadotraspaso_organico';
        $codigo = 'LO26-';
    }

    $result = [];
    if ($datos_del_mes_pasado) {
        $result['existenciaPasada'] = $datos_del_mes_pasado['totalInventario'];
        $result['importeAcumuladoPasado'] = $datos_del_mes_pasado['totalImportesAcumulados'];
    } else {

        if ($idMes == 1 || ($fechaInicial && $fechaFinal) || $acumulado) {
            $seleccionarPasado = $con->prepare("SELECT existenciaPasada, importeAcumuladoPasado FROM saldoinicialinventario WHERE nombre = :nombre;");
            $seleccionarPasado->bindParam('nombre', $nombre);
            $seleccionarPasado->bindColumn('existenciaPasada', $result['existenciaPasada']);
            $seleccionarPasado->bindColumn('importeAcumuladoPasado', $result['importeAcumuladoPasado']);
            $seleccionarPasado->execute();
            if ($seleccionarPasado == false) {
                throw new Exception($con->errorInfo());
            } else {
                $seleccionarPasado->fetch(PDO::FETCH_BOUND);
            }
        } else {
            $result['existenciaPasada'] = 0;
            $result['importeAcumuladoPasado'] = 0;
        }
    }

    $resultado = array();

    // $existencia = 0;
    // $importeAcumulado = 0; 
    $encabezado = array(
        'totalInventario' => 0,
        'totalEntradas' => 0,
        'totalSalidas' => 0,
        'totalImportesAcumulados' => 0,
        'totalImporteEntrada' => 0,
        'totalImporteSalida' => 0,
        'promedioPrecio' => 0
    );


    $existencia = $result['existenciaPasada'];
    $importeAcumulado = $result['importeAcumuladoPasado'];

    $existenciaPasada = $result['existenciaPasada'];
    $importeAcumuladoPasado = $result['importeAcumuladoPasado'];

    // $existenciaPasada = 0;
    // $importeAcumuladoPasado = 0;
    // -- '' as clasificacion, os.idOtraSalida as folio, '' as nombre, SUM(osd.kg) as neto, osd.importe/osd.cantidad as precio, '2' as tipo


    $sql = "SELECT tipoDePersona, idPersona, fecha, totalCompra, folioInterno, clasificacion, folio, UCASE(nombre) AS nombre, SUM(neto) AS neto, precio, tipo
            FROM (SELECT '' as tipoDePersona, '' as idPersona, am.idAlmacen AS folioInterno, am.fecha, am.totalCompra, c.clasificacion,  e.idReporte AS folio, pr.nombre, pr.idSagarpa,
            al.idAlmacen as tambor, al.neto AS neto, al.precio, '1' AS tipo
            FROM $almacen_tabla al
            LEFT JOIN $almacenencabezado_tabla am ON am.idAlmacen = al.idAlmacenEncabezado
            LEFT JOIN proveedor pr ON pr.idProveedor = am.idProveedor
            LEFT JOIN $entradaysalida_tabla e ON e.lote = am.idAlmacen
            LEFT JOIN clasificacionesmiel c ON c.idClasificacionMiel = e.idClasificacion
            WHERE am.idProveedor != '83' ";
    if (!$acumulado) {
        if ($fechaInicial && $fechaFinal) {
            // $sql .= " AND am.fecha <= '" . $fechaFinal . "' AND e.idReporte = (SELECT MIN(idReporte) as idReporte FROM $entradaysalida_tabla WHERE lote = am.idAlmacen)";
            $sql .= " AND am.fecha BETWEEN '" . $fechaInicial . "' AND '" . $fechaFinal . "' AND e.idReporte = (SELECT MIN(idReporte) as idReporte FROM $entradaysalida_tabla WHERE lote = am.idAlmacen)";
        } else {
            $sql .= " AND SUBSTR(am.fecha FROM 6 FOR 2) =" . $idMes . " AND e.idReporte = (SELECT MIN(idReporte) as idReporte FROM $entradaysalida_tabla WHERE lote = am.idAlmacen)";
        }
    } else {
        $sql .= " AND e.idReporte = (SELECT MIN(idReporte) as idReporte FROM $entradaysalida_tabla WHERE lote = am.idAlmacen)";
    }

    // Consulta para los que no tiene reporte de carga o no coincide con el numero de lote

    $sql .= " ORDER BY am.idAlmacen ASC) AS tablaEntradas
        GROUP BY folioInterno
        UNION
        SELECT tipoDePersona, idPersona, fecha, totalCompra, folioInterno, clasificacion, folio, UCASE(nombre) AS nombre, SUM(neto) AS neto, precio, tipo
        FROM (SELECT '' as tipoDePersona, '' as idPersona, am.idAlmacen AS folioInterno, am.fecha, am.totalCompra, c.clasificacion,  NULL as folio, pr.nombre, pr.idSagarpa,
        al.idAlmacen as tambor, al.neto AS neto, al.precio, '1' AS tipo
        FROM $almacen_tabla al
        LEFT JOIN $almacenencabezado_tabla am ON am.idAlmacen = al.idAlmacenEncabezado
        LEFT JOIN proveedor pr ON pr.idProveedor = am.idProveedor
        LEFT JOIN $entradaysalida_tabla e ON e.lote = am.idAlmacen
        LEFT JOIN clasificacionesmiel c ON c.idClasificacionMiel = e.idClasificacion";
    if (!$acumulado) {
        if ($fechaInicial && $fechaFinal) {
            // $sql .= " WHERE am.fecha <= '" . $fechaFinal . "' AND e.idReporte IS NULL";
            $sql .= " WHERE am.fecha BETWEEN '" . $fechaInicial . "' AND '" . $fechaFinal . "' AND e.idReporte IS NULL";
        } else {
            $sql .= " WHERE SUBSTR(am.fecha FROM 6 FOR 2) =" . $idMes . " AND e.idReporte IS NULL";
        }
    } else {
        $sql .= " WHERE e.idReporte IS NULL";
    }
    $sql .= " ORDER BY am.idAlmacen ASC) AS tablaEntradas2
            GROUP BY folioInterno
            UNION
            SELECT tipoDePersona, idPersona, fecha, totalCompra, folioInterno, clasificacion, folio, UCASE(nombre) AS nombre, SUM(neto) AS neto, precio, tipo
            FROM (SELECT '' as tipoDePersona, '' as idPersona, am.idAlmacen AS folioInterno, am.fecha, am.totalCompra, c.clasificacion,  e.idReporte AS folio, pr.nombre, pr.idSagarpa,
            al.idAlmacen as tambor, al.neto AS neto, al.precio, '1' AS tipo
            FROM $traspaso al
            LEFT JOIN $traspasoEncabezado am ON am.idAlmacen = al.idAlmacenEncabezado
            LEFT JOIN proveedor pr ON pr.idProveedor = am.idProveedor
            LEFT JOIN $entradaysalida_tabla e ON e.lote = am.idAlmacen AND am.idReporteDescarga = e.idReporte
            LEFT JOIN clasificacionesmiel c ON c.idClasificacionMiel = e.idClasificacion
            WHERE ";
    if (!$acumulado) {
        if ($fechaInicial && $fechaFinal) {
            // $sql .= " AND am.fecha <= '" . $fechaFinal . "' AND e.idReporte = (SELECT MIN(idReporte) as idReporte FROM $entradaysalida_tabla WHERE lote = am.idAlmacen)";
            $sql .= "am.fecha BETWEEN '" . $fechaInicial . "' AND '" . $fechaFinal . "' AND e.idReporte = (SELECT MIN(idReporte) as idReporte FROM $entradaysalida_tabla WHERE lote = am.idAlmacen)";
        } else {
            $sql .= "SUBSTR(am.fecha FROM 6 FOR 2) =" . $idMes . " AND e.idReporte = (SELECT MIN(idReporte) as idReporte FROM $entradaysalida_tabla WHERE lote = am.idAlmacen)";
        }
    } else {
        $sql .= "e.idReporte = (SELECT MIN(idReporte) as idReporte FROM $entradaysalida_tabla WHERE lote = am.idAlmacen)";
    }

    // Consulta para los que no tiene reporte de carga o no coincide con el numero de lote

    $sql .= " ORDER BY am.idAlmacen ASC) AS tablaEntradas
        GROUP BY folioInterno
            UNION
            SELECT tipoDePersona, idPersona, fecha, totalCompra, folioInterno, clasificacion, folio, UCASE(nombre) AS nombre, SUM(neto) AS neto, precio, tipo
            FROM (SELECT '' as tipoDePersona, '' as idPersona, CONCAT('C-',am.idAlmacen) AS folioInterno, am.fecha, am.totalCompra, '' AS clasificacion,  '' AS folio, pr.nombre, pr.idSagarpa,
            al.idAlmacen as tambor, al.neto AS neto, al.precio, '1' AS tipo
            FROM $cubetasdetalle_tabla al
            LEFT JOIN $cubetasencabezado_tabla am ON am.idAlmacen = al.idAlmacenEncabezado
            LEFT JOIN proveedor pr ON pr.idProveedor = am.idProveedor";
    if (!$acumulado) {
        if ($fechaInicial && $fechaFinal) {
            // $sql .= " AND am.fecha <= '" . $fechaFinal . "'";
            $sql .= " WHERE am.fecha BETWEEN '" . $fechaInicial . "' AND '" . $fechaFinal . "'";
        } else {
            $sql .= " WHERE SUBSTR(am.fecha FROM 6 FOR 2) =" . $idMes;
        }
    }
    $sql .= " ORDER BY am.idAlmacen ASC) AS tablaCubetas
            GROUP BY folioInterno";

    if ($filtro == 2 || $filtro == 0) {
        $sql .= " UNION SELECT os.tipoDePersona, os.idPersona, os.fecha, '' AS totalCompra, CONCAT('OS-', os.idOtraSalida) as folioInterno,
    '' as clasificacion, os.idOtraSalida as folio, '' as nombre, SUM(osd.kg) as neto,'' as precio, '2' as tipo
    FROM otrassalidasdetalle osd
    LEFT JOIN otrassalidas os ON osd.idOtrasSalidas = os.idOtraSalida
    WHERE osd.idTipoDeMiel = $tipoMiel";
        if (!$acumulado) {
            if ($fechaInicial && $fechaFinal) {
                // $sql .= " AND os.fecha <= '" . $fechaFinal . "'";
                $sql .= " AND os.fecha BETWEEN '" . $fechaInicial . "' AND '" . $fechaFinal . "'";
            } else {
                $sql .= " AND SUBSTR(os.fecha FROM 6 FOR 2) =" . $idMes;
            }
        }
        $sql .= " GROUP BY os.idOtraSalida";
    }

    $sql .= " UNION
            SELECT tipoDePersona, idPersona, fecha, '' AS totalCompra, folioInterno, clasificacion, folio, nombre, neto, precio, tipo
            FROM (SELECT '' as tipoDePersona, '' as idPersona, CONCAT('$codigo', rc.idLoteInterno) AS folioInterno, rc.fechaImpresion AS fecha, c.clasificacion,
            lp.idTamborPeso AS folio, rc.lote AS nombre,
            lp.totalNeto AS neto, '' as precio, '2' AS tipo
            FROM $entradaysalida_tabla rc
            INNER JOIN listadepesos lp ON lp.lote = rc.lote AND lp.tipoMiel = $tipoMiel
            LEFT JOIN clasificacionesmiel c ON c.idClasificacionMiel = rc.idClasificacion";
    if (!$acumulado) {
        if ($fechaInicial && $fechaFinal) {
            // $sql .= " WHERE rc.fechaImpresion <= '" . $fechaFinal . "'";
            if ($filtro > 0) {
                $sql .= " WHERE rc.idClasificacion = $filtro AND rc.fechaImpresion BETWEEN '" . $fechaInicial . "' AND '" . $fechaFinal . "') AS tablaSalidas ORDER BY fecha ASC";
            } else {
                $sql .= " WHERE rc.fechaImpresion BETWEEN '" . $fechaInicial . "' AND '" . $fechaFinal . "') AS tablaSalidas ORDER BY fecha ASC";
            }
        } else {
            if ($filtro > 0) {
                $sql .= " WHERE rc.idClasificacion = $filtro AND SUBSTR(rc.fechaImpresion FROM 6 FOR 2) = $idMes) AS tablaSalidas ORDER BY fecha ASC";
            } else {
                $sql .= " WHERE SUBSTR(rc.fechaImpresion FROM 6 FOR 2) = $idMes) AS tablaSalidas ORDER BY fecha ASC";
            }
        }
    } else {
        if ($filtro > 0) {
            $sql .= " WHERE rc.idClasificacion = $filtro) AS tablaSalidas ORDER BY fecha ASC";
        } else {
            $sql .= ") AS tablaSalidas ORDER BY fecha ASC";
        }
    }
    // else {

    // }

    $datos = $con->prepare($sql);
    $datos->execute();
    if ($datos == false) {
        throw new Exception($con->errorInfo());
    }
    // else if ($datos->rowCount() <= 0) {
    //     throw new Exception('No hay datos que mostrar');
    // }

    $datos = $datos->fetchAll(PDO::FETCH_ASSOC);

    foreach ($datos as $dato) {
        // La siguiente es para producción
        // para ya no poner 50.123 por defecto, hacemos la siguiente validación:

        if ($dato['precio'] == '') {
            // Si NO tiene precio, ( salidas principalmente ): 
            if ($existencia > 0) {
                // Si hay existencias, se calcula
                $dato['precio'] = $importeAcumulado / $existencia;
            } else {
                // Si no hay existencia, pues es cero, 
                $dato['precio'] = 0;
            }
        } else {
            // si tiene precio, solo lo convertirmos a un float
            $dato['precio'] = floatval($dato['precio']);
        }

        // La siguiente para local (ya no se debe usar)
        // $dato['precio'] = $dato['precio'] == '' ? 50.123 : floatval($dato['precio']); // Prueba para el local. Descomentar para local
        $dato['nombre'] = $dato['nombre'] == '' ? retornarNombre($con, $dato['tipoDePersona'], $dato['idPersona']) : $dato['nombre'];
        if ($dato['tipo'] == '1') {
            $dato['entrada'] = floatval($dato['neto']);
            $dato['salida'] = 0;
            $dato['tipo'] = true;
            $dato['existencia'] = $existencia + $dato['entrada'] - $dato['salida'];
            $dato['precio'] = $dato['totalCompra'] / $dato['neto'];
            $dato['totalImporte'] = $dato['precio'] * $dato['neto'];
            $dato['totalSalida'] = 0;
            $importeAcumulado = $importeAcumulado + $dato['totalImporte'] - $dato['totalSalida'];
            $dato['importeAcumulado'] = $importeAcumulado;
            $encabezado['totalImporteEntrada'] += $dato['totalImporte'];
            $encabezado['totalEntradas'] += $dato['neto'];
            $existencia += $dato['neto'];
        } else if ($dato['tipo'] == '2') {
            $dato['salida'] = floatval($dato['neto']);
            $dato['entrada'] = 0;
            $dato['tipo'] = false;
            $dato['existencia'] = $existencia + $dato['entrada'] - $dato['salida'];
            $dato['totalImporte'] = 0;
            $dato['totalSalida'] = $dato['precio'] * $dato['neto'];
            $importeAcumulado = $importeAcumulado + $dato['totalImporte'] - $dato['totalSalida'];
            $dato['importeAcumulado'] = $importeAcumulado;
            $encabezado['totalImporteSalida'] += $dato['totalSalida'];
            $encabezado['totalSalidas'] += $dato['neto'];
            $existencia -= $dato['neto'];
        }
        array_push($resultado, $dato);
    }

    // Si las entradas son mayor a cero, se calcula sino es cero
    if ($encabezado['totalEntradas'] > 0) {
        $encabezado['promedioPrecio'] = $encabezado['totalImporteEntrada'] / $encabezado['totalEntradas'];
    } else {
        $encabezado['promedioPrecio'] = 0;
    }
    // Si las salidas son mayor a cero, se calcula sino es cero
    if ($encabezado['totalSalidas'] > 0) {
        $encabezado['promedioPrecioSalida'] = $encabezado['totalImporteSalida'] / $encabezado['totalSalidas'];
    } else {
        $encabezado['promedioPrecioSalida'] = 0;
    }
    $encabezado['totalInventario'] = $existenciaPasada + $encabezado['totalEntradas'] - $encabezado['totalSalidas'];
    $encabezado['totalImportesAcumulados'] = $importeAcumuladoPasado + $encabezado['totalImporteEntrada'] - $encabezado['totalImporteSalida'];

    $result = ['inventarioMiel' => $resultado, 'encabezado' => $encabezado, 'existenciaPasada' => $existenciaPasada, 'importeAcumuladoPasado' => $importeAcumuladoPasado];

    // Prueba para traer las empresas
    $result['resumenPorEmpresas'] = [
        'totalKg' => $encabezado['totalEntradas'],
        'totalImporte' => $encabezado['totalImporteEntrada'],
        'precioPromedio' => $encabezado['promedioPrecio'],
        'totalPorcentaje' => 0,
        'empresas' => []
    ];

    // ***MODIFICACIÓN ANA 2025***
    // if ($tipoMiel == '1') {
        $consultaEmpresas = 'SELECT idProveedor FROM proveedor WHERE activoInactivo = 0 AND empresa = 1 AND idProveedor != 83 ORDER BY idProveedor != 163, idProveedor';
    // } else {
    //     $consultaEmpresas = 'SELECT idProveedor FROM proveedor WHERE empresa != 1 AND idProveedor != 83 ORDER BY idProveedor != 163, idProveedor';
    // }
    // ***MODIFICACIÓN ANA 2025***

    $resultadoEmpresas = [];
    $traerEmpresas = $con->prepare($consultaEmpresas);
    $traerEmpresas->execute();
    if ($traerEmpresas == false) {
        throw new Exception($con->errorInfo());
    } else {
        foreach ($traerEmpresas->fetchAll(PDO::FETCH_ASSOC) as $Empresa) {
            $idEmpresa = intval($Empresa['idProveedor']);
            $resumenPorEmpresasSql = "SELECT pr.idProveedor, pr.nombre, SUM(al.neto) AS neto, SUM(al.costoTotal) AS importe, loc.localidad
            FROM $almacen_tabla al
            INNER JOIN $almacenencabezado_tabla am ON am.idAlmacen = al.idAlmacenEncabezado
            INNER JOIN proveedor pr ON pr.idProveedor = am.idProveedor
            LEFT JOIN direccion dir ON pr.idDireccion = dir.idDireccion
            LEFT JOIN localidades loc ON dir.idlocalidad = loc.idlocalidad
            WHERE am.idProveedor = CONVERT(:idProveedorEmpresa, UNSIGNED)";
            if (!$acumulado) {
                if ($fechaInicial && $fechaFinal) {
                    $resumenPorEmpresasSql .= " AND am.fecha <= '" . $fechaFinal . "'";
                } else {
                    $resumenPorEmpresasSql .= " AND SUBSTR(am.fecha FROM 6 FOR 2) =" . $idMes;
                }
            }
            $query = $con->prepare($resumenPorEmpresasSql);
            $query->bindParam(':idProveedorEmpresa', $idEmpresa);
            $query->execute();
            if ($query == false) {
                throw new Exception($con->errorInfo());
            } else {
                array_push($resultadoEmpresas, $query->fetch(PDO::FETCH_ASSOC));
            }
        }
    }

    foreach ($resultadoEmpresas as $index => $empresa) {
        if ($tipoMiel == '1') {
            if ($index == 0) {
                $empresa['neto'] = floatval($encabezado['totalEntradas']);
                $empresa['importe'] = floatval($encabezado['totalImporteEntrada']);
            } else {
                $empresa['neto'] = floatval($empresa['neto']);
                $empresa['importe'] = floatval($empresa['importe']);

                $result['resumenPorEmpresas']['empresas'][0]['neto'] -= $empresa['neto'];
                $result['resumenPorEmpresas']['empresas'][0]['importe'] -= $empresa['importe'];
                if ($result['resumenPorEmpresas']['empresas'][0]['neto'] > 0) {
                    $result['resumenPorEmpresas']['empresas'][0]['precioPromedio'] = $result['resumenPorEmpresas']['empresas'][0]['importe'] / $result['resumenPorEmpresas']['empresas'][0]['neto'];
                } else {
                    $result['resumenPorEmpresas']['empresas'][0]['precioPromedio'] = 0;
                }
                if ($encabezado['totalEntradas'] > 0) {
                    $result['resumenPorEmpresas']['empresas'][0]['porcentajeCompra'] = $result['resumenPorEmpresas']['empresas'][0]['neto'] / $encabezado['totalEntradas'] * 100;
                } else {
                    $result['resumenPorEmpresas']['empresas'][0]['porcentajeCompra'] = 0;
                }
            }
        } else {
            // if ($index == 0) {
            //     $empresa['neto'] = floatval($encabezado['totalEntradas']);
            //     $empresa['importe'] = floatval($encabezado['totalImporteEntrada']);
            // } else {
            $empresa['neto'] = floatval($empresa['neto']);
            $empresa['importe'] = floatval($empresa['importe']);

            // $result['resumenPorEmpresas']['empresas'][0]['neto'] -= $empresa['neto'];
            // $result['resumenPorEmpresas']['empresas'][0]['importe'] -= $empresa['importe'];
            // if ($result['resumenPorEmpresas']['empresas'][0]['neto'] > 0) {
            //     $result['resumenPorEmpresas']['empresas'][0]['precioPromedio'] = $result['resumenPorEmpresas']['empresas'][0]['importe'] / $result['resumenPorEmpresas']['empresas'][0]['neto'];
            // } else {
            //     $result['resumenPorEmpresas']['empresas'][0]['precioPromedio'] = 0;
            // }
            // $result['resumenPorEmpresas']['empresas'][0]['porcentajeCompra'] = $result['resumenPorEmpresas']['empresas'][0]['neto'] / $encabezado['totalEntradas'] * 100;

        }
        if ($empresa['neto'] <= 0) {
            $empresa['precioPromedio'] = 0;
        } else {
            $empresa['precioPromedio'] = $empresa['importe'] / $empresa['neto'];
        }

        if ($encabezado['totalEntradas'] > 0) {
            $empresa['porcentajeCompra'] = $empresa['neto'] / $encabezado['totalEntradas'] * 100;
        } else {
            $empresa['porcentajeCompra'] = 0;
        }
        // $result['resumenPorEmpresas']['totalPorcentaje'] += $empresa['porcentajeCompra'];
        if ($tipoMiel == '1') {
            array_push($result['resumenPorEmpresas']['empresas'], $empresa);
        } else {
            if ($empresa['neto'] > 0) {
                array_push($result['resumenPorEmpresas']['empresas'], $empresa);
            }
        }
    }


    foreach ($result['resumenPorEmpresas']['empresas'] as $empresa) {
        $result['resumenPorEmpresas']['totalPorcentaje'] += $empresa['porcentajeCompra'];
    }


    if ($soloEncabezado) {
        return $result['encabezado'];
    } else {
        return $result;
    }
}

function precio_promedio_por_meses($idMes, $filtro, $tipoMiel = '1')
{
    global $con;
    // $idMes = intval($idMes);
    // Solicitar el precio promedio de la miel en los meses pasados

    $compras_de_miel_por_meses = array(
        'resumenCompra' => array(
            'totalKg' => 0,
            'precioPromedio' => 0,
            'totalImporte' => 0,
            'pCompra' => 0,
            'totalKgEntrada' => 0,
            'totalImporteEntrada' => 0,
            'totalKgSalida' => 1,
            'totalImporteSalida' => 0


        ),
        'datos' => []
    );

    for ($i = 1; $i <= $idMes; $i++) {
        $info = calcularInventarioMensual($i, $filtro, true, false, false, false, false, $tipoMiel);

        $sqlMes = $con->prepare('SELECT mes FROM meses WHERE idMes = :idMes');
        $sqlMes->bindParam(':idMes', $i);
        $sqlMes->bindColumn('mes', $info['mes']);
        $sqlMes->execute();
        if ($sqlMes == false) {
            throw new Exception($con->errorInfo());
        } else {
            $sqlMes->fetch(PDO::FETCH_BOUND);
        }

        $compras_de_miel_por_meses['resumenCompra']['totalKg'] += floatval($info['totalInventario']);
        $compras_de_miel_por_meses['resumenCompra']['totalImporte'] += floatval($info['totalImportesAcumulados']);

        $compras_de_miel_por_meses['resumenCompra']['totalKgEntrada'] += floatval($info['totalEntradas']);
        $compras_de_miel_por_meses['resumenCompra']['totalImporteEntrada'] += floatval($info['totalImporteEntrada']);

        $compras_de_miel_por_meses['resumenCompra']['totalKgSalida'] += floatval($info['totalSalidas']);
        $compras_de_miel_por_meses['resumenCompra']['totalImporteSalida'] += floatval($info['totalImporteSalida']);

        array_push($compras_de_miel_por_meses['datos'], $info);
    }

    foreach ($compras_de_miel_por_meses['datos'] as $key => $value) {
        $compras_de_miel_por_meses['datos'][$key]['porcentajeCompra'] = floatval($compras_de_miel_por_meses['datos'][$key]['totalEntradas']) / $compras_de_miel_por_meses['resumenCompra']['totalKgEntrada'] * 100;
        $compras_de_miel_por_meses['resumenCompra']['pCompra'] += $compras_de_miel_por_meses['datos'][$key]['porcentajeCompra'];
    }

    $compras_de_miel_por_meses['resumenCompra']['precioPromedio'] = $compras_de_miel_por_meses['resumenCompra']['totalImporteEntrada'] / $compras_de_miel_por_meses['resumenCompra']['totalKgEntrada'];
    $compras_de_miel_por_meses['resumenCompra']['precioPromedioSalidas'] = $compras_de_miel_por_meses['resumenCompra']['totalImporteSalida'] / $compras_de_miel_por_meses['resumenCompra']['totalKgSalida'];

    return $compras_de_miel_por_meses;

    // </ Solicitar el precio promedio de la miel en los meses pasados //
}


function precio_promedio_por_periodo($fechaUno, $fechaDos, $filtro, $tipoMiel = '1')
{
    global $con;
    // $idMes = intval($idMes);
    // Solicitar el precio promedio de la miel en los meses pasados

    $compras_de_miel_por_periodo = array(
        'resumenCompra' => array(
            'totalKg' => 0,
            'precioPromedio' => 0,
            'totalImporte' => 0,
            'pCompra' => 0,
            'totalKgEntrada' => 0,
            'totalImporteEntrada' => 0,
            'totalKgSalida' => 1,
            'totalImporteSalida' => 0


        ),
        'datos' => []
    );

    // for ($i = 1; $i <= $idMes; $i++) {
    $info2 = calcularInventarioMensual(false, $filtro, false, false, false, $fechaUno, $fechaDos, $tipoMiel);
    // $sqlMes = $con->prepare('SELECT mes FROM meses WHERE idMes = :idMes');
    // $sqlMes->bindParam(':idMes', $i);
    // $sqlMes->bindColumn('mes', $info['mes']);
    // $sqlMes->execute();
    // if ($sqlMes == false) {
    //     throw new Exception($con->errorInfo());
    // } else {
    //     $sqlMes->fetch(PDO::FETCH_BOUND);
    // }
    $f1 = date("d/m/Y", strtotime($fechaUno));
    $f2 = date("d/m/Y", strtotime($fechaDos));

    $compras_de_miel_por_periodo['resumenCompra']['periodo'] = $f1 . ' al ' . $f2;
    $compras_de_miel_por_periodo['resumenCompra']['totalKg'] += floatval($info2['encabezado']['totalInventario']);
    $compras_de_miel_por_periodo['resumenCompra']['totalImporte'] += floatval($info2['encabezado']['totalImportesAcumulados']);

    $compras_de_miel_por_periodo['resumenCompra']['totalKgEntrada'] += floatval($info2['encabezado']['totalEntradas']);
    $compras_de_miel_por_periodo['resumenCompra']['totalImporteEntrada'] += floatval($info2['encabezado']['totalImporteEntrada']);

    $compras_de_miel_por_periodo['resumenCompra']['totalKgSalida'] += floatval($info2['encabezado']['totalSalidas']);
    $compras_de_miel_por_periodo['resumenCompra']['totalImporteSalida'] += floatval($info2['encabezado']['totalImporteSalida']);

    array_push($compras_de_miel_por_periodo['datos'], $info2);
    // }

    foreach ($compras_de_miel_por_periodo['datos'] as $key => $value) {
        $compras_de_miel_por_periodo['datos'][$key]['porcentajeCompra'] = floatval($compras_de_miel_por_periodo['datos'][$key]['encabezado']['totalEntradas']) / $compras_de_miel_por_periodo['resumenCompra']['totalKgEntrada'] * 100;
        $compras_de_miel_por_periodo['resumenCompra']['pCompra'] += $compras_de_miel_por_periodo['datos'][$key]['porcentajeCompra'];
    }

    $compras_de_miel_por_periodo['resumenCompra']['precioPromedio'] = $compras_de_miel_por_periodo['resumenCompra']['totalImporteEntrada'] / $compras_de_miel_por_periodo['resumenCompra']['totalKgEntrada'];
    $compras_de_miel_por_periodo['resumenCompra']['precioPromedioSalidas'] = $compras_de_miel_por_periodo['resumenCompra']['totalImporteSalida'] / $compras_de_miel_por_periodo['resumenCompra']['totalKgSalida'];

    return $compras_de_miel_por_periodo;

    // </ Solicitar el precio promedio de la miel en los meses pasados //
}

function getUltimoMes($tipoMiel)
{

    if ($tipoMiel == '1') {
        $almacenencabezado_tabla = 'almacenencabezado';
    }
     else if($tipoMiel == '5'){ //****2025***
        $almacenencabezado_tabla = 'almacenencabezado_mantequilla';
    } else if($tipoMiel == '6'){
        $almacenencabezado_tabla = 'almacenencabezado_altiplano';
    } else if($tipoMiel == '7'){
        $almacenencabezado_tabla = 'almacenencabezado_naranjo';
    } else if($tipoMiel == '8'){
        $almacenencabezado_tabla = 'almacenencabezado_aguacate';
    } else if($tipoMiel == '9'){
        $almacenencabezado_tabla = 'almacenencabezado_mezquite'; //****2025 */
    } else{
        $almacenencabezado_tabla = 'almacenencabezado_organico';
    }

    global $con;
    $sql = $con->prepare("SELECT MAX(SUBSTR(fecha FROM 6 FOR 2)) AS idMes FROM $almacenencabezado_tabla;");
    $sql->execute();
    if ($sql == false) {
        throw new Exception($con->errorInfo());
    } else {
        $res = $sql->fetch(PDO::FETCH_ASSOC);
    }

    // Ahora verifica en salidas

    $sqlMaximoSalidas = $con->prepare("SELECT MAX(SUBSTR(fecha FROM 6 FOR 2)) AS idMes FROM otrassalidas;
    SELECT MAX(SUBSTR(os.fecha FROM 6 FOR 2)) AS idMes FROM otrassalidas os
    LEFT JOIN otrassalidasdetalle osd ON osd.idOtrasSalidas = os.idOtraSalida
    WHERE osd.idTipoDeMiel = :tipoDeMiel;");
    $sqlMaximoSalidas->bindParam(':tipoDeMiel', $tipoMiel);
    $sqlMaximoSalidas->execute();

    if ($sqlMaximoSalidas == false) {
        throw new Exception($con->errorInfo());
    } else {
        $maxSalidas = $sqlMaximoSalidas->fetch(PDO::FETCH_ASSOC);
    }

    $mesMasGrande = intval($res['idMes']) > intval($maxSalidas['idMes']) ? intval($res['idMes']) : intval($maxSalidas['idMes']);

    return $mesMasGrande;
}
ob_end_clean();
