<?php


$unidades = array(null, 'uno', 'dos', 'tres', 'cuatro', 'cinco', 'seis', 'siete', 'ocho', 'nueve');
$decimas = array(null, 'diez', 'veinte', 'treinta', 'cuarenta', 'cincuenta', 'sesenta', 'setenta', 'ochenta', 'noventa');
$diez_y = array(null, 'once', 'doce', 'trece', 'catorce', 'quince');
$centenas = array(null, 'ciento ', null, null, null, 'quinientos ', null, 'setecientos ', null, 'novecientos ');
$resultado = '';
function unidades_en_letra($cantidad){
    global $unidades, $decimas, $diez_y, $centenas, $resultado;
    $unidad = $cantidad % 10;
    $decima = (($cantidad - $unidad) / 10 ) % 10;
    $centena = (($cantidad - $cantidad%100) / 100) % 10;

    if($centena != 0){
        if($centenas[$centena] == null){
            $resultado = $resultado . "$unidades[$centena]cientos ";
        } else if($centena != 1 || $unidad != 0 || $decima != 0){
            $resultado = $resultado . $centenas[$centena];
        } else {
            $resultado = $resultado . 'cien';
        }
    }

    if($unidad != 0 || $decima != 0){
        if($centena != 0){
            $resultado = $resultado . '';
        }
        if($decima == 0){
            $resultado = $resultado . $unidades[$unidad];
        } else if($unidad == 0){
            $resultado = $resultado . $decimas[$decima];
        } else if($decima == 1 && $unidad >= 1 && $unidad <= 5){
            $resultado = $resultado . $diez_y[$unidad];
        } else if($decima == 1){
            $resultado = $resultado . "dieci$unidades[$unidad]";
        } else if($decima == 2){
            $resultado = $resultado . "veinti$unidades[$unidad]";
        } else {
            $resultado = $resultado . "$decimas[$decima] y $unidades[$unidad]";
        }
    }
}

function millares_en_letra($cantidad){
    global $resultado;
    $unidades = $cantidad % 1000;
    $miles = (($cantidad - $unidades) / 1000 ) % 1000;
    if($miles > 1){
        unidades_en_letra($miles);
    }
    if($miles != 0){
        $resultado = $resultado . ' mil ';
    }
    if($unidades != 0 && $miles != 0){
        $resultado = $resultado . '';
    }

    unidades_en_letra($unidades);
}

function millones_en_letra($cantidad){
    global $resultado;
    $unidades = $cantidad % 1000000;
    $millares = (($cantidad - $unidades) / 1000000) % 1000000;
    if($millares > 1){
        millares_en_letra($millares);
        $resultado = $resultado . 'millones';
    } else if($millares != 0){
        $resultado = $resultado . 'un millon ';
    }

    if($unidades != 0 && $millares != 0){
        $resultado = $resultado . '';
    }

    millares_en_letra($unidades);
}

function convertir($numero){

    global $resultado;
    $resultado = '';

    $numeroConCentavos = number_format($numero, 2, '.', ',');

    if (strpos($numeroConCentavos, '.') !== false) {
        $position = strpos($numeroConCentavos, '.') + 1;
        $cents = substr($numeroConCentavos, $position, 2);
    } else {
        $cents = 00;
    }

    $cantidad = intval($numero);

    if($cantidad < 0){
        $resultado = $resultado . 'Menos ';
        $cantidad = -$cantidad;
    }

    if($cantidad == 0){
        $resultado = $resultado . '';
    } else {
        millones_en_letra($cantidad);
    }

    $resultado = $resultado . ' ' . "PESOS $cents/100";
    return strtoupper($resultado);
}
?>