<?php

include_once '../../DAOConeccion/conePDO.php';
include_once '../../utilerias/php/dameNombrePersonal.php';
$pdo = new conePDO(); $con = $pdo->conectar();

try {
    if(!isset($_GET['opcion'])){ throw new Exception('No se ha recibido comando'); }
    $opcion = $_GET['opcion'];

    switch ($opcion):
        case 'mantenimientosProgramados':
            $sql = "SELECT a.area, e.nombre, e.codigo, pf.fechaProgramada
                    FROM programaciondefechas pf
                    INNER JOIN areas a ON pf.idArea = a.idArea
                    INNER JOIN equipos e ON e.idEquipo = pf.idEquipo
                    WHERE fechaProgramada = DATE_ADD(CURRENT_DATE(),INTERVAL 1 DAY)";
            $query = $con->prepare($sql);
            $query->execute();
            if($query == FALSE) { throw new Exception($con->errorInfo()); }
            
            $resultado = $query->fetchAll(PDO::FETCH_ASSOC);
            $response = [];
            foreach ($resultado as $matto) {
                $matto['titulo'] = "Mantenimiento programado para el día ";
                array_push($response, $matto);
            }
            echo json_encode(['error'=>false, 'resultado'=>$response]);
            break;

        case 'mantenimientosPasados':
            $sql = "SELECT a.area, e.nombre, e.codigo, pf.fechaProgramada
                    FROM programaciondefechas pf
                    INNER JOIN areas a ON pf.idArea = a.idArea
                    INNER JOIN equipos e ON e.idEquipo = pf.idEquipo
                    WHERE pf.fechaProgramada = DATE_SUB(CURRENT_DATE(),INTERVAL 1 DAY) 
                    AND CONCAT(pf.idArea,pf.idEquipo,pf.idMes) NOT IN (SELECT CONCAT(idArea,idEquipo,idMes) FROM controlmantenimiento)";
            $query = $con->prepare($sql);
            $query->execute();
            if($query == FALSE) { throw new Exception($con->errorInfo()); }

            $resultado = $query->fetchAll(PDO::FETCH_ASSOC);
            $response = [];
            foreach ($resultado as $matto) {
                $matto['titulo'] = "Mantenimiento atrasado del día ";
                array_push($response, $matto);
            }
            echo json_encode(['error'=>false, 'resultado'=>$response]);
            break;

        case 'mandarCorreo':
            $postdata = file_get_contents("php://input");
            if(!$postdata) { throw new Exception('No se recibieron los datos para enviar'); }
            $mattos = json_decode($postdata);
            $datos_gerente = dameNombrePersonal(2, $con);
            // $to = 'rodrigo.medinacetz@gmail.com';
            $to = $datos_gerente['correo'];
            $asunto = count($mattos) . ' ' . $mattos[0]->titulo . $mattos[0]->fechaProgramada;
            $mensaje = "
                    <h4>" . $asunto . "</h4>
                    <table width='50%' style='border: 1px solid black; border-collapse: collapse;'>  
                    <thead  style='background-color: #385428'>
                    <tr>
                    <th style='text-align:center; color:#fff; border: 1px solid black; border-collapse: collapse;'>Equipo</th>
                    <th style='text-align:center; color:#fff; border: 1px solid black; border-collapse: collapse;'> Área</th>
                    </tr>
                    </thead>
                    <tbody>";
            foreach ($mattos as $matto) {
                $mensaje .= "
                        <tr align='center'>
                            <td style='border: 1px solid black; border-collapse: collapse;'>" . $matto->nombre . "</td>
                            <td style='border: 1px solid black; border-collapse: collapse;'>" . $matto->area . "</td>
                        </tr>";
            }
            $mensaje .= "
                    </tbody>
                    </table>";

            $cabeceras = 'MIME-Version: 1.0' . "\r\n";
            $cabeceras .= 'Content-Type: text/html; charset=UTF-8' . "\r\n";
            $cabeceras .= 'Reply-To: correos@pcoriente.com.mx' . "\r\n";
            $cabeceras .= 'From: correos@pcoriente.com.mx' . "\r\n";

            $enviarMail = mail($to, $asunto, $mensaje, $cabeceras);
            
            if ($enviarMail) {
                $insertar = $asunto;
                $query = $con->prepare("INSERT INTO notificacionesmantenimiento (codigoNotificacion, enviado) VALUES (:codigo, CURRENT_DATE())");
                $query->bindParam(':codigo', $insertar);
                $query->execute();
                if($query == FALSE) { throw new Exception($con->errorInfo()); }
            }

            echo json_encode(['error'=>false,'message'=>'Se ha enviado un correo electrónico con el encabezado: ' . $asunto]);

            break;

        case 'revisarSiMandarCorreo':
            $query = $con->prepare("SELECT * FROM notificacionesmantenimiento WHERE enviado = CURRENT_DATE()");
            $query->execute();
            if($query == FALSE){ throw new Exception($con->errorInfo()); } 
            if ($query->rowCount() >= 1) {
                $enviado = true;
            } else {
                $enviado = false;
            }
            echo json_encode(['error'=>false, 'enviado'=>$enviado]);
            break;
    endswitch;


} catch (Exception $e) {
    echo json_encode(['error'=>true, 'message'=>$e->getMessage()]);
}