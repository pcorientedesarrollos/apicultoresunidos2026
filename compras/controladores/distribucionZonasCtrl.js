//var app = angular.module('Oriente65App.formZonaCtrl', []);

form.config(function ($routeProvider) {
    $routeProvider.when('/distribucionZonas', {
        templateUrl: 'compras/paginas/distribucionZonas.html',
        controller: 'distribucionZonasCtrl'
    })
        .when('/distribucion/:idzona', {
            templateUrl: 'compras/paginas/distribucionZona.html',
            controller: 'distribucionZonasCtrl'
        })
})

form.controller('distribucionZonasCtrl', ['$scope', '$routeParams', '$http', 'growl', function ($scope, $routeParams, $http, growl) {

    $scope.asignacionAcopio = {};
    $idzona = $routeParams.idzona;
    if ($idzona) {
        if ($idzona > 0) {
            $http.get('compras/php/localidad/listaLocalidades.php?zona=' + $idzona).success(function (arrayLocalidades) {
                $scope.listaLocalidades = arrayLocalidades;
            });

            $http.get('compras/php/distribucionDeZonas/traeDistribucion.php?id=' + $idzona).success(function (data) {
                if (data.hasOwnProperty('error')) {
                    if (data.error) {
                        growl.error(data.message);
                    } else {
                        if (data.data) {
                            $scope.asignacionAcopio = data.data;
                        } else {
                            growl.info('No se encuentran registros');
                        }
                    }
                } else {
                    console.error(data);
                }
            });

        } else {
            $http.get('compras/php/localidad/listaLocalidades.php?creando=0').success(function (arrayLocalidades) {
                $scope.listaLocalidades = arrayLocalidades;
            });
        }
        $http.get('compras/php/comprador/listaComprador.php').success(function (arrayComprador) {
            $scope.listaCompradores = arrayComprador;
        });
        $http.get('compras/php/zona/listaZona.php').success(function (arrayZonas) {
            $scope.listaZonas = arrayZonas;
        });

    } else {
        $http.get('compras/php/distribucionDeZonas/traeDatosDistribucion.php').success(function (data) {
            if (data.hasOwnProperty('error')) {
                if (data.error) {
                    growl.error(data.message);
                } else {
                    if (data.data) {
                        $scope.distribuciones = data.data;
                    } else {
                        growl.info('No se encuentran registros');
                    }
                }
            } else {
                console.error(data);
            }
        });
    }

    $scope.guardarAsignacion = function () {
        if ($scope.asignacionAcopio.idcomprador && $scope.asignacionAcopio.idzona) {
            if ($scope.asignacionAcopio.idlocalidad.length > 0) {
                $http.post('compras/php/distribucionDeZonas/guardarDistribucion.php', $scope.asignacionAcopio).success(function (data) {
                    if (data.hasOwnProperty('error')) {
                        if (data.error) {
                            swal("", "Ocurrió un error", "error");
                        } else {
                            swal("¡Listo!", "Registro guardado", "success");
                            return window.location.href = "#/distribucionZonas";
                        }
                    } else {
                        growl.error('Error');
                        console.error(data);
                    }
                });
            } else {
                growl.info('Seleccione al menos una localidad');
            }
        } else {
            growl.info('Verifique sus datos');
        }
    }

}]);