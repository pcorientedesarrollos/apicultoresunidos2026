form.config(function ($routeProvider) {
    $routeProvider.when('/lstSolicitudesCompra', {
        templateUrl: 'utilerias/solicitudes/solicitudesCompra.html',
        controller: 'solicitudesCompraCtrl'
    }).when('/solicitudCompra/:idSolicitudCompra', {
        templateUrl: 'utilerias/solicitudes/formularioCompra.html',
        controller: 'solicitudesCompraCtrl'
    })
});
form.controller('solicitudesCompraCtrl', ['$scope', '$http', '$routeParams', 'growl', '$location', '$q', '$rootScope', function ($scope, $http, $routeParams, growl, $location, $q, $rootScope) {

    $scope.idSolicitudCompra = $routeParams.idSolicitudCompra;
    $scope.nuevoConcepto = {};
    $scope.entrada = {
        conceptos: Array(),
        total: 0
    }
    $scope.cargandoDatos = false;
    var date = new Date();
    var _mes = date.getMonth() + 1;
    $scope.mostrarMes = _mes.toString();
    $scope.area = 0;

    // Ajustar la lista de cuentas
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

    $scope.cambioSeleccionCuenta = function (idCuenta = false) {
        // Cuando cambia la cuenta seleccionada, 
        // trae las subcuentas
        if (!idCuenta) {
            // si no manda la cuenta, la toma del scope, primero la verifica
            if ($scope.nuevoConcepto.cuenta && $scope.nuevoConcepto.cuenta.idCuentaConcepto) {
                traerListaSubcuentas($scope.nuevoConcepto.cuenta.idCuentaConcepto);
            }
        } else {
            // Si envia la cuenta, lo hace directo
            traerListaSubcuentas(idCuenta);
        }
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

    $scope.imprimirSolicitudCompra = function (idSolicitudCompra) {
        if (idSolicitudCompra) {
            window.open('reportes/administrativo/pdfSolicitudCompra.php?idSolicitudCompra=' + idSolicitudCompra);
        }
    }

    function obtenerSolicitudesCompra() {
        $scope.cargandoDatos = true;
        url = 'utilerias/solicitudes/php/obtenerSolicitudesCompraFiltrado.php?';
        if ($location.path() == '/lstSolicitudesCompra') {
            if ($scope.mostrarMes) {
                url += '&mes=' + $scope.mostrarMes;
            }
            if ($scope.area) {
                url += '&area=' + $scope.area;
            }
        }
        $http.get(url).success(function (data) {
            $scope.listaSolicitudes = data.data;
            if (data.error) {
                console.error(data.message);
            }
            $scope.cargandoDatos = false;
        });
    };

    $scope.$watch('[ mostrarMes, area]', function (val) {
        obtenerSolicitudesCompra();
    });

    $scope.$watch('entrada.departamento', function (areaSeleccionada) {
        $http.get('control/php/listaNombresOM.php?idArea=' + areaSeleccionada)
            .success(function (data) {
                $scope.listaPersonal = data;
            });
    }, true);

    function prepararNuevaSolicitud() {
        traerListaCuentas(1);
        $http.get('controlAdministrativo/php/conceptosIngreso.php').success(function (data) {
            $scope.listaConceptos = data;
        });
        $http.get('personalOaxacaMiel/php/listaAreas.php').success(function (datas) {
            $scope.listaAreas = datas;
        });
        $http.post('controlAdministrativo/php/traeCatalogoProveedores.php', true).success(function (data) {
            $scope.catalogoTablaProveedoresMantto = data.data;
        });
    };

    function guardarSolicitud(entrada) {
        $http.post("utilerias/solicitudes/php/guardarSolicitudCompra.php", entrada).success(function (info) {
            if (info.hasOwnProperty('error')) {
                if (!info.error) {
                    $rootScope.lockTemplate = false;
                    swal('', info.message, info.swal);
                    window.location.href = '#/lstSolicitudesCompra';
                } else {
                    swal('Error', info.message, 'error');
                }
            } else {
                growl.error('Error');
                console.error(info);
            }
        });
    };

    function validarNuevoConcepto(concepto) {
        if (!concepto.subcuentaObj) {
            growl.info('Seleccione el catálogo');
            return false;
        } else if (!concepto.cantidad) {
            growl.info('Indique la cantidad');
            return false;
        } else if (!concepto.descripcion) {
            growl.info('La descripción no puede ser vacío');
            return false;
        }
        return true;
    };

    function verificarSolicitud(entrada) {
        if (!entrada.fecha) {
            growl.info('Selecciona la fecha de entrada');
            return false;
        } else if (!entrada.departamento || entrada.departamento == '0') {
            growl.info('Seleccione el departamento');
            return false;
        } else if (!entrada.solicita || entrada.solicita == '0') {
            growl.info('Seleccione quien solicita');
            return false;
        } else if (entrada.conceptos.length < 1) {
            growl.info('La solicitud debe contener al menos 1 concepto');
            return false;
        }
        return true;
    };

    function obtenerDetalleSolicitud(idSolicitudCompra) {
        $http.get('utilerias/solicitudes/php/obtenerSolicitudCompra.php?idSolicitudCompra=' + idSolicitudCompra).success(function (data) {
            if (data.hasOwnProperty('error')) {
                if (!data.error) {
                    $scope.entrada = data.data;
                } else {
                    swal('Error', data.message, 'error');
                }
            } else {
                growl.error('Error');
                console.error(data);
            }
        });
    };

    $scope.agregarConceptoCompra = function () {
        // Esta función agrega un nuevo concepto a la tabla
        if (validarNuevoConcepto($scope.nuevoConcepto)) {

            $scope.nuevoConcepto.idMovimiento = $scope.nuevoConcepto.cuenta && $scope.nuevoConcepto.cuenta.idCuentaConcepto ? $scope.nuevoConcepto.cuenta.idCuentaConcepto : null;
            $scope.nuevoConcepto.movimiento = $scope.nuevoConcepto.cuenta && $scope.nuevoConcepto.cuenta.cuenta ? $scope.nuevoConcepto.cuenta.cuenta : '';

            $scope.nuevoConcepto.idSubcuenta = $scope.nuevoConcepto.subcuentaObj.idSubcuenta ? $scope.nuevoConcepto.subcuentaObj.idSubcuenta : null;
            $scope.nuevoConcepto.subcuenta = $scope.nuevoConcepto.subcuentaObj.subcuenta ? $scope.nuevoConcepto.subcuentaObj.subcuenta : '';

            $scope.nuevoConcepto.idConcepto = $scope.nuevoConcepto.producto && $scope.nuevoConcepto.producto.idSubSubcuenta ? $scope.nuevoConcepto.producto.idSubSubcuenta : null;
            $scope.nuevoConcepto.concepto = $scope.nuevoConcepto.producto && $scope.nuevoConcepto.producto.subSubcuenta ? $scope.nuevoConcepto.producto.subSubcuenta : '';

            $scope.nuevoConcepto.idProveedor = $scope.nuevoConcepto.proveedor && $scope.nuevoConcepto.proveedor.idProveedorMantto ? $scope.nuevoConcepto.proveedor.idProveedorMantto : null;
            $scope.nuevoConcepto.proveedor = $scope.nuevoConcepto.proveedor && $scope.nuevoConcepto.proveedor.nombreProveedor ? $scope.nuevoConcepto.proveedor.nombreProveedor : '';

            $scope.entrada.conceptos.push($scope.nuevoConcepto);
            $scope.nuevoConcepto = {};
            // calcularTotalEntrada();
        } else {
            growl.error('Verifica los campos necesarios');
        }
    };

    $scope.guardarSolicitudCompra = function () {
        if (verificarSolicitud($scope.entrada)) {
            guardarSolicitud($scope.entrada);
        }
    };

    $scope.eliminarConceptoEntradaDerivados = function (index) {
        $scope.entrada.conceptos.splice(index, 1);
    };


    // $scope.imprimirReporte = function () {
    //     if ($scope.idSolicitudCompra) {
    //         window.open('reportes/almacen/pdfReporteApicola.php?idSolicitudCompra=' + $scope.idSolicitudCompra);
    //     }
    // }

    if ($location.path() == '/lstSolicitudesCompra') {
        obtenerSolicitudesCompra();
        $http.get('almacen/php/consultaComboMeses.php').success(function (data) {
            $scope.listaDeMeses = data;
        });
        $http.get('personalOaxacaMiel/php/listaAreas.php').success(function (datas) {
            $scope.listaAreas = datas;
        });
    } else if ($scope.idSolicitudCompra == 0) {
        traerListaCuentas(1);
        $scope.$watch('[entrada.conceptos, nuevoConcepto]', function () {
            if (angular.equals($scope.nuevoConcepto, $scope.entrada.conceptos.length == 0 || $scope.entrada.idSolicitudCompra)) {
                $rootScope.lockTemplate = false;
            } else {
                $rootScope.lockTemplate = true;
            }
        }, true);

        prepararNuevaSolicitud();
    } else if ($scope.idSolicitudCompra > 0) {
        prepararNuevaSolicitud();
        obtenerDetalleSolicitud($scope.idSolicitudCompra);
    }

}]);