form.config(function ($routeProvider) {
    $routeProvider.when('/analisisExternos', {
        templateUrl: 'laboratorio/analisisExternos.html',
        controller: 'analisisExternosCtrl.js'
    }).when('/capturaAnalisisExternos/:idAnalisis', {
        templateUrl: 'laboratorio/capturaAnalisisExternos.html',
        controller: 'analisisExternosCtrl.js'
    })
});

form.controller('analisisExternosCtrl.js', function ($scope, $http, $routeParams, growl, $location) {
    $scope.externos = new Array();
    $scope.idExterno = $routeParams.idExterno;
    $scope.idAnalisis = $routeParams.idAnalisis;
    $scope.analisisExternos = new Array();
    $scope.guardandoDatos = false;
    $scope.cargandoDatos = false;
    $scope.nuevoExterno = {};

    $scope.muestras = new Array();

    //____________________
    $scope.mostrarPorcentaje = false;
    $scope.mostrarSf = false;
    $scope.mostrarSt = false;
    $scope.mostrarC13 = false;
    $scope.mostrarHmf = false;
    $scope.mostrarColor = false;
    $scope.mostrarTt = false;
    //_________________________

    function traeCatalogoEmpresas() {
        $scope.externos = null;
        $scope.cargandoDatos = true;
        $http.post('laboratorio/php/catalogoEmpresasExternas.php').success(function (data) {
            if (data.error) {
                growl.error(data.message);
            }
            $scope.externos = data.externos;
            $scope.cargandoDatos = false;
        });
    }

    // $http.post("json/laboratorio/floraciones.json").success(function (valor) {
    //     $scope.listaFloracion = valor.floraciones;
    // });
    $http.post("laboratorio/php/listaFloraciones.php").success(function (valor) {
        $scope.listaFloracion = valor;
    });

    $scope.mostrarColumna = function (valor, analisis) {
        switch (analisis) {
            case 'porcentaje':
                if (valor == '1') {
                    $scope.mostrarPorcentaje = true;
                } else {
                    $scope.mostrarPorcentaje = false;
                }
                break;
            case 'sf':
                if (valor == '1') {
                    $scope.mostrarSf = true;
                } else {
                    $scope.mostrarSf = false;
                }
                break;
            case 'st':
                if (valor == '1') {
                    $scope.mostrarSt = true;
                } else {
                    $scope.mostrarSt = false;
                }
                break;
            case 'c13':
                if (valor == '1') {
                    $scope.mostrarC13 = true;
                } else {
                    $scope.mostrarC13 = false;
                }
                break;
            case 'hmf':
                if (valor == '1') {
                    $scope.mostrarHmf = true;
                } else {
                    $scope.mostrarHmf = false;
                }
                break;
            case 'color':
                if (valor == '1') {
                    $scope.mostrarColor = true;
                } else {
                    $scope.mostrarColor = false;
                }
                break;
            case 'tt':
                if (valor == '1') {
                    $scope.mostrarTt = true;
                } else {
                    $scope.mostrarTt = false;
                }
                break;
        }
    }

    $scope.updateModel = function () {
        for (var i = 0; i < $scope.counter; i++) {
            $scope.muestras.push({});
        }
    };

    if ($location.path() == '/empresasExternas' || $scope.idAnalisis) {
        traeCatalogoEmpresas();
    } else {
        $http.get('compras/php/localidad/listaLocalidades.php').success(function (data) {
            $scope.listaLocalidades = data;
        });

        if ($scope.idExterno > 0) {
            $scope.cargandoDatos = true;
            $scope.nuevoExterno = null;
            $http.post('laboratorio/php/traeInformacionExterno.php', $scope.idExterno)
                .success(function (data) {
                    $scope.cargandoDatos = false;
                    if (typeof (data) == 'object' && data.hasOwnProperty('error')) {
                        if (data.error) {
                            swal('', data.message, 'warning');
                        } else {
                            $scope.nuevoExterno = data.data;
                        }
                    } else {
                        growl.error('Error');
                        console.error(data);
                    }
                });
        }

        $scope.guardarEmpresaExterna = function () {
            $scope.guardandoDatos = true;
            if ($scope.nuevoExterno.nombre && $scope.nuevoExterno != '') {
                $http.post('laboratorio/php/guardarEmpresaExterna.php', $scope.nuevoExterno).success(function (data) {
                    $scope.guardandoDatos = false;
                    if (data.hasOwnProperty('error')) {
                        if (data.error) {
                            swal('Error', data.message, 'error');
                        } else {
                            swal('Éxito', data.message, 'success');
                            return window.location.href = "#/empresasExternas";
                        }
                    } else {
                        growl.error('Error');
                        console.error(data);
                    }
                });
            } else {
                $scope.guardandoDatos = false;
                growl.info('Ingresa el nombre de la empresa');
            }
        };
    }



    $http.post('laboratorio/php/listaAnalisisExternos.php').success(function (data) {
        if (data.error) {
            growl.error(data.message);
        }
        $scope.analisisExternos = data.externos;
        $scope.cargandoDatos = false;
    });

    $scope.eliminarRegistro = function (index) {
        $scope.muestras.splice(index, 1);
    }

    $scope.guardaaar = function () {
        var informacion = [];
        informacion.push($scope.analisis);
        informacion.push($scope.muestras);
        $http.post("laboratorio/php/guardarAnalisisExterno.php", informacion).success(function (info) {
            console.log(info);
            if (info.hasOwnProperty('error')) {
                if (!info.error) {
                    swal('', info.message, info.swal);
                    window.location.href = '#/analisisExternos';
                } else {
                    swal('Error', info.message, 'error');
                }
            } else {
                growl.error('Error');
                console.error(info);
            }
        });
    }

    function traeEntrada(idEntrada) {
        $http.get('laboratorio/php/detalleAnalisisExterno.php?idEntrada=' + idEntrada).success(function (data) {
            if (data.hasOwnProperty('error')) {
                if (!data.error) {
                    $scope.analisis = data.data;
                    $scope.muestras = $scope.analisis.conceptos;
                } else {
                    swal('Error', data.message, 'error');
                }
            } else {
                growl.error('Error');
                console.error(data);
            }
        });
    };

    if ($scope.idAnalisis > 0) {
        traeEntrada($scope.idAnalisis);
    }

    $scope.imprimirReporteAnalisis = function () {
        window.open('reportes/laboratorio/pdfAnalisisExternos.php?idEntrada=' + $scope.idAnalisis, '_blank');
    };

});