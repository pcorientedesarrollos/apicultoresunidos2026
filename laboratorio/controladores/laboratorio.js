form.config(function ($routeProvider) {
    $routeProvider.when('/laboratorio', {
        templateUrl: 'laboratorio/menuLaboratorio.html',
        controller: 'laboratorio',
    }).when('/nvoLaboratorio/:idAlmacen/:tipoDeMiel', {
        templateUrl: 'laboratorio/nuevoLaboratorio.html',
        controller: 'laboratorio'
    })
});

form.controller('laboratorio', function ($scope, $http, $routeParams, growl, busqueda, $location) {

    $scope.datos = $routeParams.idAlmacen;
    $scope.tipoDeMiel = $routeParams.tipoDeMiel;
    $scope.filtroBusquedaLab = "";
    $scope.menuLab = new Array();
    $scope.idLaboratorio = $routeParams.idLaboratorio;
    $scope.labEncabezado = {};
    $scope.informacion = new Array();
    $scope.moduloLaboratorio = 0;
    $scope.habilitar = false;
    $scope.controlLab = false;
    $scope.controlPorcentajeLab = false;
    $scope.informacionLab = new Array();
    $scope.analisis = {};
    $scope.punto = {};
    $scope.detalleLab = {};
    $scope.folios = {};
    $scope.folios.inicial = "";
    $scope.folios.final = "";
    $scope.dataHumedad = new Array();
    //===========================
    // ACT. 2020
    //===========================
    $scope.cargandoDatos = false;
    $scope.verTipoDeMiel = '1';
    var date = new Date();
    var _mes = date.getMonth() + 1;
    $scope.mostrarMes = _mes.toString();

    $http.get('almacen/php/consultaComboMeses.php').success(function (data) {
        $scope.listaDeMeses = data;
    });

    function traerMenuLaboratorio(tipoDeMiel) {
        $scope.menuLab = null;
        $scope.cargandoDatos = true;
        url = 'laboratorio/php/getMenuLaboratorio.php';
        if ($scope.mostrarMes && $scope.mostrarMes !== "null") {
            url += '?mes=' + $scope.mostrarMes;
        }
        $http.post(url, tipoDeMiel).success(function (data) {
            if (data.error) {
                growl.error(data.message);
            }
            $scope.menuLab = data.data;
            $scope.cargandoDatos = false;
        });
    }

    if ($location.path() == '/laboratorio') {
        if (window.localStorage.getItem('seleccionTipoMiel') != null) {
            $scope.verTipoDeMiel = window.localStorage.getItem('seleccionTipoMiel');
        }
        $scope.$watch('verTipoDeMiel', function (tipoMiel) {
            if (tipoMiel) {
                window.localStorage.setItem('seleccionTipoMiel', tipoMiel);
                traerMenuLaboratorio(tipoMiel);
            }
        })

        if (window.localStorage.getItem('seleccionMes') != null) {
            $scope.mostrarMes = window.localStorage.getItem('seleccionMes');
        }
        $scope.$watch('mostrarMes', function (mesElegido) {
            window.localStorage.setItem('seleccionMes', mesElegido);
            traerMenuLaboratorio($scope.verTipoDeMiel);
        })
    }

    //---------- 2020 -----------


    //=====================================================================================
    //          CAMBIOS A LA PARTE DE LABORATORIO 
    //=====================================================================================

    $scope.exportarExcel = function (opcion, datos = false) {
        /**
         * Función para exportar los datos
         * 
         * 1 Para 'Exportar'
         * 2 Para 'Reporte C13'
         * 3 Para 'Antibioticos'
         */
        // if (!$scope.verTipoDeMiel) {
        //     growl.info('Selecciona un tipo de Miel');
        //     return;
        // }
        switch (opcion) {
            case 1:
                // if (datos.opcion == '1') {
                return window.location.href = 'reportes/xlsLaboratorio.php?tipoDeMiel=' + $scope.verTipoDeMiel;
            // } else if (datos.opcion == '2') {
            //     return window.location.href = 'reportes/xlsLaboratorioPeriodo.php?tipoDeMiel=' + $scope.verTipoDeMiel + '$fechaUno=' + datos.fechaUno + '&fechaDos=' + datos.fechaDos;
            // }
            // break;
            case 2:
                return window.location.href = 'reportes/laboratorio/xlsAdulteracionLizeth.php?tipoDeMiel=' + $scope.verTipoDeMiel;
            // break;
            case 3:
                return window.location.href = 'reportes/laboratorio/xlsAntibioticoValores.php?tipoDeMiel=' + $scope.verTipoDeMiel;
            // break;
            default:
                return;
            // break;
        }
    }

    if ($scope.datos > 0) {
        $http.post("laboratorio/php/infoDetalladaLaboratorio.php", { idAlmacen: $scope.datos, idTipoDeMiel: $scope.tipoDeMiel }).success(function (respuesta) {
            if (respuesta.error) {
                growl.error(respuesta.message);
            }
            $scope.detalleLab = respuesta.data;
            $scope.informacionLab = respuesta.data.informacionLab;
        });

        $http.post("json/laboratorio/JsnMicrobiologia.json").success(function (respuesta) {
            $scope.listaMicrobiologia = respuesta.valores;
        });

        $http.post("json/laboratorio/resultadosFinales.json").success(function (respuesta) {
            $scope.listaResultadoFinal = respuesta.resultadoFinal;
        });

        // $http.post("json/laboratorio/floraciones.json").success(function (respuesta) {
        //     $scope.floracion = respuesta.floraciones;
        // });
        $http.post("laboratorio/php/listaFloraciones.php").success(function (respuesta) {
            $scope.floracion = respuesta;
        });
    }

    $scope.guardarLaboratorio = function () {
        $scope.habilitar = true;
        $http.post("laboratorio/php/guardarLaboratorio.php", { valor: $scope.informacionLab, tipoDeMiel: $scope.tipoDeMiel }).success(function (respuesta) {
            $scope.habilitar = false;
            if (respuesta.error) {
                growl.error(respuesta.message);
            } else {
                swal('', respuesta.message, respuesta.swal);
                return window.location.href = "#/laboratorio";
            }
        });
    };

    $scope.verReporte = function () {
        if (!$scope.punto.punto || !$scope.analisis.opcion || !$scope.punto.tipoDeMiel) {
            growl.info('Selecciona los campos necesarios');
            return;
        }
        $scope.ofecha = {};
        switch ($scope.punto.punto) {
            case '1':
                if (!$scope.punto.fechaUno || !$scope.punto.fechaDos) {
                    growl.info('Selecciona un rango de fechas');
                    return;
                }
                switch ($scope.analisis.opcion) {
                    case '3':
                        return window.location.href = 'reportes/laboratorio/xlsConcentrado.php?fechaUno=' + $scope.punto.fechaUno + '&fechaDos=' + $scope.punto.fechaDos + '&tipoDeMiel=' + $scope.punto.tipoDeMiel;
                        break;
                    case '4':
                        return window.location.href = 'reportes/laboratorio/xlsAntibioticos.php?fechaUno=' + $scope.punto.fechaUno + '&fechaDos=' + $scope.punto.fechaDos + '&tipoDeMiel=' + $scope.punto.tipoDeMiel;
                        break;
                    case '5':
                        return window.location.href = 'reportes/laboratorio/xlsHMF.php?fechaUno=' + $scope.punto.fechaUno + '&fechaDos=' + $scope.punto.fechaDos + '&tipoDeMiel=' + $scope.punto.tipoDeMiel;
                        break;
                    case '6':
                        return window.location.href = 'reportes/laboratorio/xlsAdulteracion.php?fechaUno=' + $scope.punto.fechaUno + '&fechaDos=' + $scope.punto.fechaDos + '&tipoDeMiel=' + $scope.punto.tipoDeMiel;
                        break;
                    case '7':
                        return window.location.href = 'reportes/laboratorio/xlsHumedad.php?fechaUno=' + $scope.punto.fechaUno + '&fechaDos=' + $scope.punto.fechaDos + '&tipoDeMiel=' + $scope.punto.tipoDeMiel;
                        break;
                    case '8':
                        return window.location.href = 'reportes/laboratorio/xlsRechazado.php?fechaUno=' + $scope.punto.fechaUno + '&fechaDos=' + $scope.punto.fechaDos + '&tipoDeMiel=' + $scope.punto.tipoDeMiel;
                        break;
                    default:
                        break;
                }
                break;
            case '2':
                switch ($scope.analisis.opcion) {
                    case '3':
                        return window.location.href = 'reportes/laboratorio/xlsConcentrado.php?sinFecha=' + $scope.punto.punto + '&tipoDeMiel=' + $scope.punto.tipoDeMiel;
                        break;
                    case '4':
                        return window.location.href = 'reportes/laboratorio/xlsAntibioticos.php?sinFecha=' + $scope.punto.punto + '&tipoDeMiel=' + $scope.punto.tipoDeMiel;
                        break;
                    case '5':
                        return window.location.href = 'reportes/laboratorio/xlsHMF.php?sinFecha=' + $scope.punto.punto + '&tipoDeMiel=' + $scope.punto.tipoDeMiel;
                        break;
                    case '6':
                        return window.location.href = 'reportes/laboratorio/xlsAdulteracion.php?sinFecha=' + $scope.punto.punto + '&tipoDeMiel=' + $scope.punto.tipoDeMiel;
                        break;
                    case '7':
                        return window.location.href = 'reportes/laboratorio/xlsHumedad.php?sinFecha=' + $scope.punto.punto + '&tipoDeMiel=' + $scope.punto.tipoDeMiel;
                        break;
                    case '8':
                        return window.location.href = 'reportes/laboratorio/xlsRechazado.php?sinFecha=' + $scope.punto.punto + '&tipoDeMiel=' + $scope.punto.tipoDeMiel;
                        break;
                    default:
                        break;
                }
                break;
            default:
                growl.info('Tipo de reporte inválido');
                break;
        }
    };

    $scope.modalReporte = function () {
        $("#modalExcel").modal();
    };

    //=====================================================================================
    //          TERMINAN CAMBIOS 
    //=====================================================================================


    $scope.seleccionarValor = function () {
        angular.forEach($scope.informacionLab, function (value, key) {
            value.resultadoFinal = $scope.rstFinal;
        });
    };
    $scope.seleccionarValorMicrobiologia = function () {
        angular.forEach($scope.informacionLab, function (value, key) {
            value.micro = $scope.rstMicro;
        });
    };

    $scope.seleccionarValorFloracion = function () {
        angular.forEach($scope.informacionLab, function (value, key) {
            value.idFloracion = $scope.rstFloracion;
        });
    };

    $scope.ponerMismoValor = function (id) {
        angular.forEach($scope.informacionLab, function (value, key) {
            if (id == 1) {
                value.porcentaje = $scope.por;
            } else if (id == 2) {
                value.sf = $scope.sf;
            } else if (id == 3) {
                value.st = $scope.st;
            } else if (id == 4) {
                value.c13 = $scope.c13;
            } else if (id == 5) {
                value.hmf = $scope.hmf;
            } else if (id == 6) {
                value.color = $scope.color;
            } else if (id == 7) {
                value.tt = $scope.tt;
            }
        });
    };

    $scope.verificarRango = function (idOpcion, valor, valorid, id, tipo) {
        if (valor == "") {
            $("#" + valorid + "txtPorcentaje").removeClass("error");
        }
        var paso = false;
        angular.forEach($scope.opcionesValores, function (value, key) {
            var valor2 = parseInt(value.idRango);
            if (paso == false) {
                if (value.idOpcionLab == idOpcion) {
                    switch (valor2) {
                        case 1:
                            if (parseFloat(valor) < parseFloat(value.rango1)) {
                                paso = true;
                                $("#" + valorid + "" + id).removeClass("error");
                            } else {
                                paso = false;
                                $("#" + valorid + "" + id).addClass("error");
                            }
                            break;
                        case 2:
                            if (parseFloat(valor) > parseFloat(value.rango1)) {
                                paso = true;
                                $("#" + valorid + "" + id).removeClass("error");
                            } else {
                                paso = false;
                                $("#" + valorid + "" + id).addClass("error");
                            }
                            break;
                        case 3:
                            if (parseFloat(valor) <= parseFloat(value.rango1)) {
                                paso = true;
                                $("#" + valorid + "" + id).removeClass("error");
                            } else {
                                paso = false;
                                $("#" + valorid + "" + id).addClass("error");
                            }
                            break;
                        case 4:
                            if (parseFloat(valor) >= parseFloat(value.rango1)) {
                                paso = true;
                                $("#" + valorid + "" + id).removeClass("error");
                            } else {
                                paso = false;
                                $("#" + valorid + "" + id).addClass("error");
                            }
                            break;
                        case 5:
                            if (parseFloat(valor) >= parseFloat(value.rango1) && parseFloat(valor) <= parseFloat(value.rango2)) {
                                paso = true;
                                $("#" + valorid + "" + id).removeClass("error");
                            } else {
                                paso = false;
                                $("#" + valorid + "" + id).addClass("error");
                            }
                            break;
                    }
                }
            }
        });
    };
    $scope.bucarFolio = function () {
        var ok = $scope.verificarItem($scope.folio);
        if (ok == false) {
            $http.post("laboratorio/php/buscarAlmacen.php?id=" + $scope.folio)
                .success(function (respuesta) {
                    if (respuesta == 0) {
                        growl.warning("Registro no encontrado");
                    } else {
                        $scope.informacion.push(respuesta);
                    }

                });
        } else {
            growl.warning("Registro ya agregado");
        }
        $scope.folio = "";
    };

    $scope.cancelarLaboratorio = function () {
        $scope.informacion = new Array();
        growl.success("Datos cancelados");
    };
    $scope.cargarTabla = function () {
        $http.post("almacen/php/dameAlmacen.php?id=" + $scope.datos)
            .success(function (respuesta) {
                $scope.labEncabezado = respuesta;
            });
    };
    $scope.verificarItem = function (id) {
        var ok = false;
        angular.forEach($scope.informacion, function (value, key) {
            if (value.idAlmacen == id) {
                ok = true;
                return false;
            }
        });
        return ok;
    };
    $scope.eliminarLaboratorio = function (indice) {
        $scope.informacion.splice(indice, 1);
        growl.error("Registro eliminado");
    };

    $scope.cambiarEstadoLaboratorio = function (laboratorio) {
        $http.post("laboratorio/php/cambiarEstadoLaboratorio.php?valor=" + laboratorio.estado + "&id=" + laboratorio.idAlmacen)
            .success(function (respuesta) {
                growl.success(respuesta);
            });
    };
    $scope.cancelarRangoPorcentajeLab = function () {
        $scope.controlPorcentajeLab = false;
        $scope.porcentajeSeleccionadosLab = new Array();
        $scope.porcentajeRango1 = "";
        $scope.porcentajeRango2 = "";
    };
    $scope.cancelarRangoHmfLab = function () {
        $scope.controlLab = false;
        $scope.hmfSeleccionadosLab = new Array();
        $scope.hmfRango1 = "";
        $scope.hmfRango2 = "";
    };
    $scope.elegirRangoPorcentajeLaboratorio = function () {
        angular.forEach($scope.porcentajeSeleccionadosLab, function (value, key) {
            if (value == "Por rangos") {
                $scope.controlPorcentajeLab = true;
            }
        });
        if ($scope.controlPorcentajeLab == true) {
            angular.forEach($scope.porcentajeSeleccionadosLab, function (value, key) {
                if (value != "Por rangos") {
                    $scope.porcentajeSeleccionadosLab.splice(key, 1);
                    angular.forEach($scope.porcentajeSeleccionadosLab, function (value, key) {
                    });
                }
            });
        }
    };
    $scope.elegirRangoHmfLab = function () {
        angular.forEach($scope.hmfSeleccionadosLab, function (value, key) {
            if (value == "Por rangos") {
                $scope.controlLab = true;
            }
        });
        if ($scope.controlLab == true) {
            angular.forEach($scope.hmfSeleccionadosLab, function (value, key) {
                if (value != "Por rangos") {
                    $scope.hmfSeleccionadosLab.splice(key, 1);
                    angular.forEach($scope.hmfSeleccionadosLab, function (value, key) {
                    });
                }
            });
        }
    };
    $scope.buscarCalidadPorParametrosLaboratorio = function () {
        $scope.moduloLaboratorio = 0;
        $scope.rangosHmfLab = {};
        $scope.rangosPorcentajeLab = {};
        $scope.rangosHmfLab.rango1 = $scope.hmfRango1Lab;
        $scope.rangosHmfLab.rango2 = $scope.hmfRango2Lab;
        $scope.rangosPorcentajeLab.rango1 = $scope.porcentajeRango1Lab;
        $scope.rangosPorcentajeLab.rango2 = $scope.porcentajeRango2Lab;
        $scope.listaParametros = {};
        $scope.listaParametros.listaC13 = $scope.c13SeleccionadosLab;
        //        $scope.listaParametros.listaProveedores = $scope.estadoLaboratorioProveedores;
        $scope.listaParametros.listaProveedores = $scope.proveedoresSeleccionadosLab;
        $scope.listaParametros.listaPorcentaje = $scope.porcentajeSeleccionadosLab;
        $scope.listaParametros.listaSt = $scope.stSeleccionadosLab;
        $scope.listaParametros.listaSf = $scope.sfSeleccionadosLab;
        $scope.listaParametros.listaHmf = $scope.hmfSeleccionadosLab;
        $scope.listaParametros.rangosHmf = $scope.rangosHmfLab;
        $scope.listaParametros.rangosPorcentaje = $scope.rangosPorcentajeLab;
        busqueda.buscarInformacionParametros($scope.listaParametros).then(function (info) {
            if (info == 0) {
                growl.warning("No se encontro ningun registro con esos parametros");
            } else {
                $scope.informacionCalidad = info;
            }
            $("#idModalBusquedaCalidad").modal('hide');
        });
    };

    function validarReporteHumedades() {
        if ($scope.folios.inicial > $scope.folios.final) {
            growl.error('El folio inicial tiene que ser menor al folio final');
            return false;
        }
        if (!$scope.folios.inicial || !$scope.folios.final) {
            growl.error('Ingresa un rango correcto de folios');
            return false;
        }
        if (!$scope.folios.tipoDeMiel) {
            growl.error('Selecciona un tipo de miel');
            return false;
        }
        return true;
    };

    $scope.validarFolio = function () {
        if (!validarReporteHumedades()) {
            return;
        }
        window.open('reportes/laboratorio/pdfVerificarHumedad.php?i=' + $scope.folios.inicial + '&f=' + $scope.folios.final + '&tdm=' + $scope.folios.tipoDeMiel, '_blank');
    };

    $scope.infoHumedad = function () {
        if (!validarReporteHumedades()) {
            return;
        }

        var _datos = {
            folioInicial: $scope.folios.inicial,
            folioFinal: $scope.folios.final,
            tipoDeMiel: $scope.folios.tipoDeMiel
        }

        $http.post('laboratorio/php/traerReporteHumedad.php', _datos).success(function (data) {
            $scope.dataHumedad = data.data;
            if (data.error) {
                growl.error(data.message);
            }
        });
    };

    $scope.Borrar = function () {
        $scope.folios = {};
        $scope.dataHumedad = null;
    };


});
