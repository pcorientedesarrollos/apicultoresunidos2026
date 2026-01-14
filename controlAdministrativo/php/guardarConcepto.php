<?php
include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$cn = $pdo->conectar();

$json = file_get_contents("php://input");

try {

    if (!$json) {
        throw new Exception('No se recibieron parámetros');
    } else {
        $info = json_decode($json);
    }

    $cn->beginTransaction();

    if (isset($info[0]->idSubcuenta)) {

        $sql = "UPDATE subcuentas SET subcuenta = :subcuenta, deudores = :deudores WHERE idSubcuenta = :idSubcuenta";
        $dato = $cn->prepare($sql);
        $dato->bindParam(':subcuenta', $info[0]->subcuenta);
        $dato->bindParam(':idSubcuenta', $info[0]->idSubcuenta);
        $dato->bindParam(':deudores', $info[0]->deudores);
        $dato->execute();
        if ($dato == false) {
            throw new Exception($cn - errorInfo());
        }

        //CREAMOS NUEVA SUBCUENTA SI EL ID EXISTE O ACTUALIZAMOS LA INFORMACIÓN SI EL ID EXISTE
        if (count($info[1]) > 0) {
            foreach ($info[1] as $subSubcuentas) {
                if ($subSubcuentas->idSubSubcuenta == 0) {
                    $sqlSubSub = $cn->prepare("INSERT INTO subsubcuentas (subSubcuenta, idSubcuenta, precio, precioUnitario , unidad , peso , clave, min, max) VALUES (:subSubcuenta, :idSubcuenta, :precio, :precioUnitario, :unidad, :peso , :clave, :min, :max)");
                    $sqlSubSub->bindParam(':subSubcuenta', $subSubcuentas->subSubcuenta);
                    $sqlSubSub->bindParam(':idSubcuenta', $info[0]->idSubcuenta);
                    $sqlSubSub->bindParam(':precio', $subSubcuentas->precio);
                    $sqlSubSub->bindParam(':precioUnitario', $subSubcuentas->precioUnitario);
                    $sqlSubSub->bindParam(':unidad', $subSubcuentas->unidad);
                    $sqlSubSub->bindParam(':peso', $subSubcuentas->peso);
                    $sqlSubSub->bindParam(':clave', $subSubcuentas->clave);
                    $sqlSubSub->bindParam(':min', $subSubcuentas->min);
                    $sqlSubSub->bindParam(':max', $subSubcuentas->max);
                    $sqlSubSub->execute();
                    if ($sqlSubSub == false) {
                        throw new Exception($cn->errorInfo());
                    }
                } else {
                    $sqlSubSub = $cn->prepare("UPDATE subsubcuentas SET subSubcuenta = :subSubcuenta, precio = :precio, precioUnitario = :precioUnitario, unidad = :unidad, peso = :peso, clave = :clave, min = :min, max = :max WHERE idSubSubcuenta = :idSubSubcuenta");
                    $sqlSubSub->bindParam(':subSubcuenta', $subSubcuentas->subSubcuenta);
                    $sqlSubSub->bindParam(':precio', $subSubcuentas->precio);
                    $sqlSubSub->bindParam(':precioUnitario', $subSubcuentas->precioUnitario);
                    $sqlSubSub->bindParam(':unidad', $subSubcuentas->unidad);
                    $sqlSubSub->bindParam(':peso', $subSubcuentas->peso);
                    $sqlSubSub->bindParam(':idSubSubcuenta', $subSubcuentas->idSubSubcuenta);
                    $sqlSubSub->bindParam(':clave', $subSubcuentas->clave);
                    $sqlSubSub->bindParam(':min', $subSubcuentas->min);
                    $sqlSubSub->bindParam(':max', $subSubcuentas->max);

                    $sqlSubSub->execute();
                    if ($sqlSubSub == false) {
                        throw new Exception($cn->errorInfo());
                    }
                }
            }
        }

    } else {

        $sql = "INSERT INTO subcuentas (subcuenta, idCuentaConcepto) VALUES (:subcuenta, :idCuentaConcepto)";
        $dato = $cn->prepare($sql);
        $dato->bindParam(':subcuenta', $info[0]->subcuenta);
        $dato->bindParam(':idCuentaConcepto', $info[0]->idCuentaConcepto);
        $dato->execute();

        if ($dato == false) {
            throw new Exception($cn - errorInfo());
        }
        $idSubcuenta = $cn->lastInsertId();

        if (count($info[1]) > 0) {
            foreach ($info[1] as $subSubcuentas) {
                $sqlSubSub = $cn->prepare("INSERT INTO subsubcuentas (subSubcuenta, idSubcuenta, precio, precioUnitario, unidad , peso , clave, min, max) VALUES (:subSubcuenta, :idSubcuenta, :precio, :precioUnitario, :unidad, :peso , :clave, :min, :max)");
                $sqlSubSub->bindParam(':subSubcuenta', $subSubcuentas->subSubcuenta);
                $sqlSubSub->bindParam(':precio', $subSubcuentas->precio);
                $sqlSubSub->bindParam(':precioUnitario', $subSubcuentas->precioUnitario);
                $sqlSubSub->bindParam(':unidad', $subSubcuentas->unidad);
                $sqlSubSub->bindParam(':peso', $subSubcuentas->peso);
                $sqlSubSub->bindParam(':idSubcuenta', $idSubcuenta);
                $sqlSubSub->bindParam(':clave', $subSubcuentas->clave);
                $sqlSubSub->bindParam(':min', $subSubcuentas->min);
                $sqlSubSub->bindParam(':max', $subSubcuentas->max);
                $sqlSubSub->execute();
                if ($sqlSubSub == false) {
                    throw new Exception($cn->errorInfo());
                }
            }
        }

    }

    $cn->commit();
    echo json_encode(['error' => false, 'message' => 'Consulta realizada']);
} catch (Exception $e) {
    $cn->rollBack();
    echo json_encode(['error' => true, 'message' => $e->getMessage() . ' Linea: ' . $e->getLine()]);
}




?>