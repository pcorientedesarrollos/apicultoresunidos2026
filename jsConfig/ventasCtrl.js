form.controller('ventasCtrl', ['$scope', '$http', '$routeParams', 'growl', function ($scope, $http, $routeParams, growl) {

    $scope.venta = {};
    $scope.datosventa = [];
    $scope.totalVenta = 0;

    $scope.guardarVenta = function () {
        $http.post('controlAdministrativo/php/nuevaVenta.php',[$scope.datosventa, $scope.fecha]).success(function (data) {
            console.log(data);
        });
    };

    $scope.agregarVenta = function () {

        if ($scope.fecha == "" || $scope.fecha == undefined) {
            growl.error("Se requiere una fecha");
        } else {

            switch ($scope.venta.movimiento) {
                case "2":
                    $scope.venta.movimiento = "Venta miel";
                    break;
                case "3":
                    $scope.venta.movimiento = "Maquila de cera";
                    break;
                case "4":
                    $scope.venta.movimiento = "Venta cera";
                    break;
                case "5":
                    $scope.venta.movimiento = "Venta cajas de cera";
                    break;
                case "6":
                    $scope.venta.movimiento = "Venta polen";
                    break;
                case "7":
                    $scope.venta.movimiento = "Venta chatarra";
                    break;
                case "8":
                    $scope.venta.movimiento = "Venta jalea real";
                    break;
                case "9":
                    $scope.venta.movimiento = "Otros";
                    break;
                default:
                    $scope.venta.movimiento = "Otros";
            }

            $scope.datosventa.push($scope.venta);
            $scope.venta = {};
            growl.success("Nuevo movimiento agregado");
        }
    };

    $scope.calcularImporte = function () {
        if (!isNaN(parseInt($scope.venta.kg)) && !isNaN(parseInt($scope.venta.precio))) {
            $scope.venta.importe = parseInt($scope.venta.kg) * parseInt($scope.venta.precio);
        } else {
            $scope.venta.importe = 0;
        }
    };

    $scope.$watch('datosventa', function () {
        $scope.totalVenta = 0;
        $scope.datosventa.forEach(function (element) {
            $scope.totalVenta = $scope.totalVenta + element.importe;
        }, this);
    }, true)
}]);


