<?php

include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$con = $pdo->conectar();

try {

    if (!isset($_GET['idLoteInterno'])) {
        throw new Exception('No se especificó el lote interno');
    }
    $idLoteInterno = $_GET["idLoteInterno"];

    // Nombre de las tablas
    if (isset($_GET['organica'])) {
        $calidad_tabla = 'calidad_organico';
        $tamboreslotes_tabla = 'tamboreslotes_organico';
        $almacen_tabla = 'almacen_organico';
        $almacenencabezado_tabla = 'almacenencabezado_organico';
        $laboratorio_tabla = 'laboratorio_organico';
    } else {
        $calidad_tabla = 'calidad';
        $tamboreslotes_tabla = 'tamboreslotes';
        $almacen_tabla = 'almacen';
        $almacenencabezado_tabla = 'almacenencabezado';
        $laboratorio_tabla = 'laboratorio';
    }

    $dats = $con->prepare("SELECT idLoteInterno, fechaProceso, fechaEnvasado, loteCliente,
        marcaFinalCliente, observaciones, muestraInterna, idLoteExperimental, numeroDeTambores,
        kilosTotales FROM $calidad_tabla  WHERE idLoteInterno = :idLoteInterno");
    $dats->bindParam(':idLoteInterno', $idLoteInterno);
    $dats->execute();

    if ($dats == false) {
        throw new Exception($con->errorInfo());
    } else {
        $lote = $dats->fetch(PDO::FETCH_ASSOC);
        if ($lote == false) {
            throw new Exception('El lote es 0');
        }
        $lote['informacionCalidad'] = array();

        if ($lote['fechaProceso'] == "1969-12-31") {
            $lote['fechaProceso'] = "Aún no asignada";
        }

        if ($lote['fechaEnvasado'] == "1969-12-31") {
            $lote['fechaEnvasado'] = "Aún no asignada";
        }
    }

    $sqlAlmacenDetalle = "SELECT tex.folioTambor, tex.idLoteInterno, al.bruto, al.tara,
        al.neto, al.humedad, lab.color, lab.idFloracion, ale.fecha, pr.nombre, pr.idSagarpa, l.localidad, lab.porcentaje,
        lab.sfDescripcion, lab.stDescripcion, lab.adulteracionDescripcion, lab.hmf,
        lab.resultadoFinal, rs.resultado, rs.idResultadoFinal FROM $tamboreslotes_tabla tex
        INNER JOIN $almacen_tabla al ON al.idAlmacen = tex.folioTambor
        INNER JOIN $almacenencabezado_tabla ale ON ale.idAlmacen = al.idAlmacenEncabezado
        LEFT JOIN proveedor pr ON pr.idProveedor = ale.idProveedor
        LEFT JOIN direccion d ON d.idDireccion = pr.idDireccion
        LEFT JOIN localidades l ON l.idlocalidad = d.idlocalidad
        INNER JOIN  $laboratorio_tabla lab ON lab.idAlmacen = al.idAlmacen
        INNER JOIN resultadofinal rs ON rs.idresultadoFinal = lab.resultadoFinal
        WHERE tex.idLoteInterno = :idLoteInterno";
    $datus = $con->prepare($sqlAlmacenDetalle);
    $datus->bindParam(':idLoteInterno', $idLoteInterno);
    $datus->execute();

    if ($datus == false) {
        throw new Exception($con->errorInfo());
    }

    foreach ($datus->fetchAll(PDO::FETCH_ASSOC) as $e) {
        array_push($lote['informacionCalidad'], $e);
    }

    echo json_encode($lote);

} catch (Exception $e) {
    echo $e->getMessage();
}