form.controller('temporalCtrl', ['$scope', '$http', '$routeParams', 'growl', '$location', '$q', '$rootScope',
    function ($scope, $http, $routeParams, growl, $location, $q, $rootScope) {

        $scope.listaAUP = new Array();

        if ($location.path() == '/temporal') {
            $http.get('controlAdministrativo/temporal/traeListado.php').success(function (data) {
                $scope.listaAUP = data;
                console.log(data);
            });
        }

        $scope.traeNombres = function (idTipoPersona) {
            $http.post('controlAdministrativo/polizaCheque/php/traerNombres.php', idTipoPersona).success(function (data) {
                $scope.personas = data;
            });
        };

    }]);

