form.config(function ($routeProvider) {
    $routeProvider.when('/foliosMS', {
        templateUrl: 'calidad/foliosDeMielSobrante.html',
        controller: 'foliosMielSobranteCtrl'
    })
});

form.controller('foliosMielSobranteCtrl', function ($scope, busqueda, $http, $routeParams, growl, $rootScope, $location) {

    $scope.folios = new Array();

    $http.post("almacenSobrantes/php/listaTiposDeSobrantes.php").success(function (info) {
        $scope.listaSobrantes = info;
    });

    $scope.buscarReporte = function (datos) {
        $scope.folios = null;
        $http.post('calidad/php/foliosDeMielSobranteVSAnterior.php', datos).success(function (data) {
            if (data.error) {
                growl.error(data.message);
            }
            $scope.folios = data.folios;
        });
    };

    $scope.xlsDescarga = function (datos) {
        if (datos == undefined) {
            growl.info('Elija los parámetros');
        } else if (!datos.miel || !datos.sobrante) {
            growl.info('Elija los parámetros');
        } else {
            return window.location.href = "reportes/calidad/xlsFoliosSobrantesVsIniciales.php?miel=" + datos.miel + '&sobrante=' + datos.sobrante;
        }
    };

});