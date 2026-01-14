form.controller('trazabilidadSalidaCtrl', ['$scope', '$http', '$routeParams', 'growl', function ($scope, $http, $routeParams, growl) {

    //========================= P A R A M E T R O ===========================
    $scope.parametroSalida = $routeParams.idLoteInterno;
    $scope.parametroTipoMiel = $routeParams.tipoMiel;
    //-----------------------------------------------------------------------


    $scope.menuTrazabilidadSalida = new Array();
    $scope.comboSalida = 0;
    $scope.encabezadoSalida = {};
    $scope.detalleTrazabilidadSalida = new Array();
    $scope.comboDestino = {};
    $scope.comboDestino.idEmpresaPais = "";
    $scope.comboDestino.destino = "";
    $scope.aDestino = {};
    if ($scope.parametroSalida > 0) {
        $http.get('trazabilidad/php/idLoteTrazabilidadSalida.php?idLoteInterno=' + $scope.parametroSalida + '&miel=' + $scope.parametroTipoMiel).success(function (data) {
            console.log(data);
            $scope.encabezadoSalida.tipoMiel = data.tipoMiel;
            $scope.encabezadoSalida.idLoteInterno = data.idLoteInterno;
            $scope.encabezadoSalida.homogeneizado = data.homogeneizado;
            $scope.encabezadoSalida.kilosSalida = data.kilosSalida;
            $scope.comboDestino = "" + data.idEmpresaPais + "";
            $scope.encabezadoSalida.kgExportar = data.kgExportar;
            $scope.encabezadoSalida.fechaEnvasado = data.fechaEnvasado;
            //                $scope.encabezadoSalida.fechaSalida = new Date(data.fechaSalida);
            //                $scope.encabezadoSalida.fechaSalida.setDate($scope.encabezadoSalida.fechaSalida.getDate() + 1);
            $scope.encabezadoSalida.fechaSalida = data.fechaSalida;
            $scope.kilosTotales = data.kilosTotales;
            $scope.detalleTrazabilidadSalida = data.detalleTrazabilidadSalida;
        });
        $http.get('trazabilidad/php/traeComboDestinos.php').success(function (datas) {
            $scope.comboDestinos = datas;
        });

    } else if ($scope.parametroSalida == 0) {

        traerListaDeLotes($scope.parametroTipoMiel);

        $http.get('trazabilidad/php/traeComboDestinos.php').success(function (dato) {
            $scope.comboDestinos = dato;
        });

    } else {

        // Comprobar la selección del tipo de miel

        if (window.localStorage.getItem('seleccionMielTrazabilidad') != null) {
            $scope.opcionMiel = window.localStorage.getItem('seleccionMielTrazabilidad');
        } else {
            window.localStorage.removeItem('seleccionMielTrazabilidad');
        }

        // watch cambios en la selección de miel

        $scope.$watch('opcionMiel', function (opcion) {
            if (opcion) {
                obtenerResultadosTabla(opcion);
                window.localStorage.setItem('seleccionMielTrazabilidad', opcion);
            }
        });

    }

    // trae la lista de lotes de acuerdo al tipo de miel para crear

    function traerListaDeLotes(tipoDeMiel) {
        $scope.lotesSalida = [];
        if (tipoDeMiel) {
            $http.get('trazabilidad/php/listaDeLotesSalida.php?miel=' + tipoDeMiel).success(function (datas) {
                $scope.lotesSalida = datas;
            });
        }
    }

    // Obtiene la lista de lotes de la tabla
    function obtenerResultadosTabla(tipoDeMiel) {
        $scope.menuTrazabilidadSalida = [];
        if (tipoDeMiel) {
            $http.get("trazabilidad/php/traeMenuTrazabilidadSalida.php?miel=" + tipoDeMiel).success(function (data) {
                if (data.error) {
                    growl.error(data.message);
                } else {
                    $scope.menuTrazabilidadSalida = data.data;
                }
            });
        }
    }

    $scope.concentradoSalida = function () {

        $http.get('trazabilidad/php/idLoteTrazabilidadSalida.php?idLoteInterno=' + $scope.comboSalida + '&miel=' + $scope.parametroTipoMiel).success(function (data) {

            //                if (data.paso == 1) {
            //                    $scope.encabezadoSalida.fechaEnvasado = data.fechaEnvasado;
            //                    $scope.encabezadoSalida.fechaSalida = data.fechaSalida;
            //                    $scope.kilosTotales = data.kilosTotales;
            //                    $scope.detalleTrazabilidadSalida = data.detalleTrazabilidadSalida;
            //                    console.log(data);
            //                } else {
            //                    swal("Marca final no asignada!", "Revise reportes de carga", "error");
            //                    $scope.encabezadoSalida.fechaEnvasado = "";
            //                    $scope.encabezadoSalida.fechaSalida = "";
            //                    $scope.kilosTotales = "";
            //                    $scope.comboSalida = "";
            //                }

            $scope.encabezadoSalida.fechaEnvasado = data.fechaEnvasado;
            $scope.encabezadoSalida.fechaSalida = data.fechaSalida;
            $scope.kilosTotales = data.kilosTotales;
            $scope.detalleTrazabilidadSalida = data.detalleTrazabilidadSalida;
            console.log(data);

        });

    };

    $scope.copiarKg = function (kgExportar) {
        $scope.encabezadoSalida.kilosSalida = angular.copy(kgExportar);
    };



    $scope.guardarTrazabilidadSalida = function () {

        if ($scope.parametroSalida == 0) {

            $scope.encabezadoSalida.tipoMiel = $scope.parametroTipoMiel;
            $scope.encabezadoSalida.idLoteInterno = $scope.comboSalida;
            $scope.encabezadoSalida.idEmpresaPais = $scope.comboDestino;
            $http.post("trazabilidad/php/guardarTrazabilidadSalida.php", $scope.encabezadoSalida).success(function (info) {
                console.log(info);
                swal("Éxito", "Guardado", "success");
                return window.location.href = "#/tSalida";
            });
        } else {
            $scope.encabezadoSalida.tipoMiel = $scope.parametroTipoMiel;
            $scope.encabezadoSalida.idEmpresaPais = $scope.comboDestino;
            $http.post("trazabilidad/php/editarTrazabilidadSalida.php?idLoteInterno=" + $scope.parametroSalida, $scope.encabezadoSalida).success(function (respuesta) {
                console.log(respuesta);
                swal("Éxito", "Actualizado", "success");
                return window.location.href = "#/tSalida";
            });
        }
    };


    $scope.nuevoDestino = function () {
        $("#modalDestino").modal();
    };
    $scope.guardarDestino = function () {
        if ($scope.aDestino.empresa != null && $scope.aDestino.pais != null) {
            $http.post("trazabilidad/php/guardarNuevoDestino.php", $scope.aDestino).success(function (data) {
                $http.get('trazabilidad/php/traeComboDestinos.php').success(function (dato) {
                    $scope.comboDestinos = dato;
                });
            });
            swal("Éxito!", "Nueva opción disponible", "success");
            $("#modalDestino").modal('hide');
            $scope.aDestino = "";
        } else {
            growl.warning("Se requiere tener los campos llenos");
        }
    };



    // $scope.editarTrazabilidadSalida = function () {
    //     $("#mdlEditarTrazabilidadSalida").modal();
    // };
    // $scope.guardarEdicionSalida = function () {
    //     $scope.encabezadoSalida.idEmpresaPais = $scope.comboDestino;
    //     $http.post("trazabilidad/php/editarTrazabilidadSalida.php?idLoteInterno=" + $scope.parametroSalida, $scope.encabezadoSalida)
    //         .success(function (respuesta) {
    //             swal(respuesta.encabezado, respuesta.mensaje, respuesta.tipo);
    //             $http.get('trazabilidad/php/idLoteTrazabilidadSalida.php?idLoteInterno=' + $scope.parametroSalida).success(function (data) {
    //                 $scope.encabezadoSalida.idLoteInterno = data.idLoteInterno;
    //                 $scope.encabezadoSalida.homogeneizado = data.homogeneizado;
    //                 $scope.encabezadoSalida.kilosSalida = data.kilosSalida;
    //                 $scope.comboDestino = "" + data.destino + "";
    //                 $scope.encabezadoSalida.kgExportar = data.kgExportar;
    //                 $scope.encabezadoSalida.fechaEnvasado = data.fechaEnvasado;
    //                 $scope.encabezadoSalida.fechaSalida = data.fechaSalida;
    //                 $scope.detalleTrazabilidadSalida = data.detalleTrazabilidadSalida;

    //                 if ($scope.encabezadoSalida.homogeneizado == 0) {
    //                     $scope.encabezadoSalida.homogeneizado = "No";
    //                 } else {
    //                     $scope.encabezadoSalida.homogeneizado = "Sí";
    //                 }

    //             });
    //             $("#mdlEditarTrazabilidadSalida").modal('hide');
    //         });
    // };


    //-------------------------------------------------------------------------

    $scope.pdfTramzabilidadSalida = function () {
        console.log($scope.parametroTipoMiel);
        if ($scope.parametroTipoMiel) {
            window.open('reportes/trazabilidad/pdfTranzabilidadSalida.php?idLoteInterno=' + $scope.parametroSalida + '&miel=' + $scope.parametroTipoMiel, '_blank');
        } else {
            growl.info('El tipo de miel no está definido.');
        }
    };


    $scope.xlsTranzabilidadSalida = function () {
        if ($scope.parametroTipoMiel) {
            return window.location.href = "reportes/trazabilidad/xlsTranzabilidadSalida.php?idLoteInterno=" + $scope.parametroSalida + "&miel=" + $scope.parametroTipoMiel;
        } else {
            growl.info('El tipo de miel no está definido.');
        }
    };

    $scope.agregarTrazabilidadSalida = function () {
        if ($scope.opcionMiel) {
            window.location.href = "#/nvaTrazaSalida/0/" + $scope.opcionMiel;
        } else {
            growl.info('Selecciona el tipo de miel.');
        }
    }
}]);


