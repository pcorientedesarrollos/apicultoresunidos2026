<?php

/**
 * Devuelve el importe acumulado de los inventarios
 * de miel, cera, productos apícolas y tambores
 */

function obtenerSaldosInventarios($mes = false, $meses = false)
{
    global $con;
    $resultado = array(
        'almacen' => 0,
        'almacen_organico' => 0,
        'cera' => 0,
        'cera_organico' => 0,
        'otros'
    );

    // Miel convencional y miel orgánica

    $mieles = ['almacen', 'almacen_organico'];

    foreach ($mieles as $miel) {
        $sqlInventario = "SELECT SUM(al.neto)*AVG(precio) as neto FROM $miel al WHERE al.estado = '0' ORDER BY al.idAlmacen ASC";
        $query = $con->prepare($sqlInventario);
        $query->execute();

        if ($query == false) {
            throw new Exception($con->errorInfo());
        }

        $registros = $query->fetch(PDO::FETCH_ASSOC);

        $resultado[$miel] = $registros['neto'];
    }

    //  Cera convencional y cera orgánica, seleccionar el saldo inicial para sumarlo al saldo de cera convencional

    $querySaldoInicialCera = $con->prepare("SELECT importeAcumuladoPasado AS saldo FROM saldoinicialinventario WHERE nombre = 'cera';");
    $querySaldoInicialCera->execute();

    if ($querySaldoInicialCera == false) {
        throw new Exception($con->errorInfo());
    }
    $saldoInicialCera = $querySaldoInicialCera->fetch(PDO::FETCH_ASSOC);

    // Convencional
    // Ingresos
    $queryIngresosCeraConvencional = $con->prepare("SELECT COALESCE(SUM(total),0) as ingresos FROM almacenencabezadocera WHERE tipo = 1 AND tipoCera = 1");
    $queryIngresosCeraConvencional->execute();

    if ($queryIngresosCeraConvencional == false) {
        throw new Exception($con->errorInfo());
    }
    $totalIngresosCeraConvencional = $queryIngresosCeraConvencional->fetch(PDO::FETCH_ASSOC);

    // Egresos

    $queryEgresosCeraConvencional = $con->prepare("SELECT COALESCE(SUM(total),0) as egresos FROM almacenencabezadocera WHERE tipo = 2 AND tipoCera = 1");
    $queryEgresosCeraConvencional->execute();

    if ($queryEgresosCeraConvencional == false) {
        throw new Exception($con->errorInfo());
    }
    $totalEgresosCeraConvencional = $queryEgresosCeraConvencional->fetch(PDO::FETCH_ASSOC);

    $resultado['cera'] = $saldoInicialCera['saldo'] + $totalIngresosCeraConvencional['ingresos'] - $totalEgresosCeraConvencional['egresos'];

    // Organico
    // Ingresos
    $queryIngresosCeraOrganica = $con->prepare("SELECT COALESCE(SUM(total),0) as ingresos FROM almacenencabezadocera WHERE tipo = 1 AND tipoCera = 2");
    $queryIngresosCeraOrganica->execute();

    if ($queryIngresosCeraOrganica == false) {
        throw new Exception($con->errorInfo());
    }
    $totalIngresosCeraOrganica = $queryIngresosCeraOrganica->fetch(PDO::FETCH_ASSOC);

    // Egresos

    $queryEgresosCeraOrganica = $con->prepare("SELECT COALESCE(SUM(total),0) as egresos FROM almacenencabezadocera WHERE tipo = 2 AND tipoCera = 2");
    $queryEgresosCeraOrganica->execute();

    if ($queryEgresosCeraOrganica == false) {
        throw new Exception($con->errorInfo());
    }
    $totalEgresosCeraOrganica = $queryEgresosCeraOrganica->fetch(PDO::FETCH_ASSOC);

    $resultado['cera_organico'] = $totalIngresosCeraOrganica['ingresos'] - $totalEgresosCeraOrganica['egresos'];


    // PRODUCTOS APICOLAS

    // Ingresos
    $queryIngresosApicola = $con->prepare("SELECT SUM(total) as ingresos FROM almacenencabezadoapicola WHERE tipo = 1;");
    $queryIngresosApicola->execute();

    if ($queryIngresosApicola == false) {
        throw new Exception($con->errorInfo());
    }

    $totalIngresosApicola = $queryIngresosApicola->fetch(PDO::FETCH_ASSOC);

    // Egresos

    $queryEgresosApicola = $con->prepare("SELECT SUM(total) as egresos FROM almacenencabezadoapicola WHERE tipo = 2;");
    $queryEgresosApicola->execute();

    if ($queryEgresosApicola == false) {
        throw new Exception($con->errorInfo());
    }

    $totalEgresosApicola = $queryEgresosApicola->fetch(PDO::FETCH_ASSOC);


    // Materia Prima (tambores)

    $querySaldoInicialMateriaPrima = $con->prepare("SELECT importeAcumuladoPasado AS saldo FROM saldoinicialinventario WHERE nombre = 'materia_prima';");
    $querySaldoInicialMateriaPrima->execute();

    if ($querySaldoInicialMateriaPrima == false) {
        throw new Exception($con->errorInfo());
    }
    $saldoInicialMateriaPrima = $querySaldoInicialMateriaPrima->fetch(PDO::FETCH_ASSOC);

    // Ingresos

    $queryIngresosMP = $con->prepare("SELECT SUM(importeTotal) as ingresos FROM materiaprimaencabezadoentradas;");
    $queryIngresosMP->execute();

    if ($queryIngresosMP == false) {
        throw new Exception($con->errorInfo());
    }

    $totalIngresosMP = $queryIngresosMP->fetch(PDO::FETCH_ASSOC);


    // Egresos

    $queryEgresosMP = $con->prepare("SELECT SUM(importeTotal) as egresos FROM materiaprimaencabezadosalidas;");
    $queryEgresosMP->execute();

    if ($queryEgresosMP == false) {
        throw new Exception($con->errorInfo());
    }

    $totalEgresosMP = $queryEgresosMP->fetch(PDO::FETCH_ASSOC);

    // Resultado['otros'] debe ser la suma de apicola más material prima

    $apicola = $totalIngresosApicola['ingresos'] - $totalEgresosApicola['egresos'];
    $materia = $saldoInicialMateriaPrima['saldo'] + $totalIngresosMP['ingresos'] - $totalEgresosMP['egresos'];

    $resultado['otros'] = $apicola + $materia;

    return $resultado;

}
