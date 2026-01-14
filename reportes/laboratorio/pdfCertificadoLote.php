<?php

require('../../fpdf/FPDF/fpdf.php');
date_default_timezone_set('America/Merida');
include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$con = $pdo->conectar();
class PDF extends FPDF
{
    var $startPage = 15;
    var $footerPoint = 15;

    function Header()
    {
        $this->Image('../img/LOGO.png', 15, 15, 25);
        $this->setXY(0, 15);
        $this->setFont('Arial', 'B', 11);
        $this->cell($this->getPageWidth(), 4, 'OAXACA MIEL S.A. DE C.V.', 0, 1, 'C', 0);
        $this->setFont('Arial', '', 9);
        $this->setX(0);
        $this->cell($this->getPageWidth(), 4, ' ', 0, 1, 'C', 0);
        $this->setX(0);
        $this->cell($this->getPageWidth(), 4, utf8_decode(''), 0, 1, 'C', 0);
        $this->setX(0);
        $this->cell($this->getPageWidth(), 4, utf8_decode(''), 0, 1, 'C', 0);
        $this->setX(0);
        $this->cell($this->getPageWidth(), 4, 'Tel: (999) 9.88.09.90', 0, 1, 'C', 0);
        $this->setX(0);
        $this->Ln(10);
    }
}

function obtenerCertificado($miel, $lote)
{
    global $con;

    switch ($miel) {
        case '1':
            $encabezado = 'certificadoloteterminado_encabezado';
            $detalle = 'certificadoloteterminado_detalle';
            $tipoDeMiel = '1';
            $codigo = 'CLB-LT-01';
            $revision = '5';
            break;
        case '2':
            $encabezado = 'certificadoloteterminado_encabezado_organico';
            $detalle = 'certificadoloteterminado_detalle_organico';
            $tipoDeMiel = '2';
            $codigo = 'CLB-LT-02';
            $revision = '2';
            break;
        case '5':
            $encabezado = 'certificadoloteterminado_encabezado_mantequilla';
            $detalle = 'certificadoloteterminado_detalle_mantequilla';
            $tipoDeMiel = '5';
            $codigo = 'CLB-LT-05';
            $revision = '2';
            break;
        case '6':
            $encabezado = 'certificadoloteterminado_encabezado_altiplano';
            $detalle = 'certificadoloteterminado_detalle_altiplano';
            $tipoDeMiel = '6';
            $codigo = 'CLB-LT-06';
            $revision = '2';
            break;
        case '7':
            $encabezado = 'certificadoloteterminado_encabezado_naranjo';
            $detalle = 'certificadoloteterminado_detalle_naranjo';
            $tipoDeMiel = '7';
            $codigo = 'CLB-LT-07';
            $revision = '2';
            break;
        case '8':
            $encabezado = 'certificadoloteterminado_encabezado_aguacate';
            $detalle = 'certificadoloteterminado_detalle_aguacate';
            $tipoDeMiel = '8';
            $codigo = 'CLB-LT-08';
            $revision = '2';
            break;
        case '9':
            $encabezado = 'certificadoloteterminado_encabezado_mezquite';
            $detalle = 'certificadoloteterminado_detalle_mezquite';
            $tipoDeMiel = '9';
            $codigo = 'CLB-LT-09';
            $revision = '2';
            break;
    }

    $sqlEncabezado = "SELECT c.*, $tipoDeMiel AS tipoDeMiel, '$codigo' AS codigo, $revision AS revision,
    CASE WHEN cl.marcaFinalCliente = '' THEN '----' ELSE cl.marcaFinalCliente END AS marcacionFinal
    FROM $encabezado c LEFT JOIN calidad cl ON cl.idLoteInterno = c.idLote
    WHERE c.idLote= :idLote";
    $queryEncabezado = $con->prepare($sqlEncabezado);
    $queryEncabezado->bindParam(':idLote', $lote);
    $queryEncabezado->execute();
    if (!$queryEncabezado) {
        throw new Exception($con->errorInfo());
    }
    $resultado = $queryEncabezado->fetch(PDO::FETCH_ASSOC);
    $idEncabezado = $resultado['idEncabezado'];

    $sql = "SELECT * FROM $detalle WHERE idEncabezado = :idEncabezado AND idAnalisis BETWEEN 1 AND 6 OR idAnalisis = 16";
    $sqlDetalle = $con->prepare($sql);
    $sqlDetalle->bindParam(':idEncabezado', $idEncabezado);
    $sqlDetalle->execute();
    if ($sqlDetalle == FALSE) {
        throw new Exception($con->errorInfo());
    } else {
        $resultado['caracteristicas'] = $sqlDetalle->fetchAll(PDO::FETCH_ASSOC);
        foreach ($resultado['caracteristicas'] as $caracteristicas) {
            if ($caracteristicas['idAnalisis'] == '1') {
                if ($miel == '1') {
                    $caracteristicas['parametro'] = 'Miel 100% pura de abeja';
                    $caracteristicas['resultado'] = 'Miel 100% pura de abeja';
                } else if ($miel == '5') {
                    $caracteristicas['parametro'] = 'Miel 100% Mantequilla';
                    $caracteristicas['resultado'] = 'Miel 100% Mantequilla';
                }  else if ($miel == '6') {
                    $caracteristicas['parametro'] = 'Miel 100% Altiplano';
                    $caracteristicas['resultado'] = 'Miel 100% Altiplano';
                }  else if ($miel == '7') {
                    $caracteristicas['parametro'] = 'Miel 100% Naranjo';
                    $caracteristicas['resultado'] = 'Miel 100% Naranjo';
                }  else if ($miel == '8') {
                    $caracteristicas['parametro'] = 'Miel 100% Aguacate';
                    $caracteristicas['resultado'] = 'Miel 100% Aguacate';
                }  else if ($miel == '9') {
                    $caracteristicas['parametro'] = 'Miel 100% Mezquite';
                    $caracteristicas['resultado'] = 'Miel 100% Mezquite';
                } else {
                    $caracteristicas['parametro'] = 'Miel 100% organica';
                    $caracteristicas['resultado'] = 'Miel 100% organica';
                }
                $resultado['caracteristicas']['tipoDeMiel'] = $caracteristicas;
            }
            if ($caracteristicas['idAnalisis'] == '2') {
                $resultado['caracteristicas']['humedad'] = $caracteristicas;
            }
            if ($caracteristicas['idAnalisis'] == '3') {
                $resultado['caracteristicas']['color'] = $caracteristicas;
            }
            if ($caracteristicas['idAnalisis'] == '4') {
                $resultado['caracteristicas']['hmf'] = $caracteristicas;
            }
            if ($caracteristicas['idAnalisis'] == '5') {
                $resultado['caracteristicas']['adulteracion'] = $caracteristicas;
            }
            if ($caracteristicas['idAnalisis'] == '6') {
                $resultado['caracteristicas']['fg'] = $caracteristicas;
            }
            if ($caracteristicas['idAnalisis'] == '16') {
                $resultado['caracteristicas']['brix'] = $caracteristicas;
            }
        }
    }

    $sqlAntibioticos = "SELECT * FROM $detalle WHERE idEncabezado = :idEncabezado AND idAnalisis BETWEEN 7 AND 9";
    $sqlDetalleAn = $con->prepare($sqlAntibioticos);
    $sqlDetalleAn->bindParam(':idEncabezado', $idEncabezado);
    $sqlDetalleAn->execute();

    if ($sqlDetalleAn == FALSE) {
        throw new Exception($con->errorInfo());
    } else {
        $resultado['antibioticos'] = $sqlDetalleAn->fetchAll(PDO::FETCH_ASSOC);
        foreach ($resultado['antibioticos'] as $antibioticos) {
            if ($antibioticos['idAnalisis'] == '8') {
                $resultado['antibioticos']['sulfametazinas'] = $antibioticos;
            }
            if ($antibioticos['idAnalisis'] == '7') {
                $resultado['antibioticos']['estreptomicinas'] = $antibioticos;
            }
            if ($antibioticos['idAnalisis'] == '9') {
                $resultado['antibioticos']['tetraciclinas'] = $antibioticos;
            }
        }
    }

    $sqlMicrobiologicos = "SELECT * FROM $detalle WHERE idEncabezado = :idEncabezado AND idAnalisis BETWEEN 10 AND 15";
    $sqlDetalleMicro = $con->prepare($sqlMicrobiologicos);
    $sqlDetalleMicro->bindParam(':idEncabezado', $idEncabezado);
    $sqlDetalleMicro->execute();

    if ($sqlDetalleMicro == FALSE) {
        throw new Exception($con->errorInfo());
    } else {
        $resultado['microbiologicos'] = $sqlDetalleMicro->fetchAll(PDO::FETCH_ASSOC);
        foreach ($resultado['microbiologicos'] as $microbiologicos) {
            if ($microbiologicos['idAnalisis'] == '10') {
                $resultado['microbiologicos']['aerobios'] = $microbiologicos;
            }
            if ($microbiologicos['idAnalisis'] == '11') {
                $resultado['microbiologicos']['coliformes'] = $microbiologicos;
            }
            if ($microbiologicos['idAnalisis'] == '12') {
                $resultado['microbiologicos']['salmonella'] = $microbiologicos;
            }
            if ($microbiologicos['idAnalisis'] == '13') {
                $resultado['microbiologicos']['hongos'] = $microbiologicos;
            }
            if ($microbiologicos['idAnalisis'] == '14') {
                $resultado['microbiologicos']['levaduras'] = $microbiologicos;
            }
            if ($microbiologicos['idAnalisis'] == '15') {
                $resultado['microbiologicos']['listeria'] = $microbiologicos;
            }
        }
    }

    return $resultado;
}

function cambiar_fondo($pdf, $t = 0)
{
    switch ($t) {
        case 1:
            $pdf->SetFillColor(255, 229, 88);
            $pdf->SetTextColor(0, 0, 0);
            break;
        default:
            $pdf->SetFillColor(255, 255, 255);
            $pdf->SetTextColor(0, 0, 0);
            break;
    }
}

function outputPdf($miel, $lote)
{
    global $con;
    $pdf = new PDF('P', 'mm', 'A4');
    $pdf->SetTitle('CERTIFICADO DE LOTE TERMINADO');
    $pdf->AliasNbPages();
    $pdf->AddPage();
    $certificado = obtenerCertificado($miel, $lote);
    $medidas = array(
        'alto_fila' => 5,
        'alto_fila_sm' => 4,
        'ancho_disponible' => $pdf->getPageWidth() - $pdf->startPage * 2
    );

    $pdf->setXY($pdf->startPage, $pdf->getY());
    $pdf->setFont('Arial', 'B', 10);
    $pdf->cell(0, 5, utf8_decode(strtoupper('CERTIFICADO DE LABORATORIO POR LOTE TERMINADO')), 0, 2, 'C', 0);

    $pdf->setXY($pdf->startPage - 1, $pdf->getY() + 2);
    $pdf->setFont('Arial', '', 10);
    $pdf->cell($medidas['ancho_disponible'] / 3, $medidas['alto_fila'], utf8_decode('CODIGO: '. $certificado['codigo']), 0, 0, 'C', 0);
    $pdf->cell($medidas['ancho_disponible'] / 3, $medidas['alto_fila'], utf8_decode('FECHA EMITIDA: SEP-15'), 0, 0, 'C', 0);
    $pdf->cell($medidas['ancho_disponible'] / 3, $medidas['alto_fila'], utf8_decode('Nº REVISION: '. $certificado['revision']), 0, 0, 'C', 0);

    // INSTRUCCIONES
    // $pdf->setXY($pdf->startPage - 1, $pdf->getY() + 8);
    // $pdf->setFont('Arial', '', 9);
    // $pdf->MultiCell($medidas['ancho_disponible'], $medidas['alto_fila'], utf8_decode('LLENAR CON LETRA DE MOLDE Y CLARA TODOS LOS ESPACIO, NO DEJE ESPACIOS VACÍOS, TRACE UNA DIAGONAL, USE TINTA AZUL O NEGRA NO USE TINTA ROJA.'), 0, 'L', 0);

    $pdf->setXY($pdf->startPage - 1, $pdf->getY() + 5);
    $pdf->setFont('Arial', '', 10);
    $pdf->cell(0, 5, utf8_decode(strtoupper('FECHA: ' . date('d/m/Y', strtotime($certificado['fecha'])))), 0, 2, 'L', 0);
    $pdf->setXY($pdf->startPage, $pdf->getY());
    $pdf->setXY($pdf->startPage - 1, $pdf->getY());
    $pdf->setFont('Arial', '', 10);
    $pdf->cell(0, 5, utf8_decode('N° DE LOTE: ' . $certificado['idLote']), 0, 2, 'L', 0);
    $pdf->setXY($pdf->startPage, $pdf->getY());
    $pdf->setXY($pdf->startPage - 1, $pdf->getY());
    $pdf->setFont('Arial', '', 10);
    $pdf->cell(0, 5, utf8_decode('MARCACION FINAL: ' . $certificado['marcacionFinal']), 0, 2, 'L', 0);
    $pdf->setXY($pdf->startPage, $pdf->getY());

    // Construir la tabla
    // CARACTERISTICAS
    $pdf->setFont('Arial', '', 9);
    $pdf->setXY($pdf->startPage, $pdf->getY() + 4);
    cambiar_fondo($pdf, 1);
    $pdf->cell($medidas['ancho_disponible'], $medidas['alto_fila'], utf8_decode('CARACTERISTICAS'), 1, 2, 'C', 1);
    $pdf->cell($medidas['ancho_disponible'] / 5, $medidas['alto_fila'], utf8_decode('ANALISIS'), 1, 0, 'C', 1);
    $pdf->cell($medidas['ancho_disponible'] / 5, $medidas['alto_fila'], utf8_decode('PARAMETROS'), 1, 0, 'C', 1);
    $pdf->cell($medidas['ancho_disponible'] / 5, $medidas['alto_fila'], utf8_decode('RESULTADO'), 1, 0, 'C', 1);
    $pdf->cell($medidas['ancho_disponible'] / 5, $medidas['alto_fila'], utf8_decode('DESVIACION'), 1, 0, 'C', 1);
    $pdf->cell($medidas['ancho_disponible'] / 5, $medidas['alto_fila'], utf8_decode('COMENTARIO'), 1, 1, 'C', 1);
    $pdf->setX($pdf->startPage);
    cambiar_fondo($pdf, 0);
    $pdf->cell($medidas['ancho_disponible'] / 5, $medidas['alto_fila'], utf8_decode('TIPO DE MIEL'), 1, 0, 'L', 1);
    $pdf->cell($medidas['ancho_disponible'] / 5, $medidas['alto_fila'], utf8_decode($certificado['caracteristicas']['tipoDeMiel']['parametro']), 1, 0, 'L', 1);
    $pdf->cell($medidas['ancho_disponible'] / 5, $medidas['alto_fila'], utf8_decode($certificado['caracteristicas']['tipoDeMiel']['resultado']), 1, 0, 'L', 1);
    $pdf->cell($medidas['ancho_disponible'] / 5, $medidas['alto_fila'], utf8_decode($certificado['caracteristicas']['tipoDeMiel']['desviacion']), 1, 0, 'L', 1);
    $pdf->cell($medidas['ancho_disponible'] / 5, $medidas['alto_fila'], utf8_decode($certificado['caracteristicas']['tipoDeMiel']['comentario']), 1, 1, 'L', 1);
    $pdf->setX($pdf->startPage);
    $pdf->cell($medidas['ancho_disponible'] / 5, $medidas['alto_fila'], utf8_decode('HUMEDAD'), 1, 0, 'L', 1);
    $pdf->cell($medidas['ancho_disponible'] / 5, $medidas['alto_fila'], utf8_decode($certificado['caracteristicas']['humedad']['parametro']), 1, 0, 'L', 1);
    $pdf->cell($medidas['ancho_disponible'] / 5, $medidas['alto_fila'], utf8_decode($certificado['caracteristicas']['humedad']['resultado']), 1, 0, 'L', 1);
    $pdf->cell($medidas['ancho_disponible'] / 5, $medidas['alto_fila'], utf8_decode($certificado['caracteristicas']['humedad']['desviacion']), 1, 0, 'L', 1);
    $pdf->cell($medidas['ancho_disponible'] / 5, $medidas['alto_fila'], utf8_decode($certificado['caracteristicas']['humedad']['comentario']), 1, 1, 'L', 1);
    $pdf->setX($pdf->startPage);
    $pdf->cell($medidas['ancho_disponible'] / 5, $medidas['alto_fila'], utf8_decode('COLOR'), 1, 0, 'L', 1);
    $pdf->cell($medidas['ancho_disponible'] / 5, $medidas['alto_fila'], utf8_decode($certificado['caracteristicas']['color']['parametro']), 1, 0, 'L', 1);
    $pdf->cell($medidas['ancho_disponible'] / 5, $medidas['alto_fila'], utf8_decode($certificado['caracteristicas']['color']['resultado']), 1, 0, 'L', 1);
    $pdf->cell($medidas['ancho_disponible'] / 5, $medidas['alto_fila'], utf8_decode($certificado['caracteristicas']['color']['desviacion']), 1, 0, 'L', 1);
    $pdf->cell($medidas['ancho_disponible'] / 5, $medidas['alto_fila'], utf8_decode($certificado['caracteristicas']['color']['comentario']), 1, 1, 'L', 1);
    $pdf->setX($pdf->startPage);
    $pdf->cell($medidas['ancho_disponible'] / 5, $medidas['alto_fila'], utf8_decode('HMF'), 1, 0, 'L', 1);
    $pdf->cell($medidas['ancho_disponible'] / 5, $medidas['alto_fila'], utf8_decode($certificado['caracteristicas']['hmf']['parametro']), 1, 0, 'L', 1);
    $pdf->cell($medidas['ancho_disponible'] / 5, $medidas['alto_fila'], utf8_decode($certificado['caracteristicas']['hmf']['resultado']), 1, 0, 'L', 1);
    $pdf->cell($medidas['ancho_disponible'] / 5, $medidas['alto_fila'], utf8_decode($certificado['caracteristicas']['hmf']['desviacion']), 1, 0, 'L', 1);
    $pdf->cell($medidas['ancho_disponible'] / 5, $medidas['alto_fila'], utf8_decode($certificado['caracteristicas']['hmf']['comentario']), 1, 1, 'L', 1);
    $pdf->setX($pdf->startPage);
    $pdf->cell($medidas['ancho_disponible'] / 5, $medidas['alto_fila'], utf8_decode('ADULTERACION'), 1, 0, 'L', 1);
    $pdf->cell($medidas['ancho_disponible'] / 5, $medidas['alto_fila'], utf8_decode($certificado['caracteristicas']['adulteracion']['parametro']), 1, 0, 'L', 1);
    $pdf->cell($medidas['ancho_disponible'] / 5, $medidas['alto_fila'], utf8_decode($certificado['caracteristicas']['adulteracion']['resultado']), 1, 0, 'L', 1);
    $pdf->cell($medidas['ancho_disponible'] / 5, $medidas['alto_fila'], utf8_decode($certificado['caracteristicas']['adulteracion']['desviacion']), 1, 0, 'L', 1);
    $pdf->cell($medidas['ancho_disponible'] / 5, $medidas['alto_fila'], utf8_decode($certificado['caracteristicas']['adulteracion']['comentario']), 1, 1, 'L', 1);
    $pdf->setX($pdf->startPage);
    $pdf->cell($medidas['ancho_disponible'] / 5, $medidas['alto_fila'], utf8_decode('F/G'), 1, 0, 'L', 1);
    $pdf->cell($medidas['ancho_disponible'] / 5, $medidas['alto_fila'], utf8_decode($certificado['caracteristicas']['fg']['parametro']), 1, 0, 'L', 1);
    $pdf->cell($medidas['ancho_disponible'] / 5, $medidas['alto_fila'], utf8_decode($certificado['caracteristicas']['fg']['resultado']), 1, 0, 'L', 1);
    $pdf->cell($medidas['ancho_disponible'] / 5, $medidas['alto_fila'], utf8_decode($certificado['caracteristicas']['fg']['desviacion']), 1, 0, 'L', 1);
    $pdf->cell($medidas['ancho_disponible'] / 5, $medidas['alto_fila'], utf8_decode($certificado['caracteristicas']['fg']['comentario']), 1, 1, 'L', 1);
    $pdf->setX($pdf->startPage);
    $pdf->cell($medidas['ancho_disponible'] / 5, $medidas['alto_fila'], utf8_decode('°BRIX'), 1, 0, 'L', 1);
    $pdf->cell($medidas['ancho_disponible'] / 5, $medidas['alto_fila'], utf8_decode($certificado['caracteristicas']['brix']['parametro']), 1, 0, 'L', 1);
    $pdf->cell($medidas['ancho_disponible'] / 5, $medidas['alto_fila'], utf8_decode($certificado['caracteristicas']['brix']['resultado']), 1, 0, 'L', 1);
    $pdf->cell($medidas['ancho_disponible'] / 5, $medidas['alto_fila'], utf8_decode($certificado['caracteristicas']['brix']['desviacion']), 1, 0, 'L', 1);
    $pdf->cell($medidas['ancho_disponible'] / 5, $medidas['alto_fila'], utf8_decode($certificado['caracteristicas']['brix']['comentario']), 1, 1, 'L', 1);

    // ANTIBIOTICOS
    $pdf->setFont('Arial', '', 9);
    $pdf->setXY($pdf->startPage, $pdf->getY() + 5);
    cambiar_fondo($pdf, 1);
    $pdf->cell($medidas['ancho_disponible'], $medidas['alto_fila'], utf8_decode('ANTIBIOTICOS'), 1, 2, 'C', 1);
    $pdf->cell($medidas['ancho_disponible'] / 5, $medidas['alto_fila'], utf8_decode('ANALISIS'), 1, 0, 'C', 1);
    $pdf->cell($medidas['ancho_disponible'] / 5, $medidas['alto_fila'], utf8_decode('PARAMETROS'), 1, 0, 'C', 1);
    $pdf->cell($medidas['ancho_disponible'] / 5, $medidas['alto_fila'], utf8_decode('RESULTADO'), 1, 0, 'C', 1);
    $pdf->cell($medidas['ancho_disponible'] / 5, $medidas['alto_fila'], utf8_decode('DESVIACION'), 1, 0, 'C', 1);
    $pdf->cell($medidas['ancho_disponible'] / 5, $medidas['alto_fila'], utf8_decode('COMENTARIO'), 1, 1, 'C', 1);
    $pdf->setX($pdf->startPage);
    cambiar_fondo($pdf, 0);
    $pdf->cell($medidas['ancho_disponible'] / 5, $medidas['alto_fila'], utf8_decode('SULFAMETAZINAS'), 1, 0, 'L', 1);
    $pdf->cell($medidas['ancho_disponible'] / 5, $medidas['alto_fila'], utf8_decode($certificado['antibioticos']['sulfametazinas']['parametro']), 1, 0, 'L', 1);
    $pdf->cell($medidas['ancho_disponible'] / 5, $medidas['alto_fila'], utf8_decode($certificado['antibioticos']['sulfametazinas']['resultado']), 1, 0, 'L', 1);
    $pdf->cell($medidas['ancho_disponible'] / 5, $medidas['alto_fila'], utf8_decode($certificado['antibioticos']['sulfametazinas']['desviacion']), 1, 0, 'L', 1);
    $pdf->cell($medidas['ancho_disponible'] / 5, $medidas['alto_fila'], utf8_decode($certificado['antibioticos']['sulfametazinas']['comentario']), 1, 1, 'L', 1);
    $pdf->setX($pdf->startPage);
    $pdf->cell($medidas['ancho_disponible'] / 5, $medidas['alto_fila'], utf8_decode('ESTREPTOMICINAS'), 1, 0, 'L', 1);
    $pdf->cell($medidas['ancho_disponible'] / 5, $medidas['alto_fila'], utf8_decode($certificado['antibioticos']['estreptomicinas']['parametro']), 1, 0, 'L', 1);
    $pdf->cell($medidas['ancho_disponible'] / 5, $medidas['alto_fila'], utf8_decode($certificado['antibioticos']['estreptomicinas']['resultado']), 1, 0, 'L', 1);
    $pdf->cell($medidas['ancho_disponible'] / 5, $medidas['alto_fila'], utf8_decode($certificado['antibioticos']['estreptomicinas']['desviacion']), 1, 0, 'L', 1);
    $pdf->cell($medidas['ancho_disponible'] / 5, $medidas['alto_fila'], utf8_decode($certificado['antibioticos']['estreptomicinas']['comentario']), 1, 1, 'L', 1);
    $pdf->setX($pdf->startPage);
    $pdf->cell($medidas['ancho_disponible'] / 5, $medidas['alto_fila'], utf8_decode('TETRACICLINAS'), 1, 0, 'L', 1);
    $pdf->cell($medidas['ancho_disponible'] / 5, $medidas['alto_fila'], utf8_decode($certificado['antibioticos']['tetraciclinas']['parametro']), 1, 0, 'L', 1);
    $pdf->cell($medidas['ancho_disponible'] / 5, $medidas['alto_fila'], utf8_decode($certificado['antibioticos']['tetraciclinas']['resultado']), 1, 0, 'L', 1);
    $pdf->cell($medidas['ancho_disponible'] / 5, $medidas['alto_fila'], utf8_decode($certificado['antibioticos']['tetraciclinas']['desviacion']), 1, 0, 'L', 1);
    $pdf->cell($medidas['ancho_disponible'] / 5, $medidas['alto_fila'], utf8_decode($certificado['antibioticos']['tetraciclinas']['comentario']), 1, 1, 'L', 1);

    // MICROBIOLOGICOS
    $pdf->setFont('Arial', '', 9);
    $pdf->setXY($pdf->startPage, $pdf->getY() + 5);
    cambiar_fondo($pdf, 1);
    $pdf->cell($medidas['ancho_disponible'], $medidas['alto_fila'], utf8_decode('MICROBIOLOGICOS'), 1, 2, 'C', 1);
    $pdf->cell($medidas['ancho_disponible'] / 5, $medidas['alto_fila'], utf8_decode('ANALISIS'), 1, 0, 'C', 1);
    $pdf->cell($medidas['ancho_disponible'] / 5, $medidas['alto_fila'], utf8_decode('PARAMETROS'), 1, 0, 'C', 1);
    $pdf->cell($medidas['ancho_disponible'] / 5, $medidas['alto_fila'], utf8_decode('RESULTADO'), 1, 0, 'C', 1);
    $pdf->cell($medidas['ancho_disponible'] / 5, $medidas['alto_fila'], utf8_decode('DESVIACION'), 1, 0, 'C', 1);
    $pdf->cell($medidas['ancho_disponible'] / 5, $medidas['alto_fila'], utf8_decode('COMENTARIO'), 1, 1, 'C', 1);
    $pdf->setX($pdf->startPage);
    cambiar_fondo($pdf, 0);
    $pdf->setFont('Arial', '', 8);
    $pdf->cell($medidas['ancho_disponible'] / 5, $medidas['alto_fila'], utf8_decode('AEROBIOS MESOFILOS'), 1, 0, 'L', 1);
    $pdf->setFont('Arial', '', 9);
    $pdf->cell($medidas['ancho_disponible'] / 5, $medidas['alto_fila'], utf8_decode($certificado['microbiologicos']['aerobios']['parametro']), 1, 0, 'L', 1);
    $pdf->cell($medidas['ancho_disponible'] / 5, $medidas['alto_fila'], utf8_decode($certificado['microbiologicos']['aerobios']['resultado']), 1, 0, 'L', 1);
    $pdf->cell($medidas['ancho_disponible'] / 5, $medidas['alto_fila'], utf8_decode($certificado['microbiologicos']['aerobios']['desviacion']), 1, 0, 'L', 1);
    $pdf->cell($medidas['ancho_disponible'] / 5, $medidas['alto_fila'], utf8_decode($certificado['microbiologicos']['aerobios']['comentario']), 1, 1, 'L', 1);
    $pdf->setX($pdf->startPage);
    $pdf->setFont('Arial', '', 8);
    $pdf->cell($medidas['ancho_disponible'] / 5, $medidas['alto_fila'], utf8_decode('COLIFORMES TOTALES'), 1, 0, 'L', 1);
    $pdf->setFont('Arial', '', 9);
    $pdf->cell($medidas['ancho_disponible'] / 5, $medidas['alto_fila'], utf8_decode($certificado['microbiologicos']['coliformes']['parametro']), 1, 0, 'L', 1);
    $pdf->cell($medidas['ancho_disponible'] / 5, $medidas['alto_fila'], utf8_decode($certificado['microbiologicos']['coliformes']['resultado']), 1, 0, 'L', 1);
    $pdf->cell($medidas['ancho_disponible'] / 5, $medidas['alto_fila'], utf8_decode($certificado['microbiologicos']['coliformes']['desviacion']), 1, 0, 'L', 1);
    $pdf->cell($medidas['ancho_disponible'] / 5, $medidas['alto_fila'], utf8_decode($certificado['microbiologicos']['coliformes']['comentario']), 1, 1, 'L', 1);
    $pdf->setX($pdf->startPage);
    $pdf->cell($medidas['ancho_disponible'] / 5, $medidas['alto_fila'], utf8_decode('SALMONELLA'), 1, 0, 'L', 1);
    $pdf->cell($medidas['ancho_disponible'] / 5, $medidas['alto_fila'], utf8_decode($certificado['microbiologicos']['salmonella']['parametro']), 1, 0, 'L', 1);
    $pdf->cell($medidas['ancho_disponible'] / 5, $medidas['alto_fila'], utf8_decode($certificado['microbiologicos']['salmonella']['resultado']), 1, 0, 'L', 1);
    $pdf->cell($medidas['ancho_disponible'] / 5, $medidas['alto_fila'], utf8_decode($certificado['microbiologicos']['salmonella']['desviacion']), 1, 0, 'L', 1);
    $pdf->cell($medidas['ancho_disponible'] / 5, $medidas['alto_fila'], utf8_decode($certificado['microbiologicos']['salmonella']['comentario']), 1, 1, 'L', 1);
    $pdf->setX($pdf->startPage);
    $pdf->cell($medidas['ancho_disponible'] / 5, $medidas['alto_fila'], utf8_decode('HONGOS'), 1, 0, 'L', 1);
    $pdf->cell($medidas['ancho_disponible'] / 5, $medidas['alto_fila'], utf8_decode($certificado['microbiologicos']['hongos']['parametro']), 1, 0, 'L', 1);
    $pdf->cell($medidas['ancho_disponible'] / 5, $medidas['alto_fila'], utf8_decode($certificado['microbiologicos']['hongos']['resultado']), 1, 0, 'L', 1);
    $pdf->cell($medidas['ancho_disponible'] / 5, $medidas['alto_fila'], utf8_decode($certificado['microbiologicos']['hongos']['desviacion']), 1, 0, 'L', 1);
    $pdf->cell($medidas['ancho_disponible'] / 5, $medidas['alto_fila'], utf8_decode($certificado['microbiologicos']['hongos']['comentario']), 1, 1, 'L', 1);
    $pdf->setX($pdf->startPage);
    $pdf->cell($medidas['ancho_disponible'] / 5, $medidas['alto_fila'], utf8_decode('LEVADURAS'), 1, 0, 'L', 1);
    $pdf->cell($medidas['ancho_disponible'] / 5, $medidas['alto_fila'], utf8_decode($certificado['microbiologicos']['levaduras']['parametro']), 1, 0, 'L', 1);
    $pdf->cell($medidas['ancho_disponible'] / 5, $medidas['alto_fila'], utf8_decode($certificado['microbiologicos']['levaduras']['resultado']), 1, 0, 'L', 1);
    $pdf->cell($medidas['ancho_disponible'] / 5, $medidas['alto_fila'], utf8_decode($certificado['microbiologicos']['levaduras']['desviacion']), 1, 0, 'L', 1);
    $pdf->cell($medidas['ancho_disponible'] / 5, $medidas['alto_fila'], utf8_decode($certificado['microbiologicos']['levaduras']['comentario']), 1, 1, 'L', 1);
    // $pdf->setX($pdf->startPage);
    // $pdf->cell($medidas['ancho_disponible'] / 5, $medidas['alto_fila'], utf8_decode('LISTERIA'), 1, 0, 'L', 1);
    // $pdf->cell($medidas['ancho_disponible'] / 5, $medidas['alto_fila'], utf8_decode($certificado['microbiologicos']['listeria']['parametro']), 1, 0, 'L', 1);
    // $pdf->cell($medidas['ancho_disponible'] / 5, $medidas['alto_fila'], utf8_decode($certificado['microbiologicos']['listeria']['resultado']), 1, 0, 'L', 1);
    // $pdf->cell($medidas['ancho_disponible'] / 5, $medidas['alto_fila'], utf8_decode($certificado['microbiologicos']['listeria']['desviacion']), 1, 0, 'L', 1);
    // $pdf->cell($medidas['ancho_disponible'] / 5, $medidas['alto_fila'], utf8_decode($certificado['microbiologicos']['listeria']['comentario']), 1, 1, 'L', 1);

    // NOTA
    $pdf->setXY($pdf->startPage - 1, $pdf->getY() + 10);
    $pdf->setFont('Arial', '', 9);
    $pdf->MultiCell($medidas['ancho_disponible'], $medidas['alto_fila'], utf8_decode('NOTA: EL CLIENTE DISPONDRÁ CUALES SON SUS NECESIDADES, Y PARA ESTO SE REALIZARAN LOS ANÁLISIS CORRESPONDIENTES. EL TIPO DE MIEL SE CLASIFICA COMO MIEL 100% PURA DE ABEJA O MIEL 100% ORGANICA'), 0, 'L', 0);
    $pdf->setXY($pdf->startPage - 1, $pdf->getY());
    $pdf->MultiCell($medidas['ancho_disponible'], $medidas['alto_fila'], utf8_decode('EL TIPO DE MIEL SE CLASIFICA COMO MIEL 100% PURA DE ABEJA O MIEL 100% ORGANICA'), 0, 'L', 0);

    // nombres de las personas que van a firmar
    $pdf->setXY($pdf->startPage - 1, $pdf->getY() + 10);
    $pdf->Ln(10);
    $pdf->Line($pdf->startPage - 1, $pdf->getY() + 5, 85, $pdf->getY() + 5);
    $pdf->Ln(8);
    $pdf->setFont('Arial', 'B', 8);
    $pdf->Multicell($medidas['ancho_disponible'] / 3, $medidas['alto_fila'], utf8_decode(' '), 0, 0, 'C', 0);
    $pdf->Multicell($medidas['ancho_disponible'] / 4 + 8, $medidas['alto_fila'], utf8_decode('Jefe de Laboratorio'), 0, 0, 'C', 0);

    $pdf->Output('I', 'CERTIFICADO DE LOTE TERMINADO.pdf', true);
}

function outputError($message)
{
    $pdf = new FPDF('P', 'mm', 'A4');
    $pdf->AddPage();
    $pdf->SetFont('Arial', 'B', 9);
    $pdf->cell(500, 5, utf8_decode($message), 0, 2, 'L', 0);
    $pdf->Output('I', $message . '.pdf', true);
}

try {
    if (!isset($_GET['miel']) || !isset($_GET['idLote'])) {
        throw new Exception('No se especificó la recolección');
    }
    outputPdf($_GET['miel'], $_GET['idLote']);
} catch (Exception $e) {
    outputError($e->getMessage());
    exit();
}
