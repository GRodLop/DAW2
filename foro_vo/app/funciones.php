<?php
function limpiarEntrada($valor) {
    if ($valor === null) {
        return '';
    }

    $valor = trim($valor);
    $valor = stripslashes($valor);
    $valor = htmlspecialchars($valor, ENT_QUOTES, 'UTF-8');
    return $valor;
}

function escaparHtml($valor) {
    return htmlspecialchars($valor, ENT_QUOTES, 'UTF-8');
}

function usuarioOK($usuario, $contraseña): bool {
    if (strlen($usuario) < 8) {
        return false;
    }

    $contraseñaEsperada = strrev($usuario);
    return ($usuario === trim($usuario)) && ($contraseña === $contraseñaEsperada);
}

function analizarTexto($texto): array {
    $textoSinTags = strip_tags($texto);
    $textoNormalizado = trim($textoSinTags);

    if ($textoNormalizado === '') {
        return [
            'longitud' => 0,
            'num_palabras' => 0,
            'letra_mas_repetida' => '-',
            'palabra_mas_repetida' => '-' 
        ];
    }

    $longitud = mb_strlen($textoNormalizado, 'UTF-8');
    $palabras = preg_split('/\s+/', $textoNormalizado, -1, PREG_SPLIT_NO_EMPTY);
    $numPalabras = count($palabras);

    $letras = [];
    foreach (mb_str_split($textoNormalizado) as $caracter) {
        $caracter = mb_strtolower($caracter, 'UTF-8');
        if (preg_match('/[a-záéíóúüñ]/u', $caracter)) {
            $letras[$caracter] = ($letras[$caracter] ?? 0) + 1;
        }
    }

    $letraMasRepetida = '-';
    if (!empty($letras)) {
        arsort($letras);
        $letraMasRepetida = array_key_first($letras);
    }

    $frecuenciaPalabras = [];
    foreach ($palabras as $palabra) {
        $palabra = mb_strtolower($palabra, 'UTF-8');
        $palabra = preg_replace('/[^a-záéíóúüñ0-9]/u', '', $palabra);
        if ($palabra !== '') {
            $frecuenciaPalabras[$palabra] = ($frecuenciaPalabras[$palabra] ?? 0) + 1;
        }
    }

    $palabraMasRepetida = '-';
    if (!empty($frecuenciaPalabras)) {
        arsort($frecuenciaPalabras);
        $palabraMasRepetida = array_key_first($frecuenciaPalabras);
    }

    return [
        'longitud' => $longitud,
        'num_palabras' => $numPalabras,
        'letra_mas_repetida' => $letraMasRepetida,
        'palabra_mas_repetida' => $palabraMasRepetida
    ];
}
