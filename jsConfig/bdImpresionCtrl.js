form.controller('bdImpresionCtrl', ['$scope', '$http', function ($scope, $http) {
    $scope.config = { actual: {}, opciones: [], puedeCambiar: false };
    $scope.seleccion = {};
    $scope.guardando = false;

    var aplicar = function (respuesta) {
        if (respuesta.error) {
            swal('Error', respuesta.message, 'error');
            return false;
        }
        $scope.config = respuesta;
        $scope.seleccion.bd = respuesta.actual ? respuesta.actual.valor : null;
        return true;
    };

    $scope.nombreBd = function (bd) {
        var op = $scope.config.opciones.filter(function (o) { return o.bd == bd; })[0];
        return op ? op.nombre : bd;
    };

    $scope.cargar = function () {
        $http.post('utilerias/php/bdImpresion.php').success(aplicar).error(function (respuesta) {
            swal('Error', (respuesta && respuesta.message) || 'No se pudo consultar la BD de impresión', 'error');
        });
    };

    $scope.cambiar = function () {
        var bd = $scope.seleccion.bd;
        swal({
            title: '¿Cambiar BD de impresión?',
            text: 'El programa de etiquetas usará ' + $scope.nombreBd(bd) + ' (' + bd + ')',
            type: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Sí, cambiar',
            cancelButtonText: 'Cancelar'
        }, function (confirmado) {
            if (!confirmado) {
                return;
            }
            $scope.guardando = true;
            $http.post('utilerias/php/bdImpresion.php', { bd: bd }).success(function (respuesta) {
                $scope.guardando = false;
                if (aplicar(respuesta)) {
                    swal('Listo', 'BD de impresión: ' + $scope.nombreBd(bd), 'success');
                }
            }).error(function (respuesta) {
                $scope.guardando = false;
                swal('Error', (respuesta && respuesta.message) || 'No se pudo cambiar la BD de impresión', 'error');
            });
        });
    };

    $scope.cargar();
}]);
