<?php

include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$con = $pdo->conectar();

//$sql = "SELECT * 
//FROM experimental ex
//LEFT JOIN resultadofinal rf ON rf.idresultadoFinal = ex.resultadoLaboratorio
//ORDER BY ex.idLoteExperimental DESC";

if(isset($_GET["organica"])){
    $sql = "SELECT ex.*, lc.contrato
    FROM experimental_organico ex
    LEFT JOIN lotescontratados_organico lc ON lc.idLoteContratado = ex.numContrato
    ORDER BY ex.idLoteExperimental DESC";
    $datos = $con->prepare($sql);
    $datos->execute();
    
    if ($datos == false) {
        echo "Error al ingresar";
    } else {
        $arrayCalidad = array();
        while ($rs = $datos->fetch()) {
            $calidad = new stdClass();
            $calidad->idLoteExperimental = $rs["idLoteExperimental"];
            $calidad->fecha = $rs["fecha"];
            $calidad->humedad = $rs["humedad"];
            $calidad->numeroDeTambores = $rs["numeroDeTambores"];
            $calidad->kilosTotales = $rs["kilosTotales"];
            $calidad->resultadoLaboratorio = $rs["resultadoLaboratorio"];
            $calidad->contrato = $rs["contrato"];
            $arrayCalidad [] = $calidad;
         switch ($calidad->resultadoLaboratorio) {
                case '0':
                    $calidad->resultadoLaboratorio = " ";
                    break;
                case '1':
                    $calidad->resultadoLaboratorio = "Exportacion";
                    break;
                case '2':
                    $calidad->resultadoLaboratorio = "Nacional";
                    break;
                case '3':
                    $calidad->resultadoLaboratorio = "Inventario anterior";
                    break;
                default:
                    echo "";
                    break;
            }
        }
        echo json_encode($arrayCalidad);
    }
} else if(isset($_GET["mantequilla"])){
    $sql = "SELECT ex.*, lc.contrato
    FROM experimental_mantequilla ex
    LEFT JOIN lotescontratados_mantequilla lc ON lc.idLoteContratado = ex.numContrato
    ORDER BY ex.idLoteExperimental DESC";
    $datos = $con->prepare($sql);
    $datos->execute();
    
    if ($datos == false) {
        echo "Error al ingresar";
    } else {
        $arrayCalidad = array();
        while ($rs = $datos->fetch()) {
            $calidad = new stdClass();
            $calidad->idLoteExperimental = $rs["idLoteExperimental"];
            $calidad->fecha = $rs["fecha"];
            $calidad->humedad = $rs["humedad"];
            $calidad->numeroDeTambores = $rs["numeroDeTambores"];
            $calidad->kilosTotales = $rs["kilosTotales"];
            $calidad->resultadoLaboratorio = $rs["resultadoLaboratorio"];
            $calidad->contrato = $rs["contrato"];
            $arrayCalidad [] = $calidad;
         switch ($calidad->resultadoLaboratorio) {
                case '0':
                    $calidad->resultadoLaboratorio = " ";
                    break;
                case '1':
                    $calidad->resultadoLaboratorio = "Exportacion";
                    break;
                case '2':
                    $calidad->resultadoLaboratorio = "Nacional";
                    break;
                case '3':
                    $calidad->resultadoLaboratorio = "Inventario anterior";
                    break;
                default:
                    echo "";
                    break;
            }
        }
        echo json_encode($arrayCalidad);
    }
}  else if(isset($_GET["altiplano"])){
    $sql = "SELECT ex.*, lc.contrato
    FROM experimental_altiplano ex
    LEFT JOIN lotescontratados_altiplano lc ON lc.idLoteContratado = ex.numContrato
    ORDER BY ex.idLoteExperimental DESC";
    $datos = $con->prepare($sql);
    $datos->execute();
    
    if ($datos == false) {
        echo "Error al ingresar";
    } else {
        $arrayCalidad = array();
        while ($rs = $datos->fetch()) {
            $calidad = new stdClass();
            $calidad->idLoteExperimental = $rs["idLoteExperimental"];
            $calidad->fecha = $rs["fecha"];
            $calidad->humedad = $rs["humedad"];
            $calidad->numeroDeTambores = $rs["numeroDeTambores"];
            $calidad->kilosTotales = $rs["kilosTotales"];
            $calidad->resultadoLaboratorio = $rs["resultadoLaboratorio"];
            $calidad->contrato = $rs["contrato"];
            $arrayCalidad [] = $calidad;
         switch ($calidad->resultadoLaboratorio) {
                case '0':
                    $calidad->resultadoLaboratorio = " ";
                    break;
                case '1':
                    $calidad->resultadoLaboratorio = "Exportacion";
                    break;
                case '2':
                    $calidad->resultadoLaboratorio = "Nacional";
                    break;
                case '3':
                    $calidad->resultadoLaboratorio = "Inventario anterior";
                    break;
                default:
                    echo "";
                    break;
            }
        }
        echo json_encode($arrayCalidad);
    }
} else if(isset($_GET["naranjo"])){
    $sql = "SELECT ex.*, lc.contrato
    FROM experimental_naranjo ex
    LEFT JOIN lotescontratados_naranjo lc ON lc.idLoteContratado = ex.numContrato
    ORDER BY ex.idLoteExperimental DESC";
    $datos = $con->prepare($sql);
    $datos->execute();
    
    if ($datos == false) {
        echo "Error al ingresar";
    } else {
        $arrayCalidad = array();
        while ($rs = $datos->fetch()) {
            $calidad = new stdClass();
            $calidad->idLoteExperimental = $rs["idLoteExperimental"];
            $calidad->fecha = $rs["fecha"];
            $calidad->humedad = $rs["humedad"];
            $calidad->numeroDeTambores = $rs["numeroDeTambores"];
            $calidad->kilosTotales = $rs["kilosTotales"];
            $calidad->resultadoLaboratorio = $rs["resultadoLaboratorio"];
            $calidad->contrato = $rs["contrato"];
            $arrayCalidad [] = $calidad;
         switch ($calidad->resultadoLaboratorio) {
                case '0':
                    $calidad->resultadoLaboratorio = " ";
                    break;
                case '1':
                    $calidad->resultadoLaboratorio = "Exportacion";
                    break;
                case '2':
                    $calidad->resultadoLaboratorio = "Nacional";
                    break;
                case '3':
                    $calidad->resultadoLaboratorio = "Inventario anterior";
                    break;
                default:
                    echo "";
                    break;
            }
        }
        echo json_encode($arrayCalidad);
    }
}
else if(isset($_GET["aguacate"])){
    $sql = "SELECT ex.*, lc.contrato
    FROM experimental_aguacate ex
    LEFT JOIN lotescontratados_aguacate lc ON lc.idLoteContratado = ex.numContrato
    ORDER BY ex.idLoteExperimental DESC";
    $datos = $con->prepare($sql);
    $datos->execute();
    
    if ($datos == false) {
        echo "Error al ingresar";
    } else {
        $arrayCalidad = array();
        while ($rs = $datos->fetch()) {
            $calidad = new stdClass();
            $calidad->idLoteExperimental = $rs["idLoteExperimental"];
            $calidad->fecha = $rs["fecha"];
            $calidad->humedad = $rs["humedad"];
            $calidad->numeroDeTambores = $rs["numeroDeTambores"];
            $calidad->kilosTotales = $rs["kilosTotales"];
            $calidad->resultadoLaboratorio = $rs["resultadoLaboratorio"];
            $calidad->contrato = $rs["contrato"];
            $arrayCalidad [] = $calidad;
         switch ($calidad->resultadoLaboratorio) {
                case '0':
                    $calidad->resultadoLaboratorio = " ";
                    break;
                case '1':
                    $calidad->resultadoLaboratorio = "Exportacion";
                    break;
                case '2':
                    $calidad->resultadoLaboratorio = "Nacional";
                    break;
                case '3':
                    $calidad->resultadoLaboratorio = "Inventario anterior";
                    break;
                default:
                    echo "";
                    break;
            }
        }
        echo json_encode($arrayCalidad);
    }
}
else if(isset($_GET["mezquite"])){
    $sql = "SELECT ex.*, lc.contrato
    FROM experimental_mezquite ex
    LEFT JOIN lotescontratados_mezquite lc ON lc.idLoteContratado = ex.numContrato
    ORDER BY ex.idLoteExperimental DESC";
    $datos = $con->prepare($sql);
    $datos->execute();
    
    if ($datos == false) {
        echo "Error al ingresar";
    } else {
        $arrayCalidad = array();
        while ($rs = $datos->fetch()) {
            $calidad = new stdClass();
            $calidad->idLoteExperimental = $rs["idLoteExperimental"];
            $calidad->fecha = $rs["fecha"];
            $calidad->humedad = $rs["humedad"];
            $calidad->numeroDeTambores = $rs["numeroDeTambores"];
            $calidad->kilosTotales = $rs["kilosTotales"];
            $calidad->resultadoLaboratorio = $rs["resultadoLaboratorio"];
            $calidad->contrato = $rs["contrato"];
            $arrayCalidad [] = $calidad;
         switch ($calidad->resultadoLaboratorio) {
                case '0':
                    $calidad->resultadoLaboratorio = " ";
                    break;
                case '1':
                    $calidad->resultadoLaboratorio = "Exportacion";
                    break;
                case '2':
                    $calidad->resultadoLaboratorio = "Nacional";
                    break;
                case '3':
                    $calidad->resultadoLaboratorio = "Inventario anterior";
                    break;
                default:
                    echo "";
                    break;
            }
        }
        echo json_encode($arrayCalidad);
    }
}
else{
    $sql = "SELECT ex.*, lc.contrato
    FROM experimental ex
    LEFT JOIN lotescontratados lc ON lc.idLoteContratado = ex.numContrato
    ORDER BY ex.idLoteExperimental DESC";
    $datos = $con->prepare($sql);
    $datos->execute();
    
    if ($datos == false) {
        echo "Error al ingresar";
    } else {
        $arrayCalidad = array();
        while ($rs = $datos->fetch()) {
            $calidad = new stdClass();
            $calidad->idLoteExperimental = $rs["idLoteExperimental"];
            $calidad->fecha = $rs["fecha"];
            $calidad->humedad = $rs["humedad"];
            $calidad->numeroDeTambores = $rs["numeroDeTambores"];
            $calidad->kilosTotales = $rs["kilosTotales"];
            $calidad->resultadoLaboratorio = $rs["resultadoLaboratorio"];
            $calidad->contrato = $rs["contrato"];
    //        $calidad->idresultadoFinal = $rs["idresultadoFinal"];
    //        $calidad->resultado = $rs["resultado"];
    
            $arrayCalidad [] = $calidad;
    
         switch ($calidad->resultadoLaboratorio) {
                case '0':
                    $calidad->resultadoLaboratorio = " ";
                    break;
                case '1':
                    $calidad->resultadoLaboratorio = "Exportacion";
                    break;
                case '2':
                    $calidad->resultadoLaboratorio = "Nacional";
                    break;
                case '3':
                    $calidad->resultadoLaboratorio = "Inventario anterior";
                    break;
                default:
                    echo "";
                    break;
            }
        }
        echo json_encode($arrayCalidad);
    }
}


?>