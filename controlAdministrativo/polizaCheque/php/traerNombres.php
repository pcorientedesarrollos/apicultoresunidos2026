<?php
$post = json_decode(file_get_contents('php://input'));

function returnApicultores($con)
{
    $apicultores = $con->prepare('SELECT idProveedor as id, nombre as nombre FROM proveedor WHERE activoInactivo = 0 ORDER BY nombre');
    $apicultores->execute();
    if ($apicultores->rowCount() >= 1) {
        echo  json_encode($apicultores->fetchAll(PDO::FETCH_ASSOC));
    } else {
        echo json_encode([]);
    }
};

function returnProveedores($con)
{
    $proveedores = $con->prepare('SELECT idProveedorMantto as id, nombreProveedor as nombre FROM proveedoresmantto ORDER BY nombreContacto');
    $proveedores->execute();
    if ($proveedores->rowCount() >= 1) {
        echo  json_encode($proveedores->fetchAll(PDO::FETCH_ASSOC));
    } else {
        echo json_encode([]);
    }
};


function returnPersonal($con)
{
    $personal = $con->prepare('SELECT idPersonalOM as id, nombre as nombre FROM personaloaxaca WHERE estado = 0 ORDER BY nombre');
    $personal->execute();
    if ($personal->rowCount() >= 1) {
        echo  json_encode($personal->fetchAll(PDO::FETCH_ASSOC));
    } else {
        echo json_encode([]);
    }
};

function returnClientes($con)
{
    $personal = $con->prepare('SELECT idCliente as id, nombre as nombre FROM clientes ORDER BY nombre');
    $personal->execute();
    if ($personal->rowCount() >= 1) {
        echo  json_encode($personal->fetchAll(PDO::FETCH_ASSOC));
    } else {
        echo json_encode([]);
    }
};

function nombresPrestamos($con)
{
    $personal = $con->prepare("SELECT idPersonalOM as id, nombre as nombre, '4' as tipoDePersona FROM personaloaxaca
    UNION
    SELECT idProveedorMantto as id, nombreContacto as nombre, '3' as tipoDePersona FROM proveedoresmantto ORDER BY nombre");
    $personal->execute();
    if ($personal->rowCount() >= 1) {
        echo  json_encode($personal->fetchAll(PDO::FETCH_ASSOC));
    } else {
        echo json_encode([]);
    }
};

function nombresPropios($con)
{
    $personal = $con->prepare('SELECT idPropio as id, nombre as nombre FROM propios ORDER BY nombre');
    $personal->execute();
    if ($personal->rowCount() >= 1) {
        echo  json_encode($personal->fetchAll(PDO::FETCH_ASSOC));
    } else {
        echo json_encode([]);
    }
};

function nombresAcreedores($con)
{
    $personal = $con->prepare('SELECT idAcreedor as id, nombre as nombre FROM acreedores ORDER BY nombre');
    $personal->execute();
    if ($personal->rowCount() >= 1) {
        echo  json_encode($personal->fetchAll(PDO::FETCH_ASSOC));
    } else {
        echo json_encode([]);
    }
};

function clientesExportadores($con)
{
    $personal = $con->prepare('SELECT * FROM clientesexportadores');
    $personal->execute();
    if ($personal->rowCount() >= 1) {
        echo  json_encode($personal->fetchAll(PDO::FETCH_ASSOC));
    } else {
        echo json_encode([]);
    }
};

if ($post) {
    include_once '../../../DAOConeccion/conePDO.php';
    $pdo = new conePDO();
    $con = $pdo->conectar();

    switch ($post) {
        case '1':
            returnApicultores($con);
            break;
        case '3':
            returnProveedores($con);
            break;
        case '4':
            returnPersonal($con);
            break;
        case '6':
            returnClientes($con);
            break;
        case '7':
            return nombresPrestamos($con);
            break;
        case '8':
            nombresPropios($con);
            break;
        case '9':
            nombresAcreedores($con);
            break;
        case '10':
            clientesExportadores($con);
            break;
    }
} else {
    die;
}
