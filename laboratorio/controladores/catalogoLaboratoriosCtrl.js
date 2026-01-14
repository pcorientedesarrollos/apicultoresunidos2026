form.config(function ($routeProvider) {
    $routeProvider.when('/laboratoriosExternos', {
        templateUrl: 'laboratorio/catalogoLaboratorios.html',
        controller: 'catalogoLaboratorioCtrl'
    })
});

form.controller('catalogoLaboratorioCtrl', function ($scope, $http, $routeParams, growl, $location) {

    function traeLaboratorios() {
        $http.get('laboratorio/php/listaLaboratorios.php').success(function (data) {
            $scope.laboratorios = data;
        });
    }

    traeLaboratorios();

    $scope.abrirModalNuevoLaboratorio = function (indice = undefined) {
        if (indice >= 0 && indice != undefined) {
            $scope.nuevoLaboratorio = Object.assign({}, $scope.laboratorios[indice]);
        } else {
            $scope.nuevoLaboratorio = {};
        }
        $('#modalNuevoLaboratorio').modal();
    }

    $scope.guardarLaboratorio = function () {
        if ($scope.nuevoLaboratorio.nombre && $scope.nuevoLaboratorio != '') {
            $http.post('laboratorio/php/guardarLaboratorioExterno.php', $scope.nuevoLaboratorio).success(function (data) {
                if (data.hasOwnProperty('error')) {
                    if (data.error) {
                        swal('Error', data.message, 'error');
                    } else {
                        swal('Éxito', data.message, 'success');
                        $('#modalNuevoLaboratorio').modal('hide');
                        traeLaboratorios();
                    }
                } else {
                    growl.error('Error');
                    console.error(data);
                }
            });
        } else {
            growl.info('Ingresa el nombre del laboratorio');
        }
    }

});