form.config(function ($routeProvider) {
    $routeProvider.when('/metas', {
        templateUrl: 'recoleccion/metas-compra.html',
        controller: 'metasCompraCtrl'
    })
})
form.controller('metasCompraCtrl', function ($scope, $http, growl, $routeParams, $location) {

    function traeMetasCompradores(miel) {

        $http.get('recoleccion/php/metas/metasCompradores.php?miel=' + miel).success(function (data) {
            if (typeof (data) == 'object' && data.hasOwnProperty('error')) {
                if (data.error) {
                    swal('Error', data.message, 'error');
                } else {
                    $scope.metas = data.resultado;
                }
            } else {
                growl.error('Error');
                console.error(data);
            }
        });

    }

    function indiceComprador(id_comprador) {
        // Devuelve el indice del arreglo $scope.metas que cumpla con el idComprador = id_comprador
        return $scope.metas.map(c => c.idComprador).indexOf(id_comprador);
    }

    $scope.cambioMetaTambores = function (tambores, id_comprador, indice_zona) {
        if (tambores) {
            // Calcular los kilos y ponerlos a la zona correspondiente
            let totalKilos = tambores * 300;
            // Localizar el indice del comprador con el id que se recibe,
            let indexComprador = indiceComprador(id_comprador);
            // Ajustar el valor de kilogramos a la zona con el id recibido en el comprador con el indice previamente obtenido
            $scope.metas[indexComprador].zonas[indice_zona].kilogramos = totalKilos;
            actualizarTotalMeta(indexComprador);
        } else {
            // Poner los kilos de la zona en 0
            // Localizar el indice del comprador con el id que se recibe,
            let indexComprador = indiceComprador(id_comprador);
            // Ajustar el valor de kilogramos a la zona con el id recibido en el comprador con el indice previamente obtenido
            $scope.metas[indexComprador].zonas[indice_zona].kilogramos = 0;
            actualizarTotalMeta(indexComprador);
        }
    }

    if ($location.path() == '/metas') {
        // Traer a los compradores
        // Y traer sus metas por zona, con un total
        $scope.$watch('tipoDeMiel', function (val) {
            if (val) {
                if (val == '1') {
                    $scope.tituloPlantilla = 'Metas de compra miel 100% pura de abeja';
                } else if (val == '2') {
                    $scope.tituloPlantilla = 'Metas de compra miel 100% orgánica';
                }
                traeMetasCompradores(val);
            }
        });

    }

    $scope.guardarMetas = function () {
        $scope.zonas = Array();
        $scope.datosMetas = Array();
        $scope.metas.forEach(element => {
            $scope.zonas.push(element.zonas);
        });
        $scope.zonas.forEach(element => {
            element.forEach(dato => {
                $scope.datosMetas.push(dato);
            })
        });

        $http.post('recoleccion/php/metas/guardarMetas.php?miel=' + $scope.tipoDeMiel, $scope.datosMetas).success(function (data) {
            if (typeof (data) == 'object' && data.hasOwnProperty('error')) {
                if (data.error) {
                    swal('Error', data.message, 'error');
                } else {
                    swal('Listo', data.message, 'success');
                }
            } else {
                growl.error('Error');
                console.error(data);
            }
        });
    };

    function actualizarTotalMeta(indexComprador) {
        $scope.metas[indexComprador].totalTambores = 0;
        $scope.metas[indexComprador].totalMeta = 0;
        $scope.metas[indexComprador].zonas.forEach(function (zona) {
            if (zona.tambores > 0) {
                $scope.metas[indexComprador].totalTambores += zona.tambores;
                $scope.metas[indexComprador].totalMeta += zona.kilogramos;
            }
        })
    }

});