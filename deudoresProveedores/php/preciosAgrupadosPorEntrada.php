<?php

include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$con = $pdo->conectar();

$idEntrada = $_GET['idEntrada'];
$origen = $_GET['origen'];

// $query = "SELECT COUNT(idAlmacen) AS totalTambos, SUM(neto) AS totalKilos, precio 
//           FROM almacen WHERE idAlmacenEncabezado = :idEntrada GROUP BY precio";

if($origen == 'Entrada' || $origen == 'Entrada Organico'|| $origen == 'Entrada Mantequilla'|| $origen == 'Entrada Altiplano'|| $origen == 'Entrada Naranjo'|| $origen == 'Entrada Aguacate'|| $origen == 'Entrada Mezquite'){

    switch($origen){
        case 'Entrada':
        $almacen = 'almacen';
        $cubetasDetalle = 'cubetasdetalle';
        $cubetasEncabezado = 'cubetasencabezado';
        break;
        case 'Entrada Organico':
        $almacen = 'almacen_organico';
        $cubetasDetalle = 'cubetasdetalle_organico';
        $cubetasEncabezado = 'cubetasencabezado_organico';
        break;
        case 'Entrada Mantequilla':
        $almacen = 'almacen_mantequilla';
        $cubetasDetalle = 'cubetasdetalle_mantequilla';
        $cubetasEncabezado = 'cubetasencabezado_mantequilla';
        break;
        case 'Entrada Altiplano':
            $almacen = 'almacen_altiplano';
            $cubetasDetalle = 'cubetasdetalle_altiplano';
            $cubetasEncabezado = 'cubetasencabezado_altiplano';
            break;
        case 'Entrada Naranjo':
            $almacen = 'almacen_naranjo';
            $cubetasDetalle = 'cubetasdetalle_naranjo';
            $cubetasEncabezado = 'cubetasencabezado_naranjo';
            break;
            
        case 'Entrada Aguacate':
            $almacen = 'almacen_aguacate';
            $cubetasDetalle = 'cubetasdetalle_aguacate';
            $cubetasEncabezado = 'cubetasencabezado_aguacate';
            break;
        case 'Entrada Mezquite':
            $almacen = 'almacen_mezquite';
            $cubetasDetalle = 'cubetasdetalle_mezquite';
            $cubetasEncabezado = 'cubetasencabezado_mezquite';
            break;
    }

    $query = "SELECT COUNT(ad.idAlmacen) AS totalTambos, SUM(ad.neto) AS totalKilos, ad.precio, 'Tambor' AS referencia
            FROM $almacen ad WHERE ad.idAlmacenEncabezado = :idEntrada GROUP BY ad.precio
            UNION
            SELECT COUNT(cd.idAlmacen) AS totalTambos, SUM(cd.neto) AS totalKilos, cd.precio, 'Cubeta' AS referencia
            FROM $cubetasDetalle cd 
            LEFT JOIN $cubetasEncabezado ce ON ce.idAlmacen = cd.idAlmacenEncabezado
            WHERE ce.folioEntradaTambor = :idEntrada GROUP BY cd.precio";

            $datos = $con->prepare($query);
            $datos->bindParam(':idEntrada', $idEntrada);
            $datos->execute();

            while ($row = $datos->fetch()) {
                $info = new stdClass();
                $info->totalTambos = $row["totalTambos"];
                $info->referencia = $row["referencia"];    
                $info->totalKilos = $row["totalKilos"];
                $info->precio = $row["precio"];    
                $info->importe = $info->totalKilos * $info->precio;   
                $lista[] = $info;
            }
    echo $json_response = json_encode($lista);
}else if($origen == 'EntradaCubeta' || $origen == 'Entrada Cubeta Organico' || $origen == 'Entrada Cubeta Mantequilla' || $origen == 'Entrada Cubeta Altiplano' || $origen == 'Entrada Cubeta Naranjo'|| $origen == 'Entrada Cubeta Aguacate' || $origen == 'Entrada Cubeta Mezquite'){

    switch($origen){
        case 'EntradaCubeta':
        $cubetasDetalle = 'cubetasdetalle';
        $cubetasEncabezado = 'cubetasencabezado';
        break;
        case 'Entrada Cubeta Organico':
        $cubetasDetalle = 'cubetasdetalle_organico';
        $cubetasEncabezado = 'cubetasencabezado_organico';
        break;
        case 'Entrada Cubeta Mantequilla':
        $cubetasDetalle = 'cubetasdetalle_mantequilla';
        $cubetasEncabezado = 'cubetasencabezado_mantequilla';
        break;
        case 'Entrada Cubeta Altiplano':
            $cubetasDetalle = 'cubetasdetalle_altiplano';
            $cubetasEncabezado = 'cubetasencabezado_altiplano';
            break;
        case 'Entrada Cubeta Naranjo':
            $cubetasDetalle = 'cubetasdetalle_naranjo';
            $cubetasEncabezado = 'cubetasencabezado_naranjo';
            break;
            
        case 'Entrada Cubeta Aguacate':
            $cubetasDetalle = 'cubetasdetalle_aguacate';
            $cubetasEncabezado = 'cubetasencabezado_aguacate';
            break;
        case 'Entrada Cubeta Mezquite':
            $cubetasDetalle = 'cubetasdetalle_mezquite';
            $cubetasEncabezado = 'cubetasencabezado_mezquite';
            break;
    }

    $query = "SELECT COUNT(cd.idAlmacen) AS totalTambos, SUM(cd.neto) AS totalKilos, cd.precio, 'Cubeta' AS referencia
            FROM $cubetasDetalle cd 
            LEFT JOIN $cubetasEncabezado ce ON ce.idAlmacen = cd.idAlmacenEncabezado
            WHERE ce.idAlmacen = :idEntrada GROUP BY cd.precio";

    $datos = $con->prepare($query);
    $datos->bindParam(':idEntrada', $idEntrada);
    $datos->execute();

    while ($row = $datos->fetch()) {
        $info = new stdClass();
        $info->totalTambos = $row["totalTambos"];
        $info->referencia = $row["referencia"];    
        $info->totalKilos = $row["totalKilos"];
        $info->precio = $row["precio"];    
        $info->importe = $info->totalKilos * $info->precio;   
        $lista[] = $info;
    }
echo $json_response = json_encode($lista);
}
?>