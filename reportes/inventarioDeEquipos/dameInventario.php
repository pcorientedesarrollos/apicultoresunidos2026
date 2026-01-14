<?php

header("Content-Type: application/json");
#header("Content-type:application/pdf");
#header("Content-Disposition:attachment;filename='downloaded.pdf'");

function inventarioPorArea($inventario, $area) {

    class PDF extends FPDF {

        function Footer() {
            $this->SetY(-10);
            $this->SetFont('Arial', 'I', 8);
            $this->AliasNbPages('nb');
            $this->Cell(0, 10, utf8_decode('Página ' . $this->PageNo() . '/nb'), 0, 0, 'C');
        }

    }

    #Encabezado

    $pdf = new PDF('P', 'mm', 'A4');
    $pdf->SetFont('Arial', 'B', 12);
    $pdf->AddPage();
    $pdf->Image('../../images/LOGO.png', 10, 7, 20, 20);
    $pdf->setXY(70, 10);
    $pdf->cell(70, 5, 'Oaxaca Miel', 0, 1, 'C', 0);
    $pdf->SetFont('Arial', '', 10);
    $pdf->setXY(70, 17);
    $pdf->cell(70, 5, 'Inventario de los equipos del area ' . utf8_decode($area), 0, 1, 'C', 0);

    #Inicio de la tabla

    $x = 10;
    $y = 30;

    $pdf->setXY($x, $y);
    $pdf->SetFillColor(56, 84, 40);
    $pdf->SetTextColor(255, 255, 255);
    $pdf->SetFont('Arial', 'B', 10);
    $pdf->cell(43, 5, utf8_decode('Artículo'), 1, 0, 'C', 1);
    $pdf->cell(73, 5, utf8_decode('Especificacion'), 1, 0, 'C', 1);
    $pdf->cell(73, 5, utf8_decode('Artículos'), 1, 0, 'C', 1);

    $pdf->setXY($x, $y + 5);
    $pdf->SetTextColor(0, 0, 0);
    $pdf->SetFont('Arial', '', 8);
    foreach ($inventario as $inv) {
        $pdf->cell(43, 4, utf8_decode($inv['nombre']), 1, 0, 'C', 0);
        $pdf->cell(73, 4, utf8_decode($inv['caracteristicas']), 1, 0, 'C', 0);
        $pdf->cell(73, 4, utf8_decode($inv['clasificacion']), 1, 1, 'C', 0);
    }

    $pdf->Output('I', 'Inventario de equipos por area.pdf');
}

function inventarioPorClasificacion($inventario, $clasificacion) {

    class PDFC extends FPDF {

        function Footer() {
            $this->SetY(-10);
            $this->SetFont('Arial', 'I', 8);
            $this->AliasNbPages('nb');
            $this->Cell(0, 10, utf8_decode('Página ' . $this->PageNo() . '/nb'), 0, 0, 'C');
        }

    }

    #Encabezado

    $pdf = new PDFC('P', 'mm', 'A4');
    $pdf->SetFont('Arial', 'B', 12);
    $pdf->AddPage();
    $pdf->Image('../../images/LOGO.png', 10, 7, 20, 20);
    $pdf->setXY(70, 10);
    $pdf->cell(70, 5, 'Oaxaca Miel', 0, 1, 'C', 0);
    $pdf->SetFont('Arial', '', 10);
    $pdf->setXY(70, 17);
    $pdf->cell(70, 5, 'Inventario de los equipos clasificados como ' . utf8_decode($clasificacion), 0, 1, 'C', 0);

    #Inicio de la tabla

    $x = 10;
    $y = 30;

    $pdf->setXY($x, $y);
    $pdf->SetFillColor(56, 84, 40);
    $pdf->SetTextColor(255, 255, 255);
    $pdf->SetFont('Arial', 'B', 10);
    $pdf->cell(43, 5, utf8_decode('Artículo'), 1, 0, 'C', 1);
    $pdf->cell(73, 5, utf8_decode('Especificaciones'), 1, 0, 'C', 1);
    $pdf->cell(73, 5, utf8_decode('Departamento'), 1, 0, 'C', 1);

    $pdf->setXY($x, $y + 5);
    $pdf->SetTextColor(0, 0, 0);
    $pdf->SetFont('Arial', '', 8);
    foreach ($inventario as $inv) {
        $pdf->cell(43, 4, utf8_decode($inv['nombre']), 1, 0, 'C', 0);
        $pdf->cell(73, 4, utf8_decode($inv['caracteristicas']), 1, 0, 'C', 0);
        $pdf->cell(73, 4, utf8_decode($inv['area']), 1, 1, 'C', 0);
    }

    $pdf->Output('I', 'Inventario de equipos por clasificacion.pdf');
}

function todoInventario($inventario) {

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
    $pdf->SetFont('Arial', 'B', 12);
    $pdf->AddPage();
    $pdf->Image('../../images/LOGO.png', 10, 7, 20, 20);
    $pdf->setXY(70, 10);
    $pdf->cell(70, 5, 'Oaxaca Miel', 0, 1, 'C', 0);
    $pdf->SetFont('Arial', '', 10);
    $pdf->setXY(70, 17);
    $pdf->cell(70, 5, 'Inventario de los equipos', 0, 1, 'C', 0);

    #Inicio de la tabla

    $x = 10;
    $y = 30;

    $pdf->setXY($x, $y);
    $pdf->SetFillColor(56, 84, 40);
    $pdf->SetTextColor(255, 255, 255);
    $pdf->SetFont('Arial', 'B', 10);
    $pdf->cell(35, 5, utf8_decode('Artículo'), 1, 0, 'C', 1);
    $pdf->cell(48, 5, utf8_decode('Especificaciones'), 1, 0, 'C', 1);
    $pdf->cell(48, 5, utf8_decode('Departamento'), 1, 0, 'C', 1);
    $pdf->cell(56, 5, utf8_decode('Artículos'), 1, 0, 'C', 1);

    $pdf->setXY($x, $y + 5);
    $pdf->SetTextColor(0, 0, 0);
    foreach ($inventario as $inv) {
        $pdf->SetFont('Arial', '', 7);
        $pdf->cell(35, 4, utf8_decode($inv['nombre']), 1, 0, 'C', 0);
        $pdf->cell(48, 4, utf8_decode($inv['caracteristicas']), 1, 0, 'C', 0);
        $pdf->cell(48, 4, utf8_decode($inv['area']), 1, 0, 'C', 0);
        $pdf->SetFont('Arial', '', 7);
        $pdf->cell(56, 4, utf8_decode($inv['clasificacion']), 1, 1, 'C', 0);
    }

    $pdf->Output('I', 'Inventario de equipos.pdf');
}

function inventario($type, $id = NULL) {
    if (isset($type) || $type != NULL) {

        require('../../fpdf/FPDF/fpdf.php');
        include_once '../../DAOConeccion/conePDO.php';
        $pdo = new conePDO();
        $con = $pdo->conectar();

        if ($type === 1) {
            $query = $con->prepare("SELECT eq.nombre, eq.caracteristicas, cl.clasificacion
                FROM equipos eq 
                LEFT JOIN clasificaciones cl
                ON eq.idClasificacion = cl.idClasificacion
                WHERE idArea = :id
                ORDER BY eq.nombre ASC");
            $query->bindParam(':id', $id);
            $query->execute();
            $inventario = $query->fetchAll(PDO::FETCH_ASSOC);
            $query = $con->prepare("SELECT area FROM areas WHERE idArea = :id");
            $query->bindParam(':id', $id);
            $query->execute();
            $query->bindColumn('area', $area);
            $query->fetch(PDO::FETCH_BOUND);
            inventarioPorArea($inventario, $area);
        } else if ($type === 2) {
            $query = $con->prepare("SELECT eq.nombre, eq.caracteristicas, ar.area
                FROM equipos eq 
                LEFT JOIN clasificaciones cl
                ON eq.idClasificacion = cl.idClasificacion
                LEFT JOIN areas ar
                ON eq.idArea = ar.idArea
                WHERE cl.idClasificacion = :id
                ORDER BY eq.nombre ASC");
            $query->bindParam(':id', $id);
            $query->execute();
            $inventario = $query->fetchAll(PDO::FETCH_ASSOC);
            $query = $con->prepare("SELECT clasificacion FROM clasificaciones WHERE idClasificacion = :id");
            $query->bindParam(':id', $id);
            $query->execute();
            $query->bindColumn('clasificacion', $clasificacion);
            $query->fetch(PDO::FETCH_BOUND);
            inventarioPorClasificacion($inventario, $clasificacion);
        } else if ($type === 3) {
            $query = $con->prepare("SELECT eq.nombre, eq.caracteristicas, ar.area, cl.clasificacion
                FROM equipos eq
                LEFT JOIN areas ar
                ON eq.idArea = ar.idArea
                LEFT JOIN clasificaciones cl
                ON eq.idClasificacion = cl.idClasificacion
                ORDER BY eq.nombre ASC");
            $query->execute();
            $inventario = $query->fetchAll(PDO::FETCH_ASSOC);
            todoInventario($inventario);
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
            inventario(1, $_GET['idArea']);
            break;
        case 'clasificacion':
            inventario(2, $_GET['idClasificacion']);
            break;
        case 'todo':
            inventario(3);
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
//            inventario(1, $request['idArea']);
//            break;
//        case 'clasificacion':
//            inventario(2, $request['idClasificacion']);
//            break;
//        case 'todo':
//            inventario(3);
//            break;
//    endswitch;
//} else {
//    die;
//}