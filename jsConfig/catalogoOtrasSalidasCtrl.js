form.controller('catalogoOtrasSalidasCtrl', function ($scope, $http, growl) {

    function traeOpcionesDisponibles() {
        $http.get('catalogos/php/traeOpcionesOtrasSalidas.php').success(function (data) {
            console.log(data);
            if (data.hasOwnProperty('error')) {
                if (data.error) {
                    swal('Error', data.message, 'error');
                } else {
                    $scope.opcionSalidas = data.opcionSalidas;
                    console.log($scope.opcionSalidas);
                }
            } else {
                growl.console.error(('Error'));
                console.error(data);
            }
        });
    }

    // Llamar a la función
    traeOpcionesDisponibles();

    $scope.abrirModalNuevaOpcion = function (indice = undefined) {
        if (indice >= 0 && indice != undefined) {
            $scope.nuevaOpcion = Object.assign({}, $scope.opcionSalidas[indice]);
        } else {
            $scope.nuevaOpcion = {};
        }
        $('#modalNuevaOpcion').modal();
    }

    $scope.guardarNuevaOpcion = function () {
        if ($scope.nuevaOpcion.concepto && $scope.nuevaOpcion != '') {
            $http.post('catalogos/php/guardarNuevaOpcionSalida.php', $scope.nuevaOpcion).success(function (data) {
                if (data.hasOwnProperty('error')) {
                    if (data.error) {
                        swal('Error', data.message, 'error');
                    } else {
                        swal('Éxito', data.message, 'success');
                        $('#modalNuevaOpcion').modal('hide');
                        traeOpcionesDisponibles();
                    }
                } else {
                    growl.error('Error');
                    console.error(data);
                }
            });
        } else {
            growl.info('Ingresa el nombre del acreedor');
        }
    }
});