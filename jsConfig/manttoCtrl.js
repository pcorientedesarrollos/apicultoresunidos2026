form.controller('manttoCtrl', ['$scope', '$http', '$routeParams', '$rootScope', function ($scope, $http, $routeParams, $rootScope) {

//=========================P A R A M E T R O===========================
        $scope.rutaProgramacion = $routeParams.idProgramacion;
        $scope.rutaMantto = $routeParams.idMantenimiento;
        $scope.paramEquipo = $routeParams.idEquipo;
        $scope.paramCrono = $routeParams.idOpcion;
//    ----------------------------------------------------------------

        $scope.menuMantenimiento = new Array();
        $scope.mantto = {};
        $scope.controlMantto = {};
        $scope.mantenimientos = new Array();
        $scope.manttos = {};
        $scope.crono = {};
        $scope.listaDeAreas = {};
        $scope.listaDeNombres = {};
        $scope.listaDeTecnicos = {};
        $scope.opcionPersonal = {};
        $scope.areaMantto = {};
        $scope.realizoMantto = {};
        $scope.realizoMantto.idPersonalOM = "";
        $scope.realizoMantto.nombre = "";
        $scope.realizoExterno = {};
        $scope.realizoExterno.idTecnico = "";
        $scope.realizoExterno.tecnico = "";
        $scope.opcionCrono = {};
        $scope.infoEquipo = {};
        $scope.manttoExtra = {};
        $scope.vistaMantto = {};

        if ($scope.paramEquipo > 0) {
            $http.get("controlMantenimiento/php/traeMantenimientosPorEquipo.php?idEquipo=" + $scope.paramEquipo + "&extra='0'").success(function (data) {
                $scope.extraordinarios = data;
            });
            $http.get("controlMantenimiento/php/traeEncabezadoVistaManttos.php?idEquipo=" + $scope.paramEquipo).success(function (data) {
                $scope.infoEquipo = data;
            });
            $http.get("controlMantenimiento/php/traeInfoMantto.php?idEquipo=" + $scope.paramEquipo).success(function (data) {
                $scope.controlMantto = data;
            });
            $http.get('almacen/php/listaNombresPersonal.php').success(function (data) {
                $scope.listaDeNombres = data;
            });
            $http.get('controlMantenimiento/php/listaDeTecnicos.php').success(function (data) {
                $scope.listaDeTecnicos = data;
            });
            $scope.$watch('vistaMantto.opcion', function () {
                $scope.vista = $scope.vistaMantto.opcion;
                if ($scope.vista == 1) {
                    $http.get("controlMantenimiento/php/traeMantenimientosPorEquipo.php?idEquipo=" + $scope.paramEquipo).success(function (data) {
                        $scope.mantenimientos = data;
                    });
                } else {
                    $http.get("controlMantenimiento/php/traeMantenimientosPorEquipo.php?idEquipo=" + $scope.paramEquipo + "&extra='0'").success(function (data) {
                        $scope.extraordinarios = data;
                    });
                }
            });
        }

        if ($scope.rutaMantto > 0) {
            $http.get("controlMantenimiento/php/traeInfoMantto.php?idMantenimiento=" + $scope.rutaMantto).success(function (data) {
                $scope.controlMantto = data;
                $scope.opcionPersonal.opcion = data.tipoPersonal;
                $scope.opcionPersonal.idPersonalOM = "" + $scope.controlMantto.idPersonalOM + "";
                $scope.opcionPersonal.nombreTecnico = data.nombreTecnico;
            });
            $http.get('almacen/php/listaNombresPersonal.php').success(function (data) {
                $scope.listaDeNombres = data;
            });
            $http.get('controlMantenimiento/php/listaDeTecnicos.php').success(function (data) {
                $scope.listaDeTecnicos = data;
            });
        }

        if ($scope.rutaProgramacion > 0) {
            $http.get('almacen/php/listaNombresPersonal.php').success(function (data) {
                $scope.listaDeNombres = data;
            });
            $http.get('controlMantenimiento/php/listaDeTecnicos.php').success(function (data) {
                $scope.listaDeTecnicos = data;
            });
            $http.get("controlMantenimiento/php/traeInfoMantto.php?idProgramacion=" + $scope.rutaProgramacion).success(function (data) {
                $scope.controlMantto = data;
                $scope.opcionPersonal.opcion = data.tipoPersonal;
                $scope.opcionPersonal.idPersonalOM = "" + $scope.controlMantto.idPersonalOM + "";
                $scope.opcionPersonal.nombreTecnico = data.nombreTecnico;
                console.log($scope.controlMantto);
            });
        }

        $scope.guardarMantto = function () {

            $scope.controlMantto.idPersonalOM = $scope.opcionPersonal.idPersonalOM;
            $scope.controlMantto.nombreTecnico = $scope.opcionPersonal.nombreTecnico;
            if ($scope.opcionPersonal.opcion == 1) {
                $scope.controlMantto.tipoPersonal = '1';
            } else {
                $scope.controlMantto.tipoPersonal = '2';
            }
            var fecha = $scope.controlMantto.fechaReal.split("-");
            var mes = fecha[1];
            $scope.controlMantto.idMes = mes;
            if ($scope.rutaProgramacion > 0) {
                if ($scope.controlMantto.idMantenimiento > 0) {
                    $http.post("controlMantenimiento/php/guardarMantenimiento.php?idMantenimiento=" + $scope.controlMantto.idMantenimiento, $scope.controlMantto).success(function (info) {
                        swal("Exito", "Registro actualizado", "success");
                        $http.get("controlMantenimiento/php/traeMantenimientosPorEquipo.php?idEquipo=" + $scope.controlMantto.idEquipo).success(function (data) {
                            $scope.infoEquipo = data;
                            $scope.mantenimientos = data.mantenimientos;
                        });
                        if ($scope.controlMantto.mantto == 1) {
                            return window.location.href = "#/manttoEsporadico/" + $scope.controlMantto.idEquipo;
                        } else {
                            return window.location.href = "#/modificarMantto/" + $scope.controlMantto.idEquipo;
                        }
                    });
                } else {
                    $http.post("controlMantenimiento/php/guardarMantenimiento.php", $scope.controlMantto).success(function (info) {
                        swal("Exito", "Guardado", "success");
                        $http.get("controlMantenimiento/php/traeMantenimientosPorEquipo.php?idEquipo=" + $scope.controlMantto.idEquipo).success(function (data) {
                            $scope.infoEquipo = data;
                            $scope.mantenimientos = data.mantenimientos;
                        });
                        if ($scope.controlMantto.mantto == 1) {
                            return window.location.href = "#/manttoEsporadico/" + $scope.controlMantto.idEquipo;
                        } else {
                            return window.location.href = "#/modificarMantto/" + $scope.controlMantto.idEquipo;
                        }
//                        return window.location.href = "#/modificarMantto/" + $scope.controlMantto.idEquipo;
                    });
                }
            } else {
                if ($scope.controlMantto.idMantenimiento > 0) {
                    $http.post("controlMantenimiento/php/guardarMantenimiento.php?idMantenimiento=" + $scope.controlMantto.idMantenimiento, $scope.controlMantto).success(function (info) {
                        swal("Exito", "Registro actualizado", "success");
                        $http.get("controlMantenimiento/php/traeMantenimientosPorEquipo.php?idEquipo=" + $scope.controlMantto.idEquipo).success(function (data) {
                            $scope.infoEquipo = data;
                            $scope.mantenimientos = data.mantenimientos;
                        });
                        if ($scope.controlMantto.mantto == 1) {
                            return window.location.href = "#/manttoEsporadico/" + $scope.controlMantto.idEquipo;
                        } else {
                            return window.location.href = "#/modificarMantto/" + $scope.controlMantto.idEquipo;
                        }
//                        return window.location.href = "#/modificarMantto/" + $scope.controlMantto.idEquipo;
                    });
                } else {
                    $http.post("controlMantenimiento/php/guardarMantenimiento.php?tipo='1'", $scope.controlMantto).success(function (info) {
                        swal("Exito", "Guardado", "success");
                        $http.get("controlMantenimiento/php/traeMantenimientosPorEquipo.php?idEquipo=" + $scope.controlMantto.idEquipo).success(function (data) {
                            $scope.infoEquipo = data;
                            $scope.mantenimientos = data.mantenimientos;
                        });
                        if ($scope.controlMantto.mantto == 1) {
                            return window.location.href = "#/manttoEsporadico/" + $scope.controlMantto.idEquipo;
                        } else {
                            return window.location.href = "#/modificarMantto/" + $scope.controlMantto.idEquipo;
                        }
//                        return window.location.href = "#/modificarMantto/" + $scope.controlMantto.idEquipo;
                    });
                }
            }

        };
/////////////////  C R O N O G R A M A  ///////////////////////


        $scope.enviarCrono = function (idOpcion, areaCrono) {
            if (idOpcion == "1") {
                $http.get("controlMantenimiento/php/traeCronograma.php").success(function (data) {
                    $rootScope.tablaCronograma = data;
                    angular.forEach($scope.tablaCronograma, function (e) {
                        e.fechas = {};
                        angular.forEach(e.listaFechas, function (lista) {
                            switch (lista.idMes) {
                                case '1':
                                    e.fechas.enero = {
                                        'p': lista.fechaProgramada,
                                        'r': lista.fechaReal
                                    };
                                    break;
                                case '2':
                                    e.fechas.febrero = {
                                        'p': lista.fechaProgramada,
                                        'r': lista.fechaReal
                                    };
                                    break;
                                case '3':
                                    e.fechas.marzo = {
                                        'p': lista.fechaProgramada,
                                        'r': lista.fechaReal
                                    };
                                    break;
                                case '4':
                                    e.fechas.abril = {
                                        'p': lista.fechaProgramada,
                                        'r': lista.fechaReal
                                    };
                                    break;
                                case '5':
                                    e.fechas.mayo = {
                                        'p': lista.fechaProgramada,
                                        'r': lista.fechaReal
                                    };
                                    break;
                                case '6':
                                    e.fechas.junio = {
                                        'p': lista.fechaProgramada,
                                        'r': lista.fechaReal
                                    };
                                    break;
                                case '7':
                                    e.fechas.julio = {
                                        'p': lista.fechaProgramada,
                                        'r': lista.fechaReal
                                    };
                                    break;
                                case '8':
                                    e.fechas.agosto = {
                                        'p': lista.fechaProgramada,
                                        'r': lista.fechaReal
                                    };
                                    break;
                                case '9':
                                    e.fechas.septiembre = {
                                        'p': lista.fechaProgramada,
                                        'r': lista.fechaReal
                                    };
                                    break;
                                case '10':
                                    e.fechas.octubre = {
                                        'p': lista.fechaProgramada,
                                        'r': lista.fechaReal
                                    };
                                    break;
                                case '11':
                                    e.fechas.noviembre = {
                                        'p': lista.fechaProgramada,
                                        'r': lista.fechaReal
                                    };
                                    break;
                                case '12':
                                    e.fechas.diciembre = {
                                        'p': lista.fechaProgramada,
                                        'r': lista.fechaReal
                                    };
                                    break;
                            }
                        });
                        angular.forEach(e.extraordinarias, function (lista) {
                            switch (lista.mes) {
                                case '1':
                                    e.fechas.enero = {
                                        'ex': lista.extraordinaria
                                    };
                                    break;
                                case '2':
                                    e.fechas.febrero = {
                                        'ex': lista.extraordinaria
                                    };
                                    break;
                                case '3':
                                    e.fechas.marzo = {
                                        'ex': lista.extraordinaria
                                    };
                                    break;
                                case '4':
                                    e.fechas.abril = {
                                        'ex': lista.extraordinaria
                                    };
                                    break;
                                case '5':
                                    e.fechas.mayo = {
                                        'ex': lista.extraordinaria
                                    };
                                    break;
                                case '6':
                                    e.fechas.junio = {
                                        'ex': lista.extraordinaria
                                    };
                                    break;
                                case '7':
                                    e.fechas.julio = {
                                        'ex': lista.extraordinaria
                                    };
                                    break;
                                case '8':
                                    e.fechas.agosto = {
                                        'ex': lista.extraordinaria
                                    };
                                    break;
                                case '9':
                                    e.fechas.septiembre = {
                                        'ex': lista.extraordinaria
                                    };
                                    break;
                                case '10':
                                    e.fechas.octubre = {
                                        'ex': lista.extraordinaria
                                    };
                                    break;
                                case '11':
                                    e.fechas.noviembre = {
                                        'ex': lista.extraordinaria
                                    };
                                    break;
                                case '12':
                                    e.fechas.diciembre = {
                                        'ex': lista.extraordinaria
                                    };
                                    break;
                            }
                        });
                    });
                });
            } else {

                $http.get("controlMantenimiento/php/encabezadoCronograma.php?idArea=" + areaCrono).success(function (data) {
                    $rootScope.nameArea = data;
                });
                $http.get("controlMantenimiento/php/traeCronograma.php?idArea=" + areaCrono).success(function (data) {
                    $rootScope.tablaCronograma = data;
                    angular.forEach($scope.tablaCronograma, function (e) {
                        e.fechas = {};
                        angular.forEach(e.listaFechas, function (lista) {
                            switch (lista.idMes) {
                                case '1':
                                    e.fechas.enero = {
                                        'p': lista.fechaProgramada,
                                        'r': lista.fechaReal
                                    };
                                    break;
                                case '2':
                                    e.fechas.febrero = {
                                        'p': lista.fechaProgramada,
                                        'r': lista.fechaReal
                                    };
                                    break;
                                case '3':
                                    e.fechas.marzo = {
                                        'p': lista.fechaProgramada,
                                        'r': lista.fechaReal
                                    };
                                    break;
                                case '4':
                                    e.fechas.abril = {
                                        'p': lista.fechaProgramada,
                                        'r': lista.fechaReal
                                    };
                                    break;
                                case '5':
                                    e.fechas.mayo = {
                                        'p': lista.fechaProgramada,
                                        'r': lista.fechaReal
                                    };
                                    break;
                                case '6':
                                    e.fechas.junio = {
                                        'p': lista.fechaProgramada,
                                        'r': lista.fechaReal
                                    };
                                    break;
                                case '7':
                                    e.fechas.julio = {
                                        'p': lista.fechaProgramada,
                                        'r': lista.fechaReal
                                    };
                                    break;
                                case '8':
                                    e.fechas.agosto = {
                                        'p': lista.fechaProgramada,
                                        'r': lista.fechaReal
                                    };
                                    break;
                                case '9':
                                    e.fechas.septiembre = {
                                        'p': lista.fechaProgramada,
                                        'r': lista.fechaReal
                                    };
                                    break;
                                case '10':
                                    e.fechas.octubre = {
                                        'p': lista.fechaProgramada,
                                        'r': lista.fechaReal
                                    };
                                    break;
                                case '11':
                                    e.fechas.noviembre = {
                                        'p': lista.fechaProgramada,
                                        'r': lista.fechaReal
                                    };
                                    break;
                                case '12':
                                    e.fechas.diciembre = {
                                        'p': lista.fechaProgramada,
                                        'r': lista.fechaReal
                                    };
                                    break;
                            }
                        });
                        angular.forEach(e.extraordinarias, function (lista) {
                            switch (lista.mes) {
                                case '1':
                                    e.fechas.enero = {
                                        'ex': lista.extraordinaria
                                    };
                                    break;
                                case '2':
                                    e.fechas.febrero = {
                                        'ex': lista.extraordinaria
                                    };
                                    break;
                                case '3':
                                    e.fechas.marzo = {
                                        'ex': lista.extraordinaria
                                    };
                                    break;
                                case '4':
                                    e.fechas.abril = {
                                        'ex': lista.extraordinaria
                                    };
                                    break;
                                case '5':
                                    e.fechas.mayo = {
                                        'ex': lista.extraordinaria
                                    };
                                    break;
                                case '6':
                                    e.fechas.junio = {
                                        'ex': lista.extraordinaria
                                    };
                                    break;
                                case '7':
                                    e.fechas.julio = {
                                        'ex': lista.extraordinaria
                                    };
                                    break;
                                case '8':
                                    e.fechas.agosto = {
                                        'ex': lista.extraordinaria
                                    };
                                    break;
                                case '9':
                                    e.fechas.septiembre = {
                                        'ex': lista.extraordinaria
                                    };
                                    break;
                                case '10':
                                    e.fechas.octubre = {
                                        'ex': lista.extraordinaria
                                    };
                                    break;
                                case '11':
                                    e.fechas.noviembre = {
                                        'ex': lista.extraordinaria
                                    };
                                    break;
                                case '12':
                                    e.fechas.diciembre = {
                                        'ex': lista.extraordinaria
                                    };
                                    break;
                            }
                        });
                    });
                    return window.location.href = "#/verCronograma/" + idOpcion;
                });
            }
        };
        $scope.$watch('opcionCrono.idOpcion', function () {
            $scope.opcionCrono.areaCrono = "";
        });
        if ($scope.paramCrono != 0) {
            $http.get('personalOaxacaMiel/php/listaAreas.php').success(function (datas) {
                $scope.listaDeAreas = datas;
            });
        }

        $scope.manttoPDFArea = function (idArea) {
            window.open('reportes/controlMantenimiento/pdfCronogramaMantenimientoPorArea.php?idArea=' + idArea, '_blank');
        };
        $scope.manttoPDF = function () {
            window.open('reportes/controlMantenimiento/pdfCronogramaMantenimiento.php', '_blank');
        };
    }]);