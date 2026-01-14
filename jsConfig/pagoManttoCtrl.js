form.controller('pagoManttoCtrl', ['$scope', '$http', '$routeParams', 'growl', function ($scope, $http, $routeParams, growl) {

    //=========================P A R A M E T R O===========================
    $scope.mantenimientoId = $routeParams.idMantenimiento;
    $scope.areaId = $routeParams.idArea;
    //    ----------------------------------------------------------------

    $http.get('utilerias/php/dameNombrePersonal.php?idPersonal=2').success(function (response) {
        if(response.hasOwnProperty('error')) {
            if(response.error){
                growl.error(response.message);
            } else {
                $scope.gerente = response.personal;
            }
        } else {
            console.error(response);
        }
    })

    $scope.menuPagoMantto = new Array();
    $scope.menuMantenimientos = new Array();

    $scope.nota = {};

    if ($scope.mantenimientoId > 0) {
        $http.post('controlMantenimiento/php/traeDatosParaPago.php?idMantenimiento=' + $scope.mantenimientoId).success(function (data) {
            $scope.nota = data;
            $scope.totalPago = data.totalPago;
        });
    } else {
        $http.post('personalOaxacaMiel/php/listaAreas.php').success(function (data) {
            $scope.menuPagoMantto = data;
        });
    }
    ;

    if ($scope.areaId > 0) {
        $http.post('controlMantenimiento/php/encabezadoCronograma.php?idArea=' + $scope.areaId).success(function (info) {
            $scope.areaDelMantto = info;
        });
        $http.post('controlMantenimiento/php/traeMantenimientosPorArea.php?idArea=' + $scope.areaId).success(function (info) {
            $scope.menuMantenimientos = info;
        });
    }
    ;

    $scope.guardarTotalPago = function () {
        console.log($scope.totalPago);
        if ($scope.totalPago == 0) {
            growl.error("Se requiere un total a pagar");
        } else {
            $http.post('controlMantenimiento/php/guardarTotalPago.php?totalPago=' + $scope.totalPago + "&idMantenimiento=" + $scope.nota.idMantenimiento).success(function (info) {
                $http.post('controlMantenimiento/php/traeDatosParaPago.php?idMantenimiento=' + $scope.mantenimientoId).success(function (data) {
                    $scope.nota = data;
                    $scope.totalPago = data.totalPago;
                });
                swal("Exito!", "Total guardado", "success");
            });
        }
    };

}]);


