form.controller('equiposCtrl', ['$scope', '$http', 'growl', '$routeParams', function ($scope, $http, growl, $routeParams) {

    //=========================P A R A M E T R O===========================
    $scope.equipoParam = $routeParams.idEquipo;
    $scope.areaParametro = $routeParams.idArea;
    $scope.idVista = $routeParams.id;
    //    ----------------------------------------------------------------

    $scope.menuEquipos = new Array();
    $scope.equiposSinMantto = new Array();
    $scope.verAreas = new Array();
    $scope.equipo = {};
    $scope.equipo.codigo = "";
    $scope.listaDeAreas = {};
    $scope.listaPersonalOM = {};
    $scope.areaEquipo = {};
    $scope.areaEquipo.idArea = "";
    $scope.areaEquipo.area = "";
    $scope.areaNueva = {};
    $scope.areaNueva.idArea = "";
    $scope.areaNueva.area = "";
    $scope.autorizoMovimiento = {};
    $scope.autorizoMovimiento.idPersonalOM = "";
    $scope.autorizoMovimiento.nombre = "";
    $scope.entregoEquipo = {};
    $scope.entregoEquipo.idPersonalOM = "";
    $scope.entregoEquipo.nombre = "";
    $scope.recibeEquipo = {};
    $scope.recibeEquipo.idPersonalOM = "";
    $scope.recibeEquipo.nombre = "";
    $scope.clasificaEquipo = {};
    $scope.clasificaEquipo.idClasificacion = "";
    $scope.clasificaEquipo.clasificacion = "";
    $scope.subareaEquipo = {};
    $scope.subareaEquipo.idSubarea = "";
    $scope.subareaEquipo.zona = "";
    $scope.subareaNueva = {};
    $scope.subareaNueva.idSubarea = "";
    $scope.subareaNueva.zona = "";
    $scope.opcionMantto = {};
    $scope.historial = {};
    $scope.historiales = new Array();
    $scope.lstSubareas = {};

    $scope.moverEquipo = function () {
        //            if ($scope.equipo.idSubarea > 0) {
        $("#cambiarAreaEquipoSub").modal();
        //            } else {
        //                $("#cambiarAreaEquipo").modal();
        //            }
    };

    if ($scope.equipoParam > 0) {
        $http.post("inventarioDeEquipos/php/traeEquipo.php?idEquipo=" + $scope.equipoParam).success(function (data) {
            $scope.equipo = data;
            $scope.areaEquipo = "" + $scope.equipo.idArea + "";
            $scope.opcionMantto.opcion = "" + $scope.equipo.mantto + "";
            $scope.opcionMantto.periodicidad = "" + $scope.equipo.periodicidad + "";
            $scope.clasificaEquipo = "" + $scope.equipo.idClasificacion + "";
            $scope.subareaEquipo = "" + $scope.equipo.idSubarea + "";
        });
        $http.get('personalOaxacaMiel/php/listaAreas.php').success(function (datas) {
            $scope.listaDeAreas = datas;
        });
        $http.post("json/controlMantto/periodos.json").success(function (datos) {
            $scope.listaPeriodos = datos.periodos;
        });
        $http.get('controlMantenimiento/php/listaPersonalOM.php').success(function (datas) {
            $scope.listaPersonalOM = datas;
        });
        $http.post("inventarioDeEquipos/php/traeHistoriales.php?idEquipo=" + $scope.equipoParam).success(function (data) {
            $scope.historiales = data;
            console.log(data);
        });
        $http.get("inventarioDeEquipos/php/clasificaciones.php").success(function (datos) {
            $scope.clasificacionesLista = datos;
        });
        $scope.$watch('areaNueva', function (areaNueva) {
            $http.get("inventarioDeEquipos/php/verificaSiExitenSubareas.php?idArea=" + areaNueva).success(function (respuesta) {
                if (respuesta == 1) {
                    $http.get("inventarioDeEquipos/php/listaDeSubareas.php?idArea=" + areaNueva).success(function (datos) {
                        $scope.lstSubareas = datos;
                    });
                }
            });
            $scope.subareaNueva = "";
            $scope.lstSubareas = "";
        });
    } else {
        $http.post("json/controlMantto/periodos.json").success(function (datos) {
            $scope.listaPeriodos = datos.periodos;
        });
        $http.post("inventarioDeEquipos/php/menuVistaEquipos.php").success(function (data) {
            $scope.verAreas = data;
        });
        $http.get('personalOaxacaMiel/php/listaAreas.php').success(function (datas) {
            $scope.listaDeAreas = datas;
        });
        $http.get("inventarioDeEquipos/php/clasificaciones.php").success(function (datos) {
            $scope.clasificacionesLista = datos;
        });
        $scope.$watch('areaEquipo', function (areaEquipo) {
            $http.get("inventarioDeEquipos/php/verificaSiExitenSubareas.php?idArea=" + areaEquipo).success(function (respuesta) {
                $scope.respuesta = respuesta;
                if (respuesta == 1) {
                    $http.get("inventarioDeEquipos/php/listaDeSubareas.php?idArea=" + areaEquipo).success(function (datos) {
                        $scope.lstSubareas = datos;
                    });
                }
            });
        });
    }

    if ($scope.idVista == 1) {
        $http.post("inventarioDeEquipos/php/traeMenuEquipos.php?idArea=" + $scope.areaParametro).success(function (data) {
            $scope.nombreArea = data.area;
            $scope.menuEquipos = data.equipos;
        });
    }

    if ($scope.idVista == 2) {
        $http.post("inventarioDeEquipos/php/traeMenuEquipos.php?sinMantto=0" + "&idArea=" + $scope.areaParametro).success(function (data) {
            $scope.nombreArea = data.area;
            $scope.equiposSinMantto = data.equipos;
        });
    }

    $scope.guardarEquipo = function () {
        $scope.okEquipo = $scope.validarEquipo();
        if ($scope.okEquipo == true) {
            $scope.equipo.idArea = $scope.areaEquipo;
            $scope.equipo.mantto = $scope.opcionMantto.opcion;
            $scope.equipo.periodicidad = $scope.opcionMantto.periodicidad;
            $scope.equipo.idClasificacion = $scope.clasificaEquipo;
            if ($scope.equipo.periodicidad == null) {
                $scope.equipo.periodicidad = '0';
            }
            if ($scope.subareaEquipo > 0) {
                $scope.equipo.idSubarea = $scope.subareaEquipo;
            } else {
                $scope.equipo.idSubarea = '0';
            }

            if ($scope.equipoParam > 0) {
                $http.post("inventarioDeEquipos/php/guardarEquipo.php?idEquipo=" + $scope.equipoParam, $scope.equipo).success(function (daatos) {
                    swal("Exito!", "Registro agregado", "success");

                    $http.post("inventarioDeEquipos/php/traeMenuEquipos.php?todos=0" + "&idArea=" + $scope.equipo.idArea).success(function (data) {
                        $scope.nombreArea = data.area;
                        $scope.menuEquipos = data.equipos;
                    });
                    return window.location.href = "#/verLosEquipos/" + $scope.equipo.idArea;
                });
            } else {
                $http.post("inventarioDeEquipos/php/verificarCodigoEquipo.php?codigo=" + $scope.equipo.codigo).success(function (respuesta) {
                    if (respuesta == 1) {
                        swal("Error!", "Verifique! Código de equipo duplicado", "error");
                    } else {
                        $http.post("inventarioDeEquipos/php/guardarEquipo.php", $scope.equipo).success(function (info) {
                            swal("Exito!", "Registro agregado", "success");
                            return window.location.href = "#/verLosEquipos/" + $scope.equipo.idArea;


                            //                                if ($scope.equipo.mantto == 1) {
                            //                                    $http.post("inventarioDeEquipos/php/traeMenuEquipos.php?sinMantto=0" + "&idArea=" + $scope.equipo.idArea).success(function (data) {
                            //                                        $scope.nombreArea = data.area;
                            //                                        $scope.equiposSinMantto = data.equipos;
                            //                                    });
                            //                                    return window.location.href = "#/sinManttos/2/" + $scope.equipo.idArea;
                            //                                } else {
                            //                                    $http.post("inventarioDeEquipos/php/traeMenuEquipos.php?idArea=" + $scope.equipo.idArea).success(function (data) {
                            //                                        $scope.nombreArea = data.area;
                            //                                        $scope.menuEquipos = data.equipos;
                            //                                    });
                            //                                    return window.location.href = "#/verEquipos/1/" + $scope.equipo.idArea;
                            //                                }

                        });
                    }
                });
            }
        }

    };
    $scope.validarEquipo = function () {
        $scope.okEquipo = false;
        if ($scope.areaEquipo.idArea == "") {
            growl.error("Se requiere un área");
        } else {
            $scope.okEquipo = true;
        }
        return $scope.okEquipo;
    };
    $scope.guardarHistorial = function () {
        $scope.historial.areaAntesCambio = $scope.equipo.idArea;
        $scope.historial.areaDespuesCambio = $scope.areaNueva;
        $scope.historial.subareaAntesCambio = $scope.equipo.idSubarea;
        //            $scope.historial.subareaDespuesCambio = $scope.subareaNueva;
        $scope.historial.autoriza = $scope.autorizoMovimiento.idPersonalOM;
        $scope.historial.recibe = $scope.recibeEquipo.idPersonalOM;
        $scope.historial.entrega = $scope.entregoEquipo.idPersonalOM;
        $scope.historial.idEquipo = $scope.equipoParam;

        if ($scope.subareaNueva > 0) {
            $scope.historial.subareaDespuesCambio = $scope.subareaNueva;
        } else {
            $scope.historial.subareaDespuesCambio = '0';
        }

        $http.post("inventarioDeEquipos/php/guardarCambio.php", $scope.historial).success(function () {
            swal("Exito!", "Registro agregado", "success");
            //                $http.post("controlMantenimiento/php/traeMenuEquipos.php").success(function (data) {
            //                    $scope.menuEquipos = data;
            //                });

            $("#cambiarAreaEquipoSub").modal('hide');
            $scope.historial = {};
            $scope.areaNueva = "";
            $scope.autorizoMovimiento = "";
            $scope.recibeEquipo = "";
            $scope.entregoEquipo = "";
            $scope.subareaNueva = "";
        });
        //        
    };

    $scope.verHistorial = function () {
        $scope.mostrarHistorial = false;
        $http.post("inventarioDeEquipos/php/verificarSiHayMovimientos.php?idEquipo=" + $scope.equipoParam).success(function (respuesta) {
            if (respuesta == 1) {
                $scope.mostrarHistorial = true;
            } else {
                swal({
                    title: "Lo sentimos",
                    text: "Aún no hay movimientos registrados de este artículo!",
                    type: "warning",
                    //                        confirmButtonColor: "#DD6B55",
                    confirmButtonColor: "#BED9E5",
                    closeOnConfirm: false
                });
            }
        });
    };
    $scope.cerrarCuadro = function () {
        $scope.mostrarHistorial = false;
    };
}]);
form.filter('trusted', ['$sce', function ($sce) {
    var div = document.createElement('div');
    return function (text) {
        div.innerHTML = text;
        return $sce.trustAsHtml(div.textContent);
    };
}]);
