form.controller('conciliacionMonetariaCtrl', ['$scope', '$http', '$routeParams', 'growl', '$rootScope', '$location', function ($scope, $http, $routeParams, growl, $rootScope, $location) {

    $scope.mesesLst = {};
    $scope.inventarioDeMiel = new Array();

    if ($location.path() == '/conciliacion') {
        $http.get('almacen/php/consultaComboMeses.php').success(function (data) {
            $scope.mesesLst = data;
            console.log($scope.mesesLst);
        });
    }
    $scope.verMes = function (mes) {
        $http.post('controlAdministrativo/php/inventarioComprasMiel.php?idMes=' + mes).success(function (data) {
            if (!data.error) {
                console.log(data);
                $scope.existenciaPasada = data.existenciaPasada;
                $scope.importeAcumuladoPasado = data.importeAcumuladoPasado;
                $scope.totalEntradas = data.encabezado.totalEntradas;
                $scope.totalSalidas = data.encabezado.totalSalidas;
                $scope.totalInventario = data.encabezado.totalInventario;    
                $scope.totalImportesAcumulados = data.encabezado.totalImportesAcumulados;                
                $scope.inventarioDeMiel = data.inventarioMiel;
                // console.log($scope.inventarioDeMiel);
            } else {
                growl.error(data.message);
            }
        })
    }

}]);