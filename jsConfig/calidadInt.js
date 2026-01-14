form.controller('calidad', function ($scope, busqueda, $http, $routeParams, growl, $rootScope) {
    $scope.control = false;
    $scope.controlPorcentaje = false;
    $scope.guardar = false;
    $("#idModalBusquedaCalidad").modal({keyboard: false, backdrop: false}
    );

    $scope.loteExp = $routeParams.idLoteExperimental;
    $scope.loteInt = $routeParams.idLoteInterno;

    var tblCalidadDesglozado = this;
//    $scope.informacionCalidad = new Array();
    $scope.informacionCalidad = new Array();
    $scope.todosLosNetos = new Array();
    $scope.informacionMiel = new Array();
    $scope.menuLoteExperimental = new Array();
    $scope.menuLoteInterno = new Array();
    $scope.calidad = 0;
    $scope.totalPesoNeto = 0;
    $scope.totalPesoNetoAsignado = 0;
    $scope.totalPesoNetoNoAsignado = 0;
    $scope.loteExperimental = {};
    $scope.loteExperimental.humedad = "";
    $scope.loteExperimental.kilosTotales = 0;
    $scope.loteExperimental.numeroDeTambores = 0;
    $scope.lotExperimental = {};
    $scope.lotExperimental.humedad = "";
    $scope.lotExperimental.kilosTotales = 0;
    $scope.lotExperimental.numeroDeTambores = 0;
    $scope.loteInterno = {};
    $scope.loteInterno.loteInterno = 0;
    $scope.loteInterno.fechaProceso = "";
    $scope.loteInterno.fechaEnvasado = "";
    $scope.loteInterno.loteCliente = "";
    $scope.loteInterno.marcaFinalCliente = "";
    $scope.loteInterno.observaciones = "";
    $scope.loteInterno.muestraInterna = "";
    $scope.loteInterno.numeroDeTambores = 0;
    $scope.loteInterno.kilosTotales = 0;
    $scope.loteInterno.idLoteExperimental = 0;
    $scope.numeroDeTambores = 0;
    $scope.resultadoLaboratorio = {};
    $scope.resultadoLaboratorio.idresultadoFinal = "";
    $scope.resultadoLaboratorio.resultado = "";
    $scope.listaResultadoFinal = {};

//=================================== C A M B I O S =======================================


    if ($scope.loteExp > 0) {
        $http.post("json/calidad/resultadoExperimental.json").success(function (datos) {
            $scope.resultExperimental = datos.resultExperimental;
        });
        $http.get('calidad/php/traeDetalleLoteExp.php?idLoteExperimental=' + $scope.loteExp).success(function (data) {
            $scope.loteExperimental.idLoteExperimental = data.idLoteExperimental;
            $scope.loteExperimental.fechaExperimental = data.fechaExperimental;
            $scope.loteExperimental.humedad = data.humedad;
            $scope.loteExperimental.numeroDeTambores = data.totalTambores;
            $scope.loteExperimental.kilosTotales = data.totalKilos;
            $scope.resultadoExperimental = data.resultadoExperimental;
            $scope.loteExperimental.loteInterno = data.loteInterno;
            $scope.informacionCalidad = data.informacionCalidad;
        });
    } else {
        $http.get('calidad/php/traeInformacionTotalLotesExperimentales.php').success(function (data) {
            $scope.menuLoteExperimental = data;
        });
    }

    if ($scope.loteInt > 0) {
        $http.get('calidad/php/traeDetalleLoteInt.php?idLoteInterno=' + $scope.loteInt).success(function (data) {
            $scope.loteInterno.idLoteInterno = data.idLoteInterno;
            $scope.loteInterno.idLoteExperimental = data.idLoteExperimental;
            $scope.loteInterno.fechaProceso = data.fechaProceso;
            $scope.loteInterno.fechaEnvasado = data.fechaEnvasado;
            $scope.loteInterno.numeroDeTambores = data.numeroDeTambores;
            $scope.loteInterno.kilosTotales = data.kilosTotales;
            $scope.loteInterno.loteCliente = data.loteCliente;
            $scope.loteInterno.marcaFinalCliente = data.marcaFinalCliente;
//            $scope.loteInterno.lote = data.lote;
            $scope.loteInterno.observaciones = data.observaciones;
            $scope.loteInterno.muestraInterna = data.muestraInterna;
            $scope.informacionCalidad = data.informacionCalidad;
        });
    } else {
        $http.get('calidad/php/traeInformacionTotalLotesInternos.php').success(function (dats) {
            $scope.menuLoteInterno = dats;
        });
    }

//=========================================================================================
//                            T E R M I N A N  C A M B I O S
//=========================================================================================



    $scope.modalLoteInterno = function () {
        $("#modalLoteInterno").modal();
    };

    $scope.editarMarcaCliente = function () {
        $("#editarMarcaCliente").modal();
    };

    $scope.editarHumedad = function () {
        $("#editarHumedad").modal();
    };

    busqueda.dameResultadosFinales().then(function (data) {
        $scope.listaResultadosFinales = data;
    });
    busqueda.damePorcentaje().then(function (data) {
        $scope.listaPorcentajeDisponible = data;
    });
    busqueda.dameSt().then(function (data) {
        $scope.listaStDisponible = data;
    });
    busqueda.dameSf().then(function (data) {
        $scope.listaSfDisponible = data;
    });
    busqueda.dameC13().then(function (data) {
        $scope.listaC13Disponibles = data;
    });
    busqueda.dameHmf().then(function (data) {
        $scope.listaHmfDisponible = data;
    });
    busqueda.dameFloracion().then(function (data) {
        $scope.listaFloracionDisponibles = data;
    });
    busqueda.dameLocalidad().then(function (data) {
        $scope.listaLocalidadDisponibles = data;
    });

    $scope.eliminarLoteCalidad = function (indice) {
        $scope.informacionCalidad.splice(indice, 1);
        growl.warning("Registro eliminado");
        $scope.loteExperimental.kilosTotales = 0;
        angular.forEach($scope.informacionCalidad, function (value, key) {
            $scope.loteExperimental.kilosTotales += parseInt(value.neto);
        });
    };

    $scope.eliminarMasMiel = function (indice) {
        $scope.informacionMiel.splice(indice, 1);
        growl.warning("Registro eliminado");
        $scope.lotExperimental.kilosTotales = 0;
        angular.forEach($scope.informacionMiel, function (value, key) {
            $scope.lotExperimental.kilosTotales += parseInt(value.neto);
        });
    };

    $scope.eliminarTamborExp = function (folioTambor) {
        $http.post("calidad/php/eliminarTamborExperimental.php?folioTambor=" + folioTambor)
                .success(function () {
                    swal("Exito!", "Tambor eliminado", "success");
                    $http.get('calidad/php/traeDetalleLoteExp.php?idLoteExperimental=' + $scope.loteExp).success(function (data) {
                        $scope.loteExperimental.idLoteExperimental = data.idLoteExperimental;
                        $scope.loteExperimental.fechaExperimental = data.fechaExperimental;
                        $scope.loteExperimental.humedad = data.humedad;
                        $scope.loteExperimental.numeroDeTambores = data.totalTambores;
                        $scope.loteExperimental.kilosTotales = data.totalKilos;
                        $scope.resultadoExperimental = data.resultadoExperimental;
                        $scope.informacionCalidad = data.informacionCalidad;
                        $scope.kilosTotales = $scope.loteExperimental.kilosTotales;
                        $scope.numeroDeTambores = $scope.loteExperimental.numeroDeTambores;
                        $http.post('calidad/php/updateTotales.php?idLoteExperimental=' + $scope.loteExp + "&numeroDeTambores=" + $scope.numeroDeTambores + "&kilosTotales=" + $scope.kilosTotales).success(function () {
                        });
                    });
                });
    };


    $scope.buscarCalidadPorParametros = function (granTotal) {
        $scope.rangosHmf = {};
        $scope.rangosPorcentaje = {};
        $scope.rangosHmf.rango1 = $scope.hmfRango1;
        $scope.rangosHmf.rango2 = $scope.hmfRango2;
        $scope.rangosPorcentaje.rango1 = $scope.porcentajeRango1;
        $scope.rangosPorcentaje.rango2 = $scope.porcentajeRango2;
        $scope.listaParametros = {};
        $scope.listaParametros.listaC13 = $scope.c13Seleccionados;
        //  $scope.listaParametros.listaProveedores = $scope.proveedoresSeleccionados;
        $scope.listaParametros.listaResultadosFinales = $scope.resultadosSeleccionados;
        $scope.listaParametros.listaPorcentaje = $scope.porcentajeSeleccionados;
        $scope.listaParametros.listaSt = $scope.stSeleccionados;
        $scope.listaParametros.listaSf = $scope.sfSeleccionados;
        $scope.listaParametros.listaHmf = $scope.hmfSeleccionados;
        $scope.listaParametros.rangosHmf = $scope.rangosHmf;
        $scope.listaParametros.rangosPorcentaje = $scope.rangosPorcentaje;
        $scope.listaParametros.listaFloracion = $scope.floracionesSeleccionadas;
        $scope.listaParametros.listaLocalidad = $scope.localidadesSeleccionadas;
        busqueda.buscarInformacionParametros($scope.listaParametros).then(function (info) {
            if (info == 0) {
                growl.warning("No se encontró ningún registro con esos parámetros");
            } else {
                if ($scope.loteExp > 0) {
//                    $scope.informacionMiel = info;
                    for (i = 0; i < info.length; i++) {
                        $scope.informacionMiel.push(info[i]);
                    }
                    angular.forEach(info, function (value, key) {
                        $scope.lotExperimental.kilosTotales += parseFloat(value.neto);
                    });
                } else {
//                    $scope.informacionCalidad = info;

//                    function Counter() {
//                        this.sum = 0;
//                        this.count = 0;
//                    }
//                    Counter.prototype.add = function (info) {
//                        info.forEach(function (entry) {
//                            this.sum += entry;
//                            ++this.count;
//                        }, this);
//                        console.log(this.sum);
//                        // ^---- Note
//                    };

//                    var total = info.reduce(function (a, b) {
//                        return a + b;
//                    });
//                    console.log(total);

//                    info.neto.reduce(function (valorAnterior, valorActual) {
//                        return valorAnterior + valorActual;
//                        console.log(valorAnterior + valorActual);
//                    });


//                    for (i = 0; i < info.length; i++) {
//                        if (info[i].neto == granTotal) {
//                            break;
//                        }
//                    }

//                    $scope.totalDeEnteros = function () {
//                        $scope.todosLosNetos.reduce(function (valorAnterior, valorActual) {
//                            return $scope.respuestaDe = valorAnterior + valorActual;
//                            console.log($scope.respuestaDe);
//                        });
//                    };
                    $scope.valoresSumados = function () {
                       console.log($scope.todosLosNetos);
                       angular.forEach();
                    };

                    for (i = 0; i < info.length; i++) {
                        var resultado = info[i];
                        var foin = resultado.neto;
                        var entero = parseInt(foin);
                        $scope.todosLosNetos.push(entero);
                        $scope.valoresSumados();
//                        console.log(typeof $scope.todosLosNetos);
//                        $scope.totalDeEnteros();
//                        console.log(entero);
//                        $scope.total = $scope.foin.reduce(function(a, b){ return a + b; });

//                        var foin = resultado.neto;
//                        
//                        var total = foin.reduce(function (valorAnterior, valorActual) {
//                            return valorAnterior + valorActual;
//                            console.log(total);
//                        });
//                        Counter();
                        $scope.informacionCalidad.push(resultado);
//                        console.log($scope.informacionCalidad);
//                        console.log(this.sum);
                    }

//                    console.log($scope.todosLosNetos);
//                    angular.forEach($scope.todosLosNetos, function () {
//                        $scope.sumaEnterosNetos += $scope.todosLosNetos;
//                        console.log($scope.sumaEnterosNetos);
//                    });
//                    entero.reduce(function (valorAnterior, valorActual) {
//                        return $scope.result = valorAnterior + valorActual;
//                        console.log($scope.result);
//                    });

//                    angular.forEach(info, function (value, key) {
//                        $scope.loteExperimental.kilosTotales += parseFloat(value.neto);
//                    });
                }

            }
            $("#idModalBusquedaCalidad").modal('hide');
        });
    };

    $scope.abrirBuscadorParametros = function () {
        $("#idModalBusquedaCalidad").modal({keyboard: false, backdrop: false}
        );
    };

    $scope.buscarFolioCalidad = function () {
        $http.post("calidad/php/buscarCalidad.php?id=" + $scope.folioCalidad)
                .success(function (respuesta) {
                    if (respuesta == 0) {
                        growl.warning("Registro no encontrado");
                    } else {
                        if (respuesta.idCalidad > 0) {
                            respuesta.fechaProceso = new Date(respuesta.fechaProceso);
                            respuesta.fechaEnvase = new Date(respuesta.fechaEnvase);
                        }
                        $scope.informacionCalidad.push(respuesta);
                    }
                });
        $scope.folioCalidad = "";
    };

    $scope.filtrar = function () {
        $("#idModalBusquedaCalidad").modal();
    };

    $scope.guardarExperimental = function () {

        $scope.datosExperimental = new Array();
        $scope.datosExperimental.push($scope.loteExperimental);
        $scope.datosExperimental.push($scope.informacionCalidad);

        $scope.guardar = true;
        $http.post("calidad/php/guardarLoteExperimental.php", {valor: $scope.datosExperimental})
                .success(function (respuesta) {
                    growl.success("Nuevo registro disponible");
                    $http.get('calidad/php/traeInformacionTotalLotesExperimentales.php').success(function (data) {
                        $scope.menuLoteExperimental = data;
                        $scope.guardar = false;
                    });
                    return window.location.href = "#/experimental";

                    $scope.loteExperimental = new Array();
                    $scope.informacionCalidad = new Array();
                });

    };

    $scope.asignarResult = function (resultadoExperimental) {
        $http.post("calidad/php/asignarResultadoLab.php?valor=" + resultadoExperimental + "&idLoteExperimental=" + $scope.loteExp, $scope.informacionCalidad)
                .success(function (respuesta) {
                    growl.success(respuesta);
                });
    };



    $scope.guardarMarcaFinalCliente = function () {
        $http.post("calidad/php/editarMarcaCliente.php?idLoteInterno=" + $scope.loteInt + "&marcaFinalCliente=" + $scope.marcaFinalCliente)
                .success(function (respuesta) {
                    growl.success(respuesta);
                    $http.get('calidad/php/traeDetalleLoteInt.php?idLoteInterno=' + $scope.loteInt).success(function (data) {
                        $scope.loteInterno.idLoteInterno = data.idLoteInterno;
                        $scope.loteInterno.idLoteExperimental = data.idLoteExperimental;
                        $scope.loteInterno.fechaProceso = data.fechaProceso;
                        $scope.loteInterno.fechaEnvasado = data.fechaEnvasado;
                        $scope.loteInterno.numeroDeTambores = data.numeroDeTambores;
                        $scope.loteInterno.kilosTotales = data.kilosTotales;
                        $scope.loteInterno.loteCliente = data.loteCliente;
                        $scope.loteInterno.marcaFinalCliente = data.marcaFinalCliente;
                        $scope.loteInterno.observaciones = data.observaciones;
                        $scope.loteInterno.muestraInterna = data.muestraInterna;
                        $scope.informacionCalidad = data.informacionCalidad;
                    });
                });
        $("#editarMarcaCliente").modal('hide');
        $scope.marcaFinalCliente = "";
    };

    $scope.guardarHumedad = function () {
        $http.post("calidad/php/editarHumedad.php?idLoteExperimental=" + $scope.loteExp + "&humedad=" + $scope.humedad)
                .success(function (respuesta) {
                    growl.success("Humedad actualizada");
                    $http.get('calidad/php/traeDetalleLoteExp.php?idLoteExperimental=' + $scope.loteExp).success(function (data) {
                        $scope.loteExperimental.idLoteExperimental = data.idLoteExperimental;
                        $scope.loteExperimental.fechaExperimental = data.fechaExperimental;
                        $scope.loteExperimental.humedad = data.humedad;
                        $scope.loteExperimental.numeroDeTambores = data.numeroDeTambores;
                        $scope.loteExperimental.kilosTotales = data.kilosTotales;
                        $scope.resultadoExperimental = data.resultadoExperimental;
                        $scope.informacionCalidad = data.informacionCalidad;
                    });
                    console.log(respuesta);
                });
        $("#editarHumedad").modal('hide');
        $scope.humedad = "";
    };



    $scope.guardarMasMiel = function () {
        $scope.guardar = true;
        $scope.datosMasMiel = new Array();
        $scope.datosMasMiel.push($scope.lotExperimental);
        $scope.datosMasMiel.push($scope.informacionMiel);
        $scope.datosMasMiel.push($scope.loteExperimental);
        $http.post("calidad/php/guardarMasMielExperimental.php?idLoteExperimental=" + $scope.loteExp, {valor: $scope.datosMasMiel})
                .success(function (respuesta) {
                    growl.success("Nuevo registro disponible");
                    $http.get('calidad/php/traeDetalleLoteExp.php?idLoteExperimental=' + $scope.loteExp).success(function (data) {
                        $scope.loteExperimental.idLoteExperimental = data.idLoteExperimental;
                        $scope.loteExperimental.fechaExperimental = data.fechaExperimental;
                        $scope.loteExperimental.humedad = data.humedad;
                        $scope.loteExperimental.numeroDeTambores = data.numeroDeTambores;
                        $scope.loteExperimental.kilosTotales = data.kilosTotales;
                        $scope.resultadoExperimental = data.resultadoExperimental;
                        $scope.informacionCalidad = data.informacionCalidad;
                        $scope.guardar = false;
                    });
                    return window.location.href = "#/ediCalidad/" + $scope.loteExp;

                });

    };

    $scope.guardarLoteInterno = function () {
        $scope.guardar = true;
        $scope.datosLoteInterno = new Array();
        $scope.datosLoteInterno.push($scope.loteInterno);
        $scope.datosLoteInterno.push($scope.loteExperimental);
        $scope.datosLoteInterno.push($scope.informacionCalidad);
//        $scope.siCalidad = $scope.validarCalidad();
//        if ($scope.siCalidad == true) {
        if ($scope.loteInterno.loteCliente !== '') {
            $http.post("calidad/php/guardarCalidad.php?idLoteExperimental=" + $scope.loteExp, {valor: $scope.datosLoteInterno})
                    .success(function (respuesta) {
                        growl.success("Nuevo registro disponible");
                        $scope.guardar = false;
                    });

            $("#modalLoteInterno").modal('hide');
//            return window.location.href = "#/lotInt";
        } else {
            growl.error("Se requiere una Marca de laboratorio");
        }
//        }

    };

//    $scope.validarCalidad = function () {
//        $scope.siCalidad = false;
//        if ($scope.loteInterno.fechaProceso == "") {
//            growl.error("Se requiere una fecha de proceso");
//        } else if ($scope.loteInterno.fechaEnvasado == "") {
//            growl.error("Se requiere una fecha de envasado");
//        } else if ($scope.loteInterno.loteCliente == "") {
//            growl.error("Se requiere una marca de laboratorio");
//        } else if ($scope.loteInterno.marcaFinalCliente == "") {
//            growl.error("Se requiere una marca final de cliente");
//        } else if ($scope.loteInterno.observaciones == "") {
//            growl.error("Se requiere llenar el campo observaciones");
//        } else {
//            $scope.siCalidad = true;
//        }
//        return $scope.siCalidad;
//    };

    $scope.buscar = function () {
        var mensaje = true;
        if ((event.keyCode == 13)) {
            $http.post("calidad/php/buscarCalidad.php?id=" + $scope.folioCalidad)
                    .success(function (respuesta) {
                        if (respuesta == 0) {
                            growl.warning("Folio ya agregado en experimental");
                        } else {
                            if ($scope.loteExp > 0) {
                                $scope.informacionMiel.push(respuesta);
                                $scope.lotExperimental.kilosTotales = 0;
                                angular.forEach($scope.informacionMiel, function (value, key) {
                                    $scope.lotExperimental.kilosTotales += parseInt(value.neto);
                                });
                            } else {
                                $scope.informacionCalidad.push(respuesta);
                                $scope.loteExperimental.kilosTotales = 0;
                                angular.forEach($scope.informacionCalidad, function (value, key) {
                                    $scope.loteExperimental.kilosTotales += parseInt(value.neto);
                                });
                            }
                        }
                    });
            $scope.folioCalidad = "";
        }
    };


    $scope.elegirRangoHmf = function () {
        angular.forEach($scope.hmfSeleccionados, function (value, key) {
            if (value == "Por rangos") {
                $scope.control = true;
            }
        });
        if ($scope.control == true) {
            angular.forEach($scope.hmfSeleccionados, function (value, key) {
                if (value != "Por rangos") {
                    $scope.hmfSeleccionados.splice(key, 1);
                    angular.forEach($scope.hmfSeleccionados, function (value, key) {
                    });
                }
            });
        }
    };
    $scope.elegirRangoPorcentaje = function () {
        angular.forEach($scope.porcentajeSeleccionados, function (value, key) {
            if (value == "Por rangos") {
                $scope.controlPorcentaje = true;
            }
        });
        if ($scope.controlPorcentaje == true) {
            angular.forEach($scope.porcentajeSeleccionados, function (value, key) {
                if (value != "Por rangos") {
                    $scope.porcentajeSeleccionados.splice(key, 1);
                    angular.forEach($scope.porcentajeSeleccionados, function (value, key) {
                    });
                }
            });
        }
    };
    $scope.cancelarRangoPorcentaje = function () {
        $scope.controlPorcentaje = false;
        $scope.porcentajeSeleccionados = new Array();
        $scope.porcentajeRango1 = "";
        $scope.porcentajeRango2 = "";
    };
    $scope.cancelarRangoHmf = function () {
        $scope.control = false;
        $scope.hmfSeleccionados = new Array();
        $scope.hmfRango1 = "";
        $scope.hmfRango2 = "";
    };
    $scope.cancelarCalidad = function () {
        $scope.informacionCalidad = new Array();
        return window.location.href = "#/experimental";

    };

    $scope.xlsDescarga = function () {
        return window.location.href = "reportes/calidad/xlsConformacionLote.php?idLoteInterno=" + $scope.loteInt;
    };

    $scope.pdfDescarga = function () {
        window.open('reportes/calidad/pdfConformacionLote.php?idLoteInterno=' + $scope.loteInt, '_blank');

    };


    // ================= ELIMINAR EXPERIMENTAL ================= //
    $scope.eliminarLoteExp = function () {
        swal({
            title: "¿Estás seguro de eliminar todo el lote experimental?",
            text: "Todos los registros se perderán",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#DD6B55",
            confirmButtonText: "Sí, eliminar todo.",
            closeOnConfirm: false
        },
                function () {
                    angular.forEach($scope.informacionCalidad, function (value, key) {
                        $scope.eliminarTodosFolios(value.folioTambor);
                    });

                });
    };

    $scope.eliminarTodosFolios = function (idAlmacen) {

        $http.post("calidad/php/verificarSiExisteReal.php?idLoteExperimental=" + $scope.loteExp).success(function (respuesta) {
            if (respuesta == 1) {
                swal("Error!", "Verifique! Este experimental ya es un Lote Interno", "error");
            } else {
                $http.post("calidad/php/eliminarFoliosLoteExp.php?idAlmacen=" + idAlmacen).success(function (res) {
                });
                $http.post("calidad/php/eliminarLoteExperimental.php?idLoteExperimental=" + $scope.loteExp).success(function (informacion) {
                });
                swal("Exito!", "Toda la informacion se eliminó", "success");

            }
        });


//        $http.post("calidad/php/eliminarFoliosLoteExp.php?idAlmacen=" + idAlmacen).success(function (res) {
//        });
//        $http.post("calidad/php/eliminarLoteExperimental.php?idLoteExperimental=" + $scope.loteExp).success(function (informacion) {
//        });

//        return window.location.href = "#/experimental";
    };


    // ================= ELIMINAR INTERNO ================= //
    $scope.eliminarLoteInt = function () {
        $http.post("calidad/php/verificarSiEsUltimoLoteInt.php?idLoteInterno=" + $scope.loteInt).success(function (respuesta) {
            if (respuesta == 1) {
                $scope.deleteLoteInt();
            } else {
                swal("Error!", "Sólo puedes eliminar el último Lote guardado", "error");
            }

        });
    };

    $scope.deleteLoteInt = function () {
        swal({
            title: "¿Estás seguro de eliminar todo el lote interno?",
            text: "Todos los registros se perderán",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#DD6B55",
            confirmButtonText: "Sí, eliminar todo.",
            closeOnConfirm: false
        },
                function () {
                    $http.post("calidad/php/eliminarLoteInterno.php?idLoteInterno=" + $scope.loteInt + "&idLoteExperimental=" + $scope.loteInterno.idLoteExperimental).success(function (informacion) {
                    });
                    angular.forEach($scope.informacionCalidad, function (value, key) {
                        $scope.eliminarFolios(value.folioTambor);
                    });

                });
    };

    $scope.eliminarFolios = function (idAlmacen) {


        $http.post("calidad/php/eliminarFoliosLoteInt.php?idAlmacen=" + idAlmacen).success(function (res) {
            console.log(res);
        });
        swal("Exito!", "Toda la informacion se eliminó", "success");


    };

});