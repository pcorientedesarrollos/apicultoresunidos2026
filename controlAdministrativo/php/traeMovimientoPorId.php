<?php
if (isset($_GET['idAuxiliar'])) {
    include_once '../../DAOConeccion/conePDO.php';
    $pdo = new conePDO();
    $con = $pdo->conectar();

    $respuesta = $con->prepare("SELECT idAuxiliar, idBanco, idCuenta, tipoMovimiento, fecha, hora, referencia, 
                                concepto,  cantidad, descripcion,
                         tipoDePersona, nombreDe, idTransferencia, idSubcuenta, tipoMovimiento, ingresoEgreso, idPolizaCheque, idSubsubcuenta, subsubcuenta, kg
                         FROM auxiliardebancos WHERE idAuxiliar = :idAuxiliar");
    $respuesta->bindParam(':idAuxiliar', $_GET['idAuxiliar']);
    $respuesta->execute();

    if ($respuesta->rowCount() == 1) {
        $respuesta = $respuesta->fetch(PDO::FETCH_ASSOC);

        // Verificar si el movimiento es de una transferencia, tiene que buscar el banco de la transferenci, ya se ingreso o egreso

        if ($respuesta['idTransferencia'] && $respuesta['idTransferencia'] != '0') {
            // Obtener el id del banco y la cuenta del movimiento realcionado a este 

            if ($respuesta['ingresoEgreso'] == '0') {
                // Si es un ingreso debe buscar al egreso
                $sqlSeleccionaIdRelacionado = "SELECT egreso as idAuxiliar FROM relaciondemovimientos WHERE ingreso = :valor";
            } else if ($respuesta['ingresoEgreso'] == '1') {
                // Si es un egreso debe buscar el ingreso
                $sqlSeleccionaIdRelacionado = "SELECT ingreso as idAuxiliar FROM relaciondemovimientos WHERE egreso = :valor";
            }

            $query = $con->prepare($sqlSeleccionaIdRelacionado);
            $query->bindParam(':valor', $respuesta['idAuxiliar']);
            $query->execute();

            $resultadoRelacion = $query->fetch(PDO::FETCH_ASSOC);

            // Ahora ya se tiene el idAuxiliar relacionado a la transferencia, 
            // Seleccionar el banco y cuenta de ese movimiento

            $sqlSeleccionaMovimiento = $con->prepare("SELECT idBanco, idCuenta FROM auxiliardebancos WHERE idAuxiliar = :idAuxiliar");
            $sqlSeleccionaMovimiento->bindParam(':idAuxiliar', $resultadoRelacion['idAuxiliar']);
            $sqlSeleccionaMovimiento->execute();
            $resultadoBancoTransferencia = $sqlSeleccionaMovimiento->fetch(PDO::FETCH_ASSOC);

            $respuesta['idBancoTransferencia'] = $resultadoBancoTransferencia['idBanco'];
            $respuesta['idCuentaTransferencia'] = $resultadoBancoTransferencia['idCuenta'];

        }

        echo json_encode($respuesta);
    } else {
        echo 'Error';
    }
} else {
    exit();
}