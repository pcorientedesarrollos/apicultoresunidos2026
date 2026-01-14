<?php

include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$con = $pdo->conectar();

if(isset($_GET["organica"])){
    if($_GET["organica"] == '0'){
        $query = "             
        SELECT idLoteInterno, fechaProceso, fechaEnvasado
        FROM calidad_organico
        ORDER BY idLoteInterno DESC
        ";
        $datos = $con->prepare($query);
        $datos->execute();
        
        $arrayR = array();
        while ($row = $datos->fetch()) {
            $menuFechas = new stdClass();
            $menuFechas->idLoteInterno = $row["idLoteInterno"];
            $menuFechas->fechaProceso = $row["fechaProceso"];
            $menuFechas->fechaEnvasado = $row["fechaEnvasado"];
            $arrayR[] = $menuFechas;
            if ($menuFechas->fechaProceso == "1969-12-31") {
                $menuFechas->fechaProceso = "Asignar";
            } else {
                $menuFechas->fechaProceso = $row["fechaProceso"];
            }
        
            if ($menuFechas->fechaEnvasado == "1969-12-31") {
                $menuFechas->fechaEnvasado = "Asignar";
            } else {
                $menuFechas->fechaEnvasado = $row["fechaEnvasado"];
            }
        }
        
        echo json_encode($arrayR);
    }
    if($_GET["organica"] == '1'){
        $query = "             
        SELECT idLoteInterno, fechaProceso, fechaEnvasado
        FROM calidad_mantequilla
        ORDER BY idLoteInterno DESC
        ";
        $datos = $con->prepare($query);
        $datos->execute();
        
        $arrayR = array();
        while ($row = $datos->fetch()) {
            $menuFechas = new stdClass();
            $menuFechas->idLoteInterno = $row["idLoteInterno"];
            $menuFechas->fechaProceso = $row["fechaProceso"];
            $menuFechas->fechaEnvasado = $row["fechaEnvasado"];
            $arrayR[] = $menuFechas;
            if ($menuFechas->fechaProceso == "1969-12-31") {
                $menuFechas->fechaProceso = "Asignar";
            } else {
                $menuFechas->fechaProceso = $row["fechaProceso"];
            }
        
            if ($menuFechas->fechaEnvasado == "1969-12-31") {
                $menuFechas->fechaEnvasado = "Asignar";
            } else {
                $menuFechas->fechaEnvasado = $row["fechaEnvasado"];
            }
        }
        
        echo json_encode($arrayR);
    }
    if($_GET["organica"] == '2'){
        $query = "             
        SELECT idLoteInterno, fechaProceso, fechaEnvasado
        FROM calidad_altiplano
        ORDER BY idLoteInterno DESC
        ";
        $datos = $con->prepare($query);
        $datos->execute();
        
        $arrayR = array();
        while ($row = $datos->fetch()) {
            $menuFechas = new stdClass();
            $menuFechas->idLoteInterno = $row["idLoteInterno"];
            $menuFechas->fechaProceso = $row["fechaProceso"];
            $menuFechas->fechaEnvasado = $row["fechaEnvasado"];
            $arrayR[] = $menuFechas;
            if ($menuFechas->fechaProceso == "1969-12-31") {
                $menuFechas->fechaProceso = "Asignar";
            } else {
                $menuFechas->fechaProceso = $row["fechaProceso"];
            }
        
            if ($menuFechas->fechaEnvasado == "1969-12-31") {
                $menuFechas->fechaEnvasado = "Asignar";
            } else {
                $menuFechas->fechaEnvasado = $row["fechaEnvasado"];
            }
        }
        
        echo json_encode($arrayR);
    }   if($_GET["organica"] == '3'){
        $query = "             
        SELECT idLoteInterno, fechaProceso, fechaEnvasado
        FROM calidad_naranjo
        ORDER BY idLoteInterno DESC
        ";
        $datos = $con->prepare($query);
        $datos->execute();
        
        $arrayR = array();
        while ($row = $datos->fetch()) {
            $menuFechas = new stdClass();
            $menuFechas->idLoteInterno = $row["idLoteInterno"];
            $menuFechas->fechaProceso = $row["fechaProceso"];
            $menuFechas->fechaEnvasado = $row["fechaEnvasado"];
            $arrayR[] = $menuFechas;
            if ($menuFechas->fechaProceso == "1969-12-31") {
                $menuFechas->fechaProceso = "Asignar";
            } else {
                $menuFechas->fechaProceso = $row["fechaProceso"];
            }
        
            if ($menuFechas->fechaEnvasado == "1969-12-31") {
                $menuFechas->fechaEnvasado = "Asignar";
            } else {
                $menuFechas->fechaEnvasado = $row["fechaEnvasado"];
            }
        }
        
        echo json_encode($arrayR);
    }
    if($_GET["organica"] == '4'){
        $query = "             
        SELECT idLoteInterno, fechaProceso, fechaEnvasado
        FROM calidad_aguacate
        ORDER BY idLoteInterno DESC
        ";
        $datos = $con->prepare($query);
        $datos->execute();
        
        $arrayR = array();
        while ($row = $datos->fetch()) {
            $menuFechas = new stdClass();
            $menuFechas->idLoteInterno = $row["idLoteInterno"];
            $menuFechas->fechaProceso = $row["fechaProceso"];
            $menuFechas->fechaEnvasado = $row["fechaEnvasado"];
            $arrayR[] = $menuFechas;
            if ($menuFechas->fechaProceso == "1969-12-31") {
                $menuFechas->fechaProceso = "Asignar";
            } else {
                $menuFechas->fechaProceso = $row["fechaProceso"];
            }
        
            if ($menuFechas->fechaEnvasado == "1969-12-31") {
                $menuFechas->fechaEnvasado = "Asignar";
            } else {
                $menuFechas->fechaEnvasado = $row["fechaEnvasado"];
            }
        }
        
        echo json_encode($arrayR);
    }   if($_GET["organica"] == '5'){
        $query = "             
        SELECT idLoteInterno, fechaProceso, fechaEnvasado
        FROM calidad_mezquite
        ORDER BY idLoteInterno DESC
        ";
        $datos = $con->prepare($query);
        $datos->execute();
        
        $arrayR = array();
        while ($row = $datos->fetch()) {
            $menuFechas = new stdClass();
            $menuFechas->idLoteInterno = $row["idLoteInterno"];
            $menuFechas->fechaProceso = $row["fechaProceso"];
            $menuFechas->fechaEnvasado = $row["fechaEnvasado"];
            $arrayR[] = $menuFechas;
            if ($menuFechas->fechaProceso == "1969-12-31") {
                $menuFechas->fechaProceso = "Asignar";
            } else {
                $menuFechas->fechaProceso = $row["fechaProceso"];
            }
        
            if ($menuFechas->fechaEnvasado == "1969-12-31") {
                $menuFechas->fechaEnvasado = "Asignar";
            } else {
                $menuFechas->fechaEnvasado = $row["fechaEnvasado"];
            }
        }
        
        echo json_encode($arrayR);
    }
}else{
    $query = "             
    SELECT idLoteInterno, fechaProceso, fechaEnvasado
    FROM calidad
    ORDER BY idLoteInterno DESC
    ";
    $datos = $con->prepare($query);
    $datos->execute();
    
    $arrayR = array();
    while ($row = $datos->fetch()) {
        $menuFechas = new stdClass();
        $menuFechas->idLoteInterno = $row["idLoteInterno"];
        $menuFechas->fechaProceso = $row["fechaProceso"];
        $menuFechas->fechaEnvasado = $row["fechaEnvasado"];
        $arrayR[] = $menuFechas;
        if ($menuFechas->fechaProceso == "1969-12-31") {
            $menuFechas->fechaProceso = "Asignar";
        } else {
            $menuFechas->fechaProceso = $row["fechaProceso"];
        }
    
        if ($menuFechas->fechaEnvasado == "1969-12-31") {
            $menuFechas->fechaEnvasado = "Asignar";
        } else {
            $menuFechas->fechaEnvasado = $row["fechaEnvasado"];
        }
    }
    
    echo json_encode($arrayR);
}

?>