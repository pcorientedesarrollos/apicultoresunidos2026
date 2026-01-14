
form.controller('reportesDescargaCtrl', ['$scope', '$routeParams', '$http', 'growl', '$filter', '$location', function ($scope, $routeParams, $http, growl, $filter, $location) {

    $scope.codigo = $routeParams.idReporte;
    $scope.tipoDeMiel = $routeParams.idTipoDeMiel;
    $scope.verTipoDeMiel = '1';
    $scope.estadoReporte = '0';
    $scope.cargandoDatos = false;
    $scope.descarga = {};
    $scope.nombreResponsable = {};
    $scope.nombreResponsable.idPersonalOM = "";
    $scope.nombreResponsable.nombre = "";
    $scope.nombreLimpieza = {};
    $scope.nombreLimpieza.idPersonalOM = "";
    $scope.nombreLimpieza.nombre = "";
    $scope.nombreMarcacion = {};
    $scope.nombreMarcacion.idPersonalOM = "";
    $scope.nombreMarcacion.nombre = "";
    $scope.eligeOperador = {};
    $scope.eligeOperador.idOperador = "";
    $scope.eligeOperador.operador = "";
    $scope.eligePlacas = {};
    $scope.eligePlacas.idPlaca = "";
    $scope.eligePlacas.placa = "";
    $scope.infoAnidada = {};
    $scope.datosUnidad = {};
    $scope.placasC = {};
    $scope.infoDescargas = {};
    $scope.idProducto = [];
    $scope.personalDescargas = new Array();
    $scope.arregloDePlacas = new Array();
    $scope.mostrarRecipiente = false;
    $scope.ocultarColumnas = false;
    var date = new Date();
    var _mes = date.getMonth() + 1;

    $scope.mostrarMes = _mes.toString();
    $http.get('almacen/php/consultaComboMeses.php').success(function (data) {
        $scope.listaDeMeses = data;
    });

    function traerTiposDeMiel() {
        $http.get('utilerias/php/traeTiposDeMiel.php').success(function (data) {
            if (!data.hasOwnProperty('error')) {
                $scope.tiposDeMiel = data;
            }
        });
    };

    function getInfoDescargas(arrayObtenido) {
        $scope.infoDescargas = null;
        $scope.cargandoDatos = true;
        url = 'almacen/php/getInfoReportesDescargas.php';
        if ($scope.mostrarMes && $location.path() == '/repDescarga') {
            url += '?mes=' + $scope.mostrarMes;
        }
        $http.post(url, arrayObtenido).success(function (data) {
            if (data.error) {
                growl.error(data.message);
            }
            $scope.infoDescargas = data.data;
            $scope.cargandoDatos = false;
        });
    };

    $scope.$watch('descarga.miel', function (miel) {
        if (miel) {
            $scope.mostrarRecipiente = true;
        } else {
            $scope.descarga.tambor = false;
            $scope.descarga.cubeta = false;
            $scope.mostrarRecipiente = false;
        }
    });

    $scope.$watch('[verTipoDeMiel, mostrarMes, estadoReporte]', function (val) {
        if (val[0] == '0') {
            $scope.ocultarColumnas = true;
        } else {
            $scope.ocultarColumnas = false;
        }
        getInfoDescargas(val);
    });

    if ($scope.codigo > 0 && $scope.tipoDeMiel) {
        traerTiposDeMiel();
        $scope.listaPersonal = {};
        $http.get('almacen/php/listaNombresPersonal.php').success(function (arrayPersonal) {
            $scope.listaPersonal = arrayPersonal;
        });

        $scope.operadoresC = {};
        $http.get('almacen/php/listaNombresOperadores.php').success(function (arrayOperador) {
            $scope.operadoresC = arrayOperador;
        });
        $http.post("almacen/php/dameReporteDescarga.php", { idReporte: $scope.codigo, tipoDeMiel: $scope.tipoDeMiel }).success(function (info) {
            $scope.descarga = info;
            if ($scope.descarga.miel == '1') {
                $scope.descarga.miel = true;
            } else {
                $scope.descarga.miel = false;
            }
            if ($scope.descarga.tambor == '1') {
                $scope.descarga.tambor = true;
            } else {
                $scope.descarga.tambor = false;
            }
            if ($scope.descarga.cubeta == '1') {
                $scope.descarga.cubeta = true;
            } else {
                $scope.descarga.cubeta = false;
            }
            if ($scope.descarga.cera == '1') {
                $scope.descarga.cera = true;
            } else {
                $scope.descarga.cera = false;
            }
            if ($scope.descarga.apicolas == '1') {
                $scope.descarga.apicolas = true;
            } else {
                $scope.descarga.apicolas = false;
            }
            if ($scope.descarga.mp == '1') {
                $scope.descarga.mp = true;
            } else {
                $scope.descarga.mp = false;
            }
            if ($scope.descarga.traspaso == '1') {
                $scope.descarga.traspaso = true;
            } else {
                $scope.descarga.traspaso = false;
            }
            if ($scope.descarga.envasesFrascos == '1') {
                $scope.descarga.envasesFrascos = true;
            } else {
                $scope.descarga.envasesFrascos = false;
            }
            if ($scope.descarga.productosDerivados == '1') {
                $scope.descarga.productosDerivados = true;
            } else {
                $scope.descarga.productosDerivados = false;
            }
            if ($scope.descarga.producto == '1') {
                $scope.producto = "Convencional";
                $scope.clasificacion = $scope.descarga.clasificacionMiel;
            } else if ($scope.descarga.producto == '2') {
                $scope.producto = "Orgánica";
                $scope.clasificacion = $scope.descarga.clasificacionMiel;
            } else if ($scope.descarga.producto == '5') {
                $scope.producto = "Mantequilla";
            } else if ($scope.descarga.producto == '6') {
                $scope.producto = "Altiplano";
            } else if ($scope.descarga.producto == '7') {
                $scope.producto = "Naranjo";
            } else if ($scope.descarga.producto == '8') {
                $scope.producto = "Aguacate";
            } else if ($scope.descarga.producto == '9') {
                $scope.producto = "Mezquite";
            }
            $scope.datosUnidad.marca = $scope.descarga.marca;
            $scope.datosUnidad.tipo = $scope.descarga.tipo;
            $scope.datosUnidad.marca = $scope.descarga.marca;
            $scope.datosUnidad.modelo = $scope.descarga.modelo;
            $scope.datosUnidad.remolque = $scope.descarga.remolque;
            $scope.datosUnidad.marcaRemolque = $scope.descarga.marcaRemolque;
            $scope.datosUnidad.modeloRemolque = $scope.descarga.modeloRemolque;
            $scope.datosUnidad.placaRemolque = $scope.descarga.placaRemolque;
            $scope.infoAnidada.vigencia = info.vigencia;
            $scope.infoAnidada.licencia = info.licencia;
            $scope.infoAnidada.compania = info.compania;
            $scope.personalDescargas = info.personalDescargas;
            $scope.eligeOperador = "" + $scope.descarga.idOperador + "";
            $scope.eligePlacas = "" + $scope.descarga.placa + "";
            $scope.nombreResponsable = "" + $scope.descarga.responsable + "";
            $scope.nombreLimpieza = "" + $scope.descarga.limpiezaPersonal + "";
            $scope.nombreMarcacion = "" + $scope.descarga.marcacion + "";
        });
    }

    if ($scope.codigo == 0) {
        $scope.descarga.unas = '1';
        $scope.descarga.cabello = '1';
        $scope.descarga.ropa = '1';
        $scope.descarga.roto = '0';
        $scope.descarga.abolladuras = '0';
        $scope.descarga.recipienteAdecuado = '1';
        $scope.descarga.lavadoExterior = '1';
        $scope.descarga.limpieza = '1';
        $scope.descarga.materialExtrano = '0';
        $scope.descarga.vehiculoAdecuado = '1';
        traerTiposDeMiel();
        $scope.listaPersonal = {};
        $http.get('almacen/php/listaNombresPersonal.php').success(function (arrayPersonal) {
            $scope.listaPersonal = arrayPersonal;
        });
        $scope.operadoresC = {};
        $http.get('almacen/php/listaNombresOperadores.php').success(function (arrayOperador) {
            $scope.operadoresC = arrayOperador;
        });
    }

    $scope.$watch('numPlaca.placa', function (val) {
        if (val) {
            $scope.numPlaca.placa = $filter('uppercase')(val);
        }
    }, true);

    $scope.modalOperador = function () {
        $("#modalOperador").modal();
    };

    $scope.mostrarComboPersonal = function () {
        $("#modalComboPersonal").modal();
    };

    $scope.agregarDescargador = function () {
        var _datos = {
            idReporte: $scope.codigo,
            tipoDeMiel: $scope.tipoDeMiel,
            idPersonalOM: $scope.descargador
        }
        $http.post("almacen/php/guardarNvoDescargador.php", _datos).success(function (info) {
            if (info.error) {
                growl.error(info.message);
            } else {
                $http.post("almacen/php/dameDescargadores.php", { idReporte: $scope.codigo, tipoDeMiel: $scope.tipoDeMiel }).success(function (infor) {
                    $scope.personalDescargas = infor.data;
                    if (infor.error) {
                        growl.error(infor.message);
                    }
                });
                $("#modalComboPersonal").modal('hide');
                $scope.descargador = "";
            }
        });
    };

    $scope.nuevaPlaca = function () {
        $scope.numPlaca = {};
        $scope.numPlaca.idPlaca = 0;
        $scope.numPlaca.placa = "";
        $scope.arregloDePlacas.push($scope.numPlaca);
    };

    $scope.eliminarNumPlaca = function (indice) {
        $scope.arregloDePlacas.splice(indice, 1);
        growl.warning("Registro eliminado");
    };

    $scope.$watch('eligeOperador', function (eligeOperador) {
        $http.get('almacen/php/comboPlacasId.php?idOperador=' + eligeOperador)
            .success(function (data) {
                $scope.placasC = data;
            });
        $scope.eligePlacas = $scope.descarga.idPlaca;
    }, true);

    $scope.infoOperador = function (eligeOperador) {
        $scope.datosUnidad = {};
        $http.get('almacen/php/infoOperadorAnidado.php?idOperador=' + eligeOperador)
            .success(function (informacionAnidada) {
                $scope.infoAnidada = informacionAnidada;
            });
    };

    $scope.datosVehiculo = function (eligePlacas) {
        $http.get('almacen/php/infoOperadorAnidado.php?idPlaca=' + eligePlacas)
            .success(function (informacionAnidada) {
                $scope.datosUnidad = informacionAnidada;
            });
    };

    //=================================================================
    //     GUARDAR CHOFER
    //=================================================================
    $scope.guardarOperador = function () {
        $scope.datos = new Array();
        $scope.datos.push($scope.chofer);
        $scope.datos.push($scope.arregloDePlacas);
        $http.post("almacen/php/verificarLicencia.php?licencia=" + $scope.chofer.licencia)
            .success(function (respuesta) {
                if (respuesta == 1) {
                    swal("Error!", "Verifique! No. de Licencia duplicado", "error");
                } else {
                    $http.post('almacen/php/guardarProveedorAlmacen.php', { valor: $scope.datos }).success(function (result) {
                        swal("Exito!", "Operador disponible", "success");
                        $http.get('almacen/php/listaNombresOperadores.php').success(function (arrayOperador) {
                            $scope.operadoresC = arrayOperador;
                        });
                        $scope.datos = "";
                        $("#modalOperador").modal('hide');
                    });
                }
            }
            );
    };

    $scope.guardarReporteDescarga = function () {
        var reporteValido = $scope.validarReporte();
        if (reporteValido == true) {
            $scope.descarga.responsable = $scope.nombreResponsable;
            $scope.descarga.limpiezaPersonal = $scope.nombreLimpieza;
            $scope.descarga.marcacion = $scope.nombreMarcacion;
            $scope.descarga.idOperador = $scope.eligeOperador;
            $scope.descarga.idPlaca = $scope.eligePlacas;
            switch ($scope.descarga.miel) {
                case undefined:
                    $scope.descarga.miel = 0;
                    break;
                case false:
                    $scope.descarga.miel = 0;
                    break;
                case true:
                    $scope.descarga.miel = 1;
                    break;
            }
            switch ($scope.descarga.cera) {
                case undefined:
                    $scope.descarga.cera = 0;
                    break;
                case false:
                    $scope.descarga.cera = 0;
                    break;
                case true:
                    $scope.descarga.cera = 1;
                    break;
            }
            switch ($scope.descarga.apicolas) {
                case undefined:
                    $scope.descarga.apicolas = 0;
                    break;
                case false:
                    $scope.descarga.apicolas = 0;
                    break;
                case true:
                    $scope.descarga.apicolas = 1;
                    break;
            }
            switch ($scope.descarga.tambor) {
                case undefined:
                    $scope.descarga.tambor = 0;
                    break;
                case false:
                    $scope.descarga.tambor = 0;
                    break;
                case true:
                    $scope.descarga.tambor = 1;
                    break;
            }
            switch ($scope.descarga.cubeta) {
                case undefined:
                    $scope.descarga.cubeta = 0;
                    break;
                case false:
                    $scope.descarga.cubeta = 0;
                    break;
                case true:
                    $scope.descarga.cubeta = 1;
                    break;
            }
            switch ($scope.descarga.mp) {
                case undefined:
                    $scope.descarga.mp = 0;
                    break;
                case false:
                    $scope.descarga.mp = 0;
                    break;
                case true:
                    $scope.descarga.mp = 1;
                    break;
            }
            switch ($scope.descarga.traspaso) {
                case undefined:
                    $scope.descarga.traspaso = 0;
                    break;
                case false:
                    $scope.descarga.traspaso = 0;
                    break;
                case true:
                    $scope.descarga.traspaso = 1;
                    break;
            }
            switch ($scope.descarga.envasesFrascos) {
                case undefined:
                    $scope.descarga.envasesFrascos = 0;
                    break;
                case false:
                    $scope.descarga.envasesFrascos = 0;
                    break;
                case true:
                    $scope.descarga.envasesFrascos = 1;
                    break;
            }
            switch ($scope.descarga.prductosDerivados) {
                case undefined:
                    $scope.descarga.prductosDerivados = 0;
                    break;
                case false:
                    $scope.descarga.prductosDerivados = 0;
                    break;
                case true:
                    $scope.descarga.prductosDerivados = 1;
                    break;
            }
            switch ($scope.descarga.producto) {
                //APLICARÁ EN CASOS != MIEL && CERA
                case undefined:
                    $scope.descarga.producto = 0;
                    break;
                case null:
                    $scope.descarga.producto = 0;
                    break;
            }
            $scope.datosDescarga = new Array();
            $scope.datosDescarga.push($scope.descarga);
            $scope.datosDescarga.push($scope.personalDescargas);
            if ($scope.codigo == 0) {
                $http.post("almacen/php/guardarReporteDescarga.php", { valor: $scope.datosDescarga }).success(function (data) {
                    if (!data.error) {
                        swal("", data.message, "success");
                        return window.location.href = "#/repDescarga";
                    } else {
                        growl.error(data.message);
                    }
                });
            } else {
                $http.post("almacen/php/guardarEdicionReporteDescarga.php", { valor: $scope.datosDescarga }).success(function (data) {
                    if (data.error) {
                        growl.error(data.message);
                    } else {
                        swal("", data.message, "success");
                        return window.location.href = "#/repDescarga";
                    }
                });
            }

        }
    };

    $scope.validarReporte = function () {
        $scope.reporteValido = false;
        if ($scope.descarga.fechaImpresion == undefined) {
            swal("", "Seleccione la Fecha", "info");
        } else if ($scope.eligeOperador.idOperador == 0) {
            swal("", "Se requiere un Operador", "info");
        } else if ($scope.descarga.producto == undefined) {
            swal("", "Elija tipo de producto (convencional, orgánico o no aplica)", "info");
        } else if ($scope.descarga.miel == undefined && $scope.descarga.cera == undefined && $scope.descarga.apicolas == undefined && $scope.descarga.mp == undefined && $scope.descarga.traspaso == undefined && $scope.descarga.envasesFrascos == undefined && $scope.descarga.productosDerivados == undefined) {
            swal("", "Seleccione al menos un producto", "info");
        } else if ($scope.descarga.miel == true && $scope.descarga.cubeta == false && $scope.descarga.tambor == false) {
            swal("", "Seleccione un recipiente (tambo y/o cubeta)", "info");
        } else if ($scope.descarga.miel == true && $scope.descarga.producto == '0') {
            swal("", "Seleccione tipo de miel válido", "info");
        } else if ($scope.descarga.cera == true && $scope.descarga.producto == '0') {
            swal("", "Seleccione tipo de cera válido", "info");
        } else if ($scope.descarga.mp == true && $scope.descarga.producto == '0') {
            swal("", "Seleccione convencional u orgánico", "info");
        } else if ($scope.descarga.traspaso == true && $scope.descarga.producto == '0') {
            swal("", "Seleccione convencional u orgánico", "info");
        }
        else {
            $scope.reporteValido = true;
        }
        return $scope.reporteValido;
    };

    $scope.pdfDescarga = function () {
        window.open('reportes/pdfReporteDescarga.php?tipo=1&tipoDeMiel=' + $scope.tipoDeMiel + '&idReporte=' + $scope.codigo, '_blank');
    };

    $scope.eliminarPersona = function (indice) {
        var _id = $scope.personalDescargas[indice].idPersonalDescarga;
        if ($scope.codigo == 0) {
            $scope.personalDescargas.splice(indice, 1);
        } else if ($scope.codigo > 0) {
            $http.post("almacen/php/eliminarPersonalDescarga.php", { id: _id, tipoDeMiel: $scope.tipoDeMiel }).success(function (respuesta) {
                if (respuesta.error) {
                    growl.error(respuesta.message);
                } else {
                    growl.success(respuesta.message);
                    $scope.personalDescargas.splice(indice, 1);
                }
            });
        }
    };
}]);