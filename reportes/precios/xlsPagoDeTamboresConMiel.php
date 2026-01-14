<?php
include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$conexion = $pdo->conectar();
$fecha = date("d-m-y");
$tmp = $_GET["tmp"];

//Inicio de la instacia de la exportaion
header('Content-type: application/vnd.ms-excel');
header("Content-Disposition: attachmen; filename = Pago de tambores con miel_$fecha.xls");
header("Prafma: no-cache");
header("Expires:0");
if ($tmp == 1) {
	$titulo = "Pago de Tambores con miel 100% pura de abeja";
	$sql = "SELECT *,  count(alm.idAlmacen)registros, sum(alm.neto) kgs
	FROM almacenencabezado al 
	LEFT JOIN proveedor pr 
	ON pr.idProveedor = al.idProveedor
	LEFT JOIN almacen alm
	on al.idAlmacen = alm.idAlmacenEncabezado
	LEFT JOIN direccion dr 
	on pr.idDireccion = dr.idDireccion
	LEFT JOIN localidades lo ON lo.idlocalidad = dr.idlocalidad
	LEFT JOIN archivospdf pdf ON pdf.idAlmacen = alm.idAlmacenEncabezado
	LEFT JOIN archivospdfpagos pago ON pago.idAlmacen = alm.idAlmacenEncabezado
	group by al.idAlmacen ORDER BY al.idAlmacen DESC";
	$datos = $conexion->prepare($sql);
	$datos->execute();

	if ($datos == false) {
		throw new Exception($con->errorInfo());
	} else {
		$array = array();
		$cont = 0;
		while ($rs = $datos->fetch()) {
			$almacen = new stdClass();
			$almacen->fecha = $rs["fecha"];
			$almacen->id = $rs[0];
			$almacen->proveedor = $rs["nombre"];
			$almacen->registros = $rs["registros"];
			$almacen->folio = $rs["folio"];
			$almacen->totalCompra = $rs["totalCompra"];

			$slq = "SELECT count(autorizado) FROM almacen 
			WHERE autorizado = 1 and idAlmacenEncabezado ='" . $rs[0] . "' ";
			$datosAutorizados = $conexion->prepare($slq);
			$datosAutorizados->execute();
			while ($rsAutorizado = $datosAutorizados->fetch()) {
				$almacen->datosAutorizados = $rsAutorizado[0];
			}
			$almacen->localidad = $rs["localidad"];
			$almacen->kgs = $rs['kgs'];
			$array[$cont] = $almacen;
			$cont++;
		}
	}

	$registros = $array;
} else if ($tmp == 5) {
	$titulo = "Pago de Tambores con miel 100% mantequilla";
	$sql = "SELECT *,  count(alm.idAlmacen)registros, sum(alm.neto) kgs
	FROM almacenencabezado_mantequilla al 
	LEFT JOIN proveedor pr 
	ON pr.idProveedor = al.idProveedor
	LEFT JOIN almacen_mantequilla alm
	on al.idAlmacen = alm.idAlmacenEncabezado
	LEFT JOIN direccion dr 
	on pr.idDireccion = dr.idDireccion
	LEFT JOIN localidades lo ON lo.idlocalidad = dr.idlocalidad
	LEFT JOIN archivospdf pdf ON pdf.idAlmacen = alm.idAlmacenEncabezado
	LEFT JOIN archivospdfpagos pago ON pago.idAlmacen = alm.idAlmacenEncabezado
	group by al.idAlmacen ORDER BY al.idAlmacen DESC";
	$datos = $conexion->prepare($sql);
	$datos->execute();

	if ($datos == false) {
	    throw new Exception($con->errorInfo());
	} else {
		$array = array();
		$cont = 0;
		while ($rs = $datos->fetch()) {
			$almacen = new stdClass();
			$almacen->fecha = $rs["fecha"];
			$almacen->id = $rs[0];
			$almacen->proveedor = $rs["nombre"];
			$almacen->registros = $rs["registros"];
			$almacen->folio = $rs["folio"];
			$almacen->totalCompra = $rs["totalCompra"];

			$slq = "SELECT count(autorizado) FROM almacen 
			WHERE autorizado = 1 and idAlmacenEncabezado ='" . $rs[0] . "' ";
			$datosAutorizados = $conexion->prepare($slq);
			$datosAutorizados->execute();
			while ($rsAutorizado = $datosAutorizados->fetch()) {
				$almacen->datosAutorizados = $rsAutorizado[0];
			}
			$almacen->localidad = $rs["localidad"];
			$almacen->kgs = $rs['kgs'];
			$array[$cont] = $almacen;
			$cont++;
		}
	}

	$registros = $array;
}  else if ($tmp == 6) {
	$titulo = "Pago de Tambores con miel 100% altiplano";
	$sql = "SELECT *,  count(alm.idAlmacen)registros, sum(alm.neto) kgs
	FROM almacenencabezado_altiplano al 
	LEFT JOIN proveedor pr 
	ON pr.idProveedor = al.idProveedor
	LEFT JOIN almacen_altiplano alm
	on al.idAlmacen = alm.idAlmacenEncabezado
	LEFT JOIN direccion dr 
	on pr.idDireccion = dr.idDireccion
	LEFT JOIN localidades lo ON lo.idlocalidad = dr.idlocalidad
	LEFT JOIN archivospdf pdf ON pdf.idAlmacen = alm.idAlmacenEncabezado
	LEFT JOIN archivospdfpagos pago ON pago.idAlmacen = alm.idAlmacenEncabezado
	group by al.idAlmacen ORDER BY al.idAlmacen DESC";
	$datos = $conexion->prepare($sql);
	$datos->execute();

	if ($datos == false) {
	    throw new Exception($con->errorInfo());
	} else {
		$array = array();
		$cont = 0;
		while ($rs = $datos->fetch()) {
			$almacen = new stdClass();
			$almacen->fecha = $rs["fecha"];
			$almacen->id = $rs[0];
			$almacen->proveedor = $rs["nombre"];
			$almacen->registros = $rs["registros"];
			$almacen->folio = $rs["folio"];
			$almacen->totalCompra = $rs["totalCompra"];

			$slq = "SELECT count(autorizado) FROM almacen 
			WHERE autorizado = 1 and idAlmacenEncabezado ='" . $rs[0] . "' ";
			$datosAutorizados = $conexion->prepare($slq);
			$datosAutorizados->execute();
			while ($rsAutorizado = $datosAutorizados->fetch()) {
				$almacen->datosAutorizados = $rsAutorizado[0];
			}
			$almacen->localidad = $rs["localidad"];
			$almacen->kgs = $rs['kgs'];
			$array[$cont] = $almacen;
			$cont++;
		}
	}

	$registros = $array;
} else if ($tmp == 7) {
	$titulo = "Pago de Tambores con miel 100% naranjo";
	$sql = "SELECT *,  count(alm.idAlmacen)registros, sum(alm.neto) kgs
	FROM almacenencabezado_naranjo al 
	LEFT JOIN proveedor pr 
	ON pr.idProveedor = al.idProveedor
	LEFT JOIN almacen_naranjo alm
	on al.idAlmacen = alm.idAlmacenEncabezado
	LEFT JOIN direccion dr 
	on pr.idDireccion = dr.idDireccion
	LEFT JOIN localidades lo ON lo.idlocalidad = dr.idlocalidad
	LEFT JOIN archivospdf pdf ON pdf.idAlmacen = alm.idAlmacenEncabezado
	LEFT JOIN archivospdfpagos pago ON pago.idAlmacen = alm.idAlmacenEncabezado
	group by al.idAlmacen ORDER BY al.idAlmacen DESC";
	$datos = $conexion->prepare($sql);
	$datos->execute();

	if ($datos == false) {
	    throw new Exception($con->errorInfo());
	} else {
		$array = array();
		$cont = 0;
		while ($rs = $datos->fetch()) {
			$almacen = new stdClass();
			$almacen->fecha = $rs["fecha"];
			$almacen->id = $rs[0];
			$almacen->proveedor = $rs["nombre"];
			$almacen->registros = $rs["registros"];
			$almacen->folio = $rs["folio"];
			$almacen->totalCompra = $rs["totalCompra"];

			$slq = "SELECT count(autorizado) FROM almacen 
			WHERE autorizado = 1 and idAlmacenEncabezado ='" . $rs[0] . "' ";
			$datosAutorizados = $conexion->prepare($slq);
			$datosAutorizados->execute();
			while ($rsAutorizado = $datosAutorizados->fetch()) {
				$almacen->datosAutorizados = $rsAutorizado[0];
			}
			$almacen->localidad = $rs["localidad"];
			$almacen->kgs = $rs['kgs'];
			$array[$cont] = $almacen;
			$cont++;
		}
	}

	$registros = $array;
}

else if ($tmp == 8) {
	$titulo = "Pago de Tambores con miel 100% aguacate";
	$sql = "SELECT *,  count(alm.idAlmacen)registros, sum(alm.neto) kgs
	FROM almacenencabezado_aguacate al 
	LEFT JOIN proveedor pr 
	ON pr.idProveedor = al.idProveedor
	LEFT JOIN almacen_aguacate alm
	on al.idAlmacen = alm.idAlmacenEncabezado
	LEFT JOIN direccion dr 
	on pr.idDireccion = dr.idDireccion
	LEFT JOIN localidades lo ON lo.idlocalidad = dr.idlocalidad
	LEFT JOIN archivospdf pdf ON pdf.idAlmacen = alm.idAlmacenEncabezado
	LEFT JOIN archivospdfpagos pago ON pago.idAlmacen = alm.idAlmacenEncabezado
	group by al.idAlmacen ORDER BY al.idAlmacen DESC";
	$datos = $conexion->prepare($sql);
	$datos->execute();

	if ($datos == false) {
	    throw new Exception($con->errorInfo());
	} else {
		$array = array();
		$cont = 0;
		while ($rs = $datos->fetch()) {
			$almacen = new stdClass();
			$almacen->fecha = $rs["fecha"];
			$almacen->id = $rs[0];
			$almacen->proveedor = $rs["nombre"];
			$almacen->registros = $rs["registros"];
			$almacen->folio = $rs["folio"];
			$almacen->totalCompra = $rs["totalCompra"];

			$slq = "SELECT count(autorizado) FROM almacen 
			WHERE autorizado = 1 and idAlmacenEncabezado ='" . $rs[0] . "' ";
			$datosAutorizados = $conexion->prepare($slq);
			$datosAutorizados->execute();
			while ($rsAutorizado = $datosAutorizados->fetch()) {
				$almacen->datosAutorizados = $rsAutorizado[0];
			}
			$almacen->localidad = $rs["localidad"];
			$almacen->kgs = $rs['kgs'];
			$array[$cont] = $almacen;
			$cont++;
		}
	}

	$registros = $array;
}  else if ($tmp == 9) {
	$titulo = "Pago de Tambores con miel 100% mezquite";
	$sql = "SELECT *,  count(alm.idAlmacen)registros, sum(alm.neto) kgs
	FROM almacenencabezado_mezquite al 
	LEFT JOIN proveedor pr 
	ON pr.idProveedor = al.idProveedor
	LEFT JOIN almacen_mezquite alm
	on al.idAlmacen = alm.idAlmacenEncabezado
	LEFT JOIN direccion dr 
	on pr.idDireccion = dr.idDireccion
	LEFT JOIN localidades lo ON lo.idlocalidad = dr.idlocalidad
	LEFT JOIN archivospdf pdf ON pdf.idAlmacen = alm.idAlmacenEncabezado
	LEFT JOIN archivospdfpagos pago ON pago.idAlmacen = alm.idAlmacenEncabezado
	group by al.idAlmacen ORDER BY al.idAlmacen DESC";
	$datos = $conexion->prepare($sql);
	$datos->execute();

	if ($datos == false) {
	    throw new Exception($con->errorInfo());
	} else {
		$array = array();
		$cont = 0;
		while ($rs = $datos->fetch()) {
			$almacen = new stdClass();
			$almacen->fecha = $rs["fecha"];
			$almacen->id = $rs[0];
			$almacen->proveedor = $rs["nombre"];
			$almacen->registros = $rs["registros"];
			$almacen->folio = $rs["folio"];
			$almacen->totalCompra = $rs["totalCompra"];

			$slq = "SELECT count(autorizado) FROM almacen 
			WHERE autorizado = 1 and idAlmacenEncabezado ='" . $rs[0] . "' ";
			$datosAutorizados = $conexion->prepare($slq);
			$datosAutorizados->execute();
			while ($rsAutorizado = $datosAutorizados->fetch()) {
				$almacen->datosAutorizados = $rsAutorizado[0];
			}
			$almacen->localidad = $rs["localidad"];
			$almacen->kgs = $rs['kgs'];
			$array[$cont] = $almacen;
			$cont++;
		}
	}

	$registros = $array;
}
else {
	$titulo = "Pago de Tambores con miel 100% orgánica";
	$sql = "SELECT *,  count(alm.idAlmacen)registros, sum(alm.neto) kgs
	FROM almacenencabezado_organico al 
	LEFT JOIN proveedor pr 
	ON pr.idProveedor = al.idProveedor
	LEFT JOIN almacen_organico alm
	on al.idAlmacen = alm.idAlmacenEncabezado
	LEFT JOIN direccion dr 
	on pr.idDireccion = dr.idDireccion
	LEFT JOIN localidades lo ON lo.idlocalidad = dr.idlocalidad
	LEFT JOIN archivospdf pdf ON pdf.idAlmacen = alm.idAlmacenEncabezado
	LEFT JOIN archivospdfpagos pago ON pago.idAlmacen = alm.idAlmacenEncabezado
	group by al.idAlmacen ORDER BY al.idAlmacen DESC";
	$datos = $conexion->prepare($sql);
	$datos->execute();

	if ($datos == false) {
	    throw new Exception($con->errorInfo());
	} else {
		$array = array();
		$cont = 0;
		while ($rs = $datos->fetch()) {
			$almacen = new stdClass();
			$almacen->fecha = $rs["fecha"];
			$almacen->id = $rs[0];
			$almacen->proveedor = $rs["nombre"];
			$almacen->registros = $rs["registros"];
			$almacen->folio = $rs["folio"];
			$almacen->totalCompra = $rs["totalCompra"];

			$slq = "SELECT count(autorizado) FROM almacen 
			WHERE autorizado = 1 and idAlmacenEncabezado ='" . $rs[0] . "' ";
			$datosAutorizados = $conexion->prepare($slq);
			$datosAutorizados->execute();
			while ($rsAutorizado = $datosAutorizados->fetch()) {
				$almacen->datosAutorizados = $rsAutorizado[0];
			}
			$almacen->localidad = $rs["localidad"];
			$almacen->kgs = $rs['kgs'];
			$array[$cont] = $almacen;
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

echo '<table>
	<tr>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td>
                <p style="font-size:22px; font-weight:bold"> <b>' . utf8_decode('Código:') . 'RCO-PTM-02</b> </p>
                </td>
	</tr>
</table>';

echo '<table width="100%" border="1">
	<tr align="center">
		<td style="background:#bdc3c7; font-weight:bold">Comprobante</td>
		<td style="background:#bdc3c7; font-weight:bold">Fecha</td>
		<td style="background:#bdc3c7; font-weight:bold">Proveedor</td>
		<td style="background:#bdc3c7; font-weight:bold">Localidad</td>
		<td style="background:#bdc3c7; font-weight:bold">Tambores</td>
		<td style="background:#bdc3c7; font-weight:bold">Total Compra</td>
                <td style="background:#bdc3c7; font-weight:bold">Kilos</td>
	</tr>';



foreach ($registros as $reg) {
	echo '<tr>
	<td>
		' . $reg->folio . '
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
		' . $reg->registros . '
	</td>
	<td>
		' . number_format($reg->totalCompra, 2, '.', ',') . '
	</td>
        <td>
		' . number_format($reg->kgs, 2, '.', ',') . '
	</td>
</tr>';
}
echo '</table>';
