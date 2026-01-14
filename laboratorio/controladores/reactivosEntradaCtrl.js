form.config(function ($routeProvider) {
    $routeProvider.when('/entradasReactivos', {
        templateUrl: 'laboratorio/entradasDeReactivos.html',
        controller: 'reactivosEntradaCtrl'
    }).when('/entradaReactivo/:idEntrada', {
        templateUrl: 'laboratorio/capturaEntradaDeReactivos.html',
        controller: 'reactivosEntradaCtrl'
    })
});

form.controller('reactivosEntradaCtrl', function ($scope, $http, $routeParams, growl, $location, $rootScope) {

    $scope.entradasReactivos = new Array();
    $scope.idEntrada = $routeParams.idEntrada;
    $scope.guardandoDatos = false;
    $scope.cargandoDatos = false;
    $scope.nuevaEntrada = {};
    $scope.entrada = {
        conceptos: Array(),
        total: 0
    }
    var date = new Date();
    var _mes = date.getMonth() + 1;
    $scope.mostrarMes = _mes.toString();

    $http.get('almacen/php/consultaComboMeses.php').success(function (data) {
        $scope.listaDeMeses = data;
    });

    $http.get('catalogos/php/traeReactivos.php').success(function (data) {
        $scope.listaReactivos = data.reactivos;
    });

    function traeEntradas() {
        $scope.entradasReactivos = null;
        $scope.cargandoDatos = true;
        url = 'laboratorio/php/entradasDeReactivos.php';
        if ($scope.mostrarMes && $scope.mostrarMes !== "null") {
            url += '?mes=' + $scope.mostrarMes;
        }
        $http.get(url).success(function (data) {
            if (data.error) {
                console.error(data.message);
            }
            $scope.entradasReactivos = data.data;
            $scope.cargandoDatos = false;
        });
    };

    if ($location.path() == '/entradasReactivos') {
        if (window.localStorage.getItem('seleccionMes') != null) {
            $scope.mostrarMes = window.localStorage.getItem('seleccionMes');
        }
        $scope.$watch('mostrarMes', function (mesElegido) {
            window.localStorage.setItem('seleccionMes', mesElegido);
            traeEntradas();
        })
    }

    if ($scope.idEntrada) {
        traeListas();
        traeTiposDeMovimiento(1);
        traeEntrada($scope.idEntrada)
    }

    function traeListas() {
        $http.post('controlAdministrativo/polizaCheque/php/traerNombres.php', 3).success(function (data) {
            $scope.listaProveedores = data;
        });
    }
    function traeTiposDeMovimiento(tipo) {
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
    };
    $scope.cambioSeleccionCuenta = function (idCuenta = false) {
        if (!idCuenta) {
            if ($scope.nuevaEntrada.cuenta && $scope.nuevaEntrada.cuenta.idCuentaConcepto) {
                traerListaSubcuentas($scope.nuevaEntrada.cuenta.idCuentaConcepto);
            }
        } else {
            traerListaSubcuentas(idCuenta);
        }
        $scope.calcularimportePeso();
    }
    function traerListaSubcuentas(idCuenta) {
        $scope.listaSubcuentasSeleccionadas = null;
        $scope.listaSubSubcuentas = null;
        $http.post('catalogos/php/traeSubcuentas.php?idCuentaConcepto=' + idCuenta).success(function (data) {
            if (typeof (data) == 'object' && data.length >= 0) {
                $scope.listaSubcuentasSeleccionadas = data;
            } else {
                growl.error('Error');
                console.error(data);
            }
        })
    }
    $scope.calcularimportePeso = function () {
        if ($scope.nuevaEntrada.cantidad > 0 && $scope.nuevaEntrada.costoUnitario) {
            $scope.nuevaEntrada.importe = parseFloat($scope.nuevaEntrada.cantidad) * parseFloat($scope.nuevaEntrada.costoUnitario);
        } else {
            $scope.nuevaEntrada.importe = 0;
        }
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
                $scope.calcularimportePeso();
            });
        }
    }
    $scope.agregarConcepto = function () {
        if (validarNuevoConcepto($scope.nuevaEntrada)) {
            // $scope.nuevaEntrada.idMovimiento = $scope.nuevaEntrada.cuenta && $scope.nuevaEntrada.cuenta.idCuentaConcepto ? $scope.nuevaEntrada.cuenta.idCuentaConcepto : null;
            // $scope.nuevaEntrada.movimiento = $scope.nuevaEntrada.cuenta && $scope.nuevaEntrada.cuenta.cuenta ? $scope.nuevaEntrada.cuenta.cuenta : '';
            // $scope.nuevaEntrada.idSubcuenta = $scope.nuevaEntrada.subcuentaObj.idSubcuenta ? $scope.nuevaEntrada.subcuentaObj.idSubcuenta : null;
            // $scope.nuevaEntrada.subcuenta = $scope.nuevaEntrada.subcuentaObj.subcuenta ? $scope.nuevaEntrada.subcuentaObj.subcuenta : '';
            // $scope.nuevaEntrada.idConcepto = $scope.nuevaEntrada.producto && $scope.nuevaEntrada.producto.idSubSubcuenta ? $scope.nuevaEntrada.producto.idSubSubcuenta : null;
            // $scope.nuevaEntrada.concepto = $scope.nuevaEntrada.producto && $scope.nuevaEntrada.producto.subSubcuenta ? $scope.nuevaEntrada.producto.subSubcuenta : '';
            $scope.nuevaEntrada.idReactivo = $scope.nuevaEntrada.reactivo.idReactivo;
            $scope.nuevaEntrada.reactivo = $scope.nuevaEntrada.reactivo.reactivo;
            $scope.entrada.conceptos.push($scope.nuevaEntrada);
            $scope.nuevaEntrada = {};
            calcularTotalEntrada();
        } else {
            growl.error('Verifica los campos necesarios');
        }
    };
    function calcularTotalEntrada() {
        $scope.entrada.total = 0;
        $scope.entrada.conceptos.forEach(concepto => {
            if (!isNaN(parseInt(concepto.importe))) {
                $scope.entrada.total += parseFloat(concepto.importe);
            }
        });
    };
    function validarNuevoConcepto(concepto) {
        if (!concepto.reactivo) {
            growl.info('Seleccione el catálogo');
            return false;
        } else if (!concepto.cantidad) {
            growl.info('Indique la cantidad');
            return false;
        } else if (!concepto.costoUnitario) {
            growl.info('Indique el precio unitario');
            return false;
        }
        return true;
    };
    function guardarEntrada(entrada) {
        $http.post("laboratorio/php/guardarEntradaReactivos.php", entrada).success(function (info) {
            if (info.hasOwnProperty('error')) {
                if (!info.error) {
                    $rootScope.lockTemplate = false;
                    swal('', info.message, info.swal);
                    window.location.href = '#/entradasReactivos';
                } else {
                    swal('Error', info.message, 'error');
                }
            } else {
                growl.error('Error');
                console.error(info);
            }
        });
    };

    function verificarEntrada(entrada) {
        if (!entrada.fecha) {
            growl.info('Selecciona la fecha de entrada');
            return false;
        } else if (entrada.proveedor.id == '0') {
            growl.info('Seleccione al proveedor');
            return false;
        } else if (entrada.conceptos.length < 1) {
            growl.info('La entrada debe contener al menos 1 movimiento');
            return false;
        }
        return true;
    };

    $scope.guardarEntrada = function () {
        if (verificarEntrada($scope.entrada)) {
            guardarEntrada($scope.entrada);
        }
    };
    function traeEntrada(idEntrada) {
        $http.get('laboratorio/php/detalleEntradaReactivo.php?idEntrada=' + idEntrada).success(function (data) {
            if (data.hasOwnProperty('error')) {
                if (!data.error) {
                    $scope.entrada = data.data;
                    $scope.entrada.proveedor = { id: data.data.idProveedor };
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
        $scope.entrada.conceptos.splice(index, 1);
        calcularTotalEntrada();
    };

    $scope.eliminarRegistro = function (entrada) {
        swal({
            title: "",
            text: "¿Está seguro de eliminar el registro?",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#64DAE4",
            confirmButtonText: "Sí, eliminar.",
            closeOnConfirm: false
        },
            function () {
                $http.post("laboratorio/php/eliminarRegistoEntradaReactivo.php?entrada=" + entrada).success(function (respuesta) {
                    if (respuesta.hasOwnProperty('error')) {
                        if (!respuesta.error) {
                            swal("Éxito!", "Registro eliminado", "success");
                            traeEntradas();
                        } else {
                            growl.info(respuesta.message);
                        }
                    } else {
                        growl.info("Ocurrió un error");
                    }
                });
            });
    };

});