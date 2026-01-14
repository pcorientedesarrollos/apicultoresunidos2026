<?php

#Manejar las sessiones de los usiarios
session_start();
if (isset($_GET['opcion'])) {
    $opcion = $_GET['opcion'];
    switch ($opcion) {
        case 'crearSesion':
            $post = file_get_contents('php://input');
            if ($post) {
                $postdata = json_decode($post);
                $user = $postdata->usuario;
                $contra = $postdata->password;
                $idPerfil = $postdata->idPerfil;
                $selecttedYear = $postdata->selectedYear;
                $idUsuario = $postdata->idUsuario;
                $database = $postdata->database;
                $idEmpresa = $postdata->idEmpresa;
                $_SESSION['usuario'] = $user;
                $_SESSION['password'] = $contra;
                $_SESSION['idPerfil'] = $idPerfil;
                $_SESSION['showYear'] = $selecttedYear;
                $_SESSION['idUsuario'] = $idUsuario;
                $_SESSION['database'] = $database;
                $_SESSION['idEmpresa'] = $idEmpresa;
                echo json_encode($_SESSION);
            } else {
                die;
            }
            break;
        case 'get':
            if (isset($_SESSION['usuario'])) {
                echo 1;
            } else {
                echo 0;
            }
            break;
        case 'removeSession':
            unset($_SESSION['usuario']);
            unset($_SESSION['password']);
            unset($_SESSION['idPerfil']);
            unset($_SESSION['showYear']);
            unset($_SESSION['idUsuario']);
            unset($_SESSION['database']);
            unset($_SESSION['idEmpresa']);
            break;
        case 'dameIdPerfil':
            // Edición para que regrese el año también
            if (isset($_SESSION['idPerfil'])) {
                echo json_encode(['err' => false, 'id' => $_SESSION['idPerfil'], 'showYear'=>$_SESSION['showYear'], 'idUsuario'=>$_SESSION['idUsuario']]);
            } else {
                echo json_encode(['err' => true]);
            }
            break;
    }
} else {
    die;
}
