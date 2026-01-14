form.controller('prestamosCtrl', ['$scope', '$http', '$routeParams', 'growl', '$location', function ($scope, $http, $routeParams, growl, $location) {

    $scope.prestamo = {};
    $scope.abono = {};
    $scope.guardandoNuevoPrestamo = false;

    function resetValues() {
        $scope.prestamo = {};
        $scope.abono = {};
        $scope.guardandoNuevoPrestamo = false;
    };

    var idPrestamo = $routeParams.idPrestamo;
    $scope.tipoDePersona = $routeParams.tipoDePersona;
    $scope.idNombre = $routeParams.idNombre;

    function getPrestamoInfo(idPrestamo) {
        $http.post('controlAdministrativo/prestamos/php/traerPrestamo.php', idPrestamo).success(function (data) {
            $scope.prestamo = data.content;
            if (data.error) {
                window.location = '#/controlPrestamos';
                console.info(data.message);
            }
        });
    };

    function getPrestamos() {
        $http.post('controlAdministrativo/prestamos/php/traerPrestamos.php?prestamoDePersonal', { tipoDePersona: $scope.tipoDePersona, idNombre: $scope.idNombre }).success(function (data) {
            $scope.listaDePrestamos = data.content;
            $scope.encabezadoPrestamos = data.header;
            if (data.error) {
                window.location = '#/controlPrestamos';
                console.info(data.message);
            }
        });
    };

    function getPersonalPrestamos() {
        $http.get('controlAdministrativo/prestamos/php/traerPrestamos.php?prestamosPorPersonal').success(function (data) {
            $scope.listaDePrestamosPorPersonal = data.content;
            if (data.error) {
                console.info(data.message);
            }
        });
    };

    function getListaDePersonal() {
        $http.post('controlAdministrativo/polizaCheque/php/traerNombres.php', 7).success(function (data) {
            $scope.listaDelPersonal = data;
        });
    };


    /**
     * Funciones para guardar prestamos y abonos
     */

    function guardarNuevoPrestamo(datosNuevoPrestamo) {
        $http.post('controlAdministrativo/prestamos/php/guardarNuevoPrestamo.php', datosNuevoPrestamo).success(function (data) {
            if (data.error) {
                growl.error(data.message);
            } else {
                swal('', data.message, data.swal);
                getPersonalPrestamos();
                $('#modalNuevoPrestamo').modal('hide');
            }
            $scope.guardandoNuevoPrestamo = false;
        });
    };

    function guardarNuevoAbono(datosNuevoAbono, prestamo) {
        var _post = {
            datosNuevoAbono: datosNuevoAbono,
            prestamo: prestamo
        }
        $http.post('controlAdministrativo/prestamos/php/guardarNuevoAbono.php', _post).success(function (data) {
            swal('', data.message, data.swal);
            getPrestamoInfo(idPrestamo);
            $('#modalNuevoAbono').modal('hide');
        });
    };

    function verificarDatosNuevoPrestamo(datosNuevoPrestamo) {

        if (!datosNuevoPrestamo.fecha
            || !datosNuevoPrestamo.hora
            || !datosNuevoPrestamo.nombre
            || !datosNuevoPrestamo.cantidad) {
            return false;
        }

        if (datosNuevoPrestamo.hora.indexOf(".") >= 0) {
            return false;
        }

        return true;
    };

    function verificarDatosNuevoAbono(datosNuevoAbono) {
        if (!datosNuevoAbono.fecha
            || !datosNuevoAbono.cantidad) {
            return false;
        }
        return true;
    };

    // Nuevo prestamo

    $scope.nuevoPrestamo = function () {
        $scope.prestamo = {};
        getListaDePersonal();
        $('#modalNuevoPrestamo').modal();
    };

    $scope.aceptarNuevoPrestamo = function (datosNuevoPrestamo) {
        $scope.guardandoNuevoPrestamo = true;
        if (!verificarDatosNuevoPrestamo(datosNuevoPrestamo)) {
            growl.error('Todos los datos son requeridos');
            $scope.guardandoNuevoPrestamo = false;
            return;
        } else {
            guardarNuevoPrestamo(datosNuevoPrestamo);
        }
    };

    // Nuevo Abono al préstamo

    $scope.hacerNuevoAbono = function () {
        $scope.abono = {};
        $('#modalNuevoAbono').modal();
    };

    $scope.aceptarNuevoAbono = function (datosNuevoAbono) {
        if (!verificarDatosNuevoAbono(datosNuevoAbono)) {
            growl.error('Todos los datos son requeridos');
            return;
        } else {
            datosNuevoAbono.idPrestamo = $scope.prestamo.encabezado.idPrestamo;
            guardarNuevoAbono(datosNuevoAbono, $scope.prestamo);
        }
    }

    $scope.prevenirMayorAbono = function (cantidad) {
        /**
         * Esta función se encarga de evitar hacer un monto de abono mayor al restante del préstamo
         */
        if (parseFloat(cantidad) > parseFloat($scope.prestamo.restante)) {
            $scope.abono.cantidad = parseFloat($scope.prestamo.restante);
            cantidad = parseFloat($scope.prestamo.restante);
        }
        if (cantidad < 0) {
            $scope.abono.cantidad = 0;
            cantidad = 0;
        }
    }


    /**
     *  Revisar los parámetros de la URL para mostrar la información solicitada
    */

    if ($location.path() === '/controlPrestamos') {
        resetValues();
        getPersonalPrestamos();
    } else if (idPrestamo) {
        resetValues();
        getPrestamoInfo(idPrestamo);
    } else if ($scope.tipoDePersona && $scope.idNombre) {
        resetValues();
        getPrestamos();
    }

}]);