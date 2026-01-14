form.controller('salidaCeraCtrl', ['$scope', '$http', '$routeParams', 'growl', '$location', '$q', '$rootScope', function ($scope, $http, $routeParams, growl, $location, $q, $rootScope) {
    $scope.tipoCera = null;
    $scope.cargandoDatos = false;
    $scope.idSalidaCera = $routeParams.idSalidaCera;
    $scope.nuevoConcepto = {};
    $scope.salida = {
        conceptos: Array(),
        total: 0,
        kg: 0
    }

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

    $scope.$watch('[tipoCera, mostrarMes]', function (val) {
        if ($scope.tipoCera) {
            window.localStorage.setItem('SALIDA_CERA', $scope.tipoCera);
            $scope.listaSalidasCera = [];
            // $scope.filtroPagado = null;
            traerSalidasCera();
        }
    });

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
        if ($scope.nuevoConcepto.cantidad > 0 && $scope.nuevoConcepto.producto) {
            $scope.nuevoConcepto.kgTotal = parseFloat($scope.nuevoConcepto.cantidad) * parseFloat($scope.nuevoConcepto.producto.peso);
            $scope.nuevoConcepto.importe = parseFloat($scope.nuevoConcepto.cantidad) * parseFloat($scope.nuevoConcepto.producto.precioUnitario);
        } else {
            $scope.nuevoConcepto.kgTotal = 0;
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

    function traerSalidasCera() {
        $scope.listaSalidasCera = null;
        if ($scope.tipoCera) {
            $scope.cargandoDatos = true;
            url = 'almacen/php/dameReportesDeCera.php?tipo=2&tipoCera=' + $scope.tipoCera;
            if ($scope.mostrarMes && $location.path() == '/salidaCera') {
                url += '&mes=' + $scope.mostrarMes;
            }
            $http.get(url).success(function (data) {
                $scope.listaSalidasCera = data.data;
                if (data.error) {
                    console.error(data.message);
                }
                $scope.cargandoDatos = false;
            });
        }
    };

    function prepararNuevoReporte() {

        traerListaCuentas(0);

        $http.get("proveedores/php/dameProveedores.php?activos=0").success(function (info) {
            $scope.listaApicultores = info;
        });

        $http.post('controlAdministrativo/polizaCheque/php/traerNombres.php', 3).success(function (data) {
            $scope.listaProveedores = data;
        });
        $http.post('controlAdministrativo/polizaCheque/php/traerNombres.php', 6).success(function (data) {
            $scope.listaClientes = data;
        });


        $http.get('almacen/php/consultaComboCondicionesSalidas.php').success(function (arrayCondiciones) {
            $scope.clasificaciones = arrayCondiciones;
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

    function guardarReporte(reporte) {
        // Creo que tipo se refiere a entrada o salida, 2 es salida        

        $http.post("almacen/php/guardarReporteCera.php?tipo=2", reporte).success(function (info) {
            if (info.hasOwnProperty('error')) {
                if (!info.error) {
                    $rootScope.lockTemplate = false;
                    swal('', info.message, info.swal);
                    window.location.href = '#/salidaCera';
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
        } else if (!concepto.producto.peso) {
            growl.info('Indique el peso');
            return false;
        } else if (!concepto.descripcion) {
            growl.info('La descripción no puede ser vacío');
            return false;
        } else if (!concepto.clasificacion) {
            growl.info('Seleccione la clasificación');
            return false;
        } else if (!concepto.producto.precioUnitario) {
            growl.info('Indique el precio unitario');
            return false;
        }

        return true;
    };

    function verificarReporte(reporte) {
        if (!reporte.fecha) {
            growl.info('Seleccione la fecha de salida');
            return false;
        } else if (!reporte.proveedor) {
            growl.info('Seleccione el proveedor');
            return false;
        } else if (reporte.conceptos.length < 1) {
            growl.info('La salida debe contener al menos 1 movimiento');
            return false;
        } else if (!reporte.tipoCera) {
            growl.info('Especifique el tipo de cera.');
            return false;
        }
        return true;
    };

    function calcularTotal() {
        $scope.salida.total = 0;
        $scope.salida.kg = 0;
        $scope.salida.conceptos.forEach(concepto => {
            if (!isNaN(parseInt(concepto.importe))) {
                $scope.salida.total += parseFloat(concepto.importe);
                $scope.salida.kg += parseFloat(concepto.kgTotal);
            }
        });
    };

    // 27/12/18 Ya no se usará esta función debido a que ahora los nombres de los conceptos también
    // se guardan en la base de datos y hay nuevas cuentas
    // function buscarUnidadDeConceptos(listaConceptosCera) {
    //     $scope.salida.conceptos.forEach((element, index) => {
    //         var unidadDelConcepto = listaConceptosCera.filter(function (unidad) {
    //             if (element.unidad == unidad.idSubconceptoCC) {
    //                 return unidad;
    //             }
    //         });
    //         $scope.salida.conceptos[index].unidad = unidadDelConcepto[0];
    //     });
    // };

    function seleccionaClasificacionConcepto(clasificaciones) {
        $scope.salida.conceptos.forEach((element, index) => {
            var clasificacionDelConcepto = clasificaciones.filter(function (cl) {
                if (element.clasificacion == cl.idCondicionSalida) {
                    return cl;
                }
            });
            $scope.salida.conceptos[index].clasificacion = clasificacionDelConcepto[0];
        });
    }

    function traerReporteCera(idAlmacen) {
        // El php trae la información dependiendo del ID, no hacer caso al nombre del archivo
        $http.get('almacen/php/dameEntradaDeCera.php?idAlmacen=' + idAlmacen).success(function (data) {
            if (data.hasOwnProperty('error')) {
                if (!data.error) {
                    $scope.salida = data.data;
                    $scope.salida.clasificacion = { idCondicionSalida: data.data.clasificacion };
                    $scope.salida.proveedor = { id: data.data.idProveedor };
                    // if ($scope.listaConceptosCera && $scope.listaConceptosCera.length > 0) {
                    // 27/12/18 función obsoleta v2
                    // buscarUnidadDeConceptos($scope.listaConceptosCera);
                    // } else {
                    // Mandamos en el siguiente HTTP REQ como idConcepto el 2, para traer los subconceptos de 'Cera'
                    // $http.get('controlAdministrativo/php/subconceptosAnidados.php?idConceptoCC=2').success(function (data) {
                    // 27/12/18 función obsoleta v2
                    // buscarUnidadDeConceptos(data);
                    // })
                    // }

                    /* // Como solo tenemos el id de la clasificación, tenemos que encontrarlo así:*/
                    if ($scope.clasificaciones && $scope.clasificaciones.length > 0) {
                        seleccionaClasificacionConcepto($scope.clasificaciones);
                    } else {
                        $http.get('almacen/php/consultaComboCondicionesSalidas.php').success(function (arrayCondiciones) {
                            seleccionaClasificacionConcepto(arrayCondiciones);
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
                        prepararNuevoReporte();
                        $('#modalAltaRapida').modal('hide');
                    });
                }

                break;
            case '3':
                $http.post("controlAdministrativo/php/guardarProveedor.php", { datos: altasCC, fecha: _fecha }).success(function (info) {
                    swal("", info.message, info.swal);
                    if (!info.error) {
                        $scope.altasCC = {};
                        prepararNuevoReporte();
                        $('#modalAltaRapida').modal('hide');
                    }
                });
                break;
            case '6':
                $http.post("controlAdministrativo/php/guardarCliente.php", altasCC).success(function (info) {
                    swal("", info.message, info.swal);
                    if (!info.error) {
                        $scope.altasCC = {};
                        prepararNuevoReporte();
                        $('#modalAltaRapida').modal('hide');
                    }
                });
                break;
            default:
                break;
        }
    };

    $scope.agregarConcepto = function () {
        // Esta función agrega un nuevo concepto a la tabla
        if (validarNuevoConcepto($scope.nuevoConcepto)) {
            $scope.nuevoConcepto.idMovimiento = $scope.nuevoConcepto.cuenta && $scope.nuevoConcepto.cuenta.idCuentaConcepto ? $scope.nuevoConcepto.cuenta.idCuentaConcepto : null;
            $scope.nuevoConcepto.movimiento = $scope.nuevoConcepto.cuenta && $scope.nuevoConcepto.cuenta.cuenta ? $scope.nuevoConcepto.cuenta.cuenta : '';

            $scope.nuevoConcepto.idSubcuenta = $scope.nuevoConcepto.subcuentaObj.idSubcuenta ? $scope.nuevoConcepto.subcuentaObj.idSubcuenta : null;
            $scope.nuevoConcepto.subcuenta = $scope.nuevoConcepto.subcuentaObj.subcuenta ? $scope.nuevoConcepto.subcuentaObj.subcuenta : '';

            $scope.nuevoConcepto.idConcepto = $scope.nuevoConcepto.producto && $scope.nuevoConcepto.producto.idSubSubcuenta ? $scope.nuevoConcepto.producto.idSubSubcuenta : null;
            $scope.nuevoConcepto.concepto = $scope.nuevoConcepto.producto && $scope.nuevoConcepto.producto.subSubcuenta ? $scope.nuevoConcepto.producto.subSubcuenta : '';

            $scope.nuevoConcepto.costoUnitario = $scope.nuevoConcepto.producto.precioUnitario;
            $scope.salida.conceptos.push($scope.nuevoConcepto);
            $scope.nuevoConcepto = {};
            calcularTotal();
        } else {
            growl.error('Verifica los campos necesarios');
        }
    };

    $scope.guardarReporte = function () {
        if (verificarReporte($scope.salida)) {
            guardarReporte($scope.salida);
        }
    };

    $scope.eliminarConcepto = function (index) {
        $scope.salida.conceptos.splice(index, 1);
        calcularTotal();
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
                            traerSalidasCera();
                        } else {
                            growl.info(respuesta.message);
                        }
                    } else {
                        growl.info("Ocurrió un error");
                    }
                });
            });
    };

    $scope.nuevoConceptoCera = function () {
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
                prepararNuevoReporte();
                $("#modalNuevoConcepto").modal('hide');
            }
        });
    };

    $scope.f_nuevoSubConcepto = function () {
        $scope.nvoConcepto.subconceptos.push({});
    };

    $scope.imprimirReporte = function () {
        if ($scope.idSalidaCera) {
            window.open('reportes/almacen/pdfReporteCera.php?idAlmacen=' + $scope.idSalidaCera);
        }
    }

    $scope.imprimirReporteExcel = function () {
        return window.location.href = 'reportes/almacen/xlsReporteCera.php?idAlmacen=' + $scope.idSalidaCera;
    }

    if ($location.path() == '/salidaCera') {
        if (window.localStorage.getItem('seleccionTipoCera') != null) {
            $scope.tipoCera = window.localStorage.getItem('seleccionTipoCera');
        }

        $scope.$watch('tipoCera', function (tipoCera) {
            if (tipoCera) {
                window.localStorage.setItem('seleccionTipoCera', tipoCera);
                traerSalidasCera();
            }
        })

    } else if ($scope.idSalidaCera == 0) {
        traerListaCuentas(0);
        $scope.$watch('[salida.conceptos, nuevoConcepto]', function () {
            if (angular.equals($scope.nuevoConcepto, { importe: NaN, kgTotal: NaN }) && $scope.salida.conceptos.length == 0 || $scope.salida.idAlmacen) {
                $rootScope.lockTemplate = false;
            } else {
                $rootScope.lockTemplate = true;
            }
        }, true);

        prepararNuevoReporte();
    } else if ($scope.idSalidaCera > 0) {
        prepararNuevoReporte();
        traerReporteCera($scope.idSalidaCera);
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