// ================================================
//   Controlador de comprador
// ================================================
form.controller('compradorCtrl', ['$scope', '$routeParams', '$http', 'growl', function ($scope, $routeParams, $http, growl) {

        var codigo = $routeParams.idcomprador;
        $scope.compradores = {};
        $scope.compSel = {};
        $scope.compra = {};
        $scope.compra.nombre = "";
        $scope.compra.telefono = "";
        $scope.creando = false;
        if (codigo === "nuevo") {
            $scope.creando = true;
        } else
        {
            $http.get('compras/php/comprador/getComprador.php?idcomprador=' + codigo).success(function (data) {
                $scope.compra = data;
            });
        }

// ================================================
//  contenido de la base de datos
// ================================================

        $http.get('compras/php/comprador/getTablaComprador.php').success(function (comps) {
            $scope.compradores = comps;
        });

        // ================================================
        //   Funcion para Agregar un comprador
        // ================================================
        $scope.guardarCom = function () {
            $scope.si = $scope.validarComprador();
            if ($scope.si == true) {
                if ($scope.creando) {
                    $http.post('compras/php/comprador/agregarComprador.php', $scope.compra).success(function (result) {
                        swal("Exito!", "Registro agregado", "success");
                        $http.get('compras/php/comprador/getTablaComprador.php').success(function (comps) {
                            $scope.compradores = comps;
                        });
                        return window.location.href = "#/compradores";
                    });
                } else
                {
                    $http.post('compras/php/comprador/editarComprador.php', $scope.compra).success(function (result) {
                        swal("Exito!", "Registro actualizado", "success");
                        $http.get('compras/php/comprador/getTablaComprador.php').success(function (comps) {
                            $scope.compradores = comps;
                        });
                        return window.location.href = "#/compradores";
                    });
                }
            }
        };
        //=================================================================
        //      VALIDAR REQUERIMIENTO
        //=================================================================
        $scope.validarComprador = function () {
            $scope.si = false;
            if ($scope.compra.nombre == "") {
                growl.error("Se requiere un nombre");
            } else if ($scope.compra.telefono == "") {
                growl.error("Se requiere un número de teléfono");
            } else {
                $scope.si = true;
            }
            return $scope.si;
        };
        // ================================================
        //   Funcion para eliminar
        // ================================================
        $scope.eliminarComp = function (id)
        {
            $http.get('compras/php/comprador/eliminarComprador.php?idcomprador=' + id).success(function (data) {
                swal("Exito!", "Registro eliminado", "success");
                $http.get('compras/php/comprador/getTablaComprador.php').success(function (comps) {
                    $scope.compradores = comps;
                });
                return window.location.href = "#/compradores";
            });
        };
    }]);