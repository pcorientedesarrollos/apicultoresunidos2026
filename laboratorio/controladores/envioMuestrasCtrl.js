form.config(function ($routeProvider) {
    $routeProvider.when('/envioMuestras', {
        templateUrl: 'laboratorio/envioDeMuestras.html',
        controller: 'envioMuestrasCtrl'
    }).when('/envioMuestra/:tipoMiel/:idEnvio', {
        templateUrl: 'laboratorio/capturaEnvioMuestras.html',
        controller: 'envioMuestrasCtrl'
    })
});
form.controller('envioMuestrasCtrl', function ($scope, $routeParams, $http, growl, $location) {

    $scope.idEnvio = $routeParams.idEnvio;
    $scope.tipoMiel = $routeParams.tipoMiel;
    $scope.envioMuestra = {};
    $scope.envioMuestra.idEnvio = $scope.dEnvio
    $scope.envioMuestra.tipoDeMiel = $scope.tipoMiel;
    // $scope.listaClientesExportadores = new Array();
    $scope.envios = [];

    function traeEnvios(tipoDeMiel) {
        url = 'laboratorio/php/traeEnviosMuestras.php';
        $http.post(url, tipoDeMiel).success(function (data) {
            if (data.error) {
                growl.error(data.message);
            } else {
                $scope.envios = data.data;
                // $scope.envios = [];
                // angular.forEach(data.data, function (value) {
                //     if (value.tipoDeCliente == null) {
                //         $scope.envios.push(value);
                //     } else {
                //         if (value.tipoDeCliente == '10') {
                //             var datoNombre = value.cliente.datosCliente;
                //             var nombre = JSON.parse(datoNombre);
                //             value.cliente = nombre.nombre;
                //             $scope.envios.push(value);
                //         } else if (value.tipoDeCliente == '6') {
                //             value.cliente = value.cliente.nombre
                //             $scope.envios.push(value);
                //         }
                //     }
                // });
            }
        });
    }

    if ($location.path() == '/envioMuestras') {
        if (window.localStorage.getItem('seleccionTipoMiel') != null) {
            $scope.verTipoDeMiel = window.localStorage.getItem('seleccionTipoMiel');
        }
        $scope.$watch('verTipoDeMiel', function (tipoMiel) {
            if (tipoMiel) {
                window.localStorage.setItem('seleccionTipoMiel', tipoMiel);
                traeEnvios(tipoMiel);
            }
        })
    } else if ($scope.idEnvio > 0) {
        $http.get('laboratorio/php/detalleEnvio.php?miel=' + $scope.tipoMiel + '&idEnvio=' + $scope.idEnvio).success(function (data) {
            if (data.hasOwnProperty('error')) {
                if (!data.error) {
                    $scope.envioMuestra = data.data;
                    if ($scope.tipoMiel == '1') {
                        $scope.envioMuestra.tipoDeMiel = '1';
                    } else if ($scope.tipoMiel == '2') {
                        $scope.envioMuestra.tipoDeMiel = '2';
                    }
                } else {
                    swal('Error', data.message, 'error');
                }
            } else {
                growl.error('Error');
                console.error(data);
            }
        });
        $scope.$watch('edicionContrato.contrato', function (val) {
            $scope.envioMuestra.numContrato = '';
            $http.post("laboratorio/php/traeLotesContratados.php", val).success(function (info) {
                $scope.listaContratos = info.data;
            });
        });
        $scope.$watch('envioMuestra.tipoDeMiel', function (val) {
            $scope.listaLotes = [];
            $http.get('produccion/php/listaLotes.php?tipoMiel=' + val).success(function (datas) {
                $scope.listaLotes = datas.infoLote;
            });
        });
    } else if ($scope.idEnvio == 0) {
        $scope.$watch('envioMuestra.contrato', function (val) {
            $scope.envioMuestra.numContrato = '';
            $http.post("laboratorio/php/traeLotesContratados.php", val).success(function (info) {
                $scope.listaContratos = info.data;
            });
        });
        $scope.$watch('envioMuestra.tipoDeMiel', function (val) {
            $scope.listaLotes = [];
            $http.get('produccion/php/listaLotes.php?tipoMiel=' + val).success(function (datas) {
                $scope.listaLotes = datas.infoLote;
            });
        });
    }

    $http.post('laboratorio/php/listaLaboratorios.php').success(function (data) {
        $scope.listaLaboratorios = data;
    });

    $http.post("utilerias/php/traeTiposDeMiel.php").success(function (info) {
        $scope.listaCosecha = info;
    });
    $scope.editarContrato = function () {
        $("#editarContrato").modal();
    };

    $scope.guardarEdicionContrato = function () {
        $http.post('laboratorio/php/editarContrato.php?miel=' + $scope.tipoMiel + '&idEnvio=' + $scope.idEnvio, $scope.edicionContrato)
            .success(function (respuesta) {
                growl.success("Registro actualizado");
                $http.get('laboratorio/php/detalleEnvio.php?miel=' + $scope.tipoMiel + '&idEnvio=' + $scope.idEnvio).success(function (data) {
                    if (data.hasOwnProperty('error')) {
                        if (!data.error) {
                            $scope.envioMuestra = data.data;
                            if ($scope.tipoMiel == '1') {
                                $scope.envioMuestra.tipoDeMiel = '1';
                            } else if ($scope.tipoMiel == '2') {
                                $scope.envioMuestra.tipoDeMiel = '2';
                            }
                        } else {
                            swal('Error', data.message, 'error');
                        }
                    } else {
                        growl.error('Error');
                        console.error(data);
                    }
                });
            });
        $("#editarContrato").modal('hide');
        $scope.edicionContrato = {};
    };

    $scope.guardarEnvio = function () {
        $http.post('laboratorio/php/guardarEnvio.php', $scope.envioMuestra).success(function (data) {
            if (typeof (data) == 'object' && data.hasOwnProperty('error')) {
                if (data.error) {
                    swal('Error', data.message, 'error');
                } else {
                    swal('Listo', data.message, 'success');
                    window.location = '#/envioMuestras';
                }
            } else {
                growl.error('Error');
                console.error(data);
            }
        });
    }


});