form.controller('foliosCtrl', ['$scope', '$http', 'growl', function ($scope, $http, growl) {
    $scope.idTipoDeMiel = '1';
    $scope.producto = '1';
    $scope.recipiente = '1';
    $scope.informacionTambor = new Array();
    $scope.tambores = {};
    $scope.opcionBusqueda = {};
    $scope.sobrantemd = null;
    //=================================================================
    //      FUNCION QUE TRAE LA INFORMACION SOLICITADA
    //=================================================================

    $http.post("almacenSobrantes/php/listaTiposDeSobrantes.php").success(function (info) {
        $scope.listaSobrantes = info;
    });

    $scope.$watch('sobrantemd', function (val) {
        $scope.sobrantemd = val;
    });

    $scope.buscarTambo = function () {
        if ((event.keyCode == 13)) {
            if ($scope.producto == '1') {
                $http.post("utilerias/php/buscarTambor.php?idTipoDeMiel=" + $scope.idTipoDeMiel + "&recipiente=" + $scope.recipiente + "&id=" + $scope.opcionBusqueda.tamboNo).success(function (respuesta) {
                    if (respuesta.hasOwnProperty('error')) {
                        if (respuesta.error) {
                            swal('Error', respuesta.message, 'error');
                        } else {
                            if (respuesta.tambor) {
                                $scope.informacionTambor.push(respuesta.tambor);
                                $scope.opcionBusqueda.tamboNo = null;
                            } else {
                                growl.info(respuesta.message);
                            }
                        }
                    } else {
                        growl.error('Error');
                        console.error(respuesta);
                    }
                });
            } else {
                if ($scope.sobrantemd == null) {
                    growl.info('Seleccione un tipo de sobrante');
                } else {
                    $http.post("utilerias/php/buscarTamborSobrante.php?sobrante=" + $scope.sobrantemd + "&idTipoDeMiel=" + $scope.idTipoDeMiel + "&id=" + $scope.opcionBusqueda.tamboNo).success(function (respuesta) {
                        if (respuesta.hasOwnProperty('error')) {
                            if (respuesta.error) {
                                swal('Error', respuesta.message, 'error');
                            } else {
                                if (respuesta.tambor) {
                                    $scope.informacionTambor.push(respuesta.tambor);
                                    $scope.opcionBusqueda.tamboNo = null;
                                } else {
                                    growl.info(respuesta.message);
                                }
                            }
                        } else {
                            growl.error('Error');
                            console.error(respuesta);
                        }
                    });
                }
            }
        }
    };

    $scope.buscarTambores = function () {
        if ((event.keyCode == 13)) {
            if ($scope.producto == '1') {
                $http.get("utilerias/php/buscarTambores.php?idTipoDeMiel=" + $scope.idTipoDeMiel + "&tambo1=" + $scope.opcionBusqueda.tambo1 + "&tambo2=" + $scope.opcionBusqueda.tambo2 + "&recipiente=" + $scope.recipiente).success(function (data) {
                    if (data.hasOwnProperty('error')) {
                        if (data.error) {
                            swal('Error', data.message, 'error');
                        } else {
                            if (data.hasOwnProperty('tambores')) {
                                var tambores = data.tambores;
                                tambores.forEach(function (t) {
                                    $scope.informacionTambor.push(t);
                                });
                                $scope.opcionBusqueda.tambo1 = null;
                                $scope.opcionBusqueda.tambo2 = null;
                            } else {
                                growl.info(data.message);
                            }
                        }
                    } else {
                        growl.error('Error');
                        console.error(data);
                    }
                });
            } else {
                if ($scope.sobrantemd == null) {
                    growl.info('Seleccione un tipo de sobrante');
                } else {
                    $http.post("utilerias/php/buscarTamboresSobrantes.php?sobrante=" + $scope.sobrantemd + "&idTipoDeMiel=" + $scope.idTipoDeMiel + "&tambo1=" + $scope.opcionBusqueda.tambo1 + "&tambo2=" + $scope.opcionBusqueda.tambo2).success(function (data) {
                        if (data.hasOwnProperty('error')) {
                            if (data.error) {
                                swal('Error', data.message, 'error');
                            } else {
                                if (data.hasOwnProperty('tambores')) {
                                    var tambores = data.tambores;
                                    tambores.forEach(function (t) {
                                        $scope.informacionTambor.push(t);
                                    });
                                    $scope.opcionBusqueda.tambo1 = null;
                                    $scope.opcionBusqueda.tambo2 = null;
                                } else {
                                    growl.info(data.message);
                                }
                            }
                        } else {
                            growl.error('Error');
                            console.error(data);
                        }
                    });
                }
            }
        }
    };

    $scope.eliminarTambo = function () {
        $scope.informacionTambor = [];
        $scope.opcionBusqueda.tamboNo = null;
        $scope.opcionBusqueda.tambo1 = null;
        $scope.opcionBusqueda.tambo2 = null;
        growl.warning("Se ha limpiado el registro");
    };

    $scope.imprimirTodo = function () {
        swal({
            title: "¿Estas seguro de mandar a imprimir todo?",
            text: "Todos los registros se van a mandar a imprimir",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#DD6B55",
            confirmButtonText: "Sí, mandar todo.",
            closeOnConfirm: false
        },
            function () {
                angular.forEach($scope.informacionTambor, function (value, key) {
                    $scope.imprimirEtiqueta(value);
                });
                swal("Enviado!", "Toda la informacion se mando a imprimir", "success");
            });
    };


    $scope.imprimirEtiqueta = function (folio) {
        if (folio) {
            switch (folio.identificador) {
                case 'T':
                    var datos = {
                        idTipoDeMiel: folio.idTipoDeMiel,
                        idAlmacen: folio.idAlmacen,
                        estado: 0
                    }
                    break;
                // case 'C':
                //     break;
                case 'S':
                    var datos = {
                        idTipoDeMiel: folio.idTipoDeMiel,
                        idAlmacen: folio.idEntradaSobrante,
                        estado: 5
                    }
                    break;
            }
            $http.post("./mandarImprimir.php", datos).success(function (res) {
                if (!res.error) {
                    growl.success(res.message);
                } else {
                    growl.error(res.message);
                }
            });
        } else {
            growl.error('Uno de los parámetros necesarios no es válido');
        }
    };


}]);