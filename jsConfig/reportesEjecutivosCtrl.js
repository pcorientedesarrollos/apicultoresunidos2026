form.controller('reportesEjecutivosCtrl', ['$scope', '$routeParams', '$http', 'growl', '$filter', function ($scope, $routeParams, $http, growl, $filter) {

    // Set the currency month to $scope.idMes
    $scope.idMes = new Date().toLocaleDateString().split('/')[1];

    // Meses para el combo
    $http.get('almacen/php/consultaComboMeses.php').success(function (data) {
        $scope.meses = data;
    });

    // Informe ejecutivo
    $scope.informeEjecutivo = function () {
        $('#modalImprimirReporteMensual').modal();
    };

    $scope.imprimirInformeEjecutivo = function (optImprimirReporteMensual) {
        if (optImprimirReporteMensual) {
            switch (optImprimirReporteMensual.tipo) {
                case '1':
                    if (optImprimirReporteMensual.fechaInicial && optImprimirReporteMensual.fechaFinal) {
                        window.open('reportes/cajaChica/pdfReporteMensualCC.php?inicial=' + optImprimirReporteMensual.fechaInicial + '&final=' + optImprimirReporteMensual.fechaFinal);
                        $('#modalImprimirReporteMensual').modal('hide');
                    } else {
                        growl.info('Selecciona las fechas requeridas');
                    }
                    break;
                case '2':
                    window.open('reportes/cajaChica/pdfReporteMensualCC.php?idMes=' + $scope.idMes);
                    $('#modalImprimirReporteMensual').modal('hide');
                    break;
                default:
                    growl.error('Opción desconocida');
                    break;
            }

        } else {
            growl.info('Selecciona una opción');
        }
    };


    /*ARQUEO DE CAJA CHICA*/
    function obtenerSemanas() {
        $http.get('arqueoDeCaja/php/obtenerArqueos.php').success(function (data) {
            $scope.listaArqueos = data.data;
        });
    };

    $scope.arqueoDeCaja = function () {
        $('#modalImprimirArqueo').modal();
        obtenerSemanas();
    };

    $scope.imprimirArqueoCaja = function (idArqueo) {
        window.open('reportes/cajaChica/pdfArqueoCajaChica.php?idArqueo=' + idArqueo);
        $('#modalImprimirArqueo').modal('hide');
    }

}]);