form.controller('estadoResultadosCtrl', function ($scope, $http, growl) {
    $scope.estadoResultado = null;
    $http.post('informesFinancieros/php/obtenerEstadoDeResultados.php').success(function (data) {
        console.log(data);
        if (typeof (data) == 'object') {
            if (data.error) {
                swal('', data.message, 'info');
            } else {
                $scope.estadoResultado = data;
            }
        } else {
            growl.error('Error al obtener los datos');
            console.info(data);
        }
    })

});