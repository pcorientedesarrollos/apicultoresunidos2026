form.config(function ($routeProvider) {
    $routeProvider.when('/certificadosLotes', {
        templateUrl: 'laboratorio/certificadosDeLotesInternos.html',
        controller: 'certificadoLoteCtrl'
    }).when('/capturaCertificadoLote/:idLoteInterno/:tipoMiel', {
        templateUrl: 'laboratorio/capturaCertificadoLotes.html',
        controller: 'certificadoLoteCtrl'
    })
});

form.controller('certificadoLoteCtrl', function ($scope, $http, $routeParams, growl, busqueda, $location, $rootScope) {

    $scope.informacionLotes = new Array();
    $scope.verTipoDeMiel = '1';
    $scope.idLote = $routeParams.idLoteInterno;
    $scope.tipoMiel = $routeParams.tipoMiel;
    $scope.encabezado = {};
    $scope.encabezado.idLote = $scope.idLote;
    $scope.encabezado.tipoDeMiel = $scope.tipoMiel;
    $scope.mostrarMarca = false;
    $scope.estructuraTabla = new Array();
    var date = new Date();
    var _mes = date.getMonth() + 1;
    $scope.mostrarMes = _mes.toString();

    $http.get('almacen/php/consultaComboMeses.php').success(function (data) {
        $scope.listaDeMeses = data;
    });

    function traeVistaPrincipal(tipoDeMiel) {
        $scope.informacionLotes = null;
        $scope.cargandoDatos = true;
        url = 'calidad/php/traeInformacionTotalLotesInternos.php';
        if ($scope.mostrarMes && $scope.mostrarMes !== "null") {
            url += '?mes=' + $scope.mostrarMes;
        }
        $http.post(url, tipoDeMiel).success(function (data) {
            if (data.error) {
                growl.error(data.message);
            }
            $scope.informacionLotes = data.data;
            $scope.cargandoDatos = false;
        });
    }

    if ($location.path() == '/certificadosLotes') {
        if (window.localStorage.getItem('seleccionTipoMiel') != null) {
            $scope.verTipoDeMiel = window.localStorage.getItem('seleccionTipoMiel');
        }
        $scope.$watch('verTipoDeMiel', function (tipoMiel) {
            if (tipoMiel) {
                window.localStorage.setItem('seleccionTipoMiel', tipoMiel);
                traeVistaPrincipal(tipoMiel);
            }
        })

        if (window.localStorage.getItem('seleccionMes') != null) {
            $scope.mostrarMes = window.localStorage.getItem('seleccionMes');
        }
        $scope.$watch('mostrarMes', function (mesElegido) {
            window.localStorage.setItem('seleccionMes', mesElegido);
            traeVistaPrincipal($scope.verTipoDeMiel);
        })
    }

    function armarTabla(idLote) {

        $http.get('laboratorio/php/detalleCertificado.php?miel=' + $scope.tipoMiel + '&idLote=' + idLote).success(function (data) {
            if (data.hasOwnProperty('error')) {
                if (!data.error) {
                    if (data.data == '0') {
                        $scope.detalle = {
                            sensoriales: {
                                apariencia: {
                                    idAnalisis: 18,
                                    parametro: "",
                                    resultado: "Cumple",
                                    desviacion: "N/A",
                                    comentario: "N/A",
                                    tecnicas: "Evaluación Sensorial",
                                    minimo: "",
                                    maximo: ""
                                },
                                color: {
                                    idAnalisis: 19,
                                    parametro: "",
                                    resultado: "Ámbar",
                                    desviacion: "N/A",
                                    comentario: "N/A",
                                    tecnicas: "Evaluación Sensorial",
                                    minimo: "",
                                    maximo: "",
                                    cuadroSensoriales: "Propio característico, Variable de blanca agua , extra blanca, blanca, extra clara ámbar, ámbar clara, ámbar y oscura"
                                },
                                olor: {
                                    idAnalisis: 20,
                                    parametro: "",
                                    resultado: "Característico",
                                    desviacion: "N/A",
                                    comentario: "N/A",
                                    tecnicas: "Evaluación Sensorial",
                                    minimo: "",
                                    maximo: ""
                                },
                                sabor: {
                                    idAnalisis: 21,
                                    parametro: "",
                                    resultado: "Característico",
                                    desviacion: "N/A",
                                    comentario: "N/A",
                                    tecnicas: "Evaluación Sensorial",
                                    minimo: "",
                                    maximo: ""
                                }
                            },
                            caracteristicas: {
                                tipoDeMiel: {
                                    idAnalisis: 1,
                                    parametro: "",
                                    resultado: "",
                                    desviacion: "N/A",
                                    comentario: "N/A",
                                    tecnicas: "NA",
                                    minimo: "",
                                    maximo: ""
                                },
                                humedad: {
                                    idAnalisis: 2,
                                    parametro: "",
                                    resultado: "",
                                    desviacion: "N/A",
                                    comentario: "N/A",
                                    tecnicas: "Refractómetrico",
                                    minimo: "",
                                    maximo: ""
                                },
                                color: {
                                    idAnalisis: 3,
                                    parametro: "",
                                    resultado: "",
                                    desviacion: "N/A",
                                    comentario: "N/A",
                                    tecnicas: "Escala  Pfund",
                                    minimo: "",
                                    maximo: ""
                                },
                                hmf: {
                                    idAnalisis: 4,
                                    parametro: "",
                                    resultado: "",
                                    desviacion: "N/A",
                                    comentario: "N/A",
                                    tecnicas: "Método de Winkler",
                                    minimo: "",
                                    maximo: ""
                                },
                                adulteracion: {
                                    idAnalisis: 5,
                                    parametro: "",
                                    resultado: "",
                                    desviacion: "N/A",
                                    comentario: "N/A",
                                    tecnicas: "NMX-F-036-2006",
                                    minimo: "",
                                    maximo: ""
                                },
                                fg: {
                                    idAnalisis: 6,
                                    parametro: "",
                                    resultado: "",
                                    desviacion: "N/A",
                                    comentario: "N/A",
                                    tecnicas: "NMX-F-036-2006",
                                    minimo: "",
                                    maximo: ""
                                },
                                brix: {
                                    idAnalisis: 16,
                                    parametro: "",
                                    resultado: "",
                                    desviacion: "N/A",
                                    comentario: "N/A",
                                    tecnicas: "Refractómetrico",
                                    minimo: "",
                                    maximo: ""
                                },
                                solidos: {
                                    idAnalisis: 17,
                                    parametro: "",
                                    resultado: "",
                                    desviacion: "N/A",
                                    comentario: "N/A",
                                    tecnicas: "NMX-F-036-2006",
                                    minimo: "",
                                    maximo: ""
                                }
                            },
                            antibioticos: {
                                estreptomicinas: {
                                    idAnalisis: 7,
                                    parametro: "",
                                    resultado: "",
                                    desviacion: "N/A",
                                    comentario: "N/A",
                                    tecnicas: "Charm II",
                                    minimo: "",
                                    maximo: ""
                                },
                                sulfametazinas: {
                                    idAnalisis: 8,
                                    parametro: "",
                                    resultado: "",
                                    desviacion: "N/A",
                                    comentario: "N/A",
                                    tecnicas: "Charm II",
                                    minimo: "",
                                    maximo: ""
                                },
                                tetraciclinas: {
                                    idAnalisis: 9,
                                    parametro: "",
                                    resultado: "",
                                    desviacion: "N/A",
                                    comentario: "N/A",
                                    tecnicas: "Charm II",
                                    minimo: "",
                                    maximo: ""
                                }
                            },
                            microbiologicos: {
                                aerobios: {
                                    idAnalisis: 10,
                                    parametro: "1000 UFC/g",
                                    resultado: "",
                                    desviacion: "N/A",
                                    comentario: "N/A",
                                    tecnicas: "NOM-092-SSA-1994",
                                    minimo: "",
                                    maximo: ""
                                },
                                coliformes: {
                                    idAnalisis: 11,
                                    parametro: "0 UFC/g",
                                    resultado: "",
                                    desviacion: "N/A",
                                    comentario: "N/A",
                                    tecnicas: "NOM-113-SSA1-1994",
                                    minimo: "",
                                    maximo: ""
                                },
                                salmonella: {
                                    idAnalisis: 12,
                                    parametro: "Negativo",
                                    resultado: "",
                                    desviacion: "N/A",
                                    comentario: "N/A",
                                    // tecnicas: "NOM-114-SSA1-1994",
                                    tecnicas: "NOM-113-SSA1-1994",
                                    minimo: "",
                                    maximo: ""
                                },
                                hongos: {
                                    idAnalisis: 13,
                                    parametro: "10 UFC/g",
                                    resultado: "",
                                    desviacion: "N/A",
                                    comentario: "N/A",
                                    tecnicas: "NOM-111-SSA1-1994",
                                    minimo: "",
                                    maximo: ""
                                },
                                levaduras: {
                                    idAnalisis: 14,
                                    parametro: "10 UFC/g",
                                    resultado: "",
                                    desviacion: "N/A",
                                    comentario: "N/A",
                                    tecnicas: "NOM-111-SSA-1994",
                                    minimo: "",
                                    maximo: ""
                                },
                                // listeria: {
                                //     idAnalisis: 15,
                                //     parametro: "Ausencia",
                                //     resultado: "",
                                //     desviacion: "N/A",
                                //     comentario: "N/A",
                                //     tecnicas: "Placas Petriflm 3M",
                                //     minimo: "",
                                //     maximo: ""
                                // }
                            }
                        };
                    } else {
                        $scope.encabezado = data.data;
                        $scope.detalle = $scope.encabezado.detalle;
                        var brix = {};
                        brix = $scope.detalle.caracteristicas.find(carac => carac.idAnalisis == '16');
                        if (brix == undefined) {
                            var objBrix = {
                                idAnalisis: "16",
                                parametro: "",
                                resultado: "",
                                desviacion: "N/A",
                                comentario: "N/A",
                                tecnicas: "",
                                minimo: "",
                                maximo: ""
                            }
                            $scope.detalle.caracteristicas.push(objBrix);
                        }
                        var solidos = {};
                        solidos = $scope.detalle.caracteristicas.find(carac => carac.idAnalisis == '17');
                        if (solidos == undefined) {
                            var objSolidos = {
                                idAnalisis: "17",
                                parametro: "",
                                resultado: "",
                                desviacion: "N/A",
                                comentario: "N/A",
                                tecnicas: "",
                                minimo: "",
                                maximo: ""
                            }
                            $scope.detalle.caracteristicas.push(objSolidos);
                        }
                        var apariencia = {};
                        apariencia = $scope.detalle.sensoriales.find(carac => carac.idAnalisis == '18');
                        if (apariencia == undefined) {
                            var objApariencia = {
                                idAnalisis: "18",
                                parametro: "",
                                resultado: "",
                                desviacion: "N/A",
                                comentario: "N/A",
                                tecnicas: "",
                                minimo: "",
                                maximo: ""
                            }
                            $scope.detalle.sensoriales.push(objApariencia);
                        }
                        var color = {};
                        color = $scope.detalle.sensoriales.find(carac => carac.idAnalisis == '19');
                        if (color == undefined) {
                            var objColor = {
                                idAnalisis: "19",
                                parametro: "",
                                resultado: "",
                                desviacion: "N/A",
                                comentario: "N/A",
                                tecnicas: "",
                                minimo: "",
                                maximo: "",
                                cuadroSensoriales: ""
                            }
                            $scope.detalle.sensoriales.push(objColor);
                        }
                        var olor = {};
                        olor = $scope.detalle.sensoriales.find(carac => carac.idAnalisis == '20');
                        if (olor == undefined) {
                            var objOlor = {
                                idAnalisis: "20",
                                parametro: "",
                                resultado: "",
                                desviacion: "N/A",
                                comentario: "N/A",
                                tecnicas: "",
                                minimo: "",
                                maximo: ""
                            }
                            $scope.detalle.sensoriales.push(objOlor);
                        }
                        var sabor = {};
                        sabor = $scope.detalle.sensoriales.find(carac => carac.idAnalisis == '21');
                        if (sabor == undefined) {
                            var objSabor = {
                                idAnalisis: "21",
                                parametro: "",
                                resultado: "",
                                desviacion: "N/A",
                                comentario: "N/A",
                                tecnicas: "",
                                minimo: "",
                                maximo: ""
                            }
                            $scope.detalle.sensoriales.push(objSabor);
                        }

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
                            if (value.idAnalisis == '8') {
                                $scope.detalle.antibioticos.sulfametazinas = value;
                            }
                            if (value.idAnalisis == '7') {
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
                    }
                } else {
                    swal('Error', data.message, 'error');
                }
            } else {
                growl.error('Error');
                console.error(data);
            }
        });

    };

    function traeMarcaInterna() {
        $http.post('laboratorio/php/traeMarcaInterna.php', $scope.idLote)
            .success(function (data) {
                $scope.cargandoDatos = false;
                if (typeof (data) == 'object' && data.hasOwnProperty('error')) {
                    if (data.error) {
                        swal('', data.message, 'warning');
                    } else {
                        var datos = data.data;
                        if (datos == '0') {
                            $scope.mostrarMarca = false;
                        } else {
                            $scope.encabezado.marcacionFinal = datos.marcaFinalCliente;
                            $scope.mostrarMarca = true;
                        }
                    }
                } else {
                    growl.error('Error');
                    console.error(data);
                }
            });
    };

    if ($scope.idLote) {
        armarTabla($scope.idLote);
        traeMarcaInterna();
        // $http.get('laboratorio/php/traeListaDeParametros.php').success(function (data) {
        //     $scope.listaParametros = data;
        // });
        if ($scope.tipoMiel == '1') {
            $scope.tipoDeMiel = 'Miel 100% pura de abeja';
        } else if ($scope.tipoMiel == '2') {
            $scope.tipoDeMiel = 'Miel 100% orgánica';
        } else if ($scope.tipoMiel == '5') {
            $scope.tipoDeMiel = 'Miel 100% mantequilla';
        } else if ($scope.tipoMiel == '6') {
            $scope.tipoDeMiel = 'Miel 100% altiplano';
        } else if ($scope.tipoMiel == '7') {
            $scope.tipoDeMiel = 'Miel 100% naranjo';
        } else if ($scope.tipoMiel == '8') {
            $scope.tipoDeMiel = 'Miel 100% aguacate';
        } else if ($scope.tipoMiel == '9') {
            $scope.tipoDeMiel = 'Miel 100% mezquite';
        }
        // $scope.$watch('encabezado.idParametro', function (valor) {
        //     $scope.respaldo = [];
        //     $http.get('laboratorio/php/traeListaRangos.php?valor=' + valor).success(function (datos) {
        //         $rootScope.tablaAnalisis = datos;
        //         $scope.rangos = {};
        //         angular.forEach($scope.tablaAnalisis, function (e) {
        //             if (e.idAnalisis == '2') { $scope.rangos.humedad = e.rango; }
        //             if (e.idAnalisis == '3') { $scope.rangos.color = e.rango; }
        //             if (e.idAnalisis == '4') { $scope.rangos.hmf = e.rango; }
        //             if (e.idAnalisis == '5') { $scope.rangos.adulteracion = e.rango; }
        //             if (e.idAnalisis == '6') { $scope.rangos.fg = e.rango; }
        //             if (e.idAnalisis == '7') { $scope.rangos.sulfametazinas = e.rango; }
        //             if (e.idAnalisis == '8') { $scope.rangos.estreptomicinas = e.rango; }
        //             if (e.idAnalisis == '9') { $scope.rangos.tetraciclinas = e.rango; }
        //             if (e.idAnalisis == '10') { $scope.rangos.aerobios = e.rango; }
        //             if (e.idAnalisis == '11') { $scope.rangos.coliformes = e.rango; }
        //             if (e.idAnalisis == '12') { $scope.rangos.salmonella = e.rango; }
        //             if (e.idAnalisis == '13') { $scope.rangos.hongos = e.rango; }
        //             if (e.idAnalisis == '14') { $scope.rangos.levaduras = e.rango; }
        //             if (e.idAnalisis == '15') { $scope.rangos.listeria = e.rango; }
        //         });
        //     });
        // });
    }

    $scope.guardarCertificado = function () {
        $scope.datosGuardar = new Array();
        $scope.datosGuardar.push($scope.encabezado);
        $scope.datosGuardar.push($scope.detalle);
        console.log($scope.datosGuardar);
        $http.post('laboratorio/php/guardarCertificadoLoteTerminado.php', $scope.datosGuardar).success(function (data) {
            console.log(data);
            if (typeof (data) == 'object' && data.hasOwnProperty('error')) {
                if (data.error) {
                    swal('Error', data.message, 'error');
                } else {
                    swal('Listo', data.message, 'success');
                    window.location = '#/certificadosLotes';
                }
            } else {
                growl.error('Error');
                console.error(data);
            }
        });
    }

    $scope.imprimirCertificado = function () {
        window.open('reportes/laboratorio/pdfCertificadoLote.php?miel=' + $scope.tipoMiel + '&idLote=' + $scope.idLote, '_blank');
    };

});