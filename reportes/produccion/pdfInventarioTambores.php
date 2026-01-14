<?php

if (isset($_GET['zona'])) {
    $zona = $_GET['zona'];
    require('../../fpdf/FPDF/fpdf.php');
    include_once '../../DAOConeccion/conePDO.php';

    $pdo = new conePDO();
    $con = $pdo->conectar();

    $sql = "SELECT al.idAlmacen, p.nombre, l.localidad, al.bruto, al.tara, al.neto
            FROM almacen al
            LEFT JOIN almacenencabezado bza ON al.idAlmacenEncabezado = bza.idAlmacen
            LEFT JOIN proveedor p ON p.idProveedor = bza.idProveedor
            LEFT JOIN direccion d ON d.idDireccion = p.idDireccion
            LEFT JOIN localidades l ON l.idLocalidad = d.idLocalidad
            WHERE al.zona = :zona AND al.estado != 2";

    $query = $con->prepare($sql);
    $query->bindParam(':zona', $zona);
    $query->execute();


    $resultados = $query->fetchAll(PDO::FETCH_ASSOC);
    $total = count($resultados);
    $totalNeto = 0;
    foreach ($resultados as $tambor) {
        $totalNeto = $totalNeto + $tambor['neto'];
    }

    class PDF extends FPDF {

        function Header() {
            $this->SetXY(10, 10);
            $this->Image('../../fpdf/img/logo_oaxaca.png', 10, 7, 15);
            $this->cell(100);
            $this->SetFont('Arial', '', 13);
            $this->Cell(90, 5, utf8_decode('Reporte de tambores de la zona ' . $GLOBALS['zona']), 0, 0, 'L');
            $this->cell(35);
            $this->SetFont('Arial', '', 10);
            $this->Cell(50, 4, utf8_decode('Fecha: ' . date('d-m-Y')), 0, 1, 'L');
            $this->cell(20);
            $this->Cell(50, 4, utf8_decode('Total de tambores: ' . $GLOBALS['total']), 0, 1, 'L');
            $this->cell(20);
            $this->Cell(50, 4, utf8_decode('Total Neto: ' . number_format($GLOBALS['totalNeto'], 2, '.', ',')), 0, 0, 'L');

            $this->Ln(5);
        }

        function Footer() {
            $this->SetY(-15);
            $this->SetFont('Arial', 'I', 8);
            $this->AliasNbPages('nb');
            $this->Cell(0, 10, utf8_decode('Página ' . $this->PageNo() . '/nb'), 0, 0, 'C');
        }

    }

    $pdf = new PDF('L', 'mm', 'A4');
    $pdf->AddPage();
    $pdf->SetFillColor(56, 84, 40);
    $pdf->SetTextColor(255, 255, 255);
    $pdf->SetFont('Arial', 'B', 10);
    $pdf->SetXY(10, 25);
    $pdf->Cell(10, 5, utf8_decode('N°'), 1, 0, 'C', 1);
    $pdf->Cell(20, 5, 'Folio', 1, 0, 'C', 1);
    $pdf->Cell(90, 5, 'Proveedor', 1, 0, 'C', 1);
    $pdf->Cell(50, 5, 'Localidad', 1, 0, 'C', 1);
    $pdf->Cell(30, 5, 'Bruto', 1, 0, 'C', 1);
    $pdf->Cell(30, 5, 'Tara', 1, 0, 'C', 1);
    $pdf->Cell(30, 5, 'Neto', 1, 0, 'C', 1);

    $pdf->SetXY(10, 30);
    $pdf->SetTextColor(0, 0, 0);
    $pdf->SetFont('Arial', '', 10);

    foreach ($resultados as $k => $tambor) {
        $pdf->Cell(10, 4, utf8_decode($k + 1), 1, 0, 'C', 0);
        $pdf->Cell(20, 4, utf8_decode($tambor['idAlmacen']), 1, 0, 'C', 0);
        $pdf->Cell(90, 4, utf8_decode($tambor['nombre']), 1, 0, 'C', 0);
        $pdf->Cell(50, 4, utf8_decode($tambor['localidad']), 1, 0, 'C', 0);
        $pdf->Cell(30, 4, utf8_decode($tambor['bruto']), 1, 0, 'C', 0);
        $pdf->Cell(30, 4, utf8_decode($tambor['tara']), 1, 0, 'C', 0);
        $pdf->Cell(30, 4, utf8_decode($tambor['neto']), 1, 1, 'C', 0);
    }


    $pdf->Output('', 'Reporte de tambores por zona.pdf');
} else {
    die;
}

