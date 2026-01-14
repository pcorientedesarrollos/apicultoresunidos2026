<?php

class DameCaracteristica {

    function __construct($conection, $configuracionlaboratorio_tabla){
        $this->conection = $conection;
        $this->configuracionlaboratorio_tabla = $configuracionlaboratorio_tabla;
    }

    function obtenerValorSf() {
        $con = $this->conection;
        $tabla = $this->configuracionlaboratorio_tabla;
        $sql = $con->prepare("SELECT * FROM $tabla WHERE idOpcionLab = 2");
        $sql->execute();
        if ($sql == FALSE) {
            throw new Exception($con->errorInfo());
        } else {
            return $sql->fetchAll(PDO::FETCH_ASSOC);
        }
    }

    function obtenerValorSt() {
        $con = $this->conection;
        $tabla = $this->configuracionlaboratorio_tabla;
        $sql = $con->prepare("SELECT * FROM $tabla WHERE idOpcionLab = 3");
        $sql->execute();
        if ($sql == FALSE) {
            throw new Exception($con->errorInfo());
        } else {
            return $sql->fetchAll(PDO::FETCH_ASSOC);
        }
    }

    function obtenerValoresPorcentaje() {
        $con = $this->conection;
        $tabla = $this->configuracionlaboratorio_tabla;
        $sql = $con->prepare("SELECT * FROM $tabla WHERE idOpcionLab = 1");
        $sql->execute();
        if ($sql == FALSE) {
            throw new Exception($con->errorInfo());
        } else {
            return $sql->fetchAll(PDO::FETCH_ASSOC);
        }
    }

    function obtenerValoresAdulteracion() {
        $con = $this->conection;
        $tabla = $this->configuracionlaboratorio_tabla;
        $sql = $con->prepare("SELECT * FROM $tabla WHERE idOpcionLab = 4");
        $sql->execute();
        if ($sql == FALSE) {
            throw new Exception($con->errorInfo());
        } else {
            return $sql->fetchAll(PDO::FETCH_ASSOC);
        }
    }

    function obtenerValoresHmf() {
        $con = $this->conection;
        $tabla = $this->configuracionlaboratorio_tabla;
        $sql = $con->prepare("SELECT * FROM $tabla WHERE idOpcionLab = 5");
        $sql->execute();
        if ($sql == FALSE) {
            throw new Exception($con->errorInfo());
        } else {
            return $sql->fetchAll(PDO::FETCH_ASSOC);
        }
    }
    
    function obtenerValorColor() {
        $con = $this->conection;
        $tabla = $this->configuracionlaboratorio_tabla;
        $sql = $con->prepare("SELECT * FROM configuracionlaboratorio WHERE idOpcionLab = 6");
        $sql->execute();
        if ($sql == FALSE) {
            throw new Exception($con->errorInfo());
        } else {
            return $sql->fetchAll(PDO::FETCH_ASSOC);
        }
    }
    
    function obtenerValorTt() {
        $con = $this->conection;
        $tabla = $this->configuracionlaboratorio_tabla;
        $sql = $con->prepare("SELECT * FROM configuracionlaboratorio WHERE idOpcionLab = 7");
        $sql->execute();
        if ($sql == FALSE) {
            throw new Exception($con->errorInfo());
        } else {
            return $sql->fetchAll(PDO::FETCH_ASSOC);
        }
    }
}
