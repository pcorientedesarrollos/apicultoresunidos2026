form.config(function ($routeProvider) {
    $routeProvider.when('/catalogoCompras', {
        templateUrl: 'compras/paginas/catalogoCompras.html',
        controller: 'catalogoComprasCtrl'
    })
})

form.controller('catalogoComprasCtrl', ['$scope', '$routeParams', '$http', 'growl', function ($scope, $routeParams, $http, growl) {

    $scope.verCatalogo = function (opcionCatalogo) {
        $http.get('compras/php/catalogos/traeCatalogos.php?parametro=' + opcionCatalogo).success(function (data) {
            if (data.hasOwnProperty('error')) {
                if (data.error) {
                    growl.error(data.message);
                } else {
                    if (data.data) {
                        $scope.catalogos = data.data;
                    } else {
                        growl.info('No se encuentran registros');
                    }
                }
            } else {
                console.error(data);
            }
        });
    }

    function traeEstados() {
        $http.get('proveedores/php/listaEstados.php').success(function (datas) {
            $scope.listaEstados = datas;
        });
    }

    function traerDatos(registroCatalogo) {
        $http.post('compras/php/catalogos/traeDatos.php', registroCatalogo).success(function (data) {
            if (data.hasOwnProperty('error')) {
                if (data.error) {
                    growl.error(data.message);
                } else {
                    if (data.data) {
                        $scope.registroCatalogo = data.data;
                        $scope.registroCatalogo.opcionCatalogo = registroCatalogo.opcionCatalogo;
                        console.log($scope.registroCatalogo);
                    } else {
                        growl.info('No se encuentran registros');
                    }
                }
            } else {
                console.error(data);
            }
        });
    }

    $scope.abrirModal = function (opcionCatalogo, id = false) {
        if (opcionCatalogo == undefined) {
            growl.info('Seleccione un catálogo');
        } else {
            $scope.registroCatalogo = {};
            $scope.registroCatalogo.opcionCatalogo = opcionCatalogo;
            switch (opcionCatalogo) {
                case '1':
                    $scope.registroCatalogo.catalogo = 'COMPRADORES';
                    break;
                case '2':
                    $scope.registroCatalogo.catalogo = 'ZONAS';
                    break;
                case '3':
                    $scope.registroCatalogo.catalogo = 'LOCALIDADES';
                    break;
                case '4':
                    $scope.registroCatalogo.catalogo = 'TRANSPORTES';
                    break;
            }
            if (id) {
                $scope.registroCatalogo.id = id;
                traerDatos($scope.registroCatalogo);
            }
            traeEstados();
            $("#modalCatalogoCompras").modal();
        }
    };

    $scope.guardarRegistroCatalogo = function () {
        console.log($scope.registroCatalogo);
        $http.post('compras/php/catalogos/guardarCatalogos.php', $scope.registroCatalogo).success(function (data) {
            console.log(data);
            if (data.hasOwnProperty('error')) {
                if (data.error) {
                    swal('Error', data.message, 'error');
                } else {
                    $('#modalCatalogoCompras').modal('hide');
                    growl.success(data.message);
                    $scope.verCatalogo($scope.registroCatalogo.opcionCatalogo);
                }
            } else {
                growl.error('Error');
                console.error(data);
            }
        });
    }

}]);