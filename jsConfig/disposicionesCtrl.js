form.controller('disposicionesCtrl', ['$scope', '$routeParams', '$http', 'growl', function ($scope, $routeParams, $http, growl) {
        $scope.disposicion = {};
        $scope.parametroDisposicion = $routeParams.idMes;
        $scope.nombreDelMes = {};
        $scope.mes = "";

        $scope.losMeses = {};
        $http.get('almacen/php/consultaComboMeses.php').success(function (data) {
            $scope.losMeses = data;
        });

        $scope.infoDisposiciones = {};
        $http.get('almacen/php/dameDisposiciones.php').success(function (data) {
            $scope.infoDisposiciones = data;
        });

        if ($scope.parametroDisposicion > 0) {
            $http.get('almacen/php/dameDisposicion.php?idMes=' + $scope.parametroDisposicion).success(function (arrayDisposicion) {
                $scope.disposicion = arrayDisposicion;
            });
            switch ($scope.parametroDisposicion)
            {
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
            console.log($scope.parametroDisposicion);
        }
        ;

        $scope.crearDisposicion = function (nombreDelMes) {
            $http.post('almacen/php/crearDisposicion.php?idMes=' + nombreDelMes).success(function (respuesta) {
                if ($scope.nombreDelMes > 0) {
                    $http.get('almacen/php/dameDisposicion.php?idMes=' + nombreDelMes).success(function (arrayDisposicion) {
                        $scope.disposicion = arrayDisposicion;
                        console.log($scope.disposicion);
                    });

                    switch ($scope.nombreDelMes)
                    {
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


                    return window.location.href = "#/nvaDisposicion/" + nombreDelMes;
                }
            });
        };

        $scope.cambiarDisposicion = function (valor, id, semana) {
            $http.post("almacen/php/cambiarDisposicion.php?valor=" + valor + "&id=" + id + "&semana=" + semana)
                    .success(function (respuesta) {
                        growl.success(respuesta);
                    });
        };

        $scope.guardarObservaciones = function () {
            $http.post("almacen/php/guardarObservaciones.php", $scope.disposicion)
                    .success(function (respuesta) {
                        swal("Exito!", "Registros Actualizados", "success");
                        return window.location.href = "#/disposiciones";
                    });
        };

        $scope.mirarPDF = function () {
            window.open('reportes/almacen/pdfDisposicionDelPersonal.php?idMes=' + $scope.parametroDisposicion, '_blank');
        };

    }]);