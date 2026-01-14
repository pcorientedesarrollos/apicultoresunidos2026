form.controller('configLaboratorio', function ($scope, $http, growl) {
    $scope.opciones = {};
    $scope.opcionesRango = null;
    $scope.opcionesRangosObtenidos = new Array();
    $scope.opcionLab = {};


    $scope.obtenerConfiguracion = function () {
        if ($scope.verTipoDeMiel) {
            $http.post("laboratorio/php/obtenerConfiguracion.php?id=" + $scope.opciones, $scope.verTipoDeMiel).success(function (respuesta) {
                if (respuesta.error) {
                    growl.error(respuesta.message);
                } else {
                    $scope.opcionesRangosObtenidos = respuesta.data;
                }
            });
        } else {
            growl.info('Selecciona el tipo de miel');
        }
    };

    function traeMenus(tipoDeMiel) {
        // Opciones y rango, traer cuando selecione un tipo de miel    
        $http.post("laboratorio/php/obtenerOpcionesLaboratorio.php").success(function (respuesta) {
            $scope.opcionesLaboratorio = respuesta;
        });
        $http.post("json/configuracionLaboratorio/rangos.json").success(function (respuesta) {
            $scope.listaRangos = respuesta.rangos;
        });
    }

    $scope.nuevaOpcionLab = function () {
        $("#modalConfigLaboratorio").modal();
    };
    $scope.guardarOpcionLaboratorio = function () {
        if ($scope.opcionLab.opciones != null && $scope.opcionLab.signo != null) {
            $http.post("laboratorio/php/guardarNuevaOpcionLab.php", $scope.opcionLab).success(function (data) {
                $http.post("laboratorio/php/obtenerOpcionesLaboratorio.php").success(function (respuesta) {
                    $scope.opcionesLaboratorio = respuesta;
                });
            });
            swal("Exito!", "Nueva opción disponible", "success");
            $("#modalConfigLaboratorio").modal('hide');
            $scope.opcionLab = "";
        } else {
            growl.warning("Se requiere tener los campos llenos");
        }

    };
    $scope.agregarConfiguracion = function () {
        if ($scope.opcionesRango != null) {
            $scope.configuracion = {};
            $scope.configuracion.idConfiguracionLaboratorio = 0;
            $scope.configuracion.simbolo = $scope.opcionesRango;
            $scope.configuracion.rango1 = 0;
            $scope.configuracion.rango2 = 0;
            $scope.opcionesRangosObtenidos.push($scope.configuracion);
        } else {
            growl.warning("Seleccione un rango");
        }
    };
    $scope.guardarConfiguracion = function () {
        if ($scope.verTipoDeMiel) {
            $http.post("laboratorio/php/guardarConfiguracion.php?idOpcion=" + $scope.opciones, { valor: $scope.opcionesRangosObtenidos, tipoDeMiel: $scope.verTipoDeMiel }).success(function (respuesta) {
                if (respuesta.error) {
                    growl.error(respuesta.message);
                } else {
                    growl.success(respuesta.message);
                }
            });
        } else {
            growl.info('Selecciona un tipo de miel');
        }
    };
    $scope.eliminarConfiguracion = function (id, indice) {
        if (id > 0) {
            $http.post("laboratorio/php/eliminarConfiguracion.php?id=" + id, $scope.verTipoDeMiel).success(function (respuesta) {
                if (!respuesta.error) {
                    $scope.opcionesRangosObtenidos.splice(indice, 1);
                    growl.success(respuesta.message);
                } else {
                    growl.error(respuesta.message);
                }
            });
        }
    };


    // Ver cuando cambie las opciones de miel para traer los menú

    $scope.$watch('verTipoDeMiel', function (tipoDeMiel) {
        if (tipoDeMiel) {
            traeMenus(tipoDeMiel);
            $scope.obtenerConfiguracion();
        }
    });
});
