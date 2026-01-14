form.controller('polizaChequeCtrl', ['$scope', '$http', '$routeParams', 'growl', '$location', function ($scope, $http, $routeParams, growl, $location) {
    $scope.poliza = {};
    $scope.showPDF = false;

    function traerListaPersonas(idTipoPersona) {
        $http.post('controlAdministrativo/polizaCheque/php/traerNombres.php', idTipoPersona).success(function (data) {
            $scope.personas = data;
        });
    };

    $scope.traeLasCuentasDelBanco = function (idBanco) {
        $http.get('controlAdministrativo/php/infoCuentasAnidadas.php?idBanco=' + idBanco).success(function (data) {
            $scope.lstQentas = data;
        });
    };

    $scope.validarPoliza = function () {
        var _response = true;

        _response = $scope.poliza.fecha ? true : false;
        _response = $scope.poliza.persona ? true : false;
        _response = $scope.poliza.nombre ? true : false;
        _response = $scope.poliza.cantidad ? true : false;
        _response = $scope.poliza.idBanco ? true : false;
        _response = $scope.poliza.bancoCuenta ? true : false;
        _response = $scope.poliza.concepto ? true : false;
        _response = $scope.poliza.folioCheque ? true : false;

        return _response;
    };

    $scope.verPoliza = function () {
        if ($scope.validarPoliza()) {
            $http.post('controlAdministrativo/polizaCheque/php/guardarPoliza.php', $scope.poliza).success(function (data) {
                if (!data.error) {
                    $scope.idPolizaCheque = data.idPoliza;
                    $scope.urlAPoliza = data.url;
                    $scope.poliza = {};
                    $scope.showPDF = true;
                    return;
                } else {
                    growl.error(data.message);
                }
            });
        } else {
            growl.error('Verifica todos los campos');
            return;
        }

    };

    $scope.cancelarPoliza = function () {
        swal({
            title: '',
            text: '¿Deseas cancelar esta póliza?',
            showCancelButton: true,
            confirmButtonText: 'Si',
            cancelButtonText: 'No',
            closeOnConfirm: true
        }, function (confirm) {
            if (confirm) {

                var date = new Date();
                var _mes = date.getMonth() + 1;
                var _fecha = date.getFullYear() + '-' + _mes + '-' + date.getDate();

                $http.post('controlAdministrativo/polizaCheque/php/cancelarPoliza.php', { idPoliza: $scope.idPolizaCheque, fecha: _fecha }).success(function (data) {
                    swal('', data.message, data.swal);
                    if (!data.error) {
                        $scope.showPDF = false;
                        $scope.urlAPoliza = '';
                        $scope.idPolizaCheque = 0;
                        $scope.poliza = {};
                    }
                });
            }
        });
    };

    $scope.AceptarPoliza = function () {
        swal('', 'Se ha guardado la póliza, asegúrate de imprimirla', 'success');
        $scope.showPDF = false;
        $scope.idPolizaCheque = 0;
        $scope.poliza = {};
        window.open($scope.urlAPoliza);
        $scope.urlAPoliza = '';
    };

    if ($location.path() === '/polizaCheque') {
        if (!$scope.lstBancos) {
            $http.get('controlAdministrativo/php/listaDeBancos.php').success(function (datas) {
                $scope.lstBancos = datas;
            });
        }

        $scope.$watch('poliza.persona', function (idTipoPersona) {
            if (idTipoPersona !== undefined) {
                traerListaPersonas(idTipoPersona);
            }
        });
    }
}]);