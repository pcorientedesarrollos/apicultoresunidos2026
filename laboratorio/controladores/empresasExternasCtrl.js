form.config(function ($routeProvider) {
    $routeProvider.when('/empresasExternas', {
        templateUrl: 'laboratorio/catalogoEmpresasExternas.html',
        controller: 'empresasExternasCtrl'
    }).when('/capturaExternos/:idExterno', {
        templateUrl: 'laboratorio/capturaEmpresasExternas.html',
        controller: 'empresasExternasCtrl'
    })
});

form.controller('empresasExternasCtrl', function ($scope, $http, $routeParams, growl, $location) {

    $scope.externos = new Array();
    $scope.idExterno = $routeParams.idExterno;
    $scope.idAnalisis = $routeParams.idAnalisis;
    $scope.guardandoDatos = false;
    $scope.cargandoDatos = false;
    $scope.nuevoExterno = {};

    function traeCatalogoEmpresas() {
        $scope.externos = null;
        $scope.cargandoDatos = true;
        $http.post('laboratorio/php/catalogoEmpresasExternas.php').success(function (data) {
            if (data.error) {
                growl.error(data.message);
            }
            $scope.externos = data.externos;
            $scope.cargandoDatos = false;
        });
    }

    if ($location.path() == '/empresasExternas' || $scope.idAnalisis) {
        traeCatalogoEmpresas();
    } else {
        $http.get('compras/php/localidad/listaLocalidades.php').success(function (data) {
            $scope.listaLocalidades = data;
        });

        if ($scope.idExterno > 0) {
            $scope.cargandoDatos = true;
            $scope.nuevoExterno = null;
            $http.post('laboratorio/php/traeInformacionExterno.php', $scope.idExterno)
                .success(function (data) {
                    $scope.cargandoDatos = false;
                    if (typeof (data) == 'object' && data.hasOwnProperty('error')) {
                        if (data.error) {
                            swal('', data.message, 'warning');
                        } else {
                            $scope.nuevoExterno = data.data;
                        }
                    } else {
                        growl.error('Error');
                        console.error(data);
                    }
                });
        }

        $scope.guardarEmpresaExterna = function () {
            $scope.guardandoDatos = true;
            if ($scope.nuevoExterno.nombre && $scope.nuevoExterno != '') {
                $http.post('laboratorio/php/guardarEmpresaExterna.php', $scope.nuevoExterno).success(function (data) {
                    $scope.guardandoDatos = false;
                    if (data.hasOwnProperty('error')) {
                        if (data.error) {
                            swal('Error', data.message, 'error');
                        } else {
                            swal('Éxito', data.message, 'success');
                            return window.location.href = "#/empresasExternas";
                        }
                    } else {
                        growl.error('Error');
                        console.error(data);
                    }
                });
            } else {
                $scope.guardandoDatos = false;
                growl.info('Ingresa el nombre de la empresa');
            }
        };
    }

});