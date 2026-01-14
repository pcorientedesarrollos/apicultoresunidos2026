<?php

require('../../fpdf/FPDF/fpdf.php');


function getInfo()
{
    $resultado = array();
    try {
        include_once '../../DAOConeccion/conePDO.php';
        include_once '../../controlAdministrativo/php/nombreDePersona.php';
    
        $pdo = new conePDO();
        $con = $pdo->conectar();
        //OBTENEMOS SUBCUENTAS
        $datos = $con->prepare('SELECT * FROM subcuentas WHERE idCuentaConcepto = 12 ');
        $datos->execute();
        $resultado = array();
        if ($datos == false) {
            throw new Exception($con->errorInfo());
        }
    
            if ($datos->rowCount() >= 1) {
                foreach ($datos->fetchAll(PDO::FETCH_ASSOC) as $dato) {
                    $idSubcuenta = $dato['idSubcuenta'];
                    //OBTENEMOS LA SUBSUBCUENTAS DE CADA SUBCUENTA
                    $sqlSubSub = $con->prepare("SELECT ssc.idSubSubcuenta, ssc.subSubcuenta as subcuentaConcepto, ssc.clave,ssc.min, ssc.precioUnitario, s.idSubcuenta 
                                                            FROM subsubcuentas ssc 
                                                            LEFT JOIN subcuentas s ON s.idSubcuenta = ssc.idSubcuenta 
                                                            -- WHERE s.idSubcuenta = :idSubcuenta
                                                            WHERE s.idSubcuenta = :idSubcuenta AND ssc.ocultar = 0
                                                            ORDER BY ssc.clave ASC");
                    $sqlSubSub->bindParam(':idSubcuenta', $idSubcuenta);
                    $sqlSubSub->execute();
    
                    if ($sqlSubSub == false) {
                        throw new Exception($con->errorInfo());
                    }
    
                    $dato['subSubcuentas'] = [];
                    //RECORREMOS TODAS LAS SUBSUBCUENTAS PARA CALCULAR SU EXISTENCIA
                        foreach ($sqlSubSub->fetchAll(PDO::FETCH_ASSOC) as $data) {
                            //VERIFICA QUE PRODUCTOS SON APICOLAS PARA CALCULAR EXISTENCIAS
                            if($data['idSubcuenta'] == 134){
                                $data['existenciaPasada'] = 0;
                                $data['importePasado'] = 0;
                                $data['existencia'] = 0;
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
                    
                                    // Acumular el importe
                                    $registro['acumulado'] = $data['importeAcumulado'] - $registro['importeSalida'];
                                    $data['importeAcumulado'] -= $registro['importeSalida'];
                    
                                }
                                $data['existencia'] = $data['acumulado'];
                            }
                                array_push($dato['subSubcuentas'], $data);
                                //VERIFICA QUE PRODUCTOS SON DERIVADOS PARA CALCULAR EXISTENCIAS
                            }else if($data['idSubcuenta'] == 135){

                                $data['existenciaPasada'] = 0;
                                $data['importePasado'] = 0;
                                $data['existencia'] = 0;
                                // Saldo inicial con el nuevo modulo
                                $sqlSaldoInicial = $con->prepare("SELECT existenciaPasada, importePasado FROM saldoinicial_derivados WHERE nombre = :nombre");
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
            
                                $sqlSelect = "SELECT aa.cantidad, aa.descripcion, aa.importe, aa.costoUnitario, aea.tipo, aea.tipoPersona, aea.idProveedor, aea.fecha FROM derivadosalmacendetalle aa
                                    LEFT JOIN derivadosalmacenencabezado aea ON aa.idProductoDerivado = aea.idEntrada
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
                    
                                    // Acumular el importe
                                    $registro['acumulado'] = $data['importeAcumulado'] - $registro['importeSalida'];
                                    $data['importeAcumulado'] -= $registro['importeSalida'];
                    
                                }
                                $data['existencia'] = $data['acumulado'];
                            }

                            $sqlSelect2 = "SELECT aa.cantidad, aa.descripcion, aa.importe, aa.costoUnitario, aea.tipo, aea.tipoPersona, aea.idProveedor, aea.fecha FROM derivadosalmacendetalle_salidas aa
                            LEFT JOIN derivadosalmacenencabezado_salidas aea ON aa.idProductoDerivado = aea.idSalida
                            WHERE aa.concepto = :subcuentaConcepto";
                    
                            $query2 = $con->prepare($sqlSelect2);
                            $query2->bindParam(':subcuentaConcepto', $data['subcuentaConcepto']);
                            $query2->execute();
                    
                            if ($query2 == false) {
                                throw new Exception($con->errorInfo());
                            }

                            foreach ($query2->fetchAll(PDO::FETCH_ASSOC) as $registro) {
                    
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
                    
                                    // Acumular el importe
                                    $registro['acumulado'] = $data['importeAcumulado'] - $registro['importeSalida'];
                                    $data['importeAcumulado'] -= $registro['importeSalida'];
                    
                                }
                                $data['existencia'] = $data['acumulado'];
                            }


                                array_push($dato['subSubcuentas'], $data);

                            }
                        }
                    array_push($resultado, $dato);
                }
            }
          return $resultado;

    } catch (Exception $e) {
        return ['error' => true, 'message' => $e->getMessage()];
    }
};


function addLineIngreso($y, $_info, $pdf, $GLOBALES)
{
    $pdf->SetFont('Arial', 'B', 10);

    $pdf->setY($y + 5);
    $heigthPerRow = 0;
    $pdf->MultiCell($GLOBALES['sixth'] + 110, $GLOBALES['littleRow'], utf8_decode($_info['subcuenta']), 0, 'C', 0);
    $heigthPerRow = $pdf->getY() - $y > $heigthPerRow ? $pdf->getY() - $y : $heigthPerRow;
    $pdf->setXY($GLOBALES['initialX'] + ($GLOBALES['sixth']), $y);

    // $lineY = $heigthPerRow + $y + .5;
    // $pdf->Line($GLOBALES['initialX'], $lineY, $GLOBALES['pageWidth'] - $GLOBALES['initialX'], $lineY);
    // $heigthPerRow = $pdf->getY() - $y > $heigthPerRow ? $pdf->getY() - $y : $heigthPerRow;
    return $heigthPerRow + 1;
};

function addLineIngreso2($y, $_info, $pdf, $GLOBALES)
{
    $pdf->SetFont('Arial', '', 8);
    $pdf->setY($y + 2);
    $heigthPerRow = 0;
    $pdf->SetFillColor(223, 115, 115);

    $pdf->MultiCell($GLOBALES['sixth'], $GLOBALES['littleRow'], utf8_decode($_info['clave']), 0, 'C', 0);
    $heigthPerRow = $pdf->getY() - $y > $heigthPerRow ? $pdf->getY() - $y : $heigthPerRow;
    $pdf->setXY($GLOBALES['initialX'] + ($GLOBALES['sixth']), $y + 2);
    
    $pdf->MultiCell($GLOBALES['sixth'] + 110, $GLOBALES['littleRow'], utf8_decode($_info['subcuentaConcepto']),0, 'L', 0);
    $heigthPerRow = $pdf->getY() - $y > $heigthPerRow ? $pdf->getY() - $y : $heigthPerRow;
    $pdf->setXY($GLOBALES['initialX'] + ($GLOBALES['sixth']), $y);

    $pdf->MultiCell($GLOBALES['cuarter'] + 80, $GLOBALES['littleRow'] + 4, utf8_decode('$ ' . $_info['precioUnitario']), 0, 'R', 0);
    $heigthPerRow = $pdf->getY() - $y > $heigthPerRow ? $pdf->getY() - $y : $heigthPerRow;
    $pdf->setXY($GLOBALES['initialX'] + $GLOBALES['cuarter'] + $GLOBALES['sixth'] + 83, $y + 1.80);

    if($_info['existencia'] <= $_info['min'])
    {

        $pdf->MultiCell($GLOBALES['cuarter'] - 37, $GLOBALES['littleRow'], utf8_decode($_info['existencia']), 0, 'C', true);
        $heigthPerRow = $pdf->getY() - $y > $heigthPerRow ? $pdf->getY() - $y : $heigthPerRow;
        $pdf->setXY($GLOBALES['initialX'] + $GLOBALES['cuarter'] * 2 + $GLOBALES['sixth'] * 2, $y + 1.80);

    }else{

        $pdf->MultiCell($GLOBALES['cuarter'] - 40, $GLOBALES['littleRow'], utf8_decode($_info['existencia']), 0, 'C', 0);
        $heigthPerRow = $pdf->getY() - $y > $heigthPerRow ? $pdf->getY() - $y : $heigthPerRow;
        $pdf->setXY($GLOBALES['initialX'] + $GLOBALES['cuarter'] * 2 + $GLOBALES['sixth'] * 2, $y + 1.80);
    }

    $pdf->MultiCell($GLOBALES['sixth'] + 5, $GLOBALES['littleRow'], utf8_decode($_info['min']), 0, 'C', 0);
    $heigthPerRow = $pdf->getY() - $y > $heigthPerRow ? $pdf->getY() - $y : $heigthPerRow;
    $pdf->setXY($GLOBALES['initialX'] + ($GLOBALES['sixth']), $y + 2);

    return $heigthPerRow + 1;
};



function outputPdf()
{

    class PDF extends FPDF {
                    
        function Footer() {
            $this->SetY(-10);
            $this->SetFont('Arial', 'I', 8);
            $this->AliasNbPages('nb');
            $this->Cell(0, 10, utf8_decode('' . $this->PageNo() . '/nb'), 0, 0, 'R');
        }
    
    }

    $datos = getInfo();
    $pdf = new PDF('P', 'mm', 'A4');
    $pdf->AddPage();
    $pdf->SetAutoPageBreak(false);


    $pageWidth = 190;
    $pageHeight = $pdf->getPageHeight();

    $GLOBALS = [
        'pageWidth' => $pdf->GetPageWidth(),
        'littleRow' => 3,
        'twelve' => $pageWidth / 12,
        'sixth' => $pageWidth / 6,
        'cuarter' => $pageWidth / 4,
        'middle' => $pageWidth / 2,
        'rowHeight' => 5,
        'initialX' => 10,
        'logoW' => 22

    ];

    $GLOBALSY = [
        'initialY' => 25,
        'nameRowY' => 37,
        'dataRowY' => 45,
        'dateRowY' => 20,
        'signRowY' => $pageHeight / 2 - 40,
        'logoY' => 10,
        'documentTitleY' => 10,
        'totalY' => 45 + $GLOBALS['rowHeight'] * 10
    ];

    $pdf->SetFillColor(137, 172, 118);
    $pdf->SetTextColor(0, 0, 0);
    $pdf->SetFont('Arial', '', 9);

    $pdf->Image('../img/imgMovimientoCajaChica.png', $GLOBALS['initialX'] + 20, $GLOBALSY['logoY'] + 7, $GLOBALS['logoW']);

    $pdf->setXY($GLOBALS['initialX'] + 25, $GLOBALSY['logoY']);
    $pdf->cell(100, 4, utf8_decode(''), 0, 2, 'C', 0);
    $pdf->cell(100, 4, utf8_decode(''), 0, 2, 'C', 0);
    $pdf->setXY($GLOBALS['initialX'], $GLOBALSY['logoY'] + 10);
    $pdf->cell(0, -15, date('d/m/Y'), 0, 2, 'R', 0);
    $pdf->SetFont('Arial', 'B', 10);
    $pdf->cell(190,30, utf8_decode('Precio de Productos Apicolas, Miel y Derivados'), 0, 1, 'C', 0);
    $pdf->SetFont('Arial', '', 9);
    $pdf->SetFillColor(223, 115, 115);
    $pdf->cell(190,-7, date('Y'), 0, 1, 'C', true);

    $pdf->SetTextColor(255, 255, 255);
    $pdf->SetFont('Arial', 'B', 9);

    $pdf->Image('../img/imgMovimientoCajaChica1.png', $GLOBALS['initialX'] * 15.5, $GLOBALSY['logoY'] * .70, $GLOBALS['logoW']);



    $pdf->setFont('Arial', '', 8);
    $pdf->SetTextColor(0, 0, 0);

    $x = $GLOBALS['initialX'];
    $y = $GLOBALSY['nameRowY'];
    $pdf->setXY($x, $y);

    $pdf->cell(25, $GLOBALS['rowHeight'], utf8_decode(''), 0, 0, '', 0);
    $pdf->setFont('Arial', 'B', 8);
    $pdf->cell(100, $GLOBALS['rowHeight'], utf8_decode(strtoupper('')), 0, 0, 'L', 0);

    $pdf->SetFillColor(255, 229, 88);
    $pdf->SetTextColor(0, 0, 0);
    $pdf->SetFont('Arial', '', 9);

    $x = $GLOBALS['initialX'];
    $y = $GLOBALSY['dataRowY'];
    $pdf->setXY($x, $y);

    $pdf->cell($GLOBALS['sixth'] -10, $GLOBALS['rowHeight'], utf8_decode('CLAVE'), 0, 0, 'C', 0);
    $pdf->cell($GLOBALS['sixth'] + 95, $GLOBALS['rowHeight'], utf8_decode('PRODUCTO'), 0, 0, 'C', 0);
    $pdf->cell($GLOBALS['cuarter'] -40 , $GLOBALS['rowHeight'], utf8_decode('PRECIO'), 0, 0, 'C', 0);
    $pdf->cell($GLOBALS['cuarter'] + $GLOBALS['sixth'] -60, $GLOBALS['rowHeight'], utf8_decode('EXIST'), 0, 0, 'C', 0);
    $pdf->cell($GLOBALS['cuarter'] + $GLOBALS['sixth'] -75, $GLOBALS['rowHeight'], utf8_decode('MIN'), 0, 0, 'C', 0);

    $x = $GLOBALS['initialX'];
    $y = $GLOBALSY['dataRowY'] + $GLOBALS['rowHeight'] + 1;
    $pdf->SetFont('Arial', '', 10);
    $pdf->setXY($x, $y);

    foreach ($datos as $clasificacion) {
        if($clasificacion['idSubcuenta'] == 134 || $clasificacion['idSubcuenta'] == 135){
            $_info = array(
                'subcuenta' => $clasificacion['subcuenta']
         );
    
            $size = addLineIngreso($y, $_info, $pdf, $GLOBALS);
            $y += $size;
    
            foreach ($clasificacion['subSubcuentas'] as $productos) {
                
                    $_info = array(
                        'min' => $productos['min'] | 0,
                        'clave' => $productos['clave'],
                        'subcuentaConcepto' => $productos['subcuentaConcepto'],
                        'precioUnitario' => $productos['precioUnitario'] | 0,
                        'existencia' => $productos['existencia']
                 );
            
                    $size = addLineIngreso2($y, $_info, $pdf, $GLOBALS);
                    $y += $size;
        
                    if ($y >= 250) {
                        $pdf->AddPage();
                        $y = 15;
                    }
                }
        }
    
    };




    $pdf->Output('', 'Productos.pdf');
}

    outputPdf();

 