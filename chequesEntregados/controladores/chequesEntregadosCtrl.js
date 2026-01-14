form.config(function ($routeProvider) {
    $routeProvider.when('/chequesEntregados', {
        templateUrl: 'chequesEntregados/html/listaCheques.html',
        controller: 'chequesEntregadosCtrl'
    }).when('/registroCheques/:idEncabezado', {
        templateUrl: 'chequesEntregados/html/registroCheques.html',
        controller: 'chequesEntregadosCtrl'
    })
})
form.controller('chequesEntregadosCtrl', ['$scope', '$http', '$routeParams', 'growl', '$rootScope', '$location', function ($scope, $http, $routeParams, growl, $rootScope, $location) {
    $scope.registro = $routeParams.idEncabezado;
    $scope.chequesEntregados = new Array();
    $scope.encabezado = {};
    $scope.detalle = {};
    $scope.listaDetalle = new Array();

    $http.get('controlAdministrativo/php/listaDeBancos.php').success(function (datas) {
        $scope.listaDeBancos = datas;
    });

    function traeListaCheques() {
        $http.get('chequesEntregados/php/traeListaChequesEntregados.php').success(function (datos) {
            if (datos.error) {
                growl.info("ocurrió un error");
            } else {
                $scope.chequesEntregados = datos.data;
            }
        });
    }

    function traeDetallePorEntrada(idEncabezado) {
        $http.get('chequesEntregados/php/traeRegistrosPorEntrada.php?idEncabezado=' + idEncabezado).success(function (respuesta) {
            if (respuesta.error) {
                growl.info('Ocurrió un error');
            } else {
                $scope.encabezado = respuesta.data;
                $scope.listaDetalle = $scope.encabezado.listaDetalle;
            }
        })
    }

    function traeChequesPorBanco(banco) {
        $http.get('chequesEntregados/php/traeRegistrosPorBanco.php?idBanco=' + banco).success(function (datos) {
            if (datos.error) {
                growl.info('Ocurrió un error');
            } else {
                $scope.chequesEntregados = datos.data.chequesEntregados;
                $scope.estadoCheque = datos.data;
                $scope.respaldoCheques = datos.data.chequesEntregados;
            }
        });
    }

    if ($location.path() == '/chequesEntregados') { //Vista principal

        $scope.$watch('opcionVista', function (valor) {
            $scope.chequesEntregados = [];
            $scope.banco = "";
            $scope.filtroCheque = "";
            if (valor == 1) {
                traeListaCheques();
            }
        });

        $scope.$watch('banco', function (val) {
            traeChequesPorBanco(val);
        }, true);

    } else { //Dar de alta o editar registros

        function traerListaPersonas(idTipoPersona, catalogo) {
            $http.post('controlAdministrativo/polizaCheque/php/traerNombres.php', idTipoPersona).success(function (data) {
                if (catalogo == 1) {
                    $scope.personas = data;
                } else if (catalogo == 2) {
                    $scope.lstPersonas = data;
                }
            });
        };
        $scope.$watch('detalle.tipoDePersona', function (val) {
            $scope.detalle.idPersona = "";
            traerListaPersonas(val, 1);
        });
        $scope.$watch('detalle.idPersona', function () {
            $http.get("controlAdministrativo/php/traeNombresPorTipo.php?id=" + $scope.detalle.idPersona + "&tipo=" + $scope.detalle.tipoDePersona).success(function (respuesta) {
                $rootScope.nombreId = respuesta;
            });
        }, true);

        $scope.$watch('encabezado.tipoCatalogo', function (val) {
            if ($scope.registro == 'nuevo') {
                $scope.encabezado.nombreRecibe = "";
            }
            traerListaPersonas(val, 2);
        });

        traeDetallePorEntrada($scope.registro);
    }

    $scope.agregarFolio = function () {
        if ($scope.registro == 'nuevo') {
            $scope.detalle.nombre = $rootScope.nombreId;
            $scope.listaDetalle.push($scope.detalle);
            growl.success("Nuevo movimiento agregado");
            $scope.detalle = {};
        } else {
            $scope.guardarRegistros();
        }
    }

    $scope.guardarRegistros = function () {

        if ($scope.registro == 'nuevo') {
            $scope.datosEnviar = new Array();
            $scope.datosEnviar.push($scope.encabezado);
            $scope.datosEnviar.push($scope.listaDetalle);
        } else {
            $scope.datosEnviar = $scope.detalle;
            $scope.datosEnviar.fechaEntrega = $scope.encabezado.fechaEntrega;
            $scope.datosEnviar.idBanco = $scope.encabezado.idBanco;
            $scope.datosEnviar.nombreRecibe = $scope.encabezado.nombreRecibe;
        }
        var url = 'chequesEntregados/php/guardarEntregaCheques.php';
        if ($scope.registro != 'nuevo') {
            url += '?idEncabezado=' + $scope.registro;
        }
        $http.post(url, $scope.datosEnviar).success(function (respuesta) {
            if (!respuesta.error) {
                swal("¡Éxito!", "Registro guardado", "success");
                if ($scope.registro == 'nuevo') {
                    window.open(respuesta.url, "_blank");
                    return window.location.href = "#/chequesEntregados";
                } else {
                    $scope.detalle = {};
                    traeDetallePorEntrada($scope.registro);
                }
            } else {
                growl.error("Hubo un error");
            }
        });
    }

    $scope.filtrarCheques = function (filtro) {
        $scope.nuevoArreglo = [];
        if (filtro == 1) {
            $scope.respaldoCheques.forEach(cheque => { //Cobrados
                if (cheque.fechaCobro != null) {
                    $scope.nuevoArreglo.push(cheque);
                }
            });
            if ($scope.nuevoArreglo.length == 0) {
                growl.info('No hay cheques cobrados');
                traeChequesPorBanco($scope.banco);
            }
        } else if (filtro == 2) {
            $scope.respaldoCheques.forEach(cheque => { //No cobrados
                if (cheque.fechaCobro == null) {
                    $scope.nuevoArreglo.push(cheque);
                }
            });
            if ($scope.nuevoArreglo.length == 0) {
                growl.info('No hay cheques sin cobrar');
                traeChequesPorBanco($scope.banco);
            }
        } else if (filtro == 3) { // Todos
            traeChequesPorBanco($scope.banco);
        }
        $scope.chequesEntregados = $scope.nuevoArreglo;
    }

    $scope.editarEncabezado = function () {
        $("#mdlEditarEncabezado").modal();
    };

    $scope.guardarEdicionEncabezado = function () {
        $http.post('chequesEntregados/php/guardarEdicionEncabezado.php', $scope.encabezado)
            .success(function (respuesta) {
                if (respuesta == 1) {
                    growl.success("Éxito, registro guardado");
                    traeDetallePorEntrada($scope.registro);
                } else {
                    growl.info("Ocurrió un error");
                }
            });
        $("#mdlEditarEncabezado").modal('hide');
    };

    $scope.descartarEntrega = function (indice) {
        $scope.listaDetalle.splice(indice, 1);
        growl.warning("Registro eliminado");
    };
    $scope.eliminarEntrega = function (idEntrega, cobrado) {
        if (cobrado) {
            swal({
                title: "¿Eliminar entrega?",
                text: "No se podrá recuperar los datos",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "Eliminar",
                cancelButtonText: "Cancelar",
            }, function (eliminar) {
                if (eliminar) {
                    $http.post("chequesEntregados/php/eliminarRegistroEntrega.php?idEntrega=" + idEntrega).success(function (data) {
                        if (!data.error) {
                            swal("¡Éxito!", "Registro eliminado", "success");
                            traeDetallePorEntrada($scope.registro);
                        }
                    });
                }
            });
        }
    };

    $scope.eliminarLista = function (idEncabezado) {
        if (idEncabezado) {
            swal({
                title: "¿Eliminar el registro?",
                text: "Se eliminará todos los datos relacionados",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "Eliminar",
                cancelButtonText: "Cancelar",
            }, function (eliminar) {
                if (eliminar) {
                    $http.get("chequesEntregados/php/eliminarEntrega.php?idEncabezado=" + idEncabezado).success(function (res) {
                        if (typeof (res) == 'object' && res.hasOwnProperty('error')) {
                            if (res.error) {
                                swal('Error', res.message, 'error');
                            } else {
                                traeListaCheques();
                                swal('Hecho', res.message, 'success');
                            }
                        } else {
                            growl.error('Error');
                            console.error(res);
                        }
                    });
                }
            });
        }
    }

    $scope.imprimirPDF = function (idEncabezado) {
        $http.get('../extras/getDatabase.php').then(function(response) { //llamammos a la variable de sesion con el nombre de la tabla
            let database = response.data;
            window.open('https://formatos.apicultoresunidos.com/reportes/cheques-entregados/' + idEncabezado + '/' + database, '_blank');

            // window.open('https://formatos.apicultoresunidos.com/comprobante-caja-chica/pdf/' + $scope.idCajaChica + '/' + '38', '_blank');
        });
       // window.open('reportes/compras/pdfEntregaDeCheques.php?idEncabezado=' + idEncabezado);
    };

}]);