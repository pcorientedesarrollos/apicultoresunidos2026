form.config(function ($routeProvider) {
    $routeProvider.when('/proyeccion', {
        templateUrl: 'recoleccion/proyeccion-real.html',
        controller: 'proyeccionCtrl'
    })
    $routeProvider.when('/proyeccion/nueva', {
        templateUrl: 'recoleccion/proyeccion.html',
        controller: 'proyeccionCtrl'
    })
    $routeProvider.when('/proyeccion/:tipoDeMiel/:idProyeccion', {
        templateUrl: 'recoleccion/proyeccion.html',
        controller: 'proyeccionCtrl'
    })
})
form.controller('proyeccionCtrl', function ($scope, $http, growl, $routeParams, $location) {

    function obtenerProyeccionesSemanales(tipoMiel) {
        $http.get('recoleccion/php/proyeccion/semanales.php?miel=' + tipoMiel).success(function (data) {
            if (typeof (data) == 'object' && data.hasOwnProperty('error')) {
                if (data.error) {
                    swal('Error', data.message, 'error');
                } else {
                    $scope.proyeccionesSemanales = data.resultado;
                }
            } else {
                growl.error('Error');
                console.error(data);
            }
        });
    }

    function obtenerListaZonas() {
        $http.get('recoleccion/php/proyeccion/zonas-nueva-proyeccion.php').success(function (data) {
            if (typeof (data) == 'object' && data.hasOwnProperty('error')) {
                if (data.error) {
                    swal('Error', data.message, 'error');
                } else {
                    $scope.proyeccion.listaZonas = data.resultado;
                }
            } else {
                growl.error('Error');
                console.error(data);
            }
        });
    }

    function actualizarTotalProyeccion() {
        $scope.proyeccion.totalProyeccion = 0;
        $scope.proyeccion.listaZonas.forEach(function (zona) {
            if (zona.kilogramos > 0) {
                $scope.proyeccion.totalProyeccion = parseFloat($scope.proyeccion.totalProyeccion) + parseFloat(zona.kilogramos);
                if ($scope.idProyeccion) { // Solo si está editando, ajustar la diferencia
                    $scope.proyeccion.diferencia = parseFloat($scope.proyeccion.totalReal) - parseFloat($scope.proyeccion.totalProyeccion);
                }
            }
        })
    }

    $scope.cambioCantidadProyeccion = function (cantidad, index) {

        if (cantidad) {
            let kilos = cantidad * 300;
            $scope.proyeccion.listaZonas[index].kilogramos = kilos;
            actualizarTotalProyeccion();
        } else {
            $scope.proyeccion.listaZonas[index].kilogramos = 0;
            actualizarTotalProyeccion();
        }

    }

    function verificarNuevaProyeccion() {
        if (!$scope.proyeccion.tipoDeMiel) {
            growl.info('Indique el tipo de miel');
            return false;
        } else if (!$scope.proyeccion.nombre) {
            growl.info('Escriba el nombre de la semana');
            return false;
        } else if (!$scope.proyeccion.inicio) {
            growl.info('Indique el inicio de la semana');
            return false;
        } else if (!$scope.proyeccion.fin) {
            growl.info('Indique el final de la semana');
            return false;
        } else if (!$scope.proyeccion.totalProyeccion) {
            growl.info('No hay total de proyeccion o es cero');
            return false;
        } else if (!$scope.proyeccion.listaZonas) {
            growl.info('No hay zonas que guardar');
            return false;
        }

        return true;
    }

    $scope.guardarInfoProyeccion = function () {
        // Hacer la verificación de que todos los campos esten completos
        if (verificarNuevaProyeccion()) {
            ;
            if ($scope.proyeccion.idProyeccion) {
                // Actualizar la información
                $http.post('recoleccion/php/proyeccion/actualizar-proyeccion.php', $scope.proyeccion).success(function (data) {
                    if (typeof (data) == 'object' && data.hasOwnProperty('error')) {
                        if (data.error) {
                            swal('Error', data.message, 'error');
                        } else {
                            swal('Listo', data.message, 'success');
                            window.location = '#/proyeccion';
                        }
                    } else {
                        growl.error('Error');
                        console.error(data);
                    }
                });
            } else {
                // Guardar una nueva proyeccion
                $http.post('recoleccion/php/proyeccion/guardar-proyeccion.php', $scope.proyeccion).success(function (data) {
                    if (typeof (data) == 'object' && data.hasOwnProperty('error')) {
                        if (data.error) {
                            swal('Error', data.message, 'error');
                        } else {
                            swal('Listo', data.message, 'success');
                            window.location = '#/proyeccion';
                        }
                    } else {
                        growl.error('Error');
                        console.error(data);
                    }
                });
            }
        }
    }

    function obtenerInformacionProyeccion(miel, idProyeccion) {
        $http.get('recoleccion/php/proyeccion/informacion-proyeccion.php?miel=' + miel + '&idProyeccion=' + idProyeccion).success(function (data) {
            if (typeof (data) == 'object' && data.hasOwnProperty('error')) {
                if (data.error) {
                    swal('Error', data.message, 'error');
                } else {
                    $scope.proyeccion = data.resultado;
                }
            } else {
                growl.error('Error');
                console.error(data);
            }
        });
    }

    if ($location.path() == '/proyeccion') {
        // Obtener las listas de proyeccion semanales
        // obtenerProyeccionesSemanales();
        // Traer a los compradores
        // Y traer sus metas por zona, con un total
        $scope.$watch('tipoDeMiel', function (val) {
            if (val) {
                if (val == '1') {
                    $scope.tituloPlantilla = 'Proyección semanal miel 100% pura de abeja';
                } else if (val == '2') {
                    $scope.tituloPlantilla = 'Proyección semanal miel 100% orgánica';
                }
                obtenerProyeccionesSemanales(val);
            }
        });

    } else if ($location.path().indexOf('nueva') >= 0) {
        // Creando nueva proyeccion
        $scope.tituloPlantilla = 'Nueva Proyección Semanal.';

        // 1.- Iniciar el objeto de proyección (encabezado) con valores por default
        $scope.proyeccion = {
            nombre: null,
            inicio: null,
            fin: null,
            totalProyeccion: 0,
            totalReal: 0,
            diferencia: 0
        }
        // 2.- Obtener la lista de zonas
        obtenerListaZonas();


    } else if ($routeParams.tipoDeMiel && $routeParams.idProyeccion) {
        // Ver detalle de una proyeccion
        $scope.tipoDeMiel = $routeParams.tipoDeMiel;
        $scope.idProyeccion = $routeParams.idProyeccion;
        $scope.tituloPlantilla = 'Proyección Semanal.';

        obtenerInformacionProyeccion($scope.tipoDeMiel, $scope.idProyeccion);

    }

    $scope.eliminarProyeccion = function (idProyeccion) {
        if (idProyeccion) {
            swal({
                title: "¿Eliminar proyección?",
                text: "Se eliminará todos los datos relacionados",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "Eliminar",
                cancelButtonText: "Cancelar",
            }, function (eliminar) {
                if (eliminar) {
                    $http.get("recoleccion/php/proyeccion/eliminarProyeccionSemanal.php?idProyeccion=" + idProyeccion).success(function (res) {
                        if (typeof (res) == 'object' && res.hasOwnProperty('error')) {
                            if (res.error) {
                                swal('Error', res.message, 'error');
                            } else {
                                obtenerProyeccionesSemanales();
                                swal('Hecho', res.message, 'success');
                            }
                        } else {
                            growl.error('Error');
                            console.error(res);
                        }
                    });
                }
            });
        }
    }


    $scope.descargarExcel = function (idProyeccion) {
        return window.location.href = 'reportes/compras/xlsProyeccionVsReal.php?idProyeccion=' + idProyeccion;
    };

});