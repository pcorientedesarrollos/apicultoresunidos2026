<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Mensajes
 *
 * @author Torres
 */
class Mensajes {

    function succes($mensaje) {
        $msg = new stdClass();
        $msg->encabezado = "Exito";
        $msg->mensaje = $mensaje;
        $msg->tipo = "success";
        return $msg;
    }

    function error($mensaje) {
        $msg = new stdClass();
        $msg->encabezado = "Error";
        $msg->mensaje = $mensaje;
        $msg->tipo = "error";
        return $msg;
    }

}
