form.controller('almacenCubetas', ['$scope', '$http', '$routeParams', 'growl', function ($scope, $http, $routeParams, growl) {

    $scope.almacenCubeta = new Almacenista();
    $scope.proveedor = new Proveedor();
    $scope.listaProveedor1 = {};
    $scope.prove1 = {};
    $scope.listaCosecha = {};
    $scope.idAlmacenistaCub = $routeParams.idAlmacen;
    $scope.cosechaOpcionC = $routeParams.opcionCosechaC;
    $scope.bloquearGuardar = false;
    $scope.mielCosechaC = {};
    $scope.mielCosechaC.idTipoDeMiel = "";
    $scope.mielCosechaC.tipoDeMiel = "";
    $scope.combo = {};
    $scope.combo.opcion = 1;
    $scope.radio = {};
    $scope.combo.opcion = 0;
    $scope.folioNo = {};
    $scope.folioNo.folioCub = 1;
    $scope.folioNoEntrada = {};
    $scope.folioNoEntrada.numero = "";
    $scope.opcion = {};
    $scope.listaAlmacenesCubeta = new Array();
    $scope.opcionCosechaC = '1';
    $scope.tipoReporte = '2';

    $scope.showMessage = false; // Para mostrar el mensaje si no hay entradas
    var date = new Date();
    var _mes = date.getMonth() + 1;
    $scope.mostrarMes = _mes.toString();

    $http.post("utilerias/php/traeTiposDeMiel.php").success(function (info) {
        if (!info.hasOwnProperty('error')) {
            $scope.tiposDeMiel = info;
        }
    });

    function traerAlmacen(val) {
        $scope.cargandoDatos = true;
        var url = '';
        url = 'almacen/php/dameAlmacenesCubeta.php';
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
                    $scope.listaAlmacenesCubeta = info.resultado;
                }
            } else {
                growl.error('Error');
                console.error(info);
            }
            $scope.cargandoDatos = false;
        });
    }

    $scope.$watch('[opcionCosechaC, mostrarMes, tipoReporte]', function (val) {
        if ($scope.opcionCosechaC) {
            // window.localStorage.setItem('ENTRADA_TAMBORES_MIEL', $scope.opcionCosecha);
            $scope.listaAlmacenesCubeta = [];
            traerAlmacen(val);
        }
    });

    $scope.$watch('mielCosechaC', function (val) {
        if (val.idTipoDeMiel == 1) {
            $http.get("almacen/php/traeZonasDeTambores.php?tipoMiel=" + val.idTipoDeMiel).success(function (info) {
                $scope.listaZonasTambos = info;
            });
        } else if (val.idTipoDeMiel == 2) {
            $http.get("almacen/php/traeZonasDeTambores.php?tipoMiel=" + val.idTipoDeMiel).success(function (info) {
                $scope.listaZonasTambos = info;
            });
        } else if (val.idTipoDeMiel == 5) {
            $http.get("almacen/php/traeZonasDeTambores.php?tipoMiel=" + val.idTipoDeMiel).success(function (info) {
                $scope.listaZonasTambos = info;
            });
        } else if (val.idTipoDeMiel == 6) {
            $http.get("almacen/php/traeZonasDeTambores.php?tipoMiel=" + 1).success(function (info) {
                $scope.listaZonasTambos = info;
            });
        } else if (val.idTipoDeMiel == 7) {
            console.log(val.idTipoDeMiel)
            $http.get("almacen/php/traeZonasDeTambores.php?tipoMiel=" + 1).success(function (info) {
                $scope.listaZonasTambos = info;
            });
        } else if (val.idTipoDeMiel == 8) {
            $http.get("almacen/php/traeZonasDeTambores.php?tipoMiel=" + 1).success(function (info) {
                $scope.listaZonasTambos = info;
            });
        } else if (val.idTipoDeMiel == 9) {
            console.log(val.idTipoDeMiel)
            $http.get("almacen/php/traeZonasDeTambores.php?tipoMiel=" + 1).success(function (info) {
                $scope.listaZonasTambos = info;
            });
        }
    });

    $http.get('almacen/php/consultaComboMeses.php').success(function (data) {
        $scope.listaDeMeses = data;
    });

    //    -------------------------------------------
    $scope.almacenDetalleCubeta = new Array();

    $scope.traeProveedoresLst = function (valor) {
        if ($scope.idAlmacenistaCub == 0) {
            valor = valor.idTipoDeMiel;
        }
        if(valor == 5 || valor == 6 || valor == 7|| valor == 8 || valor == 9){
            valor = 1;
        }
        $http.post('proveedores/php/dameProveedores.php?todos=1&tipo=' + valor + '&estado=0').success(function (info) {
            $scope.listaProveedor1 = info;
        });
    };

    if ($scope.idAlmacenistaCub == 0) {
        $http.post("utilerias/php/traeTiposDeMiel.php").success(function (info) {
            if (!info.hasOwnProperty('error')) {
                $scope.listaCosecha = info;
            }
        });
    } else {
        $scope.traeProveedoresLst($scope.cosechaOpcionC);
            $http.get("almacen/php/traeZonasDeTambores.php?tipoMiel=" + $scope.cosechaOpcionC).success(function (info) {
                $scope.listaZonasTambos = info;
            });
       
        $http.post("almacen/php/dameAlmacenCubeta.php?miel=" + $scope.cosechaOpcionC + "&id=" + $scope.idAlmacenistaCub)
            .success(function (respuesta) {
                $scope.prove1.id = respuesta.idProveedor;
                $scope.prove1.sagarpa = respuesta.idSagarpa;
                $scope.prove1.localidad = respuesta.localidad;
                $scope.folioNoEntrada.numero = respuesta.folioEntradaTambor;
                $scope.almacenDetalleCubeta = respuesta.almacenDetalleCubeta;
                if ($scope.cosechaOpcionC == '1') {
                    $scope.laMielC = "Miel 100% Pura de Abeja";
                } else if ($scope.cosechaOpcionC == '2') {
                    $scope.laMielC = "Miel 100% Orgánica";
                } else if ($scope.cosechaOpcionC == '5') {
                    $scope.laMielC = "Miel 100% Mantequilla";
                } else if ($scope.cosechaOpcionC == '6') {
                    $scope.laMielC = "Miel 100% Altiplano";
                } else if ($scope.cosechaOpcionC == '7') {
                    $scope.laMielC = "Miel 100% Naranjo";
                } else if ($scope.cosechaOpcionC == '8') {
                    $scope.laMielC = "Miel 100% Aguacate";
                } else if ($scope.cosechaOpcionC == '9') {
                    $scope.laMielC = "Miel 100% Mezquite";
                }
            });
    }

    $scope.nuevoProveedorC = function () {
        $("#mdlProveedorC").modal();
    };

    function verificarDatosNuevoProveedor() {

        if (!$scope.proveedor.nombre) {
            growl.info('Escriba el nombre del proveedor');
            return false;
        }

        if (!$scope.proveedor.telefono) {
            growl.info('Ingrese el número de teléfono');
            return false;
        }

        if (!$scope.proveedor.tipoDeMiel) {
            growl.info('Seleccione un tipo de producto');
            return false;
        }

        return true;
    }

    $scope.guardarProveedorC = function () {
        if (verificarDatosNuevoProveedor()) {
            $http.post("almacen/php/guardarProv.php", { valor: $scope.proveedor })
                .success(function (respuesta) {
                    swal(respuesta.encabezado, respuesta.mensaje, respuesta.tipo);
                    $http.post('proveedores/php/dameProveedores.php?todos=0&tipo=' + $scope.proveedor.tipoDeMiel + '&estado=0').success(function (info) {
                        $scope.listaProveedor1 = info;
                        $scope.proveedor = new Proveedor();
                        $("#mdlProveedorC").modal('hide');
                    });
                });
        }
    };

    $scope.agregarAlmacenCubeta = function () {
        $scope.okCub = $scope.validarAlmacenCub();
        if ($scope.okCub == true) {
            if ($scope.idAlmacenistaCub == 0) {
                $scope.nuevoObj = {};
                $scope.nuevoObj.zona = $scope.almacenCubeta.zona;
                $scope.nuevoObj.pesoLista = $scope.almacenCubeta.pesoLista;
                $scope.nuevoObj.bruto = $scope.almacenCubeta.bruto;
                $scope.nuevoObj.tara = $scope.almacenCubeta.tara;
                $scope.nuevoObj.neto = $scope.almacenCubeta.neto;
                $scope.nuevoObj.diferencia = $scope.almacenCubeta.diferencia;
                $scope.nuevoObj.humedad = $scope.almacenCubeta.humedad;
                $scope.almacenDetalleCubeta.push($scope.nuevoObj);
                growl.success("Nueva entrada agregada");
            } else {
                if ($scope.prove1.id == null) {
                    growl.info('Elija un proveedor');
                } else {
                    if (!$scope.almacenCubeta.referencia || $scope.almacenCubeta.referencia == '') {
                        $scope.almacenCubeta.referencia = 0;
                    } else {
                        $scope.almacenCubeta.referencia;
                    }
                    $http.post("almacen/php/guardarUnicoAlmacenCubeta.php?miel=" + $scope.cosechaOpcionC + "&id=" + $scope.idAlmacenistaCub, { valor: $scope.almacenCubeta })
                        .success(function (respuesta) {
                            if (typeof (respuesta) == 'object' && respuesta.hasOwnProperty('error')) {
                                if (respuesta.error) {
                                    growl.error(respuesta.message || 'No se han guardado los cambios');
                                    // swal('', respuesta.message || 'No se ha podido guardar los cambios', 'info');
                                } else {
                                    growl.success('Registro guardado');
                                    // swal("Éxito!", respuesta.message, "success");
                                    $http.post("almacen/php/dameAlmacenCubeta.php?miel=" + $scope.cosechaOpcionC + "&id=" + $scope.idAlmacenistaCub)
                                        .success(function (respuesta) {
                                            $scope.prove1.id = respuesta.idProveedor;
                                            $scope.prove1.sagarpa = respuesta.idSagarpa;
                                            $scope.prove1.localidad = respuesta.localidad;
                                            $scope.folioNoEntrada.numero = respuesta.folioEntradaTambor;
                                            $scope.almacenDetalleCubeta = respuesta.almacenDetalleCubeta;
                                        });
                                }
                            } else {
                                growl.error('Error');
                                console.error(respuesta);
                            }
                        });
                    $scope.almacenCubeta = new Almacenista();
                }
            }
        }
    };

    $scope.modalCombos = function () {
        $("#modalCombos").modal();
        $(".chosen").chosen();
    };


    $scope.validarAlmacenCub = function () {
        $scope.okCub = false;
        if ($scope.almacenCubeta.zona == 0) {
            growl.error("Se requiere una zona");
        } else if ($scope.almacenCubeta.pesoLista == "") {
            growl.error("Se requiere un peso lista");
        } else if ($scope.almacenCubeta.bruto == "") {
            growl.error("Se requiere un peso bruto");
        } else if ($scope.almacenCubeta.tara == "") {
            growl.error("Se requiere una tara");
        } else {
            $scope.okCub = true;
        }

        return $scope.okCub;
    };
    $scope.eliminarAlmacenCubeta = function (indice) {
        $scope.almacenDetalleCubeta.splice(indice, 1);
        growl.warning("Registro eliminado");
    };

    $scope.guardarAlmacenCubeta = function () {
        $scope.almacenCubeta.idProveedor = $scope.prove1.id;
        $scope.almacenCubeta.folioEntradaTambor = $scope.folioNoEntrada.numero;
        $scope.validaCub = $scope.validarCubetas();
        if ($scope.validaCub == true) {
            if ($scope.idAlmacenistaCub == 0) {
                var validarInfoDetalle = $scope.validarInformacionDet();
                if (validarInfoDetalle == true) {
                    if ($scope.mielCosechaC.idTipoDeMiel == "") {
                        growl.warning("Seleccione un tipo de miel");
                    } else {
                        $http.post('almacen/php/guardarAlmacenCubeta.php?miel=' + $scope.mielCosechaC.idTipoDeMiel + '&idProveedor=' + $scope.almacenCubeta.idProveedor + "&folioEntradaTambor=" + $scope.almacenCubeta.folioEntradaTambor + '&valorFolio=' + $scope.folioNo.folioCub, { valor: $scope.almacenDetalleCubeta })
                            .success(function (respuesta) {
                                if (typeof (respuesta) == 'object' && respuesta.hasOwnProperty('error')) {
                                    if (respuesta.error) {
                                        swal('', respuesta.message || 'No se ha podido guardar los cambios', 'info');
                                    } else {
                                        swal("Éxito!", respuesta.message, "success");
                                        $scope.almacenCubeta = new Almacenista();
                                        $scope.id = $scope.prove1.id;
                                        $scope.bloquearGuardar = false;
                                        return window.location.href = "#/cubetas";
                                    }
                                } else {
                                    growl.error('Error');
                                    console.error(respuesta);
                                }
                            });
                    }
                }
            } else {
                var validarInfoDetalle = $scope.validarInformacionDet();
                if (validarInfoDetalle == true) {
                    $http.post("almacen/php/actualizarAlmacenCubeta.php?tipo=" + $scope.cosechaOpcionC + "&folio=" + $scope.almacenCubeta.folioEntradaTambor + "&idAlmacen=" + $scope.idAlmacenistaCub, { valor: $scope.almacenDetalleCubeta })
                        .success(function (respuesta) {
                            swal("Éxito!", "Registros Actualizados", "success");
                            $scope.bloquearGuardar = false;
                            return window.location.href = "#/cubetas";
                        });
                }
            }
        }
    };

    $scope.validarInformacionDet = function () {
        var validarInfoDetalle = true;
        angular.forEach($scope.almacenDetalleCubeta, function (value, key) {
            if (validarInfoDetalle == true) {
                var taraa = parseFloat(value.tara);
                if (taraa == 0 || value.bruto == 0 || taraa == "" || value.bruto == "" || isNaN(taraa) == true || isNaN(value.bruto)) {
                    growl.warning("El tara y bruto deben ser mayores a cero");
                    validarInfoDetalle = false;
                }
            }
        });
        return validarInfoDetalle;
    };

    $scope.validarCubetas = function () {
        $scope.validaCub = false;
        if ($scope.prove1.id == undefined) {
            growl.error("Se requiere un proveedor");
        } else if ($scope.almacenDetalleCubeta.length == 0) {
            growl.error("Se requiere por lo menos un registro");
        } else if ($scope.folioNo.folioCub == 1 && $scope.folioNoEntrada.numero == "") {
            growl.error("Se requiere un número de entrada");
        } else {
            $scope.validaCub = true;
        }
        return $scope.validaCub;
    };


    $scope.obtenerInfoAlmacenProveedor = function () {
        $scope.id = $scope.prove.id;
        $http.post("almacen/php/dameAlmacenesCubeta.php?idProveedor=" + $scope.id).success(function (info) {
            $scope.listaAlmacenProveedor1 = info;
        });
    };

    $scope.calcularNetoCubeta = function () {
        $scope.almacenCubeta.neto = $scope.almacenCubeta.bruto - $scope.almacenCubeta.tara;
        $scope.calcularDiferenciaCubeta();
    };
    $scope.calcularDiferenciaCubeta = function () {
        $scope.almacenCubeta.diferencia = $scope.almacenCubeta.neto - $scope.almacenCubeta.pesoLista;
    };


    $scope.cambiarProveedorC = function () {
        $("#mdlEditarProveedorC").modal();
    };
    $scope.modificarProveedorC = function () {
        $http.post('almacen/php/editarProveedor.php?cubeta=0&miel=' + $scope.cosechaOpcionC + '&idAlmacen=' + $scope.idAlmacenistaCub + '&idProveedor=' + $scope.proveedorEditado.id).success(function (respuesta) {
            swal("¡Éxito!", "Registros Actualizados", "success");
            $http.post("almacen/php/dameAlmacenCubeta.php?miel=" + $scope.cosechaOpcionC + "&id=" + $scope.idAlmacenistaCub)
                .success(function (respuesta) {
                    $scope.prove1.id = respuesta.idProveedor;
                    $scope.prove1.sagarpa = respuesta.idSagarpa;
                    $scope.prove1.localidad = respuesta.localidad;
                    $scope.folioNoEntrada.numero = respuesta.folioEntradaTambor;
                    $scope.almacenDetalleCubeta = respuesta.almacenDetalleCubeta;
                    if ($scope.cosechaOpcionC == '1') {
                        $scope.laMielC = "Miel 100% Pura de Abeja";
                    } else if ($scope.cosechaOpcionC == '2') {
                        $scope.laMielC = "Miel 100% Orgánica";
                    } else if ($scope.cosechaOpcionC == '5') {
                        $scope.laMielC = "Miel 100% Mantequilla";
                    } else if ($scope.cosechaOpcionC == '6') {
                        $scope.laMielC = "Miel 100% Altiplano";
                    } else if ($scope.cosechaOpcionC == '7') {
                        $scope.laMielC = "Miel 100% Naranjo";
                    } else if ($scope.cosechaOpcionC == '8') {
                        $scope.laMielC = "Miel 100% Aguacate";
                    } else if ($scope.cosechaOpcionC == '9') {
                        $scope.laMielC = "Miel 100% Mezquite";
                    }
                });
        });
    };



    ////////  R E P O R T E S  ///////////

    $scope.nombresDeProveedores = {};
    $http.get('proveedores/php/listaProveedores.php').success(function (data) {
        $scope.nombresDeProveedores = data;
    });


    $scope.nomZona = {};
    $http.get('compras/php/zona/listaZona.php').success(function (arrayZonas) {
        $scope.nomZona = arrayZonas;

    });


    $scope.listaLocalidades = {};
    $http.get('compras/php/localidad/listaLocalidades.php').success(function (data) {
        $scope.listaLocalidades = data;
    });


    ///////////////////////////////////////////

    $scope.prepararRuta = function () {
        switch ($scope.opcion.opcion) {
            case '1':
                switch ($scope.radio.opcion) {
                    case '3':
                    case '4':
                        switch ($scope.combo.opcion) {
                            case '6':
                                return 'reportes/almacen/proveedores/xlsReportePorProveedor.php?recipiente=' + $scope.radio.opcion + '&idProveedor=' + $scope.combo.idProveedor + '&fecha1=' + $scope.opcion.fecha1 + '&fecha2=' + $scope.opcion.fecha2;
                                break;
                            case '7':
                                return 'reportes/almacen/proveedores/xlsReporteporTodosProveedores.php?recipiente=' + $scope.radio.opcion + '&valorProveedores=' + $scope.combo.opcion + '&fecha1=' + $scope.opcion.fecha1 + '&fecha2=' + $scope.opcion.fecha2;
                                break;
                            case '8':
                                return 'reportes/almacen/zona/xlsReporteporZona.php?recipiente=' + $scope.radio.opcion + '&idzona=' + $scope.combo.idZona + '&fecha1=' + $scope.opcion.fecha1 + '&fecha2=' + $scope.opcion.fecha2;
                                break;
                            case '9':
                                return 'reportes/almacen/zona/xlsReporteporTodasZonas.php?recipiente=' + $scope.radio.opcion + '&valorZonas=' + $scope.combo.opcion + '&fecha1=' + $scope.opcion.fecha1 + '&fecha2=' + $scope.opcion.fecha2;
                                break;
                            case '10':
                                return 'reportes/almacen/localidad/xlsReporteporLocalidad.php?recipiente=' + $scope.radio.opcion + '&idlocalidad=' + $scope.combo.idLocalidad + '&fecha1=' + $scope.opcion.fecha1 + '&fecha2=' + $scope.opcion.fecha2;
                                break;
                            case '11':
                                return 'reportes/almacen/localidad/xlsReporteporTodasLocalidades.php?recipiente=' + $scope.radio.opcion + '&valorLocalidades=' + $scope.combo.opcion + '&fecha1=' + $scope.opcion.fecha1 + '&fecha2=' + $scope.opcion.fecha2;
                                break;

                        }
                        break;
                    case '5':
                        switch ($scope.combo.opcion) {
                            case '6':
                                return 'reportes/almacen/proveedores/xlsReporteAmbosporProveedor.php?idProveedor=' + $scope.combo.idProveedor + '&fecha1=' + $scope.opcion.fecha1 + '&fecha2=' + $scope.opcion.fecha2;
                                break;
                            case '7':
                                return 'reportes/almacen/proveedores/xlsAmbosporTodosProveedores.php?valorProveedores=' + $scope.combo.opcion + '&fecha1=' + $scope.opcion.fecha1 + '&fecha2=' + $scope.opcion.fecha2 + '&ambos=' + $scope.radio.opcion;
                                break;
                            case '8':
                                return 'reportes/almacen/zona/xlsReporteAmbosporZona.php?idzona=' + $scope.combo.idZona + '&fecha1=' + $scope.opcion.fecha1 + '&fecha2=' + $scope.opcion.fecha2;
                                break;
                            case '9':
                                return 'reportes/almacen/zona/xlsReporteAmbosporTodasZonas.php?valorZonas=' + $scope.combo.opcion + '&fecha1=' + $scope.opcion.fecha1 + '&fecha2=' + $scope.opcion.fecha2 + '&ambos=' + $scope.radio.opcion;
                                break;
                            case '10':
                                return 'reportes/almacen/localidad/xlsReporteAmbosporLocalidad.php?idlocalidad=' + $scope.combo.idLocalidad + '&fecha1=' + $scope.opcion.fecha1 + '&fecha2=' + $scope.opcion.fecha2 + '&ambos=' + $scope.radio.opcion;
                                break;
                            case '11':
                                return 'reportes/almacen/localidad/xlsReporteAmbosporTodasLocalidades.php?valorLocalidades=' + $scope.combo.opcion + '&fecha1=' + $scope.opcion.fecha1 + '&fecha2=' + $scope.opcion.fecha2 + '&ambos=' + $scope.radio.opcion;
                                break;
                        }
                        break;
                    default:
                        break;
                }
                break;
            case '2':
                switch ($scope.radio.opcion) {
                    case '3':
                    case '4':
                        switch ($scope.combo.opcion) {
                            case '6':
                                return 'reportes/almacen/proveedores/xlsReportePorProveedor.php?recipiente=' + $scope.radio.opcion + '&idProveedor=' + $scope.combo.idProveedor + '&valorTodo=' + $scope.opcion.opcion;
                                break;
                            case '7':
                                return 'reportes/almacen/proveedores/xlsReporteporTodosProveedores.php?recipiente=' + $scope.radio.opcion + '&valorProveedores=' + $scope.combo.opcion + '&valorTodo=' + $scope.opcion.opcion;
                                break;
                            case '8':
                                return 'reportes/almacen/zona/xlsReporteporZona.php?recipiente=' + $scope.radio.opcion + '&idzona=' + $scope.combo.idZona + '&valorTodo=' + $scope.opcion.opcion;
                                break;
                            case '9':
                                return 'reportes/almacen/zona/xlsReporteporTodasZonas.php?recipiente=' + $scope.radio.opcion + '&valorZonas=' + $scope.combo.opcion + '&valorTodo=' + $scope.opcion.opcion;
                                break;
                            case '10':
                                return 'reportes/almacen/localidad/xlsReporteporLocalidad.php?recipiente=' + $scope.radio.opcion + '&idlocalidad=' + $scope.combo.idLocalidad + '&valorTodo=' + $scope.opcion.opcion;
                                break;
                            case '11':
                                return 'reportes/almacen/localidad/xlsReporteporTodasLocalidades.php?recipiente=' + $scope.radio.opcion + '&valorLocalidades=' + $scope.combo.opcion + '&valorTodo=' + $scope.opcion.opcion;
                                break;
                            case '12':
                                return 'reportes/almacen/proveedores/xlsConcentradoProveedores.php?recipiente=' + $scope.radio.opcion;
                                break;
                            case '13':
                                return 'reportes/almacen/zona/xlsConcentradoZona.php?recipiente=' + $scope.radio.opcion;
                                break;
                            case '14':
                                return 'reportes/almacen/localidad/xlsConcetradoLocalidad.php?recipiente=' + $scope.radio.opcion;
                                break;

                        }
                        break;
                    case '5':
                        switch ($scope.combo.opcion) {
                            case '6':
                                return 'reportes/almacen/proveedores/xlsReporteAmbosporProveedor.php?idProveedor=' + $scope.combo.idProveedor + '&valorTodo=' + $scope.opcion.opcion;
                                break;
                            case '7':
                                return 'reportes/almacen/proveedores/xlsAmbosporTodosProveedores.php?valorProveedores=' + $scope.combo.opcion + '&valorTodo=' + $scope.opcion.opcion + '&ambos=' + $scope.radio.opcion;
                                break;
                            case '8':
                                return 'reportes/almacen/zona/xlsReporteAmbosporZona.php?idzona=' + $scope.combo.idZona + '&valorTodo=' + $scope.opcion.opcion;
                                break;
                            case '9':
                                return 'reportes/almacen/zona/xlsReporteAmbosporTodasZonas.php?valorZonas=' + $scope.combo.opcion + '&valorTodo=' + $scope.opcion.opcion + '&ambos=' + $scope.radio.opcion;
                                break;
                            case '10':
                                return 'reportes/almacen/localidad/xlsReporteAmbosporLocalidad.php?idlocalidad=' + $scope.combo.idLocalidad + '&valorTodo=' + $scope.opcion.opcion + '&ambos=' + $scope.radio.opcion;
                                break;
                            case '11':
                                return 'reportes/almacen/localidad/xlsReporteAmbosporTodasLocalidades.php?valorLocalidades=' + $scope.combo.opcion + '&valorTodo=' + $scope.opcion.opcion + '&ambos=' + $scope.radio.opcion;
                                break;
                            case '12':
                                return 'reportes/almacen/proveedores/xlsConcentradoAmbosProveedores.php?ambos=' + $scope.radio.opcion;
                                break;
                            case '13':
                                return 'reportes/almacen/zona/xlsConcentradoAmbosZona.php?ambos=' + $scope.radio.opcion;
                                break;
                            case '14':
                                return 'reportes/almacen/localidad/xlsConcentradoAmbosLocalidad.php?ambos=' + $scope.radio.opcion;
                                break;
                        }
                        break;
                    default:
                        break;
                }
                break;
            default:
                break;
        }
    };

    function descargarReporte() {
        ruta = $scope.prepararRuta();
        if (ruta) {
            ruta += '&tipoDeMiel=' + $scope.opcion.tipoDeMiel;
            $http.get(ruta).success(function (info) {
                if (info == 0) {
                    swal("Error", "No hay informacion disponible con los cirterios seleccionados", "error");
                } else {
                    return window.location.href = ruta;
                }
            });
        } else {
            growl.warning('Complete los campos necesarios');
        }
    }

    $scope.validarFecha = function () {
        if ($scope.opcion.tipoDeMiel) {
            if ($scope.opcion.opcion == 1) {
                if ($scope.opcion.fecha1 == "" || $scope.opcion.fecha2 == "") {
                    swal("Error", "Se requiere de un rango de fechas", "error");
                } else {
                    descargarReporte();
                }

            } else {
                descargarReporte();
            }
        } else {
            growl.info('Seleccione un tipo de miel');
        }

    };

    $scope.$watch('opcion.opcion', function () {
        $scope.opcion.fecha1 = "";
        $scope.opcion.fecha2 = "";
    });

}]);
