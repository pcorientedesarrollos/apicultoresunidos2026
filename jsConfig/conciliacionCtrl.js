form.controller('conciliacionCtrl', ['$scope', '$http', function ($scope, $http) {

        //=================================================================
        //      TRAE INFORMACION TOTAL DE TAMBORES ENTRADA POR COLOR
        //=================================================================
        $scope.tamboresEntrada = {};
        $http.get('almacen/php/traeTotalTamboresPorColorEntrada.php').success(function (data) {
            $scope.tamboresEntrada = data;
        });
        
        //=================================================================
        //      TRAE INFORMACION TOTAL DE TAMBORES SALIDA POR COLOR
        //=================================================================
        $scope.tamboresSalida = {};
        $http.get('almacen/php/traeTotalTamboresPorColorSalida.php').success(function (datos) {
            $scope.tamboresSalida = datos;
        });


    }]);