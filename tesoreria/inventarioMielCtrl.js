form.controller('inventarioMielCtrl', ['$scope', '$http', 'growl', '$location', '$routeParams', function ($scope, $http, growl, $location, $routeParams) {

    $scope.meses = {};
    $scope.inventarioDeMiel = new Array();
    $scope.opcionMes = {};
    $scope.cargandoDatos = false;
    if ($location.path() == '/inventarioMiel') {
        $http.get('almacen/php/consultaComboMeses.php').success(function (data) {
            // Solo se muestra en la vista de creacion, son los datos de los meses
            $scope.meses = data;
        });
    }
    function validarDatos(objeto) {
        if (!objeto.opcion) {
            growl.info('Selecciona una  opción')
            return false;
        } else {
            if (objeto.opcion == '2' && !objeto.mes) {
                growl.info('Selecciona un mes');
                return false;
            }
        }
        if (!objeto.tipoMiel) {
            growl.info('Selecciona el tipo de miel');
            return false;
        }
        return true;
    };

    $scope.verMes = function (opcionMes) {
        if (validarDatos(opcionMes)) {

            $scope.cargandoDatos = true;

            $scope.promedioPrecio = null;
            $scope.totalImporteEntrada = null;
            $scope.totalImporteSalida = null;
            $scope.existenciaPasada = null;
            $scope.importeAcumuladoPasado = null;
            $scope.totalEntradas = null;
            $scope.totalSalidas = null;
            $scope.totalInventario = null;
            $scope.totalImportesAcumulados = null;
            $scope.inventarioDeMiel = null;
            $scope.resumenPorEmpresas = null;
            $scope.precioPromedioPorMeses = null;
            if (opcionMes.opcion == 1) {
                $http.post('controlAdministrativo/php/inventarioComprasMiel.php?acumulado=1&miel=' + opcionMes.tipoMiel).success(function (data) {
                    console.log(data);
                    $scope.cargandoDatos = false;
                    if (!data.error) {
                        $scope.precioPromedioPorMeses = data.comprasDeMielPorMeses;
                        $scope.resumenPorEmpresas = data.resumenPorEmpresas;
                        $scope.promedioPrecio = data.encabezado.promedioPrecio;
                        $scope.totalImporteEntrada = data.encabezado.totalImporteEntrada;
                        $scope.totalImporteSalida = data.encabezado.totalImporteSalida;
                        $scope.existenciaPasada = data.existenciaPasada;
                        $scope.importeAcumuladoPasado = data.importeAcumuladoPasado;
                        $scope.totalEntradas = data.encabezado.totalEntradas;
                        $scope.totalSalidas = data.encabezado.totalSalidas;
                        $scope.totalInventario = data.encabezado.totalInventario;
                        $scope.totalImportesAcumulados = data.encabezado.totalImportesAcumulados;
                        $scope.inventarioDeMiel = data.inventarioMiel;
                    } else {
                        growl.error(data.message);
                    }
                })
            } else if (opcionMes.opcion == 2) {
                $http.post('controlAdministrativo/php/inventarioComprasMiel.php?idMes=' + opcionMes.mes + '&miel=' + opcionMes.tipoMiel).success(function (data) {
                    console.log(data);
                    $scope.cargandoDatos = false;
                    if (!data.error) {
                        $scope.precioPromedioPorMeses = data.comprasDeMielPorMeses;
                        $scope.resumenPorEmpresas = data.resumenPorEmpresas;
                        $scope.promedioPrecio = data.encabezado.promedioPrecio;
                        $scope.totalImporteEntrada = data.encabezado.totalImporteEntrada;
                        $scope.totalImporteSalida = data.encabezado.totalImporteSalida;
                        $scope.existenciaPasada = data.existenciaPasada;
                        $scope.importeAcumuladoPasado = data.importeAcumuladoPasado;
                        $scope.totalEntradas = data.encabezado.totalEntradas;
                        $scope.totalSalidas = data.encabezado.totalSalidas;
                        $scope.totalInventario = data.encabezado.totalInventario;
                        $scope.totalImportesAcumulados = data.encabezado.totalImportesAcumulados;
                        $scope.inventarioDeMiel = data.inventarioMiel;
                    } else {
                        growl.error(data.message);
                    }
                })
            }
        }
    };


    $scope.imprimirAlmacen = function (opcionMes) {
        if (validarDatos(opcionMes)) {
            if (opcionMes.opcion == 1) {
                $http.get('reportes/administrativo/xlsInventarioDeMiel.php?opcion=1&miel='+opcionMes.tipoMiel).success(function (data) {
                    if (data.hasOwnProperty('error') && data.error == true) {
                        swal('', data.message, 'error')
                    } else {
                        return window.location.href = 'reportes/administrativo/xlsInventarioDeMiel.php?opcion=1&miel='+opcionMes.tipoMiel;
                    }
                })
            } else {
                $http.get('reportes/administrativo/xlsInventarioDeMiel.php?opcion=2&mes=' + opcionMes.mes + '&miel='+opcionMes.tipoMiel).success(function (data) {
                    if (data.hasOwnProperty('error') && data.error == true) {
                        swal('', data.message, 'error')
                    } else {
                        return window.location.href = 'reportes/administrativo/xlsInventarioDeMiel.php?opcion=2&mes=' + opcionMes.mes + '&miel='+opcionMes.tipoMiel;
                    }
                })
            }
        }
    }

    function traerMielInicialAnual() {
        $http.get('controlAdministrativo/php/mielInicialAnual.php?opt=get').success(function (data) {
            if (!data.error) {
                $scope.mielInicialAnual = data.data;
            } else {
                growl.error(data.message);
            }
        });
    }

    function guardarMielInicialAnual(mielInicial) {
        $http.post('controlAdministrativo/php/mielInicialAnual.php?opt=set', mielInicial).success(function (data) {
            if (!data.error) {
                growl.success(data.message);
                $('#modalMielInicial').modal('hide');
            } else {
                growl.error(data.message);
            }
        });
    }

    $scope.setMielInicialAnual = function () {
        traerMielInicialAnual();
        $('#modalMielInicial').modal();
    }

    $scope.guardarNuevoMienInicialAnual = function () {
        if ($scope.mielInicialAnual && $scope.mielInicialAnual.existenciaPasada && $scope.mielInicialAnual.importeAcumuladoPasado) {
            guardarMielInicialAnual($scope.mielInicialAnual);
        } else {
            growl.info('Los datos son necesarios');
        }
    }

    if ($routeParams.acumulado) {
        $scope.opcionMes = { opcion: '1', tipoMiel: '1' };
        $scope.verMes($scope.opcionMes);
    }
}]);