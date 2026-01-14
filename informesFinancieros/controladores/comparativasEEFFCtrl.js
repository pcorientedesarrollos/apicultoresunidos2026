form.config(function ($routeProvider) {
    $routeProvider.when('/comparativasrepee', {
        templateUrl: 'informesFinancieros/comparativasEEFF.html',
        controller: 'comparativasCtrl'
    })
    // .when('/capturainformesFinancieros/:idTamborPeso', {
    //     templateUrl: 'informesFinancieros/capturaFacturacion.html',
    //     controller: 'comparativasCtrl'
    // })
});

form.controller('comparativasCtrl', function ($scope, $http, $routeParams, growl, $location) {


    $http.get('almacen/php/consultaComboMeses.php').success(function (data) {
        $scope.meses = data;
    });

    function obtenerComparativa(mes) {
        $scope.cargandoDatos = true;
        var url = 'informesFinancieros/php/obtenerComparativa.php';
        if (mes) {
            url += '?mes=' + mes;
        }
        $http.post(url).success(function (data) {
            console.log(data);
            $scope.cargandoDatos = false;
            $scope.comparativa = data.data;
        });
    }

    $scope.$watch('opcionMes', function (mes) {
        if (mes) {
            obtenerComparativa(mes);
        }
    });

    // function traeListaInformesFinancieros() {
    //     $http.get('catalogos/php/traeListaInformesFinancieros.php').success(function (data) {
    //         if (data.hasOwnProperty('error')) {
    //             if (data.error) {
    //                 swal('Error', data.message, 'error');
    //             } else {
    //                 $scope.informesFinancieros = data.informesFinancieros;
    //             }
    //         } else {
    //             growl.console.error(('Error'));
    //             console.error(data);
    //         }
    //     });
    // }

    // traeListaInformesFinancieros();

    // $scope.traeCuentaPorReporte = function (tipo) {
    //     $http.get('informesFinancieros/php/traeCuentasPorInforme.php?tipo=' + tipo).success(function (data) {
    //         if (data.hasOwnProperty('error')) {
    //             if (data.error) {
    //                 swal('Error', data.message, 'error');
    //             } else {
    //                 $scope.cuentas = data.data;
    //             }
    //         } else {
    //             growl.console.error(('Error'));
    //             console.error(data);
    //         }
    //     });
    // }

    // $scope.guardarOrdenCuentas = function () {
    //     $http.post('informesFinancieros/php/guardarOrdenCuentas.php', $scope.cuentas).success(function (data) {
    //         if (data.hasOwnProperty('error')) {
    //             if (data.error) {
    //                 swal('Error', data.message, 'error');
    //             } else {
    //                 $scope.traeCuentaPorReporte($scope.tipoInforme);
    //                 swal('Éxito', 'Orden actualizado', 'success');
    //             }
    //         } else {
    //             growl.console.error(('Error'));
    //             console.error(data);
    //         }
    //     });
    // }

});