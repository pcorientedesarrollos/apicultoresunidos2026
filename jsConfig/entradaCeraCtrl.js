form.controller('entradaCeraCtrl', ['$scope', '$http', '$routeParams', 'growl', '$location', '$q', '$rootScope', function ($scope, $http, $routeParams, growl, $location, $q, $rootScope) {

    $scope.tipoCera = '1';
    $scope.cargandoDatos = false;
    $scope.idEntradaCera = $routeParams.idEntradaCera;
    $scope.nuevoConcepto = {};
    $scope.entrada = {
        conceptos: Array(),
        total: 0,
        kg: 0
    }

    $scope.tipoReporte = '2';

    var date = new Date();
    var _mes = date.getMonth() + 1;
    var _fecha = date.getFullYear() + '-' + _mes + '-' + date.getDate();

    $scope.mostrarMes = _mes.toString();
    $http.get('almacen/php/consultaComboMeses.php').success(function (data) {
        $scope.listaDeMeses = data;
    });

    function traeListaZonas(tipo) {
        $http.get('almacen/php/listaDeZonasCera.php?tipo=' + tipo).success(function (data) {
            $scope.listaZonas = data;
        });

    }


    if (window.localStorage.getItem('ENTRADA_CERA') != null) {
        $scope.tipoCera = window.localStorage.getItem('ENTRADA_CERA');
    }

    $scope.$watch('[tipoCera, mostrarMes, tipoReporte]', function (val) {
        if ($scope.tipoCera) {
            window.localStorage.setItem('ENTRADA_CERA', $scope.tipoCera);
            $scope.listaEntradasCera = [];
            // $scope.filtroPagado = null;
            traerEntradasCera();
        }
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
        if ($scope.nuevoConcepto.cantidad > 0 && $scope.nuevoConcepto.pesoUnidad) {
            $scope.nuevoConcepto.kgTotal = parseFloat($scope.nuevoConcepto.cantidad) * parseFloat($scope.nuevoConcepto.pesoUnidad);
        } else {
            $scope.nuevoConcepto.kgTotal = 0;
        }

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

    function traerEntradasCera() {
        $scope.listaEntradasCera = null;
        if ($scope.tipoCera) {
            $scope.cargandoDatos = true;
            url = 'almacen/php/dameReportesDeCera.php?tipo=1&tipoCera=' + $scope.tipoCera;
            if ($scope.mostrarMes && $location.path() == '/entradaCera') {
                url += '&mes=' + $scope.mostrarMes;
            }
            if ($scope.tipoReporte !== '0') {
                url += '&reporte=' + $scope.tipoReporte;
            }
            $http.get(url).success(function (data) {
                $scope.listaEntradasCera = data.data;
                if (data.error) {
                    console.error(data.message);
                }
                $scope.cargandoDatos = false;
            });
        }
    };

    $scope.traeListaProveedores = function (valor) {
        $http.post('proveedores/php/dameProveedores.php?todos=1&tipo=' + valor + '&estado=0').success(function (info) {
            $scope.listaApicultores = info;
        });

        traeListaZonas(valor);

    }

    function prepararNuevaEntradaCera() {

        traerListaCuentas(1);


        $http.post('controlAdministrativo/polizaCheque/php/traerNombres.php', 3).success(function (data) {
            $scope.listaProveedores = data;
        });

        $http.post('controlAdministrativo/polizaCheque/php/traerNombres.php', 6).success(function (data) {
            $scope.listaClientes = data;
        });

        // Clasificaciones de la entrada de cera
        $http.get('controlAdministrativo/php/clasificacionesCeraYApicolas.php?tipo=1').success(function (data) {
            $scope.clasificaciones = data.clasificaciones;
        });

        // Mandamos en el siguiente HTTP REQ como idConcepto el 2, para traer los subconceptos de 'Cera'
        // 27/12/2018 Ya no necesitamos estos conceptos de cera
        // $http.get('controlAdministrativo/php/subconceptosAnidados.php?idConceptoCC=2').success(function (data) {
        //     $scope.listaConceptosCera = data;
        // });

        $http.get('controlAdministrativo/php/conceptosIngreso.php').success(function (data) {
            $scope.listaConceptos = data;
        });
    };

    function guardarEntrada(entrada) {
        $http.post("almacen/php/guardarReporteCera.php?tipo=1", entrada).success(function (info) {
            if (info.hasOwnProperty('error')) {
                if (!info.error) {
                    $rootScope.lockTemplate = false;
                    swal('', info.message, info.swal);
                    window.location.href = '#/entradaCera';
                } else {
                    growl.error('Error');
                    console.error(info.message);
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
        } else if (!concepto.pesoUnidad) {
            growl.info('Indique el peso');
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
            growl.info('Seleccione la fecha de entrada');
            return false;
        } else if (entrada.proveedor.id == '0') {
            growl.info('Seleccione al proveedor');
            return false;
        } else if (entrada.conceptos.length < 1) {
            growl.info('La entrada debe contener al menos 1 movimiento');
            return false;
        } else if (!entrada.tipoCera) {
            growl.info('Especifique el tipo de cera.');
            return false;
        }
        return true;
    };

    function calcularTotalEntrada() {
        $scope.entrada.total = 0;
        $scope.entrada.kg = 0;
        $scope.entrada.conceptos.forEach(concepto => {
            if (!isNaN(parseInt(concepto.importe))) {
                $scope.entrada.total += parseFloat(concepto.importe);
                $scope.entrada.kg += parseFloat(concepto.kgTotal);
            }
        });
    };

    // function buscarUnidadDeConceptos(listaConceptosCera) {
    //     $scope.entrada.conceptos.forEach((element, index) => {
    //         var unidadDelConcepto = listaConceptosCera.filter(function (unidad) {
    //             if (element.unidad == unidad.idSubconceptoCC) {
    //                 return unidad;
    //             }
    //         });
    //         $scope.entrada.conceptos[index].unidad = unidadDelConcepto[0];
    //     });
    // };

    function seleccionaClasificacionConcepto(clasificaciones) {
        $scope.entrada.conceptos.forEach((element, index) => {
            var clasificacionDelConcepto = clasificaciones.filter(function (cl) {
                if (element.clasificacion == cl.idClasificacionCera) {
                    return cl;
                }
            });
            $scope.entrada.conceptos[index].clasificacion = clasificacionDelConcepto[0];
        });
    }

    function traerEntradaDeCera(idEntrada) {
        $http.get('almacen/php/dameEntradaDeCera.php?idAlmacen=' + idEntrada).success(function (data) {
            if (data.hasOwnProperty('error')) {
                if (!data.error) {
                    $scope.entrada = data.data;
                    $scope.entrada.clasificacion = { idClasificacionCera: data.data.clasificacion };
                    $scope.entrada.proveedor = { id: data.data.idProveedor };
                    if ($scope.listaConceptosCera && $scope.listaConceptosCera.length > 0) {
                        // buscarUnidadDeConceptos($scope.listaConceptosCera);
                    } else {
                        // Mandamos en el siguiente HTTP REQ como idConcepto el 2, para traer los subconceptos de 'Cera'
                        $http.get('controlAdministrativo/php/subconceptosAnidados.php?idConceptoCC=2').success(function (data) {
                            // buscarUnidadDeConceptos(data);
                        })
                    }

                    if ($scope.clasificaciones && $scope.clasificaciones.length > 0) {
                        seleccionaClasificacionConcepto($scope.clasificaciones);
                    } else {
                        // Clasificaciones de la entrada de cera
                        $http.get('controlAdministrativo/php/clasificacionesCeraYApicolas.php?tipo=1').success(function (data) {
                            seleccionaClasificacionConcepto(data);
                        });
                    }
                    $scope.traeListaProveedores($scope.entrada.tipoCera);

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
                        prepararNuevaEntradaCera();
                        $('#modalAltaRapida').modal('hide');
                    });
                }
                break;
            case '3':
                $http.post("controlAdministrativo/php/guardarProveedor.php", { datos: altasCC, fecha: _fecha }).success(function (info) {
                    swal("", info.message, info.swal);
                    if (!info.error) {
                        $scope.altasCC = {};
                        prepararNuevaEntradaCera();
                        $('#modalAltaRapida').modal('hide');
                    }
                });
                break;
            case '6':
                $http.post("controlAdministrativo/php/guardarCliente.php", altasCC).success(function (info) {
                    swal("", info.message, info.swal);
                    if (!info.error) {
                        $scope.altasCC = {};
                        prepararNuevaEntradaCera();
                        $('#modalAltaRapida').modal('hide');
                    }
                });
                break;
            default:
                break;
        }
    };


    $scope.agregarConceptoEntradaCera = function () {
        // Esta función agrega un nuevo concepto a la tabla
        if (validarNuevoConcepto($scope.nuevoConcepto)) {

            $scope.nuevoConcepto.idMovimiento = $scope.nuevoConcepto.cuenta && $scope.nuevoConcepto.cuenta.idCuentaConcepto ? $scope.nuevoConcepto.cuenta.idCuentaConcepto : null;
            $scope.nuevoConcepto.movimiento = $scope.nuevoConcepto.cuenta && $scope.nuevoConcepto.cuenta.cuenta ? $scope.nuevoConcepto.cuenta.cuenta : '';

            $scope.nuevoConcepto.idSubcuenta = $scope.nuevoConcepto.subcuentaObj.idSubcuenta ? $scope.nuevoConcepto.subcuentaObj.idSubcuenta : null;
            $scope.nuevoConcepto.subcuenta = $scope.nuevoConcepto.subcuentaObj.subcuenta ? $scope.nuevoConcepto.subcuentaObj.subcuenta : '';

            $scope.nuevoConcepto.idConcepto = $scope.nuevoConcepto.producto && $scope.nuevoConcepto.producto.idSubSubcuenta ? $scope.nuevoConcepto.producto.idSubSubcuenta : null;
            $scope.nuevoConcepto.concepto = $scope.nuevoConcepto.producto && $scope.nuevoConcepto.producto.subSubcuenta ? $scope.nuevoConcepto.producto.subSubcuenta : '';
            $scope.nuevoConcepto.nombre = $scope.nuevoConcepto.zona.nombre;
            $scope.nuevoConcepto.zona = $scope.nuevoConcepto.zona.idZonaCera;
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

    $scope.eliminarConceptoEntradaCera = function (index) {
        $scope.entrada.conceptos.splice(index, 1);
        calcularTotalEntrada();
    };

    $scope.eliminarEntrada = function (entrada) {
        swal({
            title: "",
            text: "¿Está seguro de eliminar el registro con folio AL-CER-" + entrada + "?",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#64DAE4",
            confirmButtonText: "Sí, eliminar.",
            closeOnConfirm: false
        },
            function () {
                $http.post("almacen/php/eliminarRegistoDeCera.php?entrada=" + entrada).success(function (respuesta) {
                    if (respuesta.hasOwnProperty('error')) {
                        if (!respuesta.error) {
                            swal("Éxito!", "Registro eliminado", "success");
                            traerEntradasCera();
                        } else {
                            growl.info(respuesta.message);
                        }
                    } else {
                        growl.info("Ocurrió un error");
                    }
                });
            });
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
                prepararNuevaEntradaCera();
                $("#modalNuevoConcepto").modal('hide');
            }
        });
    };

    $scope.f_nuevoSubConcepto = function () {
        $scope.nvoConcepto.subconceptos.push({});
    };

    $scope.imprimirReporte = function () {
        if ($scope.idEntradaCera) {
            window.open('reportes/almacen/pdfReporteCera.php?idAlmacen=' + $scope.idEntradaCera);
        }
    }
    $scope.imprimirReporteExcel = function () {
        return window.location.href = 'reportes/almacen/xlsReporteCera.php?idAlmacen=' + $scope.idEntradaCera;
    }


    if ($location.path() == '/entradaCera') {
        if (window.localStorage.getItem('seleccionTipoCera') != null) {
            $scope.tipoCera = window.localStorage.getItem('seleccionTipoCera');
        }

        $scope.$watch('tipoCera', function (tipoCera) {
            if (tipoCera) {
                window.localStorage.setItem('seleccionTipoCera', tipoCera);
                traerEntradasCera();
            }
        })

    } else if ($scope.idEntradaCera == 0) {
        traerListaCuentas(1);
        $scope.$watch('[entrada.conceptos, nuevoConcepto]', function () {
            if (angular.equals($scope.nuevoConcepto, { importe: NaN, kgTotal: NaN }) && $scope.entrada.conceptos.length == 0 || $scope.entrada.idAlmacen) {
                $rootScope.lockTemplate = false;
            } else {
                $rootScope.lockTemplate = true;
            }
        }, true);
        prepararNuevaEntradaCera();
    } else if ($scope.idEntradaCera > 0) {
        prepararNuevaEntradaCera();
        traerEntradaDeCera($scope.idEntradaCera);
    }

    // $scope.mostrarDatosUnidad = function () {
    //     if (isNaN($scope.nuevoConcepto.unidad.peso) || $scope.nuevoConcepto.unidad.peso == null) {
    //         $scope.nuevoConcepto.pesoUnidad = 0;
    //     } else {
    //         $scope.nuevoConcepto.pesoUnidad = parseFloat($scope.nuevoConcepto.unidad.peso);
    //     }

    //     if (isNaN($scope.nuevoConcepto.unidad.precioUnitario) || $scope.nuevoConcepto.unidad.precioUnitario == null) {
    //         $scope.nuevoConcepto.costoUnitario = 0;
    //     } else {
    //         $scope.nuevoConcepto.costoUnitario = parseFloat($scope.nuevoConcepto.unidad.precioUnitario);
    //     }
    // }
}]);