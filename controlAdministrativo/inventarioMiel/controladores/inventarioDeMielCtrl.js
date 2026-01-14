form.config(function ($routeProvider) {
    $routeProvider.when('/invMiel', {
        templateUrl: 'controlAdministrativo/inventarioMiel/inventarioDeMiel.html',
        controller: 'inventarioDeMielCtrl'
    })
});
form.controller('inventarioDeMielCtrl', ['$scope', '$http', 'growl', '$location', '$routeParams', function ($scope, $http, growl, $location, $routeParams) {
    $scope.meses = {};
    $scope.inventarioMiel = new Array();
    $scope.totales = {};
    $scope.opcionMes = {};
    $scope.cargandoDatos = false;

    if ($location.path() == '/invMiel') {
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
            } else if (objeto.opcion == '3' && !objeto.fechaUno || objeto.opcion == '3' && !objeto.fechaDos) {
                growl.info('Selecciona ambas fechas');
                return false;
            }
        }
        return true;
    };

    $scope.imprimirAlmacen = function (opcionMes) {
        if (validarDatos(opcionMes)) {
            if (opcionMes.opcion == '1') {
                $http.get('controlAdministrativo/inventarioMiel/php/xlsInventarioMiel.php?opcion=1').success(function (data) {
                    if (data.hasOwnProperty('error') && data.error == true) {
                        swal('', data.message, 'error')
                    } else {
                        return window.location.href = 'controlAdministrativo/inventarioMiel/php/xlsInventarioMiel.php?opcion=1';
                    }
                })
            } else {
                if(opcionMes.opcion == '2'){
                    $http.get('controlAdministrativo/inventarioMiel/php/xlsInventarioMiel.php?opcion=2&mes=' + opcionMes.mes).success(function (data) {
                        if (data.hasOwnProperty('error') && data.error == true) {
                            swal('', data.message, 'error')
                        } else {
                            return window.location.href = 'controlAdministrativo/inventarioMiel/php/xlsInventarioMiel.php?opcion=2&mes=' + opcionMes.mes;
                        }
                    })
                }else{
                    $http.get('controlAdministrativo/inventarioMiel/php/xlsInventarioMiel.php?opcion=3' + '&fechaUno=' + opcionMes.fechaUno + '&fechaDos=' + opcionMes.fechaDos).success(function (data) {
                        if (data.hasOwnProperty('error') && data.error == true) {
                            swal('', data.message, 'error')
                        } else {
                            return window.location.href = 'controlAdministrativo/inventarioMiel/php/xlsInventarioMiel.php?opcion=3' + '&fechaUno=' + opcionMes.fechaUno + '&fechaDos=' + opcionMes.fechaDos;
                        }
                    })
                }
               
            }
        }
    }

    $scope.verMes = function (opcionMes) {
        if (validarDatos(opcionMes)) {
            $scope.cargandoDatos = true;
            $scope.inventarioDeMiel = null;
            $http.post('controlAdministrativo/inventarioMiel/php/inventarioMielAdmin.php', opcionMes).success(function (data) {
                if (data.hasOwnProperty('error')) {
                    if (!data.error) {
                        $scope.inventarioMiel = data.resultado.productos;
                        $scope.totales = data.resultado;
                        angular.forEach($scope.inventarioMiel, function (value) {
                            if (value.entradas == 0 && value.salidas == 0) {
                                value.saldo = value.existencia;
                            }
                        });
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
    };

}]);