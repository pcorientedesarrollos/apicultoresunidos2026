form.controller('programacionCtrl', ['$scope', '$http', 'growl', '$routeParams', function ($scope, $http, growl, $routeParams) {

    //=========================P A R A M E T R O===========================
    $scope.programacionParam = $routeParams.idEquipo;
    $scope.parametroArea = $routeParams.idArea;
    $scope.vistaParam = $routeParams.id;
    //    ----------------------------------------------------------------

    $scope.menuProgramacion = new Array();
    $scope.areasEquipos = new Array();
    $scope.menuEquiposProgramar = new Array();

    $scope.programacion = {};
    $scope.arregloDeFechas = new Array();

    $scope.fechasArreglo = {};
    $scope.fechaP = {};

    $scope.modalFechaP = function (idProgramacion) {
        $("#modalFechaP").modal();
        $scope.fechaP.idProgramacion = idProgramacion;
    };

    $scope.obtenerEquipos = function () {
        $http.post("inventarioDeEquipos/php/traeMenuEquipos.php?todos=0" + "&idArea=" + $scope.parametroArea).success(function (data) {
            $scope.nombreArea = data.area;
            $scope.menuEquipos = data.equipos;
        });
    }

    if ($scope.parametroArea > 0) {
        $scope.obtenerEquipos();
    }
    if ($scope.vistaParam == 1) {
        $http.post("inventarioDeEquipos/php/traeMenuEquipos.php?idArea=" + $scope.parametroArea).success(function (data) {
            $scope.nombreArea = data.area;
            $scope.menuEquiposProgramar = data.equipos;
        });
    }

    if ($scope.programacionParam > 0) {
        $http.post("controlMantenimiento/php/traeProgramacion.php?idEquipo=" + $scope.programacionParam).success(function (data) {
            $scope.programacion = data;
            $scope.arregloDeFechas = data.arregloDeFechas;
        });
    }

    $scope.guardarProgramacion = function () {

        angular.forEach($scope.fechasArreglo, function (a) {
            var fechaProgram = a.fechaProgramada.split("-");
            a.idMes = fechaProgram[1];
        });
        $http.post("controlMantenimiento/php/guardarProgramacion.php?idArea=" + $scope.programacion.idArea + '&idEquipo=' + $scope.programacion.idEquipo, $scope.fechasArreglo).success(function (daatos) {
            swal("Exito!", "Registro agregado", "success");
            $http.post("controlMantenimiento/php/traeMenuProgramacionDeFechas.php").success(function (data) {
                $scope.menuProgramacion = data;
            });
            //                return window.location.href = "#/verEquipos/1/" + $scope.programacion.idArea;
            return window.location.href = "#/equiposProgramar/1/" + $scope.programacion.idArea;

        });

    };

    $scope.editarFechaP = function () {
        $http.post("controlMantenimiento/php/editarFecha.php", $scope.fechaP)
            .success(function (respuesta) {
                growl.success("Fecha actualizada");
                $http.post("controlMantenimiento/php/traeProgramacion.php?idEquipo=" + $scope.programacionParam).success(function (data) {
                    $scope.programacion = data;
                    $scope.arregloDeFechas = data.arregloDeFechas;
                });
            });
        $("#modalFechaP").modal('hide');
        $scope.fechaP = {};
    };


    $scope.guardarCostos = function () {
        $http.post('inventarioDeEquipos/php/guardarCostos.php', { valor: $scope.menuEquipos }).success(function (result) {
        });
        growl.success("Nuevos costos registrados");
    };



    function mandarAImprimir(arreglo) {
        if (typeof (arreglo) == 'object' && arreglo.length > 0) {
            $scope.cargandoPrinter = true;
            $http.post('inventarioDeEquipos/php/mandarAImprimir.php', arreglo).success(function (data) {
                $scope.cargandoPrinter = false;
                if (data.hasOwnProperty('error')) {
                    if (data.error) {
                        swal('Error', data.message, 'error');
                    } else {
                        growl.success(data.message);
                    }
                } else {
                    growl.error('Error');
                    console.error(data);
                }
            })
        } else {
            console.warn('Tratando de imprimir sobre un objeto que no es un arreglo o el arreglo está vacío')
        }
    }

    /* Pasar el id del equipo en un arreglo para enviarlo */
    $scope.colaDeImpresion = function (mt) {
        mandarAImprimir([mt]);
    }

    $scope.eliminarActivo = function (id) {
        swal({
            title: "¿Eliminar artículo?",
            text: "Una vez eliminado no podrá recuperar la información",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#DD6B55",
            confirmButtonText: "Eliminar",
            closeOnConfirm: false
        },
            function () {
                $http.post('inventarioDeEquipos/php/eliminarActivo.php?idEquipo=' + id).success(function (data) {
                    if (data.err) {
                        swal("", data.message, "error");
                        $scope.obtenerEquipos();
                    } else {
                        swal("", data.message, "success");
                        $scope.obtenerEquipos();
                    }
                });
            });
    }

    //---------------------------------------------------------



    /* Mandar a imprimir todas las etiquetas, minimizar el arreglo */

    $scope.mandarTodosEquipos = function () {
        if ($scope.menuEquipos.length > 0) {
            let data = $scope.menuEquipos.map(function (e) {
                return e.idEquipo
            })
            mandarAImprimir(data);
        }
    }

    /* Descargar el excel con la información de los activos en pantalla */

    $scope.descargarExcelActivos = function () {
        return window.location.href = 'reportes/inventarioDeEquipos/xlsEquiposArea.php?idArea=' + $scope.parametroArea;
    }

}]);