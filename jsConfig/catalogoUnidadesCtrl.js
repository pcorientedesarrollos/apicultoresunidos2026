form.controller('catalogoUnidadesCtrl', function ($scope, $http, growl) {

    function traeUnidadesDisponibles() {
        $http.get('catalogos/php/traeUnidadesDeMedida.php').success(function (data) {
            if (data.hasOwnProperty('error')) {
                if (data.error) {
                    swal('Error', data.message, 'error');
                } else {
                    $scope.unidades = data.unidades;
                }
            } else {
                growl.console.error(('Error'));
                console.error(data);
            }
        });
    }

    // Llamar a la función
    traeUnidadesDisponibles();

    $scope.abrirModalNuevaUnidad = function () {
        $scope.nuevaUnidad = {};
        $('#modalNuevaUnidad').modal();
    }

    function validarNuevaUnidad() {
        // Valida los campos de la nueva unidad
        if (!$scope.nuevaUnidad.clave || $scope.nuevaUnidad.clave == '') {
            growl.info('Verificar clave');
            return false;
        } else if (!$scope.nuevaUnidad.nombre || $scope.nuevaUnidad.nombre == '') {
            growl.info('Verificar nombre');
            return false;
        } else if (!$scope.nuevaUnidad.descripcion) {
            $scope.nuevaUnidad.descripcion = '';
        }
        return true;
    }

    $scope.guardarNuevaUnidad = function () {
        if (validarNuevaUnidad()) {
            $http.post('catalogos/php/guardarNuevaUnidad.php', $scope.nuevaUnidad).success(function (data) {
                if (data.hasOwnProperty('error')) {
                    if (data.error) {
                        swal('Error', data.message, 'error');
                    } else {
                        swal('Éxito', data.message, 'success');
                        $('#modalNuevaUnidad').modal('hide');
                        traeUnidadesDisponibles();
                    }
                } else {
                    growl.error('Error');
                    console.error(data);
                }
            });
        }
    }
});