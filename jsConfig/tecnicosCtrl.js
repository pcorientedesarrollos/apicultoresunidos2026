form.controller('tecnicosCtrl', ['$scope', '$http', '$routeParams', function ($scope, $http, $routeParams) {

//=========================P A R A M E T R O===========================
        $scope.paramTecnico = $routeParams.idProveedorMantto;
//    ----------------------------------------------------------------

        $scope.menuTecnicos = new Array();

        $scope.tecnico = {};
        $scope.listDeAreas = {};
        $scope.listaTiposProveedores = {};

        $scope.areaTecnico = {};
        $scope.areaTecnico.idArea = "";
        $scope.areaTecnico.area = "";

        $scope.elTipoDeProveedor = {};
        $scope.elTipoDeProveedor.idTipoProveedor = "";
        $scope.elTipoDeProveedor.tipoProveedor = "";

        if ($scope.paramTecnico > 0) {
            $http.post("controlMantenimiento/php/traeProveedorMantto.php?idProveedorMantto=" + $scope.paramTecnico).success(function (data) {
                $scope.tecnico = data;
                $scope.areaTecnico = "" + $scope.tecnico.idArea + "";
                $scope.elTipoDeProveedor = "" + $scope.tecnico.idTipoProveedor + "";
            });

            $http.get('personalOaxacaMiel/php/listaAreas.php').success(function (datas) {
                $scope.listDeAreas = datas;
            });
            $http.get('json/controlMantto/tipoProveedor.json').success(function (datas) {
                $scope.listaTiposProveedores = datas.tiposProveedores;
            });
        } else {
            $http.post("controlMantenimiento/php/traeMenuProveeMantto.php").success(function (data) {
                $scope.menuTecnicos = data;
            });
            $http.get('personalOaxacaMiel/php/listaAreas.php').success(function (datas) {
                $scope.listDeAreas = datas;
            });
            $http.get('json/controlMantto/tipoProveedor.json').success(function (datas) {
                $scope.listaTiposProveedores = datas.tiposProveedores;
            });
        }

        $scope.guardarTecnico = function () {
            $scope.tecnico.idArea = $scope.areaTecnico;
            $scope.tecnico.idTipoProveedor = $scope.elTipoDeProveedor;
            if ($scope.paramTecnico > 0) {
                $http.post("controlMantenimiento/php/guardarTecnico.php?idProveedorMantto=" + $scope.paramTecnico, $scope.tecnico).success(function (info) {
                    console.log(info);
                    swal("Exito!", "Registro agregado", "success");
                    $http.post("controlMantenimiento/php/traeMenuTecnicos.php").success(function (data) {
                        $scope.menuTecnicos = data;
                    });
                    return window.location.href = "#/proveeMantto";
                });
            } else {
                $http.post("controlMantenimiento/php/guardarTecnico.php", $scope.tecnico).success(function (info) {
                    console.log(info);
                    swal("Exito!", "Registro agregado", "success");
                    $http.post("controlMantenimiento/php/traeMenuTecnicos.php").success(function (data) {
                        $scope.menuTecnicos = data;
                    });
                    return window.location.href = "#/proveeMantto";
                });
            }


        };

    }]);