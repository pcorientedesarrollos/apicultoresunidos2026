form.controller('mielDisponibleSobranteCtrl', ['$scope', '$http', '$routeParams', 'growl', '$location', '$q', '$rootScope', function ($scope, $http, $routeParams, growl, $location, $q, $rootScope) {
    $scope.zonas = {};
    $scope.mielOpcion = {};
    $scope.cargandoDatos = false;
    $scope.mielDisponible = new Array();

    $scope.$watch('mielOpcion.opcion', function (val) {
        if (val == 3) {
            $http.post('almacenSobrantes/php/listaTiposDeSobrantes.php').success(function (data) {
                $scope.lstSobrantes = data;
            });
        }
    });

    function validarDatos(objeto) {
        if (!objeto.tipoMiel) {
            growl.info('Selecciona una tipo de miel')
            return false;
        } else {
            if (!objeto.opcion) {
                growl.info('Selecciona acumulado o clasificación');
                return false;
            }
            if (objeto.opcion == '3' && !objeto.sobrante) {
                growl.info('Selecciona una clasificación');
                return false;
            }
        }
        return true;
    };

    $scope.verMielSobranteDisponible = function (mielOpcion) {
        if (validarDatos(mielOpcion)) {
            $scope.cargandoDatos = true;
            $scope.mielDisponible = null;
            $http.post('inventarios/php/mielDisponibleSobrante.php', mielOpcion).success(function (data) {
                if (data.hasOwnProperty('error')) {
                    if (!data.error) {
                        if (data.resultado.registros.length == 0) {
                            growl.info("No se encuentran registros");
                        } else {
                            $scope.mielDisponible = data.resultado;
                        }
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

    function generarurl(mielOpcion) {
        if (mielOpcion.opcion == 1) {
            return 'reportes/produccion/xlsInventarioMielSobranteDisponible.php?idTipoDeMiel=' + mielOpcion.tipoMiel;
        } else {
            return 'reportes/produccion/xlsInventarioMielSobranteDisponible.php?idTipoDeMiel=' + mielOpcion.tipoMiel + '&idSobrante=' + mielOpcion.sobrante;
        }
    }

    $scope.descargarExcel = function () {
        if (validarDatos($scope.mielOpcion)) {
            var url = generarurl($scope.mielOpcion);
            $http.get(url).success(function (data) {
                if (data.hasOwnProperty('error') && data.error == true) {
                    swal('', data.message, 'error')
                } else {
                    return window.location.href = url
                }
            })
        }
    };

    if ($routeParams.acumulado) {
        if ($routeParams.miel) {
            // Si trae este parámetro, deberá mostrar la miel disponible acumulada
            $scope.mielOpcion = {
                tipoMiel: $routeParams.miel,
                opcion: '1'
            }
        }
        $scope.verMielDisponible($scope.mielOpcion);
    }
}]);