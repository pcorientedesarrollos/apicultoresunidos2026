form.controller('inventarioTamboresCtrl', ['$scope', '$http', 'growl', '$routeParams', function ($scope, $http, growl, $routeParams) {

//=========================P A R A M E T R O===========================
        $scope.parametroZona = $routeParams.zona;
//    ----------------------------------------------------------------

        $scope.verLasZonas = new Array();
        $scope.foliosPorZona = new Array();


        if ($scope.parametroZona) {

            $http.post("produccion/php/traeFoliosPorZona.php?zona=" + $scope.parametroZona).success(function (data) {
                $scope.total = data.total;
                $scope.netoTotal = data.netoTotal;
                $scope.foliosPorZona = data.folios;
                console.log(data);
            });
        } else {
            $http.post('produccion/php/menuDeZonas.php').success(function (data) {
                $scope.verLasZonas = data;
            });
        }

        $scope.pdfInventario = function () {
            window.open('reportes/produccion/pdfInventarioTambores.php?zona=' + $scope.parametroZona, '_blank');
        };

        $scope.xlsInventario = function () {
            return window.location.href = "reportes/produccion/xlsInventarioTambores.php?zona=" + $scope.parametroZona;
        };

    }]);