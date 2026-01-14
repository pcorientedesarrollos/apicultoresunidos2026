form.config(function ($routeProvider) {
    $routeProvider.when('/aprobarSolicitudes', {
        templateUrl: 'controlAdministrativo/aprobarSolicitudes/solicitudesComprasServicios.html',
        controller: 'aprobarSolicitudesCtrl'
    }).when('/aprobarCompra/:idSolicitudCompra', {
        templateUrl: 'controlAdministrativo/aprobarSolicitudes/formularioCompra.html',
        controller: 'aprobarSolicitudesCtrl'
    }).when('/aprobarServicio/:idSolicitudServicio', {
        templateUrl: 'controlAdministrativo/aprobarSolicitudes/formularioServicio.html',
        controller: 'aprobarSolicitudesCtrl'
    })
});
form.controller('aprobarSolicitudesCtrl', ['$scope', '$http', '$routeParams', 'growl', '$location', '$q', '$rootScope', function ($scope, $http, $routeParams, growl, $location, $q, $rootScope) {

    $scope.idSolicitudCompra = $routeParams.idSolicitudCompra;
    $scope.idSolicitudServicio = $routeParams.idSolicitudServicio;
    $scope.tipoSolicitud = '1';
    $scope.cargandoDatos = false;
    var date = new Date();
    var _mes = date.getMonth() + 1;
    $scope.mostrarMes = _mes.toString();
    $scope.area = 0;

    function obtenerSolicitudesServicio() {
        $scope.cargandoDatos = true;
        url = 'utilerias/solicitudes/php/obtenerSolicitudesServicio.php?';
        if ($scope.mostrarMes) {
            url += '&mes=' + $scope.mostrarMes;
        }
        if ($scope.area) {
            url += '&area=' + $scope.area;
        }
        $http.get(url).success(function (data) {
            $scope.listaServicios = data.data;
            if (data.error) {
                console.error(data.message);
            }
            $scope.cargandoDatos = false;
        });
    };

    function obtenerSolicitudesCompra() {
        $scope.cargandoDatos = true;
        url = 'utilerias/solicitudes/php/obtenerSolicitudesCompra.php?';
        if ($scope.mostrarMes) {
            url += '&mes=' + $scope.mostrarMes;
        }
        if ($scope.area) {
            url += '&area=' + $scope.area;
        }
        $http.get(url).success(function (data) {
            $scope.listaCompras = data.data;
            if (data.error) {
                console.error(data.message);
            }
            $scope.cargandoDatos = false;
        });
    };

    $scope.$watch('[ mostrarMes, area, tipoSolicitud]', function (val) {
        if (val[2] == '1') {
            obtenerSolicitudesCompra();
        } else if (val[2] == '2') {
            obtenerSolicitudesServicio();
        }
    });

    function obtenerDetalleSolicitudCompra(idSolicitudCompra) {
        $http.get('utilerias/solicitudes/php/obtenerSolicitudCompra.php?idSolicitudCompra=' + idSolicitudCompra).success(function (data) {
            if (data.hasOwnProperty('error')) {
                if (!data.error) {
                    $scope.entrada = data.data;
                } else {
                    swal('Error', data.message, 'error');
                }
            } else {
                growl.error('Error');
                console.error(data);
            }
        });
    };

    function obtenerDetalleSolicitudServicio(idSolicitudServicio) {
        $http.get('utilerias/solicitudes/php/obtenerSolicitudServicio.php?idSolicitudServicio=' + idSolicitudServicio).success(function (data) {
            if (data.hasOwnProperty('error')) {
                if (!data.error) {
                    $scope.entrada = data.data;
                } else {
                    swal('Error', data.message, 'error');
                }
            } else {
                growl.error('Error');
                console.error(data);
            }
        });
    };

    $http.get('personalOaxacaMiel/php/listaAreas.php').success(function (datas) {
        $scope.listaAreas = datas;
    });
    $http.get('almacen/php/consultaComboMeses.php').success(function (data) {
        $scope.listaDeMeses = data;
    });
    $scope.$watch('entrada.departamento', function (areaSeleccionada) {
        $http.get('control/php/listaNombresOM.php?idArea=' + areaSeleccionada)
            .success(function (data) {
                $scope.listaPersonal = data;
            });
    }, true);

    if ($location.path() == '/aprobarSolicitudes') {
        $scope.$watch('tipoSolicitud', function (valor) {
            if (valor == 1) {
                obtenerSolicitudesCompra();
            } else if (valor == 2) {
                obtenerSolicitudesServicio();
            }
        }, true);
    } else if ($scope.idSolicitudCompra > 0) {
        obtenerDetalleSolicitudCompra($scope.idSolicitudCompra);
    } else if ($scope.idSolicitudServicio > 0) {
        obtenerDetalleSolicitudServicio($scope.idSolicitudServicio);
    }

    $scope.cambiarEstado = function (detalle, indice) {
        if (detalle.idDetalleCompra && detalle.idDetalleCompra > 0) {
            var tipo = '1';
            var id = detalle.idDetalleCompra;
            var estado = detalle.estado;
        } else if (detalle.idDetalleServicio && detalle.idDetalleServicio > 0) {
            var tipo = '2';
            var id = detalle.idDetalleServicio;
            var estado = detalle.estado;
        }
        $http.get("controlAdministrativo/aprobarSolicitudes/php/autorizarSolicitud.php?tipo=" + tipo + "&id=" + id + "&estado=" + estado).success(function (res) {
            if (typeof (res) == 'object' && res.hasOwnProperty('error')) {
                if (res.error) {
                    swal('Error', res.message, 'error');
                } else {
                    if ($scope.idDetalleServicio) {
                        obtenerDetalleSolicitudServicio($scope.idDetalleServicio)
                    } else if ($scope.idDetalleCompra) {
                        obtenerDetalleSolicitudCompra($scope.idDetalleCompra);
                    }
                    growl.success('Hecho, registro modificado');
                }
            } else {
                growl.error('Error');
                console.error(res);
            }
        });
    }

    $scope.imprimirSolicitudCompra = function (idSolicitudCompra) {
        if (idSolicitudCompra) {
            window.open('reportes/administrativo/pdfSolicitudCompra.php?idSolicitudCompra=' + idSolicitudCompra);
        }
    }
    $scope.imprimirSolicitudServicio = function (idSolicitudServicio) {
        if (idSolicitudServicio) {
            window.open('reportes/administrativo/pdfSolicitudServicio.php?idSolicitudServicio=' + idSolicitudServicio);
        }
    }

    $scope.ocultarSolicitud = function (detalle) {
        if (detalle.idSolicitudCompra && detalle.idSolicitudCompra > 0) {
            var tipo = '1';
            var id = detalle.idSolicitudCompra;
            var estado = detalle.estado;
        } else if (detalle.idSolicitudServicio && detalle.idSolicitudServicio > 0) {
            var tipo = '2';
            var id = detalle.idSolicitudServicio;
            var estado = detalle.estado;
        }
        $http.post("controlAdministrativo/aprobarSolicitudes/php/ocultarSolicitud.php?tipo=" + tipo + "&id=" + id + "&estado=" + estado)
            .success(function (respuesta) {
                growl.success('Realizado');
            });
    }

}]);