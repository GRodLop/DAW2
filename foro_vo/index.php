<?php
// PRIMERA APROXIMACIÓN AL MODELO VISTA CONTROLADOR.
// Funciones auxiliares: usuarioOK, sanitización, estadísticas del texto.
include_once 'app/funciones.php';

// Salida hacia buffer
ob_start();
$msg = '';
$estadisticas = [
    'longitud' => 0,
    'num_palabras' => 0,
    'letra_mas_repetida' => '-',
    'palabra_mas_repetida' => '-' 
];

if (!isset($_REQUEST['orden'])) {
    include_once 'app/plantillas/entrada.php';
} else {
    switch ($_REQUEST['orden']) {
        case 'Entrar':
            $usuario = limpiarEntrada($_REQUEST['nombre'] ?? '');
            $password = limpiarEntrada($_REQUEST['contraseña'] ?? '');

            if (isset($_REQUEST['nombre']) && isset($_REQUEST['contraseña']) && usuarioOK($usuario, $password)) {
                $msg = 'Bienvenido <b>' . escaparHtml($usuario) . '</b>';
                include_once 'app/plantillas/comentario.html';
            } else {
                $msg = 'Usuario no válido';
                include_once 'app/plantillas/entrada.php';
            }
            break;

        case 'Nueva opinión':
            include_once 'app/plantillas/comentario.html';
            break;

        case 'Detalles':
            $tema = limpiarEntrada($_REQUEST['tema'] ?? '');
            $comentario = limpiarEntrada($_REQUEST['comentario'] ?? '');
            $estadisticas = analizarTexto($comentario);

            $_REQUEST['tema'] = $tema;
            $_REQUEST['comentario'] = $comentario;

            echo '<div style="margin-bottom: 10px;"><strong>Detalles de su opinión</strong></div>';
            include_once 'app/plantillas/comentariorelleno.php';
            include_once 'app/plantillas/detalles.php';
            break;

        case 'Terminar':
            include_once 'app/plantillas/entrada.php';
            break;
    }
}

$contenido_php = ob_get_clean();
include_once 'app/plantillas/principal.php';

