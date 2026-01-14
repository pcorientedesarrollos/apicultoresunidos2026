form.controller('main', ['$rootScope', '$scope', '$cookies', 'auth', '$q', function ($rootScope, $scope, $cookies, auth, $q) {
    $rootScope.lstMenu = new Array();
    $scope.cerrarSesion = function () {
        $rootScope.lstMenu = new Array();
        auth.logout();
    };
    $scope.searchSubmenu = '';

    function subMenuMatch(input, subMenu) {
        var deferred = $q.defer();
        var subMenuCopy = angular.copy(subMenu);
        res = subMenuCopy.filter(element => {
            if (element.modulo.toLowerCase().indexOf(input) >= 0) {
                return element;
            }
        });
        deferred.resolve(res.length > 0 ? true : false);
        return deferred.promise;
    }

    $scope.lookForResults = function (input) {
        $scope.searchSubmenu = input != '' && input ? input : '';
        if (input != '' && input) {
            $scope.lstMenu.forEach(function (element) {
                subMenuMatch(input, element.lstSubMenus).then(function (value) {
                    element.searchResultsInside = value;
                });
            });
        } else {
            $scope.lstMenu.forEach((e) => e.searchResultsInside = false);
        }
    }

    $scope.limpiarBusqueda = function(){
        $scope.searchInput = '';
        $scope.searchSubmenu = '';
    }
}]);