form.controller('exportadoresCtrl', ['$scope', '$http', '$routeParams', 'growl', '$filter', '$rootScope', '$location', '$q', function ($scope, $http, $routeParams, growl, $filter, $rootScope, $location, $q) {

    //========================= P A R A M E T R O S ===========================

    $scope.solicitudSinLote = $routeParams.sinLote;

    $scope.clienteExportador = $routeParams.idClienteExportador;
    $scope.solicitudCertificado = $routeParams.idSolicitudCertificado;
    $scope.lote = $routeParams.idLoteInterno;
    $scope.tipoMiel = $routeParams.tipoMiel;
    $scope.contrato = {};
    $scope.marcaDistintiva = window.localStorage.getItem('marcaDistintiva');
    //---------------------------------------------------------------------

    $scope.clientesExportadores = new Array();
    $scope.datosCuestionario = {};
    $scope.solicitudesCertificados = new Array();
    $scope.datosProducto = {};
    $scope.datosProducto.lugarTramite = "Mérida, Yucatán";
    $scope.datosProducto.codigoOrigen = "MEX"
    $scope.datosProducto.paisOrigen = "N/A";
    $scope.datosTransporte = {};
    $scope.datosTransporte.marcasEmbarque = "N/A";
    $scope.datosCuestionario2 = {};
    $scope.listaClientesExportadores = new Array();
    $scope.datosCliente = {
        tipoAdministracion: '',
        derechoTransito: '',
        terminoGenerico: '',
        codigoCarretera: '',
        tramoCarretera: '',
        kilometroCarretera: '',
        tramoCamino: '',
        margen: '',
        kilometroCamino: '',
    };
    $scope.datosDestino = {
        administracionDestino: '',
        transitoDestino: '',
        terminoGenericoDestino: '',
        codigoCarreteraDestino: '',
        tramoCarreteraDestino: '',
        kilometroCarreteraDestino: '',
        tramoCaminoDestino: '',
        margenDestino: '',
        kilometroCaminoDestino: '',
    };

    $scope.datosAnexo = {};
    $scope.verBoton = false;
    $scope.vista = 1;


    if (window.localStorage.getItem('VISTA_EXPORTACION') != null) {
        $scope.vista = window.localStorage.getItem('VISTA_EXPORTACION');
    }

    $scope.traeListaExportadores = function () {
        $http.post("exportacion/php/traeClienteExportador.php").success(function (data) {
            angular.forEach(data, function (value) {
                var cadenaCliente = value.datosCliente;
                var arreglo = JSON.parse(cadenaCliente);
                arreglo.idClienteExportador = value.idClienteExportador;
                $scope.listaClientesExportadores.push(arreglo);
            });
        });
    }

    $scope.limpiarMedio = function () {
        $scope.datosCliente.tipoAdministracion = '';
        $scope.datosCliente.derechoTransito = '';
        $scope.datosCliente.terminoGenerico = '';
        $scope.datosCliente.codigoCarretera = '';
        $scope.datosCliente.tramoCarretera = '';
        $scope.datosCliente.kilometroCarretera = '';
        $scope.datosCliente.tramoCamino = '';
        $scope.datosCliente.margen = '';
        $scope.datosCliente.kilometroCamino = '';
    };

    $scope.limpiarMedioDestino = function () {
        $scope.datosDestino.administracionDestino = '';
        $scope.datosDestino.transitoDestino = '';
        $scope.datosDestino.terminoGenericoDestino = '';
        $scope.datosDestino.codigoCarreteraDestino = '';
        $scope.datosDestino.tramoCarreteraDestino = '';
        $scope.datosDestino.kilometroCarreteraDestino = '';
        $scope.datosDestino.tramoCaminoDestino = '';
        $scope.datosDestino.margenDestino = '';
        $scope.datosDestino.kilometroCaminoDestino = '';
    };

    $scope.traeClientesExportadores = function () {
        $http.post("exportacion/php/traeSolicitudCertificado.php").success(function (data) {
            angular.forEach(data, function (value) {
                var cadenaDatos = value.datosProducto;
                var arregloProducto = JSON.parse(cadenaDatos);
                arregloProducto.idSolicitudCertificado = value.idSolicitudCertificado;
                arregloProducto.fechaSolicitud = value.fechaSolicitud;
                $scope.solicitudesCertificados.push(arregloProducto);
            });
        });
    }

    $scope.traeSolicitud = function () {
        $http.post("exportacion/php/traeSolicitudCertificado.php").success(function (data) {
            angular.forEach(data, function (value) {
                var cadenaDatos = value.datosProducto;
                var arregloProducto = JSON.parse(cadenaDatos);
                arregloProducto.idSolicitudCertificado = value.idSolicitudCertificado;
                arregloProducto.fechaSolicitud = value.fechaSolicitud;
                $scope.solicitudesCertificados.push(arregloProducto);
            });
        });
    };

    $scope.traeMenuSolicitud = function (miel) {
        $http.post("exportacion/php/traeSolicitudCertificado.php?miel=" + miel).success(function (data) {
            console.log(data)
            angular.forEach(data, function (value) {
                if (value.idSolicitudCertificado == "") {
                    var arregloProducto = [];
                    arregloProducto.idLoteInterno = value.idLoteInterno;
                    arregloProducto.lote = value.lote;
                    arregloProducto.fechaSalida = value.fechaSalida;
                    arregloProducto.idSolicitudCertificado = 0;
                    arregloProducto.fechaSolicitud = "";
                    arregloProducto.cliente = "";
                    arregloProducto.datosAnexo = "";
                    arregloProducto.numContrato = "";
                    arregloProducto.sinLote = value.sinLote == 1 ? true : false;
                    $scope.solicitudesCertificados.push(arregloProducto);
                } else {
                    var cadenaDatos = value.datosProducto;
                    console.log(cadenaDatos)
                    var arregloProducto = JSON.parse(cadenaDatos);
                    var datoNombre = value.datosCliente;
                    var nombre = JSON.parse(datoNombre);
                    arregloProducto.idLoteInterno = value.idLoteInterno;
                    arregloProducto.lote = value.lote;
                    arregloProducto.idSolicitudCertificado = value.idSolicitudCertificado;
                    arregloProducto.fechaSolicitud = value.fechaSolicitud;
                    arregloProducto.fechaSalida = value.fechaSalida;
                    arregloProducto.cliente = nombre.nombre;
                    arregloProducto.datosAnexo = value.datosAnexo;
                    arregloProducto.numContrato = value.numContrato;
                    arregloProducto.sinLote = value.sinLote == 1 ? true : false;
                    $scope.solicitudesCertificados.push(arregloProducto);
                }
            });
        });
    }

    if ($scope.clienteExportador > 0) {
        $scope.traeListaExportadores();
        $http.post("exportacion/php/traeClienteExportador.php?idClienteExportador=" + $scope.clienteExportador).success(function (data) {
            var cadenaCliente = data.datosCliente;
            $scope.datosCliente = JSON.parse(cadenaCliente);
            var cadenaDestino = data.datosDestino;
            $scope.datosDestino = JSON.parse(cadenaDestino);
        });
    } else if ($scope.clienteExportador == 0) {
        $scope.traeListaExportadores();
    } else if ($location.path() == '/exportadores') {
        $http.post("exportacion/php/traeClienteExportador.php").success(function (data) {
            angular.forEach(data, function (value) {
                var cadenaCliente = value.datosCliente;
                var arreglo = JSON.parse(cadenaCliente);
                arreglo.idClienteExportador = value.idClienteExportador;
                arreglo.fecha = value.fechaAlta;
                $scope.clientesExportadores.push(arreglo);
            });
        });
    } else if ($scope.solicitudCertificado > 0) {
        $scope.traeListaExportadores();
        $http.post("exportacion/php/traeAnexo.php?idSolicitudCertificado=" + $scope.solicitudCertificado).success(function (data) {

            if (data.datosAnexo !== "") {
                var cadenaAnexo = data.datosAnexo;
                $scope.datosAnexo = JSON.parse(cadenaAnexo);
                $scope.verBoton = true;
            } else {
                $http.post("exportacion/php/traeSolicitudCertificado.php?idSolicitudCertificado=" + $scope.solicitudCertificado).success(function (data) {
                    angular.forEach(data, function (value) {
                        var transporte = value;
                        var infoTransporte = JSON.parse(transporte);
                        $scope.datosAnexo.medioTransporte = infoTransporte.medioTransporte;
                        $scope.datosAnexo.identificacion = infoTransporte.identificacionTransporte;
                        $scope.datosAnexo.idContenedor = infoTransporte.numeroContenedor;
                        $scope.datosAnexo.sello = infoTransporte.numeroFleje;
                    });
                });
                $scope.datosAnexo.medioTransporte = $scope.datosTransporte.medioTransporte;
            }
        });
    } else if ($scope.solicitudCertificado == 0) {
        $scope.traeListaExportadores();
    } else if ($location.path() == '/solicitudCertificado') {
        $scope.$watch('vista', function (val) {
            if ($scope.vista) {
                window.localStorage.setItem('VISTA_EXPORTACION', $scope.vista);
                $scope.solicitudesCertificados = [];
                $scope.traeMenuSolicitud($scope.vista);
            }
        });
    } else if ($scope.lote >= 0) {

        // aqui

        $scope.traeListaExportadores();

        var urlCertificado = 'exportacion/php/traeSolicitudCertificado.php?miel=' + $scope.tipoMiel;
        if ($scope.solicitudSinLote) {
            urlCertificado += '&sinLote=1';
            urlCertificado += '&idSolicitudCertificado=' + $scope.lote;
        } else {
            urlCertificado += '&idLoteInterno=' + $scope.lote;
            urlCertificado += '&lote=' + $scope.marcaDistintiva;

        }

        $http.post(urlCertificado).success(function (data) {
            $scope.solicitud = data.idSolicitudCertificado;
            if (data.idSolicitudCertificado == null) {
                $scope.datosProducto.marcaDistintiva = data.marcaDistintiva;
                $scope.datosProducto.cantidad = data.cantidad;
                $scope.datosProducto.pesoNeto = data.pesoNeto;
                $scope.datosProducto.pesoBruto = data.pesoBruto;
            } else {
                var cadenaProducto = data.datosProducto;
                $scope.datosProducto = JSON.parse(cadenaProducto);
                var cadenaTransporte = data.datosTransporte;
                $scope.datosTransporte = JSON.parse(cadenaTransporte);
                $scope.exportadorCliente = data.idClienteExportador;
                $scope.fechaSolicitud = data.fechaSolicitud;
            }
        });
    }

    function copiarDatos(datosCliente) {
        var deferred = $q.defer();
        $scope.datosDestino.administracionDestino = angular.copy(datosCliente.tipoAdministracion);
        $scope.datosDestino.transitoDestino = angular.copy(datosCliente.derechoTransito);
        $scope.datosDestino.terminoGenericoDestino = angular.copy(datosCliente.terminoGenerico);
        $scope.datosDestino.codigoCarreteraDestino = angular.copy(datosCliente.codigoCarretera);
        $scope.datosDestino.tramoCarreteraDestino = angular.copy(datosCliente.tramoCarretera);
        $scope.datosDestino.kilometroCarreteraDestino = angular.copy(datosCliente.kilometroCarretera);
        $scope.datosDestino.tramoCaminoDestino = angular.copy(datosCliente.tramoCamino);
        $scope.datosDestino.margenDestino = angular.copy(datosCliente.margen);
        $scope.datosDestino.kilometroCaminoDestino = angular.copy(datosCliente.kilometroCamino);
        $scope.datosDestino.nombreDestino = angular.copy(datosCliente.nombre);
        $scope.datosDestino.numeroEstablecimiento = angular.copy(datosCliente.establecimiento);
        $scope.datosDestino.calleDestino = angular.copy(datosCliente.calle);
        $scope.datosDestino.numeroExteriorDestino = angular.copy(datosCliente.numeroExterior);
        $scope.datosDestino.numeroInteriorDestino = angular.copy(datosCliente.numeroInterior);
        $scope.datosDestino.codigoPostalDestino = angular.copy(datosCliente.codigoPostal);
        $scope.datosDestino.asentamientoDestino = angular.copy(datosCliente.asentamiento);
        $scope.datosDestino.localidadDestino = angular.copy(datosCliente.localidad);
        $scope.datosDestino.municipioDestino = angular.copy(datosCliente.municipio);
        $scope.datosDestino.estadoDestino = angular.copy(datosCliente.estado);
        $scope.datosDestino.entreCallesDestino = angular.copy(datosCliente.entreCalles);
        $scope.datosDestino.callePosteriorDestino = angular.copy(datosCliente.callePosterior);
        $scope.datosDestino.medioDestino = angular.copy(datosCliente.medio);
        deferred.resolve(1);
        return deferred.promise;
    }

    $scope.copiarDatos = function (datosCliente) {
        swal({
            title: "¿Desea utilizar los mismos datos?",
            text: "Los datos del destino serán iguales a los del cliente",
            type: "warning",
            showCancelButton: true,
            confirmButtonText: "Sí, copiar datos.",
            closeOnConfirm: false
        }, function (confirm) {
            if (confirm) {
                copiarDatos(datosCliente).then(function (res) {
                    swal("", "Toda la información se copió", "success");
                })
            }
        });
    };

    $scope.guardarClienteExportador = function () {
        var cliente = JSON.stringify($scope.datosCliente);
        var destino = JSON.stringify($scope.datosDestino);
        $scope.datosCuestionario.datosCliente = cliente;
        $scope.datosCuestionario.datosDestino = destino;

        // if ($scope.datosCliente.medio == undefined || $scope.datosDestino.medioDestino == undefined) {
        //     growl.info('Seleccione un medio (carretera o camino.)', 'info');
        // } else if ($scope.datosCliente.medio == 2) {
        //     if ($scope.datosCliente.terminoGenerico == '') {
        //         growl.info('Seleccione un término genérico', 'info');
        //     }
        // } else if ($scope.datosCliente.medioDestino == 2) {
        //     if ($scope.datosDestino.terminoGenericoDestino == '') {
        //         growl.info('Seleccione un término genérico', 'info');
        //     }

        // } else {
        if ($scope.clienteExportador > 0) {
            $http.post("exportacion/php/guardarClienteExportador.php?idClienteExportador=" + $scope.clienteExportador, $scope.datosCuestionario).success(function (info) {
                swal("¡Éxito!", "Registro agregado", "success");
                $scope.traeClientesExportadores();
                return window.location.href = "#/exportadores";
            });
        } else {
            $http.post("exportacion/php/guardarClienteExportador.php", $scope.datosCuestionario).success(function (info) {
                swal("¡Éxito!", "Registro agregado", "success");
                $scope.traeClientesExportadores();
                return window.location.href = "#/exportadores";
            });
        }
        // }
    };

    $scope.guardarSolicitudCertificado = function () {
        if ($scope.exportadorCliente == undefined) {
            growl.info("Seleccione un cliente", 'info');
        } else {
            $scope.datosProducto.paisProcedencia = $scope.datosProducto.paisOrigen;
            $scope.datosProducto.pesoBruto = $scope.datosProducto.pesoBruto;
            var producto = JSON.stringify($scope.datosProducto);
            var transporte = JSON.stringify($scope.datosTransporte);
            $scope.datosCuestionario2.fechaSolicitud = $scope.fechaSolicitud;
            $scope.datosCuestionario2.idClienteExportador = $scope.exportadorCliente;
            $scope.datosCuestionario2.idLoteInterno = $scope.lote;
            $scope.datosCuestionario2.lote = $scope.marcaDistintiva ? $scope.marcaDistintiva : 'NA';
            $scope.datosCuestionario2.datosProducto = producto;
            $scope.datosCuestionario2.datosTransporte = transporte;
            $scope.datosCuestionario2.tipoMiel = $scope.tipoMiel;
            $scope.datosCuestionario2.sinLote = $scope.solicitudSinLote ? 1 : 0;
            if ($scope.solicitud > 0) {
                $http.post("exportacion/php/guardarSolicitudCertificado.php?idSolicitudCertificado=" + $scope.solicitud, $scope.datosCuestionario2).success(function (info) {
                    swal("Exito!", "Registro agregado", "success");
                    $scope.traeSolicitud();
                    return window.location.href = "#/solicitudCertificado";
                });
            } else {
                $http.post("exportacion/php/guardarSolicitudCertificado.php", $scope.datosCuestionario2).success(function (info) {
                    swal("Exito!", "Registro agregado", "success");
                    $scope.traeSolicitud();
                    return window.location.href = "#/solicitudCertificado";
                });
            }
        }
    };

    $scope.imprimirSolicitudCertificado = function (miel, idSolicitudCertificado) {
        window.open('reportes/administrativo/pdfSolicitudCZE.php?id=' + idSolicitudCertificado + '&miel=' + miel, '_blank');
    };

    $scope.guardarAnexo = function () {
        $http.post("exportacion/php/guardarAnexo.php?idSolicitudCertificado=" + $scope.solicitudCertificado, $scope.datosAnexo).success(function (info) {
            swal("Éxito!", "Registro agregado", "success");
            $http.post("exportacion/php/traeAnexo.php?idSolicitudCertificado=" + $scope.solicitudCertificado).success(function (data) {
                if (data.datosAnexo !== "") {
                    var cadenaAnexo = data.datosAnexo;
                    $scope.datosAnexo = JSON.parse(cadenaAnexo);
                    $scope.verBoton = true;
                }
            });
        });
    }

    $scope.imprimirAnexo = function (valor) {
        switch (valor) {
            case 1:
                window.open('reportes/administrativo/anexoSolicitudCZE.php?id=' + $scope.solicitudCertificado, '_blank');
                break;
            case 2:
                window.open('reportes/administrativo/anexoSolicitudCZE_aleman.php?id=' + $scope.solicitudCertificado, '_blank');
                break;
            case 3:
                window.open('reportes/administrativo/anexoSolicitudCZE_ingles.php?id=' + $scope.solicitudCertificado, '_blank');
                break;
        }
    };

    $scope.modalContrato = function (idSolicitudCertificado, index) {
        $("#altaContrato").modal();
        $scope.contrato.idSolicitudCertificado = idSolicitudCertificado;
        $scope.contrato.index = index;
        $http.post("exportacion/php/traeAnexo.php?contrato=0&idSolicitudCertificado=" + $scope.contrato.idSolicitudCertificado).success(function (data) {
            $scope.loteInterno = data.idLoteInterno;
            $scope.marcaDistintiva = data.lote;
        });
    }

    $scope.guardarNumeroContrato = function (contrato) {
        var id = contrato.index;
        $http.post("exportacion/php/guardarNumeroContrato.php", contrato).success(function (info) {
            $http.post("exportacion/php/traeAnexo.php?contrato=0&idSolicitudCertificado=" + contrato.idSolicitudCertificado).success(function (data) {
                var contrato = data.numContrato;
                $scope.$watch('solicitudesCertificados', function (val) {
                    var objeto = val[id];
                    objeto.numContrato = contrato;
                });
            });
            swal("Éxito!", "Registro agregado", "success");
            $("#altaContrato").modal('hide');
            $scope.contrato = {};
        });
    }

    $scope.editarSolicitud = function (info, vista) {
        if (info.sinLote) {
            window.localStorage.setItem('marcaDistintiva', info.lote);
            window.location.href = '#/nvaSolicitud/' + info.idSolicitudCertificado + '/' + vista + '?sinLote';
        } else {
            window.localStorage.setItem('marcaDistintiva', info.lote);
            window.location.href = '#/nvaSolicitud/' + info.idLoteInterno + '/' + vista;
        }
    }

}]);