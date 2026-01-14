form.controller('produccionCtrl', ['$scope', '$http', '$routeParams', 'growl', function ($scope, $http, $routeParams, growl) {

    //=========================P A R A M E T R O===========================
    $scope.proceso = $routeParams.idReporteProceso;
    $scope.loteInterno = $routeParams.idLoteInterno;
    $scope.parametroTipo = $routeParams.opcionCosechaFechas;
    $scope.parametroTipoCosecha = $routeParams.opcionCosechaProceso;
    //    ----------------------------------------------------------------

    var DEFAULT_PROCESO_SELECTION = 'DEFAULT_PROCESO_SELECTION';

    $scope.infoLote = {};
    $scope.numeroLote = 0;
    $scope.responsable = {};
    $scope.responsable.idPersonalOM = "";
    $scope.responsable.nombre = "";
    $scope.supervisor = {};
    $scope.supervisor.idPersonalOM = "";
    $scope.supervisor.nombre = "";
    $scope.reporteProceso = {};
    $scope.reporteProceso.idLoteInterno = 0;
    $scope.reporteProceso.horaInicio = "";
    $scope.reporteProceso.numeroTanque = "";
    $scope.reporteProceso.cabello = "";
    $scope.reporteProceso.cofia = "";
    $scope.reporteProceso.botas = "";
    $scope.reporteProceso.unas = "";
    $scope.reporteProceso.cubreBoca = "";
    $scope.reporteProceso.sanitizacionManos = "";
    $scope.reporteProceso.ropa = "";
    $scope.reporteProceso.lavadoManos = "";
    $scope.reporteProceso.sanitizacionBotas = "";
    $scope.reporteProceso.lavadoHerramientas = "";
    // $scope.reporteProceso.producto = "";
    $scope.reporteProceso.horaFinal = "";
    $scope.reporteProceso.inicioHomogeneizado = "";
    $scope.reporteProceso.finalHomogeneizado = "";
    $scope.reporteProceso.horaFinalReposo = "";
    $scope.reporteProceso.tiempoHomogeneizacion = "";
    $scope.reporteProceso.tiempoReposo = "";
    $scope.reporteProceso.otrasHerramientas = "";
    $scope.reporteProceso.noProcesada = "";
    $scope.reporteProceso.procesada = "";
    $scope.menuProceso = new Array();
    $scope.personalConformacion = new Array();
    $scope.personalProcesoInocuo = new Array();
    $scope.personalFolios = new Array();
    $scope.menuFechas = new Array();
    $scope.menuFechas.fechaEnvasado = "";
    $scope.menuFechas.fechaProceso = "";
    $scope.menuFechas.idLoteInterno = 0;
    $scope.fechaInterno = {};
    $scope.listaLotes = {};
    $scope.listaPersonal = {};
    $scope.herramienta = {};
    $scope.herramienta.herramientaLlave = "";
    $scope.herramienta.cantidadLlave = "";
    $scope.herramienta.condicionLlave = "";
    $scope.herramienta.herramientaPala = "";
    $scope.herramienta.cantidadPala = "";
    $scope.herramienta.condicionPala = "";
    $scope.herramienta.herramientaOtras = "";
    $scope.herramienta.cantidadOtras = "";
    $scope.herramienta.condicionOtras = "";
    $scope.opcionCosechaFechas = 0;
    $scope.opcionCosechaProceso = 0;
    $scope.producto = "";
    //==========================================================================
    //              A S I G N A C I Ó N  D E  F E C H A S
    //==========================================================================

    if ($scope.loteInterno > 0) {
        if ($scope.parametroTipo == 1) {
            $http.get('produccion/php/traeDetalleFechas.php?idLoteInterno=' + $scope.loteInterno).success(function (data) {
                $scope.fechaInterno = data;
                $scope.fechaInterno.fechaProceso = data.fechaProceso;
                $scope.fechaInterno.fechaEnvasado = data.fechaEnvasado;
            });
        } else {
            $http.get('produccion/php/traeDetalleFechas.php?organica=0&idLoteInterno=' + $scope.loteInterno).success(function (data) {
                $scope.fechaInterno = data;
                $scope.fechaInterno.fechaProceso = data.fechaProceso;
                $scope.fechaInterno.fechaEnvasado = data.fechaEnvasado;

            });
        }
    } else {
        $scope.$watch('opcionCosechaFechas', function (val) {
            console.log(val)
            if (val == 1) {
                $http.post("produccion/php/traeEncabezadoFechas.php").success(function (info) {
                    $scope.menuFechas = info;
                });
            } else if (val == 2) {
                $http.post("produccion/php/traeEncabezadoFechas.php?organica=0").success(function (info) {
                    $scope.menuFechas = info;
                });
            }else if (val == 5) {
                $http.post("produccion/php/traeEncabezadoFechas.php?organica=1").success(function (info) {
                    $scope.menuFechas = info;
                });
            }else if (val == 6) {
                $http.post("produccion/php/traeEncabezadoFechas.php?organica=2").success(function (info) {
                    $scope.menuFechas = info;
                });
            }else if (val == 7) {
                $http.post("produccion/php/traeEncabezadoFechas.php?organica=3").success(function (info) {
                    $scope.menuFechas = info;
                });
            }else if (val == 8) {
                $http.post("produccion/php/traeEncabezadoFechas.php?organica=4").success(function (info) {
                    $scope.menuFechas = info;
                });
            }else if (val == 9) {
                $http.post("produccion/php/traeEncabezadoFechas.php?organica=5").success(function (info) {
                    $scope.menuFechas = info;
                });
            }
             else {
                console.warn('$scope.opcionCosechaFechas equivale a :' + val);
            }
        });
    }

    $scope.guardarFechasLoteInterno = function () {
        $scope.fechasPE = {};
        procesoEnvasado();
        if ($scope.parametroTipo == 1) {
            $http.post("produccion/php/guardarFechasProcesoYEnvasado.php?idLoteInterno=" + $scope.loteInterno + "&fechaProceso=" + $scope.fechasPE.proceso + "&fechaEnvasado=" + $scope.fechasPE.envasado).success(function (info) {
                swal("Exito", "Fechas guardadas", "success");
                $http.post("produccion/php/traeEncabezadoFechas.php").success(function (info) {
                    $scope.menuFechas = info;
                });
            });
        } else if($scope.parametroTipo == 2) {
            $http.post("produccion/php/guardarFechasProcesoYEnvasado.php?organica=0&idLoteInterno=" + $scope.loteInterno + "&fechaProceso=" + $scope.fechasPE.proceso + "&fechaEnvasado=" + $scope.fechasPE.envasado).success(function (info) {
                swal("Exito", "Fechas guardadas", "success");
                $http.post("produccion/php/traeEncabezadoFechas.php").success(function (info) {
                    $scope.menuFechas = info;
                });
            });
        }
        else if($scope.parametroTipo == 5) {
            $http.post("produccion/php/guardarFechasProcesoYEnvasado.php?organica=1&idLoteInterno=" + $scope.loteInterno + "&fechaProceso=" + $scope.fechasPE.proceso + "&fechaEnvasado=" + $scope.fechasPE.envasado).success(function (info) {
                swal("Exito", "Fechas guardadas", "success");
                $http.post("produccion/php/traeEncabezadoFechas.php").success(function (info) {
                    $scope.menuFechas = info;
                });
            });
        }
        else if($scope.parametroTipo == 6) {
            $http.post("produccion/php/guardarFechasProcesoYEnvasado.php?organica=2&idLoteInterno=" + $scope.loteInterno + "&fechaProceso=" + $scope.fechasPE.proceso + "&fechaEnvasado=" + $scope.fechasPE.envasado).success(function (info) {
                swal("Exito", "Fechas guardadas", "success");
                $http.post("produccion/php/traeEncabezadoFechas.php").success(function (info) {
                    $scope.menuFechas = info;
                });
            });
        }
        else if($scope.parametroTipo == 7) {
            $http.post("produccion/php/guardarFechasProcesoYEnvasado.php?organica=3&idLoteInterno=" + $scope.loteInterno + "&fechaProceso=" + $scope.fechasPE.proceso + "&fechaEnvasado=" + $scope.fechasPE.envasado).success(function (info) {
                swal("Exito", "Fechas guardadas", "success");
                $http.post("produccion/php/traeEncabezadoFechas.php").success(function (info) {
                    $scope.menuFechas = info;
                });
            });
        }
        else if($scope.parametroTipo == 8) {
            $http.post("produccion/php/guardarFechasProcesoYEnvasado.php?organica=4&idLoteInterno=" + $scope.loteInterno + "&fechaProceso=" + $scope.fechasPE.proceso + "&fechaEnvasado=" + $scope.fechasPE.envasado).success(function (info) {
                swal("Exito", "Fechas guardadas", "success");
                $http.post("produccion/php/traeEncabezadoFechas.php").success(function (info) {
                    $scope.menuFechas = info;
                });
            });
        }
        else if($scope.parametroTipo == 9) {
            $http.post("produccion/php/guardarFechasProcesoYEnvasado.php?organica=5&idLoteInterno=" + $scope.loteInterno + "&fechaProceso=" + $scope.fechasPE.proceso + "&fechaEnvasado=" + $scope.fechasPE.envasado).success(function (info) {
                swal("Exito", "Fechas guardadas", "success");
                $http.post("produccion/php/traeEncabezadoFechas.php").success(function (info) {
                    $scope.menuFechas = info;
                });
            });
        }

        return window.location.href = "#/fechas";
    };
    //======================== TERMINA ASIGNACIÓN DE FECHAS =========================


    //==========================================================================
    //                              REPORTE DE PROCESO
    //==========================================================================

    if ($scope.proceso > 0) {
        //            $http.get('produccion/php/listaLotes.php').success(function (datas) {
        //                $scope.listaLotes = datas;
        //            });
        $http.get('almacen/php/listaNombresPersonal.php').success(function (arrayPersonal) {
            $scope.listaPersonal = arrayPersonal;
        });
        if ($scope.parametroTipoCosecha == 1) {
            $http.get('produccion/php/traeDetalleReporte.php?idReporteProceso=' + $scope.proceso).success(function (data) {
                $scope.reporteProceso = data;
                $scope.numeroLote = "" + $scope.reporteProceso.idLoteInterno + "";
                $scope.responsable = "" + $scope.reporteProceso.responsable + "";
                $scope.supervisor = "" + $scope.reporteProceso.supervisor + "";
                $scope.personalConformacion = data.personalConformacion;
                $scope.personalProcesoInocuo = data.personalProcesoInocuo;
                $scope.personalFolios = data.personalFolios;
                $scope.herramienta.cantidadLlave = data.cantidadLlave;
                $scope.herramienta.condicionLlave = data.condicionLlave;
                $scope.herramienta.cantidadPala = data.cantidadPala;
                $scope.herramienta.condicionPala = data.condicionPala;
                $scope.herramienta.cantidadOtras = data.cantidadOtras;
                $scope.herramienta.condicionOtras = data.condicionOtras;
            });
        } 
        else if ($scope.parametroTipoCosecha == 5){
            $http.get('produccion/php/traeDetalleReporte.php?organica=1&idReporteProceso=' + $scope.proceso).success(function (data) {
                $scope.reporteProceso = data;
                $scope.numeroLote = "" + $scope.reporteProceso.idLoteInterno + "";
                $scope.responsable = "" + $scope.reporteProceso.responsable + "";
                $scope.supervisor = "" + $scope.reporteProceso.supervisor + "";
                $scope.personalConformacion = data.personalConformacion;
                $scope.personalProcesoInocuo = data.personalProcesoInocuo;
                $scope.personalFolios = data.personalFolios;
                $scope.herramienta.cantidadLlave = data.cantidadLlave;
                $scope.herramienta.condicionLlave = data.condicionLlave;
                $scope.herramienta.cantidadPala = data.cantidadPala;
                $scope.herramienta.condicionPala = data.condicionPala;
                $scope.herramienta.cantidadOtras = data.cantidadOtras;
                $scope.herramienta.condicionOtras = data.condicionOtras;
            });
        }
        else if ($scope.parametroTipoCosecha == 6){
            $http.get('produccion/php/traeDetalleReporte.php?organica=2&idReporteProceso=' + $scope.proceso).success(function (data) {
                $scope.reporteProceso = data;
                $scope.numeroLote = "" + $scope.reporteProceso.idLoteInterno + "";
                $scope.responsable = "" + $scope.reporteProceso.responsable + "";
                $scope.supervisor = "" + $scope.reporteProceso.supervisor + "";
                $scope.personalConformacion = data.personalConformacion;
                $scope.personalProcesoInocuo = data.personalProcesoInocuo;
                $scope.personalFolios = data.personalFolios;
                $scope.herramienta.cantidadLlave = data.cantidadLlave;
                $scope.herramienta.condicionLlave = data.condicionLlave;
                $scope.herramienta.cantidadPala = data.cantidadPala;
                $scope.herramienta.condicionPala = data.condicionPala;
                $scope.herramienta.cantidadOtras = data.cantidadOtras;
                $scope.herramienta.condicionOtras = data.condicionOtras;
            });
        }
        else if ($scope.parametroTipoCosecha == 7){
            $http.get('produccion/php/traeDetalleReporte.php?organica=3&idReporteProceso=' + $scope.proceso).success(function (data) {
                $scope.reporteProceso = data;
                $scope.numeroLote = "" + $scope.reporteProceso.idLoteInterno + "";
                $scope.responsable = "" + $scope.reporteProceso.responsable + "";
                $scope.supervisor = "" + $scope.reporteProceso.supervisor + "";
                $scope.personalConformacion = data.personalConformacion;
                $scope.personalProcesoInocuo = data.personalProcesoInocuo;
                $scope.personalFolios = data.personalFolios;
                $scope.herramienta.cantidadLlave = data.cantidadLlave;
                $scope.herramienta.condicionLlave = data.condicionLlave;
                $scope.herramienta.cantidadPala = data.cantidadPala;
                $scope.herramienta.condicionPala = data.condicionPala;
                $scope.herramienta.cantidadOtras = data.cantidadOtras;
                $scope.herramienta.condicionOtras = data.condicionOtras;
            });
        }
        else if ($scope.parametroTipoCosecha == 8){
            $http.get('produccion/php/traeDetalleReporte.php?organica=4&idReporteProceso=' + $scope.proceso).success(function (data) {
                $scope.reporteProceso = data;
                $scope.numeroLote = "" + $scope.reporteProceso.idLoteInterno + "";
                $scope.responsable = "" + $scope.reporteProceso.responsable + "";
                $scope.supervisor = "" + $scope.reporteProceso.supervisor + "";
                $scope.personalConformacion = data.personalConformacion;
                $scope.personalProcesoInocuo = data.personalProcesoInocuo;
                $scope.personalFolios = data.personalFolios;
                $scope.herramienta.cantidadLlave = data.cantidadLlave;
                $scope.herramienta.condicionLlave = data.condicionLlave;
                $scope.herramienta.cantidadPala = data.cantidadPala;
                $scope.herramienta.condicionPala = data.condicionPala;
                $scope.herramienta.cantidadOtras = data.cantidadOtras;
                $scope.herramienta.condicionOtras = data.condicionOtras;
            });
        }
        else if ($scope.parametroTipoCosecha == 9){
            $http.get('produccion/php/traeDetalleReporte.php?organica=5&idReporteProceso=' + $scope.proceso).success(function (data) {
                $scope.reporteProceso = data;
                $scope.numeroLote = "" + $scope.reporteProceso.idLoteInterno + "";
                $scope.responsable = "" + $scope.reporteProceso.responsable + "";
                $scope.supervisor = "" + $scope.reporteProceso.supervisor + "";
                $scope.personalConformacion = data.personalConformacion;
                $scope.personalProcesoInocuo = data.personalProcesoInocuo;
                $scope.personalFolios = data.personalFolios;
                $scope.herramienta.cantidadLlave = data.cantidadLlave;
                $scope.herramienta.condicionLlave = data.condicionLlave;
                $scope.herramienta.cantidadPala = data.cantidadPala;
                $scope.herramienta.condicionPala = data.condicionPala;
                $scope.herramienta.cantidadOtras = data.cantidadOtras;
                $scope.herramienta.condicionOtras = data.condicionOtras;
            });
        }
        else {
            $http.get('produccion/php/traeDetalleReporte.php?organica=0&idReporteProceso=' + $scope.proceso).success(function (data) {
                $scope.reporteProceso = data;
                $scope.numeroLote = "" + $scope.reporteProceso.idLoteInterno + "";
                $scope.responsable = "" + $scope.reporteProceso.responsable + "";
                $scope.supervisor = "" + $scope.reporteProceso.supervisor + "";
                $scope.personalConformacion = data.personalConformacion;
                $scope.personalProcesoInocuo = data.personalProcesoInocuo;
                $scope.personalFolios = data.personalFolios;
                $scope.herramienta.cantidadLlave = data.cantidadLlave;
                $scope.herramienta.condicionLlave = data.condicionLlave;
                $scope.herramienta.cantidadPala = data.cantidadPala;
                $scope.herramienta.condicionPala = data.condicionPala;
                $scope.herramienta.cantidadOtras = data.cantidadOtras;
                $scope.herramienta.condicionOtras = data.condicionOtras;
            });
        }
    } else {
        $scope.$watch('opcionCosechaProceso', function (val) {
            $scope.menuProceso = [];
            if (val == 1) {
                window.localStorage.setItem(DEFAULT_PROCESO_SELECTION, 1);
                $http.post("produccion/php/traeEncabezadoReporte.php").success(function (info) {
                    $scope.menuProceso = info;
                });
            } else if (val == 2) {
                window.localStorage.setItem(DEFAULT_PROCESO_SELECTION, 2);
                $http.post("produccion/php/traeEncabezadoReporte.php?organica=0").success(function (info) {
                    $scope.menuProceso = info;
                });
            }
            else if (val == 5) {
                window.localStorage.setItem(DEFAULT_PROCESO_SELECTION, 5);
                $http.post("produccion/php/traeEncabezadoReporte.php?organica=1").success(function (info) {
                    $scope.menuProceso = info;
                });
            } 
            else if (val == 6) {
                window.localStorage.setItem(DEFAULT_PROCESO_SELECTION, 6);
                $http.post("produccion/php/traeEncabezadoReporte.php?organica=2").success(function (info) {
                    $scope.menuProceso = info;
                });
            } 
            else if (val == 7) {
                window.localStorage.setItem(DEFAULT_PROCESO_SELECTION, 7);
                $http.post("produccion/php/traeEncabezadoReporte.php?organica=3").success(function (info) {
                    $scope.menuProceso = info;
                });
            } 
            else if (val == 8) {
                window.localStorage.setItem(DEFAULT_PROCESO_SELECTION, 8);
                $http.post("produccion/php/traeEncabezadoReporte.php?organica=4").success(function (info) {
                    $scope.menuProceso = info;
                });
            } 
            else if (val == 9) {
                window.localStorage.setItem(DEFAULT_PROCESO_SELECTION, 9);
                $http.post("produccion/php/traeEncabezadoReporte.php?organica=5").success(function (info) {
                    $scope.menuProceso = info;
                });
            }  
            else {
                console.warn('$scope.opcionCosechaProceso equivale a :' + val);
            }
        });

        if (window.localStorage.getItem(DEFAULT_PROCESO_SELECTION) != null) {
            $scope.opcionCosechaProceso = window.localStorage.getItem(DEFAULT_PROCESO_SELECTION);
        }
    }

    $scope.nuevoProceso = function (val) {

        if (val == 0) {
            growl.info("Seleccione un tipo de miel");
        } else {
            return window.location.href = "#/nvoProceso/0/" + val;
        }
    };

    //    ----------------------------------------------------------------

    $scope.$watch('reporteProceso.inicioHomogeneizado', function () {
        $scope.reporteProceso.inicioHomogeneizado = $scope.reporteProceso.horaFinal;
    }, true);
    if ($scope.proceso == 0) {
        if ($scope.parametroTipoCosecha == 1) {
            $http.get('produccion/php/listaDeLotesProduccion.php').success(function (datas) {
                $scope.listaLotes = datas;
            });
        } 
        else if ($scope.parametroTipoCosecha == 5) {
            $http.get('produccion/php/listaDeLotesProduccion.php?organica=1').success(function (datas) {
                $scope.listaLotes = datas;
            });
        } 
        else if ($scope.parametroTipoCosecha == 6) {
            $http.get('produccion/php/listaDeLotesProduccion.php?organica=2').success(function (datas) {
                $scope.listaLotes = datas;
            });
        }
        else if ($scope.parametroTipoCosecha == 7) {
            $http.get('produccion/php/listaDeLotesProduccion.php?organica=3').success(function (datas) {
                $scope.listaLotes = datas;
            });
        }
        else if ($scope.parametroTipoCosecha == 8) {
            $http.get('produccion/php/listaDeLotesProduccion.php?organica=4').success(function (datas) {
                $scope.listaLotes = datas;
            });
        }
        else if ($scope.parametroTipoCosecha == 9) {
            $http.get('produccion/php/listaDeLotesProduccion.php?organica=5').success(function (datas) {
                $scope.listaLotes = datas;
            });
        }else {
            $http.get('produccion/php/listaDeLotesProduccion.php?organica=0').success(function (datas) {
                $scope.listaLotes = datas;
            });
        }
        $http.get('almacen/php/listaNombresPersonal.php').success(function (arrayPersonal) {
            $scope.listaPersonal = arrayPersonal;
        });
    }

    //===========================================================================
    //      NG-CHANGE
    //===========================================================================


    $scope.$watch('numeroLote', function (numeroLote) {
        if ($scope.parametroTipoCosecha == 1) {
            $http.get('produccion/php/informacionDelLoteInterno.php?idLoteInterno=' + numeroLote)
                .success(function (data) {
                    $scope.infoLote = data;
                });
        }
        else if ($scope.parametroTipoCosecha == 5) {
            $http.get('produccion/php/informacionDelLoteInterno.php?organica=1&idLoteInterno=' + numeroLote)
                .success(function (data) {
                    $scope.infoLote = data;
                });
        }
        else if ($scope.parametroTipoCosecha == 6) {
            $http.get('produccion/php/informacionDelLoteInterno.php?organica=2&idLoteInterno=' + numeroLote)
                .success(function (data) {
                    $scope.infoLote = data;
                });
        }
        else if ($scope.parametroTipoCosecha == 7) {
            $http.get('produccion/php/informacionDelLoteInterno.php?organica=3&idLoteInterno=' + numeroLote)
                .success(function (data) {
                    $scope.infoLote = data;
                });
        }
        else if ($scope.parametroTipoCosecha == 8) {
            $http.get('produccion/php/informacionDelLoteInterno.php?organica=4&idLoteInterno=' + numeroLote)
                .success(function (data) {
                    $scope.infoLote = data;
                });
        }
        else if ($scope.parametroTipoCosecha == 9) {
            $http.get('produccion/php/informacionDelLoteInterno.php?organica=5&idLoteInterno=' + numeroLote)
                .success(function (data) {
                    $scope.infoLote = data;
                });
        }
         else {
            $http.get('produccion/php/informacionDelLoteInterno.php?organica=0&idLoteInterno=' + numeroLote)
                .success(function (data) {
                    $scope.infoLote = data;
                });
        }
    }, true);
    //   ---------------------------------------------------------------


    //==========================================================================
    // MODALES
    //==========================================================================
    $scope.agregarPersonalConformacion = function () {
        $("#agregarPersonalConformacion").modal();
    };
    $scope.agregarPersonalProceso = function () {
        $("#agregarPersonalProceso").modal();
    };
    $scope.agregarPersonalFolios = function () {
        $("#agregarPersonalFolios").modal();
    };
    $scope.asignarFechas = function () {
        $("#asignarFechas").modal();
    };
    $scope.eliminarConformacion = function (idConformacionLote) {
        if ($scope.parametroTipoCosecha == 1) {
            $http.post("produccion/php/eliminarConformacion.php?idConformacionLote=" + idConformacionLote)
                .success(function (respuesta) {
                    growl.warning("Registro eliminado");
                    $http.get('produccion/php/traeDetalleReporte.php?idReporteProceso=' + $scope.proceso).success(function (data) {
                        $scope.reporteProceso = data;
                        $scope.numeroLote = "" + $scope.reporteProceso.idLoteInterno + "";
                        $scope.responsable = "" + $scope.reporteProceso.responsable + "";
                        $scope.supervisor = "" + $scope.reporteProceso.supervisor + "";
                        $scope.personalConformacion = data.personalConformacion;
                        $scope.personalProcesoInocuo = data.personalProcesoInocuo;
                        $scope.personalFolios = data.personalFolios;
                    });
                });
        }
        else if ($scope.parametroTipoCosecha == 5) {
            $http.post("produccion/php/eliminarConformacion.php?organica=1&idConformacionLote=" + idConformacionLote)
                .success(function (respuesta) {
                    growl.warning("Registro eliminado");
                    $http.get('produccion/php/traeDetalleReporte.php?organica=1&idReporteProceso=' + $scope.proceso).success(function (data) {
                        $scope.reporteProceso = data;
                        $scope.numeroLote = "" + $scope.reporteProceso.idLoteInterno + "";
                        $scope.responsable = "" + $scope.reporteProceso.responsable + "";
                        $scope.supervisor = "" + $scope.reporteProceso.supervisor + "";
                        $scope.personalConformacion = data.personalConformacion;
                        $scope.personalProcesoInocuo = data.personalProcesoInocuo;
                        $scope.personalFolios = data.personalFolios;
                    });
                });
        }
        else if ($scope.parametroTipoCosecha == 6) {
            $http.post("produccion/php/eliminarConformacion.php?organica=2&idConformacionLote=" + idConformacionLote)
                .success(function (respuesta) {
                    growl.warning("Registro eliminado");
                    $http.get('produccion/php/traeDetalleReporte.php?organica=2&idReporteProceso=' + $scope.proceso).success(function (data) {
                        $scope.reporteProceso = data;
                        $scope.numeroLote = "" + $scope.reporteProceso.idLoteInterno + "";
                        $scope.responsable = "" + $scope.reporteProceso.responsable + "";
                        $scope.supervisor = "" + $scope.reporteProceso.supervisor + "";
                        $scope.personalConformacion = data.personalConformacion;
                        $scope.personalProcesoInocuo = data.personalProcesoInocuo;
                        $scope.personalFolios = data.personalFolios;
                    });
                });
        }
        else if ($scope.parametroTipoCosecha == 7) {
            $http.post("produccion/php/eliminarConformacion.php?organica=3&idConformacionLote=" + idConformacionLote)
                .success(function (respuesta) {
                    growl.warning("Registro eliminado");
                    $http.get('produccion/php/traeDetalleReporte.php?organica=3&idReporteProceso=' + $scope.proceso).success(function (data) {
                        $scope.reporteProceso = data;
                        $scope.numeroLote = "" + $scope.reporteProceso.idLoteInterno + "";
                        $scope.responsable = "" + $scope.reporteProceso.responsable + "";
                        $scope.supervisor = "" + $scope.reporteProceso.supervisor + "";
                        $scope.personalConformacion = data.personalConformacion;
                        $scope.personalProcesoInocuo = data.personalProcesoInocuo;
                        $scope.personalFolios = data.personalFolios;
                    });
                });
        }
        else if ($scope.parametroTipoCosecha == 8) {
            $http.post("produccion/php/eliminarConformacion.php?organica=4&idConformacionLote=" + idConformacionLote)
                .success(function (respuesta) {
                    growl.warning("Registro eliminado");
                    $http.get('produccion/php/traeDetalleReporte.php?organica=4&idReporteProceso=' + $scope.proceso).success(function (data) {
                        $scope.reporteProceso = data;
                        $scope.numeroLote = "" + $scope.reporteProceso.idLoteInterno + "";
                        $scope.responsable = "" + $scope.reporteProceso.responsable + "";
                        $scope.supervisor = "" + $scope.reporteProceso.supervisor + "";
                        $scope.personalConformacion = data.personalConformacion;
                        $scope.personalProcesoInocuo = data.personalProcesoInocuo;
                        $scope.personalFolios = data.personalFolios;
                    });
                });
        }
        else if ($scope.parametroTipoCosecha == 9) {
            $http.post("produccion/php/eliminarConformacion.php?organica=5&idConformacionLote=" + idConformacionLote)
                .success(function (respuesta) {
                    growl.warning("Registro eliminado");
                    $http.get('produccion/php/traeDetalleReporte.php?organica=5&idReporteProceso=' + $scope.proceso).success(function (data) {
                        $scope.reporteProceso = data;
                        $scope.numeroLote = "" + $scope.reporteProceso.idLoteInterno + "";
                        $scope.responsable = "" + $scope.reporteProceso.responsable + "";
                        $scope.supervisor = "" + $scope.reporteProceso.supervisor + "";
                        $scope.personalConformacion = data.personalConformacion;
                        $scope.personalProcesoInocuo = data.personalProcesoInocuo;
                        $scope.personalFolios = data.personalFolios;
                    });
                });
        }
         else {
            $http.post("produccion/php/eliminarConformacion.php?organica=0&idConformacionLote=" + idConformacionLote)
                .success(function (respuesta) {
                    growl.warning("Registro eliminado");
                    $http.get('produccion/php/traeDetalleReporte.php?organica=0&idReporteProceso=' + $scope.proceso).success(function (data) {
                        $scope.reporteProceso = data;
                        $scope.numeroLote = "" + $scope.reporteProceso.idLoteInterno + "";
                        $scope.responsable = "" + $scope.reporteProceso.responsable + "";
                        $scope.supervisor = "" + $scope.reporteProceso.supervisor + "";
                        $scope.personalConformacion = data.personalConformacion;
                        $scope.personalProcesoInocuo = data.personalProcesoInocuo;
                        $scope.personalFolios = data.personalFolios;
                    });
                });
        }

    };
    $scope.eliminarProceso = function (idProceso) {
        if ($scope.parametroTipoCosecha == 1) {
            $http.post("produccion/php/eliminarProceso.php?idProceso=" + idProceso)
                .success(function (respuesta) {
                    growl.warning("Registro eliminado");
                    $http.get('produccion/php/traeDetalleReporte.php?idReporteProceso=' + $scope.proceso).success(function (data) {
                        $scope.reporteProceso = data;
                        $scope.numeroLote = "" + $scope.reporteProceso.idLoteInterno + "";
                        $scope.responsable = "" + $scope.reporteProceso.responsable + "";
                        $scope.supervisor = "" + $scope.reporteProceso.supervisor + "";
                        $scope.personalConformacion = data.personalConformacion;
                        $scope.personalProcesoInocuo = data.personalProcesoInocuo;
                        $scope.personalFolios = data.personalFolios;
                    });
                });
        } 
        else if ($scope.parametroTipoCosecha == 5) {
            $http.post("produccion/php/eliminarProceso.php?organica=1&idProceso=" + idProceso)
                .success(function (respuesta) {
                    growl.warning("Registro eliminado");
                    $http.get('produccion/php/traeDetalleReporte.php?organica=1&idReporteProceso=' + $scope.proceso).success(function (data) {
                        $scope.reporteProceso = data;
                        $scope.numeroLote = "" + $scope.reporteProceso.idLoteInterno + "";
                        $scope.responsable = "" + $scope.reporteProceso.responsable + "";
                        $scope.supervisor = "" + $scope.reporteProceso.supervisor + "";
                        $scope.personalConformacion = data.personalConformacion;
                        $scope.personalProcesoInocuo = data.personalProcesoInocuo;
                        $scope.personalFolios = data.personalFolios;
                    });
                });
        }
        else if ($scope.parametroTipoCosecha == 6) {
            $http.post("produccion/php/eliminarProceso.php?organica=2&idProceso=" + idProceso)
                .success(function (respuesta) {
                    growl.warning("Registro eliminado");
                    $http.get('produccion/php/traeDetalleReporte.php?organica=2&idReporteProceso=' + $scope.proceso).success(function (data) {
                        $scope.reporteProceso = data;
                        $scope.numeroLote = "" + $scope.reporteProceso.idLoteInterno + "";
                        $scope.responsable = "" + $scope.reporteProceso.responsable + "";
                        $scope.supervisor = "" + $scope.reporteProceso.supervisor + "";
                        $scope.personalConformacion = data.personalConformacion;
                        $scope.personalProcesoInocuo = data.personalProcesoInocuo;
                        $scope.personalFolios = data.personalFolios;
                    });
                });
        }
        else if ($scope.parametroTipoCosecha == 7) {
            $http.post("produccion/php/eliminarProceso.php?organica=3&idProceso=" + idProceso)
                .success(function (respuesta) {
                    growl.warning("Registro eliminado");
                    $http.get('produccion/php/traeDetalleReporte.php?organica=3&idReporteProceso=' + $scope.proceso).success(function (data) {
                        $scope.reporteProceso = data;
                        $scope.numeroLote = "" + $scope.reporteProceso.idLoteInterno + "";
                        $scope.responsable = "" + $scope.reporteProceso.responsable + "";
                        $scope.supervisor = "" + $scope.reporteProceso.supervisor + "";
                        $scope.personalConformacion = data.personalConformacion;
                        $scope.personalProcesoInocuo = data.personalProcesoInocuo;
                        $scope.personalFolios = data.personalFolios;
                    });
                });
        }
        else if ($scope.parametroTipoCosecha == 8) {
            $http.post("produccion/php/eliminarProceso.php?organica=4&idProceso=" + idProceso)
                .success(function (respuesta) {
                    growl.warning("Registro eliminado");
                    $http.get('produccion/php/traeDetalleReporte.php?organica=4&idReporteProceso=' + $scope.proceso).success(function (data) {
                        $scope.reporteProceso = data;
                        $scope.numeroLote = "" + $scope.reporteProceso.idLoteInterno + "";
                        $scope.responsable = "" + $scope.reporteProceso.responsable + "";
                        $scope.supervisor = "" + $scope.reporteProceso.supervisor + "";
                        $scope.personalConformacion = data.personalConformacion;
                        $scope.personalProcesoInocuo = data.personalProcesoInocuo;
                        $scope.personalFolios = data.personalFolios;
                    });
                });
        }
        else if ($scope.parametroTipoCosecha == 9) {
            $http.post("produccion/php/eliminarProceso.php?organica=5&idProceso=" + idProceso)
                .success(function (respuesta) {
                    growl.warning("Registro eliminado");
                    $http.get('produccion/php/traeDetalleReporte.php?organica=5&idReporteProceso=' + $scope.proceso).success(function (data) {
                        $scope.reporteProceso = data;
                        $scope.numeroLote = "" + $scope.reporteProceso.idLoteInterno + "";
                        $scope.responsable = "" + $scope.reporteProceso.responsable + "";
                        $scope.supervisor = "" + $scope.reporteProceso.supervisor + "";
                        $scope.personalConformacion = data.personalConformacion;
                        $scope.personalProcesoInocuo = data.personalProcesoInocuo;
                        $scope.personalFolios = data.personalFolios;
                    });
                });
        }
        else {
            $http.post("produccion/php/eliminarProceso.php?organica=0&idProceso=" + idProceso)
                .success(function (respuesta) {
                    growl.warning("Registro eliminado");
                    $http.get('produccion/php/traeDetalleReporte.php?organica=0&idReporteProceso=' + $scope.proceso).success(function (data) {
                        $scope.reporteProceso = data;
                        $scope.numeroLote = "" + $scope.reporteProceso.idLoteInterno + "";
                        $scope.responsable = "" + $scope.reporteProceso.responsable + "";
                        $scope.supervisor = "" + $scope.reporteProceso.supervisor + "";
                        $scope.personalConformacion = data.personalConformacion;
                        $scope.personalProcesoInocuo = data.personalProcesoInocuo;
                        $scope.personalFolios = data.personalFolios;
                    });
                });
        }
    };
    $scope.eliminarFolios = function (idBusqueda) {
        if ($scope.parametroTipoCosecha == 1) {
            $http.post("produccion/php/eliminarFolios.php?idBusqueda=" + idBusqueda)
                .success(function (respuesta) {
                    growl.warning("Registro eliminado");
                    $http.get('produccion/php/traeDetalleReporte.php?idReporteProceso=' + $scope.proceso).success(function (data) {
                        $scope.reporteProceso = data;
                        $scope.numeroLote = "" + $scope.reporteProceso.idLoteInterno + "";
                        $scope.responsable = "" + $scope.reporteProceso.responsable + "";
                        $scope.supervisor = "" + $scope.reporteProceso.supervisor + "";
                        $scope.personalConformacion = data.personalConformacion;
                        $scope.personalProcesoInocuo = data.personalProcesoInocuo;
                        $scope.personalFolios = data.personalFolios;
                    });
                });
        } 
        else if ($scope.parametroTipoCosecha == 5){
            $http.post("produccion/php/eliminarFolios.php?organica=1&idBusqueda=" + idBusqueda)
                .success(function (respuesta) {
                    growl.warning("Registro eliminado");
                    $http.get('produccion/php/traeDetalleReporte.php?organica=1&idReporteProceso=' + $scope.proceso).success(function (data) {
                        $scope.reporteProceso = data;
                        $scope.numeroLote = "" + $scope.reporteProceso.idLoteInterno + "";
                        $scope.responsable = "" + $scope.reporteProceso.responsable + "";
                        $scope.supervisor = "" + $scope.reporteProceso.supervisor + "";
                        $scope.personalConformacion = data.personalConformacion;
                        $scope.personalProcesoInocuo = data.personalProcesoInocuo;
                        $scope.personalFolios = data.personalFolios;
                    });
                });
        }
        else if ($scope.parametroTipoCosecha == 6){
            $http.post("produccion/php/eliminarFolios.php?organica=2&idBusqueda=" + idBusqueda)
                .success(function (respuesta) {
                    growl.warning("Registro eliminado");
                    $http.get('produccion/php/traeDetalleReporte.php?organica=2&idReporteProceso=' + $scope.proceso).success(function (data) {
                        $scope.reporteProceso = data;
                        $scope.numeroLote = "" + $scope.reporteProceso.idLoteInterno + "";
                        $scope.responsable = "" + $scope.reporteProceso.responsable + "";
                        $scope.supervisor = "" + $scope.reporteProceso.supervisor + "";
                        $scope.personalConformacion = data.personalConformacion;
                        $scope.personalProcesoInocuo = data.personalProcesoInocuo;
                        $scope.personalFolios = data.personalFolios;
                    });
                });
        }
        else if ($scope.parametroTipoCosecha == 7){
            $http.post("produccion/php/eliminarFolios.php?organica=3&idBusqueda=" + idBusqueda)
                .success(function (respuesta) {
                    growl.warning("Registro eliminado");
                    $http.get('produccion/php/traeDetalleReporte.php?organica=3&idReporteProceso=' + $scope.proceso).success(function (data) {
                        $scope.reporteProceso = data;
                        $scope.numeroLote = "" + $scope.reporteProceso.idLoteInterno + "";
                        $scope.responsable = "" + $scope.reporteProceso.responsable + "";
                        $scope.supervisor = "" + $scope.reporteProceso.supervisor + "";
                        $scope.personalConformacion = data.personalConformacion;
                        $scope.personalProcesoInocuo = data.personalProcesoInocuo;
                        $scope.personalFolios = data.personalFolios;
                    });
                });
        }
        else if ($scope.parametroTipoCosecha == 8){
            $http.post("produccion/php/eliminarFolios.php?organica=4&idBusqueda=" + idBusqueda)
                .success(function (respuesta) {
                    growl.warning("Registro eliminado");
                    $http.get('produccion/php/traeDetalleReporte.php?organica=4&idReporteProceso=' + $scope.proceso).success(function (data) {
                        $scope.reporteProceso = data;
                        $scope.numeroLote = "" + $scope.reporteProceso.idLoteInterno + "";
                        $scope.responsable = "" + $scope.reporteProceso.responsable + "";
                        $scope.supervisor = "" + $scope.reporteProceso.supervisor + "";
                        $scope.personalConformacion = data.personalConformacion;
                        $scope.personalProcesoInocuo = data.personalProcesoInocuo;
                        $scope.personalFolios = data.personalFolios;
                    });
                });
        }
        else if ($scope.parametroTipoCosecha == 9){
            $http.post("produccion/php/eliminarFolios.php?organica=5&idBusqueda=" + idBusqueda)
                .success(function (respuesta) {
                    growl.warning("Registro eliminado");
                    $http.get('produccion/php/traeDetalleReporte.php?organica=5&idReporteProceso=' + $scope.proceso).success(function (data) {
                        $scope.reporteProceso = data;
                        $scope.numeroLote = "" + $scope.reporteProceso.idLoteInterno + "";
                        $scope.responsable = "" + $scope.reporteProceso.responsable + "";
                        $scope.supervisor = "" + $scope.reporteProceso.supervisor + "";
                        $scope.personalConformacion = data.personalConformacion;
                        $scope.personalProcesoInocuo = data.personalProcesoInocuo;
                        $scope.personalFolios = data.personalFolios;
                    });
                });
        }
        else {
            $http.post("produccion/php/eliminarFolios.php?organica=0&idBusqueda=" + idBusqueda)
                .success(function (respuesta) {
                    growl.warning("Registro eliminado");
                    $http.get('produccion/php/traeDetalleReporte.php?organica=0&idReporteProceso=' + $scope.proceso).success(function (data) {
                        $scope.reporteProceso = data;
                        $scope.numeroLote = "" + $scope.reporteProceso.idLoteInterno + "";
                        $scope.responsable = "" + $scope.reporteProceso.responsable + "";
                        $scope.supervisor = "" + $scope.reporteProceso.supervisor + "";
                        $scope.personalConformacion = data.personalConformacion;
                        $scope.personalProcesoInocuo = data.personalProcesoInocuo;
                        $scope.personalFolios = data.personalFolios;
                    });
                });
        }

    };
    $scope.agregarConformador = function () {
        if ($scope.parametroTipoCosecha == 1) {
            $http.post("produccion/php/guardarNvoConformador.php?idReporteProceso=" + $scope.proceso + "&idPersonalOM=" + $scope.conformador).success(function (info) {
                swal("Exito", info, "success");
                $http.post("produccion/php/dameConformadores.php?idReporteProceso=" + $scope.proceso).success(function (conformadores) {
                    $scope.personalConformacion = conformadores;
                });
            });
            $("#agregarPersonalConformacion").modal('hide');
            $scope.conformador = "";
        } else if ($scope.parametroTipoCosecha == 5) {
            $http.post("produccion/php/guardarNvoConformador.php?organica=1&idReporteProceso=" + $scope.proceso + "&idPersonalOM=" + $scope.conformador).success(function (info) {
                swal("Exito", info, "success");
                $http.post("produccion/php/dameConformadores.php?organica=1&idReporteProceso=" + $scope.proceso).success(function (conformadores) {
                    $scope.personalConformacion = conformadores;
                });
            });
            $("#agregarPersonalConformacion").modal('hide');
            $scope.conformador = "";
        }
        else if ($scope.parametroTipoCosecha == 6) {
            $http.post("produccion/php/guardarNvoConformador.php?organica=2&idReporteProceso=" + $scope.proceso + "&idPersonalOM=" + $scope.conformador).success(function (info) {
                swal("Exito", info, "success");
                $http.post("produccion/php/dameConformadores.php?organica=2&idReporteProceso=" + $scope.proceso).success(function (conformadores) {
                    $scope.personalConformacion = conformadores;
                });
            });
            $("#agregarPersonalConformacion").modal('hide');
            $scope.conformador = "";
        }
        else if ($scope.parametroTipoCosecha == 7) {
            $http.post("produccion/php/guardarNvoConformador.php?organica=3&idReporteProceso=" + $scope.proceso + "&idPersonalOM=" + $scope.conformador).success(function (info) {
                swal("Exito", info, "success");
                $http.post("produccion/php/dameConformadores.php?organica=3&idReporteProceso=" + $scope.proceso).success(function (conformadores) {
                    $scope.personalConformacion = conformadores;
                });
            });
            $("#agregarPersonalConformacion").modal('hide');
            $scope.conformador = "";
        }
        else if ($scope.parametroTipoCosecha == 8) {
            $http.post("produccion/php/guardarNvoConformador.php?organica=4&idReporteProceso=" + $scope.proceso + "&idPersonalOM=" + $scope.conformador).success(function (info) {
                swal("Exito", info, "success");
                $http.post("produccion/php/dameConformadores.php?organica=4&idReporteProceso=" + $scope.proceso).success(function (conformadores) {
                    $scope.personalConformacion = conformadores;
                });
            });
            $("#agregarPersonalConformacion").modal('hide');
            $scope.conformador = "";
        }
        else if ($scope.parametroTipoCosecha == 9) {
            $http.post("produccion/php/guardarNvoConformador.php?organica=5&idReporteProceso=" + $scope.proceso + "&idPersonalOM=" + $scope.conformador).success(function (info) {
                swal("Exito", info, "success");
                $http.post("produccion/php/dameConformadores.php?organica=5&idReporteProceso=" + $scope.proceso).success(function (conformadores) {
                    $scope.personalConformacion = conformadores;
                });
            });
            $("#agregarPersonalConformacion").modal('hide');
            $scope.conformador = "";
        } 
        else {
            $http.post("produccion/php/guardarNvoConformador.php?organica=0&idReporteProceso=" + $scope.proceso + "&idPersonalOM=" + $scope.conformador).success(function (info) {
                swal("Exito", info, "success");
                $http.post("produccion/php/dameConformadores.php?organica=0&idReporteProceso=" + $scope.proceso).success(function (conformadores) {
                    $scope.personalConformacion = conformadores;
                });
            });
            $("#agregarPersonalConformacion").modal('hide');
            $scope.conformador = "";
        }
    };
    $scope.$watch(function () {
        $scope.reporteProceso.procesada = parseFloat($scope.infoLote.netoTotal) - parseFloat($scope.reporteProceso.noProcesada);
    });
    $scope.copiarHora = function (horaFinal) {
        $scope.reporteProceso.inicioHomogeneizado = angular.copy(horaFinal)
    };
    $scope.$watch(function () {
        $scope.reporteProceso.tiempoHomogeneizacion = parseFloat($scope.reporteProceso.finalHomogeneizado) - parseFloat($scope.reporteProceso.inicioHomogeneizado);
    });

    procesoEnvasado = function () {
        proceso = $scope.fechaInterno.fechaProceso;
        $scope.fechasPE.proceso = proceso;
        envasado = $scope.fechaInterno.fechaEnvasado;
        $scope.fechasPE.envasado = envasado;
        return $scope.fechasPE;
    };

    $scope.agregarProcesador = function () {
        if ($scope.parametroTipoCosecha == 1) {
            $http.post("produccion/php/guardarNvoProcesador.php?idReporteProceso=" + $scope.proceso + "&idPersonalOM=" + $scope.procesador).success(function (info) {
                swal("Exito", info, "success");
                $http.post("produccion/php/dameProcesadores.php?idReporteProceso=" + $scope.proceso).success(function (procesadores) {
                    $scope.personalProcesoInocuo = procesadores;
                });
            });
            $("#agregarPersonalProceso").modal('hide');
            $scope.procesador = "";
        } else if ($scope.parametroTipoCosecha == 5){
            $http.post("produccion/php/guardarNvoProcesador.php?organica=1&idReporteProceso=" + $scope.proceso + "&idPersonalOM=" + $scope.procesador).success(function (info) {
                swal("Exito", info, "success");
                $http.post("produccion/php/dameProcesadores.php?organica=1&idReporteProceso=" + $scope.proceso).success(function (procesadores) {
                    $scope.personalProcesoInocuo = procesadores;
                });
            });
            $("#agregarPersonalProceso").modal('hide');
            $scope.procesador = "";
        }
        else if ($scope.parametroTipoCosecha == 6){
            $http.post("produccion/php/guardarNvoProcesador.php?organica=2&idReporteProceso=" + $scope.proceso + "&idPersonalOM=" + $scope.procesador).success(function (info) {
                swal("Exito", info, "success");
                $http.post("produccion/php/dameProcesadores.php?organica=2&idReporteProceso=" + $scope.proceso).success(function (procesadores) {
                    $scope.personalProcesoInocuo = procesadores;
                });
            });
            $("#agregarPersonalProceso").modal('hide');
            $scope.procesador = "";
        }
        else if ($scope.parametroTipoCosecha == 7){
            $http.post("produccion/php/guardarNvoProcesador.php?organica=3&idReporteProceso=" + $scope.proceso + "&idPersonalOM=" + $scope.procesador).success(function (info) {
                swal("Exito", info, "success");
                $http.post("produccion/php/dameProcesadores.php?organica=3&idReporteProceso=" + $scope.proceso).success(function (procesadores) {
                    $scope.personalProcesoInocuo = procesadores;
                });
            });
            $("#agregarPersonalProceso").modal('hide');
            $scope.procesador = "";
        }
        else if ($scope.parametroTipoCosecha == 8){
            $http.post("produccion/php/guardarNvoProcesador.php?organica=4&idReporteProceso=" + $scope.proceso + "&idPersonalOM=" + $scope.procesador).success(function (info) {
                swal("Exito", info, "success");
                $http.post("produccion/php/dameProcesadores.php?organica=4&idReporteProceso=" + $scope.proceso).success(function (procesadores) {
                    $scope.personalProcesoInocuo = procesadores;
                });
            });
            $("#agregarPersonalProceso").modal('hide');
            $scope.procesador = "";
        }
        else if ($scope.parametroTipoCosecha == 9){
            $http.post("produccion/php/guardarNvoProcesador.php?organica=5&idReporteProceso=" + $scope.proceso + "&idPersonalOM=" + $scope.procesador).success(function (info) {
                swal("Exito", info, "success");
                $http.post("produccion/php/dameProcesadores.php?organica=5&idReporteProceso=" + $scope.proceso).success(function (procesadores) {
                    $scope.personalProcesoInocuo = procesadores;
                });
            });
            $("#agregarPersonalProceso").modal('hide');
            $scope.procesador = "";
        }
        else {
            $http.post("produccion/php/guardarNvoProcesador.php?organica=0&idReporteProceso=" + $scope.proceso + "&idPersonalOM=" + $scope.procesador).success(function (info) {
                swal("Exito", info, "success");
                $http.post("produccion/php/dameProcesadores.php?organica=0&idReporteProceso=" + $scope.proceso).success(function (procesadores) {
                    $scope.personalProcesoInocuo = procesadores;
                });
            });
            $("#agregarPersonalProceso").modal('hide');
            $scope.procesador = "";
        }
    };

    $scope.agregarBuscador = function () {
        if ($scope.parametroTipoCosecha == 1) {
            $http.post("produccion/php/guardarNvoBuscador.php?idReporteProceso=" + $scope.proceso + "&idPersonalOM=" + $scope.buscador).success(function (info) {
                swal("Exito", info, "success");
                $http.post("produccion/php/dameBuscadores.php?idReporteProceso=" + $scope.proceso).success(function (buscadores) {
                    $scope.personalFolios = buscadores;
                });
            });
            $("#agregarPersonalFolios").modal('hide');
            $scope.buscador = "";
        }
        else if ($scope.parametroTipoCosecha == 5){
            $http.post("produccion/php/guardarNvoBuscador.php?organica=1&idReporteProceso=" + $scope.proceso + "&idPersonalOM=" + $scope.buscador).success(function (info) {
                swal("Exito", info, "success");
                $http.post("produccion/php/dameBuscadores.php?organica=1&idReporteProceso=" + $scope.proceso).success(function (buscadores) {
                    $scope.personalFolios = buscadores;
                });
            });
            $("#agregarPersonalFolios").modal('hide');
            $scope.buscador = "";
        }
        else if ($scope.parametroTipoCosecha == 6){
            $http.post("produccion/php/guardarNvoBuscador.php?organica=2&idReporteProceso=" + $scope.proceso + "&idPersonalOM=" + $scope.buscador).success(function (info) {
                swal("Exito", info, "success");
                $http.post("produccion/php/dameBuscadores.php?organica=2&idReporteProceso=" + $scope.proceso).success(function (buscadores) {
                    $scope.personalFolios = buscadores;
                });
            });
            $("#agregarPersonalFolios").modal('hide');
            $scope.buscador = "";
        }
        else if ($scope.parametroTipoCosecha == 7){
            $http.post("produccion/php/guardarNvoBuscador.php?organica=3&idReporteProceso=" + $scope.proceso + "&idPersonalOM=" + $scope.buscador).success(function (info) {
                swal("Exito", info, "success");
                $http.post("produccion/php/dameBuscadores.php?organica=3&idReporteProceso=" + $scope.proceso).success(function (buscadores) {
                    $scope.personalFolios = buscadores;
                });
            });
            $("#agregarPersonalFolios").modal('hide');
            $scope.buscador = "";
        }
        else if ($scope.parametroTipoCosecha == 8){
            $http.post("produccion/php/guardarNvoBuscador.php?organica=4&idReporteProceso=" + $scope.proceso + "&idPersonalOM=" + $scope.buscador).success(function (info) {
                swal("Exito", info, "success");
                $http.post("produccion/php/dameBuscadores.php?organica=4&idReporteProceso=" + $scope.proceso).success(function (buscadores) {
                    $scope.personalFolios = buscadores;
                });
            });
            $("#agregarPersonalFolios").modal('hide');
            $scope.buscador = "";
        }
        else if ($scope.parametroTipoCosecha == 9){
            $http.post("produccion/php/guardarNvoBuscador.php?organica=5&idReporteProceso=" + $scope.proceso + "&idPersonalOM=" + $scope.buscador).success(function (info) {
                swal("Exito", info, "success");
                $http.post("produccion/php/dameBuscadores.php?organica=5&idReporteProceso=" + $scope.proceso).success(function (buscadores) {
                    $scope.personalFolios = buscadores;
                });
            });
            $("#agregarPersonalFolios").modal('hide');
            $scope.buscador = "";
        }
         else {
            $http.post("produccion/php/guardarNvoBuscador.php?organica=0&idReporteProceso=" + $scope.proceso + "&idPersonalOM=" + $scope.buscador).success(function (info) {
                swal("Exito", info, "success");
                $http.post("produccion/php/dameBuscadores.php?organica=0&idReporteProceso=" + $scope.proceso).success(function (buscadores) {
                    $scope.personalFolios = buscadores;
                });
            });
            $("#agregarPersonalFolios").modal('hide');
            $scope.buscador = "";
        }
    };

    // ================================================
    //   GUARDAR REPORTE PROCESO
    // ================================================
    $scope.guardarReporteProceso = function () {
        $scope.validProceso = $scope.validarProceso();
        if ($scope.validProceso == true) {
            $scope.guardandoReporte = true;
            if ($scope.proceso == 0) {
                if ($scope.parametroTipoCosecha == 1) {
                    $http.post("produccion/php/verificarLote.php?idLoteInterno=" + $scope.numeroLote).success(function (respuesta) {

                        if (typeof (respuesta) == 'object' && respuesta.hasOwnProperty('error')) {
                            if (respuesta.error) {
                                $scope.guardandoReporte = false;
                                swal('Error', respuesta.message, 'error');
                            } else {
                                if (respuesta.existe == 1) {
                                    $scope.guardandoReporte = false;
                                    swal("", "Verifique, Lote interno existe en otro reporte", "info");
                                } else {
                                    $scope.reporteProceso.idLoteInterno = $scope.numeroLote;
                                    $scope.reporteProceso.responsable = $scope.responsable;
                                    $scope.reporteProceso.supervisor = $scope.supervisor;
                                    $scope.reporteProceso.producto = 1;
                                    $scope.arregloProceso = new Array();
                                    $scope.arregloProceso.push($scope.reporteProceso);
                                    $scope.arregloProceso.push($scope.personalConformacion);
                                    $scope.arregloProceso.push($scope.personalProcesoInocuo);
                                    $scope.arregloProceso.push($scope.personalFolios);
                                    $scope.arregloProceso.push($scope.herramienta);
                                    $http.post("produccion/php/guardarReporteProceso.php", $scope.arregloProceso).success(function (res) {
                                        $scope.guardandoReporte = false;
                                        if (typeof (res) == 'object' && res.hasOwnProperty('error')) {
                                            if (res.error) {
                                                swal('Error', res.message, 'error');
                                            } else {
                                                swal('Hecho', res.message, 'success');
                                                return window.location.href = "#/control";
                                            }
                                        } else {
                                            growl.error('Error');
                                            console.error(res);
                                        }
                                    });
                                }
                            }
                        } else {
                            $scope.guardandoReporte = false;
                            growl.error('Error');
                            console.error(respuesta);
                        }
                    });
                } else if ($scope.parametroTipoCosecha == 5) {
                    $http.post("produccion/php/verificarLote.php?organica=1&idLoteInterno=" + $scope.numeroLote).success(function (respuesta) {
                        if (typeof (respuesta) == 'object' && respuesta.hasOwnProperty('error')) {
                            if (respuesta.error) {
                                $scope.guardandoReporte = false;
                                swal('Error', respuesta.message, 'error');
                            } else {
                                if (respuesta.existe == 1) {
                                    $scope.guardandoReporte = false;
                                    swal("", "Verifique, Lote interno existe en otro reporte", "info");
                                } else {
                                    $scope.reporteProceso.idLoteInterno = $scope.numeroLote;
                                    $scope.reporteProceso.responsable = $scope.responsable;
                                    $scope.reporteProceso.supervisor = $scope.supervisor;
                                    $scope.reporteProceso.producto = 2;
                                    $scope.arregloProceso = new Array();
                                    $scope.arregloProceso.push($scope.reporteProceso);
                                    $scope.arregloProceso.push($scope.personalConformacion);
                                    $scope.arregloProceso.push($scope.personalProcesoInocuo);
                                    $scope.arregloProceso.push($scope.personalFolios);
                                    $scope.arregloProceso.push($scope.herramienta);
                                    $http.post("produccion/php/guardarReporteProcesoMantequilla.php", $scope.arregloProceso).success(function (res) {
                                        $scope.guardandoReporte = false;
                                        if (typeof (res) == 'object' && res.hasOwnProperty('error')) {
                                            if (res.error) {
                                                swal('Error', res.message, 'error');
                                            } else {
                                                swal('Hecho', res.message, 'success');
                                                return window.location.href = "#/control";
                                            }
                                        } else {
                                            growl.error('Error');
                                            console.error(res);
                                        }
                                    });
                                }
                            }
                        } else {
                            $scope.guardandoReporte = false;
                            growl.error('Error');
                            console.error(respuesta);
                        }
                    });
                } 
                else if ($scope.parametroTipoCosecha == 6) {
                    $http.post("produccion/php/verificarLote.php?organica=2&idLoteInterno=" + $scope.numeroLote).success(function (respuesta) {
                        if (typeof (respuesta) == 'object' && respuesta.hasOwnProperty('error')) {
                            if (respuesta.error) {
                                $scope.guardandoReporte = false;
                                swal('Error', respuesta.message, 'error');
                            } else {
                                if (respuesta.existe == 1) {
                                    $scope.guardandoReporte = false;
                                    swal("", "Verifique, Lote interno existe en otro reporte", "info");
                                } else {
                                    $scope.reporteProceso.idLoteInterno = $scope.numeroLote;
                                    $scope.reporteProceso.responsable = $scope.responsable;
                                    $scope.reporteProceso.supervisor = $scope.supervisor;
                                    $scope.reporteProceso.producto = 2;
                                    $scope.arregloProceso = new Array();
                                    $scope.arregloProceso.push($scope.reporteProceso);
                                    $scope.arregloProceso.push($scope.personalConformacion);
                                    $scope.arregloProceso.push($scope.personalProcesoInocuo);
                                    $scope.arregloProceso.push($scope.personalFolios);
                                    $scope.arregloProceso.push($scope.herramienta);
                                    $http.post("produccion/php/guardarReporteProcesoAltiplano.php", $scope.arregloProceso).success(function (res) {
                                        $scope.guardandoReporte = false;
                                        if (typeof (res) == 'object' && res.hasOwnProperty('error')) {
                                            if (res.error) {
                                                swal('Error', res.message, 'error');
                                            } else {
                                                swal('Hecho', res.message, 'success');
                                                return window.location.href = "#/control";
                                            }
                                        } else {
                                            growl.error('Error');
                                            console.error(res);
                                        }
                                    });
                                }
                            }
                        } else {
                            $scope.guardandoReporte = false;
                            growl.error('Error');
                            console.error(respuesta);
                        }
                    });
                } 
                else if ($scope.parametroTipoCosecha == 7) {
                    $http.post("produccion/php/verificarLote.php?organica=3&idLoteInterno=" + $scope.numeroLote).success(function (respuesta) {
                        if (typeof (respuesta) == 'object' && respuesta.hasOwnProperty('error')) {
                            if (respuesta.error) {
                                $scope.guardandoReporte = false;
                                swal('Error', respuesta.message, 'error');
                            } else {
                                if (respuesta.existe == 1) {
                                    $scope.guardandoReporte = false;
                                    swal("", "Verifique, Lote interno existe en otro reporte", "info");
                                } else {
                                    $scope.reporteProceso.idLoteInterno = $scope.numeroLote;
                                    $scope.reporteProceso.responsable = $scope.responsable;
                                    $scope.reporteProceso.supervisor = $scope.supervisor;
                                    $scope.reporteProceso.producto = 2;
                                    $scope.arregloProceso = new Array();
                                    $scope.arregloProceso.push($scope.reporteProceso);
                                    $scope.arregloProceso.push($scope.personalConformacion);
                                    $scope.arregloProceso.push($scope.personalProcesoInocuo);
                                    $scope.arregloProceso.push($scope.personalFolios);
                                    $scope.arregloProceso.push($scope.herramienta);
                                    $http.post("produccion/php/guardarReporteProcesoNaranjo.php", $scope.arregloProceso).success(function (res) {
                                        $scope.guardandoReporte = false;
                                        if (typeof (res) == 'object' && res.hasOwnProperty('error')) {
                                            if (res.error) {
                                                swal('Error', res.message, 'error');
                                            } else {
                                                swal('Hecho', res.message, 'success');
                                                return window.location.href = "#/control";
                                            }
                                        } else {
                                            growl.error('Error');
                                            console.error(res);
                                        }
                                    });
                                }
                            }
                        } else {
                            $scope.guardandoReporte = false;
                            growl.error('Error');
                            console.error(respuesta);
                        }
                    });
                } 
                else if ($scope.parametroTipoCosecha == 8) {
                    $http.post("produccion/php/verificarLote.php?organica=3&idLoteInterno=" + $scope.numeroLote).success(function (respuesta) {
                        if (typeof (respuesta) == 'object' && respuesta.hasOwnProperty('error')) {
                            if (respuesta.error) {
                                $scope.guardandoReporte = false;
                                swal('Error', respuesta.message, 'error');
                            } else {
                                if (respuesta.existe == 1) {
                                    $scope.guardandoReporte = false;
                                    swal("", "Verifique, Lote interno existe en otro reporte", "info");
                                } else {
                                    $scope.reporteProceso.idLoteInterno = $scope.numeroLote;
                                    $scope.reporteProceso.responsable = $scope.responsable;
                                    $scope.reporteProceso.supervisor = $scope.supervisor;
                                    $scope.reporteProceso.producto = 2;
                                    $scope.arregloProceso = new Array();
                                    $scope.arregloProceso.push($scope.reporteProceso);
                                    $scope.arregloProceso.push($scope.personalConformacion);
                                    $scope.arregloProceso.push($scope.personalProcesoInocuo);
                                    $scope.arregloProceso.push($scope.personalFolios);
                                    $scope.arregloProceso.push($scope.herramienta);
                                    $http.post("produccion/php/guardarReporteProcesoAguacate.php", $scope.arregloProceso).success(function (res) {
                                        $scope.guardandoReporte = false;
                                        if (typeof (res) == 'object' && res.hasOwnProperty('error')) {
                                            if (res.error) {
                                                swal('Error', res.message, 'error');
                                            } else {
                                                swal('Hecho', res.message, 'success');
                                                return window.location.href = "#/control";
                                            }
                                        } else {
                                            growl.error('Error');
                                            console.error(res);
                                        }
                                    });
                                }
                            }
                        } else {
                            $scope.guardandoReporte = false;
                            growl.error('Error');
                            console.error(respuesta);
                        }
                    });
                } 
                else if ($scope.parametroTipoCosecha == 9) {
                    $http.post("produccion/php/verificarLote.php?organica=3&idLoteInterno=" + $scope.numeroLote).success(function (respuesta) {
                        if (typeof (respuesta) == 'object' && respuesta.hasOwnProperty('error')) {
                            if (respuesta.error) {
                                $scope.guardandoReporte = false;
                                swal('Error', respuesta.message, 'error');
                            } else {
                                if (respuesta.existe == 1) {
                                    $scope.guardandoReporte = false;
                                    swal("", "Verifique, Lote interno existe en otro reporte", "info");
                                } else {
                                    $scope.reporteProceso.idLoteInterno = $scope.numeroLote;
                                    $scope.reporteProceso.responsable = $scope.responsable;
                                    $scope.reporteProceso.supervisor = $scope.supervisor;
                                    $scope.reporteProceso.producto = 2;
                                    $scope.arregloProceso = new Array();
                                    $scope.arregloProceso.push($scope.reporteProceso);
                                    $scope.arregloProceso.push($scope.personalConformacion);
                                    $scope.arregloProceso.push($scope.personalProcesoInocuo);
                                    $scope.arregloProceso.push($scope.personalFolios);
                                    $scope.arregloProceso.push($scope.herramienta);
                                    $http.post("produccion/php/guardarReporteProcesoMezquite.php", $scope.arregloProceso).success(function (res) {
                                        $scope.guardandoReporte = false;
                                        if (typeof (res) == 'object' && res.hasOwnProperty('error')) {
                                            if (res.error) {
                                                swal('Error', res.message, 'error');
                                            } else {
                                                swal('Hecho', res.message, 'success');
                                                return window.location.href = "#/control";
                                            }
                                        } else {
                                            growl.error('Error');
                                            console.error(res);
                                        }
                                    });
                                }
                            }
                        } else {
                            $scope.guardandoReporte = false;
                            growl.error('Error');
                            console.error(respuesta);
                        }
                    });
                } 
                
                else {
                    $http.post("produccion/php/verificarLote.php?organica=0&idLoteInterno=" + $scope.numeroLote).success(function (respuesta) {
                        if (typeof (respuesta) == 'object' && respuesta.hasOwnProperty('error')) {
                            if (respuesta.error) {
                                $scope.guardandoReporte = false;
                                swal('Error', respuesta.message, 'error');
                            } else {
                                if (respuesta.existe == 1) {
                                    $scope.guardandoReporte = false;
                                    swal("", "Verifique, Lote interno existe en otro reporte", "info");
                                } else {
                                    $scope.reporteProceso.idLoteInterno = $scope.numeroLote;
                                    $scope.reporteProceso.responsable = $scope.responsable;
                                    $scope.reporteProceso.supervisor = $scope.supervisor;
                                    $scope.reporteProceso.producto = 2;
                                    $scope.arregloProceso = new Array();
                                    $scope.arregloProceso.push($scope.reporteProceso);
                                    $scope.arregloProceso.push($scope.personalConformacion);
                                    $scope.arregloProceso.push($scope.personalProcesoInocuo);
                                    $scope.arregloProceso.push($scope.personalFolios);
                                    $scope.arregloProceso.push($scope.herramienta);
                                    $http.post("produccion/php/guardarReporteProcesoOrganico.php", $scope.arregloProceso).success(function (res) {
                                        $scope.guardandoReporte = false;
                                        if (typeof (res) == 'object' && res.hasOwnProperty('error')) {
                                            if (res.error) {
                                                swal('Error', res.message, 'error');
                                            } else {
                                                swal('Hecho', res.message, 'success');
                                                return window.location.href = "#/control";
                                            }
                                        } else {
                                            growl.error('Error');
                                            console.error(res);
                                        }
                                    });
                                }
                            }
                        } else {
                            $scope.guardandoReporte = false;
                            growl.error('Error');
                            console.error(respuesta);
                        }
                    });
                }
            } else {
                url = $scope.parametroTipoCosecha == 1 ? 'produccion/php/guardarEdicionReporteProceso.php' : 'produccion/php/guardarEdicionReporteProceso.php?organica=0';
                switch($scope.parametroTipoCosecha ){
                    case 1: url = 'produccion/php/guardarEdicionReporteProceso.php';
                    break;
                    case 2: url = 'produccion/php/guardarEdicionReporteProceso.php?organica=0';
                    break;
                    case 5: url = 'produccion/php/guardarEdicionReporteProceso.php?organica=1';
                    break;
                    case 6: url = 'produccion/php/guardarEdicionReporteProceso.php?organica=2';
                    break;
                    case 7: url = 'produccion/php/guardarEdicionReporteProceso.php?organica=3';
                    break;
                    case 8: url = 'produccion/php/guardarEdicionReporteProceso.php?organica=4';
                    break;
                    case 9: url = 'produccion/php/guardarEdicionReporteProceso.php?organica=5';
                    break;
                }
                $scope.reporteProceso.idLoteInterno = $scope.numeroLote;
                $scope.reporteProceso.responsable = $scope.responsable;
                $scope.reporteProceso.supervisor = $scope.supervisor;
                $http.post(url, $scope.reporteProceso).success(function (res) {
                    $scope.guardandoReporte = false;
                    if (typeof (res) == 'object' && res.hasOwnProperty('error')) {
                        if (res.error) {
                            swal('Error', res.message, 'error');
                        } else {
                            swal('Hecho', res.message, 'success');
                            return window.location.href = "#/control";
                        }
                    } else {
                        growl.error('Error');
                        console.error(res);
                    }
                });
            }
        }
    };
    //=================================================================
    //      VALIDAR 
    //=================================================================
    $scope.validarProceso = function () {
        $scope.validProceso = false;
        if ($scope.numeroLote == "") {
            growl.error("Se requiere un número de lote");
        } else if ($scope.responsable.idPersonalOM == "") {
            growl.error("Se requiere un responsable");
        } else if ($scope.supervisor.idPersonalOM == "") {
            growl.error("Se requiere un supervisor");
        } else if ($scope.reporteProceso.horaInicio == "") {
            growl.error("Se requiere una hora de inicio");
        } else if ($scope.reporteProceso.numeroTanque == "") {
            growl.error("Se requiere un número de tanque");
        } else if ($scope.reporteProceso.inicioHomogeneizado == "") {
            growl.error("Se requiere una hora de Inicio homogeneizado");
        } else if ($scope.reporteProceso.finalHomogeneizado == "") {
            growl.error("Se requiere una hora de final homogeneizado");
        } else if ($scope.reporteProceso.tiempoReposo == "") {
            growl.error("Se requiere un tiempo de reposo");
        } else if ($scope.reporteProceso.horaFinal == "") {
            growl.error("Se requiere una hora final");
        } else {
            $scope.validProceso = true;
        }
        return $scope.validProceso;
    };


    $scope.pdfProcesos = function () {
        window.open('reportes/produccion/pdfProduccion.php?idReporteProceso=' + $scope.proceso + '&tmp=' + $scope.parametroTipoCosecha, '_blank');
    };
    $scope.xlsProcesos = function () {
        return window.location.href = "reportes/produccion/xlsProduccion.php?idReporteProceso=" + $scope.proceso + '&tmp=' + $scope.parametroTipoCosecha;
    };
}]);


