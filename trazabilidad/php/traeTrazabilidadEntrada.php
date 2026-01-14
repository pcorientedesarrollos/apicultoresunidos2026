<?php

include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$con = $pdo->conectar();

$idLoteInterno = $_GET["idLoteInterno"];

$tipo = $_GET['tipo'];

switch ($tipo) {
    case '1':
        $tamboreslotes = 'tamboreslotes';
        $almacen = 'almacen';
        $almacenencabezado = 'almacenencabezado';
        $entradaysalida = 'entradaysalida';
        $calidad = 'calidad';
        break;
    case '2':
        $tamboreslotes = 'tamboreslotes_organico';
        $almacen = 'almacen_organico';
        $almacenencabezado = 'almacenencabezado_organico';
        $entradaysalida = 'entradaysalida_organico';
        $calidad = 'calidad_organico';
        break;
    case '5':
        $tamboreslotes = 'tamboreslotes_mantequilla';
        $almacen = 'almacen_mantequilla';
        $almacenencabezado = 'almacenencabezado_mantequilla';
        $entradaysalida = 'entradaysalida_mantequilla';
        $calidad = 'calidad_mantequilla';
        break;
    case '6':
        $tamboreslotes = 'tamboreslotes_altiplano';
        $almacen = 'almacen_altiplano';
        $almacenencabezado = 'almacenencabezado_altiplano';
        $entradaysalida = 'entradaysalida_altiplano';
        $calidad = 'calidad_altiplano';
        break;
    case '7':
        $tamboreslotes = 'tamboreslotes_naranjo';
        $almacen = 'almacen_naranjo';
        $almacenencabezado = 'almacenencabezado_naranjo';
        $entradaysalida = 'entradaysalida_naranjo';
        $calidad = 'calidad_naranjo';
        break;
    case '8':
        $tamboreslotes = 'tamboreslotes_aguacate';
        $almacen = 'almacen_aguacate';
        $almacenencabezado = 'almacenencabezado_aguacate';
        $entradaysalida = 'entradaysalida_aguacate';
        $calidad = 'calidad_aguacate';
        break;
    case '9':
        $tamboreslotes = 'tamboreslotes_mezquite';
        $almacen = 'almacen_mezquite';
        $almacenencabezado = 'almacenencabezado_mezquite';
        $entradaysalida = 'entradaysalida_mezquite';
        $calidad = 'calidad_mezquite';
        break;
}
$tEntrada = new stdClass();
$tEntrada->kilosTotales = 0;
$tEntrada->trazabilidadEntrada = array();

// $sqlE = "SELECT max(ale.fecha) AS fecha, tex.idLoteInterno, pr.idSagarpa, c.marcaFinalCliente,
//                          UCASE(CONCAT(l.localidad,', ',es.estado)) AS domicilio, SUM(al.neto)AS kilosP
//                          FROM $tamboreslotes tex
//                          INNER JOIN $almacen al ON al.idAlmacen = tex.folioTambor
//                          INNER JOIN $almacenencabezado ale ON ale.idAlmacen = al.idAlmacenEncabezado
//                          INNER JOIN proveedor pr ON pr.idProveedor = ale.idProveedor
//                          INNER JOIN direccion d ON d.idDireccion = pr.idDireccion
//                          INNER JOIN estados es ON es.idEstado = d.idEstado
//                          INNER JOIN localidades l ON l.idlocalidad = d.idlocalidad
//                          INNER JOIN $calidad c ON c.idLoteInterno = tex.idLoteInterno
//                          LEFT JOIN $entradaysalida e ON e.idLoteInterno = tex.idLoteInterno
//                          WHERE tex.idLoteInterno = :idLoteInterno AND tex.tipo = '0'
//                          GROUP BY idSagarpa";

$sqlE = "SELECT MIN(ale.fecha) AS fecha, tex.idLoteInterno, pr.idSagarpa, c.marcaFinalCliente,
        UCASE(CONCAT(l.localidad,', ',es.estado)) AS domicilio, SUM(al.neto)AS kilosP
        FROM $almacenencabezado ale 
        LEFT JOIN $almacen al ON ale.idAlmacen = al.idAlmacenEncabezado
        LEFT JOIN $tamboreslotes tex ON tex.folioTambor = al.idAlmacen AND tex.tipo = '0' AND tex.clasificacion = '0'
        LEFT JOIN proveedor pr ON pr.idProveedor = ale.idProveedor
        LEFT JOIN direccion d ON d.idDireccion = pr.idDireccion
        LEFT JOIN estados es ON es.idEstado = d.idEstado
        LEFT JOIN localidades l ON l.idlocalidad = d.idlocalidad
        LEFT JOIN $calidad c ON c.idLoteInterno = tex.idLoteInterno
        WHERE tex.idLoteInterno = :idLoteInterno
        GROUP BY idSagarpa
    UNION
    SELECT MIN(al.fecha) AS fecha, tex.idLoteInterno, pr.idSagarpa, c.marcaFinalCliente,
            UCASE(CONCAT(l.localidad,', ',es.estado)) AS domicilio, SUM(alsde.neto)AS kilosP
    FROM almacensobrantesencabezado al 
    LEFT JOIN almacensobrantes alsde ON alsde.consecutivoEntrada = al.idEncabezadoSobrante
    LEFT JOIN $tamboreslotes tex ON tex.folioTambor = alsde.consecutivo AND tex.tipo = '1' AND tex.clasificacion = alsde.sobrante
    INNER JOIN proveedor pr
    LEFT JOIN direccion d ON d.idDireccion = pr.idDireccion
    LEFT JOIN estados es ON es.idEstado = d.idEstado
    LEFT JOIN localidades l ON l.idlocalidad = d.idlocalidad
    LEFT JOIN $calidad c ON c.idLoteInterno = tex.idLoteInterno
    WHERE tex.idLoteInterno = :idLoteInterno AND al.tipoDeMiel = '" . $tipo . "' AND pr.idProveedor = '83'
    GROUP BY idSagarpa";

// $sqlE = "SELECT max(ale.fecha) AS fecha, tex.idLoteInterno, pr.idSagarpa, c.marcaFinalCliente,
//                 UCASE(CONCAT(l.localidad,', ',es.estado)) AS domicilio, SUM(al.neto)AS kilosP
//                 FROM $tamboreslotes tex
//                 INNER JOIN $almacen al ON al.idAlmacen = tex.folioTambor
//                 INNER JOIN $almacenencabezado ale ON ale.idAlmacen = al.idAlmacenEncabezado
//                 left JOIN proveedor pr ON pr.idProveedor = ale.idProveedor
//                 left JOIN direccion d ON d.idDireccion = pr.idDireccion
//                 left JOIN estados es ON es.idEstado = d.idEstado
//                 left JOIN localidades l ON l.idlocalidad = d.idlocalidad
//                 left JOIN $calidad c ON c.idLoteInterno = tex.idLoteInterno
//                 LEFT JOIN $entradaysalida e ON e.idLoteInterno = tex.idLoteInterno
//                 WHERE tex.idLoteInterno = :idLoteInterno AND tex.tipo = '0'
//                 GROUP BY idSagarpa
//             UNION
//                 SELECT max(al.fecha) AS fecha, tex.idLoteInterno, pr.idSagarpa, c.marcaFinalCliente,
//                         UCASE(CONCAT(l.localidad,', ',es.estado)) AS domicilio, SUM(al.neto)AS kilosP
//                         FROM $tamboreslotes tex
//                         INNER JOIN almacensobrantes al ON al.consecutivo = tex.folioTambor AND al.sobrante = tex.clasificacion
//                         INNER JOIN proveedor pr
//                         LEFT JOIN direccion d ON d.idDireccion = pr.idDireccion
//                         LEFT JOIN estados es ON es.idEstado = d.idEstado
//                         LEFT JOIN localidades l ON l.idlocalidad = d.idlocalidad
//                         LEFT JOIN $calidad c ON c.idLoteInterno = tex.idLoteInterno
//                         LEFT JOIN $entradaysalida e ON e.idLoteInterno = tex.idLoteInterno
//                         WHERE tex.idLoteInterno = :idLoteInterno AND tex.tipo = '1' AND al.tipoDeMiel = '" . $tipo . "' AND pr.idProveedor = '83'
//                         GROUP BY idSagarpa";

$dato = $con->prepare($sqlE);
$dato->bindParam(':idLoteInterno', $idLoteInterno);
$dato->execute();
$cont = 0;
if ($dato == false) {
    echo 'Error al ingresar';
} else {
    while ($rs = $dato->fetch()) {
        $trazabilidadEntrada = new stdClass();
        $trazabilidadEntrada->fecha = $rs["fecha"];
        $trazabilidadEntrada->idLoteInterno = $rs["idLoteInterno"];
        $trazabilidadEntrada->marcaFinalCliente = $rs["marcaFinalCliente"];
        $trazabilidadEntrada->idSagarpa = $rs["idSagarpa"];
        $trazabilidadEntrada->domicilio = $rs["domicilio"];
        $trazabilidadEntrada->kilosP = $rs["kilosP"];
        $tEntrada->trazabilidadEntrada[$cont] = $trazabilidadEntrada;
        $cont++;
        $tEntrada->kilosTotales += $trazabilidadEntrada->kilosP;
    }
}
echo json_encode($tEntrada);
