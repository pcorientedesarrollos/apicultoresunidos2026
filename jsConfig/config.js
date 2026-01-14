"use strict";
var form = angular.module('ctr', [
    'ngRoute',
    'jcs-autoValidate',
    'angular-growl',
    'ngCookies',
    'ngResource',
    'ngSanitize',
    'ngCsv',
    'localytics.directives',
    'ui.bootstrap',
    'ngMask']);

form.config(function ($routeProvider) {
    $routeProvider.when('/', {
        //=== VISTA PRINCIPAL ===//
        templateUrl: 'login.php',
        controller: 'login'

        //=== MENU PROVEEDORES ===//
        // }).when('/proveedores', {
        //     templateUrl: 'proveedores/proveedores.html',
        //     controller: 'proveedores',
        //     controllerAs: 'vm'
        // }).when('/nvoProveedor/:idProveedor', {
        //     templateUrl: 'proveedores/nuevoProveedor.html',
        //     controller: 'proveedores'
    }).when('/subirPdf/:idAlmacen', {
        templateUrl: 'precios/subirPdf.html'
    }).when('/subirPdfPago/:idAlmacen', {
        templateUrl: 'precios/subirPdfPago.html'
        // }).when('/carta/:idProveedor', {
        //     templateUrl: 'proveedores/subirCarta.html'
        // }).when('/contrato/:idProveedor', {
        //     templateUrl: 'proveedores/o .html'
        // }).when('/apiario/:idProveedor', {
        //     templateUrl: 'proveedores/subirRApiarios.html'
        // }).when('/instalacion/:idProveedor', {
        //     templateUrl: 'proveedores/subirRInstalaciones.html'
        // }).when('/upDoc/:idProveedor', {
        //     templateUrl: 'proveedores/subirDocumentos.html',
        //     controller: 'subirArchivosCtrl'
        //=== MENU COMPRAS ===//
        // }).when('/compradores', {
        //     templateUrl: 'compras/paginas/comprador.html',
        //     controller: 'compradorCtrl'
        // }).when('/editarComprador/:idComprador', {
        //     templateUrl: 'compras/paginas/foreditarComprador.html',
        //     controller: 'compradorCtrl'
        // }).when('/zonasAsignadas/:idComprador', {
        //     templateUrl: 'compras/paginas/zonaAsig.html',
        //     controller: 'compradorCtrl'
        //     //    }).when('/zonas', {
        //     //        templateUrl: 'compras/paginas/zonas.html',
        //     //        controller: 'formZonaCtrl'
        // }).when('/zonas', {
        //     templateUrl: 'compras/paginas/formZona.html',
        //     controller: 'formZonaCtrl'
        // }).when('/editarZona/:idzona', {
        //     templateUrl: 'compras/paginas/forEditarZona.html',
        //     controller: 'formZonaCtrl'
        // }).when('/verMapa', {
        //     templateUrl: 'compras/paginas/zonas.html',
        //     controller: 'formZonaCtrl'
        // }).when('/localidades', {
        //     templateUrl: 'compras/paginas/formLocalidad.html',
        //     controller: 'formLocalidadCtrl'
        // }).when('/editarLocalidad/:idlocalidad', {
        //     templateUrl: 'compras/paginas/forEditarLocalidad.html',
        //     controller: 'formLocalidadCtrl'
        // }).when('/requerimiento', {
        //     templateUrl: 'reqDepositoCompra/depositoCompra.html',
        //     controller: 'requerimientoCtrl'
        // }).when('/nvoReq/:idRequisicion', {
        //     templateUrl: 'reqDepositoCompra/nuevoRequerimiento.html',
        //     controller: 'requerimientoCtrl'
    }).when('/indicadores', {
        templateUrl: 'compras/paginas/estadisticas.html',
        controller: 'estadisticaCtrl'
        //    }).when('/repCompras', {
        //        templateUrl: 'compras/paginas/reportesCompras.html',
        //        controller: 'compradorCtrl'
    }).when('/docCompras', {
        templateUrl: 'compras/paginas/documentacion.html'
        // }).when('/docProveedores', {
        //     templateUrl: 'proveedores/verProveedores.html',
        //     controller: 'proveedores'

        //=== MENU ALMACEN ===//
    }).when('/repDescarga', {
        templateUrl: 'almacen/reporteDescarga.html',
        controller: 'reportesDescargaCtrl'
    }).when('/nvoDescarga/:idReporte/:idTipoDeMiel', {
        templateUrl: 'almacen/nuevoReporteDescarga.html',
        controller: 'reportesDescargaCtrl'
    }).when('/tambores', {
        templateUrl: 'almacen/menuEntradaTambores.html',
        controller: 'almacen'
    }).when('/nvoAlmacen/:idAlmacen/:opcionCosecha', {
        templateUrl: 'almacen/nuevaEntradaTambores.html',
        controller: 'almacen'
    }).when('/entradaCera', {
        templateUrl: 'almacen/menuEntradaCera.html',
        controller: 'entradaCeraCtrl'
    }).when('/nuevaEntradaCera/:idEntradaCera', {
        templateUrl: 'almacen/nuevaEntradaCera.html',
        controller: 'entradaCeraCtrl'
    }).when('/salidaCera', {
        templateUrl: 'almacen/menuSalidaCera.html',
        controller: 'salidaCeraCtrl'
    }).when('/nuevaSalidaCera/:idSalidaCera', {
        templateUrl: 'almacen/nuevaSalidaCera.html',
        controller: 'salidaCeraCtrl'
    }).when('/salidaApicola', {
        templateUrl: 'almacen/menuSalidaApicola.html',
        controller: 'salidaApicolaCtrl'
    }).when('/nuevaSalidaApicola/:idSalidaApicola', {
        templateUrl: 'almacen/nuevaSalidaApicola.html',
        controller: 'salidaApicolaCtrl'
    }).when('/entradaApicola', {
        templateUrl: 'almacen/menuEntradaApicola.html',
        controller: 'entradaApicolaCtrl'
    }).when('/nuevaEntradaApicola/:idEntradaApicola', {
        templateUrl: 'almacen/nuevaEntradaApicola.html',
        controller: 'entradaApicolaCtrl'
    }).when('/cubetas', {
        templateUrl: 'almacen/menuEntradaCubetas.html',
        controller: 'almacenCubetas'
    }).when('/nvoAlmacenQ/:idAlmacen/:opcionCosechaC', {
        templateUrl: 'almacen/nuevaEntradaCubetas.html',
        controller: 'almacenCubetas'
    }).when('/repCarga', {
        templateUrl: 'almacen/reporteCarga.html',
        controller: 'reportesCargasCtrl'
    }).when('/nvoCarga/:idReporte/:idTipoDeMiel', {
        templateUrl: 'almacen/nuevoReporteCarga.html',
        controller: 'reportesCargasCtrl'
    }).when('/condiciones', {
        templateUrl: 'almacen/condicionesAlmacenamiento.html',
        controller: 'condicionesCtrl'
    }).when('/nvaCondicion/:idMes/:idAlmacen', {
        templateUrl: 'almacen/nuevaCondicionAlmacenamiento.html',
        controller: 'condicionesCtrl'
    }).when('/disposiciones', {
        templateUrl: 'almacen/disposicionDelPersonal.html',
        controller: 'disposicionesCtrl'
    }).when('/nvaDisposicion/:idMes', {
        templateUrl: 'almacen/nuevaDisposicionPersonal.html',
        controller: 'disposicionesCtrl'
    }).when('/entradaPrima', {
        templateUrl: 'almacen/entradaMateriaPrima.html',
        controller: 'entradaMateriaPrimaCtrl'
    }).when('/nvaMatPrima/:idEntradaMateria/:tipoDeMiel', {
        templateUrl: 'almacen/nuevaEntradaMateriaPrima.html',
        controller: 'entradaMateriaPrimaCtrl'
    }).when('/salidaPrima', {
        templateUrl: 'almacen/salidaMateriaPrima.html',
        controller: 'salidaMateriaPrimaCtrl'
    }).when('/nvaSalidaMatPrima/:idSalidaMateria/:tipoDeMiel', {
        templateUrl: 'almacen/nuevaSalidaMateriaPrima.html',
        controller: 'salidaMateriaPrimaCtrl'
    }).when('/conciliacion', {
        templateUrl: 'almacen/conciliacionDeTambores.html',
        controller: 'conciliacionCtrl'
    }).when('/desgloseS', {
        templateUrl: 'almacen/desgloseSalidas.html',
        controller: 'salidaMateriaPrimaCtrl'
    }).when('/desgloseE', {
        templateUrl: 'almacen/desgloseEntradas.html',
        controller: 'salidaMateriaPrimaCtrl'
    }).when('/operadores', {
        templateUrl: 'almacen/proveedorAlmacen.html',
        controller: 'choferesCtrl'
    }).when('/proveAlm/:idOperador', {
        templateUrl: 'almacen/nvoProveedorAlmacen.html',
        controller: 'choferesCtrl'
    }).when('/docAlmacen', {
        templateUrl: 'almacen/documentacion.html'
    }).when('/repAlmacen', {
        templateUrl: 'almacen/combosReportesCubetas.html',
        controller: 'almacenCubetas'
    }).when('/lstPesos', {
        templateUrl: 'almacen/listaDePesos.html',
        controller: 'listaPesosCtrl'
    }).when('/nvoRepPesos/:idTamborPeso', {
        templateUrl: 'almacen/reporteListaPesos.html',
        controller: 'listaPesosCtrl'
    }).when('/menuSobrantes', {
        templateUrl: 'almacenSobrantes/menuEntradaMielSobrante.html',
        controller: 'almacenSobranteCtrl'
        // }).when('/mielSobrante/:idEntradaSobrante', {
        //     templateUrl: 'almacenSobrantes/nuevaEntradaSobrante.html',
        //     controller: 'almacenSobranteCtrl'
    }).when('/mielSobrante/:idEncabezadoSobrante', {
        templateUrl: 'almacenSobrantes/nuevaEntradaSobrante.html',
        controller: 'almacenSobranteCtrl'

        //=== MENU ANALISIS ===//
        // }).when('/laboratorio', {
        //     templateUrl: 'laboratorio/menuLaboratorio.html',
        //     controller: 'laboratorio',
        //     controllerAs: 'vm'
        // }).when('/nvoLaboratorio/:idAlmacen/:tipoDeMiel', {
        //     templateUrl: 'laboratorio/nuevoLaboratorio.html',
        //     controller: 'laboratorio'
    }).when('/configuracion', {
        templateUrl: 'laboratorio/configuracionLaboratorio.html',
        controller: 'configLaboratorio'
    }).when('/repLaboratorio', {
        templateUrl: 'laboratorio/reportesLaboratorio.html',
        controller: 'laboratorio'
    }).when('/repHumedades', {
        templateUrl: 'laboratorio/ReporteHumedades.html',
        controller: 'laboratorio'

        //=== MENU CALIDAD ===//
    }).when('/experimental', {
        templateUrl: 'calidad/menuLotesExperimentales.html',
        controller: 'calidad',
        controllerAs: 'cal'
    }).when('/nvaCalidad', {
        templateUrl: 'calidad/nuevoLoteExperimental.html',
        controller: 'calidad',
        controllerAs: 'cal'
    })
    // .when('/nvaCalidadSF', {
    //     templateUrl: 'calidad/experimentalSF.html',
    //     controller: 'calidad'
    // })
    // .when('/ediCalidad/:idLoteExperimental/:opcionCosechaEx', {
    //     templateUrl: 'calidad/edicionLoteExperimental.html',
    //     controller: 'calidad'
    // })
    .when('/masMiel/:idLoteExperimental/:opcionCosechaEx', {
        templateUrl: 'calidad/agregarMasMielAExperimental.html',
        controller: 'calidad'
    }).when('/interno', {
        templateUrl: 'calidad/menuLotesInternos.html',
        controller: 'calidad',
        controllerAs: 'cal'
    })
    // .when('/vrLotInt/:idLoteInterno/:opcionCosechaInt', {
    //     templateUrl: 'calidad/verLoteInternoArmado.html',
    //     controller: 'calidad'
    // })
    .when('/especificaciones/', {
        templateUrl: 'calidad/menuEspecificacionesCliente.html',
        controller: 'especificacionCtrl'
    }).when('/nvaEspecificacion/:idLoteInterno/:opcionCosechaEspecificacion', {
        templateUrl: 'calidad/nuevasEspecificaciones.html',
        controller: 'especificacionCtrl'

        //=== MENU TRAZABILIDAD ===//
    }).when('/tEntrada', {
        templateUrl: 'trazabilidad/trazabilidadEntrada.html',
        controller: 'trazabilidadEntradaCtrl'
    }).when('/tLaboratorio', {
        templateUrl: 'trazabilidad/trazabilidadLaboratorio.html',
        controller: 'trazabilidadAnalisisCtrl'
    }).when('/nvaTrazaLab/:idLoteInterno/:tipoMiel', {
        templateUrl: 'trazabilidad/nuevaTrazabilidadLaboratorio.html',
        controller: 'trazabilidadAnalisisCtrl'
    }).when('/tSalida', {
        templateUrl: 'trazabilidad/trazabilidadSalida.html',
        controller: 'trazabilidadSalidaCtrl'
    }).when('/nvaTrazaSalida/:idLoteInterno/:tipoMiel', {
        templateUrl: 'trazabilidad/nuevaTrazabilidadSalida.html',
        controller: 'trazabilidadSalidaCtrl'

        //=== MENU PRODUCCION ===//
    }).when('/fechas', {
        templateUrl: 'produccion/asignacionFechaProcesoYEnvasado.html',
        controller: 'produccionCtrl'
    }).when('/nvasFechs/:idLoteInterno/:opcionCosechaFechas', {
        templateUrl: 'produccion/asignarNvasFechas.html',
        controller: 'produccionCtrl'
    }).when('/control', {
        templateUrl: 'produccion/controlDeProceso.html',
        controller: 'produccionCtrl'
    }).when('/nvoProceso/:idReporteProceso/:opcionCosechaProceso', {
        templateUrl: 'produccion/reporteDeProceso.html',
        controller: 'produccionCtrl'
    }).when('/envasado', {
        templateUrl: 'produccion/controlDeEnvasado.html',
        controller: 'envasadoCtrl'
    }).when('/nvoEnvasado/:idReporteEnvasado/:idTipoDeMiel', {
        templateUrl: 'produccion/reporteDeEnvasado.html',
        controller: 'envasadoCtrl'
    }).when('/docProduccion', {
        templateUrl: 'produccion/documentacion.html'
    }).when('/inventarioTambores', {
        templateUrl: 'produccion/inventarioDeTambores.html',
        controller: 'inventarioTamboresCtrl'
    }).when('/verFolios/:zona', {
        templateUrl: 'produccion/verFolios.html',
        controller: 'inventarioTamboresCtrl'
    }).when('/mielDisponible', {
        templateUrl: 'inventarios/mielDisponibleAlmacen.html',
        controller: 'mielDisponibleCtrl'
    }).when('/mielSobranteDisponible', {
        templateUrl: 'inventarios/mielDisponibleAlmacenSobrante.html',
        controller: 'mielDisponibleSobranteCtrl'

        //=== MENU ADMINISTRACION ===//
    }).when('/personal', {
        templateUrl: 'personalOaxacaMiel/personalOM.html',
        controller: 'personalOMCtrl'
    }).when('/nvoPersonal/:idPersonalOM', {
        templateUrl: 'personalOaxacaMiel/nuevoPersonalOM.html',
        controller: 'personalOMCtrl'
    }).when('/precios', {
        templateUrl: 'precios/precios.php',
        controller: 'almacen'
    }).when('/configPrecios/:idAlmacen/:opcionCosecha', {
        templateUrl: 'precios/nvoAlmacen.php',
        controller: 'almacen'
    }).when('/pagoCub', {
        templateUrl: 'precios/preciosCub.html',
        controller: 'precioCubeta'
    }).when('/configPreciosCub/:idAlmacen/:opcionCosechaCub', {
        templateUrl: 'precios/asignaPreciosCubeta.php',
        controller: 'precioCubeta'

    }).when('/pagoMantto', {
        templateUrl: 'controlMantenimiento/pagoMantto.html',
        controller: 'pagoManttoCtrl'
    }).when('/menuManttosArea/:idArea', {
        templateUrl: 'controlMantenimiento/menuMantenimientosPorArea.html',
        controller: 'pagoManttoCtrl'
    }).when('/pagarMantto/:idMantenimiento', {
        templateUrl: 'controlMantenimiento/nvoPagoMantto.html',
        controller: 'pagoManttoCtrl'

        //=== MENU CONTROL ADMINISTRATIVO ===//
    }).when('/menuBancos', {
        templateUrl: 'controlAdministrativo/menuBancos.html',
        controller: 'auxBancosCtrl'
    }).when('/nvoBanco/:idBanco', {
        templateUrl: 'controlAdministrativo/nuevoBanco.html',
        controller: 'auxBancosCtrl'
    }).when('/auxBancos/:idMesBanco', {
        templateUrl: 'controlAdministrativo/auxiliarBancos.html',
        controller: 'auxBancosCtrl'
    }).when('/nvoAuxBanco/:idCuenta/:nvoAuxBancoIdMes', {
        templateUrl: 'controlAdministrativo/nuevoAuxiliarBancos.html',
        controller: 'auxBancosCtrl'
    }).when('/nvoAuxBanco/:idCuenta', {
        templateUrl: 'controlAdministrativo/nuevoAuxiliarBancos.html',
        controller: 'auxBancosCtrl'
    }).when('/editarMovimiento/:idAuxiliar', {
        templateUrl: 'controlAdministrativo/editarMovimientoAuxiliar.html',
        controller: 'auxBancosCtrl'
    }).when('/menuAuxBancos', {
        templateUrl: 'controlAdministrativo/menuAuxBancos.html',
        controller: 'auxBancosCtrl'
    }).when('/polizas', {
        templateUrl: 'controlAdministrativo/formularioPolizas.html',
        controller: 'auxBancosCtrl'
    }).when('/entreCuentas', {
        templateUrl: 'controlAdministrativo/formularioEntreCuentas.html',
        controller: 'auxBancosCtrl'
    }).when('/cajaChica', {
        templateUrl: 'controlAdministrativo/menuCajaChica.html',
        controller: 'cajaChicaCtrl'
    }).when('/nvaCajaChica/:idMes', {
        templateUrl: 'controlAdministrativo/nuevaCajaChica.html',
        controller: 'cajaChicaCtrl'
    }).when('/nvaVenta', {
        templateUrl: 'controlAdministrativo/nuevoIngreso.html',
        controller: 'cajaChicaCtrl'
    }).when('/nvoEgreso', {
        templateUrl: 'controlAdministrativo/nuevoEgreso.html',
        controller: 'cajaChicaCtrl'
    }).when('/movimiento/:idCajaChica', {
        templateUrl: 'controlAdministrativo/movimiento.html',
        controller: 'cajaChicaCtrl'
    }).when('/inventarioMiel', {
        templateUrl: 'controlAdministrativo/inventarioComprasMiel.html',
        controller: 'inventarioMielCtrl'
    }).when('/conciliacion', {
        templateUrl: 'controlAdministrativo/conciliacionMonetaria.html',
        controller: 'conciliacionMonetariaCtrl'

        //=== MENU INVENTARIO DE EQUIPOS ===//
    }).when('/equipos', {
        templateUrl: 'inventarioDeEquipos/menuEquipos.html',
        controller: 'equiposCtrl'
    }).when('/verLosEquipos/:idArea', {
        templateUrl: 'inventarioDeEquipos/verEquipos.html',
        controller: 'programacionCtrl'
    }).when('/nvoEquipo/:idEquipo', {
        templateUrl: 'inventarioDeEquipos/nvoEquipo.html',
        controller: 'equiposCtrl'
    }).when('/clasificaciones', {
        templateUrl: 'inventarioDeEquipos/menuClasificaciones.html',
        controller: 'clasificacionesCtrl'
    }).when('/nvaClasificacion/:idClasificacion', {
        templateUrl: 'inventarioDeEquipos/nvaClasificacion.html',
        controller: 'clasificacionesCtrl'
    }).when('/areas', {
        templateUrl: 'inventarioDeEquipos/menuAreas.html',
        controller: 'clasificacionesCtrl'
    }).when('/nvaArea/:idArea', {
        templateUrl: 'inventarioDeEquipos/nvaArea.html',
        controller: 'clasificacionesCtrl'
    }).when('/repInventario', {
        templateUrl: 'inventarioDeEquipos/reportesInventario.html',
        controller: 'clasificacionesCtrl'
    }).when('/verReporte/:idOpcion', {
        templateUrl: 'inventarioDeEquipos/verInventario.html',
        controller: 'clasificacionesCtrl'
    }).when('/asignarCostos/:idArea', {
        templateUrl: 'inventarioDeEquipos/asignarCostos.html',
        controller: 'programacionCtrl'

        //=== MENU CONTROL MANTTO ===//
    }).when('/proveeMantto', {
        templateUrl: 'controlMantenimiento/menuTecnicos.html',
        controller: 'tecnicosCtrl'
    }).when('/nvoTecnico/:idProveedorMantto', {
        templateUrl: 'controlMantenimiento/nvoTecnico.html',
        controller: 'tecnicosCtrl'
        //* PROGRAMACION DE FECHAS *//
    }).when('/programacion', {
        templateUrl: 'controlMantenimiento/menuProgramacion.html',
        controller: 'equiposCtrl'
    }).when('/equiposProgramar/:id/:idArea', {
        templateUrl: 'controlMantenimiento/equiposParaProgramar.html',
        controller: 'programacionCtrl'
    }).when('/nvaProgramacion/:idEquipo', {
        templateUrl: 'controlMantenimiento/nvaProgramacion.html',
        controller: 'programacionCtrl'
    }).when('/verProgramacion/:idEquipo', {
        templateUrl: 'controlMantenimiento/verFechasProgramadas.html',
        controller: 'programacionCtrl'
        //* REPORTES MANTTO *//
    }).when('/vistaManttos', {
        templateUrl: 'controlMantenimiento/menuReportesMantenimientos.html',
        controller: 'equiposCtrl'
    }).when('/conManttos/:id/:idArea', {
        templateUrl: 'controlMantenimiento/conManttos.html',
        controller: 'equiposCtrl'
    }).when('/sinManttos/:id/:idArea', {
        templateUrl: 'controlMantenimiento/sinManttos.html',
        controller: 'equiposCtrl'
    }).when('/nvoMantto/:idProgramacion', {
        templateUrl: 'controlMantenimiento/nuevoMantenimiento.html',
        controller: 'manttoCtrl'
    }).when('/modificarMantto/:idEquipo', {
        templateUrl: 'controlMantenimiento/editarMantenimiento.html',
        controller: 'manttoCtrl'
    }).when('/manttoExtra/:idMantenimiento', {
        templateUrl: 'controlMantenimiento/nuevoMantenimiento.html',
        controller: 'manttoCtrl'
    }).when('/manttoExtraNvo/:idEquipo', {
        templateUrl: 'controlMantenimiento/nuevoMantenimiento.html',
        controller: 'manttoCtrl'
    }).when('/manttoEsporadico/:idEquipo', {
        templateUrl: 'controlMantenimiento/manttoEsporadico.html',
        controller: 'manttoCtrl'
    }).when('/repEsporadico/:idEquipo', {
        templateUrl: 'controlMantenimiento/reporteManttoEsporadico.html',
        controller: 'manttoCtrl'
    }).when('/cronograma', {
        templateUrl: 'controlMantenimiento/areaCronograma.html',
        controller: 'manttoCtrl'
    }).when('/verCronograma/:idOpcion', {
        templateUrl: 'controlMantenimiento/cronogramaDeMantenimiento.html',
        controller: 'manttoCtrl'
    }).when('/evaluacion', {
        templateUrl: 'controlMantenimiento/menuEvaluacion.html',
        controller: 'evaluacionCtrl'
    }).when('/nvaEvaluacion/:idEvaluacion', {
        templateUrl: 'controlMantenimiento/nvaEvaluacion.html',
        controller: 'evaluacionCtrl'
        //=== MENU ADMINISTRADOR ===//
    }).when('/cambios', {
        templateUrl: 'control/controlCambios.html',
        controller: 'controlCambiosCtrl'
    }).when('/nvoCtrl/:idControl', {
        templateUrl: 'control/nuevoControlCambio.html',
        controller: 'controlCambiosCtrl'
    }).when('/secciones', {
        templateUrl: 'habilitarSecciones/seccionesYModulos.html',
        controller: 'seccionesCtrl'
    }).when('/secciones/:idSeccion', {
        templateUrl: 'habilitarSecciones/detalleSeccion.html',
        controller: 'seccionesCtrl'
    }).when('/usuariosSistema', {
        templateUrl: 'habilitarSecciones/usuariosSistema.html',
        controller: 'usuariosSistemaCtrl'
    }).when('/usuariosSistema/:idUsuario', {
        templateUrl: 'habilitarSecciones/usuarioSistema.html',
        controller: 'usuariosSistemaCtrl'
    }).when('/perfilesSistema', {
        templateUrl: 'habilitarSecciones/perfilesSistema.html',
        controller: 'perfilesSistemaCtrl'
    }).when('/perfilesSistema/:idPerfil', {
        templateUrl: 'habilitarSecciones/detallePerfil.html',
        controller: 'perfilesSistemaCtrl'

        //=== MENU DEUDORES Y PROVEEDORES ===//
        // }).when('/estadosCuentas', {
        //     templateUrl: 'deudoresProveedores/estadosDeCuentas.html',
        //     controller: 'deudoresProveedoresCtrl'
        // }).when('/saldosDeudores', {
        //     templateUrl: 'deudoresProveedores/saldosIniciales.html',
        //     controller: 'deudoresProveedoresCtrl'
        // }).when('/gastosRealizados', {
        //     templateUrl: 'deudoresProveedores/menuGastosRealizados.html',
        //     controller: 'deudoresProveedoresCtrl'
        // }).when('/menuGastosProveedor/:idProveedor', {
        //     templateUrl: 'deudoresProveedores/menuGastosProveedor.html',
        //     controller: 'deudoresProveedoresCtrl'
        // }).when('/nvoGasto/:idGasto', {
        //     templateUrl: 'deudoresProveedores/nuevoGastoRealizado.html',
        //     controller: 'deudoresProveedoresCtrl'
        // }).when('/concentradoDeudor', {
        //     templateUrl: 'deudoresProveedores/concentradoDeudores.html',
        //     controller: 'deudoresProveedoresCtrl'

        //=== MENU INVENTARIOS ===//
    }).when('/inventarioMP', {
        templateUrl: 'inventarios/inventarioMP.html',
        controller: 'inventarioMPCtrl'

        //=== MENU UTILERIAS ===//
    }).when('/busqueda', {
        templateUrl: 'utilerias/busquedaDeFolios.html',
        controller: 'foliosCtrl'
    }).when('/buscadorEquipo', {
        templateUrl: 'utilerias/busquedaDeEquipos.html',
        controller: 'buscaEquipoCtrl'

        //=== MENU EXPORTACIÓN ===//
    }).when('/exportadores', {
        templateUrl: 'exportacion/menuClientesExportadores.html',
        controller: 'exportadoresCtrl'
    }).when('/exportador/:idClienteExportador', {
        templateUrl: 'exportacion/clienteExportador.html',
        controller: 'exportadoresCtrl'
    }).when('/solicitudCertificado', {
        templateUrl: 'exportacion/solicitudCertificado.html',
        controller: 'exportadoresCtrl'
        // }).when('/nvaSolicitud/:idLoteInterno', {
        //     templateUrl: 'exportacion/formularioCertificado.html',
        //     controller: 'exportadoresCtrl'
    }).when('/nvaSolicitud/:idLoteInterno/:tipoMiel', {
        templateUrl: 'exportacion/formularioCertificado.html',
        controller: 'exportadoresCtrl'
    }).when('/anexo/:idSolicitudCertificado', {
        templateUrl: 'exportacion/anexo.html',
        controller: 'exportadoresCtrl'

        //=== MENU PABLO ===//
    }).when('/login', {
        templateUrl: 'login.php',
        controller: 'login'
    }).when('/home', {
        templateUrl: 'menu.php',
        controller: 'login'
        //Préstamos
    }).when('/controlPrestamos', {
        templateUrl: 'controlAdministrativo/prestamos/controlDePrestamos.html',
        controller: 'prestamosCtrl'
    }).when('/controlPrestamos/:idPrestamo', {
        templateUrl: 'controlAdministrativo/prestamos/detallePrestamo.html',
        controller: 'prestamosCtrl'
    }).when('/controlPrestamos/personal/:tipoDePersona/:idNombre', {
        templateUrl: 'controlAdministrativo/prestamos/detallePrestamoPersonal.html',
        controller: 'prestamosCtrl'


        // Deudores y Proveedores
    }).when('/deudores y proveedores', {
        templateUrl: 'controlAdministrativo/deudoresYProveedores/deudoresYProveedores.html',
        controller: 'deudoresYProveedoresCtrl'

        // Auditoría
    }).when('/auditoria/:idAuditoria', {
        templateUrl: 'almacen/auditoria.html',
        controller: 'auditoriaCtrl'
    }).when('/auditoria/:idZonaAuditoria/:idTipoDeMiel/:idAuditoria', {
        templateUrl: 'almacen/auditoria.html',
        controller: 'auditoriaCtrl'
    }).when('/auditoriasLst', {
        templateUrl: 'almacen/auditoriasLst.html',
        controller: 'auditoriaCtrl'

        // Inventario de cera y productos apícolas+

    }).when('/inventarioCera', {
        templateUrl: 'controlAdministrativo/inventarioDeCera.html',
        controller: 'inventarioCeraCtrl'
    }).when('/inventarioProdApicolas', {
        templateUrl: 'controlAdministrativo/inventarioProdApicolas.html',
        controller: 'inventarioProdApicolasCtrl'
    }).when('/reportesEjecutivos', {
        templateUrl: 'reportesEjecutivos/reportesEjecutivos.html',
        controller: 'reportesEjecutivosCtrl'
    }).when('/catalogoProductos', {
        templateUrl: 'catalogos/catalogoProductos.html',
        controller: 'catalogoProductosCtrl'
    }).when('/catalogoUnidades', {
        templateUrl: 'catalogos/catalogoUnidades.html',
        controller: 'catalogoUnidadesCtrl'
    }).when('/catalogoMielSobrante', {
        templateUrl: 'catalogos/catalogoMielSobrante.html',
        controller: 'catalogoMielSobrante'
    }).when('/catalogoAcreedores', {
        templateUrl: 'catalogos/catalogoAcreedores.html',
        controller: 'catalogoAcreedores'
    }).when('/catalogoClientes', {
        templateUrl: 'catalogos/catalogoClientes.html',
        controller: 'catalogoClientes'
    }).when('/catalogoClientes/cliente', {
        templateUrl: 'catalogos/registroCliente.html',
        controller: 'catalogoClientes'
    }).when('/catalogoClientes/cliente/:idCliente', {
        templateUrl: 'catalogos/registroCliente.html',
        controller: 'catalogoClientes'
    }).when('/catalogoTiposMovimiento', {
        templateUrl: 'catalogos/tiposMovimientos.html',
        controller: 'catalogoTiposDeMovimiento'
    }).when('/otrasSalidas', {
        templateUrl: 'almacen/otrasSalidas.html',
        controller: 'otrasSalidasCtrl'
    }).when('/nvaSalida/:idSalida?', {
        templateUrl: 'almacen/nvaSalida.html',
        controller: 'otrasSalidasCtrl'

        /////// TEMPORAL /////////
    }).when('/temporal', {
        templateUrl: 'controlAdministrativo/temporal/listaMovimientosAUP.html',
        controller: 'temporalCtrl'

        // M E N Ú  I N F OR M E S  E J E C U T I V O S
    }).when('/acumuladoGastos', {
        templateUrl: 'informesFinancieros/acumuladoDeGastos.html',
        controller: 'acumuladoGastosCtrl'
    }).when('/flujoEfectivo', {
        templateUrl: 'informesFinancieros/flujoEfectivo.html',
        controller: 'flujoEfectivoCtrl'
    }).when('/estadoResultados', {
        templateUrl: 'informesFinancieros/estadoDeResultados.html',
        controller: 'estadoResultadosCtrl'
    }).when('/balanceGeneral', {
        templateUrl: 'informesFinancieros/balanceGeneral.html',
        controller: 'balanceGeneralCtrl'

        // MENÚ CATÁLOGOS
    }).when('/cuentasSubcuentas', {
        templateUrl: 'catalogos/catalogoCuentasSubcuentas.html',
        controller: 'catalogoCuentasSubcuentasCtrl'
    }).when('/catalogoSalidas', {
        templateUrl: 'catalogos/catalogoOtrasSalidas.html',
        controller: 'catalogoOtrasSalidasCtrl'

        // INFORMES FINANCIEROS
    }).when('/informesFinancieros', {
        templateUrl: 'informesFinancieros/informesFinancieros.html',
        controller: 'informesFinancierosCtrl'
    }).when('/saldosiniciales', {
        templateUrl: 'saldosiniciales/saldosiniciales.html',
        controller: 'saldosinicialesCtrl'

        //EMPRESAS
    }).when('/empresas', {
        templateUrl: 'empresas/verEmpresas.html',
        controller: 'empresasCtrl'

    }).otherwise({
        redirectTo: '/home'
    });


}).factory("listaDeProveedores", function ($http) {
    var datos = new Array();
    $http.post("proveedores/php/dameProveedores.php").success(function (info) {

        datos = info;
    });
    return datos;
});
form.config(['growlProvider', function (growlProvider) {
    growlProvider.globalTimeToLive(3000);
    growlProvider.globalPosition('bottom-right');
    growlProvider.globalDisableCountDown(true);
}]);
form.factory("auth", ["$cookies", "$location", "$http", "$rootScope", function ($cookies, $location, $http, $rootScope) {
    return {
        login: function (usuario, password, idPerfil, selectedYear, idUsuario, database, idEmpresa) {
            var credentials = {
                usuario: usuario,
                password: password,
                idPerfil: idPerfil,
                selectedYear: selectedYear,
                idUsuario: idUsuario,
                database: database,
                idEmpresa: idEmpresa
            };
            $http.post('./usuarios.php?opcion=crearSesion', credentials).success(function (data) {
                $location.path("/home");
            });
        },
        logout: function () {
            $http.get('./usuarios.php?opcion=removeSession').success(function () {
                $location.path("/login");
            });
            $cookies.remove('datosRespuesta');
        },
        checkStatus: function (event, nuevaRuta) {
            if ($rootScope.lockTemplate) {
                event.preventDefault();
                swal({
                    title: '',
                    text: '¿Salir sin guardar los cambios?',
                    showCancelButton: true,
                    confirmButtonText: 'Permanecer en esta página',
                    cancelButtonText: 'Salir',
                    closeOnConfirm: true
                }, function (confirm) {
                    if (confirm) {
                        $rootScope.lockTemplate = true;
                    } else {
                        $rootScope.lockTemplate = false;
                        window.location.href = '#' + nuevaRuta;
                    }
                });

            } else {
                this.checkStatus2(event, nuevaRuta);
            }
        },
        checkStatus2: function (event, nuevaRuta) {
            var rutasPrivadas;
            $http.post("./seguridad/dameRutas.php").success(function (respuesta) {
                rutasPrivadas = respuesta;
            });
            $http.get('./usuarios.php?opcion=get').success(function (data) {
                $rootScope.session_de_usuario = data;
                if (data == 0) {
                    $location.path("/login");
                }
            });

            if (this.in_array("/login", rutasPrivadas) && $rootScope.session_de_usuario == 1) {
                $location.path("/home");
            }

            if (nuevaRuta == '/login' && $rootScope.session_de_usuario == 1) {
                $location.path("/home");
            }

            $http.get('./usuarios.php?opcion=dameIdPerfil').success(function (data) {
                if (!data.err) {
                    $rootScope.datosRespuesta = $cookies.getObject('datosRespuesta');
                    $http.post("./seguridad/dameMenu.php", { idPerfil: data.id, selectYear: data.showYear }).success(function (respuesta) {
                        $rootScope.lstMenu = respuesta;
                    });

                    $rootScope.dBSistema = data.showYear;

                }
            });
        },
        in_array: function (needle, rutas) {
            var ok = false;
            var key = '';
            for (key in rutas) {
                if (rutas[key] == needle) {
                    ok = true;
                }
            }
            return ok;
        }
    };
}]);

form.run(function ($rootScope, auth) {
    $rootScope.$on('$routeChangeStart', function (event, next, current) {
        auth.checkStatus(event, next.$$route.originalPath);
    });
});
form.service('busqueda', function ($http, $q) {
    this.dameProveedores = function () {
        var defered = $q.defer();
        var promise = defered.promise;
        $http.post("calidad/php/obtenerProveedoresDisponiblesCalidad.php")
            .success(function (valor) {
                defered.resolve(valor);
            });
        return promise;
    };
    this.dameResultadosFinales = function () {
        var defered = $q.defer();
        var promise = defered.promise;
        $http.post("calidad/php/obtenerResultadosFinales.php")
            .success(function (valor) {
                defered.resolve(valor);
            });
        return promise;
    };
    this.damePorcentaje = function () {
        var defered = $q.defer();
        var promise = defered.promise;
        $http.post("calidad/php/obtenerPorcentajeDisponible.php").success(function (valor) {
            defered.resolve(valor);
        });
        return promise;
    };
    this.dameSt = function () {
        var defered = $q.defer();
        var promise = defered.promise;
        $http.post("calidad/php/obtenerStDisponibles.php").success(function (valor) {
            defered.resolve(valor);
        });
        return promise;
    };
    this.dameSf = function () {
        var defered = $q.defer();
        var promise = defered.promise;
        $http.post("calidad/php/obtenerSfDisponible.php").success(function (valor) {
            defered.resolve(valor);
        });
        return promise;
    };
    this.dameC13 = function () {
        var defered = $q.defer();
        var promise = defered.promise;
        $http.post("calidad/php/obtenerC13Disponibles.php").success(function (valor) {
            defered.resolve(valor);
        });
        return promise;
    };
    this.dameHmf = function () {
        var defered = $q.defer();
        var promise = defered.promise;
        $http.post("calidad/php/obtenerHmfDisponible.php").success(function (valor) {
            defered.resolve(valor);
        });
        return promise;
    };
    this.dameFloracion = function () {
        var defered = $q.defer();
        var promise = defered.promise;
        // $http.post("json/laboratorio/floraciones.json").success(function (valor) {
        //     defered.resolve(valor.floraciones);
        // });
        $http.post("laboratorio/php/listaFloraciones.json").success(function (valor) {
            defered.resolve(valor);
        });
        return promise;
    };

    this.dameLocalidad = function () {
        var defered = $q.defer();
        var promise = defered.promise;
        $http.post("calidad/php/obtenerLocalidad.php").success(function (valor) {
            defered.resolve(valor);
        });
        return promise;
    };

    this.dameProveedoresO = function () {
        var defered = $q.defer();
        var promise = defered.promise;
        $http.post("calidad/php/obtenerProveedoresDisponiblesCalidad.php?organica=0")
            .success(function (valor) {
                defered.resolve(valor);
            });
        return promise;
    };
    this.dameResultadosFinalesO = function () {
        var defered = $q.defer();
        var promise = defered.promise;
        $http.post("calidad/php/obtenerResultadosFinales.php?organica=0")
            .success(function (valor) {
                defered.resolve(valor);
            });
        return promise;
    };
    this.damePorcentajeO = function () {
        var defered = $q.defer();
        var promise = defered.promise;
        $http.post("calidad/php/obtenerPorcentajeDisponible.php?organica=0").success(function (valor) {
            defered.resolve(valor);
        });
        return promise;
    };
    this.dameStO = function () {
        var defered = $q.defer();
        var promise = defered.promise;
        $http.post("calidad/php/obtenerStDisponibles.php?organica=0").success(function (valor) {
            defered.resolve(valor);
        });
        return promise;
    };
    this.dameSfO = function () {
        var defered = $q.defer();
        var promise = defered.promise;
        $http.post("calidad/php/obtenerSfDisponible.php?organica=0").success(function (valor) {
            defered.resolve(valor);
        });
        return promise;
    };
    this.dameC13O = function () {
        var defered = $q.defer();
        var promise = defered.promise;
        $http.post("calidad/php/obtenerC13Disponibles.php?organica=0").success(function (valor) {
            defered.resolve(valor);
        });
        return promise;
    };
    this.dameHmfO = function () {
        var defered = $q.defer();
        var promise = defered.promise;
        $http.post("calidad/php/obtenerHmfDisponible.php?organica=0").success(function (valor) {
            defered.resolve(valor);
        });
        return promise;
    };

    this.dameLocalidadO = function () {
        var defered = $q.defer();
        var promise = defered.promise;
        $http.post("calidad/php/obtenerLocalidad.php?organica=0").success(function (valor) {
            defered.resolve(valor);
        });
        return promise;
    };

    this.buscarInformacionParametros = function (listaParametros) {
        var defered = $q.defer();
        var promise = defered.promise;
        $http.post("calidad/php/buscarCalidadPorParametros.php", { valor: listaParametros }).success(function (info) {
            defered.resolve(info);
            // console.log(info);
        });
        return promise;
    };
    this.buscarInformacionParametrosOrganica = function (listaParametros) {
        var defered = $q.defer();
        var promise = defered.promise;
        $http.post("calidad/php/buscarCalidadPorParametrosOrganicos.php", { valor: listaParametros }).success(function (info) {
            defered.resolve(info);
        });
        return promise;
    };
});
