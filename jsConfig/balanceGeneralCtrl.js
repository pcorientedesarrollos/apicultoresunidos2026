form.controller('balanceGeneralCtrl', function ($scope, $http, growl) {
    console.info('Controlador de balance general Listo.')

    // Iniciamos la variable con un valor nulo
    $scope.balanceGeneral = null;

    // Asignamos la fecha en que se genera
    $scope.balanceGeneral = {};
    $scope.balanceGeneral.fecha = new Date();

    // Llamamos al PHP que trae los datos
    // Los asignamos a la propiedad resultado que se usará en la vista

    $http.post('informesFinancieros/php/obtenerBalanceGeneral.php').success(function (data) {
        console.log(data);
        if (typeof (data) == 'object') {
            if (data.error) {
                swal('', data.message, 'info');
            } else {
                $scope.balanceGeneral.resultado = data;
            }
        } else {
            growl.error('Error al obtener los datos');
            console.info(data);
        }
    })

    // Ejecuta esta función para obtener la descargar del informe

    $scope.descargarInformeBalanceGeneral = function () {
        // return window.location.href = 'reportes/informesFinancieros/[nombre_archivo].php?descargar';
    }

});