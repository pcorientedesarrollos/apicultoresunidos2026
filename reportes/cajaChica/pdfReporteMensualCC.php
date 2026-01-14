<?php

require('../../fpdf/FPDF/fpdf.php');
include_once '../../DAOConeccion/conePDO.php';
include_once '../../controlAdministrativo/php/nombreDePersona.php';
include_once '../../controlAdministrativo/prestamos/php/traerTotalesPrestamos.php';
include_once '../../inventarios/php/obtenerInventarioMiel.php';
include_once '../../inventarios/php/obtenerInventarioCera.php';
include_once '../../inventarios/php/obtenerInventarioApicola.php';
include_once '../../inventarios/php/obtenerInventarioMP.php';


date_default_timezone_set('America/Merida');

$pdo = new conePDO();
$con = $pdo->conectar();

function getInfo($idMes, $fechaInicial, $fechaFinal)
{
    global $con;
    try {
        $response = [];
        $response['encabezado'] = [];
        $response['encabezado']['mesActual'] = [];
        $response['encabezado']['mesAnterior'] = [];
        $response['encabezado']['saldoInicial'];
        $response['respaldo']['saldoInicial'];
        $response['detalle'] = [];
        $response['bancos'] = [];
        $response['saldoInicial'] = 0;
        $response['respaldoSaldoInicial'] = 0;

        $inicialEnero = 0;
        $ingresos = 0;
        $egresos = 0;

        if ($idMes) {

            #Obtener el inventario de miel
            if ($idMes > 1) {
                $saldoPasado = array(
                    'totalInventario' => 0,
                    'totalImportesAcumulados' => 0
                );
                for ($i = intval($idMes) - 1; $i > 0; $i--) {
                    $EncabezadoMesPasado = calcularInventarioMensual($i, TRUE, FALSE);
                    $saldoPasado['totalInventario'] += $EncabezadoMesPasado['totalInventario'];
                    $saldoPasado['totalImportesAcumulados'] += $EncabezadoMesPasado['totalImportesAcumulados'];
                }
                $response['inventarioMiel'] = calcularInventarioMensual($idMes, TRUE, $saldoPasado, FALSE);
            } else {
                $response['inventarioMiel'] = calcularInventarioMensual($idMes, TRUE, FALSE, FALSE);
            }

            #Obtener inventario de cera
            if ($idMes > 1) {

                $saldoPasado = array(
                    'importeAcumulado' => 0,
                    'existenciaAcumulada' => 0
                );
                for ($i = intval($idMes) - 1; $i > 0; $i--) {
                    $EncabezadoMesPasado = dameInventarioCera($i, FALSE, FALSE, TRUE);
                    $saldoPasado['importeAcumulado'] += $EncabezadoMesPasado['importeAcumulado'];
                    $saldoPasado['existenciaAcumulada'] += $EncabezadoMesPasado['existenciaAcumulada'];
                }
                $response['inventarioCera'] = dameInventarioCera($idMes, FALSE, $saldoPasado, TRUE);
            } else {
                $response['inventarioCera'] = dameInventarioCera($idMes, FALSE, FALSE, TRUE);
            }

            # Obtener inventario de productos apícolas
            $response['inventarioApicola'] = obtenerInventarioApicola($idMes, FALSE, FALSE);

            #Obtener el inventario de materia prima
            $response['inventarioMP'] = getInventarioMP('1', FALSE, $idMes, FALSE, TRUE);


            $sqlMes = $con->prepare('SELECT mes FROM meses WHERE idMes = :idMes');
            $sqlMes->bindParam(':idMes', $idMes);
            $sqlMes->bindColumn('mes', $response['nombreMes']);
            $sqlMes->execute();
            if ($sqlMes == FALSE) {
                throw new Exception($con->errorInfo());
            } else {
                $sqlMes->fetch(PDO::FETCH_BOUND);
            }
        } else {
            $response['inventarioMiel'] = calcularInventarioMensual(FALSE, TRUE, FALSE, FALSE, $fechaInicial, $fechaFinal);
            $response['inventarioCera'] = dameInventarioCera(FALSE, FALSE, FALSE, TRUE, $fechaFinal);
            $response['inventarioApicola'] = obtenerInventarioApicola(FALSE, FALSE, $fechaFinal);
            $response['inventarioMP'] = getInventarioMP('1', FALSE, FALSE, $fechaFinal, TRUE);
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

        // $saldoAcumulado = 0;

        // foreach ($registros->fetchAll(PDO::FETCH_ASSOC) as $registro) {
        //     $_date = date_create($registro['fecha']);
        //     $registro['fecha'] = date_format($_date, 'd / m / Y');

        //     if ($registro['tipo'] == '0') {
        //         $registro['saldo'] = $saldoAcumulado += $registro['importe'];
        //     } else if ($registro['tipo'] == '1') {
        //         $registro['saldo'] = $saldoAcumulado -= $registro['cantidad'];
        //     }
        //     array_push($response['detalle'], $registro);
        // }

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
                $response['saldoInicial'] = $response['encabezado']['mesAnterior']['saldoFinal'];
                $response['respaldoSaldoInicial'] = $response['encabezado']['mesAnterior']['saldoFinal'];
            } else {
                $response['encabezado']['saldoInicial'] = $response['encabezado']['mesActual']['saldoIncial'];
                $response['respaldo']['saldoInicial'] = $response['encabezado']['mesActual']['saldoIncial'];
                $response['saldoInicial'] = $response['encabezado']['mesActual']['saldoIncial'];
                $response['respaldoSaldoInicial'] = $response['encabezado']['mesActual']['saldoIncial'];
            }
        } else if ($fechaInicial && $fechaFinal) {

            $sqlInicial = "SELECT cd.importe AS inicialEnero FROM cajachicadetalle cd LEFT JOIN cajachica ce ON ce.idCajaChica = cd.idCajaChica WHERE cd.idMovimiento = 0 AND ce.idMes = 1";
            $queryInicial = $con->prepare($sqlInicial);
            $queryInicial->execute();
            $queryInicial->bindColumn('inicialEnero', $inicialEnero);
            $queryInicial->fetch(PDO::FETCH_BOUND);
            $inicialEnero = floatval($inicialEnero);

            $sqlSaldoIngresos = "SELECT SUM(cd.importe) AS ingresos FROM cajachicadetalle cd LEFT JOIN cajachica ce ON ce.idCajaChica = cd.idCajaChica WHERE ce.fecha < :fechaInicial AND cd.idMovimiento > 0";
            $querySaldoIngresos = $con->prepare($sqlSaldoIngresos);
            $querySaldoIngresos->bindParam(':fechaInicial', $fechaInicial);
            $querySaldoIngresos->execute();
            $querySaldoIngresos->bindColumn('ingresos', $ingresos);
            $querySaldoIngresos->fetch(PDO::FETCH_BOUND);
            $ingresos = floatval($ingresos);

            $sqlSaldoEgresos = "SELECT SUM(cd.cantidad) AS egresos FROM cajachicadetalle cd LEFT JOIN cajachica ce ON ce.idCajaChica = cd.idCajaChica WHERE ce.fecha < :fechaInicial";
            $querySaldoEgresos = $con->prepare($sqlSaldoEgresos);
            $querySaldoEgresos->bindParam(':fechaInicial', $fechaInicial);
            $querySaldoEgresos->execute();
            $querySaldoEgresos->bindColumn('egresos', $egresos);
            $querySaldoEgresos->fetch(PDO::FETCH_BOUND);
            $egresos = floatval($egresos);

            $saldoInicial = $inicialEnero + $ingresos - $egresos;
            $response['saldoInicial'] = $saldoInicial;
            $response['respaldoSaldoInicial'] = $saldoInicial;


            // $_consultaIngresosEgresos = $con->prepare("SELECT cc.tipo, cc.total FROM cajachica cc WHERE cc.fecha < :fechaInicial");
            // $_consultaIngresosEgresos->bindParam(':fechaInicial', $fechaInicial);
            // $_consultaIngresosEgresos->execute();

            // if ($_consultaIngresosEgresos->rowCount() >= 1) {
            //     $_totalIngresos = 0;
            //     $_totalEgresos = 0;
            //     foreach ($_consultaIngresosEgresos as $data) {
            //         $data['tipo'] = $data['tipo'] == 0 || $data['tipo'] == 'INGRESO' ? true : false;

            //         if ($data['tipo']) {
            //             $_totalIngresos += $data['total'];
            //         } else {
            //             $_totalEgresos += $data['total'];
            //         }
            //     }
            //     $_saldoFinalMesActual = $_totalIngresos - $_totalEgresos;

            //     $response['encabezado']['saldoInicial'] = $_saldoFinalMesActual;
            //     $response['respaldo']['saldoInicial'] = $_saldoFinalMesActual;
            // } else {
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
            // }

            // // }

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
            $response['encabezado']['mesActual']['saldoFinal'] =  $response['saldoInicial'] + $_totalIngresos - $_totalEgresos;

        }


        // $saldoAcumulado = $response['saldoInicial'];

        foreach ($registros->fetchAll(PDO::FETCH_ASSOC) as $registro) {
            $_date = date_create($registro['fecha']);
            $registro['fecha'] = date_format($_date, 'd / m / Y');

            if ($registro['tipo'] == '0') {
                // $registro['saldo'] = $response['respaldo']['saldoInicial'] += $registro['importe'];
                $registro['saldo'] = $response['respaldoSaldoInicial'] += $registro['importe'];
            } else if ($registro['tipo'] == '1') {
                // $registro['saldo'] = $response['respaldo']['saldoInicial'] -= $registro['cantidad'];
                $registro['saldo'] = $response['respaldoSaldoInicial'] -= $registro['cantidad'];
            }
            array_push($response['detalle'], $registro);
        }

        return [
            'Error' => false,
            'message' => 'Success',
            'content' => $response
        ];
    } catch (Exception $e) {
        return [
            'Error' => true,
            'message' => $e->getMessage() . '.Line ' . $e->getLine(),
            'content' => []
        ];
        exit();
    }
};

function addLine($y, $_info, $pdf, $_Sizes, $_Coor)
{

    $pdf->setY($y + 2);
    $heigthPerRow = 0;

    $pdf->MultiCell($_Sizes['heigth'] - 15, $_Sizes['littleRow'], utf8_decode($_info['fecha']), 0, 'C', 0);
    $heigthPerRow = $pdf->getY() - $y > $heigthPerRow ? $pdf->getY() - $y : $heigthPerRow;
    $pdf->setXY($_Coor['initialX'] - 13  + $_Sizes['heigth'], $y);

    $pdf->MultiCell($_Sizes['heigth'] + 5, $_Sizes['littleRow'], utf8_decode($_info['nombre']), 0, 'L', 0);
    $heigthPerRow = $pdf->getY() - $y > $heigthPerRow ? $pdf->getY() - $y : $heigthPerRow;
    $pdf->setXY($_Coor['initialX'] - 12 + $_Sizes['heigth'] * 2 + 5, $y);

    $pdf->MultiCell($_Sizes['quarter'], $_Sizes['littleRow'], utf8_decode($_info['concepto']), 0, 'L', 0);
    $heigthPerRow = $pdf->getY() - $y > $heigthPerRow ? $pdf->getY() - $y : $heigthPerRow;
    $pdf->setXY($_Coor['initialX'] - 13 + $_Sizes['heigth'] * 2 + $_Sizes['quarter'] + 5, $y);

    $pdf->MultiCell($_Sizes['quarter'], $_Sizes['littleRow'], utf8_decode($_info['descripcion']), 0, 'L', 0);
    $heigthPerRow = $pdf->getY() - $y > $heigthPerRow ? $pdf->getY() - $y : $heigthPerRow;
    $pdf->setXY($_Coor['initialX'] - 23 + $_Sizes['heigth'] * 2 + $_Sizes['quarter'] * 2 + 5, $y);

    $pdf->MultiCell($_Sizes['nine'], $_Sizes['littleRow'], utf8_decode($_info['importe']), 0, 'R', 0);
    $heigthPerRow = $pdf->getY() - $y > $heigthPerRow ? $pdf->getY() - $y : $heigthPerRow;
    $pdf->setXY($_Coor['initialX'] - 23 + $_Sizes['heigth'] * 2 + $_Sizes['quarter'] * 2 + $_Sizes['nine'] + 5, $y);

    $pdf->MultiCell($_Sizes['nine'], $_Sizes['littleRow'], utf8_decode($_info['ingreso']), 0, 'R', 0);
    $heigthPerRow = $pdf->getY() - $y > $heigthPerRow ? $pdf->getY() - $y : $heigthPerRow;
    $pdf->setXY($_Coor['initialX'] - 23 + $_Sizes['heigth'] * 2 + $_Sizes['quarter'] * 2 + $_Sizes['nine'] + 5, $y);

    $pdf->MultiCell($_Sizes['nine'] + 35, $_Sizes['littleRow'], utf8_decode($_info['saldo']), 0, 'R', 0);
    $heigthPerRow = $pdf->getY() - $y > $heigthPerRow ? $pdf->getY() - $y : $heigthPerRow;

    $lineY = $heigthPerRow + $y + .5;
    $pdf->Line($_Coor['initialX'] - 8.5, $lineY, $_Sizes['pageWidth'] - $_Coor['initialX'] + 8.5, $lineY);
    $heigthPerRow = $pdf->getY() - $y > $heigthPerRow ? $pdf->getY() - $y : $heigthPerRow;
    return $heigthPerRow + 1;
};

function outputPdf($idMes, $fechaInicial, $fechaFinal)
{
    global $con;
    $datos = $idMes ? getInfo($idMes, FALSE, FALSE) : getInfo(FALSE, $fechaInicial, $fechaFinal);
    $inventario_de_miel = $datos['content']['inventarioMiel'];
    $inventario_de_cera = $datos['content']['inventarioCera'];
    $inventario_de_apicola = $datos['content']['inventarioApicola'];
    $inventario_MP = $datos['content']['inventarioMP'];
    if (!isset($inventario_MP['totalExistencia'])) {
        $inventario_MP['totalExistencia'] = 0;
    }


    $capacidad_lote = 22000;
    $capacidad_caja_cera = 5;

    if ($idMes) {
        $titulo_inventarios = 'Inventarios al mes de ' . $datos['content']['nombreMes'];
    } else {
        $titulo_inventarios = 'Inventarios a la fecha: ' . $fechaFinal;
    }

    $pdf = new FPDF('L', 'mm', 'A4');
    $pdf->AddPage();
    // $pdf->SetAutoPageBreak(true, 20);
    $pdf->SetAutoPageBreak(false);
    $lateralMargins = 10;
    $pageWidth = $pdf->getPageWidth() - ($lateralMargins * 2);

    $_Sizes = [
        'pageWidth' => $pdf->GetPageWidth(),
        'logo' => 22,
        'row' => 5,
        'littleRow' => 3,
        'mediumRow' => 4,
        'middle' => $pageWidth / 2,
        'thirth' => $pageWidth / 3,
        'quarter' => $pageWidth / 4,
        'sixth' => $pageWidth / 6,
        'heigth' => $pageWidth / 8,
        'nine' => $pageWidth / 9
    ];
    $_Coor = [
        'initialX' => $lateralMargins,
        'logo' => 10,
        'tablasDeComparacionY' => 35,
        'tablaDeMovimientos' => 65,
    ];

    $pdf->SetFillColor(137, 172, 118);
    $pdf->SetTextColor(0, 0, 0);
    $pdf->SetFont('Arial', '', 9);

    $pdf->Image('../img/imgMovimientoCajaChica.png', $_Coor['initialX'], $_Coor['logo'], $_Sizes['logo']);

    $pdf->setXY($_Coor['initialX'] + 25, $_Coor['logo']);
    $pdf->cell(100, 4, utf8_decode('OAXACA MIEL S.A. DE C.V.'), 0, 2, 'C', 0);
    $pdf->cell(100, 4, utf8_decode(' '), 0, 2, 'C', 0);
    $pdf->cell(100, 4, utf8_decode(''), 0, 2, 'C', 0);
    $pdf->cell(100, 4, utf8_decode(''), 0, 2, 'C', 0);
    $pdf->cell(100, 4, utf8_decode(''), 0, 2, 'C', 0);


    $pdf->SetTextColor(255, 255, 255);
    $pdf->SetFont('Arial', 'B', 9);

    $pdf->setXY($_Sizes['sixth'] * 4 + $_Coor['initialX'], $_Coor['logo']);

    $pdf->RoundedRect($_Sizes['sixth'] * 4 + $_Coor['initialX'], $_Coor['logo'], $_Sizes['sixth'] * 2, $_Sizes['row'], 2, '12', 'DF');
    $pdf->Cell($_Sizes['sixth'] * 2, $_Sizes['row'], utf8_decode('INFORME EJECUTIVO'), 0, 0, 'C', 0);


    $pdf->SetTextColor(0, 0, 0);
    $pdf->SetFont('Arial', '', 9);
    $pdf->setXY($_Sizes['sixth'] * 4 + $_Coor['initialX'], $_Coor['logo'] + $_Sizes['row']);
    $pdf->RoundedRect($_Sizes['sixth'] * 4 + $_Coor['initialX'], $_Coor['logo'] + $_Sizes['row'], $_Sizes['sixth'] * 2, $_Sizes['row'], 2, '34', '');
    $pdf->Cell($_Sizes['sixth'] * 2, $_Sizes['row'], utf8_decode($datos['content']['nombreMes']), 0, 0, 'C', 0);

    if ($idMes) {


        #Mes Anterior
        $pdf->setXY($_Coor['initialX'], $_Coor['tablasDeComparacionY'] - 5);
        $pdf->cell($_Sizes['thirth'], $_Sizes['mediumRow'], utf8_decode('Mes Anterior'), 0, 2, 'C', 0);
        $pdf->cell($_Sizes['sixth'], $_Sizes['mediumRow'], utf8_decode('SALDO INICIAL'), 'LTRB', 0, 'C', 0);
        $pdf->cell($_Sizes['sixth'], $_Sizes['mediumRow'], utf8_decode('$ ' . number_format($datos['content']['encabezado']['mesAnterior']['saldoIncial'], 2, '.', ',')), 'TRB', 1, 'R', 0);

        $pdf->setX($_Coor['initialX']);
        $pdf->cell($_Sizes['sixth'], $_Sizes['mediumRow'], utf8_decode('INGRESOS'), 'LRB', 0, 'C', 0);
        $pdf->cell($_Sizes['sixth'], $_Sizes['mediumRow'], utf8_decode('$ ' . number_format($datos['content']['encabezado']['mesAnterior']['totalIngresos'], 2, '.', ',')), 'RB', 1, 'R', 0);

        $pdf->setX($_Coor['initialX']);
        $pdf->cell($_Sizes['sixth'], $_Sizes['mediumRow'], utf8_decode('EGRESOS'), 'LRB', 0, 'C', 0);
        $pdf->cell($_Sizes['sixth'], $_Sizes['mediumRow'], utf8_decode('$ ' . number_format($datos['content']['encabezado']['mesAnterior']['totalEgresos'], 2, '.', ',')), 'RB', 1, 'R', 0);

        $pdf->setX($_Coor['initialX']);
        $pdf->cell($_Sizes['sixth'], $_Sizes['mediumRow'], utf8_decode('PENDIENTES'), 'LRB', 0, 'C', 0);
        $pdf->cell($_Sizes['sixth'], $_Sizes['mediumRow'], utf8_decode('$ ' . number_format($datos['content']['encabezado']['mesAnterior']['totalPendientes'], 2, '.', ',')), 'RB', 1, 'R', 0);

        $pdf->setX($_Coor['initialX']);
        $pdf->cell($_Sizes['sixth'], $_Sizes['mediumRow'], utf8_decode('SALDO REAL'), 'LRB', 0, 'C', 0);
        $pdf->cell($_Sizes['sixth'], $_Sizes['mediumRow'], utf8_decode('$ ' . number_format($datos['content']['encabezado']['mesAnterior']['saldoReal'], 2, '.', ',')), 'RB', 1, 'R', 0);

        $pdf->setX($_Coor['initialX']);
        $pdf->cell($_Sizes['sixth'], $_Sizes['mediumRow'], utf8_decode('SALDO FINAL'), 'LRB', 0, 'C', 0);
        $pdf->cell($_Sizes['sixth'], $_Sizes['mediumRow'], utf8_decode('$ ' . number_format($datos['content']['encabezado']['mesAnterior']['saldoFinal'], 2, '.', ',')), 'RB', 1, 'R', 0);

        #Mes Actual
        $pdf->setXY($_Coor['initialX'] + $_Sizes['sixth'] * 4, $_Coor['tablasDeComparacionY'] - 5);
        $pdf->cell($_Sizes['thirth'], $_Sizes['mediumRow'], utf8_decode('Mes Actual'), 0, 2, 'C', 0);
        $pdf->cell($_Sizes['sixth'], $_Sizes['mediumRow'], utf8_decode('SALDO INICIAL'), 'LTRB', 0, 'C', 0);
        $pdf->cell($_Sizes['sixth'], $_Sizes['mediumRow'], utf8_decode('$ ' . number_format($datos['content']['encabezado']['mesActual']['saldoIncial'], 2, '.', ',')), 'TRB', 1, 'R', 0);

        $pdf->setX($_Coor['initialX'] + $_Sizes['sixth'] * 4);
        $pdf->cell($_Sizes['sixth'], $_Sizes['mediumRow'], utf8_decode('INGRESOS'), 'LRB', 0, 'C', 0);
        $pdf->cell($_Sizes['sixth'], $_Sizes['mediumRow'], utf8_decode('$ ' . number_format($datos['content']['encabezado']['mesActual']['totalIngresos'], 2, '.', ',')), 'RB', 1, 'R', 0);

        $pdf->setX($_Coor['initialX'] + $_Sizes['sixth'] * 4);
        $pdf->cell($_Sizes['sixth'], $_Sizes['mediumRow'], utf8_decode('EGRESOS'), 'LRB', 0, 'C', 0);
        $pdf->cell($_Sizes['sixth'], $_Sizes['mediumRow'], utf8_decode('$ ' . number_format($datos['content']['encabezado']['mesActual']['totalEgresos'], 2, '.', ',')), 'RB', 1, 'R', 0);

        $pdf->setX($_Coor['initialX'] + $_Sizes['sixth'] * 4);
        $pdf->cell($_Sizes['sixth'], $_Sizes['mediumRow'], utf8_decode('PENDIENTES'), 'LRB', 0, 'C', 0);
        $pdf->cell($_Sizes['sixth'], $_Sizes['mediumRow'], utf8_decode('$ ' . number_format($datos['content']['encabezado']['mesActual']['totalPendientes'], 2, '.', ',')), 'RB', 1, 'R', 0);

        $pdf->setX($_Coor['initialX'] + $_Sizes['sixth'] * 4);
        $pdf->cell($_Sizes['sixth'], $_Sizes['mediumRow'], utf8_decode('SALDO REAL'), 'LRB', 0, 'C', 0);
        $pdf->cell($_Sizes['sixth'], $_Sizes['mediumRow'], utf8_decode('$ ' . number_format($datos['content']['encabezado']['mesActual']['saldoReal'], 2, '.', ',')), 'RB', 1, 'R', 0);

        $pdf->setX($_Coor['initialX'] + $_Sizes['sixth'] * 4);
        $pdf->cell($_Sizes['sixth'], $_Sizes['mediumRow'], utf8_decode('SALDO FINAL'), 'LRB', 0, 'C', 0);
        $pdf->cell($_Sizes['sixth'], $_Sizes['mediumRow'], utf8_decode('$ ' . number_format($datos['content']['encabezado']['mesActual']['saldoFinal'], 2, '.', ',')), 'RB', 1, 'R', 0);
    }

    if (!$idMes) {
        $_Coor['tablaDeMovimientos'] = $_Coor['tablasDeComparacionY'];
    }

    $pdf->setXY($_Coor['initialX'] - 9, $_Coor['tablaDeMovimientos'] - 5);

    $pdf->SetFillColor(137, 172, 118);
    $pdf->SetTextColor(255, 255, 255);
    $pdf->SetFont('Arial', '', 7);

    $pdf->cell($_Sizes['heigth'] - 5, $_Sizes['mediumRow'], utf8_decode('FECHA'), 1, 0, 'C', 1);
    $pdf->cell($_Sizes['heigth'] + 5, $_Sizes['mediumRow'], utf8_decode('NOMBRE'), 1, 0, 'C', 1);
    $pdf->cell($_Sizes['quarter'], $_Sizes['mediumRow'], utf8_decode('CONCEPTO'), 1, 0, 'C', 1);
    $pdf->cell($_Sizes['quarter'], $_Sizes['mediumRow'], utf8_decode('DESCRIPCION'), 1, 0, 'C', 1);
    $pdf->cell($_Sizes['nine'], $_Sizes['mediumRow'], utf8_decode('EGRESOS'), 1, 0, 'C', 1);
    $pdf->cell($_Sizes['nine'], $_Sizes['mediumRow'], utf8_decode('INGRESOS'), 1, 0, 'C', 1);
    $pdf->cell($_Sizes['nine'] - 5, $_Sizes['mediumRow'], utf8_decode('SALDO'), 1, 0, 'C', 1);

    //nuevo

    // $y = $_Coor['tablaDeMovimientos'] + $_Sizes['mediumRow'];
    // $pdf->setY($y);

    $pdf->setXY($_Coor['initialX'] - 9, $_Coor['tablaDeMovimientos'] + $_Sizes['mediumRow'] - 5);
    $pdf->SetFillColor(255, 255, 255);
    $pdf->SetTextColor(0, 0, 0);
    $pdf->cell($_Sizes['heigth'] - 5 + 239.6, $_Sizes['mediumRow'], utf8_decode('SALDO INICIAL'), 'B', 0, 'C', 1);
    $pdf->cell($_Sizes['nine'] - 5, $_Sizes['mediumRow'], utf8_decode('$ ' . number_format($datos['content']['saldoInicial'], 2, '.', ',')), 'B', 0, 'R', 1);
    // $pdf->cell($_Sizes['nine'] - 5, $_Sizes['mediumRow'], utf8_decode('$ ' . number_format($datos['content']['encabezado']['saldoInicial'], 2, '.', ',')), 'B', 0, 'R', 1);
    $y = $_Coor['tablaDeMovimientos'] + $_Sizes['mediumRow'] + 5 - 5;
    $pdf->setY($y);
    $pdf->setX($_Coor['initialX'] - 9);
    $pdf->SetFillColor(255, 255, 255);
    $pdf->SetTextColor(0, 0, 0);

    foreach ($datos['content']['detalle'] as $detalle) {
        $_info = array(
            'fecha' => $detalle['fecha'],
            'nombre' => retornarNombre($con, $detalle['tipoDeCliente'], $detalle['nombre']),
            'concepto' => $detalle['concepto'],
            'descripcion' => $detalle['descripcion'],
            'importe' => $detalle['cantidad'],
            'ingreso' => $detalle['importe'],
            'saldo' => $detalle['saldo'],

        );
        $size = addLine($y, $_info, $pdf, $_Sizes, $_Coor);
        $y += $size;
        if ($y >= 200) {
            $pdf->AddPage();
            $y = 15;
        }
    };
    $pdf->setY($y);
    $pdf->setX($_Coor['initialX'] - 9);
    $pdf->SetFillColor(255, 255, 255);
    $pdf->SetTextColor(0, 0, 0);
    $pdf->cell($_Sizes['heigth'] - 5 + 200, $_Sizes['mediumRow'], utf8_decode(''), 'B', 0, 'C', 1);
    $pdf->cell($_Sizes['nine'] - 30, $_Sizes['mediumRow'], utf8_decode('$ ' . number_format($datos['content']['encabezado']['mesActual']['totalEgresos'], 2, '.', ',')), 'B', 0, 'R', 1);
    $pdf->cell($_Sizes['nine'] + 3, $_Sizes['mediumRow'], utf8_decode('$ ' . number_format($datos['content']['encabezado']['mesActual']['totalIngresos'], 2, '.', ',')), 'B', 0, 'R', 1);
    $pdf->cell($_Sizes['nine'], $_Sizes['mediumRow'], utf8_decode('$ ' . number_format($datos['content']['encabezado']['mesActual']['saldoFinal'], 2, '.', ',')), 'B', 0, 'R', 1);

    if (TRUE) {
        // $pdf->SetFillColor(137, 172, 118);
        // $pdf->SetTextColor(255, 255, 255);
        $pdf->addPage();
        $pdf->SetFillColor(255, 255, 255);
        $pdf->SetTextColor(0, 0, 0);

        $pdf->SetFont('Arial', 'B', 9);
        $pdf->setXY($_Coor['initialX'], $pdf->getY() + 10);
        $pdf->cell(100, 5, utf8_decode($titulo_inventarios), 0, 1, 'L', 0);
        $pdf->Ln(5);
        $tablas_totales_y = $pdf->getY();
        $pdf->cell($_Sizes['middle'], $_Sizes['row'], utf8_decode('SALDO AL CIERRE'), 1, 1, 'C', 1);

        // $pdf->SetFillColor(255, 255, 255);
        // $pdf->SetTextColor(0, 0, 0);
        $pdf->SetFont('Arial', '', 9);
        foreach ($datos['content']['bancos'] as $_banco) {
            $pdf->cell($_Sizes['quarter'], $_Sizes['row'], utf8_decode($_banco['banco'] . ' ' . $_banco['numDeCuenta']), 'LBR', 0, 'C', 0);
            $pdf->cell($_Sizes['quarter'], $_Sizes['row'], utf8_decode('$' . number_format($_banco['saldoActual'], 2, '.', ',')), 'BR', 1, 'R', 0);
        }

        $pdf->setXY($_Coor['initialX'] + $_Sizes['middle'], $tablas_totales_y);
        $pdf->cell($_Sizes['middle'] - 30, $_Sizes['row'], utf8_decode('Total Acumulado Existencias Kg de Miel'), 1, 0, 'C', 1);
        $pdf->cell(30, $_Sizes['row'], utf8_decode(number_format($inventario_de_miel['totalInventario'], 2, '.', ',')), 1, 2, 'R', 0);

        $pdf->setX($_Coor['initialX'] + $_Sizes['middle']);
        $pdf->cell($_Sizes['middle'] - 30, $_Sizes['row'], utf8_decode('Total Importe Acumulado de Miel'), 1, 0, 'C', 1);
        $pdf->cell(30, $_Sizes['row'], utf8_decode('$ ' . number_format($inventario_de_miel['totalImportesAcumulados'], 2, '.', ',')), 1, 2, 'R', 0);

        $pdf->setX($_Coor['initialX'] + $_Sizes['middle']);
        $pdf->cell($_Sizes['middle'] - 30, $_Sizes['row'], utf8_decode('Lotes Estimados de Miel'), 1, 0, 'C', 1);
        $pdf->cell(30, $_Sizes['row'], utf8_decode(number_format(intval($inventario_de_miel['totalInventario'] / $capacidad_lote), 2, '.', ',')), 1, 2, 'R', 0);

        $pdf->setX($_Coor['initialX'] + $_Sizes['middle']);
        $pdf->cell($_Sizes['middle'] - 30, $_Sizes['row'], utf8_decode('Precio Promedio de Inventario de Miel'), 1, 0, 'C', 1);
        $pdf->cell(30, $_Sizes['row'], utf8_decode('$ ' . number_format($inventario_de_miel['promedioPrecio'], 2, '.', ',')), 1, 2, 'R', 0);


        // 


        $pdf->setXY($_Coor['initialX'], $pdf->getY() + 70);
        $pdf->cell($_Sizes['middle'] - 30, $_Sizes['row'], utf8_decode('Total Acumulado Existencias Kg de Cera'), 1, 0, 'C', 1);
        $pdf->cell(30, $_Sizes['row'], utf8_decode(number_format($inventario_de_cera['existenciaAcumulada'], 2, '.', ',')), 1, 0, 'R', 0);

        $pdf->cell($_Sizes['middle'] - 30, $_Sizes['row'], utf8_decode('Cajas Estimadas de Cera'), 1, 0, 'C', 1);
        $pdf->cell(30, $_Sizes['row'], utf8_decode(number_format(intval($inventario_de_cera['existenciaAcumulada'] / $capacidad_caja_cera), 2, '.', ',')), 1, 2, 'R', 0);


        $pdf->setX($_Coor['initialX']);
        $pdf->cell($_Sizes['middle'] - 30, $_Sizes['row'], utf8_decode('Total Importe Acumulado de Cera'), 1, 0, 'C', 1);
        $pdf->cell(30, $_Sizes['row'], utf8_decode('$ ' . number_format($inventario_de_cera['importeAcumulado'], 2, '.', ',')), 1, 2, 'R', 0);

        $pdf->Ln(10);

        $pdf->setX($_Coor['initialX']);
        $pdf->cell($_Sizes['middle'] - 30, $_Sizes['row'], utf8_decode('Total Acumulado Existencias de Tambores'), 1, 0, 'C', 1);
        $pdf->cell(30, $_Sizes['row'], utf8_decode(number_format($inventario_MP['totalExistencia'], 2, '.', ',')), 1, 0, 'R', 0);
        // $pdf->cell($_Sizes['middle'] - 30, $_Sizes['row'], utf8_decode('Cajas de Abeja'), 1, 0, 'C', 1);
        // $pdf->cell(30, $_Sizes['row'], utf8_decode(''), 1, 2, 'C', 0);

        $pdf->Ln(10);

        foreach ($inventario_de_apicola as $producto) {
            if (intval($producto['acumulado']) != 0) {
                $pdf->setX($_Coor['initialX']);
                $pdf->cell($_Sizes['middle'] - 30, $_Sizes['row'], utf8_decode($producto['subconceptoCC']), 1, 0, 'C', 1);
                $pdf->cell(30, $_Sizes['row'], utf8_decode(number_format($producto['acumulado'], 2, '.', ',')), 1, 2, 'R', 0);
            }
        }
    }

    $_espacioFirma = 20;
    $_largoFirma = 70;
    $_lineas_de_firma = $pdf->getY() + 10;
    $pdf->setXY($_Coor['initialX'], $_lineas_de_firma);
    $pdf->Ln($_espacioFirma);
    $pdf->Line($_Coor['initialX'], $pdf->getY(), $_Coor['initialX'] + $_largoFirma, $pdf->getY());
    $pdf->cell($_largoFirma, $_Sizes['mediumRow'], utf8_decode('ELABORÓ'), 0, 0, 'C', 0);

    $pdf->setXY($_Coor['initialX'] + $_largoFirma + 20, $_lineas_de_firma);
    $pdf->Ln($_espacioFirma);
    $pdf->Line($_Coor['initialX'] + $_largoFirma + 20, $pdf->getY(), $_Coor['initialX'] + $_largoFirma + 20 + $_largoFirma, $pdf->getY());
    $pdf->setX($_Coor['initialX'] + $_largoFirma + 20);
    $pdf->cell($_largoFirma, $_Sizes['mediumRow'], utf8_decode('AUTORIZÓ'), 0, 0, 'C', 0);


    $pdf->Output('I', 'Informe ejecutivo.pdf', TRUE);
}

if (isset($_GET['idMes'])) {
    outputPdf($_GET['idMes'], FALSE, FALSE);
} else if (isset($_GET['inicial']) && isset($_GET['final'])) {
    outputPdf(FALSE, $_GET['inicial'], $_GET['final']);
} else {
    exit();
}
