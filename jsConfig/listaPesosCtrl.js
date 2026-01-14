form.controller('listaPesosCtrl', ['$scope', '$http', '$routeParams', 'growl', '$q', function ($scope, $http, $routeParams, growl, $q) {

    //=========================P A R A M E T R O===========================
    $scope.tamborPeso = $routeParams.idTamborPeso;
    if ($scope.tamborPeso > 0) {
        $scope.bloquearDocumento = true;
    } else {
        $scope.bloquearDocumento = false;
    }
    //    ----------------------------------------------------------------

    $scope.menuPesos = new Array();
    $scope.lotePesos = "";
    $scope.reportePesos = {};
    //        $scope.reportePesos.fecha = "";
    $scope.reportePesos.totalBruto = 0;
    $scope.reportePesos.totalTara = 0;
    $scope.reportePesos.totalNeto = 0;
    $scope.reportePesos.lote = "";
    //        $scope.reportePesos.tracto = "";
    //        $scope.reportePesos.lote = "";
    $scope.pesosTambos = new Array();
    $scope.pesoTambo = {};
    $scope.anidaCarga = {};
    $scope.listaClientesExportadores = new Array();


    function traeCatalogoSobrantes() {
        $http.get('almacenSobrantes/php/listaTiposDeSobrantes.php').success(function (data) {
            $scope.catalogoSobrantes = data;
        })
    }

    traeCatalogoSobrantes();


    //==========================================================================
    // TRAE INFORMACION PARA ARMAR LA TABLA QUE MUESTRA INFORMACION DEL ENCABEZADO
    //==========================================================================

    function traerTiposDeMiel() {
        $http.get('utilerias/php/traeTiposDeMiel.php').success(function (data) {
            if (!data.hasOwnProperty('error')) {
                $scope.tiposDeMiel = data;
            }
        });
        // $http.post("json/laboratorio/floraciones.json").success(function (valor) {
        //     $scope.listaFloracion = valor.floraciones;
        // });
        $http.post("laboratorio/php/listaFloraciones.php").success(function (valor) {
            $scope.listaFloracion = valor;
        });
    };

    function traeDetalleReporteListaDePesos(idTamborPeso) {
        $http.get('almacen/php/traeDetalleReporteListaDePesos.php?idTamborPeso=' + idTamborPeso).success(function (data) {
            if (data.hasOwnProperty('error')) {
                growl.error(data.message);
            } else {
                $scope.reportePesos = data;
                $scope.lotePesos = "" + $scope.reportePesos.lote + "";
                $scope.anidaCarga.fechaImpresion = data.fechaImpresion;
                $scope.anidaCarga.idLoteInterno = data.idLoteInterno;
                $scope.anidaCarga.compania = data.compania;
                $scope.anidaCarga.operador = data.operador;
                $scope.anidaCarga.placa = data.placa;
                $scope.anidaCarga.contenedor = data.contenedor;
                $scope.anidaCarga.sello = data.sello;
                $scope.anidaCarga.marca = data.marca;
                $scope.anidaCarga.modelo = data.modelo;
                $scope.pesosTambos = data.pesosTambos;
            }
        });
    }

    function establecerTambores(listaTambores) {
        var d = $q.defer();
        $scope.pesosTambos = [];
        listaTambores.forEach(function (tambor) {
            var inf = {
                folio: tambor.folioTambor,
                bruto: tambor.bruto,
                tara: tambor.tara,
                neto: tambor.neto,
                porcentaje: tambor.porcentaje,
                color: tambor.color,
                idFloracion: tambor.idFloracion,
                tipo: tambor.tipo,
                clasificacion: tambor.clasificacion
            };
            $scope.pesosTambos.push(inf);
        });
        d.resolve();
        return d.promise;
    }

    function obtenerTamboresDeLote() {
        // Solo aplica cuando es un reporte de miel no homogeneizada
        if ($scope.reportePesos.idLoteInterno && $scope.reportePesos.mielHomogeneizada == '2' && $scope.reportePesos.tipoMiel) {
            url = 'calidad/php/traeDetalleLoteInt.php?miel=' + $scope.reportePesos.tipoMiel + '&idLoteInterno=' + $scope.reportePesos.idLoteInterno;
            $http.get(url).success(function (data) {
                if (typeof (data) == 'object' && data.hasOwnProperty('error')) {

                    if (data.error) {
                        swal('Error', data.message, 'error');
                    } else {
                        if (data.data.informacionCalidad.length > 0) {
                            swal({
                                title: 'Se ha encontrado información de ' + data.data.informacionCalidad.length + ' tambor (es)',
                                text: '¿Desea llenar los registros con los datos encontrados?',
                                type: 'warning',
                                showCancelButton: true,
                                confirmButtonText: 'Usar registros',
                                cancelButtonText: 'No usar',
                                closeOnConfirm: false
                            }, function (c) {
                                if (c && c == true) {
                                    establecerTambores(data.data.informacionCalidad).then(function (r) {
                                        swal('Hecho', 'Se ha registrado los datos', 'success');
                                    })
                                }
                            });
                        }
                    }

                } else {
                    growl.error('Error');
                    console.error(data);
                }
            })
        }
    }

    $scope.obtenerListaMarcasLotes = function () {
        $scope.listaMarcasLotes = [];
        if ($scope.reportePesos.tipoMiel) {
            $http.get('almacen/php/listaMarcasLotes.php?tipoDeMiel=' + $scope.reportePesos.tipoMiel).success(function (datas) {
                if (datas.hasOwnProperty('error')) {
                    if (datas.error) {
                        swal('Error', datas.message, 'error');
                    } else {
                        $scope.listaMarcasLotes = datas.lotes;
                    }
                } else {
                    growl.error('Error');
                    console.error(datas);
                }
            });
        }
    }

    $http.post('controlAdministrativo/polizaCheque/php/traerNombres.php', 6).success(function (data) {
        $scope.listaClientes = data;
    });

    // $scope.traeListaExportadores = function () {
    $http.post("exportacion/php/traeClienteExportador.php").success(function (data) {
        angular.forEach(data, function (value) {
            var cadenaCliente = value.datosCliente;
            var arreglo = JSON.parse(cadenaCliente);
            arreglo.idClienteExportador = value.idClienteExportador;
            $scope.listaClientesExportadores.push(arreglo);
        });
    });
    // }

    $scope.$watch('reportePesos.tipoMiel', function (tipoDeMiel) {
        $scope.obtenerListaMarcasLotes();
    })

    if ($scope.tamborPeso > 0) {
        traerTiposDeMiel();
        traeDetalleReporteListaDePesos($scope.tamborPeso);
    } else {
        traerTiposDeMiel();

        // Comprobar la selección del tipo de miel

        if (window.localStorage.getItem('seleccionMielPesos') != null) {
            $scope.tipoDeMiel = window.localStorage.getItem('seleccionMielPesos');
        } else {
            window.localStorage.removeItem('seleccionMielPesos');
        }

        // watch cambios en la selección de miel

        $scope.$watch('tipoDeMiel', function (opcion) {
            if (opcion) {
                traeInformacionVista(opcion);
                window.localStorage.setItem('seleccionMielPesos', opcion);
            }
        });
    }

    function traeInformacionVista(valor) {
        $http.post("almacen/php/traeEncabezadoReportePesos.php?tipoMiel=" + valor).success(function (info) {
            if (info.hasOwnProperty('error')) {
                if (info.error) {
                    growl.error(info.message);
                } else {
                    $scope.menuPesos = info.data;
                }
            } else {
                console.error(info);
            }
        });
    }

    $scope.informacionCarga = function (lotePesos) {
        if ($scope.reportePesos.tipoMiel) {
            $http.get('almacen/php/informacionParaListaPesos.php?lote=' + lotePesos + '&tipoDeMiel=' + $scope.reportePesos.tipoMiel).success(function (informacionAnidada) {
                if (informacionAnidada.hasOwnProperty('error')) {
                    if (informacionAnidada.error) {
                        swal('Error', informacionAnidada.message, 'error');
                    } else {
                        $scope.reportePesos.idLoteInterno = informacionAnidada.info.idLoteInterno;
                        $scope.anidaCarga = informacionAnidada.info;
                        obtenerTamboresDeLote();
                    }
                } else {
                    growl.error('Error');
                    console.error(informacionAnidada);
                }
            });
        } else {
            growl.info('Selecciona el tipo de miel');
        }
    };

    //    ----------------------------------------------------------------

    $scope.updateModel = function () {
        // $scope.pesosTambos = [];
        for (var i = 0; i < $scope.counter; i++) {
            $scope.pesosTambos.push({});
        }
    };

    $scope.valoresIguales = function (id) {
        switch (id) {
            case 1:
                angular.forEach($scope.pesosTambos, function (value, key) {
                    value.bruto = $scope.pBruto;
                });
                break;
            case 2:
                angular.forEach($scope.pesosTambos, function (value, key) {
                    value.tara = $scope.pTara;
                });
                break;
            case 3:
                angular.forEach($scope.pesosTambos, function (value, key) {
                    value.porcentaje = $scope.pHumedad;
                });
                break;
            case 4:
                angular.forEach($scope.pesosTambos, function (value, key) {
                    value.color = $scope.pColor;
                });
                break;
            case 5:
                angular.forEach($scope.pesosTambos, function (value, key) {
                    value.idFloracion = $scope.pFloracion;
                });
                break;
            default:
                console.info(id);
                break;
        }
    };

    $scope.agregaTambo = function (nuevoTamborFolio = false, clasificacion = false) {
        if ((event.keyCode == 13)) {
            if (nuevoTamborFolio) {
                clasificacion = clasificacion ? clasificacion : '0';
                var indice = $scope.pesosTambos.push({}) - 1;
                $scope.buscarDatosFolioTambor(nuevoTamborFolio, indice, clasificacion);
                $scope.$on('no_encuentra_tambor_' + nuevoTamborFolio, function () {
                    growl.info('Tambor no disponible');
                });
                $scope.nuevoTamborFolio = null;
                $scope.clasificacionTambor = null;
            } else {
                $scope.pesosTambos.push($scope.pesoTambo);
                $scope.pesoTambo = "";
            }
        }
    };

    $scope.$watch(function () {
        $scope.pesoTambo.neto = parseFloat($scope.pesoTambo.bruto) - parseFloat($scope.pesoTambo.tara);
        $scope.totalDeLosNetos();
    });
    $scope.totalDeLosNetos = function () {
        $scope.reportePesos.totalNeto = 0;
        angular.forEach($scope.pesosTambos, function (value, key) {
            $scope.reportePesos.totalNeto += parseFloat(value.neto);
        });
    };

    $scope.$watch(function () {
        $scope.pesoTambo.bruto;
        $scope.totalDePesosBrutos();
    });

    $scope.totalDePesosBrutos = function () {
        $scope.reportePesos.totalBruto = 0;
        angular.forEach($scope.pesosTambos, function (value, key) {
            $scope.reportePesos.totalBruto += parseFloat(value.bruto);
        });
    };

    $scope.$watch(function () {
        $scope.pesoTambo.tara;
        $scope.totalDePesosTara();
    });
    $scope.totalDePesosTara = function () {
        $scope.reportePesos.totalTara = 0;
        angular.forEach($scope.pesosTambos, function (value, key) {
            $scope.reportePesos.totalTara += parseFloat(value.tara);
        });
    };

    $scope.$watch('[reportePesos.mielHomogeneizada, lotePesos, reportePesos.tipoMiel]', function (t) {
        if (!$scope.bloquearDocumento) {
            obtenerTamboresDeLote();
        }
    })

    $scope.borraTambo = function (indice) {
        if ($scope.bloquearDocumento && $scope.reportePesos.mielHomogeneizada == '2' && $scope.pesosTambos[indice].folio) {
            var mensaje = $scope.pesosTambos[indice].hasOwnProperty('folio')
                ? '¿Eliminar el registro con folio ' + $scope.pesosTambos[indice].folio + '?'
                : '¿Eliminar el registro?';
            swal({
                title: mensaje,
                text: 'No podrá usar de nuevo el folio en este reporte',
                type: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Eliminar',
                cancelButtonText: 'Cancelar',
                closeOnConfirm: false
            }, function (c) {
                if (c && c == true) {
                    $scope.pesosTambos.splice(indice, 1);
                    $scope.$apply();
                    swal({
                        title: 'Eliminado',
                        text: 'El registro fue eliminado del reporte',
                        type: 'success',
                        timer: 700
                    });
                }
            });
        } else {
            $scope.pesosTambos.splice(indice, 1);
        }
    };

    // ================================================
    //   GUARDAR REPORTE PROCESO
    // ================================================
    $scope.guardarReportePesos = function () {
        $scope.validPesos = $scope.validarPesos();
        if ($scope.validPesos == true) {
            if ($scope.tamborPeso == 0) {
                $scope.reportePesos.lote = $scope.lotePesos;
                $scope.arregloPesos = new Array();
                $scope.arregloPesos.push($scope.reportePesos);
                $scope.arregloPesos.push($scope.pesosTambos);
                $http.post("almacen/php/guardarReportePesos.php", { valor: $scope.arregloPesos }).success(function (respuesta) {
                    if (respuesta.hasOwnProperty('error')) {
                        if (respuesta.error) {
                            swal('Error', respuesta.message, 'error');
                        } else {
                            $http.post("almacen/php/actualizarEstadoTambor.php", $scope.arregloPesos).success(function (data) {
                                if (data.hasOwnProperty('error')) {
                                    if (data.error) {
                                        swal('Error', data.message, 'error');
                                    } else {
                                        swal('Hecho', respuesta.message, 'success');
                                        growl.info(data.message);
                                        $scope.tipoDeMiel = $scope.reportePesos.tipoMiel;
                                        return window.location.href = "#/lstPesos";
                                    }
                                } else {
                                    growl.info('Error');
                                    console.error(data);
                                }
                            });
                        }
                    } else {
                        growl.error('Error');
                        console.error(respuesta);
                    }
                });
            } else {
                $scope.reportePesos.lote = $scope.lotePesos;
                $scope.arregloPesosEdicion = new Array();
                $scope.arregloPesosEdicion.push($scope.reportePesos);
                $scope.arregloPesosEdicion.push($scope.pesosTambos);
                $http.post("almacen/php/guardarEdicionReportePesos.php", { valor: $scope.arregloPesosEdicion }).success(function (respuesta) {
                    if (respuesta.hasOwnProperty('error')) {
                        if (respuesta.error) {
                            swal('Error', respuesta.message, 'error');
                        } else {
                            if (respuesta.error) {
                                swal('Error', respuesta.message, 'error');
                            } else {
                                $http.post("almacen/php/actualizarEstadoTambor.php", $scope.arregloPesosEdicion).success(function (data) {
                                    if (data.hasOwnProperty('error')) {
                                        if (data.error) {
                                            swal('Error', data.message, 'error');
                                        } else {
                                            swal('Hecho', respuesta.message, 'success');
                                            growl.info(data.message);
                                            // return window.location.href = "#/lstPesos";
                                        }
                                    } else {
                                        growl.info('Error');
                                        console.error(data);
                                    }
                                });
                            }
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
    //      VALIDAR 
    //=================================================================
    $scope.validarPesos = function () {
        $scope.validPesos = false;
        if ($scope.lotePesos == "") {
            growl.error("Se requiere de una marcación final");
        } else if ($scope.reportePesos.tipoMiel == undefined) {
            growl.error("Se requiere un tipo de miel");
        } else if ($scope.reportePesos.mielHomogeneizada == undefined) {
            growl.error("Seleccione si es miel homogeneizada o no");
        } else {
            $scope.validPesos = true;
        }
        return $scope.validPesos;
    };

    $scope.pdfListaPesos = function (tamborPeso, tipoTratamiento) {
        if (tipoTratamiento == 'Homogeneizada') {
            window.open('reportes/listaPesos/pdfListaPesos.php?idTamborPeso=' + tamborPeso, '_blank');
        } else if (tipoTratamiento == 'No homogeneizada') {
            window.open('reportes/listaPesos/pdfListaPesos.php?conFolios=0&idTamborPeso=' + tamborPeso, '_blank');
            window.open('reportes/listaPesos/pdfListaPesos.php?idTamborPeso=' + tamborPeso, '_blank');
        } else {
            console.info(tipoTratamiento);
        }
    };

    goToDocument = function (url) {
        return window.location.href = url;
    }

    $scope.xlsListaPesos = function (tamborPeso, tipoTratamiento) {
        if (tipoTratamiento == 'No homogeneizada') {
            goToDocument("reportes/listaPesos/xlsListaPesos.php?idTamborPeso=" + tamborPeso);
            setTimeout(function () {
                goToDocument("reportes/listaPesos/xlsListaPesos.php?folios&idTamborPeso=" + tamborPeso);
            }, 1000);
        } else {
            goToDocument("reportes/listaPesos/xlsListaPesos.php?idTamborPeso=" + tamborPeso);
        }
    };




    function vaciarCamposTambor(index) {
        $scope.pesosTambos[index].bruto = null;
        $scope.pesosTambos[index].tara = null;
        $scope.pesosTambos[index].porcentaje = null;
        $scope.pesosTambos[index].color = null;
        $scope.pesosTambos[index].idFloracion = null;
        // $scope.pesosTambos.splice(index, 1);
    }

    $scope.buscarDatosFolioTambor = function (folio, index, clasificacion) {

        // Función para traer los datos de los tambores de miel no homogeneizada
        if ($scope.reportePesos.tipoMiel) {
            var folioTambor = parseInt(folio);
            if (folioTambor) {
                clasificacion = clasificacion ? clasificacion : '0';
                var url = 'almacen/php/treInformacionTambor.php?folioTambor=' + folio + '&tipoDeMiel=' + $scope.reportePesos.tipoMiel + '&idLoteInterno=' + $scope.reportePesos.idLoteInterno + '&sobrante=' + clasificacion;
                $http.get(url).success(function (data) {
                    if (data.hasOwnProperty('error')) {
                        if (data.error) {
                            swal('Error', data.message, 'error');
                        } else {
                            if (data.data) {
                                $scope.pesosTambos[index].tipo = clasificacion == '0' ? '0' : '1';
                                $scope.pesosTambos[index].folio = folio;
                                $scope.pesosTambos[index].bruto = data.data.bruto;
                                $scope.pesosTambos[index].tara = data.data.tara;
                                $scope.pesosTambos[index].porcentaje = data.data.porcentaje;
                                $scope.pesosTambos[index].color = data.data.color;
                                $scope.pesosTambos[index].idFloracion = data.data.idFloracion;
                                $scope.pesosTambos[index].tipo = data.data.tipo;
                                $scope.pesosTambos[index].clasificacion = data.data.clasificacion;
                            } else {
                                vaciarCamposTambor(index);
                                $scope.$broadcast('no_encuentra_tambor_' + folio);
                            }
                        }
                    } else {
                        growl.info('Error');
                        console.error(data);
                    }
                })
            } else {
                vaciarCamposTambor(index);
            }
        } else {
            growl.warning('Selecciona un tipo de miel');
        }

    };

    $scope.mandarEtiquetas = function (reporte, tamborSalida) {
        if (reporte) {
            var datosImpresion = {
                idAlmacen: reporte.idTamborPeso,
                idTipoDeMiel: reporte.tipoMiel,
                estado: 3
            }
        } else if (tamborSalida) {
            var datosImpresion = {
                idAlmacen: tamborSalida.idPesoTambo,
                idTipoDeMiel: $scope.reportePesos.tipoMiel,
                estado: 4
            }
        }

        $http.post("./mandarImprimir.php", datosImpresion).success(function (res) {
            if (!res.error) {
                growl.success(res.message);
            } else {
                growl.error(res.message);
            }
        });
    }


    $scope.eliminarReporteListaPesos = function () {
        if ($scope.reportePesos.hasOwnProperty('idTamborPeso') && $scope.reportePesos.idTamborPeso) {
            var reporte = Object.assign({}, {
                idTamborPeso: $scope.reportePesos.idTamborPeso,
                idLoteInterno: $scope.reportePesos.idLoteInterno
            });
            swal({
                title: '¿Eliminar el reporte de lista de pesos?',
                text: 'El registro desaparecerá y los tambores estarán disponibles en lote interno',
                type: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Eliminar',
                cancelButtonText: 'Cancelar',
                closeOnConfirm: false
            }, function (c) {
                if (c && c == true) {
                    $http.post('almacen/php/eliminarReporteListaDePesos.php?eliminarReporte', reporte).success(function (data) {
                        if (data.hasOwnProperty('error')) {
                            if (data.error) {
                                swal('Error', data.message, 'error');
                            } else {
                                window.location.href = '#/lstPesos';
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


