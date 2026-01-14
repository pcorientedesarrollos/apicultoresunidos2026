form.controller('clasificacionesCtrl', ['$scope', '$http', '$routeParams', 'growl', '$rootScope', function ($scope, $http, $routeParams, growl, $rootScope) {

//=========================P A R A M E T R O===========================
        $scope.idClas = $routeParams.idClasificacion;
        $scope.idAres = $routeParams.idArea;
        $scope.paramInventario = $routeParams.idOpcion;
//    ----------------------------------------------------------------
        $scope.arrayClasificaciones = new Array();
        $scope.detalleClasificacion = {};
        $scope.arrayDeAreas = new Array();
        $scope.detalleDeAreas = {};
        $scope.arregloSubareas = new Array();
        $scope.opcionInventario = {};
        $scope.lstAreas = {};
        $scope.lstClasificaciones = {};
//==========================================================================
// TRAE INFORMACION 
//==========================================================================

        if ($scope.idClas > 0) {
            $http.get('inventarioDeEquipos/php/detalleClasificacion.php?idClasificacion=' + $scope.idClas).success(function (datas) {
                $scope.detalleClasificacion = datas;
            });

        } else {
            $http.post("inventarioDeEquipos/php/clasificaciones.php").success(function (info) {
                $scope.arrayClasificaciones = info;
            });
        }
        if ($scope.idAres > 0) {
            $http.get('inventarioDeEquipos/php/traeDetalleArea.php?idArea=' + $scope.idAres).success(function (datas) {
                $scope.detalleDeAreas = datas;
                $scope.arregloSubareas = datas.arregloSubareas;
            });
        } else {
            $http.post("personalOaxacaMiel/php/listaAreas.php").success(function (info) {
                console.log(info);
                $scope.arrayDeAreas = info;
            });
        }

        $scope.nuevasSubareas = function () {
            $scope.subarea = {};
            $scope.subarea.idSubarea = 0;
            $scope.subarea.subarea = "";
            $scope.subarea.nombre = "";
            $scope.arregloSubareas.push($scope.subarea);
        };

        $scope.eliminarSubarea = function (indice) {
            $scope.subarea = {};
            $scope.subarea.idSubarea = 0;
            $scope.subarea.subarea = "";
            $scope.subarea.nombre = "";
            $scope.subarea.indice = 0;
            angular.forEach($scope.arregloSubareas, function (value, key) {
                if (key == indice) {
                    $scope.subarea.indice = key;
                    $scope.subarea.idSubarea = value.idSubarea;
                    $scope.subarea.subarea = value.subarea;
                    $scope.subarea.nombre = value.nombre;
                }
            });
            if ($scope.idAres == 0) {
                $scope.arregloSubareas.splice($scope.subarea.indice, 1);
            } else if ($scope.idAres > 0) {
                $http.post("inventarioDeEquipos/php/eliminarSubareas.php?idSubarea=" + $scope.subarea.idSubarea)
                        .success(function (respuesta) {
                            console.log(respuesta);
                            growl.warning("Registro eliminado");
                            $scope.arregloSubareas.splice($scope.subarea.indice, 1);
                        });
            } else {
                console.log("HOLA");
            }
        };

// ================================================
//   GUARDAR 
// ================================================
        $scope.guardarClasificacion = function () {
            if ($scope.idClas == 0) {
                $http.post('inventarioDeEquipos/php/guardarClasificacion.php?clasificacion=' + $scope.detalleClasificacion.clasificacion).success(function (result) {
                    swal("Exito!", "Registro agregado", "success");
                    $http.post("inventarioDeEquipos/php/clasificaciones.php").success(function (info) {
                        $scope.arrayClasificaciones = info;
                        return window.location.href = "#/clasificaciones";
                    });
                });
            } else {
                $http.post('inventarioDeEquipos/php/guardarClasificacion.php', $scope.detalleClasificacion).success(function (result) {
                    swal("Exito!", "Registro agregado", "success");
                    $http.post("inventarioDeEquipos/php/clasificaciones.php").success(function (info) {
                        $scope.arrayClasificaciones = info;
                        return window.location.href = "#/clasificaciones";
                    });
                });
            }
        };
        $scope.guardarNuevaArea = function () {

            if ($scope.detalleDeAreas.area !== undefined) {

                $scope.envioAreasAndSubareas = new Array();
                $scope.envioAreasAndSubareas.push($scope.detalleDeAreas);
                $scope.envioAreasAndSubareas.push($scope.arregloSubareas);
                console.log($scope.envioAreasAndSubareas);
                if ($scope.idAres == 0) {
//                    $http.post('personalOaxacaMiel/php/guardarArea.php?area=' + $scope.detalleDeAreas.area).success(function (result) {
                    $http.post('personalOaxacaMiel/php/guardarArea.php', $scope.envioAreasAndSubareas).success(function (result) {
                        swal("Exito!", "Registro agregado", "success");
                        console.log(result);
                        $http.post("personalOaxacaMiel/php/listaAreas.php").success(function (info) {
                            $scope.arrayDeAreas = info;
                            return window.location.href = "#/areas";
                        });
                    });
                } else {
                    $http.post('personalOaxacaMiel/php/guardarArea.php', $scope.envioAreasAndSubareas).success(function (result) {
                        swal("Exito!", "Registro agregado", "success");
                        console.log(result);
                        $http.post("personalOaxacaMiel/php/listaAreas.php").success(function (info) {
                            $scope.arrayDeAreas = info;
                            return window.location.href = "#/areas";
                        });
                    });
                }
            } else {
                growl.error("Se requiere llenar el campo de área");
            }
        };

//        $scope.$watch('opcionInventario.option', function () {
//            $scope.opcionInventario.areaInventario = "";
//            $scope.opcionInventario.tipoClasificacion = "";
//        });
        if ($scope.paramInventario != 0) {
            $http.get('personalOaxacaMiel/php/listaAreas.php').success(function (datas) {
                $scope.lstAreas = datas;
            });
            $http.post("inventarioDeEquipos/php/clasificaciones.php").success(function (info) {
                $scope.lstClasificaciones = info;
            });
        }
        $scope.enviarInventario = function (option, idOpcion, areaInventario, tipoClasificacion) {

            if (option == "1") { // BALANCE CONTABLE
                switch (idOpcion) {
                    case "3":
                        $http.get("inventarioDeEquipos/php/traeBalanceContable.php").success(function (data) {
                            $rootScope.inventarios = data;
                            $rootScope.inventarios.costosTotales = 0;
                            angular.forEach($rootScope.inventarios, function (value) {
                                $rootScope.inventarios.costosTotales += parseInt(value.costoTotal);
                            });
                            $rootScope.title = "Concentrado";
                            $rootScope.granTotal = $rootScope.inventarios.costosTotales;
                            $rootScope.parametroID = idOpcion;
                        });
                        break;
                    case "4":
                        $http.get("inventarioDeEquipos/php/detalleClasificacion.php?idClasificacion=" + tipoClasificacion).success(function (data) {
                            $rootScope.title = data.clasificacion;
                        });
                        $http.get("inventarioDeEquipos/php/traeBalanceContable.php?idClasificacion=" + tipoClasificacion).success(function (data) {
                            $rootScope.inventarios = data;
                            $rootScope.inventarios.costosTotales = 0;
                            angular.forEach($rootScope.inventarios, function (value) {
                                $rootScope.inventarios.costosTotales += parseInt(value.costo);
                            });
                            $rootScope.granTotal = $rootScope.inventarios.costosTotales;
                            $rootScope.parametroID = idOpcion;
                            $rootScope.parametroReporte = tipoClasificacion;
                        });
                        break;
                    case "5":
                        $http.get('inventarioDeEquipos/php/traeDetalleArea.php?idArea=' + areaInventario).success(function (datas) {
                            $rootScope.title = datas.area;
                        });
                        $http.get("inventarioDeEquipos/php/traeBalanceContable.php?idArea=" + areaInventario).success(function (data) {
                            $rootScope.inventarios = data;
                            $rootScope.inventarios.costosTotales = 0;
                            angular.forEach($rootScope.inventarios, function (value) {
                                $rootScope.inventarios.costosTotales += parseInt(value.costo);
                            });
                            $rootScope.granTotal = $rootScope.inventarios.costosTotales;
                            $rootScope.parametroID = idOpcion;
                            $rootScope.parametroReporte = areaInventario;
                        });
                        break;
                }

            } else if (option == "2") {

                switch (idOpcion) {
                    case "6":
                        $http.get('inventarioDeEquipos/php/traeDetalleArea.php?idArea=' + areaInventario).success(function (datas) {
                            $rootScope.title = datas.area;
                        });
                        $http.get("inventarioDeEquipos/php/traeInventario.php?idArea=" + areaInventario).success(function (data) {
                            $rootScope.inventarios = data;
                            $rootScope.parametroID = idOpcion;
                            $rootScope.parametroReporte = areaInventario;
                        });
                        break;
                    case "7":
                        $http.get("inventarioDeEquipos/php/detalleClasificacion.php?idClasificacion=" + tipoClasificacion).success(function (data) {
                            $rootScope.title = data.clasificacion;
                        });
                        $http.get("inventarioDeEquipos/php/traeInventario.php?idClasificacion=" + tipoClasificacion).success(function (data) {
                            $rootScope.inventarios = data;
                            $rootScope.parametroID = idOpcion;
                            $rootScope.parametroReporte = tipoClasificacion;
                            console.log($rootScope.parametroReporte);
                        });
                        break;
                    case "8":
                        $http.get("inventarioDeEquipos/php/traeInventario.php").success(function (data) {
                            $rootScope.title = "General";
                            $rootScope.inventarios = data;
                            $rootScope.parametroID = idOpcion;
                        });
                        break;
                }
            } else {
                console.log(option);
            }
        };


        $scope.balancePDF = function (parametroID, parametroReporte) {
            console.log(parametroID, parametroReporte);
            switch (parametroID) {
                case "3":
                    window.open('reportes/inventarioDeEquipos/balanceContable.php?opcion=todo', '_blank');
                    break;
                case "4":
                    window.open("reportes/inventarioDeEquipos/balanceContable.php?opcion=clasificacion&idClasificacion=" + parametroReporte, '_blank');
                    break;
                case "5":
                    window.open("reportes/inventarioDeEquipos/balanceContable.php?opcion=area&idArea=" + parametroReporte, '_blank');
                    break;
            }
        };

        $scope.inventarioPDF = function (parametroID, parametroReporte) {
            console.log(parametroID, parametroReporte);
            switch (parametroID) {
                case "6":
                    window.open("reportes/inventarioDeEquipos/dameInventario.php?opcion=area&idArea=" + parametroReporte, '_blank');
                    break;
                case "7":
                    window.open("reportes/inventarioDeEquipos/dameInventario.php?opcion=clasificacion&idClasificacion=" + parametroReporte, '_blank');
                    break;
                case "8":
                    window.open("reportes/inventarioDeEquipos/dameInventario.php?opcion=todo", '_blank');
                    break;
            }
        };

    }]);