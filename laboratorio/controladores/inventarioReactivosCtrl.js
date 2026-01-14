form.config(function ($routeProvider) {
    $routeProvider.when('/inventarioReactivos', {
        templateUrl: 'laboratorio/inventarioReactivos.html',
        controller: 'inventarioReactivosCtrl'
    })
});

form.controller('inventarioReactivosCtrl', function ($scope, $http, $routeParams, growl, $location, $rootScope) {

    $scope.meses = {};
    $scope.opcionMes = {};
    $scope.cargandoDatos = false;

    $scope.registrosReactivos = new Array();

    if ($location.path() == '/inventarioReactivos') {
        $http.get('almacen/php/consultaComboMeses.php').success(function (data) {
            $scope.meses = data;
        });
    }

    function validarDatos(objeto) {
        if (!objeto.opcion) {
            growl.info('Selecciona una  opción')
            return false;
        } else {
            if (objeto.opcion == '2' && !objeto.mes) {
                growl.info('Selecciona un mes');
                return false;
            }
        }
        return true;
    };

    $scope.verMes = function (opcionMes) {
        if (validarDatos(opcionMes)) {
            $scope.cargandoDatos = true;
            $scope.inventarioDeCera = null;
            if (opcionMes.opcion == 1) {
                $http.get('laboratorio/php/inventarioReactivos.php?acumulado=1').success(function (data) {
                    console.log(data);
                    if (data.hasOwnProperty('error')) {
                        if (!data.error) {
                            $scope.registrosReactivos = data.resultado;
                        } else {
                            growl.error(data.message);
                        }
                    } else {
                        console.error(data);
                    }
                    $scope.cargandoDatos = false;
                }).error(function (error) {
                    $scope.cargandoDatos = false;
                    growl.error('Ha ocurrido un error con la petición al servidor');
                })
            } else {
                $http.get('laboratorio/php/inventarioReactivos.php?idMes=' + opcionMes.mes).success(function (data) {
                    console.log(data);
                    if (data.hasOwnProperty('error')) {
                        if (!data.error) {
                            $scope.registrosReactivos = data.resultado;
                        } else {
                            growl.error(data.message);
                        }
                    } else {
                        console.error(data);
                    }
                    $scope.cargandoDatos = false;
                }).error(function (error) {
                    $scope.cargandoDatos = false;
                    growl.error('Ha ocurrido un error con la petición al servidor');
                })
            }


        }
    };

    $scope.imprimirAlmacen = function (opcionMes) {
        if (validarDatos(opcionMes)) {
            if (opcionMes.opcion == 1) {
                $http.get('reportes/administrativo/xlsInventarioMP.php?opcion=1').success(function (data) {
                    if (data.hasOwnProperty('error') && data.error == true) {
                        swal('', data.message, 'error')
                    } else {
                        return window.location.href = 'reportes/administrativo/xlsInventarioMP.php?opcion=1';
                    }
                })
            } else {
                $http.get('reportes/administrativo/xlsInventarioMP.php?opcion=2&mes=' + opcionMes.mes).success(function (data) {
                    if (data.hasOwnProperty('error') && data.error == true) {
                        swal('', data.message, 'error')
                    } else {
                        return window.location.href = 'reportes/administrativo/xlsInventarioMP.php?opcion=2&mes=' + opcionMes.mes;
                    }
                })
            }
        }
    }

});