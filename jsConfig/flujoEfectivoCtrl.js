form.controller('flujoEfectivoCtrl', function ($scope, $http, growl) {


    $scope.flujoEfectivo = null;
    $scope.opcionMes = null;
    $scope.opcionTipoReporte = '1';
    $scope.cargandoDatos = false;
    // Ver cambios en la seleción de los meses

    $scope.$watch('opcionMes', function (mes) {
        if ($scope.opcionTipoReporte == '2' && mes) {
            obtenerFlujoEfectivo(mes);
        } else {
            if ($scope.opcionTipoReporte == '3' && mes && $scope.opcionMesDos) {
                obtenerFlujoEfectivo($scope.opcionMes, $scope.opcionMesDos);
            }
        }
    });

    $scope.$watch('opcionMesDos', function (mes) {
        if ($scope.opcionTipoReporte == '3' && mes) {
            obtenerFlujoEfectivo($scope.opcionMes, $scope.opcionMesDos);
        }
    });

    $scope.$watch('opcionTipoReporte', function (tipo) {
        if (tipo == '1') {
            $scope.opcionMes = null;
            obtenerFlujoEfectivo();
        }
    });

    $http.get('almacen/php/consultaComboMeses.php').success(function (data) {
        $scope.meses = data;
    });

    function obtenerFlujoEfectivo(mes = false, mes2 = false) {
        if (mes && mes2) {
            if (mes2 < mes) {
                growl.info('Especifique correctamente el rango de meses');
                return;
            }
        }

        
        $scope.flujoEfectivo = null;
        $scope.cargandoDatos = true;
        var url = 'informesFinancieros/php/obtenerFlujoEfectivo.php';
        if (mes) {
            url += '?mes=' + mes;
        }
        if (mes2) {
            url += '&mesFinal=' + mes2;
        }
        $http.post(url).success(function (data) {
            $scope.cargandoDatos = false;
            if (typeof (data) == 'object') {
                if (data.error) {
                    swal('', data.message, 'info');
                } else {
                    $scope.flujoEfectivo = data;
                }
            } else {
                growl.error('Error al obtener los datos');
                console.info(data);
            }
        })
    }

    // Descarga el Excel de FLujo de Efectivo
    $scope.descargarInformeFlujoEfectivo = function () {
        var url = 'reportes/informesFinancieros/xlsFlujoDeEfectivo.php?descargar'
        if ($scope.opcionMes) {
            url += '&mes=' + $scope.opcionMes;
        }
        return window.location.href = url;
    }

    // Abre el PDF de flujo de efectivo
    $scope.pdfFlujoDeEfectivo = function () {
        var url = 'reportes/informesFinancieros/pdfFlujoDeEfectivo.php?descargar'
        if ($scope.opcionMes) {
            url += '&mes=' + $scope.opcionMes;
        }
        window.open(url);
    }

});