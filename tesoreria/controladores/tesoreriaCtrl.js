form.config(function ($routeProvider) {
    $routeProvider.when('/revisionRequerimiento', {
        templateUrl: 'tesoreria/html/depositoCompra.html',
        controller: 'tesoreriaCtrl'
    }).when('/nvoRequerimiento/:idRequisicion', {
        templateUrl: 'tesoreria/html/nuevoRequerimiento.html',
        controller: 'tesoreriaCtrl'
    })
})
form.controller('tesoreriaCtrl', ['$scope', '$http', '$routeParams', 'growl', function ($scope, $http, $routeParams, growl) {
    $scope.requerimiento = $routeParams.idRequisicion;
    $scope.encabezado = {};
    $scope.detalle = {};
    $scope.requerimientosDetalle = new Array();
    $scope.encabezado.totalTambores = 0;
    $scope.encabezado.totalKilos = 0;
    $scope.encabezado.importeTotal = 0;
    $scope.infoDeposito = new Array();
    //=================================================================
    //      TRAE INFORMACION DE RELACION DE REQUERIMIENTOS
    //=================================================================

    // Función que trae la información de Un requerimiento, para no repetir varias veces la llamada

    function traeInformacionRequerimiento(id) {
        if (id) {
            $http.get('reqDepositoCompra/php/infoRequerimiento.php?id=' + id).success(function (data) {
                if (typeof (data) == 'object' && data.hasOwnProperty('error')) {
                    if (data.error) {
                        swal('Error', data.message, 'error');
                    } else {
                        $scope.encabezado = data.data;
                        $scope.requerimientosDetalle = data.data.requerimientosDetalle;
                    }
                } else {
                    growl.error('Error');
                    console.error(data);
                }
            });
        }
    }

    // función para traer la lista de requerimientos
    function traeListaDeRequerimientos() {
        $scope.infoDeposito = null;
        $http.get('reqDepositoCompra/php/getTablaRequerimiento.php').success(function (data) {
            console.log(data);
            if (typeof (data) == 'object' && data.hasOwnProperty('error')) {
                if (data.error) {
                    swal('Error', data.message, 'error');
                } else {
                    $scope.infoDeposito = data.resultado;
                }
            } else {
                growl.error('Error');
                console.error(data);
            }

        });
    }
    traeListaDeRequerimientos();

    //=================================================================
    //      TRAE INFORMACION DEL DETALLE DEL REQUERIMIENTO
    //================================================================

    if ($scope.requerimiento > 0) {
        traeInformacionRequerimiento($scope.requerimiento);
    }

    // Función para cambiar el estado de un requerimiento cuando selecciona cobrado
    $scope.cambiarEstadoRequerimientoCobrado = function (detalle, indice) {

        if (detalle.cobrado == '1') {
            // Seleccionó el checkbox cobrado, preguntar si quiere guardarlo así

            swal({
                title: "¿Marcar el requerimiento como cobrado?",
                text: "Una vez cobrado no lo podrá revertir",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "Marcar cobrado",
                cancelButtonText: "Cancelar",
            }, function (marcar) {
                if (marcar) {
                    // Si quiere marcar, mandarlo guardado en la base de datos
                    $http.get("reqDepositoCompra/php/marcarRequerimientoCobrado.php?idDetalle=" + detalle.idDetalle).success(function (res) {
                        if (typeof (res) == 'object' && res.hasOwnProperty('error')) {
                            if (res.error) {
                                swal('Error', res.message, 'error');
                            } else {
                                traeInformacionRequerimiento($scope.requerimiento);
                                swal('Hecho', res.message, 'success');
                            }
                        } else {
                            growl.error('Error');
                            console.error(res);
                        }
                    });
                } else {
                    // si no, lo desmarcamos
                    $scope.requerimientosDetalle[indice].cobrado = '0';
                    $scope.$apply();
                }
            });

        } else {
            // Si no está cobrado, dejarlo así
        }

    }
}]);

