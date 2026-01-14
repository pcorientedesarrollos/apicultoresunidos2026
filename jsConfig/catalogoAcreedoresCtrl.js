form.controller('catalogoAcreedores', function ($scope, $http, growl) {

    function traeAcreedoresDisponibles() {
        $http.get('catalogos/php/traeAcreedores.php').success(function (data) {
            if (data.hasOwnProperty('error')) {
                if (data.error) {
                    swal('Error', data.message, 'error');
                } else {
                    $scope.acreedores = data.acreedores;
                }
            } else {
                growl.console.error(('Error'));
                console.error(data);
            }
        });
    }

    // Llamar a la función
    traeAcreedoresDisponibles();

    $scope.abrirModalNuevoAcreedor = function (indice = undefined) {
        if (indice >= 0 && indice != undefined) {
            $scope.nuevoAcreedor = Object.assign({}, $scope.acreedores[indice]);
        } else {
            $scope.nuevoAcreedor = {};
        }
        $('#modalNuevoAcreedor').modal();
    }

    $scope.guardarNuevoAcreedor = function () {
        if ($scope.nuevoAcreedor.nombre && $scope.nuevoAcreedor != '') {
            $http.post('catalogos/php/guardarNuevoAcreedor.php', $scope.nuevoAcreedor).success(function (data) {
                if (data.hasOwnProperty('error')) {
                    if (data.error) {
                        swal('Error', data.message, 'error');
                    } else {
                        swal('Éxito', data.message, 'success');
                        $('#modalNuevoAcreedor').modal('hide');
                        traeAcreedoresDisponibles();
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