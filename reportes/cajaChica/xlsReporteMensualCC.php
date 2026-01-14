<?php
include_once '../../DAOConeccion/conePDO.php';
include_once '../../controlAdministrativo/php/nombreDePersona.php';
include_once '../../controlAdministrativo/prestamos/php/traerTotalesPrestamos.php';
include_once '../../inventarios/php/obtenerInventarioMiel.php';
include_once '../../inventarios/php/obtenerInventarioCera.php';
include_once '../../inventarios/php/obtenerInventarioApicola.php';
include_once '../../inventarios/php/obtenerInventarioMP.php';

$pdo = new conePDO();
$con = $pdo->conectar();
$fecha = Date('y-m-d');
header('Content-type: application/vnd.ms-excel');
header("Content-Disposition: attachment; filename=Estado_de_cuenta_menusal_$fecha.xls");
header("Prafma: no-cache");
header("Expires:0");

function getDatos($idMes, $fechaInicial, $fechaFinal)
{

    global $con;

    $response = [];
    $response['encabezado'] = [];
    $response['encabezado']['mesActual'] = [];
    $response['encabezado']['mesAnterior'] = [];
    $response['encabezado']['saldoInicial'];
    $response['respaldo']['saldoInicial'];
    $response['detalle'] = [];
    $response['bancos'] = [];

    if ($idMes) {

        #Obtener el inventario de miel
        if ($idMes > 1) {
            $saldoPasado = array(
                'totalInventario' => 0,
                'totalImportesAcumulados' => 0
            );
            for ($i = intval($idMes) - 1; $i > 0; $i--) {
                $EncabezadoMesPasado = calcularInventarioMensual($i, true, false);
                $saldoPasado['totalInventario'] += $EncabezadoMesPasado['totalInventario'];
                $saldoPasado['totalImportesAcumulados'] += $EncabezadoMesPasado['totalImportesAcumulados'];
            }
            $response['inventarioMiel'] = calcularInventarioMensual($idMes, true, $saldoPasado, false);
        } else {
            $response['inventarioMiel'] = calcularInventarioMensual($idMes, true, false, false);
        }

        #Obtener inventario de cera
        if ($idMes > 1) {

            $saldoPasado = array(
                'importeAcumulado' => 0,
                'existenciaAcumulada' => 0
            );
            for ($i = intval($idMes) - 1; $i > 0; $i--) {
                $EncabezadoMesPasado = dameInventarioCera($i, false, false, true);
                $saldoPasado['importeAcumulado'] += $EncabezadoMesPasado['importeAcumulado'];
                $saldoPasado['existenciaAcumulada'] += $EncabezadoMesPasado['existenciaAcumulada'];
            }
            $response['inventarioCera'] = dameInventarioCera($idMes, false, $saldoPasado, true);
        } else {
            $response['inventarioCera'] = dameInventarioCera($idMes, false, false, true);
        }

        # Obtener inventario de productos apícolas
        $response['inventarioApicola'] = obtenerInventarioApicola($idMes, false, false);

        #Obtener el inventario de materia prima
        $response['inventarioMP'] = getInventarioMP('1', false, $idMes, false, true);


        $sqlMes = $con->prepare('SELECT mes FROM meses WHERE idMes = :idMes');
        $sqlMes->bindParam(':idMes', $idMes);
        $sqlMes->bindColumn('mes', $response['nombreMes']);
        $sqlMes->execute();
        if ($sqlMes == false) {
            throw new Exception($con->errorInfo());
        } else {
            $sqlMes->fetch(PDO::FETCH_BOUND);
        }
    } else {
        $response['inventarioMiel'] = calcularInventarioMensual(false, true, false, false, $fechaInicial, $fechaFinal);
        $response['inventarioCera'] = dameInventarioCera(false, false, false, true, $fechaFinal);
        $response['inventarioApicola'] = obtenerInventarioApicola(false, false, $fechaFinal);
        $response['inventarioMP'] = getInventarioMP('1', false, false, $fechaFinal, true);
        $fecha_inicio = DateTime::createFromFormat('Y-m-d', $fechaInicial);
        $fecha_final = DateTime::createFromFormat('Y-m-d', $fechaFinal);
        $response['nombreMes'] = date_format($fecha_inicio, 'd/m/Y') . ' - ' . date_format($fecha_final, 'd/m/Y');
    }

    $consultaBancosSql = "SELECT b.banco, cb.idCuenta, cb.numDeCuenta, ab.idBanco 
        FROM auxiliardebancos ab    
        INNER JOIN bancos b ON b.idBanco = ab.idBanco
        INNER JOIN cuentasbancarias cb ON cb.idCuenta = ab.idCuenta
        INNER JOIN meses m ON m.idMes = SUBSTR(ab.fecha FROM 6 FOR 2)";

    if ($idMes) {
        $consultaBancosSql .= " WHERE m.idMes = $idMes";
    } else  if ($fechaInicial && $fechaFinal) {
        $consultaBancosSql .= " WHERE ab.fecha BETWEEN $fechaInicial AND $fechaFinal";
    }

    $consultaBancosSql .= " GROUP BY ab.idCuenta ORDER BY b.banco";
    $consultabancos = $con->prepare($consultaBancosSql);
    $consultabancos->execute();

    foreach ($consultabancos->fetchAll(PDO::FETCH_ASSOC) as $datoBanco) {
        $seleccionaElSaldoSql = "SELECT COALESCE(SUM(cantidad),0) AS saldoIngresos,
            (SELECT COALESCE(SUM(cantidad),0) FROM auxiliardebancos WHERE ingresoEgreso = 1 AND idCuenta = :idCuenta AND";
        if ($idMes) {
            $seleccionaElSaldoSql .= " SUBSTR(fecha FROM 6 FOR 2) = $idMes";
        } else  if ($fechaInicial && $fechaFinal) {
            $seleccionaElSaldoSql .= " fecha BETWEEN $fechaInicial AND $fechaFinal";
        }

        $seleccionaElSaldoSql .= ") AS saldoEgresos,
            (SELECT cantidad FROM auxiliardebancos WHERE tipoDePersona = 0 AND ingresoEgreso = 0 AND idCuenta = :idCuenta AND";

        if ($idMes) {
            $seleccionaElSaldoSql .= " SUBSTR(fecha FROM 6 FOR 2) = $idMes";
        } else  if ($fechaInicial && $fechaFinal) {
            $seleccionaElSaldoSql .= " fecha BETWEEN $fechaInicial AND $fechaFinal";
        }

        $seleccionaElSaldoSql .= ") AS saldoInicial
            FROM auxiliardebancos WHERE tipoDePersona != 0 AND ingresoEgreso = 0 AND idCuenta = :idCuenta AND";
        if ($idMes) {
            $seleccionaElSaldoSql .= " SUBSTR(fecha FROM 6 FOR 2) = $idMes";
        } else  if ($fechaInicial && $fechaFinal) {
            $seleccionaElSaldoSql .= " fecha BETWEEN $fechaInicial AND $fechaFinal";
        }

        $seleccionaElSaldo = $con->prepare($seleccionaElSaldoSql);
        $seleccionaElSaldo->bindParam(':idCuenta', $datoBanco['idCuenta']);
        $seleccionaElSaldo->execute();
        $saldo = $seleccionaElSaldo->fetch(PDO::FETCH_ASSOC);
        $saldoIngresos = $saldo['saldoIngresos'];
        $saldoEgresos = $saldo['saldoEgresos'];
        $saldoInicial = $saldo['saldoInicial'];
        $datoBanco['saldoInicial'] = $saldoInicial;
        $datoBanco['saldoIngresos'] = $saldoIngresos;
        $datoBanco['saldoEgresos'] = $saldoEgresos;
        $datoBanco['saldoActual'] = $saldoInicial + $saldoIngresos - $saldoEgresos;
        $datoBanco['numDeCuenta'];
        array_push($response['bancos'], $datoBanco);
    }

    $registrosSql = "SELECT cd.idDetalle, cc.idCajaChica, cc.fecha, cd.concepto,
        cd.descripcion, cc.tipoDeCliente, cc.nombre, cc.tipo, cd.cantidad, cd.importe
        FROM cajachicadetalle cd
        LEFT JOIN cajachica cc ON cc.idCajaChica = cd.idCajaChica
        WHERE cd.movimiento != 'Saldo inicial'";
    if ($idMes) {
        $registrosSql .= " AND cc.idMes = :idMes";
    } else  if ($fechaInicial && $fechaFinal) {
        $registrosSql .= " AND cc.fecha BETWEEN :fechaInicial AND :fechaFinal";
    }

    $registrosSql .= " ORDER BY cc.idCajaChica NOT IN (SELECT idCajaChica FROM cajachica WHERE tipoDeCliente IS NOT NULL) DESC,
        cc.fecha, cc.hora";

    $registros = $con->prepare($registrosSql);

    if ($idMes) {
        $registros->bindParam(':idMes', $idMes);
    } else  if ($fechaInicial && $fechaFinal) {
        $registros->bindParam(':fechaInicial', $fechaInicial);
        $registros->bindParam(':fechaFinal', $fechaFinal);
    }
    $registros->execute();

    //Encabezado de la información
    $_fecha = date('d / m / Y');
    $response['encabezado']['fecha'] = $_fecha;

    if ($idMes) {

        #Mes actual
        $_consultaSaldoInicial = $con->prepare("SELECT total FROM cajachica WHERE tipoDeCliente IS NULL AND idMes = :idMes ORDER BY idCajaChica LIMIT 1");
        $_consultaSaldoInicial->bindParam(':idMes', $idMes);
        $_consultaSaldoInicial->execute();
        if ($_consultaSaldoInicial->rowCount() == 1) {
            $_data = $_consultaSaldoInicial->fetch(PDO::FETCH_ASSOC);
            $_saldoInicialMensual = $_data['total'];
        } else {
            $_saldoInicialMensual = 0;
        }

        $_consultaIngresosEgresos = $con->prepare("SELECT cc.tipo, cc.total FROM cajachica cc WHERE cc.idMes = :idMes");
        $_consultaIngresosEgresos->bindParam(':idMes', $idMes);
        $_consultaIngresosEgresos->execute();

        $_totalIngresos = 0;
        $_totalEgresos = 0;
        foreach ($_consultaIngresosEgresos as $data) {
            $data['tipo'] = $data['tipo'] == 0 || $data['tipo'] == 'INGRESO' ? true : false;

            if ($data['tipo']) {
                $_totalIngresos += $data['total'];
            } else {
                $_totalEgresos += $data['total'];
            }
        }

        $prestamos = getData($con);
        $_totalPendientes = $prestamos['pendientes'] - $prestamos['abonos'];
        $_totalIngresos -= $_saldoInicialMensual;
        $_saldoFinalMesActual = $_saldoInicialMensual + $_totalIngresos - $_totalEgresos;
        $_saldoRealMesActual = $_saldoFinalMesActual - $_totalPendientes;

        $response['encabezado']['fecha'] = $_fecha;
        $response['encabezado']['mesActual']['saldoIncial'] = $_saldoInicialMensual;
        $response['encabezado']['mesActual']['totalIngresos'] = $_totalIngresos;
        $response['encabezado']['mesActual']['totalEgresos'] = $_totalEgresos;
        $response['encabezado']['mesActual']['totalPendientes'] = $_totalPendientes;
        $response['encabezado']['mesActual']['saldoReal'] = $_saldoRealMesActual;
        $response['encabezado']['mesActual']['saldoFinal'] = $_saldoFinalMesActual;

        #Mes anterior

        $idMes = $idMes - 1;
        $_consultaSaldoInicial = $con->prepare("SELECT total FROM cajachica WHERE tipoDeCliente IS NULL AND idMes = :idMes ORDER BY idCajaChica LIMIT 1");
        $_consultaSaldoInicial->bindParam(':idMes', $idMes);
        $_consultaSaldoInicial->execute();
        if ($_consultaSaldoInicial->rowCount() == 1) {
            $_data = $_consultaSaldoInicial->fetch(PDO::FETCH_ASSOC);
            $_saldoInicialMensual = $_data['total'];
        } else {
            $_saldoInicialMensual = 0;
        }

        $_consultaIngresosEgresos = $con->prepare("SELECT cc.tipo, cc.total FROM cajachica cc WHERE cc.idMes = :idMes");
        $_consultaIngresosEgresos->bindParam(':idMes', $idMes);
        $_consultaIngresosEgresos->execute();

        $_totalIngresos = 0;
        $_totalEgresos = 0;
        foreach ($_consultaIngresosEgresos as $data) {
            $data['tipo'] = $data['tipo'] == 0 || $data['tipo'] == 'INGRESO' ? true : false;

            if ($data['tipo']) {
                $_totalIngresos += $data['total'];
            } else {
                $_totalEgresos += $data['total'];
            }
        }

        $prestamos = getData($con);
        $_totalPendientes = $prestamos['pendientes'] - $prestamos['abonos'];
        $_totalIngresos -= $_saldoInicialMensual;
        $_saldoFinalMesActual = $_saldoInicialMensual + $_totalIngresos - $_totalEgresos;
        $_saldoRealMesActual = $_saldoFinalMesActual - $_totalPendientes;

        $response['encabezado']['fecha'] = $_fecha;
        $response['encabezado']['mesAnterior']['saldoIncial'] = $_saldoInicialMensual;
        $response['encabezado']['mesAnterior']['totalIngresos'] = $_totalIngresos;
        $response['encabezado']['mesAnterior']['totalEgresos'] = $_totalEgresos;
        $response['encabezado']['mesAnterior']['totalPendientes'] = $_totalPendientes;
        $response['encabezado']['mesAnterior']['saldoReal'] = $_saldoRealMesActual;
        $response['encabezado']['mesAnterior']['saldoFinal'] = $_saldoFinalMesActual;

        if ($idMes > 1) {
            $response['encabezado']['saldoInicial'] = $response['encabezado']['mesAnterior']['saldoFinal'];
            $response['respaldo']['saldoInicial'] = $response['encabezado']['mesAnterior']['saldoFinal'];
        } else {
            $response['encabezado']['saldoInicial'] = $response['encabezado']['mesActual']['saldoIncial'];
            $response['respaldo']['saldoInicial'] = $response['encabezado']['mesActual']['saldoIncial'];
        }
    } else if ($fechaInicial && $fechaFinal) {
        //Consulta saldos iniciales por periodo (columna saldo)
        // if ($fechaInicial === '2021-01-01') {
        //     $_consultaSaldoInicial = $con->prepare("SELECT total FROM cajachica WHERE tipoDeCliente IS NULL AND idMes = 1 ORDER BY idCajaChica LIMIT 1");
        //     $_consultaSaldoInicial->execute();
        //     if ($_consultaSaldoInicial->rowCount() == 1) {
        //         $_data = $_consultaSaldoInicial->fetch(PDO::FETCH_ASSOC);
        //         $response['encabezado']['saldoInicial'] = $_data['total'];
        //         $response['respaldo']['saldoInicial'] = $_data['total'];
        //     } else {
        //         $response['encabezado']['saldoInicial'] = 0;
        //         $response['respaldo']['saldoInicial'] = 0;
        //     }
        // } else {
        $_consultaIngresosEgresos = $con->prepare("SELECT cc.tipo, cc.total FROM cajachica cc WHERE cc.fecha < :fechaInicial");
        $_consultaIngresosEgresos->bindParam(':fechaInicial', $fechaInicial);
        $_consultaIngresosEgresos->execute();

        if ($_consultaIngresosEgresos->rowCount() >= 1) {
            $_totalIngresos = 0;
            $_totalEgresos = 0;
            foreach ($_consultaIngresosEgresos as $data) {
                $data['tipo'] = $data['tipo'] == 0 || $data['tipo'] == 'INGRESO' ? true : false;

                if ($data['tipo']) {
                    $_totalIngresos += $data['total'];
                } else {
                    $_totalEgresos += $data['total'];
                }
            }
            $_saldoFinalMesActual = $_totalIngresos - $_totalEgresos;

            $response['encabezado']['saldoInicial'] = $_saldoFinalMesActual;
            $response['respaldo']['saldoInicial'] = $_saldoFinalMesActual;
        } else {
            $_consultaSaldoInicial = $con->prepare("SELECT total FROM cajachica WHERE tipoDeCliente IS NULL AND idMes = 1 ORDER BY idCajaChica LIMIT 1");
            $_consultaSaldoInicial->execute();
            if ($_consultaSaldoInicial->rowCount() == 1) {
                $_data = $_consultaSaldoInicial->fetch(PDO::FETCH_ASSOC);
                $response['encabezado']['saldoInicial'] = $_data['total'];
                $response['respaldo']['saldoInicial'] = $_data['total'];
            } else {
                $response['encabezado']['saldoInicial'] = 0;
                $response['respaldo']['saldoInicial'] = 0;
            }
        }

        // }

        $_consultaIngresosEgresos = $con->prepare("SELECT cc.tipo, cc.total FROM cajachica cc WHERE cc.fecha BETWEEN :fechaInicial AND :fechaFinal");
        $_consultaIngresosEgresos->bindParam(':fechaInicial', $fechaInicial);
        $_consultaIngresosEgresos->bindParam(':fechaFinal', $fechaFinal);
        $_consultaIngresosEgresos->execute();

        $_totalIngresos = 0;
        $_totalEgresos = 0;
        foreach ($_consultaIngresosEgresos as $data) {
            $data['tipo'] = $data['tipo'] == 0 || $data['tipo'] == 'INGRESO' ? true : false;

            if ($data['tipo']) {
                $_totalIngresos += $data['total'];
            } else {
                $_totalEgresos += $data['total'];
            }
        }
        $response['encabezado']['mesActual']['totalIngresos'] = $_totalIngresos;
        $response['encabezado']['mesActual']['totalEgresos'] = $_totalEgresos;
        $response['encabezado']['mesActual']['saldoFinal'] =  $response['encabezado']['saldoInicial'] + $_totalIngresos - $_totalEgresos;
    }

    foreach ($registros->fetchAll(PDO::FETCH_ASSOC) as $registro) {
        $_date = date_create($registro['fecha']);
        $registro['fecha'] = date_format($_date, 'd / m / Y');

        if ($registro['tipo'] == '0') {
            $registro['saldo'] = $response['respaldo']['saldoInicial'] += $registro['importe'];
        } else if ($registro['tipo'] == '1') {
            $registro['saldo'] = $response['respaldo']['saldoInicial'] -= $registro['cantidad'];
        }
        array_push($response['detalle'], $registro);
    }

    return $response;
}

if (isset($_GET['idMes'])) {
    $idMes = $_GET['idMes'];
    $resultado = getDatos($idMes, false, false);
} else if (isset($_GET['inicial']) && isset($_GET['final'])) {
    $fechaInicial = $_GET['inicial'];
    $fechaFinal = $_GET['final'];
    $resultado = getDatos(false, $fechaInicial, $fechaFinal);
} else {
    exit();
}

$resultado = isset($_GET['idMes']) ? getDatos($_GET['idMes'], false, false) : getDatos(false, $_GET['inicial'], $_GET['final']);
$inventario_de_miel = $resultado['inventarioMiel'];
$inventario_de_cera = $resultado['inventarioCera'];
$inventario_de_apicola = $resultado['inventarioApicola'];
$inventario_MP = $resultado['inventarioMP'];
if (!isset($inventario_MP['totalExistencia'])) {
    $inventario_MP['totalExistencia'] = 0;
}
$capacidad_lote = 22000;
$capacidad_caja_cera = 5;

echo '

<table width="100%">
    <thead>
        <tr>
            <th colspan="7">
                Oaxaca Miel S.A. de C.V
            </th>
        </tr>
        <tr>';
if (isset($_GET['idMes'])) {
    echo '<th colspan="7"> Registros al mes de ' . $resultado['nombreMes'] . '</th>';
} else {
    echo '<th colspan="7"> Registros a periodo:  ' . $resultado['nombreMes'] . '</th>';
}
echo '</tr>

        <tr>
            <th colspan="6">  </th>
        </tr>';
if (isset($_GET['idMes'])) {
    echo '<tr>
    <th colspan="2">Mes anterior</th>
    <th></th>
    <th colspan="2">Mes Actual</th>
</tr>
<tr>
    <td>SALDO INICIAL</td>
    <td style= "text-align:right;"> ' . "$" . number_format($resultado['encabezado']['mesAnterior']['saldoIncial'], 2, '.', ',') . ' </td>
     <td></td>
    <td>SALDO INICIAL</td>
    <td style= "text-align:right;"> ' . "$" . number_format($resultado['encabezado']['mesActual']['saldoIncial'], 2, '.', ',') . ' </td>
</tr>

<tr>    
    <td>INGRESOS</td>
    <td style= "text-align:right;"> ' . "$" . number_format($resultado['encabezado']['mesAnterior']['totalIngresos'], 2, '.', ',') . ' </td>
    <td></td>
    <td>INGRESOS</td>
    <td style= "text-align:right;"> ' . "$" . number_format($resultado['encabezado']['mesActual']['totalIngresos'], 2, '.', ',') . ' </td>
</tr>            
<tr>    
    <td>EGRESOS</td>
    <td style= "text-align:right;"> ' . "$" . number_format($resultado['encabezado']['mesAnterior']['totalEgresos'], 2, '.', ',') . ' </td>
    <td></td>
    <td>EGRESOS</td>
    <td style= "text-align:right;"> ' . "$" . number_format($resultado['encabezado']['mesActual']['totalEgresos'], 2, '.', ',') . ' </td>   
</tr>
<tr>
    <td>PENDIENTES</td>
    <td style= "text-align:right;"> ' . "$" . number_format($resultado['encabezado']['mesAnterior']['totalPendientes'], 2, '.', ',') . ' </td>
    <td></td>
    <td>PENDIENTES</td>
    <td style= "text-align:right;"> ' . "$" . number_format($resultado['encabezado']['mesAnterior']['totalPendientes'], 2, '.', ',') . ' </td>
    </tr>
<tr>    
    <td>SALDO REAL</td>
    <td style= "text-align:right;"> ' . "$" . number_format($resultado['encabezado']['mesAnterior']['saldoReal'], 2, '.', ',') . ' </td>
    <td></td>  
    <td>SALDO REAL</td>
    <td style= "text-align:right;"> ' . "$" . number_format($resultado['encabezado']['mesActual']['saldoReal'], 2, '.', ',') . ' </td>
</tr>
<tr>    
    <td>SALDO FINAL</td>
    <td style= "text-align:right;"> ' . "$" . number_format($resultado['encabezado']['mesAnterior']['saldoFinal'], 2, '.', ',') . ' </td>
    <td></td>
    <td>SALDO FINAL</td>
    <td style= "text-align:right;"> ' . "$" . number_format($resultado['encabezado']['mesActual']['saldoFinal'], 2, '.', ',') . ' </td>
</tr>';
}

echo '<tr>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
      
        </tr>
        <tr>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
        
        </tr>
        <tr>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
        
        </tr>
        <tr>
            <th>' . utf8_decode("FECHA") . '</th>
            <th>' . utf8_decode("NOMBRE") . '</th>
            <th>' . utf8_decode("CONCEPTO") . '</th>
            <th>' . utf8_decode("DESCRIPCIÓN") . '</th>
            <th>' . utf8_decode("EGRESOS") . '</th>
            <th>' . utf8_decode("INGRESOS") . '</th>
            <th>' . utf8_decode("SALDO") . '</th>
        </tr>
        </thead>
    <tbody>
        <tr>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td>SALDO INICIAL</td>
            <td style= "text-align:right;"> ' . "$" . number_format($resultado['encabezado']['saldoInicial'], 2, '.', ',') . ' </td>
        </tr>
    ';

foreach ($resultado['detalle'] as $detalle) {

    echo '
        <tr>
            <td style= "text-align:left;">' . $detalle['fecha'] . '</td>
            <td style= "text-align:left;">' . utf8_decode(retornarNombre($con, $detalle['tipoDeCliente'], $detalle['nombre'])) . '</td>
            <td style= "text-align:left;">' . utf8_decode($detalle['concepto']) . '</td>
            <td style= "text-align:left;">' . utf8_decode($detalle['descripcion']) . '</td>
            <td style= "text-align:right;">' . "$" . number_format($detalle['cantidad'] == '' ? 0 : $detalle['cantidad'], 2, '.', ',') . '</td>
            <td style= "text-align:right;">' . "$" . number_format($detalle['importe'] == '' ? 0 : $detalle['importe'], 2, '.', ',') . '</td>
            <td style= "text-align:right;">' . "$" . number_format($detalle['saldo'] == '' ? 0 : $detalle['saldo'], 2, '.', ',') . '</td>
        </tr>';
}


echo '<tr>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td style= "text-align:right;"> ' . "$" . number_format($resultado['encabezado']['mesActual']['totalEgresos'], 2, '.', ',') . ' </td>
        <td style= "text-align:right;"> ' . "$" . number_format($resultado['encabezado']['mesActual']['totalIngresos'], 2, '.', ',') . ' </td>
        <td style= "text-align:right;"> ' . "$" . number_format($resultado['encabezado']['mesActual']['saldoFinal'], 2, '.', ',') . ' </td>
    </tr>
    <tr>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
    </tr>
    <tr>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
    </tr>
    <tr>
        <th></th>
        <th></th>
        <th></th>
        <th></th>
        <th></th>
        <th></th>
        <th></th>
    </tr>';
if (isset($_GET['idMes'])) {
    echo '<th colspan="7"> Inventarios al mes de ' . $resultado['nombreMes'] . '</th>';
} else {
    echo '<th colspan="7"> Inventarios a la fecha:  ' . $_GET['final'] . '</th>';
}
echo '
    <tr>
        <th>SALDO AL CIERRE</th>
    </tr>';
foreach ($resultado['bancos'] as $banco) {
    echo '<tr>
                <td style= "text-align:left;">' . utf8_decode($banco['banco'] . ' ' . $banco['numDeCuenta']) . '</td>
                <td style= "text-align:right;">' . "$" . number_format($banco['saldoActual'] == '' ? 0 : $banco['saldoActual'], 2, '.', ',') . '</td>
        </tr>';
}
echo '
    <tr>
        <td> ' . utf8_decode('Total Acumulado Existencias Kg de Miel') . ' </td>
        <td style= "text-align:right;">' . number_format($inventario_de_miel['totalInventario'] == '' ? 0 : $inventario_de_miel['totalInventario'], 2, '.', ',') . '</td>
    </tr>

    <tr>
        <td> ' . utf8_decode('Total Importe Acumulado de Miel') . ' </td>
        <td style= "text-align:right;">' . "$" . number_format($inventario_de_miel['totalImportesAcumulados'] == '' ? 0 : $inventario_de_miel['totalImportesAcumulados'], 2, '.', ',') . '</td>
    </tr>

    <tr>
    <td> ' . utf8_decode('Lotes Estimados de Miel') . ' </td>
    <td style= "text-align:right;">' . utf8_decode(number_format(intval($inventario_de_miel['totalInventario'] / $capacidad_lote), 2, '.', ',')) . '</td>
</tr>

<tr>
    <td> ' . utf8_decode('Precio Promedio de Inventario de Miel') . ' </td>
    <td style= "text-align:right;">' . "$" . number_format($inventario_de_miel['promedioPrecio'] == '' ? 0 : $inventario_de_miel['promedioPrecio'], 2, '.', ',') . '</td>
</tr>

<tr>
    <td> ' . utf8_decode('Total Acumulado Existencias Kg de Cera') . ' </td>
    <td style= "text-align:right;">' . number_format($inventario_de_cera['existenciaAcumulada'] == '' ? 0 : $inventario_de_cera['existenciaAcumulada'], 2, '.', ',') . '</td>
    <td> ' . utf8_decode('Cajas Estimadas de Cera') . ' </td>
    <td style= "text-align:right;">' . number_format(intval($inventario_de_cera['existenciaAcumulada'] / $capacidad_caja_cera), 2, '.', ',') . '</td>
</tr>

<tr>
    <td> ' . utf8_decode('Total Importe Acumulado de Cera') . ' </td>
    <td style= "text-align:right;">' . "$" . number_format($inventario_de_cera['importeAcumulado'] == '' ? 0 : $inventario_de_cera['importeAcumulado'], 2, '.', ',') . '</td>
</tr>

</tbody>
</table>';
