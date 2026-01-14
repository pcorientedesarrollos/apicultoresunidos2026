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
                        PAGO DE TAMBORES CON MIEL</h5>
                </div>

                <div class="box-body">
                    <div class="row">
                        <div class="col-sm-3 col-sm-offset-9">
                            <a title="Totales" ng-click="modalTotales()" class="btn btn-warning btn-sm">
                                <i class="zmdi zmdi-assignment zmdi-hc-lg"></i> Informe general
                            </a>
                        </div>
                    </div>
                    <br>
                    <div class="table-responsive">
                        <table class="table table-striped table-condensed" style="align-content: center">
                            <thead>
                                <tr>
                                    <th style="text-transform: none; font-weight: 600">Comprobante</th>
                                    <th style="text-transform: none; font-weight: 600">Lista</th>
                                    <th style="text-transform: none; font-weight: 600">Solicitud</th>
                                    <th style="text-transform: none; font-weight: 600">Fecha</th>
                                    <th style="text-transform: none; font-weight: 600">Proveedor</th>
                                    <th style="text-transform: none; font-weight: 600">Localidad</th>
                                    <th style="text-transform: none; font-weight: 600">Registros</th>
                                    <th style="text-transform: none; font-weight: 600; text-align: right">Total</th> 
                                    <th style="text-transform: none; font-weight: 600">Agregar</th>
                                    <th style="text-transform: none; font-weight: 600">Lista</th>
                                    <th style="text-transform: none; font-weight: 600">Solicitud</th> 
                                </tr>
                            </thead>
                            <tbody>
                                <tr style="font-size: 13px" ng-repeat="almacen in listaAlmacenes| filter:busqueda2 ">
                                    <td>{{almacen.folio}}</td> 
                                    <td class="btnEditar">
                                        <a ng-if="almacen.archivoRelacion" ng-href="precios/{{almacen.archivoRelacion}}" title="Ver lista de pesos" target="_blank" class="btn btn-primary">
                                            <i class="zmdi zmdi-file"></i>
                                        </a>
                                    </td>
                                    <td class="btnEditar">
                                        <a ng-if="almacen.archivoPago" ng-href="precios/{{almacen.archivoPago}}" title="Ver solicitud de compra" target="_blank" class="btn btn-primary">
                                            <i class="zmdi zmdi-file"></i>
                                        </a>
                                    </td>
                                    <td>{{almacen.fecha}}</td>
                                    <td>{{almacen.proveedor|uppercase}}</td>
                                    <td>{{almacen.localidad|uppercase}}</td>
                                    <td>{{almacen.datosAutorizados}}/{{almacen.registros}}</td>
                                    <td style="text-align: right">{{almacen.totalCompra|currency}}</td>
                                    <td>
                                       <a class="btn btn-warning btn-xs" ng-href="#/configPrecios/{{almacen.id}}" title="Agregar precios">
                                            <i class="zmdi zmdi-money" ></i>
                                        </a>
                                    </td>
                                    <td>
                                        <a  href="#/subirPdf/{{almacen.id}}" class="btn btn-info btn-xs" title="Subir Lista de pesos">
                                            <i class="zmdi zmdi-upload"></i>
                                        </a>
                                    </td>
                                    <td>
                                        <a href="#/subirPdfPago/{{almacen.id}}" class="btn btn-info btn-xs" title="Subir Solicitud de compra">                                           <i class="zmdi zmdi-upload"></i>
                                        </a>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
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
                                <th colspan="7" style="text-align: center; font-size: 15px;" class="bgm-yellow">INFORME GENERAL DE INGRESO DE MIEL </th>
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
<div growl>

</div>