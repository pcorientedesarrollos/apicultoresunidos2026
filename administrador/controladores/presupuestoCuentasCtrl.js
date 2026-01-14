form.config(function ($routeProvider) {
    $routeProvider.when('/presupuestosCuentas', {
        templateUrl: 'administrador/presupuestoCuentas.html',
        controller: 'presupuestoCuentasCtrl'
    }).when('/nuevoPresupuesto/:idMes', {
        templateUrl: 'administrador/nuevoPresupuesto.html',
        controller: 'presupuestoCuentasCtrl'
    })
});
form.controller('presupuestoCuentasCtrl', function ($scope, $routeParams, $http, growl) {
    // $scope.disposicion = {};
    $scope.parametro = $routeParams.idMes;
    // $scope.nombreDelMes = {};
    // $scope.mes = "";

    $http.get('almacen/php/consultaComboMeses.php').success(function (data) {
        $scope.losMeses = data;
    });

    $http.get('administrador/php/obtenerPresupuestos.php').success(function (data) {
        $scope.presupuestos = data.data;
    });

    if ($scope.parametro > 0) {
        $http.get('administrador/php/obtenerPresupuestoPorMes.php?idMes=' + $scope.parametro).success(function (data) {
            console.log(data);
            $scope.presupuestosAsignados = data.data;
        });
        switch ($scope.parametro) {
            case "1":
                $scope.mes = "Enero";
                break;
            case "2":
                $scope.mes = "Febrero";
                break;
            case "3":
                $scope.mes = "Marzo";
                break;
            case "4":
                $scope.mes = "Abril";
                break;
            case "5":
                $scope.mes = "Mayo";
                break;
            case "6":
                $scope.mes = "Junio";
                break;
            case "7":
                $scope.mes = "Julio";
                break;
            case "8":
                $scope.mes = "Agosto";
                break;
            case "9":
                $scope.mes = "Septiembre";
                break;
            case "10":
                $scope.mes = "Octubre";
                break;
            case "11":
                $scope.mes = "Noviembre";
                break;
            case "12":
                $scope.mes = "Diciembre";
                break;
        }
        console.log($scope.parametro);
    }
    ;

    $scope.crearPresupuesto = function (nombreDelMes) {
        console.log(nombreDelMes);
        $http.post('administrador/php/crearPresupuesto.php?idMes=' + nombreDelMes).success(function (respuesta) {
            if ($scope.nombreDelMes > 0) {
                $http.get('administrador/php/obtenerPresupuestoPorMes.php?idMes=' + nombreDelMes).success(function (arrayDisposicion) {
                    $scope.disposicion = arrayDisposicion;
                    console.log($scope.disposicion);
                });
                switch ($scope.nombreDelMes) {
                    case "1":
                        $scope.mes = "Enero";
                        break;
                    case "2":
                        $scope.mes = "Febrero";
                        break;
                    case "3":
                        $scope.mes = "Marzo";
                        break;
                    case "4":
                        $scope.mes = "Abril";
                        break;
                    case "5":
                        $scope.mes = "Mayo";
                        break;
                    case "6":
                        $scope.mes = "Junio";
                        break;
                    case "7":
                        $scope.mes = "Julio";
                        break;
                    case "8":
                        $scope.mes = "Agosto";
                        break;
                    case "9":
                        $scope.mes = "Septiembre";
                        break;
                    case "10":
                        $scope.mes = "Octubre";
                        break;
                    case "11":
                        $scope.mes = "Noviembre";
                        break;
                    case "12":
                        $scope.mes = "Diciembre";
                        break;
                }
                return window.location.href = "#/nuevoPresupuesto/" + nombreDelMes;
            }
        });
    };

    $scope.guardarPresupuestos = function () {
        var datoss = [];
        angular.forEach($scope.presupuestosAsignados, function (value) {
            angular.forEach(value.subcuentas, function (valor) {
                datoss.push(valor);
                // var presupuestos = $scope.presupuestosAsignados.map(function (element) {
                //     var presupuesto = {
                //         idPresupuesto: parseInt(element.idPresupuesto),
                //         // idEstado: parseInt(element.idEstado),
                //         cantidad: parseFloat(element.cantidad)
                //     }
                //     return presupuesto;
                // });
                // console.log(presupuestos);
                // $http.post('administrador/php/guardarPresupuestos.php', { valor: presupuestos }).success(function (data) {
                // });
            });
        });
        if (datoss) {
            $http.post('administrador/php/guardarPresupuestos.php', { valor: datoss }).success(function (data) {
                swal("¿Éxito!", "Registros Actualizados", "success");
            });
        }
    };

    // $scope.mirarPDF = function () {
    //     window.open('reportes/almacen/pdfDisposicionDelPersonal.php?idMes=' + $scope.parametro, '_blank');
    // };

});