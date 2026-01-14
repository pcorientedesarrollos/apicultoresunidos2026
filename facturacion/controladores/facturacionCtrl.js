form.config(function ($routeProvider) {
    $routeProvider.when('/facturacion', {
        templateUrl: 'facturacion/facturacion.html',
        controller: 'facturaCtrl'
    }).when('/capturaFacturacion/:idTamborPeso/:tipoMiel', {
        templateUrl: 'facturacion/capturaFacturacion.html',
        controller: 'facturaCtrl'
    })
});

form.controller('facturaCtrl', function ($scope, $http, $routeParams, growl, $location, $rootScope) {
    $scope.idTamborPeso = $routeParams.idTamborPeso;
    $scope.tipoMiel = $routeParams.tipoMiel;

    $scope.factura = {};
    $scope.precioLote = {};
    $scope.listadoExportacion = new Array();

    function traeDatos(val) {
        var clasificacion = val[0];
        var miel = val[1];
        if (clasificacion !== undefined && miel !== undefined) {
            console.log(clasificacion)
            $scope.listadoExportacion = [];
            $http.get('facturacion/php/traeListadoExportacion.php?clasificacion=' + clasificacion + '&miel=' + miel).success(function (data) {
                angular.forEach(data.data, function (value) {
                    if (value.tipoDeCliente == '10') {
                        var datoNombre = value.cliente.datosCliente;
                        var nombre = JSON.parse(datoNombre);
                        value.cliente = nombre.nombre;
                        $scope.listadoExportacion.push(value);
                    } else if (value.tipoDeCliente == '6') {
                        value.cliente = value.cliente.nombre
                        $scope.listadoExportacion.push(value);
                    }
                });
            });
        }
    };

    if ($location.path() == '/facturacion') {
        $http.get('utilerias/php/traeClasificacionesDeMiel.php?ver=1').success(function (data) {
            $scope.clasificacionesDeMiel = data;
        });
        if (window.localStorage.getItem('filtroClasificacion') != null) {
            $scope.clasificacion = window.localStorage.getItem('filtroClasificacion');
        }
        if (window.localStorage.getItem('filtroMiel') != null) {
            $scope.miel = window.localStorage.getItem('filtroMiel');
        }
        $scope.$watch('[clasificacion, miel]', function (val) {
            if ($scope.miel && $scope.clasificacion) {
                window.localStorage.setItem('filtroClasificacion', val[0]);
                window.localStorage.setItem('filtroMiel', val[1]);
                $scope.listadoExportacion = [];
                traeDatos(val);
            }
        });

    } else if ($scope.idTamborPeso > 0) {
        $http.get('facturacion/php/traeListadoExportacion.php?id=' + $scope.idTamborPeso + '&tipoMiel=' + $scope.tipoMiel).success(function (data) {
            $scope.detalleFactura = data.data;
            if ($scope.detalleFactura.tipoDeCliente == '10') {
                var datoNombre = $scope.detalleFactura.cliente.datosCliente;
                var nombre = JSON.parse(datoNombre);
                $scope.detalleFactura.cliente = nombre.nombre;
            } else if ($scope.detalleFactura.tipoDeCliente == '6') {
                $scope.detalleFactura.cliente = $scope.detalleFactura.cliente.nombre
            }
        });
    }

    $scope.guardarPrecioLote = function () {
        if ($scope.detalleFactura.precio !== '') {
            if ($scope.detalleFactura.condicionPago == '2') {
                $scope.detalleFactura.tiempoPago = null;
            }
            $http.post('facturacion/php/guardarPrecioLote.php', $scope.detalleFactura).success(function (data) {
                if (data.hasOwnProperty('error')) {
                    if (data.error) {
                        swal('Error', data.message, 'error');
                    } else {
                        swal('Éxito', data.message, 'success');
                        return window.location.href = "#/facturacion";
                    }
                } else {
                    growl.error('Error');
                    console.error(data);
                }
            });
        } else {
            growl.info('Ingresa la cantidad');
        }
    }

    $scope.modalFac = function (idTamborPeso, index) {
        $rootScope.idTamborPeso = idTamborPeso;
        $rootScope.index = index;
        $("#altaFac").modal();
    };

    $scope.actualizar = function (factura, id) {
        $http.post("facturacion/php/traeFolioFactura.php?id=" + factura.idTamborPeso).success(function (data) {
            var factura = data.folioFactura;
            $scope.$watch('listadoExportacion', function (val) {
                // console.log(val);
                // val = [];
                val[id].folioFactura = factura;
                // console.log(val);
                swal("Éxito!", "Registro modificado", "success");
                $("#altaFac").modal('hide');
                $scope.factura = {};
            });
        });
    }

    $scope.guardarFolioFactura = function (factura) {
        var id = $rootScope.index;
        factura.idTamborPeso = $rootScope.idTamborPeso;
        $http.post("facturacion/php/guardarFolioFactura.php", factura).success(function (info) {
            if (info == 'Se modificó') {
                $scope.actualizar(factura, id);
            }
        });
    };

    $scope.imprimirPrefactura = function () {
        window.open('reportes/facturacion/pdfPrefactura.php?idTamborPeso=' + $scope.idTamborPeso + '&tipoMiel=' + $scope.tipoMiel, '_blank');
    };

});