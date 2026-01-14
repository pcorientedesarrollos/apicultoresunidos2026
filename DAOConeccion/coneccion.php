<?php

class Coneccion
{

    function Conectarse()
    {

        // Por defecto
        $baseDeDatos = 'apicultorescontrol';
		// $baseDeDatos = 'resp_control';

        // Si la sesión ya está creada, entonces que tome el nombre de la base de datos de la sesión
        session_start();
        if (isset($_SESSION['database'])) {
            $baseDeDatos = $_SESSION['database'];
        }
        session_write_close();

        /**OAXACA MIEL 2019 */
        // $baseDeDatos = 'apicultores2019';

        /**PASAS 2019 */
        // $baseDeDatos = 'erpasas2019';

        /**ERPOM 2019 */
        // $baseDeDatos = 'erpom2019';

        //LOCAL
        // if (!($link = mysql_connect("localhost", "root", ""))) {
        //     echo "Error conectando a la base de datos.";
        //     exit();
        // }

        //PHPMYADMIN
        // if (!($link = mysql_connect("aup.apicultoresunidos.com", "apicultores", "oaxacaMiel65"))) {
        //     echo "Error conectando a la base de datos.";
        //     exit();
        // }

        // BASE 2022 AMAZON
        if (!($link = mysql_connect("dbpcoriente.ct5sef54k5xo.us-east-2.rds.amazonaws.com", "root", "Oriente65"))) {
            echo "Error conectando a la base de datos.";
            exit();
        }

        //PRUEBAS DREAMHOST
        // if (!($link = mysql_connect("178.62.105.229", "pco", "Oriente65$"))) {
        //     echo "Error conectando a la base de datos.";
        //     exit();
        // }

        if (!mysql_select_db($baseDeDatos, $link)) {
            echo "NO SELECCIONO LA BASE DE DATOS";
            exit();
        }

        return $link;
    }


    function cerrarBd()
    {
        mysql_close();
    }
}
