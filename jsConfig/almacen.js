form.controller('almacen', ['$scope', '$http', '$routeParams', 'growl', '$filter', 'subir', '$location', function ($scope, $http, $routeParams, growl, $filter, subir, $location) {
    $http.get('precios/php/getSolicitudCompra.php?idAlmacenEncabezado=' + $scope.idAlmacenista).success(function (data) {
        $scope.imgPago.images = data;
    });
    $http.get('precios/php/getListaPesos.php?idAlmacenEncabezado=' + $scope.idAlmacenista).success(function (data) {
        $scope.imgLista.images = data;
    });
    $scope.almacen = new Almacenista();
    $scope.proveedor = new Proveedor();
    if ($location.path() == '/precios') {
        $scope.reporteEstado = '1';
    } else {
        $scope.reporteEstado = '2';
    }

    $scope.listaProveedor = {};
    $scope.listaCosecha = {};
    $scope.prove = {};
    $scope.mielCosecha = {};
    $scope.mielCosecha.idTipoDeMiel = "";
    $scope.mielCosecha.tipoDeMiel = "";
    $scope.idAlmacenista = $routeParams.idAlmacen;
    $scope.cosechaOpcion = $routeParams.opcionCosecha;
    $scope.folioEntradaTambor = $scope.idAlmacenista;
    $scope.bloquearGuardar = false;
    $scope.ocultar = false;
    //    -------------------------------------------
    $scope.almacenes = new Array();
    $scope.almacenesEncabezado = {};
    $scope.almacenesDetalle = new Array();
    $scope.almacen.precio = 0;
    $scope.prove.totalCompra = 0;
    $scope.$watch('almacen.zona', function (val) {
        $scope.almacen.zona = $filter('uppercase')(val);
    }, true);
    $scope.repEntrada = {};
    $scope.listaZonasTambos = new Array();
    $scope.detalleAlmacen = new Array();
    $scope.listaAlmacenes = new Array();
    $scope.zona = {};
    $scope.zona.idZonaTambor = 0;
    $scope.zona.nombre = "";
    $scope.cargandoDatos = false;
    $scope.opcionCosecha = '1';
    $scope.showMessage = false; // Para mostrar el mensaje si no hay entradas
    var date = new Date();
    var _mes = date.getMonth() + 1;
    $scope.mostrarMes = _mes.toString();

    $http.get('almacen/php/consultaComboMeses.php').success(function (data) {
        $scope.listaDeMeses = data;
    });

    function traerAlmacen(val) {
        $scope.cargandoDatos = true;
        var url = '';
        url = 'almacen/php/dameAlmacenes.php';
        if ($scope.mostrarMes) {
            url += '?mes=' + $scope.mostrarMes;
        }
        $http.post(url, val).success(function (info) {
            if (info.hasOwnProperty('error')) {
                if (info.error) {
                    swal('Error', info.message, 'error');
                } else {
                    if (info.resultado.length == 0) {
                        $scope.showMessage = true;
                    } else {
                        $scope.showMessage = false;
                    }
                    $scope.listaAlmacenes = info.resultado;
                    $scope.respaldoLista = info.resultado;
                    if ($location.path() == '/precios') {
                        $scope.filtrarPagosTambores($scope.filtroPagado);
                    }
                }
            } else {
                growl.error('Error');
                console.error(info);
            }
            $scope.cargandoDatos = false;
        });
    }

    if ($location.path() == '/tambores') {
        $scope.$watch('[opcionCosecha, mostrarMes, reporteEstado]', function (val) {
            if ($scope.opcionCosecha) {
                window.localStorage.setItem('ENTRADA_TAMBORES_MIEL', $scope.opcionCosecha);
                $scope.listaAlmacenes = [];
                $scope.filtroPagado = null;
                traerAlmacen(val);
            }
        });
    } else if ($location.path() == '/precios') {
        if (window.localStorage.getItem('miel') != null) {
            $scope.opcionCosecha = window.localStorage.getItem('miel');
        }
        $scope.$watch('opcionCosecha', function (tipoMiel) {
            if (tipoMiel) {
                window.localStorage.setItem('opcionCosecha', tipoMiel);
                var datos = [
                    opcionCosecha = tipoMiel,
                    mostrarMes = $scope.mostrarMes,
                    reporteEstado = '1',
                ]
                traerAlmacen(datos);
            }
        });
        if (window.localStorage.getItem('seleccionMes') != null) {
            $scope.mostrarMes = window.localStorage.getItem('seleccionMes');
        }
        $scope.$watch('mostrarMes', function (mesElegido) {
            window.localStorage.setItem('seleccionMes', mesElegido);
            var datos = [
                opcionCosecha = $scope.opcionCosecha,
                mostrarMes = mesElegido,
                reporteEstado = '1',
            ]
            traerAlmacen(datos);
        });
        if (window.localStorage.getItem('filtroPago') != null) {
            $scope.filtroPagado = window.localStorage.getItem('filtroPago');
        }
        $scope.$watch('filtroPagado', function (filtro) {
            window.localStorage.setItem('filtroPago', filtro);
            $scope.filtrarPagosTambores(filtro);
        });
    }



    $scope.$watch('mielCosecha', function (val) {
        if (val.idTipoDeMiel == 1) {
            $http.get("almacen/php/traeZonasDeTambores.php?tipoMiel=" + val.idTipoDeMiel).success(function (info) {
                $scope.listaZonasTambos = info;
            });
        } else if (val.idTipoDeMiel == 2) {
            $http.get("almacen/php/traeZonasDeTambores.php?tipoMiel=" + val.idTipoDeMiel).success(function (info) {
                $scope.listaZonasTambos = info;
            });
        }else if (val.idTipoDeMiel == 5) {
            $http.get("almacen/php/traeZonasDeTambores.php?tipoMiel=" + val.idTipoDeMiel).success(function (info) {
                $scope.listaZonasTambos = info;
            });
        }else if (val.idTipoDeMiel == 6) {
            $http.get("almacen/php/traeZonasDeTambores.php?tipoMiel=" + val.idTipoDeMiel).success(function (info) {
                $scope.listaZonasTambos = info;
            });
        }else if (val.idTipoDeMiel == 7) {
            $http.get("almacen/php/traeZonasDeTambores.php?tipoMiel=" + val.idTipoDeMiel).success(function (info) {
                $scope.listaZonasTambos = info;
            });
        }else if (val.idTipoDeMiel == 8) {
            $http.get("almacen/php/traeZonasDeTambores.php?tipoMiel=" + val.idTipoDeMiel).success(function (info) {
                $scope.listaZonasTambos = info;
            });
        }else if (val.idTipoDeMiel == 9) {
            $http.get("almacen/php/traeZonasDeTambores.php?tipoMiel=" + val.idTipoDeMiel).success(function (info) {
                $scope.listaZonasTambos = info;
            });
        }
    });


    $scope.modalPeriodo = function () {
        $("#modalPeriodo").modal();
    };

    $scope.validarTodoMuestreo = function () {
        angular.forEach($scope.almacenesDetalle, function (value, key) {
            value.estado = $scope.seleccionarTodo;
            $scope.cambiarEstado(value);
        });
    };

    $scope.numeroTambos = function () {
        // $scope.almacenesDetalle = [];
        for (var i = 0; i < $scope.counter; i++) {
            $scope.almacenesDetalle.push({});
        }
    };

    $scope.agregarAlmacen = function () {
        $scope.ok = $scope.validarAlmacen();
        if ($scope.ok == true) {
            if ($scope.idAlmacenista == 0) {
                $scope.almacenesDetalle.push($scope.almacen);
                growl.success("Nuevo pedido agregado");
            } else {
                if ($scope.prove.id == null) {
                    growl.info('Elija un proveedor');
                } else {
                    if (!$scope.almacen.referencia || $scope.almacen.referencia == '') {
                        $scope.almacen.referencia = 0;
                    } else {
                        $scope.almacen.referencia;
                    }
                    $http.post("almacen/php/guardarUnicoAlmacen.php?miel=" + $scope.cosechaOpcion + "&id=" + $scope.idAlmacenista, { valor: $scope.almacen })
                        .success(function (respuesta) {
                            if (typeof (respuesta) == 'object' && respuesta.hasOwnProperty('error')) {
                                if (respuesta.error) {
                                    growl.error(respuesta.message || 'No se han guardado los cambios');
                                } else {
                                    growl.success('Registro guardado');
                                    $http.post("almacen/php/dameAlmacen.php?miel=" + $scope.cosechaOpcion + "&id=" + $scope.idAlmacenista)
                                        .success(function (respuesta) {
                                            $scope.prove.id = respuesta.idProveedor;
                                            $scope.prove.sagarpa = respuesta.idSagarpa;
                                            $scope.prove.localidad = respuesta.localidad;
                                            $scope.prove.folio = respuesta.folio;
                                            $scope.prove.clasificacionMiel = respuesta.clasificacionMiel;
                                            $scope.prove.totalCompra = respuesta.costosTotales;
                                            $scope.almacenesDetalle = respuesta.almacenDetalle;
                                        });
                                }
                            } else {
                                growl.error('Error');
                                console.error(respuesta);
                            }
                        });
                    $scope.almacen = new Almacenista();
                }
            }
        }
    };

    $scope.validarAlmacen = function () {
        $scope.ok = false;
        if ($scope.almacen.zona == 0) {
            growl.error("Se requiere una zona");
        } else
            if ($scope.almacen.bruto == "") {
                growl.error("Se requiere un peso bruto");
            } else if ($scope.almacen.tara == "") {
                growl.error("Se requiere una tara");
            } else if ($scope.almacen.pesoLista == "") {
                growl.error("Se requiere un peso lista");
            } else {
                $scope.ok = true;
            }
        return $scope.ok;
    };

    $scope.eliminarAlmacen = function (indice) {
        $scope.almacenesDetalle.splice(indice, 1);
        growl.warning("Registro eliminado");
    };

    // $scope.editarAlmacenBd = function (id) {
    // $http.post("almacen/php/editarAlmacen.php?id=" + id, { valor: $scope.detalleAlmacen })
    //     .success(function (respuesta) {
    //         swal(respuesta.encabezado, respuesta.mensaje, respuesta.tipo);
    //         $http.post("almacen/php/dameAlmacen.php?id=" + $scope.idAlmacenista)
    //             .success(function (respuesta) {
    //                 $scope.prove.id = respuesta.idProveedor;
    //                 $scope.prove.sagarpa = respuesta.idSagarpa;
    //                 $scope.prove.localidad = respuesta.localidad;
    //                 $scope.almacenesDetalle = respuesta.almacenDetalle;
    //             });
    //     });
    // };

    $scope.traeProv = function (val) {
        if ($scope.idAlmacenista == 0) {
            val = val.idTipoDeMiel;
        }
        if(val == 5 || val == 6 || val == 7|| val == 8 || val == 9){
            val = 1;
        }
        $http.post('proveedores/php/dameProveedores.php?todos=1&tipo=' + val + '&estado=0').success(function (info) {
            $scope.listaProveedor = info;
        });
    }

    if ($scope.idAlmacenista > 0) {
        $scope.traeProv($scope.cosechaOpcion);
        $http.get("almacen/php/traeZonasDeTambores.php?tipoMiel=" + $scope.cosechaOpcion).success(function (info) {
            $scope.listaZonasTambos = info;
        });
        $http.post("almacen/php/dameAlmacen.php?miel=" + $scope.cosechaOpcion + "&id=" + $scope.idAlmacenista)
            .success(function (respuesta) {
                console.log(respuesta)
                $scope.prove.id = respuesta.idProveedor;
                $scope.prove.sagarpa = respuesta.idSagarpa;
                $scope.prove.localidad = respuesta.localidad;
                $scope.prove.folio = respuesta.folio;
                $scope.prove.totalCompra = respuesta.costosTotales;
                $scope.almacenesDetalle = respuesta.almacenDetalle;
                $scope.prove.clasificacionMiel = respuesta.clasificacionMiel;
                if ($scope.cosechaOpcion == '1') {
                    $scope.laMiel = "Miel 100% Pura de Abeja";
                } else if ($scope.cosechaOpcion == '2') {
                    $scope.laMiel = "Miel 100% Orgánica";
                }else if ($scope.cosechaOpcion == '5') {
                    $scope.laMiel = "Miel 100% Mantequilla";
                }else if ($scope.cosechaOpcion == '6') {
                    $scope.laMiel = "Miel 100% Altiplano";
                }else if ($scope.cosechaOpcion == '7') {
                    $scope.laMiel = "Miel 100% Naranjo";
                }else if ($scope.cosechaOpcion == '8') {
                    $scope.laMiel = "Miel 100% Aguacate";
                }else if ($scope.cosechaOpcion == '9') {
                    $scope.laMiel = "Miel 100% Mezquite";
                }
            });

        var urlDiferencia = "almacen/php/totalKgDiferencia.php?id=" + $scope.idAlmacenista;
        if ($scope.cosechaOpcion == '2') {
            urlDiferencia += '&organica';
        }else if ($scope.cosechaOpcion == '5') {
            urlDiferencia += '&mantequilla';
        }else if ($scope.cosechaOpcion == '6') {
            urlDiferencia += '&altiplano';
        }else if ($scope.cosechaOpcion == '7') {
            urlDiferencia += '&naranjo';
        }else if ($scope.cosechaOpcion == '8') {
            urlDiferencia += '&aguacate';
        }else if ($scope.cosechaOpcion == '9') {
            urlDiferencia += '&mezquite';
        }
        $http.post(urlDiferencia).success(function (info) {
            $scope.totalKgBruto = info.totalKgBruto;
            $scope.totalKgTara = info.totalKgTara;
            $scope.totalKgNeto = info.totalKgNeto;
            $scope.totalKgDiferencia = info.totalKgDiferencia;
        });

    }

    if ($scope.idAlmacenista == 0) {
        $http.post("utilerias/php/traeTiposDeMiel.php").success(function (info) {
            if (!info.hasOwnProperty('error')) {
                $scope.listaCosecha = info;
            }
        });
    }

    $scope.cambiarModulo = function (menu) {
        $scope.modulo = menu;
    };

    $scope.guardarAlmacen = function () {
        $scope.almacen.idProveedor = $scope.prove.id;
        if ($scope.idAlmacenista == 0) {
            var ok = $scope.validar();
            if (ok == true) {
                var ok = $scope.validarInformacionDetalle();
                if (ok == true) {
                    $scope.bloquearGuardar = true;
                    $http.post("almacen/php/guardarAlmacen.php?miel=" + $scope.mielCosecha.idTipoDeMiel + "&idProveedor=" + $scope.almacen.idProveedor, { valor: $scope.almacenesDetalle })
                        .success(function (respuesta) {
                            if (typeof (respuesta) == 'object' && respuesta.hasOwnProperty('error')) {
                                if (respuesta.error) {
                                    swal('', respuesta.message || 'No se ha podido guardar los cambios', 'info');
                                } else {
                                    swal("Éxito!", respuesta.message, "success");
                                    $scope.almacen = new Almacenista();
                                    $scope.id = $scope.prove.id;
                                    $scope.bloquearGuardar = false;
                                    return window.location.href = "#/tambores";
                                }
                            } else {
                                growl.error('Error');
                                console.error(respuesta);
                            }
                        });
                }
            }
        } else {
            var ok = $scope.validarInformacionDetalle();
            if (ok == true) {
                $scope.bloquearGuardar = true;
                $http.post("almacen/php/actualizarAlmacen.php?tipoMiel=" + $scope.cosechaOpcion, { valor: $scope.almacenesDetalle })
                    .success(function (respuesta) {
                        if (typeof (respuesta) == 'object' && respuesta.hasOwnProperty('error')) {
                            if (respuesta.error) {
                                swal('', respuesta.message || 'No se ha podido guardar los cambios', 'info');
                            } else {
                                swal("Éxito!", respuesta.message, "success");
                                $scope.bloquearGuardar = false;
                                return window.location.href = "#/tambores";
                            }
                        } else {
                            growl.error('Error');
                            console.error(respuesta);
                        }
                    });
            }
        }
        $http.post("almacen/php/dameAlmacenes.php?idAlmacen=" + $scope.id).success(function (info) {
            $scope.listaAlmacenProveedor = info;
        });
    };


    $scope.validar = function () {
        var ok = false;
        if ($scope.prove.id == undefined) {
            growl.warning("Seleccione un proveedor");
        } else if ($scope.mielCosecha.idTipoDeMiel == "") {
            growl.warning("Seleccione un tipo de miel");
        } else if ($scope.almacenesDetalle.length == 0) {
            growl.warning("Se requiere por lo menos un registro");
        } else {
            ok = true;
        }
        return ok;
    };

    $scope.validarInformacionDetalle = function () {
        var ok = true;
        angular.forEach($scope.almacenesDetalle, function (value, key) {
            if (ok == true) {
                var tara = parseFloat(value.tara);
                if (tara == 0 || value.bruto == 0 || tara == "" || value.bruto == "" || isNaN(tara) == true || isNaN(value.bruto)) {
                    growl.warning("El tara y bruto deben ser mayores a cero");
                    ok = false;
                }
            }
        });
        return ok;
    };
    //AQUI NETOS...
    $scope.pdfPrecios = function () {
        window.open('reportes/envoiceOM.php?idAlmacen=' + $scope.idAlmacenista + '&folioEntradaTambor=' + $scope.folioEntradaTambor + '&tmp=' + $scope.cosechaOpcion, '_blank');
    };


    $scope.xlsPrecios = function () {
        return window.location.href = "reportes/exportaExcel.php?idAlmacen=" + $scope.idAlmacenista + "&folioEntradaTambor=" + $scope.folioEntradaTambor + '&tmp=' + $scope.cosechaOpcion;
    };

    $scope.repEntradaAlmacen = function (opcionReporte) {
        if (opcionReporte == undefined) {
            growl.info("Seleccione un tipo de miel");
        } else if ($scope.repEntrada.fecha1 == undefined) {
            growl.info("Seleccione una fecha inicial");
        } else if ($scope.repEntrada.fecha2 == undefined) {
            growl.info("Seleccione una fecha final");
        } else {
            $("#modalPeriodo").modal('hide');
            return window.location.href = 'reportes/almacen/ReporteEntradaAlmacen.php?tipoCosecha=' + opcionReporte + '&fecha1=' + $scope.repEntrada.fecha1 + '&fecha2=' + $scope.repEntrada.fecha2;
        }

    };

    $scope.almacenn = {};
    if ($scope.folioEntradaTambor > 0) {
        $http.get('./precios/php/infoAlmacen.php?tipo=' + $scope.cosechaOpcion + '&id=' + $scope.idAlmacenista).success(function (data) {
            $scope.almacenn = data;
            $scope.prove.totalNeto = 0;
            for (d in data) {
                $scope.prove.totalNeto += parseInt(data[d].neto);
            }
            $scope.dameTotal();
            angular.forEach($scope.almacenn, function (value, key) {
                if (value.aprobado == '1') {
                    $scope.ocultar = true;
                }
            });
        });
    }

    $scope.dameTotal = function () {
        $scope.prove.totalCompra = 0;
        angular.forEach($scope.almacenn, function (value, key) {
            $scope.prove.totalCompra += parseFloat(value.costoTotal);
        });
    };

    $scope.guardarPrecio = function () {
        if ($scope.prove.folio == 0) {
            swal("Error!", "Ingrese folio!", "error");
        } else {
            $http.post("./precios/php/guardarPrecio.php?miel=" + $scope.cosechaOpcion + "&folio=" + $scope.prove.folio + "&totalCompra=" + $scope.prove.totalCompra + "&id=" + $scope.idAlmacenista, { valor: $scope.almacenn }, $scope.idAlmacenista).success(function (data) {
                if (typeof (data) == 'object' && data.hasOwnProperty('error')) {
                    if (data.error) {
                        swal('', data.message || 'No se ha podido guardar los cambios', 'info');
                    } else {
                        swal("Éxito!", data.message, "success");
                        return window.location.href = "#/precios";
                    }
                } else {
                    growl.error('Error');
                    console.error(respuesta);
                }
            });
        }
    };

    //=================================================================
    // SUBIR ARCHIVO RELACION PDF
    //=================================================================

    $scope.subirPdf = function (id) {
        var name = JSON.stringify(id);
        var file = $scope.file;
        subir.archivo(file, $scope.idAlmacenista);
        $scope.actualiza = true;
        swal("Excelente!", "Archivo guardado!", "success");
        $http.post("almacen/php/dameAlmacenes.php").success(function (info) {
            $scope.listaAlmacenes = info;
        });
        return window.location.href = "#/precios";
    };


    //=================================================================
    // SUBIR ARCHIVO PAGO PDF
    //=================================================================

    $scope.subirPagoPdf = function (id) {
        var name = JSON.stringify(id);
        var file = $scope.file;
        subir.archivoPdf(file, $scope.idAlmacenista);
        $scope.actualiza = true;
        swal("Excelente!", "Archivo guardado!", "success");
        $http.post("almacen/php/dameAlmacenes.php").success(function (info) {
            $scope.listaAlmacenes = info;
        });
        return window.location.href = "#/precios";
    };


    $scope.imgPago = {};
    $http.get('precios/php/getSolicitudCompra.php?idAlmacenEncabezado=' + $scope.idAlmacenista).success(function (data) {
        $scope.imgPago.images = data;
    });

    $scope.eliminarImagen = function (idPdf) {
        $scope.imgPago = {};
        $http.get('precios/php/eliminarSolicitudCompra.php?id=' + idPdf).success(function () {
            $http.get('precios/php/getSolicitudCompra.php?idAlmacenEncabezado=' + $scope.idAlmacenista).success(function (data) {
                $scope.imgPago.images = data;
            });
            growl.success("Archivo eliminado");
        });
    };

    $scope.imgLista = {};
    $http.get('precios/php/getListaPesos.php?idAlmacenEncabezado=' + $scope.idAlmacenista).success(function (data) {
        $scope.imgLista.images = data;
    });

    $scope.eliminarImagenLista = function (idImagen) {
        $scope.imgLista = {};
        $http.get('precios/php/eliminarListaPesos.php?id=' + idImagen).success(function () {
            $http.get('precios/php/getListaPesos.php?idAlmacenEncabezado=' + $scope.idAlmacenista).success(function (data) {
                $scope.imgLista.images = data;
            });
            growl.success("Archivo eliminado");
        });
    };



    /* ------------- P R E C I O S -------------- */

    $scope.filtrarPagosTambores = function (filtro) {
        $scope.nuevoArreglo = [];
        if (filtro == '1') {
            $scope.respaldoLista.forEach(pago => { //Pagados
                if (pago.totalCompra != "0.00") {
                    $scope.nuevoArreglo.push(pago);
                }
            });
            if ($scope.nuevoArreglo.length == 0) {
                growl.info('No hay registros pagados');
            }
            $scope.lista = $scope.nuevoArreglo;
        } else if (filtro == '2') {
            $scope.respaldoLista.forEach(cheque => { //No pagados
                if (cheque.totalCompra == "0.00") {
                    $scope.nuevoArreglo.push(cheque);
                }
            });
            if ($scope.nuevoArreglo.length == 0) {
                growl.info('No hay registros sin pagar');
            }
            $scope.lista = $scope.nuevoArreglo;
        } else if (filtro == '3') { // Todos
            $scope.lista = $scope.respaldoLista;
        }
    }

    $scope.modalTotales = function (val) {
        if (val == 0) {
            growl.info("Seleccione un tipo de miel");
        } else {
            $("#modalTotales").modal();
            $scope.totales = {};
            $http.get('./precios/php/totales.php?tmp=' + val).success(function (data) {
                $scope.totales = data;
            });
        }
    };

    // function traeListaProveedores() {
    //     $http.post('proveedores/php/dameProveedores.php?todos=0&tipo=' + 1 + '&estado=0').success(function (info) {
    //         $scope.listaProveedor = info;
    //     });
    // };

    $scope.modalNetoTotal = function () {
        $("#modalNetoTotales").modal();
    };

    $scope.obtenerValor = function (parametro) {
        angular.forEach($scope.almacenn, function (value, key) {
            if (parametro == 1) {
                value.precio = $scope.precio;
            }
        });
    };

    $scope.obeterInfoAlmacenProveedor = function () {
        $scope.id = $scope.prove.id;
        $http.post("almacen/php/dameAlmacenes.php?idProveedor=" + $scope.id).success(function (info) {
            $scope.listaAlmacenProveedor = info;
        });
    };
    $scope.calcularNeto = function () {
        $scope.almacen.neto = $scope.almacen.bruto - $scope.almacen.tara;
        $scope.calcularDiferencia();
    };
    $scope.calcularDiferencia = function () {
        $scope.almacen.diferencia = $scope.almacen.neto - $scope.almacen.pesoLista;
    };

    $scope.cambiarEstado = function (almacen) {
        if ($scope.cosechaOpcion == 1) {
            $http.post("almacen/php/cambiarEstadoAlmacen.php?valor=" + almacen.estado + "&id=" + almacen.idAlmacen)
                .success(function (respuesta) {
                    growl.success(respuesta);
                });
        } else {
            $http.post("almacen/php/cambiarEstadoAlmacen.php?organica=0&valor=" + almacen.estado + "&id=" + almacen.idAlmacen)
                .success(function (respuesta) {
                    growl.success(respuesta);
                });
        }

    };

    //--------------------------------//

    $scope.mandarImprimirTodo = function () {
        swal({
            title: "¿Estas seguro de mandar a imprimir todo?",
            text: "Todos los registros se van a mandar a imprimir",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#DD6B55",
            confirmButtonText: "Sí, mandar todo.",
            closeOnConfirm: false
        },
            function () {
                angular.forEach($scope.almacenesDetalle, function (value, key) {
                    $scope.mandarImprimir(value.idAlmacen);
                });
                swal("Enviado!", "Toda la informacion se mando a imprimir", "success");
            });
    };



    $scope.nuevoProveedor = function () {
        $("#mdlProveedor").modal();
    };

    function verificarDatosNuevoProveedor() {

        if (!$scope.proveedor.nombre) {
            growl.info('Escriba el nombre del proveedor');
            return false;
        }

        if (!$scope.proveedor.telefono) {
            growl.info('Ingrese el número de teléfono');
            return false;
        }

        if (!$scope.proveedor.tipoDeMiel) {
            growl.info('Seleccione un tipo de producto');
            return false;
        }

        return true;
    }

    $scope.guardarProveedor = function () {
        if (verificarDatosNuevoProveedor()) {
            $http.post("almacen/php/guardarProv.php", { valor: $scope.proveedor })
                .success(function (respuesta) {
                    swal(respuesta.encabezado, respuesta.mensaje, respuesta.tipo);
                    $http.post('proveedores/php/dameProveedores.php?todos=0&tipo=' + $scope.proveedor.tipoDeMiel + '&estado=0').success(function (info) {
                        $scope.listaProveedor = info;
                        $scope.proveedor = new Proveedor();
                        $("#mdlProveedor").modal('hide');
                    });
                });
        }
    };

    $scope.cambiarProveedor = function () {
        $("#mdlEditarProveedor").modal();
    };
    $scope.modificarProveedor = function () {
        $http.post('almacen/php/editarProveedor.php?miel=' + $scope.cosechaOpcion + '&idAlmacen=' + $scope.idAlmacenista + '&idProveedor=' + $scope.proveedorEditado.id)
            .success(function (respuesta) {
                swal("¡Éxito!", "Registros Actualizados", "success");
                $http.post("almacen/php/dameAlmacen.php?miel=" + $scope.cosechaOpcion + "&id=" + $scope.idAlmacenista)
                    .success(function (respuesta) {
                        $scope.prove.id = respuesta.idProveedor;
                        $scope.prove.sagarpa = respuesta.idSagarpa;
                        $scope.prove.localidad = respuesta.localidad;
                        $scope.prove.folio = respuesta.folio;
                        $scope.prove.totalCompra = respuesta.costosTotales;
                        $scope.almacenesDetalle = respuesta.almacenDetalle;
                        $scope.prove.clasificacionMiel = respuesta.clasificacionMiel;

                    });
            });
    };
    $scope.mandarImprimir = function (idAlmacen) {
        if (idAlmacen && $scope.cosechaOpcion) {
            var datos = {
                idTipoDeMiel: $scope.cosechaOpcion,
                idAlmacen: idAlmacen,
                estado: 0
            }
            $http.post("./mandarImprimir.php", datos).success(function (res) {
                if (!res.error) {
                    growl.success(res.message);
                } else {
                    growl.error(res.message);
                }
            });
        } else {
            growl.error('Uno de los parámetros necesarios no es válido');
        }
    };

    $scope.modalProveedor = function (val) {
        if (val == 0) {
            growl.info("Seleccione un tipo de miel");
        } else {
            $("#modalProveedores").modal();
            $scope.listaNombreProveedores = {};
            $http.get('proveedores/php/dameProveedores.php?todos=0&tipo=' + val + '&estado=0').success(function (info) {
                $scope.listaNombreProveedores = info;
            });
        }
    };
    $scope.generarReportePrecios = function () {
        $scope.idProveedor = $scope.prove.id;
        if ($scope.idProveedor == undefined) {
            growl.info("Seleccione un proveedor");
        } else {
            return window.location.href = 'reportes/administrativo/xlsReporteGerenalPago.php?idProveedor=' + $scope.idProveedor + '&tmp=' + $scope.opcionCosecha;
        }
    };

    $scope.pagoProveedores = function (valor) {
        if (valor == 0) {
            growl.info("Seleccione un tipo de miel");
        } else {
            return window.location.href = 'reportes/precios/xlsPagoDeTamboresConMiel.php?tmp=' + valor;
        }
    };
}])

    .directive('uploaderModel', ["$parse", function ($parse) {
        return {
            restrict: 'A',
            link: function (scope, iElement, iAttrs) {
                iElement.on("change", function (e) {
                    $parse(iAttrs.uploaderModel).assign(scope, iElement[0].files[0]);
                });
            }
        };
    }]).service('subir', ["$http", "$q", function ($http, $q) {
        this.archivo = function (file, idAlmacenista) {
            var deferred = $q.defer();
            var pdf = new FormData();
            pdf.append("file", file);

            return $http.post("./precios/php/insertarPdf.php?idAlmacenEncabezado=" + idAlmacenista, pdf, {
                headers: {
                    "Content-type": undefined
                },
                transformRequest: angular.identity
            })
                .success(function (res) {
                    deferred.resolve(res);
                })
                .error(function (msg, code) {
                    deferred.reject(msg);
                });
            return deferred.promise;
        };

        this.archivoPdf = function (file, idAlmacenista) {
            var deferred = $q.defer();
            var pdf1 = new FormData();
            pdf1.append("file", file);

            return $http.post("./precios/php/insertarPagoPdf.php?idAlmacenEncabezado=" + idAlmacenista, pdf1, {
                headers: {
                    "Content-type": undefined
                },
                transformRequest: angular.identity
            })
                .success(function (res) {
                    deferred.resolve(res);
                })
                .error(function (msg, code) {
                    deferred.reject(msg);
                });
            return deferred.promise;
        };

    }]);


