form.config(function ($routeProvider) {
    $routeProvider.when('/aprobarPrecios', {
        templateUrl: 'administrador/preciosAprobados/preciosTambos.html',
        controller: 'aprobarPreciosCtrl'
    }).when('/precioTambos/:id/:mielT', {
        templateUrl: 'administrador/preciosAprobados/asignarPreciosTambo.html',
        controller: 'aprobarPreciosCtrl'
    }).when('/aprobarCubetas', {
        templateUrl: 'administrador/preciosAprobados/preciosCubetas.html',
        controller: 'aprobarPreciosCtrl'
    }).when('/precioCubetas/:idCubeta/:mielC', {
        templateUrl: 'administrador/preciosAprobados/asignarPreciosCubeta.html',
        controller: 'aprobarPreciosCtrl'
    })
});

form.controller('aprobarPreciosCtrl', ['$scope', '$http', '$routeParams', 'growl', '$location', function ($scope, $http, $routeParams, growl, $location) {
    // Tambos
    $scope.reporteEstado = '1';
    $scope.opcionCosecha = '1';
    $scope.cargandoDatos = false;
    var date = new Date();
    var _mes = date.getMonth() + 1;
    $scope.mostrarMes = _mes.toString();
    $scope.idTambo = $routeParams.id;
    $scope.mielTambo = $routeParams.mielT;
    $scope.prove = {};
    // -----
    // Cubetas
    $scope.opcionCosechaCub = '1';
    $scope.idCubeta = $routeParams.idCubeta;
    $scope.mielCubeta = $routeParams.mielC;
    $scope.prove2 = {};
    var date1 = new Date();
    var _mes1 = date1.getMonth() + 1;
    $scope.mostrarMes1 = _mes1.toString();
    // -----

    $http.get('almacen/php/consultaComboMeses.php').success(function (data) {
        $scope.listaDeMeses = data;
    });

    /*** T A M  B O R E S ***/
    function traerAlmacen(val) {
        $scope.cargandoDatos = true;
        var url = '';
        url = 'almacen/php/dameAlmacenes.php';
        if ($scope.mostrarMes) {
            url += '?mes=' + $scope.mostrarMes;
        }
        $http.post(url, val).success(function (info) {
            console.log(info);
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
                    if ($location.path() == '/aprobarPrecios') {
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
    //------------------------------
    /*** C U B E T A S ***/
    function traeCubetas(tipoDeMiel) {
        var url = '';
        url = 'precios/php/traeCubetas.php?miel=' + tipoDeMiel;
        if ($scope.mostrarMes1) {
            url += '&mes=' + $scope.mostrarMes1;
        }
        $http.post(url).success(function (info) {
            $scope.respaldoListaCubetas = info;
            if ($scope.respaldoListaCubetas) {
                $scope.filtrarPagos($scope.filtroPagado);
            }
        });
    }
    //-------------------------------
    if ($location.path() == '/aprobarPrecios') {
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
    } else if ($location.path() == '/aprobarCubetas') {
        if (window.localStorage.getItem('miel') != null) {
            $scope.opcionCosechaCub = window.localStorage.getItem('miel');
        }
        $scope.$watch('opcionCosechaCub', function (tipoMiel) {
            if (tipoMiel) {
                window.localStorage.setItem('opcionCosechaCub', tipoMiel);
                traeCubetas(tipoMiel);
            }
        });
        if (window.localStorage.getItem('mesElegido') != null) {
            $scope.mostrarMes1 = window.localStorage.getItem('mesElegido');
        }
        $scope.$watch('mostrarMes1', function (mesElegido) {
            window.localStorage.setItem('mesElegido', mesElegido);
            traeCubetas($scope.opcionCosechaCub);
        });
        if (window.localStorage.getItem('filtro') != null) {
            $scope.filtroPagado = window.localStorage.getItem('filtro');
        }
        $scope.$watch('filtroPagado', function (filtroP) {
            window.localStorage.setItem('filtro', filtroP);
            $scope.filtrarPagos(filtroP);
        });
    }

    /*** T A M B O S ***/
    $scope.filtrarPagosTambores = function (filtro) {
        $scope.nuevoArreglo = [];
        if (filtro === '1') {
            $scope.respaldoLista.forEach(pago => { //Pagados
                if (pago.totalCompra != "0.00") {
                    $scope.nuevoArreglo.push(pago);
                }
            });
            if ($scope.nuevoArreglo.length == 0) {
                growl.info('No hay registros pagados');
            }
            $scope.lista = $scope.nuevoArreglo;
        } else if (filtro === '2') {
            $scope.respaldoLista.forEach(cheque => { //No pagados
                if (cheque.totalCompra == "0.00") {
                    $scope.nuevoArreglo.push(cheque);
                }
            });
            if ($scope.nuevoArreglo.length == 0) {
                growl.info('No hay registros sin pagar');
            }
            $scope.lista = $scope.nuevoArreglo;
        } else if (filtro === '3') { // Todos
            $scope.lista = $scope.respaldoLista;
        }
    }

    $scope.traeProv = function (val) {
        if ($scope.idTambo == 0) {
            val = val.idTipoDeMiel;
        }
        $http.post('proveedores/php/dameProveedores.php?todos=1&tipo=' + val + '&estado=0').success(function (info) {
            $scope.listaProveedor = info;
        });
    }

    $scope.obtenerTambores = function () {
        $http.get('precios/php/infoAlmacen.php?tipo=' + $scope.mielTambo + '&id=' + $scope.idTambo).success(function (data) {
            $scope.almacenn = data;
            $scope.prove.totalNeto = 0;
            for (d in data) {
                $scope.prove.totalNeto += parseInt(data[d].neto);
            }
            $scope.dameTotal();
        });
        $http.post("almacen/php/dameAlmacen.php?miel=" + $scope.mielTambo + "&id=" + $scope.idTambo)
            .success(function (respuesta) {
                $scope.prove.id = respuesta.idProveedor;
                $scope.prove.sagarpa = respuesta.idSagarpa;
                $scope.prove.localidad = respuesta.localidad;
                $scope.prove.folio = respuesta.folio;
                $scope.prove.totalCompra = respuesta.costosTotales;
            });

        var urlDiferencia = "almacen/php/totalKgDiferencia.php?id=" + $scope.idTambo;
        if ($scope.mielTambo == '2') {
            urlDiferencia += '&organica';
        }
        $http.post(urlDiferencia).success(function (info) {
            $scope.totalKgBruto = info.totalKgBruto;
            $scope.totalKgTara = info.totalKgTara;
            $scope.totalKgNeto = info.totalKgNeto;
            $scope.totalKgDiferencia = info.totalKgDiferencia;
        });

    }

    if ($scope.idTambo > 0) {
        $scope.traeProv($scope.mielTambo);
        $scope.obtenerTambores();
    }

    $scope.obeterInfoAlmacenProveedor = function () {
        $scope.id = $scope.prove.id;
        $http.post("almacen/php/dameAlmacenes.php?idProveedor=" + $scope.id).success(function (info) {
            $scope.listaAlmacenProveedor = info;
        });
    };

    $scope.obtenerValor = function (parametro) {
        angular.forEach($scope.almacenn, function (value, key) {
            if (parametro == 1) {
                value.precio = $scope.precio;
            }
        });
    };

    $scope.dameTotal = function () {
        $scope.prove.totalCompra = 0;
        angular.forEach($scope.almacenn, function (value, key) {
            $scope.prove.totalCompra += parseFloat(value.costoTotal);
        });
    }

    $scope.guardarPrecio = function () {
        if ($scope.prove.folio == 0) {
            swal("Error!", "Ingrese folio!", "error");
        } else {
            $http.post("precios/php/guardarPrecio.php?miel=" + $scope.mielTambo + "&folio=" + $scope.prove.folio + "&totalCompra=" + $scope.prove.totalCompra + "&id=" + $scope.idTambo, { valor: $scope.almacenn }, $scope.idTambo).success(function (data) {
                if (typeof (data) == 'object' && data.hasOwnProperty('error')) {
                    if (data.error) {
                        swal('', data.message || 'No se ha podido guardar los cambios', 'info');
                    } else {
                        swal("Éxito!", data.message, "success");
                        return window.location.href = "#/aprobarPrecios";
                    }
                } else {
                    growl.error('Error');
                    console.error(respuesta);
                }
            });
        }
    };

    $scope.aprobarPrecioTambo = function (infoTambo) {
        $http.post("administrador/php/aprobarPrecio.php?tambo=1&miel=" + $scope.mielTambo + "&id=" + infoTambo.idAlmacen + "&valor=" + infoTambo.aprobado)
            .success(function (respuesta) {
                growl.success('Realizado');
            });
    };

    $scope.aprobarTodo = function () {
        angular.forEach($scope.almacenn, function (value, key) {
            value.aprobado = $scope.seleccionarTodo;
            $scope.aprobarPrecioTambo(value);
        });
    };

    $scope.cambiarEstado = function (detalle) {
        $http.post("administrador/php/aprobarPrecio.php?tambo=1&miel=" + $scope.opcionCosecha + "&valor=" + detalle.estado + "&entrada=" + detalle.folio)
            .success(function (respuesta) {
                growl.success('Realizado');
            });
    }

    $scope.cambiarEstadoCubeta = function (detalle) {
        $http.post("administrador/php/aprobarPrecio.php?cubeta=1&miel=" + $scope.opcionCosecha + "&valor=" + detalle.estado + "&entrada=" + detalle.id)
            .success(function (respuesta) {
                growl.success('Realizado');
            });
    }

    // $scope.aprobarPrecioTambo = function (infoTambo, indice) {
    //     if (infoTambo.aprobado == '1') {
    //         // Seleccionó el checkbox cobrado, preguntar si quiere guardarlo así
    //         swal({
    //             title: "¿Marcar el precio como aprobado?",
    //             text: "Una vez aprobado no lo podrá revertir",
    //             type: "warning",
    //             showCancelButton: true,
    //             confirmButtonColor: "#DD6B55",
    //             confirmButtonText: "Marcar aprobado",
    //             cancelButtonText: "Cancelar",
    //         }, function (marcar) {
    //             if (marcar) {
    //                 // Si quiere marcar, mandarlo guardado en la base de datos
    //                 $http.get("administrador/php/aprobarPrecio.php?tambo=1&miel=" + $scope.mielTambo + "&id=" + infoTambo.idAlmacen).success(function (res) {
    //                     if (typeof (res) == 'object' && res.hasOwnProperty('error')) {
    //                         if (res.error) {
    //                             growl.error('Ocurrió un error');
    //                         } else {
    //                             obtenerTambores();
    //                             growl.success('Registro actualizado');
    //                         }
    //                     } else {
    //                         growl.error('Error');
    //                         console.error(res);
    //                     }
    //                 });
    //             } else {
    //                 // si no, lo desmarcamos
    //                 $scope.almacenn[indice].aprobado = '0';
    //                 $scope.$apply();
    //             }
    //         });
    //     }
    // }

    //---------------------------------------------------------

    /*** C U B E T A S  ***/
    $scope.filtrarPagos = function (filtro) {
        $scope.nuevoArreglo = [];
        if (filtro == '1') {
            $scope.respaldoListaCubetas.forEach(pago => { //Pagados
                if (pago.totalCompra != "0.00") {
                    $scope.nuevoArreglo.push(pago);
                }
            });
            if ($scope.nuevoArreglo.length == 0) {
                growl.info('No hay registros pagados');
            }
            $scope.cubeta = $scope.nuevoArreglo;
        } else if (filtro == '2') {
            $scope.respaldoListaCubetas.forEach(cheque => { //No pagados
                if (cheque.totalCompra == "0.00") {
                    $scope.nuevoArreglo.push(cheque);
                }
            });
            if ($scope.nuevoArreglo.length == 0) {
                growl.info('No hay registros sin pagar');
            }
            $scope.cubeta = $scope.nuevoArreglo;
        } else if (filtro == '3') { // Todos
            $scope.cubeta = $scope.respaldoListaCubetas;
        }
    }

    if ($scope.idCubeta > 0) {
        if ($scope.mielCubeta == 1) {
            $http.post("precios/php/dameAlmacenesProveedor.php?id=" + $scope.idCubeta)
                .success(function (respuesta) {
                    $scope.prove2.id = respuesta.idProveedor;
                    $scope.prove2.idAlmacen = respuesta.idAlmacen;
                    $scope.prove2.proveedor = respuesta.proveedor;
                    $scope.prove2.sagarpa = respuesta.idSagarpa;
                    $scope.prove2.localidad = respuesta.localidad;
                    $scope.prove2.folioEntradaTambor = respuesta.folioEntradaTambor;
                    $scope.prove2.totalCompra = respuesta.costosTotales;
                });
            $http.get('precios/php/infoCubetas.php?id=' + $scope.idCubeta).success(function (data) {
                $scope.cubetaMiel = data;
                $scope.dameTotalCub();
            });
        } else if ($scope.mielCubeta == 5) {
            $http.post("precios/php/dameAlmacenesProveedor.php?organicam=0&id=" + $scope.idCubeta)
            .success(function (respuesta) {
                $scope.prove2.id = respuesta.idProveedor;
                $scope.prove2.idAlmacen = respuesta.idAlmacen;
                $scope.prove2.proveedor = respuesta.proveedor;
                $scope.prove2.sagarpa = respuesta.idSagarpa;
                $scope.prove2.localidad = respuesta.localidad;
                $scope.prove2.folioEntradaTambor = respuesta.folioEntradaTambor;
                $scope.prove2.totalCompra = respuesta.costosTotales;
            });
        $http.get('precios/php/infoCubetas.php?organicam=0&id=' + $scope.idCubeta).success(function (data) {
            $scope.cubetaMiel = data;
            $scope.dameTotalCub();
        });
        }  else if ($scope.mielCubeta == 6) {
            $http.post("precios/php/dameAlmacenesProveedor.php?organicaa=0&id=" + $scope.idCubeta)
            .success(function (respuesta) {
                $scope.prove2.id = respuesta.idProveedor;
                $scope.prove2.idAlmacen = respuesta.idAlmacen;
                $scope.prove2.proveedor = respuesta.proveedor;
                $scope.prove2.sagarpa = respuesta.idSagarpa;
                $scope.prove2.localidad = respuesta.localidad;
                $scope.prove2.folioEntradaTambor = respuesta.folioEntradaTambor;
                $scope.prove2.totalCompra = respuesta.costosTotales;
            });
        $http.get('precios/php/infoCubetas.php?organicaa=0&id=' + $scope.idCubeta).success(function (data) {
            $scope.cubetaMiel = data;
            $scope.dameTotalCub();
        });
        } else if ($scope.mielCubeta == 7) {
            $http.post("precios/php/dameAlmacenesProveedor.php?organican=0&id=" + $scope.idCubeta)
            .success(function (respuesta) {
                $scope.prove2.id = respuesta.idProveedor;
                $scope.prove2.idAlmacen = respuesta.idAlmacen;
                $scope.prove2.proveedor = respuesta.proveedor;
                $scope.prove2.sagarpa = respuesta.idSagarpa;
                $scope.prove2.localidad = respuesta.localidad;
                $scope.prove2.folioEntradaTambor = respuesta.folioEntradaTambor;
                $scope.prove2.totalCompra = respuesta.costosTotales;
            });
        $http.get('precios/php/infoCubetas.php?organican=0&id=' + $scope.idCubeta).success(function (data) {
            $scope.cubetaMiel = data;
            $scope.dameTotalCub();
        });
        }
        else if ($scope.mielCubeta == 8) {
            $http.post("precios/php/dameAlmacenesProveedor.php?organicaagua=0&id=" + $scope.idCubeta)
            .success(function (respuesta) {
                $scope.prove2.id = respuesta.idProveedor;
                $scope.prove2.idAlmacen = respuesta.idAlmacen;
                $scope.prove2.proveedor = respuesta.proveedor;
                $scope.prove2.sagarpa = respuesta.idSagarpa;
                $scope.prove2.localidad = respuesta.localidad;
                $scope.prove2.folioEntradaTambor = respuesta.folioEntradaTambor;
                $scope.prove2.totalCompra = respuesta.costosTotales;
            });
        $http.get('precios/php/infoCubetas.php?organicaagua=0&id=' + $scope.idCubeta).success(function (data) {
            $scope.cubetaMiel = data;
            $scope.dameTotalCub();
        });
        }
        else if ($scope.mielCubeta == 9) {
            $http.post("precios/php/dameAlmacenesProveedor.php?organicame=0&id=" + $scope.idCubeta)
            .success(function (respuesta) {
                $scope.prove2.id = respuesta.idProveedor;
                $scope.prove2.idAlmacen = respuesta.idAlmacen;
                $scope.prove2.proveedor = respuesta.proveedor;
                $scope.prove2.sagarpa = respuesta.idSagarpa;
                $scope.prove2.localidad = respuesta.localidad;
                $scope.prove2.folioEntradaTambor = respuesta.folioEntradaTambor;
                $scope.prove2.totalCompra = respuesta.costosTotales;
            });
        $http.get('precios/php/infoCubetas.php?organicame=0&id=' + $scope.idCubeta).success(function (data) {
            $scope.cubetaMiel = data;
            $scope.dameTotalCub();
        });
        }
         else {
            $http.post("precios/php/dameAlmacenesProveedor.php?organica=0&id=" + $scope.idCubeta)
                .success(function (respuesta) {
                    $scope.prove2.id = respuesta.idProveedor;
                    $scope.prove2.idAlmacen = respuesta.idAlmacen;
                    $scope.prove2.proveedor = respuesta.proveedor;
                    $scope.prove2.sagarpa = respuesta.idSagarpa;
                    $scope.prove2.localidad = respuesta.localidad;
                    $scope.prove2.folioEntradaTambor = respuesta.folioEntradaTambor;
                    $scope.prove2.totalCompra = respuesta.costosTotales;
                });
            $http.get('precios/php/infoCubetas.php?organica=0&id=' + $scope.idCubeta).success(function (data) {
                $scope.cubetaMiel = data;
                $scope.dameTotalCub();
            });
        }
    }

    $scope.dameTotalCub = function () {
        $scope.prove2.totalCompra = 0;
        angular.forEach($scope.cubetaMiel, function (value, key) {
            $scope.prove2.totalCompra += parseFloat(value.costoTotal);
        });
    };

    $scope.guardarPrecioCub = function () {
        $http.post("precios/php/guardarPrecioCubetas.php?miel=" + $scope.mielCubeta + "&totalCompra=" + $scope.prove2.totalCompra + "&id=" + $scope.idCubeta, { valor: $scope.cubetaMiel }, $scope.idCubeta).success(function (data) {
            if (typeof (data) == 'object' && data.hasOwnProperty('error')) {
                if (data.error) {
                    swal('', data.message || 'No se ha podido guardar los cambios', 'info');
                } else {
                    swal("Éxito!", data.message, "success");
                    return window.location.href = "#/aprobarCubetas";
                }
            } else {
                growl.error('Error');
                console.error(data);
            }
        });
    };

    $scope.aprobarPrecioCubetas = function (infoCubeta) {
        $http.post("administrador/php/aprobarPrecio.php?cubeta=1&miel=" + $scope.mielCubeta + "&id=" + infoCubeta.idAlmacen + "&valor=" + infoCubeta.aprobado)
            .success(function (respuesta) {
                growl.success('Realizado');
            });
    };

    $scope.aprobarTodoCubetas = function () {
        angular.forEach($scope.cubetaMiel, function (value, key) {
            value.aprobado = $scope.seleccionarTodo;
            $scope.aprobarPrecioCubetas(value);
        });
    };

    // $scope.aprobarPrecioCubetas = function (infoCubeta, indice) {
    //     if (infoCubeta.aprobado == '1') {
    //         // Seleccionó el checkbox cobrado, preguntar si quiere guardarlo así
    //         swal({
    //             title: "¿Marcar el precio como aprobado?",
    //             text: "Una vez aprobado no lo podrá revertir",
    //             type: "warning",
    //             showCancelButton: true,
    //             confirmButtonColor: "#DD6B55",
    //             confirmButtonText: "Marcar aprobado",
    //             cancelButtonText: "Cancelar",
    //         }, function (marcar) {
    //             if (marcar) {
    //                 // Si quiere marcar, mandarlo guardado en la base de datos
    //                 $http.get("administrador/php/aprobarPrecio.php?cubeta=1&miel=" + $scope.mielCubeta + "&id=" + infoCubeta.idAlmacen).success(function (res) {
    //                     if (typeof (res) == 'object' && res.hasOwnProperty('error')) {
    //                         if (res.error) {
    //                             growl.error('Ocurrió un error');
    //                         } else {
    //                             obtenerTambores();
    //                             growl.success('Registro actualizado');
    //                         }
    //                     } else {
    //                         growl.error('Error');
    //                         console.error(res);
    //                     }
    //                 });
    //             } else {
    //                 // si no, lo desmarcamos
    //                 $scope.cubetaMiel[indice].aprobado = '0';
    //                 $scope.$apply();
    //             }
    //         });
    //     }
    // }

}]);


