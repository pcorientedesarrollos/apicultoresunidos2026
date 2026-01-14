form.controller('catalogoCuentasSubcuentasCtrl', function ($scope, $http, growl) {

    var defaultCatalogoCS = 'defaultCatalogoIE';
    var defaultTipo = 'defaultTipo';
    $scope.tipo = '0';
    $scope.mostrarLista = false;
    $scope.subSubcuentas = new Array();
    $scope.subSubcuentas_temp = new Array();
    $scope.array_historial_precios = new Array();

    $scope.$watch('tipo', function (value) {
        if (value) {
            setDefaultCatalogo(defaultTipo, value);
            traeCatalogos();
        } else {
            localStorage.removeItem(defaultCatalogoCS);
        }
    });

    if (localStorage.getItem(defaultTipo) != null) {
        try {
            $scope.tipo = JSON.parse(localStorage.getItem(defaultTipo));
        } catch (e) {
            localStorage.removeItem(defaultTipo);
        }
    }
    if (localStorage.getItem(defaultCatalogoCS) != null) {
        try {
            $scope.cuenta = JSON.parse(localStorage.getItem(defaultCatalogoCS));
        } catch (e) {
            localStorage.removeItem(defaultCatalogoCS);
        }
        if ($scope.cuenta) {
            traeConceptosCatalogo($scope.cuenta.idCuentaConcepto);
        }
    }

    function traeCatalogos() {
        if ($scope.tipo) {
            $scope.subcuentas = [];
            $scope.cuenta = null;
            $http.post('catalogos/php/traeCuentas.php?informes', $scope.tipo).success(function (data) {
                if (data.hasOwnProperty('error')) {
                    if (data.error) {
                        swal('Error', data.message, 'error');
                    } else {
                        $scope.cuentas = data.data;
                    }
                } else {
                    growl.console.error(('Error'));
                    console.error(data);
                }
            });
        }
    }

    $scope.traePrecioHistorial = function(id) {
        $('#modalInfoProducto').modal('hide');

        console.log(id)
            $scope.subcuentas = [];
            $scope.cuenta = null;
            $http.post('catalogos/php/traeHistorialPrecios.php?', id).success(function (data) {
                if (data.hasOwnProperty('error')) {
                    if (data.error) {
                        swal('Error', data.message, 'error');
                    } else {
                        $scope.array_historial_precios = data.data;
                        console.log($scope.array_historial_precios)
                        $('#modalPrecio').modal();
                        // $('#modalInfoProducto').modal('toggle');


                    }
                } else {
                    growl.console.error(('Error'));
                    console.error(data);
                }
            });
    }

    function traeListaInformesFinancieros() {
        $http.get('catalogos/php/traeListaInformesFinancieros.php').success(function (data) {
            if (data.hasOwnProperty('error')) {
                if (data.error) {
                    swal('Error', data.message, 'error');
                } else {
                    $scope.informesFinancieros = data.informesFinancieros;
                    $scope.mostrarLista = true;
                }
            } else {
                growl.console.error(('Error'));
                console.error(data);
            }
        });
    }

    // Llamar a la función
    traeCatalogos();
    traeUnidadesDisponibles();

    function traeUnidadesDisponibles() {
        $http.get('catalogos/php/traeUnidadesDeMedida.php').success(function (data) {
            if (data.hasOwnProperty('error')) {
                if (data.error) {
                    swal('Error', data.message, 'error');
                } else {
                    $scope.unidades = data.unidades;
                }
            } else {
                growl.console.error(('Error'));
                console.error(data);
            }
        });
    }

    $scope.eliminarCatalogo = function (opcion, datos) {
        var texto = '';
        if (opcion == 1 || opcion == 2) {
            texto = 'Al eliminarlo también borrará los conceptos relacionados';
        } else if (opcion == 3) {
            texto = '';
        }
        swal({
            title: '¿Desea eliminar el registro?',
            text: texto,
            showCancelButton: true,
            confirmButtonText: 'Sí',
            cancelButtonText: 'No',
            closeOnConfirm: true
        }, function (confirm) {
            if (confirm) {

                $http.post('catalogos/php/eliminarCatalogoCuentas.php?valor=' + opcion, datos).success(function (data) {
                    if (data.hasOwnProperty('error')) {
                        if (data.error) {
                            growl.error('Ocurrió un error');
                        } else {
                            
                            if (opcion == 3) {
                                $('#modalInfoProducto').modal('hide');
                            }

                            if (opcion == '1') {
                                traeCatalogos();
                            } else {
                                var idCuenta = $scope.cuenta.idCuentaConcepto;
                                traeConceptosCatalogo(idCuenta);
                            }

                            growl.success('Registro Eliminado');
                        }
                    } else {
                        growl.console.error(('Error'));
                        console.error(data);
                    }
                });
            }
        });
    };


    //ESTA FUNCIÓN TRAE LAS SUBCUENTAS Y SUBSUBCUENTAS PARA EL MODAL
    function traeConceptosCatalogo(idCuentaConcepto) {
        $http.post('catalogos/php/traeSubcuentas.php?idCuentaConcepto=' + idCuentaConcepto).success(function (data) {
            $scope.subcuentas = data;

        });
    }

    function setDefaultCatalogo(variable, value) {
        localStorage.setItem(variable, JSON.stringify(value));
    };

    $scope.mostrarCatalogo = function (indiceCatalogo) {
        $scope.cuenta = null;
        if (isFinite(indiceCatalogo) && !isNaN(indiceCatalogo)) {
            $scope.cuenta = $scope.cuentas[indiceCatalogo];
        } else if (isNaN(indiceCatalogo) && typeof (indiceCatalogo) == 'object') {
            $scope.cuenta = indiceCatalogo;
        }

        setDefaultCatalogo(defaultCatalogoCS, $scope.cuenta);
        traeConceptosCatalogo($scope.cuenta.idCuentaConcepto);
    }

    $scope.editarInfoProducto = function (indice) {
        if (indice >= 0) {
            $scope.verConcepto = Object.assign({}, $scope.subcuentas[indice]);
            $scope.subSubcuentas = $scope.verConcepto.subSubcuentas;
            $scope.subSubcuentas_temp = deepClone($scope.subSubcuentas);
            console.log($scope.subSubcuentas)
            console.log($scope.subSubcuentas_temp)
        } else {
            $scope.verConcepto = {};
            $scope.subSubcuentas = [];
        }
        $('#modalInfoProducto').modal();
    }

    $scope.nuevaSubSubcuenta = function () {
        $scope.subSubcuenta = {};
        $scope.subSubcuenta.idSubSubcuenta = 0;
        $scope.subSubcuenta.subSubcuenta = "";
        $scope.subSubcuentas.push($scope.subSubcuenta);
    };

    $scope.eliminarSubSubcuenta = function (indice) {
        $scope.subSubcuentas.splice(indice, 1);
        growl.warning("Registro eliminado");
    };

    $scope.eliminarRegistro = function (id, indice, idsubcuenta) {

        swal({
            title: "¿Estás seguro de eliminar este producto?",
            text: "El registro se perderá permanentemente",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#DD6B55",
            confirmButtonText: "Sí, eliminar",
            closeOnConfirm: false
        },
            function () {

                $scope.subcuentas.forEach(subcuenta => {
                            if (subcuenta.idSubcuenta == idsubcuenta) {
                                subcuenta.subSubcuentas.splice(indice, 1);
                            }
                        });

               $http.post('catalogos/php/eliminarProductos.php?idSubSubcuenta=' + id)
                            .success(function () {
                                swal("Exito!", "Producto eliminado", "success");
                                
                                // traeConceptosCatalogo($scope.cuenta.idCuentaConcepto);
                            });
            });
    };


    $scope.guardarInfoProducto = function () {
        $scope.verConcepto.idCuentaConcepto = $scope.cuenta.idCuentaConcepto;
        $scope.datosEnviar = new Array();
        $scope.datosEnviar.push($scope.verConcepto);
        $scope.datosEnviar.push($scope.subSubcuentas);
        console.log($scope.datosEnviar)
        console.log($scope.subSubcuentas_temp)
        var i = 0
        angular.forEach($scope.subSubcuentas, function(value){

            angular.forEach($scope.subSubcuentas_temp, function(value_temp){
                if(value.idSubSubcuenta == value_temp.idSubSubcuenta){
                    if(value.precioUnitario == value_temp.precioUnitario){

                    }else{
                        var date = new Date();
                        var _mes = date.getMonth() + 1;
                        // var _fecha = date.getFullYear() + '-' + _mes + '-' + date.getDate();
                        var objeto = {
                            idSubSubcuenta: value_temp.idSubSubcuenta,
                            precioUnitario: value_temp.precioUnitario,
                            subSubcuenta: value_temp.subSubcuenta,
                            fecha: date.getFullYear() + '-' + _mes + '-' + date.getDate()
                        }
                $http.post('controlAdministrativo/php/guardarHistorialPrecios.php', objeto).success(function (data) {
                });

                        i++;
                    }
                }
                
            })

        })
        $http.post('controlAdministrativo/php/guardarConcepto.php', $scope.datosEnviar).success(function (data) {
            $('#modalInfoProducto').modal('hide');
            swal('Éxito', 'Registro guardado', 'success');
            traeConceptosCatalogo($scope.cuenta.idCuentaConcepto);
        });
    }

    $('#modalPrecio').on('hidden.bs.modal', function (e) {
        // do something...
        $('#modalInfoProducto').modal();
      })


    $scope.nuevaCuenta = function (indice) {
        if (indice >= 0) {
            $scope.arreglo = [];
            angular.forEach($scope.cuentas[indice].idInforme, function (value) {
                $scope.holi = value.idInforme;
                $scope.arreglo.push($scope.holi);
            });
            $scope.respaldoCuenta = Object.assign({}, $scope.cuentas[indice]);
            $scope.verCuenta = Object.assign({}, $scope.cuentas[indice]);
            $scope.verCuenta.idInforme = $scope.arreglo;
            $scope.respaldoCuenta.idInforme = $scope.arreglo;
        } else {
            $scope.verCuenta = {};
        }
        traeListaInformesFinancieros();
        $('#modalNuevoCatalogo').modal();
    }

    $scope.guardarNuevoCatalogo = function () {
        if ($scope.verCuenta.idCuentaConcepto) {
            if ($scope.verCuenta.idInforme === $scope.respaldoCuenta.idInforme) {
                $scope.verCuenta.edicion = false;
            } else {
                $scope.verCuenta.edicion = true;
            }
        }
        if ($scope.verCuenta && $scope.verCuenta.cuenta != '') {
            $http.post('catalogos/php/guardarCuenta.php', $scope.verCuenta).success(function (data) {
                traeCatalogos();
                $('#modalNuevoCatalogo').modal('hide');
                if (data.hasOwnProperty('error')) {
                    if (data.error) {
                        growl.error('Ocurrió un error');
                    } else {
                        growl.success('Registro guardado');
                    }
                } else {
                    growl.console.error(('Error'));
                    console.error(data);
                }
            });
        } else {
            growl.info('Escriba el nombre del nuevo catálogo.');
        }
    }
});