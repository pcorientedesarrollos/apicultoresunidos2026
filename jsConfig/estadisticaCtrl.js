form.controller('estadisticaCtrl', ['$scope', '$http', 'growl', function ($scope, $http, growl) {

    $scope.estadisticaDetalle = new Array();
    $scope.estadisticas = new Array();

    //=================================================================
    //      FUNCION PARA AGREGAR
    //=================================================================
    $scope.agregarEstadistica = function () {
        if ($scope.periodo == undefined) {
            growl.info('Seleccione un periodo de fechas');
        } else {
            if ($scope.periodo.fechaUno == undefined || $scope.periodo.fechaDos == undefined) {
                growl.info('Seleccione ambas fechas');
            } else if ($scope.periodo.fechaUno > $scope.periodo.fechaDos) {
                growl.info('Seleccione fechas válidas');
            } else {
                $http.post("compras/php/estadistica/getEstadisticas.php", $scope.periodo).success(function (respuesta) {
                    if (!respuesta.error) {
                        $scope.estadisticaDetalle = {};
                        $scope.periodo.fechaUno = null;
                        $scope.periodo.fechaDos = null;
                        $scope.estadisticaDetalle = respuesta.data;
                        $scope.estadisticas.push($scope.estadisticaDetalle);
                    }
                });
            }
        }
    };

    //=================================================================
    //      FUNCION PARA ELIMINAR
    //=================================================================
    $scope.eliminarEstadistica = function (indice) {
        $scope.estadisticas.splice(indice, 1);
        growl.warning("Registro eliminado");
    };

}]);