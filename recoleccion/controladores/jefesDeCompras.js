form.config(function ($routeProvider) {
    $routeProvider.when('/jefesCompras', {
        templateUrl: 'recoleccion/jefesDeCompras.html',
        controller: 'jefesDeComprasCtrl'
    }).when('/proyeccionAcumulado', {
        templateUrl: 'recoleccion/jefesDeComprasProyeccionAcumulado.html',
        controller: 'jefesDeComprasCtrl'
    })
})

form.controller('jefesDeComprasCtrl', function ($scope, $http, growl, $routeParams, $location) {

    $scope.fecha1 = null;
    $scope.fecha2 = null;

    function traeComprasReales(tipoDeMiel, opcionVista, fecha1 = false, fecha2 = false) {
        var url = 'recoleccion/php/jefesDeCompras/totalComprasReales.php?vista=1';

        if (tipoDeMiel) {
            url += '&miel=' + tipoDeMiel;
        }

        if (opcionVista == 1) {
            url += '&fecha1=' + fecha1 + '&fecha2=' + fecha2;
        }
        $http.get(url).success(function (data) {
            $scope.jefesCompras = data;
        });
    }

    $scope.$watch('tipoDeMiel', function (val) {
        if ($scope.opcionVista == 2) {
            traeComprasReales(val, $scope.opcionVista);
        } else if ($scope.opcionVista == 1) {
            if ($scope.fecha1 || $scope.fecha1 != undefined || $scope.fecha1 != null && $scope.fecha2 || $scope.fecha2 != undefined || $scope.fecha2 != null) {
                traeComprasReales(val, $scope.opcionVista, $scope.fecha1, $scope.fecha2);
            }
        }
    });

    $scope.$watch('opcionVista', function (val) {
        $scope.fecha1 = null;
        $scope.fecha2 = null;
        if (val == 2) {
            traeComprasReales($scope.tipoDeMiel, $scope.opcionVista);
        }
    });

    $scope.$watch('fecha1', function (fecha1) {
        if (fecha1) {
            if ($scope.fecha2 || $scope.fecha2 != undefined || $scope.fecha2 != null) {
                traeComprasReales($scope.tipoDeMiel, $scope.opcionVista, fecha1, $scope.fecha2);
            }
        }
    });

    $scope.$watch('fecha2', function (fecha2) {
        if ($scope.fecha1 || $scope.fecha1 != undefined || $scope.fecha1 != null) {
            traeComprasReales($scope.tipoDeMiel, $scope.opcionVista, $scope.fecha1, fecha2);
        }
    });

    if ($location.path() == '/proyeccionAcumulado') {
        $scope.$watch('tipoDeMiel', function (val) {
            traeComprasReales(val);
        });
    }

    $scope.guardarMetas = function () {
        $scope.zonas = Array();
        $scope.datosMetas = Array();
        $scope.jefesCompras.forEach(element => {
            $scope.zonas.push(element.zonas);
        });
        $scope.zonas.forEach(element => {
            element.forEach(dato => {
                $scope.datosMetas.push(dato);
            })
        });

        $http.post('recoleccion/php/metas/guardarMetas.php', $scope.datosMetas).success(function (data) {
            if (typeof (data) == 'object' && data.hasOwnProperty('error')) {
                if (data.error) {
                    swal('Error', data.message, 'error');
                } else {
                    swal('Listo', data.message, 'success');
                }
            } else {
                growl.error('Error');
                console.error(data);
            }
        });
    };

    $scope.descargarReporteExcel = function () {
        if ($location.path() == '/proyeccionAcumulado') {
            if (!$scope.tipoDeMiel) {
                growl.info('Elige un tipo de miel');
            } else {
                return window.location.href = 'reportes/compras/xlsJefesDeCompras.php?acumulado=0&miel=' + $scope.tipoDeMiel;
            }
        } else {
            if (!$scope.tipoDeMiel) {
                growl.info('Elige un tipo de miel');
            } else if (!$scope.opcionVista) {
                growl.info('Elige una opción');
            } else {
                var url = 'reportes/compras/xlsJefesDeCompras.php?miel=' + $scope.tipoDeMiel;

                if ($scope.opcionVista == 1) {
                    if (!$scope.fecha1 || !$scope.fecha2) {
                        growl.info('Seleccione ambas fechas');
                    } else {
                        url += '&fecha1=' + $scope.fecha1 + '&fecha2=' + $scope.fecha2;
                        return window.location.href = url;
                    }
                } else if ($scope.opcionVista == 2) {
                    return window.location.href = url;
                }
            }
        }
    }

});