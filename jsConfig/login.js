form.controller('login', function ($cookies, $rootScope, $scope, $http, auth) {
    $rootScope.listaModulos = new Array();
    $rootScope.lstMenu = new Array();

    function noLogin(res) {
        // Muestra un mensaje e imprime el resultado del post
        swal('', 'No puede iniciar sesión en este momento', 'error');
        console.warn(res);
    }

    function validarLogin() {
        if (!$scope.usuario || $scope.usuario == '') {
            swal("", "Indique el usuario", "warning");
            return false;
        }
        if (!$scope.password || $scope.password == '') {
            swal("", "Indique la contraseña", "warning");
            return false;
        }
        if (!$scope.selectYear || $scope.selectYear == '') {
            swal("", "Seleccione en año", "warning");
            return false;
        }

        return true;
    }

    $scope.acceder = function () {
        if (validarLogin()) {
            var credentials = {
                user: $scope.usuario,
                password: $scope.password,
                selectedYear: $scope.selectYear
            };

            $http.post('./validarLogin.php', credentials).success(function (respuesta) {
                if (typeof (respuesta) == 'object' && respuesta.hasOwnProperty('error')) {
                    if (respuesta.error) {
                        swal('', 'Acceso denegado', 'error');
                    } else {
                        if (respuesta.hasOwnProperty('acceso')) {
                            var respuesta = respuesta.acceso;
                            if (respuesta.hasOwnProperty('idPerfil') && respuesta.hasOwnProperty('idUsuario')) {
                                $cookies.putObject('datosRespuesta', respuesta);
                                $rootScope.datosRespuesta = $cookies.getObject('datosRespuesta');
                                auth.login(credentials.user, credentials.password, respuesta.idPerfil, respuesta.tituloEmpresa, respuesta.idUsuario, respuesta.database, respuesta.idEmpresa);
                            } else {
                                noLogin(respuesta);
                            }

                        } else {
                            noLogin(respuesta);
                        }
                    }
                } else {
                    noLogin(respuesta);
                }
            });
        }
    };
});