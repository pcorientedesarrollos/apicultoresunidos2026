form.controller('buscaEquipoCtrl', ['$scope', '$http', 'growl', function ($scope, $http, growl) {
        $scope.informacionDeArticulos = new Array();
        $scope.articulos = {};
        $scope.opcionDeBuscar = {};

        //=================================================================
        //      FUNCION QUE TRAE LA INFORMACION SOLICITADA
        //=================================================================
        $scope.buscarArticulo = function () {
            if ((event.keyCode == 13)) {
                $http.post("utilerias/php/buscarArticulo.php?id=" + $scope.opcionDeBuscar.articuloNo)
                        .success(function (respuesta) {
                            console.log(respuesta);
                            $scope.articulo = respuesta;
                            if (respuesta == 0) {
                                growl.warning("Código no existente");
                            } else {
                                $scope.informacionDeArticulos.push($scope.articulo);
                            }
                        });
                $scope.opcionDeBuscar.articuloNo = "";
            }
        };
        $scope.buscarArticulos = function () {
            if ((event.keyCode == 13)) {
                $http.get("utilerias/php/buscarArticulos.php?articulo1=" + $scope.opcionDeBuscar.articulo1 + "&articulo2=" + $scope.opcionDeBuscar.articulo2).success(function (data) {
                    $scope.articulos = data;
                    $scope.articulo = $scope.articulos;
                    if (data == 0) {
                        growl.warning("Error en los códigos");
                    } else {
                        $scope.informacionDeArticulos = data;
                        $scope.opcionDeBuscar.articulo1 = "";
                        $scope.opcionDeBuscar.articulo2 = "";
                    }
                });
            }
        };
        $scope.buscarArticuloPorNombre = function () {
            if ((event.keyCode == 13)) {
                console.log($scope.opcionDeBuscar.nombreArticulo);
                $http.get("utilerias/php/buscarArticulos.php?nombre=" + $scope.opcionDeBuscar.nombreArticulo).success(function (data) {
                    $scope.articulos = data;
                    $scope.articulo = $scope.articulos;
                    if (data == 0) {
                        growl.warning("Error en la busqueda");
                    } else {
                        $scope.informacionDeArticulos = data;
                        $scope.opcionDeBuscar.nombreArticulo = "";
                    }
                    console.log(data);
                });
            }
        };
        $scope.eliminarArticulo = function (indice) {
            $scope.informacionDeArticulos.splice(indice);
            growl.warning("Registros eliminado");
        };
    }]);

