form.controller('inventarioCeraCtrl', ['$scope', '$http', '$routeParams', 'growl', '$location', '$q', '$rootScope', function ($scope, $http, $routeParams, growl, $location, $q, $rootScope) {

    $scope.meses = {};
    $scope.inventarioDeCera = new Array();
    $scope.opcionMes = {};
    $scope.cargandoDatos = false;
    if ($location.path() == '/inventarioCera') {
        $http.get('almacen/php/consultaComboMeses.php').success(function (data) {
            // Solo se muestra en la vista de creacion, son los datos de los meses
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

        if (!objeto.tipoCera) {
            growl.info('Seleccione el tipo de cera');
            return false;
        }
        return true;
    };

    $scope.verMes = function (opcionMes) {
        if (validarDatos(opcionMes)) {

            $scope.cargandoDatos = true;
            $scope.inventarioDeCera = null;
            $http.post('controlAdministrativo/php/inventarioCera.php', opcionMes).success(function (data) {
              console.log(data);
                if (typeof (data) == 'object' && data.hasOwnProperty('error')) {
                    if (!data.error) {
                        $scope.inventarioDeCera = data.resultado;
                    } else {
                        growl.error(data.message);
                    }
                } else {
                    growl.error('Error');
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
                $http.get('reportes/administrativo/xlsInventarioDeCera.php?opcion=1&tipoCera=' + opcionMes.tipoCera).success(function (data) {
                    if (data.hasOwnProperty('error') && data.error == true) {
                        swal('', data.message, 'error')
                    } else {
                        return window.location.href = 'reportes/administrativo/xlsInventarioDeCera.php?opcion=1&tipoCera=' + opcionMes.tipoCera;
                    }
                })
            } else {
                $http.get('reportes/administrativo/xlsInventarioDeCera.php?opcion=2&mes=' + opcionMes.mes + '&tipoCera=' + opcionMes.tipoCera).success(function (data) {
                    if (data.hasOwnProperty('error') && data.error == true) {
                        swal('', data.message, 'error')
                    } else {
                        return window.location.href = 'reportes/administrativo/xlsInventarioDeCera.php?opcion=2&mes=' + opcionMes.mes + '&tipoCera=' + opcionMes.tipoCera;
                    }
                })
            }
        }
    }

}]);