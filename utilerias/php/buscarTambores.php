<?php

include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$con = $pdo->conectar();

try {
    if (!isset($_GET['tambo1']) || !isset($_GET['tambo2']) || !isset($_GET['idTipoDeMiel'])) {
        throw new Exception('No se especificaron parámetros');
    } else {
        $tambo1 = $_GET["tambo1"];
        $tambo2 = $_GET["tambo2"];
        $idTipoDeMiel = $_GET['idTipoDeMiel'];
        if (isset($_GET['recipiente'])) {
            $recipiente = $_GET['recipiente'];
        }
    }

    if ($recipiente == '1') {
        switch ($idTipoDeMiel) {
            case '1':
                $almacenencabezado_tabla = 'almacenencabezado';
                $almacen_tabla = 'almacen';
                $tamboresexperimentales_tabla = 'tamboresexperimentales';
                $tamboreslotes_tabla = 'tamboreslotes';
                break;
            case '2':
                $almacenencabezado_tabla = 'almacenencabezado_organico';
                $almacen_tabla = 'almacen_organico';
                $tamboresexperimentales_tabla = 'tamboresexperimentales_organico';
                $tamboreslotes_tabla = 'tamboreslotes_organico';
                break;
            case '5':
                $almacenencabezado_tabla = 'almacenencabezado_mantequilla';
                $almacen_tabla = 'almacen_mantequilla';
                $tamboresexperimentales_tabla = 'tamboresexperimentales_mantequilla';
                $tamboreslotes_tabla = 'tamboreslotes_mantequilla';
                break;
            case '6':
                $almacenencabezado_tabla = 'almacenencabezado_altiplano';
                $almacen_tabla = 'almacen_altiplano';
                $tamboresexperimentales_tabla = 'tamboresexperimentales_altiplano';
                $tamboreslotes_tabla = 'tamboreslotes_altiplano';
                break;
            case '7':
                $almacenencabezado_tabla = 'almacenencabezado_naranjo';
                $almacen_tabla = 'almacen_naranjo';
                $tamboresexperimentales_tabla = 'tamboresexperimentales_naranjo';
                $tamboreslotes_tabla = 'tamboreslotes_naranjo';
                break;
                
            case '8':
                $almacenencabezado_tabla = 'almacenencabezado_aguacate';
                $almacen_tabla = 'almacen_aguacate';
                $tamboresexperimentales_tabla = 'tamboresexperimentales_aguacate';
                $tamboreslotes_tabla = 'tamboreslotes_aguacate';
                break;
            case '9':
                $almacenencabezado_tabla = 'almacenencabezado_mezquite';
                $almacen_tabla = 'almacen_mezquite';
                $tamboresexperimentales_tabla = 'tamboresexperimentales_mezquite';
                $tamboreslotes_tabla = 'tamboreslotes_mezquite';
                break;

            default:
                throw new Exception('Tipo de miel inválido');
                break;
        }
    } else if ($recipiente == '2') {
        switch ($idTipoDeMiel) {
            case '1':
                $almacenencabezado_tabla = 'cubetasencabezado';
                $almacen_tabla = 'cubetasdetalle';
                break;
            case '2':
                $almacenencabezado_tabla = 'cubetasencabezado_organico';
                $almacen_tabla = 'cubetasdetalle_organico';
                break;
            case '5':
                $almacenencabezado_tabla = 'cubetasencabezado_mantequilla';
                $almacen_tabla = 'cubetasdetalle_mantequilla';
                break;
            case '6':
                $almacenencabezado_tabla = 'cubetasencabezado_altiplano';
                $almacen_tabla = 'cubetasdetalle_altiplano';
                break;
            case '7':
                $almacenencabezado_tabla = 'cubetasencabezado_naranjo';
                $almacen_tabla = 'cubetasdetalle_naranjo';
                break;
                
            case '8':
                $almacenencabezado_tabla = 'cubetasencabezado_aguacate';
                $almacen_tabla = 'cubetasdetalle_aguacate';
                break;
            case '9':
                $almacenencabezado_tabla = 'cubetasencabezado_mezquite';
                $almacen_tabla = 'cubetasdetalle_mezquite';
                break;
            default:
                throw new Exception('Tipo de miel inválido');
                break;
        }
    }

    if ($recipiente == '1') {
        $sql = $con->prepare("SELECT al.idAlmacen, 'T' AS identificador, al.idAlmacen Folio, ae.folio Entrada,ae.fecha Fecha,  pr.nombre Proveedor,
    pr.idSagarpa, lo.localidad Localidad, z.nombre AS zona, al.pesoLista Lista, al.bruto Bruto, al.tara Tara,
    al.neto Neto, al.precio Precio, te.idLoteExperimental LExperimental, tl.idLoteInterno LInterno, '$idTipoDeMiel' AS idTipoDeMiel
    FROM $almacenencabezado_tabla ae 
    LEFT JOIN proveedor pr ON pr.idProveedor = ae.idProveedor 
    LEFT JOIN direccion dr ON dr.idDireccion = pr.idDireccion
    LEFT JOIN localidades lo ON lo.idlocalidad = dr.idlocalidad
    LEFT JOIN $almacen_tabla al ON al.idAlmacenEncabezado= ae.idAlmacen
    LEFT JOIN $tamboresexperimentales_tabla te ON te.folioTambor = al.idAlmacen AND te.clasificacion = 0 AND te.tipo = '0'
    LEFT JOIN $tamboreslotes_tabla tl ON tl.folioTambor = al.idAlmacen AND tl.clasificacion = 0 AND tl.tipo = '0'
    LEFT JOIN zonastambores z ON z.idZonaTambor = al.zona
    WHERE al.idAlmacen BETWEEN :tambor1 AND :tambor2 
    ORDER BY al.idAlmacen ASC");
    } else if ($recipiente == '2') { 
        $sql = $con->prepare("SELECT al.idAlmacen, 'C' AS identificador, CONCAT('C-', al.idAlmacen) AS Folio, '' AS Entrada,ae.fecha Fecha,  pr.nombre Proveedor,
    pr.idSagarpa, lo.localidad Localidad, z.nombre AS zona, al.pesoLista Lista, al.bruto Bruto, al.tara Tara,
    al.neto Neto, al.precio Precio, '' AS LExperimental, '' AS LInterno, '$idTipoDeMiel' AS idTipoDeMiel
    FROM $almacenencabezado_tabla ae 
    LEFT JOIN proveedor pr ON pr.idProveedor = ae.idProveedor 
    LEFT JOIN direccion dr ON dr.idDireccion = pr.idDireccion
    LEFT JOIN localidades lo ON lo.idlocalidad = dr.idlocalidad
    LEFT JOIN $almacen_tabla al ON al.idAlmacenEncabezado= ae.idAlmacen
    LEFT JOIN zonastambores z ON z.idZonaTambor = al.zona
    WHERE al.idAlmacen BETWEEN :tambor1 AND :tambor2
    ORDER BY al.idAlmacen ASC");
    }


    $sql->bindParam(':tambor1', $tambo1);
    $sql->bindParam(':tambor2', $tambo2);
    $sql->execute();

    if ($sql == false) {
        throw new Exception($con->errorInfo());
    } else {
        $tambores = $sql->fetchAll(PDO::FETCH_ASSOC);
    }

    if (count($tambores) == 0) {
        echo json_encode(['error' => false, 'message' => 'No hay tambores con los folios especificados']);
    } else {
        echo json_encode(['error' => false, 'tambores' => $tambores]);
    }
} catch (Exception $e) {
    echo json_encode(['error' => true, 'message' => $e->getMessage()]);
}
