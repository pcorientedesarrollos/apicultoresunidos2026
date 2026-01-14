<?php

// Incluye este archivo en otro PHP, llama a la funcion `calcularNotaCajaChica()`
// pasándole la variable de conexion y el idCajaChica correspondiente, la función retornará un objeto de tipo:
// { error: bool, message: string, total: number }

function calcularNotaCajaChica($con, $idCajaChica)
{   

    if(!isset($idCajaChica)){
        return ['error'=>true, 'message'=>'Debes enviar el idCajaChica como un argumento'];
    }

    try{

        $sql_tipoCajaChica = $con->prepare("SELECT tipo, total FROM cajachica WHERE idCajaChica = :idCajaChica");
        $sql_tipoCajaChica->bindParam(':idCajaChica', $idCajaChica);
        $sql_tipoCajaChica->execute();
        $res = $sql_tipoCajaChica->fetch(PDO::FETCH_ASSOC);
        $totalDelMovimiento = 0;
        if($res['tipo'] == '0'){
            $sql = $con->prepare("SELECT importe as cantidad FROM cajachicadetalle WHERE idCajaChica = :idCajaChica");
        } else {
            $sql = $con->prepare("SELECT cantidad as cantidad FROM cajachicadetalle WHERE idCajaChica = :idCajaChica");
        }

        $sql->bindParam(':idCajaChica', $idCajaChica);
        $sql->execute();

        if($sql->rowCount() >= 1) { 
            #Actualizar el campo total de caja chica encabezado

            foreach($sql->fetchAll(PDO::FETCH_ASSOC) as $detalle) {
                $totalDelMovimiento += $detalle['cantidad'];
            };

        }

        #Actualizar el campo total de caja chica encabezado

        if ($res['total'] != $totalDelMovimiento){
            $sqlUpdate = $con->prepare("UPDATE cajachica SET total = :total WHERE idCajaChica = :idCajaChica");
            $sqlUpdate->bindParam(':total', $totalDelMovimiento);
            $sqlUpdate->bindParam(':idCajaChica', $idCajaChica);
            $sqlUpdate->execute();
            if($sqlUpdate->rowCount() == 1) {
                return ['error'=>false, 'message'=>'Se actualizó el total de caja chica', 'total'=>$totalDelMovimiento];
            } else {
                throw new Exception('No se pudo actualizar el encabezado de caja chica');
            }
        } else {
            return ['error'=>false, 'message'=>'No se actualizó el total de caja chica', 'total'=>$totalDelMovimiento];
        }
    } catch(Exception $e){
        return ['error'=>true, 'message'=>$e->getMessage()];
    }
    


    
}
