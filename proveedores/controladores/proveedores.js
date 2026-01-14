form.config(function ($routeProvider) {
    $routeProvider.when('/proveedores', {
        templateUrl: 'proveedores/proveedores.html',
        controller: 'proveedores',
        controllerAs: 'vm'
    }).when('/nvoProveedor/:idProveedor', {
        templateUrl: 'proveedores/nuevoProveedor.html',
        controller: 'proveedores'
    }).when('/carta/:idProveedor', {
        templateUrl: 'proveedores/subirCarta.html'
    }).when('/contrato/:idProveedor', {
        templateUrl: 'proveedores/subirContrato.html'
    }).when('/apiario/:idProveedor', {
        templateUrl: 'proveedores/subirRApiarios.html'
    }).when('/instalacion/:idProveedor', {
        templateUrl: 'proveedores/subirRInstalaciones.html'
    }).when('/upDoc/:idProveedor', {
        templateUrl: 'proveedores/subirDocumentos.html',
        controller: 'subirArchivosCtrl'
    }).when('/docProveedores', {
        templateUrl: 'proveedores/verProveedores.html',
        controller: 'proveedores'
    });
})
form.controller('proveedores', ['$scope', '$routeParams', 'growl', '$filter', '$http', '$location', function ($scope, $routeParams, growl, $filter, $http, $location) {
    $scope.telefonos = new Array();
    $scope.contactos = new Array();
    $scope.correos = new Array();
    $scope.valor = $routeParams.idProveedor;
    $scope.infoProveedor = new Array();
    $scope.proveedor = {};
    $scope.proveedor.empresa = false;
    $scope.proveedor.id = 0;
    $scope.proveedor.nombre = "";
    $scope.proveedor.idDireccion = "";
    $scope.proveedor.direccionCompleta = "";
    $scope.proveedor.estado = "";
    $scope.proveedor.idEstado = "";
    $scope.proveedor.localidad = "";
    $scope.proveedor.idlocalidad = "";
    $scope.proveedor.cp = "";
    $scope.proveedor.idSagarpa = "";
    $scope.proveedor.tipo = "";
    $scope.proveedor.idDatosFiscales = "";
    $scope.proveedor.razonSocial = "";
    $scope.proveedor.rfc = "";
    $scope.proveedor.curp = "";
    $scope.proveedor.correo = "";
    $scope.proveedor.colonia = "";
    var self = this;
    $scope.listaProveedor = {};
    $scope.seleccionZonas = {};
    $scope.seleccionZonas.idlocalidad = "";
    $scope.seleccionZonas.localidad = "";
    $scope.eligeZona = {};
    $scope.municipio = {};
    $scope.miRespuesta = "";
    // $scope.estadoProveedor = '1';
    $scope.filtros = {};
    $scope.$watch('proveedor.idSagarpa', function (val) {
        $scope.proveedor.idSagarpa = $filter('uppercase')(val);
    }, true);

    $scope.traeLocalidades = function (id) {
        if (id) {
            console.log(id);
            $scope.localidadC = [];
            $http.get('proveedores/php/traeLocalidadesPorComprador.php?idcomprador=' + id).success(function (data) {
                $scope.localidadC = data;
            });
        }

    };

    $scope.traeEstados = function (id) {
        $http.get('proveedores/php/listaEstados.php?idlocalidad=' + id).success(function (arrayEstados) {
            $scope.estadosMexico = arrayEstados;
            $scope.seleccionEstado = $scope.estadosMexico[0].idEstado;
        });
    };


    if ($scope.valor > 0) {
        // Si trae en los params un id, traer la información del proveedor

        $http.post("proveedores/php/dameProveedor.php?id=" + $scope.valor).success(function (info) {
            $scope.seleccionComprador = info.idComprador;
            $scope.seleccionZonas = info.idlocalidad;
            $scope.seleccionEstado = info.idEstado;
            $scope.proveedor = info;
            $scope.telefonos = info.telefonos;
            $scope.contactos = info.contactos;
            $scope.correos = info.correos;
            $scope.tipoCosecha = "" + $scope.proveedor.tipoDeMiel + "";
            $scope.traeEstados($scope.seleccionZonas);
            $scope.traeLocalidades($scope.seleccionComprador);
        });
    }


    // window.onload = function () {
    //     console.log('loaded');
    //     var input = document.getElementById('latitud');
    //     console.log(input);

    //     input.addEventListener('invalid', function (event) {
    //         console.log('event con event listener');
    //         event.target.setCustomValidity('Escriba con el formato correcto');
    //         $scope.$apply();
    //     });
    //     input.oninvalid = function (event) {
    //         console.log('event');
    //         event.target.setCustomValidity('Escriba con el formato correcto');
    //     }
    // }

    //=================================================================
    //      TRAE INFORMACION DE RELACION DE REQUERIMIENTOS
    //=================================================================

    function traeTiposDeMiel() {
        $http.get('utilerias/php/traeTiposDeMiel.php?proveedor=0').success(function (response) {
            $scope.tiposDeMiel = response;
        });
    }

    if ($location.path() == '/proveedores') {

        // Comprobar la selección del tipo de miel
        traeTiposDeMiel();
        if (window.localStorage.getItem('filtroProveedor') != null) {
            $scope.filtros.tipoProveedor = window.localStorage.getItem('filtroProveedor');
        } else {
            window.localStorage.removeItem('filtroProveedor');
        }


        if (window.localStorage.getItem('filtroEstado') != null) {
            $scope.filtros.estadoProveedor = window.localStorage.getItem('filtroEstado');
        } else {
            window.localStorage.removeItem('filtroEstado');
        }

        // watch cambios en la selección de miel
        $scope.$watch('filtros', function (filtros) {
            if (filtros.tipoProveedor) {
                $scope.infoProveedor = [];
            }
            if (filtros.tipoProveedor && filtros.estadoProveedor) {
                $http.post('proveedores/php/dameProveedores.php?tipo=' + filtros.tipoProveedor + '&estado=' + filtros.estadoProveedor).success(function (info) {
                    $scope.infoProveedor = info;
                });
            }
            window.localStorage.setItem('filtroEstado', filtros.estadoProveedor);
            window.localStorage.setItem('filtroProveedor', filtros.tipoProveedor);
        }, true);
    }

    $scope.activarProveedor = function (id) {
        swal({
            title: "",
            text: "¿Desea cambiar el estado de ésta persona?",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#DD6B55",
            confirmButtonText: "Sí, activar.",
            closeOnConfirm: false
        },
            function () {
                $http.post('proveedores/php/cambiarActivoInactivo.php?activoInactivo=0&id=' + id).success(function (info) {
                    $http.post('proveedores/php/dameProveedores.php?tipo=' + $scope.filtros.tipoProveedor + '&estado=' + $scope.filtros.estadoProveedor).success(function (info) {
                        $scope.infoProveedor = info;
                    });
                });
                swal("Enviado!", "El estado cambió", "success");
            });
    };

    $scope.desactivarProveedor = function (id) {

        swal({
            title: "",
            text: "¿Desea cambiar el estado de ésta persona?",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#DD6B55",
            confirmButtonText: "Sí, desactivar.",
            closeOnConfirm: false
        },
            function () {
                $http.post('proveedores/php/cambiarActivoInactivo.php?activoInactivo=1&id=' + id).success(function (info) {
                    $http.post('proveedores/php/dameProveedores.php?tipo=' + $scope.filtros.tipoProveedor + '&estado=' + $scope.filtros.estadoProveedor).success(function (info) {
                        $scope.infoProveedor = info;
                    });
                });
                swal("", "El estado cambió", "success");
            });
    };

    $scope.eliminarProveedor = function (id) {

        swal({
            title: "",
            text: "¿Eliminar provedor de manera permanente?",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#DD6B55",
            confirmButtonText: "Sí, eliminar.",
            closeOnConfirm: false
        },
            function () {
                $http.post('proveedores/php/eliminarProveedor.php?deleteProve=1&id=' + id).success(function (info) {
                    $http.post('proveedores/php/dameProveedores.php?tipo=' + $scope.filtros.tipoProveedor + '&estado=' + $scope.filtros.estadoProveedor).success(function (info) {
                        $scope.infoProveedor = info;
                    });
                });
                swal("", "El estado cambió", "success");
            }
            
            
            );
    };


    //=================================================================
    // CONSULTA (Combo)
    //=================================================================
    // Obtener compradores
    $http.get('reqDepositoCompra/php/listaCompradores.php').success(function (resultado) {
        if (typeof (resultado) == 'object' && resultado.hasOwnProperty('error')) {

            if (resultado.error) {
                swal('Error', resultado.message, 'error');
            } else {
                $scope.listaCompradores = resultado.resultado;
            }

        } else {
            growl.error('Error');
            console.error(resultado);
        }
    });


    $scope.comboTipoMiel = {};
    $http.get("utilerias/php/traeTiposDeMiel.php?proveedor=0").success(function (informacion) {
        if (!informacion.hasOwnProperty('error')) {
            $scope.comboTipoMiel = informacion;
        }
    });

    $scope.mostrarDialog = function () {
        $("#modalTelefonos").modal();
    };
    $scope.mostrarDialogContactos = function () {
        $("#modalContactos").modal();
    };
    $scope.agregarTelefono = function () {
        if ($scope.valor > 0) {
            // $scope.nuevoTelefono();

            $scope.telefono = {
                id: null,
                telefono: null
            };
            // $scope.telefono.id = "registro";
            // $scope.telefono.telefono = $scope.addTelefono;
            $scope.telefonos.push($scope.telefono);
        } else {
            $scope.telefono = {
                id: null,
                telefono: null
            };
            // $scope.telefono.id = "registro";
            // $scope.telefono.telefono = $scope.addTelefono;
            $scope.telefonos.push($scope.telefono);
        }
        $("#modalTelefonos").modal('hide');
        $scope.addTelefono = "";
    };

    //*== ======================FUNCIONES PARA SUBIR ARCHIVOS DE DOCUMENTACION==================================*//

    $scope.nomZonas = {};
    $http.get('compras/php/zona/listaZona.php').success(function (arrayZonas) {
        $scope.nomZonas = arrayZonas; // formEditarLocalidad.html
    });



    $scope.guardarLocalidad = function () {
        $scope.municipio.idzona = $scope.eligeZona;
        $http.post('proveedores/php/agregarLocalidad.php', $scope.municipio).success(function (result) {
            $http.get('compras/php/localidad/listaLocalidades.php').success(function (data) {
                $scope.localidadC = data;
            });
        });
        ;
        swal("¡Éxito!", "Nueva localidad disponible", "success");
        $("#modalLocalidad").modal('hide');
    };

    $scope.elementos = [
        { idArchivo: 1, documento: 'Contrato Mercantil' },
        { idArchivo: 2, documento: 'Carta Compromiso' },
        { idArchivo: 3, documento: 'Revisión de Instalaciones' },
        { idArchivo: 4, documento: 'Revisión de Apiarios' }
    ];

    $scope.selectDoc = "";

    $scope.aceptar = function (idArchivo) {

    };

    //=================================================================
    // LLAMADAS A MODALES
    //=================================================================

    //        $scope.modalRFC = function () {
    //            $("#modalRFC").modal();
    //        };
    //        $scope.modalIdSagarpa = function () {
    //            $("#modalIdSagarpa").modal();
    //        };
    //        $scope.modalInstalacion = function () {
    //            $("#modalInstalacion").modal();
    //        };
    //        $scope.modalContrato = function () {
    //            $("#modalContrato").modal();
    //        };
    //        $scope.modalCarta = function () {
    //            $("#modalCarta").modal();
    //        };
    //        $scope.modalApiario = function () {
    //            $("#modalApiario").modal();
    //        };

    $scope.nuevaLocalidad = function () {
        $("#modalLocalidad").modal();
    };

    //        //=================================================================
    //        // FUNCION PARA GUARDAR ARCHIVO "REVISION DE INSTALACIONES"
    //        //=================================================================
    //
    //        $scope.guardarInstalacion = function (id) {
    //            var name = JSON.stringify(id);
    //            var file = $scope.file;
    //            subirDocs.archivoInstalaciones(file, $scope.valor);
    //
    //            swal("Excelente!", "Archivo guardado!", "success");
    //
    //            $("#modalInstalacion").modal('hide');
    //        };
    //
    //        //=================================================================
    //        // FUNCION PARA GUARDAR ARCHIVO "RFC"
    //        //=================================================================
    //
    //        $scope.guardarRFC = function (id) {
    //            alert("hola");
    //            var name = JSON.stringify(id);
    //            var file = $scope.file;
    //            subirDocs.archivoRFCS(file, $scope.valor);
    //
    //            swal("Excelente!", "Archivo guardado!", "success");
    //
    //            $("#modalRFC").modal('hide');
    //        };
    //
    //
    //        //=================================================================
    //        // FUNCION PARA GUARDAR ARCHIVO "ID SAGARPA"
    //        //=================================================================
    //
    //        $scope.guardarIdSagarpa = function (id) {
    //            var name = JSON.stringify(id);
    //            var file = $scope.file;
    //            subirDocs.archivoSagarpa(file, $scope.valor);
    //
    //            swal("Excelente!", "Archivo guardado!", "success");
    //
    //            $("#modalIdSagarpa").modal('hide');
    //        };
    //
    //        //=================================================================
    //        // FUNCION PARA GUARDAR "CONTRATO MERCANTIL"
    //        //=================================================================
    //
    //        $scope.guardarContrato = function (id) {
    //            var name = JSON.stringify(id);
    //            var file = $scope.file;
    //            subirDocs.archivoContrato(file, $scope.valor);
    //
    //            swal("Excelente!", "Archivo guardado!", "success");
    //            $http.get('proveedores/php/getContratoMercantil.php?id=' + $scope.valor).success(function (data) {
    //                $scope.docContrato.images = data;
    //            });
    //            $("#modalContrato").modal('hide');
    //        };

    //        //=================================================================
    //        // FUNCION PARA GUARDAR "REVISION DE APIARIOS"
    //        //=================================================================
    //
    //        $scope.guardarApiario = function (id) {
    //            var name = JSON.stringify(id);
    //            var file = $scope.file;
    //            subirDocs.archivoApiario(file, $scope.valor);
    //
    //            swal("Excelente!", "Archivo guardado!", "success");
    //            $http.post("proveedores/php/dameProveedor.php?id=" + $scope.valor).success(function (info) {
    //                $scope.proveedor = info;
    //                $scope.telefonos = info.telefonos;
    //                $scope.contactos = info.contactos;
    //                $scope.correos = info.correos;
    //                $scope.seleccionZonas = "" + $scope.proveedor.idlocalidad + "";
    //            });
    //            $("#modalApiario").modal('hide');
    //        };
    //
    //        //=================================================================
    //        // FUNCION PARA GUARDAR "CARTA COMPROMISO"
    //        //=================================================================
    //
    //        $scope.guardarCarta = function (id) {
    //            alert("Hola");
    //            var name = JSON.stringify(id);
    //            var file = $scope.file;
    //            alert($scope.file);
    //            subirDocs.archivoCarta(file, $scope.valor);
    //
    //            swal("Excelente!", "Archivo guardado!", "success");
    ////            $http.post("proveedores/php/dameProveedor.php?id=" + $scope.valor).success(function (info) {
    ////                $scope.proveedor = info;
    ////                $scope.telefonos = info.telefonos;
    ////                $scope.contactos = info.contactos;
    ////                $scope.correos = info.correos;
    ////                $scope.seleccionZonas = "" + $s
    ////                cope.proveedor.idlocalidad + "";
    ////            });
    ////            $("#modalCarta").modal('hide');
    //        };
    //
    //        //=================================================================
    //        // FUNCION PARA MOSTRAR Y ELIMINAR "CARTA RESPONSIVA"
    //        //=================================================================

    //        $scope.docCarta = {};
    //        $http.get('proveedores/php/getCartaResponsiva.php?id=' + $scope.valor).success(function (data) {
    //            $scope.docCarta.images = data;
    //        });
    //
    //        $scope.eliminarDocCarta = function (idCarta) {
    //            $scope.docCarta = {};
    //            $http.get('proveedores/php/eliminarCartaResponsiva.php?id=' + idCarta).success(function () {
    //            });
    //            $http.post("proveedores/php/dameProveedor.php?id=" + $scope.valor).success(function (info) {
    //                $scope.proveedor = info;
    //                $scope.telefonos = info.telefonos;
    //                $scope.contactos = info.contactos;
    //                $scope.correos = info.correos;
    //                $scope.seleccionZonas = "" + $scope.proveedor.idlocalidad + "";
    //            });
    //            growl.success("Archivo eliminado");
    //        };

    //        //=================================================================
    //        // FUNCION PARA MOSTRAR Y ELIMINAR "RFC"
    //        //=================================================================
    //
    //        $scope.docRFC = {};
    //        $http.get('proveedores/php/getRFC.php?id=' + $scope.valor).success(function (data) {
    //            $scope.docRFC.images = data;
    //        });
    //
    //        $scope.eliminarDocRFC = function (idRFC) {
    //            $scope.docRFC = {};
    //            $http.get('proveedores/php/eliminarRFC.php?id=' + idRFC).success(function () {
    //            });
    //            $http.post("proveedores/php/dameProveedor.php?id=" + $scope.valor).success(function (info) {
    //                $scope.proveedor = info;
    //                $scope.telefonos = info.telefonos;
    //                $scope.contactos = info.contactos;
    //                $scope.correos = info.correos;
    //                $scope.seleccionZonas = "" + $scope.proveedor.idlocalidad + "";
    //            });
    //            growl.success("Archivo eliminado");
    //        };

    //        //=================================================================
    //        // FUNCION PARA MOSTRAR Y ELIMINAR "ID SAGARPA"
    //        //=================================================================
    //
    //        $scope.docIdSagarpa = {};
    //        $http.get('proveedores/php/getIdSagarpa.php?id=' + $scope.valor).success(function (datos) {
    //            $scope.docIdSagarpa.images = datos;
    //        });
    //
    //        $scope.eliminarDocIdSagarpa = function (idSagarpaId) {
    //            $scope.docIdSagarpa = {};
    //            $http.get('proveedores/php/eliminarSagarpaId.php?id=' + idSagarpaId).success(function () {
    //            });
    //            $http.post("proveedores/php/dameProveedor.php?id=" + $scope.valor).success(function (info) {
    //                $scope.proveedor = info;
    //                $scope.telefonos = info.telefonos;
    //                $scope.contactos = info.contactos;
    //                $scope.correos = info.correos;
    //                $scope.seleccionZonas = "" + $scope.proveedor.idlocalidad + "";
    //            });
    //            growl.success("Archivo eliminado");
    //        };

    //
    //        //=================================================================
    //        // FUNCION PARA MOSTRAR Y ELIMINAR "REVISION A APIARIOS"
    //        //=================================================================
    //
    //        $scope.docApiario = {};
    //        $http.get('proveedores/php/getRevisionApiario.php?id=' + $scope.valor).success(function (data) {
    //            $scope.docApiario.images = data;
    //        });
    //
    //        $scope.eliminarDocApiario = function (idApiario) {
    //            $scope.docApiario = {};
    //            $http.get('proveedores/php/eliminarRevisionApiario.php?id=' + idApiario).success(function () {
    //            });
    //            $http.post("proveedores/php/dameProveedor.php?id=" + $scope.valor).success(function (info) {
    //                $scope.proveedor = info;
    //                $scope.telefonos = info.telefonos;
    //                $scope.contactos = info.contactos;
    //                $scope.correos = info.correos;
    //                $scope.seleccionZonas = "" + $scope.proveedor.idlocalidad + "";
    //            });
    //        };
    //
    //        //=================================================================
    //        // FUNCION PARA MOSTRAR Y ELIMINAR "REVISION A INSTALACIONES"
    //        //=================================================================

    //        $scope.docInstalacion = {};
    //        $http.get('proveedores/php/getRevisionInstalacion.php?id=' + $scope.valor).success(function (data) {
    //            $scope.docInstalacion.images = data;
    //        });
    //
    //        $scope.eliminarDocInstalacion = function (idInstalacion) {
    //            $scope.docInstalacion = {};
    //            $http.get('proveedores/php/eliminarRevisionInstalacion.php?id=' + idInstalacion).success(function () {
    //            });
    //            $http.post("proveedores/php/dameProveedor.php?id=" + $scope.valor).success(function (info) {
    //                $scope.proveedor = info;
    //                $scope.telefonos = info.telefonos;
    //                $scope.contactos = info.contactos;
    //                $scope.correos = info.correos;
    //                $scope.seleccionZonas = "" + $scope.proveedor.idlocalidad + "";
    //            });
    //        };

    //        //=================================================================
    //        // FUNCION PARA MOSTRAR Y ELIMINAR "CONTRATO MERCANTIL"
    //        //=================================================================
    //
    //        $scope.docContrato = {};
    //        $http.get('proveedores/php/getContratoMercantil.php?id=' + $scope.valor).success(function (data) {
    //            $scope.docContrato.images = data;
    //        });
    //
    //        $scope.eliminarDocContrato = function (idContrato) {
    //            $scope.docContrato = {};
    //            $http.get('proveedores/php/eliminarContratoMercantil.php?id=' + idContrato).success(function () {
    //
    //            });
    //
    //        };


    //*=================================================================================================*//

    $scope.guardarP = function () {

        $scope.okProveedor = $scope.validarProveedores();
        if ($scope.okProveedor == true) {
            $scope.proveedor.idComprador = $scope.seleccionComprador;
            $scope.proveedor.idlocalidad = $scope.seleccionZonas;
            $scope.proveedor.idEstado = $scope.seleccionEstado;
            $scope.proveedor.tipoDeMiel = $scope.tipoCosecha;
            $scope.proveedor.id = $scope.valor;
            $scope.informacion = new Array();
            $scope.informacion.push($scope.proveedor);
            $scope.informacion.push($scope.telefonos);
            $scope.informacion.push($scope.contactos);
            $scope.informacion.push($scope.correos);
            $scope.informacion.push($scope.correos);
            if ($scope.valor > 0) {
                $http.post("proveedores/php/actualizarProveedor.php",
                    { valor: $scope.informacion }
                ).success(function (respuesta) {
                    return window.location.href = "#/proveedores";
                });
            } else {
                $http.post("proveedores/php/verificarIdSagarpa.php?idSagarpa=" + $scope.proveedor.idSagarpa)
                    .success(function (respuesta) {
                        if (respuesta == 1) {
                            swal("Error!", "Verifique! Id Sagarpa duplicado", "error");
                        } else {
                            console.log("HOLI");
                            $http.post("proveedores/php/guardarProveedores.php",
                                { valor: $scope.informacion }
                            ).success(function (respuesta) {

                                if (typeof (respuesta) == 'object') {

                                    swal(respuesta.encabezado, respuesta.mensaje, respuesta.tipo);
                                    $scope.proveedor = {};
                                    $scope.telefonos = {};
                                    $scope.correos = {};
                                    return window.location.href = "#/proveedores";
                                } else {
                                    growl.error('Error');
                                    console.error(respuesta);
                                }

                            });
                        }
                    }
                    );
            }
        }
    };

    $scope.validarProveedores = function () {
        $scope.okP = false;

        if (!$scope.tipoCosecha) {
            swal("Error!", "Se requiere indicar tipo de producto", "error");
        } else if ($scope.tipoCosecha != '4') {
            if ($scope.proveedor.idSagarpa == "") {
                swal("Error!", "Se requiere un ID Sagarpa", "error");
            }
        }
        // else {
        // swal("Error!", "Se requiere un ID Sagarpa", "error");
        // }

        if ($scope.proveedor.nombre == "") {
            swal("Error!", "Se requiere el nombre del proveedor", "error");
        } else if ($scope.proveedor.tipo == "") {
            swal("Error!", "Se requiere indicar tipo de proveedor", "error");
        } else if ($scope.proveedor.direccionCompleta == "") {
            swal("Error!", "Se requiere indicar dirección completa", "error");
        } else if ($scope.proveedor.colonia == "") {
            swal("Error!", "Se requiere indicar colonia", "error");
        } else if (!$scope.seleccionComprador) {
            swal("Error!", "Se requiere indicar un comprador", "error");
        } else if ($scope.seleccionZonas.idlocalidad == 0) {
            swal("Error!", "Se requiere el nombre de la localidad", "error");
        } else if ($scope.proveedor.cp == "") {
            swal("Error!", "Se requiere el Código Postal", "error");
        } else {
            $scope.okP = true;
        }
        return $scope.okP;
    };

    $scope.eliminarTelefono = function (indice) {
        $scope.tel = {};
        $scope.tel.id = 0;
        $scope.tel.numero = "";
        $scope.tel.indice = 0;
        angular.forEach($scope.telefonos, function (value, key) {
            if (key == indice) {
                $scope.tel.indice = key;
                $scope.tel.id = value.id;
                $scope.tel.numero = value.telefono;
            }
        });
        if ($scope.tel.id == "registro") {
            $scope.telefonos.splice($scope.tel.indice, 1);
        } else {
            $http.post("proveedores/php/eliminarTelefono.php?id=" + $scope.tel.id).success(function (info) {
                swal("¡Éxito", info, "success");
                $http.post("proveedores/php/dameTelefonos.php?id=" + $scope.valor).success(function (telefonos) {
                    $scope.telefonos = telefonos;
                });
            });
        }

    };
    $scope.eliminarContacto = function (indice) {
        $scope.contacto = {};
        $scope.contacto.id = 0;
        $scope.contacto.nombre = "";
        $scope.contacto.indice = 0;
        angular.forEach($scope.contactos, function (value, key) {
            if (key == indice) {
                $scope.contacto.indice = key;
                $scope.contacto.id = value.id;
                $scope.contacto.nombre = value.nombre;
            }
        });
        if ($scope.valor == 0) {
            $scope.contactos.splice($scope.contacto.indice, 1);
        } else if ($scope.valor > 0) {
            $http.post("proveedores/php/eliminarContacto.php?idContacto=" + $scope.contacto.id, { valor: $scope.aquiVaInfo })
                .success(function (respuesta) {
                    //                        alert(respuesta);
                    swal(respuesta.encabezado, respuesta.mensaje, respuesta.tipo);
                    $scope.contactos.splice($scope.contacto.indice, 1);
                });
        }
    };
    $scope.eliminarCorreo = function (indice) {
        $scope.correo = {};
        $scope.correo.id = "";
        $scope.correo.email = "";
        $scope.correo.indice = 0;
        angular.forEach($scope.correos, function (value, key) {
            if (key == indice) {
                $scope.correo.indice = key;
                $scope.correo.id = value.id;
                $scope.correo.email = value.email;
            }
        });
        if ($scope.valor == 0) {
            $scope.correos.splice($scope.correo.indice, 1);
        } else if ($scope.valor > 0) {
            $http.post("proveedores/php/eliminarCorreo.php?idCorreo=" + $scope.correo.id)
                .success(function (respuesta) {
                    swal(respuesta.encabezado, respuesta.mensaje, respuesta.tipo);
                    $scope.correos.splice($scope.correo.indice, 1);
                });
        }
    };
    $scope.nuevoTelefono = function () {
        $http.post("proveedores/php/nuevoTelefono.php?id=" + $scope.valor + "&idTipo=1" + "&telefono=" + $scope.addTelefono).success(function (info) {
            swal("¡Éxito", info, "success");
            $http.post("proveedores/php/dameTelefonos.php?id=" + $scope.valor).success(function (telefonos) {
                $scope.telefonos = telefonos;
            });
        });
    };
    $scope.nuevoContacto = function () {

        $scope.contacto = {};
        $scope.contacto.id = 0;
        $scope.contacto.nombre = "";
        $scope.contactos.push($scope.contacto);
    };
    $scope.nuevoCorreo = function () {
        $scope.correo = {};
        $scope.correo.id = "";
        $scope.correo.email = "";
        $scope.correos.push($scope.correo);
    };

}
]);
form.filter('trusted', ['$sce', function ($sce) {
    var div = document.createElement('div');
    return function (text) {
        div.innerHTML = text;
        return $sce.trustAsHtml(div.textContent);
    };
}]);
//=================================================================
// DIRECTIVA PARA SUBIR ARCHIVOS DE DOCUMENTACION
//=================================================================

//        .directive('uploaderModel', ["$parse", function ($parse) {
//                return {
//                    restrict: 'A',
//                    link: function (scope, iElement, iAttrs)
//                    {
//                        iElement.on("change", function (e)
//                        {
//                            $parse(iAttrs.uploaderModel).assign(scope, iElement[0].files[0]);
//                        });
//                    }
//                };
//            }]).service('subirDocs', ["$http", "$q", function ($http, $q)
//    {
//        this.archivoInstalaciones = function (file, valor)
//        {
//            var deferred = $q.defer();
//            var pdf = new FormData();
//            pdf.append("file", file);
//
//            return $http.post("proveedores/php/guardarInstalaciones.php?id=" + valor, pdf, {
//                headers: {
//                    "Content-type": undefined
//                },
//                transformRequest: angular.identity
//            })
//                    .success(function (res)
//                    {
//                        deferred.resolve(res);
//                    })
//                    .error(function (msg, code)
//                    {
//                        deferred.reject(msg);
//                    });
//            return deferred.promise;
//        };
//
//        this.archivoContrato = function (file, valor)
//        {
//            var deferred = $q.defer();
//            var pdf1 = new FormData();
//            pdf1.append("file", file);
//
//            return $http.post("proveedores/php/guardarContratos.php?id=" + valor, pdf1, {
//                headers: {
//                    "Content-type": undefined
//                },
//                transformRequest: angular.identity
//            })
//                    .success(function (res)
//                    {
//                        deferred.resolve(res);
//                    })
//                    .error(function (msg, code)
//                    {
//                        deferred.reject(msg);
//                    });
//            return deferred.promise;
//        };
//        this.archivoApiario = function (file, valor)
//        {
//            var deferred = $q.defer();
//            var pdf2 = new FormData();
//            pdf2.append("file", file);
//
//            return $http.post("proveedores/php/guardarApiarios.php?id=" + valor, pdf2, {
//                headers: {
//                    "Content-type": undefined
//                },
//                transformRequest: angular.identity
//            })
//                    .success(function (res)
//                    {
//                        deferred.resolve(res);
//                    })
//                    .error(function (msg, code)
//                    {
//                        deferred.reject(msg);
//                    });
//            return deferred.promise;
//        };
//        this.archivoCarta = function (file, valor)
//        {
//            var deferred = $q.defer();
//            var pdf3 = new FormData();
//            pdf3.append("file", file);
//
//            return $http.post("proveedores/php/guardarCartas.php?id=" + valor, pdf3, {
//                headers: {
//                    "Content-type": undefined
//                },
//                transformRequest: angular.identity
//            })
//                    .success(function (res)
//                    {
//                        deferred.resolve(res);
//                        console.log(res);
//                    })
//                    .error(function (msg, code)
//                    {
//                        deferred.reject(msg);
//                    });
//            return deferred.promise;
//        };
//        this.archivoRFCS = function (file, valor)
//        {
//            var deferred = $q.defer();
//            var pdf4 = new FormData();
//            pdf4.append("file", file);
//
//            return $http.post("proveedores/php/guardarRFC.php?id=" + valor, pdf4, {
//                headers: {
//                    "Content-type": undefined
//                },
//                transformRequest: angular.identity
//            })
//                    .success(function (res)
//                    {
//                        console.log(res);
//                        deferred.resolve(res);
//                    })
//                    .error(function (msg, code)
//                    {
//                        deferred.reject(msg);
//                        console.log(msg);
//                    });
//            return deferred.promise;
//            console.log(deferred.promise);
//        };
//        this.archivoSagarpa = function (file, valor)
//        {
//            var deferred = $q.defer();
//            var pdf5 = new FormData();
//            pdf5.append("file", file);
//
//            return $http.post("proveedores/php/guardarSagarpa.php?id=" + valor, pdf5, {
//                headers: {
//                    "Content-type": undefined
//                },
//                transformRequest: angular.identity
//            })
//                    .success(function (res)
//                    {
//                        deferred.resolve(res);
//                    })
//                    .error(function (msg, code)
//                    {
//                        deferred.reject(msg);
//                    });
//            return deferred.promise;
//        };
//    }]);