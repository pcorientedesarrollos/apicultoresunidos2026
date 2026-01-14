// form.controller('calidad', function ($scope, busqueda, $http, $routeParams, growl, $rootScope, $location) {
//     $scope.control = false;
//     $scope.controlPorcentaje = false;
//     $scope.guardar = false;
//     $("#idModalBusquedaCalidad").modal({ keyboard: false, backdrop: false }
//     );
//     $scope.loteExp = $routeParams.idLoteExperimental;
//     $scope.loteInt = $routeParams.idLoteInterno;
//     $scope.paramCosecha = $routeParams.opcionCosechaEx;
//     $scope.paramCosechaInt = $routeParams.opcionCosechaInt;
//     var tblCalidadDesglozado = this;
//     $scope.informacionCalidad = new Array();
//     $scope.informacionMiel = new Array();
//     $scope.menuLoteExperimental = new Array();
//     $scope.menuLoteInterno = new Array();
//     $scope.calidad = 0;
//     $scope.totalPesoNeto = 0;
//     $scope.totalPesoNetoAsignado = 0;
//     $scope.totalPesoNetoNoAsignado = 0;
//     $scope.loteExperimental = {};
//     $scope.loteExperimental.humedad = 0;
//     $scope.loteExperimental.kilosTotales = 0;
//     $scope.loteExperimental.numeroDeTambores = 0;
//     $scope.lotExperimental = {};
//     $scope.lotExperimental.humedad = 0;
//     $scope.lotExperimental.kilosTotales = 0;
//     $scope.lotExperimental.numeroDeTambores = 0;
//     $scope.loteInterno = {};
//     $scope.loteInterno.loteInterno = 0;
//     $scope.loteInterno.fechaProceso = "";
//     $scope.loteInterno.fechaEnvasado = "";
//     $scope.loteInterno.loteCliente = "";
//     $scope.loteInterno.marcaFinalCliente = "";
//     $scope.loteInterno.observaciones = "";
//     $scope.loteInterno.muestraInterna = "";
//     $scope.loteInterno.numeroDeTambores = 0;
//     $scope.loteInterno.kilosTotales = 0;
//     $scope.loteInterno.idLoteExperimental = 0;
//     $scope.numeroDeTambores = 0;
//     $scope.resultadoLaboratorio = {};
//     $scope.resultadoLaboratorio.idresultadoFinal = "";
//     $scope.resultadoLaboratorio.resultado = "";
//     $scope.listaResultadoFinal = {};
//     $scope.opcionCosechaEx = 1;
//     $scope.opcionCosechaInt = 1;
//     $scope.nvoTambo = false;
//     $scope.infoTambo = {};
//     // $scope.sobrante = '0';
//     $scope.listaSobrantes = {};

//     if ($scope.paramCosecha > 0 && $scope.loteExp > 0) {
//         $scope.mielSeleccionada = $scope.paramCosecha;
//     };

//     $scope.traeDetalleExperimental = function () {
//         $http.get('calidad/php/traeDetalleLoteExp.php?miel=' + $scope.paramCosecha + '&idLoteExperimental=' + $scope.loteExp).success(function (data) {
//             console.log(data);
//             $scope.loteExperimental.idLoteExperimental = data.data.idLoteExperimental;
//             $scope.loteExperimental.fechaExperimental = data.data.fecha;
//             $scope.loteExperimental.humedad = data.data.humedad;
//             $scope.loteExperimental.numeroDeTambores = data.data.totalTambores;
//             $scope.loteExperimental.kilosTotales = data.data.totalKilos;
//             $scope.resultadoExperimental = data.data.resultadoExperimental;
//             $scope.loteExperimental.loteInterno = data.data.loteInterno;
//             $scope.informacionCalidad = data.data.experimental;
//             console.log($scope.informacionCalidad);
//         });
//     }

//     $scope.traeDetalleInterno = function () {
//         $http.get('calidad/php/traeDetalleLoteInt.php?miel=' + $scope.paramCosechaInt + '&idLoteInterno=' + $scope.loteInt).success(function (data) {
//             console.log(data);
//             $scope.loteInterno.idLoteInterno = data.data.idLoteInterno;
//             $scope.loteInterno.idLoteExperimental = data.data.idLoteExperimental;
//             $scope.loteInterno.fechaProceso = data.data.fechaProceso;
//             $scope.loteInterno.fechaEnvasado = data.data.fechaEnvasado;
//             $scope.loteInterno.numeroDeTambores = data.data.numeroDeTambores;
//             $scope.loteInterno.kilosTotales = data.data.kilosTotales;
//             $scope.loteInterno.loteCliente = data.data.loteCliente;
//             $scope.loteInterno.marcaFinalCliente = data.data.marcaFinalCliente;
//             $scope.loteInterno.observaciones = data.data.observaciones;
//             $scope.loteInterno.muestraInterna = data.data.muestraInterna;
//             $scope.informacionCalidad = data.data.informacionCalidad;
//             console.log($scope.informacionCalidad);
//         });
//     }

//     $scope.anexarTamboInt = function () {
//         $scope.nvoTambo = true;
//     };

//     function vaciarCamposTambor() {
//         $scope.tamborInt = null;
//         $scope.infoTambo = {};
//     }

//     $scope.$watch('sobrante', function (sobrante) {
//         vaciarCamposTambor();
//     });
//     $scope.$watch('tipo', function (val) {
//         $scope.sobrante = $scope.tipo == '0' ? '0' : $scope.sobrante;
//         vaciarCamposTambor();
//     });

//     $scope.buscarDatosTambor = function (folioTambor, sobrante) {
//         // Función para traer los datos de los tambores de miel no homogeneizada
//         var folio = parseInt(folioTambor);
//         if (folio) {
//             $http.get('almacen/php/treInformacionTambor.php?sinLote=0&sobrante=' + sobrante + '&folioTambor=' + folio + '&tipoDeMiel=' + $scope.paramCosechaInt).success(function (data) {
//                 if (data.hasOwnProperty('error')) {
//                     if (data.error) {
//                         growl.error(data.message);
//                     } else {
//                         if (data.data) {
//                             $scope.disponible = true;
//                             $scope.infoTambo.neto = data.data.neto;
//                             $scope.infoTambo.bruto = data.data.bruto;
//                             $scope.infoTambo.tara = data.data.tara;
//                         } else {
//                             $scope.disponible = false;
//                             growl.info('El folio ' + folio + ' no está disponible');
//                         }
//                     }
//                 } else {
//                     console.error(data);
//                 }
//             })
//         } else {
//             vaciarCamposTambor();
//         }
//     };

//     $scope.guardarTamboInt = function (tamborInt, sobrante, tipo) {
//         $scope.infoTambo.idAlmacen = tamborInt;
//         $scope.infoTambo.clasificacion = sobrante;
//         $scope.infoTambo.tipo = tipo
//         var respaldoNeto = $scope.loteInterno.kilosTotales;
//         var respaldoTambos = $scope.loteInterno.numeroDeTambores;
//         $scope.infoTambo.kilosTotales = parseFloat(respaldoNeto) + parseFloat($scope.infoTambo.neto);
//         $scope.infoTambo.numeroDeTambores = parseFloat(respaldoTambos) + 1;
//         $scope.infoTambo.idLoteInterno = $scope.loteInt;
//         swal({
//             title: "",
//             text: "¿Estás seguro de agregar el folio " + tamborInt + " al lote?",
//             type: "warning",
//             showCancelButton: true,
//             confirmButtonColor: "#00ACD6",
//             confirmButtonText: "Sí, agregar.",
//             closeOnConfirm: false
//         }, function (confirm) {
//             if (confirm) {
//                 if ($scope.disponible) {
//                     $http.post("calidad/php/anexarTamboInterno.php?tipoDeMiel=" + $scope.paramCosechaInt, { valor: $scope.infoTambo })
//                         .success(function (respuesta) {
//                             if (respuesta.hasOwnProperty('error')) {
//                                 if (!respuesta.error) {
//                                     swal("Éxito!", "Folio guardado", "success");
//                                     $scope.traeDetalleInterno();
//                                     vaciarCamposTambor();
//                                     $scope.nvoTambo = false;
//                                 } else {
//                                     growl.info(respuesta.message);
//                                 }
//                             } else {
//                                 growl.info("Ocurrió un error");
//                                 console.error(respuesta);
//                             }
//                         });
//                 } else {
//                     swal("", "Folio no disponible", "info");
//                     vaciarCamposTambor();
//                     $scope.nvoTambo = false;
//                     $scope.$apply();
//                 }
//             } else {
//                 vaciarCamposTambor();
//                 $scope.nvoTambo = false;
//                 $scope.$apply();
//             }
//         });
//     }

//     if ($scope.loteExp > 0) {
//         $http.post("json/calidad/resultadoExperimental.json").success(function (datos) {
//             $scope.resultExperimental = datos.resultExperimental;
//         });
//         $http.post("almacenSobrantes/php/listaTiposDeSobrantes.php").success(function (info) {
//             $scope.listaSobrantes = info;
//         });
//         $scope.traeDetalleExperimental();
//     } else {
//         $scope.$watch('opcionCosechaEx', function (val) {
//             if (val == 1) {
//                 $http.get('calidad/php/traeInformacionTotalLotesExperimentales.php').success(function (data) {
//                     $scope.menuLoteExperimental = data;
//                 });
//             } else if (val == 2) {
//                 $http.get('calidad/php/traeInformacionTotalLotesExperimentales.php?organica=0').success(function (data) {
//                     $scope.menuLoteExperimental = data;
//                 });
//             }
//         });
//         $http.post("almacenSobrantes/php/listaTiposDeSobrantes.php").success(function (info) {
//             $scope.listaSobrantes = info;
//         });
//     }

//     if ($scope.loteInt > 0) {
//         $http.post("almacenSobrantes/php/listaTiposDeSobrantes.php").success(function (info) {
//             $scope.listaSobrantes = info;
//         });
//         $scope.traeDetalleInterno();
//     } else {
//         $scope.$watch('opcionCosechaInt', function (val) {
//             // if (val == 1) {
//             //     $http.get('calidad/php/traeInformacionTotalLotesInternos.php').success(function (dats) {
//             //         $scope.menuLoteInterno = dats;
//             //     });
//             // } else if (val == 2) {
//             //     $http.get('calidad/php/traeInformacionTotalLotesInternos.php?organica=0').success(function (data) {
//             //         $scope.menuLoteInterno = data;
//             //     });
//             // }

//             url = 'calidad/php/traeInformacionTotalLotesInternos.php';
//             if ($scope.mostrarMes && $scope.mostrarMes !== "null") {
//                 url += '?mes=' + $scope.mostrarMes;
//             }
//             $http.post(url, val).success(function (data) {
//                 if (data.error) {
//                     growl.error(data.message);
//                 }
//                 $scope.menuLoteInterno = data.data;
//                 // $scope.cargandoDatos = false;
//             });

//         });
//     }

//     $scope.modalLoteInterno = function () {
//         $("#modalLoteInterno").modal();
//     };

//     $scope.editarMarcaCliente = function () {
//         $("#editarMarcaCliente").modal();
//     };

//     $scope.editarHumedad = function () {
//         $("#editarHumedad").modal();
//     };

//     $scope.$watch('mielSeleccionada', function (val) {
//         if (val == 1) {
//             busqueda.dameResultadosFinales().then(function (data) {
//                 $scope.listaResultadosFinales = data;
//             });
//             busqueda.damePorcentaje().then(function (data) {
//                 $scope.listaPorcentajeDisponible = data;
//             });
//             busqueda.dameSt().then(function (data) {
//                 $scope.listaStDisponible = data;
//             });
//             busqueda.dameSf().then(function (data) {
//                 $scope.listaSfDisponible = data;
//             });
//             busqueda.dameC13().then(function (data) {
//                 $scope.listaC13Disponibles = data;
//             });
//             busqueda.dameHmf().then(function (data) {
//                 $scope.listaHmfDisponible = data;
//             });
//             busqueda.dameFloracion().then(function (data) {
//                 $scope.listaFloracionDisponibles = data;
//             });
//             busqueda.dameLocalidad().then(function (data) {
//                 $scope.listaLocalidadDisponibles = data;
//             });
//         } else if (val == 2) {
//             busqueda.dameResultadosFinalesO().then(function (data) {
//                 $scope.listaResultadosFinales = data;
//             });
//             busqueda.damePorcentajeO().then(function (data) {
//                 $scope.listaPorcentajeDisponible = data;
//             });
//             busqueda.dameStO().then(function (data) {
//                 $scope.listaStDisponible = data;
//             });
//             busqueda.dameSfO().then(function (data) {
//                 $scope.listaSfDisponible = data;
//             });
//             busqueda.dameC13O().then(function (data) {
//                 $scope.listaC13Disponibles = data;
//             });
//             busqueda.dameHmfO().then(function (data) {
//                 $scope.listaHmfDisponible = data;
//             });
//             busqueda.dameFloracion().then(function (data) {
//                 $scope.listaFloracionDisponibles = data;
//             });
//             busqueda.dameLocalidadO().then(function (data) {
//                 $scope.listaLocalidadDisponibles = data;
//             });
//         }
//     });

//     $scope.eliminarLoteCalidad = function (indice) {
//         $scope.informacionCalidad.splice(indice, 1);
//         growl.warning("Registro eliminado");
//         $scope.loteExperimental.kilosTotales = 0;
//         angular.forEach($scope.informacionCalidad, function (value, key) {
//             $scope.loteExperimental.kilosTotales += parseInt(value.neto);
//         });
//     };

//     $scope.eliminarMasMiel = function (indice) {
//         $scope.informacionMiel.splice(indice, 1);
//         growl.warning("Registro eliminado");
//         $scope.lotExperimental.kilosTotales = 0;
//         angular.forEach($scope.informacionMiel, function (value, key) {
//             $scope.lotExperimental.kilosTotales += parseInt(value.neto);
//         });
//     };

//     $scope.eliminarTamborExp = function (folioTambor, clasificacion) {
//         $scope.datos = {
//             clasificacion: clasificacion,
//             folioTambor: folioTambor,
//             miel: $scope.paramCosecha
//         };
//         $http.post('calidad/php/eliminarTamborExperimental.php?experimental=1', { valor: $scope.datos })
//             .success(function (respuesta) {
//                 if (respuesta.hasOwnProperty('error')) {
//                     if (!respuesta.error) {
//                         swal("Éxito!", "Tambor eliminado", "success");
//                         $http.get('calidad/php/traeDetalleLoteExp.php?miel=' + $scope.paramCosecha + '&idLoteExperimental=' + $scope.loteExp).success(function (data) {
//                             $scope.loteExperimental.idLoteExperimental = data.data.idLoteExperimental;
//                             $scope.loteExperimental.fechaExperimental = data.data.fecha;
//                             $scope.loteExperimental.humedad = data.data.humedad;
//                             $scope.loteExperimental.numeroDeTambores = data.data.totalTambores;
//                             $scope.loteExperimental.kilosTotales = data.data.totalKilos;
//                             $scope.resultadoExperimental = data.data.resultadoExperimental;
//                             $scope.loteExperimental.loteInterno = data.data.loteInterno;
//                             $scope.informacionCalidad = data.data.experimental;
//                             $scope.kilosTotales = $scope.loteExperimental.kilosTotales;
//                             $scope.numeroDeTambores = $scope.loteExperimental.numeroDeTambores;
//                             $http.post('calidad/php/updateTotales.php?experimental=1&miel=' + $scope.paramCosecha + '&idLoteExperimental=' + $scope.loteExp + '&numeroDeTambores=' + $scope.numeroDeTambores + '&kilosTotales=' + $scope.kilosTotales).success(function (respuesta) {
//                             });
//                         });
//                     } else {
//                         growl.info(respuesta.message);
//                     }
//                 } else {
//                     growl.info("Ocurrió un error");
//                 }
//             });
//     };

//     $scope.eliminarTamboLote = function (folioTambor, clasificacion) {
//         swal({
//             title: "",
//             text: "¿Estás seguro de eliminar el folio " + folioTambor + " ?",
//             type: "warning",
//             showCancelButton: true,
//             confirmButtonColor: "#64DAE4",
//             confirmButtonText: "Sí, eliminar.",
//             closeOnConfirm: false
//         },
//             function (resp) {
//                 if (resp) {
//                     $scope.datos = {
//                         clasificacion: clasificacion,
//                         folioTambor: folioTambor,
//                         miel: $scope.paramCosechaInt
//                     };
//                     $http.post('calidad/php/eliminarTamborExperimental.php?idLoteInterno=' + $scope.loteInt, { valor: $scope.datos })
//                         .success(function (respuesta) {
//                             if (respuesta.hasOwnProperty('error')) {
//                                 if (!respuesta.error) {
//                                     swal("Éxito!", "Tambor eliminado", "success");
//                                     $scope.traeDetalleInterno();
//                                 } else {
//                                     growl.info(respuesta.message);
//                                 }
//                             } else {
//                                 growl.info("Ocurrió un error");
//                             }
//                         });
//                 }
//             });
//     };


//     $scope.buscarCalidadPorParametros = function () {
//         $scope.rangosHmf = {};
//         $scope.rangosPorcentaje = {};
//         $scope.rangosHmf.rango1 = $scope.hmfRango1;
//         $scope.rangosHmf.rango2 = $scope.hmfRango2;
//         $scope.rangosPorcentaje.rango1 = $scope.porcentajeRango1;
//         $scope.rangosPorcentaje.rango2 = $scope.porcentajeRango2;
//         $scope.listaParametros = {};
//         $scope.listaParametros.listaC13 = $scope.c13Seleccionados;
//         $scope.listaParametros.listaResultadosFinales = $scope.resultadosSeleccionados;
//         $scope.listaParametros.listaPorcentaje = $scope.porcentajeSeleccionados;
//         $scope.listaParametros.listaSt = $scope.stSeleccionados;
//         $scope.listaParametros.listaSf = $scope.sfSeleccionados;
//         $scope.listaParametros.listaHmf = $scope.hmfSeleccionados;
//         $scope.listaParametros.rangosHmf = $scope.rangosHmf;
//         $scope.listaParametros.rangosPorcentaje = $scope.rangosPorcentaje;
//         $scope.listaParametros.listaFloracion = $scope.floracionesSeleccionadas;
//         $scope.listaParametros.listaLocalidad = $scope.localidadesSeleccionadas;
//         if ($scope.mielSeleccionada == undefined) {
//             growl.error("elige un tipo de miel");
//         } else {
//             if ($scope.mielSeleccionada == 1) { // MIEL 100% PURA
//                 busqueda.buscarInformacionParametros($scope.listaParametros).then(function (info) {
//                     if (info == 0) {
//                         growl.warning("No se encontró ningún registro con esos parámetros");
//                     } else {
//                         if ($scope.loteExp > 0) {
//                             for (i = 0; i < info.length; i++) {
//                                 info[i].clasificacion = '0';
//                                 info[i].tipo = '0';
//                                 $scope.informacionMiel.push(info[i]);
//                             }
//                             angular.forEach(info, function (value, key) {
//                                 $scope.lotExperimental.kilosTotales += parseFloat(value.neto);
//                             });
//                         } else {
//                             for (i = 0; i < info.length; i++) {
//                                 info[i].clasificacion = '0';
//                                 info[i].tipo = '0';
//                                 $scope.informacionCalidad.push(info[i]);
//                             }
//                             angular.forEach(info, function (value, key) {
//                                 $scope.loteExperimental.kilosTotales += parseFloat(value.neto);
//                             });
//                         }
//                     }
//                     $("#idModalBusquedaCalidad").modal('hide');
//                 });
//             } else { // MIEL 100% ORGÁNICA
//                 busqueda.buscarInformacionParametrosOrganica($scope.listaParametros).then(function (info) {
//                     if (info == 0) {
//                         growl.warning("No se encontró ningún registro con esos parámetros");
//                     } else {
//                         if ($scope.loteExp > 0) {
//                             for (i = 0; i < info.length; i++) {
//                                 info[i].clasificacion = '0';
//                                 info[i].tipo = '0';
//                                 $scope.informacionMiel.push(info[i]);
//                             }
//                             angular.forEach(info, function (value, key) {
//                                 $scope.lotExperimental.kilosTotales += parseFloat(value.neto);
//                             });
//                         } else {
//                             for (i = 0; i < info.length; i++) {
//                                 info[i].clasificacion = '0';
//                                 info[i].tipo = '0';
//                                 $scope.informacionCalidad.push(info[i]);
//                             }
//                             angular.forEach(info, function (value, key) {
//                                 $scope.loteExperimental.kilosTotales += parseFloat(value.neto);
//                             });
//                         }
//                     }
//                     $("#idModalBusquedaCalidad").modal('hide');
//                 });
//             }
//         }
//     };

//     $scope.abrirBuscadorParametros = function () {
//         $("#idModalBusquedaCalidad").modal({ keyboard: false, backdrop: false }
//         );
//     };

//     $scope.buscarFolioCalidad = function () {
//         $http.post("calidad/php/buscarCalidad.php?id=" + $scope.folioCalidad)
//             .success(function (respuesta) {
//                 if (respuesta == 0) {
//                     growl.warning("Registro no encontrado");
//                 } else {
//                     if (respuesta.idCalidad > 0) {
//                         respuesta.fechaProceso = new Date(respuesta.fechaProceso);
//                         respuesta.fechaEnvase = new Date(respuesta.fechaEnvase);
//                     }
//                     $scope.informacionCalidad.push(respuesta);
//                 }
//             });
//         $scope.folioCalidad = "";
//     };

//     $scope.filtrar = function () {
//         $("#idModalBusquedaCalidad").modal();
//     };

//     $scope.guardarExperimental = function () {
//         if ($scope.loteExperimental.humedad == undefined) {
//             $scope.loteExperimental.humedad = 0;
//         } else {
//             $scope.loteExperimental.humedad = $scope.loteExperimental.humedad;
//         }
//         $scope.datosExperimental = new Array();
//         $scope.datosExperimental.push($scope.loteExperimental);
//         $scope.datosExperimental.push($scope.informacionCalidad);
//         $scope.guardar = true;
//         console.log($scope.datosExperimental);
//         $http.post("calidad/php/guardarLoteExperimental.php?miel=" + $scope.mielSeleccionada, { valor: $scope.datosExperimental })
//             .success(function (data) {
//                 console.log(data);
//                 if (data.hasOwnProperty('error')) {
//                     if (!data.error) {
//                         swal("Éxito!", "Experimental guardado", "success");
//                         $scope.opcionCosechaEx = '1';
//                         $http.get('calidad/php/traeInformacionTotalLotesExperimentales.php').success(function (data) {
//                             $scope.menuLoteExperimental = data;
//                         });
//                         return window.location.href = "#/experimental";
//                         $scope.loteExperimental = new Array();
//                         $scope.informacionCalidad = new Array();
//                     } else {
//                         growl.error(data.message);
//                     }
//                 } else {
//                     console.error(data);
//                 }
//             });
//     };

//     $scope.asignarResult = function (resultadoExperimental) {
//         if ($scope.paramCosecha == 1) {
//             $http.post("calidad/php/asignarResultadoLab.php?valor=" + resultadoExperimental + "&idLoteExperimental=" + $scope.loteExp, $scope.informacionCalidad)
//                 .success(function (respuesta) {
//                     growl.success(respuesta);
//                 });
//         } else {
//             $http.post("calidad/php/asignarResultadoLab.php?organica=0&valor=" + resultadoExperimental + "&idLoteExperimental=" + $scope.loteExp, $scope.informacionCalidad)
//                 .success(function (respuesta) {
//                     growl.success(respuesta);
//                 });
//         }
//     };

//     $scope.guardarMarcaFinalCliente = function () {
//         $http.post('calidad/php/editarMarcaCliente.php?miel=' + $scope.paramCosechaInt + '&idLoteInterno=' + $scope.loteInt + "&marcaFinalCliente=" + $scope.marcaFinalCliente)
//             .success(function (respuesta) {
//                 if (respuesta == 1) {
//                     growl.success("Éxito, registro guardado");
//                     $scope.traeDetalleInterno();
//                 } else {
//                     growl.info("Ocurrió un error");
//                 }
//             });
//         $("#editarMarcaCliente").modal('hide');
//         $scope.marcaFinalCliente = "";
//     };

//     $scope.guardarHumedad = function () {
//         $http.post('calidad/php/editarHumedad.php?miel=' + $scope.paramCosecha + '&idLoteExperimental=' + $scope.loteExp + '&humedad=' + $scope.humedad)
//             .success(function (respuesta) {
//                 growl.success("Humedad actualizada");
//                 $scope.traeDetalleExperimental();
//             });
//         $("#editarHumedad").modal('hide');
//         $scope.humedad = "";
//     };

//     $scope.guardarMasMiel = function () {
//         $scope.guardar = true;
//         $scope.datosMasMiel = new Array();
//         $scope.datosMasMiel.push($scope.lotExperimental);
//         $scope.datosMasMiel.push($scope.informacionMiel);
//         $scope.datosMasMiel.push($scope.loteExperimental);
//         $http.post('calidad/php/guardarMasMielExperimental.php?miel=' + $scope.paramCosecha + '&idLoteExperimental=' + $scope.loteExp, { valor: $scope.datosMasMiel })
//             .success(function (data) {
//                 if (data.hasOwnProperty('error')) {
//                     if (!data.error) {
//                         swal("Éxito!", "Experimental modificado", "success");
//                         return window.location.href = "#/ediCalidad/" + $scope.loteExp + '/' + $scope.paramCosecha;
//                     } else {
//                         growl.error(data.message);
//                     }
//                 } else {
//                     console.error(data);
//                 }
//             });
//     };

//     $scope.guardarLoteInterno = function () {
//         $scope.guardar = true;
//         $scope.datosLoteInterno = new Array();
//         $scope.datosLoteInterno.push($scope.loteInterno);
//         $scope.datosLoteInterno.push($scope.loteExperimental);
//         $scope.datosLoteInterno.push($scope.informacionCalidad);
//         if ($scope.loteInterno.loteCliente !== '') {
//             $http.post('calidad/php/guardarCalidad.php?miel=' + $scope.paramCosecha + '&idLoteExperimental=' + $scope.loteExp, { valor: $scope.datosLoteInterno })
//                 .success(function (data) {
//                     if (data.hasOwnProperty('error')) {
//                         if (!data.error) {
//                             swal("Éxito!", "Nuevo registro disponible", "success");
//                             $scope.traeDetalleExperimental();
//                         } else {
//                             growl.error(data.message);
//                         }
//                     } else {
//                         console.error(data);
//                     }

//                     $scope.guardar = false;
//                 });
//             $("#modalLoteInterno").modal('hide');
//         }
//     };

//     $scope.buscar = function (folio, miel, clasificacion) {
//         var mensaje = true;
//         console.log(folio, miel, clasificacion);
//         if ((event.keyCode == 13)) {
//             if (clasificacion == 0) {
//                 $http.post('calidad/php/buscarCalidad.php?miel=' + miel + '&id=' + folio)
//                     .success(function (respuesta) {
//                         console.log(respuesta);
//                         if (respuesta == 0) {
//                             growl.warning("Verifique. Éste folio no está disponible");
//                             $scope.opcionExperimental.folioCalidad = '';
//                         } else {
//                             if ($scope.loteExp > 0) {
//                                 $scope.opcionExperimental.folioCalidad = '';
//                                 $scope.informacionMiel.push(respuesta);
//                                 $scope.lotExperimental.kilosTotales = 0;
//                                 angular.forEach($scope.informacionMiel, function (value, key) {
//                                     $scope.lotExperimental.kilosTotales += parseInt(value.neto);
//                                 });
//                             } else {
//                                 $scope.opcionExperimental.folioCalidad = '';
//                                 $scope.informacionCalidad.push(respuesta);
//                                 $scope.loteExperimental.kilosTotales = 0;
//                                 angular.forEach($scope.informacionCalidad, function (value, key) {
//                                     $scope.loteExperimental.kilosTotales += parseInt(value.neto);
//                                 });
//                             }
//                         }
//                     });
//             } else {
//                 $http.post('calidad/php/buscarFolioSobrante.php?miel=' + miel + '&folio=' + folio + '&clasificacion=' + clasificacion)
//                     .success(function (respuesta) {
//                         console.log(respuesta);

//                         if (respuesta == 0) {
//                             growl.warning("Verifique. Éste folio no está disponible");
//                             $scope.opcionExperimental.folioCalidad = '';
//                         } else {
//                             if ($scope.loteExp > 0) {
//                                 $scope.opcionExperimental.folioCalidad = '';
//                                 $scope.informacionMiel.push(respuesta);
//                                 $scope.lotExperimental.kilosTotales = 0;
//                                 angular.forEach($scope.informacionMiel, function (value, key) {
//                                     $scope.lotExperimental.kilosTotales += parseInt(value.neto);
//                                 });
//                             } else {
//                                 $scope.opcionExperimental.folioCalidad = '';
//                                 $scope.informacionCalidad.push(respuesta);
//                                 $scope.loteExperimental.kilosTotales = 0;
//                                 angular.forEach($scope.informacionCalidad, function (value, key) {
//                                     $scope.loteExperimental.kilosTotales += parseInt(value.neto);
//                                 });
//                             }
//                         }
//                     });
//             }
//         }
//     };


//     $scope.elegirRangoHmf = function () {
//         angular.forEach($scope.hmfSeleccionados, function (value, key) {
//             if (value == "Por rangos") {
//                 $scope.control = true;
//             }
//         });
//         if ($scope.control == true) {
//             angular.forEach($scope.hmfSeleccionados, function (value, key) {
//                 if (value != "Por rangos") {
//                     $scope.hmfSeleccionados.splice(key, 1);
//                     angular.forEach($scope.hmfSeleccionados, function (value, key) {
//                     });
//                 }
//             });
//         }
//     };
//     $scope.elegirRangoPorcentaje = function () {
//         angular.forEach($scope.porcentajeSeleccionados, function (value, key) {
//             if (value == "Por rangos") {
//                 $scope.controlPorcentaje = true;
//             }
//         });
//         if ($scope.controlPorcentaje == true) {
//             angular.forEach($scope.porcentajeSeleccionados, function (value, key) {
//                 if (value != "Por rangos") {
//                     $scope.porcentajeSeleccionados.splice(key, 1);
//                     angular.forEach($scope.porcentajeSeleccionados, function (value, key) {
//                     });
//                 }
//             });
//         }
//     };
//     $scope.cancelarRangoPorcentaje = function () {
//         $scope.controlPorcentaje = false;
//         $scope.porcentajeSeleccionados = new Array();
//         $scope.porcentajeRango1 = "";
//         $scope.porcentajeRango2 = "";
//     };
//     $scope.cancelarRangoHmf = function () {
//         $scope.control = false;
//         $scope.hmfSeleccionados = new Array();
//         $scope.hmfRango1 = "";
//         $scope.hmfRango2 = "";
//     };
//     $scope.cancelarCalidad = function () {
//         $scope.informacionCalidad = new Array();
//         return window.location.href = "#/experimental";

//     };

//     $scope.xlsDescarga = function (int, cosecha) {
//         // return window.location.href = "reportes/calidad/xlsConformacionLote.php?idLoteInterno=" + $scope.loteInt + '&tmp=' + $scope.paramCosechaInt;
//         return window.location.href = "reportes/calidad/xlsConformacionLote.php?idLoteInterno=" + int + '&tmp=' + cosecha;
//     };

//     $scope.pdfDescarga = function (int, cosecha) {
//         // window.open('reportes/calidad/pdfConformacionLote.php?idLoteInterno=' + $scope.loteInt + '&tmp=' + $scope.paramCosechaInt, '_blank');
//         window.open('reportes/calidad/pdfConformacionLote.php?idLoteInterno=' + int + '&tmp=' + cosecha, '_blank');

//     };


//     // ================= ELIMINAR EXPERIMENTAL ================= //
//     $scope.eliminarLoteExp = function () {
//         swal({
//             title: "¿Estás seguro de eliminar todo el lote experimental?",
//             text: "Todos los registros se perderán",
//             type: "warning",
//             showCancelButton: true,
//             confirmButtonColor: "#DD6B55",
//             confirmButtonText: "Sí, eliminar todo.",
//             closeOnConfirm: false
//         },
//             function () {
//                 $http.post('calidad/php/verificarSiExisteReal.php?miel=' + $scope.paramCosecha + '&idLoteExperimental=' + $scope.loteExp).success(function (respuesta) {
//                     if (respuesta == 1) {
//                         swal("Error!", "Verifique! Éste experimental ya es un Lote Interno", "error");
//                     } else {
//                         $http.post('calidad/php/eliminarFoliosLoteExp.php?miel=' + $scope.paramCosecha + '&loteExp=' + $scope.loteExp, { valor: $scope.informacionCalidad })
//                             .success(function (data) {
//                                 if (data.hasOwnProperty('error')) {
//                                     if (data.error) {
//                                         growl.error(data.message);
//                                     } else {
//                                         swal("Éxito!", "Toda la informacion se eliminó", "success");
//                                         return window.location.href = "#/experimental";
//                                     }
//                                 } else {
//                                     console.error(data);
//                                 }
//                             });
//                     }
//                 });
//             });
//     };

//     // ================= ELIMINAR INTERNO ================= //
//     $scope.eliminarLoteInt = function () {
//         $http.post('calidad/php/verificarSiEsUltimoLoteInt.php?miel=' + $scope.paramCosechaInt + '&idLoteInterno=' + $scope.loteInt).success(function (respuesta) {
//             if (respuesta == 1) {
//                 $scope.deleteLoteInt();
//             } else {
//                 swal("Error!", "Sólo puedes eliminar el último Lote guardado", "error");
//             }
//         });
//     };
//     $scope.deleteLoteInt = function () {
//         swal({
//             title: "¿Estás seguro de eliminar todo el lote interno?",
//             text: "Todos los registros se perderán",
//             type: "warning",
//             showCancelButton: true,
//             confirmButtonColor: "#DD6B55",
//             confirmButtonText: "Sí, eliminar todo.",
//             closeOnConfirm: false
//         },
//             function () {
//                 $http.post('calidad/php/eliminarLoteInterno.php?miel=' + $scope.paramCosechaInt + '&idLoteInterno=' + $scope.loteInt + '&idLoteExperimental=' + $scope.loteInterno.idLoteExperimental, { valor: $scope.informacionCalidad })
//                     .success(function (data) {
//                         if (data.hasOwnProperty('error')) {
//                             if (data.error) {
//                                 growl.error(data.message);
//                             } else {
//                                 swal("Éxito!", "Toda la informacion se eliminó", "success");
//                                 return window.location.href = "#/experimental";
//                             }
//                         } else {
//                             console.error(data);
//                         }
//                     });
//             });
//     };

//     $scope.eliminarTambosLoteInt = function () {
//         swal({
//             title: "¿Estás seguro de eliminar todos los tambos?",
//             text: "El lote se conservará",
//             type: "warning",
//             showCancelButton: true,
//             confirmButtonColor: "#DD6B55",
//             confirmButtonText: "Sí, eliminar todo.",
//             closeOnConfirm: false
//         },
//             function () {
//                 $http.post('calidad/php/eliminarTamboresLoteInterno.php?miel=' + $scope.paramCosechaInt + '&idLoteInterno=' + $scope.loteInt + '&idLoteExperimental=' + $scope.loteInterno.idLoteExperimental, { valor: $scope.informacionCalidad })
//                     .success(function (data) {
//                         console.log(data);
//                         if (data.hasOwnProperty('error')) {
//                             if (data.error) {
//                                 growl.error(data.message);
//                             } else {
//                                 swal("Éxito!", "Todos los tambos se eliminaron", "success");
//                                 return window.location.href = "#/experimental";
//                             }
//                         } else {
//                             console.error(data);
//                         }
//                     });
//             });
//     };


// });