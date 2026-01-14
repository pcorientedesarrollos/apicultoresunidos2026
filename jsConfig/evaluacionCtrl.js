form.controller('evaluacionCtrl', ['$scope', '$http', '$routeParams', function ($scope, $http, $routeParams) {

//=========================P A R A M E T R O===========================
        $scope.evaluacionId = $routeParams.idEvaluacion;
//    ----------------------------------------------------------------

        $scope.menuEvaluacion = new Array();

        $scope.evaluacion = {};
        $scope.cuestionario = {};
        $scope.listaTecnicos = {};
        $scope.listaTiposProveedores = {};

        $scope.proveedorMantto = {};
        $scope.proveedorMantto.idProveedorMantto = "";
        $scope.proveedorMantto.nombreProveedor = "";

        $scope.tipoDeProveedor = {};
        $scope.tipoDeProveedor.idTipoProveedor = "";
        $scope.tipoDeProveedor.tipoProveedor = "";

        $scope.datosProveedor = {};

        if ($scope.evaluacionId > 0) {
            $http.post("controlMantenimiento/php/traeEvaluacion.php?idEvaluacion=" + $scope.evaluacionId).success(function (data) {
                $scope.evaluacion = data;
                $scope.proveedorMantto = "" + $scope.evaluacion.idProveedorMantto + "";
                $scope.tipoDeProveedor = "" + $scope.evaluacion.idTipoProveedor + "";
                $scope.datosProveedor.domicilio = data.domicilio;
                $scope.datosProveedor.telefono = data.telefono;
                $scope.datosProveedor.web = data.web;
                $scope.datosProveedor.correo = data.correo;
                $scope.datosProveedor.nombreContacto = data.nombreContacto;
                var cadena = data.cuestionario;
                $scope.cuestionario = JSON.parse(cadena);
            });
//            $http.get('controlMantenimiento/php/listaDeTecnicos.php').success(function (datas) {
//                $scope.listaTecnicos = datas;
//            });
            $http.get('json/controlMantto/tipoProveedor.json').success(function (datas) {
                $scope.listaTiposProveedores = datas.tiposProveedores;
            });
        } else {
            $http.post("controlMantenimiento/php/traeMenuEvaluaciones.php").success(function (data) {
                $scope.menuEvaluacion = data;
            });
//            $http.get('controlMantenimiento/php/listaDeTecnicos.php').success(function (datas) {
//                $scope.listaTecnicos = datas;
//            });
            $http.get('json/controlMantto/tipoProveedor.json').success(function (datas) {
                $scope.listaTiposProveedores = datas.tiposProveedores;
            });
        }

        $scope.$watch('tipoDeProveedor', function (tipoDeProveedor) {
            $http.get('controlMantenimiento/php/listaDeTecnicosTipo.php?idTipoProveedor=' + tipoDeProveedor)
                    .success(function (data) {
                         $scope.listaTecnicos = data;
                    });
//            $scope.tipoDeProveedor = $scope.evaluacion.idTipoProveedor;
        }, true);

//        $scope.proveedoresDisponibles = function (tipo) {
//            $http.post('controlMantenimiento/php/listaDeTecnicosTipo.php?idTipoProveedor=' + tipo).success(function (datas) {
//                $scope.listaTecnicos = datas;
//            });
//        };

        $scope.proveedorDatos = function (proveedorMantto) {
            $http.post("controlMantenimiento/php/datosDelProveedor.php?idProveedorMantto=" + proveedorMantto)
                    .success(function (respuesta) {
                        $scope.datosProveedor = respuesta;
                    });
        };

        $scope.guardarEvaluacion = function () {
            $scope.evaluacion.idProveedorMantto = $scope.proveedorMantto;
            $scope.evaluacion.idTipoProveedor = $scope.tipoDeProveedor;
            var texto = JSON.stringify($scope.cuestionario);
            $scope.evaluacion.cuestionario = texto;

            if ($scope.evaluacionId > 0) {
                $http.post("controlMantenimiento/php/guardarEvaluacion.php?idEvaluacion=" + $scope.evaluacionId, $scope.evaluacion).success(function (info) {
                    console.log(info);
                    swal("Exito!", "Registro agregado", "success");
                    $http.post("controlMantenimiento/php/traeMenuEvaluaciones.php").success(function (data) {
                        $scope.menuEvaluacion = data;
                    });
                    return window.location.href = "#/evaluacion";
                });
            } else {
                $http.post("controlMantenimiento/php/guardarEvaluacion.php", $scope.evaluacion).success(function (info) {
                    console.log(info);
                    swal("Exito!", "Registro agregado", "success");
                    $http.post("controlMantenimiento/php/traeMenuEvaluaciones.php").success(function (data) {
                        $scope.menuEvaluacion = data;
                    });
                    return window.location.href = "#/evaluacion";
                });
            }
        };

        $scope.pdfEvaluacion = function () {
            window.open('reportes/controlMantenimiento/evaluacionProveedores.php?idEvaluacion=' + $scope.evaluacionId, '_blank');
        };

    }]);