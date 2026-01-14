form.controller('controlCambiosCtrl', ['$scope', '$http', '$routeParams', 'growl', function ($scope, $http, $routeParams, growl) {

//=========================P A R A M E T R O===========================
        $scope.controlId = $routeParams.idControl;
//    ----------------------------------------------------------------

        $scope.controlDeCambios = new Array();
        $scope.cambios = {};
        $scope.cambios.observaciones = "";
        $scope.areaSeleccionada = {};
        $scope.areaSeleccionada.idArea = "";
        $scope.areaSeleccionada.area = "";
        $scope.responsableSeleccionado = {};
        $scope.responsableSeleccionado.idPersonalOM = "";
        $scope.responsableSeleccionado.nombre = "";
        $scope.nombresOM = {};

//==========================================================================
// TRAE INFORMACION PARA ARMAR LA TABLA QUE MUESTRA INFORMACION DEL PERSONAL
//==========================================================================

        $http.post("control/php/traeVistaPrincipalControlCambios.php").success(function (info) {
            $scope.controlDeCambios = info;
        });
//    ----------------------------------------------------------------

//==========================================================================
// TRAE DETALLE DE LA INFORMACION DEL PERSONAL
//==========================================================================

        if ($scope.controlId > 0) {
            $http.get('control/php/informacionDetalladaDelCambio.php?idControl=' + $scope.controlId)
                    .success(function (data) {
                        $scope.cambios = data;
                        $scope.cambios.fecha = new Date(data.fecha);
                        $scope.cambios.fecha.setDate($scope.cambios.fecha.getDate() + 1);
                        $scope.areaSeleccionada = "" + $scope.cambios.idArea + "";
                        $scope.responsableSeleccionado = "" + $scope.cambios.idPersonalOM + "";
                    });
        }

//=====================================================================
// CONSULTAS COMBOS
//=====================================================================
        $scope.listaAreas = {};
        $http.get('personalOaxacaMiel/php/listaAreas.php').success(function (datas) {
            $scope.listaAreas = datas;
        });
//----------------------------------------------------------------------

//=====================================================================
//    FUNCION TRAE PERSONAL
//=====================================================================
        $scope.$watch('areaSeleccionada', function (areaSeleccionada) {
            $http.get('control/php/listaNombresOM.php?idArea=' + areaSeleccionada)
                    .success(function (data) {
                        $scope.nombresOM = data;
                    });
            $scope.responsableSeleccionado = $scope.cambios.idPersonalOM;
        }, true);
//----------------------------------------------------------------------


// ================================================
//   GUARDAR CAMBIO
// ================================================
        $scope.guardarCambio = function () {
            $scope.validaCambio = $scope.validarCambio();
            if ($scope.validaCambio == true) {
                if ($scope.controlId == 0) {
                    $scope.cambios.idArea = $scope.areaSeleccionada;
                    $scope.cambios.idPersonalOM = $scope.responsableSeleccionado;
                    $http.post('control/php/guardarNvoCambio.php', $scope.cambios).success(function () {
                        swal("Exito!", "Registro agregado", "success");
                        $http.post("control/php/traeVistaPrincipalControlCambios.php").success(function (info) {
                            $scope.controlDeCambios = info;
                        });
                        return window.location.href = "#/cambios";
                    });
                } else {
                    $scope.cambios.idArea = $scope.areaSeleccionada;
                    $scope.cambios.idPersonalOM = $scope.responsableSeleccionado;
                    $http.post('control/php/guardarEdicionCambio.php', $scope.cambios).success(function () {
                        swal("Exito!", "Registro agregado", "success");
                        $http.post("control/php/traeVistaPrincipalControlCambios.php").success(function (info) {
                            $scope.controlDeCambios = info;
                        });
                        return window.location.href = "#/cambios";
                    });
                }
            }
        };
        //=================================================================
        //      VALIDAR PERSONAL
        //=================================================================
        $scope.validarCambio = function () {
            $scope.validaCambio = false;
            if ($scope.cambios.fecha == undefined) {
                growl.error("Se requiere una fecha");
            } else if ($scope.areaSeleccionada.idArea == 0) {
                growl.error("Se requiere un área");
            } else if ($scope.cambios.observaciones == "") {
                growl.error("Se requiere llenar el cuadro de texto");
            } else {
                $scope.validaCambio = true;
            }
            return $scope.validaCambio;
        };
    }]);


