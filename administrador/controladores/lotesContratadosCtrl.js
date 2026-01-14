form.config(function ($routeProvider) {
    $routeProvider.when('/lotesContratados', {
        templateUrl: 'administrador/lotesContratados/lotesContratados.html',
        controller: 'lotesContratadosCtrl'
    }).when('/nuevoLoteContratado/:tipoMiel/:idLoteContrato', {
        templateUrl: 'administrador/lotesContratados/loteContratado.html',
        controller: 'lotesContratadosCtrl'
    })
});
form.controller('lotesContratadosCtrl', function ($scope, $routeParams, $http, growl, $location) {

    $scope.idLoteContrato = $routeParams.idLoteContrato;
    $scope.tipoMiel = $routeParams.tipoMiel;
    $scope.loteContratado = {};
    $scope.loteContratado.idLoteContrato = $scope.idLoteContrato;
    $scope.loteContratado.tipoDeMiel = $scope.tipoMiel;
    $scope.listaClientesExportadores = new Array();
    $scope.contratos = [];
    $scope.contrato = {};


    function traeContratos(tipoDeMiel) {
        url = 'administrador/php/traeLotesContratados.php';
        $http.post(url, tipoDeMiel).success(function (data) {
            if (data.error) {
                growl.error(data.message);
            } else {
                $scope.contratos = [];
                angular.forEach(data.data, function (value) {
                    if (value.tipoDeCliente == null) {
                        $scope.contratos.push(value);
                    } else {
                        if (value.tipoDeCliente == '10') {
                            var datoNombre = value.cliente.datosCliente;
                            var nombre = JSON.parse(datoNombre);
                            value.cliente = nombre.nombre;
                            $scope.contratos.push(value);
                        } else if (value.tipoDeCliente == '6') {
                            value.cliente = value.cliente.nombre
                            $scope.contratos.push(value);
                        }
                    }
                });
            }
        });
    }

    if ($location.path() == '/lotesContratados') {
        if (window.localStorage.getItem('seleccionTipoMiel') != null) {
            $scope.verTipoDeMiel = window.localStorage.getItem('seleccionTipoMiel');
        }
        $scope.$watch('verTipoDeMiel', function (tipoMiel) {
            if (tipoMiel) {
                window.localStorage.setItem('seleccionTipoMiel', tipoMiel);
                traeContratos(tipoMiel);
            }
        })
    } else if ($scope.idLoteContrato > 0) {
        $http.get('administrador/php/detalleContrato.php?miel=' + $scope.tipoMiel + '&idLote=' + $scope.idLoteContrato).success(function (data) {
            if (data.hasOwnProperty('error')) {
                if (!data.error) {
                    $scope.loteContratado = data.data;
                    if ($scope.tipoMiel == '1') {
                        $scope.loteContratado.tipoDeMiel = '1';
                    } else if ($scope.tipoMiel == '2') {
                        $scope.loteContratado.tipoDeMiel = '2';
                    }
                } else {
                    swal('Error', data.message, 'error');
                }
            } else {
                growl.error('Error');
                console.error(data);
            }
        });
    }

    $scope.modalAprobacion = function (idLoteContratado, index) {
        $("#altaFecha").modal();
        $scope.contrato.idLoteContratado = idLoteContratado;
        $scope.contrato.index = index;
    }

    $scope.guardarFechaContrato = function (contrato) {
        var id = contrato.index;
        $http.post("administrador/php/guardarFechaAprobacion.php", contrato).success(function (info) {
            traeContratos($scope.verTipoDeMiel);
            swal("¡Éxito!", "Registro agregado", "success");
            $("#altaFecha").modal('hide');
            $scope.contrato = {};
        });
    }

    $http.post('controlAdministrativo/polizaCheque/php/traerNombres.php', 6).success(function (data) {
        $scope.listaClientes = data;
    });

    $http.post("exportacion/php/traeClienteExportador.php").success(function (data) {
        angular.forEach(data, function (value) {
            var cadenaCliente = value.datosCliente;
            var arreglo = JSON.parse(cadenaCliente);
            arreglo.idClienteExportador = value.idClienteExportador;
            $scope.listaClientesExportadores.push(arreglo);
        });
    });

    $scope.altaRapida = function () {
        $('#modalAltaRapida').modal();
    }

    $scope.guardarNuevoProveedorCC = function (altasCC) {
        $http.post("controlAdministrativo/php/guardarCliente.php", altasCC).success(function (info) {
            swal("", info.message, info.swal);
            if (!info.error) {
                $scope.altasCC = {};
                $http.post('controlAdministrativo/polizaCheque/php/traerNombres.php', 6).success(function (data) {
                    $scope.listaClientes = data;
                });
                $('#modalAltaRapida').modal('hide');
            }
        });
    };

    $scope.guardarLoteContratado = function () {
        $http.post('administrador/php/guardarLoteContratado.php', $scope.loteContratado).success(function (data) {
            if (typeof (data) == 'object' && data.hasOwnProperty('error')) {
                if (data.error) {
                    swal('Error', data.message, 'error');
                } else {
                    swal('Listo', data.message, 'success');
                    window.location = '#/lotesContratados';
                }
            } else {
                growl.error('Error');
                console.error(data);
            }
        });
    }

    $scope.imprimirArchivo = function (idLoteInt, tipoMiel) {
        window.open('reportes/trazabilidad/pdfTrazabilidad.php?lote=' + idLoteInt + '&tipoMiel=' + tipoMiel);
    };


});