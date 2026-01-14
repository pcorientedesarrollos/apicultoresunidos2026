form.controller('empresasCtrl', ['$scope', '$http', '$routeParams', 'growl', '$q', '$rootScope', 'auth', function ($scope, $http, $routeParams, growl, $q, $rootScope, auth) {

    // =========================P A R A M E T R O S===========================
    $scope.empresas = new Array();

    //==========================================================================
    // TRAE TODAS LAS EMPRESAS
    //==========================================================================
    function traerEmpresas() {
        $http.post("empresas/php/traeEmpresas.php").success(function (info) {
            if (info.hasOwnProperty('error')) {
                if (info.error) {
                    swal('Error', info.message, 'error');
                } else {
                    $scope.empresas = info.empresas;
                }
            } else {
                growl.error('Error');
                console.error(info);
            }
        });
    }

    traerEmpresas();


    $scope.modalNuevaEmpresa = function (empresa = false) {
        console.log(empresa);
        if (empresa) {
            $scope.nuevaEmpresa = empresa;
        } else {
            $scope.nuevaEmpresa = null;
        }
        $('#modalNuevaEmpresa').modal();
    }

    $scope.guardarNuevaEmpresa = function () {
        $http.post("empresas/php/guardarNuevaEmpresa.php", $scope.nuevaEmpresa).success(function (info) {
            if (info.hasOwnProperty('error')) {
                if (info.error) {
                    swal('Error', info.message, 'error');
                } else {
                    $('#modalNuevaEmpresa').modal('hide');
                    swal('Hecho', info.message, 'success');
                    traerEmpresas();
                }
            } else {
                growl.error('Error');
                console.error(info);
            }
        });
    }
}]);