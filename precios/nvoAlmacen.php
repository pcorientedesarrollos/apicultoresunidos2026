<section class="content">
    <div class="row">
        <div class="col-lg-12">
            <div class="box box-solid box-warning">
                <div class="box-header" style="height: 50px;">
                    <h5 class="encabezadosTabla">
                        ASIGNACION DE PRECIOS
                        <a title="Regresas" href="#/precios" class="btn btn-success btn-xs" style="float: right">
                            <i class="zmdi zmdi-arrow-left"></i>
                        </a>
                    </h5>
                </div>
                <div class="box-body">
                    <div class="row">
                        <div class="col-lg-3">
                            <div class="form-group">
                                <label style="font-weight: bold;">
                                    Proveedor:
                                </label>
                                <select disabled="true" style="margin-top: 8px" ng-model="prove" chosen ng-change="obeterInfoAlmacenProveedor();" class="form-control" ng-options="proveedores as proveedores.nombre for proveedores in listaProveedor track by proveedores.id">
                                    <option value="">Seleccione un Proveedor</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-lg-3">
                            <div class="form-group">
                                <label style="font-weight: bold;">Localidad:</label>
                                <input class="form-control" type="text" style="margin-top: 8px" disabled="true" ng-model="prove.localidad" />
                            </div>
                        </div>
                        <div class="col-lg-3">
                            <div class="form-group">
                                <label style="font-weight: bold;">Trazabilidad:</label>
                                <input class="form-control" type="text" disabled="true" style="margin-top: 8px" ng-model="prove.sagarpa" />
                            </div>
                        </div>
                        <div class="col-lg-2">
                            <div class="form-group">
                                <label style="font-weight: bold; color:red">folio:</label>
                                {{prove.folio}}
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-sm-2 col-sm-offset-9">
                            <a style="margin-right: 10px" title="Imprimir PDF" class="pull-right btn btn-danger btn-sm" ng-click="pdfPrecios()">
                                <i class="zmdi zmdi-font zmdi-hc-lg"></i>
                            </a>
                            <!--                            <a style="margin-right: 10px"
                               title="Imprimir PDF"
                               class="pull-right btn btn-danger btn-sm"
                               href="reportes/envoiceOM.php/{{idAlmacenista}}"
                               target="_blank">
                                <i class="zmdi zmdi-font zmdi-hc-lg"></i>
                            </a>-->
                            <a style="margin-right: 10px" title="Imprimir XLS" class="pull-right btn btn-success btn-sm" ng-click="xlsPrecios()">
                                <i class="zmdi zmdi-storage zmdi-hc-lg"></i>
                            </a>
                        </div>
                    </div>
                    <br>
                    <div class="row" ng-show="!ocultar">
                        <div class="col-lg-offset-6 col-lg-6">
                            <div class="form-group">
                                <label>Asignar mismo precio</label>
                                <input class="small" ng-change="obtenerValor(1);" ng-model="precio" ng-keyup="dameTotal();" type="text" class="form-control" />
                            </div>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-striped table-condensed">
                            <thead>
                                <th style="text-transform: none; font-weight: 600">Folio Tambor</th>
                                <th style="text-transform: none; font-weight: 600">Zona</th>
                                <th style="text-transform: none; font-weight: 600">Peso Lista</th>
                                <th style="text-transform: none; font-weight: 600">Bruto</th>
                                <th style="text-transform: none; font-weight: 600">Tara</th>
                                <th style="text-transform: none; font-weight: 600">Neto</th>
                                <th style="text-transform: none; font-weight: 600">Diferencia</th>
                                <th style="text-transform: none; font-weight: 600">Humedad almacén</th>
                                <th style="text-transform: none; font-weight: 600">Humedad laboratorio</th>
                                <th style="text-transform: none; font-weight: 600">
                                    <center>Precio</center>
                                </th>
                                <th style="text-align: right; text-transform: none; font-weight: 600">Costo Total</th>
                                <th ng-if="idAlmacenista > 0">Aprobado</th>
                            </thead>
                            <tr ng-repeat="al in almacenn">
                                <td>
                                    {{al.idAlmacen}}
                                </td>
                                <td>
                                    {{al.zona}}
                                </td>
                                <td>
                                    {{al.pesoLista|number}}
                                </td>
                                <td>
                                    {{al.bruto|number}}</td>
                                <td>
                                    {{al.tara|number}}
                                </td>
                                <td>{{al.neto|number}}</td>
                                <td>{{al.diferencia|number}}</td>
                                <td>{{al.humedad}}</td>
                                <td>{{al.porcentaje}}</td>
                                <td>
                                    <label>
                                        <!-- ng-disabled="al.copiaPrecio > 0" -->
                                        <input type="text" class="text-right small" required="required" ng-keyup="dameTotal();" ng-model="al.precio" ng-disabled="al.aprobado == '1'">
                                        <i class="input-helper"></i>
                                    </label>
                                </td>
                                <td style="text-align: right">
                                    <!-- ng-disabled="al.copiaPrecio > 0" -->
                                    <input ng-model="al.costoTotal" class="text-right small" type="text" ng-value="al.costoTotal = al.neto*al.precio" ng-disabled="al.aprobado == '1'"/>
                                    </label>
                                </td>
                                <td class="text-center">
                                    <input ng-if="al.idAlmacen > 0" type="checkbox" ng-true-value="1" ng-false-value="0" ng-model="al.aprobado" ng-disabled="al.idAlmacen > 0">
                                </td>
                            </tr>
                        </table>
                    </div>
                    <br>
                    <div class="row">
                        <div class="col-sm-5 col-sm-offset-7">
                            <p class="text-right" style="margin-right: 8%">
                                <b> <strong>Total Neto (Kgs)</strong> = <span style="color:red;">{{prove.totalNeto}}</span> </b>
                            </p>
                            <p class="text-right" style="margin-right: 8%">
                                <b> <strong>Total de la Compra</strong> = <span style="color:red;">{{prove.totalCompra|currency}}</span> </b>
                            </p>
                            <p class="text-right" style="margin-right: 8%">
                                <b> <strong>Total diferencia</strong> = <span style="color:red;">{{totalKgDiferencia}} Kg.</span> </b>
                            </p>
                        </div>
                    </div>
                </div>
                <center>
                    <br>
                    <button class="btn btn-warning btn-sm" ng-click="guardarPrecio()">Guardar</button>
                </center>
                <br>
            </div>
        </div>
    </div>
</section>
<div growl>
</div>