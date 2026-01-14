<?php //

require('../../fpdf/FPDF/fpdf.php');
include_once '../../DAOConeccion/conePDO.php';

$pdo = new conePDO();
$con = $pdo->conectar();

if (isset($_GET['idMantenimiento'])) {

    $sqlDetalle = "SELECT a.area, e.idArea, e.nombre AS equipo, e.idEquipo, CONCAT('AF-MID-', e.idEquipo) AS codigo,
             mt.idMantenimiento, mt.fechaReal, mt.idMes, mt.descripcion, mt.observaciones, mt.tipoPersonal,
             mt.idPersonalOM, mt.nombreTecnico, mt.totalPago
             FROM equipos e 
             LEFT JOIN areas a ON a.idArea = e.idArea
             LEFT JOIN controlmantenimiento mt ON mt.idEquipo = e.idEquipo
             WHERE mt.idMantenimiento = :idMantenimiento";
    $dato = $con->prepare($sqlDetalle);
    $dato->bindParam(':idMantenimiento', $_GET['idMantenimiento']);
    $dato->execute();

    if ($dato == false) {
        echo mysql_error();
    } else {
        $ctrlMantto = new stdClass();
        while ($rs = $dato->fetch()) {
            $ctrlMantto = new stdClass();
            $ctrlMantto->area = $rs["area"];
            $ctrlMantto->idArea = $rs["idArea"];
            $ctrlMantto->idEquipo = $rs["idEquipo"];
            $ctrlMantto->equipo = $rs["equipo"];
            $ctrlMantto->codigo = $rs["codigo"];
            $ctrlMantto->idMantenimiento = $rs["idMantenimiento"];
            $ctrlMantto->fechaReal = $rs["fechaReal"];
            $ctrlMantto->idMes = $rs["idMes"];
            $ctrlMantto->descripcion = $rs["descripcion"];
            $ctrlMantto->observaciones = $rs["observaciones"];
            $ctrlMantto->tipoPersonal = $rs["tipoPersonal"];
            $ctrlMantto->idPersonalOM = $rs["idPersonalOM"];
            $ctrlMantto->nombreTecnico = $rs["nombreTecnico"];
            $ctrlMantto->totalPago = $rs["totalPago"];

            if ($ctrlMantto->tipoPersonal == "1") {
                $sqlPersonal = "SELECT p.nombre, mt.idPersonalOM
          FROM controlmantenimiento mt
         INNER JOIN personaloaxaca p ON p.idPersonalOM = mt.idPersonalOM
          WHERE mt.idMantenimiento = :idMantenimiento";
                $data = $con->prepare($sqlPersonal);
                $data->bindParam(':idMantenimiento', $_GET['idMantenimiento']);
                $data->execute();

                while ($rs = $data->fetch()) {
                    $ctrlMantto->nombre = $rs["nombre"];
                    $ctrlMantto->idPersonalOM = $rs["idPersonalOM"];
                }
            } else {
                $sqlPersonalEx = "SELECT p.nombreProveedor AS nombre, p.nombreContacto, p.domicilio, p.telefono, p.correo, mt.idPersonalOM
          FROM controlmantenimiento mt
         INNER JOIN proveedoresmantto p ON p.idProveedorMantto = mt.idPersonalOM
          WHERE mt.idMantenimiento = :idMantenimiento";
                $dat = $con->prepare($sqlPersonalEx);
                $dat->bindParam(':idMantenimiento', $_GET['idMantenimiento']);
                $dat->execute();

                while ($rs = $dat->fetch()) {
                    $ctrlMantto->nombre = $rs["nombre"];
                    $ctrlMantto->idPersonalOM = $rs["idPersonalOM"];
                    $ctrlMantto->nombreContacto = $rs["nombreContacto"];
                    $ctrlMantto->domicilio = $rs["domicilio"];
                    $ctrlMantto->telefono = $rs["telefono"];
                    $ctrlMantto->correo = $rs["correo"];
                }
            }
        }
    }
}



#Meses y fechas

foreach ($arrayCrono as $res) {
    $res->fechas = array();
    $res->fechas['enero'] = array('p' => '', 'r' => '');
    $res->fechas['febrero'] = array('p' => '', 'r' => '');
    $res->fechas['marzo'] = array('p' => '', 'r' => '');
    $res->fechas['abril'] = array('p' => '', 'r' => '');
    $res->fechas['mayo'] = array('p' => '', 'r' => '');
    $res->fechas['junio'] = array('p' => '', 'r' => '');
    $res->fechas['julio'] = array('p' => '', 'r' => '');
    $res->fechas['agosto'] = array('p' => '', 'r' => '');
    $res->fechas['septiembre'] = array('p' => '', 'r' => '');
    $res->fechas['octubre'] = array('p' => '', 'r' => '');
    $res->fechas['noviembre'] = array('p' => '', 'r' => '');
    $res->fechas['diciembre'] = array('p' => '', 'r' => '');
    foreach ($res->listaFechas as $fecha) {
        switch ($fecha->idMes) {
            case '1':
                $enero = array('p' => $fecha->fechaProgramada,
                    'r' => $fecha->fechaReal);
                $res->fechas['enero'] = $enero;
                break;
            case '2':
                $febrero = array('p' => $fecha->fechaProgramada,
                    'r' => $fecha->fechaReal);
                $res->fechas['febrero'] = $febrero;
                break;
            case '3':
                $marzo = array('p' => $fecha->fechaProgramada,
                    'r' => $fecha->fechaReal);
                $res->fechas['marzo'] = $marzo;
                break;
            case '4':
                $abril = array('p' => $fecha->fechaProgramada,
                    'r' => $fecha->fechaReal);
                $res->fechas['abril'] = $abril;
                break;
            case '5':
                $mayo = array('p' => $fecha->fechaProgramada,
                    'r' => $fecha->fechaReal);
                $res->fechas['mayo'] = $mayo;
                break;
            case '6':
                $junio = array('p' => $fecha->fechaProgramada,
                    'r' => $fecha->fechaReal);
                $res->fechas['junio'] = $junio;
                break;
            case '7':
                $julio = array('p' => $fecha->fechaProgramada,
                    'r' => $fecha->fechaReal);
                $res->fechas['julio'] = $julio;
                break;
            case '8':
                $agosto = array('p' => $fecha->fechaProgramada,
                    'r' => $fecha->fechaReal);
                $res->fechas['agosto'] = $agosto;
                break;
            case '9':
                $septiembre = array('p' => $fecha->fechaProgramada,
                    'r' => $fecha->fechaReal);
                $res->fechas['septiembre'] = $septiembre;
                break;
            case '10':
                $octubre = array('p' => $fecha->fechaProgramada,
                    'r' => $fecha->fechaReal);
                $res->fechas['octubre'] = $octubre;
                break;
            case '11':
                $noviembre = array('p' => $fecha->fechaProgramada,
                    'r' => $fecha->fechaReal);
                $res->fechas['noviembre'] = $noviembre;
                break;
            case '12':
                $diciembre = array('p' => $fecha->fechaProgramada,
                    'r' => $fecha->fechaReal);
                $res->fechas['diciembre'] = $diciembre;
                break;
        }
    }
}

# / meses y fechas

class PDF extends FPDF {

    function Header() {
        $this->SetFont('Arial', 'B', 15);
        $this->Cell(80);
        $this->Cell(100, 10, utf8_decode('CRONOGRAMA DE MANTENIMIENTO 2017. CÓDIGO: ' . 'RST-M-01'), 0, 0, 'C');
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
$pdf->setXY(10, 20);
$pdf->Cell(35, 20, "Area", 1, 0, 'C', 1);
$pdf->setXY(45, 20);
$pdf->Cell(45, 20, "Equipo", 1, 0, 'C', 1);
$pdf->setXY(90, 20);
$pdf->Cell(150, 10, "Meses", 1, 0, 'C', 1);
$pdf->setXY(90, 30);
$pdf->Cell(25, 10, "Enero", 1, 0, 'C', 1);
$pdf->Cell(25, 10, "Febrero", 1, 0, 'C', 1);
$pdf->Cell(25, 10, "Marzo", 1, 0, 'C', 1);
$pdf->Cell(25, 10, "Abril", 1, 0, 'C', 1);
$pdf->Cell(25, 10, "Mayo", 1, 0, 'C', 1);
$pdf->Cell(25, 10, "Junio", 1, 0, 'C', 1);
$pdf->setXY(240, 20);
$pdf->Cell(35, 20, "Periodicidad", 1, 0, 'C', 1);
$pdf->SetTextColor(0, 0, 0);

//Contenido de la tabla
$x = 10;
$y = 40;

$pdf->SetFont('Arial', '', 9);

foreach ($arrayCrono as $res) {
    $pdf->setXY($x, $y);
    $pdf->Cell(35, 10, $res->area, 1, 0, 'C');
    $pdf->Cell(45, 10, $res->equipo, 1, 0, 'C');
    $pdf->SetFillColor(241, 196, 15);
    $pdf->setXY(85, 0 + $y);
    $pdf->Cell(5, 5, 'P', 1, 0, 'C', 1);
    $pdf->setXY(85, 5 + $y);
    $pdf->SetFillColor(39, 174, 96);
    $pdf->SetTextColor(255, 255, 255);
    $pdf->Cell(5, 5, 'R', 1, 0, 'C', 1);
    $pdf->SetTextColor(0, 0, 0);
    #ENERO
    $pdf->setXY(90, 0 + $y);
    $pdf->Cell(25, 5, $res->fechas['enero']['p'], 1, 0, 'C');
    $pdf->setXY(90, 5 + $y);
    $pdf->Cell(25, 5, $res->fechas['enero']['r'], 1, 0, 'C');

    #FEBRERO
    $pdf->setXY(115, 0 + $y);
    $pdf->Cell(25, 5, $res->fechas['febrero']['p'], 1, 0, 'C');
    $pdf->setXY(115, 5 + $y);
    $pdf->Cell(25, 5, $res->fechas['febrero']['r'], 1, 0, 'C');

    #MARZO
    $pdf->setXY(140, 0 + $y);
    $pdf->Cell(25, 5, $res->fechas['marzo']['p'], 1, 0, 'C');
    $pdf->setXY(140, 5 + $y);
    $pdf->Cell(25, 5, $res->fechas['marzo']['r'], 1, 0, 'C');

    #ABRIL
    $pdf->setXY(165, 0 + $y);
    $pdf->Cell(25, 5, $res->fechas['abril']['p'], 1, 0, 'C');
    $pdf->setXY(165, 5 + $y);
    $pdf->Cell(25, 5, $res->fechas['abril']['r'], 1, 0, 'C');

    #MAYO
    $pdf->setXY(190, 0 + $y);
    $pdf->Cell(25, 5, $res->fechas['mayo']['p'], 1, 0, 'C');
    $pdf->setXY(190, 5 + $y);
    $pdf->Cell(25, 5, $res->fechas['mayo']['r'], 1, 0, 'C');

    #JUNIO
    $pdf->setXY(215, 0 + $y);
    $pdf->Cell(25, 5, $res->fechas['junio']['p'], 1, 0, 'C');
    $pdf->setXY(215, 5 + $y);
    $pdf->Cell(25, 5, $res->fechas['junio']['r'], 1, 0, 'C');
    $pdf->setXY(240, $y);
    $pdf->Cell(35, 10, $res->periodicidad, 1, 0, 'C');
    $x = 10;
    $y = $y + 10;
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
$pdf->Cell(5, 5, 'RP', 0, 0, 'C', 1);
$pdf->SetTextColor(0, 0, 0);
$pdf->Cell(20, 5, 'Reprogramado', 0, 0, 'L');
$pdf->Ln();


$pdf->setX(30);
$pdf->SetFillColor(39, 174, 96);
$pdf->SetTextColor(255, 255, 255);
$pdf->Cell(5, 5, 'R', 0, 0, 'C', 1);
$pdf->SetTextColor(0, 0, 0);
$pdf->Cell(20, 5, 'Real', 0, 0, 'L');
$pdf->Ln();

$pdf->setX(30);
$pdf->SetFillColor(52, 152, 219);
$pdf->SetTextColor(255, 255, 255);
$pdf->Cell(5, 5, 'M', 0, 0, 'C', 1);
$pdf->SetTextColor(0, 0, 0);
$pdf->Cell(20, 5, 'Muestreo', 0, 0, 'L');

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
$pdf->setXY(10, 20);
$pdf->Cell(35, 20, "Area", 1, 0, 'C', 1);
$pdf->setXY(45, 20);
$pdf->Cell(45, 20, "Equipo", 1, 0, 'C', 1);
$pdf->setXY(90, 20);
$pdf->Cell(150, 10, "Meses", 1, 0, 'C', 1);
$pdf->setXY(90, 30);
$pdf->Cell(25, 10, "Julio", 1, 0, 'C', 1);
$pdf->Cell(25, 10, "Agosto", 1, 0, 'C', 1);
$pdf->Cell(25, 10, "Septiembre", 1, 0, 'C', 1);
$pdf->Cell(25, 10, "Octubre", 1, 0, 'C', 1);
$pdf->Cell(25, 10, "Noviembre", 1, 0, 'C', 1);
$pdf->Cell(25, 10, "Diciembre", 1, 0, 'C', 1);
$pdf->setXY(240, 20);
$pdf->Cell(35, 20, "Periodicidad", 1, 0, 'C', 1);
$pdf->SetTextColor(0, 0, 0);

//Contenido de la tabla
$x = 10;
$y = 40;

$pdf->SetFont('Arial', '', 9);

foreach ($arrayCrono as $res) {
    $pdf->setXY($x, $y);
    $pdf->Cell(35, 10, $res->area, 1, 0, 'C');
    $pdf->Cell(45, 10, $res->equipo, 1, 0, 'C');
    $pdf->SetFillColor(241, 196, 15);
    $pdf->setXY(85, 0 + $y);
    $pdf->Cell(5, 5, 'P', 1, 0, 'C', 1);
    $pdf->setXY(85, 5 + $y);
    $pdf->SetFillColor(39, 174, 96);
    $pdf->SetTextColor(255, 255, 255);
    $pdf->Cell(5, 5, 'R', 1, 0, 'C', 1);
    $pdf->SetTextColor(0, 0, 0);
    #ENERO
    $pdf->setXY(90, 0 + $y);
    $pdf->Cell(25, 5, $res->fechas['julio']['p'], 1, 0, 'C');
    $pdf->setXY(90, 5 + $y);
    $pdf->Cell(25, 5, $res->fechas['julio']['r'], 1, 0, 'C');

    #FEBRERO
    $pdf->setXY(115, 0 + $y);
    $pdf->Cell(25, 5, $res->fechas['agosto']['p'], 1, 0, 'C');
    $pdf->setXY(115, 5 + $y);
    $pdf->Cell(25, 5, $res->fechas['agosto']['r'], 1, 0, 'C');

    #MARZO
    $pdf->setXY(140, 0 + $y);
    $pdf->Cell(25, 5, $res->fechas['septiembre']['p'], 1, 0, 'C');
    $pdf->setXY(140, 5 + $y);
    $pdf->Cell(25, 5, $res->fechas['septiembre']['r'], 1, 0, 'C');

    #ABRIL
    $pdf->setXY(165, 0 + $y);
    $pdf->Cell(25, 5, $res->fechas['octubre']['p'], 1, 0, 'C');
    $pdf->setXY(165, 5 + $y);
    $pdf->Cell(25, 5, $res->fechas['octubre']['r'], 1, 0, 'C');

    #MAYO
    $pdf->setXY(190, 0 + $y);
    $pdf->Cell(25, 5, $res->fechas['noviembre']['p'], 1, 0, 'C');
    $pdf->setXY(190, 5 + $y);
    $pdf->Cell(25, 5, $res->fechas['noviembre']['r'], 1, 0, 'C');

    #JUNIO
    $pdf->setXY(215, 0 + $y);
    $pdf->Cell(25, 5, $res->fechas['diciembre']['p'], 1, 0, 'C');
    $pdf->setXY(215, 5 + $y);
    $pdf->Cell(25, 5, $res->fechas['diciembre']['r'], 1, 0, 'C');
    $pdf->setXY(240, $y);
    $pdf->Cell(35, 10, $res->periodicidad, 1, 0, 'C');
    $x = 10;
    $y = $y + 10;
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
$pdf->Cell(5, 5, 'RP', 0, 0, 'C', 1);
$pdf->SetTextColor(0, 0, 0);
$pdf->Cell(20, 5, 'Reprogramado', 0, 0, 'L');
$pdf->Ln();


$pdf->setX(30);
$pdf->SetFillColor(39, 174, 96);
$pdf->SetTextColor(255, 255, 255);
$pdf->Cell(5, 5, 'R', 0, 0, 'C', 1);
$pdf->SetTextColor(0, 0, 0);
$pdf->Cell(20, 5, 'Real', 0, 0, 'L');
$pdf->Ln();

$pdf->setX(30);
$pdf->SetFillColor(52, 152, 219);
$pdf->SetTextColor(255, 255, 255);
$pdf->Cell(5, 5, 'M', 0, 0, 'C', 1);
$pdf->SetTextColor(0, 0, 0);
$pdf->Cell(20, 5, 'Muestreo', 0, 0, 'L');

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
