<?php

if (!isset($_GET['informeFinanciero'])) {
    include_once '../../DAOConeccion/conePDO.php';
    include_once '../../controlAdministrativo/php/nombreDePersona.php';
    $pdo = new conePDO();
    $con = $pdo->conectar();
    $con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
}

function obtenerSaldosIniciales($tipoDeCera)
{
    global $con;
    if ($tipoDeCera == '1') {
        $nombre = 'cera';
    } else {
        $nombre = 'cera_organica';
    }
    $seleccionarPasado = $con->prepare('SELECT existenciaPasada, importeAcumuladoPasado FROM saldoinicialinventario WHERE nombre = :nombre;');
    $seleccionarPasado->bindParam(':nombre', $nombre);
    $seleccionarPasado->execute();
    if ($seleccionarPasado == FALSE) {
        throw new Exception($con->errorInfo());
    }

    return $seleccionarPasado->fetch(PDO::FETCH_ASSOC);
}

function dameInventarioCera($idMes, $acumulado, $encabezado = FALSE, $soloEncabezado = FALSE, $fechaFinal = FALSE, $tipoCera = false)
{
    global $con;
    $resultado = array(
        'registros' => [],
        'encabezado' => []
    );

    if ($encabezado) {
        $resultado['encabezado']['importeAcumuladoPasado'] = $encabezado['importeAcumulado'];
        $resultado['encabezado']['existenciaAcumuladaPasada'] = $encabezado['existenciaAcumulada'];
    } else {
        if (!$acumulado) {
            if ($idMes == 1 || $fechaFinal) {
                $datosIniciales = obtenerSaldosIniciales($tipoCera);
                $resultado['encabezado']['importeAcumuladoPasado'] = $datosIniciales['importeAcumuladoPasado'];
                $resultado['encabezado']['existenciaAcumuladaPasada'] = $datosIniciales['existenciaPasada'];
            } else {
                // Si el mes no es enero y no trajo los encabezados, entonces solo debe devolver el mes que se solicita, ya que el saldo inical se solicitará en enero
                $resultado['encabezado']['importeAcumuladoPasado'] = 0;
                $resultado['encabezado']['existenciaAcumuladaPasada'] = 0;
            }
        } else {
            $datosIniciales = obtenerSaldosIniciales($tipoCera);
            $resultado['encabezado']['importeAcumuladoPasado'] = $datosIniciales['importeAcumuladoPasado'];
            $resultado['encabezado']['existenciaAcumuladaPasada'] = $datosIniciales['existenciaPasada'];
        }
    }

    $resultado['encabezado']['importeAcumulado'] = $resultado['encabezado']['importeAcumuladoPasado'];
    $resultado['encabezado']['existenciaAcumulada'] = $resultado['encabezado']['existenciaAcumuladaPasada'];
    $resultado['encabezado']['totalEntradas'] = 0;
    $resultado['encabezado']['totalSalidas'] = 0;
    $resultado['encabezado']['totalPrecioKg'] = 0;
    $resultado['encabezado']['totalExistencia'] = 0;
    $resultado['encabezado']['totalKgEntradas'] = 0;
    $resultado['encabezado']['totalKgSalidas'] = 0;


    $consultaInventario = "SELECT aec.fecha, ac.movimiento, ac.subcuenta, ac.concepto, ac.importe, aec.tipo, ac.kgTotal, aec.tipoPersona, aec.idProveedor
    FROM almacencera ac
    LEFT JOIN almacenencabezadocera aec ON ac.idAlmacenEncabezado = aec.idAlmacen";
    if (!$acumulado) {
        if ($fechaFinal) {
            $consultaInventario .= " WHERE aec.fecha <= '" . $fechaFinal . "'";
        } else {
            $consultaInventario .= " WHERE SUBSTR(aec.fecha FROM 6 FOR 2) = " . $idMes;
        }
        if ($tipoCera) {
            $consultaInventario .= " AND aec.tipoCera = " . $tipoCera;
        }
    } else {
        if ($tipoCera) {
            $consultaInventario .= " WHERE aec.tipoCera = " . $tipoCera;
        }
    }
    $consultaInventario .= " ORDER BY fecha DESC";
    $sqlQuery = $con->prepare($consultaInventario);
    $sqlQuery->execute();

    if ($sqlQuery == FALSE) {
        throw new Exception($con->errorInfo());
    } else {
        $queryResult = $sqlQuery->fetchAll(PDO::FETCH_ASSOC);
    }

    foreach ($queryResult as $registro) {
        $registro['nombre'] = retornarNombre($con, $registro['tipoPersona'], $registro['idProveedor']);
        if ($registro['tipo'] == '1') {
            $registro['importeEntrada'] = floatval($registro['importe']);
            $registro['importeSalida'] = 0;
            $registro['kgEntrada'] = floatval($registro['kgTotal']);
            $registro['kgSalida'] = 0;
            if ($registro['kgEntrada'] > 0) {
                $registro['precioKg'] = $registro['importeEntrada'] / $registro['kgEntrada'];
            } else {
                $registro['precioKg'] = 0;
            }
        } else if ($registro['tipo'] == '2') {

            // Si NO tiene precio, ( salidas principalmente ): 
            if ($resultado['encabezado']['existenciaAcumulada'] > 0) {
                // Si hay existencias, se calcula
                $registro['importeSalida'] = $resultado['encabezado']['importeAcumulado'] / $resultado['encabezado']['existenciaAcumulada'];
            } else {
                // Si no hay existencia, pues es cero, 
                $registro['importeSalida'] = 0;
            }

            $registro['importeEntrada'] = 0;
            // $registro['importeSalida'] = floatval($registro['importe']);
            $registro['kgEntrada'] = 0;
            $registro['kgSalida'] = floatval($registro['kgTotal']);
            if ($registro['kgSalida'] > 0) {
                $registro['precioKg'] = $registro['importeSalida'] / $registro['kgSalida'];
            } else {
                $registro['precioKg'] = 0;
            }
        }


        // Acumulación del importe
        $resultado['encabezado']['importeAcumulado'] += floatval($registro['importeEntrada']) - floatval($registro['importeSalida']);
        $registro['importeAcumulado'] = $resultado['encabezado']['importeAcumulado'];

        // Acumulación de los KG
        $resultado['encabezado']['existenciaAcumulada'] += floatval($registro['kgEntrada']) - floatval($registro['kgSalida']);
        $registro['existenciakg'] = $resultado['encabezado']['existenciaAcumulada'];


        $resultado['encabezado']['totalEntradas'] += floatval($registro['importeEntrada']);
        $resultado['encabezado']['totalSalidas'] += floatval($registro['importeSalida']);
        $resultado['encabezado']['totalKgEntradas'] += floatval($registro['kgEntrada']);
        $resultado['encabezado']['totalKgSalidas'] += floatval($registro['kgSalida']);

        array_push($resultado['registros'], $registro);
    }
    if ($resultado['encabezado']['totalKgEntradas']) {
        $resultado['encabezado']['totalPrecioKg'] = $resultado['encabezado']['totalEntradas'] / $resultado['encabezado']['totalKgEntradas'];
    }

    return $soloEncabezado ? $resultado['encabezado'] : $resultado;
};
