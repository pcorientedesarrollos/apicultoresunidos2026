form.controller('catalogoMielSobrante', function ($scope, $http, growl) {

    function traeClasificacionesDisponibles() {
        $http.get('catalogos/php/traeClasificacionesSobrante.php').success(function (data) {
            if (data.hasOwnProperty('error')) {
                if (data.error) {
                    swal('Error', data.message, 'error');
                } else {
                    $scope.clasificaciones = data.clasificaciones;
                }
            } else {
                growl.console.error(('Error'));
                console.error(data);
            }
        });
    }

    // Llamar a la función
    traeClasificacionesDisponibles();

    $scope.abrirModalNuevaClasificacion = function (indice = undefined) {

        console.log(indice);
        if (indice >= 0 && indice != undefined) {
            $scope.nuevaClasificacion = Object.assign({}, $scope.clasificaciones[indice]);
        } else {
            $scope.nuevaClasificacion = {};
        }


        $('#modalNuevaClasificacion').modal();
    }

    $scope.guardarNuevaClasificacion = function () {
        if ($scope.nuevaClasificacion.nombre && $scope.nuevaClasificacion != '') {
            $http.post('catalogos/php/guardarNuevaClasificacion.php', $scope.nuevaClasificacion).success(function (data) {
                if (data.hasOwnProperty('error')) {
                    if (data.error) {
                        swal('Error', data.message, 'error');
                    } else {
                        swal('Éxito', data.message, 'success');
                        $('#modalNuevaClasificacion').modal('hide');
                        traeClasificacionesDisponibles();
                    }
                } else {
                    growl.error('Error');
                    console.error(data);
                }
            });
        } else {
            growl.info('Ingresa el nombre de la clasificación');
        }
    }
});