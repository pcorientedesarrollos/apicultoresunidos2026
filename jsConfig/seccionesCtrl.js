form.controller('seccionesCtrl', ['$scope', '$http', '$routeParams', 'growl', function ($scope, $http, $routeParams, growl) {

    //=========================P A R A M E T R O S===========================
    var idSeccion = $routeParams.idSeccion;
    var defaultSectionName = 'SECCIONES_DEFAULT_SECCION';
    $scope.seccionesModulos = new Array();


    //==========================================================================
    // TRAE LOS NOMBRES DE TODAS LAS SECCIONES REGISTRADAS EN EL SISTEMA
    //==========================================================================
    function traerSecciones() {
        $http.post("habilitarSecciones/php/traeEncabezadoSecciones.php").success(function (info) {
            if (info.hasOwnProperty('error')) {
                if (info.error) {
                    swal('Error', info.message, 'error');
                } else {
                    $scope.seccionesModulos = info.secciones;
                }
            } else {
                growl.error('Error');
                console.error(info);
            }
        });
    }
    if (!idSeccion) {
        traerSecciones();
    }


    //==========================================================================
    // TRAE LOS MODULOS ASIGNADOS A LA SECCIÓN EN SCOPE.SECCION
    //==========================================================================

    function traerModulos() {
        if ($scope.seccion.hasOwnProperty('idSeccion') && $scope.seccion.idSeccion >= 0) {
            $http.get('habilitarSecciones/php/traeDetalleSeccion.php?idSeccion=' + $scope.seccion.idSeccion).success(function (data) {
                if (data.hasOwnProperty('error')) {
                    if (data.error) {
                        swal('Error', data.message, 'error');
                    } else {
                        $scope.seccion.modulos = data.resultado.modulos;
                    }
                } else {
                    growl.error('Error');
                    console.error(data);
                }
            });
        }
    }
    //==========================================================================
    // TRAE LOS PERMISOS ASIGNADOS A LA SECCIÓN
    //==========================================================================

    function traePermisos(idSeccion) {
        if (idSeccion >= 0) {
            $http.get('habilitarSecciones/php/traePermisosSeccion.php?idSeccion=' + idSeccion).success(function (data) {
                if (data.hasOwnProperty('error')) {
                    if (data.error) {
                        swal('Error', data.message, 'error');
                    } else {
                        $scope.seccion = data.resultado.seccion;
                        $scope.seccion.permisos = data.resultado.permisos;
                    }
                } else {
                    growl.error('Error');
                    console.error(data);
                }
            });
        }
    }

    //==========================================================================
    // ASIGNA A SCOPE.SECCION EL OBJETO DE LA SECCION, POR EL INDICE DEL ARREGLO
    //==========================================================================

    $scope.mostrarModulosDeSeccion = function (seccion) {
        if (seccion) {
            $scope.seccion = seccion;
            if (!$scope.seccion.modulos) {
                traerModulos();
            }
            var _seccion = {
                idSeccion: $scope.seccion.idSeccion,
                seccion: $scope.seccion.seccion
            }
            window.localStorage.setItem(defaultSectionName, JSON.stringify(_seccion));
        }
    }

    //==========================================================================
    // SI NO SE HA ESPECIFICADO UN idSeccion Y EL LOCALSTORAGE CONTIENE INFO,
    // ASIGNAR AL SCOPE.SECCION ESE OBBEJO Y TRAER SUS MODULOS.
    // PROPÓSITO: MOSTRAR DE FORMA AUTOMÁTICA LA ÚLTIMA SELECCIÓN
    //==========================================================================


    if (window.localStorage.getItem(defaultSectionName) != null && !idSeccion) {

        $scope.seccion = JSON.parse(window.localStorage.getItem(defaultSectionName));
        traerModulos();
    }

    if (idSeccion) {
        traePermisos(idSeccion);
    }

}]);
