form.config(function ($routeProvider) {
    $routeProvider.when('/envasesFrascosInv', {
        templateUrl: 'inventarios/inventarioEnvasesFrascos.html',
        controller: 'inventarioEnvasesCtrl'
    })
});
form.controller('inventarioEnvasesCtrl', ['$scope', '$http', '$routeParams', 'growl', '$location', '$q', '$rootScope', function ($scope, $http, $routeParams, growl, $location, $q, $rootScope) {
    $scope.meses = {};
    $scope.opcionMes = {};
    $scope.cargandoDatos = false;

    $scope.registrosMP = new Array();

    if ($location.path() == '/envasesFrascosInv') {
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
                $http.get('inventarios/php/inventarioEnvasesFrascos.php?acumulado=1').success(function (data) {
                    console.log(data);
                    if (data.hasOwnProperty('error')) {
                        if (!data.error) {
                            $scope.registrosMP = data.resultado;
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
            else {
                $http.get('inventarios/php/inventarioEnvasesFrascos.php?idMes=' + opcionMes.mes).success(function (data) {
                    if (data.hasOwnProperty('error')) {
                        if (!data.error) {
                            $scope.registrosMP = data.resultado;
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


    $scope.imprimirEnvasesFrascosXls = function (opcionMes) {
        if (validarDatos(opcionMes)) {
            if (opcionMes.opcion == 1) {
                $http.get('reportes/administrativo/xlsInventarioEnvasesFrascos.php?opcion=1').success(function (data) {
                    if (data.hasOwnProperty('error') && data.error == true) {
                        swal('', data.message, 'error')
                    } else {
                        return window.location.href = 'reportes/administrativo/xlsInventarioEnvasesFrascos.php?opcion=1';
                    }
                })
            } else {
                $http.get('reportes/administrativo/xlsInventarioEnvasesFrascos.php?opcion=2&mes=' + opcionMes.mes).success(function (data) {
                    if (data.hasOwnProperty('error') && data.error == true) {
                        swal('', data.message, 'error')
                    } else {
                        return window.location.href = 'reportes/administrativo/xlsInventarioEnvasesFrascos.php?opcion=2&mes=' + opcionMes.mes;
                    }
                })
            }
        }
    }

}]);