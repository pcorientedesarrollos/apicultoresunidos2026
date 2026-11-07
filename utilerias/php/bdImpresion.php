<?php

header('Content-Type: application/json');
include_once __DIR__ . '/../../WebServices/bdImpresion.php';

const PERFILES_CAMBIO_BD_IMPRESION = [7, 8];

session_start();
$usuario = $_SESSION['usuario'] ?? null;
$password = $_SESSION['password'] ?? null;
session_write_close();

try {
    $pdo = new conePDO();
    $control = $pdo->conectar('apicultorescontrol');

    $query = $control->prepare("SELECT idUsuario, idPerfil FROM usuarios WHERE usuario = :usuario AND password = :password");
    $query->execute([':usuario' => (string) $usuario, ':password' => md5((string) $password)]);
    $sesion = $query->fetch(PDO::FETCH_ASSOC);

    if (!$usuario || !$sesion) {
        http_response_code(401);
        throw new Exception('Sesión no válida');
    }

    $idUsuario = (int) $sesion['idUsuario'];
    $puedeCambiar = in_array((int) $sesion['idPerfil'], PERFILES_CAMBIO_BD_IMPRESION, true);

    $post = json_decode(file_get_contents('php://input'));
    if ($post && isset($post->bd)) {
        if (!$puedeCambiar) {
            http_response_code(403);
            throw new Exception('Tu perfil no tiene permiso para cambiar la BD de impresión');
        }
        if (!array_key_exists($post->bd, BD_IMPRESION_PERMITIDAS)) {
            throw new Exception('BD no permitida: ' . $post->bd);
        }
        $query = $control->prepare("UPDATE configuracion SET valor = :valor, idUsuario = :idUsuario WHERE clave = 'bd_impresion'");
        $query->execute([':valor' => $post->bd, ':idUsuario' => $idUsuario]);
    }

    $query = $control->query("SELECT c.valor, c.actualizado, u.usuario
        FROM configuracion c
        LEFT JOIN usuarios u ON u.idUsuario = c.idUsuario
        WHERE c.clave = 'bd_impresion'");
    $actual = $query->fetch(PDO::FETCH_ASSOC);

    $opciones = [];
    foreach (BD_IMPRESION_PERMITIDAS as $bd => $nombre) {
        $opciones[] = ['bd' => $bd, 'nombre' => $nombre];
    }

    echo json_encode([
        'error' => false,
        'actual' => $actual,
        'opciones' => $opciones,
        'puedeCambiar' => $puedeCambiar,
    ]);
} catch (Exception $e) {
    echo json_encode(['error' => true, 'message' => $e->getMessage()]);
}
