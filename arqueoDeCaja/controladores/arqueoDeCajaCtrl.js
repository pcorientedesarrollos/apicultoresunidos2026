form.config(function ($routeProvider) {
    $routeProvider.when('/arqueoDeCaja', {
        templateUrl: 'arqueoDeCaja/arqueoDeCaja.html',
        controller: 'arqueoCajaCtrl'
    }).when('/capturaArqueo/:idArqueo', {
        templateUrl: 'arqueoDeCaja/capturaArqueoDeCaja.html',
        controller: 'arqueoCajaCtrl'
    })
});

form.controller('arqueoCajaCtrl', function ($scope, $http, $routeParams, growl, busqueda, $location, $rootScope) {

    $scope.idArqueo = $routeParams.idArqueo;
    $scope.arqueosDeCaja = new Array();
    $scope.arregloGastos = new Array();
    $scope.arregloBilletes = new Array();

    function obtenerArqueos() {
        $http.get('arqueoDeCaja/php/obtenerArqueos.php').success(function (data) {
            $scope.arqueosDeCaja = data.data;
        });
    };

    function obtenerArqueo(idArqueo) {
        $http.get('arqueoDeCaja/php/obtenerArqueo.php?idArqueo=' + idArqueo).success(function (data) {
            $scope.encabezadoArqueo = data.data;
            $scope.arregloGastos = $scope.encabezadoArqueo.gastos;
            $scope.arregloBilletes = $scope.encabezadoArqueo.billetes;
        });
    };

    if ($location.path() == '/arqueoDeCaja') {
        obtenerArqueos();
    }

    if ($scope.idArqueo > 0) {
        obtenerArqueo($scope.idArqueo);
    }

    $scope.nuevoGasto = function () {
        $scope.gasto = {};
        $scope.gasto.idGasto = 0;
        $scope.gasto.concepto = "";
        $scope.gasto.cantidad = "";
        $scope.arregloGastos.push($scope.gasto);
    };

    $scope.eliminarGasto = function (indice) {
        $scope.gasto = {};
        $scope.gasto.idGasto = 0;
        $scope.gasto.concepto = "";
        $scope.gasto.cantidad = "";
        $scope.gasto.indice = 0;
        angular.forEach($scope.arregloGastos, function (value, key) {
            if (key == indice) {
                $scope.gasto.indice = key;
                $scope.gasto.idGasto = value.idGasto;
                $scope.gasto.concepto = value.concepto;
                $scope.gasto.cantidad = value.cantidad;
            }
        });
        $scope.arregloGastos.splice($scope.gasto.indice, 1);
    };

    $scope.nuevoBillete = function () {
        $scope.billete = {};
        $scope.billete.idBillete = 0;
        $scope.billete.billeteMoneda = "";
        $scope.billete.numero = "";
        $scope.billete.total = "";
        $scope.arregloBilletes.push($scope.billete);
    };

    $scope.eliminarBillete = function (indice) {
        $scope.billete = {};
        $scope.billete.idBillete = 0;
        $scope.billete.billeteMoneda = "";
        $scope.billete.numero = "";
        $scope.billete.total = "";
        $scope.billete.indice = 0;
        angular.forEach($scope.arregloBilletes, function (value, key) {
            if (key == indice) {
                $scope.billete.indice = key;
                $scope.billete.idBillete = value.idBillete;
                $scope.billete.billeteMoneda = value.billeteMoneda;
                $scope.billete.numero = value.numero;
                $scope.billete.total = value.total;
            }
        });
        $scope.arregloBilletes.splice($scope.billete.indice, 1);
    };

    $scope.guardarCapturaArqueo = function () {
        $scope.datosEnviar = new Array();
        $scope.datosEnviar.push($scope.encabezadoArqueo);
        $scope.datosEnviar.push($scope.arregloGastos);
        $scope.datosEnviar.push($scope.arregloBilletes);
        $http.post("arqueoDeCaja/php/guardarCapturaArqueo.php", $scope.datosEnviar).success(function (data) {
            if (!data.error) {
                swal("¡Éxito!", "registro guardado", "success");
                return window.location.href = "#/arqueoDeCaja";
            } else {
                swal("", "ocurrió un error", "info");
                return window.location.href = "#/arqueoDeCaja";
            }
        });
    };
});