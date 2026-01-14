form.controller('entradaMateriaPrimaCtrl', ['$scope', '$routeParams', '$http', 'growl', '$location', function ($scope, $routeParams, $http, growl, $location) {
    $scope.entrada = $routeParams.idEntradaMateria;
    $scope.cosechaOpcion = $routeParams.tipoDeMiel;
    $scope.encabezadoEntrada = {};
    $scope.detalleEntrada = {};
    $scope.entradasDetallePush = new Array();
    $scope.informacionEntrada = new Array();
    $scope.entradaEditada = new Array();
    $scope.condiciones = {};
    $scope.listaProveedores = {};
    $scope.lstProveedores = {};
    $scope.encabezadoEntrada.cantidadTotal = 0;
    $scope.encabezadoEntrada.importeTotal = 0;
    $scope.infoEntrada = [];
    $scope.edicionEncabezado = {};
    $scope.cargandoDatos = false;
    $scope.tipoReporte = '2';
    $scope.opcionCosecha = '1';

    var date = new Date();
    var _mes = date.getMonth() + 1;
    $scope.mostrarMes = _mes.toString();
    $http.get('almacen/php/consultaComboMeses.php').success(function (data) {
        $scope.listaDeMeses = data;
    });

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
            if ($scope.detalleEntrada.cuenta && $scope.detalleEntrada.cuenta.idCuentaConcepto) {
                traerListaSubcuentas($scope.detalleEntrada.cuenta.idCuentaConcepto);
            }
        } else {
            // Si envia la cuenta, lo hace directo
            traerListaSubcuentas(idCuenta);
        }
        $scope.calcularimportePeso();
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

    $scope.calcularimportePeso = function () {
        if ($scope.detalleEntrada.cantidad > 0 && $scope.detalleEntrada.precioUnitario) {
            $scope.detalleEntrada.importe = parseFloat($scope.detalleEntrada.cantidad) * parseFloat($scope.detalleEntrada.precioUnitario);
        } else {
            $scope.detalleEntrada.importe = 0;
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

    // Termina funciones de las nuevas cuentas
    function calcularTotal() {
        $scope.encabezadoEntrada.cantidadTotal = 0;
        $scope.encabezadoEntrada.importeTotal = 0;
        angular.forEach($scope.entradasDetallePush, function (value, key) {
            $scope.encabezadoEntrada.cantidadTotal += parseInt(value.cantidad);
            $scope.encabezadoEntrada.importeTotal += parseInt(value.importe);
        });
    }

    //=================================================================
    //      FUNCION PARA AGREGAR
    //=================================================================
    $scope.agregarEntrada = function () {
        if ($scope.entrada == "nuevo") {
            if ($scope.validarEntrada()) {
                $scope.detalleEntrada.idMovimiento = $scope.detalleEntrada.cuenta && $scope.detalleEntrada.cuenta.idCuentaConcepto ? $scope.detalleEntrada.cuenta.idCuentaConcepto : null;
                $scope.detalleEntrada.movimiento = $scope.detalleEntrada.cuenta && $scope.detalleEntrada.cuenta.cuenta ? $scope.detalleEntrada.cuenta.cuenta : '';
                $scope.detalleEntrada.idSubcuenta = $scope.detalleEntrada.subcuentaObj.idSubcuenta ? $scope.detalleEntrada.subcuentaObj.idSubcuenta : null;
                $scope.detalleEntrada.subcuenta = $scope.detalleEntrada.subcuentaObj.subcuenta ? $scope.detalleEntrada.subcuentaObj.subcuenta : '';
                $scope.detalleEntrada.idConcepto = $scope.detalleEntrada.producto && $scope.detalleEntrada.producto.idSubSubcuenta ? $scope.detalleEntrada.producto.idSubSubcuenta : null;
                $scope.detalleEntrada.concepto = $scope.detalleEntrada.producto && $scope.detalleEntrada.producto.subSubcuenta ? $scope.detalleEntrada.producto.subSubcuenta : '';
                $scope.entradasDetallePush.push($scope.detalleEntrada);
                growl.success("Nuevo registro agregado");
                calcularTotal();
                $scope.detalleEntrada = {};
            }
        } else {
            $scope.detalleEntrada.idMovimiento = $scope.detalleEntrada.cuenta && $scope.detalleEntrada.cuenta.idCuentaConcepto ? $scope.detalleEntrada.cuenta.idCuentaConcepto : null;
            $scope.detalleEntrada.movimiento = $scope.detalleEntrada.cuenta && $scope.detalleEntrada.cuenta.cuenta ? $scope.detalleEntrada.cuenta.cuenta : '';

            $scope.detalleEntrada.idSubcuenta = $scope.detalleEntrada.subcuentaObj.idSubcuenta ? $scope.detalleEntrada.subcuentaObj.idSubcuenta : null;
            $scope.detalleEntrada.subcuenta = $scope.detalleEntrada.subcuentaObj.subcuenta ? $scope.detalleEntrada.subcuentaObj.subcuenta : '';

            $scope.detalleEntrada.idConcepto = $scope.detalleEntrada.producto && $scope.detalleEntrada.producto.idSubSubcuenta ? $scope.detalleEntrada.producto.idSubSubcuenta : null;
            $scope.detalleEntrada.concepto = $scope.detalleEntrada.producto && $scope.detalleEntrada.producto.subSubcuenta ? $scope.detalleEntrada.producto.subSubcuenta : '';

            $http.post('almacen/php/guardarEdicionDetalleEntrada.php?idEntradaMateria=' + $scope.entrada + '&tipoDeMiel=' + $scope.cosechaOpcion, { valor: $scope.detalleEntrada })
                .success(function (respuesta) {
                    $http.get('almacen/php/informacionEntradaMateria.php?idEntradaMateria=' + $scope.entrada + '&tipoDeMiel=' + $scope.cosechaOpcion).success(function (data) {
                        $scope.encabezadoEntrada.idEntradaMateria = data.idEntradaMateria;
                        $scope.encabezadoEntrada.fecha = data.fecha;
                        $scope.encabezadoEntrada.cantidadTotal = data.sumaCantidadTotal;
                        $scope.encabezadoEntrada.importeTotal = data.sumaImporteTotal;
                        $scope.encabezadoEntrada.idProveedor = data.idProveedor;
                        $scope.encabezadoEntrada.tipoCliente = data.tipoCliente;
                        $scope.encabezadoEntrada.idMotivo = data.idMotivo;
                        $scope.entradasDetallePush = data.entradasDetallePush;
                        $http.post('almacen/php/guardarEdicionEncabezadoEntrada.php?idEntradaMateria=' + $scope.entrada + '&tipoDeMiel=' + $scope.cosechaOpcion, { valor: $scope.encabezadoEntrada })
                            .success(function (respuesta) {
                                swal("Exito!", "Nueva entrada agregada", "success");
                            });
                    });
                });
        }
    };

    //================================================================
    //      VALIDAR ANTES DEL PUSH
    //================================================================

    $scope.validarEntrada = function () {

        if (!$scope.detalleEntrada.cantidad) {
            growl.error("Se requiere una cantidad");
            return false;
        } else if (!$scope.detalleEntrada.precioUnitario) {
            growl.error("Se requiere un precio unitario");
            return false;
        } else if (!$scope.detalleEntrada.subcuentaObj) {
            growl.error("Se requiere el catálogo");
            return false;
        }

        return true;
    };

    //=================================================================
    //      FUNCION PARA GUARDAR "NUEVO"
    //=================================================================
    $scope.guardarEntrada = function () {
        if ($scope.encabezadoEntrada.fecha == undefined) {
            growl.error("Se requiere una fecha");
        } else if ($scope.encabezadoEntrada.idProveedor == undefined) {
            growl.error("Se requiere un proveedor");
        } else if ($scope.encabezadoEntrada.idMotivo == undefined) {
            growl.error("Se requiere un motivo");
        } else {
            $scope.informacionEntrada.push($scope.encabezadoEntrada);
            $scope.informacionEntrada.push($scope.entradasDetallePush);
            $http.post("almacen/php/guardarEntradaMaterialPrima.php?tipoDeMiel=" + $scope.cosechaOpcion, { valor: $scope.informacionEntrada })
                .success(function (respuesta) {
                    if (typeof (respuesta) == 'object' && respuesta.hasOwnProperty('error')) {
                        if (!respuesta.error) {
                            swal("Exito!", "Nueva entrada agregada", "success");
                            return window.location.href = "#/entradaPrima";
                        } else {
                            growl.warning("Se produjo un error, verifique campos", "warning");
                        }
                    } else {
                        growl.error("Error al guardar");
                        console.error(respuesta);
                    }
                });
        }
    };

    //=================================================================
    //      TRAE INFORMACION DEL DETALLE DE LA ENTRADA
    //================================================================

    function traeEntradasMP() {
        $scope.cargandoDatos = true;
        url = 'almacen/php/traeEntradasDeMateriasPrimas.php?tipoDeMiel=' + $scope.opcionCosecha;
        if ($location.path() == '/entradaPrima') {
            if ($scope.mostrarMes && $scope.tipoReporte == '0') {
                url += '&mes=' + $scope.mostrarMes;
            } else if ($scope.mostrarMes && $scope.tipoReporte !== '0') {
                url += '&mes=' + $scope.mostrarMes + '&reporte=' + $scope.tipoReporte;
            } else if (!$scope.mostrarMes && $scope.tipoReporte !== '0') {
                url += '&reporte=' + $scope.tipoReporte;
            }
        }
        $http.get(url).success(function (data) {
            $scope.infoEntrada = data;
            $scope.cargandoDatos = false;
        });
    }

    if ($scope.entrada > 0) {
        traerListaCuentas(1);
        $http.get('almacen/php/consultaComboCondicionesSalidas.php').success(function (arrayCondiciones) {
            $scope.condiciones = arrayCondiciones;
        });
        $http.get('almacen/php/informacionEntradaMateria.php?idEntradaMateria=' + $scope.entrada + '&tipoDeMiel=' + $scope.cosechaOpcion).success(function (data) {
            $scope.encabezadoEntrada.idEntradaMateria = data.idEntradaMateria;
            $scope.encabezadoEntrada.idProveedor = data.idProveedor;
            $scope.encabezadoEntrada.idMotivo = data.idMotivo;
            $scope.encabezadoEntrada.tipoCliente = data.tipoCliente;
            $scope.proveedor = data.proveedor;
            $scope.condicion = data.condicion;
            $scope.encabezadoEntrada.fecha = data.fecha;
            $scope.encabezadoEntrada.cantidadTotal = data.sumaCantidadTotal;
            $scope.encabezadoEntrada.importeTotal = data.sumaImporteTotal;
            $scope.entradasDetallePush = data.entradasDetallePush;
        });
    } else if ($scope.entrada == 'nuevo') {
        traerListaCuentas(1);
        $http.get('almacen/php/consultaComboCondicionesSalidas.php').success(function (arrayCondiciones) {
            $scope.condiciones = arrayCondiciones;
        });
    } else {
        traeEntradasMP();
    }

    $scope.$watch('[mostrarMes, opcionCosecha, tipoReporte]', function (val) {
        traeEntradasMP();
    });

    $scope.$watch('encabezadoEntrada.tipoCliente', function (val) {
        if (val == 1) {
            $http.get('proveedores/php/listaProveedores.php').success(function (data) {
                $scope.listaProveedores = data;
            });
        } else if (val == 3) {
            $http.get('controlMantenimiento/php/listaDeTecnicos.php').success(function (data) {
                $scope.lstProveedores = data;
            });
        } else if (val == 6) {
            $http.get('controlMantenimiento/php/listaDeClientes.php').success(function (data) {
                $scope.lstProveedores = data;
            });
        }
    });

    $scope.editarEncabezado = function () {
        $("#mdlEditarEncabezado").modal();
    }

    $scope.modificarEntrada = function () {
        $http.post('almacen/php/guardarEdicionEncabezadoEntrada.php?idEntradaMateria=' + $scope.entrada + '&tipoDeMiel=' + $scope.cosechaOpcion, { valor: $scope.encabezadoEntrada })
            .success(function (respuesta) {
                swal("Éxito!", "Nueva entrada agregada", "success");
                $http.get('almacen/php/informacionEntradaMateria.php?idEntradaMateria=' + $scope.entrada + '&tipoDeMiel=' + $scope.cosechaOpcion).success(function (data) {
                    $scope.encabezadoEntrada.idEntradaMateria = data.idEntradaMateria;
                    $scope.encabezadoEntrada.idProveedor = data.idProveedor;
                    $scope.encabezadoEntrada.idMotivo = data.idMotivo;
                    $scope.encabezadoEntrada.tipoCliente = data.tipoCliente;
                    $scope.proveedor = data.proveedor;
                    $scope.condicion = data.condicion;
                    $scope.encabezadoEntrada.fecha = data.fecha;
                    $scope.encabezadoEntrada.cantidadTotal = data.sumaCantidadTotal;
                    $scope.encabezadoEntrada.importeTotal = data.sumaImporteTotal;
                    $scope.entradasDetallePush = data.entradasDetallePush;
                });
                $("#mdlEditarEncabezado").modal('hide');
            });
    };

    $scope.editarDetalle = function (idDetalle) {
        $("#mdlEditarDetalle").modal();
        $http.get('almacen/php/informacionEntradaMateria.php?idDetalleEntrada=' + idDetalle + '&tipoDeMiel=' + $scope.cosechaOpcion).success(function (info) {
            $scope.detalleEntradaEdicion = info;
        });
    };


    function validarEdicionDetalle() {

        if (!$scope.detalleEntradaEdicion.cantidad) {
            growl.error("Se requiere una cantidad");
            return false;
        } else if (!$scope.detalleEntradaEdicion.precioUnitario) {
            growl.error("Se requiere un precio unitario");
            return false;
        } else if ($scope.detalleEntradaEdicion.nuevaCuenta && !$scope.detalleEntradaEdicion.nuevaSubcuentaObj) {
            growl.error('Selecciona el nuevo catálogo');
            return false;
        }



        return true;

    }

    $scope.modificarDetalle = function () {

        if (validarEdicionDetalle()) {

            var nuevoObjetoDetalle = Object.assign({}, $scope.detalleEntradaEdicion);
            // Si seleccionó la  nueva cuenta
            if (nuevoObjetoDetalle.nuevaCuenta) {
                nuevoObjetoDetalle.idMovimiento = nuevoObjetoDetalle.nuevaCuenta && nuevoObjetoDetalle.nuevaCuenta.idCuentaConcepto ? nuevoObjetoDetalle.nuevaCuenta.idCuentaConcepto : null;
                nuevoObjetoDetalle.movimiento = nuevoObjetoDetalle.nuevaCuenta && nuevoObjetoDetalle.nuevaCuenta.cuenta ? nuevoObjetoDetalle.nuevaCuenta.cuenta : '';
            }

            // Si seleccionó cuenta, debe seleccionar también catálogo, está en la validación
            if (nuevoObjetoDetalle.nuevaSubcuentaObj) {
                nuevoObjetoDetalle.idSubcuenta = nuevoObjetoDetalle.nuevaSubcuentaObj && nuevoObjetoDetalle.nuevaSubcuentaObj.idSubcuenta ? nuevoObjetoDetalle.nuevaSubcuentaObj.idSubcuenta : null;
                nuevoObjetoDetalle.subcuenta = nuevoObjetoDetalle.nuevaSubcuentaObj && nuevoObjetoDetalle.nuevaSubcuentaObj.subcuenta ? nuevoObjetoDetalle.nuevaSubcuentaObj.subcuenta : '';

                // Puede seleccionar catálogo sin seleccionar cuenta, así que verificamos y si no la selecciona ponemos en nulo
                if (!nuevoObjetoDetalle.nuevaCuenta) {
                    nuevoObjetoDetalle.idMovimiento = null;
                    nuevoObjetoDetalle.movimiento = '';
                }

                // Lo mismo con el producto, que es opcional

                if (!nuevoObjetoDetalle.nuevoProducto) {
                    nuevoObjetoDetalle.idConcepto = null;
                    nuevoObjetoDetalle.concepto = '';
                }
            }

            // Producto es opcional, así que:

            if (nuevoObjetoDetalle.nuevoProducto) {
                nuevoObjetoDetalle.idConcepto = nuevoObjetoDetalle.nuevoProducto && nuevoObjetoDetalle.nuevoProducto.idSubSubcuenta ? nuevoObjetoDetalle.nuevoProducto.idSubSubcuenta : null;
                nuevoObjetoDetalle.concepto = nuevoObjetoDetalle.nuevoProducto && nuevoObjetoDetalle.nuevoProducto.subSubcuenta ? nuevoObjetoDetalle.nuevoProducto.subSubcuenta : '';
            }

            // $scope.detalleEntradaEdicion.importe = $scope.detalleEntradaEdicion.cantidad * $scope.detalleEntradaEdicion.precioUnitario;
            $http.post('almacen/php/editarMP.php?entrada=0&idDetalleEntrada=' + nuevoObjetoDetalle.idDetalleEntrada + '&tipoDeMiel=' + $scope.cosechaOpcion, { valor: nuevoObjetoDetalle })
                .success(function (respuesta) {
                    $http.get('almacen/php/informacionEntradaMateria.php?idEntradaMateria=' + $scope.entrada + '&tipoDeMiel=' + $scope.cosechaOpcion).success(function (data) {
                        $scope.encabezadoEntrada.idEntradaMateria = data.idEntradaMateria;
                        $scope.encabezadoEntrada.fecha = data.fecha;
                        $scope.encabezadoEntrada.cantidadTotal = data.sumaCantidadTotal;
                        $scope.encabezadoEntrada.importeTotal = data.sumaImporteTotal;
                        $scope.encabezadoEntrada.idProveedor = data.idProveedor;
                        $scope.encabezadoEntrada.tipoCliente = data.tipoCliente;
                        $scope.encabezadoEntrada.idMotivo = data.idMotivo;
                        $scope.entradasDetallePush = data.entradasDetallePush;
                        $http.post('almacen/php/guardarEdicionEncabezadoEntrada.php?idEntradaMateria=' + $scope.entrada + '&tipoDeMiel=' + $scope.cosechaOpcion, { valor: $scope.encabezadoEntrada })
                            .success(function (respuesta) {
                                swal("Éxito!", "Modificación realizada", "success");
                            });
                    });
                    $("#mdlEditarDetalle").modal('hide');
                });
        }

    };


    $scope.modalAlta = function () {
        $("#altaNombres").modal();
    };

    function verificarDatosNuevoProveedor(altas) {

        if (!altas.nombre) {
            growl.info('Escriba el nombre del proveedor');
            return false;
        }

        if (!altas.idSagarpa) {
            growl.info('Ingrese el número de ID SAGARPA');
            return false;
        }

        if (!altas.telefono) {
            growl.info('Ingrese el número de teléfono');
            return false;
        }

        if (!altas.tipoDeMiel) {
            growl.info('Seleccione un tipo de producto');
            return false;
        }

        return true;
    }

    $scope.guardarAltaRapida = function (altas) {
        switch (altas.proveedorTipo) {
            case '1':
                if (verificarDatosNuevoProveedor(altas)) {
                    $http.post("almacen/php/guardarProv.php", { valor: altas })
                        .success(function (respuesta) {
                            swal("Exito!", "Registro agregado", "success");
                            $http.get('proveedores/php/listaProveedores.php').success(function (data) {
                                $scope.listaProveedores = data;
                                $scope.altas = {};
                                $("#altaNombres").modal('hide');
                            });
                        });
                }
                break;
            case '3':
                $http.post("controlMantenimiento/php/guardarTecnico.php", altas).success(function (info) {
                    swal("Exito!", "Registro agregado", "success");
                    $http.get('controlMantenimiento/php/listaDeTecnicos.php').success(function (data) {
                        $scope.lstProveedores = data;
                        $scope.altas = {};
                        $("#altaNombres").modal('hide');
                    });
                });
                break;
            default:
                break;
        }
    };

    //=================================================================
    //      FUNCION PARA ELIMINAR
    //=================================================================
    $scope.eliminarEntrada = function (indice) {
        $scope.entradasDetallePush.splice(indice, 1);
        growl.warning("Registro eliminado");
        $scope.encabezadoEntrada.cantidadTotal = 0;
        $scope.encabezadoEntrada.importeTotal = 0;
        angular.forEach($scope.entradasDetallePush, function (value, key) {
            $scope.encabezadoEntrada.cantidadTotal += parseInt(value.cantidad);
            $scope.encabezadoEntrada.importeTotal += parseInt(value.importe);
        });
    };

    $scope.eliminarEntradaBd = function (idDetalleEntrada) {
        $http.post('almacen/php/eliminarEntrada.php?idDetalleEntrada=' + idDetalleEntrada + '&tipoDeMiel=' + $scope.cosechaOpcion)
            .success(function () {
                swal("Exito!", "Entrada eliminada", "success");
                $http.get('almacen/php/informacionEntradaMateria.php?idEntradaMateria=' + $scope.entrada + '&tipoDeMiel=' + $scope.cosechaOpcion).success(function (data) {
                    $scope.encabezadoEntrada.idEntradaMateria = data.idEntradaMateria;
                    $scope.encabezadoEntrada.idProveedor = data.idProveedor;
                    $scope.encabezadoEntrada.idMotivo = data.idMotivo;
                    $scope.encabezadoEntrada.tipoCliente = data.tipoCliente;
                    $scope.encabezadoEntrada.fecha = data.fecha;
                    $scope.encabezadoEntrada.cantidadTotal = data.sumaCantidadTotal;
                    $scope.encabezadoEntrada.importeTotal = data.sumaImporteTotal;
                    $scope.entradasDetallePush = data.entradasDetallePush;
                    $scope.cantidadTotal = $scope.encabezadoEntrada.cantidadTotal;
                    $scope.importeTotal = $scope.encabezadoEntrada.importeTotal;
                    $http.post('almacen/php/updateTotales.php?tipoDeMiel=' + $scope.cosechaOpcion + '&idEntradaMateria=' + $scope.entrada + '&cantidadTotal=' + $scope.cantidadTotal + '&importeTotal=' + $scope.importeTotal).success(function () {
                    });
                });
            });
    };

    $scope.imprimirPDF = function (idEntradaMateria, tipoDeMiel) {
        window.open('reportes/almacen/pdfReporteMP.php?idEntradaMateria=' + idEntradaMateria + '&tipoDeMiel=' + tipoDeMiel);
    };

    $scope.modalDescargarReporte = function () {
        $scope.opcionDescargarMP = {};
        $('#descargarReporteModal').modal();
    }
    $scope.descargarReporteMP = function (opciones) {
        if (opciones.opcion && opciones.tipo) {
            if (opciones.opcion == '1') {
                // Todo 
                $('#descargarReporteModal').modal('hide');
                return window.location.href = 'reportes/almacen/xlsEntradaMP.php?auto&tipoDeMiel=' + opciones.tipo;
            } else if (opciones.opcion == '2') {
                if (opciones.fecha1 && opciones.fecha2) {
                    $('#descargarReporteModal').modal('hide');
                    return window.location.href = 'reportes/almacen/xlsEntradaMP.php?auto&tipoDeMiel=' + opciones.tipo + '&fecha1=' + opciones.fecha1 + '&fecha2=' + opciones.fecha2;
                } else {
                    growl.info('Selecciona las fechas');
                }
            }
        } else {
            growl.info('Selecciona una opcion');
        }
    }

}]);