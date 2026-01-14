form.controller('auditoriaCtrl', function ($scope, $http, growl, $location, $routeParams, $q, $rootScope) {

    $scope.nuevoEstadoParaAuditoria = null;
    $scope.verMas = false;
    if ($routeParams.idZonaAuditoria && $routeParams.idTipoDeMiel && $routeParams.idAuditoria) {
        getDetalleZona();
        // getDatosAuditoria($routeParams.idAuditoria);
        // getDatosZona($routeParams.idZonaAuditoria);
        $scope.mostrarDetalleZona = true;
        $scope.mostrarZonasAuditoria = false;
    } else if ($location.path() == '/auditoriasLst') {
        getAuditoriasInfo();
    } else if ($routeParams.idAuditoria) {
        var idAuditoria = $routeParams.idAuditoria;
        getZonasAuditoria($routeParams.idAuditoria);
        // getDatosAuditoria($routeParams.idAuditoria);
        $scope.mostrarDetalleZona = false;
        $scope.mostrarZonasAuditoria = true;
    }

    $scope.nuevaAuditoria = {
        fecha: null
    }

    /* VISTA UNO**/
    function getAuditoriasInfo() {
        //Vista listado con fechas 1
        $http.get('almacen/php/dameInformacionAuditorias.php').success(function (data) {
            if (data.error) {
                growl.error(data.message);
            } else {
                $scope.listaAuditorias = data.data;
            }
        })
    }
    // =========================================

    /* VISTA DOS */
    function getZonasAuditoria(idAuditoria) {
        //Vista listado de zonas con total de escaneados y no escaneados.2
        $http.get('almacen/php/dameInformacionAuditoria.php?idAuditoria=' + idAuditoria).success(function (data) {
            if (data.error) {
                growl.error(data.message);
            } else {
                $scope.listaZonasAuditoria = data.data;
            }
        })
    }
    // =========================================

    /* VISTA TRES */
    function getDetalleZona() {
        //Trae datos de zonas y totales, escaneados,no escaneados y neto.
        $http.post('almacen/php/dameInformacionAuditoriaPZona.php?obtenerDatos', $routeParams).success(function (data) {
            if (data.error) {
                growl.error(data.message);
            } else {
                $scope.detalleZona = data.data;
                $scope.foliosEscaneados = data.data.foliosEscaneados;
                $scope.foliosNoEscaneados = data.data.foliosNoEscaneados;
                $scope.foliosMateriaPrimaNoDisponible = data.data.foliosMpNoDisponibles;
            }
        })
    }
    // =========================================


    function getDatosAuditoria(idAuditoria) {
        //Trae estado de auditoría y usuarios
        $http.get('almacen/php/dameDatosAuditoria.php?idAuditoria=' + idAuditoria).success(function (data) {
            if (data.hasOwnProperty('error')) {
                if (data.error) {
                    growl.error(data.message);
                } else {
                    $scope.datosAuditoria = data.data;
                }
            } else {
                growl.error('Error al traer datos de la auditoría')
                console.error(data);
            }
        })
    }

    function getDatosZona(idZona) {
        //Trae el nombre de la zona
        $http.get('almacen/php/dameDatosAuditoria.php?idZona=' + idZona).success(function (data) {
            if (data.hasOwnProperty('error')) {
                if (data.error) {
                    growl.error(data.message);
                } else {
                    $scope.datosZona = data.data;
                }
            } else {
                growl.error('Error al traer datos de la zona')
                console.error(data);
            }
        })
    }

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

    getUserId().then(function (data) {
        $scope.idUsuarioActivo = data.data.idUsuario ? data.data.idUsuario : 0;
    });

    $scope.verAuditoria = function (idAuditoria) {
        var goToPath = '/auditoria/' + idAuditoria;
        $location.path(goToPath);
    };


    $scope.verDetalleZona = function (zona) {
        var goToPath = '/auditoria/' + zona.idZonaAuditoria + '/' + zona.idTipoDeMiel + '/' + idAuditoria;
        $location.path(goToPath);
    };


    $scope.regresarVistaZonas = function () {
        window.history.back();
    };

    $scope.abrirModalNuevaAuditoria = function () {
        $scope.nuevaAuditoria.fecha = null;
        $('#modalNuevaAuditoria').modal();
    };

    $scope.guardarNuevaAuditoria = function () {
        if ($scope.nuevaAuditoria.fecha) {
            let nuevaAuditoria = {
                fecha: $scope.nuevaAuditoria.fecha,
                usuario: $scope.idUsuarioActivo,
                estado: 0
            }

            $http.post('almacen/php/guardarNuevaAuditoria.php', nuevaAuditoria).success(function (data) {
                if (data.error) {
                    growl.error(data.message);
                } else {
                    getAuditoriasInfo();
                    $('#modalNuevaAuditoria').modal('hide');
                }
            })
        } else {
            growl.error('Selecciona la fecha');
        }
    }

    function cambiarEstadoAuditoria(idAuditoria, nuevoEstado) {
        var datos = {
            idAuditoria: idAuditoria,
            nuevoEstado: nuevoEstado
        }
        $http.post('almacen/php/cambiarEstadoAuditoria.php', datos).success(function (data) {
            if (data.error) {
                growl.error(data.message);
            } else {
                growl.success(data.message);
                getAuditoriasInfo();
            }
        })
    }

    function verificarAuditoriaActiva() {

        var deferred = $q.defer();
        var activas = $scope.listaAuditorias.filter(function (element) {
            if (element.estado == '1') {
                return element;
            }
        });
        deferred.resolve(activas);
        return deferred.promise;
    }

    $scope.cambiarEstadoDeAuditoria = function (auditoria, nuevoEstado) {

        swal({
            title: '',
            text: '¿Desea cambiar el estado de la auditoría?',
            showCancelButton: true,
            confirmButtonText: 'Cambiar',
            cancelButtonText: 'Cancelar',
            closeOnConfirm: true
        }, function (confirm) {
            if (confirm) {
                switch (auditoria.estado) {
                    case '1':
                        cambiarEstadoAuditoria(auditoria.idAuditoria, nuevoEstado);
                        break;
                    case '0':
                        if (nuevoEstado == '1') {
                            verificarAuditoriaActiva().then(function (data) {
                                if (data.length > 0) {
                                    growl.error('Ya existe una auditoría activa.');
                                } else {
                                    cambiarEstadoAuditoria(auditoria.idAuditoria, nuevoEstado);
                                }
                            });
                        } else {
                            cambiarEstadoAuditoria(auditoria.idAuditoria, nuevoEstado);
                        }
                        break;
                    default:
                        growl.error('Ya no puede cambiar el estado de esta auditoría.');
                        break;
                }
            } else {
                getAuditoriasInfo();
            }
        });
    };

    function verificarDescargaReporte() {
        if ($routeParams.idZonaAuditoria && $routeParams.idTipoDeMiel && $routeParams.idAuditoria) {
            return true;
        } else {
            return false;
        }
    }

    $scope.verReporteAuditoria = function (tipoReporte) {
        if (verificarAuditoriaActiva) {
            if (tipoReporte && tipoReporte != '') {
                return window.location.href = 'reportes/almacen/xlsAuditoria.php?idZonaAuditoria=' + $routeParams.idZonaAuditoria + '&idTipoDeMiel=' + $routeParams.idTipoDeMiel + '&idAuditoria=' + $routeParams.idAuditoria + '&tipoReporte=' + tipoReporte;
            }
        }
    }

    $scope.abrirModalDescargarReporteAuditoria = function () {
        $scope.opcionDescargaReporteAuditoria = null;
        $('#modalDescargarReporteAuditoria').modal();
    }
    $scope.descargarReporteAuditoria = function (opcion) {
        if (opcion) {
            $('#modalDescargarReporteAuditoria').modal('hide');
            return window.location.href = 'reportes/almacen/xlsReporteAuditoria.php?idAud=' + idAuditoria + '&tipoReporte=' + opcion;
        } else {
            growl.info('Selecciona una opción');
        }
    };
});