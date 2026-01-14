<?php

include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$con = $pdo->conectar();

$error = "";

if (!isset($_GET['zona'])) {
    echo $error = "Falta el codigo";
    die;
}

$zona = $_GET['zona'];
$sql = "SELECT COUNT(idAlmacen ) AS total,
SUM(neto) AS netoTotal
  FROM almacen
WHERE zona = :zona AND estado != 2";
$dato = $con->prepare($sql);
$dato->bindParam(':zona', $zona);
$dato->execute();
while ($row = $dato->fetch()) {
    $encabezadoF = new stdClass();
    $encabezadoF->total = $row["total"];
    $encabezadoF->netoTotal = $row["netoTotal"];
    $encabezadoF->folios = array();

    $query = "SELECT al.idAlmacen, p.nombre, l.localidad, al.bruto, al.tara, al.neto
            FROM almacen al
            INNER JOIN almacenencabezado bza ON al.idAlmacenEncabezado = bza.idAlmacen
            LEFT JOIN proveedor p ON p.idProveedor = bza.idProveedor
            LEFT JOIN direccion d ON d.idDireccion = p.idDireccion
            LEFT JOIN localidades l ON l.idLocalidad = d.idLocalidad
            WHERE al.zona = :zona AND al.estado != 2";
    $datos = $con->prepare($query);
    $datos->bindParam(':zona', $zona);
    $datos->execute();
    $cont = 0;
    while ($row = $datos->fetch()) {
        $folio = new stdClass();
        $folio->idAlmacen = $row["idAlmacen"];
        $folio->nombre = $row["nombre"];
        $folio->localidad = $row["localidad"];
        $folio->bruto = $row["bruto"];
        $folio->tara = $row["tara"];
        $folio->neto = $row["neto"];
//        $arrayR[] = $folio;
        $encabezadoF->folios[$cont] = $folio;
        $cont++;
    }
}

//$arrayR = array();

echo $json_response = json_encode($encabezadoF);
?>