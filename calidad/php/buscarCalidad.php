<?php
include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$con = $pdo->conectar();
$id = $_GET["id"];

switch ($_GET['miel']) {
    case '1':
        $almacen = 'almacen';
        $almacenEncabezado = 'almacenencabezado';
        $laboratorio = 'laboratorio';
    break;
    case '2':
        $almacen = 'almacen_organico';
        $almacenEncabezado = 'almacenencabezado_organico';
        $laboratorio = 'laboratorio_organico';
    break;
    case '5':
        $almacen = 'almacen_mantequilla';
        $almacenEncabezado = 'almacenencabezado_mantequilla';
        $laboratorio = 'laboratorio_mantequilla';
    break;
    case '6':
        $almacen = 'almacen_altiplano';
        $almacenEncabezado = 'almacenencabezado_altiplano';
        $laboratorio = 'laboratorio_altiplano';
    break;
    case '7':
        $almacen = 'almacen_naranjo';
        $almacenEncabezado = 'almacenencabezado_naranjo';
        $laboratorio = 'laboratorio_naranjo';
    break;
    case '8':
        $almacen = 'almacen_aguacate';
        $almacenEncabezado = 'almacenencabezado_aguacate';
        $laboratorio = 'laboratorio_aguacate';
    break;
    case '9':
        $almacen = 'almacen_mezquite';
        $almacenEncabezado = 'almacenencabezado_mezquite';
        $laboratorio = 'laboratorio_mezquite';
    break;
    default:
        throw new Exception('El parámetro no es válido');
        break;
}

$sql = "SELECT ale.fecha, al.idAlmacen, al.estado, pr.nombre, pr.idSagarpa, l.localidad, al.bruto, al.tara,
        al.neto, lab.porcentaje, lab.st, lab.porcentajeDescripcion, lab.sfDescripcion,
        lab.stDescripcion, lab.adulteracionDescripcion, lab.hmf, lab.procesoDescripcion, lab.resultadoFinal,
        rs.resultado, rs.idResultadoFinal, fl.idFloracion, fl.floracion, '0' AS clasificacion, '0' AS tipo,
        CASE WHEN al.referencia > 0 THEN al.referencia ELSE '---' END AS referencia
        FROM  $almacen al
        INNER JOIN $laboratorio lab ON lab.idAlmacen = al.idAlmacen
        LEFT JOIN $almacenEncabezado ale ON  ale.idAlmacen = al.idalmacenEncabezado
        LEFT JOIN resultadofinal rs ON rs.idresultadoFinal = lab.resultadoFinal
        LEFT JOIN proveedor pr ON pr.idProveedor = ale.idProveedor
        LEFT JOIN direccion dir ON dir.idDireccion = pr.idDireccion
        LEFT JOIN localidades l ON l.idlocalidad = dir.idlocalidad
        LEFT JOIN floraciones fl ON fl.idFloracion = lab.idFloracion
        WHERE al.estado = '0' AND al.idAlmacen = :id";
$datos = $con->prepare($sql);
$datos->bindParam(':id', $id);
$datos->execute();
$calidad = new stdClass();

if ($datos->rowCount() >= 1) {
    while ($rs = $datos->fetch()) {
        $calidad->fecha = $rs["fecha"];
        $calidad->idAlmacen = $rs["idAlmacen"];
        $calidad->proveedor = utf8_encode($rs["nombre"]);
        $calidad->idSagarpa = $rs["idSagarpa"];
        $calidad->localidad = utf8_encode($rs["localidad"]);
        $calidad->bruto = $rs["bruto"];
        $calidad->tara = $rs["tara"];
        $calidad->neto = $rs["neto"];
        $calidad->porcentaje = $rs["porcentaje"];
        $calidad->sf = $rs["sfDescripcion"];
        $calidad->st = $rs["stDescripcion"];
        $calidad->adulteracionDescripcion = $rs["adulteracionDescripcion"];
        $calidad->hmf = $rs["hmf"];
        $calidad->resultado = $rs["resultado"];
        $calidad->idResultadoFinal = $rs["idResultadoFinal"];
        $calidad->idFloracion = $rs["idFloracion"];
        $calidad->floracion = $rs["floracion"];
        $calidad->clasificacion = $rs["clasificacion"];
        $calidad->tipo = $rs["tipo"];  
        $calidad->referencia = $rs["referencia"];  
    }       
}else{
    $calidad = 0;
}

echo $json_response = json_encode($calidad);