form.controller('trazabilidadEntradaCtrl', ['$scope', '$http', '$routeParams', 'growl', function ($scope, $http, $routeParams, growl) {

    //========================= P A R A M E T R O ===========================
    $scope.parametroEntrada = $routeParams.idLoteInterno;
    //-----------------------------------------------------------------------

    $scope.comboEntrada = 0;
    $scope.trazabilidadEntrada = new Array();
    $scope.opcionCosechaC = 0;
    //========================= COMBO LOTES ===========================

    $scope.$watch('opcionCosechaC', function (val) {
        $scope.listaLotes = [];
        $http.get('produccion/php/listaLotes.php?tipoMiel=' + val).success(function (datas) {
            $scope.listaLotes = datas.infoLote;
            //**ANA 2025 (Mensaje en caso de que no existan lotes) */
            if ($scope.listaLotes.length == 0) {
                growl.info('No existen lotes registrados');
            }
            //**ANA 2025 */
        });
    });
    //----------------------------------------------------------------

    $scope.concentradoEntrada = function () {
        $http.get('trazabilidad/php/traeTrazabilidadEntrada.php?tipo=' + $scope.opcionCosechaC + '&idLoteInterno=' + $scope.comboEntrada).success(function (data) {
            console.log(data);
            $scope.trazabilidadEntrada = data.trazabilidadEntrada;
            $scope.totalEntrada = data.kilosTotales;
        });
    };
    $scope.pdfTramzabilidadEntrada = function () {
        if ($scope.opcionCosechaC == 0) {
            growl.info('Seleccione un tipo de miel');
        } else if ($scope.comboEntrada == '0' || $scope.comboEntrada == null) {
            growl.info('Seleccione un lote');
        } else {
            window.open('reportes/trazabilidad/pdfTrazabilidadEntrada.php?tipo=' + $scope.opcionCosechaC + '&idLoteInterno=' + $scope.comboEntrada, '_blank');
        }
    };
    $scope.xlsTranzabilidadEntrada = function () {
        if ($scope.opcionCosechaC == 0) {
            growl.info('Seleccione un tipo de miel');
        } else if ($scope.comboEntrada == '0' || $scope.comboEntrada == null) {
            growl.info('Seleccione un lote');
        } else {
            return window.location.href = 'reportes/trazabilidad/xlsTranzabilidadEntrada.php?tipo=' + $scope.opcionCosechaC + '&idLoteInterno=' + $scope.comboEntrada;
        }
    };
}]);