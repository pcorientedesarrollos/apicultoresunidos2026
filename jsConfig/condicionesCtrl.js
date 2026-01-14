form.controller('condicionesCtrl', ['$scope', '$routeParams', '$http', 'growl', '$location', function ($scope, $routeParams, $http, growl, $location) {
    $scope.idMesCondicion = $routeParams.idMes;
    $scope.idAlmacen = $routeParams.idAlmacen;
    $scope.infoCondiciones = {};
    $scope.condicion = {};
    $scope.nombreMes = {};
    $scope.almacenes = {};
    $scope.meses = {};

    function traerCondiciones(almacen) {
        $http.post('almacen/php/dameCondicionesAlmacenamientos.php', almacen).success(function (data) {
            if (!data.error) {
                $scope.infoCondiciones = data.data;
            } else {
                growl.error(data.message);
            }
        });
    };

    function traerAlmacenes() {
        // Esta función trae los almacenes activos para hacer condicion
        $http.get('almacen/php/almacenesParaCondiciones.php?opt=Temporales').success(function (data) {
            if (!data.error) {
                $scope.almacenes = data.data;
            } else {
                growl.error(data.message);
            }
        });
    };

    function traerAlmacenesDisponibles() {
        // Esta función trae todos los almacenes que pueden estar en el menú, para filtrado
        $http.get('almacen/php/almacenesParaCondiciones.php?opt=Disponibles').success(function (data) {
            if (!data.error) {
                $scope.almacenesDisponibles = data.data.map(function (almacen) {
                    almacen.active = almacen.active == '1' ? true : false;
                    return almacen;
                });
            } else {
                growl.error(data.message);
            }
        });
    };

    if ($location.path() == '/condiciones') {
        $http.get('almacen/php/consultaComboMeses.php').success(function (data) {
            // Solo se muestra en la vista de creacion, son los datos de los meses
            $scope.meses = data;
        });
        traerAlmacenes();
    }

    if ($scope.idMesCondicion && $scope.idAlmacen) {
        // Si existen estos dos parámetros, traer la información
        $http.post('almacen/php/dameCondicionAlmacenamiento.php', { idMes: $scope.idMesCondicion, idAlmacen: $scope.idAlmacen }).success(function (res) {
            $scope.nombreAlmacen = res.nombreAlmacen;
            $scope.condicion = res.data;
            if (res.error) {
                growl.error(res.message);
            }
        });
    }

    $scope.crearCondicionAlmacen = function (nombreMes, almacen) {
        if (!isNaN(nombreMes) && isFinite(nombreMes) && nombreMes != null && almacen) {
            $http.post('almacen/php/crearCondicionAlmacen.php', { idMes: nombreMes, idAlmacen: almacen }).success(function (respuesta) {
                if (respuesta.error) {
                    growl.error(respuesta.message);
                } else {
                    return window.location.href = "#/nvaCondicion/" + nombreMes + '/' + almacen;
                }
            });
        } else {

            growl.info('Selecciona un mes/almacén antes de crear');
        }
    };

    $scope.cambiarCondicion = function (valor, id, semana) {
        $http.post("almacen/php/cambiarCondicionAlmacenamiento.php?valor=" + valor + "&id=" + id + "&semana=" + semana)
            .success(function (respuesta) {
                growl.success(respuesta);
            });
    };

    $scope.filtrarMenuAlmacenes = function () {
        // Esta función abre un modal que permite filtrar los almacenes del menú chosen
        traerAlmacenesDisponibles();
        $('#modalFiltroAlmacenes').modal();
    };
    $scope.guardarFiltroAlmacenes = function () {
        $http.post('almacen/php/almacenesParaCondiciones.php?opt=GuardarTemporales', $scope.almacenesDisponibles).success(function (data) {
            if (!data.error) {
                traerAlmacenes();
                swal('', 'Se ha generado un nuevo filtro al menú', 'success');
                $('#modalFiltroAlmacenes').modal('hide');
            } else {
                growl.error(data.message);
            }
        });
    };

    $scope.mirarPDFCP = function () {
        if ($scope.idMesCondicion && $scope.idAlmacen) {
            window.open('reportes/almacen/pdfCondicionAlmacenamiento.php?idMes=' + $scope.idMesCondicion + '&idAlmacen=' + $scope.idAlmacen, '_blank');
        } else {
            growl.info('Los parámetros no son correctos');
        }
    };

    $scope.$watch('verAlmacen', function (almacen) {
        // Ver el tipo de almacén que se selecciona para traer los datos
        if (almacen) {
            traerCondiciones(almacen);
        } else {
            $scope.infoCondiciones = {};
        }
    })

}]);