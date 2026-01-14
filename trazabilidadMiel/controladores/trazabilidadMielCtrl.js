form.config(function ($routeProvider) {
    $routeProvider.when('/trazabilidadMiel', {
        templateUrl: 'trazabilidadMiel/trazabilidad.html',
        controller: 'trazabilidadMielCtrl'
    })
});

form.controller('trazabilidadMielCtrl', function ($scope, $http, $routeParams, growl, busqueda, $location, $rootScope) {

    $scope.informacionLotes = new Array();

    $http.get('trazabilidadMiel/php/obtenerTrazabilidad.php?miel=1&idLoteInterno=1').success(function (data) {
        console.log(data);
        $scope.datosObtenidos = data.data;
        $scope.fechaEntrada = $scope.datosObtenidos.fechaEntrada.fechaEntrada;
        $scope.procesoEnvasado = $scope.datosObtenidos.procesoEnvasado;
        $scope.fechaPT = $scope.datosObtenidos.fechaPT.fechaCalidad;
        $scope.fechaSalida = $scope.datosObtenidos.fechaSalida.fechaSalida;
        $scope.fechaEmbarque = $scope.datosObtenidos.fechaEmbarque;
        $scope.localidades = $scope.datosObtenidos.localidades;
    });

    $scope.imprimirCertificadoCalidad = function (miel, lote) {
        window.open('reportes/calidad/pdfCertificadoCalidad.php?miel=' + miel + '&idLote=' + lote, '_blank');
    };

});