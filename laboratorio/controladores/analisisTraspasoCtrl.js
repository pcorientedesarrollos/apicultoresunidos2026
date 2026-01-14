form.config(function ($routeProvider) {
    $routeProvider.when('/analisisTraspaso', {
        templateUrl: 'laboratorio/analisisTraspaso.html',
        controller: 'analisisTraspasoCtrl',
    }).when('/capturaATraspaso/:idAlmacen/:tipoDeMiel', {
        templateUrl: 'laboratorio/capturaTraspaso.html',
        controller: 'analisisTraspasoCtrl'
    })
});

form.controller('analisisTraspasoCtrl', function ($scope, $http, $routeParams, growl, busqueda, $location) {

    $scope.entrada = $routeParams.idAlmacen;
    $scope.tipoDeMiel = $routeParams.tipoDeMiel;
    $scope.filtroBusquedaLab = "";
    $scope.listaTraspasos = new Array();
    $scope.reporteEstado = '0';
    $scope.encabezadoDetalle = {};
    $scope.informacionDetalle = new Array();
    $scope.habilitar = false;
    $scope.cargandoDatos = false;
    $scope.verTipoDeMiel = '1';
    var date = new Date();
    var _mes = date.getMonth() + 1;
    $scope.mostrarMes = _mes.toString();

    $http.get('almacen/php/consultaComboMeses.php').success(function (data) {
        $scope.listaDeMeses = data;
    });

    function traeEncabezados(val) {
        $scope.cargandoDatos = true;
        var url = '';
        url = 'almacenTraspaso/php/obtenerEncabezados.php';
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
                    $scope.listaTraspasos = info.resultado;
                }
            } else {
                growl.error('Error');
                console.error(info);
            }
            $scope.cargandoDatos = false;
        });
    }

    if ($location.path() == '/analisisTraspaso') {
        $scope.$watch('[verTipoDeMiel, mostrarMes, reporteEstado]', function (val) {
            if ($scope.verTipoDeMiel) {
                window.localStorage.setItem('ENTRADA_LABORATORIO_TRASPASO', $scope.verTipoDeMiel);
                $scope.listaTraspasos = [];
                traeEncabezados(val);
            }
        });
    }

    if ($scope.entrada > 0) {
        $http.post("laboratorio/php/detalleLaboratorioTraspaso.php", { idAlmacen: $scope.entrada, idTipoDeMiel: $scope.tipoDeMiel }).success(function (respuesta) {
            console.log(respuesta);
            if (respuesta.error) {
                growl.error(respuesta.message);
            }
            $scope.encabezadoDetalle = respuesta.data;
            $scope.informacionDetalle = respuesta.data.informacionLab;
        });
        $http.post("json/laboratorio/JsnMicrobiologia.json").success(function (respuesta) {
            $scope.listaMicrobiologia = respuesta.valores;
        });
        $http.post("json/laboratorio/resultadosFinales.json").success(function (respuesta) {
            $scope.listaResultadoFinal = respuesta.resultadoFinal;
        });
        $http.post("laboratorio/php/listaFloraciones.php").success(function (respuesta) {
            $scope.floracion = respuesta;
        });
    }

    $scope.guardarLaboratorioTraspaso = function () {
        $scope.habilitar = true;
        $http.post("laboratorio/php/guardarLaboratorioTraspaso.php", { valor: $scope.informacionDetalle, tipoDeMiel: $scope.tipoDeMiel }).success(function (respuesta) {
            $scope.habilitar = false;
            if (respuesta.error) {
                growl.error(respuesta.message);
            } else {
                swal('', respuesta.message, respuesta.swal);
                return window.location.href = "#/analisisTraspaso";
            }
        });
    };


    $scope.seleccionarValor = function () {
        angular.forEach($scope.informacionDetalle, function (value, key) {
            value.resultadoFinal = $scope.rstFinal;
        });
    };
    $scope.seleccionarValorMicrobiologia = function () {
        angular.forEach($scope.informacionDetalle, function (value, key) {
            value.micro = $scope.rstMicro;
        });
    };

    $scope.seleccionarValorFloracion = function () {
        angular.forEach($scope.informacionDetalle, function (value, key) {
            value.idFloracion = $scope.rstFloracion;
        });
    };

    $scope.ponerMismoValor = function (id) {
        angular.forEach($scope.informacionDetalle, function (value, key) {
            if (id == 1) {
                value.porcentaje = $scope.por;
            } else if (id == 2) {
                value.sf = $scope.sf;
            } else if (id == 3) {
                value.st = $scope.st;
            } else if (id == 4) {
                value.c13 = $scope.c13;
            } else if (id == 5) {
                value.hmf = $scope.hmf;
            } else if (id == 6) {
                value.color = $scope.color;
            } else if (id == 7) {
                value.tt = $scope.tt;
            }
        });
    };

    $scope.verificarRango = function (idOpcion, valor, valorid, id, tipo) {
        if (valor == "") {
            $("#" + valorid + "txtPorcentaje").removeClass("error");
        }
        var paso = false;
        angular.forEach($scope.opcionesValores, function (value, key) {
            var valor2 = parseInt(value.idRango);
            if (paso == false) {
                if (value.idOpcionLab == idOpcion) {
                    switch (valor2) {
                        case 1:
                            if (parseFloat(valor) < parseFloat(value.rango1)) {
                                paso = true;
                                $("#" + valorid + "" + id).removeClass("error");
                            } else {
                                paso = false;
                                $("#" + valorid + "" + id).addClass("error");
                            }
                            break;
                        case 2:
                            if (parseFloat(valor) > parseFloat(value.rango1)) {
                                paso = true;
                                $("#" + valorid + "" + id).removeClass("error");
                            } else {
                                paso = false;
                                $("#" + valorid + "" + id).addClass("error");
                            }
                            break;
                        case 3:
                            if (parseFloat(valor) <= parseFloat(value.rango1)) {
                                paso = true;
                                $("#" + valorid + "" + id).removeClass("error");
                            } else {
                                paso = false;
                                $("#" + valorid + "" + id).addClass("error");
                            }
                            break;
                        case 4:
                            if (parseFloat(valor) >= parseFloat(value.rango1)) {
                                paso = true;
                                $("#" + valorid + "" + id).removeClass("error");
                            } else {
                                paso = false;
                                $("#" + valorid + "" + id).addClass("error");
                            }
                            break;
                        case 5:
                            if (parseFloat(valor) >= parseFloat(value.rango1) && parseFloat(valor) <= parseFloat(value.rango2)) {
                                paso = true;
                                $("#" + valorid + "" + id).removeClass("error");
                            } else {
                                paso = false;
                                $("#" + valorid + "" + id).addClass("error");
                            }
                            break;
                    }
                }
            }
        });
    };
});
