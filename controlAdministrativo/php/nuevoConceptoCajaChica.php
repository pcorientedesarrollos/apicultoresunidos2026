<?php
include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$dbh = $pdo->conectar();
$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$post = file_get_contents('php://input');

function validarDatosNuevoConcepto($nuevoConcepto)
{
    if (!isset($nuevoConcepto->concepto) || $nuevoConcepto->concepto == '') {
        return false;
    }
    if (!isset($nuevoConcepto->subconceptos)) {
        return false;
    }
    return true;
};

function validarDatosSubconceptos($subconceptos)
{
    if (!isset($subconceptos->concepto)) {
        return false;
    }
    if (!isset($subconceptos->concepto->idConceptoCC)) {
        return false;
    }
    if (!isset($subconceptos->subconceptos)) {
        return false;
    }
    return true;
};

function agregarNuevoConcepto($nuevoConcepto)
{
    global $dbh;
    try {
        $dbh->beginTransaction();
    
        if (validarDatosNuevoConcepto($nuevoConcepto)) {
            $insertarConcepto = $dbh->prepare("INSERT INTO conceptoscajachica
            (concepto) VALUES (:concepto)");
            $insertarConcepto->bindParam(':concepto', $nuevoConcepto->concepto);
            $insertarConcepto->execute();
            if ($insertarConcepto->rowCount() == 1) {
                $idDelConcepto = $dbh->lastInsertid();
                foreach ($nuevoConcepto->subconceptos as $subconcepto) {
                    if (!isset($subconcepto->nombreSc) || $subconcepto->nombreSc == '' || $subconcepto->nombreSc == '') {
                        continue;
                    }
                    $insertarSubconcepto = $dbh->prepare("INSERT INTO subconceptoscajachica
                    (subconceptoCC, idConceptoCC)
                    VALUES (:subconcepto, :idConcepto)");
                    $insertarSubconcepto->bindParam(':subconcepto', $subconcepto->nombreSc);
                    $insertarSubconcepto->bindParam(':idConcepto', $idDelConcepto);
                    $insertarSubconcepto->execute();
                    if ($insertarSubconcepto->rowCount() != 1) {
                        throw new Exception('No se insertó el subconcepto');
                    }
                };
                echo json_encode(['error'=>false, 'mensaje'=>'Se ha guardado el concepto']);
            } else {
                throw new Exception('Fallo al insertar el concepto');
            }
        } else {
            throw new Exception('No se han enviado correctamente los datos');
        }
        $dbh->commit();
    } catch (Exception $e) {
        $dbh->rollBack();
        echo json_encode(['error'=>true, 'mensaje'=>$e->getMessage()]);
    }
};

function agregarSubconceptos($subconceptos)
{
    global $dbh;
    try {
        $dbh->beginTransaction();
        if (validarDatosSubconceptos($subconceptos)) {
            foreach ($subconceptos->subconceptos as $subconcepto) {
                if (!isset($subconcepto->nombreSc) || $subconcepto->nombreSc == '' || $subconcepto->nombreSc == '') {
                    continue;
                }
                $insertarSubconcepto = $dbh->prepare("INSERT INTO subconceptoscajachica (subconceptoCC, idConceptoCC)
                VALUES (:subconceptoCC, :idConceptoCC)");
                $insertarSubconcepto->bindParam(':subconceptoCC', $subconcepto->nombreSc);
                $insertarSubconcepto->bindParam(':idConceptoCC', $subconceptos->concepto->idConceptoCC);
                $insertarSubconcepto->execute();
                if ($insertarSubconcepto->rowCount() != 1) {
                    throw new Exception('No se ha podido guardar el subconcepto');
                }
            };
            echo json_encode(['error'=>false, 'mensaje'=>'Se ha guardado los subconceptos']);
        } else {
            throw new Exception('No se ha enviado correctamente los datos');
        }
        $dbh->commit();
    } catch (Exception $e) {
        $dbh->rollBack();
        echo json_encode(['error'=>true, 'mensaje'=>$e->getMessage()]);
    }
};

if (isset($_GET['opcion']) && $post) {
    $datos = json_decode($post);
    switch ($_GET['opcion']) {
        case 'concepto':
            if (is_string($datos->concepto)) {
                agregarNuevoConcepto($datos);
            } else {
                agregarSubconceptos($datos);
            }
            break;
        // case 'subconceptos':
        //     agregarSubconceptos($datos);
        //     break;
        default:
            echo json_encode(['error'=>true, 'mensaje'=>'Opción desconocida']);
            break;
    }
} else {
    echo json_encode(['error'=>true, 'mensaje'=>'Se requiren argumentos']);
    exit();
}
