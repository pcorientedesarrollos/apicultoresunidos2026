<?php

//header("Content-Type: application/json");
//#header("Content-type:application/pdf");
//#header("Content-Disposition:attachment;filename='downloaded.pdf'");

function balanceContableArea($balance, $area, $granTotal) {

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
//    $pdf->cell(100, 10, 'Balance contable de activos fijos clasificados como ' . utf8_decode($clasificacion), 0, 1, 'C', 0);
    $pdf->cell(100, 10, 'Balance contable de activos fijos', 0, 1, 'C', 0);
    $pdf->SetFont('Arial', 'B', 10);
    $pdf->setXY(100, 22);
    $pdf->cell(100, 5, utf8_decode($area), 0, 1, 'C', 0);
    #Inicio de la tabla

    $x = 10;
    $y = 30;

    $pdf->setXY($x, $y);
    $pdf->SetFillColor(56, 84, 40);
    $pdf->SetTextColor(255, 255, 255);
    $pdf->SetFont('Arial', 'B', 10);
    $pdf->cell(17, 5, utf8_decode('CÓDIGO'), 1, 0, 'C', 1);
    $pdf->cell(40, 5, utf8_decode('NOMBRE'), 1, 0, 'C', 1);
    $pdf->cell(25, 5, utf8_decode('MARCA'), 1, 0, 'C', 1);
    $pdf->cell(25, 5, utf8_decode('MODELO'), 1, 0, 'C', 1);
    $pdf->cell(25, 5, utf8_decode('SERIE'), 1, 0, 'C', 1);
    $pdf->cell(100, 5, utf8_decode('DESCRIPCIÓN'), 1, 0, 'C', 1);
    $pdf->cell(33, 5, utf8_decode('SUBÁREA'), 1, 0, 'C', 1);
    $pdf->cell(17, 5, utf8_decode('COSTO'), 1, 0, 'C', 1);
    $pdf->setXY($x, $y + 5);
    $pdf->SetTextColor(0, 0, 0);
    $pdf->SetFont('Arial', '', 7);
    foreach ($balance as $inv) {
        $pdf->cell(17, 4, 'AF-MID-' . utf8_decode($inv['idEquipo']), 1, 0, 'L', 0);
        $pdf->cell(40, 4, strtoupper(utf8_decode($inv['equipo'])), 1, 0, 'L', 0);
        $pdf->cell(25, 4, strtoupper(utf8_decode($inv['marca'])), 1, 0, 'L', 0);
        $pdf->cell(25, 4, strtoupper(utf8_decode($inv['modelo'])), 1, 0, 'L', 0);
        $pdf->cell(25, 4, strtoupper(utf8_decode($inv['noSerie'])), 1, 0, 'L', 0);
        $pdf->cell(100, 4, strtoupper(utf8_decode($inv['caracteristicas'])), 1, 0, 'L', 0);
        $pdf->cell(33, 4, strtoupper(utf8_decode($inv['zona'])), 1, 0, 'L', 0);
        $pdf->cell(17, 4, "$" . number_format($inv['costo'], 2, '.', ','), 1, 1, 'R', 0);
    }
    $pdf->setXY(242, $pdf->getY());
    $pdf->SetFont('Arial', 'B');
    $pdf->Cell(33, 5, "TOTAL", 1, 0, 'C');
    $pdf->Cell(17, 5, "$" . number_format($granTotal, 2, '.', ','), 1, 0, 'R');

    $pdf->Output('I', 'Balance contable de activos fijos por área.pdf');
}

function balancePorClasificacion($balance, $clasificacion, $granTotal) {

    class PDFC extends FPDF {

        function Footer() {
            $this->SetY(-10);
            $this->SetFont('Arial', 'I', 8);
            $this->AliasNbPages('nb');
            $this->Cell(0, 10, utf8_decode('Página ' . $this->PageNo() . '/nb'), 0, 0, 'C');
        }

    }

    #Encabezado

    $pdf = new PDFC('L', 'mm', 'A4');
    $pdf->SetFont('Arial', 'B', 12);
    $pdf->AddPage();
    $pdf->Image('../../images/LOGO.png', 10, 7, 20, 20);
    $pdf->setXY(100, 10);
    $pdf->cell(100, 5, 'Oaxaca Miel S.A. de C.V.', 0, 1, 'C', 0);
    $pdf->SetFont('Arial', '', 10);
    $pdf->setXY(100, 15);
//    $pdf->cell(100, 10, 'Balance contable de activos fijos clasificados como ' . utf8_decode($clasificacion), 0, 1, 'C', 0);
    $pdf->cell(100, 10, 'Balance contable de activos fijos', 0, 1, 'C', 0);
    $pdf->SetFont('Arial', 'B', 10);
    $pdf->setXY(100, 22);
    $pdf->cell(100, 5, utf8_decode($clasificacion), 0, 1, 'C', 0);
    #Inicio de la tabla

    $x = 10;
    $y = 30;

    $pdf->setXY($x, $y);
    $pdf->SetFillColor(56, 84, 40);
    $pdf->SetTextColor(255, 255, 255);
    $pdf->SetFont('Arial', 'B', 10);
    $pdf->cell(17, 5, utf8_decode('CÓDIGO'), 1, 0, 'C', 1);
    $pdf->cell(40, 5, utf8_decode('NOMBRE'), 1, 0, 'C', 1);
    $pdf->cell(25, 5, utf8_decode('MARCA'), 1, 0, 'C', 1);
    $pdf->cell(25, 5, utf8_decode('MODELO'), 1, 0, 'C', 1);
    $pdf->cell(25, 5, utf8_decode('SERIE'), 1, 0, 'C', 1);
    $pdf->cell(100, 5, utf8_decode('DESCRIPCIÓN'), 1, 0, 'C', 1);
    $pdf->cell(33, 5, utf8_decode('ÁREA'), 1, 0, 'C', 1);
    $pdf->cell(17, 5, utf8_decode('COSTO'), 1, 0, 'C', 1);
    $pdf->setXY($x, $y + 5);
    $pdf->SetTextColor(0, 0, 0);
    $pdf->SetFont('Arial', '', 7);
    foreach ($balance as $inv) {
        $pdf->cell(17, 4, 'AF-MID-' . utf8_decode($inv['idEquipo']), 1, 0, 'L', 0);
        $pdf->cell(40, 4, strtoupper(utf8_decode($inv['equipo'])), 1, 0, 'L', 0);
        $pdf->cell(25, 4, strtoupper(utf8_decode($inv['marca'])), 1, 0, 'L', 0);
        $pdf->cell(25, 4, strtoupper(utf8_decode($inv['modelo'])), 1, 0, 'L', 0);
        $pdf->cell(25, 4, strtoupper(utf8_decode($inv['noSerie'])), 1, 0, 'L', 0);
        $pdf->cell(100, 4, strtoupper(utf8_decode($inv['caracteristicas'])), 1, 0, 'L', 0);
        $pdf->cell(33, 4, strtoupper(utf8_decode($inv['area'])), 1, 0, 'L', 0);
        $pdf->cell(17, 4, "$" . number_format($inv['costo'], 2, '.', ','), 1, 1, 'R', 0);
    }

    $pdf->setXY(242, $pdf->getY());
    $pdf->SetFont('Arial', 'B');
    $pdf->Cell(33, 5, "TOTAL", 1, 0, 'C');
    $pdf->Cell(17, 5, "$" . number_format($granTotal, 2, '.', ','), 1, 0, 'R');

    $pdf->Output('I', 'Balance contable de activos fijos por clasificacion.pdf');
}

function todoBalance($balance, $granTotal) {

    class PDFT extends FPDF {

        function Footer() {
            $this->SetY(-10);
            $this->SetFont('Arial', 'I', 8);
            $this->AliasNbPages('nb');
            $this->Cell(0, 10, utf8_decode('Página ' . $this->PageNo() . '/nb'), 0, 0, 'C');
        }

    }

    #Encabezado

    $pdf = new PDFT('P', 'mm', 'A4');
    $pdf->SetFont('Arial', 'B', 14);
    $pdf->AddPage();
    $pdf->Image('../../images/LOGO.png', 55, 7, 20, 20);
    $pdf->setXY(65, 10);
    $pdf->cell(100, 5, 'Oaxaca Miel S.A. de C.V. ', 0, 1, 'C', 0);
    $pdf->SetFont('Arial', '', 10);
    $pdf->setXY(65, 17);
    $pdf->cell(100, 5, 'Balance contable de activos fijos', 0, 1, 'C', 0);
    $pdf->SetXY(65, 22);
    $pdf->SetFont('Arial', 'B', 10);
    $pdf->cell(100, 5, 'Concentrado', 0, 1, 'C', 0);

    #Inicio de la tabla

    $x = 44;
    $y = 35;

    $pdf->setXY($x, $y);
    $pdf->SetFillColor(56, 84, 40);
    $pdf->SetTextColor(255, 255, 255);
    $pdf->SetFont('Arial', 'B', 10);
    $pdf->cell(100, 5, utf8_decode('CLASIFICACIÓN'), 1, 0, 'C', 1);
    $pdf->cell(20, 5, utf8_decode('COSTO'), 1, 0, 'C', 1);

    $pdf->SetY($y + 5);
    $pdf->SetTextColor(0, 0, 0);
    foreach ($balance as $inv) {
        $pdf->SetX($x);
        $pdf->SetFont('Arial', '', 7);
        $pdf->cell(100, 4, utf8_decode(strtoupper($inv['clasificacion'])), 1, 0, 'L', 0);
        $pdf->cell(20, 4, "$" . number_format($inv['costoTotal'], 2, '.', ','), 1, 1, 'R', 0);
    }

    $pdf->setXY(114, $pdf->getY());
    $pdf->SetFont('Arial', 'B');
    $pdf->Cell(30, 5, "TOTAL", 1, 0, 'C');
    $pdf->Cell(20, 5, "$" . number_format($granTotal, 2, '.', ','), 1, 0, 'R');

    $pdf->Output('I', 'Balance contable de activos fijos.pdf');
}

function balance($type, $id = NULL) {
    if (isset($type) || $type != NULL) {

        require('../../fpdf/FPDF/fpdf.php');
        include_once '../../DAOConeccion/conePDO.php';
        $pdo = new conePDO();
        $con = $pdo->conectar();

        if ($type === 1) {
            $query = $con->prepare("SELECT eq.idEquipo, LEFT(eq.nombre,25) AS equipo, LEFT(eq.caracteristicas, 64) AS caracteristicas, 
                eq.marca, eq.modelo, eq.noSerie, cl.clasificacion, eq.costo,
                LEFT((CONCAT(s.subarea,' - ',s.nombre)),22) AS zona
                FROM equipos eq 
                LEFT JOIN clasificaciones cl
                ON eq.idClasificacion = cl.idClasificacion
                LEFT JOIN subareas s ON s.idSubarea = eq.idSubarea
                WHERE eq.idArea = :idArea
                ORDER BY eq.idEquipo ASC");
            $query->bindParam(':idArea', $id);
            $query->execute();
            $balance = $query->fetchAll(PDO::FETCH_ASSOC);
            $query = $con->prepare("SELECT area FROM areas WHERE idArea = :idArea");
            $query->bindParam(':idArea', $id);
            $query->execute();
            $query->bindColumn('area', $area);
            $query->fetch(PDO::FETCH_BOUND);
            $query = $con->prepare("SELECT SUM(costo) AS granTotal FROM equipos WHERE idArea = :idArea");
            $query->bindParam(':idArea', $id);
            $query->execute();
            $query->bindColumn('granTotal', $granTotal);
            $query->fetch(PDO::FETCH_BOUND);
            balanceContableArea($balance, $area, $granTotal);
        } else if ($type === 2) {
            $query = $con->prepare("SELECT eq.idEquipo, LEFT(eq.nombre,25) AS equipo, LEFT(eq.caracteristicas, 64) AS caracteristicas, 
                eq.marca, eq.modelo, eq.noSerie, cl.clasificacion, eq.costo,
                LEFT((CONCAT(s.subarea,' - ',s.nombre)),22) AS zona, a.area
                FROM equipos eq 
                LEFT JOIN clasificaciones cl
                ON eq.idClasificacion = cl.idClasificacion
                LEFT JOIN subareas s ON s.idSubarea = eq.idSubarea
                LEFT JOIN areas a ON a.idArea = eq.idArea
                WHERE cl.idClasificacion = :idClasificacion
                ORDER BY eq.idEquipo ASC");
            $query->bindParam(':idClasificacion', $id);
            $query->execute();
            $balance = $query->fetchAll(PDO::FETCH_ASSOC);
            $query = $con->prepare("SELECT clasificacion FROM clasificaciones WHERE idClasificacion = :idClasificacion");
            $query->bindParam(':idClasificacion', $id);
            $query->execute();
            $query->bindColumn('clasificacion', $clasificacion);
            $query->fetch(PDO::FETCH_BOUND);
            $query = $con->prepare("SELECT SUM(costo) AS granTotal FROM equipos WHERE idClasificacion = :idClasificacion");
            $query->bindParam(':idClasificacion', $id);
            $query->execute();
            $query->bindColumn('granTotal', $granTotal);
            $query->fetch(PDO::FETCH_BOUND);
            balancePorClasificacion($balance, $clasificacion, $granTotal);
        } else if ($type === 3) {
            $query = $con->prepare("SELECT c.clasificacion, SUM(e.costo) AS costoTotal
                FROM equipos e
                LEFT JOIN clasificaciones c ON c.idClasificacion = e.idClasificacion 
                GROUP BY e.idClasificacion
                ORDER BY c.clasificacion ASC");
            $query->execute();
            $balance = $query->fetchAll(PDO::FETCH_ASSOC);
            $query = $con->prepare("SELECT SUM(costo) AS granTotal FROM equipos");
            $query->execute();
            $query->bindColumn('granTotal', $granTotal);
            $query->fetch(PDO::FETCH_BOUND);
            todoBalance($balance, $granTotal);
        } else {
            exit();
        }
    } else {
        die;
    }
}

if (isset($_GET['opcion'])) {
    switch ($_GET['opcion']):
        case 'area':
            balance(1, $_GET['idArea']);
            break;
        case 'clasificacion':
            balance(2, $_GET['idClasificacion']);
            break;
        case 'todo':
            balance(3);
            break;
    endswitch;
} else {
    die;
}

//$post = file_get_contents('php://input');
//if ($post) {
//    $request = json_decode($post);
//    switch ($request['opcion']):
//        case 'area':
//            balance(1, $request['idArea']);
//            break;
//        case 'clasificacion':
//            balance(2, $request['idClasificacion']);
//            break;
//        case 'todo':
//            balance(3);
//            break;
//    endswitch;
//} else {
//    die;
//}