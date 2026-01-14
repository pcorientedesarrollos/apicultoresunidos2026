form.controller('formLocalidadCtrl', ['$scope', '$routeParams', '$http', 'growl', function ($scope, $routeParams, $http, growl) {

        var codigo = $routeParams.idlocalidad;
        $scope.localidad = new Array();
        $scope.localidadSel = {};
        $scope.zonasSel = {};
        $scope.zonasSel.idzona = "";
        $scope.zonasSel.zona = "";
        $scope.ciudades = {};
        $scope.ciudades.localidad = "";

        $scope.creando = false;
        if (codigo === "nuevo")
        {
            $scope.creando = true;
        } else
        {
            $http.get('compras/php/localidad/getLocalidad.php?idlocalidad=' + codigo).success(function (data) {
                $scope.ciudades = data;
                $scope.zonasSel = "" + $scope.ciudades.idzona + "";
            });
        }

        // ================================================
        //   Mostrar contenido de la base de datos
        // ================================================

        $http.get('compras/php/localidad/getTablaLocalidad.php').success(function (local) {
            $scope.localidad = local;
        });

        //=================================================================
        // CONSULTAS (Combos)
        //=================================================================

        $scope.nomZonas = {};
        $http.get('compras/php/zona/listaZona.php').success(function (arrayZonas) {
            $scope.nomZonas = arrayZonas; 
        });

        // ================================================
        //   Funcion para agregar localidad
        // ================================================
        $scope.guardarLoc = function () {
            $scope.valida = $scope.validarLocalidad();
            if ($scope.valida == true) {
                if ($scope.creando) {
                    $scope.ciudades.idzona = $scope.zonasSel;
                    $http.post('compras/php/localidad/agregarLocalidad.php', $scope.ciudades).success(function (result) {
                        swal("Exito!", "Registro agregado", "success");
                        $http.get('compras/php/localidad/getTablaLocalidad.php').success(function (local) {
                            $scope.localidad = local;
                        });
                        return window.location.href = "#/localidades";
                    });
                } else {
                    $scope.ciudades.idzona = $scope.zonasSel;
                    $http.post('compras/php/localidad/editarLocalidad.php', $scope.ciudades).success(function (result) {
                        swal("Exito!", "Registro actualizado", "success");
                        $http.get('compras/php/localidad/getTablaLocalidad.php').success(function (local) {
                            $scope.localidad = local;
                        });
                        return window.location.href = "#/localidades";
                    });
                }
            }
        };
        //=================================================================
        //      VALIDAR LOCALIDAD
        //=================================================================
        $scope.validarLocalidad = function () {
            $scope.valida = false;
            if ($scope.zonasSel.idzona == "") {
                growl.error("Se requiere una zona");
            } else if ($scope.ciudades.localidad == "") {
                growl.error("Se requiere una localidad");
            } else {
                $scope.valida = true;
            }
            return $scope.valida;
        };


        // ================================================
        //   Funcion para eliminar
        // ================================================
        $scope.eliminarLoc = function (id)
        {

            $http.get('compras/php/localidad/eliminarLocalidad.php?idlocalidad=' + id).success(function (data) {
                swal("Exito!", "Registro eliminado", "success");
                $http.get('compras/php/localidad/getTablaLocalidad.php').success(function (local) {
                    $scope.localidad = local;
                });
                return window.location.href = "#/localidades";
            });

        };
    }]);
