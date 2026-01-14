<section class="content">
    <div class="row">
        <div class="col-lg-12">
            <div class="box-tools col s4 visible-lg visible-md hidden-sm hidden-xs" style="float: right">
                <div class="input-group" style="width: 300px;">
                    <input type="text" ng-model="busqueda2" class="form-control input-sm pull-right" placeholder="Ingrese lo que desea buscar">
                    <div class="input-group-btn">
                        <button class="btn btn-sm btn-warning"><i class="zmdi zmdi-search"></i></button>
                    </div>
                </div>
            </div>
            <div class="box-tools col-sm-4 col-xs-4 visible-xs visible-sm hidden-md hidden-lg">
                <div class="input-group" style="width: 150px;">
                    <input type="text" ng-model="busqueda2" name="table_search" class="form-control input-sm pull-right" placeholder="Buscar">
                    <div class="input-group-btn">
                        <button class="btn btn-sm btn-default"><i class="zmdi zmdi-search"></i></button>
                    </div>
                </div>
            </div>
            <br><br><br>
            <a href="#/pagoCub">
                <div class="btn btn-sm btn-success">
                    Pago a Cubetas
                </div>
            </a>
            <br><br>
            <div class="box box-solid box-warning">
                <div class="box-header" style="height: 50px;">
                    <h5 class="encabezadosTabla">
                        PAGO DE TAMBORES CON MIEL
                        <b ng-show="opcionCosecha == 1">100% PURA DE ABEJA</b>
                        <b ng-show="opcionCosecha == 2">100% ORGÁNICA</b></h5>
                        <b ng-show="opcionCosecha == 5">100% MANTEQUILLA</b></h5>
                        <b ng-show="opcionCosecha == 6">100% ALTIPLANO</b></h5>
                        <b ng-show="opcionCosecha == 7">100% NARANJO</b></h5>
                        <b ng-show="opcionCosecha == 8">100% AGUACATE</b></h5>
                        <b ng-show="opcionCosecha == 9">100% MEZQUITE</b></h5>
                </div>

                <div class="box-body">
                    <div class="row">
                        <div class="col-sm-5 pull-right">
                            <a title="Totales" ng-click="modalTotales(opcionCosecha)" class="btn btn-warning btn-sm">
                                <i class="zmdi zmdi-assignment zmdi-hc-lg"></i> Informe general
                            </a>
                            <a title="Reporte General por Proveedor" ng-click="modalProveedor(opcionCosecha)" class="btn btn-success btn-sm">
                                <i class="fa fa-file-excel-o"></i> Reporte por proveedor
                            </a>
                            <a title="Pago a Proveedores" ng-click="pagoProveedores(opcionCosecha)" class="btn btn-success btn-sm">
                                <i class="fa fa-file-excel-o"></i> Pago a proveedores
                            </a>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xs-12 col-md-2">
                            <div class="form-group">
                                <select chosen class="form-control" ng-model="mostrarMes" ng-options="mes.idMes as mes.mes for mes in listaDeMeses" ng-disabled="cargandoDatos">
                                    <option value="">Todo</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-xs-12 col-md-3">
                            <select chosen class="form-control" ng-model="opcionCosecha">
                                <option selected value="">Elija una opción</option>
                                <option value="1">Convencional</option>
                                <option value="2">Orgánica</option>
                                <option value="5">Mantequilla</option>
                                <option value="6">Altiplano</option>
                                <option value="7">Naranjo</option>
                                <option value="8">Aguacate</option>
                                <option value="9">Mezquite</option>
                            </select>
                        </div>
                        <div class="col-xs-12 col-md-2">
                            <select chosen class="form-control" ng-model="filtroPagado" ng-change="filtrarPagosTambores(filtroPagado)">
                                <option selected value="">Elija una opción</option>
                                <option value="1">Pagados</option>
                                <option value="2">No pagados</option>
                                <option value="3">Todos</option>
                            </select>
                        </div>
                    </div>
                    <!-- <br>
                    <div class="row" ng-show="lista.length > 0">
                        <div class="col-lg-12">
                            <div class="form-group">
                                <div class="col-md-1">
                                    <label>Filtrar: </label>
                                </div>
                                <div class="col-md-3">
                                    <select class="form-control" ng-model="filtroPagado" ng-change="filtrarPagos(filtroPagado)">
                                        <option value="1">Pagados</option>
                                        <option value="2">No pagados</option>
                                        <option value="3" selected>Todos</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div> -->
                    <br>
                    <div ng-if="lista.length > 0" class="table-responsive">
                        <table class="table table-striped table-condensed" style="align-content: center">
                            <thead>
                                <tr>
                                    <th style="text-transform: none; font-weight: 600">Comprobante</th>
                                    <!-- <th ng-show="opcionCosecha == 1" style="text-transform: none; font-weight: 600">Lista</th>
                                    <th ng-show="opcionCosecha == 1" style="text-transform: none; font-weight: 600">Solicitud</th> -->
                                    <th style="text-transform: none; font-weight: 600">Fecha</th>
                                    <th style="text-transform: none; font-weight: 600">Proveedor</th>
                                    <th style="text-transform: none; font-weight: 600">Localidad</th>
                                    <th style="text-transform: none; font-weight: 600">Registros</th>
                                    <th style="text-transform: none; font-weight: 600; text-align: right">Total</th>
                                    <th style="text-transform: none; font-weight: 600; text-align: right">Kgs.</th>
                                    <th style="text-transform: none; font-weight: 600; text-align: right">Dif.</th>
                                    <th style="text-transform: none; font-weight: 600">Agregar</th>
                                    <!-- <th ng-show="opcionCosecha == 1" style="text-transform: none; font-weight: 600">Lista</th>
                                    <th ng-show="opcionCosecha == 1" style="text-transform: none; font-weight: 600">Solicitud</th>  -->
                                </tr>
                            </thead>
                            <tbody>
                                <tr style="font-size: 13px" ng-repeat="almacen in lista| filter:busqueda2 ">
                                    <td>{{almacen.folio}}</td>
                                    <!-- <td ng-show="opcionCosecha == 1" class="btnEditar">
                                        <a ng-if="almacen.archivoRelacion" ng-href="precios/{{almacen.archivoRelacion}}" title="Ver lista de pesos" target="_blank" class="btn btn-primary btn-xs">
                                            <i class="zmdi zmdi-file"></i>
                                        </a>
                                    </td>
                                    <td ng-show="opcionCosecha == 1" class="btnEditar">
                                        <a ng-if="almacen.archivoPago" ng-href="precios/{{almacen.archivoPago}}" title="Ver solicitud de compra" target="_blank" class="btn btn-primary btn-xs">
                                            <i class="zmdi zmdi-file"></i>
                                        </a>
                                    </td> -->
                                    <td>{{almacen.fecha}}</td>
                                    <td>{{almacen.proveedor|uppercase}}</td>
                                    <td>{{almacen.localidad|uppercase}}</td>
                                    <td>{{almacen.datosAutorizados}}/{{almacen.registros}}</td>
                                    <td style="text-align: right">{{almacen.totalCompra|currency}}</td>
                                    <td style="text-align: right">{{almacen.kgs}}</td>
                                    <td style="text-align: right">{{almacen.totalKgDiferencia}}</td>
                                    <td>
                                        <a class="btn btn-warning btn-xs" ng-href="#/configPrecios/{{almacen.id}}/{{opcionCosecha}}" title="Agregar precios">
                                            <i class="zmdi zmdi-money"></i>
                                        </a>
                                    </td>
                                    <!-- <td ng-show="opcionCosecha == 1">
                                        <a  href="#/subirPdf/{{almacen.id}}" class="btn btn-info btn-xs" title="Subir Lista de pesos">
                                            <i class="zmdi zmdi-upload"></i>
                                        </a>
                                    </td>
                                    <td ng-show="opcionCosecha == 1">
                                        <a href="#/subirPdfPago/{{almacen.id}}" class="btn btn-info btn-xs" title="Subir Solicitud de compra">                                           <i class="zmdi zmdi-upload"></i>
                                        </a>
                                    </td> -->
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>
</section>

<div class="modal  fade" id="modalTotales" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-body">
                <br><br>
                <div class="table table-responsive">
                    <table class="table table-condensed table-bordered">
                        <thead>
                            <tr>
                                <th ng-show="opcionCosecha == 1" colspan="7" style="text-align: center; font-size: 15px;" class="bgm-yellow">INFORME GENERAL DE INGRESO DE MIEL 100% PURA</th>
                                <th ng-show="opcionCosecha == 2" colspan="7" style="text-align: center; font-size: 15px;" class="bgm-yellow">INFORME GENERAL DE INGRESO DE MIEL 100% ORGÁNICA</th>
                            <tr>
                                <th style="text-align: right; text-transform: none; font-weight: 600">Tambores</th>
                                <th style="text-align: right; text-transform: none; font-weight: 600">Lista</th>
                                <th style="text-align: right; text-transform: none; font-weight: 600">Bruto</th>
                                <th style="text-align: right; text-transform: none; font-weight: 600">Tara</th>
                                <th style="text-align: right; text-transform: none; font-weight: 600">Neto</th>
                                <th style="text-align: right; text-transform: none; font-weight: 600">Diferencia</th>
                                <th style="text-align: right; text-transform: none; font-weight: 600">Compra</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td style="text-align: right">{{totales.tamboresTotal| number:0}}</td>
                                <td style="text-align: right">{{totales.pesoListaTotal| number:0}}</td>
                                <td style="text-align: right">{{totales.pesoBrutoTotal| number:0}}</td>
                                <td style="text-align: right">{{totales.pesoTaraTotal| number:0}}</td>
                                <td style="text-align: right">{{totales.pesoNetoTotal| number:0}}</td>
                                <td style="text-align: right">{{totales.diferenciaTotal| number:0}}</td>
                                <td style="text-align: right">{{totales.compraTotal|currency}}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <br>
                <button type="button" class="btn btn-danger" data-dismiss="modal">Salir</button>
            </div>
        </div>
    </div>
</div>

<div class="modal  fade" id="modalProveedores" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Reporte General de Precios por Proveedor</h4>
            </div>
            <div class="modal-body">
                <br>
                <div class="form-group">
                    <label style="font-weight: bold;" class="col-sm-2 col-sm-offset-1 control-label">Proveedor:</label>
                    <div class="col-sm-9">
                        <select width="'100%'" chosen ng-model="prove" class="form-control" ng-options="proveedor as proveedor.nombre for proveedor in listaNombreProveedores track by proveedor.id">
                            <option value="">Seleccione un Proveedor</option>
                        </select>
                    </div>
                </div>

            </div>
            <div class="modal-footer">
                <br>
                <button type="button" class="btn btn-success" data-dismiss="modal" ng-click="generarReportePrecios()">Generar</button>
            </div>
        </div>
    </div>
</div>
<div growl>

</div>