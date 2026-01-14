form.controller('almacenSobranteCtrl', ['$scope', '$http', '$routeParams', 'growl', '$location', function ($scope, $http, $routeParams, growl, $location) {
    // ============== PARÁMETROS PRINCIPALES ================== //
    // $scope.entradaSobrante = $routeParams.idEntradaSobrante;
    $scope.entradaSobrante = $routeParams.idEncabezadoSobrante;

    //---------------------------------------------------------//

    // ==================== VARIABLES ======================= //
    $scope.tipoDeMiel = 1;
    $scope.listaSobrantes = {};
    $scope.listaCosecha = {};
    $scope.listaZonas = {};
    $scope.almacenSobrante = {
        // bruto: "",
        fecha: "",
        // neto: 0,
        // zona: 0,
        // observaciones: "",
        sobrante: 0,
        // tara: "",
        tipoDeMiel: 0
    };
    $scope.bloquearGuardar = false;
    // $scope.mostrarTable = false;
    $scope.sobrantes = new Array();
    $scope.tambos = new Array();
    //---------------------------------------------------------//

    var date = new Date();
    var _mes = date.getMonth() + 1;;
    $scope.mostrarMes = _mes.toString();
    $http.get('almacen/php/consultaComboMeses.php').success(function (data) {
        $scope.listaDeMeses = data;
    });

    if (window.localStorage.getItem('ENTRADA_MIEL_SOBRANTE') != null) {
        $scope.tipoDeMiel = window.localStorage.getItem('ENTRADA_MIEL_SOBRANTE');
    }

    function traerMenuSobrantes() {
        $scope.sobrantes = null;
        if ($scope.tipoDeMiel) {
            url = 'almacenSobrantes/php/menuEntradaDeSobrantes.php?tipoDeMiel=' + $scope.tipoDeMiel;
            if ($scope.mostrarMes && $location.path() == '/menuSobrantes') {
                url += '&mes=' + $scope.mostrarMes;
            }
            if ($scope.tipoSobrante && $location.path() == '/menuSobrantes') {
                url += '&tipoSobrante=' + $scope.tipoSobrante;
            }
            $http.get(url).success(function (data) {
                $scope.sobrantes = data
                console.log(data);
                if (data.error) {
                    console.error(data.message);
                }
            });
        }
    }

    $scope.$watch('[tipoDeMiel, mostrarMes, tipoSobrante]', function (val) {
        if ($scope.tipoDeMiel) {
            window.localStorage.setItem('ENTRADA_MIEL_SOBRANTE', $scope.tipoDeMiel);
            $scope.sobrantes = [];
            traerMenuSobrantes();
        }
    });

    function traerListaSobrantes() {
        // Llama a esta función cuando necesitas actualizar la lista de sobrantes
        $http.post("almacenSobrantes/php/listaTiposDeSobrantes.php").success(function (info) {
            $scope.listaSobrantes = info;
        });
    }

    // $scope.mostrarCaptura = function (valor) {
    //     $scope.tambos = [];
    //     $scope.counter = "";
    //     if (valor == '1') {
    //         $scope.mostrarTable = true;
    //     } else if (valor == '0') {
    //         $scope.mostrarTable = false;
    //     }
    // };

    $scope.updateModel = function () {
        for (var i = 0; i < $scope.counter; i++) {
            $scope.tambos.push({});
        }
    };

    $scope.eliminarRegistro = function (index) {
        $scope.tambos.splice(index, 1);
    }

    if ($location.path() == '/menuSobrantes') {
        traerMenuSobrantes();
        traerListaSobrantes();
    } else {
        $http.post("utilerias/php/traeTiposDeMiel.php").success(function (info) {
            $scope.listaCosecha = info;
        });
        traerListaSobrantes();
        $scope.$watch('almacenSobrante.tipoDeMiel', function (miel) {
            $http.get("almacen/php/traeZonasDeTambores.php?tipoMiel=" + miel).success(function (info) {
                $scope.listaZonas = info;
            });
            $http.get('produccion/php/listaLotes.php?tipoMiel=' + miel).success(function (dato) {
                $scope.listaLotesCarga = dato.infoLote;
            });
        });

    }

    $scope.calcularNeto = function () {
        $scope.almacenSobrante.neto = $scope.almacenSobrante.bruto - $scope.almacenSobrante.tara;
    };

    $scope.netoSobrantes = function (tipoMiel) {
        $("#modalSobrantes").modal();
        if (tipoMiel == 1) {
            $scope.miel = "MIEL 100% PURA DE ABEJA";
        } else {
            $scope.miel = "MIEL 100% ORGÁNICA";
        }
        $http.get("almacenSobrantes/php/netosDeSobrantes.php?tipoDeMiel=" + tipoMiel).success(function (data) {
            $scope.totalesEnSobrantes = data;
        });
    };

    $scope.validarSobrante = function () {
        $scope.sobranteValido = false;
        if ($scope.almacenSobrante.fecha == "") {
            growl.error("Se requiere una fecha");
        } else if ($scope.almacenSobrante.tipoDeMiel == 0) {
            growl.error("Se requiere un tipo de miel");
        } else if ($scope.almacenSobrante.sobrante == 0) {
            growl.error("Se requiere un tipo de sobrante");
        } else if ($scope.almacenSobrante.bruto == "") {
            growl.error("Se requiere un peso bruto");
        } else if ($scope.almacenSobrante.tara == "") {
            growl.error("Se requiere una tara");
        } else if ($scope.almacenSobrante.zona == 0) {
            growl.error("Se requiere una zona");
        } else {
            $scope.sobranteValido = true;
        }
        return $scope.sobranteValido;
    };

    $scope.guardarSobrante = function () {
        $scope.arreglo = [];
        $scope.arreglo.push($scope.almacenSobrante);
        $scope.arreglo.push($scope.tambos);
        // $scope.sobranteValido = $scope.validarSobrante();
        // if ($scope.sobranteValido == true) {
        $http.post("almacenSobrantes/php/guardarEntradaSobrante.php", $scope.arreglo).success(function (info) {
            if (info.hasOwnProperty('error')) {
                if (!info.error) {
                    swal('', info.message, info.swal);
                    window.location.href = '#/menuSobrantes';
                } else {
                    swal('Error', info.message, 'error');
                }
            } else {
                growl.error('Error');
                console.error(info);
            }
            // swal("¡Éxito!", "Entrada registrada", "success");
            // return window.location.href = "#/menuSobrantes/";
        });
        // }
    };

    if ($scope.entradaSobrante > 0) {
        $http.get("almacenSobrantes/php/detalleEntradaSobrante.php?entrada=" + $scope.entradaSobrante).success(function (info) {
            $scope.almacenSobrante = info.data;
            $scope.tambos = $scope.almacenSobrante.tambos;
        });
    };


    // Para alta rapida de clasificaciones de miel sobrante
    $scope.abrirModalNuevaClasificacion = function () {
        $scope.nuevaClasificacion = {};
        $('#modalNuevaClasificacion').modal();
    }

    $scope.guardarNuevaClasificacion = function () {
        if ($scope.nuevaClasificacion.nombre && $scope.nuevaClasificacion != '') {
            $http.post('catalogos/php/guardarNuevaClasificacion.php', $scope.nuevaClasificacion).success(function (data) {
                if (data.hasOwnProperty('error')) {
                    if (data.error) {
                        swal('Error', data.message, 'error');
                    } else {
                        $('#modalNuevaClasificacion').modal('hide');
                        growl.success(data.message);
                        traerListaSobrantes();
                    }
                } else {
                    growl.error('Error');
                    console.error(data);
                }
            });
        } else {
            growl.info('Ingresa el nombre de la clasificación');
        }
    }

    $scope.mandarImprimirEtiquetaMielSobrante = async function (idEntradaSobrante) {
        datos = {
            idEntradaSobrante: idEntradaSobrante
        }
        await $http.post('almacenSobrantes/php/mandarImprimir.php', datos).success(function (data) {
            if (data.hasOwnProperty('error')) {
                if (data.error) {
                    swal('Error', data.message, 'error');
                } else {
                    growl.success(data.message);
                }
            } else {
                growl.error('Error');
                console.error(data);
            }
        })
    }

    $scope.mandarImprimirTodo = function () {
        swal({
            title: "¿Estas seguro de mandar a imprimir todo?",
            text: "Todos los registros se van a mandar a imprimir",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#DD6B55",
            confirmButtonText: "Sí, mandar todo.",
            closeOnConfirm: false
        },
            async function () {
                await angular.forEach($scope.tambos, function (value, key) {
                    $scope.mandarImprimirEtiquetaMielSobrante(value.idEntradaSobrante);
                });
                swal("Enviado!", "Toda la informacion se mando a imprimir", "success");
            });
    };


}]);
