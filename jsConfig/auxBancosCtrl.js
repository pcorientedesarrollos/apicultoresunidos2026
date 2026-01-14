form.controller('auxBancosCtrl', ['$scope', '$http', '$routeParams', 'growl', '$rootScope', function ($scope, $http, $routeParams, growl, $rootScope) {

    //========================= P A R A M E T R O S===========================
    $scope.idParam = $routeParams.idCuenta;
    $scope.paramBanco = $routeParams.idBanco;
    $scope.idMesBanco = $routeParams.idMesBanco;
    $scope.nvoAuxBancoIdMes = $routeParams.nvoAuxBancoIdMes;
    // $scope.auxiliarId = $routeParams.idAuxiliar;
    //-----------------------------------------------------------------------

    $rootScope.lockTemplate = false;

    $scope.menuBancos = new Array();
    $scope.arregloCuentas = new Array();
    $scope.menuAuxiliarDeBancos = new Array();
    $scope.auxiliar = new BancosAuxiliar;
    $scope.auxiliar2 = {};
    $scope.auxiliar.concepto = "";
    $scope.ultimoSaldo = 0;
    $scope.saldoRespaldo = 0;
    $scope.auxiliarDeBancos = new Array();
    $scope.arrayMovimientos = new Array();
    $scope.banco = {};
    $scope.banco.idBanco = "";
    $scope.banco.banco = "";
    $scope.cuentaNo = {};
    $scope.cuentaNo.idCuenta = "";
    $scope.cuentaNo.numDeCuenta = "";
    $scope.listaDeCuentas = {};
    $scope.listaDeConceptos = {};
    $scope.listaDeCompras = [];
    $scope.conceptos = {};
    $scope.conceptos.idSubcuenta = "";
    $scope.conceptos.subcuenta = "";
    $scope.bancoTrans = {};
    $scope.bancoTrans.idBanco = "";
    $scope.bancoTrans.banco = "";
    $scope.numeroCuenta = {};
    $scope.numeroCuenta.idCuenta = "";
    $scope.numeroCuenta.numDeCuenta = "";
    $scope.saldoDeposito = 0;
    $scope.saldoRespaldoDeposito = 0;
    $scope.subcuenta = {};
    $scope.subcuenta.idSubcuenta = "";
    $scope.subcuenta.subcuenta = "";
    $scope.nombresLista = {};
    $scope.bdCajaChica = 0;
    $scope.saldoCajaChica = 0;
    $scope.conceptosGrandes = {};
    $scope.bloquearGuardar = false;


    $scope.listaCuentasSeleccionadas = [];
    $scope.listaSubcuentasSeleccionadas = [];

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

    function obtenerListaMovimientosAuxiliar() {
        $http.get('controlAdministrativo/php/detalleAuxiliarDeBancos.php?idCuenta=' + $scope.idParam + '&idMes=' + $scope.nvoAuxBancoIdMes).success(function (datas) {
            $scope.baco = datas.banco;
            $scope.noCuenta = datas.numDeCuenta;
            $scope.bonos = datas.bonos;
            $scope.salidas = datas.salidas;
            $scope.saldoActual = datas.saldoActual;
            $scope.saldoInicial = datas.saldoInicial;
            $scope.auxiliarDeBancos = datas.auxiliarDeBancos;
        });
    }


    // Función que se ejecuta cuando se selecciona la fecha en un nuevo movimiento de bancos
    // Va a verificar si se puede hacer un movimiento en el mes que se seleccionó

    $scope.verificarSiPuedeHacerMovimiento = function () {
        var mesSeleccionado = $scope.auxiliar.fecha.split('-')[1];
        $http.post('controlAdministrativo/php/verificarMesAuxiliarBancos.php', mesSeleccionado).success(function (data) {

            // Solo si el resultado de 1, puede continuar
            // Si es 0 o 2, debe llevarlo a la lista de bancos, para cerrar el mes o establecer saldo inicial

            if (typeof (data) == 'object' && data.hasOwnProperty('error')) {
                if (data.error) {
                    swal('Error', data.message, 'error');
                } else {
                    if (data.resultado >= 0) {
                        if (data.resultado != 1) {
                            window.location.href = '#/menuAuxBancos';
                            swal('', 'Cierra el mes anterior o establezca el saldo inicial para hacer los movimientos');
                        }
                    }
                }
            } else {
                growl.error('Error');
                console.error(data);
            }
        });

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

    $scope.cambioTipoIngresoEgreso = function () {
        // $scope.valor;
        if ($scope.auxiliar.ingresoEgreso) {
            $scope.valor = $scope.auxiliar.ingresoEgreso;
        } else if ($scope.poliza.ingresoEgreso) {
            $scope.valor = $scope.poliza.ingresoEgreso;
        }
        // Traer las cuentas según el tipo de movimiento (ingreso o egreso)
        // if ($scope.auxiliar.ingresoEgreso) {
        traerListaCuentas($scope.valor);
        // }
        $scope.calcularElSaldo();
    }


    $scope.cambioSeleccionCuenta = function (idCuenta = false) {
        // Cuando cambia la cuenta seleccionada, 
        // trae las subcuentas
        if (!idCuenta) {
            // si no manda la cuenta, la toma del scope, primero la verifica
            if ($scope.auxiliar.tipoMovimiento && $scope.auxiliar.tipoMovimiento.idCuentaConcepto) {
                traerListaSubcuentas($scope.auxiliar.tipoMovimiento.idCuentaConcepto);
            } else if ($scope.poliza.tipoMovimiento && $scope.poliza.tipoMovimiento.idCuentaConcepto) {
                traerListaSubcuentas($scope.poliza.tipoMovimiento.idCuentaConcepto);
            }
        } else {
            // Si envia la cuenta, lo hace directo
            traerListaSubcuentas(idCuenta);
        }
    }


    //========================= B A N C O S ===========================

    if ($scope.paramBanco > 0) {
        $http.get('controlAdministrativo/php/traeBanco.php?idBanco=' + $scope.paramBanco).success(function (data) {
            $scope.banco = data;
            $scope.arregloCuentas = data.arregloCuentas;
        });
    } else {
        $http.get('controlAdministrativo/php/menuBancos.php').success(function (datas) {
            $scope.menuBancos = datas;
        });
        $http.get('controlAdministrativo/php/traeProveedores.php').success(function (datas) {
            $scope.nombresLista = datas;
        });
    }

    $scope.nuevasCuentas = function () {
        $scope.cuenta = {};
        $scope.cuenta.idCuenta = 0;
        $scope.cuenta.numDeCuenta = "";
        $scope.arregloCuentas.push($scope.cuenta);
    };

    $scope.guardarBanco = function () {
        $scope.datosBanco = new Array();
        $scope.datosBanco.push($scope.banco);
        $scope.datosBanco.push($scope.arregloCuentas);
        if ($scope.paramBanco == 0) {
            $http.post("controlAdministrativo/php/guardarBanco.php", { valor: $scope.datosBanco }).success(function (Response) {
                if (typeof (Response) == 'object' && Response.hasOwnProperty('error')) {
                    if (Response.error) {
                        swal('', Response.message, 'error');
                    } else {
                        swal("Éxito!", Response.message, "success");
                        return window.location.href = "#/menuBancos";
                    }
                } else {
                    growl.error('Error');
                    console.error(resultado);
                }
            });
        } else {
            $http.post('controlAdministrativo/php/editarBanco.php', { valor: $scope.datosBanco }).success(function (Response) {
                if (typeof (Response) == 'object' && Response.hasOwnProperty('error')) {
                    if (Response.error) {
                        swal('', Response.message, 'error');
                    } else {
                        swal("Éxito!", Response.message, "success");
                        return window.location.href = "#/menuBancos";
                    }
                } else {
                    growl.error('Error');
                    console.error(resultado);
                }
            });
        }
    };

    $scope.eliminarCuenta = function (indice) {
        $scope.arregloCuentas.splice(indice, 1);
    };
    /////////////////////////// T E R M I N A  B A N C O S //////////////////////////////

    //========================= A U X I L I A R  D E  B A N C O S ===========================

    var date = new Date();
    var _mes = date.getMonth() + 1;
    var _fecha = date.getFullYear() + '-' + _mes + '-' + date.getDate();

    if ($scope.idParam > 0) {
        $http.get('controlAdministrativo/php/listaDeBancos.php').success(function (datas) {
            $scope.listaDeBancos = datas;
        });
        $http.get('controlAdministrativo/php/comboSubcuentas.php').success(function (data) {
            $scope.listaDeConceptos = data;
        });
        // $http.get('controlAdministrativo/php/conceptosIngreso.php').success(function (data) {
        //     $scope.listaDeCompras = data;
        // });
        obtenerListaMovimientosAuxiliar();
    } else if ($scope.idMesBanco) {
        function confirmarCierreDeMesBancos() {
            $http.post('controlAdministrativo/php/confirmarCierreMesBancos.php', $scope.idMesBanco).success(function (data) {
                $scope.bloquearBtnCerrarMes = data.block;
            });
        };
        confirmarCierreDeMesBancos();
        $http.post('controlAdministrativo/php/menuBancosYCuentas.php?traerCuentasDelmes', { idMes: $scope.idMesBanco }).success(function (datas) {
            $scope.menuAuxiliarDeBancos = datas;
            $scope.totalIniciales = 0;
            $scope.totalIngresos = 0;
            $scope.totalEgresos = 0;
            $scope.totalSaldos = 0;
            angular.forEach($scope.menuAuxiliarDeBancos, function (value) {
                $scope.totalIniciales += parseFloat(value.saldoInicial);
            });
            angular.forEach($scope.menuAuxiliarDeBancos, function (value) {
                $scope.totalIngresos += parseFloat(value.saldoIngresos);
            });
            angular.forEach($scope.menuAuxiliarDeBancos, function (value) {
                if (value.saldoEgresos == null) {
                    value.saldoEgresos = 0;
                }
                $scope.totalEgresos += parseFloat(value.saldoEgresos);
            });
            angular.forEach($scope.menuAuxiliarDeBancos, function (value) {
                $scope.totalSaldos += parseFloat(value.saldoActual);
            });
        });
    } else {
        if ($scope.idParam == 0) {
            $scope.respaldoAuxiliar = $scope.auxiliar;
            $scope.$watch('auxiliar', function () {
                if (angular.equals($scope.auxiliar, $scope.respaldoAuxiliar)) {
                    $rootScope.lockTemplate = false;
                } else {
                    $rootScope.lockTemplate = true;
                }
            }, true);
        }
        $http.get('controlAdministrativo/php/comboSubcuentas.php').success(function (data) {
            $scope.listaDeConceptos = data;
        });
        $http.get('controlAdministrativo/php/listaDeBancos.php').success(function (datas) {
            $scope.listaDeBancos = datas;
        });
        $http.get('controlAdministrativo/php/listaDeMovimientos.php').success(function (datas) {
            $scope.tiposDeMovimientos = datas;
        });
        $http.get('controlAdministrativo/php/traeMesesBancos.php?meses').success(function (data) {
            $scope.mesesBancos = data;
            angular.forEach($scope.mesesBancos, function (value) {
                if (value.egresosMes == null) {
                    value.egresosMes = 0;
                }
                if (value.ingresosMes == null) {
                    value.ingresosMes = 0;
                }
                value.totalMes = 0;
                value.totalMes = parseFloat(value.ingresosMes) - parseFloat(value.egresosMes);
            });
        });
        // $http.get('controlAdministrativo/php/conceptosIngreso.php').success(function (data) {
        //     $scope.listaDeCompras = data;
        // });
        $http.get('controlAdministrativo/php/listaConceptosGrandes.php').success(function (data) {
            $scope.conceptosGrandes = data;
        });
        $http.get('proveedores/php/listaProveedores.php?clientes').success(function (data) {
            $scope.clientes = data;
        })
        $http.get('controlMantenimiento/php/listaDeTecnicos.php').success(function (data) {
            $scope.lstTenicosP = data;
        })
        $http.get('controlMantenimiento/php/listaPersonalOM.php').success(function (data) {
            $scope.lstOMPersonal = data;
        })
        $http.get('personalOaxacaMiel/php/listaAreas.php').success(function (datas) {
            $scope.listaAreas = datas;
        });

        $http.get('personalOaxacaMiel/php/listaPuestos.php').success(function (datos) {
            $scope.listaPuestos = datos;
        });
        $http.post('controlAdministrativo/php/traeCatalogoClientes.php', true).success(function (data) {
            $scope.catalogoTablaClientes = data.data;
        });
    }

    $scope.eliminarMovimiento = function (auxiliar) {
        swal({
            title: '',
            text: '¿Deseas eliminar este movimiento?',
            showCancelButton: true,
            confirmButtonText: 'Si',
            cancelButtonText: 'No',
            closeOnConfirm: true
        }, function (confirm) {
            if (confirm) {
                if (auxiliar.movimiento == 'ENTRE CUENTAS' && auxiliar.ingresoEgreso == '0') {
                    growl.info('Debe eliminar desde el registro de origen');
                } else {
                    $http.post('controlAdministrativo/php/eliminarMovimientoAuxiliar.php', auxiliar).success(function (respuesta) {
                        if (typeof (respuesta) == 'object' && respuesta.hasOwnProperty('error')) {
                            if (respuesta.error) {
                                swal('', respuesta.message || 'No se ha podido guardar los cambios', 'info');
                            } else {
                                swal("Éxito!", respuesta.message, "success");
                                obtenerListaMovimientosAuxiliar();
                            }
                        } else {
                            growl.error('Error');
                            console.error(respuesta);
                        }
                    });
                }
                // $http.post('controlAdministrativo/php/eliminarMovimientoAuxiliar.php', { idAuxiliar: idAuxiliar, tipoMovimiento: tipoMovimiento }).success(function (data) {
                //     obtenerListaMovimientosAuxiliar();
                //     swal('Éxito', 'Se eliminó correctamente', 'success');
                // });
            }
        });
    };

    // $scope.$watch('auxiliar.tipoMovimiento', function () {
    //     switch ($scope.auxiliar.tipoMovimiento) {
    //         case '1':
    //             $scope.conceptos = '';
    //             $scope.auxiliar.concepto = '';
    //             $scope.mostrarListaDeConceptos = false;
    //             $scope.mostrarListaDeCompras = false;
    //             break;
    //         case '2':
    //             $scope.mostrarListaDeCompras = true;
    //             $scope.mostrarListaDeConceptos = false;
    //             break;
    //         case '3':
    //             $scope.mostrarListaDeConceptos = true;
    //             $scope.mostrarListaDeCompras = false;
    //             break;
    //         case '4':
    //             $scope.conceptos = 'Entre cuentas';
    //             $scope.auxiliar.concepto = 'Entre cuentas';
    //             break;
    //         case '5':
    //             $scope.conceptos = "Depositos RSS Otros";
    //             $scope.auxiliar.concepto = "Depositos RSS Otros";
    //             break;
    //         case '6':
    //             $scope.conceptos = 'Control caja chica';
    //             $scope.auxiliar.concepto = 'Control caja chica';
    //             $http.post('controlAdministrativo/php/ultimoSaldoCajaChica.php', _mes).success(function (data) {
    //                 if (parseFloat(data.elUltimoSaldo) > 0) {
    //                     $scope.bdCajaChica = data.elUltimoSaldo;
    //                     $scope.saldoCajaChica = $scope.bdCajaChica;
    //                 } else {
    //                     $scope.bdCajaChica = 0;
    //                     $scope.saldoCajaChica = $scope.bdCajaChica;
    //                 }
    //             });
    //             break;
    //         default:
    //             $scope.mostrarListaDeCompras = true;
    //             $scope.mostrarListaDeConceptos = true;
    //             $scope.conceptos = "";
    //             break;
    //     };
    // }, true);

    $scope.cerrarMes = function (menuAuxBancos) {

        swal({
            title: "¿Desea cerrar el mes?",
            text: "No deberá hacer más movimientos en este",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#DD6B55",
            cancelButtonText: "Cancelar",
            confirmButtonText: "Sí, cerrar mes",
            closeOnConfirm: false
        },
            function () {
                var _datos = [];
                menuAuxBancos.forEach(function (cuenta) {
                    var _nuevaCuenta = {};
                    _nuevaCuenta.idBanco = cuenta.idBanco;
                    _nuevaCuenta.idCuenta = cuenta.idCuenta;
                    _nuevaCuenta.saldoInicial = cuenta.saldoActual;
                    _datos.push(_nuevaCuenta);
                });
                $http.post('controlAdministrativo/php/cerrarEIniciarMes.php', { datos: _datos, idMes: $scope.idMesBanco }).success(function (data) {
                    if (!data.error) {
                        confirmarCierreDeMesBancos();
                        window.location.href = '#/menuAuxBancos';
                    }
                    swal('', data.message, data.swal);
                });
            });
    };

    $scope.traeNumerosDeCuentas = function (bancoTrans) {
        $http.get('controlAdministrativo/php/infoCuentasAnidadas.php?idBanco=' + bancoTrans + '&idCuenta=' + $scope.cuentaNo)
            .success(function (data) {
                $scope.listaCuentas = data;
            });
        $http.get('controlAdministrativo/php/traeBanco.php?idBanco=' + bancoTrans)
            .success(function (data) {
                $scope.auxiliar.banco = data.banco;
            });

        $rootScope.idBancoTrans = bancoTrans;
    };

    $scope.traeSaldoTrans = function (numeroCuenta) {
        $http.post('controlAdministrativo/php/infoSaldosAnidados.php?idCuenta=' + numeroCuenta, { idMes: _mes, fecha: _fecha, idBanco: $scope.bancoTrans }).success(function (data) {
            if (data.ultimoSaldo == 0) {
                $scope.saldoRespaldoDeposito = data;
                $scope.saldoDeposito = $scope.saldoRespaldoDeposito;
            } else {
                $scope.saldoRespaldoDeposito = data.ultimoSaldo;
                $scope.saldoDeposito = $scope.saldoRespaldoDeposito;
            }
        });
        $http.get('controlAdministrativo/php/traeCuenta.php?idCuenta=' + numeroCuenta)
            .success(function (data) {
                $scope.auxiliar.cuenta = data.numDeCuenta;
            });
        $rootScope.cuentaTrans = numeroCuenta;
    };

    $scope.traeCuentasBancarias = function (banco) {
        $scope.cuentaNo = null;
        $http.get('controlAdministrativo/php/infoCuentasAnidadas.php?idBanco=' + banco)
            .success(function (data) {
                $scope.listaDeCuentas = data;
            });
    };
    $scope.traeSaldos = function (cuentaNo) {

        if (localStorage.getItem('idMesParaMovimiento') === null) {
            var date = new Date();
            var _mes = date.getMonth() + 1;
            var _fecha = date.getFullYear() + '-' + _mes + '-' + date.getDate();
        } else {
            var date = new Date();
            var _mes = localStorage.getItem('idMesParaMovimiento');
            var _fecha = date.getFullYear() + '-' + _mes + '-' + date.getDate();
        }

        $http.post('controlAdministrativo/php/infoSaldosAnidados.php?idCuenta=' + cuentaNo, { idMes: _mes, fecha: _fecha, idBanco: $scope.banco })
            .success(function (data) {
                if (data.ultimoSaldo == 0) {
                    swal({
                        title: "¡Espera!",
                        text: "Por favor ingresa un saldo inicial para la cuenta seleccionada:",
                        type: "input",
                        closeOnConfirm: false,
                        animation: "slide-from-top",
                        inputPlaceholder: "Cantidad"
                    },
                        function (inputValue) {
                            if (inputValue === false || inputValue === '') {
                                swal.showInputError("¡Necesitas llenar el campo!");
                                return false
                            } else {
                                $http.post('controlAdministrativo/php/insertarSaldoInicial.php', { idBanco: $scope.banco, idCuenta: $scope.cuentaNo, saldoInicial: inputValue, fecha: _fecha, idMes: _mes }).success(function (data) {
                                    swal('', data.message, data.swal);
                                    if (!data.error) {

                                        $http.post('controlAdministrativo/php/consultaSaldoIncial.php?idCuenta=' + cuentaNo, { idMes: _mes }).success(function (data) {
                                            $scope.saldoRespaldo = parseFloat(data.saldoInicial);
                                            $scope.ultimoSaldo = $scope.saldoRespaldo;
                                        });
                                    }
                                });
                            }
                        });
                } else {
                    $scope.saldoRespaldo = parseFloat(data.ultimoSaldo);
                    $scope.ultimoSaldo = $scope.saldoRespaldo;
                }
            });
    };

    $scope.$watch('banco', function () {
        // Si llamamo a un nuevo auxiliar, se borra la configuración que se haya puesto
        // $scope.auxiliar = new BancosAuxiliar();
        $scope.ultimoSaldo = "";
    });

    $scope.calcularElSaldo = function () {
        $scope.miRespaldo = parseFloat($scope.saldoRespaldo);
        $scope.miRespaldo3 = parseFloat($scope.bdCajaChica);
        if (typeof $scope.auxiliar.cantidad != "number" || $scope.auxiliar.cantidad == '') {
            $scope.ultimoSaldo = $scope.miRespaldo;
            $scope.saldoCajaChica = $scope.miRespaldo3;
            return;
        }
        // En lugar de hacer el siguiente switch se va a usar el campo IngresoEgreso para sumar o restar la cantidad
        // al saldo actual de la cuenta seleccionada

        if ($scope.auxiliar.ingresoEgreso == '0') {
            // Es ingreso
            $scope.ultimoSaldo = parseFloat($scope.auxiliar.cantidad) + parseFloat($scope.saldoRespaldo);
            $scope.auxiliar.tipo = true;
            $scope.auxiliar.ingreso = $scope.auxiliar.cantidad;
        } else {
            // Es egreso (1)
            $scope.ultimoSaldo = parseFloat($scope.saldoRespaldo) - parseFloat($scope.auxiliar.cantidad);
            $scope.auxiliar.tipo = false;
            $scope.auxiliar.egreso = $scope.auxiliar.cantidad;
        }

        // switch ($scope.auxiliar.tipoMovimiento) {
        //     case "1":
        //         $scope.ultimoSaldo = parseFloat($scope.auxiliar.cantidad) + parseFloat($scope.saldoRespaldo);
        //         $scope.auxiliar.movimiento = 'Depósito';
        //         $scope.auxiliar.tipo = true;
        //         $scope.auxiliar.ingreso = $scope.auxiliar.cantidad;
        //         break;
        //     case "2":
        //         $scope.ultimoSaldo = parseFloat($scope.saldoRespaldo) - parseFloat($scope.auxiliar.cantidad);
        //         $scope.auxiliar.movimiento = 'Compra';
        //         $scope.auxiliar.tipo = false;
        //         $scope.auxiliar.egreso = $scope.auxiliar.cantidad;
        //         break;
        //     case "3":
        //         $scope.ultimoSaldo = parseFloat($scope.saldoRespaldo) - parseFloat($scope.auxiliar.cantidad);
        //         $scope.auxiliar.movimiento = 'Diversos';
        //         $scope.auxiliar.tipo = false;
        //         $scope.auxiliar.egreso = $scope.auxiliar.cantidad;
        //         break;
        //     case "4":
        //         $scope.ultimoSaldo = parseFloat($scope.saldoRespaldo) - parseFloat($scope.auxiliar.cantidad);
        //         $scope.auxiliar.movimiento = 'Entre cuentas';
        //         $scope.auxiliar.tipo = false;
        //         $scope.auxiliar.egreso = $scope.auxiliar.cantidad;
        //         break;
        //     case "5":
        //         $scope.ultimoSaldo = parseFloat($scope.saldoRespaldo) - parseFloat($scope.auxiliar.cantidad);
        //         $scope.auxiliar.movimiento = 'Depósitos RSS Otros';
        //         $scope.auxiliar.tipo = false;
        //         $scope.auxiliar.egreso = $scope.auxiliar.cantidad;
        //         break;
        //     case "6":
        //         $scope.ultimoSaldo = parseFloat($scope.saldoRespaldo) - parseFloat($scope.auxiliar.cantidad);
        //         $scope.saldoCajaChica = parseFloat($scope.bdCajaChica) + parseFloat($scope.auxiliar.cantidad);
        //         $scope.auxiliar.movimiento = 'Control caja chica';
        //         $scope.auxiliar.tipo = false;
        //         $scope.auxiliar.egreso = $scope.auxiliar.cantidad;
        //         break;
        //     case "9":
        //         $scope.ultimoSaldo = parseFloat($scope.auxiliar.cantidad) + parseFloat($scope.saldoRespaldo);
        //         $scope.auxiliar.movimiento = 'Devolución de efectivo';
        //         $scope.auxiliar.tipo = true;
        //         $scope.auxiliar.ingreso = $scope.auxiliar.cantidad;
        //         break;
        // }
    }

    $scope.calcularSaldoDeposito = function () {
        $scope.miRespaldo2 = parseFloat($scope.saldoRespaldoDeposito);
        $scope.miRespaldo = parseFloat($scope.saldoRespaldo);
        if (typeof $scope.auxiliar.cantidadDeposito !== "number" || $scope.auxiliar.cantidadDeposito == '') {
            $scope.saldoDeposito = $scope.miRespaldo2;
            $scope.ultimoSaldo = $scope.miRespaldo;
            return;
        }
        $scope.saldoDeposito = parseFloat($scope.saldoRespaldoDeposito) + parseFloat($scope.auxiliar.cantidadDeposito);
        $scope.ultimoSaldo = parseFloat($scope.saldoRespaldo) - parseFloat($scope.auxiliar.cantidadDeposito);
    };


    // $scope.$watch('auxiliar.concepto', function () {
    //     if ($scope.auxiliar.tipoMovimiento == '3') {
    //         $http.get('controlAdministrativo/php/subcuentas.php?idSubcuenta=' + $scope.auxiliar.concepto).success(function (data) {
    //             $rootScope.concepto = data.subcuenta;
    //         });
    //     }
    //     if ($scope.auxiliar.tipoMovimiento == '2') {
    //         $http.get('controlAdministrativo/php/conceptosCaja.php?idConceptoCC=' + $scope.auxiliar.concepto).success(function (data) {
    //             $rootScope.concepto = data.concepto;
    //         });
    //     }
    // }, true);

    $scope.$watch('auxiliar.nombreDe', function () {
        $http.get("controlAdministrativo/php/traeNombresPorTipo.php?id=" + $scope.auxiliar.nombreDe + "&tipo=" + $scope.auxiliar.tipoDePersona).success(function (respuesta) {
            $rootScope.nombreId = respuesta;
        });
    }, true);

    $scope.$watch('auxiliar.tipoDePersona', function (val) {
        $scope.auxiliar.nombreDe = "";
        traerListaPersonas(val);
    });

    $scope.$watch('banco', function (banco) {
        $http.get('controlAdministrativo/php/traeBanco.php?idBanco=' + banco)
            .success(function (data) {
                $scope.auxiliar2.banco = data.banco;
            });
    }, true);

    $scope.$watch('cuentaNo', function (cuentaNo) {
        $http.get('controlAdministrativo/php/traeCuenta.php?idCuenta=' + cuentaNo)
            .success(function (data) {
                $scope.auxiliar2.cuenta = data.numDeCuenta;
            });
    }, true);

    $scope.$watch('correccionBancos.tipoDePersona', function (val) {
        $http.post('controlAdministrativo/polizaCheque/php/traerNombres.php', val).success(function (data) {
            $scope.personas = data;
        });
    });

    $scope.editarMovimientoBanco = function (idAuxiliar) {
        $("#modalEditarMovimiento").modal();
        $http.get('controlAdministrativo/php/traeMovimientoPorId.php?idAuxiliar=' + idAuxiliar).success(function (data) {
            // Revisar las propiedades de data para saber si debe traer las cuentas y subcuentas

            // Propiedades tipoMovimiento y idSubcuebta deben ser diferente a cero
            // Considerar ingresoEgreso para traer las cuentas

            if (data.tipoMovimiento && data.tipoMovimiento != '' && data.idSubcuenta && data.idSubcuenta != '') {
                traerListaCuentas(data.ingresoEgreso);
                traerListaSubcuentas(data.tipoMovimiento);
                $scope.obtenerSubsubcuentas(data.idSubcuenta);
            }

            if (data.idTransferencia && data.idTransferencia != '') {
                if (data.idBancoTransferencia) {
                    $scope.traeCuentasBancarias(data.idBancoTransferencia);
                }
            }

            setTimeout(() => {
                $scope.correccionBancos = data;
            }, 100);

            $http.post('controlAdministrativo/polizaCheque/php/traerNombres.php', data.tipoDePersona).success(function (data) {
                $scope.personas = data;
            });
        })
    };

    $scope.guardarEdicionMovimiento = function (correccionBancos) {
        correccionBancos.idMes = correccionBancos.fecha.split("-")[1];
        $http.post("controlAdministrativo/php/guardarEdicionAuxiliar.php", correccionBancos).success(function (respuesta) {
            if (typeof (respuesta) == 'object' && respuesta.hasOwnProperty('error')) {
                if (respuesta.error) {
                    swal('', respuesta.message || 'No se ha podido guardar los cambios', 'info');
                } else {
                    swal("Éxito!", respuesta.message, "success");
                    obtenerListaMovimientosAuxiliar();
                    $("#modalEditarMovimiento").modal('hide');
                }
            } else {
                growl.error('Error');
                console.error(respuesta);
            }
        });
    };

    $scope.proveedorDesdeBancos = function () {
        $("#modalDeProveedor").modal();
    };

    function verificarDatosNuevoProveedor(altas) {

        if (!altas.nombre) {
            growl.info('Escriba el nombre del proveedor');
            return false;
        }

        if (!altas.idSagarpa) {
            growl.info('Ingrese el número ID SAGARPA');
            return false;
        }

        if (!altas.telefono) {
            growl.info('Ingrese el número de teléfono');
            return false;
        }

        if (!altas.tipoDeMiel) {
            growl.info('Seleccione un tipo de producto');
            return false;
        }

        return true;
    }

    $scope.guardarNuevoProveedorB = function (altas) {
        switch (altas.proveedorTipo) {
            case '1':
                if (verificarDatosNuevoProveedor(altas)) {
                    $http.post("almacen/php/guardarProv.php", { valor: altas }).success(function (respuesta) {
                        swal("Éxito!", "Registro agregado", "success");
                        traerListaPersonas(altas.proveedorTipo);
                        $scope.altas = {};
                        $("#modalDeProveedor").modal('hide');
                    });
                }

                break;
            case '3':
                $http.post("controlMantenimiento/php/guardarTecnico.php", altas).success(function (info) {
                    swal("Éxito!", "Registro agregado", "success");
                    traerListaPersonas(altas.proveedorTipo);
                    $scope.altas = {};
                    $("#modalDeProveedor").modal('hide');
                });
                break;
            case '4':
                $http.post("personalOaxacaMiel/php/guardarPersonalOM.php?alta_rapida", altas).success(function (info) {
                    if (info.hasOwnProperty('error')) {
                        if (info.error) {
                            swal('Error', info.message, 'error');
                        } else {
                            swal('Éxito', info.message, 'success');
                            traerListaPersonas(altas.proveedorTipo);
                            $scope.altas = {};
                            $("#modalDeProveedor").modal('hide');
                        }
                    } else {
                        growl.error('Error');
                        console.error(info);
                    }
                });
                break;
            case '6':
                $http.post("controlAdministrativo/php/guardarCliente.php", altas).success(function (info) {
                    swal("Éxito!", "Registro agregado", "success");
                    traerListaPersonas(altas.proveedorTipo);
                    $scope.altas = {};
                    $("#modalDeProveedor").modal('hide');
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

    $scope.nuevosConceptos = function () {
        $("#modalParaConceptos").modal();
    };

    $scope.guardarNuevoConcepto = function (nuevoConcepto) {

        $http.post("controlAdministrativo/php/guardarConcepto.php", nuevoConcepto)
            .success(function (respuesta) {
                swal("Éxito!", "Registro guardado", "success");
                $http.get('controlAdministrativo/php/comboSubcuentas.php').success(function (data) {
                    $scope.listaDeConceptos = data;
                    $scope.nuevoConcepto = "";
                    $("#modalParaConceptos").modal('hide');
                });
            });
    };

    $scope.guardarTransferencia = function () {
        $scope.auxiliar2.idTransferencia = 1;
        $scope.auxiliar2.idBanco = $rootScope.idBancoTrans;
        $scope.auxiliar2.idCuenta = $rootScope.cuentaTrans;
        $scope.auxiliar2.fecha = $scope.auxiliar.fecha;
        $scope.auxiliar2.hora = $scope.auxiliar.hora;
        $scope.auxiliar2.referencia = $scope.auxiliar.referencia;
        $scope.auxiliar2.miNombre = $rootScope.nombreId;
        // $scope.auxiliar2.concepto = $scope.auxiliar.concepto;
        $scope.auxiliar2.concepto = 'DEPÓSITO';
        $scope.auxiliar2.nombreDe = $scope.auxiliar.nombreDe;
        $scope.auxiliar2.idSubcuenta = "0";
        $scope.auxiliar2.descripcion = "TRASPASO ENTRE CUENTAS DE" + " " + $scope.auxiliar2.banco + " " + $scope.auxiliar2.cuenta;
        $scope.auxiliar2.movimiento = "ENTRE CUENTAS";
        $scope.auxiliar2.tipoDepositoCompra = "";
        $scope.auxiliar2.tipoDePersona = $scope.auxiliar.tipoDePersona;
        $scope.auxiliar2.cantidad = $scope.auxiliar.cantidad;
        $scope.auxiliar2.saldo = $scope.saldoDeposito;
        $scope.auxiliar2.tipoMovimiento = { idCuentaConcepto: '' };
        $scope.auxiliar2.ingresoEgreso = '0';
        $scope.auxiliar2.ingreso = $scope.auxiliar2.cantidad;
        $scope.auxiliar2.tipo = true;
        $scope.auxiliar2.guardarRelacion = true;
        $scope.auxiliarDeBancos.push($scope.auxiliar2);

    };

    $scope.agregarAuxiliar = function (esEntreCuentas = false) {
        // Poner el valor de la hora
        var horaAuxiliar = document.getElementById("auxiliarInputHora");
        $scope.auxiliar.hora = horaAuxiliar.value;

        if (esEntreCuentas) {
            $scope.auxiliar.idTransferencia = 1;
            $scope.auxiliar.tipoMovimiento = { idCuentaConcepto: '' };
            $scope.auxiliar.movimiento = 'ENTRE CUENTAS';
            $scope.auxiliar.concepto = 'TRANSFERENCIA';

            $scope.auxiliar.cantidad = $scope.auxiliar.cantidadDeposito;
            $scope.auxiliar.ingresoEgreso = '1';
            $scope.auxiliar.egreso = $scope.auxiliar.cantidad;
            $scope.auxiliar.tipo = false;

            $scope.auxiliar.descripcion = "TRASPASO ENTRE CUENTAS A" + " " + $scope.auxiliar.banco + " " + $scope.auxiliar.cuenta;

            $scope.auxiliar.idSubcuenta = "0";
        }

        $scope.okAuxiliar = $scope.validarAuxiliar(esEntreCuentas);


        // if ($scope.auxiliar.tipoMovimiento == '4') {
        //     $scope.auxiliar.cantidad = $scope.auxiliar.cantidadDeposito;
        //     $scope.auxiliar.ingresoEgreso = '1';
        //     $scope.auxiliar.egreso = $scope.auxiliar.cantidad;
        //     $scope.auxiliar.tipo = false;

        //     $scope.auxiliar.descripcion = "TRASPASO ENTRE CUENTAS A" + " " + $scope.auxiliar.banco + " " + $scope.auxiliar.cuenta;
        //     $scope.guardarTransferencia();
        //     $scope.auxiliar.idSubcuenta = "0";
        // } else {
        //     $scope.auxiliar.idSubcuenta = $scope.auxiliar.concepto;
        // }

        // if ($scope.auxiliar.tipoMovimiento == 6) {
        //     $scope.auxiliar.saldoCajaChica = $scope.saldoCajaChica;

        // } else {
        //     $scope.auxiliar.saldoCajaChica = 0;
        // }

        if ($scope.okAuxiliar == true) {

            if ($scope.idParam == 0 || esEntreCuentas) {

                if (!esEntreCuentas) {
                    $scope.auxiliar.movimiento = $scope.auxiliar.tipoMovimiento.cuenta;
                    $scope.auxiliar.idSubcuenta = $scope.auxiliar.concepto.idSubcuenta;
                    $scope.auxiliar.concepto = $scope.auxiliar.concepto.subcuenta;
                    $scope.auxiliar.idTransferencia = 0;
                }


                // switch ($scope.auxiliar.tipoMovimiento) {
                //     case "1":
                //         $scope.auxiliar.movimiento = "DEPÓSITO";
                //         $scope.auxiliar.idSubcuenta = '0';
                //         $scope.auxiliar.ingresoEgreso = '0';
                //         break;
                //     case "2":
                //         $scope.auxiliar.concepto = $rootScope.concepto;
                //         $scope.auxiliar.movimiento = "COMPRA";
                //         $scope.auxiliar.ingresoEgreso = '1'
                //         break;
                //     case "3":
                //         $scope.auxiliar.concepto = $rootScope.concepto;
                //         $scope.auxiliar.movimiento = "DIVERSOS";
                //         $scope.auxiliar.ingresoEgreso = '1'
                //         break;
                //     case "4":
                //         $scope.auxiliar.movimiento = "ENTRE CUENTAS";
                //         $scope.auxiliar.ingresoEgreso = '1'
                //         break;
                //     case "5":
                //         $scope.auxiliar.movimiento = "DEPÓSITOS RSS OTROS";
                //         $scope.auxiliar.ingresoEgreso = '1'
                //         break;
                //     case "6":
                //         $scope.auxiliar.movimiento = "CONTROL CAJA CHICA";
                //         $scope.auxiliar.ingresoEgreso = '1'
                //         break;
                //     case "9":
                //         $scope.auxiliar.movimiento = "DEVOLUCIÓN DE EFECTIVO";
                //         $scope.auxiliar.idSubcuenta = '0';
                //         $scope.auxiliar.ingresoEgreso = '0';
                //         break;
                // }

                $scope.auxiliar.idBanco = $scope.banco;
                $scope.auxiliar.idCuenta = $scope.cuentaNo;
                $scope.auxiliar.miNombre = $rootScope.nombreId;
                $scope.auxiliar.saldo = $scope.ultimoSaldo;
                $scope.auxiliarDeBancos.push($scope.auxiliar);
                $scope.saldoRespaldo = parseFloat($scope.auxiliar.saldo);
                $scope.ultimoSaldo = $scope.saldoRespaldo;
                growl.success("Nuevo movimiento agregado");

                if (esEntreCuentas) {
                    $scope.guardarTransferencia();
                }

            }

            $scope.auxiliar = new BancosAuxiliar();
            $scope.auxiliar.cantidadDeposito = 0;
            $scope.bancoTrans = "";
            $scope.conceptos = "";

        }
    };

    $scope.validarAuxiliar = function (esEntreCuentas = false) {
        if ((!$scope.auxiliar.ingresoEgreso || $scope.auxiliar.ingresoEgreso == "") && !esEntreCuentas) {
            growl.error("Especifique ingreso o egreso");
            return false;
        }
        if ($scope.banco.idBanco == "") {
            growl.error("Se requiere de un banco");
            return false;
        }
        if ($scope.cuentaNo.idCuenta == "") {
            growl.error("Se requiere de una cuenta");
            return false;
        }
        if (!$scope.auxiliar.fecha) {
            growl.error("Se requiere de una fecha");
            return false;
        }
        if (!$scope.auxiliar.hora) {
            growl.error("Se requiere una hora");
            return false;
        }
        if (!$scope.auxiliar.tipoMovimiento && !esEntreCuentas) {
            growl.error("Se requiere seleccionar un movimiento");
            return false;
        }
        if (($scope.auxiliar.ingresoEgreso == '1' && !$scope.auxiliar.referencia) || (esEntreCuentas && !$scope.auxiliar.referencia)) {
            growl.error("Se requiere una referencia");
            return false;
        }
        if (!$scope.auxiliar.nombreDe) {
            growl.error("Se requiere un nombre");
            return false;
        }
        if (!$scope.auxiliar.concepto) {
            growl.error("Se requiere un concepto");
            return false;
        }
        if (!esEntreCuentas) {
            if (!$scope.auxiliar.descripcion) {
                growl.error("Se requiere una descripción");
                return false;
            }
            if (!$scope.auxiliar.cantidad) {
                growl.error("Se requiere una cantidad");
                return false;
            }
        }
        return true;
    };
    $scope.guardarAuxiliar = function () {
        console.log($scope.auxiliarDeBancos);
        $scope.bloquearGuardar = true;
        if ($scope.auxiliarDeBancos.length == 0) {
            growl.warning("Agregue el movimiento antes de guardar");
            $scope.bloquearGuardar = false;
        } else {
            angular.forEach($scope.auxiliarDeBancos, function (a) {
                var mees = a.fecha.split("-");
                a.idMes = mees[1];
            });
            $http.post("controlAdministrativo/php/guardarAuxiliarBancos.php", $scope.auxiliarDeBancos).success(function (respuesta) {
                $rootScope.lockTemplate = false;
                swal("Éxito!", "Registro guardado", "success");
                $scope.auxiliar = new BancosAuxiliar();
                $scope.auxiliar.fecha = _fecha;
                $scope.bloquearGuardar = false;
                return window.location.href = "#/menuAuxBancos";
            });
        }
    };


    $scope.imprimirReporteBancario = function (cuenta, mes) {
        window.open('./reportes/bancos/reporteMensualBancario.php?idCuenta=' + cuenta + '&idMes=' + mes, '_blank');
    }
    $scope.imprimirReporteBancarioXls = function (cuenta, mes) {
        return window.location.href = './reportes/bancos/xlsReporteMensualBancario.php?idCuenta=' + cuenta + '&idMes=' + mes;
    }


    /**
         * Funciones para imprimir o cancelar las pólizas
         * 
         */

    $scope.imprimirPoliza = function (idPoliza) {
        window.open('./reportes/polizaCheque/poliza.php?idPoliza=' + idPoliza);
    };
    $scope.eliminarPoliza = function (idPoliza) {
        swal({
            title: '',
            text: '¿Deseas cancelar esta póliza?',
            showCancelButton: true,
            confirmButtonText: 'Si',
            cancelButtonText: 'No',
            closeOnConfirm: true
        }, function (confirm) {
            if (confirm) {
                var date = new Date();
                var _mes = date.getMonth() + 1;
                var _fecha = date.getFullYear() + '-' + _mes + '-' + date.getDate();

                $http.post('controlAdministrativo/polizaCheque/php/cancelarPoliza.php', { idPoliza: idPoliza, fecha: _fecha }).success(function (data) {
                    swal('', data.message, data.swal);
                    if (!data.error) {
                        obtenerListaMovimientosAuxiliar();
                    }
                });
            }
        });
    };


    /**Hacer las pólizas de cheque desde el auxiliar de bancos */
    $scope.poliza = {};
    $scope.showPDF = false;

    function traerListaPersonas(idTipoPersona) {
        $http.post('controlAdministrativo/polizaCheque/php/traerNombres.php', idTipoPersona).success(function (data) {
            $scope.personas = data;
        });
    };

    $scope.traeLasCuentasDelBanco = function (idBanco) {
        $http.get('controlAdministrativo/php/infoCuentasAnidadas.php?idBanco=' + idBanco).success(function (data) {
            $scope.lstQentas = data;
        });
    };
    $scope.actualizarSaldoBanco = function (cantidad) {
        var _saldoBanco = parseFloat($scope.saldoRespaldo);
        if (cantidad && $scope.hacerPolizaCheque) {
            var _nuevoSaldo = _saldoBanco - parseFloat(cantidad);
            $scope.ultimoSaldo = _nuevoSaldo;
        } else {
            $scope.ultimoSaldo = _saldoBanco;
        }
    }
    $scope.validarPoliza = function () {
        if (!$scope.banco) {
            growl.error("Seleccione banco");
        } else {
            console.log(typeof $scope.banco);
            if (typeof ($scope.banco) == 'number') {
                $scope.poliza.idBanco = $scope.banco;
            } else if (typeof ($scope.banco) == 'object') {
                return false;
            } else {
                growl.error("Seleccione banco");
            }
        }

        if (!$scope.cuentaNo) {
            growl.error("Seleccione cuenta");
        } else {
            console.log(typeof $scope.cuentaNo);
            if (typeof ($scope.cuentaNo) == 'number') {
                $scope.poliza.bancoCuenta = $scope.cuentaNo;
            } else if (typeof ($scope.cuentaNo == 'object')) {
                return false;
            } else {
                growl.error("Seleccione cuenta");
            }
        }

        if (!$scope.poliza.fecha) {
            growl.error("Seleccione fecha");
        }

        if (!$scope.poliza.hora) {
            growl.error("Seleccione hora");
        }
        if (!$scope.poliza.descripcion) {
            growl.error("Se requiere una descripción");
        }

        if (!$scope.poliza.persona) {
            growl.error("Seleccione un tipo de persona");
        }
        if (!$scope.poliza.nombre) {
            growl.error("Se requiere de un nombre");
        }

        // El nombre 2 ya no va a ser obligatorio, para lo de compras
        // if (!$scope.poliza.nombre2) {
        //     return false;
        // }

        if (!$scope.poliza.cantidad) {
            growl.error("Se requiere una cantidad");
        }
        if (!$scope.poliza.concepto) {
            growl.error("Seleccione un concepto");
        }

        if (!$scope.poliza.folioCheque) {
            growl.error("Se requiere folio");
        }

        return true;
    };

    $scope.verPoliza = function () {
        $scope.validado = $scope.validarPoliza();
        if ($scope.validado == true) {
            // $scope.poliza.tipoMovimiento = '';
            var polizaHora = document.getElementById("polizaHora");
            $scope.poliza.hora = polizaHora.value;
            $http.post('controlAdministrativo/polizaCheque/php/guardarPoliza.php', $scope.poliza).success(function (data) {
                if (!data.error) {
                    $scope.idPolizaCheque = data.idPoliza;
                    $scope.urlAPoliza = data.url;
                    $scope.poliza = {};
                    $scope.showPDF = true;
                    return;
                } else {
                    growl.error(data.message);
                }
            });
        } else {
            growl.error('Verifica todos los campos');
            return;
        }

    };

    $scope.cancelarPoliza = function () {
        swal({
            title: '',
            text: '¿Deseas cancelar esta póliza?',
            showCancelButton: true,
            confirmButtonText: 'Si',
            cancelButtonText: 'No',
            closeOnConfirm: true
        }, function (confirm) {
            if (confirm) {

                var date = new Date();
                var _mes = date.getMonth() + 1;
                var _fecha = date.getFullYear() + '-' + _mes + '-' + date.getDate();

                $http.post('controlAdministrativo/polizaCheque/php/cancelarPoliza.php', { idPoliza: $scope.idPolizaCheque, fecha: _fecha }).success(function (data) {
                    swal('', data.message, data.swal);
                    if (!data.error) {
                        $scope.showPDF = false;
                        $scope.urlAPoliza = '';
                        $scope.idPolizaCheque = 0;
                        $scope.poliza = {};
                    }
                });
            }
        });
    };

    $scope.AceptarPoliza = function () {

        swal('', 'Se ha guardado la póliza, asegúrate de imprimirla', 'success');
        $scope.showPDF = false;
        $scope.idPolizaCheque = 0;
        $scope.poliza = {};
        window.open($scope.urlAPoliza);
        $scope.urlAPoliza = '';
    };

    $scope.controladorPolizaCheque = function () {

        // Traer los movimientos para egreso
        $scope.poliza.ingresoEgreso = "1";
        $scope.cambioTipoIngresoEgreso();
        if (!$scope.lstBancos) {
            $http.get('controlAdministrativo/php/listaDeBancos.php').success(function (datas) {
                $scope.lstBancos = datas;
            });
        }

        $scope.$watch('poliza.persona', function (idTipoPersona) {
            if (idTipoPersona !== undefined) {
                traerListaPersonas(idTipoPersona);
            }
        });

    }

    $scope.$on('agregar-movimiento-mes', function (event) {
        window.location.href = '#/nvoAuxBanco/0';
    });

    $scope.movimientoConMes = function () {
        if (typeof (Storage) !== "undefined") {
            localStorage.setItem('idMesParaMovimiento', $scope.idMesBanco);
            $rootScope.$broadcast('agregar-movimiento-mes');
        } else {
            growl.error('Navegador no soporta localstorage, prueba a usar Chrome');
        }
    }
}]);