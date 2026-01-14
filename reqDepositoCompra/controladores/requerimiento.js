form.config(function ($routeProvider) {
    $routeProvider.when('/requerimiento', {
        templateUrl: 'reqDepositoCompra/depositoCompra.html',
        controller: 'requerimientoCtrl'
    }).when('/nvoReq/:idRequisicion', {
        templateUrl: 'reqDepositoCompra/nuevoRequerimiento.html',
        controller: 'requerimientoCtrl'
    })
})
form.controller('requerimientoCtrl', ['$scope', '$http', '$routeParams', 'growl', function ($scope, $http, $routeParams, growl) {
    $scope.requerimiento = $routeParams.idRequisicion;
    $scope.encabezado = {};
    $scope.detalle = {};
    var self = this;
    //        $scope.detalle.peso = "";
    //        $scope.detalle.peso = parse($scope.detalle.noTambores * 300);
    $scope.requerimientosDetalle = new Array();
    $scope.informacion = new Array();
    $scope.informacionEditada = new Array();
    $scope.selectProvee = {};
    $scope.selectProvee.idProveedor = "";
    $scope.selectProvee.nombre = "";
    $scope.selectCosecha = {};
    $scope.selectCosecha.idTipoDeMiel = "";
    $scope.selectCosecha.tipoDeMiel = "";
    $scope.hoy = new Date();
    $scope.encabezado.totalTambores = 0;
    $scope.encabezado.totalKilos = 0;
    $scope.encabezado.importeTotal = 0;
    $scope.infoDeposito = new Array();
    //=================================================================
    //      TRAE INFORMACION DE RELACION DE REQUERIMIENTOS
    //=================================================================

    // Función que trae la información de Un requerimiento, para no repetir varias veces la llamada

    function traeInformacionRequerimiento(id) {
        if (id) {
            $http.get('reqDepositoCompra/php/infoRequerimiento.php?id=' + id).success(function (data) {
                if (typeof (data) == 'object' && data.hasOwnProperty('error')) {
                    if (data.error) {
                        swal('Error', data.message, 'error');
                    } else {
                        $scope.encabezado = data.data;
                        $scope.requerimientosDetalle = data.data.requerimientosDetalle;
                    }
                } else {
                    growl.error('Error');
                    console.error(data);
                }
            });
        }
    }

    // función para traer la lista de requerimientos
    function traeListaDeRequerimientos() {
        $scope.infoDeposito = null;
        $http.get('reqDepositoCompra/php/getTablaRequerimiento.php').success(function (data) {

            if (typeof (data) == 'object' && data.hasOwnProperty('error')) {
                if (data.error) {
                    swal('Error', data.message, 'error');
                } else {
                    $scope.infoDeposito = data.resultado;
                }
            } else {
                growl.error('Error');
                console.error(data);
            }

        });
    }
    traeListaDeRequerimientos();

    $scope.pdfRequerimiento = function () {
        // window.open('reportes/pdfRequerimientosDeposito.php?idRequisicion=' + $scope.requerimiento, '_blank');
        window.open('reportes/compras/pdfRequerimientoCompra.php?idRequerimiento=' + $scope.requerimiento, '_blank');

    };

    //        $scope.pdfRequerimiento = function () {
    //            window.open('reportes/pdfRequerimientosDeposito.php?idRequisicion=' + $scope.requerimiento, '_blank');
    ////            return window.location.href = "reportes/pdfRequerimientosDeposito.php?idRequisicion=" + $scope.requerimiento;
    //        };

    $scope.xlsRequerimiento = function () {
        return window.location.href = "reportes/xlsRequerimiento.php?idRequisicion=" + $scope.requerimiento;
    };

    //=================================================================
    //      TRAE INFORMACION DEL DETALLE DEL REQUERIMIENTO
    //================================================================

    if ($scope.requerimiento > 0) {
        traeInformacionRequerimiento($scope.requerimiento);
    }



    //=================================================================
    // CONSULTA (Combo)
    //=================================================================
    // Obtener proveedores, dependiendo del comprador que seleccionen
    $scope.obtenerProveedoresDelComprador = function (idComprador) {
        $scope.nomProveedor = [];
        $http.get('reqDepositoCompra/php/listaProveedores.php?idComprador=' + idComprador).success(function (arrayProveedor) {
            // $scope.nomProveedor = arrayProveedor;
            if (typeof (arrayProveedor) == 'object' && arrayProveedor.hasOwnProperty('error')) {

                if (arrayProveedor.error) {
                    swal('Error', arrayProveedor.message, 'error');
                } else {
                    $scope.nomProveedor = arrayProveedor.resultado;
                }

            } else {
                growl.error('Error');
                console.error(arrayProveedor);
            }
        });
    }
    $scope.lstMieles = {};
    $http.get('utilerias/php/traeTiposDeMiel.php').success(function (array) {
        if (!array.hasOwnProperty('error')) {
            $scope.lstMieles = array;
        }
    });
    // Obtener compradores
    $http.get('reqDepositoCompra/php/listaCompradores.php').success(function (resultado) {
        if (typeof (resultado) == 'object' && resultado.hasOwnProperty('error')) {

            if (resultado.error) {
                swal('Error', resultado.message, 'error');
            } else {
                $scope.listaCompradores = resultado.resultado;
            }

        } else {
            growl.error('Error');
            console.error(resultado);
        }
    });

    //=================================================================
    // TRAE LA LOCALIDAD AL SELECCIONAR EL PROVEEDOR
    //=================================================================
    $scope.obtenerInfoLocalidad = function () {
        $scope.id = $scope.selectProvee.idProveedor;
        $http.post("reqDepositoCompra/php/dameLocalidad.php?idProveedor=" + $scope.id).success(function (info) {
            $scope.proveeLocalidad = info;
            $scope.detalle.localidad = $scope.proveeLocalidad.localidad;
        });
        $http.post("reqDepositoCompra/php/saldoDeudor.php?idProveedor=" + $scope.id + "&fecha=" + $scope.encabezado.fechaRequisicion).success(function (info) {
            console.log(info);
            $scope.sl = info;
            console.log($scope.sl.totalSaldo);
            if ($scope.sl.totalSaldo == undefined) {
                $scope.detalle.saldoDeudor = 0.00;
            } else {
                $scope.detalle.saldoDeudor = $scope.sl.totalSaldo;
            }
        });
    };

    /** función para guardar fecha */

    $scope.guardarEdicionFecha = function () {
        $http.post("reqDepositoCompra/php/guardarEdicionEncabezado.php?idRequisicion=" + $scope.requerimiento, { valor: $scope.encabezado }).success(function (respuesta) {
            swal("Exito!", "Fecha actualizada", "success");
        });
    }

    /** Función para calcular el importe o el peso basado en el precio */

    $scope.calcularImporteOPeso = function (tipo) {
        if (tipo == 'precio') {
            if ($scope.detalle.precio) {
                if ($scope.detalle.peso) {
                    $scope.detalle.importe = parseFloat($scope.detalle.precio * $scope.detalle.peso).toFixed(2);
                } else if ($scope.detalle.importe) {
                    $scope.detalle.peso = parseFloat($scope.detalle.importe / $scope.detalle.precio).toFixed(2);
                }
            }
        }
        if (tipo == 'peso') {
            if ($scope.detalle.precio) {
                $scope.detalle.importe = parseFloat($scope.detalle.precio * $scope.detalle.peso).toFixed(2);
            }
        }
        if (tipo == 'importe') {
            if ($scope.detalle.precio) {
                $scope.detalle.peso = parseFloat($scope.detalle.importe / $scope.detalle.precio).toFixed(2);
            }
        }
    }

    //=================================================================
    //      FUNCION PARA AGREGAR
    //=================================================================
    $scope.agregarRequerimiento = function () {
        $scope.listo = $scope.validarRequerimiento();
        if ($scope.listo == true) {
            if ($scope.requerimiento == "nuevo") {
                $scope.obj = {};
                $scope.obj.idComprador = $scope.selectComprador.idcomprador;
                $scope.obj.nombreComprador = $scope.selectComprador.nombre;
                $scope.obj.idProveedor = $scope.selectProvee.idProveedor;
                $scope.obj.nombre = $scope.selectProvee.nombre;
                $scope.obj.idTipoDeMiel = $scope.selectCosecha.idTipoDeMiel;
                $scope.obj.tipoDeMiel = $scope.selectCosecha.tipoDeMiel;
                $scope.obj.importe = $scope.detalle.importe;
                $scope.obj.localidad = $scope.detalle.localidad;
                $scope.obj.saldoDeudor = $scope.detalle.saldoDeudor;
                $scope.obj.noTambores = $scope.detalle.peso / 300;
                $scope.obj.precio = $scope.detalle.precio;
                $scope.obj.peso = $scope.detalle.peso;
                $scope.obj.banco = $scope.detalle.banco;
                $scope.obj.observaciones = $scope.detalle.observaciones;
                $scope.requerimientosDetalle.push($scope.obj);
                growl.success("Nuevo requerimiento agregado");
                $scope.encabezado.totalTambores = 0;
                $scope.encabezado.totalKilos = 0;
                $scope.encabezado.importeTotal = 0;
                angular.forEach($scope.requerimientosDetalle, function (value, key) {
                    $scope.encabezado.totalKilos += parseFloat(value.peso);
                    $scope.encabezado.importeTotal += parseFloat(value.importe);
                    $scope.encabezado.totalTambores += parseInt(value.noTambores)
                });
                $scope.selectProvee.idProveedor = "";
                $scope.detalle.localidad = "";
                $scope.selectCosecha.idTipoDeMiel = "";
                $scope.detalle.noTambores = "";
                $scope.detalle.peso = "";
                $scope.detalle.precio = "";
                $scope.detalle.banco = "";
                $scope.detalle.observaciones = "";
                $scope.detalle.importe = 0;
            } else {
                $scope.detalle.idComprador = $scope.selectComprador.idcomprador;
                $scope.detalle.idProveedor = $scope.selectProvee.idProveedor;
                $scope.detalle.nombre = $scope.selectProvee.nombre;
                $scope.detalle.idTipoDeMiel = $scope.selectCosecha.idTipoDeMiel;
                $scope.detalle.tipoDeMiel = $scope.selectCosecha.tipoDeMiel;
                $scope.detalle.noTambores = $scope.detalle.peso / 300;
                $scope.detalle.importe = $scope.detalle.importe;
                $scope.detalle.peso = $scope.detalle.peso;
                $scope.encabezado.totalKilos = parseFloat($scope.encabezado.totalKilos) + parseFloat($scope.detalle.peso);
                $scope.encabezado.importeTotal = parseFloat($scope.encabezado.importeTotal) + parseFloat($scope.detalle.importe);
                $scope.encabezado.totalTambores += parseInt($scope.detalle.noTambores);
                $scope.informacionEditada.push($scope.encabezado);
                $scope.informacionEditada.push($scope.detalle);
                $http.post("reqDepositoCompra/php/guardarEdicionDetalle.php", { valor: $scope.informacionEditada }).success(function (respuesta) {
                    if (typeof (respuesta) == 'object' && respuesta.hasOwnProperty('error')) {
                        if (respuesta.error) {
                            growl.error('Ocurrió un error');
                            console.error(respuesta.message);
                        } else {
                            swal("¡Listo!", respuesta.message, "success");
                            return window.location.href = "#/requerimiento";
                        }
                    } else {
                        growl.error('Error');
                        console.error(respuesta);
                    }
                });
            }
        }
    };
    //=================================================================
    //      VALIDAR REQUERIMIENTO
    //=================================================================
    $scope.validarRequerimiento = function () {
        $scope.listo = false;
        if ($scope.selectProvee.idProveedor == "") {
            growl.error("Se requiere un proveedor");
        } else if ($scope.selectCosecha.idTipoDeMiel == "") {
            growl.error("Se requiere un tipo de miel");
        } else if ($scope.detalle.peso == undefined) {
            growl.error("Se requiere un número de tambores");
        } else if ($scope.detalle.precio == undefined) {
            growl.error("Se requiere un precio");
        } else if ($scope.detalle.observaciones == undefined) {
            growl.error("Se requieren observaciones");
        } else if ($scope.detalle.banco == undefined) {
            growl.error("Se requiere un banco");
        } else {
            $scope.listo = true;
        }
        return $scope.listo;
    };
    //=================================================================
    //      FUNCION PARA GUARDAR
    //=================================================================
    $scope.guardarRequerimiento = function () {
        if ($scope.encabezado.fechaRequisicion == undefined) {
            growl.error("Se requiere una fecha");
        } else {
            $scope.informacion.push($scope.encabezado);
            $scope.informacion.push($scope.requerimientosDetalle);
            $http.post("reqDepositoCompra/php/guardarRequerimiento.php", { valor: $scope.informacion }).success(function (respuesta) {
                if (typeof (respuesta) == 'object' && respuesta.hasOwnProperty('error')) {
                    if (respuesta.error) {
                        growl.error('Ocurrió un error');
                        console.error(respuesta.message);
                    } else {
                        swal("¡Listo!", respuesta.message, "success");
                        return window.location.href = "#/requerimiento";
                    }
                } else {
                    growl.error('Error');
                    console.error(respuesta);
                }
            });
        }
    };
    //=================================================================
    //      FUNCION PARA ELIMINAR
    //=================================================================
    $scope.eliminarRequerimientoDetalle = function (indice) {
        console.log('recibe indice: ' + indice + ' para eliminar');
        $scope.requerimientosDetalle.splice(indice, 1);
        growl.warning("Registro eliminado");
        $scope.encabezado.totalTambores = 0;
        $scope.encabezado.importeTotal = 0;
        $scope.encabezado.totalKilos = 0;
        angular.forEach($scope.requerimientosDetalle, function (value, key) {
            $scope.encabezado.totalTambores += parseInt(value.noTambores);
            $scope.encabezado.importeTotal += parseInt(value.importe);
            $scope.encabezado.totalKilos += parseInt(value.peso);
        });
    };
    $scope.eliminarRequerimientoBd = function (idDetalle, cobrado) {
        if (cobrado == '0') {
            swal({
                title: "¿Eliminar este requerimiento?",
                text: "No se podrá recuperar los datos",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "Eliminar",
                cancelButtonText: "Cancelar",
            }, function (eliminar) {
                if (eliminar) {
                    $http.post("reqDepositoCompra/php/eliminarRequerimiento.php?idDetalle=" + idDetalle + "&idRequisicion=" + $scope.requerimiento).success(function (data) {
                        swal("Exito!", "Requerimiento eliminado", "success");
                        traeInformacionRequerimiento($scope.requerimiento);
                    });
                }
            });
        }
    };

    $scope.eliminarRequerimiento = function (idRequisicion) {
        if (idRequisicion) {

            swal({
                title: "¿Eliminar el requerimiento?",
                text: "Se eliminará todos los datos relacionados",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "Eliminar",
                cancelButtonText: "Cancelar",
            }, function (eliminar) {
                if (eliminar) {
                    $http.get("reqDepositoCompra/php/eliminarRequisicion.php?idRequisicion=" + idRequisicion).success(function (res) {
                        if (typeof (res) == 'object' && res.hasOwnProperty('error')) {
                            if (res.error) {
                                swal('Error', res.message, 'error');
                            } else {
                                traeListaDeRequerimientos();
                                swal('Hecho', res.message, 'success');
                            }
                        } else {
                            growl.error('Error');
                            console.error(res);
                        }
                    });
                }
            });
        }
    }


    // Función para cambiar el estado de un requerimiento cuando selecciona cobrado

}]);

