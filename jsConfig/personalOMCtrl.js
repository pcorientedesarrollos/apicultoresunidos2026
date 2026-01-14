form.controller('personalOMCtrl', ['$scope', '$http', '$routeParams', 'growl', '$location', function ($scope, $http, $routeParams, growl, $location) {

    //=========================P A R A M E T R O===========================
    $scope.oaxacaMiel = $routeParams.idPersonalOM;
    //    ----------------------------------------------------------------

    $scope.selectArea = {};
    $scope.selectArea.idArea = "";
    $scope.selectArea.area = "";
    $scope.selectPuesto = {};
    $scope.selectPuesto.idPuesto = "";
    $scope.selectPuesto.puesto = "";
    $scope.personalOaxaca = {};
    $scope.espacio = "";
    $scope.empleo = "";
    $scope.personalOM = new Array();
    $scope.listaAreas = {};
    $scope.listaPuestos = {};
    $scope.estadoPersonal = '1';
    $scope.periodos = new Array();

    //==========================================================================
    // TRAE INFORMACION PARA ARMAR LA TABLA QUE MUESTRA INFORMACION DEL PERSONAL
    //==========================================================================
    if ($location.path() == '/personal') {
        $scope.$watch('estadoPersonal', function (val) {
            switch (val) {
                case '1':
                    $http.post("personalOaxacaMiel/php/traePersonal.php?activos=1").success(function (info) {
                        $scope.personalOM = info;
                    });
                    break;
                case '2':
                    $http.post("personalOaxacaMiel/php/traePersonal.php?inactivos=1").success(function (info) {
                        $scope.personalOM = info;
                    });
                    break;
                case '3':
                    $http.post("personalOaxacaMiel/php/traePersonal.php").success(function (info) {
                        $scope.personalOM = info;
                    });
                    break;
            }
        }, true);
    }
    //    ----------------------------------------------------------------

    $scope.activarPersonal = function (idPersonal) {
        swal({
            title: "",
            text: "¿Desea cambiar el estado de ésta persona?",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#DD6B55",
            confirmButtonText: "Sí, activar.",
            closeOnConfirm: false
        },
            function () {
                $http.post('personalOaxacaMiel/php/cambiarEstado.php?estado=0&idPersonal=' + idPersonal).success(function (info) {
                    $http.post("personalOaxacaMiel/php/traePersonal.php?activos=1").success(function (info) {
                        $scope.personalOM = info;
                    });
                    $scope.estadoPersonal = '1';
                });
                swal("Enviado!", "El estado cambió", "success");
            });
    };

    $scope.desactivarPersonal = function (idPersonal) {
        swal({
            title: "",
            text: "¿Desea cambiar el estado de ésta persona?",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#DD6B55",
            confirmButtonText: "Sí, desactivar.",
            closeOnConfirm: false
        },
            function () {
                $http.post('personalOaxacaMiel/php/cambiarEstado.php?estado=1&idPersonal=' + idPersonal).success(function (info) {
                    $http.post("personalOaxacaMiel/php/traePersonal.php?inactivos=1").success(function (info) {
                        $scope.personalOM = info;
                    });
                    $scope.estadoPersonal = '2';
                });
                swal("", "El estado cambió", "success");
            });
    };

    $scope.nuevoPeriodo = function () {
        $scope.periodo = {};
        $scope.periodo.idPeriodo = 0;
        $scope.periodo.fechaUno = "";
        $scope.periodo.fechaDos = "";
        $scope.periodo.idPersonal = '';
        $scope.periodos.push($scope.periodo);
    };

    $scope.eliminarPeriodo = function (indice) {
        $scope.periodo = {};
        $scope.periodo.idPeriodo = 0;
        $scope.periodo.fechaUno = "";
        $scope.periodo.fechaDos = "";
        $scope.periodo.idPersonal = '';
        $scope.periodo.indice = 0;
        angular.forEach($scope.periodos, function (value, key) {
            if (key == indice) {
                $scope.periodo.indice = key;
                $scope.periodo.idPeriodo = value.idPeriodo;
                $scope.periodo.fechaUno = value.fechaUno;
                $scope.periodo.fechaDos = value.fechaDos;
                $scope.periodo.idPersonal = value.idPersonal;
            }
        });
        $scope.periodos.splice($scope.periodo.indice, 1);
    };

    //==========================================================================
    // TRAE DETALLE DE LA INFORMACION DEL PERSONAL
    //==========================================================================

    if ($scope.oaxacaMiel > 0) {
        $http.get('personalOaxacaMiel/php/listaAreas.php').success(function (datas) {
            $scope.listaAreas = datas;
        });

        $http.get('personalOaxacaMiel/php/listaPuestos.php').success(function (datos) {
            $scope.listaPuestos = datos;
        });
        $http.get('personalOaxacaMiel/php/infoPersonalOM.php?idPersonalOM=' + $scope.oaxacaMiel).success(function (data) {
            $scope.personalOaxaca = data;
            $scope.selectArea = "" + $scope.personalOaxaca.idArea + "";
            $scope.selectPuesto = "" + $scope.personalOaxaca.idPuesto + "";
            $scope.periodos = data.periodos;
        });
    } else {
        $http.get('personalOaxacaMiel/php/listaAreas.php').success(function (datas) {
            $scope.listaAreas = datas;
        });

        $http.get('personalOaxacaMiel/php/listaPuestos.php').success(function (datos) {
            $scope.listaPuestos = datos;
        });
    }
    ;



    //=====================================================================
    // MODALES
    //=====================================================================
    $scope.modalArea = function () {
        $("#modalArea").modal();
    };

    $scope.modalPuesto = function () {
        $("#modalPuesto").modal();
    };
    //----------------------------------------------------------------------


    // ================================================
    //   GUARDAR PERSONAL DE OAXACA MIEL
    // ================================================
    $scope.guardarPersonal = function () {
        $scope.validaP = $scope.validarPersonal();
        if ($scope.validaP == true) {
            $scope.datos = new Array();
            $scope.datos.push($scope.personalOaxaca);
            $scope.datos.push($scope.periodos);
            if ($scope.oaxacaMiel == 0) {
                $http.post("personalOaxacaMiel/php/verificarClave.php?clave=" + $scope.personalOaxaca.clave).success(function (respuesta) {
                    if (respuesta == 1) {
                        swal("Error!", "Verifique! Clave duplicada", "error");
                    } else {
                        $scope.personalOaxaca.idArea = $scope.selectArea;
                        $scope.personalOaxaca.idPuesto = $scope.selectPuesto;
                        $http.post('personalOaxacaMiel/php/guardarPersonalPeriodos.php', { valor: $scope.datos }).success(function (result) {
                            if (result.error) {
                                swal("", "Ocurrió un error", "error");
                            } else {
                                swal("", "Nuevos datos registrados", "success");
                                return window.location.href = "#/personal";
                            }
                        });
                    }
                });

            } else {
                $scope.personalOaxaca.idArea = $scope.selectArea;
                $scope.personalOaxaca.idPuesto = $scope.selectPuesto;
                $http.post('personalOaxacaMiel/php/editarPersonalPeriodos.php', { valor: $scope.datos }).success(function (result) {
                    if (result.error) {
                        swal("", "Ocurrió un error", "info");
                    } else {
                        swal("", "Se han guardado los datos", "success");
                        $http.post("personalOaxacaMiel/php/traePersonal.php").success(function (info) {
                            $scope.personalOM = info;
                        });
                        return window.location.href = "#/personal";
                    }
                });
            }
        }
    };
    //=================================================================
    //      VALIDAR PERSONAL
    //=================================================================
    $scope.validarPersonal = function () {
        $scope.validaP = false;
        if ($scope.personalOaxaca.nombres == undefined) {
            growl.error("Se requiere un nombre");
        } else if ($scope.personalOaxaca.clave == undefined) {
            growl.error("Se requiere una clave");
        } else if ($scope.personalOaxaca.apellido_paterno == undefined) {
            growl.error("Se requiere apellido paterno");
        } else if ($scope.personalOaxaca.apellido_materno == undefined) {
            growl.error("Se requiere apellido materno");
        } else if ($scope.selectArea.idArea == 0) {
            growl.error("Se requiere un area");
        } else if ($scope.selectPuesto.idPuesto == 0) {
            growl.error("Se requiere un puesto");
        } else {
            $scope.validaP = true;
        }
        return $scope.validaP;
    };


    $scope.guardarArea = function () {
        $scope.validA = $scope.validarArea();
        if ($scope.validA == true) {
            $http.post('personalOaxacaMiel/php/guardarArea.php?area=' + $scope.espacio).success(function (result) {
                $http.get('personalOaxacaMiel/php/listaAreas.php').success(function (datas) {
                    $scope.listaAreas = datas;
                });
            });
            ;
            swal("Exito!", "Nueva area disponible", "success");
            $("#modalArea").modal('hide');
        }
    };

    $scope.guardarPuesto = function () {
        $scope.validaPu = $scope.validarPuesto();
        if ($scope.validaPu == true) {
            $http.post('personalOaxacaMiel/php/guardarPuesto.php?puesto=' + $scope.empleo).success(function (result) {
                $http.get('personalOaxacaMiel/php/listaPuestos.php').success(function (datos) {
                    $scope.listaPuestos = datos;
                });
            });
            ;
            swal("Exito!", "Nuevo puesto disponible", "success");
            $("#modalPuesto").modal('hide');
        }
    };

    //=================================================================
    //      VALIDAR AREA
    //=================================================================
    $scope.validarArea = function () {
        $scope.validA = false;
        if ($scope.espacio == "") {
            growl.error("Se requiere un area");
        } else {
            $scope.validA = true;
        }
        return $scope.validA;
    };

    //=================================================================
    //      VALIDAR PUESTO
    //=================================================================
    $scope.validarPuesto = function () {
        $scope.validaPu = false;
        if ($scope.empleo == "") {
            growl.error("Se requiere un puesto");
        } else {
            $scope.validaPu = true;
        }
        return $scope.validaPu;
    };


}]);


