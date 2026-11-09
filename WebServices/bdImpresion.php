<?php

include_once __DIR__ . '/../DAOConeccion/conexionWebServices.php';

const BD_IMPRESION_PERMITIDAS = [
    'apicultores2026' => 'Convencional 2026',
    'apicultores2026_organica' => 'Orgánica 2026',
];

function dameBdsImpresion()
{
    return array_keys(BD_IMPRESION_PERMITIDAS);
}

function dameBdImpresion()
{
    $pdo = new conePDO();
    $control = $pdo->conectar('apicultorescontrol');

    $query = $control->prepare("SELECT valor FROM configuracion WHERE clave = 'bd_impresion'");
    $query->execute();
    $bd = $query->fetchColumn();

    if (!$bd || !array_key_exists($bd, BD_IMPRESION_PERMITIDAS)) {
        throw new Exception('BD de impresión no configurada o no permitida: ' . ($bd ?: '(vacía)'));
    }

    return $bd;
}
