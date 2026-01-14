<?php

require('../../fpdf/FPDF/fpdf.php');
include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$con = $pdo->conectar();

$idMes = $_GET["idMes"];
switch ($idMes) {
    case "1":
        $mes = "Enero";
        break;
    case "2":
        $mes = "Febrero";
        break;
    case "3":
        $mes = "Marzo";
        break;
    case "4":
        $mes = "Abril";
        break;
    case "5":
        $mes = "Mayo";
        break;
    case "6":
        $mes = "Junio";
        break;
    case "7":
        $mes = "Julio";
        break;
    case "8":
        $mes = "Agosto";
        break;
    case "9":
        $mes = "Septiembre";
        break;
    case "10":
        $mes = "Octubre";
        break;
    case "11":
        $mes = "Noviembre";
        break;
    case "12":
        $mes = "Diciembre";
        break;
};

$sql = "SELECT ca.*,
        m.mes, om.nombre, om.idPersonalOM FROM disposiciondelpersonal ca  
INNER JOIN personaloaxaca om ON om.idPersonalOM = ca.idPersonalOM        
INNER JOIN meses m 
        ON m.idMes = ca.idMes
        WHERE ca.idMes = :idMes";
$datos = $con->prepare($sql);
$datos->bindParam(':idMes', $idMes);
$datos->execute();

if ($datos == false) {
    echo mysql_error();
} else {
    $arrayDisposicion = array();
    while ($respuAbono = $datos->fetch()) {
        $disposicion = new stdClass();
        $disposicion->idDisposicion = $respuAbono["idDisposicion"];
        $disposicion->mes = $respuAbono["mes"];
        $disposicion->idMes = $respuAbono["idMes"];
        $disposicion->idPersonalOM = $respuAbono["idPersonalOM"];
        $disposicion->nombre = $respuAbono["nombre"];
        $disposicion->observaciones = $respuAbono["observaciones"];

        $disposicion->semana1 = $respuAbono["semana1"];
        $disposicion->semana2 = $respuAbono["semana2"];
        $disposicion->semana3 = $respuAbono["semana3"];
        $disposicion->semana4 = $respuAbono["semana4"];
        $disposicion->semana5 = $respuAbono["semana5"];

        $disposicion->semana1A = $respuAbono["semana1A"];
        $disposicion->semana2A = $respuAbono["semana2A"];
        $disposicion->semana3A = $respuAbono["semana3A"];
        $disposicion->semana4A = $respuAbono["semana4A"];
        $disposicion->semana5A = $respuAbono["semana5A"];

        $disposicion->semana1B = $respuAbono["semana1B"];
        $disposicion->semana2B = $respuAbono["semana2B"];
        $disposicion->semana3B = $respuAbono["semana3B"];
        $disposicion->semana4B = $respuAbono["semana4B"];
        $disposicion->semana5B = $respuAbono["semana5B"];

        $disposicion->semana1C = $respuAbono["semana1C"];
        $disposicion->semana2C = $respuAbono["semana2C"];
        $disposicion->semana3C = $respuAbono["semana3C"];
        $disposicion->semana4C = $respuAbono["semana4C"];
        $disposicion->semana5C = $respuAbono["semana5C"];

        $disposicion->semana1D = $respuAbono["semana1D"];
        $disposicion->semana2D = $respuAbono["semana2D"];
        $disposicion->semana3D = $respuAbono["semana3D"];
        $disposicion->semana4D = $respuAbono["semana4D"];
        $disposicion->semana5D = $respuAbono["semana5D"];

        $disposicion->semana1E = $respuAbono["semana1E"];
        $disposicion->semana2E = $respuAbono["semana2E"];
        $disposicion->semana3E = $respuAbono["semana3E"];
        $disposicion->semana4E = $respuAbono["semana4E"];
        $disposicion->semana5E = $respuAbono["semana5E"];

        $disposicion->semana1F = $respuAbono["semana1F"];
        $disposicion->semana2F = $respuAbono["semana2F"];
        $disposicion->semana3F = $respuAbono["semana3F"];
        $disposicion->semana4F = $respuAbono["semana4F"];
        $disposicion->semana5F = $respuAbono["semana5F"];

        $arrayDisposicion[] = $disposicion;
    }
}

foreach ($arrayDisposicion as $obj) {
    foreach ($obj as $key => $value) {
        if (mb_stristr($key, 'semana')) {
            if ($value == '1') {
                $obj->$key = '4';
            } else {
                $obj->$key = '';
            }
        }
    }
};

class PDF extends FPDF {

// Cabecera de página
    function Header() {
// Logo
        $this->Image('../../fpdf/img/logo_oaxaca.png', 240, 20, 40);
// Arial bold 15
        $this->SetFont('Arial', 'B', 15);
// Movernos a la derecha
        $this->Cell(80);
// Título
        $this->Cell(100, 10, utf8_decode('DISPOSICIÓN DEL PERSONAL'), 0, 0, 'C');
// Salto de línea
        $this->Ln(20);
    }

}

// Creación del objeto de la clase heredada
$pdf = new PDF('L', 'mm', 'A4');
$pdf->AliasNbPages();
$pdf->AddPage();

$pdf->SetTextColor(0, 0, 0);

#ENCABEZADO DE REPORTE
$pdf->SetFont('Arial', '', 12);
$pdf->SetXY(20, 35);
$pdf->Cell(24, 5, utf8_decode('Área: Producción '), 0, 0, 'L', 0);
$pdf->SetX(70);
$pdf->Cell(50, 5, "Mes: " . $mes, 0, 0, 'C', 0);
$pdf->SetXY(20, 43);
$pdf->SetFont('Arial', 'B', 10);
$pdf->SetXY(20, 50);
$pdf->Cell(0, 5, utf8_decode('CÓDIGO: RPR-DP-01 - VERIFICACIÓN: 01 '), 0, 0, 'L', 0);


$pdf->SetFillColor(189, 195, 199);
$pdf->SetTextColor(255, 255, 255);
#TABLA
$pdf->SetFont('Arial', '', 10);
$pdf->SetXY(16, 55);
$pdf->Cell(34, 30, 'Nombre', 1, 0, 'C', 1);

$pdf->SetFont('Arial', '', 9);
$pdf->SetX(50);

$pdf->Cell(26, 20, "Concepto1", 1, 0, 'C', 1);
$pdf->SetX(76);
$pdf->Cell(26, 20, 'Concepto2', 1, 0, 'C', 1);
$pdf->SetX(102);
$pdf->Cell(26, 20, 'Concepto3', 1, 0, 'C', 1);
$pdf->SetX(128);
$pdf->Cell(26, 20, 'Concepto4', 1, 0, 'C', 1);
$pdf->SetX(154);
$pdf->Cell(26, 20, 'Concepto5', 1, 0, 'C', 1);
$pdf->SetX(180);
$pdf->Cell(26, 20, 'Concepto6', 1, 0, 'C', 1);
$pdf->SetX(206);
$pdf->Cell(26, 20, 'Concepto7', 1, 0, 'C', 1);
$pdf->SetX(232);
$pdf->Cell(26, 30, 'Firma', 1, 0, 'C', 1);
$pdf->SetX(258);
$pdf->Cell(26, 30, 'Observaciones', 1, 0, 'C', 1);

#SEMANAS

$pdf->SetXY(50, 75);
for ($a = 1; $a <= 7; $a++) {
    $pdf->Cell(5.2, 10, '1', 1, 0, 'C', 1);
    $pdf->Cell(5.2, 10, '2', 1, 0, 'C', 1);
    $pdf->Cell(5.2, 10, '3', 1, 0, 'C', 1);
    $pdf->Cell(5.2, 10, '4', 1, 0, 'C', 1);
    $pdf->Cell(5.2, 10, '5', 1, 0, 'C', 1);
}

#Contenido
$pdf->SetTextColor(0, 0, 0);
$pdf->SetXY(16, 85);
foreach ($arrayDisposicion as $r) {
    $pdf->SetFont('Arial', '', 5);
    $pdf->Cell(34, 5, $r->nombre, 1, 0, 'C', 0);
    $pdf->SetFont('ZapfDingbats', '', 9);
    $pdf->Cell(5.2, 5, $r->semana1, 1, 0, 'C', 0);
    $pdf->Cell(5.2, 5, $r->semana2, 1, 0, 'C', 0);
    $pdf->Cell(5.2, 5, $r->semana3, 1, 0, 'C', 0);
    $pdf->Cell(5.2, 5, $r->semana4, 1, 0, 'C', 0);
    $pdf->Cell(5.2, 5, $r->semana5, 1, 0, 'C', 0);
    $pdf->Cell(5.2, 5, $r->semana1A, 1, 0, 'C', 0);
    $pdf->Cell(5.2, 5, $r->semana2A, 1, 0, 'C', 0);
    $pdf->Cell(5.2, 5, $r->semana3A, 1, 0, 'C', 0);
    $pdf->Cell(5.2, 5, $r->semana4A, 1, 0, 'C', 0);
    $pdf->Cell(5.2, 5, $r->semana5A, 1, 0, 'C', 0);
    $pdf->Cell(5.2, 5, $r->semana1B, 1, 0, 'C', 0);
    $pdf->Cell(5.2, 5, $r->semana2B, 1, 0, 'C', 0);
    $pdf->Cell(5.2, 5, $r->semana3B, 1, 0, 'C', 0);
    $pdf->Cell(5.2, 5, $r->semana4B, 1, 0, 'C', 0);
    $pdf->Cell(5.2, 5, $r->semana5B, 1, 0, 'C', 0);
    $pdf->Cell(5.2, 5, $r->semana1C, 1, 0, 'C', 0);
    $pdf->Cell(5.2, 5, $r->semana2C, 1, 0, 'C', 0);
    $pdf->Cell(5.2, 5, $r->semana3C, 1, 0, 'C', 0);
    $pdf->Cell(5.2, 5, $r->semana4C, 1, 0, 'C', 0);
    $pdf->Cell(5.2, 5, $r->semana5C, 1, 0, 'C', 0);
    $pdf->Cell(5.2, 5, $r->semana1D, 1, 0, 'C', 0);
    $pdf->Cell(5.2, 5, $r->semana2D, 1, 0, 'C', 0);
    $pdf->Cell(5.2, 5, $r->semana3D, 1, 0, 'C', 0);
    $pdf->Cell(5.2, 5, $r->semana4D, 1, 0, 'C', 0);
    $pdf->Cell(5.2, 5, $r->semana5D, 1, 0, 'C', 0);
    $pdf->Cell(5.2, 5, $r->semana1E, 1, 0, 'C', 0);
    $pdf->Cell(5.2, 5, $r->semana2E, 1, 0, 'C', 0);
    $pdf->Cell(5.2, 5, $r->semana3E, 1, 0, 'C', 0);
    $pdf->Cell(5.2, 5, $r->semana4E, 1, 0, 'C', 0);
    $pdf->Cell(5.2, 5, $r->semana5E, 1, 0, 'C', 0);
    $pdf->Cell(5.2, 5, $r->semana1F, 1, 0, 'C', 0);
    $pdf->Cell(5.2, 5, $r->semana2F, 1, 0, 'C', 0);
    $pdf->Cell(5.2, 5, $r->semana3F, 1, 0, 'C', 0);
    $pdf->Cell(5.2, 5, $r->semana4F, 1, 0, 'C', 0);
    $pdf->Cell(5.2, 5, $r->semana5F, 1, 0, 'C', 0);
    $pdf->Cell(26, 5, '', 1, 0, 'C', 0);
    $pdf->SetFont('Arial', '', 5);
    $pdf->Cell(26, 5, $r->observaciones, 1, 0, 'C', 0);
    $pdf->Ln();
    $pdf->SetX(16);
}

#Conceptos
$pdf->Ln(10);
$pdf->SetFont('Arial', '', 7);

$pdf->SetX(16);
$pdf->Cell(16, 5, "Concepto1: ", 0, 0, 'L', 0);
$pdf->SetX(40);
$pdf->Cell(16, 5, "Uniforme limpio y sin objetos en bolsillos superiores ", 0, 0, 'L', 0);
$pdf->Ln();

$pdf->SetX(16);
$pdf->Cell(16, 5, "Concepto2: ", 0, 0, 'L', 0);
$pdf->SetX(40);
$pdf->Cell(16, 5, "Calzado de Seguridad, limpio y en condiciones ", 0, 0, 'L', 0);
$pdf->Ln();

$pdf->SetX(16);
$pdf->Cell(16, 5, "Concepto3: ", 0, 0, 'L', 0);
$pdf->SetX(40);
$pdf->Cell(16, 5, "Uso correcto de cofia y cubrebocas", 0, 0, 'L', 0);
$pdf->Ln();

$pdf->SetX(16);
$pdf->Cell(16, 5, "Concepto4: ", 0, 0, 'L', 0);
$pdf->SetX(40);
$pdf->Cell(16, 5, utf8_decode("Uñas cortas y sin barniz, manos limpias"), 0, 0, 'L', 0);
$pdf->Ln();

$pdf->SetX(16);
$pdf->Cell(16, 5, "Concepto5: ", 0, 0, 'L', 0);
$pdf->SetX(40);
$pdf->Cell(16, 5, "Sin joyeria, accesorios o maquillaje", 0, 0, 'L', 0);
$pdf->Ln();

$pdf->SetX(16);
$pdf->Cell(16, 5, "Concepto6: ", 0, 0, 'L', 0);
$pdf->SetX(40);
$pdf->Cell(16, 5, "Sin signos de enfermedad o heridas", 0, 0, 'L', 0);
$pdf->Ln();

$pdf->SetX(16);
$pdf->Cell(16, 5, "Concepto7: ", 0, 0, 'L', 0);
$pdf->SetX(40);
$pdf->Cell(16, 5, "Cabello y barba cortos", 0, 0, 'L', 0);
$pdf->Ln();

$pdf->Output('', 'reporte.pdf');
