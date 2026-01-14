form.config(function ($routeProvider) {
    $routeProvider.when('/salidasReactivos', {
        templateUrl: 'laboratorio/salidasDeReactivos.html',
        controller: 'reactivosSalidaCtrl'
    }).when('/salidaReactivo/:idSalida/:miel', {
        templateUrl: 'laboratorio/capturaSalidaDeReactivos.html',
        controller: 'reactivosSalidaCtrl'
    })
});

form.controller('reactivosSalidaCtrl', function ($scope, $http, $routeParams, growl, $location, $rootScope) {

    $scope.salidasReactivos = new Array();
    $scope.idSalida = $routeParams.idSalida;
    $scope.miel = $routeParams.miel;
    $scope.guardandoDatos = false;
    $scope.cargandoDatos = false;
    $scope.nuevaSalida = {};
    $scope.listaFolios = new Array();
    $scope.reactivos = new Array();
    $scope.opcionCosecha = '1';
    $scope.habilitarOpciones = false;



    var date = new Date();
    var _mes = date.getMonth() + 1;
    $scope.mostrarMes = _mes.toString();
    $http.get('almacen/php/consultaComboMeses.php').success(function (data) {
        $scope.listaDeMeses = data;
    });

    $http.get('catalogos/php/traeReactivos.php').success(function (data) {
        $scope.listaReactivos = data.reactivos;
    });

    $http.get('laboratorio/php/listaPersonalLaboratorio.php').success(function (data) {
        $scope.personalLaboratorio = data;
    });

    $http.post("almacenSobrantes/php/listaTiposDeSobrantes.php").success(function (info) {
        $scope.listaSobrantes = info;
    });


    function traeLotes(miel) {
        $http.post("laboratorio/php/listaLotesExperimentales.php?miel=" + miel).success(function (info) {
            $scope.listaExp = info;
        });
    }

    $scope.$watch('nuevaSalida.tipoMiel', function (miel) {
        traeLotes(miel)
    });

    $scope.traeExperimental = function (miel, exp) {
        $http.get('calidad/php/traeDetalleLoteExp.php?miel=' + miel + '&idLoteExperimental=' + exp).success(function (data) {
            $scope.listaFolios = data.data.experimental;
        });
    }

    $scope.$watch('nuevaSalida.almacen', function (almacen) {
        $scope.nuevaSalida.sobrante = 0;
        if (almacen == '1') {
            $scope.habilitarOpciones = false;
        } else if (almacen == '2') {
            $scope.habilitarOpciones = true;
        }
    });

    $scope.$watch('nuevaSalida.opcionBusqueda', function (busqueda) {
        if (busqueda == 3) {
            $scope.listaFolios = [];
        } 
        if(busqueda == 4){
            $scope.listaFolios = [];
        }
    });

    function traeFolio() {
        var datos = {
            almacen: $scope.nuevaSalida.almacen,
            miel: $scope.nuevaSalida.tipoMiel,
            tipo: $scope.nuevaSalida.tipoAnalisis,
            folio: $scope.nuevaSalida.folio,
            sobrante: $scope.nuevaSalida.sobrante,
        }
        if (datos.folio != "") {
            $http.post('laboratorio/php/buscarFolioConformacion.php', datos)
                .success(function (respuesta) {
                    if (respuesta.content == 1) {
                        growl.warning("Verifique. Folio no disponible");
                        $scope.nuevaSalida.folio = null;
                    } else if (respuesta.content == 0) {
                        growl.warning("Verifique. Folio sin existencia");
                        $scope.nuevaSalida.folio = null;
                    } else {
                        $scope.nuevaSalida.folio = null;
                        $scope.listaFolios.push(respuesta.content);
                    }
                });
        } else {
            growl.warning("Verifique su captura");
        }
    }

    $scope.validarFolio = function () {
        if ((event.keyCode == 13)) {
            if ($scope.nuevaSalida.almacen == '1') {
                if ($scope.nuevaSalida.tipoAnalisis == undefined || $scope.nuevaSalida.tipoMiel == undefined) {
                    growl.warning("Verifique su captura, se requieren tipos de miel y de análisis");
                } else {
                    traeFolio();
                }
            } else if ($scope.nuevaSalida.almacen == '2') {
                if ($scope.nuevaSalida.tipoAnalisis == undefined || $scope.nuevaSalida.tipoMiel == undefined || $scope.nuevaSalida.sobrante == 0) {
                    growl.warning("Verifique su captura, se requieren tipos de miel, de análisis y clasificación");
                } else {
                    traeFolio();
                }
            }
        }
    };

    function traeFolios() {
        var datos = {
            almacen: $scope.nuevaSalida.almacen,
            miel: $scope.nuevaSalida.tipoMiel,
            tipo: $scope.nuevaSalida.tipoAnalisis,
            folioUno: $scope.nuevaSalida.folioUno,
            folioDos: $scope.nuevaSalida.folioDos,
            sobrante: $scope.nuevaSalida.sobrante,
        }
        $http.post('laboratorio/php/buscarFoliosConformacion.php', datos)
            .success(function (respuesta) {
                if (respuesta.content == 1) {
                    growl.warning("Verifique. Algún folio no está disponible");
                    $scope.nuevaSalida.folioUno = null;
                    $scope.nuevaSalida.folioDos = null;
                } else if (respuesta.content == 0) {
                    growl.warning("Verifique. Algún folio está sin existencia");
                    $scope.nuevaSalida.folioUno = null;
                    $scope.nuevaSalida.folioDos = null;
                } else {
                    var folios = respuesta.content;
                    folios.forEach(function (f) {
                        $scope.listaFolios.push(f);
                    });
                    $scope.nuevaSalida.folioUno = null;
                    $scope.nuevaSalida.folioDos = null;
                }
            });
    }

    $scope.validarFolios = function () {
        if ((event.keyCode == 13)) {
            if ($scope.nuevaSalida.almacen == '1') {
                if ($scope.nuevaSalida.tipoAnalisis == undefined || $scope.nuevaSalida.tipoMiel == undefined) {
                    growl.warning("Verifique su captura, se requieren tipos de miel y de análisis");
                } else {
                    traeFolios();
                }
            } else if ($scope.nuevaSalida.almacen == '2') {
                if ($scope.nuevaSalida.tipoAnalisis == undefined || $scope.nuevaSalida.tipoMiel == undefined || $scope.nuevaSalida.sobrante == 0) {
                    growl.warning("Verifique su captura, se requieren tipos de miel, de análisis y clasificación");
                } else {
                    traeFolios();
                }
            }
        }
    };

    function traeSalidas(miel) {
        $scope.salidasReactivos = null;
        $scope.cargandoDatos = true;
        url = 'laboratorio/php/salidasDeReactivos.php?miel=' + miel;
        if ($scope.mostrarMes && $scope.mostrarMes !== "null") {
            url += '&mes=' + $scope.mostrarMes;
        }
        $http.get(url).success(function (data) {
            if (data.error) {
                console.error(data.message);
            }
            $scope.salidasReactivos = data.data;
            $scope.cargandoDatos = false;
        });
    };


    $scope.updateModel = function () {
        for (var i = 0; i < $scope.nuevaSalida.muestras; i++) {
            $scope.listaFolios.push({});
        }
    };
    if ($location.path() == '/salidasReactivos') {
        if (window.localStorage.getItem('seleccionTipoMiel') != null) {
            $scope.opcionCosecha = window.localStorage.getItem('seleccionTipoMiel');
        }
        $scope.$watch('opcionCosecha', function (tipoMiel) {
            if (tipoMiel) {
                window.localStorage.setItem('seleccionTipoMiel', tipoMiel);
                traeSalidas(tipoMiel);
            }
        })

        if (window.localStorage.getItem('seleccionMes') != null) {
            $scope.mostrarMes = window.localStorage.getItem('seleccionMes');
        }
        $scope.$watch('mostrarMes', function (mesElegido) {
            window.localStorage.setItem('seleccionMes', mesElegido);
            traeSalidas($scope.opcionCosecha);
        })
    }

    if ($scope.idSalida > 0) {
        traeSalida($scope.idSalida)
    } else {
        $scope.nuevaSalida.tipoAnalisis = '1';
    }

    $scope.anadirReactivo = function (reactivo, resultado, cantidad) {
        var informacion = {
            reactivo: reactivo.idReactivo,
            nombre: reactivo.reactivo,
            resultadoReactivo: resultado,
            cantidad: cantidad
        }
        $scope.reactivos.push(informacion);
        $scope.nuevaSalida.reactivo = '';
        $scope.nuevaSalida.resultadoReactivo = null;
        $scope.nuevaSalida.cantidad = '';
    }

    $scope.guardarSalida = function (edicion = false) {
        if (edicion) {
            $http.post("laboratorio/php/guardarSalidaReactivos.php?edicion=1", $scope.nuevaSalida).success(function (info) {
                if (info.hasOwnProperty('error')) {
                    if (!info.error) {
                        $rootScope.lockTemplate = false;
                        swal('', info.message, info.swal);
                        window.location.href = '#/salidasReactivos';
                    } else {
                        swal('Error', info.message, 'error');
                    }
                } else {
                    growl.error('Error');
                    console.error(info);
                }
            });
        } else {
            var informacion = [];
            informacion.push($scope.nuevaSalida);
            informacion.push($scope.listaFolios);
            informacion.push($scope.reactivos);
            $http.post("laboratorio/php/guardarSalidaReactivos.php", informacion).success(function (info) {
                if (info.hasOwnProperty('error')) {
                    if (!info.error) {
                        $rootScope.lockTemplate = false;
                        swal('', info.message, info.swal);
                        window.location.href = '#/salidasReactivos';
                    } else {
                        swal('Error', info.message, 'error');
                    }
                } else {
                    growl.error('Error');
                    console.error(info);
                }
            });
        }
    };


    $scope.pdfDescarga = function (idSalida) {
        window.open('reportes/laboratorio/conformacionHomogeneoPdf.php?idSalida=' + idSalida + '&miel=' + $scope.miel);
    };

    function traeSalida(idSalida) {
        $http.get('laboratorio/php/detalleSalidaReactivo.php?idSalida=' + idSalida + '&miel=' + $scope.miel).success(function (data) {
            if (data.hasOwnProperty('error')) {
                if (!data.error) {
                    $scope.nuevaSalida = data.data;
                    $scope.listaFolios = $scope.nuevaSalida.listaFolios;
                    $scope.reactivos = $scope.nuevaSalida.reactivos;
                } else {
                    swal('Error', data.message, 'error');
                }
            } else {
                growl.error('Error');
                console.error(data);
            }
        });
    };

    $scope.eliminarConcepto = function (index) {
        $scope.listaFolios.splice(index, 1);
    };

    $scope.eliminarReactivo = function (index) {
        $scope.reactivos.splice(index, 1);
    };

});