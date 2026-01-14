form.controller('catalogoTiposDeMovimiento', function ($scope, $http, growl) {

    function traeTiposMovimientos() {

        $http.get('catalogos/php/traeTiposMovimientoBancos.php').success(function (data) {
            if (typeof (data) == 'object' && data.hasOwnProperty('error')) {
                if (data.error) {
                    swal('Error', data.message, 'error');
                } else {
                    $scope.tipos = data.data;
                }
            } else {
                growl.error('Error');
                console.error(data);
            }
        });
    }

    // Al iniciar el controlador, traer la lista de todos los movimientos
    traeTiposMovimientos();

    $scope.modificarTipo = function (indice = undefined) {
        if (indice >= 0 && indice != undefined) {
            $scope.tipo = Object.assign({}, $scope.tipos[indice]);
        } else {
            $scope.tipo = {};
        }
        $('#modalTipoMovimiento').modal();
    }

    $scope.guardarTipoDeMovimiento = function () {
        if ($scope.tipo.movimiento && $scope.tipo.movimiento != '') {
            $http.post('catalogos/php/guardarTipoMovimientoBancos.php', $scope.tipo).success(function (data) {
                if (typeof (data) == 'object' && data.hasOwnProperty('error')) {
                    if (data.error) {
                        swal('Error', data.message, 'error');
                    } else {
                        swal('', data.message, 'success');
                        $('#modalTipoMovimiento').modal('hide');
                        traeTiposMovimientos();
                    }
                } else {
                    growl.error('Error');
                    console.error(data);
                }
            });
        } else {
            growl.info('Ingresa nombre o descripción del tipo de movimiento');
        }
    }
});