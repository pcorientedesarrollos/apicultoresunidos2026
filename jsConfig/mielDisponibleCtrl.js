form.controller('mielDisponibleCtrl', ['$scope', '$http', '$routeParams', 'growl', '$location', '$q', '$rootScope', function ($scope, $http, $routeParams, growl, $location, $q, $rootScope) {
    $scope.zonas = {};
    $scope.opcionVista = {};
    $scope.cargandoDatos = false;
    $scope.mielDisponible = new Array();

    $scope.$watch('opcionVista.tipoMiel', function (val) {
        $http.post('almacen/php/traeZonasDeTambores.php?tipoMiel=' + val).success(function (data) {
            $scope.zonas = data;
        });
    });

    function validarDatos(objeto) {
        if (!objeto.tipoMiel) {
            growl.info('Selecciona una tipo de miel')
            return false;
        } else {
            if (!objeto.opcion) {
                growl.info('Selecciona acumulado o zona');
                return false;
            }
            if (objeto.opcion == '2' && !objeto.zona) {
                growl.info('Selecciona una zona');
                return false;
            }
        }
        return true;
    };

    $scope.verMielDisponible = function (opcionVista) {
        if (validarDatos(opcionVista)) {
            $scope.cargandoDatos = true;
            $scope.mielDisponible = null;
            $http.post('inventarios/php/mielDisponible.php', opcionVista).success(function (data) {
                if (data.hasOwnProperty('error')) {
                    if (!data.error) {
                        $scope.mielDisponible = data.resultado;
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

    function generarurl(opcionVista) {
        if (opcionVista.opcion == 1) {
            return 'reportes/produccion/xlsInventarioMielDisponible.php?idTipoDeMiel=' + opcionVista.tipoMiel;
        } else {
            return 'reportes/produccion/xlsInventarioMielDisponible.php?idTipoDeMiel=' + opcionVista.tipoMiel + '&idZona=' + opcionVista.zona;
        }
    }

    $scope.descargarExcel = function (laboratorio) {
        $('#modalDescargarExcel').modal('hide');
        var url = generarurl($scope.opcionVista);
        if (laboratorio == '1') {
            url += '&laboratorio';
        }
        $http.get(url).success(function (data) {
            if (data.hasOwnProperty('error') && data.error == true) {
                swal('', data.message, 'error')
            } else {
                return window.location.href = url
            }
        })
    }

    $scope.abrirModal = function (opcionVista) {
        if (validarDatos(opcionVista)) {
            $('#modalDescargarExcel').modal();
        }
    }

    if ($routeParams.acumulado) {
        if ($routeParams.miel) {
            // Si trae este parámetro, deberá mostrar la miel disponible acumulada
            $scope.opcionVista = {
                tipoMiel: $routeParams.miel,
                opcion: '1'
            }
        }
        $scope.verMielDisponible($scope.opcionVista);
    }
}]);