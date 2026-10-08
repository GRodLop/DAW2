<?php
declare(strict_types=1);

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    require __DIR__ . '/captura.html';
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    header('Allow: GET, POST');
    echo 'Método no permitido.';
    exit;
}

function cleanText($value): string
{
    if (!is_string($value)) {
        return '';
    }

    $value = strip_tags($value);
    $value = preg_replace('/[\x00-\x1F\x7F]/', '', $value);

    return trim($value ?? '');
}

function escapeHtml(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

$nombre = cleanText($_POST['nombre'] ?? '');
$alias = cleanText($_POST['alias'] ?? '');
$errores = [];

if ($nombre === '') {
    $errores[] = 'El nombre es obligatorio.';
}
if ($alias === '') {
    $errores[] = 'El alias es obligatorio.';
}

$edad = '';
if (isset($_POST['edad']) && $_POST['edad'] !== '') {
    $edadRecibida = is_string($_POST['edad']) ? filter_var($_POST['edad'], FILTER_VALIDATE_INT) : false;
    if ($edadRecibida === false || $edadRecibida < 0 || $edadRecibida > 130) {
        $errores[] = 'La edad debe ser un número entre 0 y 130.';
    } else {
        $edad = (string) $edadRecibida;
    }
}

$armasPermitidas = ['Maza', 'Antorcha', 'Martillo', 'Látigo'];
$armasRecibidas = $_POST['armas'] ?? [];
$armas = [];
if (is_array($armasRecibidas)) {
    foreach ($armasRecibidas as $arma) {
        if (is_string($arma) && in_array($arma, $armasPermitidas, true) && !in_array($arma, $armas, true)) {
            $armas[] = $arma;
        }
    }
}

$magiaRecibida = $_POST['magia'] ?? '';
$magia = is_string($magiaRecibida) && in_array($magiaRecibida, ['Sí', 'No'], true)
    ? $magiaRecibida
    : '';

$directorioUploads = __DIR__ . '/uploads';
$rutaImagen = 'calavera.png';
$estadoImagen = 'default';
$mensajeErrorImagen = '';

if (!is_dir($directorioUploads)
    && !mkdir($directorioUploads, 0755, true)
    && !is_dir($directorioUploads)) {
    $estadoImagen = 'error';
    $mensajeErrorImagen = 'Error al subir la imagen: no se pudo preparar el directorio de destino.';
} else {
    $archivo = $_FILES['imagen'] ?? null;

    if (is_array($archivo) && isset($archivo['error']) && is_int($archivo['error'])) {
        if ($archivo['error'] !== UPLOAD_ERR_NO_FILE) {
            if ($archivo['error'] === UPLOAD_ERR_FORM_SIZE || $archivo['error'] === UPLOAD_ERR_INI_SIZE) {
                $estadoImagen = 'error';
                $mensajeErrorImagen = 'Error al subir la imagen: el tamaño máximo permitido es 10 KB.';
            } elseif ($archivo['error'] !== UPLOAD_ERR_OK) {
                $estadoImagen = 'error';
                $mensajeErrorImagen = 'Error al subir la imagen: no se pudo recibir el archivo correctamente.';
            } elseif (!isset($archivo['tmp_name'], $archivo['size'])
                || !is_string($archivo['tmp_name'])
                || !is_int($archivo['size'])
                || !is_uploaded_file($archivo['tmp_name'])) {
                $estadoImagen = 'error';
                $mensajeErrorImagen = 'Error al subir la imagen: no se pudo verificar el archivo.';
            } elseif ($archivo['size'] === 0) {
                $estadoImagen = 'error';
                $mensajeErrorImagen = 'Error al subir la imagen: el archivo está vacío.';
            } elseif ($archivo['size'] > 10240) {
                $estadoImagen = 'error';
                $mensajeErrorImagen = 'Error al subir la imagen: el tamaño máximo permitido es 10 KB.';
            } else {
                $finfo = finfo_open(FILEINFO_MIME_TYPE);
                $mime = $finfo === false ? false : finfo_file($finfo, $archivo['tmp_name']);
                if ($finfo !== false) {
                    finfo_close($finfo);
                }

                $datosImagen = @getimagesize($archivo['tmp_name']);
                $esPng = $mime === 'image/png'
                    && is_array($datosImagen)
                    && isset($datosImagen['mime'])
                    && $datosImagen['mime'] === 'image/png';

                if (!$esPng) {
                    $estadoImagen = 'error';
                    $mensajeErrorImagen = 'Error al subir la imagen: solo se permiten archivos PNG válidos.';
                } else {
                    $nombreArchivo = bin2hex(random_bytes(16)) . '.png';
                    if (move_uploaded_file($archivo['tmp_name'], $directorioUploads . '/' . $nombreArchivo)) {
                        $rutaImagen = 'uploads/' . $nombreArchivo;
                        $estadoImagen = 'success';
                    } else {
                        $estadoImagen = 'error';
                        $mensajeErrorImagen = 'Error al subir la imagen: no se pudo guardar el archivo.';
                    }
                }
            }
        }
    } elseif ($archivo !== null) {
        $estadoImagen = 'error';
        $mensajeErrorImagen = 'Error al subir la imagen: datos de archivo no válidos.';
    }
}
?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Datos del jugador</title>
    <style>
        :root { font-family: Arial, Helvetica, sans-serif; color: #080808; background: #f4f4f4; }

        * { box-sizing: border-box; }

        body {
            min-height: 100vh;
            margin: 0;
            padding: 12px 14px;
            background: #f4f4f4;
        }

        .result-card {
            display: grid;
            grid-template-columns: 1fr 1fr;
            align-items: center;
            gap: 12px 20px;
            width: min(100%, 484px);
            margin: 0 auto;
            padding: 20px;
            border-radius: 10px;
            background: #ffff36;
            box-shadow: 0 5px 14px rgb(0 0 0 / 12%);
        }

        h1 {
            grid-column: 1 / -1;
            margin: 15px 0 22px;
            text-align: center;
            font-size: 1.35rem;
        }

        .player-data { min-width: 0; }
        .player-data p { margin: 0 0 15px; line-height: 1.2; }
        .player-data p:last-child { margin-bottom: 0; }
        .player-data strong { font-weight: 700; }

        .image-panel {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: flex-start;
            gap: 10px;
            min-width: 0;
            text-align: center;
        }

        .image-status { min-height: 18px; margin: 0; font-weight: 700; }

        .image-panel img {
            display: block;
            width: min(100%, 190px);
            max-height: 235px;
            object-fit: contain;
            border: 1px solid #222;
            background: #ffff36;
        }

        .image-status.error { color: #8b1f16; }

        .image-error { max-width: 250px; margin: 8px 0 0; }
        .errors {
            margin: 0 0 20px;
            padding: 12px 15px 12px 34px;
            border-radius: 10px;
            color: #7b1d15;
            background: #fff0eb;
        }

        @media (max-width: 620px) {
            .result-card { gap: 12px; padding: 18px; }
            .player-data p { margin-bottom: 13px; }
        }
    </style>
</head>
<body>
    <main class="result-card">
        <h1 id="titulo">Datos del Jugador</h1>
        <section class="player-data" aria-labelledby="titulo">
            <?php if ($errores !== []): ?>
                <ul class="errors">
                    <?php foreach ($errores as $error): ?>
                        <li><?= escapeHtml($error) ?></li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
            <p><strong>Nombre:</strong> <?= escapeHtml($nombre !== '' ? $nombre : 'No indicado') ?></p>
            <p><strong>Alias:</strong> <?= escapeHtml($alias !== '' ? $alias : 'No indicado') ?></p>
            <p><strong>Edad:</strong> <?= escapeHtml($edad !== '' ? $edad : 'No indicada') ?></p>
            <p><strong>Armas seleccionadas:</strong> <?= escapeHtml($armas !== [] ? implode(', ', $armas) : 'Ninguna') ?></p>
            <p><strong>¿Practica artes mágicas?</strong><br><?= escapeHtml($magia !== '' ? $magia : 'No indicado') ?></p>
        </section>

        <section class="image-panel" aria-label="Imagen del jugador">
            <p class="image-status">
                <?= $estadoImagen === 'success' ? 'Imagen subida:' : 'No se subió ninguna imagen.' ?>
            </p>
            <img src="<?= escapeHtml($rutaImagen) ?>" alt="Imagen del jugador">
            <?php if ($estadoImagen === 'error'): ?>
                <p class="image-status error image-error"><?= escapeHtml($mensajeErrorImagen) ?></p>
            <?php endif; ?>
        </section>

    </main>
</body>
</html>
