form.controller('catalogoProductosCtrl', function ($scope, $http, growl) {

    $scope.catalogo = null;
    var defaultCatalogoLS = 'defaultCatalogo';

    if (localStorage.getItem(defaultCatalogoLS) != null) {
        try {
            $scope.catalogo = JSON.parse(localStorage.getItem(defaultCatalogoLS));
        } catch (e) {
            localStorage.removeItem(defaultCatalogoLS);
        }
        if ($scope.catalogo) {
            traeConceptosCatalogo($scope.catalogo.idSubcuenta);
        }
    }

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

    $scope.eliminarRegistro = function (id, indice) {

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

                $scope.catalogo.subsubcuentas.splice(indice, 1);
                let i = 0;
                $scope.listaSubcuentas.forEach(subcuenta => {
                    if (subcuenta.idSubcuenta == $scope.catalogo.idSubcuenta) {
                        $scope.listaSubcuentas[i] = $scope.catalogo;
                    }
                    i++;
                });

               $http.post('catalogos/php/eliminarProductos.php?idSubSubcuenta=' + id)
                            .success(function () {
                                swal("Exito!", "Producto eliminado", "success");
                                
                                $scope.mostrarSubsubcuentas($scope.nombreNuevoCatalogo);
                            });
                     

            });
    };


    function traeCatalogos() {
        $http.get('catalogos/php/traerCatalogos.php').success(function (data) {
            if (data.hasOwnProperty('error')) {
                if (data.error) {
                    swal('Error', data.message, 'error');
                } else {
                    console.log(data.data)
                    $scope.catalogos = data.data;
                }
            } else {
                growl.console.error(('Error'));
                console.error(data);
            }
        });
    }

    $scope.ImprimirReporte = function () {
                return window.location.href = 'catalogos/php/xlsReporteProductos.php?opcion=1';
    }

    $scope.ImprimirReportePDF = function () {
                window.open('catalogos/php/pdfProductosReporteV3.php', '_blank')
    }

    // Llamar a la función
    traeUnidadesDisponibles();
    traeCatalogos();



    /**
     * Actualización de la función y el script del php
     * propósito: trae subsubcuentas de una subcuenta
     * EL id que se envia deberá ser el id de la subcuenta
     * @param {int} idConcepto id de la subcuenta
     * ?actualizado se envia por el momento para usar los regisros de la nueva tabla subsubcuentas
     * ya que en "Otras salidas" también se usa el php
     */
    function traeConceptosCatalogo(idConcepto) {
        if (idConcepto) {
            $http.post('catalogos/php/traeConceptosCatalogo.php?actualizado', idConcepto).success(function (data) {
                if (data.hasOwnProperty('error')) {
                    if (data.error) {
                        swal('Error', data.message, 'error');
                    } else {
                        $scope.catalogo.subsubcuentas = data.conceptos;
                    }
                } else {
                    growl.console.error(('Error'));
                    console.error(data);

                }
            });
        }
    }


    // ||| Ya no se va a guardar la subcuenta, para que se pueda actualizar |||
    // function setDefaultCatalogo(catalogo) {
    //     localStorage.setItem(defaultCatalogoLS, JSON.stringify(catalogo));
    // };

    $scope.mostrarSubsubcuentas = function (indiceSubcuenta) {
        $scope.catalogo = null;

        if (isFinite(indiceSubcuenta) && !isNaN(indiceSubcuenta)) {
            $scope.catalogo = $scope.listaSubcuentas[indiceSubcuenta];
        } else if (isNaN(indiceSubcuenta) && typeof (indiceSubcuenta) == 'object') {
            $scope.catalogo = indiceSubcuenta;
        }

        // setDefaultCatalogo($scope.catalogo);
        // traeConceptosCatalogo($scope.catalogo.idConceptoCC);
    }

    validarInformacionConcepto = function (concepto) {
        if (!concepto.hasOwnProperty('unidad')
            || !concepto.hasOwnProperty('peso')
            || !concepto.hasOwnProperty('precioUnitario')
        ) {
            console.error('Error, el concepto no cuenta con las características necesarias para guardar.');
            console.warn(concepto);
            return false;
        } else {
            if (isNaN(parseFloat(concepto.precioUnitario))) {
                growl.info('Formato de precio incorrecto.');
                return false;
            }
        }

        return true;
    }

    $scope.editarInfoProducto = function (indice) {
        if (indice >= 0) {
            $scope.verConcepto = Object.assign({}, $scope.catalogo.subsubcuentas[indice]);
        }
        // Se elimina la opcion de objeto nuevo, ya no se va a poder crear desde esta vista
        // else {
        //     $scope.verConcepto = {};
        // }
        $('#modalInfoProducto').modal();
    }

    $scope.guardarInfoProducto = function () {
        if (validarInformacionConcepto($scope.verConcepto)) {

            // El siguiente valor es equivalente al idSubcuenta, el cual ya lo tiene el objeto, así que se comentó
            // $scope.verConcepto.idConceptoCC = $scope.catalogo.idConceptoCC;
            $http.post('catalogos/php/actualizarInfoProducto.php', $scope.verConcepto).success(function (data) {
                if (data.hasOwnProperty('error')) {
                    if (data.error) {
                        swal('Error', data.message, 'error');
                    } else {
                        $('#modalInfoProducto').modal('hide');
                        swal('Éxito', data.message, 'success');
                        traeConceptosCatalogo($scope.catalogo.idSubcuenta);
                    }
                } else {
                    growl.console.error(('Error'));
                    console.error(data);
                }
            });
        }
    }

    $scope.nuevoCatalogo = function () {
        $scope.nombreNuevoCatalogo = null;
        $('#modalNuevoCatalogo').modal();
    }

    $scope.guardarNuevoCatalogo = function () {
        if ($scope.nombreNuevoCatalogo && $scope.nombreNuevoCatalogo != '') {
            switch (typeof ($scope.nombreNuevoCatalogo)) {
                case 'object':
                    growl.success('El catálogo ya existe');
                    $scope.mostrarSubsubcuentas($scope.nombreNuevoCatalogo);
                    $('#modalNuevoCatalogo').modal('hide');
                    break;
                case 'string':
                    var nuevoConcepto = {
                        concepto: $scope.nombreNuevoCatalogo,
                        subconceptos: []
                    }

                    $http.post('catalogos/php/guardarCatalogo.php', nuevoConcepto).success(function (data) {
                        if (data.hasOwnProperty('error')) {
                            if (data.error) {
                                swal('Error', data.message, 'error');
                            } else {
                                traeCatalogos();
                                $('#modalNuevoCatalogo').modal('hide');
                                growl.success(data.message);
                            }
                        } else {
                            growl.error('Error');
                            console.error(data);
                        }
                    });
                    break;
                default:
                    growl.error('Catálogo inválido');
                    break;
            }
        } else {
            growl.info('Escriba el nombre del nuevo catálogo.');
        }
    }

    /**
     * Cuando se cambia la selección del catalogo en el chose, se debe mostrar sus subcuentas
     */

    $scope.cambioSeleccionCatalogo = function () {
        $scope.listaSubcuentas = null;
        if ($scope.selectedCatalogue) {
            $scope.listaSubcuentas = null;
            $scope.listaSubcuentas = $scope.selectedCatalogue.subcuentas;
        }
    }
});