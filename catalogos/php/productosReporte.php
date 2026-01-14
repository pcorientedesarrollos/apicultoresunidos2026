<?php

        
function ObtenerSubCuentas(){
    include_once '../../DAOConeccion/conePDO.php';
    include_once '../../controlAdministrativo/php/nombreDePersona.php';

    $pdo = new conePDO();
    $con = $pdo->conectar();
    $datos = $con->prepare('SELECT * FROM subcuentas WHERE idCuentaConcepto = 12 ');
    $datos->execute();
    $resultado = array();
    if ($datos == false) {
        throw new Exception($con->errorInfo());
    }

        if ($datos->rowCount() >= 1) {
            foreach ($datos->fetchAll(PDO::FETCH_ASSOC) as $dato) {
                $idSubcuenta = $dato['idSubcuenta'];
                $sqlSubSub = $con->prepare("SELECT ssc.idSubSubcuenta, ssc.clave, ssc.subSubcuenta as subcuentaConcepto, ssc.precioUnitario, s.idSubcuenta 
                                                        FROM subsubcuentas ssc 
                                                        LEFT JOIN subcuentas s ON s.idSubcuenta = ssc.idSubcuenta 
                                                        WHERE s.idSubcuenta = :idSubcuenta AND ssc.ocultar = 0
                                                        ORDER BY subsubcuenta ASC");
                $sqlSubSub->bindParam(':idSubcuenta', $idSubcuenta);
                $sqlSubSub->execute();

                if ($sqlSubSub == false) {
                    throw new Exception($con->errorInfo());
                }


                $dato['subSubcuentas'] = [];
                    foreach ($sqlSubSub->fetchAll(PDO::FETCH_ASSOC) as $data) {
                    $data['existenciaPasada'] = 0;
                    $data['importePasado'] = 0;
                    $data['existencia'] = 0;
                    // $data['totalImporteSalida'] = 0;


                    // Saldo inicial con el nuevo modulo
                    $sqlSaldoInicial = $con->prepare("SELECT existenciaPasada, importePasado FROM saldoinicial_apicola WHERE nombre = :nombre");
                    $sqlSaldoInicial->bindParam(':nombre', $data['subcuentaConcepto']);
                    $sqlSaldoInicial->bindColumn('existenciaPasada', $data['existenciaPasada']);
                    $sqlSaldoInicial->execute();
                    if ($sqlSaldoInicial == false) {
                        throw new Exception($con->errorInfo());
                    } else {
                        $sqlSaldoInicial->fetch(PDO::FETCH_BOUND);
                    }

                    if (!$data['existenciaPasada']) {
                        $data['existenciaPasada'] = 0;
                    }

                    if (!$data['importePasado']) {
                        $data['importePasado'] = 0;
                    }

                    $data['importeAcumulado'] = $data['importePasado'];
                    $data['acumulado'] = $data['existenciaPasada'];
                    $data['entradas'] = $data['existenciaPasada'];
                    $data['salidas'] = 0;
                    $data['importeEntrada'] = 0;
                    $data['importeSalida'] = 0;
                    $data['precioPromedio'] = 0;

                    $sqlSelect = "SELECT aa.cantidad, aa.descripcion, aa.importe, aa.costoUnitario, aea.tipo, aea.tipoPersona, aea.idProveedor, aea.fecha FROM `almacenapicola` aa
                        LEFT JOIN almacenencabezadoapicola aea ON aa.idAlmacenEncabezado = aea.idAlmacen
                        WHERE aa.concepto = :subcuentaConcepto";
                
                    $query = $con->prepare($sqlSelect);
                    $query->bindParam(':subcuentaConcepto', $data['subcuentaConcepto']);
                    $query->execute();
            
                    if ($query == false) {
                        throw new Exception($con->errorInfo());
                    }

                    foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $registro) {
        
                    $registro['nombre'] = retornarNombre($con, $registro['tipoPersona'], $registro['idProveedor']);
                    if ($registro['tipo'] == '1') {
                        // Entrada
                        $registro['entrada'] = floatval($registro['cantidad']);
                        $registro['salida'] = 0;
                        $registro['importeEntrada'] = $registro['importe'];
                        $registro['importeSalida'] = 0;
                        $data['acumulado'] += $registro['entrada'];
                        $data['entradas'] += $registro['entrada'];
                        $data['importeEntrada'] += $registro['importe'];
        
                        // Acumular el importe
                        $registro['acumulado'] = $data['importeAcumulado'] + $registro['importeEntrada'];
                        $data['importeAcumulado'] += $registro['importeEntrada'];
        
                    } else if ($registro['tipo'] == '2') {
                        // Salida
                        $registro['entrada'] = 0;
                        $registro['salida'] = floatval($registro['cantidad']);
                        $registro['importeSalida'] = $registro['importe'];
                        $registro['importeEntrada'] = 0;
                        $data['acumulado'] -= $registro['salida'];
                        $data['salidas'] += $registro['salida'];
                        $data['importeSalida'] += $registro['importe'];
                        // $resultado['totalImporteSalida'] += $registro['importe'];
        
                        // Acumular el importe
                        $registro['acumulado'] = $data['importeAcumulado'] - $registro['importeSalida'];
                        $data['importeAcumulado'] -= $registro['importeSalida'];
        
                    }
        
                    $data['existencia'] = $data['acumulado'];
        
                    // array_push($data['registros'], $registro);
                }



                    array_push($dato['subSubcuentas'], $data);
                    }
                array_push($resultado, $dato);
            }
        }
  


// echo $json_response = json_encode($resultado);
return $resultado;

}
