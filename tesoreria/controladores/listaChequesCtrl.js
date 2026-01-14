form.config(function ($routeProvider) {
    $routeProvider.when('/listaCheques', {
        templateUrl: 'tesoreria/html/listaChequesEntregados.html',
        controller: 'listaChequesCtrl'
    })
})
form.controller('listaChequesCtrl', ['$scope', '$http', '$routeParams', 'growl', '$rootScope', '$location', function ($scope, $http, $routeParams, growl, $rootScope, $location) {

    $scope.chequesEntregados = new Array();

    $http.get('controlAdministrativo/php/listaDeBancos.php').success(function (datas) {
        $scope.listaDeBancos = datas;
    });

    function traeListaCheques(banco) {
        $http.get('chequesEntregados/php/traeRegistrosPorBanco.php?idBanco=' + banco).success(function (datos) {
            if (datos.error) {
                growl.info('Ocurrió un error');
            } else {
                $scope.chequesEntregados = datos.data;
                $scope.respaldoCheques = datos.data.chequesEntregados;
            }
        });
    }

    if ($location.path() == '/listaCheques') { //Vista principal
        $scope.$watch('banco', function (val) {
            traeListaCheques(val)
        }, true);
    }

    $scope.filtrarCheques = function (filtro) {
        $scope.nuevoArreglo = [];
        if (filtro == 1) {
            $scope.respaldoCheques.forEach(cheque => { //Cobrados
                if (cheque.fechaCobro != null) {
                    $scope.nuevoArreglo.push(cheque);
                }
            });
            if ($scope.nuevoArreglo.length == 0) {
                growl.info('No hay cheques cobrados');
                traeListaCheques($scope.banco);
            }
        } else if (filtro == 2) {
            $scope.respaldoCheques.forEach(cheque => { //No cobrados
                if (cheque.fechaCobro == null) {
                    $scope.nuevoArreglo.push(cheque);
                }
            });
            if ($scope.nuevoArreglo.length == 0) {
                growl.info('No hay cheques sin cobrar');
                traeListaCheques($scope.banco);
            }
        } else if (filtro == 3) { // Todos
            traeListaCheques($scope.banco);
        }
        $scope.chequesEntregados.chequesEntregados = $scope.nuevoArreglo;
    }

}]);