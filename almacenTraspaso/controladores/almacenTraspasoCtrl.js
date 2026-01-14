form.config(function ($routeProvider) {
    $routeProvider.when('/almacenTraspaso', {
        templateUrl: 'almacenTraspaso/almacenTraspaso.html',
        controller: 'almacenTraspasoCtrl'
    }).when('/detalleTraspaso/:idAlmacen/:tipoMiel', {
        templateUrl: 'almacenTraspaso/nuevoAlmacenTraspaso.html',
        controller: 'almacenTraspasoCtrl'
    })
});
form.controller('almacenTraspasoCtrl', function ($scope, busqueda, $http, $routeParams, growl, $rootScope, $location) {
    $scope.entrada = $routeParams.idAlmacen;
    $scope.tipoMiel = $routeParams.tipoMiel;
    $scope.listaTraspasos = new Array();
    $scope.detalle = new Array();
    $scope.cargandoDatos = false;
    $scope.opcionCosecha = '1';
    $scope.reporteEstado = '2';
    $scope.mielCosecha = {};
    $scope.mielCosecha.idTipoDeMiel = "";
    $scope.mielCosecha.tipoDeMiel = "";
    $scope.prove = {};
    $scope.showMessage = false; // Para mostrar el mensaje si no hay entradas
    var date = new Date();
    var _mes = date.getMonth() + 1;
    $scope.mostrarMes = _mes.toString();

    $http.get('almacen/php/consultaComboMeses.php').success(function (data) {
        $scope.listaDeMeses = data;
    });

    function traeEncabezados(val) {
        $scope.cargandoDatos = true;
        var url = '';
        url = 'almacenTraspaso/php/obtenerEncabezados.php';
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
                    $scope.listaTraspasos = info.resultado;
                }
            } else {
                growl.error('Error');
                console.error(info);
            }
            $scope.cargandoDatos = false;
        });
    }

    if ($location.path() == '/almacenTraspaso') {
        $scope.$watch('[opcionCosecha, mostrarMes, reporteEstado]', function (val) {
            if ($scope.opcionCosecha) {
                window.localStorage.setItem('ENTRADA_ALMACEN_TRASPASO', $scope.opcionCosecha);
                $scope.listaTraspasos = [];
                traeEncabezados(val);
            }
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
        }
    });

    $scope.obtenerProveedores = function (val) {
        if ($scope.idAlmacenista == 0) {
            val = val.idTipoDeMiel;
        }
        $http.post('proveedores/php/dameProveedores.php?todos=1&tipo=' + val + '&estado=0').success(function (info) {
            $scope.listaProveedor = info;
        });
    }

    $scope.obtenerDetalle = function () {
        $http.post("almacenTraspaso/php/obtenerDetalle.php?miel=" + $scope.tipoMiel + "&id=" + $scope.entrada)
            .success(function (respuesta) {
                $scope.prove.id = respuesta.idProveedor;
                $scope.prove.sagarpa = respuesta.idSagarpa;
                $scope.prove.localidad = respuesta.localidad;
                $scope.prove.folio = respuesta.folio;
                $scope.prove.totalCompra = respuesta.costosTotales;
                $scope.detalle = respuesta.almacenDetalle;
            });
    }

    $scope.modificarProveedor = function () {
        $http.post('almacenTraspaso/php/editarProveedor.php?miel=' + $scope.tipoMiel + '&idAlmacen=' + $scope.entrada + '&idProveedor=' + $scope.proveedorEditado.id)
            .success(function (respuesta) {
                $scope.obtenerDetalle();
                swal("¡Éxito!", "Registros Actualizados", "success");
            });
    };

    $scope.calcularNeto = function () {
        $scope.traspaso.neto = $scope.traspaso.bruto - $scope.traspaso.tara;
        $scope.calcularDiferencia();
    };
    $scope.calcularDiferencia = function () {
        $scope.traspaso.diferencia = $scope.traspaso.neto - $scope.traspaso.pesoLista;
    };

    $scope.cambiarEstado = function (traspaso) {
        $http.post("almacenTraspaso/php/cambiarEstadoAlmacen.php?valor=" + traspaso.estado + "&id=" + traspaso.idAlmacen + "&miel=" + $scope.tipoMiel)
            .success(function (respuesta) {
                console.log(respuesta)
                growl.success('Realizado');
            });
    };

    $scope.validarTodoMuestreo = function () {
        angular.forEach($scope.detalle, function (value, key) {
            value.estado = $scope.seleccionarTodo;
            $scope.cambiarEstado(value);
        });
    };

    if ($scope.entrada > 0) {
        if ($scope.tipoMiel == '1') {
            $scope.laMiel = "Miel 100% Pura de Abeja";
        } else if ($scope.tipoMiel == '2') {
            $scope.laMiel = "Miel 100% Orgánica";
        }
        $scope.obtenerProveedores($scope.tipoMiel);
        $scope.obtenerDetalle();
        $http.get("almacen/php/traeZonasDeTambores.php?tipoMiel=" + $scope.tipoMiel).success(function (info) {
            $scope.listaZonasTambos = info;
        });
        $http.post("almacenTraspaso/php/totalKgDiferencia.php?id=" + $scope.entrada + "&miel=" + $scope.tipoMiel).success(function (info) {
            $scope.totalKgBruto = info.totalKgBruto;
            $scope.totalKgTara = info.totalKgTara;
            $scope.totalKgNeto = info.totalKgNeto;
            $scope.totalKgDiferencia = info.totalKgDiferencia;
        });
    }

    if ($scope.entrada == 0) {
        $http.post("utilerias/php/traeTiposDeMiel.php").success(function (info) {
            if (!info.hasOwnProperty('error')) {
                $scope.listaCosecha = info;
            }
        });
    }

    $scope.cambiarProveedor = function () {
        $("#mdlEditarProveedor").modal();
    };

    $scope.validarAlmacen = function () {
        $scope.ok = false;
        if ($scope.traspaso.zona == 0) {
            growl.error("Se requiere una zona");
        } else
            if ($scope.traspaso.bruto == "") {
                growl.error("Se requiere un peso bruto");
            } else if ($scope.traspaso.tara == "") {
                growl.error("Se requiere una tara");
            } else if ($scope.traspaso.pesoLista == "") {
                growl.error("Se requiere un peso lista");
            } else {
                $scope.ok = true;
            }
        return $scope.ok;
    };

    $scope.validarInformacionDetalle = function () {
        var ok = true;
        angular.forEach($scope.detalle, function (value, key) {
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

    $scope.agregarAlmacen = function () {
        $scope.ok = $scope.validarAlmacen();
        if ($scope.ok == true) {
            if ($scope.entrada == 0) {
                $scope.detalle.push($scope.traspaso);
                growl.success("Nuevo pedido agregado");
            } else {
                if ($scope.prove.id == null) {
                    growl.info('Elija un proveedor');
                } else {
                    $http.post("almacenTraspaso/php/guardarTraspaso.php?miel=" + $scope.tipoMiel + "&id=" + $scope.entrada, { valor: $scope.traspaso })
                        .success(function (respuesta) {
                            if (typeof (respuesta) == 'object' && respuesta.hasOwnProperty('error')) {
                                if (respuesta.error) {
                                    growl.error(respuesta.message || 'No se han guardado los cambios');
                                } else {
                                    growl.success('Registro guardado');
                                    $scope.obtenerDetalle();
                                }
                            } else {
                                growl.error('Error');
                                console.error(respuesta);
                            }
                        });
                    $scope.traspaso = new Almacenista();
                }
            }
        }
    };

    $scope.actualizarTraspaso = function () {
        var ok = $scope.validarInformacionDetalle();
        if (ok == true) {
            $scope.bloquearGuardar = true;
            $http.post("almacenTraspaso/php/actualizarTraspaso.php?tipoMiel=" + $scope.tipoMiel, { valor: $scope.detalle })
                .success(function (respuesta) {
                    console.log(respuesta);
                    if (typeof (respuesta) == 'object' && respuesta.hasOwnProperty('error')) {
                        if (respuesta.error) {
                            swal('', respuesta.message || 'No se ha podido guardar los cambios', 'info');
                        } else {
                            swal("Éxito!", respuesta.message, "success");
                            $scope.bloquearGuardar = false;
                            return window.location.href = "#/almacenTraspaso";
                        }
                    } else {
                        growl.error('Error');
                        console.error(respuesta);
                    }
                });
        }

        $http.post("almacen/php/dameAlmacenes.php?idAlmacen=" + $scope.id).success(function (info) {
            $scope.listaAlmacenProveedor = info;
        });
    };

});