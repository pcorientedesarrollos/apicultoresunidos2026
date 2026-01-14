form.controller('acumuladoGastosCtrl', function ($scope, $http, growl, $location) {

    $scope.gastosAup = new Array();
    $scope.opcionMes = null;
    $scope.opcionMes1 = null;
    $scope.opcionTipoReporte = '1';
    $scope.cargandoDatos = false;

    $scope.$watch('opcionTipoReporte', function (tipo) {
        $scope.opcionMes = null;
        $scope.opcionMes1 = null;
        if (tipo == '1') {
            obtenerAcumulado();
        }
    });

    $scope.$watch('opcionMes', function (mes) {
        if ($scope.opcionTipoReporte == '2') {
            if (mes) {
                obtenerAcumulado(mes);
            }
        } else if ($scope.opcionTipoReporte == '3') {
            if ($scope.opcionMes1 !== null) {
                obtenerAcumulado(mes, $scope.opcionMes1);
            }
        }
    });

    $scope.$watch('opcionMes1', function (mes) {
        if ($scope.opcionTipoReporte == '3') {
            if (mes && $scope.opcionMes !== null) {
                obtenerAcumulado($scope.opcionMes, mes);
            }
        }
    });

    $http.get('almacen/php/consultaComboMeses.php').success(function (data) {
        $scope.meses = data;
    });

    function obtenerAcumulado(mes = false, mes1 = false) {
        $scope.cargandoDatos = true;
        var url = 'informesFinancieros/php/acumuladoDeGastos.php';
        if (mes && mes1) {
            url += '?mes=' + mes + '&mes1=' + mes1;
        } else if (mes) {
            url += '?mes=' + mes;
        }
        $http.post(url).success(function (data) {
            console.log(data);
            $scope.cargandoDatos = false;
            $scope.gastosAup = data;
        });
    }

    $scope.abrirAcumuladoXls = function () {
        var url = 'reportes/informesFinancieros/xlsAcumuladoDeGastos.php?acumulado=0'
        if ($scope.opcionMes && $scope.opcionMes1) {
            url += '&mes=' + $scope.opcionMes + '&mes1=' + $scope.opcionMes1;
        } else if ($scope.opcionMes) {
            url += '&mes=' + $scope.opcionMes;
        }
        return window.location.href = url;
    }

    $scope.abrirAcumuladoPdf = function () {
        var url = 'reportes/informesFinancieros/pdfAcumuladoDeGastos.php?acumulado=0'
        if ($scope.opcionMes && $scope.opcionMes1) {
            url += '&mes=' + $scope.opcionMes + '&mes1=' + $scope.opcionMes1;
        } else if ($scope.opcionMes) {
            url += '&mes=' + $scope.opcionMes;
        }
        window.open(url);
    }

});