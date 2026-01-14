form.controller('envasadoCtrl', ['$scope', '$http', '$routeParams', 'growl', '$q', function ($scope, $http, $routeParams, growl, $q) {

    //=========================P A R A M E T R O===========================
    $scope.ctrlEnvasado = $routeParams.idReporteEnvasado;
    $scope.idTipoDeMiel = $routeParams.idTipoDeMiel;
    //    ----------------------------------------------------------------

    $scope.guardando_reporte = false;
    $scope.menuEnvasado = new Array();
    $scope.loteEnvasado = 0;
    $scope.reporteEnvasado = {};
    $scope.reporteEnvasado.numeroTanque = "";
    $scope.reporteEnvasado.horaInicio = "";
    $scope.reporteEnvasado.horaFinal = "";
    $scope.reporteEnvasado.tiempo = "";
    $scope.reporteEnvasado.observaciones = "";
    $scope.reporteEnvasado.kilosProcesados = 0;
    $scope.reporteEnvasado.netosEnvasados = 0;
    $scope.reporteEnvasado.merma = "";
    $scope.reporteEnvasado.faltante = 0;
    $scope.personalEnvasado = new Array();
    $scope.personalAyudanteInterno = new Array();
    $scope.personalAyudanteExterno = new Array();
    $scope.envasados = new Array();
    $scope.envasado = {};
    //==========================================================================
    // TRAE INFORMACION PARA ARMAR LA TABLA QUE MUESTRA INFORMACION DEL ENCABEZADO
    //==========================================================================

    function getLotes(tipoDeMiel) {
        $http.get('produccion/php/listaLotes.php?tipoMiel=' + tipoDeMiel).success(function (datas) {
            $scope.listaLotes = datas.infoLote;
        });
    };

    if ($scope.ctrlEnvasado > 0 && $scope.idTipoDeMiel) {
        getLotes($scope.idTipoDeMiel);
        $http.get('almacen/php/listaNombresPersonal.php').success(function (arrayPersonal) {
            $scope.listaPersonal = arrayPersonal;
        });

        $http.post('produccion/php/traeDetalleReporteEnvasado.php', { idReporte: $scope.ctrlEnvasado, tipoDeMiel: $scope.idTipoDeMiel }).success(function (data) {
            $scope.reporteEnvasado = data.data;
            $scope.loteEnvasado = "" + $scope.reporteEnvasado.idLoteInterno + "";
            $scope.reporteEnvasado.fecha = new Date(data.data.fecha);
            $scope.reporteEnvasado.fecha.setDate($scope.reporteEnvasado.fecha.getDate() + 1);
            $scope.personalEnvasado = data.data.personalEnvasado;
            $scope.personalAyudanteInterno = data.data.personalAyudanteInterno;
            $scope.personalAyudanteExterno = data.data.personalAyudanteExterno;
            $scope.envasados = data.data.envasados;
        });
    } else {
        getLotes($scope.idTipoDeMiel);
        function traerEncabezadoEnvasado(tipoDeMiel) {
            $http.post("produccion/php/traeEncabezadoCtrlEnvasado.php", tipoDeMiel).success(function (info) {
                if (info.error) {
                    growl.error(info.message);
                }
                $scope.menuEnvasado = info.data;
            });
        }
        $http.get('almacen/php/listaNombresPersonal.php').success(function (arrayPersonal) {
            $scope.listaPersonal = arrayPersonal;
        });
    }


    //    ----------------------------------------------------------------

    $scope.updateModel2 = function () {
        $scope.envasados = [];
        for (var i = 0; i < $scope.counter; i++) {
            $scope.envasados.push({});
        }
    };

    $scope.ponerValoresIguales = function (id) {
        angular.forEach($scope.envasados, function (value, key) {
            if (id == 1) {
                value.bruto = $scope.pesBruto;
            } else if (id == 2) {
                value.tara = $scope.pesTara;
            } else {
                console.info(id);
            }
        });
    };

    //==========================================================================
    // MODALES
    //==========================================================================
    $scope.agregarPersonalEnvasado = function () {
        $("#agregarPersonalEnvasado").modal();
    };
    $scope.agregarAyudanteInterno = function () {
        $("#agregarAyudanteInterno").modal();
    };
    $scope.agregarAyudanteExterno = function () {
        $("#agregarAyudanteExterno").modal();
    };
    $scope.$watch('[reporteEnvasado.horaFinal, reporteEnvasado.horaInicio]', function () {
        $scope.reporteEnvasado.tiempo = parseFloat($scope.reporteEnvasado.horaFinal) - parseFloat($scope.reporteEnvasado.horaInicio);
    });
    $scope.$watch('[envasados, envasado]', function () {
        $scope.envasado.neto = parseFloat($scope.envasado.bruto) - parseFloat($scope.envasado.tara);
        $scope.totalNetos();
    }, true);

    $scope.agregarTambo = function () {
        if ((event.keyCode == 13)) {
            $scope.envasados.push($scope.envasado);
            $scope.envasado = "";
        }
    };

    $scope.$watch('[reporteEnvasado.kilosProcesados, reporteEnvasado.netosEnvasados, reporteEnvasado.merma]', function () {
        $scope.reporteEnvasado.faltante = parseFloat($scope.reporteEnvasado.kilosProcesados) - parseFloat($scope.reporteEnvasado.netosEnvasados) - parseFloat($scope.reporteEnvasado.merma);
    }, true);

    function sumarNetos() {
        var promise = $q.defer();
        var totalNeto = 0;
        angular.forEach($scope.envasados, function (value, key) {
            totalNeto += parseFloat(value.neto);
        });
        $scope.reporteEnvasado.netosEnvasados = totalNeto;
        promise.resolve(totalNeto);
        return promise.promise;
    }

    $scope.totalNetos = function () {
        sumarNetos().then(function (totalNeto) {
            $scope.reporteEnvasado.netosEnvasados = totalNeto;
        });
    };

    $scope.$watch('loteEnvasado', function (loteEnvasado) {
        if (loteEnvasado && $scope.idTipoDeMiel) {
            $http.get('produccion/php/informacionMielProcesada.php?idLoteInterno=' + loteEnvasado + '&tipoDeMiel=' + $scope.idTipoDeMiel).success(function (data) {
                if (typeof (data) == 'object' && data.hasOwnProperty('error')) {
                    if (data.error) {
                        swal('Error', data.message, 'error');
                    } else {
                        $scope.reporteEnvasado.kilosProcesados = data.infoLote.procesada;
                        $scope.reporteEnvasado.fechaEnvasado = data.infoLote.fechaEnvasado;
                    }
                } else {
                    growl.error('Error');
                    console.error(data);
                }
            });
        }
    }, true);



    $scope.borrarTambo = function (indice) {
        $scope.envasadoo = {};
        $scope.envasadoo.idPesoEnvasado = 0;
        $scope.envasadoo.indice = 0;
        angular.forEach($scope.envasados, function (value, key) {
            if (key == indice) {
                $scope.envasadoo.indice = key;
                $scope.envasadoo.idPesoEnvasado = value.idPesoEnvasado;
            }
        });
        if ($scope.ctrlEnvasado == 0) {
            $scope.envasados.splice($scope.envasadoo.indice, 1);
            growl.warning("Registro eliminado");
        } else if ($scope.ctrlEnvasado > 0) {
            $http.post("produccion/php/eliminarTambo.php?idPesoEnvasado=" + $scope.envasadoo.idPesoEnvasado).success(function (respuesta) {
                if (respuesta.hasOwnProperty('error')) {
                    if (respuesta.error) {
                        swal('Error', respuesta.message, 'error');
                    } else {
                        $scope.envasados.splice($scope.envasadoo.indice, 1);
                        growl.success(respuesta.message);
                        sumarNetos().then(function (totalNeto) {
                            $scope.guardarReporteEnvasado();
                        });
                    }
                } else {
                    growl.error('Error al eliminar.');
                    console.error(respuesta);
                }
            });
        }
    };
    // ================================================
    //   GUARDAR REPORTE ENVASADO
    // ================================================
    $scope.guardarReporteEnvasado = function () {
        $scope.validEnvasado = $scope.validarEnvasado();
        if ($scope.validEnvasado == true) {
            $scope.guardando_reporte = true;
            if ($scope.ctrlEnvasado == 0) {
                $scope.reporteEnvasado.idLoteInterno = $scope.loteEnvasado;
                $scope.arregloEnvasado = new Array();
                $scope.arregloEnvasado.push($scope.reporteEnvasado);
                $scope.arregloEnvasado.push($scope.personalEnvasado);
                $scope.arregloEnvasado.push($scope.personalAyudanteInterno);
                $scope.arregloEnvasado.push($scope.personalAyudanteExterno);
                $scope.arregloEnvasado.push($scope.envasados);
                $http.post("produccion/php/guardarReporteEnvasado.php",
                    { valor: $scope.arregloEnvasado, tipoDeMiel: $scope.idTipoDeMiel }).success(function (respuesta) {
                        $scope.guardando_reporte = false;
                        if (respuesta.hasOwnProperty('error')) {
                            if (respuesta.error) {
                                swal('Error', respuesta.message, 'error');
                            } else {
                                swal('', respuesta.message, 'success');
                                return window.location.href = "#/envasado";
                            }
                        } else {
                            growl.error('Error');
                            console.error(respuesta);
                        }
                    });
            } else {
                $scope.reporteEnvasado.idLoteInterno = $scope.loteEnvasado;
                $scope.arregloEdicion = new Array();
                $scope.arregloEdicion.push($scope.reporteEnvasado);
                $scope.arregloEdicion.push($scope.envasados);

                $http.post("produccion/php/guardarEdicionReporteEnvasado.php",
                    { valor: $scope.arregloEdicion, tipoDeMiel: $scope.idTipoDeMiel }).success(function (response) {
                        $scope.guardando_reporte = false;
                        if (response.hasOwnProperty('error')) {
                            if (response.error) {
                                swal('Error', response.message, 'error');
                            } else {
                                swal('', response.message, 'success');
                            }
                        } else {
                            growl.error('Error');
                            console.error(response);
                        }
                    });
            }
        }
    };

    //=================================================================
    //      VALIDAR 
    //=================================================================
    $scope.validarEnvasado = function () {
        $scope.validEnvasado = false;
        if ($scope.loteEnvasado == "") {
            growl.error("Se requiere un número de lote");
        } else if ($scope.reporteEnvasado.numeroTanque == "") {
            growl.error("Se requiere un número de tanque");
        } else if ($scope.reporteEnvasado.horaInicio == "") {
            growl.error("Se requiere una hora de inicio");
        } else if ($scope.reporteEnvasado.horaFinal == "") {
            growl.error("Se requiere una hora final");
        } else if ($scope.reporteEnvasado.merma == "") {
            growl.error("Se requiere una cantidad en merma");
        } else {
            $scope.validEnvasado = true;
        }
        return $scope.validEnvasado;
    };
    $scope.guardarEnvasador = function () {
        $http.post("produccion/php/guardarNvoEnvasador.php",
            {
                idReporteEnvasado: $scope.ctrlEnvasado,
                idPersonal: $scope.envasador,
                tipoDeMiel: $scope.idTipoDeMiel
            }).success(function (info) {
                if (info.error) {
                    growl.error(info.message);
                } else {
                    growl.success(info.message);
                    $http.post("produccion/php/dameEnvasadores.php", { idReporteEnvasado: $scope.ctrlEnvasado, tipoDeMiel: $scope.idTipoDeMiel }).success(function (envasadores) {
                        if (envasadores.error) {
                            growl.error(envasadores.message);
                        } else {
                            $scope.personalEnvasado = envasadores.data;
                        }
                    });
                }
            });
        $("#agregarPersonalEnvasado").modal('hide');
        $scope.envasador = "";
    };
    $scope.guardarAyudanteInterno = function () {
        $http.post("produccion/php/guardarNvoAyudanteInterno.php",
            {
                idReporteEnvasado: $scope.ctrlEnvasado,
                idPersonalOM: $scope.ayudanteInterno,
                tipoDeMiel: $scope.idTipoDeMiel
            }).success(function (info) {
                if (info.error) {
                    growl.error(info.message());
                } else {
                    growl.success(info.message);
                }
                $http.post("produccion/php/dameAyudantesInternos.php",
                    {
                        idReporteEnvasado: $scope.ctrlEnvasado,
                        tipoDeMiel: $scope.idTipoDeMiel
                    }).success(function (ayudantesI) {
                        if (ayudantesI.error) {
                            growl.error(ayudantesI.message);
                        } else {
                            $scope.personalAyudanteInterno = ayudantesI.data;
                        }
                    });
            });
        $("#agregarAyudanteInterno").modal('hide');
        $scope.ayudanteInterno = "";
    };
    $scope.guardarAyudanteExterno = function () {
        $http.post("produccion/php/guardarNvoAyudanteExterno.php",
            {
                idReporteEnvasado: $scope.ctrlEnvasado,
                idPersonalOM: $scope.ayudanteExterno,
                tipoDeMiel: $scope.idTipoDeMiel
            }).success(function (info) {
                if (info.error) {
                    growl.error(info.message);
                } else {
                    growl.success(info.message);
                    $http.post("produccion/php/dameAyudantesExternos.php",
                        {
                            idReporteEnvasado: $scope.ctrlEnvasado,
                            tipoDeMiel: $scope.idTipoDeMiel
                        }).success(function (ayudantesE) {
                            if (ayudantesE.error) {
                                growl.error(ayudantesE.message);
                            } else {
                                $scope.personalAyudanteExterno = ayudantesE.data;
                            }
                        });
                }
            });
        $("#agregarAyudanteExterno").modal('hide');
        $scope.ayudanteExterno = "";
    };
    $scope.eliminarEnvasado = function (idEnvasado, index) {
        $http.post("produccion/php/eliminarEnvasado.php?idEnvasado=" + idEnvasado, { idEnvasado: idEnvasado, tipoDeMiel: $scope.idTipoDeMiel }).success(function (respuesta) {
            if (respuesta.error) {
                growl.error(respuesta.message);
            } else {
                $scope.personalEnvasado.splice(index, 1);
                growl.success(respuesta.message);
            }
        });
    };
    $scope.eliminarInterno = function (idAyudanteInterno, indice) {
        $http.post("produccion/php/eliminarAyudanteInterno.php", {
            idAyudanteInterno: idAyudanteInterno,
            tipoDeMiel: $scope.idTipoDeMiel
        }).success(function (respuesta) {
            if (respuesta.error) {
                growl.error(respuesta.message);
            } else {
                growl.success(respuesta.message);
                $scope.personalAyudanteInterno.splice(indice, 1);
            }

        });
    };
    $scope.eliminarExterno = function (idAyudanteExterno, indice) {
        $http.post("produccion/php/eliminarAyudanteExterno.php",
            {
                idAyudanteExterno: idAyudanteExterno,
                tipoDeMiel: $scope.idTipoDeMiel
            })
            .success(function (respuesta) {
                if (respuesta.error) {
                    growl.error(respuesta.message);
                } else {
                    growl.success(respuesta.message);
                    $scope.personalAyudanteExterno.splice(indice, 1);
                }
            });
    };

    $scope.pdfEnvasado = function () {
        console.log($scope.idTipoDeMiel);
        window.open('reportes/produccion/pdfEnvasado.php?idReporteEnvasado=' + $scope.ctrlEnvasado + '&tipoDeMiel=' + $scope.idTipoDeMiel, '_blank');
    };

    $scope.xlsEnvasado = function () {
        console.log($scope.idTipoDeMiel);
        return window.location.href = "reportes/produccion/xlsEnvasado.php?idReporteEnvasado=" + $scope.ctrlEnvasado + "&tipoDeMiel=" + $scope.idTipoDeMiel;
    };

    $scope.$watch('verTipoDeMiel', function (tipoDeMiel) {
        $scope.menuEnvasado = null;
        if (tipoDeMiel) {
            localStorage.setItem('opcion_miel_control_envasado', tipoDeMiel);
            traerEncabezadoEnvasado(tipoDeMiel);
        }
    });

    if (localStorage.getItem('opcion_miel_control_envasado') != null && traerEncabezadoEnvasado) {
        var tipoDeMiel = localStorage.getItem('opcion_miel_control_envasado');
        $scope.verTipoDeMiel = tipoDeMiel;
        traerEncabezadoEnvasado(tipoDeMiel);
    }

    $scope.nuevoControlEnvasado = function () {
        if ($scope.verTipoDeMiel) {
            window.location.href = "#/nvoEnvasado/0/" + $scope.verTipoDeMiel;
        } else {
            growl.info('Selecciona un tipo de Miel');
        }
    };


}]);


