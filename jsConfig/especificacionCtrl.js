form.controller('especificacionCtrl', ['$scope', '$http', '$routeParams', 'growl', function ($scope, $http, $routeParams, growl) {

    //=========================P A R A M E T R O===========================
    $scope.loteInter = $routeParams.idLoteInterno;
    $scope.idEspecificaciones = $routeParams.idEspecificacion;
    $scope.tipoDeCosecha = $routeParams.opcionCosechaEspecificacion;
    $scope.numLote = "0";
    $scope.numLote.idLoteInterno = "";
    $scope.listaLoteses = {};
    $scope.encargadoLab = "0";
    $scope.listaAsignado = {};
    $scope.listaAsignado.id = "";
    $scope.listaAsignado.nombre = "";
    $scope.menuEspecificacion = new Array();
    $scope.cliente = {};
    $scope.cliente.humedad = "";
    $scope.cliente.color = "";
    $scope.cliente.adulteracion = "";
    $scope.cliente.sf = "";
    $scope.cliente.st = "";
    $scope.cliente.tt = "";
    $scope.cliente.hmf = "";
    $scope.laboratorioes = {};
    $scope.laboratorioes.humedad = "";
    $scope.laboratorioes.color = "";
    $scope.laboratorioes.adulteracion = "";
    $scope.laboratorioes.sf = "";
    $scope.laboratorioes.st = "";
    $scope.laboratorioes.tt = "";
    $scope.laboratorioes.hmf = "";

    if ($scope.loteInter > 0) {
        $scope.$watch('mielTipo', function (val) {
            if (val == 1) {
                $http.get('calidad/php/listaLoteEspe.php').success(function (data) {
                    $scope.listaLoteses = data;
                });
            } else if (val == 2) {
                $http.get('calidad/php/listaLoteEspe.php?organica=0').success(function (data) {
                    $scope.listaLoteses = data;
                });
            }
        });
        $http.post("utilerias/php/traeTiposDeMiel.php").success(function (info) {
            if (!info.hasOwnProperty('error')) {
                $scope.lstTipoCosecha = info;
            }
        });
        if ($scope.tipoDeCosecha == 1) {
            $http.get('calidad/php/traeEspecificacionesCliente.php?idLoteInterno=' + $scope.loteInter).success(function (data) {
                $scope.numLote = data[1].idLoteInterno;
                console.log($scope.numLote);
                $scope.mielTipoTex = "Miel 100% pura de abeja";
                $scope.mielTipo = 1;
                if (data[0].tipo == 1) {
                    $scope.cliente = data[0];
                }
                if (data[1].tipo == 2) {
                    $scope.laboratorioes = data[1];

                }
            });
        } else {
            $http.get('calidad/php/traeEspecificacionesCliente.php?organica=0&idLoteInterno=' + $scope.loteInter).success(function (data) {
                $scope.numLote = data[1].idLoteInterno;
                $scope.mielTipoTex = "Miel 100% orgánica";
                $scope.mielTipo = 2;
                if (data[0].tipo == 1) {
                    $scope.cliente = data[0];
                }
                if (data[1].tipo == 2) {
                    $scope.laboratorioes = data[1];

                }
            });
        }
    } else {
        $scope.$watch('mielTipo', function (val) {
            if (val == 1) {
                $http.get('calidad/php/listaLoteEspe.php').success(function (data) {
                    $scope.listaLoteses = data;
                });
            } else if (val == 2) {
                $http.get('calidad/php/listaLoteEspe.php?organica=0').success(function (data) {
                    $scope.listaLoteses = data;
                });
            }
        });
        $http.post("utilerias/php/traeTiposDeMiel.php").success(function (info) {
            if (!info.hasOwnProperty('error')) {
                $scope.lstTipoCosecha = info;
            }
        });
        $scope.$watch('opcionCosechaEspecificacion', function (val) {
            if (val == 1) {
                $http.get('calidad/php/traeResumenDeEspecificaciones.php').success(function (data) {
                    $scope.menuEspecificacion = data;
                });
            } else if (val == 2) {
                $http.get('calidad/php/traeResumenDeEspecificaciones.php?organica=0').success(function (data) {
                    $scope.menuEspecificacion = data;
                });
            }
        });
    }

    $scope.guardarEspecificacion = function (mielTipo) {
        if ($scope.loteInter > 0) {
            $scope.cliente.idLoteInterno = $scope.numLote;
            $scope.laboratorioes.idLoteInterno = $scope.numLote;
            $scope.edicion = new Array();
            $scope.edicion.push($scope.cliente);
            $scope.edicion.push($scope.laboratorioes);
            if (mielTipo == 1) {
                $http.post("calidad/php/guardarEdicionEspecificacion.php", { valor: $scope.edicion }).success(function () {
                    swal("Exito!", "Se ha actualizado las especificaciones", "success");
                });
            } else {
                $http.post("calidad/php/guardarEdicionEspecificacion.php?organica=0", { valor: $scope.edicion }).success(function () {
                    swal("Exito!", "Se ha actualizado las especificaciones", "success");
                });
            }
        } else {
            $scope.cliente.tipo = "1";
            $scope.laboratorioes.tipo = "2";
            $scope.cliente.idLoteInterno = $scope.numLote;
            $scope.laboratorioes.idLoteInterno = $scope.numLote;
            $scope.especificacion = new Array();
            $scope.especificacion.push($scope.cliente);
            $scope.especificacion.push($scope.laboratorioes);

            if (mielTipo == 1) {
                $http.post("calidad/php/guardarEspecificaciones1.php", $scope.especificacion).success(function (respuesta) {
                    swal("Especificacion guardada", "Exito");
                });
            } else {
                $http.post("calidad/php/guardarEspecificaciones1.php?organica=0", $scope.especificacion).success(function (respuesta) {
                    swal("Especificacion guardada", "Exito");
                });
            }
        }
    };

}]);