form.config(function ($routeProvider) {
    $routeProvider.when('/certificadosCalidad', {
        templateUrl: 'calidad/certificadosDeCalidad.html',
        controller: 'certificadoCalidadCtrl'
    }).when('/capturaCertificadoCalidad/:idLoteInterno/:tipoMiel', {
        templateUrl: 'calidad/capturaCertificadoCalidad.html',
        controller: 'certificadoCalidadCtrl'
    })
});

form.controller('certificadoCalidadCtrl', function ($scope, $http, $routeParams, growl, busqueda, $location, $rootScope) {

    $scope.informacionLotes = new Array();
    $scope.verTipoDeMiel = '1';
    $scope.idLote = $routeParams.idLoteInterno;
    $scope.tipoMiel = $routeParams.tipoMiel;
    $scope.certificadoEncabezado = {};
    $scope.certificadoEncabezado.idLote = $scope.idLote;
    $scope.certificadoEncabezado.tipoDeMiel = $scope.tipoMiel;
    var date = new Date();
    var _mes = date.getMonth() + 1;
    $scope.mostrarMes = _mes.toString();
    $scope.listaClientesExportadores = new Array();

    $http.get('almacen/php/consultaComboMeses.php').success(function (data) {
        $scope.listaDeMeses = data;
    });

    function traeVistaPrincipal(tipoDeMiel) {
        $scope.cargandoDatos = true;
        url = 'calidad/php/traeEncabezadoCertificadoCalidad.php';
        if ($scope.mostrarMes && $scope.mostrarMes !== "null") {
            url += '?mes=' + $scope.mostrarMes;
        }
        $http.post(url, tipoDeMiel).success(function (data) {
            if (data.error) {
                $scope.cargandoDatos = false;
                growl.error(data.message);
            } else {
                $scope.informacionLotes = [];
                angular.forEach(data.data, function (value) {
                    if (value.tipoDeCliente == null) {
                        $scope.informacionLotes.push(value);
                        // $scope.cargandoDatos = false;
                    } else {
                        if (value.tipoDeCliente == '10') {
                            if (value.cliente && value.cliente.datosCliente) {
                                var nombre = JSON.parse(value.cliente.datosCliente);
                                value.cliente = nombre.nombre;
                            } else {
                                value.cliente = '';
                            }
                            $scope.informacionLotes.push(value);
                            // $scope.cargandoDatos = false;
                        } else if (value.tipoDeCliente == '6') {
                            value.cliente = value.cliente.nombre
                            $scope.informacionLotes.push(value);
                            // $scope.cargandoDatos = false;
                        }
                    }
                });
                $scope.cargandoDatos = false;
                // $scope.informacionLotes = data.data;
            }
        });
    }

    if ($location.path() == '/certificadosCalidad') {
        if (window.localStorage.getItem('seleccionTipoMiel') != null) {
            $scope.verTipoDeMiel = window.localStorage.getItem('seleccionTipoMiel');
        }
        $scope.$watch('verTipoDeMiel', function (tipoMiel) {
            if (tipoMiel) {
                window.localStorage.setItem('seleccionTipoMiel', tipoMiel);
                traeVistaPrincipal(tipoMiel);
            }
        })

        var storedMes = window.localStorage.getItem('seleccionMes');
        if (storedMes && storedMes !== "null" && storedMes !== "") {
            $scope.mostrarMes = storedMes;
        }
        $scope.$watch('mostrarMes', function (mesElegido) {
            window.localStorage.setItem('seleccionMes', mesElegido);
            traeVistaPrincipal($scope.verTipoDeMiel);
        })
    }

    $scope.$watch('certificadoEncabezado.fechaCalidad', function (fecha) {
        if (fecha) {
            var start = new Date(fecha);
            start.setFullYear(start.getFullYear() + 2);
            var startf = start.toISOString().slice(0, 10).replace(/-/g, "-");
            $scope.certificadoEncabezado.fechaCaducidad = startf;
        }
    })

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

    function armarTabla(idLote) {
        $http.get('laboratorio/php/detalleCertificado.php?miel=' + $scope.tipoMiel + '&idLote=' + idLote).success(function (data) {
            if (data.hasOwnProperty('error')) {
                if (!data.error) {
                    $scope.certificadoEncabezado = data.data;
                    $scope.detalle = $scope.certificadoEncabezado.detalle;
                    angular.forEach($scope.detalle.caracteristicas, function (value) {
                        if (value.idAnalisis == '1') {
                            $scope.detalle.caracteristicas.tipoDeMiel = value;
                        }
                        if (value.idAnalisis == '2') {
                            $scope.detalle.caracteristicas.humedad = value;
                        }
                        if (value.idAnalisis == '3') {
                            $scope.detalle.caracteristicas.color = value;
                        }
                        if (value.idAnalisis == '4') {
                            $scope.detalle.caracteristicas.hmf = value;
                        }
                        if (value.idAnalisis == '5') {
                            $scope.detalle.caracteristicas.adulteracion = value;
                        }
                        if (value.idAnalisis == '6') {
                            $scope.detalle.caracteristicas.fg = value;
                        }
                        if (value.idAnalisis == '16') {
                            $scope.detalle.caracteristicas.brix = value;
                        }
                        if (value.idAnalisis == '17') {
                            $scope.detalle.caracteristicas.solidos = value;
                        }
                    });
                    angular.forEach($scope.detalle.antibioticos, function (value) {
                        if (value.idAnalisis == '7') {
                            $scope.detalle.antibioticos.sulfametazinas = value;
                        }
                        if (value.idAnalisis == '8') {
                            $scope.detalle.antibioticos.estreptomicinas = value;
                        }
                        if (value.idAnalisis == '9') {
                            $scope.detalle.antibioticos.tetraciclinas = value;
                        }
                    });
                    angular.forEach($scope.detalle.microbiologicos, function (value) {
                        if (value.idAnalisis == '10') {
                            $scope.detalle.microbiologicos.aerobios = value;
                        }
                        if (value.idAnalisis == '11') {
                            $scope.detalle.microbiologicos.coliformes = value;
                        }
                        if (value.idAnalisis == '12') {
                            $scope.detalle.microbiologicos.salmonella = value;
                        }
                        if (value.idAnalisis == '13') {
                            $scope.detalle.microbiologicos.hongos = value;
                        }
                        if (value.idAnalisis == '14') {
                            $scope.detalle.microbiologicos.levaduras = value;
                        }
                        if (value.idAnalisis == '15') {
                            $scope.detalle.microbiologicos.listeria = value;
                        }
                    });
                    angular.forEach($scope.detalle.sensoriales, function (value) {
                        if (value.idAnalisis == '18') {
                            $scope.detalle.sensoriales.apariencia = value;
                        }
                        if (value.idAnalisis == '19') {
                            $scope.detalle.sensoriales.color = value;
                        }
                        if (value.idAnalisis == '20') {
                            $scope.detalle.sensoriales.olor = value;
                        }
                        if (value.idAnalisis == '21') {
                            $scope.detalle.sensoriales.sabor = value;
                        }
                    });
                } else {
                    swal('Error', data.message, 'error');
                }
            } else {
                growl.error('Error');
                console.error(data);
            }
        });

    };

    if ($scope.idLote) {
        armarTabla($scope.idLote);
        if ($scope.tipoMiel == '1') {
            $scope.tipoDeMiel = 'Miel 100% pura de abeja';
        } else if ($scope.tipoMiel == '2') {
            $scope.tipoDeMiel = 'Miel 100% orgánica';
        }
    }

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

    $scope.guardarCertificadoCalidad = function () {
        console.log($scope.certificadoEncabezado);
        $http.post('calidad/php/guardarCertificadoCalidad.php', $scope.certificadoEncabezado).success(function (data) {
            console.log(data);
            if (typeof (data) == 'object' && data.hasOwnProperty('error')) {
                if (data.error) {
                    swal('Error', data.message, 'error');
                } else {
                    swal('Listo', data.message, 'success');
                    window.location = '#/certificadosCalidad';
                }
            } else {
                growl.error('Error');
                console.error(data);
            }
        });
    }

    $scope.imprimirCertificadoCalidad = function (miel, lote) {
        window.open('reportes/calidad/pdfCertificadoCalidad.php?miel=' + miel + '&idLote=' + lote, '_blank');
    };

});