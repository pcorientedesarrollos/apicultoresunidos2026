form.controller('perfilesSistemaCtrl', ['$scope', '$http', '$routeParams', 'growl', '$q', '$rootScope', 'auth', function ($scope, $http, $routeParams, growl, $q, $rootScope, auth) {

    // =========================P A R A M E T R O S===========================
    var idPerfil = $routeParams.idPerfil;
    var defaultSectionName = 'SECCIONES_DEFAULT_SECCION'; // Para redirigir a la seccion
    $scope.perfiles = new Array();

    //==========================================================================
    // TRAE LAS SECCIONES Y USUARIOS RELACIONADOS AL PERFIL
    //==========================================================================
    function traeDetallePerfil(idPerfil) {
        $http.post("habilitarSecciones/php/traeDetallePerfil.php?idPerfil=" + idPerfil).success(function (info) {
            if (info.hasOwnProperty('error')) {
                if (info.error) {
                    swal('Error', info.message, 'error');
                } else {
                    $scope.detallePerfil = info.detallePerfil;
                }
            } else {
                growl.error('Error');
                console.error(info);
            }
        });
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

    if (!idPerfil) {
        traePerfiles();
    } else {
        traeDetallePerfil(idPerfil);
    }

    //==========================================================================
    // TRAE TODOS LOS USUARIOS DEL SISTEMA Y LA PERSONA ENCARGADA
    //==========================================================================

    function usuariosDisponiblesPerfil(usuario) {
        // si el usuario no se encuentra en los usuarios del perfil, devolver aquí
        var existe = false;
        for (i = 0; i < $scope.detallePerfil.usuarios.length; i++) {
            var usuarioPerfil = $scope.detallePerfil.usuarios[i];
            if (usuarioPerfil.idUsuario == usuario.idUsuario) {
                existe = true;
                i = $scope.detallePerfil.usuarios.length;
            }
        }

        if (!existe) {
            return usuario;
        }
    }

    function traerUsuarios(compare = false) {
        $http.post("habilitarSecciones/php/traeUsuariosSistema.php").success(function (info) {
            if (info.hasOwnProperty('error')) {
                if (info.error) {
                    swal('Error', info.message, 'error');
                } else {
                    if (compare) {
                        // Si recibe el argumento compare, filtrará los usuarios que ya pertenecen al perfil
                        $scope.usuarios = info.usuarios.filter(usuariosDisponiblesPerfil);
                    } else {
                        $scope.usuarios = info.usuarios;
                    }
                }
            } else {
                growl.error('Error');
                console.error(info);
            }
        });
    }

    //==========================================================================
    // REVISA UNO POR UNO LAS SECCIONES DEL SISTEMA,
    // SI COICIDE CON ALGUNA SECCIÓN DEL PERFIL, LE AGREGA LA PROPIEDAD 'onProfile' A TRUE,
    // SI NO, LA PONE EN FALSE
    // MOTIVO: MOSTRAR EN LA VISTA LAS SECCIONES QUE SI Y QUE NO ESTÁN EN EL PERFIL
    //==========================================================================

    function compararSeccionesConPerfil() {
        var deferred = $q.defer();
        if ($scope.secciones && $scope.detallePerfil.secciones) {
            if ($scope.detallePerfil.secciones.length > 0) { // Si el perfil tiene secciones
                $scope.secciones.forEach(function (seccion) {
                    for (i = 0; i < $scope.detallePerfil.secciones.length; i++) {
                        var s = $scope.detallePerfil.secciones[i];
                        if (s.idSeccion == seccion.idSeccion) {
                            seccion.onProfile = true;
                            i = $scope.detallePerfil.secciones.length;
                        } else {
                            seccion.onProfile = false;
                        }
                    }
                });
            } else {
                // Si no, poner todas las secciones en falso
                $scope.secciones.forEach(function (seccion) {
                    seccion.onProfile = false;
                });
            }

            deferred.resolve();
        } else {
            console.warn('Sin definir: $scope.secciones && $scope.detallePerfil.secciones');
        }

        return deferred.promise;
    }

    //==========================================================================
    // TRAE TODAS LAS SECCIONES DEL SISTEMA, 
    // SI RECIBE UN ÚNICO ARGUMENTO TRUE, LOS COMPARARÁ CON LAS SECCIONES DEL PERFIL
    // ACTUAL EN LA VISTA
    //==========================================================================

    function traerSecciones(compare = false) {
        $http.post('habilitarSecciones/php/traeEncabezadoSecciones.php').success(function (info) {
            if (info.hasOwnProperty('error')) {
                if (info.error) {
                    swal('Error', info.message, 'error');
                } else {
                    $scope.secciones = info.secciones;
                    if (compare) {
                        compararSeccionesConPerfil().then(function (r) {
                            $scope.perfil.secciones = $scope.secciones;
                        })
                    }
                }
            } else {
                growl.error('Error');
                console.error(info);
            }
        });
    }

    //==========================================================================
    // NOS LLEVA A LA VISTA DEL PERFIL PARA VER SECCIONES Y USUARIOS
    //==========================================================================
    $scope.verPerfil = function (idPerfil) {
        if (idPerfil >= 0) {
            window.location.href = '#/perfilesSistema/' + idPerfil;
        }
    }

    //==========================================================================
    // ASIGNA AL LOCALSTORAGE UNA SECCION POR DEFECTO PARA VER EN LA VISTA DE
    // SECCIONES Y MODULOS
    //==========================================================================
    $scope.verModulosSeccion = function (seccion) {
        window.localStorage.setItem(defaultSectionName, JSON.stringify(seccion));
        window.location.href = '#/secciones';

    }

    //==========================================================================
    // ABRIR MODAL PARA LA CREACIÓN DE UN NUEVO PERFIL
    //==========================================================================
    $scope.modalNuevoPerfil = function () {
        $scope.nuevoPerfil = '';
        $scope.copia = '0';
        $('#modalNuevoPerfil').modal();
        $scope.$watch('copia', function (val) {
            $scope.perfilCopia = '';
        });
    }

    //==========================================================================
    // VALIDAR Y GUARDAR EL NUEVO PERFIL
    //==========================================================================
    $scope.guardarNuevoPerfil = function () {
        if ($scope.nuevoPerfil && $scope.nuevoPerfil != '') {
            if ($scope.copia == '1' && $scope.perfilCopia == '') {
                growl.info("Escribe el nombre del perfil a copiar");
            } else {
                $scope.datosPerfil = {
                    nuevoPerfil: $scope.nuevoPerfil,
                    copia: $scope.copia,
                    perfilCopia: $scope.perfilCopia.idPerfil
                };
                $http.post('habilitarSecciones/php/guardarNuevoPerfil.php', $scope.datosPerfil).success(function (info) {
                    if (info.hasOwnProperty('error')) {
                        if (info.error) {
                            swal('Error', info.message, 'error');
                        } else {
                            $('#modalNuevoPerfil').modal('hide');
                            swal('Hecho', info.message, 'success');
                            traePerfiles();
                        }
                    } else {
                        growl.error('Error');
                        console.error(info);
                    }
                });
            }
        } else {
            growl.info('Escribe el nombre del nuevo perfil');
        }
    };

    //==========================================================================
    // ABRIR MODAL PARA AGREGAR SECCIONES AL PERFIL EN LA VISTA
    //==========================================================================
    $scope.modalAgregarSeccion = function () {
        $scope.perfil = $scope.detallePerfil.perfil;
        traerSecciones(true);
        $('#modalAgregarSeccion').modal();
    }

    $scope.guardarSeccionesDelPerfil = function () {
        if ($scope.perfil) {
            $http.post('habilitarSecciones/php/guardarSeccionesPerfil.php', $scope.perfil).success(function (data) {
                if (data.hasOwnProperty('error')) {
                    if (data.error) {
                        swal('Error', data.message, 'error');
                    } else {
                        $('#modalAgregarSeccion').modal('hide');
                        swal('Hecho', data.message, 'success');
                        traeDetallePerfil(idPerfil);
                    }
                } else {
                    growl.error('Error');
                    console.error(data);
                }
            });
        }
    };

    //==========================================================================
    // ABRIR MODAL PARA AGREGAR USUARIOS AL PERFIL EN LA VISTA
    //==========================================================================
    $scope.modalAgregarUsuario = function () {
        traerUsuarios(true);
        $scope.perfil = $scope.detallePerfil.perfil;
        $scope.perfil.nuevoUsuario = null;
        $('#modalAgregarUsuario').modal();
    }

    $scope.guardarNuevoUsuarioPerfil = function () {
        if ($scope.perfil.nuevoUsuario.idUsuario) {
            $http.post('habilitarSecciones/php/agregarUsuarioPerfil.php', $scope.perfil).success(function (data) {
                if (data.hasOwnProperty('error')) {
                    if (data.error) {
                        swal('Error', data.message, 'error');
                    } else {
                        $('#modalAgregarUsuario').modal('hide');
                        swal('Hecho', data.message, 'success');
                        traeDetallePerfil(idPerfil);
                    }
                } else {
                    growl.error('Error');
                    console.error(data);
                }
            });

        }
    }

    $scope.eliminarPerfil = function (perfil) {
        swal({
            title: "",
            text: "¿Está seguro de eliminar el perfil " + perfil.perfil + "?",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#64DAE4",
            confirmButtonText: "Sí, eliminar.",
            closeOnConfirm: false
        },
            function () {
                $http.post("habilitarSecciones/php/eliminarPerfil.php?idPerfil=" + perfil.idPerfil).success(function (respuesta) {
                    if (respuesta.hasOwnProperty('error')) {
                        if (!respuesta.error) {
                            swal("Éxito!", "Registro eliminado", "success");
                            traePerfiles();
                        } else {
                            growl.info(respuesta.message);
                        }
                    } else {
                        growl.info("Ocurrió un error");
                    }
                });
            });
    };

}]);
