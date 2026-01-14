form.config(function ($routeProvider) {
    $routeProvider.when('/recoleccion', {
        templateUrl: 'recoleccion/listaLocalidades.html',
        controller: 'listaLocalidadesCtrl'
    })
    $routeProvider.when('/recoleccion/:idRecoleccion', {
        templateUrl: 'recoleccion/recoleccion.html',
        controller: 'listaLocalidadesCtrl'
    })
    $routeProvider.when('/recoleccion/nueva', {
        templateUrl: 'recoleccion/recoleccion.html',
        controller: 'listaLocalidadesCtrl'
    })
})
form.controller('listaLocalidadesCtrl', function ($scope, $http, growl, $routeParams, $location) {

    $scope.arregloPersonal = new Array();

    $scope.verPDFRecoleccion = function () {
        if ($scope.idRecoleccion) {
            window.open('reportes/recoleccion/pdfRecoleccion.php?idRecoleccion=' + $scope.idRecoleccion, '_blank');
        }
    }

    $scope.eliminarRecoleccionDetalle = function (index) {
        // Borrar el registro de la tabla, al  guardar el form, debe actualzar todo
        if (index >= 0 && $scope.recoleccion.localidades[index]) {
            // Vamos a hacer algunas cosas que hacemos al momento de agregar, solo que al contrario
            let copiaLocalidadEliminar = Object.assign({}, $scope.recoleccion.localidades[index]);
            // Restamos el precio de esa localidad, para sacar el precio promedio
            $scope.sumaPrecios -= copiaLocalidadEliminar.precio;
            $scope.recoleccion.localidades.splice(index, 1);
            // Restamos al total de tambores la recoleccion de la localidad
            $scope.recoleccion.totalTambores -= parseInt(copiaLocalidadEliminar.recoleccion);
            // Restamos al total de la compra el importe de la localidad
            $scope.recoleccion.totalImporteCompra -= parseFloat(copiaLocalidadEliminar.importe);
            // Caculamos el precio promedio
            if ($scope.recoleccion.localidades && $scope.recoleccion.localidades.length) {
                $scope.recoleccion.totalPrecioPromedio = $scope.sumaPrecios / $scope.recoleccion.localidades.length;
            } else {
                $scope.recoleccion.totalPrecioPromedio = 0;
            }
        }
    }

    $scope.obtenerFechasRecoleccion = function () {
        $scope.listaFechasRecoleccion = [];
        $http.get('recoleccion/php/obtenerFechasRecoleccion.php').success(function (data) {
            if (typeof (data) == 'object' && data.hasOwnProperty('error')) {
                if (data.error) {
                    swal('Error', data.message, 'error');
                } else {
                    $scope.totalRecoleccion = data.totalTambores;
                    $scope.listaFechasRecoleccion = data.resultado;
                }
            } else {
                growl.error('Error');
                console.error(data);
            }
        });
    }

    function traeInformacionRecoleccion(idRecoleccion) {
        $scope.recoleccion = {};
        $http.get('recoleccion/php/traeRecoleccion.php?idRecoleccion=' + idRecoleccion).success(function (data) {
            if (typeof (data) == 'object' && data.hasOwnProperty('error')) {
                if (data.error) {
                    swal('Error', data.message, 'error');
                } else {
                    $scope.recoleccion = data.resultado;
                    $scope.recoleccion.totalImporteCompra = parseFloat($scope.recoleccion.totalImporteCompra);
                    $scope.recoleccion.totalPrecioPromedio = parseFloat($scope.recoleccion.totalPrecioPromedio);
                    $scope.recoleccion.totalTambores = parseInt($scope.recoleccion.totalTambores);
                    $scope.sumaPrecios = data.resultado.localidades.length * data.resultado.totalPrecioPromedio;
                }
            } else {
                growl.error('Error');
                console.error(data);
            }
        });
    }

    function traerListaLocalidades() {
        $http.get('compras/php/localidad/listaLocalidades.php').success(function (resultado) {
            if (typeof (resultado) == 'object') {
                $scope.listaLocalidades = resultado;
            } else {
                growl.error('Error');
                console.error(data);
            }
        });
    }

    function traerListaPersonal() {
        $http.get('recoleccion/php/listaNombresPersonal.php').success(function (arrayPersonal) {
            if (typeof (arrayPersonal) == 'object') {
                $scope.listaPersonal = arrayPersonal;
            } else {
                growl.error('Error');
                console.error(data);
            }
        });
    }

    function traerListaTransportes() {
        $http.get('recoleccion/php/listaTransportes.php').success(function (data) {
            if (typeof (data) == 'object' && data.hasOwnProperty('error')) {
                if (data.error) {
                    swal('Error', data.message, 'error');
                } else {
                    $scope.listaDeTransportes = data.resultado;
                }
            } else {
                growl.error('Error');
                console.error(data);
            }
        });
    }

    function obtenerTotalUltimaRecoleccionLocalidad(idLocalidad) {
        $http.get('recoleccion/php/ultimaRecoleccion.php?idLocalidad=' + idLocalidad).success(function (data) {
            if (typeof (data) == 'object' && data.hasOwnProperty('error')) {
                if (data.error) {
                    swal('Error', data.message, 'error');
                } else {
                    $scope.nuevaLocalidad.anterior = data.resultado.total;
                    $scope.calcularTotalRecoleccion();
                }
            } else {
                growl.error('Error');
                console.error(data);
            }
        });
    }

    function verificarLocalidad() {
        if (!$scope.nuevaLocalidad.localidad) {
            growl.info('Falta localidad');
            return false;
        } else if ($scope.nuevaLocalidad.anterior == null || $scope.nuevaLocalidad.anterior == undefined) {
            growl.info('Falta cantidad anterior');
            return false;
        } else if ($scope.nuevaLocalidad.nuevo == null || $scope.nuevaLocalidad.nuevo == undefined) {
            growl.info('Falta cantidad nueva');
            return false;
        } else if ($scope.nuevaLocalidad.recoleccion == null || $scope.nuevaLocalidad.recoleccion == undefined) {
            growl.info('Falta cantidad de recoleccion');
            return false;
        } else if ($scope.nuevaLocalidad.total == null || $scope.nuevaLocalidad.total == undefined) {
            growl.info('Falta el pendiente');
            return false;
        } else if ($scope.nuevaLocalidad.precio == null || $scope.nuevaLocalidad.precio == undefined) {
            growl.info('Falta el precio');
            return false;
        } else if ($scope.nuevaLocalidad.humedad == null || $scope.nuevaLocalidad.humedad == undefined) {
            growl.info('Falta la humedad');
            return false;
        }

        return true;
    }

    $scope.agregarLocalidad = function () {
        if (verificarLocalidad()) {
            // Obtenemos los datos de la localidad del formulario
            let copiaLocalidad = Object.assign({}, $scope.nuevaLocalidad);
            $scope.nuevaLocalidad = {};

            // Le asignamos algunos valores que no tiene
            copiaLocalidad.idLocalidad = copiaLocalidad.localidad.idlocalidad;
            copiaLocalidad.localidad = copiaLocalidad.localidad.localidad;
            copiaLocalidad.importe = copiaLocalidad.recoleccion * 300 * copiaLocalidad.precio;

            // Sumamos el precio de esa localidad, para sacar el precio promedio
            $scope.sumaPrecios += copiaLocalidad.precio;

            // Si no hay localidades aún, creamos el arreglo
            if (!$scope.recoleccion.localidades) {
                $scope.recoleccion.localidades = new Array();
            }
            $scope.recoleccion.localidades.push(copiaLocalidad);

            // Sumamos al total de tambores la recoleccion de la localidad
            $scope.recoleccion.totalTambores += parseInt(copiaLocalidad.recoleccion);
            // Sumamos al total de la compra el importe de la localidad
            $scope.recoleccion.totalImporteCompra += parseFloat(copiaLocalidad.importe);

            // Caculamos el precio promedio
            if ($scope.recoleccion.localidades.length) {
                $scope.recoleccion.totalPrecioPromedio = $scope.sumaPrecios / $scope.recoleccion.localidades.length;
            } else {
                $scope.recoleccion.totalPrecioPromedio = 0;
            }

        }
    }

    function verificarRecoleccion() {

        if (!$scope.recoleccion.localidades || !$scope.recoleccion.localidades.length) {
            growl.info('No puede guardar sin agregar localidades');
            return false;
        } else if (!$scope.recoleccion.fecha) {
            growl.info('Falta fecha');
            return false;
        } else if (!$scope.recoleccion.personal.length) {
            growl.info('Falta el personal');
            return false;
        } else if (!$scope.recoleccion.transporte || !$scope.recoleccion.transporte.length) {
            growl.info('Falta el transporte');
            return false;
        } else if (!$scope.recoleccion.operadores || !$scope.recoleccion.operadores.length) {
            growl.info('Faltan los operadores');
            return false;
        } else if (!$scope.recoleccion.totalTambores) {
            growl.info('No hay tambores recolectados');
            return false;
        } else if (!$scope.recoleccion.totalImporteCompra) {
            growl.info('No hay importe de compra');
            return false;
        } else if (!$scope.recoleccion.totalPrecioPromedio) {
            growl.info('El precio promedio no se ha calculado correctamente');
            return false;
        }

        return true;
    }

    $scope.guardarRecoleccion = function () {
        // Antes de guardar, verificar los campos
        console.log($scope.recoleccion);
        if (verificarRecoleccion()) {
            if ($scope.recoleccion.idRecoleccion) {

                // editar el registro
                $http.post('recoleccion/php/guardarEdicionRecoleccion.php', $scope.recoleccion).success(function (data) {
                    if (typeof (data) == 'object' && data.hasOwnProperty('error')) {
                        if (data.error) {
                            swal('Error', data.message, 'error');
                        } else {
                            growl.success(data.message);
                            traeInformacionRecoleccion($routeParams.idRecoleccion);
                        }
                    } else {
                        growl.error('Error');
                        console.error(data);
                    }
                });
            } else {
                // guardar la recoleccion
                $http.post('recoleccion/php/guardarRecoleccion.php', $scope.recoleccion).success(function (data) {
                    if (typeof (data) == 'object' && data.hasOwnProperty('error')) {
                        if (data.error) {
                            swal('Error', data.message, 'error');
                        } else {
                            window.location = '#/recoleccion';
                            growl.success(data.message);
                        }
                    } else {
                        growl.error('Error');
                        console.error(data);
                    }
                });
            }
        }
    }

    $scope.calcularTotalRecoleccion = function () {
        /** Calcula el campo de total de una recolección
        *  total = anterior + nuevo - recoleccion;
        */

        $scope.nuevaLocalidad.total = $scope.nuevaLocalidad.anterior + $scope.nuevaLocalidad.nuevo - $scope.nuevaLocalidad.recoleccion;

    }

    if ($location.path() == '/recoleccion') {
        $scope.obtenerFechasRecoleccion();
    } else if ($location.path().indexOf('nueva') >= 0) {
        // Nueva recoleccion
        traerListaPersonal();
        traerListaTransportes();
        traerListaLocalidades();
        $scope.$watch('nuevaLocalidad.localidad', function (localidad) {
            if (localidad) {
                obtenerTotalUltimaRecoleccionLocalidad(localidad.idlocalidad);
            }
        }, true);
        $scope.recoleccion = {
            fecha: new Date().toISOString().split('T')[0],
            transporte: [],
            personal: [],
            operadores: [],
            totalTambores: 0,
            totalImporteCompra: 0,
            totalPrecioPromedio: 0
        };

        $scope.sumaPrecios = 0;

    } else if ($routeParams.idRecoleccion) {
        // Editar recoleccion
        traerListaPersonal();
        traerListaTransportes();
        traerListaLocalidades();
        $scope.$watch('nuevaLocalidad.localidad', function (localidad) {
            if (localidad) {
                obtenerTotalUltimaRecoleccionLocalidad(localidad.idlocalidad);
            }
        }, true);
        $scope.idRecoleccion = $routeParams.idRecoleccion;
        traeInformacionRecoleccion($routeParams.idRecoleccion);

    }

    $scope.nuevoPersonal = function () {
        $scope.personal = {};
        $scope.personal.nombre = "";
        $scope.recoleccion.personal.push($scope.personal);
    }

});