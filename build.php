<?php

declare(strict_types=1);

/**
 * Genera la web estática de avantek en dist/ a partir de las plantillas Twig
 * y del contenido en data/ (sitio.json, noticias.json y paginas/*.json).
 *
 * Uso: php build.php
 * Para desarrollo usa el servidor con recarga: php -S localhost:8000 dev.php
 */

const ESTATICOS = ['css', 'assets'];

$autoload = __DIR__ . '/vendor/autoload.php';
if (!is_file($autoload)) {
    fwrite(STDERR, "[ERROR] Falta vendor/autoload.php. Ejecuta primero: composer install\n");
    exit(1);
}
require $autoload;
require __DIR__ . '/src/comun.php';

/** Copia un directorio de forma recursiva. */
function copiarDirectorio(string $origen, string $destino): void
{
    if (!is_dir($origen)) {
        return;
    }
    if (!is_dir($destino) && !mkdir($destino, 0775, true) && !is_dir($destino)) {
        throw new RuntimeException("No se puede crear $destino");
    }
    $iterador = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($origen, FilesystemIterator::SKIP_DOTS),
        RecursiveIteratorIterator::SELF_FIRST
    );
    foreach ($iterador as $elemento) {
        $ruta = $destino . '/' . $iterador->getSubPathname();
        if ($elemento->isDir()) {
            if (!is_dir($ruta)) {
                mkdir($ruta, 0775, true);
            }
        } else {
            copy($elemento->getPathname(), $ruta);
        }
    }
}

$dist = RAIZ . '/dist';
if (!is_dir($dist) && !mkdir($dist, 0775, true)) {
    fwrite(STDERR, "[ERROR] No se puede crear $dist\n");
    exit(1);
}

$archivos = glob(RAIZ . '/data/paginas/*.json') ?: [];
if ($archivos === []) {
    fwrite(STDERR, "[ERROR] No hay páginas en data/paginas/\n");
    exit(1);
}

try {
    $twig = crearTwig();
    foreach ($archivos as $archivo) {
        $nombre = basename($archivo, '.json');
        file_put_contents("$dist/$nombre.html", renderizarPagina($twig, $nombre));
        echo "[OK] dist/$nombre.html\n";
    }
} catch (Throwable $e) {
    fwrite(STDERR, "[ERROR] {$e->getMessage()}\n");
    exit(1);
}

foreach (ESTATICOS as $carpeta) {
    copiarDirectorio(RAIZ . "/$carpeta", "$dist/$carpeta");
}
echo "[OK] Recursos copiados (" . implode(', ', ESTATICOS) . ")\n";
