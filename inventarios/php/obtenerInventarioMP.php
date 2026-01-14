<?php
include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$con = $pdo->conectar();

function getInventarioMP($tipoMiel, $acumulado = FALSE, $idMes = FALSE, $fechaFinal = FALSE, $soloEncabezado = FALSE)
{
    global $con;
    $resultado = [];
    switch ($tipoMiel) {
        case '1':
            $encabezado_Entrada = 'materiaprimaencabezadoentradas';
            $detalle_Entrada = 'materiaprimadetalleentradas';
            $encabezado_Salida = 'materiaprimaencabezadosalidas';
            $detalle_Salida = 'materiaprimadetallesalidas';
            $nombre = 'materia_prima';
            break;
        case '2':
            $encabezado_Entrada = 'materiaprimaencabezadoentradas_organico';
            $detalle_Entrada = 'materiaprimadetalleentradas_organico';
            $encabezado_Salida = 'materiaprimaencabezadosalidas_organico';
            $detalle_Salida = 'materiaprimadetallesalidas_organico';
            $nombre = 'materia_prima_organico';
    }
    if ($acumulado || $fechaFinal) {
        $seleccionarPasado = $con->prepare('SELECT existenciaPasada, importeAcumuladoPasado FROM saldoinicialinventario WHERE nombre = :nombre');
        $seleccionarPasado->bindParam(':nombre', $nombre);
        $seleccionarPasado->execute();
        if ($seleccionarPasado == false) {
            throw new Exception($con->errorInfo());
        } else {
            $dato = $seleccionarPasado->fetch(PDO::FETCH_ASSOC);
        }

        $sqlInventarioMP = "SELECT mee.idEntradaMateria, mee.fecha, mee.idProveedor, mee.tipoCliente,
        mde.subcuenta, mde.concepto, mde.cantidad, mde.precioUnitario, mde.importe, '0' AS movimiento
        FROM $encabezado_Entrada mee
        LEFT JOIN $detalle_Entrada mde ON mde.idEntradaMateria = mee.idEntradaMateria";
        if ($fechaFinal) {
            $sqlInventarioMP .= " WHERE mee.fecha <= '" . $fechaFinal . "'";
        }
        $sqlInventarioMP .= " UNION
        SELECT mes.idSalidaMateria, mes.fecha, mes.idProveedor, mes.tipoCliente,
        mds.subcuenta, mds.concepto, mds.cantidad, mds.precioUnitario, mds.importe, '1' AS movimiento
        FROM $encabezado_Salida mes
        LEFT JOIN $detalle_Salida mds ON mds.idSalidaMateria = mes.idSalidaMateria";
        if ($fechaFinal) {
            $sqlInventarioMP .= " WHERE mes.fecha <= '" . $fechaFinal . "'";
        }
        $sqlInventarioMP .= " ORDER BY fecha ASC";

        $datos = $con->prepare($sqlInventarioMP);
        $datos->execute();
        $resultado = array(
            'registros' => [],
            'encabezado' => [],
            // 'porConcepto' => []
            // $result['resumenPorEmpresas'] = [
            //     'totalKg' => $encabezado['totalEntradas'],
            //     'totalImporte' => $encabezado['totalImporteEntrada'],
            //     'precioPromedio' => $encabezado['promedioPrecio'],
            //     'totalPorcentaje' => 0,
            //     'empresas' => []
            // ];
        );
        $entradas = 0;
        $salidas = 0;
        $entradasImporte = 0;
        $salidasImporte = 0;
        $existenciaPasada = $dato['existenciaPasada'];
        $importePasado = $dato['importeAcumuladoPasado'];
        if ($datos->rowCount() >= 1) {
            foreach ($datos->fetchAll(PDO::FETCH_ASSOC) as $registros) {
                if ($registros['movimiento'] == '0') {
                    $registros['entrada'] = $registros['cantidad'];
                    $registros['importeEntrada'] = $registros['importe'];
                    $entradas += $registros['cantidad'];
                    $entradasImporte += $registros['importe'];
                } else if ($registros['movimiento'] == '1') {
                    $registros['salida'] = $registros['cantidad'];
                    $registros['importeSalida'] = $registros['importe'];
                    $salidas += $registros['cantidad'];
                    $salidasImporte += $registros['importe'];
                }

                if ($registros['tipoCliente'] == '1') {
                    $sqlNombre = "SELECT nombre AS proveedor FROM proveedor WHERE idProveedor = :idProveedor";
                    $datoNombre = $con->prepare($sqlNombre);
                    $datoNombre->bindParam(':idProveedor', $registros['idProveedor']);
                    $datoNombre->execute();
                    while ($row = $datoNombre->fetch()) {
                        $registros['proveedor'] = $row["proveedor"];
                    }
                } else if ($registros['tipoCliente'] == '3') {
                    $sqlNombre = "SELECT nombreProveedor AS proveedor FROM proveedoresmantto WHERE idProveedorMantto = :idProveedor";
                    $datoNombre = $con->prepare($sqlNombre);
                    $datoNombre->bindParam(':idProveedor', $registros['idProveedor']);
                    $datoNombre->execute();
                    while ($row = $datoNombre->fetch()) {
                        $registros['proveedor'] = $row["proveedor"];
                    }
                } else {
                    $registros['proveedor'] = "";
                }

                $registros['existencia'] = $existenciaPasada + $entradas - $salidas;
                $registros['importeAcumulado'] = $importePasado + $entradasImporte - $salidasImporte;
                array_push($resultado['registros'], $registros);
            }
            $resultado['encabezado']['existenciaAcumuladaPasada'] = $existenciaPasada;
            $resultado['encabezado']['importeAcumuladoPasado'] = $importePasado;
            $resultado['encabezado']['totalEntradas'] = $entradas;
            $resultado['encabezado']['totalSalidas'] = $salidas;
            $resultado['encabezado']['totalExistencia'] = $registros['existencia'];
            $resultado['encabezado']['totalImporteEntradas'] = $entradasImporte;
            $resultado['encabezado']['totalImporteSalidas'] = $salidasImporte;
            $resultado['encabezado']['totalImporteAcumulado'] = $registros['importeAcumulado'];
        }
    } else if ($idMes) {
        if ($idMes == 1) {

            $seleccionarPasado = $con->prepare('SELECT existenciaPasada, importeAcumuladoPasado FROM saldoinicialinventario WHERE nombre = :nombre;');
            $seleccionarPasado->bindParam(':nombre', $nombre);
            $seleccionarPasado->execute();
            if ($seleccionarPasado == false) {
                throw new Exception($con->errorInfo());
            } else {
                $dato = $seleccionarPasado->fetch(PDO::FETCH_ASSOC);
            }

            $datos = $con->prepare("SELECT mee.idEntradaMateria, mee.fecha, mee.idProveedor, mee.tipoCliente,
                                    mde.subcuenta, mde.concepto, mde.cantidad, mde.precioUnitario, mde.importe, '0' AS movimiento
                                    FROM $encabezado_Entrada mee
                                    LEFT JOIN $detalle_Entrada mde ON mde.idEntradaMateria = mee.idEntradaMateria
                                    WHERE SUBSTR(mee.fecha FROM 6 FOR 2) = $idMes
                                        UNION
                                    SELECT mes.idSalidaMateria, mes.fecha, mes.idProveedor, mes.tipoCliente,
                                    mds.subcuenta, mds.concepto, mds.cantidad, mds.precioUnitario, mds.importe, '1' AS movimiento
                                    FROM $encabezado_Salida mes
                                    LEFT JOIN $detalle_Salida mds ON mds.idSalidaMateria = mes.idSalidaMateria
                                    WHERE SUBSTR(mes.fecha FROM 6 FOR 2) = $idMes ORDER BY fecha ASC");
            $datos->execute();
            $resultado = array(
                'registros' => [],
                'encabezado' => []
            );
            $entradas = 0;
            $salidas = 0;
            $entradasImporte = 0;
            $salidasImporte = 0;
            $existenciaPasada = $dato['existenciaPasada'];
            $importePasado = $dato['importeAcumuladoPasado'];
            if ($datos->rowCount() >= 1) {
                foreach ($datos->fetchAll(PDO::FETCH_ASSOC) as $registros) {
                    if ($registros['movimiento'] == '0') {
                        $registros['entrada'] = $registros['cantidad'];
                        $registros['importeEntrada'] = $registros['importe'];
                        $entradas += $registros['cantidad'];
                        $entradasImporte += $registros['importe'];
                    } else if ($registros['movimiento'] == '1') {
                        $registros['salida'] = $registros['cantidad'];
                        $registros['importeSalida'] = $registros['importe'];
                        $salidas += $registros['cantidad'];
                        $salidasImporte += $registros['importe'];
                    }

                    if ($registros['tipoCliente'] == '1') {
                        $sqlNombre = "SELECT nombre AS proveedor FROM proveedor WHERE idProveedor = :idProveedor";
                        $datoNombre = $con->prepare($sqlNombre);
                        $datoNombre->bindParam(':idProveedor', $registros['idProveedor']);
                        $datoNombre->execute();
                        while ($row = $datoNombre->fetch()) {
                            $registros['proveedor'] = $row["proveedor"];
                        }
                    } else if ($registros['tipoCliente'] == '3') {
                        $sqlNombre = "SELECT nombreProveedor AS proveedor FROM proveedoresmantto WHERE idProveedorMantto = :idProveedor";
                        $datoNombre = $con->prepare($sqlNombre);
                        $datoNombre->bindParam(':idProveedor', $registros['idProveedor']);
                        $datoNombre->execute();
                        while ($row = $datoNombre->fetch()) {
                            $registros['proveedor'] = $row["proveedor"];
                        }
                    } else {
                        $registros['proveedor'] = "";
                    }

                    $registros['existencia'] = $existenciaPasada + $entradas - $salidas;
                    $registros['importeAcumulado'] = $importePasado + $entradasImporte - $salidasImporte;
                    array_push($resultado['registros'], $registros);
                }
                $resultado['encabezado']['existenciaAcumuladaPasada'] = $existenciaPasada;
                $resultado['encabezado']['importeAcumuladoPasado'] = $importePasado;
                $resultado['encabezado']['totalEntradas'] = $entradas;
                $resultado['encabezado']['totalSalidas'] = $salidas;
                $resultado['encabezado']['totalExistencia'] = $registros['existencia'];
                $resultado['encabezado']['totalImporteEntradas'] = $entradasImporte;
                $resultado['encabezado']['totalImporteSalidas'] = $salidasImporte;
                $resultado['encabezado']['totalImporteAcumulado'] = $registros['importeAcumulado'];
            }
        } else {

            $entradasExistencia = 0;
            $entradasImporte = 0;
            $salidasExistencia = 0;
            $salidasImporte = 0;
            $datoExistenciaPasado = 0;
            $datoImportePasado = 0;

            $seleccionarInicial = $con->prepare('SELECT existenciaPasada AS existenciaInicial, importeAcumuladoPasado AS importeInicial FROM saldoinicialinventario WHERE nombre = :nombre');
            $seleccionarInicial->bindParam(':nombre', $nombre);
            $seleccionarInicial->execute();
            if ($seleccionarInicial == false) {
                throw new Exception($con->errorInfo());
            } else {
                $info = $seleccionarInicial->fetch(PDO::FETCH_ASSOC);
            }

            $seleccionarPasado = $con->prepare("SELECT SUM(me.cantidadTotal) AS existenciaPasada, SUM(me.importeTotal) AS importeAcumuladoPasado, '1' AS movimiento 
            FROM $encabezado_Entrada me WHERE SUBSTR(me.fecha FROM 6 FOR 2) < $idMes
            UNION
            SELECT SUM(ms.cantidadTotal) AS existenciaPasada, SUM(ms.importeTotal) AS importeAcumuladoPasado, '2' AS movimiento
            FROM $encabezado_Salida ms WHERE SUBSTR(ms.fecha FROM 6 FOR 2)  < $idMes");
            $seleccionarPasado->execute();
            if ($seleccionarPasado->rowCount() >= 1) {
                foreach ($seleccionarPasado->fetchAll(PDO::FETCH_ASSOC) as $registro) {
                    if ($registro['movimiento'] == '1') {
                        $entradasExistencia = $registro['existenciaPasada'];
                        $entradasImporte = $registro['importeAcumuladoPasado'];
                    } else if ($registro['movimiento'] == '2') {
                        $salidasExistencia = $registro['existenciaPasada'];
                        $salidasImporte += $registro['importeAcumuladoPasado'];
                    }
                    $datoExistenciaPasado = $entradasExistencia - $salidasExistencia;
                    $datoImportePasado = $entradasImporte - $salidasImporte;
                }
            }

            $datos = $con->prepare("SELECT mee.idEntradaMateria, mee.fecha, mee.idProveedor, mee.tipoCliente,
            mde.subcuenta, mde.concepto, mde.cantidad, mde.precioUnitario, mde.importe, '0' AS movimiento
            FROM $encabezado_Entrada mee
            LEFT JOIN $detalle_Entrada mde ON mde.idEntradaMateria = mee.idEntradaMateria
            WHERE SUBSTR(mee.fecha FROM 6 FOR 2) = $idMes
                UNION
            SELECT mes.idSalidaMateria, mes.fecha, mes.idProveedor, mes.tipoCliente,
            mds.subcuenta, mds.concepto, mds.cantidad, mds.precioUnitario, mds.importe, '1' AS movimiento
            FROM $encabezado_Salida mes
            LEFT JOIN $detalle_Salida mds ON mds.idSalidaMateria = mes.idSalidaMateria
            WHERE SUBSTR(mes.fecha FROM 6 FOR 2) = $idMes ORDER BY fecha ASC");
            $datos->execute();
            $resultado = array(
                'registros' => [],
                'encabezado' => []
            );
            $entradas = 0;
            $salidas = 0;
            $entradasImporte = 0;
            $salidasImporte = 0;
            $existenciaPasada = $datoExistenciaPasado + $info['existenciaInicial'];
            $importePasado = $datoImportePasado + $info['importeInicial'];
            if ($datos->rowCount() >= 1) {
                foreach ($datos->fetchAll(PDO::FETCH_ASSOC) as $registros) {
                    if ($registros['movimiento'] == '0') {
                        $registros['entrada'] = $registros['cantidad'];
                        $registros['importeEntrada'] = $registros['importe'];
                        $entradas += $registros['cantidad'];
                        $entradasImporte += $registros['importe'];
                    } else if ($registros['movimiento'] == '1') {
                        $registros['salida'] = $registros['cantidad'];
                        $registros['importeSalida'] = $registros['importe'];
                        $salidas += $registros['cantidad'];
                        $salidasImporte += $registros['importe'];
                    }

                    if ($registros['tipoCliente'] == '1') {
                        $sqlNombre = "SELECT nombre AS proveedor FROM proveedor WHERE idProveedor = :idProveedor";
                        $datoNombre = $con->prepare($sqlNombre);
                        $datoNombre->bindParam(':idProveedor', $registros['idProveedor']);
                        $datoNombre->execute();
                        while ($row = $datoNombre->fetch()) {
                            $registros['proveedor'] = $row["proveedor"];
                        }
                    } else if ($registros['tipoCliente'] == '3') {
                        $sqlNombre = "SELECT nombreProveedor AS proveedor FROM proveedoresmantto WHERE idProveedorMantto = :idProveedor";
                        $datoNombre = $con->prepare($sqlNombre);
                        $datoNombre->bindParam(':idProveedor', $registros['idProveedor']);
                        $datoNombre->execute();
                        while ($row = $datoNombre->fetch()) {
                            $registros['proveedor'] = $row["proveedor"];
                        }
                    } else {
                        $registros['proveedor'] = "";
                    }

                    $registros['existencia'] = $existenciaPasada + $entradas - $salidas;
                    $registros['importeAcumulado'] = $importePasado + $entradasImporte - $salidasImporte;
                    array_push($resultado['registros'], $registros);
                }
                $resultado['encabezado']['existenciaAcumuladaPasada'] = $existenciaPasada;
                $resultado['encabezado']['importeAcumuladoPasado'] = $importePasado;
                $resultado['encabezado']['totalEntradas'] = $entradas;
                $resultado['encabezado']['totalSalidas'] = $salidas;
                $resultado['encabezado']['totalExistencia'] = $registros['existencia'];
                $resultado['encabezado']['totalImporteEntradas'] = $entradasImporte;
                $resultado['encabezado']['totalImporteSalidas'] = $salidasImporte;
                $resultado['encabezado']['totalImporteAcumulado'] = $registros['importeAcumulado'];
            }
        }
    }

    return $soloEncabezado ? $resultado['encabezado'] : $resultado;
}
