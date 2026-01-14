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
            $calidad = 'calidad';
            $tipoDeMiel = '1';
            $producto = 'Miel 100% pura de abeja';
            break;
        case '2':
            $encabezado = 'certificadoloteterminado_encabezado_organico';
            $detalle = 'certificadoloteterminado_detalle_organico';
            $calidad = 'calidad_organico';
            $tipoDeMiel = '2';
            $producto = 'Miel 100% orgánica';
            break;
        case '5':
            $encabezado = 'certificadoloteterminado_encabezado_mantequilla';
            $detalle = 'certificadoloteterminado_detalle_mantequilla';
            $calidad = 'calidad_mantequilla';
            $tipoDeMiel = '5';
            $producto = 'Miel 100% Mantequilla';
            break;
        case '6':
            $encabezado = 'certificadoloteterminado_encabezado_altiplano';
            $detalle = 'certificadoloteterminado_detalle_altiplano';
            $calidad = 'calidad_altiplano';
            $tipoDeMiel = '6';
            $producto = 'Miel 100% Altiplano';
            break;
        case '7':
            $encabezado = 'certificadoloteterminado_encabezado_naranjo';
            $detalle = 'certificadoloteterminado_detalle_naranjo';
            $calidad = 'calidad_naranjo';
            $tipoDeMiel = '7';
            $producto = 'Miel 100% Naranjo';
            break;
        case '8':
            $encabezado = 'certificadoloteterminado_encabezado_aguacate';
            $detalle = 'certificadoloteterminado_detalle_aguacate';
            $calidad = 'calidad_aguacate';
            $tipoDeMiel = '8';
            $producto = 'Miel 100% Aguacate';
            break;
        case '9':
            $encabezado = 'certificadoloteterminado_encabezado_mezquite';
            $detalle = 'certificadoloteterminado_detalle_mezquite';
            $calidad = 'calidad_mezquite';
            $tipoDeMiel = '9';
            $producto = 'Miel 100% Mezquite';
            break;
    }

    $sqlEncabezado = "SELECT c.*, $tipoDeMiel AS tipoDeMiel
    FROM $encabezado c LEFT JOIN $calidad cl ON cl.idLoteInterno = c.idLote
    WHERE c.idLote= :idLote";
    $queryEncabezado = $con->prepare($sqlEncabezado);
    $queryEncabezado->bindParam(':idLote', $lote);
    $queryEncabezado->execute();
    if (!$queryEncabezado) {
        throw new Exception($con->errorInfo());
    }
    $resultado = $queryEncabezado->fetch(PDO::FETCH_ASSOC);
    $idEncabezado = $resultado['idEncabezado'];
    $resultado['producto'] = $producto;

    if ($resultado['tipoDeCliente'] == '10') {
        $query = "SELECT *
            FROM clientesexportadores
            WHERE idClienteExportador = :idClienteExportador";
        $datosResp = $con->prepare($query);
        $datosResp->bindParam(':idClienteExportador', $resultado['cliente']);
        $datosResp->execute();
        $result = $datosResp->fetch(PDO::FETCH_ASSOC);
        $datosCliente = json_decode($result['datosCliente']);
        $resultado['cliente'] = $datosCliente->nombre;
    } else if ($resultado['tipoDeCliente'] == '6') {
        $seleccionarClientes = $con->prepare("SELECT nombre FROM clientes WHERE idCliente = :idCliente");
        $seleccionarClientes->bindParam(':idCliente', $resultado['cliente']);
        $seleccionarClientes->execute();
        $resultado['cliente'] = $seleccionarClientes->fetch(PDO::FETCH_ASSOC);
    }


    $sql = "SELECT * FROM $detalle WHERE idEncabezado = :idEncabezado AND idAnalisis BETWEEN 1 AND 6 OR idAnalisis IN(16, 17)";
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
                } else  if ($miel == '5') {
                    $caracteristicas['parametro'] = 'Miel 100% mantequilla';
                    $caracteristicas['resultado'] = 'Miel 100% mantequilla';
                } else  if ($miel == '6') {
                    $caracteristicas['parametro'] = 'Miel 100% altiplano';
                    $caracteristicas['resultado'] = 'Miel 100% altiplano';
                } else  if ($miel == '7') {
                    $caracteristicas['parametro'] = 'Miel 100% naranjo';
                    $caracteristicas['resultado'] = 'Miel 100% naranjo';
                } else  if ($miel == '8') {
                    $caracteristicas['parametro'] = 'Miel 100% aguacate';
                    $caracteristicas['resultado'] = 'Miel 100% aguacate';
                } else  if ($miel == '9') {
                    $caracteristicas['parametro'] = 'Miel 100% mezquite';
                    $caracteristicas['resultado'] = 'Miel 100% mezquite';
                } else {
                    $caracteristicas['parametro'] = 'Miel 100% organica';
                    $caracteristicas['resultado'] = 'Miel 100% pura de abeja';
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
            if ($caracteristicas['idAnalisis'] == '17') {
                $resultado['caracteristicas']['solidos'] = $caracteristicas;
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
            if ($antibioticos['idAnalisis'] == '7') {
                $resultado['antibioticos']['sulfametazinas'] = $antibioticos;
            }
            if ($antibioticos['idAnalisis'] == '8') {
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

    $sqlSensoriales = "SELECT * FROM $detalle WHERE idEncabezado = :idEncabezado AND idAnalisis BETWEEN 18 AND 21";
    $sqlDetalleSen = $con->prepare($sqlSensoriales);
    $sqlDetalleSen->bindParam(':idEncabezado', $idEncabezado);
    $sqlDetalleSen->execute();

    if ($sqlDetalleSen == FALSE) {
        throw new Exception($con->errorInfo());
    } else {
        $resultado['sensoriales'] = $sqlDetalleSen->fetchAll(PDO::FETCH_ASSOC);
        foreach ($resultado['sensoriales'] as $sensoriales) {
            if ($sensoriales['idAnalisis'] == '18') {
                $resultado['sensoriales']['apariencia'] = $sensoriales;
            }
            if ($sensoriales['idAnalisis'] == '19') {
                $resultado['sensoriales']['color'] = $sensoriales;
            }
            if ($sensoriales['idAnalisis'] == '20') {
                $resultado['sensoriales']['olor'] = $sensoriales;
            }
            if ($sensoriales['idAnalisis'] == '21') {
                $resultado['sensoriales']['sabor'] = $sensoriales;
            }
        }
    }


    return $resultado;
}

function cambiar_fondo($pdf, $t = 0)
{
    switch ($t) {
        case 1:
            $pdf->SetFillColor(232, 229, 229);
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
    $pdf->SetTitle('CERTIFICADO DE CALIDAD');
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
    $pdf->cell(0, 5, utf8_decode(strtoupper('CERTIFICADO DE CALIDAD')), 0, 2, 'C', 0);

    // INSTRUCCIONES
    // $pdf->setXY($pdf->startPage - 1, $pdf->getY() + 8);
    // $pdf->setFont('Arial', '', 9);
    // $pdf->MultiCell($medidas['ancho_disponible'], $medidas['alto_fila'], utf8_decode('LLENAR CON LETRA DE MOLDE Y CLARA TODOS LOS ESPACIO, NO DEJE ESPACIOS VACÍOS, TRACE UNA DIAGONAL, USE TINTA AZUL O NEGRA NO USE TINTA ROJA.'), 0, 'L', 0);

    $pdf->setXY($pdf->startPage - 1, $pdf->getY() + 5);
    $pdf->setFont('Arial', '', 10);
    $pdf->cell(0, 5, utf8_decode('Fecha: ' . date('d/m/Y', strtotime($certificado['fechaCalidad']))), 0, 2, 'L', 0);
    $pdf->setXY($pdf->startPage, $pdf->getY());

    $pdf->setXY($pdf->startPage - 1, $pdf->getY());
    $pdf->setFont('Arial', '', 10);

    if ($certificado['tipoDeCliente'] == '10') {
        $pdf->cell(0, 5, utf8_decode('Cliente: ' . $certificado['cliente']), 0, 2, 'L', 0);
    } else if ($certificado['tipoDeCliente'] == '6') {
        $pdf->cell(0, 5, utf8_decode('Cliente: ' . $certificado['cliente']['nombre']), 0, 2, 'L', 0);
        // $pdf->cell(100, $GLOBALS['rowHeight'], utf8_decode(strtoupper($datos['cliente']['nombre'])), 0, 1, 'L', 0);
    }
    // $pdf->cell(0, 5, utf8_decode('Cliente: ' . $certificado['cliente']), 0, 2, 'L', 0);
    $pdf->setXY($pdf->startPage, $pdf->getY());


    $pdf->setXY($pdf->startPage - 1, $pdf->getY());
    $pdf->setFont('Arial', '', 10);
    $pdf->cell(0, 5, utf8_decode('Lote: ' . $certificado['loteCalidad']), 0, 2, 'L', 0);
    $pdf->setXY($pdf->startPage, $pdf->getY());

    $pdf->setXY($pdf->startPage - 1, $pdf->getY());
    $pdf->setFont('Arial', '', 10);
    $pdf->cell(0, 5, utf8_decode('Producto: ' . $certificado['producto']), 0, 2, 'L', 0);
    $pdf->setXY($pdf->startPage, $pdf->getY());

    $pdf->setXY($pdf->startPage - 1, $pdf->getY());
    $pdf->setFont('Arial', '', 10);
    $pdf->cell(0, 5, utf8_decode('Cant./Descripción: ' . $certificado['cantidad']), 0, 2, 'L', 0);
    $pdf->setXY($pdf->startPage, $pdf->getY());

    $pdf->setXY($pdf->startPage - 1, $pdf->getY());
    $pdf->setFont('Arial', '', 10);
    $pdf->cell(0, 5, utf8_decode('Fecha de caducidad: ' . date('d/m/Y', strtotime($certificado['fechaCaducidad']))), 0, 2, 'L', 0);
    $pdf->setXY($pdf->startPage, $pdf->getY());

    if ($certificado['factura'] != '' || $certificado['factura'] != null) {
        $pdf->setXY($pdf->startPage - 1, $pdf->getY());
        $pdf->setFont('Arial', '', 10);
        $pdf->cell(0, 5, utf8_decode('Factura: ' . $certificado['factura']), 0, 2, 'L', 0);
        $pdf->setXY($pdf->startPage, $pdf->getY());
    }

    if ($certificado['sello'] != '' || $certificado['sello'] != null) {
        $pdf->setXY($pdf->startPage - 1, $pdf->getY());
        $pdf->setFont('Arial', '', 10);
        $pdf->cell(0, 5, utf8_decode('Sello: ' . $certificado['sello']), 0, 2, 'L', 0);
        $pdf->setXY($pdf->startPage, $pdf->getY());
    }

    if ($certificado['chofer'] != '' || $certificado['chofer'] != null) {
        $pdf->setXY($pdf->startPage - 1, $pdf->getY());
        $pdf->setFont('Arial', '', 10);
        $pdf->cell(0, 5, utf8_decode('Chófer: ' . $certificado['chofer']), 0, 2, 'L', 0);
        $pdf->setXY($pdf->startPage, $pdf->getY());
    }
    // Construir la tabla
    // SENSORIALES
    $pdf->setFont('Arial', '', 9);
    $pdf->setXY($pdf->startPage, $pdf->getY() + 5);
    cambiar_fondo($pdf, 1);
    $pdf->cell($medidas['ancho_disponible'] / 5, $medidas['alto_fila'], utf8_decode('SENSORIALES'), 1, 0, 'C', 1);
    $pdf->cell($medidas['ancho_disponible'] / 3 + 12, $medidas['alto_fila'], utf8_decode('NMX-F-036-NORMEX-2006'), 1, 0, 'C', 1);
    $pdf->cell($medidas['ancho_disponible'] / 5, $medidas['alto_fila'], utf8_decode('RESULTADOS'), 1, 0, 'C', 1);
    $pdf->cell($medidas['ancho_disponible'] / 5, $medidas['alto_fila'], utf8_decode('TECNICAS'), 1, 1, 'C', 1);
    $pdf->setX($pdf->startPage);
    cambiar_fondo($pdf, 0);
    $pdf->cell($medidas['ancho_disponible'] / 5, $medidas['alto_fila'], utf8_decode('Apariencia'), 1, 0, 'L', 1);
    $pdf->cell($medidas['ancho_disponible'] / 3 + 12, $medidas['alto_fila'], utf8_decode($certificado['caracteristicas']['tipoDeMiel']['parametro']), 1, 0, 'L', 1);
    $pdf->cell($medidas['ancho_disponible'] / 5, $medidas['alto_fila'], utf8_decode($certificado['sensoriales']['apariencia']['resultado']), 1, 0, 'L', 1);
    $pdf->cell($medidas['ancho_disponible'] / 5, $medidas['alto_fila'], utf8_decode($certificado['sensoriales']['apariencia']['tecnicas']), 1, 1, 'L', 1);
    $pdf->setX($pdf->startPage);
    $pdf->cell($medidas['ancho_disponible'] / 5, $medidas['alto_fila'], utf8_decode('Color'), 1, 0, 'L', 1);
    $pdf->MultiCell($medidas['ancho_disponible'] / 3 + 12, $medidas['alto_fila'], utf8_decode($certificado['sensoriales']['color']['cuadroSensoriales']), 1, 1, 'L', 0);
    $pdf->setXY($pdf->getX() + 113, $pdf->getY() - 15);
    // $pdf->cell($medidas['ancho_disponible'] / 3 + 12, $medidas['alto_fila'], utf8_decode($certificado['sensoriales']['color']['cuadroSensoriales']), 1, 0, 'L', 1);
    $pdf->cell($medidas['ancho_disponible'] / 5, $medidas['alto_fila'], utf8_decode($certificado['sensoriales']['color']['resultado']), 1, 0, 'L', 1);
    $pdf->cell($medidas['ancho_disponible'] / 5, $medidas['alto_fila'], utf8_decode($certificado['sensoriales']['color']['tecnicas']), 1, 1, 'L', 1);
    $pdf->setX($pdf->startPage);
    $pdf->cell($medidas['ancho_disponible'] / 5, $medidas['alto_fila'], utf8_decode('Olor'), 1, 0, 'L', 1);
    $pdf->setX($pdf->startPage + 108);
    $pdf->cell($medidas['ancho_disponible'] / 5, $medidas['alto_fila'], utf8_decode($certificado['sensoriales']['olor']['resultado']), 1, 0, 'L', 1);
    $pdf->cell($medidas['ancho_disponible'] / 5, $medidas['alto_fila'], utf8_decode($certificado['sensoriales']['olor']['tecnicas']), 1, 1, 'L', 1);
    $pdf->setX($pdf->startPage);
    $pdf->cell($medidas['ancho_disponible'] / 5, $medidas['alto_fila'], utf8_decode('Sabor'), 1, 0, 'L', 1);
    $pdf->setX($pdf->startPage + 108);
    $pdf->cell($medidas['ancho_disponible'] / 5, $medidas['alto_fila'], utf8_decode($certificado['sensoriales']['sabor']['resultado']), 1, 0, 'L', 1);
    $pdf->cell($medidas['ancho_disponible'] / 5, $medidas['alto_fila'], utf8_decode($certificado['sensoriales']['sabor']['tecnicas']), 1, 1, 'L', 1);

    // CARACTERISTICAS
    $pdf->setFont('Arial', '', 9);
    $pdf->setXY($pdf->startPage, $pdf->getY() + 4);
    cambiar_fondo($pdf, 1);
    $pdf->cell($medidas['ancho_disponible'] / 5, $medidas['alto_fila'], utf8_decode('FISICO - QUIMICOS'), 1, 0, 'C', 1);
    $pdf->cell($medidas['ancho_disponible'] / 5, $medidas['alto_fila'], utf8_decode('MINIMO'), 1, 0, 'C', 1);
    $pdf->cell($medidas['ancho_disponible'] / 5, $medidas['alto_fila'], utf8_decode('MAXIMO'), 1, 0, 'C', 1);
    $pdf->cell($medidas['ancho_disponible'] / 5, $medidas['alto_fila'], utf8_decode('RESULTADOS'), 1, 0, 'C', 1);
    $pdf->cell($medidas['ancho_disponible'] / 5, $medidas['alto_fila'], utf8_decode('TECNICAS'), 1, 1, 'C', 1);
    cambiar_fondo($pdf, 0);

    $pdf->setX($pdf->startPage);
    $pdf->cell($medidas['ancho_disponible'] / 5, $medidas['alto_fila'], utf8_decode('COLOR'), 1, 0, 'L', 1);
    $pdf->cell($medidas['ancho_disponible'] / 5, $medidas['alto_fila'], utf8_decode($certificado['caracteristicas']['color']['minimo']), 1, 0, 'L', 1);
    $pdf->cell($medidas['ancho_disponible'] / 5, $medidas['alto_fila'], utf8_decode($certificado['caracteristicas']['color']['maximo']), 1, 0, 'L', 1);
    $pdf->cell($medidas['ancho_disponible'] / 5, $medidas['alto_fila'], utf8_decode($certificado['caracteristicas']['color']['resultado']), 1, 0, 'L', 1);
    $pdf->cell($medidas['ancho_disponible'] / 5, $medidas['alto_fila'], utf8_decode($certificado['caracteristicas']['color']['tecnicas']), 1, 1, 'L', 1);
    $pdf->setX($pdf->startPage);
    $pdf->cell($medidas['ancho_disponible'] / 5, $medidas['alto_fila'], utf8_decode('% HUMEDAD'), 1, 0, 'L', 1);
    $pdf->cell($medidas['ancho_disponible'] / 5, $medidas['alto_fila'], utf8_decode($certificado['caracteristicas']['humedad']['minimo']), 1, 0, 'L', 1);
    $pdf->cell($medidas['ancho_disponible'] / 5, $medidas['alto_fila'], utf8_decode($certificado['caracteristicas']['humedad']['maximo']), 1, 0, 'L', 1);
    $pdf->cell($medidas['ancho_disponible'] / 5, $medidas['alto_fila'], utf8_decode($certificado['caracteristicas']['humedad']['resultado']), 1, 0, 'L', 1);
    $pdf->cell($medidas['ancho_disponible'] / 5, $medidas['alto_fila'], utf8_decode($certificado['caracteristicas']['humedad']['tecnicas']), 1, 1, 'L', 1);
    $pdf->setX($pdf->startPage);
    $pdf->cell($medidas['ancho_disponible'] / 5, $medidas['alto_fila'], utf8_decode('ADULTERACION'), 1, 0, 'L', 1);
    $pdf->cell($medidas['ancho_disponible'] / 5, $medidas['alto_fila'], utf8_decode($certificado['caracteristicas']['adulteracion']['minimo']), 1, 0, 'L', 1);
    $pdf->cell($medidas['ancho_disponible'] / 5, $medidas['alto_fila'], utf8_decode($certificado['caracteristicas']['adulteracion']['maximo']), 1, 0, 'L', 1);
    $pdf->cell($medidas['ancho_disponible'] / 5, $medidas['alto_fila'], utf8_decode($certificado['caracteristicas']['adulteracion']['resultado']), 1, 0, 'L', 1);
    $pdf->cell($medidas['ancho_disponible'] / 5, $medidas['alto_fila'], utf8_decode($certificado['caracteristicas']['adulteracion']['tecnicas']), 1, 1, 'L', 1);
    $pdf->setX($pdf->startPage);
    $pdf->cell($medidas['ancho_disponible'] / 5, $medidas['alto_fila'], utf8_decode('SOLIDOS TOTALES'), 1, 0, 'L', 1);
    $pdf->cell($medidas['ancho_disponible'] / 5, $medidas['alto_fila'], utf8_decode($certificado['caracteristicas']['brix']['minimo']), 1, 0, 'L', 1);
    $pdf->cell($medidas['ancho_disponible'] / 5, $medidas['alto_fila'], utf8_decode($certificado['caracteristicas']['brix']['maximo']), 1, 0, 'L', 1);
    $pdf->cell($medidas['ancho_disponible'] / 5, $medidas['alto_fila'], utf8_decode($certificado['caracteristicas']['brix']['resultado']), 1, 0, 'L', 1);
    $pdf->cell($medidas['ancho_disponible'] / 5, $medidas['alto_fila'], utf8_decode($certificado['caracteristicas']['brix']['tecnicas']), 1, 1, 'L', 1);
    $pdf->setX($pdf->startPage);
    $pdf->cell($medidas['ancho_disponible'] / 5, $medidas['alto_fila'], utf8_decode('H.M.F'), 1, 0, 'L', 1);
    $pdf->cell($medidas['ancho_disponible'] / 5, $medidas['alto_fila'], utf8_decode($certificado['caracteristicas']['hmf']['minimo']), 1, 0, 'L', 1);
    $pdf->cell($medidas['ancho_disponible'] / 5, $medidas['alto_fila'], utf8_decode($certificado['caracteristicas']['hmf']['maximo']), 1, 0, 'L', 1);
    $pdf->cell($medidas['ancho_disponible'] / 5, $medidas['alto_fila'], utf8_decode($certificado['caracteristicas']['hmf']['resultado']), 1, 0, 'L', 1);
    $pdf->cell($medidas['ancho_disponible'] / 5, $medidas['alto_fila'], utf8_decode($certificado['caracteristicas']['hmf']['tecnicas']), 1, 1, 'L', 1);
    $pdf->setX($pdf->startPage);
    $pdf->cell($medidas['ancho_disponible'] / 5, $medidas['alto_fila'], utf8_decode('F/G'), 1, 0, 'L', 1);
    $pdf->cell($medidas['ancho_disponible'] / 5, $medidas['alto_fila'], utf8_decode($certificado['caracteristicas']['fg']['minimo']), 1, 0, 'L', 1);
    $pdf->cell($medidas['ancho_disponible'] / 5, $medidas['alto_fila'], utf8_decode($certificado['caracteristicas']['fg']['maximo']), 1, 0, 'L', 1);
    $pdf->cell($medidas['ancho_disponible'] / 5, $medidas['alto_fila'], utf8_decode($certificado['caracteristicas']['fg']['resultado']), 1, 0, 'L', 1);
    $pdf->cell($medidas['ancho_disponible'] / 5, $medidas['alto_fila'], utf8_decode($certificado['caracteristicas']['fg']['tecnicas']), 1, 1, 'L', 1);

    // ANTIBIOTICOS
    $pdf->setFont('Arial', '', 9);
    $pdf->setXY($pdf->startPage, $pdf->getY() + 5);
    cambiar_fondo($pdf, 1);
    $pdf->cell($medidas['ancho_disponible'] / 2 + 18, $medidas['alto_fila'], utf8_decode('DETECCION DE CONTAMINANTES'), 1, 0, 'C', 1);
    // $pdf->cell($medidas['ancho_disponible'] / 5, $medidas['alto_fila'], utf8_decode(''), 1, 0, 'C', 1);
    // $pdf->cell($medidas['ancho_disponible'] / 5, $medidas['alto_fila'], utf8_decode(''), 1, 0, 'C', 1);
    $pdf->cell($medidas['ancho_disponible'] / 5, $medidas['alto_fila'], utf8_decode('RESULTADOS'), 1, 0, 'C', 1);
    $pdf->cell($medidas['ancho_disponible'] / 5, $medidas['alto_fila'], utf8_decode('TECNICAS'), 1, 1, 'C', 1);
    $pdf->setX($pdf->startPage);
    cambiar_fondo($pdf, 0);
    $pdf->cell($medidas['ancho_disponible'] / 2 + 18, $medidas['alto_fila'], utf8_decode('SULFONAMIDAS'), 1, 0, 'L', 1);
    // $pdf->cell($medidas['ancho_disponible'] / 5, $medidas['alto_fila'], utf8_decode(''), 1, 0, 'L', 1);
    // $pdf->cell($medidas['ancho_disponible'] / 5, $medidas['alto_fila'], utf8_decode(''), 1, 0, 'L', 1);
    $pdf->cell($medidas['ancho_disponible'] / 5, $medidas['alto_fila'], utf8_decode($certificado['antibioticos']['sulfametazinas']['resultado']), 1, 0, 'L', 1);
    $pdf->cell($medidas['ancho_disponible'] / 5, $medidas['alto_fila'], utf8_decode($certificado['antibioticos']['sulfametazinas']['tecnicas']), 1, 1, 'L', 1);
    $pdf->setX($pdf->startPage);
    $pdf->cell($medidas['ancho_disponible'] / 2 + 18, $medidas['alto_fila'], utf8_decode('TETRACICLINAS'), 1, 0, 'L', 1);
    // $pdf->cell($medidas['ancho_disponible'] / 5, $medidas['alto_fila'], utf8_decode(''), 1, 0, 'L', 1);
    // $pdf->cell($medidas['ancho_disponible'] / 5, $medidas['alto_fila'], utf8_decode(''), 1, 0, 'L', 1);
    $pdf->cell($medidas['ancho_disponible'] / 5, $medidas['alto_fila'], utf8_decode($certificado['antibioticos']['tetraciclinas']['resultado']), 1, 0, 'L', 1);
    $pdf->cell($medidas['ancho_disponible'] / 5, $medidas['alto_fila'], utf8_decode($certificado['antibioticos']['tetraciclinas']['tecnicas']), 1, 1, 'L', 1);
    $pdf->setX($pdf->startPage);
    $pdf->cell($medidas['ancho_disponible'] / 2 + 18, $medidas['alto_fila'], utf8_decode('ESTREPTOMICINAS'), 1, 0, 'L', 1);
    // $pdf->cell($medidas['ancho_disponible'] / 5, $medidas['alto_fila'], utf8_decode(''), 1, 0, 'L', 1);
    // $pdf->cell($medidas['ancho_disponible'] / 5, $medidas['alto_fila'], utf8_decode(''), 1, 0, 'L', 1);
    $pdf->cell($medidas['ancho_disponible'] / 5, $medidas['alto_fila'], utf8_decode($certificado['antibioticos']['estreptomicinas']['resultado']), 1, 0, 'L', 1);
    $pdf->cell($medidas['ancho_disponible'] / 5, $medidas['alto_fila'], utf8_decode($certificado['antibioticos']['estreptomicinas']['tecnicas']), 1, 1, 'L', 1);

    // MICROBIOLOGICOS
    $pdf->setFont('Arial', '', 9);
    $pdf->setXY($pdf->startPage, $pdf->getY() + 5);
    cambiar_fondo($pdf, 1);
    $pdf->cell($medidas['ancho_disponible'] / 2 - 18, $medidas['alto_fila'], utf8_decode('MICROBIOLOGICOS'), 1, 0, 'C', 1);
    // $pdf->cell($medidas['ancho_disponible'] / 5, $medidas['alto_fila'], utf8_decode(''), 1, 0, 'C', 1);
    $pdf->cell($medidas['ancho_disponible'] / 5, $medidas['alto_fila'], utf8_decode('NORMAS SSA'), 1, 0, 'C', 1);
    $pdf->cell($medidas['ancho_disponible'] / 5, $medidas['alto_fila'], utf8_decode('RESULTADOS'), 1, 0, 'C', 1);
    $pdf->cell($medidas['ancho_disponible'] / 5, $medidas['alto_fila'], utf8_decode('TECNICAS'), 1, 1, 'C', 1);
    $pdf->setX($pdf->startPage);
    cambiar_fondo($pdf, 0);
    $pdf->setFont('Arial', '', 7.5);
    $pdf->cell($medidas['ancho_disponible'] / 2 - 18, $medidas['alto_fila'], utf8_decode('MICROORGANISMOS MESOFILICOS AEROBIOS UFC/g'), 1, 0, 'L', 1);
    // $pdf->cell($medidas['ancho_disponible'] / 5, $medidas['alto_fila'], utf8_decode(''), 1, 0, 'L', 1);
    $pdf->setFont('Arial', '', 9);
    $pdf->cell($medidas['ancho_disponible'] / 5, $medidas['alto_fila'], utf8_decode($certificado['microbiologicos']['aerobios']['parametro']), 1, 0, 'L', 1);
    $pdf->cell($medidas['ancho_disponible'] / 5, $medidas['alto_fila'], utf8_decode($certificado['microbiologicos']['aerobios']['resultado']), 1, 0, 'L', 1);
    $pdf->cell($medidas['ancho_disponible'] / 5, $medidas['alto_fila'], utf8_decode($certificado['microbiologicos']['aerobios']['tecnicas']), 1, 1, 'L', 1);
    $pdf->setX($pdf->startPage);
    $pdf->cell($medidas['ancho_disponible'] / 2 - 18, $medidas['alto_fila'], utf8_decode('HONGOS'), 1, 0, 'L', 1);
    // $pdf->cell($medidas['ancho_disponible'] / 5, $medidas['alto_fila'], utf8_decode(''), 1, 0, 'L', 1);
    $pdf->cell($medidas['ancho_disponible'] / 5, $medidas['alto_fila'], utf8_decode($certificado['microbiologicos']['hongos']['parametro']), 1, 0, 'L', 1);
    $pdf->cell($medidas['ancho_disponible'] / 5, $medidas['alto_fila'], utf8_decode($certificado['microbiologicos']['hongos']['resultado']), 1, 0, 'L', 1);
    $pdf->cell($medidas['ancho_disponible'] / 5, $medidas['alto_fila'], utf8_decode($certificado['microbiologicos']['hongos']['tecnicas']), 1, 1, 'L', 1);
    $pdf->setX($pdf->startPage);
    $pdf->cell($medidas['ancho_disponible'] / 2 - 18, $medidas['alto_fila'], utf8_decode('LEVADURAS'), 1, 0, 'L', 1);
    // $pdf->cell($medidas['ancho_disponible'] / 5, $medidas['alto_fila'], utf8_decode(''), 1, 0, 'L', 1);
    $pdf->cell($medidas['ancho_disponible'] / 5, $medidas['alto_fila'], utf8_decode($certificado['microbiologicos']['levaduras']['parametro']), 1, 0, 'L', 1);
    $pdf->cell($medidas['ancho_disponible'] / 5, $medidas['alto_fila'], utf8_decode($certificado['microbiologicos']['levaduras']['resultado']), 1, 0, 'L', 1);
    $pdf->cell($medidas['ancho_disponible'] / 5, $medidas['alto_fila'], utf8_decode($certificado['microbiologicos']['levaduras']['tecnicas']), 1, 1, 'L', 1);
    $pdf->setX($pdf->startPage);
    $pdf->setFont('Arial', '', 9);
    $pdf->cell($medidas['ancho_disponible'] / 2 - 18, $medidas['alto_fila'], utf8_decode('COLIFORMES TOTALES UFC/g'), 1, 0, 'L', 1);
    // $pdf->cell($medidas['ancho_disponible'] / 5, $medidas['alto_fila'], utf8_decode(''), 1, 0, 'L', 1);
    $pdf->cell($medidas['ancho_disponible'] / 5, $medidas['alto_fila'], utf8_decode($certificado['microbiologicos']['coliformes']['parametro']), 1, 0, 'L', 1);
    $pdf->cell($medidas['ancho_disponible'] / 5, $medidas['alto_fila'], utf8_decode($certificado['microbiologicos']['coliformes']['resultado']), 1, 0, 'L', 1);
    $pdf->cell($medidas['ancho_disponible'] / 5, $medidas['alto_fila'], utf8_decode($certificado['microbiologicos']['coliformes']['tecnicas']), 1, 1, 'L', 1);
    $pdf->setX($pdf->startPage);
    $pdf->cell($medidas['ancho_disponible'] / 2 - 18, $medidas['alto_fila'], utf8_decode('SALMONELLA'), 1, 0, 'L', 1);
    // $pdf->cell($medidas['ancho_disponible'] / 5, $medidas['alto_fila'], utf8_decode(''), 1, 0, 'L', 1);
    $pdf->cell($medidas['ancho_disponible'] / 5, $medidas['alto_fila'], utf8_decode($certificado['microbiologicos']['salmonella']['parametro']), 1, 0, 'L', 1);
    $pdf->cell($medidas['ancho_disponible'] / 5, $medidas['alto_fila'], utf8_decode($certificado['microbiologicos']['salmonella']['resultado']), 1, 0, 'L', 1);
    $pdf->cell($medidas['ancho_disponible'] / 5, $medidas['alto_fila'], utf8_decode($certificado['microbiologicos']['salmonella']['tecnicas']), 1, 1, 'L', 1);

    // nombres de las personas que van a firmar
    $pdf->setXY($pdf->startPage - 1, $pdf->getY() + 10);
    $pdf->Ln(10);
    $pdf->Line($pdf->startPage - 1, $pdf->getY() + 5, 85, $pdf->getY() + 5);
    $pdf->Ln(8);
    $pdf->setFont('Arial', 'B', 8);
    $pdf->Multicell($medidas['ancho_disponible'] / 3, $medidas['alto_fila'], utf8_decode(' '), 0, 0, 'C', 0);
    $pdf->Multicell($medidas['ancho_disponible'] / 4 + 11, $medidas['alto_fila'], utf8_decode('Jefe Aseguramiento de Calidad'), 0, 0, 'C', 0);

    $pdf->Output('I', 'CERTIFICADO DE CALIDAD.pdf', true);
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
