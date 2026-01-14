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
        $pdo = new conePDO;
        $pdo->conectar();
        $sql = "SELECT * FROM configuracionlaboratorio WHERE idOpcionLab = 2";
        $datos = $pdo->prepare($sql);
        $datos->execute();
        if ($datos == false) {
            echo 0;
        } else {
            $caracteristicas = array();
            while ($rs = $datos->fetch()) {
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
        $datos = $pdo->prepare($sql);
        $datos->execute();
        if ($datos == false) {
            echo 0;
        } else {
            $caracteristicas = array();
            while ($rs = $datos->fetch()) {
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
        $datos = $pdo->prepare($sql);
        $datos->execute();
        if ($datos == false) {
            echo 0;
        } else {
            $caracteristicas = array();
            while ($rs = $datos->fetch()) {
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
        $datos = $pdo->prepare($sql);
        $datos->execute();
        if ($datos == false) {
            echo 0;
        } else {
            $caracteristicas = array();
            while ($rs = $datos->fetch()) {
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
        $datos = $pdo->prepare($sql);
        $datos->execute();
        if ($datos == false) {
            echo 0;
        } else {
            $caracteristicas = array();
            while ($rs = $datos->fetch()) {
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
        $datos = $pdo->prepare($sql);
        $datos->execute();
        if ($datos == false) {
            echo 0;
        } else {
            $caracteristicas = array();
            while ($rs = $datos->fetch()) {
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
        $datos = $pdo->prepare($sql);
        $datos->execute();
        if ($datos == false) {
            echo 0;
        } else {
            $caracteristicas = array();
            while ($rs = $datos->fetch()) {
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
