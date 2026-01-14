<?php

include_once '../../DAOConeccion/conePDO.php';
include_once './nombreDePersona.php';
include_once '../prestamos/php/traerTotalesPrestamos.php';

$pdo = new conePDO();
$con = $pdo->conectar();
if (!isset($_GET['idMes'])) {
        exit();
}

$idMes = $_GET['idMes'];
$datos = $con->prepare("SELECT cc.*, m.mes 
                        FROM cajachica cc
                        LEFT JOIN meses m ON m.idMes = cc.idMes
                        WHERE cc.idMes !=0 AND cc.idMes = :idMes
                        ORDER BY cc.idCajaChica NOT IN (SELECT idCajaChica FROM cajachica WHERE tipoDeCliente IS NOT NULL), cc.fecha DESC, cc.hora DESC
                        ");
$datos->bindParam(':idMes', $idMes);
$datos->execute();

$resultado = array(); // Datos
$encabezado = array(
    'totalCajaChica' => 0,
    'totalIngresos' => 0,
    'totalEgresos' => 0
); //Encabezado

$saldoAcumulado = 0;
#Hago un array reverse: 
#La consulta lo trae ordenado, pero al momento de calcular el saldo acumulado, necesitamos hacerlo desde el primero, no desde el último
foreach (array_reverse($datos->fetchAll(PDO::FETCH_ASSOC)) as $data) {
    $data['tipo'] = $data['tipo'] == 0 || $data['tipo'] == 'INGRESO' ? true : false;

    $data['saldo'] = $data['tipo'] ?
    $saldoAcumulado += $data['total']:
    $saldoAcumulado -= $data['total'];

    if ($data['tipo']) {
        $data['ingreso'] = $data['total'];
        $encabezado['totalCajaChica'] += $data['total'];
        $encabezado['totalIngresos'] += $data['total'];
    } else {
        $data['egreso'] = $data['total'];
        $encabezado['totalCajaChica'] -= $data['total'];
        $encabezado['totalEgresos'] += $data['total'];
    }

    $data['nombre'] = retornarNombre($con, $data['tipoDeCliente'], $data['nombre']);
        array_push($resultado, $data);
}

$prestamos = getData($con);
$encabezado['pendientes'] = $prestamos['pendientes'] - $prestamos['abonos'];
$encabezado['saldoActualCajaChica'] = $encabezado['totalCajaChica'] - $prestamos['pendientes'] + $prestamos['abonos'];

#Volvemos a ordenar reverso el arreglo del resultado
echo json_encode(['datos'=>array_reverse($resultado), 'encabezado'=>$encabezado]);
