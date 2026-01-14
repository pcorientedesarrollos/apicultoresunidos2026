<?php

header('Access-Control-Allow-Origin: *');
header('Content-Type: text/plain');
include_once '../DAOConeccion/conexionWebServices.php';
date_default_timezone_set('America/Merida');
/*
 * Resolve values:
 *  Conection status
 *  Error
 *  Message
 *  Content
 */

function backup() {

    $pdo = new conePDO();
    // $dbh = $pdo->conectar('apicultores2019');
    // $dbh = $pdo->conectar('apicultores2020');
    // $dbh = $pdo->conectar('apicultores2021');
    // $dbh = $pdo->conectar('apicultores2022');
    // $dbh = $pdo->conectar('apicultores2023');
    $dbh = $pdo->conectar('apicultores2024');
    // $bd = "erpasas";
    // $bd = "erpom";
    // $bd = "apicultores2018";
    // $bd = "apicultores2019";
    // $bd = "apicultores2020";
    // $bd = "apicultores2021";
    // $bd = "apicultores2022";
    // $bd = "apicultores2023";
    $bd = "apicultores2024";
    $nombre = 'DB ' . $bd . ' - Respaldo al ' . date('d-m-Y') . ' a las ' . date("h_i_s A") . '.sql';

    #Start the queries

    $drop = true;
    $tablas = false;

    /* Se busca las tablas en la base de datos */
    if (empty($tablas)) {
        $consulta = "SHOW TABLES FROM $bd;";
        $respuesta = $dbh->prepare($consulta);
        $respuesta->execute();
        $resultado = $respuesta->fetchAll(PDO::FETCH_COLUMN);
        $tablas = $resultado;
    }

    /* Se crea la cabecera del archivo */
    $info['dumpversion'] = "1.1b";
    $info['fecha'] = date("d-m-Y");
    $info['hora'] = date("h:m:s A");
    $info['mysqlver'] = PDO::ATTR_SERVER_INFO;
    $info['phpver'] = phpversion();


    ob_start(); //Abrir el búfer de salida
#    print_r($tablas);


    $representacion = ob_get_contents(); //Obtener el contenido del buffer
    ob_end_clean(); //Limpiar el buffer

    preg_match_all('/(\[\d+\] => .*)\n/', $representacion, $matches);

    $info['tablas'] = implode(";  ", $matches[1]);

    $dump = <<<EOT
# +===================================================================
# |
# | Generado el {$info['fecha']} a las {$info['hora']} 
# | Servidor: {$_SERVER['HTTP_HOST']}
# | MySQL Version: {$info['mysqlver']}
# | PHP Version: {$info['phpver']}
# | Base de datos: '$bd'
# | Tablas: {$info['tablas']}
# |
# +-------------------------------------------------------------------
 
EOT;

    foreach ($tablas as $tabla) {

        $drop_table_query = "";
        $create_table_query = "";
        $insert_into_query = "";

        /* Se halla el query que será capaz vaciar la tabla. */
        if ($drop) {
            $drop_table_query = "DROP TABLE IF EXISTS `$tabla`;";
        } else {
            $drop_table_query = "# No especificado.";
        }

        /* Se halla el query que será capaz de recrear la estructura de la tabla. */
        $create_table_query = "";
        $consulta = "SHOW CREATE TABLE `$tabla`;";
        $respuesta = $dbh->prepare($consulta);
        $respuesta->execute();
        $fila = $respuesta->fetch(PDO::FETCH_NUM);
        $create_table_query = $fila[1] . ";";

        /* Se halla el query que será capaz de insertar los datos. */
        $insert_into_query = "";
        $consulta = "SELECT * FROM `$tabla`;";
        $respuesta = $dbh->prepare($consulta);
        $respuesta->execute();
        $filas = $respuesta->fetchAll(PDO::FETCH_ASSOC);

        foreach ($filas as $fila) {
            $columnas = array_keys($fila);
            foreach ($columnas as $columna) {
                if (gettype($fila[$columna]) == "NULL") {
                    $values[] = "NULL";
                } else {
                    $values[] = $dbh->quote($fila[$columna]);
                }
            }
            $insert_into_query .= "INSERT INTO `$tabla` VALUES (" . implode(", ", $values) . ");\n";
            unset($values);
        }


        $dump .= <<<EOT
 
# | Vaciado de tabla '$tabla'
# +------------------------------------->
$drop_table_query
 
 
# | Estructura de la tabla '$tabla'
# +------------------------------------->
$create_table_query
 
 
# | Carga de datos de la tabla '$tabla'
# +------------------------------------->
$insert_into_query
 
EOT;
    }


    /* Envio */
    if (!headers_sent()) {
        header("Content-Transfer-Encoding: binary");
        echo json_encode([
            'ConectionStatus' => 'Conected',
            'Error' => false,
            'Filename' => $nombre,
            'FileContent' => $dump
        ]);
    } else {
        echo json_encode([
            'ConectionStatus' => 'Conected',
            'Error' => true,
            'Message' => 'Headers already sent',
            'Content' => 'Cannot display information.'
        ]);
    }
}

#Access control

if (isset($_POST['accessKey'])) {
    $key = '8ca4c7322b882552296176913fde7efa';
    $gotKey = $_POST['accessKey'];
    if ($gotKey === $key) {
        backup();
    } else {
        echo json_encode([
            'ConectionStatus' => 'Conected',
            'Error' => true,
            'Message' => 'Incorrect Access Key',
            'Content' => 'Refused Key: ' . $gotKey
        ]);
    }
} else {
    echo json_encode([
        'ConectionStatus' => 'Refused',
        'Error' => true,
        'Message' => 'Cannot conect to the service',
        'Content' => 'Refused conection'
    ]);
}

