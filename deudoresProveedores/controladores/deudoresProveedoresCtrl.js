form.config(function ($routeProvider) {
    $routeProvider.when('/estadosCuentas', {
        templateUrl: 'deudoresProveedores/estadosDeCuentas.html',
        controller: 'deudoresProveedoresCtrl'
    }).when('/saldosDeudores', {
        templateUrl: 'deudoresProveedores/saldosIniciales.html',
        controller: 'deudoresProveedoresCtrl'
    }).when('/gastosRealizados', {
        templateUrl: 'deudoresProveedores/menuGastosRealizados.html',
        controller: 'deudoresProveedoresCtrl'
    }).when('/menuGastosProveedor/:idProveedor', {
        templateUrl: 'deudoresProveedores/menuGastosProveedor.html',
        controller: 'deudoresProveedoresCtrl'
    }).when('/nvoGasto/:idGasto', {
        templateUrl: 'deudoresProveedores/nuevoGastoRealizado.html',
        controller: 'deudoresProveedoresCtrl'
    }).when('/concentradoDeudor', {
        templateUrl: 'deudoresProveedores/concentradoDeudores.html',
        controller: 'deudoresProveedoresCtrl'
    });
})
form.controller('deudoresProveedoresCtrl', ['$scope', '$routeParams', '$http', 'growl', '$location', function ($scope, $routeParams, $http, growl, $location) {

    //==========================================
    //             SALDOS INICIALES
    //==========================================
    $scope.arrayProveedores = new Array();
    $scope.concentradoDeudores = new Array();
    $scope.totalSaldoDeudor = 0;
    $scope.totalSaldoProveedor = 0;
    $scope.meses = {};
    $scope.opcionCuenta = {
        vista: "1",
        opcionDeudor: "todos"
    };

    $scope.cantidadTotalBancos = 0;
    $scope.cantidadTotalEfectivo = 0;
    $scope.cantidadTotalIngresosCeraApicola = 0;
    $scope.cantidadTotalKilosMiel = 0;
    $scope.cantidadTotalPrecioPromedio = 0;
    $scope.cantidadTotalImporteMiel = 0;
    $scope.cantidadTotalEgresosCeraApicola = 0;
    $scope.cantidadTotalGastos = 0;
    $scope.cantidadTotalRetenciones = 0;
    $scope.cantidadTotalDevolucionesEfectivo = 0;
    $scope.cantidadTotalDevolucionesCeraApicola = 0;
    $scope.cantidadTotalVentasCeraApicola = 0;
    $scope.cantidadTotalGranTotal = 0;

    if ($location.path() == '/saldosDeudores') {
        $http.get('json/administracion/estadosDeudores.json').success(function (data) {
            $scope.estados = data.estadosDeudores;
        });
        $http.get('deudoresProveedores/php/traeDeudores.php').success(function (data) {
            $scope.arrayProveedores = data;
        });
    }

    $scope.guardarSaldos = function () {
        var proveedores = $scope.arrayProveedores.map(function (element) {
            var proveedor = {
                idProveedor: parseInt(element.idProveedor),
                idEstado: parseInt(element.idEstado),
                cantidad: parseFloat(element.cantidad)
            }
            return proveedor;
        });
        $http.post('deudoresProveedores/php/guardarSaldosDeudores.php', { valor: proveedores }).success(function (data) {
            swal("Exito!", "Registros Actualizados", "success");
        });
    };
    /* -------------------------------------- */

    //==========================================
    //            ESTADOS DE CUENTAS
    //==========================================
    $scope.arrayDeudores = new Array();
    $scope.opcionDeudores;
    $scope.estados = {};
    $scope.cargandoDatos = false;

    function validarDatos(objeto) {
        if (!objeto.vista) {
            growl.info('Selecciona una  opción')
            return false;
        } else {
            if (objeto.vista == '2' && !objeto.mes) {
                growl.info('Selecciona un mes');
                return false;
            }
        }
        return true;
    };

    $scope.verEstadoCuentaPDF = function (opcionCuenta) {
        if (validarDatos(opcionCuenta)) {
            let urlPdf = '';
            // Preguntar si quiere mostrar los liquidados
            if (opcionCuenta.vista == '1') {
                urlPdf = 'reportes/deudoresProveedores/reportePdfConcentrado.php?function&acumulado=1&parametro=' + opcionCuenta.opcionDeudor;
            } else {
                urlPdf = 'reportes/deudoresProveedores/reportePdfConcentrado.php?function&idMes=' + opcionCuenta.mes + '&parametro=' + opcionCuenta.opcionDeudor;
            }

            if (opcionCuenta.comprador) {
                urlPdf += '&idComprador=' + opcionCuenta.comprador;
            }

            swal({
                title: "¿Mostrar los proveedores liquidados?",
                text: "",
                type: "info",
                showCancelButton: true,
                // confirmButtonColor: "#DD6B55",
                confirmButtonText: "Mostrar",
                cancelButtonText: "No mostrar",
            }, function (mostrar) {
                if (!mostrar) {
                    urlPdf += '&noLiquidados'
                }
                window.open(urlPdf);
            });
        }
    }

    $scope.verEstadoCuentaEXCEL = function (opcionCuenta) {
        if (validarDatos(opcionCuenta)) {
            let urlExcel = '';
            // Preguntar si quiere mostrar los liquidados
            if (opcionCuenta.vista == '1') {

                urlExcel = 'reportes/deudoresProveedores/xlsConcentradoDeudores.php?function&acumulado=1&parametro=' + opcionCuenta.opcionDeudor;

            } else {
                urlExcel = 'reportes/deudoresProveedores/xlsConcentradoDeudores.php?function&idMes=' + opcionCuenta.mes + '&parametro=' + opcionCuenta.opcionDeudor;
            }

            if (opcionCuenta.comprador) {
                urlExcel += '&idComprador=' + opcionCuenta.comprador;
            }

            swal({
                title: "¿Mostrar los proveedores liquidados?",
                text: "",
                type: "info",
                showCancelButton: true,
                // confirmButtonColor: "#DD6B55",
                confirmButtonText: "Mostrar",
                cancelButtonText: "No mostrar",
            }, function (mostrar) {
                if (!mostrar) {
                    urlExcel += '&noLiquidados'
                }
                return window.location.href= urlExcel;
            });
        }
    }

    $scope.verEstadoCuenta = function (opcionCuenta) {
        if (validarDatos(opcionCuenta)) {
            $scope.cargandoDatos = true;
            $scope.arrayDeudores = [];
            $scope.cantidadTotalBancos = 0;
            $scope.cantidadTotalEfectivo = 0;
            $scope.cantidadTotalIngresosCeraApicola = 0;
            $scope.cantidadTotalKilosMiel = 0;
            $scope.cantidadTotalPrecioPromedio = 0;
            $scope.cantidadTotalImporteMiel = 0;
            $scope.cantidadTotalEgresosCeraApicola = 0;
            $scope.cantidadTotalGastos = 0;
            $scope.cantidadTotalRetenciones = 0;
            $scope.cantidadTotalDevolucionesEfectivo = 0;
            $scope.cantidadTotalDevolucionesCeraApicola = 0;
            $scope.cantidadTotalVentasCeraApicola = 0;
            $scope.cantidadTotalGranTotal = 0;
            $scope.totalSaldoDeudor = 0;
            $scope.totalSaldoProveedor = 0;
            $scope.conciliacion = 0;
            if (opcionCuenta.vista == '1') {
                var url = 'deudoresProveedores/php/deudoresProveedores.php?acumulado=1&parametro=' + opcionCuenta.opcionDeudor;
                if (opcionCuenta.comprador) {
                    url += '&idComprador=' + opcionCuenta.comprador;
                }
                $http.get(url).success(function (data) {
                    if (data.length > 0) {
                        $scope.arrayDeudores = data;

                        angular.forEach(data, function (value) {

                            if (value.totalSaldo === 0) {
                                value.saldoDeudor = 0;
                                value.saldoProveedor = 0;
                            }
                            //  else if (value.cantidad === 0) {
                            //     value.saldoDeudor = 0;
                            //     value.saldoProveedor = 0;
                            // }
                            else if (value.totalSaldo > 0) {
                                value.saldoDeudor = parseFloat(value.totalSaldo);
                                value.saldoProveedor = 0;
                                $scope.totalSaldoDeudor += parseFloat(value.saldoDeudor);
                            } else if (value.totalSaldo < 0) {
                                value.saldoProveedor = parseFloat(value.totalSaldo);
                                value.saldoDeudor = 0;
                                $scope.totalSaldoProveedor += parseFloat(value.saldoProveedor);
                            } else if (value.cantidad > 0) {
                                value.saldoProveedor = 0;
                                value.saldoDeudor = parseFloat(value.cantidad);
                                $scope.totalSaldoDeudor += parseFloat(value.saldoDeudor);
                            } else if (value.cantidad < 0) {
                                value.saldoProveedor = parseFloat(value.cantidad);
                                value.saldoDeudor = 0;
                                $scope.totalSaldoProveedor += parseFloat(value.saldoProveedor);
                            }
                        });
                        $scope.conciliacion = $scope.totalSaldoDeudor + $scope.totalSaldoProveedor;
                        angular.forEach(data, function (value) {
                            if (value.totalBancos == undefined) {
                                value.totalBancos = 0;
                            }
                            if (value.totalCaja == undefined) {
                                value.totalCaja = 0;
                            }
                            if (value.totalDevolucionesCeraApicola == undefined) {
                                value.totalDevolucionesCeraApicola = 0;
                            }
                            if (value.totalDevolucionesMiel == undefined) {
                                value.totalDevolucionesMiel = 0;
                            }
                            if (value.totalEgresosCeraApicola == undefined) {
                                value.totalEgresosCeraApicola = 0;
                            }
                            if (value.totalGastos == undefined) {
                                value.totalGastos = 0;
                            }
                            if (value.totalRetenciones == undefined) {
                                value.totalRetenciones = 0;
                            }
                            if (value.totalImporte == undefined) {
                                value.totalImporte = 0;
                            }
                            if (value.totalIngresosCeraApicola == undefined) {
                                value.totalIngresosCeraApicola = 0;
                            }
                            if (value.totalKilos == undefined) {
                                value.totalKilos = 0;
                            }
                            if (value.totalPrecio == undefined) {
                                value.totalPrecio = 0;
                            }
                            if (value.totalVentasCeraApicola == undefined) {
                                value.totalVentasCeraApicola = 0;
                            }
                            if (value.totalSaldo == undefined) {
                                value.totalSaldo = parseFloat(value.cantidad);
                            }
                            $scope.cantidadTotalBancos += parseFloat(value.totalBancos);
                            $scope.cantidadTotalEfectivo += parseFloat(value.totalCaja);
                            $scope.cantidadTotalIngresosCeraApicola += parseFloat(value.totalIngresosCeraApicola);
                            $scope.cantidadTotalKilosMiel += parseFloat(value.totalKilos);
                            $scope.cantidadTotalImporteMiel += parseFloat(value.totalImporte);
                            $scope.cantidadTotalPrecioPromedio = $scope.cantidadTotalImporteMiel / $scope.cantidadTotalKilosMiel;
                            $scope.cantidadTotalEgresosCeraApicola += parseFloat(value.totalEgresosCeraApicola);
                            $scope.cantidadTotalGastos += parseFloat(value.totalGastos);
                            $scope.cantidadTotalRetenciones += parseFloat(value.totalRetenciones);
                            $scope.cantidadTotalDevolucionesEfectivo += parseFloat(value.totalDevolucionesMiel);
                            $scope.cantidadTotalDevolucionesCeraApicola += parseFloat(value.totalDevolucionesCeraApicola);
                            $scope.cantidadTotalVentasCeraApicola += parseFloat(value.totalVentasCeraApicola);
                            $scope.cantidadTotalGranTotal += parseFloat(value.totalSaldo);
                        });
                    } else {
                        growl.info("No se encuentran proveedores activos");
                    }
                    $scope.cargandoDatos = false;
                });
            } else {
                var url = 'deudoresProveedores/php/deudoresProveedores.php?idMes=' + opcionCuenta.mes + '&parametro=' + opcionCuenta.opcionDeudor;
                if (opcionCuenta.comprador) {
                    url += '&idComprador=' + opcionCuenta.comprador;
                }
                $http.get(url).success(function (data) {
                    if (data.length > 0) {

                        angular.forEach(data, function (value) {

                            if (value.totalSaldo === 0) {
                                value.saldoDeudor = 0;
                                value.saldoProveedor = 0;
                            }
                            // else if (value.cantidad == 0) {
                            //     value.saldoDeudor = 0;
                            //     value.saldoProveedor = 0;
                            // }
                            else if (value.totalSaldo > 0) {
                                value.saldoDeudor = parseFloat(value.totalSaldo);
                                value.saldoProveedor = 0;
                                $scope.totalSaldoDeudor += parseFloat(value.saldoDeudor);
                            } else if (value.totalSaldo < 0) {
                                value.saldoProveedor = parseFloat(value.totalSaldo);
                                value.saldoDeudor = 0;
                                $scope.totalSaldoProveedor += parseFloat(value.saldoProveedor);
                            } else if (value.cantidad > 0) {
                                value.saldoProveedor = 0;
                                value.saldoDeudor = parseFloat(value.cantidad);
                                $scope.totalSaldoDeudor += parseFloat(value.saldoDeudor);
                            } else if (value.cantidad < 0) {
                                value.saldoProveedor = parseFloat(value.cantidad);
                                value.saldoDeudor = 0;
                                $scope.totalSaldoProveedor += parseFloat(value.saldoProveedor);
                            } else {
                                value.saldoDeudor = 0;
                                value.saldoProveedor = 0;
                            }
                        });
                        $scope.conciliacion = $scope.totalSaldoDeudor + $scope.totalSaldoProveedor;

                        $scope.arrayDeudores = data;
                        angular.forEach(data, function (value) {

                            if (value.totalBancos == undefined) {
                                value.totalBancos = 0;
                            }
                            if (value.totalCaja == undefined) {
                                value.totalCaja = 0;
                            }
                            if (value.totalDevolucionesCeraApicola == undefined) {
                                value.totalDevolucionesCeraApicola = 0;
                            }
                            if (value.totalDevolucionesMiel == undefined) {
                                value.totalDevolucionesMiel = 0;
                            }
                            if (value.totalEgresosCeraApicola == undefined) {
                                value.totalEgresosCeraApicola = 0;
                            }
                            if (value.totalGastos == undefined) {
                                value.totalGastos = 0;
                            }
                            if (value.totalRetenciones == undefined) {
                                value.totalRetenciones = 0;
                            }
                            if (value.totalImporte == undefined) {
                                value.totalImporte = 0;
                            }
                            if (value.totalIngresosCeraApicola == undefined) {
                                value.totalIngresosCeraApicola = 0;
                            }
                            if (value.totalKilos == undefined) {
                                value.totalKilos = 0;
                            }
                            if (value.totalPrecio == undefined) {
                                value.totalPrecio = 0;
                            }
                            if (value.totalVentasCeraApicola == undefined) {
                                value.totalVentasCeraApicola = 0;
                            }
                            if (value.totalSaldo == undefined) {
                                value.totalSaldo = parseFloat(value.cantidad);
                            }

                            $scope.cantidadTotalBancos += parseFloat(value.totalBancos);
                            $scope.cantidadTotalEfectivo += parseFloat(value.totalCaja);
                            $scope.cantidadTotalIngresosCeraApicola += parseFloat(value.totalIngresosCeraApicola);
                            $scope.cantidadTotalKilosMiel += parseFloat(value.totalKilos);
                            $scope.cantidadTotalImporteMiel += parseFloat(value.totalImporte);
                            $scope.cantidadTotalPrecioPromedio = $scope.cantidadTotalImporteMiel / $scope.cantidadTotalKilosMiel;
                            $scope.cantidadTotalEgresosCeraApicola += parseFloat(value.totalEgresosCeraApicola);
                            $scope.cantidadTotalGastos += parseFloat(value.totalGastos);
                            $scope.cantidadTotalRetenciones += parseFloat(value.totalRetenciones);
                            $scope.cantidadTotalDevolucionesEfectivo += parseFloat(value.totalDevolucionesMiel);
                            $scope.cantidadTotalDevolucionesCeraApicola += parseFloat(value.totalDevolucionesCeraApicola);
                            $scope.cantidadTotalVentasCeraApicola += parseFloat(value.totalVentasCeraApicola);
                            $scope.cantidadTotalGranTotal += parseFloat(value.totalSaldo);
                        });
                    } else {
                        growl.info("No se encuentran proveedores activos");
                    }
                    $scope.cargandoDatos = false;
                });
            }
        }
    };


    if ($location.path() == '/estadosCuentas') {
        $scope.verEstadoCuenta($scope.opcionCuenta);
        $http.get('almacen/php/consultaComboMeses.php').success(function (data) {
            $scope.meses = data;
        });
        $http.get('compras/php/comprador/listaComprador.php').success(function (arrayComprador) {
            $scope.listaCompradores = arrayComprador;
        });
    }

    $scope.imprimirEstadoCuenta = function (idProveedor) {
        return window.location.href = 'reportes/deudoresProveedores/xlsEstadoDeCuenta.php?idProveedor=' + idProveedor;
    }
    /* -------------------------------------- */

    if ($location.path() == '/concentradoDeudor') {

        $scope.verEstadoCuenta($scope.opcionCuenta);
        $http.get('almacen/php/consultaComboMeses.php').success(function (data) {
            $scope.meses = data;
        });
        $http.get('compras/php/comprador/listaComprador.php').success(function (arrayComprador) {
            $scope.listaCompradores = arrayComprador;
        });



        // $http.post('deudoresProveedores/php/deudoresProveedores.php?acumulado=1&parametro=todos').success(function (data) {
        //     if (data.length > 0) {
        //         angular.forEach(data, function (value) {
        //             if (value.totalSaldo > 0) {
        //                 value.saldoDeudor = value.totalSaldo;
        //                 value.saldoProveedor = 0;
        //                 $scope.totalSaldoDeudor += parseFloat(value.saldoDeudor);
        //             } else if (value.totalSaldo < 0) {
        //                 value.saldoProveedor = value.totalSaldo;
        //                 value.saldoDeudor = 0;
        //                 $scope.totalSaldoProveedor += parseFloat(value.saldoProveedor);
        //             } else if (value.cantidad > 0) {
        //                 value.saldoProveedor = 0;
        //                 value.saldoDeudor = value.cantidad;
        //             } else if (value.cantidad < 0) {
        //                 value.saldoProveedor = value.cantidad;
        //                 value.saldoDeudor = 0;
        //             } else {
        //                 value.saldoDeudor = 0;
        //                 value.saldoProveedor = 0;
        //             }
        //         });
        //         $scope.concentradoDeudores = data;
        //         $scope.conciliacion = $scope.totalSaldoDeudor + $scope.totalSaldoProveedor;
        //     } else {
        //         growl.info("No se encuentran proveedores");
        //     }
        // })
    }

    //==========================================
    //             GASTOS REALIZADOS
    //==========================================
    $scope.proveedor = $routeParams.idProveedor;
    $scope.idGasto = $routeParams.idGasto;
    $scope.gastosRealizados = new Array();
    $scope.gastosDetalle = new Array();
    $scope.gastos = {};
    $scope.lstProveedores = {};
    $scope.nombreDeudor = 0;

    if ($location.path() == '/gastosRealizados') {
        $http.get('deudoresProveedores/php/traeMenuGastos.php').success(function (data) {
            $scope.gastosRealizados = data;
        });
    }

    if ($scope.idGasto > 0) {
        $http.get('proveedores/php/listaProveedores.php').success(function (data) {
            $scope.lstProveedores = data;
        });
        $http.get('deudoresProveedores/php/detalleGastoRealizado.php?idGasto=' + $scope.idGasto).success(function (data) {
            $scope.gastos = data;
            $scope.nombreDeudor = data.idProveedor;
        });
    } else if ($scope.idGasto == 0) {
        $http.get('proveedores/php/listaProveedores.php').success(function (data) {
            $scope.lstProveedores = data;
        });
    }

    if ($scope.proveedor > 0) {
        $http.get('deudoresProveedores/php/traeMenuGastosPorProveedor.php?idProveedor=' + $scope.proveedor).success(function (data) {
            $scope.nombreProveedor = data.nombre;
            $scope.gastosDetalle = data.gastosDetalle;
        });
    }

    $scope.guardarGastoRealizado = function () {
        if ($scope.idGasto == 0) {
            $scope.gastos.idProveedor = $scope.nombreDeudor;
            $scope.validado = $scope.validarGasto();
            if ($scope.validado == true) {
                var mes = $scope.gastos.fecha.split("-");
                $scope.gastos.idMes = mes[1];
                $http.post('deudoresProveedores/php/guardarGastoRealizado.php', $scope.gastos).success(function (data) {
                    swal("Exito!", "Registros Actualizados", "success");
                    return window.location.href = "#/menuGastosProveedor/" + $scope.gastos.idProveedor;
                });
            }
        } else {
            $scope.gastos.idProveedor = $scope.nombreDeudor;
            var mes = $scope.gastos.fecha.split("-");
            $scope.gastos.idMes = mes[1];
            $http.post('deudoresProveedores/php/guardarGastoRealizado.php?update=1', $scope.gastos).success(function (data) {
                swal("Exito!", "Registros Actualizados", "success");
                return window.location.href = "#/menuGastosProveedor/" + $scope.gastos.idProveedor;
            });
        }
    };

    $scope.validarGasto = function () {
        if ($scope.gastos.idProveedor == 0) {
            growl.error("Seleccione un nombre");
        } else if ($scope.gastos.concepto == undefined) {
            growl.error("Se requiere de una descripción/concepto");
        } else if ($scope.gastos.fecha == undefined) {
            growl.error("Se requiere una fecha");
        } else if ($scope.gastos.cantidad == undefined) {
            growl.error("Se requiere una cantidad");
        } else {
            $scope.validado = true;
        }
        return $scope.validado;
    };

    /* -------------------------------------- */

    $scope.verPrecios = function (entrada, origen) {
        $scope.id = entrada;
        if (origen == 'Entrada' || origen == 'EntradaCubeta') {
            $scope.tipo = 'Miel 100% Pura de Abeja';
        } else if (origen == 'Entrada Organico' || origen == 'Entrada Cubeta Organico') {
            $scope.tipo = 'Miel 100% Orgánica';
        } else if (origen == 'Entrada Mantequilla' || origen == 'Entrada Cubeta Mantequilla') {
            $scope.tipo = 'Miel 100% Mantequilla';
        } else if (origen == 'Entrada Altiplano' || origen == 'Entrada Cubeta Altiplano') {
            $scope.tipo = 'Miel 100% Altiplano';
        } else if (origen == 'Entrada Naranjo' || origen == 'Entrada Cubeta Naranjo') {
            $scope.tipo = 'Miel 100% Naranjo';
        } else if (origen == 'Entrada Aguacate' || origen == 'Entrada Cubeta Aguacate') {
            $scope.tipo = 'Miel 100% Aguacate';
        } else if (origen == 'Entrada Mezquite' || origen == 'Entrada Cubeta Mezquite') {
            $scope.tipo = 'Miel 100% Mezquite';
        }
        $http.post('deudoresProveedores/php/preciosAgrupadosPorEntrada.php?idEntrada=' + entrada + '&origen=' + origen).success(function (data) {
            $scope.preciosGrupo = data;
            $scope.importeTotal = 0;
            
            angular.forEach($scope.preciosGrupo, function (value) {
                $scope.importeTotal += parseFloat(value.importe);
            });
        });
        $("#preciosPorEntrada").modal();
    }

    // Reportes
    $scope.verPdf = function (idProveedor) {
        window.open('reportes/deudoresProveedores/pdfConcentradoDeudores.php?idProveedor=' + idProveedor);
    }
}]);