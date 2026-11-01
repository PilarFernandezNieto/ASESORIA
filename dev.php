<?php

declare(strict_types=1);

/**
 * Router del servidor de desarrollo: renderiza Twig en cada petición.
 *
 * Uso: php -S localhost:8000 dev.php
 * Solo para desarrollo local; no exponer a red.
 */

require __DIR__ . '/vendor/autoload.php';
require __DIR__ . '/src/comun.php';

$ruta = rawurldecode(parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/');

// Estáticos: solo css/ y assets/
if (preg_match('#^/(css|assets)/#', $ruta) && !str_contains($ruta, '..')) {
    return false; // lo sirve el propio servidor de PHP
}

// Páginas: / -> index, /nombre.html -> data/paginas/nombre.json
$nombre = $ruta === '/' ? 'index' : basename($ruta, '.html');
if (!preg_match('/^[a-z0-9-]+$/', $nombre) || !is_file(__DIR__ . "/data/paginas/$nombre.json")) {
    http_response_code(404);
    echo 'No encontrado';
    return true;
}

try {
    header('Content-Type: text/html; charset=utf-8');
    echo renderizarPagina(crearTwig(true), $nombre);
} catch (Throwable $e) {
    http_response_code(500);
    header('Content-Type: text/plain; charset=utf-8');
    echo "[ERROR] {$e->getMessage()}";
}
return true;
