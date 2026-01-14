form.controller('otrasSalidasCtrl', ['$scope', '$http', 'growl', '$routeParams', '$q', function ($scope, $http, growl, $routeParams, $q) {

    var date = new Date();
    var _mes = date.getMonth() + 1;
    var _fecha = date.getFullYear() + '-' + _mes + '-' + date.getDate();

    $scope.salida = {
        totalPeso: 0,
        totalImporte: 0
    };
    $scope.nuevoConcepto = {};
    $scope.guardando_reporte = false;
    // 

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

        $scope.calcularimportePeso();
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

    traerListaCuentas(0);

    // 
    function calcularTotalSalida() {
        $scope.salida.totalPeso = 0;
        $scope.salida.totalImporte = 0;
        $scope.salida.conceptos.forEach(function (concepto) {
            $scope.salida.totalPeso += parseFloat(concepto.kg);
            $scope.salida.totalImporte += parseFloat(concepto.importe);
        });
    }

    // function traeProductosMiel() {
    //     $http.post('catalogos/php/traeConceptosCatalogo.php', 1).success(function (productosMiel) {
    //         if (productosMiel.hasOwnProperty('error')) {
    //             if (productosMiel.error) {
    //                 swal('Error', productosMiel.message, 'error');
    //             } else {
    //                 $scope.productosMiel = productosMiel.conceptos;
    //             }
    //         } else {
    //             growl.error('Error');
    //             console.error(productosMiel);
    //         }
    //     });
    // }
    // traeProductosMiel();

    function traeTiposDeMiel() {
        $http.get('utilerias/php/traeTiposDeMiel.php').success(function (data) {
            if (!data.hasOwnProperty('error')) {
                $scope.tiposDeMiel = data;
            }
        })
    }
    traeTiposDeMiel();

    function traerNombreCajaChica() {
        $http.get('proveedores/php/listaProveedores.php?clientes').success(function (data) {
            $scope.clientes = data;
        })
        $http.get('controlMantenimiento/php/listaPersonalOM.php').success(function (data) {
            $scope.lstOMPersonal = data;
        });
        $http.post('controlAdministrativo/php/traeCatalogoProveedores.php', true).success(function (data) {
            $scope.catalogoTablaProveedoresMantto = data.data;
        });
        $http.post('controlAdministrativo/php/traeCatalogoClientes.php', true).success(function (data) {
            $scope.catalogoTablaClientes = data.data;
        });
    };

    traerNombreCajaChica();

    $http.get('almacen/php/dameOtrasSalidas.php').success(function (data) {
        if (data.hasOwnProperty('error')) {
            if (data.error) {
                swal('Error', data.message, 'error');
            } else {
                $scope.salidas = data.salidas;
            }
        } else {
            growl.error('Error');
            console.error(data);
        }
    });

    function obtenerConceptosSalida() {
        var promise = $q.defer();
        $http.get('almacen/php/dameOtrosConceptosSalida.php').success(function (data) {
            if (data.hasOwnProperty('error')) {
                if (data.error) {
                    swal('Error', data.message, 'error');
                } else {
                    $scope.conceptosSalida = data.conceptos;
                    promise.resolve(true);
                }
            } else {
                growl.error('Error');
                console.error(data);
            }
        });
        return promise.promise;
    }
    obtenerConceptosSalida();

    function verificarDatosSalida() {

        if (!$scope.salida.tipoDePersona) {
            return false;
        } else if ($scope.salida.tipoDePersona != '2' && !$scope.salida.idPersona) {
            return false;
        } else if (!$scope.salida.fecha) {
            return false;
        } else if (!$scope.salida.conceptos || $scope.salida.conceptos.length == 0) {
            return false;
        }
        return true;
    }

    function verificarNuevoConcepto() {
        if (!$scope.nuevoConcepto.idConcepto) {
            growl.info('Seleccione el concepto');
            return false;
        } else if (!$scope.nuevoConcepto.hasOwnProperty('kg') && !$scope.nuevoConcepto.hasOwnProperty('cantidad')) {
            growl.info('Indique el peso');
            return false;
        } else if (!$scope.nuevoConcepto.hasOwnProperty('importe')) {
            growl.info('Indique el importe');
            return false;
        } else if ($scope.nuevoConcepto.ocupaTambor && !$scope.nuevoConcepto.folioTambor) {
            growl.info('Indique el folio del tambor ocupado');
            return false;
        } else if (!$scope.nuevoConcepto.idTipoDeMiel) {
            growl.info('Indique el tipo de producto');
            return false;
        }
        return true;
    }

    function setTipoDeMiel(indice = undefined) {
        if (indice >= 0) {
            concepto = $scope.salida.conceptos[indice];
            if (concepto.idTipoDeMiel) {
                var tdm = $scope.tiposDeMiel.filter(function (tipo) {
                    if (tipo.idTipoDeMiel == concepto.idTipoDeMiel) {
                        return tipo;
                    }
                });
                $scope.salida.conceptos[indice].tipoDeMiel = tdm[0].tipoDeMiel;
            } else {
                $scope.salida.conceptos[indice].tipoDeMiel = '';
            }
        } else {
            $scope.salida.conceptos.forEach(function (concepto, index) {
                if (concepto.idTipoDeMiel) {
                    var tdm = $scope.tiposDeMiel.filter(function (tipo) {
                        if (tipo.idTipoDeMiel == concepto.idTipoDeMiel) {
                            return tipo;
                        }
                    });
                    $scope.salida.conceptos[index].tipoDeMiel = tdm[0].tipoDeMiel;
                } else {
                    $scope.salida.conceptos[index].tipoDeMiel = '';
                }
            });
        }
    }

    function setConcepto(indice = undefined) {
        if (!$scope.conceptosSalida || $scope.conceptosSalida == undefined) {
            obtenerConceptosSalida().then(function (res) {
                if (res) {
                    if (indice >= 0) {
                        concepto = $scope.salida.conceptos[indice];
                        var conc = $scope.conceptosSalida.filter(function (c) {
                            if (c.idConcepto == concepto.idConcepto) {
                                return c;
                            }
                        });
                        $scope.salida.conceptos[indice].concepto = conc[0].concepto;
                    } else {
                        $scope.salida.conceptos.forEach(function (concepto, index) {
                            var conc = $scope.conceptosSalida.filter(function (c) {
                                if (c.idConcepto == concepto.idConcepto) {
                                    return c;
                                }
                            });
                            $scope.salida.conceptos[index].concepto = conc[0].concepto;
                        });
                    }
                }
            })
        } else {
            if (indice >= 0) {
                concepto = $scope.salida.conceptos[indice];
                var conc = $scope.conceptosSalida.filter(function (c) {
                    if (c.idConcepto == concepto.idConcepto) {
                        return c;
                    }
                });
                $scope.salida.conceptos[indice].concepto = conc[0].concepto;
            } else {
                $scope.salida.conceptos.forEach(function (concepto, index) {
                    var conc = $scope.conceptosSalida.filter(function (c) {
                        if (c.idConcepto == concepto.idConcepto) {
                            return c;
                        }
                    });
                    $scope.salida.conceptos[index].concepto = conc[0].concepto;
                });
            }
        }
    }
    function setProducto(indice = undefined) {
        if (indice >= 0) {
            concepto = $scope.salida.conceptos[indice];
            var prod = $scope.productosMiel.filter(function (p) {
                if (p.idSubconceptoCC == concepto.idSubconceptoCC) {
                    return p;
                }
            });
            $scope.salida.conceptos[indice].producto = prod[0];
        } else {
            $scope.salida.conceptos.forEach(function (concepto, index) {
                // var prod = $scope.productosMiel.filter(function (p) {
                //     if (p.idSubconceptoCC == concepto.idSubconceptoCC) {
                //         return p;
                //     }
                // });
                $scope.salida.conceptos[index].producto = { idSubSubcuenta: concepto.idSubSubcuenta };
            });
        }
    }

    $scope.agregarConceptoSalida = function () {
        if (verificarNuevoConcepto()) {
            if (!$scope.salida.conceptos || $scope.salida.conceptos == undefined) {
                $scope.salida.conceptos = [];
            }
            var newIndex = $scope.salida.conceptos.push($scope.nuevoConcepto) - 1;

            // Establece el nombre del tipo de miel en una propiedad:
            setTipoDeMiel(newIndex);

            setConcepto(newIndex);
            $scope.nuevoConcepto = {};
            calcularTotalSalida();
        }
    };

    $scope.guardarNuevaSalida = function () {
        if (verificarDatosSalida()) {
            $scope.guardando_reporte = true;
            $scope.salida.idMes = $scope.salida.fecha.split('-')[1];
            $http.post('almacen/php/guardarNuevaSalida.php', $scope.salida).success(function (data) {
                if (data.hasOwnProperty('error')) {
                    if (data.error) {
                        swal('Error', data.message, 'error');
                    } else {
                        swal('Éxito', data.message, 'success');
                        return window.location.href = '#/otrasSalidas';
                    }
                } else {
                    growl.error('Error');
                    console.error(data);
                }
                $scope.guardando_reporte = false;
            })
        } else {
            growl.info('Revisa todos los campos');
        }
    };

    $scope.mostrarEnInventario = function (infoTambo) {
        $http.post("almacen/php/mostrarEnInventarioMiel.php?id=" + infoTambo.idOtrasSalidasDetalle + "&valor=" + infoTambo.cajaChica)
            .success(function (respuesta) {
                growl.success('Realizado');
            });
    };

    $scope.abrirModalAltasRapidas = function () {
        $scope.altaRapida = {};
        $http.get('personalOaxacaMiel/php/listaAreas.php').success(function (datas) {
            $scope.listaAreas = datas;
        });

        $http.get('personalOaxacaMiel/php/listaPuestos.php').success(function (datos) {
            $scope.listaPuestos = datos;
        });
        $("#modalAltasRapidas").modal();
    };

    $scope.calcularimportePeso = function () {
        if ($scope.nuevoConcepto.cantidad > 0 && $scope.nuevoConcepto.producto) {
            $scope.nuevoConcepto.kg = parseFloat($scope.nuevoConcepto.cantidad) * parseFloat($scope.nuevoConcepto.producto.peso);
            $scope.nuevoConcepto.importe = parseFloat($scope.nuevoConcepto.cantidad) * parseFloat($scope.nuevoConcepto.producto.precioUnitario);
        } else {
            $scope.nuevoConcepto.kg = 0;
            $scope.nuevoConcepto.importe = 0;
        }
    }

    $scope.eliminarConcepto = function (index) {
        $scope.salida.conceptos.splice(index, 1);
        calcularTotalSalida();
    }

    // Altas rapidas

    function verificarDatosNuevoProveedor(altaRapida) {

        if (!altaRapida.nombre) {
            growl.info('Escriba el nombre del proveedor');
            return false;
        }

        if (!altaRapida.idSagarpa) {
            growl.info('Ingrese el número de ID SAGARPA');
            return false;
        }

        if (!altaRapida.telefono) {
            growl.info('Ingrese el número de teléfono');
            return false;
        }

        if (!altaRapida.tipoDeMiel) {
            growl.info('Seleccione un tipo de producto');
            return false;
        }

        return true;
    }

    $scope.guardarNuevaAltaRapida = function (altaRapida) {
        switch (altaRapida.tipoDePersona) {
            case '1':
                if (verificarDatosNuevoProveedor(altaRapida)) {
                    $http.post("almacen/php/guardarProv.php", { valor: altaRapida }).success(function (respuesta) {
                        swal(respuesta.encabezado, respuesta.mensaje, respuesta.tipo);
                        altaRapida = {};
                        $http.get('proveedores/php/listaProveedores.php?clientes').success(function (data) {
                            $scope.clientes = data;
                            $("#modalAltasRapidas").modal('hide');
                        });
                    });
                }
                break;
            case '3':
                $http.post("controlAdministrativo/php/guardarProveedor.php", { datos: altaRapida, fecha: _fecha }).success(function (info) {
                    swal("", info.message, info.swal);
                    if (!info.error) {
                        altaRapida = {};
                        $http.post('controlAdministrativo/php/traeCatalogoProveedores.php', true).success(function (data) {
                            $scope.catalogoTablaProveedoresMantto = data.data;
                            $("#modalAltasRapidas").modal('hide');
                        });
                    }
                });
                break;
            case '4':
                $http.post("personalOaxacaMiel/php/guardarPersonalOM.php?alta_rapida", altaRapida).success(function (info) {
                    if (info.hasOwnProperty('error')) {
                        if (info.error) {
                            swal('Error', info.message, 'error');
                        } else {
                            $("#modalAltasRapidas").modal('hide');
                            swal('Éxito', info.message, 'success');
                            $http.get('controlMantenimiento/php/listaPersonalOM.php').success(function (data) {
                                $scope.lstOMPersonal = data;
                                $scope.altaRapida = {};
                            });
                        }
                    } else {
                        growl.error('Error');
                        console.error(info);
                    }
                });
                break;
            case '6':
                $http.post("controlAdministrativo/php/guardarCliente.php", altaRapida).success(function (info) { //Guardar cliente con alta rápida
                    swal("", info.message, info.swal);
                    if (!info.error) {
                        altaRapida = {};
                        $http.post('controlAdministrativo/php/traeCatalogoClientes.php', true).success(function (data) {
                            $scope.catalogoTablaClientes = data.data;
                            $("#modalAltasRapidas").modal('hide');
                        });
                    }
                });
                break;
        }
    };

    function traerReporteSalida(idSalida) {
        $http.get('almacen/php/dameReporteSalida.php?idSalida=' + idSalida).success(function (data) {
            if (data.hasOwnProperty('error')) {
                if (data.error) {
                    swal('Error', data.message, 'error');
                } else {
                    $scope.salida = data.salida;
                    setProducto();
                    setTipoDeMiel();
                    setConcepto();
                }
            } else {
                growl.error('Error');
                console.error(data);
            }
        })
    }

    if ($routeParams.idSalida) {
        traerReporteSalida($routeParams.idSalida);
    }

}]);