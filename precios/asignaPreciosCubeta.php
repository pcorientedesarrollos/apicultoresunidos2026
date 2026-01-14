<section class="content">
    <div class="row">
        <div class="col-lg-12">
            <div class="box box-solid box-warning">
                <div class="box-header" style="height: 50px;">
                    <h5 class="encabezadosTabla">
                        ASIGNACION DE PRECIOS
                        <b ng-show="parametroCosecha == 1">100% PURA DE ABEJA</b>
                        <b ng-show="parametroCosecha == 2">100% ORGÁNICA</b>
                        <b ng-show="parametroCosecha == 5">100% MANTEQUILLA</b>
                        <b ng-show="parametroCosecha == 6">100% ALTIPLANO</b>
                        <b ng-show="parametroCosecha == 7">100% NARANJO</b>
                        <b ng-show="parametroCosecha == 8">100% AGUACATE</b>
                        <b ng-show="parametroCosecha == 9">100% MEZQUITE</b>
                        <a title="Regresar" href="#/pagoCub" class="btn btn-success btn-xs" style="float: right">
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
                                <input class="form-control" type="text" style="margin-top: 8px" disabled="true" ng-model="prove2.proveedor" />
                            </div>
                        </div>
                        <div class="col-lg-3">
                            <div class="form-group">
                                <label style="font-weight: bold;">Localidad:</label>
                                <input class="form-control" type="text" style="margin-top: 8px" disabled="true" ng-model="prove2.localidad" />
                            </div>
                        </div>
                        <div class="col-lg-3">
                            <div class="form-group">
                                <label style="font-weight: bold;">Trazabilidad:</label>
                                <input class="form-control" type="text" disabled="true" style="margin-top: 8px" ng-model="prove2.sagarpa" />
                            </div>
                        </div>
                        <div class="col-lg-3">
                            <div class="form-group">
                                <label style="font-weight: bold; color:red">folio:</label>
                                {{prove2.idAlmacen}}
                            </div>
                        </div>
                    </div>
                    <br>
                    <div class="row">
                        <div class="col-sm-2 col-sm-offset-9">
                            <a style="margin-right: 10px" title="Imprimir PDF" class="pull-right btn btn-sm btn-danger" ng-click="pdfPreciosCub()">
                                <i class="zmdi zmdi-font zmdi-hc-lg"></i>
                            </a>
                            <a style="margin-right: 10px" title="Imprimir XLS" class="pull-right btn btn-sm btn-success" ng-click="xlsPreciosCub()">

                                <i class="zmdi zmdi-storage zmdi-hc-lg"></i>
                            </a>
                        </div>
                    </div>
                    <br>

                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <th style="text-transform: none; font-weight: 600">Folio Tambor</th>
                                <th style="text-transform: none; font-weight: 600">Zona</th>
                                <th style="text-transform: none; font-weight: 600">Peso Lista</th>
                                <th style="text-transform: none; font-weight: 600">Bruto</th>
                                <th style="text-transform: none; font-weight: 600">Tara</th>
                                <th style="text-transform: none; font-weight: 600">Neto</th>
                                <th style="text-transform: none; font-weight: 600">Diferencia</th>
                                <th>Humedad</th>
                                <th style="text-transform: none; font-weight: 600">
                                    <center>Precio</center>
                                </th>
                                <th style="text-align: right; text-transform: none; font-weight: 600">Costo Total</th>
                                <th ng-if="parametro > 0">Aprobado</th>
                            </thead>
                            <tr ng-repeat="cb in cubetaMiel">
                                <td>
                                    {{cb.idAlmacen}}
                                </td>
                                <td>
                                    {{cb.zona}}
                                </td>
                                <td>
                                    {{cb.pesoLista|number}}
                                </td>
                                <td>
                                    {{cb.bruto|number}}</td>
                                <td>
                                    {{cb.tara|number}}
                                </td>
                                <td>{{cb.neto|number}}</td>
                                <td>{{cb.diferencia|number}}</td>
                                <td>{{cb.humedad}}</td>
                                <td>
                                    <label>
                                        <input type="text" class="text-right small" required="required" ng-keyup="dameTotalCub();" ng-model="cb.precio" ng-disabled="cb.aprobado == '1'">
                                        <i class="input-helper"></i>
                                    </label>
                                </td>
                                <td style="text-align: right">
                                    <input ng-model="cb.costoTotal" class="text-right small" type="text" ng-value="cb.costoTotal = cb.neto*cb.precio" ng-disabled="cb.aprobado == '1'">
                                    </label>
                                </td>
                                <td class="text-center">
                                    <input ng-if="cb.idAlmacen > 0" type="checkbox" ng-true-value="'1'" ng-false-value="'0'" ng-model="cb.aprobado" ng-disabled="cb.idAlmacen > 0">
                                </td>
                            </tr>
                        </table>
                    </div>
                    <br>
                    <div class="row">
                        <div class="col-sm-5 col-sm-offset-7">
                            <p class="text-right" style="margin-right: 8%">
                                <b> <strong>Total de la Compra</strong> = <span style="color:red;">{{prove2.totalCompra|currency}}</span> </b>
                            </p>
                            <p class="text-right" style="margin-right: 8%">
                                <b> <strong>Total diferencia</strong> = <span style="color:red;">{{ totalKgDiferencia }} Kg.</span> </b>
                            </p>
                        </div>
                    </div>
                </div>
                <center>
                    <br>
                    <button class="btn btn-primary" ng-click="guardarPrecioCub()">Guardar</button>
                </center>
                <br>
            </div>
        </div>
    </div>
</section>
<div growl>
</div>