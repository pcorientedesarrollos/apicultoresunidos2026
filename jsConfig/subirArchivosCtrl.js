form.controller('subirArchivosCtrl', ['$scope', '$routeParams', '$http', 'cargar', 'growl', function ($scope, $routeParams, $http, cargar, growl) {

        $scope.valor = $routeParams.idProveedor;
        $scope.docContrato = {};
        $scope.docINE = {};
        $scope.docInstalacion = {};
        $scope.docApiario = {};
        $scope.docCarta = {};
        $scope.docPagare = {};
        $scope.comboArchivo = {};
        $scope.comboArchivo.formato = "2";
        $scope.archivos = {};
        $scope.archivos.idFormato = "2";
        if ($scope.valor > 0) {
            $scope.$watch('archivos.idFormato', function () {
                $scope.tipoFormato = $scope.archivos.idFormato;
                if ($scope.tipoFormato == 1) {
                    $http.get('proveedores/php/getContratoMercantil.php?id=' + $scope.valor).success(function (data) {
                        $scope.docContrato.images = data;
                    });
                } else {
                    $http.get('proveedores/php/getContratoMercantil.php?pdf=0' + "&id=" + $scope.valor).success(function (data) {
                        angular.forEach(data, function (a) {
                            var nombre = a.archivoContrato.split("/");
                            a.nombreImagen = nombre[1];
                        });
                        $scope.docContrato.images = data;

                    });
                }
            });
            $scope.$watch('archivos.idFormato', function () {
                $scope.tipoDeFormato = $scope.archivos.idFormato;
                if ($scope.tipoDeFormato == 1) {
                    $http.get('proveedores/php/getINE.php?id=' + $scope.valor).success(function (data) {
                        $scope.docINE.images = data;
                    });
                } else {
                    $http.get('proveedores/php/getINE.php?pdf=0' + "&id=" + $scope.valor).success(function (data) {
                        angular.forEach(data, function (a) {
                            var ineDoc = a.archivoINE.split("/");
                            a.nombreINE = ineDoc[1];
                        });
                        $scope.docINE.images = data;
                        console.log(data);
                    });
                }
            });

            $scope.$watch('archivos.idFormato', function () {
                $scope.formatoTipo = $scope.archivos.idFormato;
                if ($scope.formatoTipo == 1) {
                    $http.get('proveedores/php/getRevisionInstalacion.php?id=' + $scope.valor).success(function (data) {
                        $scope.docInstalacion.images = data;
                    });
                } else {
                    $http.get('proveedores/php/getRevisionInstalacion.php?pdf=0' + "&id=" + $scope.valor).success(function (data) {
                        angular.forEach(data, function (a) {
                            var instaDoc = a.archivoInstalacion.split("/");
                            a.nombreInsta = instaDoc[1];
                        });
                        $scope.docInstalacion.images = data;
                    });
                }
            });

            $scope.$watch('archivos.idFormato', function () {
                $scope.formatoDeTipo = $scope.archivos.idFormato;
                if ($scope.formatoDeTipo == 1) {
                    $http.get('proveedores/php/getRevisionApiario.php?id=' + $scope.valor).success(function (data) {
                        $scope.docApiario.images = data;
                    });
                } else {
                    $http.get('proveedores/php/getRevisionApiario.php?pdf=0' + "&id=" + $scope.valor).success(function (data) {
                        angular.forEach(data, function (a) {
                            var ApiarioDoc = a.archivoApiario.split("/");
                            a.nombreApiario = ApiarioDoc[1];
                        });
                        $scope.docApiario.images = data;
                    });
                }
            });

            $scope.$watch('archivos.idFormato', function () {
                $scope.claseFormato = $scope.archivos.idFormato;
                if ($scope.claseFormato == 1) {
                    $http.get('proveedores/php/getCartaResponsiva.php?id=' + $scope.valor).success(function (data) {
                        $scope.docCarta.images = data;
                    });
                } else {
                    $http.get('proveedores/php/getCartaResponsiva.php?pdf=0' + "&id=" + $scope.valor).success(function (data) {
                        angular.forEach(data, function (a) {
                            var cartaDoc = a.archivoCarta.split("/");
                            a.nombreCarta = cartaDoc[1];
                        });
                        $scope.docCarta.images = data;
                    });
                }
            });

            $scope.$watch('archivos.idFormato', function () {
                $scope.archivoT = $scope.archivos.idFormato;
                if ($scope.archivoT == 1) {
                    $http.get('proveedores/php/getPagare.php?id=' + $scope.valor).success(function (data) {
                        $scope.docPagare.images = data;
                    });
                } else {
                    $http.get('proveedores/php/getPagare.php?pdf=0' + "&id=" + $scope.valor).success(function (data) {
                        angular.forEach(data, function (a) {
                            var pagareDoc = a.archivoPagare.split("/");
                            a.nombrePagare = pagareDoc[1];
                        });
                        $scope.docPagare.images = data;

                    });
                }
            });

        }

        $scope.modalDocumento = function (valor, id) {
            $("#documentos").modal();
            switch (valor) {
                case 1:
                    $http.post("proveedores/php/traeDocumento.php?idContrato=" + id).success(function (data) {
                        $scope.nombreArchivo = data;
                    });
                    break;
                case 2:
                    $http.post("proveedores/php/traeDocumento.php?idIne=" + id).success(function (data) {
                        $scope.nombreArchivo = data;
                    });
                    break;
                case 3:
                    $http.post("proveedores/php/traeDocumento.php?idInstalacion=" + id).success(function (data) {
                        $scope.nombreArchivo = data;
                    });
                    break;
                case 4:
                    $http.post("proveedores/php/traeDocumento.php?idApiario=" + id).success(function (data) {
                        $scope.nombreArchivo = data;
                    });
                    break;
                case 5:
                    $http.post("proveedores/php/traeDocumento.php?idCarta=" + id).success(function (data) {
                        $scope.nombreArchivo = data;
                    });
                    break;
                case 6:
                    $http.post("proveedores/php/traeDocumento.php?idPagare=" + id).success(function (data) {
                        $scope.nombreArchivo = data;
                    });
                    break;
            }
        };

        //=================================================================
        // FUNCION PARA SUBIR, MOSTRAR Y ELIMINAR "CONTRATO MERCANTIL"
        //=================================================================
        $scope.subirContrato = function (idProveedor) {
            $scope.tipo = $scope.comboArchivo.formato;
            if ($scope.tipo == undefined) {
                growl.error("Seleccione un tipo de archivo");
            } else if ($scope.file == undefined) {
                growl.error("Seleccione un archivo");
            } else {
                var name = JSON.stringify(idProveedor);
                var file = $scope.file;
                cargar.contrato(file, $scope.valor, $scope.tipo);
                $http.get('proveedores/php/getContratoMercantil.php?id=' + $scope.valor).success(function (data) {
                    $scope.docContrato = data;
                    swal("Excelente!", "Archivo guardado!", "success");
                });
            }
        };

        $scope.eliminarDocContrato = function (idContrato) {
            $http.get('proveedores/php/eliminarContratoMercantil.php?id=' + idContrato).success(function (respuesta) {
                $http.get('proveedores/php/getContratoMercantil.php?id=' + $scope.valor).success(function (data) {
                    $scope.docContrato = data;
                });
                growl.success("Archivo eliminado");
            });
        };
        //------------------------------------------------------------------//

        //=================================================================
        // FUNCION PARA SUBIR, MOSTRAR Y ELIMINAR "INE"
        //=================================================================
        $scope.subirINE = function (idProveedor) {
            $scope.tipo = $scope.comboArchivo.formato;
            if ($scope.tipo == undefined) {
                growl.error("Seleccione un tipo de archivo");
            } else if ($scope.file == undefined) {
                growl.error("Seleccione un archivo");
            } else {
                var name = JSON.stringify(idProveedor);
                var file = $scope.file;
                cargar.ine(file, $scope.valor, $scope.tipo);
                $http.get('proveedores/php/getINE.php?id=' + $scope.valor).success(function (data) {
                    $scope.docINE.images = data;
                    swal("Excelente!", "Archivo guardado!", "success");
                });
            }
            console.log($scope.file);
        };

        $scope.eliminarDocINE = function (idINE) {
            $http.get('proveedores/php/eliminarINE.php?id=' + idINE).success(function () {
                $http.get('proveedores/php/getINE.php?id=' + $scope.valor).success(function (data) {
                    $scope.docINE.images = data;
                });
                growl.success("Archivo eliminado");
            });
        };
        //------------------------------------------------------------------//

        //=================================================================
        // FUNCION PARA SUBIR, MOSTRAR Y ELIMINAR "INSTALACIONES"
        //=================================================================
        $scope.subirInstalacion = function (idProveedor) {
            $scope.tipo = $scope.comboArchivo.formato;
            if ($scope.tipo == undefined) {
                growl.error("Seleccione un tipo de archivo");
            } else if ($scope.file == undefined) {
                growl.error("Seleccione un archivo");
            } else {
                var name = JSON.stringify(idProveedor);
                var file = $scope.file;
                cargar.instalacion(file, $scope.valor, $scope.tipo);
                $http.get('proveedores/php/getRevisionInstalacion.php?id=' + $scope.valor).success(function (data) {
                    $scope.docInstalacion.images = data;
                    swal("Excelente!", "Archivo guardado!", "success");
                });
            }

        };

        $scope.eliminarDocInstalacion = function (idInstalacion) {
            $http.get('proveedores/php/eliminarRevisionInstalacion.php?id=' + idInstalacion).success(function () {
                $http.get('proveedores/php/getRevisionInstalacion.php?id=' + $scope.valor).success(function (data) {
                    $scope.docInstalacion.images = data;
                });
                growl.success("Archivo eliminado");
            });
        };
        //------------------------------------------------------------------//

        //=================================================================
        // FUNCION PARA MOSTRAR Y ELIMINAR "REVISION A APIARIOS"
        //=================================================================
        $scope.subirApiario = function (idProveedor) {
            $scope.clase = $scope.comboArchivo.formato;
            if ($scope.clase == undefined) {
                growl.error("Seleccione un tipo de archivo");
            } else if ($scope.file == undefined) {
                growl.error("Seleccione un archivo");
            } else {
                var name = JSON.stringify(idProveedor);
                var file = $scope.file;
                cargar.apiario(file, $scope.valor, $scope.clase);
                $http.get('proveedores/php/getRevisionApiario.php?id=' + $scope.valor).success(function (data) {
                    $scope.docApiario.images = data;
                    swal("Excelente!", "Archivo guardado!", "success");
                });
            }
        };
        $scope.eliminarDocApiario = function (idApiario) {
            $http.get('proveedores/php/eliminarRevisionApiario.php?id=' + idApiario).success(function () {
                $http.get('proveedores/php/getRevisionApiario.php?id=' + $scope.valor).success(function (data) {
                    $scope.docApiario.images = data;
                });
                growl.success("Archivo eliminado");
            });
        };
        //------------------------------------------------------------------//

        //=================================================================
        // FUNCION PARA MOSTRAR Y ELIMINAR "CARTA RESPONSIVA"
        //=================================================================
        $scope.subirCarta = function (idProveedor) {
            $scope.claseArchivo = $scope.comboArchivo.formato;
            if ($scope.claseArchivo == undefined) {
                growl.error("Seleccione un tipo de archivo");
            } else if ($scope.file == undefined) {
                growl.error("Seleccione un archivo");
            } else {
                var name = JSON.stringify(idProveedor);
                var file = $scope.file;
                cargar.pagare(file, $scope.valor, $scope.claseArchivo);
                $http.get('proveedores/php/getCartaResponsiva.php?id=' + $scope.valor).success(function (data) {
                    $scope.docCarta.images = data;
                    swal("Excelente!", "Archivo guardado!", "success");
                });
            }
        };

        $scope.eliminarDocCarta = function (idCarta) {
            $scope.docCarta = {};
            $http.get('proveedores/php/eliminarCartaResponsiva.php?id=' + idCarta).success(function () {
                $http.get('proveedores/php/getCartaResponsiva.php?id=' + $scope.valor).success(function (data) {
                    $scope.docCarta.images = data;
                });
                growl.success("Archivo eliminado");
            });
        };
        //------------------------------------------------------------------//

        //=================================================================
        // FUNCION PARA MOSTRAR Y ELIMINAR "ARCHIVOS PAGARÉS"
        //=================================================================
        $scope.subirPagare = function (idProveedor) {
            $scope.claseDeArchivo = $scope.comboArchivo.formato;
            if ($scope.claseDeArchivo == undefined) {
                growl.error("Seleccione un tipo de archivo");
            } else if ($scope.file == undefined) {
                growl.error("Seleccione un archivo");
            } else {
                var name = JSON.stringify(idProveedor);
                var file = $scope.file;
                cargar.pagare(file, $scope.valor, $scope.claseDeArchivo);
                $http.get('proveedores/php/getPagare.php?id=' + $scope.valor).success(function (data) {
                    $scope.docPagare.images = data;
                    swal("Excelente!", "Archivo guardado!", "success");
                });
            }
        };

        $scope.eliminarDocPagare = function (idPagare) {
            $scope.docPagare = {};
            $http.get('proveedores/php/eliminarPagare.php?id=' + idPagare).success(function () {
                $http.get('proveedores/php/getPagare.php?id=' + $scope.valor).success(function (data) {
                    $scope.docPagare.images = data;
                });
                growl.success("Archivo eliminado");
            });
        };
        //------------------------------------------------------------------//



    }])
        .directive('uploaderModel', ["$parse", function ($parse) {
                return {
                    restrict: 'A',
                    link: function (scope, iElement, iAttrs)
                    {
                        iElement.on("change", function (e)
                        {
                            $parse(iAttrs.uploaderModel).assign(scope, iElement[0].files[0]);
                        });
                    }
                };
            }]).service('cargar', ["$http", "$q", function ($http, $q)
    {
        this.contrato = function (file, idProveedor, tipo)
        {
            var deferred = $q.defer();
            var im = new FormData();
            im.append("file", file);

            return $http.post("proveedores/php/guardarContratos.php?id=" + idProveedor + "&tipo=" + tipo, im, {
                headers: {
                    "Content-type": undefined
                },
                transformRequest: angular.identity
            })
                    .success(function (res)
                    {
                        deferred.resolve(res);
                    })
                    .error(function (msg, code)
                    {
                        deferred.reject(msg);
                    });
            return deferred.promise;
        };
        this.ine = function (file, idProveedor, tipo)
        {
            var deferred = $q.defer();
            var ineI = new FormData();
            ineI.append("file", file);

            return $http.post("proveedores/php/guardarINE.php?id=" + idProveedor + "&tipo=" + tipo, ineI, {
                headers: {
                    "Content-type": undefined
                },
                transformRequest: angular.identity
            })
                    .success(function (res)
                    {
                        deferred.resolve(res);
                    })
                    .error(function (msg, code)
                    {
                        deferred.reject(msg);
                    });
            return deferred.promise;
        };
        this.instalacion = function (file, idProveedor, tipo)
        {
            var deferred = $q.defer();
            var insta = new FormData();
            insta.append("file", file);

            return $http.post("proveedores/php/guardarInstalaciones.php?id=" + idProveedor + "&tipo=" + tipo, insta, {
                headers: {
                    "Content-type": undefined
                },
                transformRequest: angular.identity
            })
                    .success(function (res)
                    {
                        deferred.resolve(res);
                    })
                    .error(function (msg, code)
                    {
                        deferred.reject(msg);
                    });
            return deferred.promise;
        };
        this.apiario = function (file, idProveedor, tipo)
        {
            var deferred = $q.defer();
            var apiarioS = new FormData();
            apiarioS.append("file", file);

            return $http.post("proveedores/php/guardarApiarios.php?id=" + idProveedor + "&tipo=" + tipo, apiarioS, {
                headers: {
                    "Content-type": undefined
                },
                transformRequest: angular.identity
            })
                    .success(function (res)
                    {
                        deferred.resolve(res);
                    })
                    .error(function (msg, code)
                    {
                        deferred.reject(msg);
                    });
            return deferred.promise;
        };
        this.carta = function (file, idProveedor, tipo)
        {
            var deferred = $q.defer();
            var car = new FormData();
            car.append("file", file);

            return $http.post("proveedores/php/guardarCartas.php?id=" + idProveedor + "&tipo=" + tipo, car, {
                headers: {
                    "Content-type": undefined
                },
                transformRequest: angular.identity
            })
                    .success(function (res)
                    {
                        deferred.resolve(res);
                    })
                    .error(function (msg, code)
                    {
                        deferred.reject(msg);
                    });
            return deferred.promise;
        };
        this.pagare = function (file, idProveedor, tipo)
        {
            var deferred = $q.defer();
            var pag = new FormData();
            pag.append("file", file);

            return $http.post("proveedores/php/guardarPagare.php?id=" + idProveedor + "&tipo=" + tipo, pag, {
                headers: {
                    "Content-type": undefined
                },
                transformRequest: angular.identity
            })
                    .success(function (res)
                    {
                        deferred.resolve(res);
                    })
                    .error(function (msg, code)
                    {
                        deferred.reject(msg);
                    });
            return deferred.promise;
        };
    }]);