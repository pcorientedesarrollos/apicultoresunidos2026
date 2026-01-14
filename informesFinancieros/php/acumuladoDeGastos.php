<?php
include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$con = $pdo->conectar();
function obtenerAcumulado() {
    global $con;

    $resultado = array(
        'meses' => array(),
        'cuentas' => array(),
        'sumaTotal' => array(), 
        'totalAcumulado' => 0,         
     );
     if(isset($_GET['mes']) && isset($_GET['mes1'])) {
        $sqlMeses = $con->prepare("SELECT idMes, mes FROM meses WHERE idMes BETWEEN '" . $_GET['mes'] . "' AND '" . $_GET['mes1'] . "' ORDER BY idMes");
    }else if (isset($_GET['mes'])) {
        $sqlMeses = $con->prepare("SELECT idMes, mes FROM meses WHERE idMes = '" . $_GET['mes'] . "' ORDER BY idMes");
    } else {
        // $sqlMeses = $con->prepare("SELECT idMes, mes FROM meses WHERE idMes <= MONTH(CURRENT_DATE()) ORDER BY idMes");
        $sqlMeses = $con->prepare("SELECT idMes, mes FROM meses ORDER BY idMes");    
    }
    $sqlMeses->execute();
    $resultado['meses'] = $sqlMeses->fetchAll(PDO::FETCH_ASSOC);
   
   $sqlCuentas = $con->prepare("SELECT c.idCuentaConcepto, UPPER(c.cuenta) AS cuenta 
                          FROM cuentas c 
                          LEFT JOIN relacioncuentainformes r ON r.idCuentaConcepto = c.idCuentaConcepto
                          WHERE r.idInforme = '1'");
    $sqlCuentas->execute();
    $listaCuentas = $sqlCuentas->fetchAll(PDO::FETCH_ASSOC);
        foreach ($listaCuentas as $dato) {
            $nueva_cuenta = new stdClass();
            $nueva_cuenta->idCuentaConcepto = $dato['idCuentaConcepto'];
            $nueva_cuenta->cuenta = $dato['cuenta'];
            $nueva_cuenta->gastosAup = array();
            $nueva_cuenta->sumaPorMes = array(); 
            
            $nueva_cuenta->acumuladoPorCuenta = 0;                       

            $sqlSubcuentas = $con->prepare("SELECT idSubcuenta, UPPER(subcuenta) as subcuenta FROM subcuentas WHERE idCuentaConcepto = :idCuenta");
            $sqlSubcuentas->bindParam(':idCuenta', $nueva_cuenta->idCuentaConcepto);
            $sqlSubcuentas->execute();
            $listaSubcuentas = $sqlSubcuentas->fetchAll(PDO::FETCH_ASSOC);

            foreach ($listaSubcuentas as $subcuenta) {
                $nueva_subcuenta = new stdClass();
                $nueva_subcuenta->nombre = $subcuenta['subcuenta'];
                $nueva_subcuenta->totales = array ();
                $nueva_subcuenta->total = 0;  
                foreach ($resultado['meses'] as $indice => $mes) {
                $nueva_cuenta->sumaPorMes[$indice] = isset($nueva_cuenta->sumaPorMes[$indice]) ? $nueva_cuenta->sumaPorMes[$indice] : 0;
                $resultado['sumaTotal'][$indice] = isset($resultado['sumaTotal'][$indice]) ? $resultado['sumaTotal'][$indice] : 0;                
                $sqlTotales = $con->prepare("SELECT SUM(total) AS total, mes
                                                FROM(SELECT SUM(cantidad) AS total, SUBSTR(fecha FROM 6 FOR 2 ) AS mes
                                                FROM auxiliardebancos WHERE tipoMovimiento = :idCuentaConcepto AND idSubcuenta = :idSubcuenta
                                                AND SUBSTR(fecha FROM 6 FOR 2) = CONVERT(:idMes, UNSIGNED INTEGER)
                                                GROUP BY idSubcuenta
                                                UNION
                                                SELECT SUM(d.cantidad) AS total, SUBSTR(e.fecha FROM 6 FOR 2 ) AS mes
                                                FROM cajachicadetalle d 
                                                LEFT JOIN cajachica e ON e.idCajaChica = d.idCajaChica
                                                WHERE e.tipo = 1 AND d.idMovimiento = 3 AND d.idConcepto = :idSubcuenta
                                                AND SUBSTR(fecha FROM 6 FOR 2) = CONVERT(:idMes, UNSIGNED INTEGER)
                                                GROUP BY d.idConcepto) AS caja");
                        $sqlTotales->bindParam(':idCuentaConcepto', $nueva_cuenta->idCuentaConcepto);
                        $sqlTotales->bindParam(':idSubcuenta', $subcuenta['idSubcuenta']);
                        $sqlTotales->bindParam(':idMes', $mes['idMes']);                    
                        $sqlTotales->execute(); 
                        $resultado_subcuenta_mes = $sqlTotales->fetch(PDO::FETCH_ASSOC);
                        $nueva_cuenta->sumaPorMes[$indice] += $resultado_subcuenta_mes['total'];
                        $nueva_subcuenta->total += $resultado_subcuenta_mes['total'];
                        $resultado['sumaTotal'][$indice] += $resultado_subcuenta_mes['total'];
                        array_push($nueva_subcuenta->totales, $resultado_subcuenta_mes['total']);           
                }         

                     array_push($nueva_cuenta->gastosAup, $nueva_subcuenta);
        }

        foreach ($nueva_cuenta->sumaPorMes as $suma) {
             $nueva_cuenta->acumuladoPorCuenta += $suma;
          }
          $resultado['totalAcumulado']+= $nueva_cuenta->acumuladoPorCuenta;                  
          array_push($resultado['cuentas'], $nueva_cuenta);       

 }       
    
    return $resultado;

}

    if(!isset($_GET['acumulado'])) {
        $resultado = obtenerAcumulado();
        echo json_encode($resultado);
    }
?>