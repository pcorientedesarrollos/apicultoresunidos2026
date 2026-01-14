//form.controller('trazabilidadSalidaCtrl', ['$scope', '$http', '$routeParams', 'growl', function ($scope, $http, $routeParams, growl) {
//
//        //========================= P A R A M E T R O ===========================
//        $scope.parametroSalida = $routeParams.idLoteInterno;
//        //-----------------------------------------------------------------------
//
//
//        $scope.menuTrazabilidadSalida = new Array();
//        $scope.comboSalida = 0;
//        $scope.encabezadoSalida = {};
//        $scope.detalleTrazabilidadSalida = new Array();
//        $scope.comboDestino = {};
//        $scope.comboDestino.idEmpresaPais = "";
//        $scope.comboDestino.destino = "";
//        $scope.aDestino = {};
//
//        if ($scope.parametroSalida > 0) {
//            $http.get('trazabilidad/php/idLoteTrazabilidadSalida.php?idLoteInterno=' + $scope.parametroSalida).success(function (data) {
//                $scope.encabezadoSalida.idLoteInterno = data.idLoteInterno;
//                $scope.encabezadoSalida.homogeneizado = data.homogeneizado;
//                $scope.encabezadoSalida.kilosSalida = data.kilosSalida;
//                $scope.comboDestino = "" + data.idEmpresaPais + "";
//                $scope.encabezadoSalida.kgExportar = data.kgExportar;
//                $scope.encabezadoSalida.fechaEnvasado = data.fechaEnvasado;
//                $scope.encabezadoSalida.fechaSalida = new Date(data.fechaSalida);
//                $scope.encabezadoSalida.fechaSalida.setDate($scope.encabezadoSalida.fechaSalida.getDate() + 1);
//                $scope.kilosTotales = data.kilosTotales;
//                $scope.detalleTrazabilidadSalida = data.detalleTrazabilidadSalida;
//            });
//            $http.get('trazabilidad/php/traeComboDestinos.php').success(function (datas) {
//                $scope.comboDestinos = datas;
//            });
//
//        } else if ($scope.parametroSalida == 0) {
//            $http.get('trazabilidad/php/listaDeLotesSalida.php').success(function (datas) {
//                $scope.lotesSalida = datas;
//            });
//
//            $http.get('trazabilidad/php/traeComboDestinos.php').success(function (dato) {
//                $scope.comboDestinos = dato;
//            });
//
//        } else {
//            $http.get("trazabilidad/php/traeMenuTrazabilidadSalida.php").success(function (data) {
//                $scope.menuTrazabilidadSalida = data;
//            });
//        }
//
//        $scope.concentradoSalida = function () {
//
//            $http.get('trazabilidad/php/idLoteTrazabilidadSalida.php?idLoteInterno=' + $scope.comboSalida).success(function (data) {
//
//                if (data.paso == 1) {
//                    $scope.encabezadoSalida.fechaEnvasado = data.fechaEnvasado;
//                    $scope.encabezadoSalida.fechaSalida = data.fechaSalida;
//                    $scope.kilosTotales = data.kilosTotales;
//                    $scope.detalleTrazabilidadSalida = data.detalleTrazabilidadSalida;
//                    console.log(data);
//                } else {
//                    swal("Marca final no asignada!", "Revise reportes de carga", "error");
//                    $scope.encabezadoSalida.fechaEnvasado = "";
//                    $scope.encabezadoSalida.fechaSalida = "";
//                    $scope.kilosTotales = "";
//                    $scope.comboSalida = "";
//                }
//
//            });
//
//        };
//
//        $scope.copiarKg = function (kgExportar) {
//            $scope.encabezadoSalida.kilosSalida = angular.copy(kgExportar);
//        };
//
//
//
//        $scope.guardarTrazabilidadSalida = function () {
//
//            if ($scope.parametroSalida == 0) {
////                var cadena = $scope.encabezadoSalida.fechaSalida,
////                        subCadena = cadena.substring(15, 10);
//                console.log($scope.encabezadoSalida.fechaSalida);
////                console.log(subCadena);
////                $scope.encabezadoSalida.idLoteInterno = $scope.comboSalida;
////                $scope.encabezadoSalida.idEmpresaPais = $scope.comboDestino;
////                $http.post("trazabilidad/php/guardarTrazabilidadSalida.php", $scope.encabezadoSalida).success(function (info) {
////                    swal("Exito", "Guardado", "success");
////                    $http.get("trazabilidad/php/traeMenuTrazabilidadSalida.php").success(function (data) {
////                        $scope.menuTrazabilidadSalida = data;
////                    });
////                    return window.location.href = "#/tSalida";
////                });
//            } else {
//                $scope.encabezadoSalida.idEmpresaPais = $scope.comboDestino;
//                $http.post("trazabilidad/php/editarTrazabilidadSalida.php?idLoteInterno=" + $scope.parametroSalida, $scope.encabezadoSalida)
//                        .success(function (respuesta) {
//                            swal("Exito", "Actualizado", "success");
//                            $http.get('trazabilidad/php/idLoteTrazabilidadSalida.php?idLoteInterno=' + $scope.parametroSalida).success(function (data) {
//                                $scope.encabezadoSalida.idLoteInterno = data.idLoteInterno;
//                                $scope.encabezadoSalida.homogeneizado = data.homogeneizado;
//                                $scope.encabezadoSalida.kilosSalida = data.kilosSalida;
//                                $scope.comboDestino = "" + data.idEmpresaPais + "";
//                                $scope.encabezadoSalida.kgExportar = data.kgExportar;
//                                $scope.encabezadoSalida.fechaEnvasado = data.fechaEnvasado;
////                                $scope.encabezadoSalida.fechaSalida = data.fechaSalida;
//                                $scope.encabezadoSalida.fechaSalida = data.fechaSalida;
//                                $scope.detalleTrazabilidadSalida = data.detalleTrazabilidadSalida;
//                            });
//                        });
//                return window.location.href = "#/tSalida";
//
//            }
//
//        };
//
//        $scope.nuevoDestino = function () {
//            $("#modalDestino").modal();
//        };
//        $scope.guardarDestino = function () {
//            if ($scope.aDestino.empresa != null && $scope.aDestino.pais != null) {
//                $http.post("trazabilidad/php/guardarNuevoDestino.php", $scope.aDestino).success(function (data) {
//                    $http.get('trazabilidad/php/traeComboDestinos.php').success(function (dato) {
//                        $scope.comboDestinos = dato;
//                    });
//                });
//                swal("Exito!", "Nueva opción disponible", "success");
//                $("#modalDestino").modal('hide');
//                $scope.aDestino = "";
//            } else {
//                growl.warning("Se requiere tener los campos llenos");
//            }
//        };
//
//
//
//        $scope.editarTrazabilidadSalida = function () {
//            $("#mdlEditarTrazabilidadSalida").modal();
//        };
//        $scope.guardarEdicionSalida = function () {
//            $scope.encabezadoSalida.idEmpresaPais = $scope.comboDestino;
//            $http.post("trazabilidad/php/editarTrazabilidadSalida.php?idLoteInterno=" + $scope.parametroSalida, $scope.encabezadoSalida)
//                    .success(function (respuesta) {
//                        swal(respuesta.encabezado, respuesta.mensaje, respuesta.tipo);
//                        $http.get('trazabilidad/php/idLoteTrazabilidadSalida.php?idLoteInterno=' + $scope.parametroSalida).success(function (data) {
//                            $scope.encabezadoSalida.idLoteInterno = data.idLoteInterno;
//                            $scope.encabezadoSalida.homogeneizado = data.homogeneizado;
//                            $scope.encabezadoSalida.kilosSalida = data.kilosSalida;
//                            $scope.comboDestino = "" + data.destino + "";
//                            $scope.encabezadoSalida.kgExportar = data.kgExportar;
//                            $scope.encabezadoSalida.fechaEnvasado = data.fechaEnvasado;
//                            $scope.encabezadoSalida.fechaSalida = data.fechaSalida;
//                            $scope.detalleTrazabilidadSalida = data.detalleTrazabilidadSalida;
//
//                            if ($scope.encabezadoSalida.homogeneizado == 0) {
//                                $scope.encabezadoSalida.homogeneizado = "No";
//                            } else {
//                                $scope.encabezadoSalida.homogeneizado = "Sí";
//                            }
//
//                        });
//                        $("#mdlEditarTrazabilidadSalida").modal('hide');
//                    });
//        };
//
//
//        //-------------------------------------------------------------------------
//
//        $scope.pdfTramzabilidadSalida = function () {
//            window.open('reportes/trazabilidad/pdfTranzabilidadSalida.php?idLoteInterno=' + $scope.parametroSalida, '_blank');
//        };
//
//
//        $scope.xlsTranzabilidadSalida = function () {
//            return window.location.href = "reportes/trazabilidad/xlsTranzabilidadSalida.php?idLoteInterno=" + $scope.parametroSalida;
//        };
//    }]);
//
//
