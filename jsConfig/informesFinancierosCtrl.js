form.controller('informesFinancierosCtrl', ['$scope', '$http', 'growl', '$routeParams', '$sce', function ($scope, $http, growl, $routeParams, $sce) {

    $scope.tipoInforme = null;
    $scope.opcionMes = null;
    $scope.opcionMes1 = null;
    $scope.cargandoDatos = false;

    $scope.$watch('tipoInforme', function (tipo) {
        if ($scope.opcionTipoReporte) {
            switch ($scope.opcionTipoReporte) {
                case '1':
                    obtenerAcumulado();
                    break;
                case '2':
                    obtenerAcumulado($scope.opcionMes);
                    break;
                case '3':
                    obtenerAcumulado($scope.opcionMes, $scope.opcionMes1);
                    break;
            }
        }
    });

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

    function traeListaInformesFinancieros() {
        $http.get('catalogos/php/traeListaInformesFinancieros.php').success(function (data) {
            if (data.hasOwnProperty('error')) {
                if (data.error) {
                    swal('Error', data.message, 'error');
                } else {
                    $scope.informesFinancieros = data.informesFinancieros;
                }
            } else {
                growl.console.error(('Error'));
                console.error(data);
            }
        });
    }
    traeListaInformesFinancieros();

    function obtenerAcumulado(mes = false, mes1 = false, xls = false) {
        if (!xls) {
            $scope.cargandoDatos = true;
        } else {
            $scope.cargandoXls = true;
        }
        $scope.arreglo = [
            $scope.tipoInforme,
        ];
        var url = 'informesFinancieros/php/traerInformeFinanciero.php';
        if (mes && mes1) {
            url += '?meses&idMes=' + mes1;
            $scope.arreglo.push(mes);
            $scope.arreglo.push(mes1);
        } else if (mes) {
            url += '?mes&idMes=' + mes;
            $scope.arreglo.push(mes);
        } else {
            url += '?acumulado';
        }

        if (xls) {
            url += '&xls';
        }
            url += '&informeFinanciero';

        $http.post(url, $scope.arreglo).success(function (data) {
            console.log(data);
            $scope.cargandoDatos = false;
            $scope.cargandoXls = false;
            if (data.hasOwnProperty('xls') && data.xls && xls) {
                var url = 'reportes/informesFinancieros/xlsInformeFinanciero.php'
                return window.location.href = url;
            } else {
                // Aquí se tiene que limpiar el HTML que se recive desde el PHP
                // Y guardarlo en alguna variable
                $scope.informeFinancieroEstructura = $sce.trustAsHtml(data.informeFinancieroEstructura);

                // Ya no se va a usar el siguiente scope, todo viene en la estructura
                // $scope.gastosAup = data;
            }
        });
    }

    $scope.descargarXls = function (mes = false, meses = false) {
        // Validar las propiedades
        if ($scope.tipoInforme && $scope.opcionTipoReporte) {
            obtenerAcumulado(mes, meses, true);
        } else {
            growl.info('Seleccione las opciones necesarias');
        }
    }

    // guardar valores de balance general

    $scope.guardarValoresBalanceGeneral = function () {
        var listaEditables = document.getElementsByClassName('editables');

        // Si no hay ningún campo editable, no debe guardar porque lo dejaría todo vacío
        if (!listaEditables.length) return;

        var arregloValores = [];

        for (var i = 0; i < listaEditables.length; i++) {
            var element = listaEditables[i];

            var cantidad = 0;
            var idCampo = element.id;

            var valorDelCampo = $('#' + element.id).html().split(',').join('').match(/[\d\.]+/);
            if (valorDelCampo) {
                cantidad = parseFloat(valorDelCampo[0]);
            }

            arregloValores.push({ idCampo: idCampo, cantidad: cantidad });

        }


        $http.post('informesFinancieros/php/guardarValoresBalanceGeneral.php', arregloValores).success(function (data) {
            if (data.hasOwnProperty('error')) {
                if (data.error) {
                    swal('Error', data.message, 'error');
                } else {
                    obtenerAcumulado($scope.opcionMes, $scope.opcionMes1);
                    swal('Listo', 'Se guardó los valores', 'success');
                }
            } else {
                growl.console.error(('Error'));
                console.error(data);
            }
        });

    }
}]);
