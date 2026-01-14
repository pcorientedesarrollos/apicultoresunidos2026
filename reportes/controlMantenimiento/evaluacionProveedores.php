<?php

require('../../fpdf/FPDF/fpdf.php');
include_once '../../DAOConeccion/conePDO.php';

$pdo = new conePDO();
$con = $pdo->conectar();

$idEvaluacion = $_GET['idEvaluacion'];

$query = "SELECT e.*, p.*
         FROM evaluaciones e 
         INNER JOIN proveedoresmantto p ON p.idProveedorMantto = e.idProveedorMantto
         WHERE idEvaluacion = :idEvaluacion";
$datos = $con->prepare($query);
$datos->bindParam(':idEvaluacion', $idEvaluacion);
$datos->execute();

while ($row = $datos->fetch()) {
    $infoMantto = new stdClass();
    $infoMantto->idEvaluacion = $row["idEvaluacion"];
    $infoMantto->idTipoProveedor = $row["idTipoProveedor"];
    $infoMantto->idProveedorMantto = $row["idProveedorMantto"];
    $infoMantto->sistemaCalidad = $row["sistemaCalidad"];
    $infoMantto->idCertificacion = $row["idCertificacion"];
    $infoMantto->certificacion = $row["certificacion"];
    $infoMantto->nombre = $row["nombre"];
    $infoMantto->cargo = $row["cargo"];
    $infoMantto->fecha = $row["fecha"];
    $infoMantto->cuestionario = $row["cuestionario"];
    $infoMantto->nombreProveedor = $row["nombreProveedor"];
    $infoMantto->domicilio = $row["domicilio"];
    $infoMantto->telefono = $row["telefono"];
    $infoMantto->correo = $row["correo"];
    $infoMantto->web = $row["web"];
    $infoMantto->nombreContacto = $row["nombreContacto"];
}
//echo json_encode($infoMantto);

$array = json_decode($infoMantto->cuestionario);

//echo $array->defensa1;
class PDF extends FPDF {

    function Header() {
        $this->SetFont('Arial', 'B', 15);
        $this->Cell(80);
        $this->Cell(100, 10, utf8_decode('CUESTIONARIO DE EVALUACION A PROVEEDORES. ' . 'CODIGO: RST-AI-04'), 0, 0, 'C');
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
//
//Encabezado de la tabla
$pdf->SetFillColor(127, 140, 141);
$pdf->SetTextColor(255, 255, 255);
$pdf->Cell(25, 10, "No", 1, 0, 'C', 1);
$pdf->Cell(25, 10, "Concepto", 1, 0, 'C', 1);
$pdf->Cell(25, 10, "0", 1, 0, 'C', 1);
$pdf->Cell(25, 10, "1", 1, 0, 'C', 1);
$pdf->Cell(25, 10, "2", 1, 0, 'C', 1);
$pdf->Cell(25, 10, "3", 1, 0, 'C', 1);
$pdf->Cell(25, 10, "N/A", 1, 0, 'C', 1);
$pdf->setXY(240, 20);
$pdf->SetTextColor(0, 0, 0);

//Contenido de la tabla
$x = 10;
$y = 30;

$pdf->SetFont('Arial', '', 9);

$pdf->setXY($x, $y);
$pdf->Cell(25, 5, "1", 1, 0, 'C');
$pdf->Cell(25, 5, "pregunta 1", 1, 0, 'C');
$pdf->Cell(25, 5, $array->defensa1, 1, 0, 'C');
$pdf->Cell(25, 5, $array->defensa1, 1, 0, 'C');
$pdf->Cell(25, 5, $array->defensa1, 1, 0, 'C');
$pdf->Cell(25, 5, $array->defensa1, 1, 0, 'C');
$pdf->Cell(25, 5, $array->defensa1, 1, 0, 'C');





$x = 10;
$y = $y + 15;


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
$pdf->Output('', 'evaluacion.pdf');
