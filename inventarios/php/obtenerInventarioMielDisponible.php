<?php

include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO(); $con = $pdo->conectar();
$con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);


function obtenerInventarioMielDisponible($idTipoDeMiel = FALSE, $idZona = FALSE, $laboratorio = FALSE){
    global $con;
    $resultado = array(
        'encabezado' => array(
            'bruto' => 0,
            'tara' => 0,
            'neto' => 0
        ),
        'registros' => array(),
    );
    if($idTipoDeMiel) {
        switch($idTipoDeMiel) {
            case '1':
                $almacenencabezado_tabla = 'almacenencabezado';
                $almacen_tabla = 'almacen';
                $laboratorio_tabla = 'laboratorio';
                break;
            case '2':
                $almacenencabezado_tabla = 'almacenencabezado_organico';
                $almacen_tabla = 'almacen_organico';
                $laboratorio_tabla = 'laboratorio_organico';
                break;
            case '5':
                $almacenencabezado_tabla = 'almacenencabezado_mantequilla';
                $almacen_tabla = 'almacen_mantequilla';
                $laboratorio_tabla = 'laboratorio_mantequilla';
                break;
            case '6':
                $almacenencabezado_tabla = 'almacenencabezado_altiplano';
                $almacen_tabla = 'almacen_altiplano';
                $laboratorio_tabla = 'laboratorio_altiplano';
                break;
            case '7':
                $almacenencabezado_tabla = 'almacenencabezado_naranjo';
                $almacen_tabla = 'almacen_naranjo';
                $laboratorio_tabla = 'laboratorio_naranjo';
                break;
                
            case '8':
                $almacenencabezado_tabla = 'almacenencabezado_aguacate';
                $almacen_tabla = 'almacen_aguacate';
                $laboratorio_tabla = 'laboratorio_aguacate';
                break;
            case '9':
                $almacenencabezado_tabla = 'almacenencabezado_mezquite';
                $almacen_tabla = 'almacen_mezquite';
                $laboratorio_tabla = 'laboratorio_mezquite';
                break;
            default:
                throw new Exception('Tipo de miel inválido');
                break;
        }
    } else {
        throw new Exception('No se ha recibido el tipo de miel');
    }

    $sqlInventario = "SELECT al.idAlmacen AS folio, ae.folio AS entrada, ae.fecha, pr.nombre AS Proveedor, 
        pr.idSagarpa, lo.localidad AS localidad, al.pesoLista, al.bruto, al.tara, al.neto, z.nombre AS zona
        FROM $almacenencabezado_tabla ae 
        LEFT JOIN proveedor pr 
        ON pr.idProveedor = ae.idProveedor 
        LEFT JOIN direccion dr 
        ON dr.idDireccion = pr.idDireccion
        LEFT JOIN localidades lo ON lo.idlocalidad = dr.idlocalidad
        LEFT JOIN $almacen_tabla al on al.idAlmacenEncabezado= ae.idAlmacen
        LEFT JOIN zonastambores z on z.idZonaTambor = al.zona
        WHERE al.estado = '0'";
    if($idZona) {
        $sqlInventario .= " AND z.idZonaTambor = " . $idZona;
    }
    $sqlInventario .= " ORDER BY al.idAlmacen ASC";
    $query = $con->prepare($sqlInventario);
    $query->execute();

    if($query == FALSE) {
        throw new Exception($con->errorInfo());
    }
    $registros = $query->fetchAll(PDO::FETCH_ASSOC);

    foreach($registros as $tambor) {
        $resultado['encabezado']['bruto'] += floatval($tambor['bruto']);
        $resultado['encabezado']['tara'] += floatval($tambor['tara']);
        $resultado['encabezado']['neto'] += floatval($tambor['neto']);

        if ($laboratorio) {
            $sqlLaboratorio = $con->prepare("SELECT lab.porcentaje, lab.sf, lab.st,
                lab.c13, lab.hmf, lab.color, lab.tt, lab.micro,
                f.floracion, rf.resultado
                FROM $almacen_tabla al
                LEFT JOIN $laboratorio_tabla lab 
                on lab.idAlmacen = al.idAlmacen
                LEFT JOIN floraciones fl
                ON fl.idFloracion = lab.idFloracion
                LEFT JOIN $almacenencabezado_tabla ale
                on ale.idAlmacen = al.idAlmacenEncabezado
                LEFT JOIN floraciones f ON f.idFloracion = lab.idFloracion
				LEFT JOIN resultadofinal rf ON rf.idresultadoFinal = lab.resultadoFinal
                WHERE al.idAlmacen = :idAlmacen");
            $sqlLaboratorio->bindParam(':idAlmacen', $tambor['folio']);
            $sqlLaboratorio->execute();

            if($sqlLaboratorio == FALSE) {
                throw new Exception($con->errorInfo());
            } else {
                $tambor['laboratorio'] = $sqlLaboratorio->fetch(PDO::FETCH_ASSOC);
            }
        }

        array_push($resultado['registros'], $tambor);
    }

    return $resultado;
}