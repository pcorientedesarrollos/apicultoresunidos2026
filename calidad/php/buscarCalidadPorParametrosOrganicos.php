<?php

include_once '../../DAOConeccion/coneccion.php';
$cn = new Coneccion();
$cn->Conectarse();
$json = file_get_contents("php://input");
$datos = json_decode($json);
$info = $datos->valor;
$contadorResultadoFinal = 1;
$contadorC13 = 1;
$contadorPorcentaje = 1;
$contadorSt = 1;
$contadorSf = 1;
$contadorHmf = 1;
$contadorFloracion = 1;
$contadorLocalidad = 1;
$longitud = 0;
$longitudListaC13 = 0;
$longitudListaPorcentaje = 0;
$longitudListaSt = 0;
$longitudListaSf = 0;
$longitudListaHmf = 0;
$longitudFloracion = 0;
$longitudLocalidad = 0;
$informacionArrglos = false;
if (isset($info->listaResultadosFinales)) {
    $longitud = count($info->listaResultadosFinales);
    if (count($info->listaResultadosFinales) > 0) {
        $sqlResulFinal = "SELECT idresultadoFinal FROM resultadofinal WHERE ";
        foreach ($info->listaResultadosFinales as $resultados) {
            $informacionArrglos = true;
            $sqlResulFinal.=" idresultadoFinal = '" . $resultados . "' ";
            if ($contadorResultadoFinal < $longitud) {
                $sqlResulFinal.=" or ";
            }
//        echo $proveedor;
            $contadorResultadoFinal++;
        }
    }
}
if (isset($info->listaC13)) {
    $longitudListaC13 = count($info->listaC13);
    if (count($info->listaC13) > 0) {
        $sqlC13 = "SELECT adulteracionDescripcion FROM laboratorio_organico WHERE ";
        foreach ($info->listaC13 as $c13) {
            $informacionArrglos = true;
            $sqlC13.=" adulteracionDescripcion = '" . $c13 . "' ";
            if ($contadorC13 < $longitudListaC13) {
                $sqlC13.=" or ";
            }
            $contadorC13++;
        }
    }
}


if (isset($info->listaPorcentaje)) {
    $longitudListaPorcentaje = count($info->listaPorcentaje);
    if (count($info->listaPorcentaje) > 0) {
        $sqlPorcentaje = "SELECT porcentaje FROM laboratorio_organico WHERE ";
        foreach ($info->listaPorcentaje as $porcentaje) {
            $informacionArrglos = true;
            if ($porcentaje == "Por rangos") {
                $rango = $info->rangosPorcentaje;
                $rango->rango1;
                $rango->rango2;
                $sqlPorcentaje.="porcentaje BETWEEN '" . $rango->rango1 . "' AND '" . $rango->rango2 . "'";
            } else {
                $sqlPorcentaje.=" porcentaje = '$porcentaje' ";
                if ($contadorPorcentaje < $longitudListaPorcentaje) {
                    $sqlPorcentaje.=" or ";
                }
                $contadorPorcentaje++;
            }
        }
    }
}
if (isset($info->listaSt)) {
    $longitudListaSt = count($info->listaSt);
    if (count($info->listaSt) > 0) {
        $sqlSt = "SELECT st FROM laboratorio_organico WHERE ";
        foreach ($info->listaSt as $st) {
            $informacionArrglos = true;
            $sqlSt .=" stDescripcion = '" . $st . "'";
            if ($contadorSt < $longitudListaSt) {
                $sqlSt.=" or ";
            }
            $contadorSt++;
        }
    }
}

if (isset($info->listaSf)) {
    $longitudListaSf = count($info->listaSf);
    if (count($info->listaSf) > 0) {
        $sqlSf = "SELECT sf FROM laboratorio_organico WHERE ";
        foreach ($info->listaSf as $st) {
            $informacionArrglos = true;
            $sqlSf .=" sfDescripcion = '" . $st . "'";
            if ($contadorSf < $longitudListaSf) {
                $sqlSf.=" or ";
            }
            $contadorSf++;
        }
    }
}

if (isset($info->listaHmf)) {
    $longitudListaHmf = count($info->listaHmf);
    if (count($info->listaHmf) > 0) {
        $sqlHmf = "SELECT hmf FROM laboratorio_organico WHERE ";
        foreach ($info->listaHmf as $hmf) {
            $informacionArrglos = true;
            if ($hmf == "Por rangos") {
                $rango = $info->rangosHmf;
                $rango->rango1;
                $rango->rango2;
                $sqlHmf.="hmf BETWEEN '" . $rango->rango1 . "' AND '" . $rango->rango2 . "'";
            } else {
                $sqlHmf .=" hmf = '" . $hmf . "'";
                if ($contadorHmf < $longitudListaHmf) {
                    $sqlHmf.=" or ";
                }
                $contadorHmf++;
            }
        }
    }
}

if (isset($info->listaFloracion)) {
    $longitudFloracion = count($info->listaFloracion);
    if (count($info->listaFloracion) > 0) {
        $sqlFloracion = "SELECT fl.idFloracion FROM floraciones WHERE ";
        foreach ($info->listaFloracion as $floraciones) {
            $informacionArrglos = true;
            $sqlFloracion.=" fl.idFloracion = '" . $floraciones . "' ";
            if ($contadorFloracion < $longitudFloracion) {
                $sqlFloracion.=" or ";
            }
            $contadorFloracion++;
        }
    }
}

if (isset($info->listaLocalidad)) {
    $longitudLocalidad = count($info->listaLocalidad);
    if (count($info->listaLocalidad) > 0) {
        $sqlLocalidad = "SELECT l.idlocalidad FROM localidades WHERE ";
        foreach ($info->listaLocalidad as $localidades) {
            $informacionArrglos = true;
            $sqlLocalidad.=" l.idlocalidad = '" . $localidades . "' ";
            if ($contadorLocalidad < $longitudLocalidad) {
                $sqlLocalidad.=" or ";
            }
            $contadorLocalidad++;
        }
    }
}

$sql = "SELECT ale.fecha, al.idAlmacen, al.estado, pr.nombre, pr.idSagarpa, l.localidad, al.bruto, al.tara,
                al.neto, lab.porcentaje, lab.st, lab.porcentajeDescripcion, lab.sfDescripcion,
                lab.stDescripcion, lab.adulteracionDescripcion, lab.hmf, lab.procesoDescripcion, lab.resultadoFinal,
                rs.resultado, rs.idResultadoFinal, fl.idFloracion, fl.floracion
    FROM        almacen_organico al
    INNER JOIN  laboratorio_organico lab ON lab.idAlmacen = al.idAlmacen
    LEFT JOIN   almacenencabezado_organico ale ON  ale.idAlmacen = al.idalmacenEncabezado
    LEFT JOIN   resultadofinal rs ON rs.idresultadoFinal = lab.resultadoFinal
    LEFT JOIN   proveedor pr ON pr.idProveedor = ale.idProveedor
    LEFT JOIN   direccion dir ON dir.idDireccion = pr.idDireccion
    LEFT JOIN   localidades l ON l.idlocalidad = dir.idlocalidad
LEFT JOIN floraciones fl ON fl.idFloracion = lab.idFloracion
    WHERE ";
if ($longitud > 0) {
    $sql.="rs.idresultadoFinal in (" . $sqlResulFinal . ")";
}
if ($longitud > 0) {
    $sql.=" and";
}

if ($longitudListaPorcentaje > 0) {
    $sql.=" porcentaje in (" . $sqlPorcentaje . ")";
}
if ($longitud > 0 && $longitudListaPorcentaje > 0 || $longitudListaPorcentaje > 0) {
    $sql .=" and";
}
if ($longitudListaSt > 0) {
    $sql .=" st in (" . $sqlSt . ")";
}
if ($longitud > 0 && $longitudListaPorcentaje > 0 && $longitudListaSt > 0 || $longitudListaSt > 0) {
    $sql .=" and";
}
if ($longitudListaSf > 0) {
    $sql .=" sf in (" . $sqlSf . ")";
}
if ($longitud > 0 && $longitudListaPorcentaje > 0 && $longitudListaSt > 0 && $longitudListaSf > 0 || $longitudListaSf > 0) {
    $sql .=" and";
}
if ($longitudListaC13 > 0) {
    $sql.=" adulteracionDescripcion in (" . $sqlC13 . ")";
}
if ($longitud > 0 && $longitudListaPorcentaje > 0 && $longitudListaSt > 0 && $longitudListaSf > 0 && $longitudListaC13 > 0 || $longitudListaC13 > 0) {
    $sql .=" and";
}
if ($longitudListaHmf > 0) {
    $sql.=" hmf in (" . $sqlHmf . ")";
}
if ($longitud > 0 && $longitudListaPorcentaje > 0 && $longitudListaSt > 0 && $longitudListaSf > 0 && $longitudListaC13 > 0 && $longitudListaHmf > 0 || $longitudListaHmf > 0) {
    $sql .=" and";
}
if ($longitudFloracion > 0) {
    $sql.=" fl.idFloracion in (" . $sqlFloracion . ")";
}
if ($longitud > 0 && $longitudListaPorcentaje > 0 && $longitudListaSt > 0 && $longitudListaSf > 0 && $longitudListaC13 > 0 && $longitudListaHmf > 0 && $longitudFloracion > 0 || $longitudFloracion > 0) {
    $sql .=" and";
}
if ($longitudLocalidad > 0) {
    $sql.=" l.idlocalidad in (" . $sqlLocalidad . ")";
}
$valorCadena = substr($sql, -3);
if ($valorCadena == "and") {
//    541
    $longitud = strlen($sql) - 3;
    $sql = substr($sql, 0, $longitud);

    $nuevaLongitud = strlen($sql);
}
$valorCadena = substr($sql, -6);
if ($valorCadena == "WHERE ") {
//    541
    $longitud = strlen($sql) - 6;
    $sql = substr($sql, 0, $longitud);

    $nuevaLongitud = strlen($sql);
}
if ($informacionArrglos == true) {
    $sql .=" and al.estado = 0";
} else {
    $sql.=" WHERE al.estado = 0";
}

//echo $sql;
$datosBusqueda = mysql_query($sql);
if ($datosBusqueda == false) {
//    echo mysql_error();
    echo $sql;
} else {
    $array = array();
    $neto = 0;
    while ($rs = mysql_fetch_array($datosBusqueda)) {
//        if ($neto / 1000 > 22.5) {
//            break;
//        } else {
            $neto += $rs["neto"];
            $calidad = new stdClass();
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
            $calidad->adulteracionDescripcion = utf8_encode($rs["adulteracionDescripcion"]);
            $calidad->hmf = $rs["hmf"];
            $calidad->resultado = utf8_encode($rs["resultado"]);
            $calidad->idResultadoFinal = $rs["idResultadoFinal"];
            $calidad->idFloracion = $rs["idFloracion"];
            $calidad->floracion = utf8_encode($rs["floracion"]);
            $array[] = $calidad;
//        }
    }

    if (count($array) == 0) {
        echo 0;
    } else {
        echo json_encode($array);
    }
}