<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of DameCaracteristica
 *
 * @author pablo temporal
 */
class DameCaracteristica {

    function obtenerValorSf() {
        $cn = new Coneccion();
        $cn->Conectarse();
        $sql = "SELECT * FROM configuracionlaboratorio WHERE idOpcionLab = 2";
        $datos = mysql_query($sql);
        if ($datos == false) {
            echo 0;
        } else {
            $caracteristicas = array();
            while ($rs = mysql_fetch_array($datos)) {
                $opciones = new stdClass();
                $opciones->rango1 = $rs["rango1"];
                $opciones->rango2 = $rs["rango2"];
                $opciones->signo = $rs["signo"];
                $opciones->descripcion = $rs["descripcion"];
                $caracteristicas[] = $opciones;
            }
            return $caracteristicas;
        }
    }

    function obtenerValorSt() {
        $sql = "SELECT * FROM configuracionlaboratorio WHERE idOpcionLab = 3";
        $datos = mysql_query($sql);
        if ($datos == false) {
            echo 0;
        } else {
            $caracteristicas = array();
            while ($rs = mysql_fetch_array($datos)) {
                $opciones = new stdClass();
                $opciones->rango1 = $rs["rango1"];
                $opciones->rango2 = $rs["rango2"];
                $opciones->signo = $rs["signo"];
                $opciones->descripcion = $rs["descripcion"];
                $caracteristicas[] = $opciones;
            }
            return $caracteristicas;
        }
    }

    function obtenerValoresPorcentaje() {
        $sql = "SELECT * FROM configuracionlaboratorio WHERE idOpcionLab = 1";
        $datos = mysql_query($sql);
        if ($datos == false) {
            echo 0;
        } else {
            $caracteristicas = array();
            while ($rs = mysql_fetch_array($datos)) {
                $opciones = new stdClass();
                $opciones->rango1 = $rs["rango1"];
                $opciones->rango2 = $rs["rango2"];
                $opciones->signo = $rs["signo"];
                $opciones->descripcion = $rs["descripcion"];
                $caracteristicas[] = $opciones;
            }
            return $caracteristicas;
        }
    }

    function obtenerValoresAdulteracion() {
        $sql = "SELECT * FROM configuracionlaboratorio WHERE idOpcionLab = 4";
        $datos = mysql_query($sql);
        if ($datos == false) {
            echo 0;
        } else {
            $caracteristicas = array();
            while ($rs = mysql_fetch_array($datos)) {
                $opciones = new stdClass();
                $opciones->rango1 = $rs["rango1"];
                $opciones->rango2 = $rs["rango2"];
                $opciones->signo = $rs["signo"];
                $opciones->descripcion = $rs["descripcion"];
                $caracteristicas[] = $opciones;
            }
            return $caracteristicas;
        }
    }

    function obtenerValoresHmf() {
        $sql = "SELECT * FROM configuracionlaboratorio WHERE idOpcionLab = 5";
        $datos = mysql_query($sql);
        if ($datos == false) {
            echo 0;
        } else {
            $caracteristicas = array();
            while ($rs = mysql_fetch_array($datos)) {
                $opciones = new stdClass();
                $opciones->rango1 = $rs["rango1"];
                $opciones->rango2 = $rs["rango2"];
                $opciones->signo = $rs["signo"];
                $opciones->descripcion = $rs["descripcion"];
                $caracteristicas[] = $opciones;
            }
            return $caracteristicas;
        }
    }
    
    function obtenerValorColor() {
        $sql = "SELECT * FROM configuracionlaboratorio WHERE idOpcionLab = 6";
        $datos = mysql_query($sql);
        if ($datos == false) {
            echo 0;
        } else {
            $caracteristicas = array();
            while ($rs = mysql_fetch_array($datos)) {
                $opciones = new stdClass();
                $opciones->rango1 = $rs["rango1"];
                $opciones->rango2 = $rs["rango2"];
                $opciones->signo = $rs["signo"];
                $opciones->descripcion = $rs["descripcion"];
                $caracteristicas[] = $opciones;
            }
            return $caracteristicas;
        }
    }
    
    function obtenerValorTt() {
        $sql = "SELECT * FROM configuracionlaboratorio WHERE idOpcionLab = 7";
        $datos = mysql_query($sql);
        if ($datos == false) {
            echo 0;
        } else {
            $caracteristicas = array();
            while ($rs = mysql_fetch_array($datos)) {
                $opciones = new stdClass();
                $opciones->rango1 = $rs["rango1"];
                $opciones->rango2 = $rs["rango2"];
                $opciones->signo = $rs["signo"];
                $opciones->descripcion = $rs["descripcion"];
                $caracteristicas[] = $opciones;
            }
            return $caracteristicas;
        }
    }

}
