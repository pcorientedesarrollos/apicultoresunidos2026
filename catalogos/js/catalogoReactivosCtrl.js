form.config(function ($routeProvider) {
    $routeProvider.when('/catalogoReactivos', {
        templateUrl: 'catalogos/catalogoReactivos.html',
        controller: 'catalogoReactivos'
    })
});
form.controller('catalogoReactivos', function ($scope, $http, growl) {

    function traeReactivos() {
        $http.get('catalogos/php/traeReactivos.php').success(function (data) {
            if (data.hasOwnProperty('error')) {
                if (data.error) {
                    swal('Error', data.message, 'error');
                } else {
                    $scope.reactivos = data.reactivos;
                }
            } else {
                growl.console.error(('Error'));
                console.error(data);
            }
        });
    }

    // Llamar a la función
    traeReactivos();

    $scope.abrirModalNuevoReactivo = function (indice = undefined) {
        if (indice >= 0 && indice != undefined) {
            $scope.nuevoReactivo = Object.assign({}, $scope.reactivos[indice]);
        } else {
            $scope.nuevoReactivo = {};
        }
        $('#modalNuevoReactivo').modal();
    }

    $scope.guardarNuevoReactivo = function () {
        if ($scope.nuevoReactivo.reactivo && $scope.nuevoReactivo != '') {
            $http.post('catalogos/php/guardarNuevoReactivo.php', $scope.nuevoReactivo).success(function (data) {
                if (data.hasOwnProperty('error')) {
                    if (data.error) {
                        swal('Error', data.message, 'error');
                    } else {
                        swal('Éxito', data.message, 'success');
                        $('#modalNuevoReactivo').modal('hide');
                        traeReactivos();
                    }
                } else {
                    growl.error('Error');
                    console.error(data);
                }
            });
        } else {
            growl.info('Ingresa el nombre del reactivo');
        }
    }
});