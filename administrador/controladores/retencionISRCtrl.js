form.config(function ($routeProvider) {
    $routeProvider.when('/retencionIsr', {
        templateUrl: 'administrador/retencionISR/menuRetencion.html',
        controller: 'retencionISRCtrl'
    }).when('/nvaRetencion/:idRetencion', {
        templateUrl: 'administrador/retencionISR/nuevaRetencion.html',
        controller: 'retencionISRCtrl'
    }).when('/menuRetencionesProveedor/:idProveedor', {
        templateUrl: 'administrador/retencionISR/menuRetencionesProveedor.html',
        controller: 'retencionISRCtrl'
    })
});
form.controller('retencionISRCtrl', function ($scope, $routeParams, $http, growl, $location) {

    $scope.proveedor = $routeParams.idProveedor;
    $scope.idRetencion = $routeParams.idRetencion;
    $scope.retencionesRelizadas = new Array();
    $scope.retencionesDetalle = new Array();
    $scope.retenciones = {};
    $scope.lstProveedores = {};
    $scope.nombreDeudor = 0;

    if ($location.path() == '/retencionIsr') {
        $http.get('administrador/php/traeMenuRetenciones.php').success(function (data) {
            $scope.retencionesRelizadas = data;
        });
    }

    if ($scope.idRetencion > 0) {
        $http.get('proveedores/php/listaProveedores.php').success(function (data) {
            $scope.lstProveedores = data;
        });
        $http.get('administrador/php/detalleRetencionRealizada.php?idRetencion=' + $scope.idRetencion).success(function (data) {
            $scope.retenciones = data;
            $scope.nombreDeudor = data.idProveedor;
        });
    } else if ($scope.idRetencion == 0) {
        $http.get('proveedores/php/listaProveedores.php').success(function (data) {
            $scope.lstProveedores = data;
        });
    }

    if ($scope.proveedor > 0) {
        $http.get('administrador/php/traeMenuRetencionesPorProveedor.php?idProveedor=' + $scope.proveedor).success(function (data) {
            $scope.nombreProveedor = data.nombre;
            $scope.retencionesDetalle = data.retencionesDetalle;
        });
    }

    $scope.guardarRetencionRealizada = function () {
        if ($scope.idRetencion == 0) {
            $scope.retenciones.idProveedor = $scope.nombreDeudor;
            $scope.validado = $scope.validarRetencion();
            if ($scope.validado == true) {
                var mes = $scope.retenciones.fecha.split("-");
                $scope.retenciones.idMes = mes[1];
                $http.post('administrador/php/guardarRetencionRealizada.php', $scope.retenciones).success(function (data) {
                    console.log(data);
                    swal("¡Éxito!", "Registros Actualizados", "success");
                    return window.location.href = "#/menuRetencionesProveedor/" + $scope.retenciones.idProveedor;
                });
            }
        } else {
            $scope.retenciones.idProveedor = $scope.nombreDeudor;
            var mes = $scope.retenciones.fecha.split("-");
            $scope.retenciones.idMes = mes[1];
            $http.post('administrador/php/guardarRetencionRealizada.php?update=1', $scope.retenciones).success(function (data) {
                console.log(data);
                swal("¡Éxito!", "Registros Actualizados", "success");
                return window.location.href = "#/menuRetencionesProveedor/" + $scope.retenciones.idProveedor;
            });
        }
    };

    $scope.validarRetencion = function () {
        if ($scope.retenciones.idProveedor == 0) {
            growl.error("Seleccione un nombre");
        } else if ($scope.retenciones.concepto == undefined) {
            growl.error("Se requiere de una descripción/concepto");
        } else if ($scope.retenciones.fecha == undefined) {
            growl.error("Se requiere una fecha");
        } else if ($scope.retenciones.cantidad == undefined) {
            growl.error("Se requiere una cantidad");
        } else {
            $scope.validado = true;
        }
        return $scope.validado;
    };


});