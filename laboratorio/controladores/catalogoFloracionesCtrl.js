form.config(function ($routeProvider) {
    $routeProvider.when('/floraciones', {
        templateUrl: 'laboratorio/catalogoFloraciones.html',
        controller: 'catalogoFloracionesCtrl'
    })
});

form.controller('catalogoFloracionesCtrl', function ($scope, $http, $routeParams, growl, $location) {

    function traeFloracionesDisponibles() {
        $http.get('laboratorio/php/listaFloraciones.php').success(function (data) {
            $scope.floraciones = data;
        });
    }

    traeFloracionesDisponibles();

    $scope.abrirModalNuevaFloracion = function (indice = undefined) {
        if (indice >= 0 && indice != undefined) {
            $scope.nuevaFloracion = Object.assign({}, $scope.floraciones[indice]);
        } else {
            $scope.nuevaFloracion = {};
        }
        $('#modalNuevaFloracion').modal();
    }

    $scope.guardarFloracion = function () {
        if ($scope.nuevaFloracion.floracion && $scope.nuevaFloracion != '') {
            $http.post('laboratorio/php/guardarFloracion.php', $scope.nuevaFloracion).success(function (data) {
                console.log(data);
                if (data.hasOwnProperty('error')) {
                    if (data.error) {
                        swal('Error', data.message, 'error');
                    } else {
                        swal('Éxito', data.message, 'success');
                        $('#modalNuevaFloracion').modal('hide');
                        traeFloracionesDisponibles();
                    }
                } else {
                    growl.error('Error');
                    console.error(data);
                }
            });
        } else {
            growl.info('Ingresa el nombre de la floración');
        }
    }

});