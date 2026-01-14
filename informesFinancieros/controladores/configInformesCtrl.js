form.config(function ($routeProvider) {
    $routeProvider.when('/configInformes', {
        templateUrl: 'informesFinancieros/configuracionInformes.html',
        controller: 'configInformesCtrl'
    })
    // .when('/capturainformesFinancieros/:idTamborPeso', {
    //     templateUrl: 'informesFinancieros/capturaFacturacion.html',
    //     controller: 'configInformesCtrl'
    // })
});

form.controller('configInformesCtrl', function ($scope, $http, $routeParams, growl, $location) {

    function traeListaInformesFinancieros() {
        $http.get('catalogos/php/traeListaInformesFinancieros.php').success(function (data) {
            if (data.hasOwnProperty('error')) {
                if (data.error) {
                    swal('Error', data.message, 'error');
                } else {
                    $scope.informesFinancieros = data.informesFinancieros;
                }
            } else {
                growl.console.error(('Error'));
                console.error(data);
            }
        });
    }

    traeListaInformesFinancieros();

    $scope.traeCuentaPorReporte = function (tipo) {
        $http.get('informesFinancieros/php/traeCuentasPorInforme.php?tipo=' + tipo).success(function (data) {
            if (data.hasOwnProperty('error')) {
                if (data.error) {
                    swal('Error', data.message, 'error');
                } else {
                    $scope.cuentas = data.data;
                }
            } else {
                growl.console.error(('Error'));
                console.error(data);
            }
        });
    }

    $scope.guardarOrdenCuentas = function () {
        $http.post('informesFinancieros/php/guardarOrdenCuentas.php', $scope.cuentas).success(function (data) {
            if (data.hasOwnProperty('error')) {
                if (data.error) {
                    swal('Error', data.message, 'error');
                } else {
                    $scope.traeCuentaPorReporte($scope.tipoInforme);
                    swal('Éxito', 'Orden actualizado', 'success');
                }
            } else {
                growl.console.error(('Error'));
                console.error(data);
            }
        });
    }

});