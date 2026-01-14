form.config(function ($routeProvider) {
    $routeProvider.when('/saldosMiel', {
        templateUrl: 'saldosiniciales/saldosInicialesMiel.html',
        controller: 'saldosInicialesMielCtrl'
    })
});
form.controller('saldosInicialesMielCtrl', ['$scope', '$http', 'growl', function ($scope, $http, growl) {

    $scope.nuevoSaldoInicialMiel = {};
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

    $scope.cambioSeleccionCuenta = function (idCuenta = false) {
        // Cuando cambia la cuenta seleccionada, 
        // trae las subcuentas
        if (!idCuenta) {
            // si no manda la cuenta, la toma del scope, primero la verifica
            if ($scope.nuevoSaldoInicialMiel.tipoMovimiento && $scope.nuevoSaldoInicialMiel.tipoMovimiento.idCuentaConcepto) {
                traerListaSubcuentas($scope.nuevoSaldoInicialMiel.tipoMovimiento.idCuentaConcepto);
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

    $scope.guardarNuevoSaldoMiel = function () {
        // validar los campos
        // tipoMovimiento
        // concepto
        // subconcepto

        if ($scope.nuevoSaldoInicialMiel.tipoMovimiento && $scope.nuevoSaldoInicialMiel.concepto && $scope.nuevoSaldoInicialMiel.subconcepto
            && $scope.nuevoSaldoInicialMiel.existenciaPasada && $scope.nuevoSaldoInicialMiel.importePasado) {

            // guardar
            $http.post('saldosiniciales/php/guardarNuevoSaldoApicola.php?miel=0', $scope.nuevoSaldoInicialMiel).success(function (data) {
                if (typeof (data) == 'object' && data.hasOwnProperty('error')) {
                    if (data.error) {
                        swal('Error', data.message, 'warning');
                    } else {
                        growl.success('Hecho', 'Se ha registrado un nuevo saldo inicial');
                        $scope.crearNuevoSaldo = false;
                        obtenerSaldosMiel();
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

    traerListaCuentas(0);

    // productos apicolas desde otro archivo (es otra tabla)
    function obtenerSaldosMiel() {
        $http.get('saldosiniciales/php/traerSaldosMiel.php').success(function (data) {
            if (typeof (data) == 'object' && data.hasOwnProperty('error')) {
                if (data.error) {
                    swal('Error', data.message, 'warning');
                } else {
                    $scope.inventarios_miel = data.data;
                }
            } else {
                growl.error('Error al traer datos');
                console.error(data);
            }
        });
    }
    obtenerSaldosMiel();


    // Funcion para } los inventarios de productos apícolas
    // Guardar los saldos iniciales de UN SOLO inventario APICOLA
    $scope.guardarSaldosInventario_miel = function (inventario, index) {
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

            $http.post('saldosiniciales/php/guardarSaldosInventarioApicola.php?miel=0', inventario).success(function (data) {

                if (typeof (data) == 'object' && data.hasOwnProperty('error')) {
                    if (data.error) {
                        swal('Error', data.message, 'warning');
                    } else {
                        growl.success('Hecho', data.message);
                        obtenerSaldosMiel();
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