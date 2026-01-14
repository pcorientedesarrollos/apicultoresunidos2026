<?php

include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$con = $pdo->conectar();

//TAMBORES EXPERIMENTALES
if(isset($_GET['experimental'])){
    
    switch($_GET['miel']){
        case '1':
            $experimental = 'experimental';
        break;
        case '2':
            $experimental = 'experimental_organico';
        break;
    }

    try{ 
        $idLoteExperimental = $_GET["idLoteExperimental"];
        $numeroDeTambores = $_GET["numeroDeTambores"];
        $kilosTotales = $_GET["kilosTotales"];
        $con->beginTransaction();

        $sql = "UPDATE $experimental SET numeroDeTambores = :numeroDeTambores, kilosTotales = :kilosTotales WHERE idLoteExperimental = :idLoteExperimental";
        $dats = $con->prepare($sql);
        $dats->bindParam(':numeroDeTambores', $numeroDeTambores);
        $dats->bindParam(':kilosTotales', $kilosTotales);
        $dats->bindParam(':idLoteExperimental', $idLoteExperimental);
        $dats->execute();
        if ($dats == false) {
            throw new Exception($con->errorInfo());
        }
        
        $con->commit();
        echo json_encode(['error' => false, 'message' => 'Consulta realizada']);

    } catch (Exception $e) {
        $con->rollBack();
        echo json_encode(['error' => true, 'message' => $e->getMessage() . ' Linea: ' . $e->getLine()]);
    }
}
exit();

// if (isset($_GET["organica"]) && isset($_GET['experimental'])) {

//     $idLoteExperimental = $_GET["idLoteExperimental"];
//     $numeroDeTambores = $_GET["numeroDeTambores"];
//     $kilosTotales = $_GET["kilosTotales"];

//     $sql = "UPDATE experimental_organico SET numeroDeTambores = :numeroDeTambores, kilosTotales = :kilosTotales WHERE experimental_organico.idLoteExperimental = :idLoteExperimental";
//     $dats = $con->prepare($sql);
//     $dats->bindParam(':numeroDeTambores', $numeroDeTambores);
//     $dats->bindParam(':kilosTotales', $kilosTotales);
//     $dats->bindParam(':idLoteExperimental', $idLoteExperimental);
//     $dats->execute();
// } else if(isset($_GET['idLoteExperimental'])){
//     $idLoteExperimental = $_GET["idLoteExperimental"];
//     $numeroDeTambores = $_GET["numeroDeTambores"];
//     $kilosTotales = $_GET["kilosTotales"];

//     $sql = "UPDATE experimental SET numeroDeTambores = :numeroDeTambores, kilosTotales = :kilosTotales WHERE experimental.idLoteExperimental = :idLoteExperimental";
//     $dats = $con->prepare($sql);
//     $dats->bindParam(':numeroDeTambores', $numeroDeTambores);
//     $dats->bindParam(':kilosTotales', $kilosTotales);
//     $dats->bindParam(':idLoteExperimental', $idLoteExperimental);
//     $dats->execute();
// }

//TAMBORES DE LOTE INTERNO
if (isset($_GET["organica"]) && isset($_GET['idLoteInterno'])) {
    $sql = "UPDATE calidad_organico SET numeroDeTambores = :numeroDeTambores, kilosTotales = :kilosTotales WHERE idLoteInterno = :idLoteInterno";
    $dats = $con->prepare($sql);
    $dats->bindParam(':numeroDeTambores', $numeroDeTambores);
    $dats->bindParam(':kilosTotales', $kilosTotales);
    $dats->bindParam(':idLoteInterno', $_GET['idLoteInterno']);
    $dats->execute();
} else if (isset($_GET['idLoteInterno'])) {
    $sql = "UPDATE calidad SET numeroDeTambores = :numeroDeTambores, kilosTotales = :kilosTotales WHERE idLoteInterno = :idLoteInterno";
    $dats = $con->prepare($sql);
    $dats->bindParam(':numeroDeTambores', $numeroDeTambores);
    $dats->bindParam(':kilosTotales', $kilosTotales);
    $dats->bindParam(':idLoteInterno', $_GET['idLoteInterno']);
    $dats->execute();
}

?>