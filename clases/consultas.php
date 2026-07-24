<?php

$miSELECT = "SELECT ae.fecha, l.localidad AS Localidad, count(al.idAlmacen) AS Tambores, SUM(al.neto) AS Kgs,
                            COUNT(IF((lab.sf >= 1980 OR lab.sf BETWEEN 1576 AND 1979),'SFAprobado',NULL)) AS SFAprobado,
                            COUNT(IF(lab.sf < 1575,'1',NULL)) AS SFRechazado,
                            COUNT(IF(lab.st<1281,1,NULL)) AS STRechazados,
                            COUNT(IF(lab.hmf<=10,1,NULL)) AS HMFAprobado,
                            COUNT(IF(lab.hmf>10,1,NULL)) AS HMFRechazado,
                            COUNT(IF((lab.c13 < 983 OR lab.c13 BETWEEN 985 AND 100000),'Adulteracion',NULL)) AS Adulteracion,
                            COUNT(IF((lab.c13 BETWEEN 983 AND 984.79),'Delta 13',NULL)) AS Delta13,
                            COUNT(IF((lab.c13 BETWEEN 984.80 AND 984.99),'C4',NULL)) AS C4,
                            COUNT(IF(porcentaje <= 19.5, 'Humedad1',NULL )) AS menor195,
                            COUNT(IF((lab.porcentaje BETWEEN 19.6 AND 20),'Exportacion20',NULL)) AS Exportacion20,
                            COUNT(IF((lab.porcentaje BETWEEN 20.1 AND 20.5),'Exportacion205',NULL)) AS Exportacion205,
                            COUNT(IF((lab.porcentaje BETWEEN 20.6 AND 21),'Exportacion21',NULL)) AS Exportacion21,
                            COUNT(IF((lab.porcentaje BETWEEN 21.1 AND 22),'Exportacion20',NULL)) AS Exportacionmas21,
                            COUNT(IF(lab.porcentaje >22, 'RECHAZADOS',NULL )) AS Mayores22
                            FROM almacen al
                            LEFT JOIN almacenencabezado ae on ae.idAlmacen = al.idAlmacenEncabezado
                            LEFT JOIN proveedor p ON p.idProveedor = ae.idProveedor 
                            LEFT JOIN direccion d ON d.idDireccion = p.idDireccion 
                            LEFT JOIN localidades l ON l.idlocalidad = d.idlocalidad 
                            LEFT JOIN laboratorio lab ON lab.idAlmacen = al.idAlmacen";

$GLOBALS['miSELECT'];

class consultas
{

    function globales()
    {




        return $miSELECT;
    }

    function encabezadoPrecios($id, $cubeta, $tmp)
    {
        include_once '../DAOConeccion/conePDO.php';
        $pdo = new conePDO();
        $cn = $pdo->conectar();
        // include_once '../DAOConeccion/coneccion.php';
        // $cn = new Coneccion();

        if ($cubeta == 99999) {
            if ($tmp == 1) {
                $query = "SELECT ce.idAlmacen, rp.nombre, rp.idSagarpa, ce.fecha, ol.localidad, ce.totalCompra 
                FROM cubetasencabezado ce 
                    LEFT JOIN proveedor rp ON rp.idProveedor = ce.idProveedor 
                    LEFT JOIN direccion rd ON rd.idDireccion = rp.idDireccion
                    LEFT JOIN localidades ol ON ol.idlocalidad = rd.idlocalidad
                    WHERE ce.idAlmacen = '$id'";
            } else if ($tmp == 5){
                $query = "SELECT ce.idAlmacen, rp.nombre, rp.idSagarpa, ce.fecha, ol.localidad, ce.totalCompra 
                FROM cubetasencabezado_mantequilla ce 
                    LEFT JOIN proveedor rp ON rp.idProveedor = ce.idProveedor 
                    LEFT JOIN direccion rd ON rd.idDireccion = rp.idDireccion
                    LEFT JOIN localidades ol ON ol.idlocalidad = rd.idlocalidad
                    WHERE ce.idAlmacen = '$id'";
            }  else if ($tmp == 6){
                $query = "SELECT ce.idAlmacen, rp.nombre, rp.idSagarpa, ce.fecha, ol.localidad, ce.totalCompra 
                FROM cubetasencabezado_altiplano ce 
                    LEFT JOIN proveedor rp ON rp.idProveedor = ce.idProveedor 
                    LEFT JOIN direccion rd ON rd.idDireccion = rp.idDireccion
                    LEFT JOIN localidades ol ON ol.idlocalidad = rd.idlocalidad
                    WHERE ce.idAlmacen = '$id'";
            }  else if ($tmp == 7){
                $query = "SELECT ce.idAlmacen, rp.nombre, rp.idSagarpa, ce.fecha, ol.localidad, ce.totalCompra 
                FROM cubetasencabezado_naranjo ce 
                    LEFT JOIN proveedor rp ON rp.idProveedor = ce.idProveedor 
                    LEFT JOIN direccion rd ON rd.idDireccion = rp.idDireccion
                    LEFT JOIN localidades ol ON ol.idlocalidad = rd.idlocalidad
                    WHERE ce.idAlmacen = '$id'";
            } 
            else if ($tmp == 8){
                $query = "SELECT ce.idAlmacen, rp.nombre, rp.idSagarpa, ce.fecha, ol.localidad, ce.totalCompra 
                FROM cubetasencabezado_aguacate ce 
                    LEFT JOIN proveedor rp ON rp.idProveedor = ce.idProveedor 
                    LEFT JOIN direccion rd ON rd.idDireccion = rp.idDireccion
                    LEFT JOIN localidades ol ON ol.idlocalidad = rd.idlocalidad
                    WHERE ce.idAlmacen = '$id'";
            } 
            else if ($tmp == 9){
                $query = "SELECT ce.idAlmacen, rp.nombre, rp.idSagarpa, ce.fecha, ol.localidad, ce.totalCompra 
                FROM cubetasencabezado_mezquite ce 
                    LEFT JOIN proveedor rp ON rp.idProveedor = ce.idProveedor 
                    LEFT JOIN direccion rd ON rd.idDireccion = rp.idDireccion
                    LEFT JOIN localidades ol ON ol.idlocalidad = rd.idlocalidad
                    WHERE ce.idAlmacen = '$id'";
            } 
            else {
                $query = "SELECT ce.idAlmacen, rp.nombre, rp.idSagarpa, ce.fecha, ol.localidad, ce.totalCompra 
                FROM cubetasencabezado_organico ce 
                    LEFT JOIN proveedor rp ON rp.idProveedor = ce.idProveedor 
                    LEFT JOIN direccion rd ON rd.idDireccion = rp.idDireccion
                    LEFT JOIN localidades ol ON ol.idlocalidad = rd.idlocalidad
                    WHERE ce.idAlmacen = '$id'";
            }
        } else {
            if ($tmp == 1) {
                $query = "SELECT al.idAlmacen, pr.nombre, pr.idSagarpa, al.folio, al.fecha, lo.localidad, al.totalCompra  FROM almacenencabezado al 
                LEFT JOIN proveedor pr ON pr.idProveedor = al.idProveedor 
                LEFT JOIN direccion dr ON dr.idDireccion = pr.idDireccion
                LEFT JOIN localidades lo ON lo.idlocalidad = dr.idlocalidad
                WHERE idAlmacen = '$id'";
            } else if ($tmp == 5){
                $query = "SELECT al.idAlmacen, pr.nombre, pr.idSagarpa, al.folio, al.fecha, lo.localidad, al.totalCompra  FROM almacenencabezado_mantequilla al 
                LEFT JOIN proveedor pr ON pr.idProveedor = al.idProveedor 
                LEFT JOIN direccion dr ON dr.idDireccion = pr.idDireccion
                LEFT JOIN localidades lo ON lo.idlocalidad = dr.idlocalidad
                WHERE idAlmacen = '$id'";
            }  else if ($tmp == 6){
                $query = "SELECT al.idAlmacen, pr.nombre, pr.idSagarpa, al.folio, al.fecha, lo.localidad, al.totalCompra  FROM almacenencabezado_altiplano al 
                LEFT JOIN proveedor pr ON pr.idProveedor = al.idProveedor 
                LEFT JOIN direccion dr ON dr.idDireccion = pr.idDireccion
                LEFT JOIN localidades lo ON lo.idlocalidad = dr.idlocalidad
                WHERE idAlmacen = '$id'";
            } else if ($tmp == 7){
                $query = "SELECT al.idAlmacen, pr.nombre, pr.idSagarpa, al.folio, al.fecha, lo.localidad, al.totalCompra  FROM almacenencabezado_naranjo al 
                LEFT JOIN proveedor pr ON pr.idProveedor = al.idProveedor 
                LEFT JOIN direccion dr ON dr.idDireccion = pr.idDireccion
                LEFT JOIN localidades lo ON lo.idlocalidad = dr.idlocalidad
                WHERE idAlmacen = '$id'";
            }
            else if ($tmp == 8){
                $query = "SELECT al.idAlmacen, pr.nombre, pr.idSagarpa, al.folio, al.fecha, lo.localidad, al.totalCompra  FROM almacenencabezado_aguacate al 
                LEFT JOIN proveedor pr ON pr.idProveedor = al.idProveedor 
                LEFT JOIN direccion dr ON dr.idDireccion = pr.idDireccion
                LEFT JOIN localidades lo ON lo.idlocalidad = dr.idlocalidad
                WHERE idAlmacen = '$id'";
            }
            else if ($tmp == 9){
                $query = "SELECT al.idAlmacen, pr.nombre, pr.idSagarpa, al.folio, al.fecha, lo.localidad, al.totalCompra  FROM almacenencabezado_mezquite al 
                LEFT JOIN proveedor pr ON pr.idProveedor = al.idProveedor 
                LEFT JOIN direccion dr ON dr.idDireccion = pr.idDireccion
                LEFT JOIN localidades lo ON lo.idlocalidad = dr.idlocalidad
                WHERE idAlmacen = '$id'";
            }
            else {
                $query = "SELECT al.idAlmacen, pr.nombre, pr.idSagarpa, al.folio, al.fecha, lo.localidad, al.totalCompra  FROM almacenencabezado_organico al 
                LEFT JOIN proveedor pr ON pr.idProveedor = al.idProveedor 
                LEFT JOIN direccion dr ON dr.idDireccion = pr.idDireccion
                LEFT JOIN localidades lo ON lo.idlocalidad = dr.idlocalidad
                WHERE idAlmacen = '$id'";
            }
        }

        // $datos = mysql_query($query, $cn->Conectarse());
        // $cn->cerrarBd();
        // return $datos;
        $datos = $cn->prepare($query);
        // $datos->bindParam(':idRequisicion', $idRequisicion);
        $datos->execute();
        return $datos;
    }

    function tablaPrecios($id, $cubeta, $tmp)
    {
        // include_once '../DAOConeccion/coneccion.php';
        // $cn = new Coneccion();

        include_once '../DAOConeccion/conePDO.php';
        $pdo = new conePDO();
        $cn = $pdo->conectar();

        if ($cubeta == 99999) {
            if ($tmp == 1) {
                $query = "SELECT cd.idAlmacen, cd.idAlmacenEncabezado, cd.zona, cd.pesoLista, cd.bruto, 
                cd.tara, cd.neto, cd.diferencia, cd.humedad, cd.autorizado, cd.precio, cd.costoTotal
         FROM cubetasdetalle cd
         LEFT JOIN cubetasencabezado ce ON ce.idAlmacen = cd.idAlmacenEncabezado
         LEFT JOIN almacenencabezado alm ON alm.idAlmacen = ce.folioEntradaTambor
         WHERE cd.idAlmacenEncabezado = '$id'
         ORDER BY cd.idAlmacen ASC;";
            } else if ($tmp == 5) {
                $query = "SELECT cd.idAlmacen, cd.idAlmacenEncabezado, cd.zona, cd.pesoLista, cd.bruto, 
                cd.tara, cd.neto, cd.diferencia, cd.humedad, cd.autorizado, cd.precio, cd.costoTotal
         FROM cubetasdetalle_mantequilla cd
         LEFT JOIN cubetasencabezado_mantequilla ce ON ce.idAlmacen = cd.idAlmacenEncabezado
         LEFT JOIN almacenencabezado_mantequilla alm ON alm.idAlmacen = ce.folioEntradaTambor
         WHERE cd.idAlmacenEncabezado = '$id'";
            } else if ($tmp == 6) {
                $query = "SELECT cd.idAlmacen, cd.idAlmacenEncabezado, cd.zona, cd.pesoLista, cd.bruto, 
                cd.tara, cd.neto, cd.diferencia, cd.humedad, cd.autorizado, cd.precio, cd.costoTotal
         FROM cubetasdetalle_altiplano cd
         LEFT JOIN cubetasencabezado_altiplano ce ON ce.idAlmacen = cd.idAlmacenEncabezado
         LEFT JOIN almacenencabezado_altiplano alm ON alm.idAlmacen = ce.folioEntradaTambor
         WHERE cd.idAlmacenEncabezado = '$id'";
            } else if ($tmp == 7) {
                $query = "SELECT cd.idAlmacen, cd.idAlmacenEncabezado, cd.zona, cd.pesoLista, cd.bruto, 
                cd.tara, cd.neto, cd.diferencia, cd.humedad, cd.autorizado, cd.precio, cd.costoTotal
         FROM cubetasdetalle_naranjo cd
         LEFT JOIN cubetasencabezado_naranjo ce ON ce.idAlmacen = cd.idAlmacenEncabezado
         LEFT JOIN almacenencabezado_naranjo alm ON alm.idAlmacen = ce.folioEntradaTambor
         WHERE cd.idAlmacenEncabezado = '$id'";
            } else if ($tmp == 8) {
                $query = "SELECT cd.idAlmacen, cd.idAlmacenEncabezado, cd.zona, cd.pesoLista, cd.bruto, 
                cd.tara, cd.neto, cd.diferencia, cd.humedad, cd.autorizado, cd.precio, cd.costoTotal
         FROM cubetasdetalle_aguacate cd
         LEFT JOIN cubetasencabezado_aguacate ce ON ce.idAlmacen = cd.idAlmacenEncabezado
         LEFT JOIN almacenencabezado_aguacate alm ON alm.idAlmacen = ce.folioEntradaTambor
         WHERE cd.idAlmacenEncabezado = '$id'";
            }
            else if ($tmp == 9) {
                $query = "SELECT cd.idAlmacen, cd.idAlmacenEncabezado, cd.zona, cd.pesoLista, cd.bruto, 
                cd.tara, cd.neto, cd.diferencia, cd.humedad, cd.autorizado, cd.precio, cd.costoTotal
         FROM cubetasdetalle_mezquite cd
         LEFT JOIN cubetasencabezado_mezquite ce ON ce.idAlmacen = cd.idAlmacenEncabezado
         LEFT JOIN almacenencabezado_mezquite alm ON alm.idAlmacen = ce.folioEntradaTambor
         WHERE cd.idAlmacenEncabezado = '$id'";
            }
            
            else {
                $query = "SELECT cd.idAlmacen, cd.idAlmacenEncabezado, cd.zona, cd.pesoLista, cd.bruto, 
                cd.tara, cd.neto, cd.diferencia, cd.humedad, cd.autorizado, cd.precio, cd.costoTotal
         FROM cubetasdetalle_organico cd
         LEFT JOIN cubetasencabezado_organico ce ON ce.idAlmacen = cd.idAlmacenEncabezado
         LEFT JOIN almacenencabezado_organico alm ON alm.idAlmacen = ce.folioEntradaTambor
         WHERE cd.idAlmacenEncabezado = '$id'";
            }
        } else {
            if ($tmp == 1) {
                $query = "SELECT al.idAlmacen, al.idAlmacenEncabezado, al.zona, al.pesoLista, al.bruto,
                al.tara, al.neto, al.diferencia, al.autorizado, al.precio, al.costoTotal, MAX(l.porcentaje) AS humedad
                FROM almacen al
                LEFT JOIN laboratorio l ON l.idAlmacen = al.idAlmacen
                WHERE al.idAlmacenEncabezado = '$id'
                GROUP BY al.idAlmacen
                ORDER BY al.idAlmacen ASC;
                               UNION
                               SELECT cd.idAlmacen, cd.idAlmacenEncabezado, cd.zona, cd.pesoLista, cd.bruto,
                                cd.tara, cd.neto, cd.diferencia, cd.autorizado, cd.precio, cd.costoTotal, cd.humedad
                               FROM cubetasdetalle cd
                               LEFT JOIN cubetasencabezado ce ON ce.idAlmacen = cd.idAlmacenEncabezado
                               WHERE ce.folioEntradaTambor = '$id'";
            } else if ($tmp == 5) {
                $query = "SELECT al.idAlmacen, al.idAlmacenEncabezado, al.zona,
                al.pesoLista, al.bruto, al.tara, al.neto, al.diferencia, al.autorizado,
                al.precio, al.costoTotal, MAX(l.porcentaje) AS humedad
               FROM almacen_mantequilla al
               LEFT JOIN laboratorio_mantequilla l ON l.idAlmacen = al.idAlmacen
               WHERE al.idAlmacenEncabezado = '$id'
               GROUP BY al.idAlmacen
               ORDER BY al.idAlmacen ASC;
               UNION
               SELECT cd.idAlmacen, cd.idAlmacenEncabezado, cd.zona, cd.pesoLista, cd.bruto,
                cd.tara, cd.neto, cd.diferencia, cd.autorizado, cd.precio, cd.costoTotal, cd.humedad
               FROM cubetasdetalle_mantequilla cd
               LEFT JOIN cubetasencabezado_mantequilla ce ON ce.idAlmacen = cd.idAlmacenEncabezado
               WHERE ce.folioEntradaTambor = '$id'";
            } else if ($tmp == 6) {
                $query = "SELECT al.idAlmacen, al.idAlmacenEncabezado, al.zona,
                al.pesoLista, al.bruto, al.tara, al.neto, al.diferencia, al.autorizado,
                al.precio, al.costoTotal, MAX(l.porcentaje) AS humedad
               FROM almacen_altiplano al
               LEFT JOIN laboratorio_altiplano l ON l.idAlmacen = al.idAlmacen
               WHERE al.idAlmacenEncabezado = '$id'
               GROUP BY al.idAlmacen
               ORDER BY al.idAlmacen ASC;
               UNION
               SELECT cd.idAlmacen, cd.idAlmacenEncabezado, cd.zona, cd.pesoLista, cd.bruto,
                cd.tara, cd.neto, cd.diferencia, cd.autorizado, cd.precio, cd.costoTotal, cd.humedad
               FROM cubetasdetalle_altiplano cd
               LEFT JOIN cubetasencabezado_altiplano ce ON ce.idAlmacen = cd.idAlmacenEncabezado
               WHERE ce.folioEntradaTambor = '$id'";
            }  else if ($tmp == 7) {
                $query = "SELECT al.idAlmacen, al.idAlmacenEncabezado, al.zona,
                al.pesoLista, al.bruto, al.tara, al.neto, al.diferencia, al.autorizado,
                al.precio, al.costoTotal, MAX(l.porcentaje) AS humedad
               FROM almacen_naranjo al
               LEFT JOIN laboratorio_naranjo l ON l.idAlmacen = al.idAlmacen
               WHERE al.idAlmacenEncabezado = '$id'
               GROUP BY al.idAlmacen
               ORDER BY al.idAlmacen ASC;
               UNION
               SELECT cd.idAlmacen, cd.idAlmacenEncabezado, cd.zona, cd.pesoLista, cd.bruto,
                cd.tara, cd.neto, cd.diferencia, cd.autorizado, cd.precio, cd.costoTotal, cd.humedad
               FROM cubetasdetalle_naranjo cd
               LEFT JOIN cubetasencabezado_naranjo ce ON ce.idAlmacen = cd.idAlmacenEncabezado
               WHERE ce.folioEntradaTambor = '$id'";
            }  
            else if ($tmp == 8) {
                $query = "SELECT al.idAlmacen, al.idAlmacenEncabezado, al.zona,
                al.pesoLista, al.bruto, al.tara, al.neto, al.diferencia, al.autorizado,
                al.precio, al.costoTotal, MAX(l.porcentaje) AS humedad
               FROM almacen_aguacate al
               LEFT JOIN laboratorio_aguacate l ON l.idAlmacen = al.idAlmacen
               WHERE al.idAlmacenEncabezado = '$id'
               GROUP BY al.idAlmacen
               ORDER BY al.idAlmacen ASC;
               UNION
               SELECT cd.idAlmacen, cd.idAlmacenEncabezado, cd.zona, cd.pesoLista, cd.bruto,
                cd.tara, cd.neto, cd.diferencia, cd.autorizado, cd.precio, cd.costoTotal, cd.humedad
               FROM cubetasdetalle_aguacate cd
               LEFT JOIN cubetasencabezado_aguacate ce ON ce.idAlmacen = cd.idAlmacenEncabezado
               WHERE ce.folioEntradaTambor = '$id'";
            } 
            else if ($tmp == 9) {
                $query = "SELECT al.idAlmacen, al.idAlmacenEncabezado, al.zona,
                al.pesoLista, al.bruto, al.tara, al.neto, al.diferencia, al.autorizado,
                al.precio, al.costoTotal, MAX(l.porcentaje) AS humedad
               FROM almacen_mezquite al
               LEFT JOIN laboratorio_mezquite l ON l.idAlmacen = al.idAlmacen
               WHERE al.idAlmacenEncabezado = '$id'
               GROUP BY al.idAlmacen
               ORDER BY al.idAlmacen ASC;
               UNION
               SELECT cd.idAlmacen, cd.idAlmacenEncabezado, cd.zona, cd.pesoLista, cd.bruto,
                cd.tara, cd.neto, cd.diferencia, cd.autorizado, cd.precio, cd.costoTotal, cd.humedad
               FROM cubetasdetalle_mezquite cd
               LEFT JOIN cubetasencabezado_mezquite ce ON ce.idAlmacen = cd.idAlmacenEncabezado
               WHERE ce.folioEntradaTambor = '$id'";
            } 
            else {
                $query = "SELECT al.idAlmacen, al.idAlmacenEncabezado, al.zona,
                al.pesoLista, al.bruto, al.tara, al.neto, al.diferencia, al.autorizado,
                al.precio, al.costoTotal, MAX(l.porcentaje) AS humedad
               FROM almacen_organico al
               LEFT JOIN laboratorio_organico l ON l.idAlmacen = al.idAlmacen
               WHERE al.idAlmacenEncabezado = '$id'
               GROUP BY al.idAlmacen
               ORDER BY al.idAlmacen ASC;
               UNION
               SELECT cd.idAlmacen, cd.idAlmacenEncabezado, cd.zona, cd.pesoLista, cd.bruto,
                cd.tara, cd.neto, cd.diferencia, cd.autorizado, cd.precio, cd.costoTotal, cd.humedad
               FROM cubetasdetalle_organico cd
               LEFT JOIN cubetasencabezado_organico ce ON ce.idAlmacen = cd.idAlmacenEncabezado
               WHERE ce.folioEntradaTambor = '$id'";
            }
        }


        // $datos = mysql_query($query, $cn->Conectarse());
        // $cn->cerrarBd();
        // return $datos;

        $datos = $cn->prepare($query);
        // $datos->bindParam(':idRequisicion', $idRequisicion);
        $datos->execute();
        return $datos;
    }

    function EntradaTambores($fechas, $tipoCosecha)
    {
        //        include_once '../DAOConeccion/coneccion.php';
        //        $cn = new Coneccion();
        //        $cn->Conectarse();

        if ($tipoCosecha == 1) {
            $query2 = "SELECT am.fecha, pr.nombre, loca.localidad, pr.idSagarpa, zt.nombre as zona, al.idAlmacen as tambor, al.pesoLista, 
                al.bruto, al.tara, al.neto, al.diferencia FROM almacen al
                LEFT JOIN almacenencabezado am ON am.idAlmacen = al.idAlmacenEncabezado
                LEFT JOIN proveedor pr ON pr.idProveedor = am.idProveedor
                LEFT JOIN direccion dir ON dir.idDireccion = pr.idDireccion
                LEFT JOIN localidades loca ON loca.idlocalidad = dir.idlocalidad
                LEFT JOIN zonastambores zt ON zt.idZonaTambor = al.zona
                WHERE am.fecha BETWEEN '$fechas->fecha1' and '$fechas->fecha2'";
            return $query2;
        } else if ($tipoCosecha == 2) {
            $query2 = "SELECT am.fecha, pr.nombre, loca.localidad, pr.idSagarpa, zt.nombre as zona, al.idAlmacen as tambor, al.pesoLista, 
                al.bruto, al.tara, al.neto, al.diferencia FROM almacen_organico al
                LEFT JOIN almacenencabezado_organico am ON am.idAlmacen = al.idAlmacenEncabezado
                LEFT JOIN proveedor pr ON pr.idProveedor = am.idProveedor
                LEFT JOIN direccion dir ON dir.idDireccion = pr.idDireccion
                LEFT JOIN localidades loca ON loca.idlocalidad = dir.idlocalidad
                LEFT JOIN zonastambores zt ON zt.idZonaTambor = al.zona
                WHERE am.fecha BETWEEN '$fechas->fecha1' and '$fechas->fecha2'";
            return $query2;
        }else if ($tipoCosecha == 5) {
            $query2 = "SELECT am.fecha, pr.nombre, loca.localidad, pr.idSagarpa, zt.nombre as zona, al.idAlmacen as tambor, al.pesoLista, 
                al.bruto, al.tara, al.neto, al.diferencia FROM almacen_mantequilla al
                LEFT JOIN almacenencabezado_mantequilla am ON am.idAlmacen = al.idAlmacenEncabezado
                LEFT JOIN proveedor pr ON pr.idProveedor = am.idProveedor
                LEFT JOIN direccion dir ON dir.idDireccion = pr.idDireccion
                LEFT JOIN localidades loca ON loca.idlocalidad = dir.idlocalidad
                LEFT JOIN zonastambores zt ON zt.idZonaTambor = al.zona
                WHERE am.fecha BETWEEN '$fechas->fecha1' and '$fechas->fecha2'";
            return $query2;
        }else if ($tipoCosecha == 6) {
            $query2 = "SELECT am.fecha, pr.nombre, loca.localidad, pr.idSagarpa, zt.nombre as zona, al.idAlmacen as tambor, al.pesoLista, 
                al.bruto, al.tara, al.neto, al.diferencia FROM almacen_altiplano al
                LEFT JOIN almacenencabezado_altiplano am ON am.idAlmacen = al.idAlmacenEncabezado
                LEFT JOIN proveedor pr ON pr.idProveedor = am.idProveedor
                LEFT JOIN direccion dir ON dir.idDireccion = pr.idDireccion
                LEFT JOIN localidades loca ON loca.idlocalidad = dir.idlocalidad
                LEFT JOIN zonastambores zt ON zt.idZonaTambor = al.zona
                WHERE am.fecha BETWEEN '$fechas->fecha1' and '$fechas->fecha2'";
            return $query2;
        }else if ($tipoCosecha == 7) {
            $query2 = "SELECT am.fecha, pr.nombre, loca.localidad, pr.idSagarpa, zt.nombre as zona, al.idAlmacen as tambor, al.pesoLista, 
                al.bruto, al.tara, al.neto, al.diferencia FROM almacen_naranjo al
                LEFT JOIN almacenencabezado_naranjo am ON am.idAlmacen = al.idAlmacenEncabezado
                LEFT JOIN proveedor pr ON pr.idProveedor = am.idProveedor
                LEFT JOIN direccion dir ON dir.idDireccion = pr.idDireccion
                LEFT JOIN localidades loca ON loca.idlocalidad = dir.idlocalidad
                LEFT JOIN zonastambores zt ON zt.idZonaTambor = al.zona
                WHERE am.fecha BETWEEN '$fechas->fecha1' and '$fechas->fecha2'";
            return $query2;
        }else if ($tipoCosecha == 8) {
            $query2 = "SELECT am.fecha, pr.nombre, loca.localidad, pr.idSagarpa, zt.nombre as zona, al.idAlmacen as tambor, al.pesoLista, 
                al.bruto, al.tara, al.neto, al.diferencia FROM almacen_aguacate al
                LEFT JOIN almacenencabezado_aguacate am ON am.idAlmacen = al.idAlmacenEncabezado
                LEFT JOIN proveedor pr ON pr.idProveedor = am.idProveedor
                LEFT JOIN direccion dir ON dir.idDireccion = pr.idDireccion
                LEFT JOIN localidades loca ON loca.idlocalidad = dir.idlocalidad
                LEFT JOIN zonastambores zt ON zt.idZonaTambor = al.zona
                WHERE am.fecha BETWEEN '$fechas->fecha1' and '$fechas->fecha2'";
            return $query2;
        }else if ($tipoCosecha == 9) {
            $query2 = "SELECT am.fecha, pr.nombre, loca.localidad, pr.idSagarpa, zt.nombre as zona, al.idAlmacen as tambor, al.pesoLista, 
                al.bruto, al.tara, al.neto, al.diferencia FROM almacen_mezquite al
                LEFT JOIN almacenencabezado_mezquite am ON am.idAlmacen = al.idAlmacenEncabezado
                LEFT JOIN proveedor pr ON pr.idProveedor = am.idProveedor
                LEFT JOIN direccion dir ON dir.idDireccion = pr.idDireccion
                LEFT JOIN localidades loca ON loca.idlocalidad = dir.idlocalidad
                LEFT JOIN zonastambores zt ON zt.idZonaTambor = al.zona
                WHERE am.fecha BETWEEN '$fechas->fecha1' and '$fechas->fecha2'";
            return $query2;
        }



        //        $datos = mysql_query($query2);
        //        $cn->cerrarBd();
    }

    function Proveedor()
    {

        $prove = "SELECT pr.idProveedor, pr.nombre, pr.idSagarpa, z.zona, l.localidad, es.estado, c.nombre as comprador  FROM proveedor pr
                    LEFT JOIN direccion d on d.idDireccion = pr.idDireccion
                    LEFT JOIN localidades l ON l.idlocalidad = d.idlocalidad
                    LEFT JOIN estados es ON es.idEstado =  d.idEstado 
                    INNER JOIN zonas z ON z.idzona = l.idzona
                    INNER JOIN compradores c ON c.idcomprador = z.idcomprador
                    ORDER BY pr.idProveedor ASC";

        return $prove;
    }

    function laboratorio($tipoDeMiel)
    {
        include_once '../DAOConeccion/conePDO.php';
        $pdo = new conePDO();
        $con = $pdo->conectar();
        switch ($tipoDeMiel) {
            case '1':
                $almacen_tabla = 'almacen';
                $laboratorio_tabla = 'laboratorio';
                $almacenencabezado_tabla = 'almacenencabezado';
                $tamboresexperimentales_tabla = 'tamboresexperimentales';
                $tamboreslotes_tabla = 'tamboreslotes';
                break;
            case '2':
                $almacen_tabla = 'almacen_organico';
                $laboratorio_tabla = 'laboratorio_organico';
                $almacenencabezado_tabla = 'almacenencabezado_organico';
                $tamboresexperimentales_tabla = 'tamboresexperimentales_organico';
                $tamboreslotes_tabla = 'tamboreslotes_organico';
                break;
            default:
                return;
                break;
        }
        $sqlLab = "SELECT al.idAlmacen, pr.nombre, lab.porcentaje AS porcentajeDescripcion, lab.sfDescripcion,
                lab.stDescripcion, lab.adulteracionDescripcion, lab.hmf AS procesoDescripcion, lab.color, lab.colorDescripcion,
                rs.resultado, lab.marcaInterna, ale.fecha,al.bruto, al.tara,
                al.neto, rs.resultado, l.localidad, pr.idSagarpa, f.floracion,
                te.idLoteExperimental AS exp, tl.idLoteInterno AS lote, 'fg' AS fg
                FROM $almacen_tabla al
                LEFT JOIN $laboratorio_tabla lab ON lab.idAlmacen = al.idAlmacen
                INNER JOIN $almacenencabezado_tabla ale ON  ale.idAlmacen = al.idalmacenEncabezado
                LEFT JOIN resultadofinal rs ON rs.idresultadoFinal = lab.resultadoFinal
                INNER JOIN proveedor pr ON pr.idProveedor = ale.idProveedor
                LEFT JOIN direccion dir ON dir.idDireccion = pr.idDireccion
                LEFT JOIN localidades l ON l.idlocalidad = dir.idlocalidad
                LEFT JOIN floraciones f ON f.idFloracion = lab.idFloracion
                LEFT JOIN $tamboresexperimentales_tabla te ON te.folioTambor = al.idAlmacen AND te.clasificacion = 0 AND te.tipo = '0'
                LEFT JOIN $tamboreslotes_tabla tl ON tl.folioTambor = al.idAlmacen AND tl.clasificacion = 0 AND tl.tipo = '0' 
                ORDER BY al.idAlmacen";
        $lab = $con->prepare($sqlLab);
        $lab->execute();
        $datos = $lab->fetchAll(PDO::FETCH_ASSOC);
        return $datos;
    }

    //CONSULTA REPORTE LAB (ACTUALIZACIÓN 2021)
    function laboratorio212($tipoDeMiel)
    {
        include_once '../DAOConeccion/conePDO.php';
        $pdo = new conePDO();
        $con = $pdo->conectar();
        switch ($tipoDeMiel) {
            case '1':
                $almacen_tabla = 'almacen';
                $laboratorio_tabla = 'laboratorio';
                $almacenencabezado_tabla = 'almacenencabezado';
                $tamboresexperimentales_tabla = 'tamboresexperimentales';
                $tamboreslotes_tabla = 'tamboreslotes';
                break;
            case '2':
                $almacen_tabla = 'almacen_organico';
                $laboratorio_tabla = 'laboratorio_organico';
                $almacenencabezado_tabla = 'almacenencabezado_organico';
                $tamboresexperimentales_tabla = 'tamboresexperimentales_organico';
                $tamboreslotes_tabla = 'tamboreslotes_organico';
                break;
            default:
                return;
                break;
        }

        $sql = "SELECT al.idAlmacen, pr.nombre, ale.fecha,al.bruto, al.tara, al.neto, l.localidad, pr.idSagarpa
        FROM $almacen_tabla al
        INNER JOIN $almacenencabezado_tabla ale ON  ale.idAlmacen = al.idalmacenEncabezado
        INNER JOIN proveedor pr ON pr.idProveedor = ale.idProveedor
        LEFT JOIN direccion dir ON dir.idDireccion = pr.idDireccion
        LEFT JOIN localidades l ON l.idlocalidad = dir.idlocalidad
        ORDER BY al.idAlmacen";
        $sqlDetalle = $con->prepare($sql);
        $sqlDetalle->execute();
        $resultado = $sqlDetalle->fetchAll(PDO::FETCH_ASSOC);

        foreach ($resultado as $index => $e) {
            $sql2 = "SELECT lab.porcentaje AS porcentajeDescripcion, lab.sfDescripcion,
            lab.stDescripcion, lab.adulteracionDescripcion, lab.hmf AS procesoDescripcion, lab.color, 
            rs.resultado, lab.marcaInterna, f.floracion
            FROM $laboratorio_tabla lab 
            LEFT JOIN resultadofinal rs ON rs.idresultadoFinal = lab.resultadoFinal
            LEFT JOIN floraciones f ON f.idFloracion = lab.idFloracion
            WHERE lab.idAlmacen = :folio
            ORDER BY lab.idAlmacen ASC";
            $sqlDetalle2 = $con->prepare($sql2);
            $sqlDetalle2->bindParam(':folio', $e['idAlmacen']);
            $sqlDetalle2->execute();
            if ($sqlDetalle2->rowCount() >= 1) {
                $e2 = $sqlDetalle2->fetch(PDO::FETCH_ASSOC);
            } else {
                $e2['porcentajeDescripcion'] = '';
                $e2['sfDescripcion'] = '';
                $e2['stDescripcion'] = '';
                $e2['adulteracionDescripcion'] = '';
                $e2['color'] = '';
                $e2['colorDescripcion'] = '';
                $e2['resultado'] = '';
                $e2['marcaInterna'] = '';
                $e2['floracion'] = '';
            }
            // $e2 = $sqlDetalle2->fetch(PDO::FETCH_ASSOC);
            // $sql3 = "SELECT te.idLoteExperimental AS exp
            // FROM $tamboresexperimentales_tabla te 
            // WHERE te.folioTambor = :folio AND te.clasificacion = 0 AND te.tipo = '0'
            // ORDER BY te.folioTambor";
            // $sqlDetalle3 = $con->prepare($sql3);
            // $sqlDetalle3->bindParam(':folio', $e['idAlmacen']);
            // $sqlDetalle3->execute();
            // if ($sqlDetalle3->rowCount() >= 1) {
            //     $e3 = $sqlDetalle3->fetch(PDO::FETCH_ASSOC);
            // } else {
            //     $e3['exp'] = '';
            // }
            // $sql4 = "SELECT tl.idLoteInterno AS lote
            // FROM $tamboreslotes_tabla tl WHERE tl.folioTambor = :folio AND tl.clasificacion = 0 AND tl.tipo = '0' 
            // ORDER BY tl.folioTambor";
            // $sqlDetalle4 = $con->prepare($sql4);
            // $sqlDetalle4->bindParam(':folio', $e['idAlmacen']);
            // $sqlDetalle4->execute();
            // if ($sqlDetalle4->rowCount() >= 1) {
            //     $e4 = $sqlDetalle4->fetch(PDO::FETCH_ASSOC);
            // } else {
            //     $e4['lote'] = '';
            // }

            // $new = array_merge($e, $e2, $e3, $e4);
            $new = array_merge($e, $e2);
            $resultado[$index] = $new;
        }
        return $resultado;
    }


    //CONSULTA REPORTE LABORATORIO (OPTIMIZACIÓN 2022)
    function laboratorioConsulta2022($tipoDeMiel)
    {
        include_once '../DAOConeccion/conePDO.php';
        $pdo = new conePDO();
        $con = $pdo->conectar();
        switch ($tipoDeMiel) {
            case '1':
                $almacen = 'almacen';
                $laboratorio = 'laboratorio';
                $almacenencabezado = 'almacenencabezado';
                break;
            case '2':
                $almacen = 'almacen_organico';
                $laboratorio = 'laboratorio_organico';
                $almacenencabezado = 'almacenencabezado_organico';
                break;
            case '5':
                $almacen = 'almacen_mantequilla';
                $laboratorio = 'laboratorio_mantequilla';
                $almacenencabezado = 'almacenencabezado_mantequilla';
                break;
            case '6':
                $almacen = 'almacen_altiplano';
                $laboratorio = 'laboratorio_altiplano';
                $almacenencabezado = 'almacenencabezado_altiplano';
                break;
            case '7':
                $almacen = 'almacen_naranjo';
                $laboratorio = 'laboratorio_naranjo';
                $almacenencabezado = 'almacenencabezado_naranjo';
                break;
            case '8':
                $almacen = 'almacen_aguacate';
                $laboratorio = 'laboratorio_aguacate';
                $almacenencabezado = 'almacenencabezado_aguacate';
                break;
            case '9':
                $almacen = 'almacen_mezquite';
                $laboratorio = 'laboratorio_mezquite';
                $almacenencabezado = 'almacenencabezado_mezquite';
                break;
            default:
                return;
                break;
        }

        $sql = "SELECT al.idAlmacen, pr.nombre, ale.fecha, ale.clasificacionMiel ,al.bruto, al.tara, al.neto, l.localidad, pr.idSagarpa,
        lab.porcentaje AS porcentajeDescripcion, lab.sfDescripcion,
        lab.stDescripcion, lab.c13 AS adulteracionDescripcion, lab.hmf AS procesoDescripcion, lab.color, lab.marcaInterna,
        rs.resultado,f.floracion
                FROM $almacen al
                INNER JOIN $almacenencabezado ale ON  ale.idAlmacen = al.idalmacenEncabezado
                INNER JOIN proveedor pr ON pr.idProveedor = ale.idProveedor
                LEFT JOIN direccion dir ON dir.idDireccion = pr.idDireccion
                LEFT JOIN localidades l ON l.idlocalidad = dir.idlocalidad
                        LEFT JOIN $laboratorio lab ON lab.idAlmacen = al.idAlmacen
                        LEFT JOIN resultadofinal rs ON rs.idresultadoFinal = lab.resultadoFinal
                        LEFT JOIN floraciones f ON f.idFloracion = lab.idFloracion
                ORDER BY al.idAlmacen
        ";
        $sqlDetalle = $con->prepare($sql);
        $sqlDetalle->execute();
        $resultado = $sqlDetalle->fetchAll(PDO::FETCH_ASSOC);
        return $resultado;
    }




    //Consulta de tabla de Requerimiento de deposito de Compra
    function RTablaDeposito($idRequisicion)
    {
        include_once '../DAOConeccion/conePDO.php';
        $pdo = new conePDO();
        $con = $pdo->conectar();
        $RDC = "SELECT rd.idDetalle, p.nombre,  l.localidad, 
        rd.noTambores,rd.peso, rd.precio, rd.importe, rd.banco, rd.observaciones, 
        tm.tipoDeMiel
        FROM requisiciondetalle rd
        LEFT JOIN requisicionencabezado re ON rd.idRequisicion = re.idRequisicion
        LEFT JOIN proveedor p ON p.idProveedor = rd.idProveedor
        LEFT JOIN direccion d ON d.idDireccion = p.idDireccion
        LEFT JOIN localidades l ON l.idlocalidad = d.idlocalidad
        LEFT JOIN tiposdemiel tm ON tm.idTipoDeMiel = rd.idTipoDeMiel        
        WHERE re.idRequisicion  = :idRequisicion ORDER BY rd.idDetalle";

        $datos = $con->prepare($RDC);
        $datos->bindParam(':idRequisicion', $idRequisicion);
        $datos->execute();
        return $datos;
    }

    //Consulta de encabezado  PDF de Requerimiento de Deposito de Compra
    function Rencabezado($idRequisicion)
    {
        include_once '../DAOConeccion/conePDO.php';
        $pdo = new conePDO();
        $con = $pdo->conectar();
        $RDC1 = "SELECT idRequisicion, fechaRequisicion, fechaImpresion, importeTotal, totalTambores, totalKilos  
               FROM requisicionencabezado WHERE idRequisicion = :idRequisicion";

        $datos = $con->prepare($RDC1);
        $datos->bindParam(':idRequisicion', $idRequisicion);
        $datos->execute();
        return $datos;
    }

    function reportesdDescargayCarga($tipoDeMiel, $idReporte, $tipoReporte)
    {

        switch ($tipoDeMiel) {
            case '0':
                $entradaysalida_tabla = 'entradaysalida';
                $personaldescarga_tabla = 'personaldescarga';
                $cubetasencabezado = 'cubetasencabezado';
                $cubetasDetalle = 'cubetasdetalle';
                $mp_encabezado_entradas = 'materiaprimaencabezadoentradas';
                $mp_detalle_entradas = 'materiaprimadetalleentradas';
                $mp_encabezado_salidas = 'materiaprimaencabezadosalidas';
                $mp_detalle_salidas = 'materiaprimadetallesalidas';
                $traspaso_encabezado = 'almacenencabezadotraspaso';
                $traspaso_detalle = 'almacentraspaso';
                $tipo = '1'; //convencional
                break;
            case '1':
                $entradaysalida_tabla = 'entradaysalida';
                $personaldescarga_tabla = 'personaldescarga';
                $cubetasencabezado = 'cubetasencabezado';
                $cubetasDetalle = 'cubetasdetalle';
                $mp_encabezado_entradas = 'materiaprimaencabezadoentradas';
                $mp_detalle_entradas = 'materiaprimadetalleentradas';
                $mp_encabezado_salidas = 'materiaprimaencabezadosalidas';
                $mp_detalle_salidas = 'materiaprimadetallesalidas';
                $traspaso_encabezado = 'almacenencabezadotraspaso';
                $traspaso_detalle = 'almacentraspaso';
                $tipo = '1'; //convencional
                break;
            case '2':
                $entradaysalida_tabla = 'entradaysalida_organico';
                $personaldescarga_tabla = 'personaldescarga_organico';
                $cubetasencabezado = 'cubetasencabezado_organico';
                $cubetasDetalle = 'cubetasdetalle_organico';
                $mp_encabezado_entradas = 'materiaprimaencabezadoentradas_organico';
                $mp_detalle_entradas = 'materiaprimadetalleentradas_organico';
                $mp_encabezado_salidas = 'materiaprimaencabezadosalidas_organico';
                $mp_detalle_salidas = 'materiaprimadetallesalidas_organico';
                $traspaso_encabezado = 'almacenencabezadotraspaso_organico';
                $traspaso_detalle = 'almacentraspaso_organico';
                $tipo = '2'; //orgánico
                break;
            case '5':
                $entradaysalida_tabla = 'entradaysalida_mantequilla';
                $personaldescarga_tabla = 'personaldescarga_mantequilla';
                $cubetasencabezado = 'cubetasencabezado_mantequilla';
                $cubetasDetalle = 'cubetasdetalle_mantequilla';
                $mp_encabezado_entradas = 'materiaprimaencabezadoentradas_mantequilla';
                $mp_detalle_entradas = 'materiaprimadetalleentradas_mantequilla';
                $mp_encabezado_salidas = 'materiaprimaencabezadosalidas_mantequilla';
                $mp_detalle_salidas = 'materiaprimadetallesalidas_mantequilla';
                $traspaso_encabezado = 'almacenencabezadotraspaso_mantequilla';
                $traspaso_detalle = 'almacentraspaso_mantequilla';
                $tipo = '5'; //Mantequilla
                break;
            case '6':
                $entradaysalida_tabla = 'entradaysalida_altiplano';
                $personaldescarga_tabla = 'personaldescarga_altiplano';
                $cubetasencabezado = 'cubetasencabezado_altiplano';
                $cubetasDetalle = 'cubetasdetalle_altiplano';
                $mp_encabezado_entradas = 'materiaprimaencabezadoentradas_altiplano';
                $mp_detalle_entradas = 'materiaprimadetalleentradas_altiplano';
                $mp_encabezado_salidas = 'materiaprimaencabezadosalidas_altiplano';
                $mp_detalle_salidas = 'materiaprimadetallesalidas_altiplano';
                $traspaso_encabezado = 'almacenencabezadotraspaso_altiplano';
                $traspaso_detalle = 'almacentraspaso_altiplano';
                $tipo = '6'; //altiplano
                break;
            case '7':
                $entradaysalida_tabla = 'entradaysalida_naranjo';
                $personaldescarga_tabla = 'personaldescarga_naranjo';
                $cubetasencabezado = 'cubetasencabezado_naranjo';
                $cubetasDetalle = 'cubetasdetalle_naranjo';
                $mp_encabezado_entradas = 'materiaprimaencabezadoentradas_naranjo';
                $mp_detalle_entradas = 'materiaprimadetalleentradas_naranjo';
                $mp_encabezado_salidas = 'materiaprimaencabezadosalidas_naranjo';
                $mp_detalle_salidas = 'materiaprimadetallesalidas_naranjo';
                $traspaso_encabezado = 'almacenencabezadotraspaso_naranjo';
                $traspaso_detalle = 'almacentraspaso_naranjo';
                $tipo = '7'; //naranjo
                break;
            case '8':
                $entradaysalida_tabla = 'entradaysalida_aguacate';
                $personaldescarga_tabla = 'personaldescarga_aguacate';
                $cubetasencabezado = 'cubetasencabezado_aguacate';
                $cubetasDetalle = 'cubetasdetalle_aguacate';
                $mp_encabezado_entradas = 'materiaprimaencabezadoentradas_aguacate';
                $mp_detalle_entradas = 'materiaprimadetalleentradas_aguacate';
                $mp_encabezado_salidas = 'materiaprimaencabezadosalidas_aguacate';
                $mp_detalle_salidas = 'materiaprimadetallesalidas_aguacate';
                $traspaso_encabezado = 'almacenencabezadotraspaso_aguacate';
                $traspaso_detalle = 'almacentraspaso_aguacate';
                $tipo = '8'; //aguacate
                break;
            case '9':
                $entradaysalida_tabla = 'entradaysalida_mezquite';
                $personaldescarga_tabla = 'personaldescarga_mezquite';
                $cubetasencabezado = 'cubetasencabezado_mezquite';
                $cubetasDetalle = 'cubetasdetalle_mezquite';
                $mp_encabezado_entradas = 'materiaprimaencabezadoentradas_mezquite';
                $mp_detalle_entradas = 'materiaprimadetalleentradas_mezquite';
                $mp_encabezado_salidas = 'materiaprimaencabezadosalidas_mezquite';
                $mp_detalle_salidas = 'materiaprimadetallesalidas_mezquite';
                $traspaso_encabezado = 'almacenencabezadotraspaso_mezquite';
                $traspaso_detalle = 'almacentraspaso_mezquite';
                $tipo = '9'; //mezquite
                break;
            default:
                throw new Exception('Tipo de miel no es válido');
                break;
        }

        if ($tipoReporte == 1) { //entradas
            $sqlReporteCarDes = "SELECT rd.idReporte, rd.fechaImpresion, rd.responsable, rd.idOperador, rd.idPlaca, rd.lote, rd.observaciones, rd.estado,
            rd.idClasificacion, rd.miel, rd.cera, rd.clasificacionMiel, rd.apicolas, rd.tambor, rd.cubeta, rd.mp, rd.traspaso, rd.envasesFrascos, rd.productosDerivados, rd.idLoteInterno,
            rd.contenedor, rd.sello, cm.clasificacion,
            cu.limpieza, cu.materialExtrano, cu.vehiculoAdecuado,
                CASE WHEN cu.cabello = '1' THEN 'Sí' ELSE 'No' END AS cabello, 
                CASE WHEN cu.unas = '1' THEN 'Sí' ELSE 'No' END AS unas, 
                CASE WHEN cu.ropa = '1' THEN 'Sí' ELSE 'No' END AS ropa,
                d.producto, d.cantidad, d.pesoBruto, d.pesoTara, d.pesoNeto, p.limpiezaPersonal, p.marcacion,
                t.operador, t.compania, t.licencia, t.vigencia,
                ex.roto, ex.abolladuras, ex.recipienteAdecuado, ex.lavadoExterior, pch.placa, pch.tipo,
                pch.marca, pch.modelo, 
                CASE WHEN tr.remolque IS NULL THEN 'No aplica' ELSE tr.remolque END AS remolque, 
                CASE WHEN pch.marcaRemolque IS NULL OR LENGTH(pch.marcaRemolque) = 0 THEN '---' ELSE pch.marcaRemolque END AS marcaRemolque, 
                CASE WHEN pch.modeloRemolque IS NULL OR LENGTH(pch.modeloRemolque) = 0  THEN '---' ELSE pch.modeloRemolque END AS modeloRemolque, 
                CASE WHEN pch.placaRemolque IS NULL OR LENGTH(pch.placaRemolque) = 0  THEN '---' ELSE pch.placaRemolque END AS placaRemolque, 
                    (SELECT ce.idAlmacen FROM $cubetasencabezado ce 
                    INNER JOIN $cubetasDetalle cd ON cd.idAlmacenEncabezado = ce.idAlmacen
                    WHERE ce.idReporteDescarga = $idReporte GROUP BY ce.idAlmacen) AS entradaCubeta, 
                    (SELECT ce.idAlmacen FROM almacenencabezadocera ce 
                    INNER JOIN almacencera cd ON cd.idAlmacenEncabezado = ce.idAlmacen
                    WHERE ce.idReporteDescarga = $idReporte AND ce.tipoCera = $tipo AND ce.tipo = 1 GROUP BY ce.idAlmacen) AS entradaCera, 
                    (SELECT ea.idAlmacen FROM almacenencabezadoapicola ea
                    INNER JOIN almacenapicola ad ON ad.idAlmacenEncabezado = ea.idAlmacen
                    WHERE ea.idReporteDescarga = $idReporte AND ea.tipo = 1 GROUP BY ea.idAlmacen) AS entradaApicola, 
                    (SELECT mpe.idEntradaMateria FROM $mp_encabezado_entradas mpe
                    INNER JOIN $mp_detalle_entradas mpd ON mpd.idEntradaMateria = mpe.idEntradaMateria 
                    WHERE mpe.idReporteDescarga = $idReporte GROUP BY mpe.idEntradaMateria) AS entradaMP,
                    (SELECT ate.idAlmacen FROM $traspaso_encabezado ate
                     INNER JOIN $traspaso_detalle atd ON atd.idAlmacenEncabezado = ate.idAlmacen 
                     WHERE ate.idReporteDescarga = $idReporte GROUP BY ate.idAlmacen) AS entradaTraspaso,
                    (SELECT enfre.idEntradaEnvases FROM envasesfrascosencabezadoentradas enfre
                    INNER JOIN envasesfrascosdetalleentradas enfrd ON enfrd.idEntradaEnvases = enfre.idEntradaEnvases 
                    WHERE enfre.idReporteDescarga = $idReporte GROUP BY enfre.idEntradaEnvases) AS entradaEnvasesFrascos,
                    (SELECT ea.idEntrada FROM derivadosalmacenencabezado ea
                    INNER JOIN derivadosalmacendetalle ad ON ad.idEntrada = ea.idEntrada
                    WHERE ea.idReporteDescarga = $idReporte AND tipo = 1 GROUP BY ea.idEntrada) AS entradaProductosDerivados
                    FROM $entradaysalida_tabla rd
                    LEFT JOIN condicionesunidad cu ON cu.idCondicion = rd.idCondicion
                    LEFT JOIN descripciones d ON d.idDescripcion = rd.idDescripcion
                    LEFT JOIN personalacciones p ON p.idPersonal = rd.idPersonal
                    LEFT JOIN choferes t ON t.idOperador = rd.idOperador
                    LEFT JOIN externotambores ex ON ex.idExternoTambor = rd.idExternoTambor
                    LEFT JOIN placaschoferes pch ON pch.idPlaca = rd.idPlaca
                    LEFT JOIN clasificacionesmiel cm ON cm.idClasificacionMiel = rd.idClasificacion
                    LEFT JOIN tiposremolque tr ON tr.idRemolque = pch.remolque
                    WHERE rd.idReporte = $idReporte";
        } else if ($tipoReporte == 2) {
            $sqlReporteCarDes = "SELECT rd.idReporte, rd.fechaImpresion, rd.responsable, rd.idOperador, rd.idPlaca, rd.lote, rd.observaciones, rd.estado,
            rd.idClasificacion, rd.miel, rd.clasificacionMiel, rd.cera, rd.apicolas, rd.tambor, rd.cubeta, rd.mp, rd.traspaso, rd.envasesFrascos, rd.productosDerivados, rd.idLoteInterno,
            rd.contenedor, rd.sello, cm.clasificacion,
            cu.limpieza, cu.materialExtrano, cu.vehiculoAdecuado,
                CASE WHEN cu.cabello = '1' THEN 'Sí' ELSE 'No' END AS cabello, 
                CASE WHEN cu.unas = '1' THEN 'Sí' ELSE 'No' END AS unas, 
                CASE WHEN cu.ropa = '1' THEN 'Sí' ELSE 'No' END AS ropa,
                d.producto, d.cantidad, d.pesoBruto, d.pesoTara, d.pesoNeto, p.limpiezaPersonal, p.marcacion,
                t.operador, t.compania, t.licencia, t.vigencia,
                ex.roto, ex.abolladuras, ex.recipienteAdecuado, ex.lavadoExterior, pch.placa, pch.tipo,
                pch.marca, pch.modelo, 
                CASE WHEN tr.remolque IS NULL THEN 'No aplica' ELSE tr.remolque END AS remolque, 
                CASE WHEN pch.marcaRemolque IS NULL OR LENGTH(pch.marcaRemolque) = 0 THEN '---' ELSE pch.marcaRemolque END AS marcaRemolque, 
                CASE WHEN pch.modeloRemolque IS NULL OR LENGTH(pch.modeloRemolque) = 0  THEN '---' ELSE pch.modeloRemolque END AS modeloRemolque, 
                CASE WHEN pch.placaRemolque IS NULL OR LENGTH(pch.placaRemolque) = 0  THEN '---' ELSE pch.placaRemolque END AS placaRemolque, 
                    (SELECT ce.idAlmacen FROM $cubetasencabezado ce 
                    INNER JOIN $cubetasDetalle cd ON cd.idAlmacenEncabezado = ce.idAlmacen
                    WHERE ce.idReporteDescarga = $idReporte GROUP BY ce.idAlmacen) AS entradaCubeta, 
                    (SELECT ce.idAlmacen FROM almacenencabezadocera ce 
                    INNER JOIN almacencera cd ON cd.idAlmacenEncabezado = ce.idAlmacen
                    WHERE ce.idReporteDescarga = $idReporte AND ce.tipoCera = $tipo AND ce.tipo = 2 GROUP BY ce.idAlmacen) AS entradaCera, 
                    (SELECT ea.idAlmacen FROM almacenencabezadoapicola ea
                    INNER JOIN almacenapicola ad ON ad.idAlmacenEncabezado = ea.idAlmacen
                    WHERE ea.idReporteDescarga = $idReporte AND tipo = 2 GROUP BY ea.idAlmacen) AS entradaApicola, 
                    (SELECT mpe.idSalidaMateria FROM $mp_encabezado_salidas mpe
                    INNER JOIN $mp_detalle_salidas mpd ON mpd.idSalidaMateria = mpe.idSalidaMateria 
                    WHERE mpe.idReporteCarga = $idReporte GROUP BY mpe.idSalidaMateria) AS entradaMP,
                    (SELECT ate.idAlmacen FROM $traspaso_encabezado ate
                     INNER JOIN $traspaso_detalle atd ON atd.idAlmacenEncabezado = ate.idAlmacen 
                     WHERE ate.idReporteDescarga = $idReporte GROUP BY ate.idAlmacen) AS entradaTraspaso,
                    (SELECT enfre.idSalidaEnvases FROM envasesfrascosencabezadosalidas enfre
                    INNER JOIN envasesfrascosdetallesalidas enfrd ON enfrd.idSalidaEnvases = enfre.idSalidaEnvases 
                    WHERE enfre.idReporteCarga = $idReporte GROUP BY enfre.idSalidaEnvases) AS entradaEnvasesFrascos,
                    (SELECT ea.idSalida FROM derivadosalmacenencabezado_salidas ea
                    INNER JOIN derivadosalmacendetalle_salidas ad ON ad.idSalida = ea.idSalida
                    WHERE ea.idReporteCarga = $idReporte AND tipo = 2 GROUP BY ea.idSalida) AS entradaProductosDerivados
                    FROM $entradaysalida_tabla rd
                    LEFT JOIN condicionesunidad cu ON cu.idCondicion = rd.idCondicion
                    LEFT JOIN descripciones d ON d.idDescripcion = rd.idDescripcion
                    LEFT JOIN personalacciones p ON p.idPersonal = rd.idPersonal
                    LEFT JOIN choferes t ON t.idOperador = rd.idOperador
                    LEFT JOIN externotambores ex ON ex.idExternoTambor = rd.idExternoTambor
                    LEFT JOIN placaschoferes pch ON pch.idPlaca = rd.idPlaca
                    LEFT JOIN clasificacionesmiel cm ON cm.idClasificacionMiel = rd.idClasificacion
                    LEFT JOIN tiposremolque tr ON tr.idRemolque = pch.remolque
                    WHERE rd.idReporte = $idReporte";
        }

        return $sqlReporteCarDes;
    }

    function nombreEncargadoReporteDesCar($tipoDeMiel, $idReporte)
    {
        switch ($tipoDeMiel) {
            case '0':
                $entradaysalida_tabla = 'entradaysalida';
                break;
            case '1':
                $entradaysalida_tabla = 'entradaysalida';
                break;
            case '2':
                $entradaysalida_tabla = 'entradaysalida_organico';
                break;
            case '5':
                $entradaysalida_tabla = 'entradaysalida_mantequilla';
                break;
            case '6':
                $entradaysalida_tabla = 'entradaysalida_altiplano';
                break;
            case '7':
                $entradaysalida_tabla = 'entradaysalida_naranjo';
                break;
                
            case '8':
                $entradaysalida_tabla = 'entradaysalida_aguacate';
                break;
            case '9':
                $entradaysalida_tabla = 'entradaysalida_mezquite';
                break;
            default:
                throw new Exception('Tipo de miel no es válido');
                break;
        }
        $sqlNombreResponsable = "SELECT p.nombre FROM $entradaysalida_tabla rd
                                LEFT JOIN personaloaxaca p ON p.idPersonalOM = rd.responsable
                                WHERE rd.idReporte = $idReporte";
        return $sqlNombreResponsable;
    }

    function nombreEncargadoReporteDesCarOrganico($tipoDeMiel, $idReporte)
    {
        switch ($tipoDeMiel) {
            case '0':
                $entradaysalida_tabla = 'entradaysalida';
                break;
            case '1':
                $entradaysalida_tabla = 'entradaysalida';
                break;
            case '2':
                $entradaysalida_tabla = 'entradaysalida_organico';
                break;
            case '5':
                $entradaysalida_tabla = 'entradaysalida_mantequilla';
                break;
            case '6':
                $entradaysalida_tabla = 'entradaysalida_altiplano';
                break;
            case '7':
                $entradaysalida_tabla = 'entradaysalida_naranjo';
                break;
                
            case '8':
                $entradaysalida_tabla = 'entradaysalida_aguacate';
                break;
            case '9':
                $entradaysalida_tabla = 'entradaysalida_mezquite';
                break;
            default:
                throw new Exception('Tipo de miel no es válido');
                break;
        }


        $sqlNombreResponsable = "SELECT p.nombre FROM $entradaysalida_tabla rd
                                LEFT JOIN personaloaxaca p ON p.idPersonalOM = rd.responsable
                                WHERE rd.idReporte = $idReporte";
        return $sqlNombreResponsable;
    }


    function nombreSupervisorReporteDesCar($tipoDeMiel, $idReporte)
    {

        switch ($tipoDeMiel) {
            case '0':
                $entradaysalida_tabla = 'entradaysalida';
                break;
            case '1':
                $entradaysalida_tabla = 'entradaysalida';
                break;
            case '2':
                $entradaysalida_tabla = 'entradaysalida_organico';
                break;
            case '5':
                $entradaysalida_tabla = 'entradaysalida_mantequilla';
                break;
            case '6':
                $entradaysalida_tabla = 'entradaysalida_altiplano';
                break;
            case '7':
                $entradaysalida_tabla = 'entradaysalida_naranjo';
                break;
                
            case '8':
                $entradaysalida_tabla = 'entradaysalida_aguacate';
                break;
            case '9':
                $entradaysalida_tabla = 'entradaysalida_mezquite';
                break;
            default:
                throw new Exception('Tipo de miel no es válido');
                break;
        }


        $sqlNombreSuper = "SELECT p.nombre FROM $entradaysalida_tabla rd
                                LEFT JOIN personaloaxaca p ON p.idPersonalOM = rd.supervisor
                                WHERE rd.idReporte = $idReporte";
        return $sqlNombreSuper;
    }

    function nombrePersonalLimpieza($tipoDeMiel, $idReporte)
    {

        switch ($tipoDeMiel) {
            case '0':
                $entradaysalida_tabla = 'entradaysalida';
                break;
            case '1':
                $entradaysalida_tabla = 'entradaysalida';
                break;
            case '2':
                $entradaysalida_tabla = 'entradaysalida_organico';
                break;
            case '5':
                $entradaysalida_tabla = 'entradaysalida_mantequilla';
                break;
            case '6':
                $entradaysalida_tabla = 'entradaysalida_altiplano';
                break;
            case '7':
                $entradaysalida_tabla = 'entradaysalida_naranjo';
                break;
                
            case '8':
                $entradaysalida_tabla = 'entradaysalida_aguacate';
                break;
            case '9':
                $entradaysalida_tabla = 'entradaysalida_mezquite';
                break;
            default:
                throw new Exception('Tipo de miel no es válido');
                break;
        }

        $sqlNombreLimpieza = "SELECT po.nombre FROM $entradaysalida_tabla rd
                            LEFT JOIN personalacciones per ON per.idPersonal = rd.idPersonal
                            LEFT JOIN personaloaxaca po ON po.idPersonalOM = per.limpiezaPersonal
                            WHERE rd.idReporte = $idReporte ";
        return $sqlNombreLimpieza;
    }

    function nombrePersonalMarcacion($tipoDeMiel, $idReporte)
    {
        switch ($tipoDeMiel) {
            case '0':
                $entradaysalida_tabla = 'entradaysalida';
                break;
            case '1':
                $entradaysalida_tabla = 'entradaysalida';
                break;
            case '2':
                $entradaysalida_tabla = 'entradaysalida_organico';
                break;
            case '5':
                $entradaysalida_tabla = 'entradaysalida_mantequilla';
                break;
            case '6':
                $entradaysalida_tabla = 'entradaysalida_altiplano';
                break;
            case '7':
                $entradaysalida_tabla = 'entradaysalida_naranjo';
                break;
                
            case '8':
                $entradaysalida_tabla = 'entradaysalida_aguacate';
                break;
            case '9':
                $entradaysalida_tabla = 'entradaysalida_mezquite';
                break;
                
            default:
                throw new Exception('Tipo de miel no es válido');
                break;
        }

        $sqlMaracacion = "SELECT po.nombre FROM $entradaysalida_tabla rd
                        LEFT JOIN personalacciones per ON per.idPersonal = rd.idPersonal
                        LEFT JOIN personaloaxaca po ON po.idPersonalOM = per.marcacion
                        WHERE rd.idReporte  =$idReporte";
        return $sqlMaracacion;
    }

    function nombrePersonalRotulacion($tipoDeMiel, $idReporte)
    {
        switch ($tipoDeMiel) {
            case '0':
                $entradaysalida_tabla = 'entradaysalida';
                break;
            case '1':
                $entradaysalida_tabla = 'entradaysalida';
                break;
            case '2':
                $entradaysalida_tabla = 'entradaysalida_organico';
                break;
            case '5':
                $entradaysalida_tabla = 'entradaysalida_mantequilla';
                break;
            case '6':
                $entradaysalida_tabla = 'entradaysalida_altiplano';
                break;
            case '7':
                $entradaysalida_tabla = 'entradaysalida_naranjo';
                break;
                
            case '8':
                $entradaysalida_tabla = 'entradaysalida_aguacate';
                break;
            case '9':
                $entradaysalida_tabla = 'entradaysalida_mezquite';
                break;
            default:
                throw new Exception('Tipo de miel no es válido');
                break;
        }

        $sqlRotulacion = "SELECT po.nombre FROM $entradaysalida_tabla rd
                        LEFT JOIN personalacciones per ON per.idPersonal = rd.idPersonal
                        LEFT JOIN personaloaxaca po ON po.idPersonalOM = per.rotulacion
                        WHERE rd.idReporte =$idReporte";
        return $sqlRotulacion;
    }

    function nombreMontacargas($tipoDeMiel, $idReporte)
    {

        switch ($tipoDeMiel) {
            case '0':
                $entradaysalida_tabla = 'entradaysalida';
                break;
            case '1':
                $entradaysalida_tabla = 'entradaysalida';
                break;
            case '2':
                $entradaysalida_tabla = 'entradaysalida_organico';
                break;
            case '5':
                $entradaysalida_tabla = 'entradaysalida_mantequilla';
                break;
            case '6':
                $entradaysalida_tabla = 'entradaysalida_altiplano';
                break;
            case '7':
                $entradaysalida_tabla = 'entradaysalida_naranjo';
                break;
                
            case '8':
                $entradaysalida_tabla = 'entradaysalida_aguacate';
                break;
            case '9':
                $entradaysalida_tabla = 'entradaysalida_mezquite';
                break;
            default:
                throw new Exception('Tipo de miel no es válido');
                break;
        }


        $sqlMontacargas = "SELECT po.nombre FROM $entradaysalida_tabla rd
                        LEFT JOIN personalacciones per ON per.idPersonal = rd.idPersonal
                        LEFT JOIN personaloaxaca po ON po.idPersonalOM = per.montacargas
                        WHERE rd.idReporte = $idReporte";
        return $sqlMontacargas;
    }

    function personalDesCar($tipoDeMiel, $idReporte)
    {

        switch ($tipoDeMiel) {
            case '0':
                $personal_tabla = 'personaldescarga';
                break;
            case '1':
                $personal_tabla = 'personaldescarga';
                break;
            case '2':
                $personal_tabla = 'personaldescarga_organico';
                break;
            case '5':
                $personal_tabla = 'personaldescarga_mantequilla';
                break;
            case '6':
                $personal_tabla = 'personaldescarga_altiplano';
                break;
            case '7':
                $personal_tabla = 'personaldescarga_naranjo';
                break;
                
            case '8':
                $personal_tabla = 'personaldescarga_aguacate';
                break;
            case '9':
                $personal_tabla = 'personaldescarga_mezquite';
                break;
            default:
                throw new Exception('Tipo de miel no es válido');
                break;
        }


        $sqlPersonal = "SELECT pd.idPersonalDescarga, pd.idPersonalOM, om.nombre 
                        FROM $personal_tabla pd 
                        LEFT JOIN personaloaxaca om ON om.idPersonalOM = pd.idPersonalOM
                        WHERE idReporte = $idReporte";
        return $sqlPersonal;
    }

    function configuracionLaboratorio($tipoDeMiel)
    {

        switch ($tipoDeMiel) {
            case '1':
                $configuracionlaboratorio_tabla = 'configuracionlaboratorio';
                break;
            case '2':
                $configuracionlaboratorio_tabla = 'configuracionlaboratorio_organico';
                break;
            default:
                break;
        };

        $sqlConf = "SELECT * FROM $configuracionlaboratorio_tabla WHERE idOpcionLab = 2 OR idOpcionLab = 3 or idOpcionLab = 4 or idOpcionLab = 5;";
        return $sqlConf;
    }

    function reportesLaboratorio($oLaboratorio, $tipoDeMiel)
    {

        switch ($tipoDeMiel) {
            case '1':
                $almacen_tabla = 'almacen';
                $almacenencabezado_tabla = 'almacenencabezado';
                $laboratorio_tabla = 'laboratorio';
                break;
            case '2':
                $almacen_tabla = 'almacen_organico';
                $almacenencabezado_tabla = 'almacenencabezado_organico';
                $laboratorio_tabla = 'laboratorio_organico';
                break;

            default:
                break;
        };

        if ($oLaboratorio->sinFecha == 2) {
            $sqlLab = "SELECT ae.fecha, l.localidad, al.idAlmacen, al.neto, lab.sf, lab.st, lab.hmf, lab.c13
                            FROM $almacen_tabla al
                            LEFT JOIN $almacenencabezado_tabla ae on ae.idAlmacen = al.idAlmacenEncabezado
                            LEFT JOIN proveedor p ON p.idProveedor = ae.idProveedor 
                            LEFT JOIN direccion d ON d.idDireccion = p.idDireccion 
                            LEFT JOIN localidades l ON l.idlocalidad = d.idlocalidad 
                            LEFT JOIN $laboratorio_tabla lab ON lab.idAlmacen = al.idAlmacen  
                            WHERE lab.idLaboratorio != 0";
        } else {
            $sqlLab = "SELECT ae.fecha, l.localidad, al.idAlmacen, al.neto, lab.sf, lab.st, lab.hmf, lab.c13
                            FROM $almacen_tabla al
                            LEFT JOIN $almacenencabezado_tabla ae on ae.idAlmacen = al.idAlmacenEncabezado
                            LEFT JOIN proveedor p ON p.idProveedor = ae.idProveedor 
                            LEFT JOIN direccion d ON d.idDireccion = p.idDireccion 
                            LEFT JOIN localidades l ON l.idlocalidad = d.idlocalidad 
                            LEFT JOIN $laboratorio_tabla lab ON lab.idAlmacen = al.idAlmacen  
                            WHERE lab.idLaboratorio != 0 AND ae.fecha BETWEEN '$oLaboratorio->fInicial' AND '$oLaboratorio->fFinal'";
        }

        return $sqlLab;
    }

    //    function reportesLaboratorio($oLaboratorio) {
    //
    //        if ($oLaboratorio->sinFecha == 2) {
    //            $sqlLab = "SELECT ae.fecha, l.localidad AS Localidad, count(al.idAlmacen) AS Tambores, SUM(al.neto) AS Kgs,
    //                             COUNT(IF((lab.sf >= 1980 OR lab.sf BETWEEN 1576 AND 1979),'SFAprobado',NULL)) AS SFAprobado,
    //                            COUNT(IF(lab.sf < 1575,'1',NULL)) AS SFRechazado,
    //                            COUNT(IF(lab.st<1281,1,NULL)) AS STRechazados,
    //                            COUNT(IF(lab.hmf<=10,1,NULL)) AS HMFAprobado,
    //                            COUNT(IF(lab.hmf>10,1,NULL)) AS HMFRechazado,
    //                            COUNT(IF((lab.c13 < 983 OR lab.c13 BETWEEN 985 AND 100000),'Adulteracion',NULL)) AS Adulteracion,
    //                            COUNT(IF((lab.c13 BETWEEN 983 AND 984.79),'Delta 13',NULL)) AS Delta13,
    //                            COUNT(IF((lab.c13 BETWEEN 984.80 AND 984.99),'C4',NULL)) AS C4
    //                            FROM almacen al
    //                            LEFT JOIN almacenencabezado ae on ae.idAlmacen = al.idAlmacenEncabezado
    //                            LEFT JOIN proveedor p ON p.idProveedor = ae.idProveedor 
    //                            LEFT JOIN direccion d ON d.idDireccion = p.idDireccion 
    //                            LEFT JOIN localidades l ON l.idlocalidad = d.idlocalidad 
    //                            LEFT JOIN laboratorio lab ON lab.idAlmacen = al.idAlmacen  
    //                            WHERE lab.sf != 0
    //                            GROUP BY l.localidad ORDER BY Tambores DESC";
    //        } else {
    //            $sqlLab = "SELECT ae.fecha, l.localidad AS Localidad, count(al.idAlmacen) AS Tambores, SUM(al.neto) AS Kgs,
    //                             COUNT(IF((lab.sf >= 1980 OR lab.sf BETWEEN 1576 AND 1979),'SFAprobado',NULL)) AS SFAprobado,
    //                            COUNT(IF(lab.sf < 1575,'1',NULL)) AS SFRechazado,
    //                            COUNT(IF(lab.st<1281,1,NULL)) AS STRechazados,
    //                            COUNT(IF(lab.hmf<=10,1,NULL)) AS HMFAprobado,
    //                            COUNT(IF(lab.hmf>10,1,NULL)) AS HMFRechazado,
    //                            COUNT(IF((lab.c13 < 983 OR lab.c13 BETWEEN 985 AND 100000),'Adulteracion',NULL)) AS Adulteracion,
    //                            COUNT(IF((lab.c13 BETWEEN 983 AND 984.79),'Delta 13',NULL)) AS Delta13,
    //                            COUNT(IF((lab.c13 BETWEEN 984.80 AND 984.99),'C4',NULL)) AS C4
    //                            FROM almacen al
    //                            LEFT JOIN almacenencabezado ae on ae.idAlmacen = al.idAlmacenEncabezado
    //                            LEFT JOIN proveedor p ON p.idProveedor = ae.idProveedor 
    //                            LEFT JOIN direccion d ON d.idDireccion = p.idDireccion 
    //                            LEFT JOIN localidades l ON l.idlocalidad = d.idlocalidad 
    //                            LEFT JOIN laboratorio lab ON lab.idAlmacen = al.idAlmacen  
    //                            WHERE lab.sf != 0 AND ae.fecha BETWEEN '$oLaboratorio->fInicial' AND '$oLaboratorio->fFinal'
    //                            GROUP BY l.localidad ORDER BY Tambores DESC";
    //        }
    //
    //        return $sqlLab;
    //    }

    function resumenConcentrado($oLaboratorio)
    {
        //        //require_once 'C:/xampp/htdocs/oaxacamiel/DAOConeccion/coneccion.php';
        //        require_once '/home/darias66/public_html/sysaindustrial.mx/oaxacaMiel/DAOConeccion/coneccion.php';
        //        $cn = new Coneccion();
        //        $cn->Conectarse();

        $glo = new consultas();
        if ($oLaboratorio->sinFecha == 2) {
            $porCiento = "(SUM(menor195) + SUM(Exportacion20) + SUM(Exportacion205) + SUM(Exportacion21) + SUM(Exportacionmas21)+  SUM(Mayores22)";

            $sqAnt = "SELECT SUM(SFAprobado) as AprobadosSF, SUM(SFRechazado) AS SFRechazado, SUM(STRechazados) AS STRechazado,
                         SUM(Tambores) AS Tambores, (SUM(SFAprobado)/SUM(Tambores))*100 as Aprob, (SUM(SFRechazado)/SUM(Tambores))*100 as SFRech,
                        (SUM(STRechazados)/SUM(Tambores))*100 as STRech,
                         SUM(HMFAprobado) AS HMFapro, SUM(HMFRechazado) AS HMFRech, 
                        (SUM(HMFAprobado)/SUM(Tambores))*100 as HMFapropor,(SUM(HMFRechazado)/SUM(Tambores))*100 as HMFrechporc,
                         SUM(Delta13) AS delta13, SUM(C4) AS C4, SUM(Adulteracion) AS Adulteracion, (SUM(Delta13)/SUM(Tambores))*100 AS porcDelta13, 
                        (SUM(C4)/SUM(Tambores))*100 AS porcC4, (SUM(Adulteracion)/SUM(Tambores))*100 as porcAdult, 
                         SUM(menor195)  AS Exportmenor19, SUM(Exportacion20) AS Exportacio20, SUM(Exportacion205) AS Exportacion205,
                         SUM(Exportacion21) AS Exportacion21, SUM(Exportacionmas21) AS Exportacionmas21, SUM(Mayores22) AS Mayores22,
                        ((SUM(menor195)/$porCiento)*100)) AS procentaje19,
			((SUM(Exportacion20)/$porCiento)*100)) AS procentaje20,
			((SUM(Exportacion205)/$porCiento)*100)) AS procentaje205,
			((SUM(Exportacion21)/$porCiento)*100)) AS procentaje21,
			((SUM(Exportacionmas21)/$porCiento)*100)) AS procentajemas21,
			((SUM(Mayores22)/$porCiento)*100)) AS procentajemas22
                         FROM ((" . $GLOBALS['miSELECT'] . " WHERE lab.sf != 0
                            GROUP BY l.localidad ORDER BY Tambores DESC)) AS ANALISIS";
        } else {
            $porCiento = "(SUM(menor195) + SUM(Exportacion20) + SUM(Exportacion205) + SUM(Exportacion21) + SUM(Exportacionmas21)+  SUM(Mayores22)";

            $sqAnt = "SELECT SUM(SFAprobado) as AprobadosSF, SUM(SFRechazado) AS SFRechazado, SUM(STRechazados) AS STRechazado,
                         SUM(Tambores) AS Tambores, (SUM(SFAprobado)/SUM(Tambores))*100 as Aprob, (SUM(SFRechazado)/SUM(Tambores))*100 as SFRech,
                        (SUM(STRechazados)/SUM(Tambores))*100 as STRech,
                         SUM(HMFAprobado) AS HMFapro, SUM(HMFRechazado) AS HMFRech, 
                        (SUM(HMFAprobado)/SUM(Tambores))*100 as HMFapropor,(SUM(HMFRechazado)/SUM(Tambores))*100 as HMFrechporc,
                         SUM(Delta13) AS delta13, SUM(C4) AS C4, SUM(Adulteracion) AS Adulteracion, (SUM(Delta13)/SUM(Tambores))*100 AS porcDelta13, 
                        (SUM(C4)/SUM(Tambores))*100 AS porcC4, (SUM(Adulteracion)/SUM(Tambores))*100 as porcAdult, 
                         SUM(menor195)  AS Exportmenor19, SUM(Exportacion20) AS Exportacio20, SUM(Exportacion205) AS Exportacion205,
                         SUM(Exportacion21) AS Exportacion21, SUM(Exportacionmas21) AS Exportacionmas21, SUM(Mayores22) AS Mayores22,
                        ((SUM(menor195)/$porCiento)*100)) AS procentaje19,
			((SUM(Exportacion20)/$porCiento)*100)) AS procentaje20,
			((SUM(Exportacion205)/$porCiento)*100)) AS procentaje205,
			((SUM(Exportacion21)/$porCiento)*100)) AS procentaje21,
			((SUM(Exportacionmas21)/$porCiento)*100)) AS procentajemas21,
			((SUM(Mayores22)/$porCiento)*100)) AS procentajemas22
                         FROM ((" . $GLOBALS['miSELECT'] . " WHERE lab.sf != 0 AND ae.fecha BETWEEN '$oLaboratorio->fInicial' AND '$oLaboratorio->fFinal'
                            GROUP BY l.localidad ORDER BY Tambores DESC)) AS ANALISIS
                        WHERE fecha BETWEEN '$oLaboratorio->fInicial' AND '$oLaboratorio->fFinal'";
        }

        //        $datos = mysql_query($sqAnt);
        //        $cn->cerrarBd();
        return $sqAnt;
    }

    function antibioticos($oLaboratorio)
    {
        //require_once 'C:/xampp/htdocs/oaxacamiel/DAOConeccion/coneccion.php';
        //        require_once '/home/darias66/public_html/sysaindustrial.mx/oaxacaMiel/DAOConeccion/coneccion.php';
        //        $cn = new Coneccion();
        //        $cn->Conectarse();

        if ($oLaboratorio->sinFecha == 2) {
            $sqlAnti = "SELECT localidad,  SFRechazado, STRechazados
                    FROM  ((" . $GLOBALS['miSELECT'] . " WHERE lab.sf != 0
                            GROUP BY l.localidad ORDER BY Tambores DESC)) as ANALISIS
                            WHERE  SFRechazado > 0 
                            ORDER BY  SFRechazado DESC";
        } else {
            $sqlAnti = "SELECT localidad,  SFRechazado, STRechazados
                    FROM  ((" . $GLOBALS['miSELECT'] . "  WHERE lab.sf != 0 AND fecha BETWEEN '$oLaboratorio->fInicial' AND '$oLaboratorio->fFinal'
                            GROUP BY l.localidad ORDER BY Tambores DESC)) as ANALISIS
                            WHERE  SFRechazado > 0 AND fecha BETWEEN '$oLaboratorio->fInicial' AND '$oLaboratorio->fFinal'
                            ORDER BY  SFRechazado DESC";
        }

        //        $datos = mysql_query($sqlAnti);
        //        $cn->cerrarBd();
        return $sqlAnti;
    }

    function sumaTotal($valor, $oLaboratorio)
    {
        //require_once 'C:/xampp/htdocs/oaxacamiel/DAOConeccion/coneccion.php';
        //        require_once '/home/darias66/public_html/sysaindustrial.mx/oaxacaMiel/DAOConeccion/coneccion.php';
        //        $cn = new Coneccion();
        //        $cn->Conectarse();
        if ($oLaboratorio->sinFecha == 2) {
            $suma = "SELECT SUM($valor) as SumaTotalValor
                    FROM  ((" . $GLOBALS['miSELECT'] . " WHERE lab.sf != 0
                            GROUP BY l.localidad ORDER BY Tambores DESC)) as ANALISIS                         
                            ORDER BY  SFRechazado DESC";
        } else {
            $suma = "SELECT SUM($valor) as SumaTotalValor
                    FROM  ((" . $GLOBALS['miSELECT'] . " WHERE lab.sf != 0 AND fecha BETWEEN '$oLaboratorio->fInicial' AND '$oLaboratorio->fFinal'
                            GROUP BY l.localidad ORDER BY Tambores DESC)) as ANALISIS 
                            WHERE fecha BETWEEN '$oLaboratorio->fInicial' AND '$oLaboratorio->fFinal'
                            ORDER BY  SFRechazado DESC";
        }


        //        $datos = mysql_query($suma);
        //        $cn->cerrarBd();
        //        return $datos;
        return $suma;
    }

    function antibioticos1($oLaboratorio)
    {
        // require_once 'C:/xampp/htdocs/oaxacamiel/DAOConeccion/coneccion.php';
        //        require_once '/home/darias66/public_html/sysaindustrial.mx/oaxacaMiel/DAOConeccion/coneccion.php';
        //        $cn = new Coneccion();
        //        $cn->Conectarse();
        if ($oLaboratorio->sinFecha == 2) {
            $sqlAnti = "SELECT localidad, STRechazados
                    FROM  (SELECT ae.fecha, l.localidad AS Localidad, count(al.idAlmacen) AS Tambores, SUM(al.neto) AS Kgs,
                            COUNT(IF((lab.sf >= 1980 OR lab.sf BETWEEN 1576 AND 1979),'SFAprobado',NULL)) AS SFAprobado,
                            COUNT(IF(lab.sf < 1575,'1',NULL)) AS SFRechazado,
                            COUNT(IF(lab.st<1281,1,NULL)) AS STRechazados,
                            COUNT(IF(lab.hmf<=10,1,NULL)) AS HMFAprobado,
                            COUNT(IF(lab.hmf>10,1,NULL)) AS HMFRechazado,
                            COUNT(IF((lab.c13 < 983 OR lab.c13 BETWEEN 985 AND 100000),'Adulteracion',NULL)) AS Adulteracion,
                            COUNT(IF((lab.c13 BETWEEN 983 AND 984.79),'Delta 13',NULL)) AS Delta13,
                            COUNT(IF((lab.c13 BETWEEN 984.80 AND 984.99),'C4',NULL)) AS C4,
                            COUNT(IF(porcentaje <= 19.5, 'Humedad1',NULL )) AS menor195,
                            COUNT(IF((lab.porcentaje BETWEEN 19.6 AND 20),'Exportacion20',NULL)) AS Exportacion20,
                            COUNT(IF((lab.porcentaje BETWEEN 20.1 AND 20.5),'Exportacion205',NULL)) AS Exportacion205,
                            COUNT(IF((lab.porcentaje BETWEEN 20.6 AND 21),'Exportacion21',NULL)) AS Exportacion21,
                            COUNT(IF((lab.porcentaje BETWEEN 21.1 AND 22),'Exportacion20',NULL)) AS Exportacionmas21,
                            COUNT(IF(lab.porcentaje >22, 'RECHAZADOS',NULL )) AS Mayores22
                            FROM almacen al
                            LEFT JOIN almacenencabezado ae on ae.idAlmacen = al.idAlmacenEncabezado
                            LEFT JOIN proveedor p ON p.idProveedor = ae.idProveedor 
                            LEFT JOIN direccion d ON d.idDireccion = p.idDireccion 
                            LEFT JOIN localidades l ON l.idlocalidad = d.idlocalidad 
                            LEFT JOIN laboratorio lab ON lab.idAlmacen = al.idAlmacen  
                            GROUP BY l.localidad ORDER BY Tambores DESC) as ANALISIS
                            WHERE  STRechazados > 0                       
                            ORDER BY  SFRechazado DESC";
        } else {
            $sqlAnti = "SELECT localidad, STRechazados
                    FROM  (SELECT ae.fecha, l.localidad AS Localidad, count(al.idAlmacen) AS Tambores, SUM(al.neto) AS Kgs,
                            COUNT(IF((lab.sf >= 1980 OR lab.sf BETWEEN 1576 AND 1979),'SFAprobado',NULL)) AS SFAprobado,
                            COUNT(IF(lab.sf < 1575,'1',NULL)) AS SFRechazado,
                            COUNT(IF(lab.st<1281,1,NULL)) AS STRechazados,
                            COUNT(IF(lab.hmf<=10,1,NULL)) AS HMFAprobado,
                            COUNT(IF(lab.hmf>10,1,NULL)) AS HMFRechazado,
                            COUNT(IF((lab.c13 < 983 OR lab.c13 BETWEEN 985 AND 100000),'Adulteracion',NULL)) AS Adulteracion,
                            COUNT(IF((lab.c13 BETWEEN 983 AND 984.79),'Delta 13',NULL)) AS Delta13,
                            COUNT(IF((lab.c13 BETWEEN 984.80 AND 984.99),'C4',NULL)) AS C4,
                            COUNT(IF(porcentaje <= 19.5, 'Humedad1',NULL )) AS menor195,
                            COUNT(IF((lab.porcentaje BETWEEN 19.6 AND 20),'Exportacion20',NULL)) AS Exportacion20,
                            COUNT(IF((lab.porcentaje BETWEEN 20.1 AND 20.5),'Exportacion205',NULL)) AS Exportacion205,
                            COUNT(IF((lab.porcentaje BETWEEN 20.6 AND 21),'Exportacion21',NULL)) AS Exportacion21,
                            COUNT(IF((lab.porcentaje BETWEEN 21.1 AND 22),'Exportacion20',NULL)) AS Exportacionmas21,
                            COUNT(IF(lab.porcentaje >22, 'RECHAZADOS',NULL )) AS Mayores22
                            FROM almacen al
                            LEFT JOIN almacenencabezado ae on ae.idAlmacen = al.idAlmacenEncabezado
                            LEFT JOIN proveedor p ON p.idProveedor = ae.idProveedor 
                            LEFT JOIN direccion d ON d.idDireccion = p.idDireccion 
                            LEFT JOIN localidades l ON l.idlocalidad = d.idlocalidad 
                            LEFT JOIN laboratorio lab ON lab.idAlmacen = al.idAlmacen 
                            WHERE ae.fecha BETWEEN '$oLaboratorio->fInicial' AND '$oLaboratorio->fFinal'
                            GROUP BY l.localidad ORDER BY Tambores DESC) as ANALISIS
                            WHERE  STRechazados > 0  AND fecha BETWEEN '$oLaboratorio->fInicial' AND '$oLaboratorio->fFinal'                      
                            ORDER BY  SFRechazado DESC";
        }

        //        $datos = mysql_query($sqlAnti);
        //        $cn->cerrarBd();
        return $sqlAnti;
    }

    function porcentajeRechazo($oLaboratorio, $tipoDeMiel)
    {

        switch ($tipoDeMiel) {
            case '1':
                $almacen_tabla = 'almacen';
                $almacenencabezado_tabla = 'almacenencabezado';
                $laboratorio_tabla = 'laboratorio';
                break;
            case '2':
                $almacen_tabla = 'almacen_organico';
                $almacenencabezado_tabla = 'almacenencabezado_organico';
                $laboratorio_tabla = 'laboratorio_organico';
                break;

            default:
                break;
        };

        if ($oLaboratorio->sinFecha == 2) {
            $sqlRechazo = "SELECT p.nombre, l.localidad, COUNT(al.idAlmacen) as Tambores, COUNT(IF(lab.sf < 1575,'1',NULL)) AS SFRechazado,COUNT(IF(lab.st<1281,1,NULL)) AS STRechazados,
				COUNT(IF(lab.hmf>10,1,NULL)) AS HMFRechazado,COUNT(IF((lab.c13 < 983 OR lab.c13 BETWEEN 985 AND 100000),'Adulteracion',NULL)) AS Adulteracion,
				COUNT(IF((lab.porcentaje BETWEEN 21.1 AND 22 OR lab.porcentaje > 22),'Rechazado',null)) AS Rechazado
                                FROM $almacen_tabla al
                                LEFT JOIN $almacenencabezado_tabla ae on ae.idAlmacen = al.idAlmacenEncabezado
                                LEFT JOIN proveedor p ON p.idProveedor = ae.idProveedor 
                                LEFT JOIN direccion d ON d.idDireccion = p.idDireccion 
                                LEFT JOIN localidades l ON l.idlocalidad = d.idlocalidad 
                                LEFT JOIN $laboratorio_tabla lab ON lab.idAlmacen = al.idAlmacen 
                                WHERE lab.sf != 0 
                                GROUP BY p.nombre
                                ORDER BY p.nombre ASC";
        } else {
            $sqlRechazo = "SELECT p.nombre, l.localidad, COUNT(al.idAlmacen) as Tambores, COUNT(IF(lab.sf < 1575,'1',NULL)) AS SFRechazado,COUNT(IF(lab.st<1281,1,NULL)) AS STRechazados,
				COUNT(IF(lab.hmf>10,1,NULL)) AS HMFRechazado,COUNT(IF((lab.c13 < 983 OR lab.c13 BETWEEN 985 AND 100000),'Adulteracion',NULL)) AS Adulteracion,
				COUNT(IF((lab.porcentaje BETWEEN 21.1 AND 22 OR lab.porcentaje > 22),'Rechazado',null)) AS Rechazado
                                FROM $almacen_tabla al
                                LEFT JOIN $almacenencabezado_tabla ae on ae.idAlmacen = al.idAlmacenEncabezado
                                LEFT JOIN proveedor p ON p.idProveedor = ae.idProveedor 
                                LEFT JOIN direccion d ON d.idDireccion = p.idDireccion 
                                LEFT JOIN localidades l ON l.idlocalidad = d.idlocalidad 
                                LEFT JOIN $laboratorio_tabla lab ON lab.idAlmacen = al.idAlmacen 
                                WHERE lab.sf != 0  AND  ae.fecha BETWEEN '$oLaboratorio->fInicial' AND '$oLaboratorio->fFinal'
                                GROUP BY p.nombre
                                ORDER BY p.nombre ASC";
        }

        return $sqlRechazo;
    }

    function concentradoProveedores($oConcentradoProve)
    {


        switch ($oConcentradoProve->tipoDeMiel) {
            case '1':
                if ($oConcentradoProve->recipiente == 3) {
                    $tabla = 'almacen';
                    $encabezado = 'almacenencabezado';
                } else {
                    $tabla = 'cubetasdetalle';
                    $encabezado = 'cubetasencabezado';
                }
                $tambores_tabla = 'almacen';
                $tambores_encabezado = 'almacenencabezado';
                $cubetas_tabla = 'cubetasdetalle';
                $cubetasencabezado_tabla = 'cubetasencabezado';
                break;
            case '2':
                if ($oConcentradoProve->recipiente == 3) {
                    $tabla = 'almacen_organico';
                    $encabezado = 'almacenencabezado_organico';
                } else {
                    $tabla = 'cubetasdetalle_organico';
                    $encabezado = 'cubetasencabezado_organico';
                }
                $tambores_tabla = 'almacen_organico';
                $tambores_encabezado = 'almacenencabezado_organico';
                $cubetas_tabla = 'cubetasdetalle_organico';
                $cubetasencabezado_tabla = 'cubetasencabezado_organico';
                break;
        }



        if ($oConcentradoProve->ambos == 5) {
            $sqlConcentradopro = "SELECT UCASE(nombrep) AS nombrep, UCASE(localidadp) AS localidadp, COUNT(tc) AS tambores, SUM(plistap) AS plista, SUM(brutop) AS bruto, SUM(tarap) AS tara, 
                SUM(netop) AS neto , SUM(difp) AS dif            
                FROM(SELECT al.idAlmacen AS tc, al.idAlmacenEncabezado, alms.fecha, al.zona, 
                al.pesoLista AS plistap, al.bruto AS brutop, al.tara AS tarap, al.neto AS netop, al.diferencia AS difp,
                al.precio, al.costoTotal,prv.nombre AS nombrep, lc.localidad AS localidadp, prv.idSagarpa
                FROM $tambores_tabla al
                LEFT JOIN $tambores_encabezado alms ON alms.idAlmacen = al.idAlmacenEncabezado
                LEFT JOIN proveedor prv ON prv.idProveedor = alms.idProveedor
                LEFT JOIN direccion dir ON dir.idDireccion = prv.idDireccion
                LEFT JOIN localidades lc ON lc.idlocalidad = dir.idlocalidad
                UNION
                SELECT cd.idAlmacen AS tc, cd.idAlmacenEncabezado, ce.fecha, cd.zona, 
                cd.pesoLista AS plistap, cd.bruto AS brutop, cd.tara AS tarap, cd.neto AS netop, cd.diferencia AS difp, 
                cd.precio, cd.costoTotal, prs.nombre AS nombrep, lcc.localidad AS localidadp, prs.idSagarpa
                FROM $cubetas_tabla cd
                LEFT JOIN $cubetasencabezado_tabla ce ON ce.idAlmacen = cd.idAlmacenEncabezado
                LEFT JOIN $tambores_encabezado alm ON alm.idAlmacen = ce.folioEntradaTambor
                LEFT JOIN proveedor prs ON prs.idProveedor = ce.idProveedor
                LEFT JOIN direccion dirc ON dirc.idDireccion = prs.idDireccion
                LEFT JOIN localidades lcc ON lcc.idlocalidad = dirc.idlocalidad 
                ORDER BY idAlmacenEncabezado ASC) AS concentrado
                GROUP BY nombrep
                ORDER BY nombrep";
        } else {
            $sqlConcentradopro = "SELECT UCASE(nombrep) AS nombrep, UCASE(localidadp) AS localidadp, COUNT(tambor) as Tambores, SUM(pesoListap) AS pesoLista, SUM(brutop) AS bruto, SUM(tarap) AS tara,
                SUM(netop) AS neto, SUM(diferp) AS dif 
                FROM (SELECT am.idAlmacen, am.fecha, pr.nombre AS nombrep, loca.localidad AS localidadp , pr.idSagarpa, al.zona,
                al.idAlmacen as tambor, al.pesoLista AS pesoListap, al.bruto AS brutop, al.tara AS tarap, al.neto AS netop,
                al.diferencia AS diferp
                FROM $tabla al
                LEFT JOIN $encabezado am ON am.idAlmacen = al.idAlmacenEncabezado
                LEFT JOIN proveedor pr ON pr.idProveedor = am.idProveedor
                LEFT JOIN direccion dir ON dir.idDireccion = pr.idDireccion
                LEFT JOIN localidades loca ON loca.idlocalidad = dir.idlocalidad                         
                ORDER BY am.idAlmacen ASC) AS Provconsetrado
                GROUP BY nombrep
                ORDER BY nombrep";
        }

        return $sqlConcentradopro;
    }

    function concentradoLocalidad($oConcentradoLoc)
    {

        switch ($oConcentradoLoc->tipoDeMiel) {
            case '1':
                if ($oConcentradoLoc->recipiente == 3) {
                    $tabla = 'almacen';
                    $encabezado = 'almacenencabezado';
                } else {
                    $tabla = 'cubetasdetalle';
                    $encabezado = 'cubetasencabezado';
                }
                $tambores_tabla = 'almacen';
                $tambores_encabezado = 'almacenencabezado';
                $cubetas_tabla = 'cubetasdetalle';
                $cubetasencabezado_tabla = 'cubetasencabezado';
                break;
            case '2':
                if ($oConcentradoLoc->recipiente == 3) {
                    $tabla = 'almacen_organico';
                    $encabezado = 'almacenencabezado_organico';
                } else {
                    $tabla = 'cubetasdetalle_organico';
                    $encabezado = 'cubetasencabezado_organico';
                }
                $tambores_tabla = 'almacen_organico';
                $tambores_encabezado = 'almacenencabezado_organico';
                $cubetas_tabla = 'cubetasdetalle_organico';
                $cubetasencabezado_tabla = 'cubetasencabezado_organico';
                break;
        }

        if ($oConcentradoLoc->ambos == 5) {
            $sqlConcentradoLoc = "SELECT  UCASE(localidadp) AS localidadp, COUNT(tc) AS tambores, SUM(plistap) AS plista, SUM(brutop) AS bruto, SUM(tarap) AS tara, 
                SUM(netop) AS neto , SUM(difp) AS dif            
                FROM(SELECT al.idAlmacen AS tc, al.idAlmacenEncabezado, alms.fecha, al.zona, 
                al.pesoLista AS plistap, al.bruto AS brutop, al.tara AS tarap, al.neto AS netop, al.diferencia AS difp,
                al.precio, al.costoTotal,prv.nombre AS nombrep, lc.localidad AS localidadp, prv.idSagarpa
                FROM $tambores_tabla al
                LEFT JOIN $tambores_encabezado alms ON alms.idAlmacen = al.idAlmacenEncabezado
                LEFT JOIN proveedor prv ON prv.idProveedor = alms.idProveedor
                LEFT JOIN direccion dir ON dir.idDireccion = prv.idDireccion
                LEFT JOIN localidades lc ON lc.idlocalidad = dir.idlocalidad
                UNION
                SELECT cd.idAlmacen AS tc, cd.idAlmacenEncabezado, ce.fecha, cd.zona, 
                cd.pesoLista AS plistap, cd.bruto AS brutop, cd.tara AS tarap, cd.neto AS netop, cd.diferencia AS difp, 
                cd.precio, cd.costoTotal, prs.nombre AS nombrep, lcc.localidad AS localidadp, prs.idSagarpa
                FROM $cubetas_tabla cd
                LEFT JOIN $cubetasencabezado_tabla ce ON ce.idAlmacen = cd.idAlmacenEncabezado
                LEFT JOIN $tambores_encabezado alm ON alm.idAlmacen = ce.folioEntradaTambor
                LEFT JOIN proveedor prs ON prs.idProveedor = ce.idProveedor
                LEFT JOIN direccion dirc ON dirc.idDireccion = prs.idDireccion
                LEFT JOIN localidades lcc ON lcc.idlocalidad = dirc.idlocalidad 
                ORDER BY idAlmacenEncabezado ASC) AS concentrado
                GROUP BY localidadp
                ORDER BY localidadp";
        } else {
            $sqlConcentradoLoc = "SELECT  UCASE(localidadp) AS localidadp, UCASE(nombrep) AS nombrep, COUNT(tambor) as Tambores, SUM(pesoListap) AS pesoLista, SUM(brutop) AS bruto, SUM(tarap) AS tara,
                SUM(netop) AS neto, SUM(diferp) AS dif 
                FROM (SELECT am.idAlmacen, am.fecha as fecha, pr.nombre AS nombrep, loca.localidad AS localidadp , pr.idSagarpa, al.zona,
                al.idAlmacen as tambor, al.pesoLista AS pesoListap, al.bruto AS brutop, al.tara AS tarap, al.neto AS netop,
                al.diferencia AS diferp
                FROM $tabla al
                LEFT JOIN $encabezado am ON am.idAlmacen = al.idAlmacenEncabezado
                LEFT JOIN proveedor pr ON pr.idProveedor = am.idProveedor
                LEFT JOIN direccion dir ON dir.idDireccion = pr.idDireccion
                LEFT JOIN localidades loca ON loca.idlocalidad = dir.idlocalidad                         
                ORDER BY am.idAlmacen ASC) AS Provconsetrado
                GROUP BY localidadp
                ORDER BY localidadp";
        }
        return $sqlConcentradoLoc;
    }

    function concentradoZona($oConcentradoZon)
    {


        switch ($oConcentradoZon->tipoDeMiel) {
            case '1':
                if ($oConcentradoZon->recipiente == 3) {
                    $tabla = 'almacen';
                    $encabezado = 'almacenencabezado';
                } else {
                    $tabla = 'cubetasdetalle';
                    $encabezado = 'cubetasencabezado';
                }
                $tambores_tabla = 'almacen';
                $tambores_encabezado = 'almacenencabezado';
                $cubetas_tabla = 'cubetasdetalle';
                $cubetasencabezado_tabla = 'cubetasencabezado';
                break;
            case '2':
                if ($oConcentradoZon->recipiente == 3) {
                    $tabla = 'almacen_organico';
                    $encabezado = 'almacenencabezado_organico';
                } else {
                    $tabla = 'cubetasdetalle_organico';
                    $encabezado = 'cubetasencabezado_organico';
                }
                $tambores_tabla = 'almacen_organico';
                $tambores_encabezado = 'almacenencabezado_organico';
                $cubetas_tabla = 'cubetasdetalle_organico';
                $cubetasencabezado_tabla = 'cubetasencabezado_organico';
                break;
        }



        if ($oConcentradoZon->ambos == 5) {
            $sqlConcentradoZon = "SELECT  UCASE(zonaCam) AS zonaCam, comprador, COUNT(tc) AS tambores, SUM(plistap) AS plista, SUM(brutop) AS bruto, SUM(tarap) AS tara, 
                SUM(netop) AS neto , SUM(difp) AS dif            
                FROM(SELECT al.idAlmacen AS tc, al.idAlmacenEncabezado, alms.fecha, al.zona, 
                al.pesoLista AS plistap, al.bruto AS brutop, al.tara AS tarap, al.neto AS netop, al.diferencia AS difp,
                al.precio, al.costoTotal,prv.nombre AS nombrep, lc.localidad AS localidadp, prv.idSagarpa, z.zona as zonaCam,
                cr.nombre AS comprador
                FROM $tambores_tabla al
                LEFT JOIN $tambores_encabezado alms ON alms.idAlmacen = al.idAlmacenEncabezado
                LEFT JOIN proveedor prv ON prv.idProveedor = alms.idProveedor
                LEFT JOIN direccion dir ON dir.idDireccion = prv.idDireccion
                LEFT JOIN localidades lc ON lc.idlocalidad = dir.idlocalidad
                LEFT JOIN zonas  z ON z.idzona = lc.idzona
                LEFT JOIN compradores cr ON cr.idcomprador = z.idcomprador
                UNION
                SELECT cd.idAlmacen AS tc, cd.idAlmacenEncabezado, ce.fecha, cd.zona, 
                cd.pesoLista AS plistap, cd.bruto AS brutop, cd.tara AS tarap, cd.neto AS netop, cd.diferencia AS difp, 
                cd.precio, cd.costoTotal, prs.nombre AS nombrep, lcc.localidad AS localidadp, prs.idSagarpa, zc.zona as zonaCam,
                crc.nombre AS comprador
                FROM $cubetas_tabla cd
                LEFT JOIN $cubetasencabezado_tabla ce ON ce.idAlmacen = cd.idAlmacenEncabezado
                LEFT JOIN $tambores_encabezado alm ON alm.idAlmacen = ce.folioEntradaTambor
                LEFT JOIN proveedor prs ON prs.idProveedor = ce.idProveedor
                LEFT JOIN direccion dirc ON dirc.idDireccion = prs.idDireccion
                LEFT JOIN localidades lcc ON lcc.idlocalidad = dirc.idlocalidad
                LEFT JOIN zonas zc ON zc.idzona =lcc.idzona
                LEFT JOIN compradores crc ON crc.idcomprador = zc.idcomprador
                ORDER BY idAlmacenEncabezado ASC) AS concentrado
                GROUP BY zonaCam
                ORDER BY zonaCam ";
        } else {
            $sqlConcentradoZon = "SELECT  UCASE(zontam) AS zona ,UCASE(localidadp) AS localidadp, UCASE(nombrep) AS nombrep, COUNT(tambor) as Tambores, SUM(pesoListap) AS pesoLista, SUM(brutop) AS bruto, SUM(tarap) AS tara,
                SUM(netop) AS neto, SUM(diferp) AS dif , comprador  
                FROM (SELECT am.idAlmacen, am.fecha as fecha, pr.nombre AS nombrep, loca.localidad AS localidadp , pr.idSagarpa, al.zona,
                al.idAlmacen as tambor, al.pesoLista AS pesoListap, al.bruto AS brutop, al.tara AS tarap, al.neto AS netop,
                al.diferencia AS diferp, z.zona AS zontam, cr.nombre AS comprador
                FROM $tabla al
                LEFT JOIN $encabezado am ON am.idAlmacen = al.idAlmacenEncabezado
                LEFT JOIN proveedor pr ON pr.idProveedor = am.idProveedor
                LEFT JOIN direccion dir ON dir.idDireccion = pr.idDireccion
                LEFT JOIN localidades loca ON loca.idlocalidad = dir.idlocalidad
                LEFT JOIN zonas z ON z.idzona = loca.idzona
                LEFT JOIN compradores cr ON cr.idcomprador = z.idcomprador
                ORDER BY am.idAlmacen ASC) AS Provconsetrado
                GROUP BY zontam
                ORDER BY zontam";
        }

        return $sqlConcentradoZon;
    }

    function proveedorsagarpa()
    {
        $sqlSagarpa = "SELECT pr.idProveedor, UCASE(pr.nombre) AS proveedor, pr.idSagarpa AS sagarpa, UCASE(z.zona) AS zona, UCASE(es.estado) AS estado, 
			    UCASE(l.localidad) AS localidad, d.direccionCompleta as direccion, d.colonia, t.telefono, c.correo, SUM(al.neto) AS neto 
                            FROM proveedor pr
                            LEFT JOIN direccion d ON d.idDireccion = pr.idDireccion
                            LEFT JOIN localidades l ON l.idlocalidad = d.idlocalidad
                            LEFT JOIN estados es ON es.idEstado = d.idEstado
                            INNER JOIN zonas z ON z.idzona = l
                            .idzona
                            LEFT JOIN telefonos t ON t.idContacto = pr.idDireccion
                            LEFT JOIN correos c ON c.idContacto = pr.idDireccion
                            LEFT JOIN almacenencabezado enca ON enca.idProveedor = pr.idProveedor
                            LEFT JOIN almacen al ON al.idAlmacenEncabezado = enca.idAlmacen
			    GROUP BY proveedor
                            ORDER BY idProveedor";
        return $sqlSagarpa;
    }

    //// A L E X ////
    function reporteAntibioticos($oLaboratorio, $tipoDeMiel)
    {

        switch ($tipoDeMiel) {
            case '1':
                $almacen_tabla = 'almacen';
                $almacenencabezado_tabla = 'almacenencabezado';
                $laboratorio_tabla = 'laboratorio';
                break;
            case '2':
                $almacen_tabla = 'almacen_organico';
                $almacenencabezado_tabla = 'almacenencabezado_organico';
                $laboratorio_tabla = 'laboratorio_organico';
                break;
            default:
                break;
        };

        if ($oLaboratorio->sinFecha == 2) {
            $sqlLab = "SELECT ae.fecha, l.localidad, al.idAlmacen, al.neto, lab.sf, lab.st
                            FROM $almacen_tabla al
                            LEFT JOIN $almacenencabezado_tabla ae on ae.idAlmacen = al.idAlmacenEncabezado
                            LEFT JOIN proveedor p ON p.idProveedor = ae.idProveedor 
                            LEFT JOIN direccion d ON d.idDireccion = p.idDireccion 
                            LEFT JOIN localidades l ON l.idlocalidad = d.idlocalidad 
                            LEFT JOIN $laboratorio_tabla lab ON lab.idAlmacen = al.idAlmacen  
                            WHERE lab.idLaboratorio != 0";
        } else {
            $sqlLab = "SELECT ae.fecha, l.localidad, al.idAlmacen, al.neto, lab.sf, lab.st
                            FROM $almacen_tabla al
                            LEFT JOIN $almacenencabezado_tabla ae on ae.idAlmacen = al.idAlmacenEncabezado
                            LEFT JOIN proveedor p ON p.idProveedor = ae.idProveedor 
                            LEFT JOIN direccion d ON d.idDireccion = p.idDireccion 
                            LEFT JOIN localidades l ON l.idlocalidad = d.idlocalidad 
                            LEFT JOIN $laboratorio_tabla lab ON lab.idAlmacen = al.idAlmacen  
                            WHERE lab.idLaboratorio != 0 AND ae.fecha BETWEEN '$oLaboratorio->fInicial' AND '$oLaboratorio->fFinal'";
        }

        return $sqlLab;
    }

    function configuracionLaboratorioAntibioticos($tipoDeMiel)
    {
        switch ($tipoDeMiel) {
            case '1':
                $configuracionlaboratorio_tabla = 'configuracionlaboratorio';
                break;
            case '2':
                $configuracionlaboratorio_tabla = 'configuracionlaboratorio_organico';
                break;
            default:
                break;
        };
        $sqlConf = "SELECT * FROM $configuracionlaboratorio_tabla WHERE idOpcionLab = 2 OR idOpcionLab = 3";
        return $sqlConf;
    }

    function configuracionLaboratorioHumedad($tipoDeMiel)
    {
        switch ($tipoDeMiel) {
            case '1':
                $configuracionlaboratorio_tabla = 'configuracionlaboratorio';
                break;
            case '2':
                $configuracionlaboratorio_tabla = 'configuracionlaboratorio_organico';
                break;
            default:
                break;
        };
        $sqlConf = "SELECT * FROM $configuracionlaboratorio_tabla WHERE idOpcionLab = 1";
        return $sqlConf;
    }

    function reportesLaboratorioHumedad($oLaboratorio, $tipoDeMiel)
    {
        switch ($tipoDeMiel) {
            case '1':
                $almacen_tabla = 'almacen';
                $almacenencabezado_tabla = 'almacenencabezado';
                $laboratorio_tabla = 'laboratorio';
                break;
            case '2':
                $almacen_tabla = 'almacen_organico';
                $almacenencabezado_tabla = 'almacenencabezado_organico';
                $laboratorio_tabla = 'laboratorio_organico';
                break;

            default:
                break;
        };
        if ($oLaboratorio->sinFecha == 2) {
            $sqlLab = "SELECT ae.fecha, l.localidad, al.idAlmacen, lab.porcentaje
                            FROM $almacen_tabla al
                            LEFT JOIN $almacenencabezado_tabla ae on ae.idAlmacen = al.idAlmacenEncabezado
                            LEFT JOIN proveedor p ON p.idProveedor = ae.idProveedor 
                            LEFT JOIN direccion d ON d.idDireccion = p.idDireccion 
                            LEFT JOIN localidades l ON l.idlocalidad = d.idlocalidad 
                            LEFT JOIN $laboratorio_tabla lab ON lab.idAlmacen = al.idAlmacen  
                            WHERE lab.idLaboratorio != 0";
        } else {
            $sqlLab = "SELECT ae.fecha, l.localidad, al.idAlmacen, lab.porcentaje
                            FROM $almacen_tabla al
                            LEFT JOIN $almacenencabezado_tabla ae on ae.idAlmacen = al.idAlmacenEncabezado
                            LEFT JOIN proveedor p ON p.idProveedor = ae.idProveedor 
                            LEFT JOIN direccion d ON d.idDireccion = p.idDireccion 
                            LEFT JOIN localidades l ON l.idlocalidad = d.idlocalidad 
                            LEFT JOIN $laboratorio_tabla lab ON lab.idAlmacen = al.idAlmacen  
                            WHERE lab.idLaboratorio != 0 AND ae.fecha BETWEEN '$oLaboratorio->fInicial' AND '$oLaboratorio->fFinal'";
        }

        return $sqlLab;
    }

    //HMF Menor a 10 es aprobado, Mayor a 10 es rechazado - Unico valor estático (Se maneja en el php) Archivos: [concentrado, hmf]

    function reporteHmf($oLaboratorio, $tipoDeMiel)
    {

        switch ($tipoDeMiel) {
            case '1':
                $almacen_tabla = 'almacen';
                $almacenencabezado_tabla = 'almacenencabezado';
                $laboratorio_tabla = 'laboratorio';
                break;
            case '2':
                $almacen_tabla = 'almacen_organico';
                $almacenencabezado_tabla = 'almacenencabezado_organico';
                $laboratorio_tabla = 'laboratorio_organico';
                break;

            default:
                break;
        };


        if ($oLaboratorio->sinFecha == 2) {
            $sqlLab = "SELECT ae.fecha, l.localidad, al.idAlmacen,lab.hmf
                            FROM $almacen_tabla al
                            LEFT JOIN $almacenencabezado_tabla ae on ae.idAlmacen = al.idAlmacenEncabezado
                            LEFT JOIN proveedor p ON p.idProveedor = ae.idProveedor 
                            LEFT JOIN direccion d ON d.idDireccion = p.idDireccion 
                            LEFT JOIN localidades l ON l.idlocalidad = d.idlocalidad 
                            LEFT JOIN $laboratorio_tabla lab ON lab.idAlmacen = al.idAlmacen  
                            WHERE lab.idLaboratorio != 0";
        } else {
            $sqlLab = "SELECT ae.fecha, l.localidad, al.idAlmacen,lab.hmf
                            FROM $almacen_tabla al
                            LEFT JOIN $almacenencabezado_tabla ae on ae.idAlmacen = al.idAlmacenEncabezado
                            LEFT JOIN proveedor p ON p.idProveedor = ae.idProveedor 
                            LEFT JOIN direccion d ON d.idDireccion = p.idDireccion 
                            LEFT JOIN localidades l ON l.idlocalidad = d.idlocalidad 
                            LEFT JOIN $laboratorio_tabla lab ON lab.idAlmacen = al.idAlmacen  
                            WHERE lab.idLaboratorio != 0 AND ae.fecha BETWEEN '$oLaboratorio->fInicial' AND '$oLaboratorio->fFinal'";
        }

        return $sqlLab;
    }

    function dameCondicionAlmacenamiento($idMes, $idAlmacen)
    {

        $sql = "SELECT mes, (SELECT CONCAT(subarea,' - ',nombre) FROM almacenestemporales WHERE idSubarea = '$idAlmacen') as almacen FROM meses WHERE idMes = '$idMes'";

        $sqlCondicion = "SELECT ca.semana1, ca.semana2, ca.semana3, ca.semana4, ca.semana5,
        t.tema, m.mes FROM condicionesalmacenamiento ca  
        INNER JOIN temas t
        ON t.idTema = ca.idTema
        INNER JOIN meses m 
        ON m.idMes = ca.idMes
        WHERE ca.idMes = '$idMes' AND ca.almacen = '$idAlmacen'";

        return [$sql, $sqlCondicion];
    }
}

class ReportesDeAlmacen
{

    //Reporte por Proveedor por todos los tambores y todas las cubetas.
    function ReportePorProveedor($objetProveedor)
    {
        switch ($objetProveedor->tipoDeMiel) {
            case '1':
                if ($objetProveedor->recipiente == 3) {
                    $tabla = 'almacen';
                    $encabezado = 'almacenencabezado';
                } else {
                    $tabla = 'cubetasdetalle';
                    $encabezado = 'cubetasencabezado';
                }
                break;
            case '2':
                if ($objetProveedor->recipiente == 3) {
                    $tabla = 'almacen_organico';
                    $encabezado = 'almacenencabezado_organico';
                } else {
                    $tabla = 'cubetasdetalle_organico';
                    $encabezado = 'cubetasencabezado_organico';
                }
                break;
        }


        if ($objetProveedor->todos == 2) {

            $sqlPorProveedor = "SELECT am.idAlmacen, am.fecha, pr.nombre, loca.localidad, pr.idSagarpa, al.zona,
                                   al.idAlmacen as tambor, al.pesoLista, al.bruto, al.tara, al.neto, al.diferencia
                            FROM $tabla al
                            LEFT JOIN $encabezado am ON am.idAlmacen = al.idAlmacenEncabezado
                            LEFT JOIN proveedor pr ON pr.idProveedor = am.idProveedor
                            LEFT JOIN direccion dir ON dir.idDireccion = pr.idDireccion
                            LEFT JOIN localidades loca ON loca.idlocalidad = dir.idlocalidad
                            WHERE pr.idProveedor = '$objetProveedor->idProveedor'";
        } else {

            $sqlPorProveedor = "SELECT am.idAlmacen, am.fecha, pr.nombre, loca.localidad, pr.idSagarpa, al.zona,
                                al.idAlmacen as tambor, al.pesoLista, al.bruto, al.tara, al.neto, al.diferencia
                            FROM $tabla al
                            LEFT JOIN $encabezado am ON am.idAlmacen = al.idAlmacenEncabezado
                            LEFT JOIN proveedor pr ON pr.idProveedor = am.idProveedor
                            LEFT JOIN direccion dir ON dir.idDireccion = pr.idDireccion
                            LEFT JOIN localidades loca ON loca.idlocalidad = dir.idlocalidad
                            WHERE pr.idProveedor = '$objetProveedor->idProveedor' AND am.fecha BETWEEN '$objetProveedor->fInicial' AND '$objetProveedor->fFinal' ";
        }
        return $sqlPorProveedor;
    }

    //Funcion por Todos los proveedores
    function ReportePorTodosProveedores($oProvedor)
    {
        switch ($oProvedor->tipoDeMiel) {
            case '1':
                if ($oProvedor->recipiente == 3) {
                    $tabla = 'almacen';
                    $encabezado = 'almacenencabezado';
                } else {
                    $tabla = 'cubetasdetalle';
                    $encabezado = 'cubetasencabezado';
                }
                $tambores_tabla = 'almacen';
                $tambores_encabezado = 'almacenencabezado';
                $cubetas_tabla = 'cubetasdetalle';
                $cubetasencabezado_tabla = 'cubetasencabezado';
                break;
            case '2':
                if ($oProvedor->recipiente == 3) {
                    $tabla = 'almacen_organico';
                    $encabezado = 'almacenencabezado_organico';
                } else {
                    $tabla = 'cubetasdetalle_organico';
                    $encabezado = 'cubetasencabezado_organico';
                }
                $tambores_tabla = 'almacen_organico';
                $tambores_encabezado = 'almacenencabezado_organico';
                $cubetas_tabla = 'cubetasdetalle_organico';
                $cubetasencabezado_tabla = 'cubetasencabezado_organico';
                break;
        }

        if ($oProvedor->todoProveedor == 2) {
            if ($oProvedor->ambos == 5) {

                $sqlProv = "SELECT al.idAlmacen, al.idAlmacenEncabezado, alms.fecha, al.zona, 
                         al.pesoLista, al.bruto, al.tara, al.neto, al.diferencia,
                         al.precio, al.costoTotal,prv.nombre, lc.localidad, prv.idSagarpa
                            FROM $tambores_tabla al
                            LEFT JOIN $tambores_encabezado alms ON alms.idAlmacen = al.idAlmacenEncabezado
                            LEFT JOIN proveedor prv ON prv.idProveedor = alms.idProveedor
                            LEFT JOIN direccion dir ON dir.idDireccion = prv.idDireccion
                            LEFT JOIN localidades lc ON lc.idlocalidad = dir.idlocalidad
                            UNION
                            SELECT cd.idAlmacen, cd.idAlmacenEncabezado, ce.fecha, cd.zona, 
                         cd.pesoLista, cd.bruto, cd.tara, cd.neto, cd.diferencia, 
                         cd.precio, cd.costoTotal, prs.nombre, lcc.localidad, prs.idSagarpa
                            FROM $cubetas_tabla cd
                            LEFT JOIN $cubetasencabezado_tabla ce ON ce.idAlmacen = cd.idAlmacenEncabezado
                            LEFT JOIN $tambores_encabezado alm ON alm.idAlmacen = ce.folioEntradaTambor
                            LEFT JOIN proveedor prs ON prs.idProveedor = ce.idProveedor
                            LEFT JOIN direccion dirc ON dirc.idDireccion = prs.idDireccion
                            LEFT JOIN localidades lcc ON lcc.idlocalidad = dirc.idlocalidad 
                            ORDER BY idAlmacenEncabezado ASC";
            } else {

                $sqlProv = "SELECT am.idAlmacen, am.fecha, pr.nombre, loca.localidad, pr.idSagarpa, al.zona,
                        al.idAlmacen as tambor, al.pesoLista, al.bruto, al.tara, al.neto, al.diferencia
                            FROM $tabla al
                            LEFT JOIN $encabezado am ON am.idAlmacen = al.idAlmacenEncabezado
                            LEFT JOIN proveedor pr ON pr.idProveedor = am.idProveedor
                             LEFT JOIN direccion dir ON dir.idDireccion = pr.idDireccion
                            LEFT JOIN localidades loca ON loca.idlocalidad = dir.idlocalidad                         
                            ORDER BY am.idAlmacen ASC";
            }
        } elseif ($oProvedor->ambos == 5) {

            $sqlProv = "SELECT al.idAlmacen, al.idAlmacenEncabezado, alms.fecha, al.zona, 
                    al.pesoLista, al.bruto, al.tara, al.neto, al.diferencia,
                    al.precio, al.costoTotal,prv.nombre, lc.localidad, prv.idSagarpa
                    FROM $tambores_tabla al
                    LEFT JOIN $tambores_encabezado alms ON alms.idAlmacen = al.idAlmacenEncabezado
                    LEFT JOIN proveedor prv ON prv.idProveedor = alms.idProveedor
                    LEFT JOIN direccion dir ON dir.idDireccion = prv.idDireccion
                    LEFT JOIN localidades lc ON lc.idlocalidad = dir.idlocalidad
                    WHERE alms.fecha BETWEEN '$oProvedor->fInicial' AND '$oProvedor->fFinal'
                    UNION
                    SELECT cd.idAlmacen, cd.idAlmacenEncabezado, ce.fecha, cd.zona, 
		            cd.pesoLista, cd.bruto, cd.tara, cd.neto, cd.diferencia, 
                    cd.precio, cd.costoTotal, prs.nombre, lcc.localidad, prs.idSagarpa
                    FROM $cubetas_tabla cd
                    LEFT JOIN $cubetasencabezado_tabla ce ON ce.idAlmacen = cd.idAlmacenEncabezado
                    LEFT JOIN $tambores_encabezado alm ON alm.idAlmacen = ce.folioEntradaTambor
                    LEFT JOIN proveedor prs ON prs.idProveedor = ce.idProveedor
                    LEFT JOIN direccion dirc ON dirc.idDireccion = prs.idDireccion
                    LEFT JOIN localidades lcc ON lcc.idlocalidad = dirc.idlocalidad
                    WHERE  ce.fecha BETWEEN '$oProvedor->fInicial' AND '$oProvedor->fFinal' ORDER BY idAlmacenEncabezado ASC";
        } else {
            $sqlProv = "SELECT am.idAlmacen, am.fecha, pr.nombre, loca.localidad, pr.idSagarpa, al.zona,
                        al.idAlmacen as tambor, al.pesoLista, al.bruto, al.tara, al.neto, al.diferencia
                            FROM $tabla al
                            LEFT JOIN $encabezado am ON am.idAlmacen = al.idAlmacenEncabezado
                            LEFT JOIN proveedor pr ON pr.idProveedor = am.idProveedor
                             LEFT JOIN direccion dir ON dir.idDireccion = pr.idDireccion
                            LEFT JOIN localidades loca ON loca.idlocalidad = dir.idlocalidad
                            WHERE am.fecha BETWEEN '$oProvedor->fInicial' AND '$oProvedor->fFinal'  
                            ORDER BY am.idAlmacen ASC";
        }

        return $sqlProv;
    }

    //Funcion por Tambores y Cubetas por todos los proveedores
    function ambosPorProveedor($objeAmbosProveedor)
    {
        switch ($objeAmbosProveedor->tipoDeMiel) {
            case '1':
                $almacen_tabla = 'almacen';
                $almacenencabezado_tabla = 'almacenencabezado';
                $cubetasdetalle_tabla = 'cubetasdetalle';
                $cubetasencabezado_tabla = 'cubetasencabezado';
                break;
            case '2':
                $almacen_tabla = 'almacen_organico';
                $almacenencabezado_tabla = 'almacenencabezado_organico';
                $cubetasdetalle_tabla = 'cubetasdetalle_organico';
                $cubetasencabezado_tabla = 'cubetasencabezado_organico';
                break;
        }
        if ($objeAmbosProveedor->todo == 2) {
            $sqlAmbos = "SELECT al.idAlmacen, al.idAlmacenEncabezado, alms.fecha, al.zona, al.trazabilidad, 
                    al.pesoLista, al.bruto, al.tara, al.neto, al.diferencia,
                    al.precio, al.costoTotal,prv.nombre, lc.localidad, prv.idSagarpa
                    FROM $almacen_tabla al
                    LEFT JOIN $almacenencabezado_tabla alms ON alms.idAlmacen = al.idAlmacenEncabezado
                    LEFT JOIN proveedor prv ON prv.idProveedor = alms.idProveedor
                    LEFT JOIN direccion dir ON dir.idDireccion = prv.idDireccion
                    LEFT JOIN localidades lc ON lc.idlocalidad = dir.idlocalidad
                    WHERE prv.idProveedor = '$objeAmbosProveedor->idProveedor' 
                    UNION
                    SELECT cd.idAlmacen, cd.idAlmacenEncabezado, ce.fecha, cd.zona, cd.trazabilidad, 
                    cd.pesoLista, cd.bruto, cd.tara, cd.neto, cd.diferencia, 
                    cd.precio, cd.costoTotal, prs.nombre, lcc.localidad, prs.idSagarpa
                    FROM $cubetasdetalle_tabla cd
                    LEFT JOIN $cubetasencabezado_tabla ce ON ce.idAlmacen = cd.idAlmacenEncabezado
                    LEFT JOIN $almacenencabezado_tabla alm ON alm.idAlmacen = ce.folioEntradaTambor
                    LEFT JOIN proveedor prs ON prs.idProveedor = ce.idProveedor
                    LEFT JOIN direccion dirc ON dirc.idDireccion = prs.idDireccion
                    LEFT JOIN localidades lcc ON lcc.idlocalidad = dirc.idlocalidad
                    WHERE prs.idProveedor = '$objeAmbosProveedor->idProveedor'";
        } else {
            $sqlAmbos = "SELECT al.idAlmacen, al.idAlmacenEncabezado, alms.fecha, al.zona, al.trazabilidad, 
                    al.pesoLista, al.bruto, al.tara, al.neto, al.diferencia,
                    al.precio, al.costoTotal,prv.nombre, lc.localidad, prv.idSagarpa
                    FROM $almacen_tabla al
                    LEFT JOIN $almacenencabezado_tabla alms ON alms.idAlmacen = al.idAlmacenEncabezado
                    LEFT JOIN proveedor prv ON prv.idProveedor = alms.idProveedor
                    LEFT JOIN direccion dir ON dir.idDireccion = prv.idDireccion
                    LEFT JOIN localidades lc ON lc.idlocalidad = dir.idlocalidad
                    WHERE prv.idProveedor = '$objeAmbosProveedor->idProveedor' AND alms.fecha BETWEEN '$objeAmbosProveedor->fInicial'AND '$objeAmbosProveedor->fFinal'
                    UNION
                    SELECT cd.idAlmacen, cd.idAlmacenEncabezado, ce.fecha, cd.zona, cd.trazabilidad, 
                    cd.pesoLista, cd.bruto, cd.tara, cd.neto, cd.diferencia, 
                    cd.precio, cd.costoTotal, prs.nombre, lcc.localidad, prs.idSagarpa
                    FROM $cubetasdetalle_tabla cd
                    LEFT JOIN $cubetasencabezado_tabla ce ON ce.idAlmacen = cd.idAlmacenEncabezado
                    LEFT JOIN $almacenencabezado_tabla alm ON alm.idAlmacen = ce.folioEntradaTambor
                    LEFT JOIN proveedor prs ON prs.idProveedor = ce.idProveedor
                    LEFT JOIN direccion dirc ON dirc.idDireccion = prs.idDireccion
                    LEFT JOIN localidades lcc ON lcc.idlocalidad = dirc.idlocalidad
                    WHERE prs.idProveedor = '$objeAmbosProveedor->idProveedor' AND ce.fecha BETWEEN '$objeAmbosProveedor->fInicial'AND '$objeAmbosProveedor->fFinal'";
        }

        return $sqlAmbos;
    }

    //Funcion de Reportes por zonas
    function ReportePorZona($objeZona)
    {
        switch ($objeZona->tipoDeMiel) {
            case '1':
                if ($objeZona->recipiente == 3) {
                    $tabla = 'almacen';
                    $encabezado = 'almacenencabezado';
                } else {
                    $tabla = 'cubetasdetalle';
                    $encabezado = 'cubetasencabezado';
                }
                break;
            case '2':
                if ($objeZona->recipiente == 3) {
                    $tabla = 'almacen_organico';
                    $encabezado = 'almacenencabezado_organico';
                } else {
                    $tabla = 'cubetasdetalle_organico';
                    $encabezado = 'cubetasencabezado_organico';
                }
                break;
        }

        if ($objeZona->todoZona == 2) {

            $sqlZona = "SELECT am.idAlmacen, am.fecha, pr.nombre, loca.localidad, pr.idSagarpa, al.zona, al.idAlmacen as tambor, 
                    al.pesoLista, al.bruto, al.tara, al.neto, al.diferencia, zn.zona as zonaCom FROM $tabla al
                    LEFT JOIN $encabezado am ON am.idAlmacen = al.idAlmacenEncabezado
                    LEFT JOIN proveedor pr ON pr.idProveedor = am.idProveedor
                    LEFT JOIN direccion dir ON dir.idDireccion = pr.idDireccion
                    LEFT JOIN localidades loca ON loca.idlocalidad = dir.idlocalidad
                    LEFT JOIN zonas zn   ON zn.idzona = loca.idzona
                    WHERE zn.idzona ='$objeZona->idZona'";
        } else {
            $sqlZona = "SELECT am.idAlmacen, am.fecha, pr.nombre, loca.localidad, pr.idSagarpa, al.zona, al.idAlmacen as tambor, 
                    al.pesoLista, al.bruto, al.tara, al.neto, al.diferencia, zn.zona as zonaCom FROM $tabla al
                    LEFT JOIN $encabezado am ON am.idAlmacen = al.idAlmacenEncabezado
                    LEFT JOIN proveedor pr ON pr.idProveedor = am.idProveedor
                    LEFT JOIN direccion dir ON dir.idDireccion = pr.idDireccion
                    LEFT JOIN localidades loca ON loca.idlocalidad = dir.idlocalidad
                    LEFT JOIN zonas zn   ON zn.idzona = loca.idzona
                    WHERE zn.idzona ='$objeZona->idZona' AND am.fecha BETWEEN '$objeZona->fInicial' AND '$objeZona->fFinal' ORDER BY zn.zona ASC";
        }
        return $sqlZona;
    }

    //Funcion por Todas las Zonas
    function ReporteporTodasZonas($oZona)
    {
        switch ($oZona->tipoDeMiel) {
            case '1':
                if ($oZona->recipiente == 3) {
                    $tabla = 'almacen';
                    $encabezado = 'almacenencabezado';
                } else {
                    $tabla = 'cubetasdetalle';
                    $encabezado = 'cubetasencabezado';
                }
                $tambores_tabla = 'almacen';
                $tambores_encabezado = 'almacenencabezado';
                $cubetas_tabla = 'cubetasdetalle';
                $cubetasencabezado_tabla = 'cubetasencabezado';
                break;
            case '2':
                if ($oZona->recipiente == 3) {
                    $tabla = 'almacen_organico';
                    $encabezado = 'almacenencabezado_organico';
                } else {
                    $tabla = 'cubetasdetalle_organico';
                    $encabezado = 'cubetasencabezado_organico';
                }
                $tambores_tabla = 'almacen_organico';
                $tambores_encabezado = 'almacenencabezado_organico';
                $cubetas_tabla = 'cubetasdetalle_organico';
                $cubetasencabezado_tabla = 'cubetasencabezado_organico';
                break;
        }
        if ($oZona->todaZona == 2) {
            if ($oZona->ambos == 5) {
                $sqlZon = "SELECT al.idAlmacen, al.idAlmacenEncabezado, alms.fecha, al.zona, al.trazabilidad, 
                    al.pesoLista, al.bruto, al.tara, al.neto, al.diferencia, al.humedad, al.autorizado,
                    al.precio, al.costoTotal,prv.nombre, lc.localidad, prv.idSagarpa, z.zona as zonaCam
                    FROM $tambores_tabla al
                    LEFT JOIN $tambores_encabezado alms ON alms.idAlmacen = al.idAlmacenEncabezado
                    LEFT JOIN proveedor prv ON prv.idProveedor = alms.idProveedor
                    LEFT JOIN direccion dir ON dir.idDireccion = prv.idDireccion
                    LEFT JOIN localidades lc ON lc.idlocalidad = dir.idlocalidad
                    LEFT JOIN zonas  z ON z.idzona = lc.idzona
                    UNION
                    SELECT cd.idAlmacen, cd.idAlmacenEncabezado, ce.fecha, cd.zona, cd.trazabilidad, 
                    cd.pesoLista, cd.bruto, cd.tara, cd.neto, cd.diferencia, cd.humedad, cd.autorizado, 
                    cd.precio, cd.costoTotal, prs.nombre, lcc.localidad, prs.idSagarpa, zc.zona as zonaCam
                    FROM $cubetas_tabla cd
                    LEFT JOIN $cubetasencabezado_tabla ce ON ce.idAlmacen = cd.idAlmacenEncabezado
                    LEFT JOIN $tambores_encabezado alm ON alm.idAlmacen = ce.folioEntradaTambor
                    LEFT JOIN proveedor prs ON prs.idProveedor = ce.idProveedor
                    LEFT JOIN direccion dirc ON dirc.idDireccion = prs.idDireccion
                    LEFT JOIN localidades lcc ON lcc.idlocalidad = dirc.idlocalidad
                    LEFT JOIN zonas zc ON zc.idzona =lcc.idzona
                    ORDER BY zonaCam, idAlmacen ASC";
            } else {
                $sqlZon = "SELECT am.idAlmacen, am.fecha, pr.nombre, loca.localidad, pr.idSagarpa, al.zona, al.idAlmacen as tambor, 
                    al.pesoLista, al.bruto, al.tara, al.neto, al.diferencia, zn.zona as zonaCom FROM $tabla al
                    LEFT JOIN $encabezado am ON am.idAlmacen = al.idAlmacenEncabezado
                    LEFT JOIN proveedor pr ON pr.idProveedor = am.idProveedor
                    LEFT JOIN direccion dir ON dir.idDireccion = pr.idDireccion
                    LEFT JOIN localidades loca ON loca.idlocalidad = dir.idlocalidad
                    LEFT JOIN zonas zn   ON zn.idzona = loca.idzona
                    ORDER BY zonaCom, am.idAlmacen ASC";
            }
        } elseif ($oZona->ambos == 5) {
            $sqlZon = "SELECT al.idAlmacen, al.idAlmacenEncabezado, alms.fecha, al.zona, al.trazabilidad, 
                al.pesoLista, al.bruto, al.tara, al.neto, al.diferencia, al.humedad, al.autorizado,
                al.precio, al.costoTotal,prv.nombre, lc.localidad, prv.idSagarpa, z.zona as zonaCam
                FROM $tambores_tabla al
                LEFT JOIN $tambores_encabezado alms ON alms.idAlmacen = al.idAlmacenEncabezado
                LEFT JOIN proveedor prv ON prv.idProveedor = alms.idProveedor
                LEFT JOIN direccion dir ON dir.idDireccion = prv.idDireccion
                LEFT JOIN localidades lc ON lc.idlocalidad = dir.idlocalidad
                LEFT JOIN zonas  z ON z.idzona = lc.idzona
                WHERE alms.fecha  BETWEEN '$oZona->fInicial' AND '$oZona->fFinal'
                UNION
                SELECT cd.idAlmacen, cd.idAlmacenEncabezado, ce.fecha, cd.zona, cd.trazabilidad, 
                cd.pesoLista, cd.bruto, cd.tara, cd.neto, cd.diferencia, cd.humedad, cd.autorizado, 
                cd.precio, cd.costoTotal, prs.nombre, lcc.localidad, prs.idSagarpa, zc.zona as zonaCam
                FROM $cubetas_tabla cd
                LEFT JOIN $cubetasencabezado_tabla ce ON ce.idAlmacen = cd.idAlmacenEncabezado
                LEFT JOIN $tambores_encabezado alm ON alm.idAlmacen = ce.folioEntradaTambor
                LEFT JOIN proveedor prs ON prs.idProveedor = ce.idProveedor
                LEFT JOIN direccion dirc ON dirc.idDireccion = prs.idDireccion
                LEFT JOIN localidades lcc ON lcc.idlocalidad = dirc.idlocalidad
                LEFT JOIN zonas zc ON zc.idzona =lcc.idzona
                WHERE ce.fecha  BETWEEN '$oZona->fInicial' AND '$oZona->fFinal' ORDER BY zonaCam, idAlmacen ASC";
        } else {
            $sqlZon = "SELECT am.idAlmacen, am.fecha, pr.nombre, loca.localidad, pr.idSagarpa, al.zona, al.idAlmacen as tambor, 
                    al.pesoLista, al.bruto, al.tara, al.neto, al.diferencia, zn.zona as zonaCom FROM $tabla al
                    LEFT JOIN $encabezado am ON am.idAlmacen = al.idAlmacenEncabezado
                    LEFT JOIN proveedor pr ON pr.idProveedor = am.idProveedor
                    LEFT JOIN direccion dir ON dir.idDireccion = pr.idDireccion
                    LEFT JOIN localidades loca ON loca.idlocalidad = dir.idlocalidad
                    LEFT JOIN zonas zn   ON zn.idzona = loca.idzona
                    WHERE am.fecha BETWEEN '$oZona->fInicial' AND '$oZona->fFinal'
                    ORDER BY  zonaCom ASC, am.idAlmacen ASC   ";
        }
        return $sqlZon;
    }

    function ReporteAmbosporZonas($objZona)
    {

        switch ($objZona->tipoDeMiel) {
            case '1':
                $almacen_tabla = 'almacen';
                $almacenencabezado_tabla = 'almacenencabezado';
                $cubetasdetalle_tabla = 'cubetasdetalle';
                $cubetasencabezado_tabla = 'cubetasencabezado';
                break;
            case '2':
                $almacen_tabla = 'almacen_organico';
                $almacenencabezado_tabla = 'almacenencabezado_organico';
                $cubetasdetalle_tabla = 'cubetasdetalle_organico';
                $cubetasencabezado_tabla = 'cubetasencabezado_organico';
                break;
        }

        if ($objZona->todo == 2) {

            $sqlAmbosZona = "SELECT al.idAlmacen, al.idAlmacenEncabezado, alms.fecha, al.zona, al.trazabilidad, 
                al.pesoLista, al.bruto, al.tara, al.neto, al.diferencia, al.humedad, al.autorizado,
                al.precio, al.costoTotal,prv.nombre, lc.localidad, prv.idSagarpa, z.zona as zonaCam
                FROM $almacen_tabla al
                LEFT JOIN $almacenencabezado_tabla alms ON alms.idAlmacen = al.idAlmacenEncabezado
                LEFT JOIN proveedor prv ON prv.idProveedor = alms.idProveedor
                LEFT JOIN direccion dir ON dir.idDireccion = prv.idDireccion
                LEFT JOIN localidades lc ON lc.idlocalidad = dir.idlocalidad
                LEFT JOIN zonas  z ON z.idzona = lc.idzona
                WHERE z.idzona ='$objZona->idZona'
                UNION
                SELECT cd.idAlmacen, cd.idAlmacenEncabezado, ce.fecha, cd.zona, cd.trazabilidad, 
                cd.pesoLista, cd.bruto, cd.tara, cd.neto, cd.diferencia, cd.humedad, cd.autorizado, 
                cd.precio, cd.costoTotal, prs.nombre, lcc.localidad, prs.idSagarpa, zc.zona as zonaCam
                FROM $cubetasdetalle_tabla cd
                LEFT JOIN $cubetasencabezado_tabla ce ON ce.idAlmacen = cd.idAlmacenEncabezado
                LEFT JOIN $almacenencabezado_tabla alm ON alm.idAlmacen = ce.folioEntradaTambor
                LEFT JOIN proveedor prs ON prs.idProveedor = ce.idProveedor
                LEFT JOIN direccion dirc ON dirc.idDireccion = prs.idDireccion
                LEFT JOIN localidades lcc ON lcc.idlocalidad = dirc.idlocalidad
                LEFT JOIN zonas zc ON zc.idzona =lcc.idzona
                WHERE zc.idzona ='$objZona->idZona'";
        } else {

            $sqlAmbosZona = "SELECT al.idAlmacen, al.idAlmacenEncabezado, alms.fecha, al.zona, al.trazabilidad, 
                al.pesoLista, al.bruto, al.tara, al.neto, al.diferencia, al.humedad, al.autorizado,
                al.precio, al.costoTotal,prv.nombre, lc.localidad, prv.idSagarpa, z.zona as zonaCam
                FROM $almacen_tabla al
                LEFT JOIN $almacenencabezado_tabla alms ON alms.idAlmacen = al.idAlmacenEncabezado
                LEFT JOIN proveedor prv ON prv.idProveedor = alms.idProveedor
                LEFT JOIN direccion dir ON dir.idDireccion = prv.idDireccion
                LEFT JOIN localidades lc ON lc.idlocalidad = dir.idlocalidad
                LEFT JOIN zonas  z ON z.idzona = lc.idzona
                WHERE z.idzona ='$objZona->idZona'  AND alms.fecha BETWEEN '$objZona->fInicial'AND '$objZona->fFinal'
                UNION
                SELECT cd.idAlmacen, cd.idAlmacenEncabezado, ce.fecha, cd.zona, cd.trazabilidad, 
                cd.pesoLista, cd.bruto, cd.tara, cd.neto, cd.diferencia, cd.humedad, cd.autorizado, 
                cd.precio, cd.costoTotal, prs.nombre, lcc.localidad, prs.idSagarpa, zc.zona as zonaCam
                FROM $cubetasdetalle_tabla cd
                LEFT JOIN $cubetasencabezado_tabla ce ON ce.idAlmacen = cd.idAlmacenEncabezado
                LEFT JOIN $almacenencabezado_tabla alm ON alm.idAlmacen = ce.folioEntradaTambor
                LEFT JOIN proveedor prs ON prs.idProveedor = ce.idProveedor
                LEFT JOIN direccion dirc ON dirc.idDireccion = prs.idDireccion
                LEFT JOIN localidades lcc ON lcc.idlocalidad = dirc.idlocalidad
                LEFT JOIN zonas zc ON zc.idzona =lcc.idzona
                WHERE zc.idzona ='$objZona->idZona' AND ce.fecha BETWEEN '$objZona->fInicial'AND '$objZona->fFinal'";
        }

        return $sqlAmbosZona;
    }

    function reporteLocalidad($oLocalidad)
    {

        switch ($oLocalidad->tipoDeMiel) {
            case '1':
                if ($oLocalidad->recipiente == 3) {
                    $tabla = 'almacen';
                    $encabezado = 'almacenencabezado';
                } else {
                    $tabla = 'cubetasdetalle';
                    $encabezado = 'cubetasencabezado';
                }
                $tambores_tabla = 'almacen';
                $tambores_encabezado = 'almacenencabezado';
                $cubetas_tabla = 'cubetasdetalle';
                $cubetasencabezado_tabla = 'cubetasencabezado';
                break;
            case '2':
                if ($oLocalidad->recipiente == 3) {
                    $tabla = 'almacen_organico';
                    $encabezado = 'almacenencabezado_organico';
                } else {
                    $tabla = 'cubetasdetalle_organico';
                    $encabezado = 'cubetasencabezado_organico';
                }
                $tambores_tabla = 'almacen_organico';
                $tambores_encabezado = 'almacenencabezado_organico';
                $cubetas_tabla = 'cubetasdetalle_organico';
                $cubetasencabezado_tabla = 'cubetasencabezado_organico';
                break;
        }

        if ($oLocalidad->todasLocalidades == 2) {

            if ($oLocalidad->ambos == 5) {
                $sqlLocalidad = "SELECT al.idAlmacen, al.idAlmacenEncabezado, alms.fecha, al.zona, al.trazabilidad, 
                    al.pesoLista, al.bruto, al.tara, al.neto, al.diferencia, al.humedad, al.autorizado,
                    al.precio, al.costoTotal,prv.nombre, lc.localidad, prv.idSagarpa, z.zona as zonaCam
                    FROM $tambores_tabla al
                    LEFT JOIN $tambores_encabezado alms ON alms.idAlmacen = al.idAlmacenEncabezado
                    LEFT JOIN proveedor prv ON prv.idProveedor = alms.idProveedor
                    LEFT JOIN direccion dir ON dir.idDireccion = prv.idDireccion
                    LEFT JOIN localidades lc ON lc.idlocalidad = dir.idlocalidad
                    LEFT JOIN zonas  z ON z.idzona = lc.idzona
                    WHERE lc.idLocalidad ='$oLocalidad->idLocalidad'
                    UNION
                    SELECT cd.idAlmacen, cd.idAlmacenEncabezado, ce.fecha, cd.zona, cd.trazabilidad, 
                    cd.pesoLista, cd.bruto, cd.tara, cd.neto, cd.diferencia, cd.humedad, cd.autorizado, 
                    cd.precio, cd.costoTotal, prs.nombre, lcc.localidad, prs.idSagarpa, zc.zona as zonaCam
                    FROM $cubetas_tabla cd
                    LEFT JOIN $cubetasencabezado_tabla ce ON ce.idAlmacen = cd.idAlmacenEncabezado
                    LEFT JOIN $tambores_encabezado alm ON alm.idAlmacen = ce.folioEntradaTambor
                    LEFT JOIN proveedor prs ON prs.idProveedor = ce.idProveedor
                    LEFT JOIN direccion dirc ON dirc.idDireccion = prs.idDireccion
                    LEFT JOIN localidades lcc ON lcc.idlocalidad = dirc.idlocalidad
                    LEFT JOIN zonas zc ON zc.idzona =lcc.idzona
                    WHERE lcc.idLocalidad ='$oLocalidad->idLocalidad'";
            } else {
                //Todos-localidad
                $sqlLocalidad = "SELECT am.idAlmacen, am.fecha, pr.nombre, loca.localidad, pr.idSagarpa, al.zona, al.idAlmacen as tambor,   
                        al.pesoLista, al.bruto, al.tara, al.neto, al.diferencia, zn.zona as zonaCom FROM $tabla al
                        LEFT JOIN $encabezado am ON am.idAlmacen = al.idAlmacenEncabezado
                        LEFT JOIN proveedor pr ON pr.idProveedor = am.idProveedor
                        LEFT JOIN direccion dir ON dir.idDireccion = pr.idDireccion
                        LEFT JOIN localidades loca ON loca.idlocalidad = dir.idlocalidad
                        LEFT JOIN zonas zn   ON zn.idzona = loca.idzona
                        WHERE loca.idLocalidad ='$oLocalidad->idLocalidad'";
            }
        } elseif ($oLocalidad->ambos == 5) {

            // Periodo-Tambores-Cubetas-Localidad
            $sqlLocalidad = "SELECT al.idAlmacen, al.idAlmacenEncabezado, alms.fecha, al.zona, al.trazabilidad, 
                    al.pesoLista, al.bruto, al.tara, al.neto, al.diferencia, al.humedad, al.autorizado,
                    al.precio, al.costoTotal,prv.nombre, lc.localidad, prv.idSagarpa, z.zona as zonaCam
                    FROM $tambores_tabla al
                    LEFT JOIN $tambores_encabezado alms ON alms.idAlmacen = al.idAlmacenEncabezado
                    LEFT JOIN proveedor prv ON prv.idProveedor = alms.idProveedor
                    LEFT JOIN direccion dir ON dir.idDireccion = prv.idDireccion
                    LEFT JOIN localidades lc ON lc.idlocalidad = dir.idlocalidad
                    LEFT JOIN zonas  z ON z.idzona = lc.idzona
                    WHERE lc.idLocalidad ='$oLocalidad->idLocalidad' AND alms.fecha BETWEEN '$oLocalidad->fInicial' AND '$oLocalidad->fFinal'
                    UNION
                    SELECT cd.idAlmacen, cd.idAlmacenEncabezado, ce.fecha, cd.zona, cd.trazabilidad, 
                    cd.pesoLista, cd.bruto, cd.tara, cd.neto, cd.diferencia, cd.humedad, cd.autorizado, 
                    cd.precio, cd.costoTotal, prs.nombre, lcc.localidad, prs.idSagarpa, zc.zona as zonaCam
                    FROM $cubetas_tabla cd
                    LEFT JOIN $cubetasencabezado_tabla ce ON ce.idAlmacen = cd.idAlmacenEncabezado
                    LEFT JOIN $tambores_encabezado alm ON alm.idAlmacen = ce.folioEntradaTambor
                    LEFT JOIN proveedor prs ON prs.idProveedor = ce.idProveedor
                    LEFT JOIN direccion dirc ON dirc.idDireccion = prs.idDireccion
                    LEFT JOIN localidades lcc ON lcc.idlocalidad = dirc.idlocalidad
                    LEFT JOIN zonas zc ON zc.idzona =lcc.idzona
                    WHERE lcc.idlocalidad ='$oLocalidad->idLocalidad' AND ce.fecha BETWEEN '$oLocalidad->fInicial' AND '$oLocalidad->fFinal' ";
        } else {

            $sqlLocalidad = "SELECT am.idAlmacen, am.fecha, pr.nombre, loca.localidad, pr.idSagarpa, al.zona, al.idAlmacen as tambor, 
                        al.pesoLista, al.bruto, al.tara, al.neto, al.diferencia, zn.zona as zonaCom FROM $tabla al
                        LEFT JOIN $encabezado am ON am.idAlmacen = al.idAlmacenEncabezado
                        LEFT JOIN proveedor pr ON pr.idProveedor = am.idProveedor
                        LEFT JOIN direccion dir ON dir.idDireccion = pr.idDireccion
                        LEFT JOIN localidades loca ON loca.idlocalidad = dir.idlocalidad
                        LEFT JOIN zonas zn   ON zn.idzona = loca.idzona
                        WHERE loca.idLocalidad ='$oLocalidad->idLocalidad' AND am.fecha BETWEEN '$oLocalidad->fInicial' AND '$oLocalidad->fFinal'";
        }

        return $sqlLocalidad;
    }

    function reportePorLocalidades($oLocalidad1)
    {
        switch ($oLocalidad1->tipoDeMiel) {
            case '1':
                if ($oLocalidad1->recipiente == 3) {
                    $tabla = 'almacen';
                    $encabezado = 'almacenencabezado';
                } else {
                    $tabla = 'cubetasdetalle';
                    $encabezado = 'cubetasencabezado';
                }
                $tambores_tabla = 'almacen';
                $tambores_encabezado = 'almacenencabezado';
                $cubetas_tabla = 'cubetasdetalle';
                $cubetasencabezado_tabla = 'cubetasencabezado';
                break;
            case '2':
                if ($oLocalidad1->recipiente == 3) {
                    $tabla = 'almacen_organico';
                    $encabezado = 'almacenencabezado_organico';
                } else {
                    $tabla = 'cubetasdetalle_organico';
                    $encabezado = 'cubetasencabezado_organico';
                }
                $tambores_tabla = 'almacen_organico';
                $tambores_encabezado = 'almacenencabezado_organico';
                $cubetas_tabla = 'cubetasdetalle_organico';
                $cubetasencabezado_tabla = 'cubetasencabezado_organico';
                break;
        }

        if ($oLocalidad1->todasLocalidades == 2) {
            if ($oLocalidad1->ambos == 5) {
                $sqlLoca1 = "SELECT al.idAlmacen, al.idAlmacenEncabezado, alms.fecha, al.zona, al.trazabilidad, 
                    al.pesoLista, al.bruto, al.tara, al.neto, al.diferencia, al.humedad, al.autorizado,
                    al.precio, al.costoTotal,prv.nombre, lc.localidad, prv.idSagarpa, z.zona as zonaCam
                    FROM $tambores_tabla al
                    LEFT JOIN $tambores_encabezado alms ON alms.idAlmacen = al.idAlmacenEncabezado
                    LEFT JOIN proveedor prv ON prv.idProveedor = alms.idProveedor
                    LEFT JOIN direccion dir ON dir.idDireccion = prv.idDireccion
                    LEFT JOIN localidades lc ON lc.idlocalidad = dir.idlocalidad
                    LEFT JOIN zonas  z ON z.idzona = lc.idzona
                    UNION
                    SELECT cd.idAlmacen, cd.idAlmacenEncabezado, ce.fecha, cd.zona, cd.trazabilidad, 
                    cd.pesoLista, cd.bruto, cd.tara, cd.neto, cd.diferencia, cd.humedad, cd.autorizado, 
                    cd.precio, cd.costoTotal, prs.nombre, lcc.localidad, prs.idSagarpa, zc.zona as zonaCam
                    FROM $cubetas_tabla cd
                    LEFT JOIN $cubetasencabezado_tabla ce ON ce.idAlmacen = cd.idAlmacenEncabezado
                    LEFT JOIN $tambores_encabezado alm ON alm.idAlmacen = ce.folioEntradaTambor
                    LEFT JOIN proveedor prs ON prs.idProveedor = ce.idProveedor
                    LEFT JOIN direccion dirc ON dirc.idDireccion = prs.idDireccion
                    LEFT JOIN localidades lcc ON lcc.idlocalidad = dirc.idlocalidad
                    LEFT JOIN zonas zc ON zc.idzona =lcc.idzona
                    ORDER BY localidad ASC, idAlmacen ASC";
            } else {
                $sqlLoca1 = "SELECT am.idAlmacen, am.fecha, pr.nombre, loca.localidad, pr.idSagarpa, al.zona, al.idAlmacen as tambor, 
                        al.pesoLista, al.bruto, al.tara, al.neto, al.diferencia, zn.zona as zonaCom FROM $tabla al
                        LEFT JOIN $encabezado am ON am.idAlmacen = al.idAlmacenEncabezado
                        LEFT JOIN proveedor pr ON pr.idProveedor = am.idProveedor
                        LEFT JOIN direccion dir ON dir.idDireccion = pr.idDireccion
                        LEFT JOIN localidades loca ON loca.idlocalidad = dir.idlocalidad
                        LEFT JOIN zonas zn   ON zn.idzona = loca.idzona ORDER BY  loca.localidad ASC, am.idAlmacen ASC";
            }
        } elseif ($oLocalidad1->ambos == 5) {

            $sqlLoca1 = "SELECT al.idAlmacen, al.idAlmacenEncabezado, alms.fecha, al.zona, al.trazabilidad, 
                    al.pesoLista, al.bruto, al.tara, al.neto, al.diferencia, al.humedad, al.autorizado,
                    al.precio, al.costoTotal,prv.nombre, lc.localidad, prv.idSagarpa, z.zona as zonaCam
                    FROM $tambores_tabla al
                    LEFT JOIN $tambores_encabezado alms ON alms.idAlmacen = al.idAlmacenEncabezado
                    LEFT JOIN proveedor prv ON prv.idProveedor = alms.idProveedor
                    LEFT JOIN direccion dir ON dir.idDireccion = prv.idDireccion
                    LEFT JOIN localidades lc ON lc.idlocalidad = dir.idlocalidad
                    LEFT JOIN zonas  z ON z.idzona = lc.idzona
                    WHERE alms.fecha BETWEEN '$oLocalidad1->fInicial' AND '$oLocalidad1->fFinal'
                    UNION
                    SELECT cd.idAlmacen, cd.idAlmacenEncabezado, ce.fecha, cd.zona, cd.trazabilidad, 
                    cd.pesoLista, cd.bruto, cd.tara, cd.neto, cd.diferencia, cd.humedad, cd.autorizado, 
                    cd.precio, cd.costoTotal, prs.nombre, lcc.localidad, prs.idSagarpa, zc.zona as zonaCam
                    FROM $cubetas_tabla cd
                    LEFT JOIN $cubetasencabezado_tabla ce ON ce.idAlmacen = cd.idAlmacenEncabezado
                    LEFT JOIN $tambores_encabezado alm ON alm.idAlmacen = ce.folioEntradaTambor
                    LEFT JOIN proveedor prs ON prs.idProveedor = ce.idProveedor
                    LEFT JOIN direccion dirc ON dirc.idDireccion = prs.idDireccion
                    LEFT JOIN localidades lcc ON lcc.idlocalidad = dirc.idlocalidad
                    LEFT JOIN zonas zc ON zc.idzona =lcc.idzona
                    WHERE ce.fecha BETWEEN '$oLocalidad1->fInicial' AND '$oLocalidad1->fFinal' ORDER BY localidad ASC, idAlmacen ASC";
        } else {

            $sqlLoca1 = "SELECT am.idAlmacen, am.fecha, pr.nombre, loca.localidad, pr.idSagarpa, al.zona, al.idAlmacen as tambor, 
                        al.pesoLista, al.bruto, al.tara, al.neto, al.diferencia, zn.zona as zonaCom FROM $tabla al
                        LEFT JOIN $encabezado am ON am.idAlmacen = al.idAlmacenEncabezado
                        LEFT JOIN proveedor pr ON pr.idProveedor = am.idProveedor
                        LEFT JOIN direccion dir ON dir.idDireccion = pr.idDireccion
                        LEFT JOIN localidades loca ON loca.idlocalidad = dir.idlocalidad
                        LEFT JOIN zonas zn   ON zn.idzona = loca.idzona
                        WHERE am.fecha BETWEEN '$oLocalidad1->fInicial' AND '$oLocalidad1->fFinal' ORDER BY loca.localidad ASC,am.idAlmacen ASC ";
        }

        return $sqlLoca1;
    }
}

class lote
{

    function EncabeLote($idLoteInterno, $tmp)
    {
        switch ($tmp) {
            case 1:
                $calidad = 'calidad';
                $experimental = 'experimental';
                $tambores = 'tamboreslotes';
                $laboratorio = 'laboratorio';
                $entradaSalida = 'entradaysalida';
                $lotesContratados = 'lotescontratados';
                break;
            case 2:
                $calidad = 'calidad_organico';
                $experimental = 'experimental_organico';
                $tambores = 'tamboreslotes_organico';
                $laboratorio = 'laboratorio_organico';
                $entradaSalida = 'entradaysalida_organico';
                $lotesContratados = 'lotescontratados_organico';
                break;
            case 5:
                $calidad = 'calidad_mantequilla';
                $experimental = 'experimental_mantequilla';
                $tambores = 'tamboreslotes_mantequilla';
                $laboratorio = 'laboratorio_mantequilla';
                $entradaSalida = 'entradaysalida_mantequilla';
                $lotesContratados = 'lotescontratados_mantequilla';
                break;
            case 6:
                $calidad = 'calidad_altiplano';
                $experimental = 'experimental_altiplano';
                $tambores = 'tamboreslotes_altiplano';
                $laboratorio = 'laboratorio_altiplano';
                $entradaSalida = 'entradaysalida_altiplano';
                $lotesContratados = 'lotescontratados_altiplano';
                break;
            case 7:
                $calidad = 'calidad_naranjo';
                $experimental = 'experimental_naranjo';
                $tambores = 'tamboreslotes_naranjo';
                $laboratorio = 'laboratorio_naranjo';
                $entradaSalida = 'entradaysalida_naranjo';
                $lotesContratados = 'lotescontratados_naranjo';
                break;
                
            case 8:
                $calidad = 'calidad_aguacate';
                $experimental = 'experimental_aguacate';
                $tambores = 'tamboreslotes_aguacate';
                $laboratorio = 'laboratorio_aguacate';
                $entradaSalida = 'entradaysalida_aguacate';
                $lotesContratados = 'lotescontratados_aguacate';
                break;
            case 9:
                $calidad = 'calidad_mezquite';
                $experimental = 'experimental_mezquite';
                $tambores = 'tamboreslotes_mezquite';
                $laboratorio = 'laboratorio_mezquite';
                $entradaSalida = 'entradaysalida_mezquite';
                $lotesContratados = 'lotescontratados_mezquite';
                break;
        }

        $sqlEncaLote = "SELECT cal.idLoteInterno, cal.fechaProceso, cal.fechaEnvasado, cal.loteCliente,
        en.lote, cal.observaciones, cal.muestraInterna,
        cal.idLoteExperimental, cal.numeroDeTambores, cal.kilosTotales, ex.fecha, ex.contrato, ex.resultadoLaboratorio, ex.numContrato, lc.contrato AS infoContrato
        FROM $calidad AS cal
        LEFT JOIN $experimental ex  ON ex.idLoteExperimental = cal.idLoteExperimental
        LEFT JOIN $tambores tamlot ON tamlot.idLoteInterno = cal.idLoteInterno
        LEFT JOIN $laboratorio lab ON lab.idAlmacen = tamlot.folioTambor AND tamlot.tipo = 0 AND clasificacion = 0
        LEFT JOIN $entradaSalida en ON en.idLoteInterno = cal.idLoteInterno
        LEFT JOIN $lotesContratados lc ON lc.idLoteContratado = ex.numContrato
        WHERE cal.idLoteInterno  = '$idLoteInterno'
        GROUP BY idLoteInterno";
        return $sqlEncaLote;
    }

    function detalleLoteInt($idLoteInterno, $tmp)
    {

        include_once '../../DAOConeccion/conePDO.php';
        $pdo = new conePDO();
        $con = $pdo->conectar();

        switch ($tmp) {
            case 1:
                $tambores = 'tamboreslotes';
                $almacen = 'almacen';
                $almacenEncabezado = 'almacenencabezado';
                $laboratorio = 'laboratorio';
                $calidad = 'calidad';
                $traspaso = 'almacentraspaso';
                break;
            case 2:
                $tambores = 'tamboreslotes_organico';
                $almacen = 'almacen_organico';
                $almacenEncabezado = 'almacenencabezado_organico';
                $laboratorio = 'laboratorio_organico';
                $calidad = 'calidad_organico';
                $traspaso = 'almacentraspaso_organico';
                break;
            case 5:
                $tambores = 'tamboreslotes_mantequilla';
                $almacen = 'almacen_mantequilla';
                $almacenEncabezado = 'almacenencabezado_mantequilla';
                $laboratorio = 'laboratorio_mantequilla';
                $calidad = 'calidad_mantequilla';
                $traspaso = 'almacentraspaso_mantequilla';
                break;
            case 6:
                $tambores = 'tamboreslotes_altiplano';
                $almacen = 'almacen_altiplano';
                $almacenEncabezado = 'almacenencabezado_altiplano';
                $laboratorio = 'laboratorio_altiplano';
                $calidad = 'calidad_altiplano';
                $traspaso = 'almacentraspaso_altiplano';
                break;
            case 7:
                $tambores = 'tamboreslotes_naranjo';
                $almacen = 'almacen_naranjo';
                $almacenEncabezado = 'almacenencabezado_naranjo';
                $laboratorio = 'laboratorio_naranjo';
                $calidad = 'calidad_naranjo';
                $traspaso = 'almacentraspaso_naranjo';
                break;
                
            case 8:
                $tambores = 'tamboreslotes_aguacate';
                $almacen = 'almacen_aguacate';
                $almacenEncabezado = 'almacenencabezado_aguacate';
                $laboratorio = 'laboratorio_aguacate';
                $calidad = 'calidad_aguacate';
                $traspaso = 'almacentraspaso_aguacate';
                break;
            case 9:
                $tambores = 'tamboreslotes_mezquite';
                $almacen = 'almacen_mezquite';
                $almacenEncabezado = 'almacenencabezado_mezquite';
                $laboratorio = 'laboratorio_mezquite';
                $calidad = 'calidad_mezquite';
                $traspaso = 'almacentraspaso_mezquite';
                break;
        }

        $sql = "SELECT tex.* FROM $tambores tex WHERE tex.idLoteInterno = :idLoteInterno";
        $sqlDetalle = $con->prepare($sql);
        $sqlDetalle->bindParam(':idLoteInterno', $idLoteInterno);
        $sqlDetalle->execute();

        $resultado = $sqlDetalle->fetchAll(PDO::FETCH_ASSOC);

        foreach ($resultado as $index => $e) {
            if ($e['tipo'] == "0") {
                $sql2 = "SELECT tex.folioTambor, z.nombre AS zona, ale.clasificacionMiel, al.bruto, al.tara, '' AS sobrante,
                al.neto, lab.porcentaje AS humedad, lab.color
                FROM $tambores tex
                INNER JOIN $almacen al ON al.idAlmacen = tex.folioTambor
                INNER JOIN $almacenEncabezado ale ON ale.idAlmacen = al.idAlmacenEncabezado
                LEFT JOIN zonastambores z ON z.idZonaTambor = al.zona
                LEFT JOIN $laboratorio lab ON lab.idAlmacen = al.idAlmacen 
                WHERE tex.folioTambor = :folio AND tex.tipo = '0'
                ORDER BY tex.folioTambor ASC";
                $sqlDetalle2 = $con->prepare($sql2);
                $sqlDetalle2->bindParam(':folio', $e['folioTambor']);
                $sqlDetalle2->execute();
                $e2 = $sqlDetalle2->fetch(PDO::FETCH_ASSOC);
                $new = array_merge($e, $e2);
                $resultado[$index] = $new;
            } else if ($e['tipo'] == "1") {
                $sql2 = "SELECT CONCAT(s.codigo, '-', tex.folioTambor) AS folioTambor, s.nombre AS sobrante, z.nombre AS zona, alms.bruto, alms.tara, alms.neto,
                        ls.porcentaje AS humedad, ls.color
                        FROM almacensobrantes alms
                        LEFT JOIN $tambores tex ON tex.folioTambor = alms.consecutivo
                        LEFT JOIN zonastambores z ON z.idZonaTambor = alms.zona
                        LEFT JOIN sobrantes s ON s.idSobrante = alms.sobrante 
                        LEFT JOIN laboratorio_sobrantes ls ON ls.idAlmacen = alms.consecutivo AND ls.sobrante = :clasificacion AND ls.tipoDeMiel = :miel
                        WHERE tex.tipo = '1' AND alms.consecutivo = :folio AND alms.sobrante = :clasificacion AND alms.tipoDeMiel = :miel
                        ORDER BY tex.folioTambor ASC";
                $sqlDetalle2 = $con->prepare($sql2);
                $sqlDetalle2->bindParam(':folio', $e['folioTambor']);
                $sqlDetalle2->bindParam(':clasificacion', $e['clasificacion']);
                $sqlDetalle2->bindParam(':miel', $tmp);
                $sqlDetalle2->execute();
                $e2 = $sqlDetalle2->fetch(PDO::FETCH_ASSOC);
                $new = array_merge($e, $e2);
                $resultado[$index] = $new;
            } else if ($e['tipo'] == "2") {
                $sql2 = "SELECT tex.folioTambor, z.nombre AS zona, al.bruto, al.tara, '' AS sobrante,
                al.neto, lab.porcentaje AS humedad, lab.color
                FROM $tambores tex
                INNER JOIN $traspaso al ON al.idAlmacen = tex.folioTambor
                LEFT JOIN zonastambores z ON z.idZonaTambor = al.zona
                LEFT JOIN laboratorio_traspaso lab ON lab.idAlmacen = al.idAlmacen AND lab.tipoDeMiel = $tmp
                WHERE tex.folioTambor = :folio AND tex.tipo = '0'
                ORDER BY tex.folioTambor ASC";
                $sqlDetalle2 = $con->prepare($sql2);
                $sqlDetalle2->bindParam(':folio', $e['folioTambor']);
                $sqlDetalle2->execute();
                $e2 = $sqlDetalle2->fetch(PDO::FETCH_ASSOC);
                $new = array_merge($e, $e2);
                $resultado[$index] = $new;
            }
        }
        return $resultado;
    }

    function floracionesPorLote($lote, $miel)
    {
        include_once '../../DAOConeccion/conePDO.php';
        $pdo = new conePDO();
        $con = $pdo->conectar();

        switch ($miel) {
            case 1:
                $tambores = 'tamboreslotes';
                $laboratorio = 'laboratorio';
                $calidad = 'calidad';
                break;
            case 2:
                $tambores = 'tamboreslotes_organico';
                $laboratorio = 'laboratorio_organico';
                $calidad = 'calidad_organico';
                break;
            case 5:
                $tambores = 'tamboreslotes_mantequilla';
                $laboratorio = 'laboratorio_mantequilla';
                $calidad = 'calidad_mantequilla';
                break;
            case 6:
                $tambores = 'tamboreslotes_altiplano';
                $laboratorio = 'laboratorio_altiplano';
                $calidad = 'calidad_altiplano';
                break;
            case 7:
                $tambores = 'tamboreslotes_naranjo';
                $laboratorio = 'laboratorio_naranjo';
                $calidad = 'calidad_naranjo';
                break;
                
            case 8:
                $tambores = 'tamboreslotes_aguacate';
                $laboratorio = 'laboratorio_aguacate';
                $calidad = 'calidad_aguacate';
                break;
            case 9:
                $tambores = 'tamboreslotes_mezquite';
                $laboratorio = 'laboratorio_mezquite';
                $calidad = 'calidad_mezquite';
                break;
        }

        $sqlFloraciones = "SELECT f.floracion
        FROM $tambores tl
        LEFT JOIN $calidad c ON c.idLoteInterno = tl.idLoteInterno
        LEFT JOIN $laboratorio l ON tl.folioTambor = l.idAlmacen
        LEFT JOIN floraciones f ON f.idFloracion = l.idFloracion
        WHERE tl.idLoteInterno = :lote AND tl.tipo = 0 AND tl.clasificacion = 0 
        UNION
        SELECT f.floracion
        FROM $tambores tl
        LEFT JOIN $calidad c ON c.idLoteInterno = tl.idLoteInterno
        LEFT JOIN laboratorio_sobrantes l ON tl.folioTambor = l.idAlmacen AND tl.clasificacion = l.sobrante AND l.tipoDeMiel = :miel
        LEFT JOIN floraciones f ON f.idFloracion = l.idFloracion
        WHERE tl.idLoteInterno = :lote AND tl.tipo = 1 AND tl.clasificacion > 0 
        GROUP BY f.floracion";
        $queryFloraciones = $con->prepare($sqlFloraciones);
        $queryFloraciones->bindParam(':lote', $lote);
        $queryFloraciones->bindParam(':miel', $miel);
        $queryFloraciones->execute();

        $resultado = $queryFloraciones->fetchAll(PDO::FETCH_ASSOC);
        return $resultado;
    }

    function lotesContratados($contrato, $miel)
    {
        include_once '../../DAOConeccion/conePDO.php';
        $pdo = new conePDO();
        $con = $pdo->conectar();

        switch ($miel) {
            case '1':
                $tabla = 'lotescontratados';
                break;
            case '2':
                $tabla = 'lotescontratados_organico';
                break;
        }

        $sqlContrato = "SELECT * FROM $tabla WHERE idLoteContratado = :idLote";
        $queryContrato = $con->prepare($sqlContrato);
        $queryContrato->bindParam(':idLote', $contrato);
        $queryContrato->execute();

        $resultado = $queryContrato->fetch(PDO::FETCH_ASSOC);
        return $resultado;
    }

    function tranzabilidadEntrada($idLoteInterno)
    {
        //require_once 'C:/xampp/htdocs/oaxacamiel/DAOConeccion/coneccion.php';
        require_once '/home/darias66/public_html/sysaindustrial.mx/oaxacaMiel/DAOConeccion/coneccion.php';
        $cn = new Coneccion();
        $cn->Conectarse();

        $sqltransEntra = "SELECT tex.folioTambor, tex.idLoteInterno, al.bruto, al.tara, SUM(al.neto)AS kgtotal, ale.fecha, pr.nombre, pr.idSagarpa, 
                                 UCASE(CONCAT(l.localidad,',',es.estado)) AS domicilio, ca.muestraInterna
                                 FROM tamboreslotes tex
                                 INNER JOIN almacen al ON al.idAlmacen = tex.folioTambor
                                 INNER JOIN almacenencabezado ale ON ale.idAlmacen = al.idAlmacenEncabezado
                                 INNER JOIN proveedor pr ON pr.idProveedor = ale.idProveedor
                                 INNER JOIN direccion d ON d.idDireccion = pr.idDireccion
                                 INNER JOIN estados es ON es.idEstado = d.idEstado
                                 INNER JOIN localidades l ON l.idlocalidad = d.idlocalidad
                                 INNER JOIN laboratorio lab ON lab.idAlmacen = al.idAlmacen
                                 INNER JOIN resultadofinal rs ON rs.idresultadoFinal = lab.resultadoFinal
                                 INNER JOIN calidad ca ON ca.idLoteInterno = tex.idLoteInterno
                                 WHERE tex.idLoteInterno = '$idLoteInterno'
                                 GROUP BY  idSagarpa";

        $datos = mysql_query($sqltransEntra);
        $cn->cerrarBd();
        return $datos;
    }

    ////////// Consulta de la tabla especificaciones ///////
    function especificaciones($idLoteInterno, $tmp)
    {
        if ($tmp == 1) {
            $sqlEspecificaciones = "SELECT * FROM especificaciones WHERE idLoteInterno = '$idLoteInterno' ";
            return $sqlEspecificaciones;
        } else if ($tmp == 2) {
            $sqlEspecificaciones = "SELECT * FROM especificaciones_organico WHERE idLoteInterno = '$idLoteInterno' ";
            return $sqlEspecificaciones;
        } else if ($tmp == 5) {
            $sqlEspecificaciones = "SELECT * FROM especificaciones_mantequilla WHERE idLoteInterno = '$idLoteInterno' ";
            return $sqlEspecificaciones;
        } else if ($tmp == 6) {
            $sqlEspecificaciones = "SELECT * FROM especificaciones_altiplano WHERE idLoteInterno = '$idLoteInterno' ";
            return $sqlEspecificaciones;
        } else if ($tmp == 7) {
            $sqlEspecificaciones = "SELECT * FROM especificaciones_naranjo WHERE idLoteInterno = '$idLoteInterno' ";
            return $sqlEspecificaciones;
        } else if ($tmp == 8) {
            $sqlEspecificaciones = "SELECT * FROM especificaciones_aguacate WHERE idLoteInterno = '$idLoteInterno' ";
            return $sqlEspecificaciones;
        } else if ($tmp == 9) {
            $sqlEspecificaciones = "SELECT * FROM especificaciones_mezquite WHERE idLoteInterno = '$idLoteInterno' ";
            return $sqlEspecificaciones;
        }
    }
}

class produccion
{

    function infoReporteProceso($idReporteProceso, $tmp)
    {
        if ($tmp == 1) {
            $sqlProceso = "SELECT r.*, p.nombre AS nombreRes, po.nombre AS nombreSuper,ca.numeroDeTambores,
            h.cantidadLlave, h.condicionLlave, h.cantidadOtras, h.condicionOtras, 
            h.cantidadPala, h.condicionPala,ca.fechaProceso
           FROM reportesdeprocesos r
           INNER JOIN personaloaxaca p ON p.idPersonalOM = r.responsable
           INNER JOIN personaloaxaca po ON po.idPersonalOM = r.supervisor
           INNER JOIN calidad        ca ON ca.idLoteInterno = r.idLoteInterno
           LEFT JOIN herramientas h
           ON h.idReporteProceso = r.idReporteProceso
           WHERE r.idReporteProceso ='$idReporteProceso'";

            return $sqlProceso;
        } else {
            $sqlProceso = "SELECT r.*, p.nombre AS nombreRes, po.nombre AS nombreSuper,ca.numeroDeTambores,
            h.cantidadLlave, h.condicionLlave, h.cantidadOtras, h.condicionOtras, 
            h.cantidadPala, h.condicionPala,ca.fechaProceso
           FROM reportesdeprocesos_organico r
           INNER JOIN personaloaxaca p ON p.idPersonalOM = r.responsable
           INNER JOIN personaloaxaca po ON po.idPersonalOM = r.supervisor
           INNER JOIN calidad_organico        ca ON ca.idLoteInterno = r.idLoteInterno
           LEFT JOIN herramientas_organico h
           ON h.idReporteProceso = r.idReporteProceso
           WHERE r.idReporteProceso ='$idReporteProceso'";

            return $sqlProceso;
        }
    }

    function descrpcionLoteProcesado($idLoteInternoPr, $tmp)
    {
        if ($tmp == 1) {
            $query = "
            SELECT idLoteInterno, fechaProceso, marcaFinalCliente, numeroDeTambores, kilosTotales,
            (SELECT SUM(al.bruto) FROM almacen al INNER JOIN tamboreslotes tex ON tex.folioTambor = al.idAlmacen WHERE tex.idLoteInterno = '$idLoteInternoPr')as brutoTotal,
            (SELECT SUM(al.tara) FROM almacen al INNER JOIN tamboreslotes tex ON tex.folioTambor = al.idAlmacen WHERE tex.idLoteInterno = '$idLoteInternoPr') as taraTotal,
            (SELECT SUM(al.neto) FROM almacen al INNER JOIN tamboreslotes tex ON tex.folioTambor = al.idAlmacen WHERE tex.idLoteInterno = '$idLoteInternoPr') as netoTotal
            FROM calidad WHERE idLoteInterno = '$idLoteInternoPr'";

            return $query;
        } else {
            $query = "
            SELECT idLoteInterno, fechaProceso, marcaFinalCliente, numeroDeTambores, kilosTotales,
            (SELECT SUM(al.bruto) FROM almacen_organico al INNER JOIN tamboreslotes_organico tex ON tex.folioTambor = al.idAlmacen WHERE tex.idLoteInterno = '$idLoteInternoPr')as brutoTotal,
            (SELECT SUM(al.tara) FROM almacen_organico al INNER JOIN tamboreslotes_organico tex ON tex.folioTambor = al.idAlmacen WHERE tex.idLoteInterno = '$idLoteInternoPr') as taraTotal,
            (SELECT SUM(al.neto) FROM almacen_organico al INNER JOIN tamboreslotes_organico tex ON tex.folioTambor = al.idAlmacen WHERE tex.idLoteInterno = '$idLoteInternoPr') as netoTotal
            FROM calidad_organico WHERE idLoteInterno = '$idLoteInternoPr'";

            return $query;
        }
    }

    function personalConforLote($idReporteProceso, $tmp)
    {
        if ($tmp == 1) {
            $sqlConLote = "SELECT pc.idConformacionLote, pc.idPersonalOM, om.nombre
            FROM conformaciondelotes pc
            INNER JOIN personaloaxaca om ON om.idPersonalOM = pc.idPersonalOM
            WHERE pc.idReporteProceso = $idReporteProceso";

            return $sqlConLote;
        } else {
            $sqlConLote = "SELECT pc.idConformacionLote, pc.idPersonalOM, om.nombre
            FROM conformaciondelotes_organico pc
            INNER JOIN personaloaxaca om ON om.idPersonalOM = pc.idPersonalOM
            WHERE pc.idReporteProceso = $idReporteProceso";

            return $sqlConLote;
        }
    }

    function personalZonainocua($idReporteProceso, $tmp)
    {
        if ($tmp == 1) {
            $sqlZonaInocua = "SELECT pp.idProceso, pp.idPersonalOM, om.nombre 
        FROM procesozonainocua pp
        INNER JOIN personaloaxaca om ON om.idPersonalOM = pp.idPersonalOM
        WHERE pp.idReporteProceso = $idReporteProceso";
            return $sqlZonaInocua;
        } else {
            $sqlZonaInocua = "SELECT pp.idProceso, pp.idPersonalOM, om.nombre 
        FROM procesozonainocua_organico pp
        INNER JOIN personaloaxaca om ON om.idPersonalOM = pp.idPersonalOM
        WHERE pp.idReporteProceso = $idReporteProceso";
            return $sqlZonaInocua;
        }
    }

    function personalFolio($idReporteProceso, $tmp)
    {
        if ($tmp == 1) {
            $sqlFolio = "SELECT pf.idBusqueda, pf.idPersonalOM, om.nombre 
        FROM busquedadefolios pf
        INNER JOIN personaloaxaca om ON om.idPersonalOM = pf.idPersonalOM
        WHERE pf.idReporteProceso = $idReporteProceso";

            return $sqlFolio;
        } else {
            $sqlFolio = "SELECT pf.idBusqueda, pf.idPersonalOM, om.nombre 
        FROM busquedadefolios_organico pf
        INNER JOIN personaloaxaca om ON om.idPersonalOM = pf.idPersonalOM
        WHERE pf.idReporteProceso = $idReporteProceso";

            return $sqlFolio;
        }
    }

    function reporteEnvasado($idReporteEnvasado, $tdm)
    {
        if ($tdm == 1) {
            $sqlEnvasado = "SELECT r.*,c.fechaEnvasado
                            FROM reportesdeenvasados r
                            LEFT JOIN calidad c ON c.idLoteInterno = r.idLoteInterno
                            WHERE idReporteEnvasado = $idReporteEnvasado";
            return $sqlEnvasado;
        }  else if ($tdm == 5) {
            $sqlEnvasado = "SELECT r.*,c.fechaEnvasado
                            FROM reportesdeenvasados_mantequilla r
                            LEFT JOIN calidad_mantequilla c ON c.idLoteInterno = r.idLoteInterno
                            WHERE idReporteEnvasado = $idReporteEnvasado";
            return $sqlEnvasado;
        } 
        else if ($tdm == 6) {
            $sqlEnvasado = "SELECT r.*,c.fechaEnvasado
                            FROM reportesdeenvasados_altiplano r
                            LEFT JOIN calidad_altiplano c ON c.idLoteInterno = r.idLoteInterno
                            WHERE idReporteEnvasado = $idReporteEnvasado";
            return $sqlEnvasado;
        } 
        else if ($tdm == 7) {
            $sqlEnvasado = "SELECT r.*,c.fechaEnvasado
                            FROM reportesdeenvasados_naranjo r
                            LEFT JOIN calidad_naranjo c ON c.idLoteInterno = r.idLoteInterno
                            WHERE idReporteEnvasado = $idReporteEnvasado";
            return $sqlEnvasado;
        } 
        else if ($tdm == 8) {
            $sqlEnvasado = "SELECT r.*,c.fechaEnvasado
                            FROM reportesdeenvasados_aguacate r
                            LEFT JOIN calidad_aguacate c ON c.idLoteInterno = r.idLoteInterno
                            WHERE idReporteEnvasado = $idReporteEnvasado";
            return $sqlEnvasado;
        } 
        else if ($tdm == 9) {
            $sqlEnvasado = "SELECT r.*,c.fechaEnvasado
                            FROM reportesdeenvasados_mezquite r
                            LEFT JOIN calidad_mezquite c ON c.idLoteInterno = r.idLoteInterno
                            WHERE idReporteEnvasado = $idReporteEnvasado";
            return $sqlEnvasado;
        } 
         else {
            $sqlEnvasado = "SELECT r.*,c.fechaEnvasado
            FROM reportesdeenvasados_organico r
            LEFT JOIN calidad_organico c ON c.idLoteInterno = r.idLoteInterno
            WHERE idReporteEnvasado = $idReporteEnvasado";
            return $sqlEnvasado;
        }
    }

    function personalEnvasado($idReporteEnvasado, $tmd)
    {
        if ($tmd == 1) {
            $sqlPersonalEnvasado = "SELECT pc.idEnvasado, pc.idPersonalOM, om.nombre
                    FROM personalenvasado pc
                    INNER JOIN personaloaxaca om ON om.idPersonalOM = pc.idPersonalOM
                    WHERE pc.idReporteEnvasado = $idReporteEnvasado";
            return $sqlPersonalEnvasado;
        }  else if($tmd == 5){
            $sqlPersonalEnvasado = "SELECT pc.idEnvasado, pc.idPersonalOM, om.nombre
            FROM personalenvasado_mantequilla pc
            INNER JOIN personaloaxaca om ON om.idPersonalOM = pc.idPersonalOM
            WHERE pc.idReporteEnvasado = $idReporteEnvasado";
            return $sqlPersonalEnvasado;
        }
        else if($tmd == 6){
            $sqlPersonalEnvasado = "SELECT pc.idEnvasado, pc.idPersonalOM, om.nombre
            FROM personalenvasado_altiplano pc
            INNER JOIN personaloaxaca om ON om.idPersonalOM = pc.idPersonalOM
            WHERE pc.idReporteEnvasado = $idReporteEnvasado";
            return $sqlPersonalEnvasado;
        }
        else if($tmd == 7){
            $sqlPersonalEnvasado = "SELECT pc.idEnvasado, pc.idPersonalOM, om.nombre
            FROM personalenvasado_naranjo pc
            INNER JOIN personaloaxaca om ON om.idPersonalOM = pc.idPersonalOM
            WHERE pc.idReporteEnvasado = $idReporteEnvasado";
            return $sqlPersonalEnvasado;
        }
        else if($tmd == 8){
            $sqlPersonalEnvasado = "SELECT pc.idEnvasado, pc.idPersonalOM, om.nombre
            FROM personalenvasado_aguacate pc
            INNER JOIN personaloaxaca om ON om.idPersonalOM = pc.idPersonalOM
            WHERE pc.idReporteEnvasado = $idReporteEnvasado";
            return $sqlPersonalEnvasado;
        }
        else if($tmd == 9){
            $sqlPersonalEnvasado = "SELECT pc.idEnvasado, pc.idPersonalOM, om.nombre
            FROM personalenvasado_mezquite pc
            INNER JOIN personaloaxaca om ON om.idPersonalOM = pc.idPersonalOM
            WHERE pc.idReporteEnvasado = $idReporteEnvasado";
            return $sqlPersonalEnvasado;
        }
        else {
            $sqlPersonalEnvasado = "SELECT pc.idEnvasado, pc.idPersonalOM, om.nombre
            FROM personalenvasado_organico pc
            INNER JOIN personaloaxaca om ON om.idPersonalOM = pc.idPersonalOM
            WHERE pc.idReporteEnvasado = $idReporteEnvasado";
            return $sqlPersonalEnvasado;
        }
    }

    function ayudanteInterno($idReporteEnvasado, $tdm)
    {
        if ($tdm == 1) {
            $sqlAyudateInt = "SELECT pp.idAyudanteInterno, pp.idPersonalOM, om.nombre 
                    FROM ayudantesinternos pp
                    INNER JOIN personaloaxaca om ON om.idPersonalOM = pp.idPersonalOM
                    WHERE pp.idReporteEnvasado =$idReporteEnvasado";
            return $sqlAyudateInt;
        } 
        else if ($tdm == 5) {
            $sqlAyudateInt = "SELECT pp.idAyudanteInterno, pp.idPersonalOM, om.nombre 
            FROM ayudantesinternos_mantequilla pp
            INNER JOIN personaloaxaca om ON om.idPersonalOM = pp.idPersonalOM
            WHERE pp.idReporteEnvasado =$idReporteEnvasado";
            return $sqlAyudateInt;
        }
        else if ($tdm == 6) {
            $sqlAyudateInt = "SELECT pp.idAyudanteInterno, pp.idPersonalOM, om.nombre 
            FROM ayudantesinternos_altiplano pp
            INNER JOIN personaloaxaca om ON om.idPersonalOM = pp.idPersonalOM
            WHERE pp.idReporteEnvasado =$idReporteEnvasado";
            return $sqlAyudateInt;
        }
        else if ($tdm == 7) {
            $sqlAyudateInt = "SELECT pp.idAyudanteInterno, pp.idPersonalOM, om.nombre 
            FROM ayudantesinternos_naranjo pp
            INNER JOIN personaloaxaca om ON om.idPersonalOM = pp.idPersonalOM
            WHERE pp.idReporteEnvasado =$idReporteEnvasado";
            return $sqlAyudateInt;
        }
        else if ($tdm == 8) {
            $sqlAyudateInt = "SELECT pp.idAyudanteInterno, pp.idPersonalOM, om.nombre 
            FROM ayudantesinternos_aguacate pp
            INNER JOIN personaloaxaca om ON om.idPersonalOM = pp.idPersonalOM
            WHERE pp.idReporteEnvasado =$idReporteEnvasado";
            return $sqlAyudateInt;
        }
        else if ($tdm == 9) {
            $sqlAyudateInt = "SELECT pp.idAyudanteInterno, pp.idPersonalOM, om.nombre 
            FROM ayudantesinternos_mezquite pp
            INNER JOIN personaloaxaca om ON om.idPersonalOM = pp.idPersonalOM
            WHERE pp.idReporteEnvasado =$idReporteEnvasado";
            return $sqlAyudateInt;
        }
        else {
            $sqlAyudateInt = "SELECT pp.idAyudanteInterno, pp.idPersonalOM, om.nombre 
            FROM ayudantesinternos_organico pp
            INNER JOIN personaloaxaca om ON om.idPersonalOM = pp.idPersonalOM
            WHERE pp.idReporteEnvasado =$idReporteEnvasado";
            return $sqlAyudateInt;
        }
    }

    function ayudanteExterno($idReporteEnvasado, $tdm)
    {
        if ($tdm == 1) {
            $sqlAyudateEx = "SELECT pf.idAyudanteExterno, pf.idPersonalOM, om.nombre 
                    FROM ayudantesexternos pf
                    INNER JOIN personaloaxaca om ON om.idPersonalOM = pf.idPersonalOM
                    WHERE pf.idReporteEnvasado = $idReporteEnvasado";
            return $sqlAyudateEx;
        } 
        else if ($tdm == 5){
            $sqlAyudateEx = "SELECT pf.idAyudanteExterno, pf.idPersonalOM, om.nombre 
            FROM ayudantesexternos_mantequilla pf
            INNER JOIN personaloaxaca om ON om.idPersonalOM = pf.idPersonalOM
            WHERE pf.idReporteEnvasado = $idReporteEnvasado";
            return $sqlAyudateEx;
        }
        else if ($tdm == 6){
            $sqlAyudateEx = "SELECT pf.idAyudanteExterno, pf.idPersonalOM, om.nombre 
            FROM ayudantesexternos_altiplano pf
            INNER JOIN personaloaxaca om ON om.idPersonalOM = pf.idPersonalOM
            WHERE pf.idReporteEnvasado = $idReporteEnvasado";
            return $sqlAyudateEx;
        }
        else if ($tdm == 7){
            $sqlAyudateEx = "SELECT pf.idAyudanteExterno, pf.idPersonalOM, om.nombre 
            FROM ayudantesexternos_naranjo pf
            INNER JOIN personaloaxaca om ON om.idPersonalOM = pf.idPersonalOM
            WHERE pf.idReporteEnvasado = $idReporteEnvasado";
            return $sqlAyudateEx;
        }
        else if ($tdm == 8){
            $sqlAyudateEx = "SELECT pf.idAyudanteExterno, pf.idPersonalOM, om.nombre 
            FROM ayudantesexternos_aguacate pf
            INNER JOIN personaloaxaca om ON om.idPersonalOM = pf.idPersonalOM
            WHERE pf.idReporteEnvasado = $idReporteEnvasado";
            return $sqlAyudateEx;
        }
        else if ($tdm == 9){
            $sqlAyudateEx = "SELECT pf.idAyudanteExterno, pf.idPersonalOM, om.nombre 
            FROM ayudantesexternos_mezquite pf
            INNER JOIN personaloaxaca om ON om.idPersonalOM = pf.idPersonalOM
            WHERE pf.idReporteEnvasado = $idReporteEnvasado";
            return $sqlAyudateEx;
        }
        else {
            $sqlAyudateEx = "SELECT pf.idAyudanteExterno, pf.idPersonalOM, om.nombre 
            FROM ayudantesexternos_organico pf
            INNER JOIN personaloaxaca om ON om.idPersonalOM = pf.idPersonalOM
            WHERE pf.idReporteEnvasado = $idReporteEnvasado";
            return $sqlAyudateEx;
        }
    }

    function TamboresEnvasado($idReporteEnvasado, $tdm)
    {
        if ($tdm == 1) {
            $sqlTamboresEnvasados = "SELECT * 
            FROM pesosenvasados
            WHERE idReporteEnvasado = $idReporteEnvasado";
            return $sqlTamboresEnvasados;
        } 
        else if ($tdm == 5)  {
            $sqlTamboresEnvasados = "SELECT * 
            FROM pesosenvasados_mantequilla
            WHERE idReporteEnvasado = $idReporteEnvasado";
            return $sqlTamboresEnvasados;
        }
        else if ($tdm == 6)  {
            $sqlTamboresEnvasados = "SELECT * 
            FROM pesosenvasados_altiplano
            WHERE idReporteEnvasado = $idReporteEnvasado";
            return $sqlTamboresEnvasados;
        }
        else if ($tdm == 7)  {
            $sqlTamboresEnvasados = "SELECT * 
            FROM pesosenvasados_naranjo
            WHERE idReporteEnvasado = $idReporteEnvasado";
            return $sqlTamboresEnvasados;
        }
        else if ($tdm == 8)  {
            $sqlTamboresEnvasados = "SELECT * 
            FROM pesosenvasados_aguacate
            WHERE idReporteEnvasado = $idReporteEnvasado";
            return $sqlTamboresEnvasados;
        }
        else if ($tdm == 9)  {
            $sqlTamboresEnvasados = "SELECT * 
            FROM pesosenvasados_mezquite
            WHERE idReporteEnvasado = $idReporteEnvasado";
            return $sqlTamboresEnvasados;
        }
        else {
            $sqlTamboresEnvasados = "SELECT * 
            FROM pesosenvasados_organico
            WHERE idReporteEnvasado = $idReporteEnvasado";
            return $sqlTamboresEnvasados;
        }
    }
}

class trazabilidad
{

    function trazabilidadEntrada($tipo, $idLoteInterno)
    {

        switch ($tipo) {
            case '1':
                $tamboresLotes = 'tamboreslotes';
                $almacen = 'almacen';
                $almacenEncabezado = 'almacenencabezado';
                $calidad = 'calidad';
                break;
            case '2':
                $tamboresLotes = 'tamboreslotes_organico';
                $almacen = 'almacen_organico';
                $almacenEncabezado = 'almacenencabezado_organico';
                $calidad = 'calidad_organico';
                break;
            case '5':
                $tamboresLotes = 'tamboreslotes_mantequilla';
                $almacen = 'almacen_mantequilla';
                $almacenEncabezado = 'almacenencabezado_mantequilla';
                $calidad = 'calidad_mantequilla';
                break;
            case '6':
                $tamboresLotes = 'tamboreslotes_altiplano';
                $almacen = 'almacen_altiplano';
                $almacenEncabezado = 'almacenencabezado_altiplano';
                $calidad = 'calidad_altiplano';
                break;
            case '7':
                $tamboresLotes = 'tamboreslotes_naranjo';
                $almacen = 'almacen_naranjo';
                $almacenEncabezado = 'almacenencabezado_naranjo';
                $calidad = 'calidad_naranjo';
                break;
                
            case '8':
                $tamboresLotes = 'tamboreslotes_aguacate';
                $almacen = 'almacen_aguacate';
                $almacenEncabezado = 'almacenencabezado_aguacate';
                $calidad = 'calidad_aguacate';
                break;
            case '9':
                $tamboresLotes = 'tamboreslotes_mezquite';
                $almacen = 'almacen_mezquite';
                $almacenEncabezado = 'almacenencabezado_mezquite';
                $calidad = 'calidad_mezquite';
                break;
        }

        // $sqlTrazaEnttrada = "SELECT MAX(ale.fecha) as fecha, rd.marcaFinalCliente,tex.idLoteInterno, pr.idSagarpa,
        //                          UCASE(CONCAT(l.localidad,',',es.estado)) AS domicilio, SUM(al.neto)AS kilos
        //                          FROM $tamboresLotes tex
        //                          INNER JOIN $almacen al ON al.idAlmacen = tex.folioTambor
        //                          INNER JOIN $almacenEncabezado ale ON ale.idAlmacen = al.idAlmacenEncabezado
        //                          INNER JOIN proveedor pr ON pr.idProveedor = ale.idProveedor
        //                          INNER JOIN direccion d ON d.idDireccion = pr.idDireccion
        //                          INNER JOIN estados es ON es.idEstado = d.idEstado
        //                          INNER JOIN localidades l ON l.idlocalidad = d.idlocalidad
        //                          LEFT JOIN $calidad rd ON rd.idLoteInterno = tex.idLoteInterno
        //                          WHERE tex.idLoteInterno = $idLoteInterno AND tex.tipo = '0'
        //                          GROUP BY idSagarpa";

        $sqlTrazaEnttrada = "SELECT MIN(ale.fecha) AS fecha, tex.idLoteInterno, pr.idSagarpa, c.marcaFinalCliente,
                                    UCASE(CONCAT(l.localidad,', ',es.estado)) AS domicilio, SUM(al.neto)AS kilos
                                    FROM $almacenEncabezado ale 
                                    LEFT JOIN $almacen al ON ale.idAlmacen = al.idAlmacenEncabezado
                                    LEFT JOIN $tamboresLotes tex ON tex.folioTambor = al.idAlmacen AND tex.tipo = '0' AND tex.clasificacion = '0'
                                    LEFT JOIN proveedor pr ON pr.idProveedor = ale.idProveedor
                                    LEFT JOIN direccion d ON d.idDireccion = pr.idDireccion
                                    LEFT JOIN estados es ON es.idEstado = d.idEstado
                                    LEFT JOIN localidades l ON l.idlocalidad = d.idlocalidad
                                    LEFT JOIN $calidad c ON c.idLoteInterno = tex.idLoteInterno
                                    WHERE tex.idLoteInterno = $idLoteInterno
                                    GROUP BY idSagarpa
                                UNION
                                SELECT MIN(al.fecha) AS fecha, tex.idLoteInterno, pr.idSagarpa, c.marcaFinalCliente,
                                        UCASE(CONCAT(l.localidad,', ',es.estado)) AS domicilio, SUM(alsde.neto)AS kilos
                                FROM almacensobrantesencabezado al 
                                LEFT JOIN almacensobrantes alsde ON alsde.consecutivoEntrada = al.idEncabezadoSobrante
                                LEFT JOIN $tamboresLotes tex ON tex.folioTambor = alsde.consecutivo AND tex.tipo = '1' AND tex.clasificacion = alsde.sobrante
                                INNER JOIN proveedor pr
                                LEFT JOIN direccion d ON d.idDireccion = pr.idDireccion
                                LEFT JOIN estados es ON es.idEstado = d.idEstado
                                LEFT JOIN localidades l ON l.idlocalidad = d.idlocalidad
                                LEFT JOIN $calidad c ON c.idLoteInterno = tex.idLoteInterno
                                WHERE tex.idLoteInterno = $idLoteInterno AND al.tipoDeMiel = '" . $tipo . "' AND pr.idProveedor = '83'
                                GROUP BY idSagarpa";

        return $sqlTrazaEnttrada;
    }

    function trazabilidadAnalisis($tipo, $idLoteInterno)
    {
        switch ($tipo) {
            case '1':
                $tamboresLotes = 'tamboreslotes';
                $almacen = 'almacen';
                $almacenEncabezado = 'almacenencabezado';
                $calidad = 'calidad';
                break;
            case '2':
                $tamboresLotes = 'tamboreslotes_organico';
                $almacen = 'almacen_organico';
                $almacenEncabezado = 'almacenencabezado_organico';
                $calidad = 'calidad_organico';
                break;
            case '5':
                $tamboresLotes = 'tamboreslotes_mantequilla';
                $almacen = 'almacen_mantequilla';
                $almacenEncabezado = 'almacenencabezado_mantequilla';
                $calidad = 'calidad_mantequilla';
                break;
            case '6':
                $tamboresLotes = 'tamboreslotes_altiplano';
                $almacen = 'almacen_altiplano';
                $almacenEncabezado = 'almacenencabezado_altiplano';
                $calidad = 'calidad_altiplano';
                break;
            case '7':
                $tamboresLotes = 'tamboreslotes_naranjo';
                $almacen = 'almacen_naranjo';
                $almacenEncabezado = 'almacenencabezado_naranjo';
                $calidad = 'calidad_naranjo';
                break;
            case '8':
                $tamboresLotes = 'tamboreslotes_aguacate';
                $almacen = 'almacen_aguacate';
                $almacenEncabezado = 'almacenencabezado_aguacate';
                $calidad = 'calidad_aguacate';
                break;
            case '9':
                $tamboresLotes = 'tamboreslotes_mezquite';
                $almacen = 'almacen_mezquite';
                $almacenEncabezado = 'almacenencabezado_mezquite';
                $calidad = 'calidad_mezquite';
                break;
        }
        $sqlAnalisis = "SELECT ale.idAlmacen AS id, tex.idLoteInterno, c.marcaFinalCliente, pr.idSagarpa, tl.nombreLaboratorio, tl.fechaProtocolo,tl.folioProtocolo 
                            FROM $tamboresLotes tex
                            INNER JOIN $almacen al ON al.idAlmacen = tex.folioTambor
                            INNER JOIN $almacenEncabezado ale ON ale.idAlmacen = al.idAlmacenEncabezado
                            INNER JOIN proveedor pr ON pr.idProveedor = ale.idProveedor
                            INNER JOIN trazabilidadlaboratorio tl ON  tl.idLoteInterno = tex.idLoteInterno
                            LEFT JOIN $calidad c ON c.idLoteInterno = tex.idLoteInterno
				            WHERE tex.idLoteInterno = $idLoteInterno AND tex.tipo = '0' AND tl.tipoMiel = '" . $tipo . "' 
                            GROUP BY idSagarpa
                        UNION
                        SELECT al.consecutivo AS id, tex.idLoteInterno, c.marcaFinalCliente, pr.idSagarpa, tl.nombreLaboratorio, tl.fechaProtocolo,tl.folioProtocolo 
                            FROM $tamboresLotes tex
                            INNER JOIN almacensobrantes al ON al.consecutivo = tex.folioTambor AND al.sobrante = tex.clasificacion
                            INNER JOIN proveedor pr 
                            INNER JOIN trazabilidadlaboratorio tl ON  tl.idLoteInterno = tex.idLoteInterno
                            LEFT JOIN $calidad c ON c.idLoteInterno = tex.idLoteInterno
				            WHERE tex.idLoteInterno = $idLoteInterno AND tex.tipo = '1' AND al.tipoDeMiel = '" . $tipo . "' AND pr.idProveedor = '83'
                            GROUP BY idSagarpa";
        return $sqlAnalisis;
    }

    function tranzabilidadSalida($idLoteInterno, $tipoMiel)
    {
        if ($tipoMiel == '1') {
            $calidad_tabla = 'calidad';
        } else if ($tipoMiel == '5') {
            $calidad_tabla = 'calidad_mantequilla';
        } else if ($tipoMiel == '6') {
            $calidad_tabla = 'calidad_altiplano';
        }else if ($tipoMiel == '7') {
            $calidad_tabla = 'calidad_naranjo';
        } else if ($tipoMiel == '8') {
            $calidad_tabla = 'calidad_aguacate';
        }else if ($tipoMiel == '9') {
            $calidad_tabla = 'calidad_mezquite';
        }else {
            $calidad_tabla = 'calidad_organico';
        }
        $sqlSalida = "SELECT c.fechaEnvasado, t.*, c.marcaFinalCliente, em.empresa, em.pais, tdm.tipoDeMiel FROM $calidad_tabla c
                    LEFT JOIN trazabilidadsalida t ON t.idLoteInterno = c.idLoteInterno
                    LEFT JOIN empresasypaises em   ON em.idEmpresaPais = t.idEmpresaPais
                    LEFT JOIN tiposdemiel tdm ON t.tipoMiel = tdm.idTipoDeMiel
                    WHERE c.idLoteInterno = $idLoteInterno AND t.tipoMiel = $tipoMiel";
        return $sqlSalida;
    }

    function tranzabilidadSalidaArray($idLoteInterno, $tipoMiel)
    {
        if ($tipoMiel == '1') {
            $calidad_tabla = 'calidad';
            $tamboreslotes_tabla = 'tamboreslotes';
            $almacen_tabla = 'almacen';
            $almacenencabezado_tabla = 'almacenencabezado';
        } else if ($tipoMiel == '2') {
            $calidad_tabla = 'calidad_organico';
            $tamboreslotes_tabla = 'tamboreslotes_organico';
            $almacen_tabla = 'almacen_organico';
            $almacenencabezado_tabla = 'almacenencabezado_organico';
        } else if ($tipoMiel == '5') {
            $calidad_tabla = 'calidad_mantequilla';
            $tamboreslotes_tabla = 'tamboreslotes_mantequilla';
            $almacen_tabla = 'almacen_mantequilla';
            $almacenencabezado_tabla = 'almacenencabezado_mantequilla';
        } else if ($tipoMiel == '6') {
            $calidad_tabla = 'calidad_altiplano';
            $tamboreslotes_tabla = 'tamboreslotes_altiplano';
            $almacen_tabla = 'almacen_altiplano';
            $almacenencabezado_tabla = 'almacenencabezado_altiplano';
        } else if ($tipoMiel == '7') {
            $calidad_tabla = 'calidad_naranjo';
            $tamboreslotes_tabla = 'tamboreslotes_naranjo';
            $almacen_tabla = 'almacen_naranjo';
            $almacenencabezado_tabla = 'almacenencabezado_naranjo';
        } else if ($tipoMiel == '8') {
            $calidad_tabla = 'calidad_aguacate';
            $tamboreslotes_tabla = 'tamboreslotes_aguacate';
            $almacen_tabla = 'almacen_aguacate';
            $almacenencabezado_tabla = 'almacenencabezado_aguacate';
        } else if ($tipoMiel == '9') {
            $calidad_tabla = 'calidad_mezquite';
            $tamboreslotes_tabla = 'tamboreslotes_mezquite';
            $almacen_tabla = 'almacen_mezquite';
            $almacenencabezado_tabla = 'almacenencabezado_mezquite';
        }
        // $sqlSalidaArray = "SELECT  pr.idSagarpa, SUM(al.neto)AS volumen
        //                          FROM $tamboreslotes_tabla tex
        //                          INNER JOIN $almacen_tabla al ON al.idAlmacen = tex.folioTambor
        //                          INNER JOIN $almacenencabezado_tabla ale ON ale.idAlmacen = al.idAlmacenEncabezado
        //                          INNER JOIN proveedor pr ON pr.idProveedor = ale.idProveedor
        //                          INNER JOIN $calidad_tabla ca ON ca.idLoteInterno = tex.idLoteInterno
        //                          WHERE tex.idLoteInterno = $idLoteInterno
        //                          GROUP BY idSagarpa";    

        $sqlSalidaArray = "SELECT ale.idAlmacen AS id, pr.idSagarpa, SUM(al.neto)AS volumen
                                 FROM $tamboreslotes_tabla tex
                                 INNER JOIN $almacen_tabla al ON al.idAlmacen = tex.folioTambor
                                 INNER JOIN $almacenencabezado_tabla ale ON ale.idAlmacen = al.idAlmacenEncabezado
                                 INNER JOIN proveedor pr ON pr.idProveedor = ale.idProveedor
                                 INNER JOIN $calidad_tabla ca ON ca.idLoteInterno = tex.idLoteInterno
                                 WHERE tex.idLoteInterno = $idLoteInterno AND tex.tipo = '0'
                                 GROUP BY  idSagarpa
                         UNION
                         SELECT  al.consecutivo AS id, pr.idSagarpa, SUM(al.neto)AS volumen
                                 FROM $tamboreslotes_tabla tex
                                 INNER JOIN almacensobrantes al ON al.consecutivo = tex.folioTambor AND al.sobrante = tex.clasificacion
                                 INNER JOIN proveedor pr
                                 INNER JOIN $calidad_tabla ca ON ca.idLoteInterno = tex.idLoteInterno
                                 WHERE tex.idLoteInterno = $idLoteInterno AND tex.tipo = '1' AND al.tipoDeMiel = '" . $tipoMiel . "' AND pr.idProveedor = '83'
                                 GROUP BY  idSagarpa";

        return $sqlSalidaArray;
    }
}

class listaPesos
{

    function listaPesosEnca($idTamborPeso)
    {

        include_once '../../DAOConeccion/conePDO.php';
        $pdo = new conePDO();
        $con = $pdo->conectar();

        $seleccionaTipoMiel = $con->prepare("SELECT tipoMiel FROM listadepesos WHERE idTamborPeso = :idTamborPeso");
        $seleccionaTipoMiel->bindParam(':idTamborPeso', $idTamborPeso);
        $seleccionaTipoMiel->execute();

        $tipoMiel = $seleccionaTipoMiel->fetch(PDO::FETCH_ASSOC);

        switch ($tipoMiel['tipoMiel']) {
            case '1':
                $entradaysalida_tabla = 'entradaysalida';
                break;
            case '2':
                $entradaysalida_tabla = 'entradaysalida_organico';
                break;
            case '5':
                $entradaysalida_tabla = 'entradaysalida_mantequilla';
                break;
            case '6':
                $entradaysalida_tabla = 'entradaysalida_altiplano';
                break;
            case '7':
                $entradaysalida_tabla = 'entradaysalida_naranjo';
                break;
                
            case '8':
                $entradaysalida_tabla = 'entradaysalida_aguacate';
                break;
            case '9':
                $entradaysalida_tabla = 'entradaysalida_mezquite';
                break;
        }

        $sqlListaPesosEnca = $con->prepare("SELECT lp.*, rc.fechaImpresion, rc.clasificacionMiel, p.marca, p.modelo, rc.contenedor, rc.sello,
            o.operador, o.compania, p.placa, tdm.tipoDeMiel as tipoMiel, rc.idLoteInterno
            FROM listadepesos lp
            LEFT JOIN $entradaysalida_tabla rc ON rc.lote = lp.lote
            LEFT JOIN choferes o ON o.idOperador = rc.idOperador 
            LEFT JOIN placaschoferes p ON p.idPlaca = rc.idPlaca
            LEFT JOIN tiposdemiel tdm ON  lp.tipoMiel = tdm.idTipoDeMiel
            WHERE lp.idTamborPeso = :idTamborPeso");
        $sqlListaPesosEnca->bindParam(':idTamborPeso', $idTamborPeso);
        $sqlListaPesosEnca->execute();

        $resultadoEncabezado = $sqlListaPesosEnca->fetch(PDO::FETCH_ASSOC);
        return $resultadoEncabezado;
    }

    function listaPesosDetalle($idTamborPeso)
    {
        include_once '../../DAOConeccion/conePDO.php';
        $pdo = new conePDO();
        $con = $pdo->conectar();
        $resultadoDetalle = array();

        $sqlMH = $con->prepare("SELECT mielHomogeneizada FROM listadepesos WHERE idTamborPeso = :idTamborPeso");
        $sqlMH->bindParam(':idTamborPeso', $idTamborPeso);
        $sqlMH->execute();
        $resultado = $sqlMH->fetch(PDO::FETCH_ASSOC);
        if ($resultado['mielHomogeneizada'] == '1') {
            $sqlListaPesosDetalle = "SELECT tp.idPesoTambo, '' AS codigo, tp.folio, tp.bruto, tp.tara, tp.neto, tp.humedad, tp.color, tp.clasificacion, f.floracion, sbr.nombre
                FROM tamboreslistapesos tp
                LEFT JOIN floraciones f ON f.idFloracion = tp.idFloracion
                LEFT JOIN sobrantes sbr ON sbr.idSobrante = tp.clasificacion
                WHERE tp.idTamborPeso = $idTamborPeso;";

            $sqlListaPesosDetalle = $con->prepare($sqlListaPesosDetalle);
            $sqlListaPesosDetalle->execute();
            $resultadoDetalle = $sqlListaPesosDetalle->fetchAll(PDO::FETCH_ASSOC);
        } else {
            $sqlTipo = $con->prepare("SELECT clasificacion, folio FROM tamboreslistapesos WHERE idTamborPeso = $idTamborPeso");
            $sqlTipo->execute();
            $resultado = $sqlTipo->fetchAll(PDO::FETCH_ASSOC);

            foreach ($resultado as $index => $e) {
                if ($e['clasificacion'] == '0') {
                    $sqlListaPesosDetalle = "SELECT tp.idPesoTambo, '' AS codigo, tp.folio, tp.bruto, tp.tara, tp.neto, tp.humedad, tp.color, f.floracion, sbr.nombre
                FROM tamboreslistapesos tp
                LEFT JOIN floraciones f ON f.idFloracion = tp.idFloracion
                LEFT JOIN sobrantes sbr ON sbr.idSobrante = tp.clasificacion
                WHERE tp.folio = :folio;";
                    $sqlListaPesosDetalle = $con->prepare($sqlListaPesosDetalle);
                    $sqlListaPesosDetalle->bindParam(':folio', $e['folio']);
                    $sqlListaPesosDetalle->execute();
                    $e2 = $sqlListaPesosDetalle->fetch(PDO::FETCH_ASSOC);
                    $new = array_merge($e, $e2);
                    $resultadoDetalle[$index] = $new;
                } else {
                    $sqlListaPesosDetalle = "SELECT tp.idPesoTambo, CONCAT(sbr.codigo, '-', tp.folio) AS folio, tp.bruto, tp.tara, tp.neto, tp.humedad, tp.color, f.floracion, sbr.nombre, sbr.codigo
                FROM tamboreslistapesos tp
                LEFT JOIN floraciones f ON f.idFloracion = tp.idFloracion
                LEFT JOIN sobrantes sbr ON sbr.idSobrante = tp.clasificacion
                WHERE tp.folio = :folio AND tp.tipo = '1' AND clasificacion = :clasificacion;";
                    $sqlListaPesosDetalle = $con->prepare($sqlListaPesosDetalle);
                    $sqlListaPesosDetalle->bindParam(':folio', $e['folio']);
                    $sqlListaPesosDetalle->bindParam(':clasificacion', $e['clasificacion']);
                    $sqlListaPesosDetalle->execute();
                    $e2 = $sqlListaPesosDetalle->fetch(PDO::FETCH_ASSOC);
                    $new = array_merge($e, $e2);
                    $resultadoDetalle[$index] = $new;
                }
            }
        }
        return $resultadoDetalle;
    }
}

class calidad
{

    function verificacionHumedades($idAlmacenInicial, $idAlmacenFinal, $tipoDeMiel)
    {
        switch ($tipoDeMiel) {
            case '1':
                $almacen_tabla = 'almacen';
                $almacenencabezado_tabla = 'almacenencabezado';
                break;
            case '2':
                $almacen_tabla = 'almacen_organico';
                $almacenencabezado_tabla = 'almacenencabezado_organico';
                break;
        }
        $sql = "SELECT alen.fecha, al.idAlmacen, pr.nombre, lo.localidad
                FROM $almacenencabezado_tabla alen
                LEFT JOIN $almacen_tabla al ON al.idAlmacenEncabezado = alen.idAlmacen
                LEFT JOIN proveedor pr   ON pr.idProveedor = alen.idProveedor
                LEFT JOIN direccion dr   ON dr.idDireccion = pr.idDireccion
                LEFT JOIN localidades lo ON lo.idlocalidad = dr.idlocalidad
                WHERE al.idAlmacen BETWEEN $idAlmacenInicial AND $idAlmacenFinal";
        return $sql;
    }
}

class adminitrativo
{
    function reporteGeneralPrecios($idProveedor, $tmp)
    {
        if ($tmp == 1) {
            $sqlReporteGeneral = "SELECT al.idAlmacen, al.fecha, al.folio, pr.nombre, lo.localidad, count(alm.idAlmacen)registros, sum(alm.neto) kgs, al.totalCompra
            FROM almacenencabezado al 
            LEFT JOIN proveedor pr ON pr.idProveedor = al.idProveedor
            LEFT JOIN almacen alm ON al.idAlmacen = alm.idAlmacenEncabezado
            LEFT JOIN direccion dr ON pr.idDireccion = dr.idDireccion
            LEFT JOIN localidades lo ON lo.idlocalidad = dr.idlocalidad
            WHERE pr.idProveedor = $idProveedor
            group by al.idAlmacen ORDER BY al.idAlmacen DESC";
            return $sqlReporteGeneral;
        } else if ($tmp == 5) {
            $sqlReporteGeneral = "SELECT al.idAlmacen, al.fecha, al.folio, pr.nombre, lo.localidad, count(alm.idAlmacen)registros, sum(alm.neto) kgs, al.totalCompra
            FROM almacenencabezado_mantequilla al 
            LEFT JOIN proveedor pr ON pr.idProveedor = al.idProveedor
            LEFT JOIN almacen_mantequilla alm ON al.idAlmacen = alm.idAlmacenEncabezado
            LEFT JOIN direccion dr ON pr.idDireccion = dr.idDireccion
            LEFT JOIN localidades lo ON lo.idlocalidad = dr.idlocalidad
            WHERE pr.idProveedor = $idProveedor
            group by al.idAlmacen ORDER BY al.idAlmacen DESC";
            return $sqlReporteGeneral;
        }  else if ($tmp == 6) {
            $sqlReporteGeneral = "SELECT al.idAlmacen, al.fecha, al.folio, pr.nombre, lo.localidad, count(alm.idAlmacen)registros, sum(alm.neto) kgs, al.totalCompra
            FROM almacenencabezado_altiplano al 
            LEFT JOIN proveedor pr ON pr.idProveedor = al.idProveedor
            LEFT JOIN almacen_altiplano alm ON al.idAlmacen = alm.idAlmacenEncabezado
            LEFT JOIN direccion dr ON pr.idDireccion = dr.idDireccion
            LEFT JOIN localidades lo ON lo.idlocalidad = dr.idlocalidad
            WHERE pr.idProveedor = $idProveedor
            group by al.idAlmacen ORDER BY al.idAlmacen DESC";
            return $sqlReporteGeneral;
        } else if ($tmp == 7) {
            $sqlReporteGeneral = "SELECT al.idAlmacen, al.fecha, al.folio, pr.nombre, lo.localidad, count(alm.idAlmacen)registros, sum(alm.neto) kgs, al.totalCompra
            FROM almacenencabezado_naranjo al 
            LEFT JOIN proveedor pr ON pr.idProveedor = al.idProveedor
            LEFT JOIN almacen_naranjo alm ON al.idAlmacen = alm.idAlmacenEncabezado
            LEFT JOIN direccion dr ON pr.idDireccion = dr.idDireccion
            LEFT JOIN localidades lo ON lo.idlocalidad = dr.idlocalidad
            WHERE pr.idProveedor = $idProveedor
            group by al.idAlmacen ORDER BY al.idAlmacen DESC";
            return $sqlReporteGeneral;
        }
        else if ($tmp == 8) {
            $sqlReporteGeneral = "SELECT al.idAlmacen, al.fecha, al.folio, pr.nombre, lo.localidad, count(alm.idAlmacen)registros, sum(alm.neto) kgs, al.totalCompra
            FROM almacenencabezado_aguacate al 
            LEFT JOIN proveedor pr ON pr.idProveedor = al.idProveedor
            LEFT JOIN almacen_aguacate alm ON al.idAlmacen = alm.idAlmacenEncabezado
            LEFT JOIN direccion dr ON pr.idDireccion = dr.idDireccion
            LEFT JOIN localidades lo ON lo.idlocalidad = dr.idlocalidad
            WHERE pr.idProveedor = $idProveedor
            group by al.idAlmacen ORDER BY al.idAlmacen DESC";
            return $sqlReporteGeneral;
        } else if ($tmp == 9) {
            $sqlReporteGeneral = "SELECT al.idAlmacen, al.fecha, al.folio, pr.nombre, lo.localidad, count(alm.idAlmacen)registros, sum(alm.neto) kgs, al.totalCompra
            FROM almacenencabezado_mezquite al 
            LEFT JOIN proveedor pr ON pr.idProveedor = al.idProveedor
            LEFT JOIN almacen_mezquite alm ON al.idAlmacen = alm.idAlmacenEncabezado
            LEFT JOIN direccion dr ON pr.idDireccion = dr.idDireccion
            LEFT JOIN localidades lo ON lo.idlocalidad = dr.idlocalidad
            WHERE pr.idProveedor = $idProveedor
            group by al.idAlmacen ORDER BY al.idAlmacen DESC";
            return $sqlReporteGeneral;
        }else {
            $sqlReporteGeneral = "SELECT al.idAlmacen, al.fecha, al.folio, pr.nombre, lo.localidad, count(alm.idAlmacen)registros, sum(alm.neto) kgs, al.totalCompra
            FROM almacenencabezado_organico al 
            LEFT JOIN proveedor pr ON pr.idProveedor = al.idProveedor
            LEFT JOIN almacen_organico alm ON al.idAlmacen = alm.idAlmacenEncabezado
            LEFT JOIN direccion dr ON pr.idDireccion = dr.idDireccion
            LEFT JOIN localidades lo ON lo.idlocalidad = dr.idlocalidad
            WHERE pr.idProveedor = $idProveedor
            group by al.idAlmacen ORDER BY al.idAlmacen DESC";
            return $sqlReporteGeneral;
        }
    }
}


class laboratorio21
{
    function laboratorioTambos($tipoDeMiel)
    {
        include_once '../DAOConeccion/conePDO.php';
        $pdo = new conePDO();
        $con = $pdo->conectar();
        switch ($tipoDeMiel) {
            case '1':
                $almacen_tabla = 'almacen';
                $almacenencabezado_tabla = 'almacenencabezado';
                break;
            case '2':
                $almacen_tabla = 'almacen_organico';
                $almacenencabezado_tabla = 'almacenencabezado_organico';
                break;
            default:
                return;
                break;
        }

        $sql = "SELECT al.idAlmacen, pr.nombre, ale.fecha,al.bruto, al.tara, al.neto, l.localidad, pr.idSagarpa
        FROM $almacen_tabla al
        INNER JOIN $almacenencabezado_tabla ale ON  ale.idAlmacen = al.idalmacenEncabezado
        INNER JOIN proveedor pr ON pr.idProveedor = ale.idProveedor
        LEFT JOIN direccion dir ON dir.idDireccion = pr.idDireccion
        LEFT JOIN localidades l ON l.idlocalidad = dir.idlocalidad
        WHERE al.estado IN (0,1,2)
        ORDER BY al.idAlmacen";
        $sqlDetalle = $con->prepare($sql);
        $sqlDetalle->execute();
        $resultado = $sqlDetalle->fetchAll(PDO::FETCH_ASSOC);
        return $resultado;
    }

    function laboratorioResultados($tipoDeMiel, $folio)
    {
        include_once '../DAOConeccion/conePDO.php';
        $pdo = new conePDO();
        $con = $pdo->conectar();
        switch ($tipoDeMiel) {
            case '1':
                $laboratorio_tabla = 'laboratorio';
                break;
            case '2':
                $laboratorio_tabla = 'laboratorio_organico';
                break;
            default:
                return;
                break;
        }

        $sql2 = "SELECT lab.porcentaje AS porcentajeDescripcion, lab.sfDescripcion,
            lab.stDescripcion, lab.adulteracionDescripcion, lab.hmf AS procesoDescripcion, lab.color, 
            lab.colorDescripcion, rs.resultado, lab.marcaInterna, f.floracion
            FROM $laboratorio_tabla lab
            LEFT JOIN resultadofinal rs ON rs.idresultadoFinal = lab.resultadoFinal
            LEFT JOIN floraciones f ON f.idFloracion = lab.idFloracion
            WHERE lab.idAlmacen = :folio
            ORDER BY lab.idAlmacen ASC";
        $sqlDetalle2 = $con->prepare($sql2);
        $sqlDetalle2->bindParam(':folio', $folio);
        $sqlDetalle2->execute();
        if ($sqlDetalle2->rowCount() >= 1) {
            $e2 = $sqlDetalle2->fetch(PDO::FETCH_ASSOC);
        } else {
            $e2['porcentajeDescripcion'] = '';
            $e2['sfDescripcion'] = '';
            $e2['stDescripcion'] = '';
            $e2['procesoDescripcion'] = '';
            $e2['adulteracionDescripcion'] = '';
            $e2['color'] = '';
            $e2['colorDescripcion'] = '';
            $e2['resultado'] = '';
            $e2['marcaInterna'] = '';
            $e2['floracion'] = '';
        }

        return $e2;
    }

    function laboratorioLotes($tipoDeMiel, $folio)
    {
        include_once '../DAOConeccion/conePDO.php';
        $pdo = new conePDO();
        $con = $pdo->conectar();
        switch ($tipoDeMiel) {
            case '1':
                $almacen_tabla = 'almacen';
                $tamboresexperimentales_tabla = 'tamboresexperimentales';
                $tamboreslotes_tabla = 'tamboreslotes';
                break;
            case '2':
                $almacen_tabla = 'almacen_organico';
                $tamboresexperimentales_tabla = 'tamboresexperimentales_organico';
                $tamboreslotes_tabla = 'tamboreslotes_organico';
                break;
            default:
                return;
                break;
        }
        $sql3 = "SELECT te.idLoteExperimental AS exp, tl.idLoteInterno AS lote
            FROM $almacen_tabla al
            LEFT JOIN $tamboresexperimentales_tabla te ON te.folioTambor = al.idAlmacen AND te.clasificacion = 0 AND te.tipo = '0'
            LEFT JOIN $tamboreslotes_tabla tl ON tl.folioTambor = al.idAlmacen AND tl.clasificacion = 0 AND tl.tipo = '0' 
            WHERE al.idAlmacen = :folio
            ORDER BY al.idAlmacen";
        $sqlDetalle3 = $con->prepare($sql3);
        $sqlDetalle3->bindParam(':folio', $folio);
        $sqlDetalle3->execute();
        if ($sqlDetalle3->rowCount() >= 1) {
            $e3 = $sqlDetalle3->fetch(PDO::FETCH_ASSOC);
        } else {
            $e3['exp'] = '';
            $e3['lote'] = '';
        }
        return $e3;
    }
}
