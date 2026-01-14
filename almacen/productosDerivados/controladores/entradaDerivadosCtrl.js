form.config(function ($routeProvider) {
    $routeProvider.when('/entradasDerivados', {
        templateUrl: 'almacen/productosDerivados/menuEntradaDerivados.html',
        controller: 'entradaDerivadosCtrl'
    }).when('/nuevaEntradaDerivados/:idEntrada', {
        templateUrl: 'almacen/productosDerivados/nuevaEntradaDerivados.html',
        controller: 'entradaDerivadosCtrl'
    })
});
form.controller('entradaDerivadosCtrl', ['$scope', '$http', '$routeParams', 'growl', '$location', '$q', '$rootScope', function ($scope, $http, $routeParams, growl, $location, $q, $rootScope) {

    $scope.idEntrada = $routeParams.idEntrada;
    $scope.nuevoConcepto = {};
    $scope.entrada = {
        conceptos: Array(),
        total: 0
    }

    $scope.cargandoDatos = false;
    $scope.tipoReporte = '2';
    var date = new Date();
    var _mes = date.getMonth() + 1;
    var _fecha = date.getFullYear() + '-' + _mes + '-' + date.getDate();
    $scope.mostrarMes = _mes.toString();

    $http.get('almacen/php/consultaComboMeses.php').success(function (data) {
        $scope.listaDeMeses = data;
    });

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
        if ($scope.nuevoConcepto.cantidad > 0 && $scope.nuevoConcepto.costoUnitario) {
            $scope.nuevoConcepto.importe = parseFloat($scope.nuevoConcepto.cantidad) * parseFloat($scope.nuevoConcepto.costoUnitario);
        } else {
            $scope.nuevoConcepto.importe = 0;
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

    function obtenerEntradas() {

        $scope.cargandoDatos = true;
        url = 'almacen/productosDerivados/php/obtenerReportesDerivados.php?tipo=1';
        if ($scope.mostrarMes && $location.path() == '/entradasDerivados') {
            url += '&mes=' + $scope.mostrarMes;
        }
        if ($scope.tipoReporte !== '0') {
            url += '&reporte=' + $scope.tipoReporte;
        }
        $http.get(url).success(function (data) {
            $scope.listaEntradasDerivados = data.data;
            if (data.error) {
                console.error(data.message);
            }
            $scope.cargandoDatos = false;
        });
    };

    $scope.$watch('[ mostrarMes, tipoReporte]', function (val) {
        obtenerEntradas();
    });

    function prepararNuevaEntradaDerivados() {
        traerListaCuentas(1);
        $http.get("proveedores/php/dameProveedores.php?estado=0").success(function (info) {
            $scope.listaApicultores = info;
        });

        $http.post('controlAdministrativo/polizaCheque/php/traerNombres.php', 3).success(function (data) {
            $scope.listaProveedores = data;
        });

        $http.post('controlAdministrativo/polizaCheque/php/traerNombres.php', 6).success(function (data) {
            $scope.listaClientes = data;
        });

        // Clasificaciones de la entrada de cera y productos apícolas, derivados*
        $http.get('controlAdministrativo/php/clasificacionesCeraYApicolas.php?tipo=1').success(function (data) {
            $scope.clasificaciones = data.clasificaciones;
        });
        $http.get('controlAdministrativo/php/conceptosIngreso.php').success(function (data) {
            $scope.listaConceptos = data;
        });
    };

    function guardarEntrada(entrada) {
        $http.post("almacen/productosDerivados/php/guardarReporteEntrada.php?tipo=1", entrada).success(function (info) {
            if (info.hasOwnProperty('error')) {
                if (!info.error) {
                    $rootScope.lockTemplate = false;
                    swal('', info.message, info.swal);
                    window.location.href = '#/entradasDerivados';
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
        } else if (!concepto.clasificacion) {
            growl.info('Seleccione la clasificación');
            return false;
        } else if (!concepto.costoUnitario) {
            growl.info('Indique el precio unitario');
            return false;
        }
        return true;
    };

    function verificarEntrada(entrada) {
        if (!entrada.fecha) {
            growl.info('Selecciona la fecha de entrada');
            return false;
        } else if (entrada.proveedor.id == '0') {
            growl.info('Seleccione al proveedor');
            return false;
        } else if (entrada.conceptos.length < 1) {
            growl.info('La entrada debe contener al menos 1 movimiento');
            return false;
        }
        return true;
    };

    function calcularTotalEntrada() {
        $scope.entrada.total = 0;
        $scope.entrada.conceptos.forEach(concepto => {
            if (!isNaN(parseInt(concepto.importe))) {
                $scope.entrada.total += parseFloat(concepto.importe);
            }
        });
    };

    function buscarUnidadDeConceptos(listaConceptosDerivados) {
        $scope.entrada.conceptos.forEach((element, index) => {
            var unidadDelConcepto = listaConceptosDerivados.filter(function (unidad) {
                if (element.unidad == unidad.idSubconceptoCC) {
                    return unidad;
                }
            });
            $scope.entrada.conceptos[index].unidad = unidadDelConcepto[0];
        });
    };

    function seleccionaClasificacionConcepto(clasificaciones) {
        $scope.entrada.conceptos.forEach((element, index) => {
            var clasificacionDelConcepto = clasificaciones.filter(function (cl) {
                if (element.clasificacion == cl.idClasificacionCera) {
                    return cl;
                }
            });
            $scope.entrada.conceptos[index].clasificacion = clasificacionDelConcepto[0];
        });
    };

    function traerEntradaDerivados(idEntrada) {
        $http.get('almacen/productosDerivados/php/obtenerEntradaDerivados.php?idEntrada=' + idEntrada).success(function (data) {
            if (data.hasOwnProperty('error')) {
                if (!data.error) {
                    $scope.entrada = data.data;
                    $scope.entrada.clasificacion = { idClasificacionCera: data.data.clasificacion };
                    $scope.entrada.proveedor = { id: data.data.idProveedor };

                    if ($scope.listaConceptosDerivados && $scope.listaConceptosDerivados.length > 0) {
                        buscarUnidadDeConceptos($scope.listaConceptosDerivados);
                    }
                    // else {

                    //     // Mandamos en el siguiente HTTP REQ como idConcepto el 3, para traer los subconceptos de 'Productos apícola'
                    //     $http.get('controlAdministrativo/php/subconceptosAnidados.php?idConceptoCC=3').success(function (data) {
                    //         // $scope.listaConceptosDerivados = data;
                    //         buscarUnidadDeConceptos(data);
                    //     });
                    // }

                    if ($scope.clasificaciones && $scope.clasificaciones.length > 0) {
                        seleccionaClasificacionConcepto($scope.clasificaciones);
                    } else {
                        // Clasificaciones de la entrada de cera y productos apícolas, derivados*
                        $http.get('controlAdministrativo/php/clasificacionesCeraYApicolas.php?tipo=1').success(function (data) {
                            seleccionaClasificacionConcepto(data);
                        });
                    }

                } else {
                    swal('Error', data.message, 'error');
                }
            } else {
                growl.error('Error');
                console.error(data);
            }
        });
    };


    $scope.altaRapida = function () {
        $('#modalAltaRapida').modal();
    }

    function verificarDatosNuevoProveedor(altasCC) {

        if (!altasCC.nombre) {
            growl.info('Escriba el nombre del proveedor');
            return false;
        }

        if (!altasCC.idSagarpa) {
            growl.info('Ingrese el número de ID SAGARPA');
            return false;
        }

        if (!altasCC.telefono) {
            growl.info('Ingrese el número de teléfono');
            return false;
        }

        if (!altasCC.tipoDeMiel) {
            growl.info('Seleccione un tipo de producto');
            return false;
        }

        return true;
    }

    $scope.guardarNuevoProveedorCC = function (altasCC) {
        switch (altasCC.tipoPersona) {
            case '1':
                if (verificarDatosNuevoProveedor(altasCC)) {
                    $http.post("almacen/php/guardarProv.php", { valor: altasCC }).success(function (respuesta) {
                        swal(respuesta.encabezado, respuesta.mensaje, respuesta.tipo);
                        $scope.altasCC = {};
                        prepararNuevaEntradaDerivados();
                        $('#modalAltaRapida').modal('hide');
                    });
                }
                break;
            case '3':
                $http.post("controlAdministrativo/php/guardarProveedor.php", { datos: altasCC, fecha: _fecha }).success(function (info) {
                    swal("", info.message, info.swal);
                    if (!info.error) {
                        $scope.altasCC = {};
                        prepararNuevaEntradaDerivados();
                        $('#modalAltaRapida').modal('hide');
                    }
                });
                break;
            case '6':
                $http.post("controlAdministrativo/php/guardarCliente.php", altasCC).success(function (info) {
                    swal("", info.message, info.swal);
                    if (!info.error) {
                        $scope.altasCC = {};
                        prepararNuevaEntradaDerivados();
                        $('#modalAltaRapida').modal('hide');
                    }
                });
                break;
            default:
                break;
        }
    };

    $scope.agregarConceptoEntradaDerivados = function () {
        // Esta función agrega un nuevo concepto a la tabla
        if (validarNuevoConcepto($scope.nuevoConcepto)) {

            $scope.nuevoConcepto.idMovimiento = $scope.nuevoConcepto.cuenta && $scope.nuevoConcepto.cuenta.idCuentaConcepto ? $scope.nuevoConcepto.cuenta.idCuentaConcepto : null;
            $scope.nuevoConcepto.movimiento = $scope.nuevoConcepto.cuenta && $scope.nuevoConcepto.cuenta.cuenta ? $scope.nuevoConcepto.cuenta.cuenta : '';

            $scope.nuevoConcepto.idSubcuenta = $scope.nuevoConcepto.subcuentaObj.idSubcuenta ? $scope.nuevoConcepto.subcuentaObj.idSubcuenta : null;
            $scope.nuevoConcepto.subcuenta = $scope.nuevoConcepto.subcuentaObj.subcuenta ? $scope.nuevoConcepto.subcuentaObj.subcuenta : '';

            $scope.nuevoConcepto.idConcepto = $scope.nuevoConcepto.producto && $scope.nuevoConcepto.producto.idSubSubcuenta ? $scope.nuevoConcepto.producto.idSubSubcuenta : null;
            $scope.nuevoConcepto.concepto = $scope.nuevoConcepto.producto && $scope.nuevoConcepto.producto.subSubcuenta ? $scope.nuevoConcepto.producto.subSubcuenta : '';

            $scope.entrada.conceptos.push($scope.nuevoConcepto);
            $scope.nuevoConcepto = {};
            calcularTotalEntrada();
        } else {
            growl.error('Verifica los campos necesarios');
        }
    };

    $scope.guardarEntrada = function () {
        if (verificarEntrada($scope.entrada)) {
            guardarEntrada($scope.entrada);
        }
    };

    $scope.eliminarConceptoEntradaDerivados = function (index) {
        $scope.entrada.conceptos.splice(index, 1);
        calcularTotalEntrada();
    };

    $scope.nuevoConceptoEntrada = function () {
        $scope.nvoConcepto = {
            concepto: '',
            subconceptos: []
        };
        $("#modalNuevoConcepto").modal();
    };

    $scope.guardarNuevoConcepto = function (nuevoConcepto) {
        $http.post('controlAdministrativo/php/nuevoConceptoCajaChica.php?opcion=concepto', nuevoConcepto).success(function (data) {
            if (data.error) {
                growl.error(data.mensaje);
            } else {
                prepararNuevaEntradaDerivados();
                $("#modalNuevoConcepto").modal('hide');
            }
        });
    };

    $scope.f_nuevoSubConcepto = function () {
        $scope.nvoConcepto.subconceptos.push({});
    };

    // $scope.imprimirReporte = function () {
    //     if ($scope.idEntrada) {
    //         window.open('reportes/almacen/pdfReporteApicola.php?idEntrada=' + $scope.idEntrada);
    //     }
    // }

    if ($location.path() == '/entradasDerivados') {
        obtenerEntradas();
    } else if ($scope.idEntrada == 0) {
        traerListaCuentas(1);
        $scope.$watch('[entrada.conceptos, nuevoConcepto]', function () {
            if (angular.equals($scope.nuevoConcepto, { importe: NaN }) && $scope.entrada.conceptos.length == 0 || $scope.entrada.idEntrada) {
                $rootScope.lockTemplate = false;
            } else {
                $rootScope.lockTemplate = true;
            }
        }, true);

        prepararNuevaEntradaDerivados();
    } else if ($scope.idEntrada > 0) {
        prepararNuevaEntradaDerivados();
        traerEntradaDerivados($scope.idEntrada);
    }

    /**27 dic 2018 nuevas cuentas */
    // $scope.mostrarDatosUnidad = function () {
    //     if (isNaN($scope.nuevoConcepto.unidad.precioUnitario) || $scope.nuevoConcepto.unidad.precioUnitario == null) {
    //         $scope.nuevoConcepto.costoUnitario = 0;
    //     } else {
    //         $scope.nuevoConcepto.costoUnitario = parseFloat($scope.nuevoConcepto.unidad.precioUnitario);
    //     }
    // }
}]);