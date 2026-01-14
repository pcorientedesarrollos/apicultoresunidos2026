<?php

require('../../fpdf/FPDF/fpdf.php');
include_once '../../DAOConeccion/conePDO.php';

$pdo = new conePDO();
$con = $pdo->conectar();

$idArea = $_GET['idArea'];

$stmt = $con->prepare("SELECT area FROM areas WHERE idArea = :idArea");
$stmt->bindParam(':idArea', $idArea);
$stmt->execute();
$stmt->bindColumn('area', $area);
$stmt->fetch(PDO::FETCH_BOUND);

$query = "SELECT pf.idArea, a.area, e.nombre AS equipo, e.idEquipo, e.periodicidad
FROM programaciondefechas pf 
LEFT JOIN areas a ON a.idArea = pf.idArea
LEFT JOIN equipos e ON e.idEquipo = pf.idEquipo
WHERE pf.idArea = :idArea
GROUP BY equipo 
ORDER BY a.area ASC";
$datos = $con->prepare($query);
$datos->bindParam(':idArea', $idArea);
$datos->execute();

$arrayCrono = array();
while ($row = $datos->fetch()) {
    $cronograma = new stdClass();
    $cronograma->periodicidad = $row["periodicidad"];
    $cronograma->area = $row["area"];
    $cronograma->idArea = $row["idArea"];
    $cronograma->equipo = $row["equipo"];
    $cronograma->idEquipo = $row["idEquipo"];

    switch ($cronograma->periodicidad) {
        case '0':
            $cronograma->periodicidad = "Aún no asignada";
            break;
        case '1':
            $cronograma->periodicidad = "Mensual";
            break;
        case '2':
            $cronograma->periodicidad = "Trimestral";
            break;
        case '3':
            $cronograma->periodicidad = "Semestral";
            break;
        case '4':
            $cronograma->periodicidad = "Anual";
            break;
    }

    $sqlFechas = "SELECT pf.fechaProgramada, pf.idMes
FROM programaciondefechas pf 
WHERE pf.idEquipo = :idEquipo
ORDER BY pf.idMes ASC";
    $datosMdl = $con->prepare($sqlFechas);
    $datosMdl->bindParam(':idEquipo', $cronograma->idEquipo);
    $datosMdl->execute();

    if ($datosMdl == false) {
        echo mysql_error();
    } else {
        while ($rsModulos = $datosMdl->fetch()) {
            $datosFechas = new stdClass();
            $datosFechas->fechaProgramada = $rsModulos["fechaProgramada"];
            $datosFechas->idMes = $rsModulos["idMes"];

            $sqlReal = "SELECT fechaReal FROM controlmantenimiento WHERE idEquipo = :idEquipo AND idMes = :idMes AND tipo != '1'";
            $rel = $con->prepare($sqlReal);
            $rel->bindParam(':idEquipo', $cronograma->idEquipo);
            $rel->bindParam(':idMes', $datosFechas->idMes);
            $rel->execute();
            while ($rsModulos = $rel->fetch()) {
                $datosFechas->fechaReal = $rsModulos["fechaReal"];
            }
            $cronograma->listaFechas[] = $datosFechas;
        }

        $sqlFechasE = "SELECT cm.fechaReal AS extraordinaria, cm.idMes AS mes
                               FROM controlmantenimiento cm 
                               WHERE cm.idEquipo = :idEquipo AND cm.tipo = '1'
                               ORDER BY cm.idMes ASC";
        $ext = $con->prepare($sqlFechasE);
        $ext->bindParam(':idEquipo', $cronograma->idEquipo);
        $ext->execute();
        if ($ext->rowCount() >= 1) {
            while ($rsModulos = $ext->fetch()) {
                $extra = new stdClass();
                $extra->extraordinaria = $rsModulos["extraordinaria"];
                $extra->mes = $rsModulos["mes"];

                $cronograma->extraordinarias[] = $extra;
            }
        } else {
            $cronograma->extraordinarias = [];
        }


        foreach ($cronograma->listaFechas as $listaFec) {
            if (!property_exists($listaFec, 'fechaReal')) {
                $listaFec->fechaReal = "";
            }
            if (!property_exists($listaFec, 'fechaProgramada')) {
                $listaFec->fechaProgramada = "";
            }
        }
        $arrayCrono[] = $cronograma;
    }
}
#Meses y fechas

foreach ($arrayCrono as $res) {
    $res->fechas = array();
    $res->fechas['enero'] = array('p' => '', 'r' => '', 'e' => '');
    $res->fechas['febrero'] = array('p' => '', 'r' => '', 'e' => '');
    $res->fechas['marzo'] = array('p' => '', 'r' => '', 'e' => '');
    $res->fechas['abril'] = array('p' => '', 'r' => '', 'e' => '');
    $res->fechas['mayo'] = array('p' => '', 'r' => '', 'e' => '');
    $res->fechas['junio'] = array('p' => '', 'r' => '', 'e' => '');
    $res->fechas['julio'] = array('p' => '', 'r' => '', 'e' => '');
    $res->fechas['agosto'] = array('p' => '', 'r' => '', 'e' => '');
    $res->fechas['septiembre'] = array('p' => '', 'r' => '', 'e' => '');
    $res->fechas['octubre'] = array('p' => '', 'r' => '', 'e' => '');
    $res->fechas['noviembre'] = array('p' => '', 'r' => '', 'e' => '');
    $res->fechas['diciembre'] = array('p' => '', 'r' => '', 'e' => '');
    foreach ($res->listaFechas as $fecha) {
        switch ($fecha->idMes) {
            case '1':
                $enero = array('p' => $fecha->fechaProgramada,
                    'r' => $fecha->fechaReal, 'e' => '');
                $res->fechas['enero'] = $enero;
                break;
            case '2':
                $febrero = array('p' => $fecha->fechaProgramada,
                    'r' => $fecha->fechaReal, 'e' => '');
                $res->fechas['febrero'] = $febrero;
                break;
            case '3':
                $marzo = array('p' => $fecha->fechaProgramada,
                    'r' => $fecha->fechaReal, 'e' => '');
                $res->fechas['marzo'] = $marzo;
                break;
            case '4':
                $abril = array('p' => $fecha->fechaProgramada,
                    'r' => $fecha->fechaReal, 'e' => '');
                $res->fechas['abril'] = $abril;
                break;
            case '5':
                $mayo = array('p' => $fecha->fechaProgramada,
                    'r' => $fecha->fechaReal, 'e' => '');
                $res->fechas['mayo'] = $mayo;
                break;
            case '6':
                $junio = array('p' => $fecha->fechaProgramada,
                    'r' => $fecha->fechaReal, 'e' => '');
                $res->fechas['junio'] = $junio;
                break;
            case '7':
                $julio = array('p' => $fecha->fechaProgramada,
                    'r' => $fecha->fechaReal, 'e' => '');
                $res->fechas['julio'] = $julio;
                break;
            case '8':
                $agosto = array('p' => $fecha->fechaProgramada,
                    'r' => $fecha->fechaReal, 'e' => '');
                $res->fechas['agosto'] = $agosto;
                break;
            case '9':
                $septiembre = array('p' => $fecha->fechaProgramada,
                    'r' => $fecha->fechaReal, 'e' => '');
                $res->fechas['septiembre'] = $septiembre;
                break;
            case '10':
                $octubre = array('p' => $fecha->fechaProgramada,
                    'r' => $fecha->fechaReal, 'e' => '');
                $res->fechas['octubre'] = $octubre;
                break;
            case '11':
                $noviembre = array('p' => $fecha->fechaProgramada,
                    'r' => $fecha->fechaReal, 'e' => '');
                $res->fechas['noviembre'] = $noviembre;
                break;
            case '12':
                $diciembre = array('p' => $fecha->fechaProgramada,
                    'r' => $fecha->fechaReal, 'e' => '');
                $res->fechas['diciembre'] = $diciembre;
                break;
        }
    }

    #Manejo de las extraordinarias
    foreach ($res->extraordinarias as $extra) {
        switch ($extra->mes) {
            case '1':
                $res->fechas['enero']['e'] = $extra->extraordinaria;
                break;
            case '2':
                $res->fechas['febrero']['e'] = $extra->extraordinaria;
                break;
            case '3':
                $res->fechas['marzo']['e'] = $extra->extraordinaria;
                break;
            case '4':
                $res->fechas['abril']['e'] = $extra->extraordinaria;
                break;
            case '5':
                $res->fechas['mayo']['e'] = $extra->extraordinaria;
                break;
            case '6':
                $res->fechas['junio']['e'] = $extra->extraordinaria;
                break;
            case '7':
                $res->fechas['julio']['e'] = $extra->extraordinaria;
                break;
            case '8':
                $res->fechas['agosto']['e'] = $extra->extraordinaria;
                break;
            case '9':
                $res->fechas['septiembre']['e'] = $extra->extraordinaria;
                break;
            case '10':
                $res->fechas['octubre']['e'] = $extra->extraordinaria;
                break;
            case '11':
                $res->fechas['noviembre']['e'] = $extra->extraordinaria;
                break;
            case '12':
                $res->fechas['diciembre']['e'] = $extra->extraordinaria;
                break;
        }
    }
}

# / meses y fechas

class PDF extends FPDF {

    function Header() {
        $this->SetFont('Arial', 'B', 15);
        $this->Cell(80);
        $this->Cell(100, 10, utf8_decode('CRONOGRAMA DE MANTENIMIENTO 2017. CÓDIGO: ' . 'RST-M-01'), 0, 1, 'C');
        $this->setXY(100, 20);
        $this->SetFont('Arial', '', 13);
        $this->Cell(100, 10, utf8_decode("Área: " . $GLOBALS['area']), 0, 0, 'C');
        $this->SetFont('Arial', '', 10);
        $this->setXY(-50, 10);
        $this->Cell(20, 10, utf8_decode('Página ' . $this->pageNo()), 0, 0, 'C');
        $this->Ln();
    }

}

// Creación del objeto de la clase heredada
$pdf = new PDF('L', 'mm', 'A4');
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->SetAutoPageBreak(true, 20);
$pdf->SetFont('Arial', '', 12);

//Encabezado de la tabla
$pdf->SetFillColor(127, 140, 141);
$pdf->SetTextColor(255, 255, 255);

$pdf->setXY(10, 30);
$pdf->Cell(80, 10, "Equipo", 1, 0, 'C', 1);
$pdf->setXY(90, 30);
$pdf->Cell(150, 5, "Meses", 1, 0, 'C', 1);
$pdf->setXY(90, 35);
$pdf->Cell(25, 5, "Enero", 1, 0, 'C', 1);
$pdf->Cell(25, 5, "Febrero", 1, 0, 'C', 1);
$pdf->Cell(25, 5, "Marzo", 1, 0, 'C', 1);
$pdf->Cell(25, 5, "Abril", 1, 0, 'C', 1);
$pdf->Cell(25, 5, "Mayo", 1, 0, 'C', 1);
$pdf->Cell(25, 5, "Junio", 1, 0, 'C', 1);
$pdf->setXY(240, 30);
$pdf->Cell(35, 10, "Periodicidad", 1, 0, 'C', 1);
$pdf->SetTextColor(0, 0, 0);

//Contenido de la tabla
$x = 10;
$y = 40;

$pdf->SetFont('Arial', '', 9);
foreach ($arrayCrono as $res) {

    $pdf->setXY($x, $y);

    $pdf->Cell(80, 15, $res->equipo, 1, 0, 'C');
    $pdf->SetFillColor(241, 196, 15);
    $pdf->setXY(85, 0 + $y);
    $pdf->Cell(5, 5, 'P', 1, 0, 'C', 1);
    $pdf->setXY(85, 5 + $y);
    $pdf->SetFillColor(39, 174, 96);
    $pdf->SetTextColor(255, 255, 255);
    $pdf->Cell(5, 5, 'R', 1, 0, 'C', 1);
    $pdf->setXY(85, 10 + $y);
    $pdf->SetFillColor(231, 76, 60);
    $pdf->SetTextColor(255, 255, 255);
    $pdf->Cell(5, 5, 'E', 1, 0, 'C', 1);
    $pdf->SetTextColor(0, 0, 0);
    #ENERO
    $pdf->setXY(90, 0 + $y);
    $pdf->Cell(25, 5, $res->fechas['enero']['p'], 1, 0, 'C');
    $pdf->setXY(90, 5 + $y);
    $pdf->Cell(25, 5, $res->fechas['enero']['r'], 1, 0, 'C');
    $pdf->setXY(90, 10 + $y);
    $pdf->Cell(25, 5, $res->fechas['enero']['e'], 1, 0, 'C');

    #FEBRERO
    $pdf->setXY(115, 0 + $y);
    $pdf->Cell(25, 5, $res->fechas['febrero']['p'], 1, 0, 'C');
    $pdf->setXY(115, 5 + $y);
    $pdf->Cell(25, 5, $res->fechas['febrero']['r'], 1, 0, 'C');
    $pdf->setXY(115, 10 + $y);
    $pdf->Cell(25, 5, $res->fechas['febrero']['e'], 1, 0, 'C');

    #MARZO
    $pdf->setXY(140, 0 + $y);
    $pdf->Cell(25, 5, $res->fechas['marzo']['p'], 1, 0, 'C');
    $pdf->setXY(140, 5 + $y);
    $pdf->Cell(25, 5, $res->fechas['marzo']['r'], 1, 0, 'C');
    $pdf->setXY(140, 10 + $y);
    $pdf->Cell(25, 5, $res->fechas['marzo']['e'], 1, 0, 'C');

    #ABRIL
    $pdf->setXY(165, 0 + $y);
    $pdf->Cell(25, 5, $res->fechas['abril']['p'], 1, 0, 'C');
    $pdf->setXY(165, 5 + $y);
    $pdf->Cell(25, 5, $res->fechas['abril']['r'], 1, 0, 'C');
    $pdf->setXY(165, 10 + $y);
    $pdf->Cell(25, 5, $res->fechas['abril']['e'], 1, 0, 'C');

    #MAYO
    $pdf->setXY(190, 0 + $y);
    $pdf->Cell(25, 5, $res->fechas['mayo']['p'], 1, 0, 'C');
    $pdf->setXY(190, 5 + $y);
    $pdf->Cell(25, 5, $res->fechas['mayo']['r'], 1, 0, 'C');
    $pdf->setXY(190, 10 + $y);
    $pdf->Cell(25, 5, $res->fechas['mayo']['e'], 1, 0, 'C');

    #JUNIO
    $pdf->setXY(215, 0 + $y);
    $pdf->Cell(25, 5, $res->fechas['junio']['p'], 1, 0, 'C');
    $pdf->setXY(215, 5 + $y);
    $pdf->Cell(25, 5, $res->fechas['junio']['r'], 1, 0, 'C');
    $pdf->setXY(215, 10 + $y);
    $pdf->Cell(25, 5, $res->fechas['junio']['e'], 1, 0, 'C');
    $pdf->setXY(240, $y);
    $pdf->Cell(35, 15, $res->periodicidad, 1, 0, 'C');
    $x = 10;
    $y = $y + 15;
};
$pdf->Ln(20);
$lastYValue = $pdf->getY();
$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(20, 5, utf8_decode("Código: "), 0, 0, 'L');
$pdf->SetFillColor(241, 196, 15);
$pdf->Cell(5, 5, 'P', 0, 0, 'C', 1);
$pdf->Cell(20, 5, 'Programado', 0, 0, 'L');
$pdf->Ln();

$pdf->setX(30);
$pdf->SetFillColor(192, 57, 43);
$pdf->SetTextColor(255, 255, 255);
$pdf->Cell(5, 5, 'E', 0, 0, 'C', 1);
$pdf->SetTextColor(0, 0, 0);
$pdf->Cell(20, 5, 'Extraordinario', 0, 0, 'L');
$pdf->Ln();


$pdf->setX(30);
$pdf->SetFillColor(39, 174, 96);
$pdf->SetTextColor(255, 255, 255);
$pdf->Cell(5, 5, 'R', 0, 0, 'C', 1);
$pdf->SetTextColor(0, 0, 0);
$pdf->Cell(20, 5, 'Real', 0, 0, 'L');
$pdf->Ln();


$pdf->setXY(185, $lastYValue);
$pdf->SetFillColor(127, 140, 141);
$pdf->SetTextColor(255, 255, 255);
$pdf->Cell(45, 5, utf8_decode("Elaboró"), 1, 0, 'C', 1);
$pdf->Cell(45, 5, utf8_decode("Revisó/Autorizó"), 1, 0, 'C', 1);

$pdf->setXY(185, $lastYValue + 5);
$pdf->SetFont('Arial', '', 9);
$pdf->SetFillColor(255, 255, 255);
$pdf->SetTextColor(0, 0, 0);
$pdf->MultiCell(45, 4, utf8_decode("Jefe de Aseguramiento de \n Calidad"), 1, 'C');
$pdf->setXY(230, $lastYValue + 5);
$pdf->MultiCell(45, 4, utf8_decode("Gerente de Planta \n "), 1, 'C');

$pdf->setXY(185, $pdf->getY());
$pdf->Cell(45, 5, "Puesto y firma", 1, 0, 'C');
$pdf->Cell(45, 5, "Puesto y firma", 1, 0, 'C');






#SEGUNDO SEMESTRE

$pdf->AddPage();
$pdf->SetFont('Arial', '', 12);

//Encabezado de la tabla
$pdf->SetFillColor(127, 140, 141);
$pdf->SetTextColor(255, 255, 255);
$pdf->setXY(10, 30);
$pdf->Cell(80, 10, "Equipo", 1, 0, 'C', 1);
$pdf->setXY(90, 30);
$pdf->Cell(150, 5, "Meses", 1, 0, 'C', 1);
$pdf->setXY(90, 35);
$pdf->Cell(25, 5, "Julio", 1, 0, 'C', 1);
$pdf->Cell(25, 5, "Agosto", 1, 0, 'C', 1);
$pdf->Cell(25, 5, "Septiembre", 1, 0, 'C', 1);
$pdf->Cell(25, 5, "Octubre", 1, 0, 'C', 1);
$pdf->Cell(25, 5, "Noviembre", 1, 0, 'C', 1);
$pdf->Cell(25, 5, "Diciembre", 1, 0, 'C', 1);
$pdf->setXY(240, 30);
$pdf->Cell(35, 10, "Periodicidad", 1, 0, 'C', 1);
$pdf->SetTextColor(0, 0, 0);

//Contenido de la tabla
$x = 10;
$y = 40;

$pdf->SetFont('Arial', '', 9);

foreach ($arrayCrono as $res) {
    $pdf->setXY($x, $y);
    $pdf->Cell(80, 15, $res->equipo, 1, 0, 'C');
    $pdf->SetFillColor(241, 196, 15);
    $pdf->setXY(85, 0 + $y);
    $pdf->Cell(5, 5, 'P', 1, 0, 'C', 1);
    $pdf->setXY(85, 5 + $y);
    $pdf->SetFillColor(39, 174, 96);
    $pdf->SetTextColor(255, 255, 255);
    $pdf->Cell(5, 5, 'R', 1, 0, 'C', 1);

    $pdf->setXY(85, 10 + $y);
    $pdf->SetFillColor(231, 76, 60);
    $pdf->SetTextColor(255, 255, 255);
    $pdf->Cell(5, 5, 'E', 1, 0, 'C', 1);
    $pdf->SetTextColor(0, 0, 0);
    #ENERO
    $pdf->setXY(90, 0 + $y);
    $pdf->Cell(25, 5, $res->fechas['julio']['p'], 1, 0, 'C');
    $pdf->setXY(90, 5 + $y);
    $pdf->Cell(25, 5, $res->fechas['julio']['r'], 1, 0, 'C');
    $pdf->setXY(90, 10 + $y);
    $pdf->Cell(25, 5, $res->fechas['julio']['e'], 1, 0, 'C');

    #FEBRERO
    $pdf->setXY(115, 0 + $y);
    $pdf->Cell(25, 5, $res->fechas['agosto']['p'], 1, 0, 'C');
    $pdf->setXY(115, 5 + $y);
    $pdf->Cell(25, 5, $res->fechas['agosto']['r'], 1, 0, 'C');
    $pdf->setXY(115, 10 + $y);
    $pdf->Cell(25, 5, $res->fechas['agosto']['e'], 1, 0, 'C');

    #MARZO
    $pdf->setXY(140, 0 + $y);
    $pdf->Cell(25, 5, $res->fechas['septiembre']['p'], 1, 0, 'C');
    $pdf->setXY(140, 5 + $y);
    $pdf->Cell(25, 5, $res->fechas['septiembre']['r'], 1, 0, 'C');
    $pdf->setXY(140, 10 + $y);
    $pdf->Cell(25, 5, $res->fechas['septiembre']['e'], 1, 0, 'C');

    #ABRIL
    $pdf->setXY(165, 0 + $y);
    $pdf->Cell(25, 5, $res->fechas['octubre']['p'], 1, 0, 'C');
    $pdf->setXY(165, 5 + $y);
    $pdf->Cell(25, 5, $res->fechas['octubre']['r'], 1, 0, 'C');
    $pdf->setXY(165, 10 + $y);
    $pdf->Cell(25, 5, $res->fechas['octubre']['e'], 1, 0, 'C');

    #MAYO
    $pdf->setXY(190, 0 + $y);
    $pdf->Cell(25, 5, $res->fechas['noviembre']['p'], 1, 0, 'C');
    $pdf->setXY(190, 5 + $y);
    $pdf->Cell(25, 5, $res->fechas['noviembre']['r'], 1, 0, 'C');
    $pdf->setXY(190, 10 + $y);
    $pdf->Cell(25, 5, $res->fechas['noviembre']['e'], 1, 0, 'C');
    #JUNIO
    $pdf->setXY(215, 0 + $y);
    $pdf->Cell(25, 5, $res->fechas['diciembre']['p'], 1, 0, 'C');
    $pdf->setXY(215, 5 + $y);
    $pdf->Cell(25, 5, $res->fechas['diciembre']['r'], 1, 0, 'C');
    $pdf->setXY(215, 10 + $y);
    $pdf->Cell(25, 5, $res->fechas['diciembre']['e'], 1, 0, 'C');
    $pdf->setXY(240, $y);
    $pdf->Cell(35, 15, $res->periodicidad, 1, 0, 'C');
    $x = 10;
    $y = $y + 15;
};

$pdf->Ln(20);
$lastYValue = $pdf->getY();
$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(20, 5, utf8_decode("Código: "), 0, 0, 'L');
$pdf->SetFillColor(241, 196, 15);
$pdf->Cell(5, 5, 'P', 0, 0, 'C', 1);
$pdf->Cell(20, 5, 'Programado', 0, 0, 'L');
$pdf->Ln();

$pdf->setX(30);
$pdf->SetFillColor(192, 57, 43);
$pdf->SetTextColor(255, 255, 255);
$pdf->Cell(5, 5, 'E', 0, 0, 'C', 1);
$pdf->SetTextColor(0, 0, 0);
$pdf->Cell(20, 5, 'Extraordinario', 0, 0, 'L');
$pdf->Ln();


$pdf->setX(30);
$pdf->SetFillColor(39, 174, 96);
$pdf->SetTextColor(255, 255, 255);
$pdf->Cell(5, 5, 'R', 0, 0, 'C', 1);
$pdf->SetTextColor(0, 0, 0);
$pdf->Cell(20, 5, 'Real', 0, 0, 'L');
$pdf->Ln();



$pdf->setXY(185, $lastYValue);
$pdf->SetFillColor(127, 140, 141);
$pdf->SetTextColor(255, 255, 255);
$pdf->Cell(45, 5, utf8_decode("Elaboró"), 1, 0, 'C', 1);
$pdf->Cell(45, 5, utf8_decode("Revisó/Autorizó"), 1, 0, 'C', 1);

$pdf->setXY(185, $lastYValue + 5);
$pdf->SetFont('Arial', '', 9);
$pdf->SetFillColor(255, 255, 255);
$pdf->SetTextColor(0, 0, 0);
$pdf->MultiCell(45, 4, utf8_decode("Jefe de Aseguramiento de \n Calidad"), 1, 'C');
$pdf->setXY(230, $lastYValue + 5);
$pdf->MultiCell(45, 4, utf8_decode("Gerente de Planta \n "), 1, 'C');

$pdf->setXY(185, $pdf->getY());
$pdf->Cell(45, 5, "Puesto y firma", 1, 0, 'C');
$pdf->Cell(45, 5, "Puesto y firma", 1, 0, 'C');


#echo json_encode($arrayCrono);
$pdf->Output('', 'cronograma.pdf');
