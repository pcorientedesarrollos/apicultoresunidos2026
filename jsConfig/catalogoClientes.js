form.controller('catalogoClientes', function ($scope, $http, growl, $routeParams, $location, ) {

    $scope.clientes = null;
    $scope.cargandoDatos = false;
    $scope.guardandoDatos = false;
    $scope.idCliente = $routeParams.idCliente;
    $scope.cliente = {};

    function obtenerListaClientes(estado = '1') {
        // Retorna la lista de clientes con el estado
        // que se haya especificado, en caso de que
        // no se haya recibido ningun parámetro, se
        // retornará la lista de clientes con estado 1

        $scope.cargandoDatos = true;
        $scope.clientes = null;

        $http.post('controlAdministrativo/php/traeCatalogoClientes.php', true)
            .success(function (data) {
                $scope.cargandoDatos = false;
                if (typeof (data) == 'object' && data.hasOwnProperty('error')) {

                    if (data.error) {
                        swal('', data.message, 'warning');
                    } else {
                        $scope.clientes = data.data.filter(function (c) {
                            if (c.estado == estado) {
                                return c;
                            }
                        });
                    }

                } else {
                    growl.error('Error');
                    console.error(data);
                }
            });

    }

    function obtenerInformacionCliente(idCliente) {
        // Obtiene la información de un cliente

        $scope.cargandoDatos = true;
        $scope.cliente = null;

        $http.post('catalogos/php/traeInformacionCliente.php', idCliente)
            .success(function (data) {
                $scope.cargandoDatos = false;
                if (typeof (data) == 'object' && data.hasOwnProperty('error')) {

                    if (data.error) {
                        swal('', data.message, 'warning');
                    } else {
                        $scope.cliente = data.data;
                        $scope.cliente.flujoEfectivo = parseInt($scope.cliente.flujoEfectivo);
                    }

                } else {
                    growl.error('Error');
                    console.error(data);
                }
            });

    }

    // Verifica que la información del
    // objeto del cliente esté completa
    // Toma como variable $scope.cliente
    function verificarCliente() {
        if (!$scope.cliente.nombre) {
            growl.info('Nombre del cliente');
            return false;
        }
        if (!$scope.cliente.domicilio) {
            growl.info('Domicilio del cliente');
            return false;
        }
        if (!$scope.cliente.telefono) {
            growl.info('Telefono del cliente');
            return false;
        }
        if (!$scope.cliente.estado) {
            growl.info('Estado del cliente');
            return false;
        }
        if (!$scope.cliente.flujoEfectivo) {
            $scope.cliente.flujoEfectivo = 0;
        }
        return true;
    }

    // Función que guardar la información del cliente
    // Sea nuevo registro o esté editando

    $scope.guardarInformacionCliente = function () {
        $scope.guardandoDatos = true;
        if (verificarCliente()) {
            $http.post('catalogos/php/guardaInformacionCliente.php', $scope.cliente).success(function (data) {
                $scope.guardandoDatos = false;
                if (typeof (data) == 'object' && data.hasOwnProperty('error')) {
                    if (data.error) {
                        swal('', data.message, 'warning');
                    } else {
                        swal('', data.message, 'success');
                        if ($scope.idCliente) {
                            obtenerInformacionCliente($scope.idCliente);
                        } else {
                            window.location = '#/catalogoClientes/cliente/' + data.idCliente;
                        }
                    }
                } else {
                    growl.error('Error');
                    console.error(data);
                }
            });
        } else {
            $scope.guardandoDatos = false;
        }
    }

    if ($location.path() != '/catalogoClientes') {
        if ($scope.idCliente) {
            // Llamar a la información del cliente y asignarla al scope.cliente
            obtenerInformacionCliente($scope.idCliente);
        } else {
            // Iniciar la variable de cliente con los valores por defecto
            $scope.cliente = {
                flujoEfectivo: 0,
                estado: 1
            }
        }
    }

    // Realizar las siguientes acciones solo si estamos en la ruta: 'catalogoClientes'
    // que es la lista de todos los clientes

    if ($location.path() == '/catalogoClientes') {

        /**
         * Método para ir a la ruta de un cliente
         * Su id se envia como un argumento
         */

        $scope.verCliente = function (idCliente) {
            if (idCliente && !isNaN(idCliente)) {
                window.location = '#/catalogoClientes/cliente/' + idCliente;
            } else {
                window.location = '#/catalogoClientes/cliente';
            }
        }

        /**
         * Cada que se inicie el controlador, 
         * revisar en el window.localStorage si 
         * ha sido seleccionado antes la opción
         */

        if (window.localStorage.getItem('opcionVerCatalogoCliente') != null) {
            $scope.opcionVerCliente = window.localStorage.getItem('opcionVerCatalogoCliente');
        } else {
            $scope.opcionVerCliente = 1;
        }

        /**
         * Listener que se ejecutará cada vez
         * que el valor de opcionVerCliente cambie
         */

        $scope.$watch('opcionVerCliente', function (valor) {

            // Guardar la opción seleccionada en el localStorage
            window.localStorage.setItem('opcionVerCatalogoCliente', valor);

            // Obtener los clientes según el valor que se haya seleccionado
            obtenerListaClientes(valor);
        })

    }




});