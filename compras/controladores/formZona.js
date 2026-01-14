//var app = angular.module('Oriente65App.formZonaCtrl', []);

// ================================================
//   Controlador de Zona
// ================================================
form.controller('formZonaCtrl', ['$scope', '$routeParams', '$http', 'growl', function ($scope, $routeParams, $http, growl) {
        var codigo = $routeParams.idzona;
        $scope.zonas = {};
        $scope.zonaSel = {};
        $scope.compradorSel = {};
        $scope.compradorSel.idcomprador = "";
        $scope.compradorSel.nombre = "";
        $scope.zonaComp = {};
        $scope.zonaComp.zona = "";
        $scope.eligeLocalidad = {};
        $scope.eligeLocalidad.idzona = "";
        $scope.eligeLocalidad.zona = "";
        $scope.pueblito = {};
        $scope.pueblito.localidad = "";

        $scope.creando = false;
        if (codigo === "nuevo")
        {
            $scope.creando = true;
        } else {
            $http.get('compras/php/zona/getZona.php?idzona=' + codigo).success(function (data) {
                $scope.zonaComp = data;
                $scope.compradorSel = "" + $scope.zonaComp.idcomprador + "";
            });
        }
//==================================================================
//      MODAL PONIENTE
//==================================================================

        $scope.modalPoniente = function () {
            $("#modalPoniente").modal();
            $scope.poniente = {};
            $http.get('compras/php/zona/localidadesPoniente.php').success(function (data) {
                $scope.poniente = data;
            });
        };
        $scope.cerrarPoniente = function () {
            $('#modalPoniente').modal('hide');
        };

        //==================================================================
        //      MODAL SUR
        //==================================================================

        $scope.modalSur = function () {
            $("#modalSur").modal();
            $scope.sur = {};
            $http.get('compras/php/zona/localidadesSur.php').success(function (data) {
                $scope.sur = data;
            });
        };
        $scope.cerrarSur = function () {
            $('#modalSur').modal('hide');
        };

        //==================================================================
        //      MODAL CAMPECHE
        //==================================================================

        $scope.modalCampeche = function () {
            $("#modalCampeche").modal();
            $scope.campeche = {};
            $http.get('compras/php/zona/localidadesCampeche.php').success(function (data) {
                $scope.campeche = data;
            });
        };
        $scope.cerrarCampeche = function () {
            $('#modalCampeche').modal('hide');
        };

        //==================================================================
        //      MODAL CENTRO MARIO
        //==================================================================

        $scope.modalCentroM = function () {
            $("#modalCentroM").modal();
            $scope.centroM = {};
            $http.get('compras/php/zona/localidadesCentroMario.php').success(function (data) {
                $scope.centroM = data;
            });
        };
        $scope.cerrarCentroM = function () {
            $('#modalCentroM').modal('hide');
        };

        //==================================================================
        //      MODAL CENTRO GERARDO
        //==================================================================

        $scope.modalCentroG = function () {
            $("#modalCentroG").modal();
            $scope.centroG = {};
            $http.get('compras/php/zona/localidadesCentroGerardo.php').success(function (data) {
                $scope.centroG = data;
            });
        };
        $scope.cerrarCentroG = function () {
            $('#modalCentroG').modal('hide');
        };

        //==================================================================
        //      MODAL ORIENTE
        //==================================================================

        $scope.modalOriente = function () {
            $("#modalOriente").modal();
            $scope.oriente = {};
            $http.get('compras/php/zona/localidadesOriente.php').success(function (data) {
                $scope.oriente = data;
            });
        };
        $scope.cerrarOriente = function () {
            $('#modalOriente').modal('hide');
        };

        //==================================================================
        //      MODAL QUINTA ROO
        //==================================================================

        $scope.modalQRoo = function () {
            $("#modalQRoo").modal();
            $scope.qRoo = {};
            $http.get('compras/php/zona/localidadesQRoo.php').success(function (data) {
                $scope.qRoo = data;
            });
        };
        $scope.cerrarQRoo = function () {
            $('#modalQRoo').modal('hide');
        };


        $scope.modalLocalidad = function () {
            $("#agregarLocalidad").modal();
            $scope.nomZonas = {};
            $http.get('compras/php/zona/listaZona.php').success(function (arrayZonas) {
                $scope.nomZonas = arrayZonas;
            });
        };

        $scope.guardarLcldd = function () {
            $scope.validan = $scope.validaLocalidad();
            if ($scope.validan == true) {
                $scope.pueblito.idzona = $scope.eligeLocalidad;
                $http.post('proveedores/php/agregarLocalidad.php', $scope.pueblito).success(function (result) {
                    $http.get('compras/php/zona/localidadesQRoo.php').success(function (data) {
                        $scope.qRoo = data;
                    });
                    $http.get('compras/php/zona/localidadesOriente.php').success(function (data) {
                        $scope.oriente = data;
                    });
                    $http.get('compras/php/zona/localidadesCentroGerardo.php').success(function (data) {
                        $scope.centroG = data;
                    });
                    $http.get('compras/php/zona/localidadesCentroMario.php').success(function (data) {
                        $scope.centroM = data;
                    });
                    $http.get('compras/php/zona/localidadesCampeche.php').success(function (data) {
                        $scope.campeche = data;
                    });
                    $http.get('compras/php/zona/localidadesSur.php').success(function (data) {
                        $scope.sur = data;
                    });
                    $http.get('compras/php/zona/localidadesPoniente.php').success(function (data) {
                        $scope.poniente = data;
                    });
                });
                ;
                swal("Exito!", "Nueva localidad disponible", "success");
                $("#agregarLocalidad").modal('hide');

            }
            $scope.eligeLocalidad = "";
            $scope.pueblito = "";
        };

        // ================================================
        //   CAMBIOS 20-03-2007
        // ================================================

        $http.get('compras/php/zona/getTablaZona.php').success(function (zons) {
            $scope.zona = zons;
        });
        $scope.nomComprador = {};
        $http.get('compras/php/comprador/listaComprador.php').success(function (arrayComprador) {
            $scope.nomComprador = arrayComprador;
        });
        $scope.sector = {};
        $http.get('compras/php/zona/zonas.php').success(function (arrayZon) {
            $scope.sector = arrayZon;
            console.log("HOLA" + arrayZon);
        });      


        // ================================================
        //   Funcion para agregar un zona
        // ================================================

        $scope.guardarZ = function () {
            $scope.valido = $scope.validarZona();
            if ($scope.valido == true) {
                if ($scope.creando) {
                    $scope.zonaComp.idcomprador = $scope.compradorSel;
                    $http.post('compras/php/zona/agregarZona.php', $scope.zonaComp).success(function (result) {
                        swal("Exito!", "Registro agregado", "success");
                        $http.get('compras/php/zona/getTablaZona.php').success(function (zons) {
                            $scope.zona = zons;
                        });
                        return window.location.href = "#/zonas";
                    });
                } else
                    $scope.zonaComp.idcomprador = $scope.compradorSel;
                $http.post('compras/php/zona/editarZona.php', $scope.zonaComp).success(function (result) {
                    swal("Exito!", "Registro actualizado", "success");
                    $http.get('compras/php/zona/getTablaZona.php').success(function (zons) {
                        $scope.zona = zons;
                    });
                    return window.location.href = "#/zonas";
                });
            }
        };

        //=================================================================
        //      VALIDAR ZONA
        //=================================================================
        $scope.validarZona = function () {
            $scope.valido = false;
            if ($scope.compradorSel.idcomprador == "") {
                growl.error("Se requiere un comprador");
            } else if ($scope.zonaComp.zona == "") {
                growl.error("Se requiere una zona");
            } else {
                $scope.valido = true;
            }
            return $scope.valido;
        };

        //=================================================================
        //      VALIDAR LOCALIDAD
        //=================================================================
        $scope.validaLocalidad = function () {
            $scope.validan = false;
            if ($scope.eligeLocalidad.idzona == "") {
                growl.error("Se requiere una zona");
            } else if ($scope.pueblito.localidad == "") {
                growl.error("Se requiere una localidad");
            } else {
                $scope.validan = true;
            }
            return $scope.validan;
        };


        // ================================================
        //   Funcion para eliminar
        // ================================================
        $scope.eliminarZ = function (id)
        {

            $http.get('compras/php/zona/eliminarZonas.php?idzona=' + id).success(function (data) {
                swal("Exito!", "Registro eliminado", "success");
                $http.get('compras/php/zona/getTablaZona.php').success(function (zons) {
                    $scope.zona = zons;
                });
                return window.location.href = "#/zonas";
            });

        };
    }]);