form.config(function ($routeProvider) {
    $routeProvider.when('/humedadesAlmacen', {
        templateUrl: 'laboratorio/humedadesPorTambo.html',
        controller: 'humedadesTamboCtrl'
    }).when('/capturaHumedades/:idAlmacen/:tipoDeMiel', {
        templateUrl: 'laboratorio/capturaDeHumedades.html',
        controller: 'humedadesTamboCtrl'
    })
});

form.controller('humedadesTamboCtrl', function ($scope, $http, $routeParams, growl, $location) {

    var date = new Date();
    var _mes = date.getMonth() + 1;
    $scope.mostrarMes = _mes.toString();
    $scope.opcionTipoMiel = '1';
    $scope.entradas = new Array();
    $scope.idAlmacen = $routeParams.idAlmacen;
    $scope.tipoDeMiel = $routeParams.tipoDeMiel;
    $scope.habilitar = false;

    function traeTablaEntradas(tipoDeMiel) {
        $scope.entradas = null;
        $scope.cargandoDatos = true;
        url = 'laboratorio/php/getMenuLaboratorio.php';
        if ($scope.mostrarMes && $scope.mostrarMes !== "null") {
            url += '?mes=' + $scope.mostrarMes;
        }
        $http.post(url, tipoDeMiel).success(function (data) {
            if (data.error) {
                growl.error(data.message);
            }
            $scope.entradas = data.data;
            $scope.cargandoDatos = false;

        });
    }

    if ($location.path() == '/humedadesAlmacen') {
        $http.get('almacen/php/consultaComboMeses.php').success(function (data) {
            $scope.listaDeMeses = data;
        });

        if (window.localStorage.getItem('opcionTipoMiel') != null) {
            $scope.opcionTipoMiel = window.localStorage.getItem('opcionTipoMiel');
        }
        $scope.$watch('opcionTipoMiel', function (tipoMiel) {
            if (tipoMiel) {
                window.localStorage.setItem('opcionTipoMiel', tipoMiel);
                traeTablaEntradas(tipoMiel);
            }
        })
        if (window.localStorage.getItem('opcionMes') != null) {
            $scope.mostrarMes = window.localStorage.getItem('opcionMes');
        }
        $scope.$watch('mostrarMes', function (mesElegido) {
            window.localStorage.setItem('opcionMes', mesElegido);
            traeTablaEntradas($scope.opcionTipoMiel);
        })
    }

    if ($scope.idAlmacen > 0) {
        $http.post("laboratorio/php/infoDetalladaLaboratorio.php", { idAlmacen: $scope.idAlmacen, idTipoDeMiel: $scope.tipoDeMiel }).success(function (respuesta) {
            if (respuesta.error) {
                growl.error(respuesta.message);
            }
            $scope.detalleLab = respuesta.data;
            $scope.informacionLab = respuesta.data.informacionLab;
        });
    }

    $scope.ponerMismoValor = function (id) {
        angular.forEach($scope.informacionLab, function (value, key) {
            if (id == 1) {
                value.porcentaje = $scope.por;
            }
        });
    };

    $scope.guardarLaboratorio = function () {
        $scope.habilitar = true;
        $http.post("laboratorio/php/guardarHumedades.php", { valor: $scope.informacionLab, tipoDeMiel: $scope.tipoDeMiel }).success(function (respuesta) {
            $scope.habilitar = false;
            if (respuesta.error) {
                growl.error(respuesta.message);
            } else {
                swal('', respuesta.message, respuesta.swal);
                return window.location.href = "#/humedadesAlmacen";
            }
        });
    };


});