form.controller('trazabilidadAnalisisCtrl', ['$scope', '$http', '$routeParams', 'growl', function ($scope, $http, $routeParams, growl) {

    //========================= P A R A M E T R O ===========================
    $scope.parametroAnalisis = $routeParams.idLoteInterno;
    $scope.tipoMiel = $routeParams.tipoMiel;
    //-----------------------------------------------------------------------


    $scope.menuTrazabilidadLaboratorio = new Array();
    $scope.comboAnalisis = 0;
    $scope.encabezadoAnalisis = {};
    $scope.detalleAnalisis = new Array();

    $scope.opcionCosechaC = 0;

    if ($scope.parametroAnalisis > 0) {
        $http.get('trazabilidad/php/traeDetalleTrazabilidadAnalisis.php?tipoMiel=' + $scope.tipoMiel + '&idLoteInterno=' + $scope.parametroAnalisis).success(function (data) {
            $scope.encabezadoAnalisis.idLoteInterno = data.idLoteInterno;
            $scope.encabezadoAnalisis.nombreLaboratorio = data.nombreLaboratorio;
            $scope.encabezadoAnalisis.fechaProtocolo = data.fechaProtocolo;
            $scope.encabezadoAnalisis.folioProtocolo = data.folioProtocolo;
            $scope.encabezadoAnalisis.marcaFinalCliente = data.marcaFinalCliente;
            $scope.encabezadoAnalisis.tipoMiel = data.tipoMiel;
            $scope.detalleAnalisis = data.detalleAnalisis;
        });
    } else if ($scope.parametroAnalisis == 0) {
        $http.get('trazabilidad/php/listaDeLotes.php?tipoMiel=' + $scope.tipoMiel).success(function (datas) {
            $scope.listaDeLotes = datas;
        });
    } else {
        $scope.$watch('opcionCosechaC', function (val) {
            $http.get("trazabilidad/php/traeMenuTrazabilidadLaboratorio.php?tipoMiel=" + val).success(function (data) {
                $scope.menuTrazabilidadLaboratorio = data;
            });
        });

    }

    $scope.nuevaTrazabilidad = function () {
        if ($scope.opcionCosechaC == 0) {
            growl.info('Selecciona un tipo de Miel');
            return;
        } else {
            return window.location.href = '#/nvaTrazaLab/0/' + $scope.opcionCosechaC;
        }
    }

    $scope.concentradoAnalisis = function () {
        //            $http.get('trazabilidad/php/idLoteTrazabilidadAnalisis.php?idLoteInterno=' + $scope.comboAnalisis).success(function (data) {
        //                if (data.paso == 1) {
        //                    $scope.detalleAnalisis = data.detalleAnalisis;
        //                    $scope.encabezadoAnalisis.lote = data.lote;
        //                } else {
        //                    swal("Marca final no asignada!", "Revise reportes de carga", "error");
        //                    $scope.comboAnalisis = "";
        //                    $scope.detalleAnalisis = "";
        //                    $scope.encabezadoAnalisis.lote = "";
        //                }
        //            });

        $http.get('trazabilidad/php/traeLoteAnalisis.php?tipoMiel=' + $scope.tipoMiel + '&idLoteInterno=' + $scope.comboAnalisis).success(function (data) {
            $scope.encabezadoAnalisis.marcaFinalCliente = data.marcaFinalCliente;
        });

        $http.get('trazabilidad/php/traeSagarpasAnalisis.php?tipoMiel=' + $scope.tipoMiel + '&idLoteInterno=' + $scope.comboAnalisis).success(function (data) {
            $scope.detalleAnalisis = data;
        });
    };


    $scope.guardarTrazabilidadAnalisis = function () {
        $scope.encabezadoAnalisis.idLoteInterno = $scope.comboAnalisis;
        $scope.encabezadoAnalisis.tipoMiel = $scope.tipoMiel;
        $http.post("trazabilidad/php/guardarAnalisisLaboratorio.php", $scope.encabezadoAnalisis).success(function (info) {
            swal("Éxito", "Registro guardado", "success");
            $http.get("trazabilidad/php/traeMenuTrazabilidadLaboratorio.php").success(function (data) {
                $scope.menuTrazabilidadLaboratorio = data;
            });
            $scope.opcionCosechaC = $scope.tipoMiel;
            return window.location.href = "#/tLaboratorio";
        });
    };


    $scope.editarTrazabilidadAnalisis = function () {
        $("#mdlEditarTrazabilidadAnalisis").modal();
    };
    $scope.guardarEdicionAnalisis = function () {
        $http.post("trazabilidad/php/editarTrazabilidadAnalisis.php?idLoteInterno=" + $scope.parametroAnalisis, $scope.encabezadoAnalisis)
            .success(function (respuesta) {
                swal(respuesta.encabezado, respuesta.mensaje, respuesta.tipo);
                $http.get('trazabilidad/php/traeDetalleTrazabilidadAnalisis.php?tipoMiel=' + $scope.tipoMiel + '&idLoteInterno=' + $scope.parametroAnalisis).success(function (data) {
                    $scope.encabezadoAnalisis.idLoteInterno = data.idLoteInterno;
                    $scope.encabezadoAnalisis.nombreLaboratorio = data.nombreLaboratorio;
                    $scope.encabezadoAnalisis.fechaProtocolo = data.fechaProtocolo;
                    $scope.encabezadoAnalisis.folioProtocolo = data.folioProtocolo;
                    $scope.detalleAnalisis = data.detalleAnalisis;
                });
                $("#mdlEditarTrazabilidadAnalisis").modal('hide');
            });
    };

    $scope.pdfTramzabilidadLaboratorio = function () {
        window.open('reportes/trazabilidad/pdfTranzabilidadLaboratorio.php?tipoMiel=' + $scope.tipoMiel + '&idLoteInterno=' + $scope.parametroAnalisis, '_blank');
    };

    $scope.xlsTranzabilidadLaboratorio = function () {
        return window.location.href = 'reportes/trazabilidad/xlsTranzabilidadLaboratorio.php?tipoMiel=' + $scope.tipoMiel + '&idLoteInterno=' + $scope.parametroAnalisis;
    };

}]);


