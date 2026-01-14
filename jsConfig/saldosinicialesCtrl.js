form.controller('saldosinicialesCtrl', ['$scope', '$http', 'growl', function ($scope, $http, growl) {

    $scope.nuevoSaldoInicialApicola = {};
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


    $scope.cambioSeleccionCuenta = function (idCuenta = false) {
        // Cuando cambia la cuenta seleccionada, 
        // trae las subcuentas
        if (!idCuenta) {
            // si no manda la cuenta, la toma del scope, primero la verifica
            if ($scope.nuevoSaldoInicialApicola.tipoMovimiento && $scope.nuevoSaldoInicialApicola.tipoMovimiento.idCuentaConcepto) {
                traerListaSubcuentas($scope.nuevoSaldoInicialApicola.tipoMovimiento.idCuentaConcepto);
            } else if ($scope.poliza.tipoMovimiento && $scope.poliza.tipoMovimiento.idCuentaConcepto) {
                traerListaSubcuentas($scope.poliza.tipoMovimiento.idCuentaConcepto);
            }
        } else {
            // Si envia la cuenta, lo hace directo
            traerListaSubcuentas(idCuenta);
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

            });

        }
    }

    $scope.guardarNuevoSaldoApicola = function () {
        // validar los campos
        // tipoMovimiento
        // concepto
        // subconcepto

        if ($scope.nuevoSaldoInicialApicola.tipoMovimiento && $scope.nuevoSaldoInicialApicola.concepto && $scope.nuevoSaldoInicialApicola.subconcepto
            && $scope.nuevoSaldoInicialApicola.existenciaPasada && $scope.nuevoSaldoInicialApicola.importePasado) {

            // guardar
            $http.post('saldosiniciales/php/guardarNuevoSaldoApicola.php', $scope.nuevoSaldoInicialApicola).success(function (data) {
                if (typeof (data) == 'object' && data.hasOwnProperty('error')) {
                    if (data.error) {
                        swal('Error', data.message, 'warning');
                    } else {
                        growl.success('Hecho', 'Se ha registrado un nuevo saldo inicial');
                        $scope.crearNuevoSaldo = false;
                        obtenerSaldosProductosApicolas();
                    }
                } else {
                    growl.error('Error al traer datos');
                    console.error(data);
                }
            });

        } else {
            growl.info('Completa todos los campos');
        }
    }

    traerListaCuentas(1);

    // 1 Al iniciar, traer los saldos iniciales guardados en la base de datos

    $http.get('saldosiniciales/php/traerSaldosInventarios.php').success(function (data) {
        if (typeof (data) == 'object' && data.hasOwnProperty('error')) {
            if (data.error) {
                swal('Error', data.message, 'warning');
            } else {
                $scope.inventarios = data.data;
            }
        } else {
            growl.error('Error al traer datos');
            console.error(data);
        }
    });

    // productos apicolas desde otro archivo (es otra tabla)
    function obtenerSaldosProductosApicolas() {
        $http.get('saldosiniciales/php/traerSaldosProductosApicolas.php').success(function (data) {
            if (typeof (data) == 'object' && data.hasOwnProperty('error')) {
                if (data.error) {
                    swal('Error', data.message, 'warning');
                } else {
                    $scope.inventarios_apicola = data.data;
                }
            } else {
                growl.error('Error al traer datos');
                console.error(data);
            }
        });
    }
    obtenerSaldosProductosApicolas();


    /** Funciones */

    // Obtiene saldos y retornar un objeto de inventario
    function traerSaldosInventario(idinventario, index) {
        $http.get('saldosiniciales/php/traerInventario.php?id=' + idinventario).success(function (data) {
            if (typeof (data) == 'object' && data.hasOwnProperty('error')) {
                if (data.error) {
                    swal('Error', data.message, 'warning');
                } else {
                    $scope.inventarios[index] = data.data;
                }
            } else {
                growl.error('Error al traer datos');
                console.error(data);
            }
        });
    }

    // Funcion para los inventarios de miel, cera, materia prima
    // Guardar los saldos iniciales de UN SOLO inventario
    $scope.guardarSaldosInventario = function (inventario, index) {

        if (inventario.id && (index >= 0)) {
            // Limpiar el input de comas, signos, etc
            var existenciaPasada = inventario.existenciaPasada.split(',').join('').match(/[\d\.]+/);
            var importeAcumuladoPasado = inventario.importeAcumuladoPasado.split(',').join('').match(/[\d\.]+/);

            if (existenciaPasada) {
                inventario.existenciaPasada = existenciaPasada[0]
            } else {
                inventario.existenciaPasada = 0;
            }

            if (importeAcumuladoPasado) {
                inventario.importeAcumuladoPasado = importeAcumuladoPasado[0]
            } else {
                inventario.importeAcumuladoPasado = 0;
            }

            $http.post('saldosiniciales/php/guardarSaldosInventario.php', inventario).success(function (data) {
                if (typeof (data) == 'object' && data.hasOwnProperty('error')) {
                    if (data.error) {
                        swal('Error', data.message, 'warning');
                    } else {
                        growl.success('Hecho', data.message);
                        traerSaldosInventario(inventario.id, index);
                    }
                } else {
                    growl.error('Error al traer datos');
                    console.error(data);
                }
            })
        } else {
            growl.error('Error', 'Intente recargando la página');
        }
    }

    // Funcion para } los inventarios de productos apícolas
    // Guardar los saldos iniciales de UN SOLO inventario APICOLA
    $scope.guardarSaldosInventario_apicola = function (inventario, index) {
        // No verificamos el id porque puede que no lo tenga, en ese caso el servidor debe agregarlo  a la tabla
        if (index >= 0) {
            // Limpiar el input de comas, signos, etc
            var existenciaPasada = inventario.existenciaPasada.split(',').join('').match(/[\d\.]+/);
            var importePasado = inventario.importePasado.split(',').join('').match(/[\d\.]+/);

            if (existenciaPasada) {
                inventario.existenciaPasada = existenciaPasada[0]
            } else {
                inventario.existenciaPasada = 0;
            }

            if (importePasado) {
                inventario.importePasado = importePasado[0]
            } else {
                inventario.importePasado = 0;
            }

            $http.post('saldosiniciales/php/guardarSaldosInventarioApicola.php', inventario).success(function (data) {

                if (typeof (data) == 'object' && data.hasOwnProperty('error')) {
                    if (data.error) {
                        swal('Error', data.message, 'warning');
                    } else {
                        growl.success('Hecho', data.message);
                        obtenerSaldosProductosApicolas();
                    }
                } else {
                    growl.error('Error al traer datos');
                    console.error(data);
                }
            })
        } else {
            growl.error('Error', 'Intente recargando la página');
        }
    }

}]);