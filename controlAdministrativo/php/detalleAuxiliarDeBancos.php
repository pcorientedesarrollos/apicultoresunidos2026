<?php

include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$con = $pdo->conectar();
if (isset($_GET['idCuenta']) && isset($_GET['idMes'])) {
    $idCuenta = $_GET['idCuenta'];
    $idMes = $_GET['idMes'];
    
    $datos = $con->prepare("SELECT ab.cantidad, ab.tipoMovimiento, b.banco, cb.idCuenta, cb.numDeCuenta, ab.tipoDePersona, ingresoEgreso
                            FROM bancos b 
                            INNER JOIN cuentasbancarias cb ON b.idBanco = cb.idBanco
                            INNER JOIN auxiliardebancos ab ON ab.idCuenta = cb.idCuenta
                            WHERE cb.idCuenta = :idCuenta AND SUBSTR(ab.fecha FROM 6 FOR 2) = $idMes
                            ORDER BY ab.tipoDePersona = 0, ab.fecha DESC, ab.hora DESC");
    $datos->bindParam(':idCuenta', $idCuenta);
    $datos->execute();

    // $resultado = $datos->fetch(PDO::FETCH_ASSOC);
    $resultado['auxiliarDeBancos'] = array();

    $ultimoSaldoEnMes = 0;
    $bonos = 0;
    $salidas = 0;
    
    if ($datos->rowCount() >= 1) {
        foreach ($datos->fetchAll(PDO::FETCH_ASSOC) as $i) {
            if ($i['ingresoEgreso'] == 0) {
                $ultimoSaldoEnMes += $i['cantidad'];                                    
                if($i['tipoDePersona'] != 0){
                $bonos += $i['cantidad'];                
                }
            } else {
                $ultimoSaldoEnMes -= $i['cantidad'];
                $salidas += $i['cantidad'];                
            }
        }
        $resultado['banco'] = $i['banco'];
        $resultado['numDeCuenta'] = $i['numDeCuenta'];
        $resultado['bonos'] = $bonos;
        $resultado['salidas'] = $salidas;       
        $resultado['saldoActual'] = $ultimoSaldoEnMes;
    }

    $consultaSaldoInicial = $con->prepare("SELECT cantidad FROM auxiliardebancos WHERE idCuenta = :idCuenta
                                AND SUBSTR(fecha FROM 6 FOR 2) = $idMes LIMIT 1");
    $consultaSaldoInicial->bindParam(':idCuenta', $idCuenta);
    $consultaSaldoInicial->execute();
    $consultaSaldoInicial->bindColumn('cantidad', $saldoInicial);
    $fetchData = $consultaSaldoInicial->fetch(PDO::FETCH_BOUND);
    $resultado['saldoInicial'] = $saldoInicial;

    $auxiliar = $con->prepare("SELECT * FROM auxiliardebancos WHERE idCuenta = :idCuenta
                                AND SUBSTR(fecha FROM 6 FOR 2) = $idMes
                                ORDER BY tipoDePersona = 0, fecha DESC, hora DESC");
    $auxiliar->bindParam(':idCuenta', $idCuenta);
    $auxiliar->execute();

    $saldoAcumulado = 0;

    #Utilizamos el array reverse, IMPORTANTE! para calcular el saldo acumulado del movimiento
    foreach (array_reverse($auxiliar->fetchAll(PDO::FETCH_ASSOC)) as $auxiliar) {
        $auxiliar['subconcepto'] = new stdClass();
        $auxiliar['subconcepto']->subSubcuenta = $auxiliar['subsubcuenta'];

        if ($auxiliar['ingresoEgreso'] == '0') {
            $auxiliar['ingreso'] = $auxiliar['cantidad'];
            $auxiliar['tipo'] = true;
            $auxiliar['saldo'] = $saldoAcumulado += $auxiliar['cantidad'];
        } else {
            $auxiliar['egreso'] = $auxiliar['cantidad'];
            $auxiliar['tipo'] = false;
            $auxiliar['saldo'] = $saldoAcumulado -= $auxiliar['cantidad'];
        }

        switch ($auxiliar['tipoDePersona']) {
            case '1':
                $obtener = $con->prepare("SELECT nombre AS miNombre FROM proveedor WHERE idProveedor = :idProveedor");
                $obtener->bindParam(':idProveedor', $auxiliar['nombreDe']);
                $obtener->execute();
                $obtener->bindColumn('miNombre', $auxiliar['miNombre']);
                $obtener->fetch(PDO::FETCH_BOUND);
            break;
            case '3':
                $obtener = $con->prepare("SELECT nombreProveedor AS miNombre FROM proveedoresmantto WHERE idProveedorMantto = :idProveedorMantto");
                $obtener->bindParam(':idProveedorMantto', $auxiliar['nombreDe']);
                $obtener->execute();
                $obtener->bindColumn('miNombre', $auxiliar['miNombre']);
                $obtener->fetch(PDO::FETCH_BOUND);
            break;
            case '4':
                $obtener = $con->prepare("SELECT nombre  AS miNombre FROM personaloaxaca WHERE idPersonalOM = :id");
                $obtener->bindParam(':id', $auxiliar['nombreDe']);
                $obtener->execute();
                $obtener->bindColumn('miNombre', $auxiliar['miNombre']);
                $obtener->fetch(PDO::FETCH_BOUND);
            break;
            case '6':
                $obtener = $con->prepare("SELECT nombre  AS miNombre FROM clientes WHERE idCliente = :id");
                $obtener->bindParam(':id', $auxiliar['nombreDe']);
                $obtener->execute();
                $obtener->bindColumn('miNombre', $auxiliar['miNombre']);
                $obtener->fetch(PDO::FETCH_BOUND);
            break;
            case '8':
                $obtener = $con->prepare("SELECT nombre  AS miNombre FROM propios WHERE idPropio = :id");
                $obtener->bindParam(':id', $auxiliar['nombreDe']);
                $obtener->execute();
                $obtener->bindColumn('miNombre', $auxiliar['miNombre']);
                $obtener->fetch(PDO::FETCH_BOUND);
            break;
            case '9':
                $obtener = $con->prepare("SELECT nombre  AS miNombre FROM acreedores WHERE idAcreedor = :id");
                $obtener->bindParam(':id', $auxiliar['nombreDe']);
                $obtener->execute();
                $obtener->bindColumn('miNombre', $auxiliar['miNombre']);
                $obtener->fetch(PDO::FETCH_BOUND);
            break;
            default:
                $auxiliar['miNombre'] = '';
                break;
        }

        array_push($resultado['auxiliarDeBancos'], $auxiliar);
    };

    #VOLVEMOS A ARRAY REVERSE MUY IMPORTANTE! PARA EL ORDEN EL LA TABLA
    $resultado['auxiliarDeBancos'] = array_reverse($resultado['auxiliarDeBancos']);
    
    echo json_encode($resultado);
} else {
    exit();
}
