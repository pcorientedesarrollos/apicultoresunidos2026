<?php

include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$con = $pdo->conectar();
$post = file_get_contents('php://input');

if ($post) {
    $info = json_decode($post);
    try {
        if ($info->almacen == '1') { //almacén
            switch ($info->miel) {
                case '1':
                    $tabla = 'conformacionhomogeneo_folios';
                    $almacen = 'almacen';
                    $almacenencabezado = 'almacenencabezado';
                    break;
                case '2':
                    $tabla = 'conformacionhomogeneo_folios_organico';
                    $almacen = 'almacen_organico';
                    $almacenencabezado = 'almacenencabezado_organico';
                    break;
            }
        } else if ($info->almacen == '2') { //sobrantes
            switch ($info->miel) {
                case '1':
                    $tabla = 'conformacionhomogeneo_folios';
                    $almacen = 'almacensobrantes';
                    $almacenencabezado = 'almacensobrantesencabezado';
                    break;
                case '2':
                    $tabla = 'conformacionhomogeneo_folios_organico';
                    $almacen = 'almacensobrantes';
                    $almacenencabezado = 'almacensobrantesencabezado';
                    break;
            }
        }

        $con->beginTransaction();
        if ($info->tipo == '1') { //Homogeneo
            $sqlVerificarFolio = $con->prepare("SELECT folio FROM $tabla WHERE folio BETWEEN :folioUno AND :folioDos AND almacen = :almacen AND sobrante = :sobrante");
            $sqlVerificarFolio->bindParam(':folioUno', $info->folioUno);
            $sqlVerificarFolio->bindParam(':folioDos', $info->folioDos);
            $sqlVerificarFolio->bindParam(':almacen', $info->almacen);
            $sqlVerificarFolio->bindParam(':sobrante', $info->sobrante);
            $sqlVerificarFolio->execute();
            if ($sqlVerificarFolio->rowCount() >= 1) {
                //Cuando ya existe folio (enviar mensaje)
                $resultado = 1;
                echo json_encode(['error' => false, 'message' => '¡Éxito!', 'content' => $resultado]);
            } else {
                if ($info->almacen == '1') {
                    $sqlTraeDatos = $con->prepare("SELECT a.idAlmacen AS folio, p.nombre, l.localidad, 0 AS sobrante, '1' AS almacen
                    FROM $almacen a 
                    LEFT JOIN $almacenencabezado e ON a.idAlmacenEncabezado = e.idAlmacen
                    LEFT JOIN proveedor p ON p.idProveedor = e.idProveedor
                    LEFT JOIN direccion d ON d.idDireccion = p.idDireccion
                    LEFT JOIN localidades l ON l.idlocalidad = d.idlocalidad
                    WHERE a.idAlmacen BETWEEN :folioUno AND :folioDos");
                } else {
                    $sqlTraeDatos = $con->prepare("SELECT CONCAT(s.codigo, '-', a.consecutivo) AS folio, a.consecutivo, a.consecutivo, s.nombre, '--' AS localidad,
                    a.sobrante, '2' AS almacen
                FROM almacensobrantes a
                LEFT JOIN sobrantes s ON s.idSobrante = a.sobrante
                WHERE a.consecutivo BETWEEN :folioUno AND :folioDos AND a.sobrante = :sobrante AND a.tipoDeMiel = :miel");
                    $sqlTraeDatos->bindParam(':sobrante', $info->sobrante);
                    $sqlTraeDatos->bindParam(':miel', $info->miel);
                }

                $sqlTraeDatos->bindParam(':folioUno', $info->folioUno);
                $sqlTraeDatos->bindParam(':folioDos', $info->folioDos);
                $sqlTraeDatos->execute();
                if ($sqlTraeDatos->rowCount() >= 1) {
                    $resultado = $sqlTraeDatos->fetchAll(PDO::FETCH_ASSOC);
                    $con->commit();
                    echo json_encode(['error' => false, 'message' => '¡Éxito!', 'content' => $resultado]);
                } else {
                    $resultado = 0;
                    $con->commit();
                    echo json_encode(['error' => true, 'message' => '¡No existe el folio!', 'content' => $resultado]);
                }
            }
        } else if ($info->tipo == '2' || $info->tipo == '3' || $info->tipo == '4') { //reclasificado, experimental o lote terminado
            if ($info->almacen == '1') {
                $sqlTraeDatos = $con->prepare("SELECT a.idAlmacen AS folio, p.nombre, l.localidad, 0 AS sobrante, '1' AS almacen
            FROM $almacen a 
            LEFT JOIN $almacenencabezado e ON a.idAlmacenEncabezado = e.idAlmacen
            LEFT JOIN proveedor p ON p.idProveedor = e.idProveedor
            LEFT JOIN direccion d ON d.idDireccion = p.idDireccion
            LEFT JOIN localidades l ON l.idlocalidad = d.idlocalidad
            WHERE a.idAlmacen BETWEEN :folioUno AND :folioDos");
            } else {
                $sqlTraeDatos = $con->prepare("SELECT CONCAT(s.codigo, '-', a.consecutivo) AS folio, a.consecutivo, a.consecutivo, s.nombre, '--' AS localidad,
                a.sobrante, '2' AS almacen
                FROM almacensobrantes a
                LEFT JOIN sobrantes s ON s.idSobrante = a.sobrante
                WHERE a.consecutivo BETWEEN :folioUno AND :folioDos AND a.sobrante = :sobrante AND a.tipoDeMiel = :miel");
                $sqlTraeDatos->bindParam(':sobrante', $info->sobrante);
                $sqlTraeDatos->bindParam(':miel', $info->miel);
            }
            $sqlTraeDatos->bindParam(':folioUno', $info->folioUno);
            $sqlTraeDatos->bindParam(':folioDos', $info->folioDos);
            $sqlTraeDatos->execute();
            if ($sqlTraeDatos->rowCount() >= 1) {
                $resultado = $sqlTraeDatos->fetchAll(PDO::FETCH_ASSOC);
                $con->commit();
                echo json_encode(['error' => false, 'message' => '¡Éxito!', 'content' => $resultado]);
            } else {
                $resultado = 0;
                $con->commit();
                echo json_encode(['error' => true, 'message' => '¡No existe el folio!', 'content' => $resultado]);
            }
        }
    } catch (Exception $e) {
        $con->rollBack();
        echo json_encode(['error' => true, 'message' => $e->getMessage(), 'content' => []]);
    }
} else {
    exit();
}
