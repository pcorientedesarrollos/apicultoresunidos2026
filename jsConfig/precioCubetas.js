form.controller('precioCubeta', ['$scope', '$http', '$routeParams', 'growl', function ($scope, $http, $routeParams, growl) {


    $scope.listaAlmacenProveedorCub = {};
    $scope.prove2 = {};
    $scope.parametro = $routeParams.idAlmacen;
    $scope.parametroCosecha = $routeParams.opcionCosechaCub;
    $scope.cubetaMiel = {};
    $scope.prove2.totalCompra = 0;
    $scope.opcionCosechaCub = '1';

    var date = new Date();
    var _mes = date.getMonth() + 1;
    $scope.mostrarMes = _mes.toString();

    $http.get('almacen/php/consultaComboMeses.php').success(function (data) {
        $scope.listaDeMeses = data;
    });

    //    -------------------------------------------

    function traeCubetas(tipoDeMiel) {
        var url = '';
        url = 'precios/php/traeCubetas.php?miel=' + tipoDeMiel;
        if ($scope.mostrarMes) {
            url += '&mes=' + $scope.mostrarMes;
        }
        $http.post(url).success(function (info) {
            $scope.respaldoLista = info;
            if ($scope.respaldoLista) {
                $scope.filtrarPagos($scope.filtroPagado);
            }
        });
    }

    if ($scope.parametro > 0) {
        if ($scope.parametroCosecha == 1) {
            $http.post("precios/php/dameAlmacenesProveedor.php?id=" + $scope.parametro)
                .success(function (respuesta) {
                    $scope.prove2.id = respuesta.idProveedor;
                    $scope.prove2.idAlmacen = respuesta.idAlmacen;
                    $scope.prove2.proveedor = respuesta.proveedor;
                    $scope.prove2.sagarpa = respuesta.idSagarpa;
                    $scope.prove2.localidad = respuesta.localidad;
                    $scope.prove2.folioEntradaTambor = respuesta.folioEntradaTambor;
                    $scope.prove2.totalCompra = respuesta.costosTotales;
                });
            $http.get('./precios/php/infoCubetas.php?id=' + $scope.parametro).success(function (data) {
                $scope.cubetaMiel = data;
                $scope.dameTotalCub();
            });
            var urlDiferencia = "almacen/php/totalKgDiferencia.php?id=" + $scope.parametro;
            if ($scope.parametroCosecha == 1) {
                urlDiferencia += '&cubeta';
            }
            $http.post(urlDiferencia).success(function (info) {
                $scope.totalKgDiferencia = parseInt(info.totalDiferencia);
            });
        } else if ($scope.parametroCosecha == 6) {
            $http.post("precios/php/dameAlmacenesProveedor.php?organicaa=0&id=" + $scope.parametro)
                .success(function (respuesta) {
                    $scope.prove2.id = respuesta.idProveedor;
                    $scope.prove2.idAlmacen = respuesta.idAlmacen;
                    $scope.prove2.proveedor = respuesta.proveedor;
                    $scope.prove2.sagarpa = respuesta.idSagarpa;
                    $scope.prove2.localidad = respuesta.localidad;
                    $scope.prove2.folioEntradaTambor = respuesta.folioEntradaTambor;
                    $scope.prove2.totalCompra = respuesta.costosTotales;
                });
            $http.get('./precios/php/infoCubetas.php?organicaa=0&id=' + $scope.parametro).success(function (data) {
                $scope.cubetaMiel = data;
                $scope.dameTotalCub();
            });
        }else if ($scope.parametroCosecha == 7) {
            $http.post("precios/php/dameAlmacenesProveedor.php?organican=0&id=" + $scope.parametro)
            .success(function (respuesta) {
                $scope.prove2.id = respuesta.idProveedor;
                $scope.prove2.idAlmacen = respuesta.idAlmacen;
                $scope.prove2.proveedor = respuesta.proveedor;
                $scope.prove2.sagarpa = respuesta.idSagarpa;
                $scope.prove2.localidad = respuesta.localidad;
                $scope.prove2.folioEntradaTambor = respuesta.folioEntradaTambor;
                $scope.prove2.totalCompra = respuesta.costosTotales;
            });
        $http.get('./precios/php/infoCubetas.php?organican=0&id=' + $scope.parametro).success(function (data) {
            $scope.cubetaMiel = data;
            $scope.dameTotalCub();
        });
        }  else if ($scope.parametroCosecha == 8) {
            $http.post("precios/php/dameAlmacenesProveedor.php?organicaagua=0&id=" + $scope.parametro)
                .success(function (respuesta) {
                    $scope.prove2.id = respuesta.idProveedor;
                    $scope.prove2.idAlmacen = respuesta.idAlmacen;
                    $scope.prove2.proveedor = respuesta.proveedor;
                    $scope.prove2.sagarpa = respuesta.idSagarpa;
                    $scope.prove2.localidad = respuesta.localidad;
                    $scope.prove2.folioEntradaTambor = respuesta.folioEntradaTambor;
                    $scope.prove2.totalCompra = respuesta.costosTotales;
                });
            $http.get('./precios/php/infoCubetas.php?organicaagua=0&id=' + $scope.parametro).success(function (data) {
                $scope.cubetaMiel = data;
                $scope.dameTotalCub();
            });
        }else if ($scope.parametroCosecha == 9) {
            $http.post("precios/php/dameAlmacenesProveedor.php?organicame=0&id=" + $scope.parametro)
            .success(function (respuesta) {
                $scope.prove2.id = respuesta.idProveedor;
                $scope.prove2.idAlmacen = respuesta.idAlmacen;
                $scope.prove2.proveedor = respuesta.proveedor;
                $scope.prove2.sagarpa = respuesta.idSagarpa;
                $scope.prove2.localidad = respuesta.localidad;
                $scope.prove2.folioEntradaTambor = respuesta.folioEntradaTambor;
                $scope.prove2.totalCompra = respuesta.costosTotales;
            });
        $http.get('./precios/php/infoCubetas.php?organicame=0&id=' + $scope.parametro).success(function (data) {
            $scope.cubetaMiel = data;
            $scope.dameTotalCub();
        });
        } 
        else if ($scope.parametroCosecha == 2) {
            $http.post("precios/php/dameAlmacenesProveedor.php?organica=0&id=" + $scope.parametro)
            .success(function (respuesta) {
                $scope.prove2.id = respuesta.idProveedor;
                $scope.prove2.idAlmacen = respuesta.idAlmacen;
                $scope.prove2.proveedor = respuesta.proveedor;
                $scope.prove2.sagarpa = respuesta.idSagarpa;
                $scope.prove2.localidad = respuesta.localidad;
                $scope.prove2.folioEntradaTambor = respuesta.folioEntradaTambor;
                $scope.prove2.totalCompra = respuesta.costosTotales;
            });
        $http.get('./precios/php/infoCubetas.php?organica=0&id=' + $scope.parametro).success(function (data) {
            $scope.cubetaMiel = data;
            $scope.dameTotalCub();
        });
        } 
        else {
            $http.post("precios/php/dameAlmacenesProveedor.php?organicam=0&id=" + $scope.parametro)
                .success(function (respuesta) {
                    $scope.prove2.id = respuesta.idProveedor;
                    $scope.prove2.idAlmacen = respuesta.idAlmacen;
                    $scope.prove2.proveedor = respuesta.proveedor;
                    $scope.prove2.sagarpa = respuesta.idSagarpa;
                    $scope.prove2.localidad = respuesta.localidad;
                    $scope.prove2.folioEntradaTambor = respuesta.folioEntradaTambor;
                    $scope.prove2.totalCompra = respuesta.costosTotales;
                });
            $http.get('./precios/php/infoCubetas.php?organicam=0&id=' + $scope.parametro).success(function (data) {
                $scope.cubetaMiel = data;
                $scope.dameTotalCub();
            });
        }

    } else {
        if (window.localStorage.getItem('miel') != null) {
            $scope.opcionCosechaCub = window.localStorage.getItem('miel');
        }
        $scope.$watch('opcionCosechaCub', function (tipoMiel) {
            if (tipoMiel) {
                window.localStorage.setItem('opcionCosechaCub', tipoMiel);
                traeCubetas(tipoMiel);
            }
        });
        if (window.localStorage.getItem('seleccionMes') != null) {
            $scope.mostrarMes = window.localStorage.getItem('seleccionMes');
        }
        $scope.$watch('mostrarMes', function (mesElegido) {
            window.localStorage.setItem('seleccionMes', mesElegido);
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

    $scope.filtrarPagos = function (filtro) {
        $scope.nuevoArreglo = [];
        if (filtro == '1') {
            $scope.respaldoLista.forEach(pago => { //Pagados
                if (pago.totalCompra != "0.00") {
                    $scope.nuevoArreglo.push(pago);
                }
            });
            if ($scope.nuevoArreglo.length == 0) {
                growl.info('No hay registros pagados');
            }
            $scope.cubeta = $scope.nuevoArreglo;
        } else if (filtro == '2') {
            $scope.respaldoLista.forEach(cheque => { //No pagados
                if (cheque.totalCompra == "0.00") {
                    $scope.nuevoArreglo.push(cheque);
                }
            });
            if ($scope.nuevoArreglo.length == 0) {
                growl.info('No hay registros sin pagar');
            }
            $scope.cubeta = $scope.nuevoArreglo;
        } else if (filtro == '3') { // Todos
            $scope.cubeta = $scope.respaldoLista;
        }
    }

    $scope.dameTotalCub = function () {
        $scope.prove2.totalCompra = 0;
        angular.forEach($scope.cubetaMiel, function (value, key) {
            $scope.prove2.totalCompra += parseFloat(value.costoTotal);
        });
    };

    $scope.guardarPrecioCub = function () {

        $http.post("./precios/php/guardarPrecioCubetas.php?miel=" + $scope.parametroCosecha + "&totalCompra=" + $scope.prove2.totalCompra + "&id=" + $scope.parametro, { valor: $scope.cubetaMiel }, $scope.parametro).success(function (data) {
            if (typeof (data) == 'object' && data.hasOwnProperty('error')) {
                if (data.error) {
                    swal('', data.message || 'No se ha podido guardar los cambios', 'info');
                } else {
                    swal("Éxito!", data.message, "success");
                    return window.location.href = "#/pagoCub";
                }
            } else {
                growl.error('Error');
                console.error(data);
            }
        });

    };
    $scope.pdfPreciosCub = function () {
        window.open('reportes/envoiceOM.php?idAlmacen=' + $scope.parametro + "&folioEntradaTambor=" + $scope.prove2.folioEntradaTambor + '&tmp=' + $scope.parametroCosecha, '_blank');
    };
    $scope.xlsPreciosCub = function () {
        return window.location.href = "reportes/exportaExcel.php?idAlmacen=" + $scope.parametro + "&folioEntradaTambor=" + $scope.prove2.folioEntradaTambor + '&tmp=' + $scope.parametroCosecha;
        ;
    };
    $scope.pagoCubProveedores = function (valor) {
        if (valor == 0) {
            growl.info("Seleccione un tipo de miel");
        } else {
            return window.location.href = "reportes/precios/xlsPagoACubetasConMiel.php?tmp=" + valor;
        }
    };

}]);


