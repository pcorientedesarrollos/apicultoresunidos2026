
form.controller('cajaChicaCtrl', ['$scope', '$http', '$routeParams', 'growl', '$rootScope', function ($scope, $http, $routeParams, growl, $rootScope) {

    //========================= P A R A M E T R O S===========================
    $scope.paramCaja = $routeParams.idMes;
    $scope.idCajaChica = $routeParams.idCajaChica;
    //-----------------------------------------------------------------------

    $scope.menuCajaChica = new Array();
    $scope.datosCajaChica = new Array();
    $scope.cajaChica = new ControlCajaChica();
    $scope.lstBancos = {};
    $scope.lstQentas = {};
    $scope.bancoIngreso = {};
    $scope.bancoIngreso.idBanco = "";
    $scope.bancoIngreso.banco = "";
    $scope.bancoCuenta = {};
    $scope.bancoCuenta.idCuenta = "";
    $scope.bancoCuenta.cuenta = "";
    $scope.respaldoTotalEnCuenta = 0;
    $scope.totalEnCuenta = 0;
    $scope.respaldoSaldoCaja = 0;
    $scope.saldo = 0;
    $scope.totalIngresos = 0;
    $scope.totalEgresos = 0;
    $scope.optImprimirReporteMensual = {};
    // $scope.$on('editar-movimiento', function (event, args) {
    //     $scope.cajaChicaEncabezado = args.cajaChicaEncabezado;
    //     $scope.datosCajaChica = args.datosCajaChica;
    //     $scope.$apply();
    // });

    //========================= C T R L  C A J A  C H I C A ===========================


    // Funciones para traer cuentas, subcuentas y subsubcuentas

    function traerListaCuentas(tipo) {
        $http.post('catalogos/php/traeCuentas.php', tipo).success(function (data) {
            if (typeof (data) == 'object' && data.hasOwnProperty('error')) {

                if (data.error) {
                    swal('', data.message, 'info');
                } else {
                    $scope.listaCuentasSeleccionadas = data.data;
                }

            } else {
                growl.error('Error');
                console.error(data);
            }
        })
    }

    function traerListaSubcuentas(idCuenta) {
        $scope.listaSubcuentasSeleccionadas = null;
        $scope.listaSubSubcuentas = null;
        $http.post('catalogos/php/traeSubcuentas.php?idCuentaConcepto=' + idCuenta).success(function (data) {
            if (typeof (data) == 'object' && data.length >= 0) {
                // Ya no se va a usar en listaDeConceptos
                $scope.listaSubcuentasSeleccionadas = data;
            } else {
                growl.error('Error');
                console.error(data);
            }
        })
    }

    $scope.obtenerSubsubcuentas = function (idSubcuenta) {
        if (idSubcuenta) {
            $scope.listaSubSubcuentas = null;
            $http.get('catalogos/php/traerSubsubcuentas.php?idSubcuenta=' + idSubcuenta).success(function (resultado) {
                if (typeof (resultado) == 'object' && resultado.hasOwnProperty('error')) {
                    if (resultado.error) {
                        swal('', resultado.message, 'error');
                    } else {
                        $scope.listaSubSubcuentas = resultado.data;
                    }
                } else {
                    growl.error('Error');
                    console.error(resultado);
                }
            });
        }
    }


    $scope.cambioSeleccionCuenta = function (idCuenta = false) {
        // Cuando cambia la cuenta seleccionada, 
        // trae las subcuentas

        if (!idCuenta) {
            // si no manda la cuenta, la toma del scope, primero la verifica
            if ($scope.cajaChica.seleccionMovimiento && $scope.cajaChica.seleccionMovimiento.idCuentaConcepto) {
                traerListaSubcuentas($scope.cajaChica.seleccionMovimiento.idCuentaConcepto);
            }
        } else {
            // Si envia la cuenta, lo hace directo
            traerListaSubcuentas(idCuenta);
        }
    }

    // $scope.traerConceptosYSubconceptos = function () {
    //     $http.get('controlAdministrativo/php/conceptosIngreso.php').success(function (data) {
    //         $scope.lstConceptosIngreso = data;
    //     });
    //     if ($scope.cajaChica.concepto) {
    //         $http.get('controlAdministrativo/php/subconceptosAnidados.php?idConceptoCC=' + $scope.cajaChica.concepto.idConceptoCC).success(function (data) {
    //             $scope.lstSubconceptosCC = data;
    //         });
    //     }
    // };

    function traerListaPersonas(idTipoPersona) {
        $http.post('controlAdministrativo/polizaCheque/php/traerNombres.php', idTipoPersona).success(function (data) {
            $scope.personas = data;
        });
    };

    $scope.$watch('cajaChicaEncabezado.tipoDeCliente', function (val) {
        $scope.cajaChicaEncabezado.nombreDe = "";
        traerListaPersonas(val);
    });

    if ($scope.paramCaja == 0) {
        return window.location.href = "#/cajaChica";
    }

    if ($scope.paramCaja > 0) {
        $scope.imprimirReporteCajaChica = function (optImprimirReporteMensual) {
            console.log(optImprimirReporteMensual);
            if (optImprimirReporteMensual) {
                switch (optImprimirReporteMensual.tipo) {
                    case '1':
                        if (optImprimirReporteMensual.fechaInicial && optImprimirReporteMensual.fechaFinal) {
                            if (optImprimirReporteMensual.reporte == 1) {
                                window.open('reportes/cajaChica/pdfReporteMensualCC.php?inicial=' + optImprimirReporteMensual.fechaInicial + '&final=' + optImprimirReporteMensual.fechaFinal);
                            } else if (optImprimirReporteMensual.reporte == 2) {
                                return window.location.href = 'reportes/cajaChica/xlsReporteMensualCC.php?inicial=' + optImprimirReporteMensual.fechaInicial + '&final=' + optImprimirReporteMensual.fechaFinal;
                            }
                            $('#modalImprimirReporteMensual').modal('hide');
                        } else {
                            growl.info('Selecciona las fechas requeridas');
                        }
                        break;
                    case '2':
                        if (optImprimirReporteMensual.reporte == 1) {
                            window.open('reportes/cajaChica/pdfReporteMensualCC.php?idMes=' + $scope.paramCaja)
                        } else if (optImprimirReporteMensual.reporte == 2) {
                            return window.location.href = 'reportes/cajaChica/xlsReporteMensualCC.php?idMes=' + $scope.paramCaja;
                        }
                        $('#modalImprimirReporteMensual').modal('hide');
                        break;
                    default:
                        growl.error('Opción desconocida');
                        break;
                }

            } else {
                growl.info('Selecciona una opción');
            }
        };

        $scope.abrirModalImprimirReporteMensual = function (tipo) {
            $scope.optImprimirReporteMensual.reporte = tipo;
            $('#modalImprimirReporteMensual').modal();
        };

        $http.get('controlAdministrativo/php/listaDeBancos.php').success(function (datas) {
            $scope.lstBancos = datas;
        });
        $http.get('controlAdministrativo/php/detalleCajaChica.php?idMes=' + $scope.paramCaja).success(function (datas) {
            $scope.datosCajaChica = datas.datos;
            $scope.saldoCajaChica = datas.encabezado.totalCajaChica;
            $scope.totalPendientes = datas.encabezado.pendientes;
            $scope.saldoActualCajaChica = datas.encabezado.saldoActualCajaChica;
        });
        $http.get('controlAdministrativo/php/traeMes.php?idMes=' + $scope.paramCaja).success(function (data) {
            if (data.hasOwnProperty('error')) {
                if (data.error) {
                    swal('Error', data.message, 'error');
                } else {
                    console.warn('Se obtuvo el mes');
                    $scope.mes = data.mes;
                    $http.post('controlAdministrativo/php/confirmarCierreMesCC.php', $scope.paramCaja).success(function (data) {
                        $scope.mes.block = data.block;
                    });
                }
            } else {
                growl.error('Error');
                console.error(data);
            }
        });
        $http.get('controlAdministrativo/php/ultimoSaldoCajaChica.php').success(function (data) {
            $scope.saldo = data.elUltimoSaldo;
        });

        // Función para cerrar la caja chica, y pasar el saldo al siguiente mes

        $scope.cerrarCajaChica = function () {
            if ($scope.mes.block) {
                growl.warning('Intentalo de nuevo');
            } else {
                swal({
                    title: "¿Desea cerrar el mes?",
                    text: "No deberá hacer más movimientos en este",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55",
                    cancelButtonText: "Cancelar",
                    confirmButtonText: "Sí, cerrar mes",
                    closeOnConfirm: false
                }, function () {
                    $http.post('controlAdministrativo/php/cerrarCajaChica.php', $scope.paramCaja).success(function (data) {
                        if (data.success) {
                            $scope.mes.block = true;
                        }
                        swal('', data.message, data.swal);
                    });
                });
            }
        };

    } else if ($scope.idCajaChica) {
        //no modificar esta función ni el PHP
        $http.post('controlAdministrativo/php/detalleMovimiento.php', { idCajaChica: $scope.idCajaChica }).success(function (data) {
            $scope.cajaChicaEncabezado = data.encabezado;
            $scope.datosCajaChica = data.detalle;
        });
    } else {
        var date = new Date();
        var _mes = date.getMonth() + 1;
        // var _mes = 10;
        var _fecha = date.getFullYear() + '-' + _mes + '-' + date.getDate();
        $scope.$watch('datosCajaChica', function () {
            $scope.cajaChicaEncabezado.total = 0;
            if ($scope.cajaChicaEncabezado.tipo == 0) {
                $scope.datosCajaChica.forEach(function (element) {
                    $scope.cajaChicaEncabezado.total = $scope.cajaChicaEncabezado.total + parseFloat(element.importe);
                }, this);
            } else {
                $scope.datosCajaChica.forEach(function (element) {
                    $scope.cajaChicaEncabezado.total = $scope.cajaChicaEncabezado.total + parseFloat(element.cantidad);
                }, this);
            }
        }, true)
        $http.post('controlAdministrativo/php/menuCajaChica.php').success(function (datas) {
            $scope.menuCajaChica = datas;
        });
        $http.get('controlAdministrativo/php/comboSubcuentas.php').success(function (data) {
            $scope.listaDeConceptos = data;
        });
        $http.get('controlAdministrativo/php/conceptosIngreso.php').success(function (data) {
            $scope.lstConceptosIngreso = data;
        });
        // $http.post('controlAdministrativo/php/traeCatalogoProveedores.php', true).success(function (data) {
        //     $scope.catalogoTablaProveedoresMantto = data.data;
        // });
        // $http.post('controlAdministrativo/php/traeCatalogoClientes.php', true).success(function (data) {
        //     $scope.catalogoTablaClientes = data.data;
        // });

        function calcularSaldo(_mes, _fecha) {

            $http.post('controlAdministrativo/php/verificarSaldoInicialCajaChica.php?mes=' + _mes, { fecha: _fecha }).success(function (data) {
                switch (data) {
                    case '0':
                        swal({
                            title: "¡Espera!",
                            text: "Por favor ingresa un saldo inicial para la caja chica:",
                            type: "input",
                            closeOnConfirm: false,
                            animation: "slide-from-top",
                            inputPlaceholder: "Cantidad"
                        },
                            function (inputValue) {
                                if (inputValue === false)
                                    return false;
                                if (inputValue === "") {
                                    swal.showInputError("Necesitas llenar el campo!");
                                    return false
                                }
                                $http.post('controlAdministrativo/php/iniciarCajaChica.php', { saldo: inputValue, fecha: _fecha, mes: _mes }).success(function (data) {
                                    swal("", data.message, data.swal);
                                    $http.post('controlAdministrativo/php/ultimoSaldoCajaChica.php', _mes).success(function (data) {
                                        $scope.respaldoSaldoCaja = data.elUltimoSaldo;
                                        $scope.saldo = data.elUltimoSaldo;
                                    });
                                });
                            });
                        break;
                    case '1':
                        $http.post('controlAdministrativo/php/ultimoSaldoCajaChica.php', _mes).success(function (data) {
                            $scope.saldo = data.elUltimoSaldo;
                            $scope.respaldoSaldoCaja = data.elUltimoSaldo;
                        });
                        break;
                    case '2':
                        window.location.href = '#/cajaChica';
                        swal('', 'Cierra el mes anterior para hacer los movimientos');
                        break;
                }
            });

        };

        try {
            throw new Error('punto y coma');
        } catch (error) {

        }

        $scope.calcularSaldoDeMes = function () {
            if ($scope.cajaChicaEncabezado.fecha && !isNaN(Date.parse($scope.cajaChicaEncabezado.fecha))) {
                calcularSaldo($scope.cajaChicaEncabezado.fecha.split('-')[1], $scope.cajaChicaEncabezado.fecha);
            } else {
                growl.warning('No hay una fecha especificada');
            }
        };

        $http.get('controlAdministrativo/php/listaDeBancos.php').success(function (datas) {
            $scope.lstBancos = datas;
        });

    }

    // function traerNombreCajaChica() {
    //     $http.get('proveedores/php/listaProveedores.php?clientes').success(function (data) {
    //         $scope.clientes = data;
    //     })
    //     $http.get('controlMantenimiento/php/listaPersonalOM.php').success(function (data) {
    //         $scope.lstOMPersonal = data;
    //     });
    //     $http.post('controlAdministrativo/php/traeCatalogoProveedores.php', true).success(function (data) {
    //         $scope.catalogoTablaProveedoresMantto = data.data;
    //     });
    //     $http.post('controlAdministrativo/php/traeCatalogoClientes.php', true).success(function (data) {
    //         $scope.catalogoTablaClientes = data.data;
    //     });
    // };

    // $scope.$watch('cajaChica.concepto', function (paramCC) {
    //     if (paramCC) {
    //         $http.get('controlAdministrativo/php/subconceptosAnidados.php?idConceptoCC=' + paramCC.idConceptoCC).success(function (data) {
    //             $scope.lstSubconceptosCC = data;
    //         });
    //     }
    // });

    // Watch para cuando se está editando el concepto
    // $scope.$watch('editar_detalleCajaChica.idConcepto', function (paramCC) {
    //     if (paramCC) {
    //         $http.get('controlAdministrativo/php/subconceptosAnidados.php?idConceptoCC=' + paramCC).success(function (data) {
    //             $scope.lstSubconceptosCC = data;
    //         });
    //     }
    // });

    $scope.traeLasCuentasDelBanco = function (bancoIngreso) {
        $http.get('controlAdministrativo/php/infoCuentasAnidadas.php?idBanco=' + bancoIngreso).success(function (data) {
            $scope.lstQentas = data;
        });
        $rootScope.elIdBancos = bancoIngreso;
        $http.get('controlAdministrativo/php/traeBanco.php?idBanco=' + bancoIngreso).success(function (data) {
            $scope.cajaChica.banco = data.banco;
        });
    };

    $scope.traeSaldoBancoSel = function (bancoCuenta) {
        $http.post('controlAdministrativo/php/infoSaldosAnidados.php?idCuenta=' + bancoCuenta, { idBanco: $scope.cajaChica.bancoCuenta, idMes: _mes, fecha: _fecha }).success(function (data) {
            $scope.respaldoTotalEnCuenta = data.ultimoSaldo;
            $scope.cajaChica.totalEnCuenta = $scope.respaldoTotalEnCuenta;
        });
        $rootScope.laCuenta = bancoCuenta;
        $http.get('controlAdministrativo/php/traeCuenta.php?idCuenta=' + bancoCuenta).success(function (data) {
            $scope.cajaChica.cuenta = data.numDeCuenta;
        });
    };

    $scope.prevCajaChica = function (cantidad) {

        if (parseFloat(cantidad) > parseFloat($scope.respaldoSaldoCaja)) {
            $scope.cajaChica.cantidad = parseFloat($scope.respaldoSaldoCaja);
            cantidad = parseFloat($scope.respaldoSaldoCaja);
        }

        if (!isNaN(parseFloat(cantidad))) {
            var cantidad = parseFloat(cantidad);
            var saldoActual = parseFloat($scope.respaldoSaldoCaja);
            $scope.datosCajaChica.forEach(function (element) {
                saldoActual = $scope.cajaChicaEncabezado.tipo == 1 ? saldoActual - parseFloat(element.cantidad) : saldoActual + parseFloat(element.importe);
            }, this);
            var nuevoSaldo = $scope.cajaChicaEncabezado.tipo == 1 ? saldoActual - cantidad : saldoActual + cantidad;;
            $scope.saldo = nuevoSaldo;

        } else if (cantidad === '' || typeof cantidad !== 'number') {
            var saldoActual = parseFloat($scope.respaldoSaldoCaja);
            $scope.datosCajaChica.forEach(function (element) {
                saldoActual = $scope.cajaChicaEncabezado.tipo == 1 ? saldoActual - parseFloat(element.cantidad) : saldoActual + parseFloat(element.importe);;
            }, this);
            $scope.saldo = saldoActual;
        }
    };

    $scope.cajaChica = {};
    $scope.datosCajaChica = [];
    $scope.cajaChicaEncabezado = {};
    // $scope.cajaChicaEncabezado.fecha = _fecha;
    $scope.filterButton = 'Ver Egresos';
    $rootScope.lockTemplate = false;
    // function traerClientes() {
    //     $http.get('proveedores/php/listaProveedores.php?clientes').success(function (data) {
    //         $scope.clientes = data;
    //     })
    //     $http.get('controlMantenimiento/php/listaDeTecnicos.php').success(function (data) {
    //         $scope.lstTenicosP = data;
    //     })
    //     $http.get('controlMantenimiento/php/listaPersonalOM.php').success(function (data) {
    //         $scope.lstOMPersonal = data;
    //     })
    // };


    $scope.inicializarIngreso = function () {
        $scope.cajaChicaEncabezado.tipo = 0;
        // traerClientes();
        traerListaCuentas($scope.cajaChicaEncabezado.tipo);
    };

    $scope.inicializarEgreso = function () {
        $scope.cajaChicaEncabezado.tipo = 1;
        $scope.esDiversos = true;
        traerListaCuentas($scope.cajaChicaEncabezado.tipo);
        // traerClientes();F
    };

    $scope.proveedorDesdeCC = function () {
        $scope.altasCC = {};
        $http.get('personalOaxacaMiel/php/listaAreas.php').success(function (datas) {
            $scope.listaAreas = datas;
        });

        $http.get('personalOaxacaMiel/php/listaPuestos.php').success(function (datos) {
            $scope.listaPuestos = datos;
        });
        $("#modlDeProveedor").modal();
    };

    function verificarDatosNuevoProveedor(altasCC) {

        if (!altasCC.nombre) {
            growl.info('Escriba el nombre del proveedor');
            return false;
        }

        if (!altasCC.idSagarpa) {
            growl.info('Ingrese el número ID SAGARPA');
            return false;
        }

        if (!altasCC.telefono) {
            growl.info('Ingrese el número de teléfono');
            return false;
        }

        if (!altasCC.tipoDeMiel) {
            growl.info('Seleccione un tipo de producto');
            return false;
        }

        return true;
    }

    $scope.guardarNuevoProveedorCC = function (altasCC) {
        switch (altasCC.proveedorTipo) {
            case '1':
                if (verificarDatosNuevoProveedor(altasCC)) {
                    $http.post("almacen/php/guardarProv.php", { valor: altasCC }).success(function (respuesta) {
                        swal(respuesta.encabezado, respuesta.mensaje, respuesta.tipo);
                        traerListaPersonas(altasCC.proveedorTipo);
                        altasCC = {};
                        // $http.get('proveedores/php/listaProveedores.php?clientes').success(function (data) {
                        //     $scope.clientes = data;
                        $("#modlDeProveedor").modal('hide');
                        // });
                    });
                }
                break;
            case '3':
                $http.post("controlAdministrativo/php/guardarProveedor.php", { datos: altasCC, fecha: _fecha }).success(function (info) {
                    swal("", info.message, info.swal);
                    if (!info.error) {
                        traerListaPersonas(altasCC.proveedorTipo);
                        altasCC = {};
                        // $http.post('controlAdministrativo/php/traeCatalogoProveedores.php', true).success(function (data) {
                        //     $scope.catalogoTablaProveedoresMantto = data.data;
                        $("#modlDeProveedor").modal('hide');
                        // });
                    }
                });
                break;
            case '4':
                $http.post("personalOaxacaMiel/php/guardarPersonalOM.php?alta_rapida", altasCC).success(function (info) {
                    if (info.hasOwnProperty('error')) {
                        if (info.error) {
                            swal('Error', info.message, 'error');
                        } else {
                            swal('Éxito', info.message, 'success');
                            traerListaPersonas(altasCC.proveedorTipo);
                            // $http.get('controlMantenimiento/php/listaPersonalOM.php').success(function (data) {
                            //     $scope.lstOMPersonal = data;
                            $scope.altasCC = {};
                            // });
                            $("#modlDeProveedor").modal('hide');
                        }
                    } else {
                        growl.error('Error');
                        console.error(info);
                    }
                });
                break;
            case '6':
                $http.post("controlAdministrativo/php/guardarCliente.php", altasCC).success(function (info) { //Guardar cliente con alta rápida
                    swal("", info.message, info.swal);
                    if (!info.error) {
                        traerListaPersonas(altasCC.proveedorTipo);
                        altasCC = {};
                        // $http.post('controlAdministrativo/php/traeCatalogoClientes.php', true).success(function (data) {
                        //     $scope.catalogoTablaClientes = data.data;
                        $("#modlDeProveedor").modal('hide');
                        // });
                    }
                });
                break;
            case '8':
                $http.post("catalogos/controlAdministrativo/php/guardarPropios.php", altasCC).success(function (data) {

                    if (data.hasOwnProperty('error')) {
                        if (data.error) {
                            swal('Error', data.message, 'error');
                        } else {
                            swal('Éxito', data.message, 'success');
                            traerListaPersonas(altasCC.proveedorTipo);
                            $scope.altasCC = {};
                            $("#modlDeProveedor").modal('hide');
                        }
                    } else {
                        growl.error('Error');
                        console.error(data);
                    }
                });
                break;
            case '9':
                $http.post("catalogos/controlAdministrativo/php/guardarAcreedores.php", altasCC).success(function (data) {
                    if (data.hasOwnProperty('error')) {
                        if (data.error) {
                            swal('Error', data.message, 'error');
                        } else {
                            swal('Éxito', data.message, 'success');
                            traerListaPersonas(altasCC.proveedorTipo);
                            $scope.altasCC = {};
                            $("#modlDeProveedor").modal('hide');
                        }
                    } else {
                        growl.error('Error');
                        console.error(data);
                    }
                });
                break;
            default:
                break;
        }
    };

    function comprobarEncabezado(cajaChicaEncabezado) {
        if (!cajaChicaEncabezado
            || !cajaChicaEncabezado.fecha
            || !cajaChicaEncabezado.tipoDeCliente
            || !cajaChicaEncabezado.hora) {
            return false;
        }
        return true;
    };

    $scope.guardarCajaChica = function () {
        if (!comprobarEncabezado($scope.cajaChicaEncabezado)) {
            growl.error("Llena todos los campos del encabezado");
        } else {
            var encabezadoHora = document.getElementById('encabezadoHora');
            $scope.cajaChicaEncabezado.hora = encabezadoHora.value;
            $scope.cajaChicaEncabezado.idMes = $scope.cajaChicaEncabezado.fecha.split('-')[1];
            if ($scope.datosCajaChica.length >= 1) {
                $http.post('controlAdministrativo/php/nuevoMovimientoCajaChica.php', [$scope.datosCajaChica, $scope.cajaChicaEncabezado]).success(function (data) {
                    if (typeof (data) == 'object' && data.hasOwnProperty('error')) {
                        if (data.error) {
                            growl.error("Ocurrió un error");
                        } else {
                            swal("¡Éxito!", "Registro guardado", "success");
                            window.open(data.url, "_blank");
                            $scope.cajaChica = {};
                            $scope.datosCajaChica = [];
                            $scope.cajaChicaEncabezado = {};
                            $scope.cajaChicaEncabezado.fecha = _fecha;
                            $rootScope.lockTemplate = false;
                            return window.location.href = "#/cajaChica";
                        }
                    } else {
                        growl.error('Error');
                        console.error(data);
                    }
                });
            } else {
                growl.warning('Agrega movimiento antes de guardar');
            }
        }
    };

    function verificarImporte() {

        if ($scope.cajaChica.importe) {
            if ($scope.cajaChica.kg == null || $scope.cajaChica.kg == undefined) {
                if ($scope.cajaChica.precio == null || $scope.cajaChica.precio == undefined) {
                    $scope.cajaChica.precio = 0;
                    $scope.cajaChica.kg = 0;
                } else {
                    $scope.cajaChica.kg = parseFloat($scope.cajaChica.importe) / parseFloat($scope.cajaChica.precio);
                }
            } else if ($scope.cajaChica.precio == null || $scope.cajaChica.precio == undefined) {
                if ($scope.cajaChica.kg == null || $scope.cajaChica.kg == undefined) {
                    $scope.cajaChica.precio = 0;
                    $scope.cajaChica.kg = 0;
                } else {
                    $scope.cajaChica.precio = parseFloat($scope.cajaChica.importe) / parseFloat($scope.cajaChica.kg);
                }
            }
        }
    };

    function verificarMovimientoDeIngreso(cajaChica) {
        if (!cajaChica.seleccionMovimiento || !cajaChica.seleccionSubcuenta) {
            return false;
        }
        if (!cajaChica.descripcion) {
            return false;
        }
        if (!cajaChica.kg && !cajaChica.importe) {
            return false;
        }
        if (!cajaChica.precio && !cajaChica.importe) {
            return false;
        }
        if (!cajaChica.importe) {
            return false;
        }
        return true;
    };

    function verificarMovimientoDeEgreso(cajaChica) {
        // if (!cajaChica.concepto) {
        //     return false;
        // }
        if (!cajaChica.descripcion) {
            return false;
        }
        if (!cajaChica.cantidad) {
            return false;
        }
        return true;
    };

    $scope.agregarMovimiento = function () {
        if ($scope.cajaChicaEncabezado.tipo == 0) {
            // Ingreso
            verificarImporte();
            // $scope.cajaChica.idMovimiento = '2';
            if (!verificarMovimientoDeIngreso($scope.cajaChica)) {
                growl.error('Revisa todos los campos');
                return;
            }
        } else if ($scope.cajaChicaEncabezado.tipo == 1) {
            // Egreso
            if (!verificarMovimientoDeEgreso($scope.cajaChica)) {
                growl.error('Revisa todos los campos');
                return;
            }
        }

        if ($scope.cajaChica.seleccionMovimiento) {
            $scope.cajaChica.idMovimiento = $scope.cajaChica.seleccionMovimiento.idCuentaConcepto;
            $scope.cajaChica.movimiento = $scope.cajaChica.seleccionMovimiento.cuenta;
        } else {
            $scope.cajaChica.idMovimiento = null;
            $scope.cajaChica.movimiento = '';
        }

        if ($scope.cajaChica.seleccionSubcuenta) {
            $scope.cajaChica.idConcepto = $scope.cajaChica.seleccionSubcuenta.idSubcuenta;
            $scope.cajaChica.concepto = $scope.cajaChica.seleccionSubcuenta.subcuenta;
        } else {
            $scope.cajaChica.idConcepto = null;
            $scope.cajaChica.concepto = '';
        }


        if ($scope.cajaChica.seleccionSubSubcuenta) {
            $scope.cajaChica.idSubConcepto = $scope.cajaChica.seleccionSubSubcuenta.idSubSubcuenta;
            $scope.cajaChica.subconcepto = $scope.cajaChica.seleccionSubSubcuenta.subSubcuenta;
        } else {
            $scope.cajaChica.idSubconcepto = null;
            $scope.cajaChica.subconcepto = '';
        }

        delete $scope.cajaChica.seleccionMovimiento;
        delete $scope.cajaChica.seleccionSubcuenta;
        delete $scope.cajaChica.seleccionSubSubcuenta;

        $scope.datosCajaChica.push($scope.cajaChica);
        $scope.cajaChica = {};
        growl.success("Nuevo movimiento agregado");
    };

    $scope.calcularImporte = function (editando) {
        if (editando) {
            if (!isNaN(parseFloat($scope.editar_detalleCajaChica.kg)) && !isNaN(parseFloat($scope.editar_detalleCajaChica.precio))) {
                $scope.editar_detalleCajaChica.importe = parseFloat($scope.editar_detalleCajaChica.kg) * parseFloat($scope.editar_detalleCajaChica.precio);
            } else {
                $scope.editar_detalleCajaChica.importe = 0;
            }
        } else {
            if (!isNaN(parseFloat($scope.cajaChica.kg)) && !isNaN(parseFloat($scope.cajaChica.precio))) {
                $scope.cajaChica.importe = parseFloat($scope.cajaChica.kg) * parseFloat($scope.cajaChica.precio);
            } else {
                $scope.cajaChica.importe = 0;
            }
        }
    };

    $scope.$watch('[cajaChica, cajaChicaEncabezado]', function () {
        if (angular.equals($scope.cajaChica, {}) && $scope.datosCajaChica.length == 0 || $scope.cajaChicaEncabezado.idCajaChica) {
            $rootScope.lockTemplate = false;
        } else {
            $rootScope.lockTemplate = true;
        }
    }, true);

    $scope.$watch('cajaChica.idMovimiento', function () {
        switch ($scope.cajaChica.idMovimiento) {
            case '2':
                $scope.esDiversos = false;
                break;
            default:
                $scope.esDiversos = true;
                break;
        }
    });

    $scope.pdfCmprobante = function () {
        /* window.open('reportes/cajaChica/pdfMovimientoCajaChica.php?idCajaChica=' + $scope.idCajaChica, '_blank'); */
        // window.open('localhost:8000/comprobante-caja-chica/pdf/' + $scope.idCajaChica, '_blank');
        //window.open('https://formatos.apicultoresunidos.com/comprobante-caja-chica/pdf/' + $scope.idCajaChica, '_blank');

        $http.get('../extras/getDatabase.php').then(function(response) {
            let database = response.data;
            window.open('https://formatos.apicultoresunidos.com/comprobante-caja-chica/pdf/' + $scope.idCajaChica + '/' + database, '_blank');

            // window.open('https://formatos.apicultoresunidos.com/comprobante-caja-chica/pdf/' + $scope.idCajaChica + '/' + '38', '_blank');
        });
    }


    //Crear nuevos conceptos
    $scope.nuevosConceptos = function () {
        $http.get('controlAdministrativo/php/listaConceptosGrandes.php').success(function (data) {
            $scope.conceptosGrandes = data;
        });
        $("#modalParaConceptos").modal();
    };

    $scope.guardarNuevoConcepto = function (nuevoConcepto) {
        $http.post("controlAdministrativo/php/guardarConcepto.php", nuevoConcepto).success(function (respuesta) {
            $("#modalParaConceptos").modal('hide');
            swal("Éxito!", "Registro guardado", "success");
            $http.get('controlAdministrativo/php/comboSubcuentas.php').success(function (data) {
                $scope.listaDeConceptos = data;
                $scope.nuevoConcepto = "";
            });
        });
    };

    $scope.nuevoConceptoCajaChica = function () {
        $scope.v_nuevoConceptoCajaChica = {
            concepto: '',
            subconceptos: []
        };
        $("#modalNuevoConceptoCajaChica").modal();
    };
    $scope.guardarNuevoConceptoCajaChica = function (v_nuevoConceptoCajaChica) {
        $http.post('controlAdministrativo/php/nuevoConceptoCajaChica.php?opcion=concepto', v_nuevoConceptoCajaChica).success(function (data) {
            if (data.error) {
                growl.error(data.mensaje);
            } else {
                $scope.traerConceptosYSubconceptos();
                growl.success(data.mensaje);
                $("#modalNuevoConceptoCajaChica").modal('hide');
            }
        });
    };

    $scope.f_nuevoSubconceptoCajaChica = function () {
        $scope.v_nuevoConceptoCajaChica.subconceptos.push({});
    };


    //Altas rápidas para proveedores, apicultores y persona
    $scope.altaRapidaDesdeCajaChica = function () {
        $http.get('personalOaxacaMiel/php/listaAreas.php').success(function (datas) {
            $scope.listaAreas = datas;
        });

        $http.get('personalOaxacaMiel/php/listaPuestos.php').success(function (datos) {
            $scope.listaPuestos = datos;
        });
        $("#modalDeProveedor").modal();

    };

    $scope.guardarNuevoProveedorB = function (altas) {
        switch (altas.proveedorTipo) {
            case '1':
                if (verificarDatosNuevoProveedor(altas)) {
                    $http.post("almacen/php/guardarProv.php", { valor: altas })
                        .success(function (respuesta) {
                            swal("Éxito!", "Registro agregado", "success");
                            traerListaPersonas(altas.proveedorTipo);
                            $scope.altas = {};
                            $("#modalDeProveedor").modal('hide');
                            // $http.get('proveedores/php/listaProveedores.php?clientes').success(function (data) {
                            //     $scope.clientes = data;
                            //     $scope.altas = {};
                            //     $("#modalDeProveedor").modal('hide');
                            // })
                        });
                }
                break;
            case '3':
                $http.post("controlMantenimiento/php/guardarTecnico.php", altas).success(function (info) {
                    swal("Éxito!", "Registro agregado", "success");
                    traerListaPersonas(altas.proveedorTipo);
                    $scope.altas = {};
                    $("#modalDeProveedor").modal('hide');
                    // $http.get('controlMantenimiento/php/listaDeTecnicos.php').success(function (data) {
                    //     $scope.lstTenicosP = data;
                    //     $scope.altas = {};
                    //     $("#modalDeProveedor").modal('hide');
                    // });
                });
                break;
            case '4':
                $http.post("personalOaxacaMiel/php/guardarPersonalOM.php?alta_rapida", altas).success(function (info) {
                    if (info.hasOwnProperty('error')) {
                        if (info.error) {
                            swal('Error', info.message, 'error');
                        } else {
                            // $("#modalDeProveedor").modal('hide');
                            swal('Éxito', info.message, 'success');
                            traerListaPersonas(altas.proveedorTipo);
                            $scope.altas = {};
                            $("#modalDeProveedor").modal('hide');
                            // $http.get('controlMantenimiento/php/listaPersonalOM.php').success(function (data) {
                            //     $scope.lstOMPersonal = data;
                            //     $scope.altas = {};
                            // });
                        }
                    } else {
                        growl.error('Error');
                        console.error(info);
                    }
                });
                break;
            case '6':
                $http.post("controlAdministrativo/php/guardarCliente.php", altas).success(function (info) { //Guardar cliente con alta rápida
                    swal("", info.message, info.swal);
                    if (!info.error) {
                        traerListaPersonas(altas.proveedorTipo);
                        $scope.altas = {};
                        $("#modalDeProveedor").modal('hide');
                        // $scope.altas = {};
                        // $http.post('controlAdministrativo/php/traeCatalogoClientes.php', true).success(function (data) {
                        //     $scope.catalogoTablaClientes = data.data;
                        //     $("#modalDeProveedor").modal('hide');
                        // });
                    }
                });
                break;
            case '8':
                $http.post("catalogos/controlAdministrativo/php/guardarPropios.php", altas).success(function (data) {

                    if (data.hasOwnProperty('error')) {
                        if (data.error) {
                            swal('Error', data.message, 'error');
                        } else {
                            swal('Éxito', data.message, 'success');
                            traerListaPersonas(altas.proveedorTipo);
                            $scope.altas = {};
                            $("#modalDeProveedor").modal('hide');
                        }
                    } else {
                        growl.error('Error');
                        console.error(data);
                    }
                });
                break;
            case '9':
                $http.post("catalogos/controlAdministrativo/php/guardarAcreedores.php", altas).success(function (data) {
                    if (data.hasOwnProperty('error')) {
                        if (data.error) {
                            swal('Error', data.message, 'error');
                        } else {
                            swal('Éxito', data.message, 'success');
                            traerListaPersonas(altas.proveedorTipo);
                            $scope.altas = {};
                            $("#modalDeProveedor").modal('hide');
                        }
                    } else {
                        growl.error('Error');
                        console.error(data);
                    }
                });
                break;
            default:
                break;
        }
    };

    /**
     * Editar movimiento de caja chica
     */

    $scope.$watch('editar_EncabezadoCajaChica.tipoDeCliente', function (val) {
        $http.post('controlAdministrativo/polizaCheque/php/traerNombres.php', val).success(function (data) {
            $scope.personas = data;
        });
    });

    $scope.editarMovimiento = function () {
        // traerNombreCajaChica();
        $scope.editar_EncabezadoCajaChica = Object.assign({}, $scope.cajaChicaEncabezado);
        $http.post('controlAdministrativo/polizaCheque/php/traerNombres.php', $scope.editar_EncabezadoCajaChica.tipoDeCliente).success(function (data) {
            $scope.personas = data;
        });
        $('#modalEditarMovimientoCajaChica').modal();
    }

    // Guardar la edición del encabezado
    $scope.guardarEdicionEncabezadoCajaChica = function () {
        if (angular.equals($scope.cajaChicaEncabezado, $scope.editar_EncabezadoCajaChica)) {
            growl.warning('No se detectó algún cambio');
        } else {
            if ($scope.editar_EncabezadoCajaChica.fecha && $scope.editar_EncabezadoCajaChica.hora && $scope.editar_EncabezadoCajaChica.tipoDeCliente) {
                $http.post('controlAdministrativo/php/updateEncabezadoCajaChica.php?opcion=encabezado', $scope.editar_EncabezadoCajaChica).success(function (response) {
                    if (!response.error) {
                        $scope.cajaChicaEncabezado = response.cajaChicaEncabezado.encabezado;
                        growl.success(response.message);
                        $('#modalEditarMovimientoCajaChica').modal('hide');
                        $scope.editar_EncabezadoCajaChica = {};
                    } else {
                        growl.error(response.message);
                    }
                });
            } else {
                growl.warning('Revisa que los campos estén llenos.');
            }
        }
    };

    // Editar detalle de un movimiento

    $scope.editarDetalleDelMovimiento = function (index) {
        $scope.cargandoModalEdicion = true;
        $scope.editar_detalleCajaChica = null;
        $scope.indexDetalleEditando = index;
        var objectoRegistro = Object.assign({}, $scope.datosCajaChica[index]);

        traerListaCuentas($scope.cajaChicaEncabezado.tipo);
        traerListaSubcuentas(objectoRegistro.idMovimiento);
        $scope.obtenerSubsubcuentas(objectoRegistro.idConcepto);

        setTimeout(() => {
            $scope.editar_detalleCajaChica = objectoRegistro;
            $scope.cargandoModalEdicion = false;
            $scope.$apply();
            $('#modalEditarDetalleCajaChica').modal();
        }, 300);
    };

    // Guardar la edición del detalle
    // function modificarMovimiento(detalle) {
    //     switch (detalle.idMovimiento) {
    //         case '2':
    //             detalle.movimiento = 'Compras';
    //             var _concepto = $scope.lstConceptosIngreso.filter(function (c) {
    //                 if (c.idConceptoCC == detalle.idConcepto) {
    //                     return c;
    //                 }
    //             });
    //             detalle.concepto = _concepto[0].concepto;
    //             if ($scope.lstSubconceptosCC.length > 0) {
    //                 var _subconcepto = $scope.lstSubconceptosCC.filter(function (subconcepto) {
    //                     if (subconcepto.idSubconceptoCC == detalle.idSubconcepto) {
    //                         return subconcepto;
    //                     }
    //                 })
    //                 if (_subconcepto.length > 0) {
    //                     detalle.concepto += ' ( ' + _subconcepto[0].subconceptoCC + ' )';
    //                 }
    //             }
    //             break;
    //         case '3':
    //             detalle.movimiento = 'Diversos';
    //             detalle.idSubconcepto = 0;
    //             var _concepto = $scope.listaDeConceptos.filter(function (subcuenta) {
    //                 if (subcuenta.idSubcuenta == detalle.idConcepto) {
    //                     return subcuenta;
    //                 }
    //             });

    //             if (_concepto.length > 0) {
    //                 detalle.concepto = _concepto[0].subcuenta;
    //             }
    //             break;
    //     }
    //     return detalle;
    // };

    $scope.guardarEdicionDetalleCajaChica = function () {

        if (angular.equals($scope.datosCajaChica[$scope.indexDetalleEditando], $scope.editar_detalleCajaChica)) {
            growl.warning('No hay cambios');
        } else {
            //    $scope.enviarDetalle = modificarMovimiento($scope.editar_detalleCajaChica);
            $http.post('controlAdministrativo/php/updateEncabezadoCajaChica.php?opcion=editarDetalle', {
                idCajaChica: $scope.idCajaChica,
                detalleCC: $scope.editar_detalleCajaChica,
                tipo: $scope.cajaChicaEncabezado.tipo
            }).success(function (response) {
                if (!response.error) {
                    growl.success(response.message);

                    //Llamar al detalle para que se actualize

                    $http.post('controlAdministrativo/php/detalleMovimiento.php', { idCajaChica: $scope.idCajaChica }).success(function (data) {
                        $scope.cajaChicaEncabezado = data.encabezado;
                        $scope.datosCajaChica = data.detalle;
                    });

                    $('#modalEditarDetalleCajaChica').modal('hide');
                    $scope.cajaChicaEncabezado.total = response.total;
                } else {
                    growl.error(response.message);
                }
            });
        }
    };

    // Eliminar detalle de un movimiento

    $scope.eliminarDetalleDelMovimiento = function (idDetalle, idMovimiento, index) {
        swal({
            title: '',
            text: 'Eliminar concepto',
            showCancelButton: true,
            cancelButtonText: 'Cancelar',
            confirmButtonText: 'Eliminar',
            closeOnConfirm: true
        }, function (confirm) {
            if (confirm) {
                $http.post('controlAdministrativo/php/updateEncabezadoCajaChica.php?opcion=borrarDetalle', { idDetalle: idDetalle, idMovimiento: idMovimiento, idCajaChica: $scope.idCajaChica }).success(function (response) {
                    if (!response.error) {
                        $scope.datosCajaChica.splice(index, 1);
                        growl.success(response.message);
                        $scope.cajaChicaEncabezado.total = response.total;
                    } else {
                        growl.error(response.message);
                    }
                });
            }
        });
    };

    // Eliminar movimiento de caja chica

    $scope.eliminarMovimientoCajaChica = function (index, idCajaChica) {
        swal({
            title: '',
            text: 'Eliminar el movimiento y sus conceptos',
            showCancelButton: true,
            cancelButtonText: 'Cancelar',
            confirmButtonText: 'Aceptar',
            closeOnConfirm: false
        }, function (confirm) {
            if (confirm) {
                $http.post('controlAdministrativo/php/eliminarMovimientoCajaChica.php', idCajaChica).success(function (response) {
                    swal('', response.message, response.swal);
                    if (!response.error) {
                        $http.get('controlAdministrativo/php/detalleCajaChica.php?idMes=' + $scope.paramCaja).success(function (datas) {
                            $scope.datosCajaChica = datas.datos;
                            $scope.saldoCajaChica = datas.encabezado.totalCajaChica;
                            $scope.totalPendientes = datas.encabezado.pendientes;
                            $scope.saldoActualCajaChica = datas.encabezado.saldoActualCajaChica;
                        });
                    }
                    //  else {
                    console.info(response);
                    // }
                })
            }
        });
    };

    // Cuando seleccione un subproducto, traer el precio de los catálogos
    $scope.mostrarDatosUnidad = function () {
        $scope.cajaChica.precio = 0;
        if ($scope.cajaChica.seleccionSubSubcuenta) {
            if (isNaN($scope.cajaChica.seleccionSubSubcuenta.precioUnitario) || $scope.cajaChica.seleccionSubSubcuenta.precioUnitario == null) {
                $scope.cajaChica.precio = 0;
            } else {
                $scope.cajaChica.precio = parseFloat($scope.cajaChica.seleccionSubSubcuenta.precioUnitario);
            }
        }
    }
}]);
