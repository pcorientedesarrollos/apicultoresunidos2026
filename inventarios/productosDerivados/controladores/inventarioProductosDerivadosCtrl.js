form.config(function ($routeProvider) {
    $routeProvider.when('/invDerivados', {
        templateUrl: 'inventarios/productosDerivados/inventarioProductosDerivados.html',
        controller: 'inventarioProductosDerivadosCtrl'
    })
});
form.controller('inventarioProductosDerivadosCtrl', ['$scope', '$http', 'growl', '$location', '$routeParams', function ($scope, $http, growl, $location, $routeParams) {
    $scope.meses = {};
    $scope.inventarioDerivados = new Array();
    $scope.totales = {};
    $scope.opcionMes = {};
    $scope.cargandoDatos = false;

    if ($location.path() == '/invDerivados') {
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

    $scope.verMes = function (opcionMes) {
        if (validarDatos(opcionMes)) {
            $scope.cargandoDatos = true;
            $scope.inventarioDeProductosDerivados = null;
            $http.post('inventarios/productosDerivados/php/inventarioProductosDerivados.php', opcionMes).success(function (data) {
                if (data.hasOwnProperty('error')) {
                    if (!data.error) {
                        $scope.inventarioDerivados = data.resultado.productos;
                        $scope.totales = data.resultado;
                        angular.forEach($scope.inventarioDerivados, function (value) {
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


    $scope.imprimirXlsDerivados = function (opcionMes) {
        console.log(opcionMes);
        if (validarDatos(opcionMes)) {
            if (opcionMes.opcion == '1') {
                $http.get('reportes/administrativo/xlsInventarioProductosDerivados.php?opcion=1').success(function (data) {
                    if (data.hasOwnProperty('error') && data.error == true) {
                        swal('', data.message, 'error')
                    } else {
                        return window.location.href = 'reportes/administrativo/xlsInventarioProductosDerivados.php?opcion=1';
                    }
                })
            } else if (opcionMes.opcion == '2') {
                $http.get('reportes/administrativo/xlsInventarioProductosDerivados.php?opcion=2&mes=' + opcionMes.mes).success(function (data) {
                    if (data.hasOwnProperty('error') && data.error == true) {
                        swal('', data.message, 'error')
                    } else {
                        return window.location.href = 'reportes/administrativo/xlsInventarioProductosDerivados.php?opcion=2&mes=' + opcionMes.mes;
                    }
                })
            } else if (opcionMes.opcion == '3') {
                $http.get('reportes/administrativo/xlsInventarioProductosDerivados.php?opcion=3' + '&fechaUno=' + opcionMes.fechaUno + '&fechaDos=' + opcionMes.fechaDos).success(function (data) {
                    if (data.hasOwnProperty('error') && data.error == true) {
                        swal('', data.message, 'error')
                    } else {
                        return window.location.href = 'reportes/administrativo/xlsInventarioProductosDerivados.php?opcion=3' + '&fechaUno=' + opcionMes.fechaUno + '&fechaDos=' + opcionMes.fechaDos;
                    }
                })
            }
        }
    }

    function traerMielInicialAnual() {
        $http.get('controlAdministrativo/php/mielInicialAnual.php?opt=get').success(function (data) {
            if (!data.error) {
                $scope.mielInicialAnual = data.data;
            } else {
                growl.error(data.message);
            }
        });
    }

    function guardarMielInicialAnual(mielInicial) {
        $http.post('controlAdministrativo/php/mielInicialAnual.php?opt=set', mielInicial).success(function (data) {
            if (!data.error) {
                growl.success(data.message);
                $('#modalMielInicial').modal('hide');
            } else {
                growl.error(data.message);
            }
        });
    }

    $scope.setMielInicialAnual = function () {
        traerMielInicialAnual();
        $('#modalMielInicial').modal();
    }

    $scope.guardarNuevoMienInicialAnual = function () {
        if ($scope.mielInicialAnual && $scope.mielInicialAnual.existenciaPasada && $scope.mielInicialAnual.importeAcumuladoPasado) {
            guardarMielInicialAnual($scope.mielInicialAnual);
        } else {
            growl.info('Los datos son necesarios');
        }
    }

    if ($routeParams.acumulado) {
        $scope.opcionMes = { opcion: '1', tipoMiel: '1' };
        $scope.verMes($scope.opcionMes);
    }
}]);