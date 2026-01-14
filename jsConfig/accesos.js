form.controller('accesos', function ($scope, $http, growl) {
//    alert("entro");
    $http.post("./controller/dameUsuarios.php").success(function (respuesta) {
        console.log(respuesta);
        $scope.listaUsuarios = respuesta;
    });

    $scope.dameModulos = function () {
        $http.post("./controller/dameAccesos.php?idUsuario=" + $scope.usuario).success(function (respuesta) {
            $scope.listaModulos = respuesta;
            console.log(respuesta);
        });
    };

    $scope.guardarPermisos = function () {
        $http.post('./controller/guardarAccesos.php?idUsuario=' + $scope.usuario, {valor: $scope.listaModulos}).success(function (respuesta) {
            console.error(respuesta);
            growl.success("Permisos dados de alta satisfactoriamente");
        });

    };



});