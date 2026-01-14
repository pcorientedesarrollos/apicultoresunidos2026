<?php

include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$con = $pdo->conectar();

$idLoteInterno = $_GET["idLoteInterno"];
$tipoMiel = $_GET['miel'];

if ($tipoMiel == '1') {
    $calidad = 'calidad';
    $entradaysalida_tabla = 'entradaysalida';
    $tamboreslotes = 'tamboreslotes';
    $almacen = 'almacen';
    $almacenencabezado = 'almacenencabezado';
} else if ($tipoMiel == '5') {
    $calidad = 'calidad_mantequilla';
    $entradaysalida_tabla = 'entradaysalida_mantequilla';
    $tamboreslotes = 'tamboreslotes_mantequilla';
    $almacen = 'almacen_mantequilla';
    $almacenencabezado = 'almacenencabezado_mantequilla';
} else if ($tipoMiel == '6') {
    $calidad = 'calidad_altiplano';
    $entradaysalida_tabla = 'entradaysalida_altiplano';
    $tamboreslotes = 'tamboreslotes_altiplano';
    $almacen = 'almacen_altiplano';
    $almacenencabezado = 'almacenencabezado_altiplano';
} else if ($tipoMiel == '7') {
    $calidad = 'calidad_naranjo';
    $entradaysalida_tabla = 'entradaysalida_naranjo';
    $tamboreslotes = 'tamboreslotes_naranjo';
    $almacen = 'almacen_naranjo';
    $almacenencabezado = 'almacenencabezado_naranjo';
}  else if ($tipoMiel == '8') {
    $calidad = 'calidad_aguacate';
    $entradaysalida_tabla = 'entradaysalida_aguacate';
    $tamboreslotes = 'tamboreslotes_aguacate';
    $almacen = 'almacen_aguacate';
    $almacenencabezado = 'almacenencabezado_aguacate';
} else if ($tipoMiel == '9') {
    $calidad = 'calidad_mezquite';
    $entradaysalida_tabla = 'entradaysalida_mezquite';
    $tamboreslotes = 'tamboreslotes_mezquite';
    $almacen = 'almacen_mezquite';
    $almacenencabezado = 'almacenencabezado_mezquite';
} else {
    $calidad = 'calidad_organico';
    $entradaysalida_tabla = 'entradaysalida_organico';
    $tamboreslotes = 'tamboreslotes_organico';
    $almacen = 'almacen_organico';
    $almacenencabezado = 'almacenencabezado_organico';
}

$sql = "SELECT c.fechaEnvasado, t.fechaSalida, t.*, c.kilosTotales,  CONCAT(ep.empresa,', ',ep.pais) AS destino
        FROM $calidad c
        LEFT JOIN $entradaysalida_tabla e ON e.idLoteInterno = c.idLoteInterno
        LEFT JOIN trazabilidadsalida t ON t.idLoteInterno = c.idLoteInterno
	    LEFT JOIN empresasypaises ep ON ep.idEmpresaPais = t.idEmpresaPais
        WHERE c.idLoteInterno = :idLoteInterno ";

$datos = $con->prepare($sql);
$datos->bindParam(':idLoteInterno', $idLoteInterno);
$datos->execute();

if ($datos == false) {
    echo mysql_error();
} else {
    $tSalida = new stdClass();
    while ($rs = $datos->fetch()) {
        $tSalida->fechaEnvasado = $rs["fechaEnvasado"];
        $tSalida->fechaSalida = $rs["fechaSalida"];
        $tSalida->idLoteInterno = $rs["idLoteInterno"];
        $tSalida->homogeneizado = $rs["homogeneizado"];
        $tSalida->idEmpresaPais = $rs["idEmpresaPais"];
        $tSalida->destino = $rs["destino"];
        $tSalida->kilosSalida = $rs["kilosSalida"];
        $tSalida->kgExportar = $rs["kgExportar"];
        $tSalida->kilosTotales = $rs["kilosTotales"];
        $tSalida->tipoMiel = $rs["tipoMiel"];
        $tSalida->detalleTrazabilidadSalida = array();

        if ($tSalida->fechaEnvasado == "1969-12-31") {
            $tSalida->fechaEnvasado = " ";
        } else {
            $tSalida->fechaEnvasado = $rs["fechaEnvasado"];
        }

        $sqlD = "SELECT ale.idAlmacen AS id, pr.idSagarpa, SUM(al.neto)AS volumen
                FROM $tamboreslotes tex
                INNER JOIN $almacen al ON al.idAlmacen = tex.folioTambor
                INNER JOIN $almacenencabezado ale ON ale.idAlmacen = al.idAlmacenEncabezado
                INNER JOIN proveedor pr ON pr.idProveedor = ale.idProveedor
                INNER JOIN $calidad ca ON ca.idLoteInterno = tex.idLoteInterno
                WHERE tex.idLoteInterno = :idLoteInterno AND tex.tipo = '0'
                GROUP BY  idSagarpa
        UNION
        SELECT  al.consecutivo AS id, pr.idSagarpa, SUM(al.neto)AS volumen
                FROM $tamboreslotes tex
                INNER JOIN almacensobrantes al ON al.consecutivo = tex.folioTambor AND al.sobrante = tex.clasificacion
                INNER JOIN proveedor pr
                INNER JOIN $calidad ca ON ca.idLoteInterno = tex.idLoteInterno
                WHERE tex.idLoteInterno = :idLoteInterno AND tex.tipo = '1' AND al.tipoDeMiel = '" . $tipoMiel . "' AND pr.idProveedor = '83'
                GROUP BY  idSagarpa";

        $data = $con->prepare($sqlD);
        $data->bindParam(':idLoteInterno', $idLoteInterno);
        $data->execute();
        $cont = 0;
        if ($data == false) {
            throw new Exception($con->errorInfo());
        } else {
            while ($rs = $data->fetch()) {
                $detalleTrazabilidadSalida = new stdClass();
                $detalleTrazabilidadSalida->idSagarpa = $rs["idSagarpa"];
                $detalleTrazabilidadSalida->volumen = $rs["volumen"];
                $tSalida->detalleTrazabilidadSalida[$cont] = $detalleTrazabilidadSalida;
                $cont++;
            }
        }
    }
    echo json_encode($tSalida);
}
