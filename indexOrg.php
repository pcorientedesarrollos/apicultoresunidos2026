<?php
//    session_start();
//    if(!isset($_SESSION['user'])){
//        echo "Acceso Degado.";
//        die;
//    }
?>
<!DOCTYPE html>
<!--[if IE 9 ]><html class="ie9"><![endif]-->
<!-- Mirrored from byrushan.com/projects/ma/1-5-2/jquery/ by HTTrack Website Copier/3.x [XR&CO'2014], Sun, 20 Mar 2016 04:22:53 GMT -->

<head>
    <!--<meta charset="utf-8">-->
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">

    <title>Oaxaca Miel</title>
    <link href="vendors/bower_components/material-design-iconic-font/dist/css/material-design-iconic-font.min.css" rel="stylesheet">

    <link rel="stylesheet" href="bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="bootstrap/bootstrap.css" />
    <link rel="stylesheet" href="bootstrap/bootstrap-theme.css" />

    <link href="datepicker/bootstrap-datepicker3.css" rel="stylesheet" type="text/css" />

    <link href="images/favicon.png" rel="icon" />

    <link rel="stylesheet" href="css/pco/clases.css" />

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.4.0/css/font-awesome.min.css">
    <!-- Ionicons -->
    <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">

    <link rel="stylesheet" href="dist/css/AdminLTE.min.css">
    <link rel="stylesheet" href="dist/css/skins/skin-purple-light.min.css">

    <!-- Estilos Personalizados -->
    <link rel="stylesheet" href="dist/css/animate.css">
    <link rel="stylesheet" href="vendors/sweetalert/sweetalert.css">

    <link rel="stylesheet" href="css/angular-growl.css" />
    <link rel="stylesheet" href="vendors/bower_components/chosen/chosen.min.css" />

</head>

<body ng-app="ctr" class="hold-transition skin-purple-light sidebar-mini fixed" ng-controller="mainCtrl">
    <!-- Main Header -->
    <header class="main-header">

        <!-- Logo -->
        <a href="#/home" class="logo">
            <!-- mini logo for sidebar mini 50x50 pixels -->
            <span class="logo-mini">
                <img src="images/favicon.png" alt="Logo" />
            </span>
            <!-- logo for regular state and mobile devices -->
            <!--<span class="logo-lg"><b> {{ config.aplicativo }} </b>{{ config.iniciales }}</span>-->
            <span class="logo-lg">
                <img src="images/logoLg.png" alt="Logo" />
            </span>

        </a>

        <!-- Header Navbar -->
        <nav class="navbar navbar-static-top" role="navigation">
            <!-- Sidebar toggle button-->
            <a href="" class="sidebar-toggle" data-toggle="offcanvas" role="button">
                <span class="sr-only">Toggle navigation</span>
            </a>
            <div class="pull-right anioDeBD">
                <p class="lead" ng-bind="dBSistema"></p>
            </div>
        </nav>
    </header>
    <aside class="main-sidebar fixed" ng-controller="main">
        <section class="sidebar">
            <ul class="sidebar-menu">
                <li>
                    <div class="input-group">
                        <input type="text" class="form-control" placeholder="Buscar" ng-model="searchInput" ng-keyup="lookForResults(searchInput)">

                        <span class="input-group-btn">
                            <button class="btn btn-default" type="button" ng-click="limpiarBusqueda()">&times;</button>
                        </span>
                    </div>
                </li>
                <li>
                    <a href="#home">
                        <i class="zmdi zmdi-home"></i>
                        <span> Home</span>
                    </a>
                </li>

                <li ng-repeat="menu in lstMenu | filter:searchSubmenu" class="treeview" ng-class="{'active': menu.searchResultsInside}">
                    <a href="">
                        <i class="zmdi zmdi-caret-right"></i>
                        <span> {{menu.seccion}}</span>
                        <i class="fa fa-angle-left pull-right"></i>
                    </a>
                    <ul class="treeview-menu">
                        <li ng-repeat="subMenu in menu.lstSubMenus | filter:searchSubmenu" ng-class="{'display-block': menu.searchResultsInside}">
                            <a ng-if="subMenu.newSistema != 1" href="#/{{subMenu.ruta}}"> {{subMenu.modulo}}</a>
                            <a ng-if="subMenu.newSistema == '1'" href="{{subMenu.ruta}}" target="_blank"> {{subMenu.modulo}} </a>
                        </li>
                    </ul>
                </li>
                <li class="">
                    <a href="" ng-click="cerrarSesion()">
                        <i class="zmdi zmdi-power"></i>
                        <span> Cerrar sesión </span>
                    </a>
                </li>
            </ul>
        </section>
    </aside>
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">


        </section>

        <!-- Main content -->
        <section class="content" ng-view>


            <!-- Your Page Content Here -->

        </section><!-- /.content -->
    </div><!-- /.content-wrapper -->

    <footer id="footer">
        Copyright &copy; 2016 Oaxaca Miel
    </footer>

    <!-- Page Loader -->
    <!--    <div class="page-loader">
            <div class="preloader pls-blue">
                <svg class="pl-circular" viewBox="25 25 50 50">
                <circle class="plc-path" cx="50" cy="50" r="20" />
                </svg>
                <p>Cargando</p>
            </div>
        </div>-->

    <!-- Javascript Libraries -->
    <script src="vendors/bower_components/jquery/dist/jquery.min.js"></script>
    <!--<script src="vendors/bower_components/bootstrap/dist/js/bootstrap.min.js"></script>-->

    <!--  GRAFICAS  
        <script src="vendors/sparklines/jquery.sparkline.min.js"></script>
        <script src="vendors/bower_components/jquery.easy-pie-chart/dist/jquery.easypiechart.min.js"></script>-->

    <!--<script src="vendors/bower_components/Waves/dist/waves.min.js"></script>-->
    <!--<script src="vendors/bower_components/bootstrap-sweetalert/lib/sweet-alert.min.js"></script>-->
    <!--<script src="vendors/bower_components/malihu-custom-scrollbar-plugin/jquery.mCustomScrollbar.concat.min.js"></script>-->

    <script src="vendors/bootstrap-growl/bootstrap-growl.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/chosen/1.4.2/chosen.jquery.min.js"></script>

    <!--<script src="librerias/jquery.elevateZoom-3.0.8.min.js"></script>-->

    <!--<script src="js/functions.js"></script>-->
    <script src="js/angular.min.js"></script>
    <script src="js/angular-route.min.js"></script>
    <script src="js/angular-cookies.js"></script>
    <script src="js/jcs-auto-validate.js"></script>
    <script src="js/angular-sanitize.min.js"></script>
    <script src="js/ng-csv.min.js"></script>
    <script src="js/angular-chosen.min.js"></script>
    <script src="js/ngMask.min.js"></script>
    <script src="jsConfig/config.js"></script>
    <script src="jsConfig/main.js"></script>
    <script src="proveedores/controladores/proveedores.js"></script>
    <script src="jsConfig/almacen.js"></script>
    <script src="jsConfig/configLaboratorio.js"></script>
    <!-- <script src="jsConfig/calidad.js"></script> -->
    <script src="js/angular_resource.js"></script>
    <script src="dominios/Almacenista.js"></script>
    <script src="dominios/Proveedor.js"></script>
    <script src="dominios/Laboratorio.js"></script>
    <script src="js/angular-growl.js"></script>
    <script src="jsConfig/login.js"></script>
    <!--<script src="jsConfig/cerrarSesion.js"></script>-->
    <script src="jsConfig/accesos.js"></script>
    <script src="jsConfig/estadisticaCtrl.js"></script>
    <script src="jsConfig/reportesDescargaCtrl.js"></script>
    <script src="jsConfig/reportesCargasCtrl.js"></script>
    <script src="jsConfig/condicionesCtrl.js"></script>
    <script src="jsConfig/almacenCubetas.js"></script>
    <script src="jsConfig/choferesCtrl.js"></script>
    <script src="jsConfig/controlCambiosCtrl.js"></script>
    <script src="jsConfig/produccionCtrl.js"></script>
    <script src="jsConfig/disposicionesCtrl.js"></script>
    <script src="jsConfig/manttoCtrl.js"></script>
    <script src="jsConfig/equiposCtrl.js"></script>
    <script src="jsConfig/pagoManttoCtrl.js"></script>
    <script src="jsConfig/deepClone.js"></script>

    <!--Import jQuery before materialize.js-->

    <script src="compras/controladores/compradorCtrl.js"></script>
    <script src="compras/controladores/formLocalidad.js"></script>
    <script src="compras/controladores/formZona.js"></script>
    <script src="compras/controladores/zonaAsigCtrl.js"></script>
    <script src="jsConfig/entradaMateriaPrimaCtrl.js"></script>
    <script src="jsConfig/salidaMateriaPrimaCtrl.js"></script>
    <script src="jsConfig/conciliacionCtrl.js"></script>
    <script src="jsConfig/precioCubetas.js"></script>
    <script src="jsConfig/personalOMCtrl.js"></script>
    <script src="jsConfig/seccionesCtrl.js"></script>
    <script src="jsConfig/usuariosSistemaCtrl.js"></script>
    <script src="jsConfig/perfilesSistemaCtrl.js"></script>
    <script src="jsConfig/envasadoCtrl.js"></script>
    <script src="jsConfig/especificacionCtrl.js"></script>
    <script src="jsConfig/foliosCtrl.js"></script>
    <script src="jsConfig/trazabilidadEntradaCtrl.js"></script>
    <script src="jsConfig/trazabilidadAnalisisCtrl.js"></script>
    <script src="jsConfig/trazabilidadSalidaCtrl.js"></script>
    <script src="jsConfig/listaPesosCtrl.js"></script>
    <script src="jsConfig/auxBancosCtrl.js"></script>
    <script src="jsConfig/tecnicosCtrl.js"></script>
    <script src="jsConfig/programacionCtrl.js"></script>
    <script src="jsConfig/inventarioTamboresCtrl.js"></script>
    <script src="jsConfig/mainCtrl.js"></script>
    <script src="jsConfig/subirArchivosCtrl.js"></script>
    <script src="jsConfig/evaluacionCtrl.js"></script>
    <script src="jsConfig/buscaEquipoCtrl.js"></script>
    <script src="jsConfig/clasificacionesCtrl.js"></script>
    <script src="jsConfig/cajaChicaCtrl.js"></script>
    <script src="reqDepositoCompra/controladores/requerimiento.js"></script>
    <script src="jsConfig/inventarioMielCtrl.js"></script>
    <script src="jsConfig/conciliacionMonetariaCtrl.js"></script>
    <script src="jsConfig/entradaCeraCtrl.js"></script>
    <script src="jsConfig/entradaApicolaCtrl.js"></script>
    <script src="deudoresProveedores/controladores/deudoresProveedoresCtrl.js"></script>
    <script src="dominios/requerimientos.js"></script>
    <script src="dominios/BancosAuxiliar.js"></script>
    <script src="dominios/controlCajaChica.js"></script>
    <script src="jsConfig/salidaCeraCtrl.js"></script>
    <script src="jsConfig/inventarioMPCtrl.js"></script>
    <script src="jsConfig/exportadoresCtrl.js"></script>
    <script src="jsConfig/salidaApicolaCtrl.js"></script>
    <script src="jsConfig/reportesEjecutivosCtrl.js"></script>
    <script src="jsConfig/mielDisponibleCtrl.js"></script>
    <script src="jsConfig/catalogoProductosCtrl.js"></script>
    <script src="jsConfig/catalogoUnidadesCtrl.js"></script>
    <script src="jsConfig/catalogoMielSobrante.js"></script>
    <script src="jsConfig/catalogoAcreedoresCtrl.js"></script>
    <script src="jsConfig/catalogoClientes.js"></script>
    <script src="jsConfig/catalogoTiposDeMovimiento.js"></script>
    <script src="jsConfig/otrasSalidasCtrl.js"></script>
    <script src="jsConfig/almacenSobranteCtrl.js"></script>
    <script src="jsConfig/temporalCtrl.js"></script>
    <script src="jsConfig/mielDisponibleSobranteCtrl.js"></script>

    <!-- <script src="jsConfig/deudoresYProveedoresCtrl.js"></script> -->
    <script src="jsConfig/prestamosCtrl.js"></script>
    <script src="jsConfig/auditoriaCtrl.js"></script>

    <!-- Catálogos -->
    <script src="jsConfig/catalogoCuentasSubcuentasCtrl.js"></script>
    <script src="jsConfig/catalogoOtrasSalidasCtrl.js"></script>

    <!-- Saldos iniciales -->
    <script src="jsConfig/saldosinicialesCtrl.js"></script>
    <script src="saldosiniciales/controladores/saldosInicialesDerivadosCtrl.js"></script>
    <script src="saldosiniciales/controladores/saldosInicialesMielCtrl.js"></script>

    <!-- Inventarios -->
    <script src="jsConfig/inventarioCeraCtrl.js"></script>
    <script src="jsConfig/inventarioProdApicolasCtrl.js"></script>
    <script src="inventarios/controladores/inventarioEnvasesCtrl.js"></script>
    <script src="inventarios/productosDerivados/controladores/inventarioProductosDerivadosCtrl.js"></script>

    <!-- Reportes financieros -->
    <script src="jsConfig/acumuladoGastosCtrl.js"></script>
    <script src="jsConfig/flujoEfectivoCtrl.js"></script>
    <script src="jsConfig/estadoDeResultadosCtrl.js"></script>
    <script src="jsConfig/balanceGeneralCtrl.js"></script>
    <script src="jsConfig/informesFinancierosCtrl.js"></script>

    <script src="dist/js/app.min.js"></script>
    <script src="vendors/sweetalert/sweetalert.min.js"></script>

    <script src="jsConfig/empresasCtrl.js"></script>

    <script src="tesoreria/controladores/tesoreriaCtrl.js"></script>
    <script src="tesoreria/controladores/listaChequesCtrl.js"></script>
    <!-- COMPRAS -->
    <script src="compras/controladores/distribucionZonasCtrl.js"></script>
    <script src="compras/controladores/catalogoComprasCtrl.js"></script>
    <script src="chequesEntregados/controladores/chequesEntregadosCtrl.js"></script>

    <!-- RECOLECCIÓN -->
    <script src="recoleccion/controladores/listaLocalidades.js"></script>
    <script src="recoleccion/controladores/proyeccion-real.js"></script>
    <script src="recoleccion/controladores/metas-compra.js"></script>
    <script src="recoleccion/controladores/jefesDeCompras.js"></script>

    <!-- LABORATORIO (ACT. 2020) -->
    <script src="laboratorio/controladores/laboratorio.js"></script>
    <script src="laboratorio/controladores/certificadoLoteCtrl.js"></script>
    <script src="laboratorio/controladores/humedadesTamboCtrl.js"></script>
    <script src="laboratorio/controladores/empresasExternasCtrl.js"></script>
    <script src="laboratorio/controladores/reactivosEntradaCtrl.js"></script>
    <script src="laboratorio/controladores/reactivosSalidaCtrl.js"></script>
    <script src="laboratorio/controladores/analisisExternosCtrl.js"></script>
    <script src="laboratorio/controladores/configuracionRangos.js"></script>
    <script src="laboratorio/controladores/inventarioReactivosCtrl.js"></script>
    <script src="laboratorio/controladores/catalogoFloracionesCtrl.js"></script>
    <script src="catalogos/js/catalogoReactivosCtrl.js"></script>
    <script src="laboratorio/controladores/analisisMielSobranteCtrl.js"></script>
    <script src="laboratorio/controladores/envioMuestrasCtrl.js"></script>
    <script src="laboratorio/controladores/catalogoLaboratoriosCtrl.js"></script>
    <script src="laboratorio/controladores/analisisTraspasoCtrl.js"></script>

    <!-- CALIDAD -->
    <script src="calidad/controladores/foliosMielSobranteCtrl.js"></script>
    <script src="calidad/controladores/calidad.js"></script>
    <script src="calidad/controladores/certificadoCalidad.js"></script>
    <script src="calidad/controladores/contratosLotesCtrl.js"></script>

    <!-- FACTURACION -->
    <script src="facturacion/controladores/facturacionCtrl.js"></script>

    <!-- INFORMES FINANCIEROS -->
    <script src="informesFinancieros/controladores/configInformesCtrl.js"></script>
    <script src="informesFinancieros/controladores/comparativasEEFFCtrl.js"></script>

    <!-- ADMINISTRADOR -->
    <script src="administrador/controladores/presupuestoCuentasCtrl.js"></script>
    <script src="administrador/controladores/lotesContratadosCtrl.js"></script>
    <script src="administrador/controladores/aprobarPreciosCtrl.js"></script>
    <script src="administrador/controladores/correccionMarcacionesCtrl.js"></script>
    <script src="administrador/controladores/retencionISRCtrl.js"></script>

    <!-- TRASPASO -->
    <script src="almacenTraspaso/controladores/almacenTraspasoCtrl.js"></script>

    <!-- TRAZABILIDAD -->
    <script src="trazabilidadMiel/controladores/trazabilidadMielCtrl.js"></script>

    <!-- ARQUEO DE CAJA -->
    <script src="arqueoDeCaja/controladores/arqueoDeCajaCtrl.js"></script>

    <!-- ALMACÉN -->
    <script src="almacen/controladores/entradaEnvasesFrascosCtrl.js"></script>
    <script src="almacen/controladores/salidaEnvasesFrascosCtrl.js"></script>
    <script src="almacen/productosDerivados/controladores/entradaDerivadosCtrl.js"></script>
    <script src="almacen/productosDerivados/controladores/salidaDerivadosCtrl.js"></script>

    <!-- UTILERIAS -->
    <script src="utilerias/solicitudes/controladores/solicitudesCompraCtrl.js"></script>
    <script src="utilerias/solicitudes/controladores/solicitudesServicioCtrl.js"></script>

    <!-- CONTROL ADMINISTRATIVO -->
    <script src="controlAdministrativo/aprobarSolicitudes/controladores/aprobarSolicitudesCtrl.js"></script>
    <script src="controlAdministrativo/inventarioMiel/controladores/inventarioDeMielCtrl.js"></script>

    <!-- jQuery 2.1.4 -->
    <!--<script src="vendors/jQuery/jQuery-2.1.4.min.js"></script>-->
    <!-- Bootstrap 3.3.5 -->
    <script src="bootstrap/js/bootstrap.min.js"></script>

    <script src="datepicker/bootstrap-datepicker.js" type="text/javascript"></script>

    <script src="push.js/push.min.js"></script>
    <script src="push.js/serviceWorker.min.js"></script>
    <script src="bootstrap/ui-bootstrap.min.js"></script>

    <script type="text/javascript" src="librerias/jquery.slimscroll.min.js"></script>


</body>

<script type="text/javascript">
    //<![CDATA[
    var tlJsHost = ((window.location.protocol == "https:") ? "https://secure.trust-provider.com/" : "http://www.trustlogo.com/");
    document.write(unescape("%3Cscript src='" + tlJsHost + "trustlogo/javascript/trustlogo.js' type='text/javascript'%3E%3C/script%3E"));
    //]]>
</script>
<script language="JavaScript" type="text/javascript">
    TrustLogo("https://www.positivessl.com/images/seals/positivessl_trust_seal_lg_222x54.png", "POSDV", "none");
</script>

</html>