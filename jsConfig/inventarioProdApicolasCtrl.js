form.controller('inventarioProdApicolasCtrl', ['$scope', '$http', '$routeParams', 'growl', '$location', '$q', '$rootScope', function ($scope, $http, $routeParams, growl, $location, $q, $rootScope) {

    $scope.meses = {};
    $scope.inventarioDeProductosApicola = new Array();
    $scope.opcionMes = {};
    $scope.cargandoDatos = false;
    if ($location.path() == '/inventarioProdApicolas') {
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
            $scope.inventarioDeProductosApicola = null;
            $http.post('controlAdministrativo/php/inventarioProductosApicola.php', opcionMes).success(function (data) {
                if (data.hasOwnProperty('error')) {
                    if (!data.error) {
                        $scope.inventarioDeProductosApicola = data.resultado.productos;
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


    $scope.imprimirAlmacen = function (opcionMes) {
        if (validarDatos(opcionMes)) {
            if (opcionMes.opcion == 1) {
                $http.get('reportes/administrativo/xlsInventarioProductosApicola.php?opcion=1').success(function (data) {
                    if (data.hasOwnProperty('error') && data.error == true) {
                        swal('', data.message, 'error')
                    } else {
                        return window.location.href = 'reportes/administrativo/xlsInventarioProductosApicola.php?opcion=1';
                    }
                })
            } else {
                $http.get('reportes/administrativo/xlsInventarioProductosApicola.php?opcion=2&mes=' + opcionMes.mes).success(function (data) {
                    if (data.hasOwnProperty('error') && data.error == true) {
                        swal('', data.message, 'error')
                    } else {
                        return window.location.href = 'reportes/administrativo/xlsInventarioProductosApicola.php?opcion=2&mes=' + opcionMes.mes;
                    }
                })
            }
        }
    }

}]);