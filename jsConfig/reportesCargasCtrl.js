form.controller('reportesCargasCtrl', ['$scope', '$routeParams', '$http', 'growl', '$location', function ($scope, $routeParams, $http, growl, $location) {

    // var CARGA_DEFAULT_TABLE_SELECTION = 'CARGA_DEFAULT_TABLE_SELECTION';
    // var URL_NODE = 'http://localhost:4000';
    var URL_NODE = 'https://aupadmin.pcoriente.com';
    $scope.dia = new Date();
    $scope.ahorita = $scope.dia.getTime();
    $scope.codigo = $routeParams.idReporte;
    $scope.idTipoDeMiel = $routeParams.idTipoDeMiel;


    $scope.verTipoDeMiel = '1';
    $scope.estadoReporte = '0';
    $scope.cargandoDatos = false;

    $scope.carga = {};
    $scope.nombreDelSupervisor = {};
    $scope.nombreDelSupervisor.idPersonalOM = "";
    $scope.nombreDelSupervisor.nombre = "";
    $scope.nombreDelResponsable = {};
    $scope.nombreDelResponsable.idPersonalOM = "";
    $scope.nombreDelResponsable.nombre = "";
    $scope.nombreDeLimpieza = {};
    $scope.nombreDeLimpieza.idPersonalOM = "";
    $scope.nombreDeLimpieza.nombre = "";
    $scope.nombreDeRotulacion = {};
    $scope.nombreDeRotulacion.idPersonalOM = "";
    $scope.nombreDeRotulacion.nombre = "";
    $scope.nombreDeMarcacion = {};
    $scope.nombreDeMarcacion.idPersonalOM = "";
    $scope.nombreDeMarcacion.nombre = "";
    $scope.nombreDeMontacargas = {};
    $scope.nombreDeMontacargas.idPersonalOM = "";
    $scope.nombreDeMontacargas.nombre = "";
    $scope.eligeElOperador = {};
    $scope.eligeElOperador.idOperador = "";
    $scope.eligeElOperador.operador = "";
    $scope.eligeLasPlacas = {};
    $scope.eligeLasPlacas.idPlaca = "";
    $scope.eligeLasPlacas.placa = "";
    $scope.informacionAnidada = {};
    $scope.datosUnidad = {};
    $scope.placasC = {};
    $scope.personalCargas = new Array();
    $scope.arrayPlacas = new Array();
    $scope.listaLotes = {};
    $scope.loteCarga = 0;
    $scope.listaPersonal = {};
    $scope.operadoresC = {};
    $scope.mostrarRecipiente = false;
    $scope.ocultarColumnas = false;
    var date = new Date();
    var _mes = date.getMonth() + 1;

    $scope.mostrarMes = _mes.toString();
    $http.get('almacen/php/consultaComboMeses.php').success(function (data) {
        $scope.listaDeMeses = data;
    });


    $scope.infoCargas = [];

    function getInfoCargas(arrayObtenido) {
        $scope.infoCargas = null;
        $scope.cargandoDatos = true;
        url = 'almacen/php/getInfoReportesCargas.php';
        if ($scope.mostrarMes && $location.path() == '/repCarga') {
            url += '?mes=' + $scope.mostrarMes;
        }
        // arrayObtenido[1] == null ? arrayObtenido[1] = '0':arrayObtenido[1]
        $http.post(url, arrayObtenido).success(function (data) {
            if (data.error) {
                growl.error(data.message);
            }
            $scope.infoCargas = data.data;
            $scope.cargandoDatos = false;
        });
    };



    // function getInfoCargas(tipoDeMiel) {
    //     $scope.infoCargas = [];
    //     $http.post('almacen/php/getInfoReportesCargas.php', tipoDeMiel).success(function (arrayCarga) {
    //         $scope.infoCargas = arrayCarga.data;
    //     });
    // };


    function traerTiposDeMiel() {
        $http.get('utilerias/php/traeTiposDeMiel.php').success(function (data) {
            if (!data.hasOwnProperty('error')) {
                $scope.tiposDeMiel = data;
            }
        });
        $http.get('utilerias/php/traeClasificacionesDeMiel.php?ver=1').success(function (data) {
            $scope.clasificacionesDeMiel = data;
        });
    };

    function traerLotesPorTipoDeMiel(tipoDeMiel) {
        $http.get('produccion/php/listaLotes.php?tipoMiel=' + tipoDeMiel).success(function (datas) {
            $scope.listaLotesCarga = datas.infoLote;
        });
    };
    if ($location.path() != '/repCarga') {
        if ($scope.codigo == 0) {
            $scope.carga.unas = '1';
            $scope.carga.cabello = '1';
            $scope.carga.ropa = '1';
            $scope.carga.roto = '0';
            $scope.carga.abolladuras = '0';
            $scope.carga.recipienteAdecuado = '1';
            $scope.carga.lavadoExterior = '1';
            $scope.carga.limpieza = '1';
            $scope.carga.materialExtrano = '0';
            $scope.carga.vehiculoAdecuado = '1';

            traerTiposDeMiel();
            getCotizaciones();
            $http.get('almacen/php/listaNombresPersonal.php').success(function (arrayPersonal) {
                $scope.listaPersonal = arrayPersonal;
            });
            $http.get('almacen/php/listaNombresOperadores.php').success(function (arrayOperador) {
                $scope.operadoresC = arrayOperador;
            });
        } else {
            traerTiposDeMiel();
            getCotizacionesAll();
            traerLotesPorTipoDeMiel($scope.idTipoDeMiel);
            $http.get('almacen/php/listaNombresPersonal.php').success(function (arrayPersonal) {
                $scope.listaPersonal = arrayPersonal;
            });
            $http.get('almacen/php/listaNombresOperadores.php').success(function (arrayOperador) {
                $scope.operadoresC = arrayOperador;
            });
            $http.post("almacen/php/dameReporteCarga.php", { idReporte: $scope.codigo, tipoDeMiel: $scope.idTipoDeMiel }).success(function (info) {
                if (!info.error) {
                    $scope.carga = info;
                    $scope.carga.folioCotizacion = parseInt(info.folioCotizacion)
                    console.log($scope.carga)
                    $scope.nombreDelSupervisor = $scope.carga.supervisor;
                    $scope.nombreDelResponsable = $scope.carga.responsable;
                    $scope.nombreDeLimpieza = $scope.carga.limpiezaPersonal;
                    $scope.nombreDeRotulacion = $scope.carga.rotulacion;
                    $scope.nombreDeMarcacion = $scope.carga.marcacion;
                    $scope.nombreDeMontacargas = $scope.carga.montacargas;
                    $scope.clasificacion = $scope.carga.clasificacionMiel;
                    if ($scope.carga.miel == '1') {
                        $scope.carga.miel = true;
                    } else {
                        $scope.carga.miel = false;
                    }
                    if ($scope.carga.cera == '1') {
                        $scope.carga.cera = true;
                    } else {
                        $scope.carga.cera = false;
                    }
                    if ($scope.carga.apicolas == '1') {
                        $scope.carga.apicolas = true;
                    } else {
                        $scope.carga.apicolas = false;
                    }

                    if ($scope.carga.tambor == '1') {
                        $scope.carga.tambor = true;
                    } else {
                        $scope.carga.tambor = false;
                    }

                    if ($scope.carga.cubeta == '1') {
                        $scope.carga.cubeta = true;
                    } else {
                        $scope.carga.cubeta = false;
                    }
                    if ($scope.carga.mp == '1') {
                        $scope.carga.mp = true;
                    } else {
                        $scope.carga.mp = false;
                    }
                    if ($scope.carga.envasesFrascos == '1') {
                        $scope.carga.envasesFrascos = true;
                    } else {
                        $scope.carga.envasesFrascos = false;
                    }
                    if ($scope.carga.productosDerivados == '1') {
                        $scope.carga.productosDerivados = true;
                    } else {
                        $scope.carga.productosDerivados = false;
                    }
                    $scope.informacionAnidada.vigencia = info.vigencia;
                    $scope.informacionAnidada.licencia = info.licencia;
                    $scope.informacionAnidada.compania = info.compania;
                    $scope.informacionAnidada.remolque = info.remolque;
                    $scope.datosUnidad.marca = $scope.carga.marca;
                    $scope.datosUnidad.tipo = $scope.carga.tipo;
                    $scope.datosUnidad.marca = $scope.carga.marca;
                    $scope.datosUnidad.modelo = $scope.carga.modelo;
                    $scope.datosUnidad.remolque = $scope.carga.remolque;
                    $scope.datosUnidad.marcaRemolque = $scope.carga.marcaRemolque;
                    $scope.datosUnidad.modeloRemolque = $scope.carga.modeloRemolque;
                    $scope.datosUnidad.placaRemolque = $scope.carga.placaRemolque;
                    $scope.personalCargas = info.personalCargas;
                    $scope.eligeElOperador = $scope.carga.idOperador;
                    $scope.eligeLasPlacas = $scope.carga.placa;
                    $scope.loteCarga = $scope.carga.idLoteInterno;
                } else {
                    growl.error(info.message);
                }
            });
        }
    }

    $scope.$watch('carga.miel', function (miel) {
        if (miel) {
            $scope.mostrarRecipiente = true;
        } else {
            $scope.carga.tambor = false;
            $scope.carga.cubeta = false;
            $scope.mostrarRecipiente = false;
        }
    });

    $scope.$watch('[verTipoDeMiel, mostrarMes, estadoReporte]', function (val) {
        if (val[0] == '0') {
            $scope.ocultarColumnas = true;
        } else {
            $scope.ocultarColumnas = false;
        }
        getInfoCargas(val);
    });

    $scope.$watch(function () {
        $scope.carga.pesoNeto = parseFloat($scope.carga.pesoBruto) - parseFloat($scope.carga.pesoTara);
    });

    $scope.modalDelOperador = function () {
        $("#modalDelOperador").modal();
    };
    $scope.mostrarComboDelPersonal = function () {
        $("#mostrarComboDelPersonal").modal();

    };

    $scope.agregarCargador = function () {
        var _datos = {
            idReporte: $scope.codigo,
            tipoDeMiel: $scope.idTipoDeMiel,
            idPersonalOM: $scope.cargador
        }
        $http.post("almacen/php/guardarNvoDescargador.php", _datos).success(function (info) {
            if (info.error) {
                growl.error(info.message);
            } else {
                $http.post("almacen/php/dameDescargadores.php", { idReporte: $scope.codigo, tipoDeMiel: $scope.idTipoDeMiel }).success(function (infor) {
                    $scope.personalCargas = infor.data;
                    if (infor.error) {
                        growl.error(infor.message);
                    }
                });
                $("#mostrarComboDelPersonal").modal('hide');
                $scope.cargador = "";
            }
        });
    };
    $scope.nvaPlaca = function () {
        $scope.numDePlaca = {};
        $scope.numDePlaca.idPlaca = 0;
        $scope.numDePlaca.placa = "";
        $scope.arrayPlacas.push($scope.numDePlaca);
    };
    $scope.eliminarNumDePlaca = function (indice) {
        $scope.arrayPlacas.splice(indice, 1);
        growl.warning("Registro eliminado");
    };
    $scope.$watch('eligeElOperador', function (eligeElOperador) {
        $http.get('almacen/php/comboPlacasId.php?idOperador=' + eligeElOperador)
            .success(function (data) {
                $scope.placasC = data;
            });
        $scope.eligeLasPlacas = $scope.carga.idPlaca;
    }, true);
    $scope.infoDelOperador = function (eligeElOperador) {
        $scope.datosUnidad = {};
        $http.get('almacen/php/infoOperadorAnidado.php?idOperador=' + eligeElOperador)
            .success(function (infAnidada) {
                $scope.informacionAnidada = infAnidada;
            });
    };
    $scope.datosVehiculo = function (eligeLasPlacas) {
        $http.get('almacen/php/infoOperadorAnidado.php?idPlaca=' + eligeLasPlacas)
            .success(function (informacionAnidada) {
                $scope.datosUnidad = informacionAnidada;
            });
    };

    //=================================================================
    //     GUARDAR CHOFER
    //=================================================================
    $scope.guardarOperador = function () {
        $scope.datos = new Array();
        $scope.datos.push($scope.chofer);
        $scope.datos.push($scope.arrayPlacas);
        $http.post("almacen/php/verificarLicencia.php?licencia=" + $scope.chofer.licencia)
            .success(function (respuesta) {
                if (respuesta == 1) {
                    swal("Error!", "Verifique! No. de Licencia duplicado", "error");
                } else {
                    $http.post('almacen/php/guardarProveedorAlmacen.php', { valor: $scope.datos }).success(function (result) {
                        swal("Exito!", "Operador disponible", "success");
                        $http.get('almacen/php/listaNombresOperadores.php').success(function (arrayOperador) {
                            $scope.operadoresC = arrayOperador;
                        });
                        $scope.datos = "";
                        $("#modalDelOperador").modal('hide');

                    });
                }
            }
            );
    };
    $scope.guardarReporteCarga = function () {
        var cargaValida = $scope.validarCarga();
        if (cargaValida == true) {
            // $scope.carga.supervisor = $scope.nombreDelSupervisor;
            $scope.carga.responsable = $scope.nombreDelResponsable;
            $scope.carga.limpiezaPersonal = $scope.nombreDeLimpieza;
            $scope.carga.rotulacion = $scope.nombreDeRotulacion;
            $scope.carga.marcacion = $scope.nombreDeMarcacion;
            $scope.carga.montacargas = $scope.nombreDeMontacargas;
            $scope.carga.idOperador = $scope.eligeElOperador;
            $scope.carga.idPlaca = $scope.eligeLasPlacas;
            $scope.carga.idLoteInterno = $scope.loteCarga;
            switch ($scope.carga.miel) {
                case undefined:
                    $scope.carga.miel = 0;
                    break;
                case false:
                    $scope.carga.miel = 0;
                    break;
                case true:
                    $scope.carga.miel = 1;
                    break;
            }
            switch ($scope.carga.cera) {
                case undefined:
                    $scope.carga.cera = 0;
                    break;
                case false:
                    $scope.carga.cera = 0;
                    break;
                case true:
                    $scope.carga.cera = 1;
                    break;
            }
            switch ($scope.carga.apicolas) {
                case undefined:
                    $scope.carga.apicolas = 0;
                    break;
                case false:
                    $scope.carga.apicolas = 0;
                    break;
                case true:
                    $scope.carga.apicolas = 1;
                    break;
            }
            switch ($scope.carga.tambor) {
                case undefined:
                    $scope.carga.tambor = 0;
                    break;
                case false:
                    $scope.carga.tambor = 0;
                    break;
                case true:
                    $scope.carga.tambor = 1;
                    break;
            }
            switch ($scope.carga.cubeta) {
                case undefined:
                    $scope.carga.cubeta = 0;
                    break;
                case false:
                    $scope.carga.cubeta = 0;
                    break;
                case true:
                    $scope.carga.cubeta = 1;
                    break;
            }
            switch ($scope.carga.mp) {
                case undefined:
                    $scope.carga.mp = 0;
                    break;
                case false:
                    $scope.carga.mp = 0;
                    break;
                case true:
                    $scope.carga.mp = 1;
                    break;
            }
            switch ($scope.carga.envasesFrascos) {
                case undefined:
                    $scope.carga.envasesFrascos = 0;
                    break;
                case false:
                    $scope.carga.envasesFrascos = 0;
                    break;
                case true:
                    $scope.carga.envasesFrascos = 1;
                    break;
            }
            switch ($scope.carga.productosDerivados) {
                case undefined:
                    $scope.carga.productosDerivados = 0;
                    break;
                case false:
                    $scope.carga.productosDerivados = 0;
                    break;
                case true:
                    $scope.carga.productosDerivados = 1;
                    break;
            }
            switch ($scope.carga.producto) {
                //APLICARÁ EN CASOS != MIEL && CERA
                case undefined:
                    $scope.carga.producto = 0;
                    break;
                case null:
                    $scope.carga.producto = 0;
                    break;
            }
            $scope.datosCarga = new Array();
            $scope.datosCarga.push($scope.carga);
            $scope.datosCarga.push($scope.personalCargas);
            if ($scope.codigo == 0) {
                $http.post("almacen/php/guardarReporteCarga.php", { valor: $scope.datosCarga }).success(function (data) {
                    if (data.hasOwnProperty('error')) {
                        swal("", data.message, data.swal);
                        if (!data.error) {
                            return window.location.href = "#/repCarga";
                        }
                    } else {
                        console.error(data);
                    }
                });
            } else {
                $http.post("almacen/php/guardarEdicionReporteCarga.php", { valor: $scope.datosCarga }).success(function (data) {
                    if (data.hasOwnProperty('error')) {
                        if (data.error) {
                            swal("", data.message, "error");
                        } else {
                            swal("", data.message, "success");
                            return window.location.href = "#/repCarga";
                        }
                    } else {
                        console.error(data);
                    }
                });
            }
        }
    };


    $scope.validarCarga = function () {
        console.log($scope.carga.folioCotizacion)
        $scope.cargaValida = false;
        if ($scope.carga.fechaImpresion == undefined) {
            swal("", "Seleccione la Fecha", "info");
        } else if ($scope.eligeElOperador.idOperador == 0) {
            swal("", "Se requiere un Operador", "info");
        } else if ($scope.carga.producto == undefined) {
            swal("", "Elija tipo de producto (convencional, orgánico o no aplica)", "info");
        } else if ($scope.carga.miel == undefined && $scope.carga.cera == undefined && $scope.carga.apicolas == undefined && $scope.carga.mp == undefined && $scope.carga.envasesFrascos == undefined && $scope.carga.productosDerivados == undefined) {
            swal("", "Seleccione al menos un producto", "info");
        } else if ($scope.carga.miel == true && $scope.carga.producto == '0') {
            swal("", "Seleccione tipo de miel válido", "info");
        } else if ($scope.carga.cera == true && $scope.carga.producto == '0') {
            swal("", "Seleccione tipo de cera válido", "info");
        }  else if (!$scope.carga.folioCotizacion > 0) {
            swal("", "SELECCIONE UNA COTIZACION PARA GUARDAR", "info");
        } else {
            $scope.cargaValida = true;
        }
        return $scope.cargaValida;

        // if ($scope.carga.fechaImpresion == undefined) {
        //     swal("Error!", "Seleccione la Fecha", "error");
        // } else if (isNaN($scope.nombreDelResponsable)) {
        //     swal("Error!", "Selecciona responsable", "error");
        // } else if ($scope.eligeElOperador.idOperador == 0) {
        //     swal("Error!", "Se requiere un Operador y placas", "error");
        // } else if (!$scope.carga.producto) {
        //     swal("Error!", "Selecciona un producto", "error");
        // } else if (isNaN($scope.nombreDeLimpieza)
        //     || isNaN($scope.nombreDeRotulacion)
        //     || isNaN($scope.nombreDeMarcacion)
        //     || isNaN($scope.nombreDeMontacargas)) {
        //     swal("Error!", "Selecciona al personal en la sección 'PERSONAL'", "error");
        // } else {
        //     $scope.cargaValida = true;
        // }
        // else if (isNaN($scope.nombreDelSupervisor)) {
        //     swal("Error!", "Selecciona supervisor", "error");
        // } 
        //  else if (!$scope.carga.marca || !$scope.carga.modelo) {
        //     swal("Error!", "Registra la marca y modelo del transporte", "error");
        // }
        // else if ($scope.carga.cantidad == undefined) {
        //     swal("Error!", "Se requiere un número de tambos", "error");
        // }
        // else if ($scope.carga.lote == undefined) {
        //     swal("Error!", "Se requiere una marcación final", "error");
        // }
        // else if (!$scope.eligeLasPlacas) {
        //     swal("Error!", "Selecciona las placas", "error");
        // }
        // else if (!$scope.carga.tipo) {
        //     swal("Error!", "Selecciona un tipo de unidad", "error");
        // }
        // else if (!$scope.carga.roto || !$scope.carga.abolladuras || !$scope.carga.recipienteAdecuado || !$scope.carga.lavadoExterior) {
        //     swal("Error!", "Completa las condiciones de los tambores", "error");
        // }
        // else if (!$scope.carga.limpieza || !$scope.carga.materialExtrano || !$scope.carga.vehiculoAdecuado) {
        //     swal("Error!", "Completa las condiciones de la unidad", "error");
        // } 
        // else if ($scope.carga.horaFinal == undefined) {
        //     swal("Error!", "Se requiere una hora final", "error");
        // } 
    };


    $scope.eliminarLaPersona = function (indice) {
        var _id = $scope.personalCargas[indice].idPersonalDescarga;

        if ($scope.codigo == 0) {
            $scope.personalCargas.splice(indice, 1);
        } else if ($scope.codigo > 0 && $scope.idTipoDeMiel) {
            $http.post("almacen/php/eliminarPersonalDescarga.php", { id: _id, tipoDeMiel: $scope.idTipoDeMiel }).success(function (respuesta) {
                if (respuesta.error) {
                    growl.error(respuesta.message);
                } else {
                    growl.success(respuesta.message);
                    $scope.personalCargas.splice(indice, 1);
                }
            });
        }
    };

    $scope.pdfCarga = function () {
        window.open('reportes/pdfReporteDescarga.php?tipo=2&tipoDeMiel=' + $scope.idTipoDeMiel + '&idReporte=' + $scope.codigo, '_blank');
    };

    $scope.pdfCotizacion = function () {
        if($scope.carga.folioCotizacion > 0){
            var url = 'https://aup.apicultoresunidos.com/#/admin/documentos/cotizacion-pdf/' + $scope.carga.folioCotizacion;
            window.open(url, '_blank');
        }
    };

    // $scope.$watch('verTipoDeMiel', function (tipoDeMiel) {
    //     if (tipoDeMiel) {
    //         window.localStorage.setItem(CARGA_DEFAULT_TABLE_SELECTION, tipoDeMiel);
    //         getInfoCargas(tipoDeMiel);
    //     }
    // });

    // if (localStorage.getItem(CARGA_DEFAULT_TABLE_SELECTION) != null) {
    //     var tipoDeMiel = localStorage.getItem(CARGA_DEFAULT_TABLE_SELECTION);
    //     $scope.verTipoDeMiel = tipoDeMiel;
    //     getInfoCargas(tipoDeMiel);
    // }

    $scope.$watch('carga.producto', function (tipoDeMiel) {
        if (tipoDeMiel) {
            traerLotesPorTipoDeMiel(tipoDeMiel);
        }
    });

    function getCotizaciones() {
        $http.get(URL_NODE + '/api/cotizacion/reportes/carga').success(function (data) {
            $scope.infoCotizaciones = data.data;
        });
    };

    function getCotizacionesAll() {
        $http.get(URL_NODE + '/api/cotizacion').success(function (data) {
            $scope.infoCotizaciones = data.data;
        });
    };

    // Función para eliminar el reporte de carga

    $scope.eliminarReporteCarga = function () {

        if ($scope.carga.idReporte && $scope.idTipoDeMiel) {
            var reporte = {
                idReporte: $scope.carga.idReporte,
                idTipoDeMiel: $scope.idTipoDeMiel
            }

            swal({
                title: '¿Eliminar el reporte de carga?',
                text: 'No se podrá recuperar los datos',
                type: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Eliminar',
                cancelButtonText: 'Cancelar',
                closeOnConfirm: false
            }, function (c) {
                if (c && c == true) {
                    $http.post('almacen/php/eliminarReporteCarga.php?eliminarReporte', reporte).success(function (data) {
                        if (data.hasOwnProperty('error')) {
                            if (data.error) {
                                swal('Error', data.message, 'error');
                            } else {
                                window.location.href = '#/repCarga';
                                swal('Hecho', data.message, 'success');
                            }
                        } else {
                            growl.error('Error');
                            console.error(data);
                        }
                    });
                }
            });
        }
    }

}]);