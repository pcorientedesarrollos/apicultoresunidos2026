<?php

include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$con = $pdo->conectar();


/**
 * Revisa el arreglo que recibe y eliminar si hay algun elemento repetido
 */
function ordenarSubcuentas($array)
{

    $resultado_subcuentas = array();
    $ids_subcuenta = array();

    foreach ($array as $subcuenta) {
        if (!in_array($subcuenta['idSubcuenta'], $ids_subcuenta)) {
            array_push($ids_subcuenta, $subcuenta['idSubcuenta']);
            array_push($resultado_subcuentas, $subcuenta);
        }
    }

    return $resultado_subcuentas;


}

/**
 * Revisa el arreglo que recibe y eliminar si hay algun elemento repetido
 */
function ordenarCuentas($array)
{

    $resultado_cuentas = array();
    $ids_cuentas = array();

    foreach ($array as $cuenta) {
        if (!in_array($cuenta['idCuentaConcepto'], $ids_cuentas)) {
            array_push($ids_cuentas, $cuenta['idCuentaConcepto']);
            array_push($resultado_cuentas, $cuenta);
        }
    }

    return $resultado_cuentas;


}


// Este programa obtiene los conceptos que aparecen en el catálogo de productos

// Se debe modificar para que obtenga las cuentas, cuyos conceptos tengan al menos un subconcepto que tenga configurados precios
// Tablas a usar:
// cuentas, subcuentas y subsubconceptos


// Necesita seleccionar las subsubcuentas que tienen precio

try {

    $sqlSeleccionaSubsubcuentas = $con->prepare("SELECT ss.*, udm.nombre as nombreUnidad
    FROM subsubcuentas ss
    LEFT JOIN unidadesdemedida udm ON ss.unidad = udm.idUnidad
    WHERE precio IS NOT NULL AND precio = 1 AND ss.ocultar = 0
    ORDER BY subSubcuenta ASC");
    $sqlSeleccionaSubsubcuentas->execute();

    if (!$sqlSeleccionaSubsubcuentas) {
        throw new Exception($con->errorInfo());
    }

    $lista_de_subsubcuentas = $sqlSeleccionaSubsubcuentas->fetchAll(PDO::FETCH_ASSOC);

} catch (Exception $e) {
    echo json_encode(['error' => true, 'message' => $e->getMessage()]);
}

// Una vez con todas las subsubcuentas, obtener las subcuentas sin que se repitan

try {

    // Por cada subsubcuenta se va a tomar la propiedad idSubcuenta y se va a obtener la subcuenta
    // Se creará una lista de todas las subcuentas

    $lista = array();

    foreach ($lista_de_subsubcuentas as $subsub) {
        // $subsub['idSubcuenta']

        $sqlSeleccionaSubcuentas = $con->prepare("SELECT *
        FROM subcuentas
        WHERE idSubcuenta = :idSubcuenta ORDER BY subcuenta ASC");

        $sqlSeleccionaSubcuentas->bindParam(':idSubcuenta', $subsub['idSubcuenta']);
        $sqlSeleccionaSubcuentas->execute();

        if (!$sqlSeleccionaSubcuentas) {
            throw new Exception($con->errorInfo());
        }

        array_push($lista, $sqlSeleccionaSubcuentas->fetch(PDO::FETCH_ASSOC));

    }

    $lista_de_subcuentas = ordenarSubcuentas($lista);



} catch (Exception $e) {
    echo json_encode(['error' => true, 'message' => $e->getMessage()]);
}

// Una vez con las subcuentas, obtener las cuentass sin que se repitan


try {

    $lista = array();

    foreach ($lista_de_subcuentas as $subcuenta) {
        // $subcuenta['idCuentaConcepto']
        $sqlSeleccionaCuenta = $con->prepare("SELECT * FROM cuentas
        WHERE idCuentaConcepto = :idCuentaConcepto AND ingresoEgreso = 0");
        $sqlSeleccionaCuenta->bindParam(':idCuentaConcepto', $subcuenta['idCuentaConcepto']);
        $sqlSeleccionaCuenta->execute();

        if (!$sqlSeleccionaCuenta) {
            throw new Exception($con->errorInfo());
        }

        array_push($lista, $sqlSeleccionaCuenta->fetch(PDO::FETCH_ASSOC));
    }

    $lista_cuentas = ordenarCuentas($lista);

} catch (Exception $e) {
    echo json_encode(['error' => true, 'message' => $e->getMessage()]);
}


// Hay que insertar cada elemento de los arreglos a donde pertenece, 
// las cuentas con sus subcuentas y las subcuentas con sus subsubcuentas
$resultado = array();

foreach ($lista_cuentas as $index_cuenta => $cuenta) {
    $cuenta['subcuentas'] = array();

    foreach ($lista_de_subcuentas as $index_subcuenta => $subcuenta) {
            $subcuenta['subsubcuentas'] = array();

        foreach ($lista_de_subsubcuentas as $index_subsub => $subsubcuenta) {


            // Si el id de la subcuenta coincide con la subcuenta que se está revisando, se añade
            // Y se elimina del arreglo
            if ($subsubcuenta['idSubcuenta'] == $subcuenta['idSubcuenta']) {
                array_push($subcuenta['subsubcuentas'], $subsubcuenta);
                // unset($lista_de_subsubcuentas[$index_subsub]);
            }



        }

        if ($subcuenta['idCuentaConcepto'] == $cuenta['idCuentaConcepto']) {
            array_push($cuenta['subcuentas'], $subcuenta);
            // unset($lista_de_subcuentas[$index_subcuenta]);
        }
    }

    array_push($resultado, $cuenta);
}




// Fin

echo json_encode(['error' => false, 'data' => $resultado]);

// try {
//     $catalogos = array();
//     $sqlSeleccionaCatalogos = $con->prepare("SELECT idConceptoCC, concepto FROM `conceptoscajachica` WHERE catalogo = 1;");
//     $sqlSeleccionaCatalogos->execute();
//     if ($sqlSeleccionaCatalogos == false) {
//         throw new Exception($con->errorInfo());
//     }
//     $catalogos = $sqlSeleccionaCatalogos->fetchAll(PDO::FETCH_ASSOC);
//     echo json_encode(['error' => false, 'catalogos' => $catalogos]);
// } catch (Exception $e) {
//     echo json_encode(['error' => true, 'message' => $e->getMessage()]);
// }
