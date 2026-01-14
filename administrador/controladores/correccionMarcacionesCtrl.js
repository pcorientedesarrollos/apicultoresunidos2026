form.config(function ($routeProvider) {
    $routeProvider.when('/marcacionFinal', {
        templateUrl: 'administrador/marcacionesFinales/listadoMarcaciones.html',
        controller: 'correccionMarcacionesCtrl'
    })
});
form.controller('correccionMarcacionesCtrl', function ($scope, $routeParams, $http, growl, $location) {

    $scope.verTipoDeMiel = '1';
    $scope.marcaciones = new Array();
    $scope.cargandoDatos = false;
    $scope.correccion = {};

    function traeInformacionVista(valor) {
        $scope.cargandoDatos = true;
        $http.post("administrador/php/traeMarcaciones.php?tipoMiel=" + valor).success(function (info) {
            if (info.hasOwnProperty('error')) {
                if (info.error) {
                    growl.error(info.message);
                } else {
                    $scope.marcaciones = info.data;
                    $scope.cargandoDatos = false;
                }
            } else {
                console.error(info);
            }
        });
    }

    if ($location.path() == '/marcacionFinal') {
        if (window.localStorage.getItem('seleccionMielPesos') != null) {
            $scope.tipoDeMiel = window.localStorage.getItem('seleccionMielPesos');
        } else {
            window.localStorage.removeItem('seleccionMielPesos');
        }
        $scope.$watch('tipoDeMiel', function (opcion) {
            if (opcion) {
                $scope.verTipoDeMiel = opcion;
                traeInformacionVista(opcion);
                window.localStorage.setItem('seleccionMielPesos', opcion);
            }
        });
    }

    $scope.modalCorreccion = function (idReporte, index) {
        $("#correccionMarca").modal();
        $scope.correccion.idReporte = idReporte;
        $scope.correccion.index = index;
    }

    $scope.guardarMarca = function (correccion) {
        var id = correccion.index;
        console.log(correccion);
        console.log($scope.verTipoDeMiel);
        $http.post("administrador/php/correccionMarcaFinal.php?miel=" + $scope.verTipoDeMiel, correccion).success(function (info) {
            console.log(info);
            traeInformacionVista($scope.verTipoDeMiel);
            swal("¡Éxito!", "Registro agregado", "success");
            $("#correccionMarca").modal('hide');
            $scope.correccion = {};
        });
    }

});