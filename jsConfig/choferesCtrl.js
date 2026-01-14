form.controller('choferesCtrl', ['$scope', '$http', '$routeParams', 'growl', '$filter', function ($scope, $http, $routeParams, growl, $filter) {

    var proveAlmacen = $routeParams.idOperador;
    $scope.operador = {};
    $scope.choferes = new Array();
    $scope.arregloPlacas = new Array();

    $scope.remolqueActivo = false;
    if (proveAlmacen === "nuevo") {
        $scope.operador.unas = '1';
        $scope.operador.cabello = '1';
        $scope.operador.ropa = '1';
        $scope.operador.remolque = '0';
        $scope.remolqueActivo = false;
    }

    $scope.$watch('operador.remolque', function (val) {
        if (val == '1') {
            $scope.remolqueActivo = true;
        } else if (val == '0') {
            $scope.operador.marca = null;
            $scope.operador.modelo = null;
            $scope.operador.placas = null;
            $scope.operador.caja = null;
            $scope.remolqueActivo = false;
        }
    });


    //=================================================================
    //      TRAE INFORMACION DE RELACION DE REQUERIMIENTOS
    //=================================================================
    $http.get('almacen/php/traeOperadores.php').success(function (data) {
        $scope.choferes = data;
    });
    //=================================================================
    //      TRAE INFORMACION DEL OPERADOR YA GUARDADO
    //=================================================================
    $scope.creando = false;
    if (proveAlmacen === "nuevo") {
        $scope.creando = true;
    } else {
        $http.get('almacen/php/traeOperador.php?idOperador=' + proveAlmacen).success(function (data) {
            $scope.operador = data;
            $scope.arregloPlacas = data.arregloPlacas;
        });
    }

    $scope.nuevasPlacas = function () {
        $scope.placa = {};
        $scope.placa.idPlaca = 0;
        $scope.placa.tipo = "";
        $scope.placa.marca = "";
        $scope.placa.modelo = "";
        $scope.placa.placa = "";
        $scope.placa.remolque = "";
        $scope.placa.marcaRemolque = "";
        $scope.placa.modeloRemolque = "";
        $scope.placa.placaRemolque = "";
        $scope.arregloPlacas.push($scope.placa);
    };

    $scope.$watch('placa.placa', function (val) {
        if (val) {
            $scope.placa.placa = $filter('uppercase')(val);
        }
    }, true);

    $scope.eliminarPlaca = function (indice) {
        $scope.placa = {};
        $scope.placa.idPlaca = 0;
        $scope.placa.placa = "";
        $scope.placa.indice = 0;
        angular.forEach($scope.arregloPlacas, function (value, key) {
            if (key == indice) {
                $scope.placa.indice = key;
                $scope.placa.idPlaca = value.idPlaca;
                $scope.placa.placa = value.placa;
            }
        });
        // if (proveAlmacen == "nuevo") {
        $scope.arregloPlacas.splice($scope.placa.indice, 1);
        // }else if(proveAlmacen > 0){
        // }
        // else if (proveAlmacen > 0) {
        //     $http.post("almacen/php/eliminarPlaca.php?idPlaca=" + $scope.placa.idPlaca)
        //         .success(function (respuesta) {
        //             //                        alert(respuesta);
        //             growl.warning("Registro eliminado");
        //             $scope.arregloPlacas.splice($scope.placa.indice, 1);
        //         });
        // }
    };

    //=================================================================
    //     GUARDAR
    //=================================================================
    $scope.guardarChofer = function () {
        $scope.datos = new Array();
        $scope.datos.push($scope.operador);
        $scope.datos.push($scope.arregloPlacas);
        $scope.valid = $scope.validarProveAlmacen();
        if ($scope.valid == true) {
            if ($scope.arregloPlacas.length == 0) {
                growl.info('Se requiere al menos un vehículo');
            } else {
                var ok = $scope.validarDetalle();
                if (ok == true) {
                    if (proveAlmacen == "nuevo") {
                        $http.post("almacen/php/verificarLicencia.php?licencia=" + $scope.operador.licencia)
                            .success(function (respuesta) {
                                if (respuesta == 1) {
                                    swal("Error!", "Verifique! No. de Licencia duplicado", "error");
                                } else {
                                    $http.post("almacen/php/guardarProveedorAlmacen.php", { valor: $scope.datos }).success(function (data) {
                                        if (data.error) {
                                            swal("", "Ocurrió un error", "error");
                                        } else {
                                            swal("Éxito", "Nuevo Operador Registrado", "success");
                                            return window.location.href = "#/operadores";
                                        }
                                    });
                                }
                            }
                            );
                    } else {
                        $http.post('almacen/php/editarProveedorAlmacen.php', { valor: $scope.datos }).success(function (result) {
                            if (result.error) {
                                swal("", "Ocurrió un error", "info");
                            } else {
                                swal("", "Se han guardado los datos", "success");
                                $http.get('almacen/php/traeOperadores.php').success(function (data) {
                                    $scope.choferes = data;
                                });
                            }
                        });
                        return window.location.href = "#/operadores";
                    }
                }
            }
        }
    };

    //=================================================================
    //      VALIDAR 
    //=================================================================
    $scope.validarProveAlmacen = function () {
        $scope.valid = false;
        // if ($scope.operador.remolque == '1') {
        //     if ($scope.operador.marca == null || $scope.operador.modelo == null || $scope.operador.placas == null || $scope.operador.caja == null) {
        //         growl.info("Indique los datos del remolque");
        //     }
        // }
        if ($scope.operador.operador == undefined) {
            growl.info("Se requiere un nombre");
        } else if ($scope.operador.licencia == undefined) {
            growl.info("Se requiere una licencia");
        } else if ($scope.operador.compania == undefined) {
            growl.info("Se requiere un nombre de Compañia");
        } else if ($scope.operador.vigencia == undefined) {
            growl.info("Se requiere una vigencia");
        } else {
            $scope.valid = true;
        }
        return $scope.valid;
    };

    $scope.validarDetalle = function () {
        var ok = true;
        angular.forEach($scope.arregloPlacas, function (value) {
            if (ok == true) {
                if (value.tipo == "" || value.marca == "" || value.modelo == "" || value.placa == "") {
                    growl.info('Indique los datos del vehículo');
                    ok = false;
                }
            }
        });
        return ok;
    };

}]);