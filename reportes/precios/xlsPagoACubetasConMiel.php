<?php

include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$conexion = $pdo->conectar();
$fecha = date("d-m-y");
$tmp = $_GET["tmp"];
//Inicio de la instacia de la exportaion
header('Content-type: application/vnd.ms-excel');
header("Content-Disposition: attachmen; filename = Reporte de Concentrado Pago por Cubetas_$fecha.xls");
header("Prafma: no-cache");
header("Expires:0");
if($tmp == 1){
	$titulo = "Pago por cubetas con miel 100% pura de abeja";	
	$sql = "SELECT ce.idAlmacen, ce.fecha, rp.nombre, ol.localidad, ce.totalCompra, cd.neto
	FROM cubetasencabezado ce 
	LEFT JOIN proveedor rp 
	ON rp.idProveedor = ce.idProveedor
	LEFT JOIN cubetasdetalle cd 
	ON ce.idAlmacen = cd.idAlmacenEncabezado
	LEFT JOIN direccion rd 
	ON rp.idDireccion = rd.idDireccion
	LEFT JOIN localidades ol ON ol.idlocalidad = rd.idlocalidad
	WHERE ce.folioEntradaTambor = 99999
	GROUP BY ce.idAlmacen
	ORDER BY ce.fecha DESC";
	$datos = $conexion->prepare($sql);
	$datos->execute();
	
	if ($datos == false) {
		    throw new Exception($con->errorInfo());
	} else {
		$array = array();
		$cont = 0;
		while ($rs = $datos->fetch()) {
			$cubeta = new stdClass();
			$cubeta->fecha = $rs["fecha"];
			$cubeta->id = $rs[0];
			$cubeta->proveedor = $rs["nombre"];
			$cubeta->totalCompra = $rs["totalCompra"];
			$cubeta->localidad = $rs["localidad"];
			$cubeta->neto = $rs["neto"];
			$array[$cont] = $cubeta;
			// $array = $cubetaTambor;
			$cont++;
		}
	}
	
	$registros = $array;
} else if ($tmp == 5){

	$titulo = "Pago por cubetas con miel 100% mantequilla";	
	$sql = "SELECT ce.idAlmacen, ce.fecha, rp.nombre, ol.localidad, ce.totalCompra, cd.neto
	FROM cubetasencabezado_mantequilla ce 
	LEFT JOIN proveedor rp 
	ON rp.idProveedor = ce.idProveedor
	LEFT JOIN cubetasdetalle_mantequilla cd 
	ON ce.idAlmacen = cd.idAlmacenEncabezado
	LEFT JOIN direccion rd 
	ON rp.idDireccion = rd.idDireccion
	LEFT JOIN localidades ol ON ol.idlocalidad = rd.idlocalidad
	WHERE ce.folioEntradaTambor = 99999
	GROUP BY ce.idAlmacen
	ORDER BY ce.fecha DESC";
	$datos = $conexion->prepare($sql);
	$datos->execute();
	
	if ($datos == false) {
		    throw new Exception($con->errorInfo());
	} else {
		$array = array();
		$cont = 0;
		while ($rs = $datos->fetch()) {
			$cubeta = new stdClass();
			$cubeta->fecha = $rs["fecha"];
			$cubeta->id = $rs[0];
			$cubeta->proveedor = $rs["nombre"];
			$cubeta->totalCompra = $rs["totalCompra"];
			$cubeta->localidad = $rs["localidad"];
			$cubeta->neto = $rs["neto"];
			$array[$cont] = $cubeta;
			// $array = $cubetaTambor;
			$cont++;
		}
	}
	
	$registros = $array;	

} else if ($tmp == 6){

	$titulo = "Pago por cubetas con miel 100% altiplano";	
	$sql = "SELECT ce.idAlmacen, ce.fecha, rp.nombre, ol.localidad, ce.totalCompra, cd.neto
	FROM cubetasencabezado_altiplano ce 
	LEFT JOIN proveedor rp 
	ON rp.idProveedor = ce.idProveedor
	LEFT JOIN cubetasdetalle_altiplano cd 
	ON ce.idAlmacen = cd.idAlmacenEncabezado
	LEFT JOIN direccion rd 
	ON rp.idDireccion = rd.idDireccion
	LEFT JOIN localidades ol ON ol.idlocalidad = rd.idlocalidad
	WHERE ce.folioEntradaTambor = 99999
	GROUP BY ce.idAlmacen
	ORDER BY ce.fecha DESC";
	$datos = $conexion->prepare($sql);
	$datos->execute();
	
	if ($datos == false) {
		    throw new Exception($con->errorInfo());
	} else {
		$array = array();
		$cont = 0;
		while ($rs = $datos->fetch()) {
			$cubeta = new stdClass();
			$cubeta->fecha = $rs["fecha"];
			$cubeta->id = $rs[0];
			$cubeta->proveedor = $rs["nombre"];
			$cubeta->totalCompra = $rs["totalCompra"];
			$cubeta->localidad = $rs["localidad"];
			$cubeta->neto = $rs["neto"];
			$array[$cont] = $cubeta;
			// $array = $cubetaTambor;
			$cont++;
		}
	}
	
	$registros = $array;	

} else if ($tmp == 7){

	$titulo = "Pago por cubetas con miel 100% naranjo";	
	$sql = "SELECT ce.idAlmacen, ce.fecha, rp.nombre, ol.localidad, ce.totalCompra, cd.neto
	FROM cubetasencabezado_naranjo ce 
	LEFT JOIN proveedor rp 
	ON rp.idProveedor = ce.idProveedor
	LEFT JOIN cubetasdetalle_naranjo cd 
	ON ce.idAlmacen = cd.idAlmacenEncabezado
	LEFT JOIN direccion rd 
	ON rp.idDireccion = rd.idDireccion
	LEFT JOIN localidades ol ON ol.idlocalidad = rd.idlocalidad
	WHERE ce.folioEntradaTambor = 99999
	GROUP BY ce.idAlmacen
	ORDER BY ce.fecha DESC";
	$datos = $conexion->prepare($sql);
	$datos->execute();
	
	if ($datos == false) {
		    throw new Exception($con->errorInfo());
	} else {
		$array = array();
		$cont = 0;
		while ($rs = $datos->fetch()) {
			$cubeta = new stdClass();
			$cubeta->fecha = $rs["fecha"];
			$cubeta->id = $rs[0];
			$cubeta->proveedor = $rs["nombre"];
			$cubeta->totalCompra = $rs["totalCompra"];
			$cubeta->localidad = $rs["localidad"];
			$cubeta->neto = $rs["neto"];
			$array[$cont] = $cubeta;
			// $array = $cubetaTambor;
			$cont++;
		}
	}
	
	$registros = $array;	

}  else if ($tmp == 8){

	$titulo = "Pago por cubetas con miel 100% aguacate";	
	$sql = "SELECT ce.idAlmacen, ce.fecha, rp.nombre, ol.localidad, ce.totalCompra, cd.neto
	FROM cubetasencabezado_aguacate ce 
	LEFT JOIN proveedor rp 
	ON rp.idProveedor = ce.idProveedor
	LEFT JOIN cubetasdetalle_aguacate cd 
	ON ce.idAlmacen = cd.idAlmacenEncabezado
	LEFT JOIN direccion rd 
	ON rp.idDireccion = rd.idDireccion
	LEFT JOIN localidades ol ON ol.idlocalidad = rd.idlocalidad
	WHERE ce.folioEntradaTambor = 99999
	GROUP BY ce.idAlmacen
	ORDER BY ce.fecha DESC";
	$datos = $conexion->prepare($sql);
	$datos->execute();
	
	if ($datos == false) {
		    throw new Exception($con->errorInfo());
	} else {
		$array = array();
		$cont = 0;
		while ($rs = $datos->fetch()) {
			$cubeta = new stdClass();
			$cubeta->fecha = $rs["fecha"];
			$cubeta->id = $rs[0];
			$cubeta->proveedor = $rs["nombre"];
			$cubeta->totalCompra = $rs["totalCompra"];
			$cubeta->localidad = $rs["localidad"];
			$cubeta->neto = $rs["neto"];
			$array[$cont] = $cubeta;
			// $array = $cubetaTambor;
			$cont++;
		}
	}
	
	$registros = $array;	

}  else if ($tmp == 9){

	$titulo = "Pago por cubetas con miel 100% mezquite";	
	$sql = "SELECT ce.idAlmacen, ce.fecha, rp.nombre, ol.localidad, ce.totalCompra, cd.neto
	FROM cubetasencabezado_mezquite ce 
	LEFT JOIN proveedor rp 
	ON rp.idProveedor = ce.idProveedor
	LEFT JOIN cubetasdetalle_mezquite cd 
	ON ce.idAlmacen = cd.idAlmacenEncabezado
	LEFT JOIN direccion rd 
	ON rp.idDireccion = rd.idDireccion
	LEFT JOIN localidades ol ON ol.idlocalidad = rd.idlocalidad
	WHERE ce.folioEntradaTambor = 99999
	GROUP BY ce.idAlmacen
	ORDER BY ce.fecha DESC";
	$datos = $conexion->prepare($sql);
	$datos->execute();
	
	if ($datos == false) {
		    throw new Exception($con->errorInfo());
	} else {
		$array = array();
		$cont = 0;
		while ($rs = $datos->fetch()) {
			$cubeta = new stdClass();
			$cubeta->fecha = $rs["fecha"];
			$cubeta->id = $rs[0];
			$cubeta->proveedor = $rs["nombre"];
			$cubeta->totalCompra = $rs["totalCompra"];
			$cubeta->localidad = $rs["localidad"];
			$cubeta->neto = $rs["neto"];
			$array[$cont] = $cubeta;
			// $array = $cubetaTambor;
			$cont++;
		}
	}
	
	$registros = $array;	

} else{
	$titulo = "Pago por cubetas con miel 100% orgánica";	
	$sql = "SELECT ce.idAlmacen, ce.fecha, rp.nombre, ol.localidad, ce.totalCompra, cd.neto
	FROM cubetasencabezado_organico ce 
	LEFT JOIN proveedor rp 
	ON rp.idProveedor = ce.idProveedor
	LEFT JOIN cubetasdetalle_organico cd 
	ON ce.idAlmacen = cd.idAlmacenEncabezado
	LEFT JOIN direccion rd 
	ON rp.idDireccion = rd.idDireccion
	LEFT JOIN localidades ol ON ol.idlocalidad = rd.idlocalidad
	WHERE ce.folioEntradaTambor = 99999
	GROUP BY ce.idAlmacen
	ORDER BY ce.fecha DESC";
	$datos = $conexion->prepare($sql);
	$datos->execute();
	
	if ($datos == false) {
		    throw new Exception($con->errorInfo());
	} else {
		$array = array();
		$cont = 0;
		while ($rs = $datos->fetch()) {
			$cubeta = new stdClass();
			$cubeta->fecha = $rs["fecha"];
			$cubeta->id = $rs[0];
			$cubeta->proveedor = $rs["nombre"];
			$cubeta->totalCompra = $rs["totalCompra"];
			$cubeta->localidad = $rs["localidad"];
			$cubeta->neto = $rs["neto"];
			$array[$cont] = $cubeta;
			// $array = $cubetaTambor;
			$cont++;
		}
	}
	
	$registros = $array;	
}


echo '<table>
	<tr>
		<td></td>
		<td></td>
		<td>
                <p style="font-size:22px; font-weight:bold">' . utf8_decode($titulo) . '</p>
                </td>
	</tr>
</table>';

echo '<table width="100%" border="1">
	<tr align="center">
		<td style="background:#bdc3c7; font-weight:bold">Folio Comprobante</td>
		<td style="background:#bdc3c7; font-weight:bold">Fecha</td>
		<td style="background:#bdc3c7; font-weight:bold">Proveedor</td>
		<td style="background:#bdc3c7; font-weight:bold">Localidad</td>
                <td style="background:#bdc3c7; font-weight:bold">Kgs</td>
		<td style="background:#bdc3c7; font-weight:bold">Total</td>
	</tr>';



foreach ($registros as $reg) {
    echo '<tr>
	<td>
		' . $reg->id . '
	</td>
	<td>
		' . $reg->fecha . '
	</td>
	<td>
		' . utf8_decode($reg->proveedor) . '
	</td>
	<td>
		' . utf8_decode($reg->localidad) . '
	</td>

	<td>
		' . utf8_decode($reg->neto) . '
	</td>

	<td>
		' . '$' . number_format($reg->totalCompra, 2, '.', ',') . '
	</td>
</tr>';
}
echo '</table>';
