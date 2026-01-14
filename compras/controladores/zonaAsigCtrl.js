form.controller('zonaAsigCtrl', ['$scope', '$routeParams', '$http', function ($scope, $routeParams, $http) {


        var codigo = $routeParams.idcomprador;



        $scope.zComprador = {};
        $http.get('compras/php/comprador/getZona.php?idcomprador=' + codigo).success(function (zonsComp) {
            if (zonsComp.err !== undefined) {
                window.location = "#/comprador";
            }
            $scope.zComprador = zonsComp;
            //console.log($scope.zComprador);
        });

        $scope.nombre = {};
        $http.get('compras/php/comprador/getNombre.php?idcomprador=' + codigo).success(function (zonsComp2) {
            $scope.nombre = zonsComp2;
            console.log($scope.nombre);
        });

        $scope.modalProveedores = function () {
            $("#modalProveedores").modal();
        };

        $scope.mostrarProveedores = function () {
            $scope.nombreP = {};
            $http.get('compras/php/comprador/getNombresDeProveedores.php?idcomprador=' + codigo).success(function (data) {
                $scope.nombreP = data;
                console.log($scope.nombreP);
            });
        };


    }]);