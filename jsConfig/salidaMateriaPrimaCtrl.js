form.controller('salidaMateriaPrimaCtrl', ['$scope', '$routeParams', '$http', 'growl', function ($scope, $routeParams, $http, growl) {
    $scope.salida = $routeParams.idSalidaMateria;
    $scope.tipoDeMiel = $routeParams.tipoDeMiel;
    $scope.encabezadoSalida = {};
    $scope.detalleSalida = {};
    $scope.salidasDetallePush = new Array();
    $scope.informacionSalida = new Array();
    $scope.salidaEditada = new Array();
    $scope.condiciones = {};
    $scope.listaProveedores = {};
    $scope.lstProveedores = {};
    $scope.encabezadoSalida.cantidadTotal = 0;
    $scope.encabezadoSalida.importeTotal = 0;
    $scope.infoSalida = [];
    $scope.opcionCosecha = '1';

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
            if ($scope.detalleSalida.cuenta && $scope.detalleSalida.cuenta.idCuentaConcepto) {
                traerListaSubcuentas($scope.detalleSalida.cuenta.idCuentaConcepto);
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
        if ($scope.detalleSalida.cantidad > 0 && $scope.detalleSalida.producto) {
            $scope.detalleSalida.importe = parseFloat($scope.detalleSalida.cantidad) * parseFloat($scope.detalleSalida.producto.precioUnitario);
        } else {
            $scope.detalleSalida.importe = 0;
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

    $scope.$watch('encabezadoSalida.tipoCliente', function (val) {
        // $scope.encabezadoSalida.idProveedor = "";
        if (val == 1) {
            $http.get('proveedores/php/listaProveedores.php').success(function (data) {
                $scope.listaProveedores = data;
            });
        } else if (val == 3) {
            $http.get('controlMantenimiento/php/listaDeTecnicos.php').success(function (data) {
                $scope.lstProveedores = data;
            });
        }
        else if (val == 6) {
            $http.get('controlMantenimiento/php/listaDeClientes.php').success(function (data) {
                $scope.lstProveedores = data;
            });
        }
    });

    //=================================================================
    //      TRAE INFORMACION DE RELACION DE SALIDAS
    //=================================================================

    if ($scope.salida > 0) {
        traerListaCuentas(0);
        $http.get('almacen/php/consultaComboCondicionesSalidas.php').success(function (arrayCondiciones) {
            $scope.condiciones = arrayCondiciones;
        });
        $http.get('almacen/php/informacionSalidaMateria.php?tipoDeMiel=' + $scope.tipoDeMiel + '&idSalidaMateria=' + $scope.salida).success(function (data) {
            $scope.encabezadoSalida.idSalidaMateria = data.idSalidaMateria;
            $scope.encabezadoSalida.fecha = data.fecha;
            $scope.encabezadoSalida.idMotivo = data.idMotivo;
            $scope.encabezadoSalida.idProveedor = data.idProveedor;
            $scope.encabezadoSalida.tipoCliente = data.tipoCliente;
            $scope.proveedor = data.proveedor;
            $scope.condicion = data.condicion;
            $scope.encabezadoSalida.cantidadTotal = data.sumaCantidadTotal;
            $scope.encabezadoSalida.importeTotal = data.sumaImporteTotal;
            $scope.salidasDetallePush = data.salidasDetallePush;
        });
    } else if ($scope.salida == "nuevo") {
        traerListaCuentas(0);
        $http.get('almacen/php/consultaComboCondicionesSalidas.php').success(function (arrayCondiciones) {
            $scope.condiciones = arrayCondiciones;
        });
    } else {
        $scope.$watch('opcionCosecha', function (val) {
            $http.get('almacen/php/traeSalidasDeMateriasPrimas.php?tipoDeMiel=' + val).success(function (data) {
                $scope.infoSalida = data;
            });
        });
    }

    function calcularTotal() {
        $scope.encabezadoSalida.cantidadTotal = 0;
        $scope.encabezadoSalida.importeTotal = 0;
        angular.forEach($scope.salidasDetallePush, function (value, key) {
            $scope.encabezadoSalida.cantidadTotal += parseInt(value.cantidad);
            $scope.encabezadoSalida.importeTotal += parseInt(value.importe);
        });
    }

    //=================================================================
    //      FUNCION PARA AGREGAR
    //=================================================================
    $scope.agregarSalida = function () {
        if ($scope.salida == "nuevo") {
            // Si es nuevo lo agrega al arreglo
            if ($scope.validarSalida()) {
                $scope.detalleSalida.idMovimiento = $scope.detalleSalida.cuenta && $scope.detalleSalida.cuenta.idCuentaConcepto ? $scope.detalleSalida.cuenta.idCuentaConcepto : null;
                $scope.detalleSalida.movimiento = $scope.detalleSalida.cuenta && $scope.detalleSalida.cuenta.cuenta ? $scope.detalleSalida.cuenta.cuenta : '';

                $scope.detalleSalida.idSubcuenta = $scope.detalleSalida.subcuentaObj.idSubcuenta ? $scope.detalleSalida.subcuentaObj.idSubcuenta : null;
                $scope.detalleSalida.subcuenta = $scope.detalleSalida.subcuentaObj.subcuenta ? $scope.detalleSalida.subcuentaObj.subcuenta : '';

                $scope.detalleSalida.idConcepto = $scope.detalleSalida.producto && $scope.detalleSalida.producto.idSubSubcuenta ? $scope.detalleSalida.producto.idSubSubcuenta : null;
                $scope.detalleSalida.concepto = $scope.detalleSalida.producto && $scope.detalleSalida.producto.subSubcuenta ? $scope.detalleSalida.producto.subSubcuenta : '';

                $scope.detalleSalida.precioUnitario = $scope.detalleSalida.producto.precioUnitario;

                $scope.salidasDetallePush.push($scope.detalleSalida);
                $scope.detalleSalida = {};
                growl.success("Nuevo registro agregado");
                calcularTotal();
            }
        } else {
            // Si es editar lo guarda directo en la base de datos
            $scope.detalleSalida.idMovimiento = $scope.detalleSalida.cuenta && $scope.detalleSalida.cuenta.idCuentaConcepto ? $scope.detalleSalida.cuenta.idCuentaConcepto : null;
            $scope.detalleSalida.movimiento = $scope.detalleSalida.cuenta && $scope.detalleSalida.cuenta.cuenta ? $scope.detalleSalida.cuenta.cuenta : '';

            $scope.detalleSalida.idSubcuenta = $scope.detalleSalida.subcuentaObj.idSubcuenta ? $scope.detalleSalida.subcuentaObj.idSubcuenta : null;
            $scope.detalleSalida.subcuenta = $scope.detalleSalida.subcuentaObj.subcuenta ? $scope.detalleSalida.subcuentaObj.subcuenta : '';

            $scope.detalleSalida.idConcepto = $scope.detalleSalida.producto && $scope.detalleSalida.producto.idSubSubcuenta ? $scope.detalleSalida.producto.idSubSubcuenta : null;
            $scope.detalleSalida.concepto = $scope.detalleSalida.producto && $scope.detalleSalida.producto.subSubcuenta ? $scope.detalleSalida.producto.subSubcuenta : '';

            $scope.detalleSalida.precioUnitario = $scope.detalleSalida.producto.precioUnitario;

            $http.post("almacen/php/guardarEdicionDetalleSalida.php?tipoDeMiel=" + $scope.tipoDeMiel + "&idSalidaMateria=" + $scope.salida, { valor: $scope.detalleSalida })
                .success(function (respuesta) {
                    $scope.detalleSalida = {};
                    $http.get('almacen/php/informacionSalidaMateria.php?tipoDeMiel=' + $scope.tipoDeMiel + '&idSalidaMateria=' + $scope.salida).success(function (data) {
                        $scope.encabezadoSalida.idSalidaMateria = data.idSalidaMateria;
                        $scope.encabezadoSalida.fecha = data.fecha;
                        $scope.encabezadoSalida.idMotivo = data.idMotivo;
                        $scope.encabezadoSalida.tipoCliente = data.tipoCliente;
                        $scope.proveedor = data.proveedor;
                        $scope.condicion = data.condicion;
                        $scope.encabezadoSalida.idProveedor = data.idProveedor;
                        $scope.encabezadoSalida.cantidadTotal = data.sumaCantidadTotal;
                        $scope.encabezadoSalida.importeTotal = data.sumaImporteTotal;
                        $scope.salidasDetallePush = data.salidasDetallePush;
                        $http.post("almacen/php/guardarEdicionEncabezadoSalida.php?tipoDeMiel=" + $scope.tipoDeMiel + "&idSalidaMateria=" + $scope.salida, { valor: $scope.encabezadoSalida })
                            .success(function (respuesta) {
                                swal("", "Nueva salida agregada", "success");
                            });
                    });
                });
        }
    };

    //================================================================
    //      VALIDAR ANTES DEL PUSH
    //================================================================

    $scope.validarSalida = function () {

        if (!$scope.detalleSalida.cantidad) {
            growl.error("Se requiere una cantidad");
            return false;
        } else if (!$scope.detalleSalida.producto.precioUnitario) {
            growl.error("Se requiere un precio unitario");
            return false;
        } else if (!$scope.detalleSalida.subcuentaObj) {
            growl.error("Se requiere el catálogo");
            return false;
        }

        return true;
    };

    //=================================================================
    //      FUNCION PARA GUARDAR
    //=================================================================
    $scope.guardarSalida = function () {
        if ($scope.encabezadoSalida.fecha == undefined) {
            growl.error("Se requiere una fecha");
        } else if ($scope.encabezadoSalida.idProveedor == undefined) {
            growl.error("Se requiere un proveedor");
        } else if ($scope.encabezadoSalida.idMotivo == "") {
            growl.error("Se requiere un motivo");
        } else {
            $scope.informacionSalida.push($scope.encabezadoSalida);
            $scope.informacionSalida.push($scope.salidasDetallePush);
            $http.post("almacen/php/guardarSalidaMaterialPrima.php", { valor: $scope.informacionSalida })
                .success(function (respuesta) {
                    if (typeof (respuesta) == 'object' && respuesta.hasOwnProperty('error')) {
                        if (!respuesta.error) {
                            swal("Exito!", "Nueva salida agregada", "success");
                            return window.location.href = "#/salidaPrima";
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
    //      FUNCION PARA ELIMINAR
    //=================================================================
    $scope.eliminarSalida = function (indice) {
        $scope.salidasDetallePush.splice(indice, 1);
        growl.warning("Registro eliminado");
        calcularTotal();
    };

    $scope.eliminarSalidaBd = function (idDetalleSalida) {
        if($scope.tipoDeMiel == 1){
            $http.post("almacen/php/eliminarSalida.php?idDetalleSalida=" + idDetalleSalida)
            .success(function () {
                swal("Exito!", "Salida eliminada", "success");
                $http.get('almacen/php/informacionSalidaMateria.php?tipoDeMiel=' + $scope.tipoDeMiel + '&idSalidaMateria=' + $scope.salida).success(function (data) {
                    $scope.encabezadoSalida.idSalidaMateria = data.idSalidaMateria;
                    $scope.encabezadoSalida.fecha = data.fecha;
                    $scope.encabezadoSalida.idMotivo = data.idMotivo;
                    $scope.encabezadoSalida.idProveedor = data.idProveedor;
                    $scope.encabezadoSalida.tipoCliente = data.tipoCliente;
                    $scope.encabezadoSalida.cantidadTotal = data.sumaCantidadTotal;
                    $scope.encabezadoSalida.importeTotal = data.sumaImporteTotal;
                    $scope.salidasDetallePush = data.salidasDetallePush;
                    $scope.cantidadTotal = $scope.encabezadoSalida.cantidadTotal;
                    $scope.importeTotal = $scope.encabezadoSalida.importeTotal;
                    $http.post('almacen/php/actualizaTotalesSalidas.php?idSalidaMateria=' + $scope.salida + "&cantidadTotal=" + $scope.cantidadTotal + "&importeTotal=" + $scope.importeTotal).success(function () {
                    });
                });
            });
        } else if($scope.tipoDeMiel == 7){
            $http.post("almacen/php/eliminarSalidaNaranjo.php?idDetalleSalida=" + idDetalleSalida)
            .success(function () {
                swal("Exito!", "Salida eliminada", "success");
                $http.get('almacen/php/informacionSalidaMateria.php?tipoDeMiel=' + $scope.tipoDeMiel + '&idSalidaMateria=' + $scope.salida).success(function (data) {
                    $scope.encabezadoSalida.idSalidaMateria = data.idSalidaMateria;
                    $scope.encabezadoSalida.fecha = data.fecha;
                    $scope.encabezadoSalida.idMotivo = data.idMotivo;
                    $scope.encabezadoSalida.idProveedor = data.idProveedor;
                    $scope.encabezadoSalida.tipoCliente = data.tipoCliente;
                    $scope.encabezadoSalida.cantidadTotal = data.sumaCantidadTotal;
                    $scope.encabezadoSalida.importeTotal = data.sumaImporteTotal;
                    $scope.salidasDetallePush = data.salidasDetallePush;
                    $scope.cantidadTotal = $scope.encabezadoSalida.cantidadTotal;
                    $scope.importeTotal = $scope.encabezadoSalida.importeTotal;
                    $http.post('almacen/php/actualizaTotalesSalidasNaranjo.php?idSalidaMateria=' + $scope.salida + "&cantidadTotal=" + $scope.cantidadTotal + "&importeTotal=" + $scope.importeTotal).success(function () {
                    });
                });
            });
        } else{
            $http.post("almacen/php/eliminarSalidaOrganico.php?idDetalleSalida=" + idDetalleSalida)
            .success(function () {
                swal("Exito!", "Salida eliminada", "success");
                $http.get('almacen/php/informacionSalidaMateria.php?tipoDeMiel=' + $scope.tipoDeMiel + '&idSalidaMateria=' + $scope.salida).success(function (data) {
                    $scope.encabezadoSalida.idSalidaMateria = data.idSalidaMateria;
                    $scope.encabezadoSalida.fecha = data.fecha;
                    $scope.encabezadoSalida.idMotivo = data.idMotivo;
                    $scope.encabezadoSalida.idProveedor = data.idProveedor;
                    $scope.encabezadoSalida.tipoCliente = data.tipoCliente;
                    $scope.encabezadoSalida.cantidadTotal = data.sumaCantidadTotal;
                    $scope.encabezadoSalida.importeTotal = data.sumaImporteTotal;
                    $scope.salidasDetallePush = data.salidasDetallePush;
                    $scope.cantidadTotal = $scope.encabezadoSalida.cantidadTotal;
                    $scope.importeTotal = $scope.encabezadoSalida.importeTotal;
                    $http.post('almacen/php/actualizaTotalesSalidasOrganico.php?idSalidaMateria=' + $scope.salida + "&cantidadTotal=" + $scope.cantidadTotal + "&importeTotal=" + $scope.importeTotal).success(function () {
                    });
                });
            });
        }
    };

    $scope.editarEncabezadoSalida = function () {
        $("#mdlEditarSalida").modal();
    }

    $scope.modificarSalida = function () {
        $http.post("almacen/php/guardarEdicionEncabezadoSalida.php?tipoDeMiel=" + $scope.tipoDeMiel + "&idSalidaMateria=" + $scope.salida, { valor: $scope.encabezadoSalida })
            .success(function (respuesta) {
                swal("Éxito!", "Nueva salida agregada", "success");
                $http.get('almacen/php/informacionSalidaMateria.php?tipoDeMiel=' + $scope.tipoDeMiel + '&idSalidaMateria=' + $scope.salida).success(function (data) {
                    $scope.encabezadoSalida.idSalidaMateria = data.idSalidaMateria;
                    $scope.encabezadoSalida.fecha = data.fecha;
                    $scope.encabezadoSalida.idMotivo = data.idMotivo;
                    $scope.encabezadoSalida.tipoCliente = data.tipoCliente;
                    $scope.proveedor = data.proveedor;
                    $scope.condicion = data.condicion;
                    $scope.encabezadoSalida.idProveedor = data.idProveedor;
                    $scope.encabezadoSalida.cantidadTotal = data.sumaCantidadTotal;
                    $scope.encabezadoSalida.importeTotal = data.sumaImporteTotal;
                    $scope.salidasDetallePush = data.salidasDetallePush;
                    $http.post("almacen/php/guardarEdicionEncabezadoSalida.php?tipoDeMiel=" + $scope.tipoDeMiel + "&idSalidaMateria=" + $scope.salida, { valor: $scope.encabezadoSalida })
                        .success(function (respuesta) {
                            swal("Exito!", "Nueva salida agregada", "success");
                        });
                });
                $("#mdlEditarSalida").modal('hide');
            });
    };

    $scope.editarDetalleSalida = function (idDetalle) {
        $("#mdlEditarDetalleS").modal();
        $http.get('almacen/php/informacionSalidaMateria.php?tipoDeMiel=' + $scope.tipoDeMiel + '&idDetalleSalida=' + idDetalle).success(function (info) {
            $scope.detalleSalidaEdicion = info;
        });
    };

    function validarEdicionDetalle() {

        if (!$scope.detalleSalidaEdicion.cantidad) {
            growl.error("Se requiere una cantidad");
            return false;
        } else if (!$scope.detalleSalidaEdicion.precioUnitario) {
            growl.error("Se requiere un precio unitario");
            return false;
        } else if ($scope.detalleSalidaEdicion.nuevaCuenta && !$scope.detalleSalidaEdicion.nuevaSubcuentaObj) {
            growl.error('Selecciona el nuevo catálogo');
            return false;
        }



        return true;

    }

    $scope.modificarDetalleSalida = function () {

        if (validarEdicionDetalle()) {

            var nuevoObjetoDetalle = Object.assign({}, $scope.detalleSalidaEdicion);
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

            $http.post("almacen/php/editarMP.php?tipoDeMiel=" + $scope.tipoDeMiel + "&salida=0&idDetalleSalida=" + nuevoObjetoDetalle.idDetalleSalida, { valor: nuevoObjetoDetalle }).success(function (respuesta) {
                $http.get('almacen/php/informacionSalidaMateria.php?tipoDeMiel=' + $scope.tipoDeMiel + '&idSalidaMateria=' + $scope.salida).success(function (data) {
                    $scope.encabezadoSalida.idSalidaMateria = data.idSalidaMateria;
                    $scope.encabezadoSalida.fecha = data.fecha;
                    $scope.encabezadoSalida.cantidadTotal = data.sumaCantidadTotal;
                    $scope.encabezadoSalida.importeTotal = data.sumaImporteTotal;
                    $scope.encabezadoSalida.idProveedor = data.idProveedor;
                    $scope.encabezadoSalida.tipoCliente = data.tipoCliente;
                    $scope.encabezadoSalida.idMotivo = data.idMotivo;
                    $scope.salidasDetallePush = data.salidasDetallePush;
                    $http.post("almacen/php/guardarEdicionEncabezadoSalida.php?tipoDeMiel=" + $scope.tipoDeMiel + "&idSalidaMateria=" + $scope.salida, { valor: $scope.encabezadoSalida }).success(function (respuesta) {
                        swal("Exito!", "Modificación realizada", "success");
                    });
                });
                $("#mdlEditarDetalleS").modal('hide');
            });
        }
    };

    $scope.modalAlta = function () {
        $("#altaNombres").modal();
    };

    function verificarDatosNuevoProveedor(nuevoProveedor) {

        if (!nuevoProveedor.nombre) {
            growl.info('Escriba el nombre del proveedor');
            return false;
        }

        if (!nuevoProveedor.idSagarpa) {
            growl.info('Ingrese el número de ID SAGARPA');
            return false;
        }

        if (!nuevoProveedor.telefono) {
            growl.info('Ingrese el número de teléfono');
            return false;
        }

        if (!nuevoProveedor.tipoDeMiel) {
            growl.info('Seleccione un tipo de producto');
            return false;
        }

        return true;
    }

    $scope.guardarNombre = function (nuevoProveedor) {
        switch (nuevoProveedor.proveedorTipo) {
            case '1':
                if (verificarDatosNuevoProveedor(nuevoProveedor)) {
                    $http.post("almacen/php/guardarProv.php", { valor: nuevoProveedor })
                        .success(function (respuesta) {
                            swal("Exito!", "Registro agregado", "success");
                            $http.get('proveedores/php/listaProveedores.php').success(function (data) {
                                $scope.listaProveedores = data;
                                $scope.nuevoProveedor = {};
                                $("#altaNombres").modal('hide');
                            });
                        });
                }

                break;
            case '3':
                $http.post("controlMantenimiento/php/guardarTecnico.php", nuevoProveedor).success(function (info) {
                    swal("Exito!", "Registro agregado", "success");
                    $http.get('controlMantenimiento/php/listaDeTecnicos.php').success(function (data) {
                        $scope.lstProveedores = data;
                        $scope.nuevoProveedor = {};
                        $("#altaNombres").modal('hide');
                    });
                });
                break;
            default:
                break;
        }
    };

    $scope.imprimirSalida = function (idSalidaMateria) {

        if($scope.opcionCosecha == '1'){
            window.open('reportes/almacen/pdfReporteMPSalida.php?idSalidaMateria=' + idSalidaMateria);
        } else if($scope.opcionCosecha == '7'){
            window.open('reportes/almacen/pdfReporteMPSalidaNaranjo.php?idSalidaMateria=' + idSalidaMateria);
        } else if($scope.opcionCosecha == '2'){
            window.open('reportes/almacen/pdfReporteMPSalidaOrganico.php?idSalidaMateria=' + idSalidaMateria);
        }

    };

    $scope.modalDescargarReporte = function () {
        $scope.opcionDescargarMP = {};
        $('#descargarReporteModal').modal();
    }
    $scope.descargarReporteMP = function (opciones) {
        if (opciones.opcion) {
            if (opciones.opcion == '1') {
                // Todo 
                $('#descargarReporteModal').modal('hide');
                return window.location.href = 'reportes/almacen/xlsSalidaMP.php?auto&tipoDeMiel=' + opciones.tipo;
            } else if (opciones.opcion == '2') {
                if (opciones.fecha1 && opciones.fecha2) {
                    $('#descargarReporteModal').modal('hide');
                    return window.location.href = 'reportes/almacen/xlsSalidaMP.php?auto&tipoDeMiel=' + opciones.tipo + '&fecha1=' + opciones.fecha1 + '&fecha2=' + opciones.fecha2;
                } else {
                    growl.info('Selecciona las fechas');
                }
            }
        } else {
            growl.info('Selecciona una opcion');
        }
    }

    // Eliminar el reporte de salida
    $scope.eliminarReporteSalidaMP = function () {
        if ($scope.salida) {
            swal({
                title: '¿Eliminar la salida de materia prima?',
                text: 'No se podrá recuperar los datos',
                type: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Eliminar',
                cancelButtonText: 'Cancelar',
                closeOnConfirm: false
            }, function (c) {
                if (c && c == true) {
                    $http.post('almacen/php/eliminarSalidaMP.php?eliminarReporte', $scope.salida).success(function (data) {
                        if (data.hasOwnProperty('error')) {
                            if (data.error) {
                                swal('Error', data.message, 'error');
                            } else {
                                window.location.href = '#/salidaPrima';
                                swal('Hecho', data.message, 'success');
                            }
                        } else {
                            growl.error('Error');
                            console.error(data);
                        }
                    });
                }
            });
        }
    }

}]);