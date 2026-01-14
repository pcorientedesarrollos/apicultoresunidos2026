<?php
include_once '../../DAOConeccion/conePDO.php';
include_once '../../controlAdministrativo/php/nombreDePersona.php';
$pdo = new conePDO();
$con = $pdo->conectar();
$fecha = Date('y-m-d');
header('Content-type: application/vnd.ms-excel');
header("Content-Disposition: attachment; filename=Estado_de_cuenta_menusal_$fecha.xls");
header("Prafma: no-cache");
header("Expires:0");

function getDatos($idCuenta, $idMes)
{

    global $con;
    $datos = $con->prepare("SELECT ab.cantidad, ab.ingresoEgreso, b.banco, cb.idCuenta, cb.numDeCuenta, m.mes, tipoDePersona,
        (SELECT COALESCE(SUM(ab.cantidad),0) FROM auxiliardebancos ab WHERE tipoDePersona != 0 AND ab.ingresoEgreso = 0 AND ab.idCuenta = :idCuenta
        AND SUBSTR(ab.fecha FROM 6 FOR 2) = $idMes)  AS saldoIngresos,
        (SELECT COALESCE(SUM(ab.cantidad),0) FROM auxiliardebancos ab WHERE ab.ingresoEgreso = 1 AND ab.idCuenta = :idCuenta
        AND SUBSTR(ab.fecha FROM 6 FOR 2) = $idMes) AS saldoEgresos
        FROM bancos b 
        INNER JOIN cuentasbancarias cb ON b.idBanco = cb.idBanco
        INNER JOIN auxiliardebancos ab ON ab.idCuenta = cb.idCuenta
        INNER JOIN meses m ON m.idMes = ab.idMes
        WHERE cb.idCuenta = :idCuenta AND SUBSTR(ab.fecha FROM 6 FOR 2) = $idMes
        ORDER BY ab.tipoDePersona = 0, ab.fecha DESC, ab.hora DESC");
    $datos->bindParam(':idCuenta', $idCuenta);
    $datos->execute();


    $resultado['auxiliarDeBancos'] = array();

    $ultimoSaldoEnMes = 0;
    if ($datos->rowCount() >= 1) {
        foreach ($datos->fetchAll(PDO::FETCH_ASSOC) as $i) {
            if ($i['ingresoEgreso'] == 0) {
                if ($i['tipoDePersona'] != 0) {
                    $ultimoSaldoEnMes += $i['cantidad'];
                }
            } else {
                $ultimoSaldoEnMes -= $i['cantidad'];
            }
        }
        $resultado['banco'] = $i['banco'];
        $resultado['numDeCuenta'] = $i['numDeCuenta'];
        $resultado['saldoIngresos'] = $i['saldoIngresos'];
        $resultado['saldoEgresos'] = $i['saldoEgresos'];
        $resultado['mes'] = $i['mes'];
    }

    $consultaSaldoInicial = $con->prepare("SELECT cantidad FROM auxiliardebancos WHERE idCuenta = :idCuenta
    AND SUBSTR(fecha FROM 6 FOR 2) = $idMes LIMIT 1");
    $consultaSaldoInicial->bindParam(':idCuenta', $idCuenta);
    $consultaSaldoInicial->execute();
    $consultaSaldoInicial->bindColumn('cantidad', $saldoInicial);
    $fetchData = $consultaSaldoInicial->fetch(PDO::FETCH_BOUND);
    $resultado['saldoInicial'] = $saldoInicial;
    $resultado['saldoActual'] = $resultado['saldoInicial'] + $resultado['saldoIngresos'] - $resultado['saldoEgresos'];


    $auxiliar = $con->prepare("SELECT idAuxiliar, fecha, hora, idMes, LEFT (descripcion, 76) AS descripcion, 
    LEFT(concepto, 36)AS concepto, nombreDe, tipoDePersona, cantidad, ingresoEgreso
    FROM auxiliardebancos WHERE idCuenta = :idCuenta
    AND SUBSTR(fecha FROM 6 FOR 2) = $idMes
    ORDER BY tipoDePersona = 0, fecha DESC, hora DESC");
    $auxiliar->bindParam(':idCuenta', $idCuenta);
    $auxiliar->execute();

    $saldoAcumulado = 0;


        #Utilizamos el array reverse, IMPORTANTE! para calcular el saldo acumulado del movimiento
    foreach (array_reverse($auxiliar->fetchAll(PDO::FETCH_ASSOC)) as $auxiliar) {
        if ($auxiliar['ingresoEgreso'] == '0') {
            $auxiliar['ingreso'] = $auxiliar['cantidad'];
            $auxiliar['egreso'] = '';
            $auxiliar['tipo'] = true;
            $auxiliar['saldo'] = $saldoAcumulado += $auxiliar['cantidad'];
        } else {
            $auxiliar['egreso'] = $auxiliar['cantidad'];
            $auxiliar['ingreso'] = '';
            $auxiliar['tipo'] = false;
            $auxiliar['saldo'] = $saldoAcumulado -= $auxiliar['cantidad'];
        }

        $auxiliar['miNombre'] = retornarNombre($con, $auxiliar['tipoDePersona'], $auxiliar['nombreDe']);
        array_push($resultado['auxiliarDeBancos'], $auxiliar);
    };
    
        #VOLVEMOS A ARRAY REVERSE MUY IMPORTANTE! PARA EL ORDEN EL LA TABLA
    $resultado['auxiliarDeBancos'] = array_reverse($resultado['auxiliarDeBancos']);

    return $resultado;
}

if (isset($_GET['idCuenta']) && isset($_GET['idMes'])) {
    $idCuenta = $_GET['idCuenta'];
    $idMes = $_GET['idMes'];

    $resultado = getDatos($idCuenta, $idMes);

    echo '<table width="100%">
    <thead>
        <tr>
            <th colspan="7">
                Oaxaca Miel S.A. de C.V
            </th>
        </tr>
        <tr>
            <th colspan="7">
                Estado de cuenta del mes de ' . $resultado["mes"] .
        '</th>
        </tr>
        <tr>
            <th colspan="7">' . $resultado["banco"] . " " . $resultado["numDeCuenta"] . '</th>
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
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
        </tr>
        <tr>
            <th>' . utf8_decode("FECHA") . '</th>
            <th>' . utf8_decode("CONCEPTO") . '</th>
            <th>' . utf8_decode("DESCRIPCIÓN") . '</th>
            <th>' . utf8_decode("A NOMBRE DE") . '</th>
            <th>' . utf8_decode("INGRESO") . '</th>
            <th>' . utf8_decode("EGRESO") . '</th>
            <th>' . utf8_decode("SALDO") . '</th>
        </tr>
    </thead>
    <tbody>';

    foreach ($resultado['auxiliarDeBancos'] as $inv) {

        echo '<tr>
            <td style= "text-align:left;">' . $inv['fecha'] . '</td>
            <td style= "text-align:left;">' . utf8_decode($inv['concepto']) . '</td>
            <td style= "text-align:left;">' . utf8_decode($inv['descripcion']) . '</td>
            <td style= "text-align:left;">' . utf8_decode($inv['miNombre']) . '</td>
            <td style= "text-align:right;">' . "$" . number_format($inv['ingreso'] == '' ? 0 : $inv['ingreso'], 2, '.', ',') . '</td>
            <td style= "text-align:right;">' . "$" . number_format($inv['egreso'] == '' ? 0 : $inv['egreso'], 2, '.', ',') . '</td>
            <td style= "text-align:right;">' . "$" . number_format($inv['saldo'], 2, '.', ',') . '</td>
        </tr>';
    }


    echo '<tr>
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
        <th>INICIAL</th>
        <th>INGRESOS</th>
        <th>EGRESOS</th>
        <th>SALDO</th>
    </tr>';

    echo '
    <tr>
        <td></td>
        <td></td>
        <td></td>
        <td style= "text-align:right;">' . "$" . number_format($resultado['saldoInicial'], 2, '.', ',') . '</td>
        <td style= "text-align:right;">' . "$" . number_format($resultado['saldoIngresos'], 2, '.', ',') . '</td>
        <td style= "text-align:right;">' . "$" . number_format($resultado['saldoEgresos'], 2, '.', ',') . '</td>
        <td style= "text-align:right;">' . "$" . number_format($resultado['saldoActual'], 2, '.', ',') . '</td>
    </tr>';

    echo '</tbody>
    </table>';
}