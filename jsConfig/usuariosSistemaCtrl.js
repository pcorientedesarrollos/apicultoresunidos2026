form.controller('usuariosSistemaCtrl', ['$scope', '$http', '$routeParams', 'growl', '$q', '$rootScope', 'auth', function ($scope, $http, $routeParams, growl, $q, $rootScope, auth) {

    // =========================P A R A M E T R O S===========================
    var idUsuario = $routeParams.idUsuario;
    var defaultSectionName = 'SECCIONES_DEFAULT_SECCION'; // Para redirigir a la seccion
    $scope.usuarios = new Array();

    //==========================================================================
    // OBTIENE EL ID DEL USUARIO QUE TIENE INICIADO SESIÓN
    //==========================================================================

    function getUserId() {
        var deferred = $q.defer();
        return $http.get('./usuarios.php?opcion=dameIdPerfil').success(function (data) {
            if (!data.err) {
                deferred.resolve();
            } else {
                console.warn('No se ha podido recuperar el ID del usuario, se ha devuelvo 0 en su lugar.');
                deferred.resolve(0);
            }
        });
        return deferred.promise;
    }

    //==========================================================================
    // TRAE TODOS LOS NOMBRES DEL PERSONAL EN EL FORMATO: { id, nombre }
    //==========================================================================

    function traerPersonalOaxaca() {
        $http.post('controlAdministrativo/polizaCheque/php/traerNombres.php', 4).success(function (array) {
            $scope.personalOaxaca = array;
        });
    }

    //==========================================================================
    // TRAE TODOS LOS USUARIOS DEL SISTEMA Y LA PERSONA ENCARGADA
    //==========================================================================
    function traerUsuarios() {
        $http.post("habilitarSecciones/php/traeUsuariosSistema.php").success(function (info) {
            if (info.hasOwnProperty('error')) {
                if (info.error) {
                    swal('Error', info.message, 'error');
                } else {
                    $scope.usuarios = info.usuarios;
                }
            } else {
                growl.error('Error');
                console.error(info);
            }
        });
    }
    if (!idUsuario) {
        traerUsuarios();
    }

    //==========================================================================
    // TRAE TODOS LOS PERFILES REGISTRADOS Y LA CANTIDAD DE USUARIOS
    //==========================================================================
    function traePerfiles() {
        $http.post("habilitarSecciones/php/traePerfilesSistema.php").success(function (info) {
            if (info.hasOwnProperty('error')) {
                if (info.error) {
                    swal('Error', info.message, 'error');
                } else {
                    $scope.perfiles = info.perfiles;
                }
            } else {
                growl.error('Error');
                console.error(info);
            }
        });
    }

    //==========================================================================
    // NOS LLEVA A LA VISTA DE UN USUARIO
    //==========================================================================
    $scope.verUsuario = function (idUsuario) {
        if (idUsuario >= 0) {
            window.location.href = '#/usuariosSistema/' + idUsuario;
        }
    }

    //==========================================================================
    // TRAE LOS DETALLES DE UN USUARIO (SECCIONES A LAS CUALES PUEDE ACCEDER)
    //==========================================================================
    function traeDetalleUsuario(idUsuario) {
        $http.post("habilitarSecciones/php/traeDetalleUsuario.php?idUsuario=" + idUsuario).success(function (info) {
            if (info.hasOwnProperty('error')) {
                if (info.error) {
                    swal('Error', info.message, 'error');
                } else {
                    $scope.detalleUsuario = info.detalleUsuario;
                    $scope.detalleUsuario.usuario.app = $scope.detalleUsuario.usuario.app == '1' ? true : false;
                }
            } else {
                growl.error('Error');
                console.error(info);
            }
        });
    }

    //==========================================================================
    // ASIGNA AL LOCALSTORAGE UNA SECCION POR DEFECTO PARA VER EN LA VISTA DE
    // SECCIONES Y MODULOS
    //==========================================================================
    $scope.verModulosSeccion = function (indice) {

        var _seccion = {
            idSeccion: $scope.detalleUsuario.permisos[indice].idSeccion,
            seccion: $scope.detalleUsuario.permisos[indice].seccion,
        }
        window.localStorage.setItem(defaultSectionName, JSON.stringify(_seccion));
        window.location.href = '#/secciones';

    }

    //==========================================================================
    // ABRIR MODAL
    //==========================================================================
    $scope.modalAccionesUsuario = function () {
        traerPersonalOaxaca();
        $scope.usuario = Object.assign({}, $scope.detalleUsuario.usuario);
        $scope.usuario.cambiarClave = false;
        $scope.usuario.nuevaPassword = {
            password: null,
            confirm: null
        }
        $('#modalAccionesUsuario').modal();
    }

    //==========================================================================
    // VALIDA Y GUARDA LAS MODIFICACIONES HECHAS A LOS DATOS DEL USUARIO
    //==========================================================================

    function validarDatosUsuario() {
        if (!$scope.usuario.usuario) {
            growl.info('Especifica el usuario');
            return false;
        } else if (!$scope.usuario.idPersonalOM) {
            growl.info('Especifica un personal encargado');
            return false;
        } else if (!$scope.usuario.idUsuario) {
            growl.info('Especifica un personal encargado');
            return false;
        } else if ($scope.usuario.cambiarClave) {
            if (!$scope.usuario.nuevaPassword.password) {
                growl.info('Especifica la nueva contraseña');
                return false;
            } else if (!$scope.usuario.nuevaPassword.confirm) {
                growl.info('Escribe de nuevo la contraseña');
                return false;
            }
        }

        return true;
    }

    $scope.guardarCambiosUsuario = function () {
        if (validarDatosUsuario()) {

            // HACE UN POST DESPUÉS DE VERIFICAR
            $http.post('habilitarSecciones/php/guardarUsuario.php', $scope.usuario).success(function (info) {
                if (info.hasOwnProperty('error')) {
                    if (info.error) {
                        swal('Error', info.message, 'error');
                    } else {
                        getUserId().then(function (data) {
                            $scope.idUsuarioActivo = data.data.idUsuario ? data.data.idUsuario : 0;
                            if ($scope.idUsuarioActivo == $scope.usuario.idUsuario) {
                                // SI EL USUARIO ACTUALIZADO ES EL MISMO QUE EL DE LA SESIÓN ACTIVA, PEDIR INICIAR SESIÓN DE NUEVO
                                swal({
                                    title: 'Inicie sesión de nuevo para continuar',
                                    text: 'Se han cambiado los datos del usuario actual',
                                    type: 'info',
                                    showCancelButton: false,
                                    confirmButtonText: 'Cerrar sesión',
                                    closeOnConfirm: true,
                                }, function (c) {
                                    $('#modalAccionesUsuario').modal('hide');
                                    $rootScope.lstMenu = new Array();
                                    auth.logout();
                                });
                            } else {
                                // SI NO, SOLO MOSTRAR UN MENSAJE DE SUCCESS
                                $('#modalAccionesUsuario').modal('hide');
                                swal('Hecho', info.message, 'success');
                                // TRAER DE NUEVO LA INFO DEL USUARIO
                                traeDetalleUsuario(idUsuario);

                            }
                        });
                    }
                } else {
                    growl.error('Error');
                    console.error(info);
                }
            });
        }
    }

    //==========================================================================
    // PREGUNTAR MEDIANTE ALERTA SI DECIDE DAR DE BAJA AL USUARIO
    //==========================================================================

    $scope.darBajaUsuario = function () {
        if ($scope.usuario && $scope.usuario.idUsuario) {
            swal({
                title: '¿Dar de baja al usuario"' + $scope.usuario.usuario + '"?',
                text: 'No se podrá acceder más al sistema con este usuario',
                type: 'warning',
                showCancelButton: true,
                cancelButtonText: 'Cancelar',
                confirmButtonText: 'Dar de baja',
                closeOnConfirm: false,
            }, function (c) {
                if (c) {
                    $http.post('habilitarSecciones/php/darBajaUsuario.php', $scope.usuario.idUsuario).success(function (data) {
                        if (data.hasOwnProperty('error')) {
                            if (data.error) {
                                swal('Error', data.message, 'error');
                            } else {
                                $('#modalAccionesUsuario').modal('hide');
                                swal('Hecho', data.message, 'success');
                                // TRAER DE NUEVO LA INFO DEL USUARIO QUE YA DEBE ESTAR VACÍO
                                traeDetalleUsuario(idUsuario);
                            }
                        } else {
                            growl.error('Error');
                            console.error(data);
                        }
                    });
                }
            });
        }
    }

    if (idUsuario) {
        traeDetalleUsuario(idUsuario);
    }

    //==========================================================================
    // ABRIR MODAL PARA REGISTRAR UN NUEVO USUARIO
    //==========================================================================
    $scope.modalNuevoUsuario = function () {
        if (!$scope.personalOaxaca || $scope.personalOaxaca.length == 0) {
            traerPersonalOaxaca();
        }

        if (!$scope.perfiles || $scope.perfiles.length == 0) {
            traePerfiles();
        }

        $scope.nuevoUsuario = null;
        $('#modalNuevoUsuario').modal();
    }


    //==========================================================================
    // VALIDAR Y GUARDAR A UN NUEVO USUARIO PARA EL SISTEMA
    //==========================================================================

    function validarNuevoUsuario() {
        if (!$scope.nuevoUsuario) {
            growl.info('Los datos del nuevo usuario son necesarios');
            return false;
        }

        if (!$scope.nuevoUsuario.usuario) {
            growl.info('Indique el usuario');
            return false;
        }

        if (!$scope.nuevoUsuario.idPersonalOM) {
            // Si no tiene definido el personal encargado, por defecto va 0
            $scope.nuevoUsuario.idPersonalOM = 0;
        }

        if (!$scope.nuevoUsuario.app) {
            // Si no tiene definido el personal encargado, por defecto va 0
            $scope.nuevoUsuario.app = false;
        }

        if (!$scope.nuevoUsuario.idPerfil) {
            // Si no tiene definido el perfil, por defecto va en 0
            $scope.nuevoUsuario.idPerfil = 0;
        }

        if (!$scope.nuevoUsuario.password) {
            return false;
        }

        if (!$scope.nuevoUsuario.passwordConfirm) {
            return false;
        }

        return true;
    }

    $scope.guardarNuevoUsuario = function () {
        if (validarNuevoUsuario()) {
            $http.post("habilitarSecciones/php/guardarNuevoUsuarioSistema.php", $scope.nuevoUsuario).success(function (info) {
                if (info.hasOwnProperty('error')) {
                    if (info.error) {
                        swal('Error', info.message, 'error');
                    } else {
                        $('#modalNuevoUsuario').modal('hide');
                        swal('Hecho', info.message, 'success');
                        traerUsuarios();
                    }
                } else {
                    growl.error('Error');
                    console.error(info);
                }
            });
        }
    }

}]);
