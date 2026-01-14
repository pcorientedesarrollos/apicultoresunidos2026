form.controller('mainCtrl', ['$scope', '$http', '$filter', 'growl', '$q', function ($scope, $http, $filter, growl, $q) {
    var nombreLSCodigos = 'codigosDeNotificacion';

    $scope.mandarCorreo = function (mattosAtrasados) {
        if (mattosAtrasados) {
            $http.post('controlMantenimiento/php/listaDeMantenimientos.php?opcion=mandarCorreo', mattosAtrasados).success(function (data) {
                if (data.hasOwnProperty('error')) {
                    if (data.error) {
                        growl.error('Error, revise consola');
                        console.warn(data.message);
                    } else {
                        console.warn('A las: ', Date());
                        console.warn(data.message);
                    }
                } else {
                    growl.error('Error, revise consola.');
                    console.error(data);
                }
            });
        }
    };

    function hacerDatosNotificacion(data) {

        var deferred = $q.defer();

        var total = data.length;
        var titulo = data[0].titulo;
        var fecha = data[0].fechaProgramada;
        var areas = [];
        data.forEach(function (matto) {
            if (areas.indexOf(matto.area) == -1) {
                areas.push(matto.area);
            }
        });

        var codigoDeNotificacion = data.length + data[0].titulo + data[0].fechaProgramada + areas.join('-');
        if (localStorage.getItem(nombreLSCodigos) != null) {
            $scope.codigos = localStorage.getItem(nombreLSCodigos).split(',');
        } else {
            $scope.codigos = [];
            localStorage.setItem(nombreLSCodigos, $scope.codigos);
        }

        var visto = false;
        $scope.codigos.forEach(function (codigo) {
            if (codigoDeNotificacion == codigo) {
                visto = true;
            }
        });
        var resultado = { total: total, titulo: titulo, fecha: fecha, areas: areas, visto: visto, codigoDeNotificacion: codigoDeNotificacion };
        deferred.resolve(resultado);
        return deferred.promise;
    }

    $scope.hacerNotificacion = function (data, correo = false) {
        hacerDatosNotificacion(data).then(function (resultado) {
            if (!resultado.visto) {
                Push.create(resultado.total + ' ' + resultado.titulo + $filter('date')(resultado.fecha, 'dd/MM/yyyy'), {
                    requireInteraction: true,
                    body: 'Area: ' + resultado.areas.join(),
                    icon: './images/LOGO.png',
                    vibrate: [100, 100],
                    onClose: function () {
                        $scope.codigos = localStorage.getItem(nombreLSCodigos).split(',');
                        $scope.codigos.push(resultado.codigoDeNotificacion);
                        localStorage.setItem(nombreLSCodigos, $scope.codigos);
                        if (correo) {
                            $http.get('controlMantenimiento/php/listaDeMantenimientos.php?opcion=revisarSiMandarCorreo').success(function (res) {
                                if (res.hasOwnProperty('error')) {
                                    if (res.error) {
                                        growl.error('Error, revise consola');
                                        console.warn(res.message);
                                    } else {
                                        if (!res.enviado) {
                                            $scope.mandarCorreo(data);
                                        }
                                    }
                                } else {
                                    growl.error('Error, revise consola.');
                                    console.error(res);
                                }
                            });
                        }
                    }
                });
            }
        })

    };

    $scope.revisarMantenimientosProgramados = function () {
        $http.get('controlMantenimiento/php/listaDeMantenimientos.php?opcion=mantenimientosProgramados').success(function (data) {
            if (data.hasOwnProperty('error')) {
                if (data.error) {
                    growl.error('Error, revise consola');
                    console.warn(data.message);
                } else {
                    if (data.resultado.length > 0) {
                        $scope.hacerNotificacion(data.resultado);
                    }
                }
            } else {
                growl.error('Error, revise consola.');
                console.error(data);
            }
        });
    };

    $scope.revisarMantenimientosPasados = function () {
        $http.get('controlMantenimiento/php/listaDeMantenimientos.php?opcion=mantenimientosPasados').success(function (data) {
            if (data.hasOwnProperty('error')) {
                if (data.error) {
                    growl.error('Error, revise consola');
                    console.warn(data.message);
                } else {
                    if (data.resultado.length > 0) {
                        $scope.hacerNotificacion(data.resultado, true);
                    }
                }
            } else {
                growl.error('Error, revise consola.');
                console.error(data);
            }
        });
    };


    function revisarHora() {
        var date = new Date();
        var hora = date.getHours();
        if (hora === 9 || hora === 17) {
            $scope.revisarMantenimientosProgramados();
            $scope.revisarMantenimientosPasados();
        }
    }
    revisarHora();
    setInterval(function () {
        revisarHora();
    }, 1000 * 60 * 50);

}]);