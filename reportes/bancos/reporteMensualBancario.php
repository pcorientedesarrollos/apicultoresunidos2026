<?php
include_once '../../controlAdministrativo/php/nombreDePersona.php';
function addLine($y, $inv, $pdf)
{
    $heigthPerRow = 0;
    $pdf->setXY(5, $y);  

    $pdf->MultiCell(15, 3, utf8_decode($inv['fecha']), 1, 'L', 0);
    $heigthPerRow = $pdf->getY() - $y > $heigthPerRow ? $pdf->getY() - $y : $heigthPerRow;
    $pdf->setXY(20, $y);
    $pdf->MultiCell(50, 3, strtoupper(utf8_decode($inv['concepto'])), 1, 'L', 0);
    $heigthPerRow = $pdf->getY() - $y > $heigthPerRow ? $pdf->getY() - $y : $heigthPerRow;
    $pdf->setXY(70, $y);  
    $pdf->MultiCell(100, 3, strtoupper(utf8_decode($inv['descripcion'])), 1, 'L', 0);
    $heigthPerRow = $pdf->getY() - $y > $heigthPerRow ? $pdf->getY() - $y : $heigthPerRow;
    $pdf->setXY(170, $y);   
    $pdf->MultiCell(70, 3, strtoupper(utf8_decode($inv['miNombre'])), 1, 'L', 0);
    $heigthPerRow = $pdf->getY() - $y > $heigthPerRow ? $pdf->getY() - $y : $heigthPerRow;
    $pdf->setXY(240, $y);
    if(is_numeric ($inv['ingreso'])){
        $pdf->MultiCell(17, 3, "$" . number_format($inv['ingreso'], 2, '.', ','),1, 'R', 0);       
    }else{
    $pdf->MultiCell(17, 3, '', 1, 'R', 0);    
    }
    $heigthPerRow = $pdf->getY() - $y > $heigthPerRow ? $pdf->getY() - $y : $heigthPerRow;
    $pdf->setXY(257, $y);
    if(is_numeric ($inv['egreso'])){
         $pdf->MultiCell(17, 3, "$" . number_format($inv['egreso'], 2, '.', ','), 1, 'R', 0);       
    }else{
    $pdf->MultiCell(17, 3, '', 1, 'R', 0);    
    }
    $heigthPerRow = $pdf->getY() - $y > $heigthPerRow ? $pdf->getY() - $y : $heigthPerRow;
    $pdf->setXY(274, $y);
    $pdf->MultiCell(17, 3, "$" . number_format($inv['saldo'], 2, '.', ','), 1, 'R', 0);        
    $heigthPerRow = $pdf->getY() - $y > $heigthPerRow ? $pdf->getY() - $y : $heigthPerRow;
    return $heigthPerRow ;
};

function reporteMensualBancario($resultado) {
    class PDF extends FPDF {
        function Footer() {
            $this->SetY(-10);
            $this->SetFont('Arial', 'I', 8);
            $this->AliasNbPages('nb');
            $this->Cell(0, 10, utf8_decode('Página ' . $this->PageNo() . '/nb'), 0, 0, 'C');
        }
    }

    #Encabezado

    $pdf = new PDF('L', 'mm', 'A4');
    $pdf->SetFont('Arial', 'B', 12);
    $pdf->AddPage();
    $pdf->Image('../../images/LOGO.png', 10, 7, 20, 20);
    $pdf->setXY(100, 10);
    $pdf->cell(100, 5, 'Oaxaca Miel S.A. de C.V.', 0, 1, 'C', 0);
    $pdf->SetFont('Arial', '', 10);
    $pdf->setXY(100, 15);
    $pdf->cell(100, 10, 'Estado de cuenta del mes de ' . $resultado['mes'], 0, 1, 'C', 0);
    $pdf->SetFont('Arial', 'B', 10);
    $pdf->setXY(100, 22);
    $pdf->cell(100, 5, utf8_decode($resultado['banco'] . ' ' . $resultado['numDeCuenta']), 0, 1, 'C', 0);
    #Inicio de la tabla

    $x = 5;
    $y = 30;

    $pdf->setXY($x, $y);
    $pdf->SetFillColor(56, 84, 40);
    $pdf->SetTextColor(255, 255, 255);
    $pdf->SetFont('Arial', 'B', 8);
    $pdf->cell(15, 5, utf8_decode('FECHA'), 1, 0, 'C', 1);
    $pdf->cell(50, 5, utf8_decode('CONCEPTO'), 1, 0, 'C', 1);
    $pdf->cell(100, 5, utf8_decode('DESCRIPCION'), 1, 0, 'C', 1);
    $pdf->cell(70, 5, utf8_decode('A NOMBRE DE'), 1, 0, 'C', 1);
    $pdf->cell(17, 5, utf8_decode('INGRESO'), 1, 0, 'C', 1);
    $pdf->cell(17, 5, utf8_decode('EGRESO'), 1, 0, 'C', 1);
    $pdf->cell(17, 5, utf8_decode('SALDO'), 1, 0, 'C', 1);
    $y = $y + 5; 
    $pdf->setXY($x, $y);
    $pdf->SetTextColor(0, 0, 0);
    $pdf->SetFont('Arial', '', 6);

    foreach ($resultado['auxiliarDeBancos'] as $inv) {

                $_info = array(
                'fecha' => $inv['fecha'],
                'hora' => $inv['hora'],
                'concepto' => $inv['concepto'],
                'descripcion' => $inv['descripcion'],
                'miNombre' => $inv['miNombre'],
                'ingreso' => $inv['ingreso'],
                'egreso' => $inv['egreso'],
                'saldo' => $inv['saldo'] 
            );
            $size = addLine($y, $_info, $pdf);
            $y += $size;
            if ($y >= 180) {
                $pdf->AddPage();
                $y = 20;
            }
    }
    $lastYValue = $pdf->getY();
    $pdf->setXY(151 , $lastYValue + 10);
    $pdf->SetFillColor(230, 193, 11);
    $pdf->SetFont('Arial', '', 8);
    $pdf->SetTextColor(1, 1, 1);
    $pdf->Cell(35, 5, utf8_decode("Inicial"), 1, 0, 'C', 1);    
    $pdf->Cell(35, 5, utf8_decode("Ingresos"), 1, 0, 'C', 1);
    $pdf->Cell(35, 5, utf8_decode("Egresos"), 1, 0, 'C', 1);
    $pdf->Cell(35, 5, utf8_decode("Saldo"), 1, 0, 'C', 1);    
    
    $pdf->setXY(151, $lastYValue + 15);
    $pdf->SetFont('Arial', '', 7);
    $pdf->SetFillColor(255, 255, 255);
    $pdf->SetTextColor(0, 0, 0);
    $pdf->MultiCell(35, 5, "$" . number_format($resultado['saldoInicial'], 2, '.', ','), 1, 'R');
    $pdf->setXY(186, $lastYValue + 15 );
    $pdf->MultiCell(35, 5, "$" . number_format($resultado['saldoIngresos'], 2, '.', ','), 1, 'R');
    $pdf->setXY(221, $lastYValue + 15 );
    $pdf->MultiCell(35, 5, "$" . number_format($resultado['saldoEgresos'], 2, '.', ','), 1, 'R');
    $pdf->setXY(256, $lastYValue + 15 );
    $pdf->MultiCell(35, 5, "$" . number_format($resultado['saldoActual'], 2, '.', ','), 1, 'R');
  
    $pdf->Output('I', 'Estado de cuenta mensual .pdf');
}

if (isset($_GET['idCuenta']) && isset($_GET['idMes'])) {
    require('../../fpdf/FPDF/fpdf.php');
    include_once '../../DAOConeccion/conePDO.php';
    $pdo = new conePDO();
    $con = $pdo->conectar();

    $idCuenta = $_GET['idCuenta'];
    $idMes = $_GET['idMes'];
    
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
                if($i['tipoDePersona'] != 0){
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
    
    reporteMensualBancario($resultado);
} else {
    exit();
}
?>
